<?php

namespace App\Livewire\Inventory;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Index extends Component
{
    use WithPagination;

    // Search and Filters
    public $search = '';
    public $categoryFilter = '';
    public $brandFilter = '';
    public $stockStatus = 'all';
    public $sortField = 'name';
    public $sortDirection = 'asc';
    public $perPage = 15;

    // Selection
    public $selectedProducts = [];
    public $selectAll = false;

    // Adjustment Modal
    public $showAdjustmentModal = false;
    public $productId = null;
    public $adjustmentType = 'addition';
    public $adjustmentQuantity = 0;
    public $adjustmentReason = '';
    public $adjustmentNotes = '';
    public $currentStock = 0;
    public $newStock = 0;

    // Summary
    public $summary = [];

    protected $queryString = [
        'search' => ['except' => ''],
        'categoryFilter' => ['except' => ''],
        'brandFilter' => ['except' => ''],
        'stockStatus' => ['except' => 'all'],
        'sortField' => ['except' => 'name'],
        'sortDirection' => ['except' => 'asc'],
        'perPage' => ['except' => 15],
    ];

    protected $listeners = [
        'refreshProducts' => '$refresh'
    ];

    public function mount()
    {
        $this->calculateSummary();
    }

    public function calculateSummary()
    {
        $this->summary = [
            'total_products' => Product::count(),
            'total_value' => Product::sum(DB::raw('stock_quantity * purchase_price')),
            'total_retail' => Product::sum(DB::raw('stock_quantity * selling_price')),
            'low_stock' => Product::whereRaw('stock_quantity <= low_stock_threshold')->count(),
            'out_of_stock' => Product::where('stock_quantity', '<=', 0)->count(),
            'in_stock' => Product::where('stock_quantity', '>', 0)->count(),
        ];
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedCategoryFilter()
    {
        $this->resetPage();
        $this->calculateSummary();
    }

    public function updatedBrandFilter()
    {
        $this->resetPage();
        $this->calculateSummary();
    }

    public function updatedStockStatus()
    {
        $this->resetPage();
        $this->calculateSummary();
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function getProductsQuery()
    {
        return Product::with(['category', 'brand'])
            ->withCount('orderItems')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('sku', 'like', '%' . $this->search . '%')
                        ->orWhere('barcode', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->categoryFilter, fn($q) => $q->where('category_id', $this->categoryFilter))
            ->when($this->brandFilter, fn($q) => $q->where('brand_id', $this->brandFilter))
            ->when($this->stockStatus !== 'all', function ($query) {
                switch ($this->stockStatus) {
                    case 'low':
                        return $query->whereRaw('stock_quantity <= low_stock_threshold');
                    case 'out':
                        return $query->where('stock_quantity', '<=', 0);
                    case 'in':
                        return $query->where('stock_quantity', '>', 0);
                }
            });
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

   public function getCurrentPageIds()
    {
        return $this->getProductsQuery()
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage)
            ->pluck('id')
            ->map(fn($id) => (string) $id)
            ->toArray();
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedProducts = $this->getCurrentPageIds();
        } else {
            $this->selectedProducts = [];
        }
    }

    public function updatedSelectedpselectedProducts()
    {
        $currentPageIds = $this->getCurrentPageIds();
        $selectedIds = $this->selectedProducts;

        sort($currentPageIds);
        sort($selectedIds);
        $this->selectAll = $currentPageIds === $selectedIds && !empty($currentPageIds);
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
        } elseif (!empty($this->selectedProducts)) {
            $product = Product::find($this->selectedProducts[0]);
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
            $adjustment = \App\Models\StockAdjustment::create([
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
            
            $this->calculateSummary();
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

    public function bulkAdjust()
    {
        if (empty($this->selectedProducts)) {
            $this->dispatch('notify', [
                'message' => 'No products selected',
                'type' => 'error'
            ]);
            return;
        }
        
        // Log bulk adjust action
        logActivity(
            'bulk_adjust_initiated',
            new Product(),
            [],
            ['selected_product_ids' => $this->selectedProducts, 'count' => count($this->selectedProducts)]
        );
        
        // Open adjustment modal with first selected product
        $this->openAdjustmentModal($this->selectedProducts[0]);
    }

    public function render()
    {
        $products = $this->getProductsQuery()
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.inventories.index', [
            'products' => $products,
            'categories' => Category::where('is_active', true)->orderBy('name')->get(),
            'brands' => Brand::where('is_active', true)->orderBy('name')->get(),
        ])->layout('layouts.app');
    }
}