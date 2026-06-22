<?php

namespace App\Livewire\Brand;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Brand;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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
    public $selectedBrands = [];
    public $selectAll = false;
    public $showBulkDeleteModal = false;
    public $showDeleteModal = false;
    public $deleteId = null;
    public $brandToDelete = null;

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
        'brandSaved' => 'refreshList',
        'brandDeleted' => 'refreshList',
        'refresh' => '$refresh'
    ];

    public function mount()
    {
        $this->calculateStats();
    }

    public function refreshList()
    {
        $this->resetPage();
        $this->selectedBrands = [];
        $this->selectAll = false;
        $this->calculateStats();
    }

    public function calculateStats()
    {
        $query = Brand::query();

        $this->stats = [
            'total_brands' => $query->count(),
            'active_brands' => $query->clone()->where('is_active', true)->count(),
            'inactive_brands' => $query->clone()->where('is_active', false)->count(),
            'brands_with_products' => $query->clone()->has('products')->count(),
            'brands_without_products' => $query->clone()->doesntHave('products')->count(),
            'total_products' => Brand::sum('products_count') ?? 0,
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

    public function getBrandsQuery()
    {
        return Brand::withCount('products')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('description', 'like', '%' . $this->search . '%')
                        ->orWhere('slug', 'like', '%' . $this->search . '%');
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
        return $this->getBrandsQuery()
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage)
            ->pluck('id')
            ->map(fn($id) => (string) $id)
            ->toArray();
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedBrands = $this->getCurrentPageIds();
        } else {
            $this->selectedBrands = [];
        }
    }

    public function updatedSelectedBrands()
    {
        $currentPageIds = $this->getCurrentPageIds();
        $selectedIds = $this->selectedBrands;

        sort($currentPageIds);
        sort($selectedIds);
        $this->selectAll = $currentPageIds === $selectedIds && !empty($currentPageIds);
    }

    public function confirmDelete($id)
    {
        $this->deleteId = $id;
        $this->brandToDelete = Brand::withCount('products')->find($id);
        $this->showDeleteModal = true;
    }

    public function deleteBrand()
    {
        if (!$this->deleteId) {
            $this->dispatch('notify', [
                'message' => 'No brand selected for deletion',
                'type' => 'error'
            ]);
            $this->showDeleteModal = false;
            return;
        }

        $brand = Brand::find($this->deleteId);

        if (!$brand) {
            $this->dispatch('notify', [
                'message' => 'Brand not found',
                'type' => 'error'
            ]);
            $this->showDeleteModal = false;
            $this->resetDeleteState();
            return;
        }

        // Check if brand has products
        if ($brand->products()->count() > 0) {
            $this->dispatch('notify', [
                'message' => 'Cannot delete brand with ' . $brand->products()->count() . ' products. Remove or reassign products first.',
                'type' => 'error'
            ]);
            $this->showDeleteModal = false;
            $this->resetDeleteState();
            return;
        }

        try {
            // Delete logo if exists
            if ($brand->logo) {
                Storage::disk('public')->delete($brand->logo);
            }

            $brand->delete();

            $this->dispatch('notify', [
                'message' => 'Brand "' . $brand->name . '" deleted successfully',
                'type' => 'success'
            ]);

            $this->showDeleteModal = false;
            $this->resetDeleteState();
            $this->calculateStats();
            $this->dispatch('brandDeleted');
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'message' => 'Error deleting brand: ' . $e->getMessage(),
                'type' => 'error'
            ]);
            $this->showDeleteModal = false;
            $this->resetDeleteState();
        }
    }

    public function confirmBulkDelete()
    {
        if (empty($this->selectedBrands)) {
            $this->dispatch('notify', [
                'message' => 'No brands selected',
                'type' => 'error'
            ]);
            return;
        }
        $this->showBulkDeleteModal = true;
    }

    public function bulkDelete()
    {
        if (empty($this->selectedBrands)) {
            $this->showBulkDeleteModal = false;
            return;
        }

        DB::beginTransaction();

        try {
            $brands = Brand::withCount('products')->whereIn('id', $this->selectedBrands)->get();

            // Check if any brand has products
            $brandsWithProducts = $brands->filter(fn($brand) => $brand->products_count > 0);

            if ($brandsWithProducts->isNotEmpty()) {
                $brandNames = $brandsWithProducts->pluck('name')->implode(', ');
                $this->dispatch('notify', [
                    'message' => 'Cannot delete brands with products: ' . $brandNames . '. Remove or reassign products first.',
                    'type' => 'error'
                ]);
                $this->showBulkDeleteModal = false;
                DB::rollBack();
                return;
            }

            // Delete logos
            foreach ($brands as $brand) {
                if ($brand->logo) {
                    Storage::disk('public')->delete($brand->logo);
                }
            }

            // Delete brands
            $deletedCount = Brand::whereIn('id', $this->selectedBrands)->delete();

            DB::commit();

            $this->selectedBrands = [];
            $this->selectAll = false;
            $this->showBulkDeleteModal = false;

            $this->dispatch('notify', [
                'message' => $deletedCount . ' brands deleted successfully',
                'type' => 'success'
            ]);

            $this->calculateStats();
            $this->dispatch('brandDeleted');
        } catch (\Exception $e) {
            DB::rollBack();

            $this->dispatch('notify', [
                'message' => 'Error deleting brands: ' . $e->getMessage(),
                'type' => 'error'
            ]);
            $this->showBulkDeleteModal = false;
        }
    }

    public function toggleStatus($id)
    {
        $brand = Brand::find($id);

        if ($brand) {
            $brand->is_active = !$brand->is_active;
            $brand->save();

            $this->dispatch('notify', [
                'message' => 'Brand "' . $brand->name . '" ' . ($brand->is_active ? 'activated' : 'deactivated'),
                'type' => 'success'
            ]);

            $this->calculateStats();
        }
    }

    private function resetDeleteState()
    {
        $this->deleteId = null;
        $this->brandToDelete = null;
    }

    public function render()
    {
        $brands = $this->getBrandsQuery()
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.brands.index', [
            'brands' => $brands,
        ])->layout('layouts.app');
    }
}
