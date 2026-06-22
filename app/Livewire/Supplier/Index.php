<?php

namespace App\Livewire\Supplier;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Index extends Component
{
    use WithPagination;

    // Search and Filters
    public $search = '';
    public $statusFilter = '';
    public $sortField = 'name';
    public $sortDirection = 'asc';
    public $perPage = 15;

    // Selection and Modals
    public $selectedSuppliers = [];
    public $selectAll = false;
    public $showDeleteModal = false;
    public $showBulkDeleteModal = false;
    public $deleteId = null;
    public $supplierToDelete = null;

    // Stats
    public $stats = [];

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'sortField' => ['except' => 'name'],
        'sortDirection' => ['except' => 'asc'],
        'perPage' => ['except' => 15],
    ];

    protected $listeners = [
        'supplierSaved' => 'refreshList',
        'supplierDeleted' => 'refreshList',
        'refresh' => '$refresh'
    ];

    public function mount()
    {
        $this->calculateStats();
    }

    public function refreshList()
    {
        $this->resetPage();
        $this->selectedSuppliers = [];
        $this->selectAll = false;
        $this->calculateStats();
    }

    public function calculateStats()
    {
        $query = Supplier::query();

        $this->stats = [
            'total_suppliers' => $query->count(),
            'active_suppliers' => $query->clone()->where('is_active', true)->count(),
            'inactive_suppliers' => $query->clone()->where('is_active', false)->count(),
            'suppliers_with_purchases' => $query->clone()->has('purchases')->count(),
            'suppliers_without_purchases' => $query->clone()->doesntHave('purchases')->count(),
            'total_purchases' => Supplier::sum('purchases_count') ?? 0,
            'total_spent' => DB::table('purchases')->where('status', 'received')->sum('total_amount') ?? 0,
            'total_outstanding' => DB::table('purchases')->where('payment_status', '!=', 'paid')->sum('due_amount') ?? 0,
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

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function getSuppliersQuery()
    {
        return Supplier::withCount('purchases')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('company_name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%')
                        ->orWhere('phone', 'like', '%' . $this->search . '%')
                        ->orWhere('tax_number', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->statusFilter !== '', fn($q) => $q->where('is_active', $this->statusFilter));
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
        return $this->getSuppliersQuery()
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage)
            ->pluck('id')
            ->map(fn($id) => (string) $id)
            ->toArray();
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedSuppliers = $this->getCurrentPageIds();
        } else {
            $this->selectedSuppliers = [];
        }
    }

    public function updatedSelectedsselectedSuppliers()
    {
        $currentPageIds = $this->getCurrentPageIds();
        $selectedIds = $this->selectedSuppliers;

        sort($currentPageIds);
        sort($selectedIds);
        $this->selectAll = $currentPageIds === $selectedIds && !empty($currentPageIds);
    }

    public function confirmDelete($id)
    {
        $this->deleteId = $id;
        $this->supplierToDelete = Supplier::withCount('purchases')->find($id);
        $this->showDeleteModal = true;
    }

    public function deleteSupplier()
    {
        if (!$this->deleteId) {
            $this->dispatch('notify', [
                'message' => 'No supplier selected for deletion',
                'type' => 'error'
            ]);
            $this->showDeleteModal = false;
            return;
        }

        $supplier = Supplier::find($this->deleteId);

        if (!$supplier) {
            $this->dispatch('notify', [
                'message' => 'Supplier not found',
                'type' => 'error'
            ]);
            $this->showDeleteModal = false;
            $this->resetDeleteState();
            return;
        }

        // Check if supplier has purchases
        if ($supplier->purchases()->count() > 0) {
            $this->dispatch('notify', [
                'message' => 'Cannot delete supplier with ' . $supplier->purchases()->count() . ' purchases. Remove or reassign purchases first.',
                'type' => 'error'
            ]);
            $this->showDeleteModal = false;
            $this->resetDeleteState();
            return;
        }

        try {
            $supplierData = collect($supplier->toArray())->except(['updated_at'])->toArray();
            $supplier->delete();

            $this->showDeleteModal = false;

            // Log activity
            logActivity('deleted', $supplier, $supplierData, []);

            $this->dispatch('notify', [
                'message' => 'Supplier "' . $supplier->name . '" deleted successfully',
                'type' => 'success'
            ]);

            $this->resetDeleteState();
            $this->calculateStats();
            $this->dispatch('supplierDeleted');

        } catch (\Exception $e) {
            $this->showDeleteModal = false;
            $this->resetDeleteState();
            $this->dispatch('notify', [
                'message' => 'Error deleting supplier: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    public function confirmBulkDelete()
    {
        if (empty($this->selectedSuppliers)) {
            $this->dispatch('notify', [
                'message' => 'No suppliers selected',
                'type' => 'error'
            ]);
            return;
        }
        $this->showBulkDeleteModal = true;
    }

    public function bulkDelete()
    {
        if (empty($this->selectedSuppliers)) {
            $this->showBulkDeleteModal = false;
            return;
        }

        DB::beginTransaction();

        try {
            $suppliers = Supplier::withCount('purchases')->whereIn('id', $this->selectedSuppliers)->get();
            
            // Check if any supplier has purchases
            $suppliersWithPurchases = $suppliers->filter(fn($supplier) => $supplier->purchases_count > 0);
            
            if ($suppliersWithPurchases->isNotEmpty()) {
                $supplierNames = $suppliersWithPurchases->pluck('name')->implode(', ');
                $this->dispatch('notify', [
                    'message' => 'Cannot delete suppliers with purchases: ' . $supplierNames . '. Remove or reassign purchases first.',
                    'type' => 'error'
                ]);
                $this->showBulkDeleteModal = false;
                DB::rollBack();
                return;
            }

            // Delete suppliers
            $deletedCount = Supplier::whereIn('id', $this->selectedSuppliers)->delete();

            DB::commit();

            $this->selectedSuppliers = [];
            $this->selectAll = false;
            $this->showBulkDeleteModal = false;

            $this->dispatch('notify', [
                'message' => $deletedCount . ' suppliers deleted successfully',
                'type' => 'success'
            ]);

            $this->calculateStats();
            $this->dispatch('supplierDeleted');

        } catch (\Exception $e) {
            DB::rollBack();

            $this->dispatch('notify', [
                'message' => 'Error deleting suppliers: ' . $e->getMessage(),
                'type' => 'error'
            ]);
            $this->showBulkDeleteModal = false;
        }
    }

    public function toggleStatus($id)
    {
        $supplier = Supplier::find($id);
        
        if ($supplier) {
            $oldStatus = $supplier->is_active;
            $supplier->is_active = !$supplier->is_active;
            $supplier->save();

            // Log activity
            logActivity(
                'status_updated',
                $supplier,
                ['is_active' => $oldStatus],
                ['is_active' => $supplier->is_active]
            );

            $this->dispatch('notify', [
                'message' => 'Supplier "' . $supplier->name . '" ' . ($supplier->is_active ? 'activated' : 'deactivated'),
                'type' => 'success'
            ]);

            $this->calculateStats();
        }
    }

    private function resetDeleteState()
    {
        $this->deleteId = null;
        $this->supplierToDelete = null;
    }

    public function render()
    {
        $suppliers = $this->getSuppliersQuery()
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.suppliers.index', [
            'suppliers' => $suppliers,
        ])->layout('layouts.app');
    }
}