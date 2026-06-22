<?php

namespace App\Livewire\Order;

use Livewire\Component;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\BusinessSetting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Show extends Component
{
    public $orderId;
    public $order;
    public $settings;

    // Payment Modal
    public $showPaymentModal = false;
    public $paymentAmount = 0;
    public $paymentMethod = 'cash';
    public $paymentReference = '';
    public $paymentNotes = '';

    // Refund Modal
    public $showRefundModal = false;
    public $refundAmount = 0;
    public $refundReason = '';
    public $refundItems = [];
    public $selectedRefundItems = [];

    public function mount($id)
    {
        $this->orderId = $id;
        $this->loadOrder();
        $this->settings = BusinessSetting::first();
    }

    public function loadOrder()
    {
        $this->order = Order::with(['customer', 'items.product', 'payments.method', 'creator'])
            ->find($this->orderId);

        if (!$this->order) {
            session()->flash('error', 'Order not found');
            return $this->redirectRoute('orders.index', navigate: true);
        }
    }

    public function goBack()
    {
        return $this->redirectRoute('orders.index', navigate: true);
    }

    public function updateStatus($id, $status)
    {
        try {
            $order = Order::find($id);
            
            if (!$order) {
                $this->dispatch('notify', [
                    'message' => 'Order not found',
                    'type' => 'error'
                ]);
                return;
            }
            
            $oldStatus = $order->status;
            $order->status = $status;
            $order->save();

            logActivity(
                'status_updated',
                $order,
                ['status' => $oldStatus],
                ['status' => $status]
            );

            $this->dispatch('notify', [
                'message' => 'Order status updated successfully',
                'type' => 'success'
            ]);

            // Refresh order
            $this->loadOrder();

        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'message' => 'Error updating status: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    public function updatePaymentStatus($id, $status)
    {
        try {
            $order = Order::find($id);
            
            if (!$order) {
                $this->dispatch('notify', [
                    'message' => 'Order not found',
                    'type' => 'error'
                ]);
                return;
            }
            
            $oldStatus = $order->payment_status;
            $order->payment_status = $status;
            $order->save();

            logActivity(
                'payment_status_updated',
                $order,
                ['payment_status' => $oldStatus],
                ['payment_status' => $status]
            );

            $this->dispatch('notify', [
                'message' => 'Payment status updated successfully',
                'type' => 'success'
            ]);

            // Refresh order
            $this->loadOrder();

        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'message' => 'Error updating payment status: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    public function openPaymentModal()
    {
        $this->paymentAmount = $this->order->due_amount;
        $this->showPaymentModal = true;
    }

    public function processPayment()
    {
        $this->validate([
            'paymentAmount' => 'required|numeric|min:0.01',
            'paymentMethod' => 'required',
        ]);

        $order = $this->order;

        if ($this->paymentAmount > $order->due_amount) {
            $this->dispatch('notify', [
                'message' => 'Payment amount cannot exceed due amount',
                'type' => 'error'
            ]);
            return;
        }

        DB::beginTransaction();

        try {
            $oldPaidAmount = $order->paid_amount;
            $oldDueAmount = $order->due_amount;
            $oldPaymentStatus = $order->payment_status;

            // Get or create payment method
            $paymentMethod = PaymentMethod::firstOrCreate(
                ['slug' => $this->paymentMethod],
                ['name' => ucfirst($this->paymentMethod), 'is_active' => true]
            );

            // Create payment
            $payment = Payment::create([
                'payment_number' => 'PAY-' . time() . '-' . rand(1000, 9999),
                'order_id' => $order->id,
                'customer_id' => $order->customer_id,
                'payment_method_id' => $paymentMethod->id,
                'payment_type' => 'sale',
                'payment_date' => now(),
                'amount' => $this->paymentAmount,
                'reference_number' => $this->paymentReference,
                'notes' => $this->paymentNotes,
                'created_by' => auth()->id(),
            ]);

            // Update order
            $order->paid_amount += $this->paymentAmount;
            $order->due_amount = $order->total_amount - $order->paid_amount;

            if ($order->due_amount <= 0) {
                $order->payment_status = 'paid';
            } else {
                $order->payment_status = 'partial';
            }

            $order->save();

            DB::commit();

            logActivity(
                'payment_processed',
                $order,
                [
                    'paid_amount' => $oldPaidAmount,
                    'due_amount' => $oldDueAmount,
                    'payment_status' => $oldPaymentStatus
                ],
                [
                    'paid_amount' => $order->paid_amount,
                    'due_amount' => $order->due_amount,
                    'payment_status' => $order->payment_status,
                    'payment_amount' => $this->paymentAmount,
                    'payment_method' => $this->paymentMethod,
                    'payment_reference' => $this->paymentReference,
                    'payment_id' => $payment->id
                ]
            );

            $this->showPaymentModal = false;
            $this->reset(['paymentAmount', 'paymentReference', 'paymentNotes']);

            $this->dispatch('notify', [
                'message' => 'Payment processed successfully',
                'type' => 'success'
            ]);

            // Refresh order
            $this->loadOrder();

        } catch (\Exception $e) {
            DB::rollBack();
            
            $this->dispatch('notify', [
                'message' => 'Error processing payment: ' . $e->getMessage(),
                'type' => 'error'
            ]);
            
            Log::error('Payment error: ' . $e->getMessage());
        }
    }

    public function openRefundModal()
    {
        $order = $this->order;

        if ($order->paid_amount <= 0) {
            $this->dispatch('notify', [
                'message' => 'This order has no paid amount to refund',
                'type' => 'error'
            ]);
            return;
        }

        $this->refundAmount = 0;
        $this->refundReason = '';
        $this->selectedRefundItems = [];
        
        $this->refundItems = $order->items->map(function ($item) {
            return [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'product_name' => $item->product_name,
                'sku' => $item->sku ?? '',
                'quantity' => $item->quantity,
                'refund_quantity' => 0,
                'unit_price' => $item->unit_price,
                'total' => $item->total,
            ];
        })->toArray();

        $this->showRefundModal = true;
    }

    public function selectAllItems()
    {
        if (count($this->selectedRefundItems) === count($this->refundItems) && count($this->refundItems) > 0) {
            $this->selectedRefundItems = [];
            foreach ($this->refundItems as $index => $item) {
                $this->refundItems[$index]['refund_quantity'] = 0;
            }
        } else {
            $this->selectedRefundItems = collect($this->refundItems)
                ->pluck('id')
                ->map(fn($id) => (string) $id)
                ->toArray();
            foreach ($this->refundItems as $index => $item) {
                $this->refundItems[$index]['refund_quantity'] = $item['quantity'];
            }
        }
        $this->calculateRefundAmount();
    }

    public function updatedSelectedRefundItems()
    {
        $this->calculateRefundAmount();
    }

    public function updatedRefundItems()
    {
        $this->calculateRefundAmount();
    }

    public function calculateRefundAmount()
    {
        $total = 0;
        foreach ($this->refundItems as $item) {
            if (in_array($item['id'], $this->selectedRefundItems)) {
                $refundQty = $item['refund_quantity'] ?? 0;
                $total += $item['unit_price'] * $refundQty;
            }
        }
        $this->refundAmount = $total;
    }

    public function processRefund()
    {
        $this->validate([
            'refundAmount' => 'required|numeric|min:0.01',
            'refundReason' => 'required|min:5',
        ]);

        if (empty($this->selectedRefundItems)) {
            $this->dispatch('notify', [
                'message' => 'Please select items to refund',
                'type' => 'error'
            ]);
            return;
        }

        $order = $this->order;

        if ($this->refundAmount > $order->paid_amount) {
            $this->dispatch('notify', [
                'message' => 'Refund amount cannot exceed paid amount',
                'type' => 'error'
            ]);
            return;
        }

        DB::beginTransaction();

        try {
            $oldPaidAmount = $order->paid_amount;
            $oldDueAmount = $order->due_amount;
            $oldPaymentStatus = $order->payment_status;
            $oldStatus = $order->status;

            // Get or create refund payment method
            $paymentMethod = PaymentMethod::firstOrCreate(
                ['slug' => 'refund'],
                ['name' => 'Refund', 'is_active' => true]
            );

            // Create refund payment (negative amount)
            $payment = Payment::create([
                'payment_number' => 'REF-' . time() . '-' . rand(1000, 9999),
                'order_id' => $order->id,
                'customer_id' => $order->customer_id,
                'payment_method_id' => $paymentMethod->id,
                'payment_type' => 'refund',
                'payment_date' => now(),
                'amount' => -$this->refundAmount,
                'notes' => 'Refund: ' . $this->refundReason,
                'created_by' => auth()->id(),
            ]);

            // Update order
            $order->paid_amount -= $this->refundAmount;
            $order->due_amount = $order->total_amount - $order->paid_amount;

            if ($order->paid_amount <= 0) {
                $order->payment_status = 'refunded';
                $order->status = 'refunded';
            } else {
                $order->payment_status = 'partial';
            }

            $order->save();

            // Update stock for refunded items
            $refundedItems = [];
            foreach ($this->refundItems as $item) {
                if (in_array($item['id'], $this->selectedRefundItems) && ($item['refund_quantity'] ?? 0) > 0) {
                    $orderItem = \App\Models\OrderItem::find($item['id']);
                    if ($orderItem && $orderItem->product) {
                        $orderItem->product->stock_quantity += $item['refund_quantity'];
                        $orderItem->product->save();
                        
                        $refundedItems[] = [
                            'product_id' => $orderItem->product_id,
                            'product_name' => $item['product_name'],
                            'quantity' => $item['refund_quantity'],
                            'amount' => $item['unit_price'] * $item['refund_quantity']
                        ];
                    }
                }
            }

            DB::commit();

            logActivity(
                'refund_processed',
                $order,
                [
                    'paid_amount' => $oldPaidAmount,
                    'due_amount' => $oldDueAmount,
                    'payment_status' => $oldPaymentStatus,
                    'status' => $oldStatus
                ],
                [
                    'paid_amount' => $order->paid_amount,
                    'due_amount' => $order->due_amount,
                    'payment_status' => $order->payment_status,
                    'status' => $order->status,
                    'refund_amount' => $this->refundAmount,
                    'refund_reason' => $this->refundReason,
                    'refunded_items' => $refundedItems
                ]
            );

            $this->showRefundModal = false;
            $this->reset(['refundAmount', 'refundReason', 'refundItems', 'selectedRefundItems']);

            $this->dispatch('notify', [
                'message' => 'Refund processed successfully',
                'type' => 'success'
            ]);

            // Refresh order
            $this->loadOrder();

        } catch (\Exception $e) {
            DB::rollBack();

            $this->dispatch('notify', [
                'message' => 'Error processing refund: ' . $e->getMessage(),
                'type' => 'error'
            ]);
            
            Log::error('Refund error: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.orders.show', [
            'order' => $this->order,
            'settings' => $this->settings,
        ])->layout('layouts.app');
    }
}