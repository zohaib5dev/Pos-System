<?php

namespace App\Livewire\Order;

use Livewire\Component;
use App\Models\Order;
use App\Models\Customer;
use App\Models\Product;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Form extends Component
{
    // Mode
    public $mode = 'create';
    public $orderId = null;

    // Form Fields
    public $customer_id = '';
    public $customer_name = '';
    public $customer_phone = '';
    public $customer_email = '';
    public $customer_address = '';
    public $order_number = '';
    public $order_date;
    public $status = 'pending';
    public $payment_status = 'pending';
    public $subtotal = 0;
    public $tax_amount = 0;
    public $discount_type = 'fixed';
    public $discount_value = 0;
    public $discount_amount = 0;
    public $shipping_cost = 0;
    public $total_amount = 0;
    public $paid_amount = 0;
    public $due_amount = 0;
    public $notes = '';

    // Order Items
    public $items = [];
    public $showProductModal = false;
    public $productSearch = '';

    protected function rules()
    {
        $rules = [
            'customer_id' => 'required|exists:customers,id',
            'order_date' => 'required|date',
            'status' => 'required|in:pending,processing,completed,cancelled,refunded',
            'notes' => 'nullable|max:1000',
        ];

        if ($this->mode === 'edit' && $this->orderId) {
            $rules['order_number'] = 'required|unique:orders,order_number,' . $this->orderId;
        } else {
            $rules['order_number'] = 'required|unique:orders,order_number';
        }

        return $rules;
    }

    public function mount($id = null)
    {
        $this->order_date = now()->format('Y-m-d');

        if ($id) {
            $this->mode = 'edit';
            $this->orderId = $id;
            $this->loadOrder();
        } else {
            $this->generateOrderNumber();
        }
    }

    public function generateOrderNumber()
    {
        $this->order_number = 'ORD-' . date('Ymd') . '-' . str_pad(Order::count() + 1, 4, '0', STR_PAD_LEFT);
    }

    public function loadOrder()
    {
        $order = Order::with(['items.product'])->find($this->orderId);

        if ($order) {
            $this->customer_id = $order->customer_id;
            $this->customer_name = $order->customer_name;
            $this->customer_phone = $order->customer_phone;
            $this->customer_email = $order->customer_email;
            $this->customer_address = $order->customer_address;
            $this->order_number = $order->order_number;
            $this->order_date = $order->order_date->format('Y-m-d');
            $this->status = $order->status;
            $this->payment_status = $order->payment_status;
            $this->subtotal = $order->subtotal;
            $this->tax_amount = $order->tax_amount;
            $this->discount_type = $order->discount_type;
            $this->discount_value = $order->discount_value;
            $this->discount_amount = $order->discount_amount;
            $this->shipping_cost = $order->shipping_cost;
            $this->total_amount = $order->total_amount;
            $this->paid_amount = $order->paid_amount;
            $this->due_amount = $order->due_amount;
            $this->notes = $order->notes;

            // Load items
            $this->items = $order->items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'product_id' => $item->product_id,
                    'product_name' => $item->product_name,
                    'sku' => $item->sku,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'total' => $item->total,
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

    public function getCustomerProperty()
    {
        if ($this->customer_id) {
            return Customer::find($this->customer_id);
        }
        return null;
    }

    public function updatedCustomerId($value)
    {
        if ($value) {
            $customer = Customer::find($value);
            if ($customer) {
                $this->customer_name = $customer->name;
                $this->customer_phone = $customer->phone;
                $this->customer_email = $customer->email;
                $this->customer_address = $customer->address;
            }
        }
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
                'unit_price' => $product->selling_price ?? 0,
                'total' => $product->selling_price ?? 0,
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

    public function updateItemPrice($index, $price)
    {
        $this->items[$index]['unit_price'] = $price;
        $this->calculateItemTotal($index);
        $this->calculateTotals();
    }

    public function calculateItemTotal($index)
    {
        $item = &$this->items[$index];
        $item['total'] = $item['unit_price'] * $item['quantity'];
    }

    public function calculateTotals()
    {
        $this->subtotal = collect($this->items)->sum('total');
        $this->tax_amount = 0; // Calculate tax if needed

        // Calculate discount
        if ($this->discount_type === 'percentage') {
            $this->discount_amount = ($this->subtotal * $this->discount_value) / 100;
        } else {
            $this->discount_amount = $this->discount_value;
        }

        $this->total_amount = $this->subtotal - $this->discount_amount + $this->tax_amount + $this->shipping_cost;
        $this->due_amount = $this->total_amount - $this->paid_amount;
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

    public function saveOrder()
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
                'customer_id' => $this->customer_id,
                'customer_name' => $this->customer_name,
                'customer_phone' => $this->customer_phone,
                'customer_email' => $this->customer_email,
                'customer_address' => $this->customer_address,
                'order_number' => $this->order_number,
                'order_date' => $this->order_date,
                'status' => $this->status,
                'payment_status' => $this->payment_status,
                'subtotal' => $this->subtotal,
                'tax_amount' => $this->tax_amount,
                'discount_type' => $this->discount_type,
                'discount_value' => $this->discount_value,
                'discount_amount' => $this->discount_amount,
                'shipping_cost' => $this->shipping_cost,
                'total_amount' => $this->total_amount,
                'paid_amount' => $this->paid_amount,
                'due_amount' => $this->due_amount,
                'notes' => $this->notes,
            ];

            if ($this->mode === 'edit') {
                $order = Order::find($this->orderId);

                if (!$order) {
                    $this->dispatch('notify', [
                        'message' => 'Order not found',
                        'type' => 'error'
                    ]);
                    return;
                }

                $oldData = collect($order->toArray())->except(['updated_at'])->toArray();
                $order->update($data);

                // Delete old items
                $order->items()->delete();
            } else {
                $data['created_by'] = auth()->id();
                $order = Order::create($data);
                $this->orderId = $order->id;
            }

            // Create new items
            foreach ($this->items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'product_name' => $item['product_name'],
                    'sku' => $item['sku'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total' => $item['total'],
                ]);
            }

            DB::commit();

            // Log activity
            $action = $this->mode === 'edit' ? 'updated' : 'created';
            logActivity($action, $order, $oldData ?? [], $data);

            $this->dispatch('notify', [
                'message' => $this->mode === 'edit' ? 'Order updated successfully' : 'Order created successfully',
                'type' => 'success'
            ]);

            $this->dispatch('orderSaved');

            return redirect()->route('orders.show', ['id' => $order->id]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            $this->dispatch('notify', [
                'message' => 'Error: ' . $e->getMessage(),
                'type' => 'error'
            ]);
            
            Log::error('Order save error: ' . $e->getMessage());
        }
    }

    public function cancel()
    {
        return redirect()->route('orders.index');
    }

    public function getOrderProperty()
    {
        if ($this->orderId) {
            return Order::find($this->orderId);
        }
        return null;
    }

    public function render()
    {
        return view('livewire.orders.form', [
            'order' => $this->order,
            'customers' => Customer::where('is_active', true)->orderBy('name')->get(),
        ])->layout('layouts.app');
    }
}