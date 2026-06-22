<?php

namespace App\Livewire\Inventory;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\StockAdjustment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LowStock extends Component
{
    use WithPagination;

    public $search = '';
    public $categoryFilter = '';
    public $brandFilter = '';
    public $perPage = 15;

    // Adjustment Modal
    public $showAdjustmentModal = false;
    public $productId = null;
    public $adjustmentType = 'addition';
    public $adjustmentQuantity = 0;
    public $adjustmentReason = '';
    public $adjustmentNotes = '';
    public $currentStock = 0;
    public $newStock = 0;

    protected $queryString = [
        'search' => ['except' => ''],
        'categoryFilter' => ['except' => ''],
        'brandFilter' => ['except' => ''],
        'perPage' => ['except' => 15],
    ];

    public function getProductsQuery()
    {
        return Product::with(['category', 'brand'])
            ->whereRaw('stock_quantity <= low_stock_threshold')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('sku', 'like', '%' . $this->search . '%')
                        ->orWhere('barcode', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->categoryFilter, fn($q) => $q->where('category_id', $this->categoryFilter))
            ->when($this->brandFilter, fn($q) => $q->where('brand_id', $this->brandFilter));
    }

    public function openAdjustmentModal($id = null)
    {
        $this->resetValidation();
        
        if ($id) {
            $product = Product::find($id);
            if ($product) {
                $this->productId = $product->id;
                $this->currentStock = $product->stock_quantity;
                $this->newStock = $product->stock_quantity;
            }
        }
        
        $this->reset(['adjustmentType', 'adjustmentQuantity', 'adjustmentReason', 'adjustmentNotes']);
        $this->adjustmentType = 'addition';
        $this->showAdjustmentModal = true;
    }

    public function updatedAdjustmentType()
    {
        $this->calculateNewStock();
    }

    public function updatedAdjustmentQuantity()
    {
        $this->calculateNewStock();
    }

    public function calculateNewStock()
    {
        if (!is_numeric($this->adjustmentQuantity) || $this->adjustmentQuantity <= 0) {
            $this->newStock = $this->currentStock;
            return;
        }
        
        if ($this->adjustmentType === 'addition') {
            $this->newStock = $this->currentStock + $this->adjustmentQuantity;
        } else {
            $this->newStock = max(0, $this->currentStock - $this->adjustmentQuantity);
        }
    }

    public function saveAdjustment()
    {
        $this->validate([
            'productId' => 'required|exists:products,id',
            'adjustmentType' => 'required|in:addition,deduction',
            'adjustmentQuantity' => 'required|numeric|min:0.01',
            'adjustmentReason' => 'required|min:3',
            'adjustmentNotes' => 'nullable|string',
        ]);
        
        if ($this->adjustmentType === 'deduction' && $this->adjustmentQuantity > $this->currentStock) {
            $this->dispatch('notify', [
                'message' => 'Deduction quantity cannot exceed current stock',
                'type' => 'error'
            ]);
            return;
        }
        
        DB::beginTransaction();
        
        try {
            $product = Product::find($this->productId);
            $oldQuantity = $product->stock_quantity;
            
            if ($this->adjustmentType === 'addition') {
                $newQuantity = $oldQuantity + $this->adjustmentQuantity;
            } else {
                $newQuantity = $oldQuantity - $this->adjustmentQuantity;
            }
            
            // Update product stock
            $product->stock_quantity = $newQuantity;
            $product->save();
            
            // Create stock adjustment record
            $adjustment = StockAdjustment::create([
                'adjustment_number' => 'ADJ-' . time() . '-' . rand(1000, 9999),
                'product_id' => $product->id,
                'adjustment_type' => $this->adjustmentType,
                'quantity' => $this->adjustmentQuantity,
                'current_quantity' => $oldQuantity,
                'new_quantity' => $newQuantity,
                'reason' => $this->adjustmentReason,
                'notes' => $this->adjustmentNotes,
                'created_by' => auth()->id(),
            ]);
            
            DB::commit();
            
            // Log activity
            logActivity(
                'stock_adjustment',
                $adjustment,
                [],
                [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_sku' => $product->sku,
                    'adjustment_type' => $this->adjustmentType,
                    'quantity' => $this->adjustmentQuantity,
                    'old_stock' => $oldQuantity,
                    'new_stock' => $newQuantity,
                    'reason' => $this->adjustmentReason,
                    'notes' => $this->adjustmentNotes
                ]
            );
            
            // Close modal
            $this->showAdjustmentModal = false;
            
            // Reset form
            $this->reset(['productId', 'adjustmentType', 'adjustmentQuantity', 'adjustmentReason', 'adjustmentNotes', 'currentStock', 'newStock']);
            
            $this->dispatch('notify', [
                'message' => 'Stock adjustment completed successfully',
                'type' => 'success'
            ]);
            
            $this->dispatch('refreshProducts');
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            $this->dispatch('notify', [
                'message' => 'Error processing adjustment: ' . $e->getMessage(),
                'type' => 'error'
            ]);
            
            Log::error('Stock adjustment error: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $products = $this->getProductsQuery()
            ->orderByRaw('(stock_quantity / low_stock_threshold) ASC')
            ->paginate($this->perPage);

        return view('livewire.inventories.low-stock', [
            'products' => $products,
            'categories' => Category::where('is_active', true)->orderBy('name')->get(),
            'brands' => Brand::where('is_active', true)->orderBy('name')->get(),
        ])->layout('layouts.app');
    }
}