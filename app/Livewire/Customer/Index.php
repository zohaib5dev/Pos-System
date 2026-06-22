<?php

namespace App\Livewire\Customer;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Customer;
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
    public $selectedCustomers = [];
    public $selectAll = false;
    public $showDeleteModal = false;
    public $showBulkDeleteModal = false;
    public $deleteId = null;
    public $customerToDelete = null;

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
        'customerSaved' => 'refreshList',
        'customerDeleted' => 'refreshList',
        'refresh' => '$refresh'
    ];

    public function mount()
    {
        $this->calculateStats();
    }

    public function refreshList()
    {
        $this->resetPage();
        $this->selectedCustomers = [];
        $this->selectAll = false;
        $this->calculateStats();
    }

    public function calculateStats()
    {
        $query = Customer::query();

        $this->stats = [
            'total_customers' => $query->count(),
            'active_customers' => $query->clone()->where('is_active', true)->count(),
            'inactive_customers' => $query->clone()->where('is_active', false)->count(),
            'customers_with_orders' => $query->clone()->has('orders')->count(),
            'customers_without_orders' => $query->clone()->doesntHave('orders')->count(),
            'total_orders' => Customer::sum('orders_count') ?? 0,
            'total_spent' => DB::table('orders')->where('status', 'completed')->sum('total_amount') ?? 0,
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

    public function getCustomersQuery()
    {
        return Customer::withCount('orders')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%')
                        ->orWhere('phone', 'like', '%' . $this->search . '%')
                        ->orWhere('customer_code', 'like', '%' . $this->search . '%');
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
        return $this->getCustomersQuery()
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage)
            ->pluck('id')
            ->map(fn($id) => (string) $id)
            ->toArray();
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedCustomers = $this->getCurrentPageIds();
        } else {
            $this->selectedCustomers = [];
        }
    }

    public function updatedSelectedCselectedCustomers()
    {
        $currentPageIds = $this->getCurrentPageIds();
        $selectedIds = $this->selectedCustomers;

        sort($currentPageIds);
        sort($selectedIds);
        $this->selectAll = $currentPageIds === $selectedIds && !empty($currentPageIds);
    }

    public function confirmDelete($id)
    {
        $this->deleteId = $id;
        $this->customerToDelete = Customer::withCount('orders')->find($id);
        $this->showDeleteModal = true;
    }

    public function deleteCustomer()
    {
        if (!$this->deleteId) {
            $this->dispatch('notify', [
                'message' => 'No customer selected for deletion',
                'type' => 'error'
            ]);
            $this->showDeleteModal = false;
            return;
        }

        $customer = Customer::find($this->deleteId);

        if (!$customer) {
            $this->dispatch('notify', [
                'message' => 'Customer not found',
                'type' => 'error'
            ]);
            $this->showDeleteModal = false;
            $this->resetDeleteState();
            return;
        }

        // Check if customer has orders
        if ($customer->orders()->count() > 0) {
            $this->dispatch('notify', [
                'message' => 'Cannot delete customer with ' . $customer->orders()->count() . ' orders. Remove or reassign orders first.',
                'type' => 'error'
            ]);
            $this->showDeleteModal = false;
            $this->resetDeleteState();
            return;
        }

        try {
            $customerData = collect($customer->toArray())->except(['updated_at'])->toArray();
            $customer->delete();

            $this->showDeleteModal = false;

            // Log activity
            logActivity('deleted', $customer, $customerData, []);

            $this->dispatch('notify', [
                'message' => 'Customer "' . $customer->name . '" deleted successfully',
                'type' => 'success'
            ]);

            $this->resetDeleteState();
            $this->calculateStats();
            $this->dispatch('customerDeleted');

        } catch (\Exception $e) {
            $this->showDeleteModal = false;
            $this->resetDeleteState();
            $this->dispatch('notify', [
                'message' => 'Error deleting customer: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    public function confirmBulkDelete()
    {
        if (empty($this->selectedCustomers)) {
            $this->dispatch('notify', [
                'message' => 'No customers selected',
                'type' => 'error'
            ]);
            return;
        }
        $this->showBulkDeleteModal = true;
    }

    public function bulkDelete()
    {
        if (empty($this->selectedCustomers)) {
            $this->showBulkDeleteModal = false;
            return;
        }

        DB::beginTransaction();

        try {
            $customers = Customer::withCount('orders')->whereIn('id', $this->selectedCustomers)->get();
            
            // Check if any customer has orders
            $customersWithOrders = $customers->filter(fn($customer) => $customer->orders_count > 0);
            
            if ($customersWithOrders->isNotEmpty()) {
                $customerNames = $customersWithOrders->pluck('name')->implode(', ');
                $this->dispatch('notify', [
                    'message' => 'Cannot delete customers with orders: ' . $customerNames . '. Remove or reassign orders first.',
                    'type' => 'error'
                ]);
                $this->showBulkDeleteModal = false;
                DB::rollBack();
                return;
            }

            // Delete customers
            $deletedCount = Customer::whereIn('id', $this->selectedCustomers)->delete();

            DB::commit();

            $this->selectedCustomers = [];
            $this->selectAll = false;
            $this->showBulkDeleteModal = false;

            $this->dispatch('notify', [
                'message' => $deletedCount . ' customers deleted successfully',
                'type' => 'success'
            ]);

            $this->calculateStats();
            $this->dispatch('customerDeleted');

        } catch (\Exception $e) {
            DB::rollBack();

            $this->dispatch('notify', [
                'message' => 'Error deleting customers: ' . $e->getMessage(),
                'type' => 'error'
            ]);
            $this->showBulkDeleteModal = false;
        }
    }

    public function toggleStatus($id)
    {
        $customer = Customer::find($id);
        
        if ($customer) {
            $oldStatus = $customer->is_active;
            $customer->is_active = !$customer->is_active;
            $customer->save();

            // Log activity
            logActivity(
                'status_updated',
                $customer,
                ['is_active' => $oldStatus],
                ['is_active' => $customer->is_active]
            );

            $this->dispatch('notify', [
                'message' => 'Customer "' . $customer->name . '" ' . ($customer->is_active ? 'activated' : 'deactivated'),
                'type' => 'success'
            ]);

            $this->calculateStats();
        }
    }

    private function resetDeleteState()
    {
        $this->deleteId = null;
        $this->customerToDelete = null;
    }

    public function render()
    {
        $customers = $this->getCustomersQuery()
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.customers.index', [
            'customers' => $customers,
        ])->layout('layouts.app');
    }
}