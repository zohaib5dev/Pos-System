<?php

namespace App\Livewire\Category;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Category;
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
    public $selectedCategories = [];
    public $selectAll = false;
    public $showDeleteModal = false;
    public $showBulkDeleteModal = false;
    public $deleteId = null;
    public $categoryToDelete = null;

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
        'categorySaved' => 'refreshList',
        'categoryDeleted' => 'refreshList',
        'refresh' => '$refresh'
    ];

    public function mount()
    {
        $this->calculateStats();
    }

    public function refreshList()
    {
        $this->resetPage();
        $this->selectedCategories = [];
        $this->selectAll = false;
        $this->calculateStats();
    }

    public function calculateStats()
    {
        $query = Category::query();

        $this->stats = [
            'total_categories' => $query->count(),
            'active_categories' => $query->clone()->where('is_active', true)->count(),
            'inactive_categories' => $query->clone()->where('is_active', false)->count(),
            'categories_with_products' => $query->clone()->has('products')->count(),
            'categories_without_products' => $query->clone()->doesntHave('products')->count(),
            'total_products' => Category::sum('products_count') ?? 0,
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
 
    public function getCurrentPageIds()
    {
        return $this->getCategoriesQuery()
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage)
            ->pluck('id')
            ->map(fn($id) => (string) $id)
            ->toArray();
    }
 
    public function getAllFilteredIds()
    {
        return $this->getCategoriesQuery()
            ->pluck('id')
            ->map(fn($id) => (string) $id)
            ->toArray();
    }

    public function getCategoriesQuery()
    {
        return Category::withCount('products')
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
 
    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedCategories = $this->getCurrentPageIds();
        } else {
            $this->selectedCategories = [];
        }
    }

    public function updatedSelectedCategories()
    {
        $currentPageIds = $this->getCurrentPageIds();
        $selectedIds = $this->selectedCategories;
        
        sort($currentPageIds);
        sort($selectedIds);
        $this->selectAll = $currentPageIds === $selectedIds && !empty($currentPageIds);
    }

    public function confirmDelete($id)
    {
        $this->deleteId = $id;
        $this->categoryToDelete = Category::withCount('products')->find($id);
        $this->showDeleteModal = true;
    }

    public function deleteCategory()
    {
        if (!$this->deleteId) {
            $this->dispatch('notify', [
                'message' => 'No category selected for deletion',
                'type' => 'error'
            ]);
            $this->showDeleteModal = false;
            return;
        }

        $category = Category::find($this->deleteId);

        if (!$category) {
            $this->dispatch('notify', [
                'message' => 'Category not found',
                'type' => 'error'
            ]);
            $this->showDeleteModal = false;
            $this->resetDeleteState();
            return;
        }

        // Check if category has products
        if ($category->products()->count() > 0) {
            $this->dispatch('notify', [
                'message' => 'Cannot delete category with ' . $category->products()->count() . ' products. Remove or reassign products first.',
                'type' => 'error'
            ]);
            $this->showDeleteModal = false;
            $this->resetDeleteState();
            return;
        }

        try {
            $categoryData = collect($category->toArray())->except(['updated_at'])->toArray();
            $category->delete();

            $this->showDeleteModal = false;

            // Log activity
            logActivity('deleted', $category, $categoryData, []);

            $this->dispatch('notify', [
                'message' => 'Category "' . $category->name . '" deleted successfully',
                'type' => 'success'
            ]);

            $this->resetDeleteState();
            $this->calculateStats();
            $this->dispatch('categoryDeleted');

        } catch (\Exception $e) {
            $this->showDeleteModal = false;
            $this->resetDeleteState();
            $this->dispatch('notify', [
                'message' => 'Error deleting category: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    public function confirmBulkDelete()
    {
        if (empty($this->selectedCategories)) {
            $this->dispatch('notify', [
                'message' => 'No categories selected',
                'type' => 'error'
            ]);
            return;
        }
        $this->showBulkDeleteModal = true;
    }

    public function bulkDelete()
    {
        if (empty($this->selectedCategories)) {
            $this->showBulkDeleteModal = false;
            return;
        }

        DB::beginTransaction();

        try {
            $categories = Category::withCount('products')->whereIn('id', $this->selectedCategories)->get();
            
            // Check if any category has products
            $categoriesWithProducts = $categories->filter(fn($category) => $category->products_count > 0);
            
            if ($categoriesWithProducts->isNotEmpty()) {
                $categoryNames = $categoriesWithProducts->pluck('name')->implode(', ');
                $this->dispatch('notify', [
                    'message' => 'Cannot delete categories with products: ' . $categoryNames . '. Remove or reassign products first.',
                    'type' => 'error'
                ]);
                $this->showBulkDeleteModal = false;
                DB::rollBack();
                return;
            }

            // Delete categories
            $deletedCount = Category::whereIn('id', $this->selectedCategories)->delete();

            DB::commit();

            $this->selectedCategories = [];
            $this->selectAll = false;
            $this->showBulkDeleteModal = false;

            $this->dispatch('notify', [
                'message' => $deletedCount . ' categories deleted successfully',
                'type' => 'success'
            ]);

            $this->calculateStats();
            $this->dispatch('categoryDeleted');

        } catch (\Exception $e) {
            DB::rollBack();

            $this->dispatch('notify', [
                'message' => 'Error deleting categories: ' . $e->getMessage(),
                'type' => 'error'
            ]);
            $this->showBulkDeleteModal = false;
        }
    }

    public function toggleStatus($id)
    {
        $category = Category::find($id);
        
        if ($category) {
            $oldStatus = $category->is_active;
            $category->is_active = !$category->is_active;
            $category->save();

            // Log activity
            logActivity(
                'status_updated',
                $category,
                ['is_active' => $oldStatus],
                ['is_active' => $category->is_active]
            );

            $this->dispatch('notify', [
                'message' => 'Category "' . $category->name . '" ' . ($category->is_active ? 'activated' : 'deactivated'),
                'type' => 'success'
            ]);

            $this->calculateStats();
        }
    }

    private function resetDeleteState()
    {
        $this->deleteId = null;
        $this->categoryToDelete = null;
    }

    public function render()
    {
        $categories = $this->getCategoriesQuery()
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.categories.index', [
            'categories' => $categories,
        ])->layout('layouts.app');
    }
}