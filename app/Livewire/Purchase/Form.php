<?php

namespace App\Livewire\Purchase;

use Livewire\Component;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\PurchaseItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Form extends Component
{
    // Mode
    public $mode = 'create';
    public $purchaseId = null;

    // Form Fields
    public $supplier_id = '';
    public $purchase_number = '';
    public $purchase_date;
    public $expected_delivery_date;
    public $delivery_date;
    public $status = 'draft';
    public $payment_status = 'pending';
    public $subtotal = 0;
    public $tax_amount = 0;
    public $discount_type = 'fixed';
    public $discount_value = 0;
    public $discount_amount = 0;
    public $shipping_cost = 0;
    public $other_cost = 0;
    public $total_amount = 0;
    public $paid_amount = 0;
    public $notes = '';

    // Purchase Items
    public $items = [];
    public $newItem = [
        'product_id' => '',
        'product_name' => '',
        'sku' => '',
        'quantity' => 1,
        'received_quantity' => 0,
        'unit_cost' => 0,
        'discount_type' => 'fixed',
        'discount_value' => 0,
        'discount_amount' => 0,
        'tax_rate' => 0,
        'tax_amount' => 0,
        'total_cost' => 0,
    ];
    public $editingItemIndex = null;
    public $showProductModal = false;
    public $productSearch = '';

    protected function rules()
    {
        $rules = [
            'supplier_id' => 'required|exists:suppliers,id',
            'purchase_date' => 'required|date',
            'expected_delivery_date' => 'nullable|date|after_or_equal:purchase_date',
            'status' => 'required|in:draft,ordered,partial,received,cancelled',
            'notes' => 'nullable|max:1000',
        ];

        // Add unique rule for purchase_number with ignore for edit mode
        if ($this->mode === 'edit' && $this->purchaseId) {
            $rules['purchase_number'] = 'required|unique:purchases,purchase_number,' . $this->purchaseId;
        } else {
            $rules['purchase_number'] = 'required|unique:purchases,purchase_number';
        }

        return $rules;
    }

    public function mount($id = null)
    {
        $this->purchase_date = now()->format('Y-m-d');

         $productId = request()->query('product');

        if ($id) {
            $this->mode = 'edit';
            $this->purchaseId = $id;
            $this->loadPurchase();
        } else {
            $this->generatePurchaseNumber();
             if ($productId) {
                $this->addItem((int)$productId);
                $this->dispatch('notify', [
                    'message' => 'Product pre-loaded from stock alert',
                    'type' => 'info'
                ]);
            }
        }
    }

      public function openProductModal()
    {
        $this->showProductModal = true;
        $this->productSearch = '';
    }

    public function closeProductModal()
    {
        $this->showProductModal = false;
        $this->productSearch = '';
    }
    public function generatePurchaseNumber()
    {
        $this->purchase_number = 'PO-' . date('Ymd') . '-' . str_pad(Purchase::count() + 1, 4, '0', STR_PAD_LEFT);
    }

    public function loadPurchase()
    {
        $purchase = Purchase::with(['items.product'])->find($this->purchaseId);

        if ($purchase) {
            $this->supplier_id = $purchase->supplier_id;
            $this->purchase_number = $purchase->purchase_number;
            $this->purchase_date = $purchase->purchase_date->format('Y-m-d');
            $this->expected_delivery_date = $purchase->expected_delivery_date?->format('Y-m-d');
            $this->delivery_date = $purchase->delivery_date?->format('Y-m-d');
            $this->status = $purchase->status;
            $this->payment_status = $purchase->payment_status;
            $this->subtotal = $purchase->subtotal;
            $this->tax_amount = $purchase->tax_amount;
            $this->discount_type = $purchase->discount_type;
            $this->discount_value = $purchase->discount_value;
            $this->discount_amount = $purchase->discount_amount;
            $this->shipping_cost = $purchase->shipping_cost;
            $this->other_cost = $purchase->other_cost;
            $this->total_amount = $purchase->total_amount;
            $this->paid_amount = $purchase->paid_amount;
            $this->notes = $purchase->notes;

            // Load items
            $this->items = $purchase->items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'product_id' => $item->product_id,
                    'product_name' => $item->product->name ?? $item->product_name,
                    'sku' => $item->product->sku ?? $item->sku,
                    'quantity' => $item->quantity,
                    'received_quantity' => $item->received_quantity,
                    'unit_cost' => $item->unit_cost,
                    'discount_type' => $item->discount_type,
                    'discount_value' => $item->discount_value,
                    'discount_amount' => $item->discount_amount,
                    'tax_rate' => $item->tax_rate,
                    'tax_amount' => $item->tax_amount,
                    'total_cost' => $item->total_cost,
                ];
            })->toArray();

            $this->calculateTotals();
        }
    }

    public function getProductsProperty()
    {
        if (strlen($this->productSearch) < 2) {
            return collect([]);
        }

        return Product::where('is_active', true)
            ->where(function ($query) {
                $query->where('name', 'like', '%' . $this->productSearch . '%')
                    ->orWhere('sku', 'like', '%' . $this->productSearch . '%')
                    ->orWhere('barcode', 'like', '%' . $this->productSearch . '%');
            })
            ->limit(10)
            ->get();
    }

    public function addItem($productId)
    {
        $product = Product::find($productId);

        if (!$product) {
            $this->dispatch('notify', [
                'message' => 'Product not found',
                'type' => 'error'
            ]);
            return;
        }

        // Check if product already exists in items
        $existingIndex = null;
        foreach ($this->items as $index => $item) {
            if ($item['product_id'] == $productId) {
                $existingIndex = $index;
                break;
            }
        }

        if ($existingIndex !== null) {
            $this->items[$existingIndex]['quantity'] += 1;
            $this->calculateItemTotal($existingIndex);
            
            $this->dispatch('notify', [
                'message' => 'Item quantity updated',
                'type' => 'success'
            ]);
        } else {
            $newItem = [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'sku' => $product->sku,
                'quantity' => 1,
                'received_quantity' => 0,
                'unit_cost' => $product->purchase_price ?? 0,
                'discount_type' => 'fixed',
                'discount_value' => 0,
                'discount_amount' => 0,
                'tax_rate' => $product->tax_rate ?? 0,
                'tax_amount' => 0,
                'total_cost' => $product->purchase_price ?? 0,
            ];

            $this->items[] = $newItem;
            $this->calculateItemTotal(count($this->items) - 1);
            
            $this->dispatch('notify', [
                'message' => 'Item added successfully',
                'type' => 'success'
            ]);
        }

        $this->showProductModal = false;
        $this->productSearch = '';
        $this->calculateTotals();
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
        $this->calculateTotals();
        
        $this->dispatch('notify', [
            'message' => 'Item removed',
            'type' => 'success'
        ]);
    }

    public function updateItemQuantity($index, $quantity)
    {
        if ($quantity <= 0) {
            $this->removeItem($index);
            return;
        }

        $this->items[$index]['quantity'] = $quantity;
        $this->calculateItemTotal($index);
        $this->calculateTotals();
    }

    public function updateItemCost($index, $cost)
    {
        $this->items[$index]['unit_cost'] = $cost;
        $this->calculateItemTotal($index);
        $this->calculateTotals();
    }

    public function calculateItemTotal($index)
    {
        $item = &$this->items[$index];

        // Calculate discount
        if ($item['discount_type'] === 'percentage') {
            $item['discount_amount'] = ($item['unit_cost'] * $item['quantity'] * $item['discount_value']) / 100;
        } else {
            $item['discount_amount'] = $item['discount_value'];
        }

        // Calculate tax
        $afterDiscount = ($item['unit_cost'] * $item['quantity']) - $item['discount_amount'];
        $item['tax_amount'] = ($afterDiscount * $item['tax_rate']) / 100;

        // Calculate total
        $item['total_cost'] = $afterDiscount + $item['tax_amount'];
    }

    public function calculateTotals()
    {
        $this->subtotal = collect($this->items)->sum(function ($item) {
            return $item['unit_cost'] * $item['quantity'];
        });

        $this->tax_amount = collect($this->items)->sum('tax_amount');

        // Calculate discount
        if ($this->discount_type === 'percentage') {
            $this->discount_amount = ($this->subtotal * $this->discount_value) / 100;
        } else {
            $this->discount_amount = $this->discount_value;
        }

        $this->total_amount = $this->subtotal - $this->discount_amount + $this->tax_amount + $this->shipping_cost + $this->other_cost;
    }

    public function updatedDiscountValue()
    {
        $this->calculateTotals();
    }

    public function updatedDiscountType()
    {
        $this->calculateTotals();
    }

    public function updatedShippingCost()
    {
        $this->calculateTotals();
    }

    public function updatedOtherCost()
    {
        $this->calculateTotals();
    }

    public function savePurchase()
    {
        $this->validate();

        if (empty($this->items)) {
            $this->dispatch('notify', [
                'message' => 'Please add at least one item',
                'type' => 'error'
            ]);
            return;
        }

        DB::beginTransaction();

        try {
            $data = [
                'supplier_id' => $this->supplier_id,
                'purchase_number' => $this->purchase_number,
                'purchase_date' => $this->purchase_date,
                'expected_delivery_date' => $this->expected_delivery_date,
                'status' => $this->status,
                'payment_status' => $this->payment_status,
                'subtotal' => $this->subtotal,
                'tax_amount' => $this->tax_amount,
                'discount_type' => $this->discount_type,
                'discount_value' => $this->discount_value,
                'discount_amount' => $this->discount_amount,
                'shipping_cost' => $this->shipping_cost,
                'other_cost' => $this->other_cost,
                'total_amount' => $this->total_amount,
                'paid_amount' => $this->paid_amount,
                'notes' => $this->notes ?? null,
            ];

            if ($this->mode === 'edit') {
                $purchase = Purchase::find($this->purchaseId);

                if (!$purchase) {
                    $this->dispatch('notify', [
                        'message' => 'Purchase not found',
                        'type' => 'error'
                    ]);
                    return;
                }

                $oldData = collect($purchase->toArray())->except(['updated_at'])->toArray();
                $purchase->update($data);

                // Delete old items
                $purchase->items()->delete();
            } else {
                $data['created_by'] = auth()->id();
                $purchase = Purchase::create($data);
                $this->purchaseId = $purchase->id;
            }

            // Create new items
            foreach ($this->items as $item) {
                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'received_quantity' => $item['received_quantity'] ?? 0,
                    'unit_cost' => $item['unit_cost'],
                    'discount_type' => $item['discount_type'],
                    'discount_value' => $item['discount_value'],
                    'discount_amount' => $item['discount_amount'],
                    'tax_rate' => $item['tax_rate'],
                    'tax_amount' => $item['tax_amount'],
                    'total_cost' => $item['total_cost'],
                ]);
            }

            DB::commit();

            // Log activity
            $action = $this->mode === 'edit' ? 'updated' : 'created';
            logActivity($action, $purchase, $oldData ?? [], $data);

            $this->dispatch('notify', [
                'message' => $this->mode === 'edit' ? 'Purchase updated successfully' : 'Purchase created successfully',
                'type' => 'success'
            ]);

            $this->dispatch('purchaseSaved');

            return $this->redirectRoute('purchases.show', ['id' => $purchase->id], navigate: true);

        } catch (\Exception $e) {
            DB::rollBack();
            
            $this->dispatch('notify', [
                'message' => 'Error: ' . $e->getMessage(),
                'type' => 'error'
            ]);
            
            Log::error('Purchase save error: ' . $e->getMessage());
        }
    }

    public function cancel()
    {
        return $this->redirectRoute('purchases.index', navigate: true);
    }

    public function getPurchaseProperty()
    {
        if ($this->purchaseId) {
            return Purchase::find($this->purchaseId);
        }
        return null;
    }

    public function render()
    {
        return view('livewire.purchases.form', [
            'purchase' => $this->purchase,
            'suppliers' => Supplier::where('is_active', true)->orderBy('name')->get(),
        ])->layout('layouts.app');
    }
}