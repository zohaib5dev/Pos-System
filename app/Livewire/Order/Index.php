<?php

namespace App\Livewire\Order;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Order;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\BusinessSetting;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Index extends Component
{
    use WithPagination;

    // Search and Filters
    public $search = '';
    public $statusFilter = '';
    public $paymentStatusFilter = '';
    public $dateRange = 'all';
    public $customerFilter = '';
    public $sortField = 'order_number';
    public $sortDirection = 'desc';
    public $perPage = 15;

    // Selection and Modals
    public $selectedOrders = [];
    public $selectAll = false;
    public $showDeleteModal = false;
    public $showBulkDeleteModal = false;
    public $deleteId = null;
    public $selectedOrderForReceipt = null;

    // Payment Modal
    public $showPaymentModal = false;
    public $paymentOrderId = null;
    public $paymentAmount = 0;
    public $paymentMethod = 'cash';
    public $paymentReference = '';
    public $paymentNotes = '';

    // Refund Modal
    public $showRefundModal = false;
    public $refundOrderId = null;
    public $refundAmount = 0;
    public $refundReason = '';
    public $refundItems = [];
    public $selectedRefundItems = [];

    // Receipt Modal
    public $showReceiptModal = false;
    public $lastOrder = null;

    // Stats
    public $stats = [];

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'paymentStatusFilter' => ['except' => ''],
        'dateRange' => ['except' => 'all'],
        'customerFilter' => ['except' => ''],
        'sortField' => ['except' => 'order_number'],
        'sortDirection' => ['except' => 'desc'],
        'perPage' => ['except' => 15],
    ];

    protected $listeners = [
        'orderSaved' => 'refreshList',
        'orderDeleted' => 'refreshList',
        'refresh' => '$refresh'
    ];

    public function mount()
    {
        $this->calculateStats();
    }

    public function refreshList()
    {
        $this->resetPage();
        $this->selectedOrders = [];
        $this->selectAll = false;
        $this->calculateStats();
    }

    public function calculateStats()
    {
        $query = Order::query();

        $this->stats = [
            'total' => $query->count(),
            'pending' => $query->clone()->where('status', 'pending')->count(),
            'processing' => $query->clone()->where('status', 'processing')->count(),
            'completed' => $query->clone()->where('status', 'completed')->count(),
            'cancelled' => $query->clone()->where('status', 'cancelled')->count(),
            'refunded' => $query->clone()->where('status', 'refunded')->count(),
            'today' => $query->clone()->whereDate('created_at', now())->count(),
            'revenue' => $query->clone()->where('status', 'completed')->sum('total_amount'),
            'pending_payment' => $query->clone()->where('payment_status', 'pending')->sum('due_amount'),
            'partial_payment' => $query->clone()->where('payment_status', 'partial')->count(),
            'paid' => $query->clone()->where('payment_status', 'paid')->count(),
        ];
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
        $this->calculateStats();
    }

    public function updatedPaymentStatusFilter()
    {
        $this->resetPage();
        $this->calculateStats();
    }

    public function updatedDateRange()
    {
        $this->resetPage();
        $this->calculateStats();
    }

    public function updatedCustomerFilter()
    {
        $this->resetPage();
        $this->calculateStats();
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function getOrdersQuery()
    {
        return Order::with(['customer'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('order_number', 'like', '%' . $this->search . '%')
                        ->orWhere('customer_name', 'like', '%' . $this->search . '%')
                        ->orWhere('customer_phone', 'like', '%' . $this->search . '%')
                        ->orWhere('invoice_number', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter))
            ->when($this->paymentStatusFilter, fn($q) => $q->where('payment_status', $this->paymentStatusFilter))
            ->when($this->customerFilter, fn($q) => $q->where('customer_id', $this->customerFilter))
            ->when($this->dateRange !== 'all', function ($query) {
                switch ($this->dateRange) {
                    case 'today':
                        return $query->whereDate('created_at', now());
                    case 'week':
                        return $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                    case 'month':
                        return $query->whereMonth('created_at', now()->month);
                    case 'year':
                        return $query->whereYear('created_at', now()->year);
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
        return $this->getOrdersQuery()
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage)
            ->pluck('id')
            ->map(fn($id) => (string) $id)
            ->toArray();
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedOrders = $this->getCurrentPageIds();
        } else {
            $this->selectedOrders = [];
        }
    }

    public function updatedSelectedOselectedOrders()
    {
        $currentPageIds = $this->getCurrentPageIds();
        $selectedIds = $this->selectedOrders;

        sort($currentPageIds);
        sort($selectedIds);
        $this->selectAll = $currentPageIds === $selectedIds && !empty($currentPageIds);
    }

    public function confirmDelete($id)
    {
        $this->deleteId = $id;
        $this->selectedOrderForReceipt = Order::with(['customer', 'items', 'payments'])->find($id);
        $this->showDeleteModal = true;
    }

    public function deleteOrder()
    {
        if (!$this->deleteId) {
            $this->dispatch('notify', [
                'message' => 'No order selected for deletion',
                'type' => 'error'
            ]);
            $this->showDeleteModal = false;
            return;
        }

        $order = Order::with(['items', 'payments'])->find($this->deleteId);

        if (!$order) {
            $this->dispatch('notify', [
                'message' => 'Order not found',
                'type' => 'error'
            ]);
            $this->showDeleteModal = false;
            return;
        }

        // Check if order has payments
        if ($order->payments()->count() > 0) {
            $this->dispatch('notify', [
                'message' => 'Cannot delete order with payments.',
                'type' => 'error'
            ]);
            $this->showDeleteModal = false;
            return;
        }

        DB::beginTransaction();

        try {
            // Restore stock
            foreach ($order->items as $item) {
                if ($item->product) {
                    $item->product->stock_quantity += $item->quantity;
                    $item->product->save();
                }
            }

            // Delete order items
            $order->items()->delete();
            $order->delete();

            DB::commit();

            $this->showDeleteModal = false;
            $this->selectedOrderForReceipt = null;

            $this->dispatch('notify', [
                'message' => 'Order deleted successfully',
                'type' => 'success'
            ]);

            $this->calculateStats();
            $this->dispatch('orderDeleted');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('notify', [
                'message' => 'Error deleting order: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
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

            $this->calculateStats();

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

            $this->calculateStats();

        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'message' => 'Error updating payment status: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    public function openPaymentModal($id)
    {
        $this->paymentOrderId = $id;
        $order = Order::find($id);
        
        if (!$order) {
            $this->dispatch('notify', [
                'message' => 'Order not found',
                'type' => 'error'
            ]);
            return;
        }
        
        $dueAmount = $order->total_amount - $order->paid_amount;
        
        if ($dueAmount <= 0) {
            $this->dispatch('notify', [
                'message' => 'This order has no outstanding balance',
                'type' => 'error'
            ]);
            return;
        }
        
        $this->paymentAmount = $dueAmount;
        $this->showPaymentModal = true;
    }

    public function processPayment()
    {
        $this->validate([
            'paymentAmount' => 'required|numeric|min:0.01',
            'paymentMethod' => 'required',
        ]);

        DB::beginTransaction();

        try {
            $order = Order::find($this->paymentOrderId);
            
            if (!$order) {
                $this->dispatch('notify', [
                    'message' => 'Order not found',
                    'type' => 'error'
                ]);
                return;
            }
            
            $dueAmount = $order->total_amount - $order->paid_amount;
            $oldPaidAmount = $order->paid_amount;
            $oldPaymentStatus = $order->payment_status;

            if ($this->paymentAmount > $dueAmount) {
                $this->dispatch('notify', [
                    'message' => 'Payment amount cannot exceed due amount',
                    'type' => 'error'
                ]);
                return;
            }

            $paymentMethod = PaymentMethod::firstOrCreate(
                ['slug' => $this->paymentMethod],
                ['name' => ucfirst($this->paymentMethod), 'is_active' => true]
            );

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
                    'payment_status' => $oldPaymentStatus
                ],
                [
                    'paid_amount' => $order->paid_amount,
                    'payment_status' => $order->payment_status,
                    'payment_amount' => $this->paymentAmount,
                    'payment_method' => $this->paymentMethod,
                    'payment_reference' => $this->paymentReference,
                    'payment_id' => $payment->id
                ]
            );

            $this->showPaymentModal = false;
            $this->reset(['paymentAmount', 'paymentReference', 'paymentNotes', 'paymentOrderId']);

            $this->dispatch('notify', [
                'message' => 'Payment processed successfully',
                'type' => 'success'
            ]);

            $this->calculateStats();
            $this->dispatch('refresh');

        } catch (\Exception $e) {
            DB::rollBack();
            
            $this->dispatch('notify', [
                'message' => 'Error processing payment: ' . $e->getMessage(),
                'type' => 'error'
            ]);
            
            Log::error('Payment error: ' . $e->getMessage());
        }
    }

    public function openRefundModal($id)
    {
        $this->refundOrderId = $id;
        $order = Order::with(['items.product'])->find($id);
        $this->selectedOrderForReceipt = $order;
        
        if (!$order) {
            $this->dispatch('notify', [
                'message' => 'Order not found',
                'type' => 'error'
            ]);
            return;
        }

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
                'product_sku' => $item->product->sku ?? '',
                'quantity' => $item->quantity,
                'refund_quantity' => 0,
                'unit_price' => $item->unit_price,
                'total' => $item->total,
            ];
        })->toArray();

        $this->showRefundModal = true;
        $this->dispatch('open-refund-modal');
    }

    public function selectAllItemsForRefund()
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

        $order = Order::find($this->refundOrderId);

        if (!$order) {
            $this->dispatch('notify', [
                'message' => 'Order not found',
                'type' => 'error'
            ]);
            return;
        }

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

            $paymentMethod = PaymentMethod::firstOrCreate(
                ['slug' => 'refund'],
                ['name' => 'Refund', 'is_active' => true]
            );

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

            $order->paid_amount -= $this->refundAmount;
            $order->due_amount = $order->total_amount - $order->paid_amount;

            if ($order->paid_amount <= 0) {
                $order->payment_status = 'refunded';
                $order->status = 'refunded';
            } else {
                $order->payment_status = 'partial';
            }

            $order->save();

            $refundedItems = [];
            foreach ($this->refundItems as $item) {
                if (in_array($item['id'], $this->selectedRefundItems) && ($item['refund_quantity'] ?? 0) > 0) {
                    $orderItem = OrderItem::find($item['id']);
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
            $this->reset(['refundAmount', 'refundReason', 'refundItems', 'selectedRefundItems', 'refundOrderId', 'selectedOrderForReceipt']);

            $this->dispatch('notify', [
                'message' => 'Refund processed successfully',
                'type' => 'success'
            ]);

            $this->calculateStats();
            $this->dispatch('refresh');
            $this->dispatch('close-refund-modal');

        } catch (\Exception $e) {
            DB::rollBack();

            $this->dispatch('notify', [
                'message' => 'Error processing refund: ' . $e->getMessage(),
                'type' => 'error'
            ]);
            
            Log::error('Refund error: ' . $e->getMessage());
        }
    }

    public function viewReceipt($id)
    {
        $order = Order::with(['customer', 'items', 'payments.method'])->find($id);
        
        if (!$order) {
            $this->dispatch('notify', [
                'message' => 'Order not found',
                'type' => 'error'
            ]);
            return;
        }
        
        $this->lastOrder = $order;
        $this->showReceiptModal = true;
    }

    public function printReceipt($id)
    {
        $order = Order::find($id);
        
        if ($order) {
            logActivity('printed_receipt', $order, [], [
                'order_number' => $order->order_number,
                'customer_name' => $order->customer_name
            ]);
        }
        
        $this->dispatch('print-receipt', orderId: $id);
    }

    public function render()
    {
        $orders = $this->getOrdersQuery()
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.orders.index', [
            'orders' => $orders,
            'customers' => Customer::select('id', 'name')->orderBy('name')->get(),
            'settings' => BusinessSetting::first(),
        ])->layout('layouts.app');
    }
}