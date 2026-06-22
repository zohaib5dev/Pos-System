<?php

namespace App\Livewire\Purchase;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\Payment;
use App\Models\PaymentMethod;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Index extends Component
{
    use WithPagination;

    // Search and Filters
    public $search = '';
    public $supplierFilter = '';
    public $statusFilter = '';
    public $paymentStatusFilter = '';
    public $dateRange = 'all';
    public $sortField = 'purchase_date';
    public $sortDirection = 'desc';
    public $perPage = 15;

    // Selection and Modals
    public $selectedPurchases = [];
    public $selectAll = false;
    public $showDeleteModal = false;
    public $showBulkDeleteModal = false;
    public $deleteId = null;
    public $purchaseToDelete = null;

    // Payment Modal
    public $showPaymentModal = false;
    public $paymentPurchaseId = null;
    public $paymentAmount = 0;
    public $paymentMethod = 'cash';
    public $paymentReference = '';
    public $paymentNotes = '';

    // Stats
    public $stats = [];

    protected $queryString = [
        'search' => ['except' => ''],
        'supplierFilter' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'paymentStatusFilter' => ['except' => ''],
        'dateRange' => ['except' => 'all'],
        'sortField' => ['except' => 'purchase_date'],
        'sortDirection' => ['except' => 'desc'],
        'perPage' => ['except' => 15],
    ];

    protected $listeners = [
        'purchaseSaved' => 'refreshList',
        'purchaseDeleted' => 'refreshList',
        'refresh' => '$refresh'
    ];

    public function mount()
    {
        $this->calculateStats();
    }

    public function refreshList()
    {
        $this->resetPage();
        $this->selectedPurchases = [];
        $this->selectAll = false;
        $this->calculateStats();
    }

    public function calculateStats()
    {
        $query = Purchase::query();

        $this->stats = [
            'total' => $query->count(),
            'draft' => $query->clone()->where('status', 'draft')->count(),
            'ordered' => $query->clone()->where('status', 'ordered')->count(),
            'partial' => $query->clone()->where('status', 'partial')->count(),
            'received' => $query->clone()->where('status', 'received')->count(),
            'cancelled' => $query->clone()->where('status', 'cancelled')->count(),
            'pending_payment' => $query->clone()->where('payment_status', 'pending')->sum(DB::raw('total_amount - paid_amount')),
            'partial_payment' => $query->clone()->where('payment_status', 'partial')->count(),
            'paid' => $query->clone()->where('payment_status', 'paid')->count(),
            'total_amount' => $query->clone()->sum('total_amount'),
            'total_paid' => $query->clone()->sum('paid_amount'),
            'total_due' => $query->clone()->sum(DB::raw('total_amount - paid_amount')),
        ];
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedSupplierFilter()
    {
        $this->resetPage();
        $this->calculateStats();
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

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function getPurchasesQuery()
    {
        return Purchase::with(['supplier'])
            ->select('*')
            ->selectRaw('(total_amount - paid_amount) as due_amount')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('purchase_number', 'like', '%' . $this->search . '%')
                        ->orWhereHas('supplier', function ($sq) {
                            $sq->where('name', 'like', '%' . $this->search . '%')
                                ->orWhere('company_name', 'like', '%' . $this->search . '%')
                                ->orWhere('email', 'like', '%' . $this->search . '%')
                                ->orWhere('phone', 'like', '%' . $this->search . '%');
                        });
                });
            })
            ->when($this->supplierFilter, fn($q) => $q->where('supplier_id', $this->supplierFilter))
            ->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter))
            ->when($this->paymentStatusFilter, fn($q) => $q->where('payment_status', $this->paymentStatusFilter))
            ->when($this->dateRange !== 'all', function ($query) {
                switch ($this->dateRange) {
                    case 'today':
                        return $query->whereDate('purchase_date', now()->toDateString());
                    case 'week':
                        return $query->whereBetween('purchase_date', [now()->startOfWeek(), now()->endOfWeek()]);
                    case 'month':
                        return $query->whereMonth('purchase_date', now()->month);
                    case 'year':
                        return $query->whereYear('purchase_date', now()->year);
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
        return $this->getPurchasesQuery()
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage)
            ->pluck('id')
            ->map(fn($id) => (string) $id)
            ->toArray();
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedPurchases = $this->getCurrentPageIds();
        } else {
            $this->selectedPurchases = [];
        }
    }

    public function updatedSelectedpselectedPurchases()
    {
        $currentPageIds = $this->getCurrentPageIds();
        $selectedIds = $this->selectedPurchases;

        sort($currentPageIds);
        sort($selectedIds);
        $this->selectAll = $currentPageIds === $selectedIds && !empty($currentPageIds);
    }

    public function confirmDelete($id)
    {
        $this->deleteId = $id;
        $this->purchaseToDelete = Purchase::withCount(['items', 'payments'])->find($id);
        $this->showDeleteModal = true;
    }

    public function deletePurchase()
    {
        if (!$this->deleteId) {
            $this->dispatch('notify', [
                'message' => 'No purchase selected for deletion',
                'type' => 'error'
            ]);
            $this->showDeleteModal = false;
            return;
        }

        $purchase = Purchase::with(['items', 'payments'])->find($this->deleteId);

        if (!$purchase) {
            $this->dispatch('notify', [
                'message' => 'Purchase not found',
                'type' => 'error'
            ]);
            $this->showDeleteModal = false;
            $this->resetDeleteState();
            return;
        }

        // Check if purchase has payments
        if ($purchase->payments()->count() > 0) {
            $this->dispatch('notify', [
                'message' => 'Cannot delete purchase with ' . $purchase->payments()->count() . ' payments.',
                'type' => 'error'
            ]);
            $this->showDeleteModal = false;
            $this->resetDeleteState();
            return;
        }

        // Check if items have been received
        foreach ($purchase->items as $item) {
            if ($item->received_quantity > 0) {
                $this->dispatch('notify', [
                    'message' => 'Cannot delete purchase with received items.',
                    'type' => 'error'
                ]);
                $this->showDeleteModal = false;
                $this->resetDeleteState();
                return;
            }
        }

        try {
            $purchaseData = collect($purchase->toArray())->except(['updated_at'])->toArray();
            
            // Delete items first
            $purchase->items()->delete();
            $purchase->delete();

            $this->showDeleteModal = false;

            // Log activity
            logActivity('deleted', $purchase, $purchaseData, []);

            $this->dispatch('notify', [
                'message' => 'Purchase "' . $purchase->purchase_number . '" deleted successfully',
                'type' => 'success'
            ]);

            $this->resetDeleteState();
            $this->calculateStats();
            $this->dispatch('purchaseDeleted');

        } catch (\Exception $e) {
            $this->showDeleteModal = false;
            $this->resetDeleteState();
            $this->dispatch('notify', [
                'message' => 'Error deleting purchase: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    public function confirmBulkDelete()
    {
        if (empty($this->selectedPurchases)) {
            $this->dispatch('notify', [
                'message' => 'No purchases selected',
                'type' => 'error'
            ]);
            return;
        }
        $this->showBulkDeleteModal = true;
    }

    public function bulkDelete()
    {
        if (empty($this->selectedPurchases)) {
            $this->showBulkDeleteModal = false;
            return;
        }

        DB::beginTransaction();

        try {
            $purchases = Purchase::with(['items', 'payments'])->whereIn('id', $this->selectedPurchases)->get();
            
            // Check if any purchase has payments or received items
            foreach ($purchases as $purchase) {
                if ($purchase->payments()->count() > 0) {
                    $this->dispatch('notify', [
                        'message' => 'Some purchases have payments and cannot be deleted.',
                        'type' => 'error'
                    ]);
                    $this->showBulkDeleteModal = false;
                    DB::rollBack();
                    return;
                }

                foreach ($purchase->items as $item) {
                    if ($item->received_quantity > 0) {
                        $this->dispatch('notify', [
                            'message' => 'Some purchases have received items and cannot be deleted.',
                            'type' => 'error'
                        ]);
                        $this->showBulkDeleteModal = false;
                        DB::rollBack();
                        return;
                    }
                }
            }

            // Delete items and purchases
            foreach ($purchases as $purchase) {
                $purchase->items()->delete();
            }

            $deletedCount = Purchase::whereIn('id', $this->selectedPurchases)->delete();

            DB::commit();

            $this->selectedPurchases = [];
            $this->selectAll = false;
            $this->showBulkDeleteModal = false;

            $this->dispatch('notify', [
                'message' => $deletedCount . ' purchases deleted successfully',
                'type' => 'success'
            ]);

            $this->calculateStats();
            $this->dispatch('purchaseDeleted');

        } catch (\Exception $e) {
            DB::rollBack();

            $this->dispatch('notify', [
                'message' => 'Error deleting purchases: ' . $e->getMessage(),
                'type' => 'error'
            ]);
            $this->showBulkDeleteModal = false;
        }
    }

    public function updateStatus($id, $status)
    {
        try {
            $purchase = Purchase::find($id);
            
            if (!$purchase) {
                $this->dispatch('notify', [
                    'message' => 'Purchase not found',
                    'type' => 'error'
                ]);
                return;
            }
            
            $oldStatus = $purchase->status;
            $purchase->status = $status;
            $purchase->save();

            // Log activity
            logActivity(
                'status_updated',
                $purchase,
                ['status' => $oldStatus],
                ['status' => $status]
            );

            $this->dispatch('notify', [
                'message' => 'Purchase status updated successfully',
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

    public function openPaymentModal($id)
    {
        $this->paymentPurchaseId = $id;
        $purchase = Purchase::find($id);
        
        if (!$purchase) {
            $this->dispatch('notify', [
                'message' => 'Purchase not found',
                'type' => 'error'
            ]);
            return;
        }
        
        // Calculate due amount
        $dueAmount = $purchase->total_amount - $purchase->paid_amount;
        
        if ($dueAmount <= 0) {
            $this->dispatch('notify', [
                'message' => 'This purchase has no outstanding balance',
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
            $purchase = Purchase::find($this->paymentPurchaseId);
            
            if (!$purchase) {
                $this->dispatch('notify', [
                    'message' => 'Purchase not found',
                    'type' => 'error'
                ]);
                return;
            }
            
            $dueAmount = $purchase->total_amount - $purchase->paid_amount;
            $oldPaidAmount = $purchase->paid_amount;
            $oldPaymentStatus = $purchase->payment_status;

            if ($this->paymentAmount > $dueAmount) {
                $this->dispatch('notify', [
                    'message' => 'Payment amount cannot exceed due amount',
                    'type' => 'error'
                ]);
                return;
            }

            // Get or create payment method
            $paymentMethod = PaymentMethod::firstOrCreate(
                ['slug' => $this->paymentMethod],
                ['name' => ucfirst($this->paymentMethod), 'is_active' => true]
            );

            // Create payment
            $payment = Payment::create([
                'payment_number' => 'PAY-' . time() . '-' . rand(1000, 9999),
                'purchase_id' => $purchase->id,
                'supplier_id' => $purchase->supplier_id,
                'payment_method_id' => $paymentMethod->id,
                'payment_type' => 'purchase',
                'payment_date' => now(),
                'amount' => $this->paymentAmount,
                'reference_number' => $this->paymentReference,
                'notes' => $this->paymentNotes,
                'created_by' => auth()->id(),
            ]);

            // Update purchase paid amount
            $purchase->paid_amount += $this->paymentAmount;

            // Calculate new due amount
            $newDueAmount = $purchase->total_amount - $purchase->paid_amount;

            if ($newDueAmount <= 0) {
                $purchase->payment_status = 'paid';
            } else {
                $purchase->payment_status = 'partial';
            }

            $purchase->save();

            DB::commit();

            // Log activity
            logActivity(
                'payment_processed',
                $purchase,
                [
                    'paid_amount' => $oldPaidAmount,
                    'payment_status' => $oldPaymentStatus
                ],
                [
                    'paid_amount' => $purchase->paid_amount,
                    'payment_status' => $purchase->payment_status,
                    'payment_amount' => $this->paymentAmount,
                    'payment_method' => $this->paymentMethod,
                    'payment_reference' => $this->paymentReference,
                    'payment_id' => $payment->id
                ]
            );

            $this->showPaymentModal = false;
            $this->reset(['paymentAmount', 'paymentReference', 'paymentNotes', 'paymentPurchaseId']);

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

    private function resetDeleteState()
    {
        $this->deleteId = null;
        $this->purchaseToDelete = null;
    }

    public function render()
    {
        $purchases = $this->getPurchasesQuery()
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.purchases.index', [
            'purchases' => $purchases,
            'suppliers' => Supplier::select('id', 'name', 'company_name')->where('is_active', true)->orderBy('name')->get(),
        ])->layout('layouts.app');
    }
}