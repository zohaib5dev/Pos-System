<?php

namespace App\Livewire\Inventory;

use Livewire\Component;
use App\Models\Product;
use App\Models\StockAdjustment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Count extends Component
{
    public $countItems = [];
    public $countDate;
    public $countNotes = '';
    public $summary = [];

    public function mount()
    {
        $this->countDate = now()->format('Y-m-d');
        $this->loadStockCount();
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

    public function loadStockCount()
    {
        $this->countItems = Product::where('is_active', true)
            ->select('id', 'name', 'sku', 'stock_quantity', 'low_stock_threshold')
            ->orderBy('name')
            ->get()
            ->map(function($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'system_quantity' => $product->stock_quantity,
                    'counted_quantity' => $product->stock_quantity,
                    'difference' => 0,
                    'notes' => '',
                ];
            })->toArray();
    }

    public function updatedCountItems()
    {
        foreach ($this->countItems as $index => $item) {
            $this->countItems[$index]['difference'] = 
                ($this->countItems[$index]['counted_quantity'] ?? 0) - 
                ($this->countItems[$index]['system_quantity'] ?? 0);
        }
    }

    public function saveStockCount()
    {
        $this->validate([
            'countDate' => 'required|date',
            'countItems.*.counted_quantity' => 'required|numeric|min:0',
        ]);
        
        DB::beginTransaction();
        
        try {
            $adjustments = [];
            $adjustmentDetails = [];
            
            foreach ($this->countItems as $item) {
                $difference = $item['counted_quantity'] - $item['system_quantity'];
                
                if ($difference != 0) {
                    $product = Product::find($item['id']);
                    $oldQuantity = $product->stock_quantity;
                    
                    // Update product stock
                    $product->stock_quantity = $item['counted_quantity'];
                    $product->save();
                    
                    // Create stock adjustment record
                    $adjustment = StockAdjustment::create([
                        'adjustment_number' => 'COUNT-' . time() . '-' . rand(1000, 9999),
                        'product_id' => $product->id,
                        'adjustment_type' => $difference > 0 ? 'addition' : 'deduction',
                        'quantity' => abs($difference),
                        'current_quantity' => $oldQuantity,
                        'new_quantity' => $item['counted_quantity'],
                        'reason' => 'Stock count adjustment',
                        'notes' => 'Physical stock count on ' . $this->countDate . ($this->countNotes ? ': ' . $this->countNotes : ''),
                        'created_by' => auth()->id(),
                    ]);
                    
                    $adjustments[] = $adjustment;
                    $adjustmentDetails[] = [
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'product_sku' => $product->sku,
                        'old_quantity' => $oldQuantity,
                        'new_quantity' => $item['counted_quantity'],
                        'difference' => $difference,
                        'adjustment_type' => $difference > 0 ? 'addition' : 'deduction'
                    ];
                }
            }
            
            DB::commit();
            
            // Log activity
            logActivity(
                'stock_count',
                new StockAdjustment(),
                [],
                [
                    'count_date' => $this->countDate,
                    'total_adjustments' => count($adjustments),
                    'notes' => $this->countNotes,
                    'adjustments' => $adjustmentDetails
                ]
            );
            
            $this->dispatch('notify', [
                'message' => 'Stock count completed with ' . count($adjustments) . ' adjustments',
                'type' => 'success'
            ]);
            
            // Reload the stock count
            $this->loadStockCount();
            $this->calculateSummary();
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            $this->dispatch('notify', [
                'message' => 'Error saving stock count: ' . $e->getMessage(),
                'type' => 'error'
            ]);
            
            Log::error('Stock count error: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.inventories.count', [
            'summary' => $this->summary,
        ])->layout('layouts.app');
    }
}