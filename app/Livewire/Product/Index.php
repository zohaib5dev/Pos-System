<?php

namespace App\Livewire\Product;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Str;


use App\Models\Brand;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class Index extends Component
{
    use WithPagination;

    // Search and Filters
    public $search = '';
    public $categoryFilter = '';
    public $brandFilter = '';
    public $statusFilter = '';
    public $stockFilter = '';
    public $sortField = 'name';
    public $sortDirection = 'asc';
    public $perPage = 15;

    // Selection and Modals
    public $selectedProducts = [];
    public $selectAll = false;
    public $showDeleteModal = false;
    public $showBulkDeleteModal = false;
    public $deleteId = null;
    public $productToDelete = null;

    // Stats
    public $stats = [];

    protected $queryString = [
        'search' => ['except' => ''],
        'categoryFilter' => ['except' => ''],
        'brandFilter' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'stockFilter' => ['except' => ''],
        'sortField' => ['except' => 'name'],
        'sortDirection' => ['except' => 'asc'],
        'perPage' => ['except' => 15],
    ];

    protected $listeners = [
        'productSaved' => 'refreshList',
        'productDeleted' => 'refreshList',
        'refresh' => '$refresh'
    ];

    public function mount()
    {
        $this->calculateStats();
    }

    public function refreshList()
    {
        $this->resetPage();
        $this->selectedProducts = [];
        $this->selectAll = false;
        $this->calculateStats();
    }

    public function calculateStats()
    {
        $query = Product::query();

        $this->stats = [
            'total_products' => $query->count(),
            'active_products' => $query->clone()->where('is_active', true)->count(),
            'inactive_products' => $query->clone()->where('is_active', false)->count(),
            'featured_products' => $query->clone()->where('is_featured', true)->count(),
            'low_stock_products' => $query->clone()->whereRaw('stock_quantity <= low_stock_threshold')->count(),
            'out_of_stock_products' => $query->clone()->where('stock_quantity', '<=', 0)->count(),
            'in_stock_products' => $query->clone()->where('stock_quantity', '>', 0)->count(),
            'total_value' => $query->clone()->sum(DB::raw('stock_quantity * purchase_price')),
        ];
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedCategoryFilter()
    {
        $this->resetPage();
        $this->calculateStats();
    }

    public function updatedBrandFilter()
    {
        $this->resetPage();
        $this->calculateStats();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
        $this->calculateStats();
    }

    public function updatedStockFilter()
    {
        $this->resetPage();
        $this->calculateStats();
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function getProductsQuery()
    {
        return Product::with(['category', 'brand'])
            ->withCount('orderItems')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('sku', 'like', '%' . $this->search . '%')
                        ->orWhere('barcode', 'like', '%' . $this->search . '%')
                        ->orWhere('description', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->categoryFilter, fn($q) => $q->where('category_id', $this->categoryFilter))
            ->when($this->brandFilter, fn($q) => $q->where('brand_id', $this->brandFilter))
            ->when($this->statusFilter !== '', fn($q) => $q->where('is_active', $this->statusFilter))
            ->when($this->stockFilter === 'low', fn($q) => $q->whereRaw('stock_quantity <= low_stock_threshold'))
            ->when($this->stockFilter === 'out', fn($q) => $q->where('stock_quantity', '<=', 0))
            ->when($this->stockFilter === 'in', fn($q) => $q->where('stock_quantity', '>', 0));
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
        return $this->getProductsQuery()
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage)
            ->pluck('id')
            ->map(fn($id) => (string) $id)
            ->toArray();
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedProducts = $this->getCurrentPageIds();
        } else {
            $this->selectedProducts = [];
        }
    }

    public function updatedSelectedpselectedProducts()
    {
        $currentPageIds = $this->getCurrentPageIds();
        $selectedIds = $this->selectedProducts;

        sort($currentPageIds);
        sort($selectedIds);
        $this->selectAll = $currentPageIds === $selectedIds && !empty($currentPageIds);
    }

    public function confirmDelete($id)
    {
        $this->deleteId = $id;
        $this->productToDelete = Product::withCount('orderItems')->find($id);
        $this->showDeleteModal = true;
    }

    public function deleteProduct()
    {
        if (!$this->deleteId) {
            $this->dispatch('notify', [
                'message' => 'No product selected for deletion',
                'type' => 'error'
            ]);
            $this->showDeleteModal = false;
            return;
        }

        $product = Product::find($this->deleteId);

        if (!$product) {
            $this->dispatch('notify', [
                'message' => 'Product not found',
                'type' => 'error'
            ]);
            $this->showDeleteModal = false;
            $this->resetDeleteState();
            return;
        }

        // Check if product has orders
        if ($product->orderItems()->count() > 0) {
            $this->dispatch('notify', [
                'message' => 'Cannot delete product with ' . $product->orderItems()->count() . ' orders. Remove or reassign orders first.',
                'type' => 'error'
            ]);
            $this->showDeleteModal = false;
            $this->resetDeleteState();
            return;
        }

        try {
            $productData = collect($product->toArray())->except(['updated_at'])->toArray();

            // Delete image from storage
            if ($product->main_image && Storage::disk('public')->exists($product->main_image)) {
                Storage::disk('public')->delete($product->main_image);
            }

            // Delete product
            $product->delete();

            $this->showDeleteModal = false;

            // Log activity
            logActivity('deleted', $product, $productData, []);

            $this->dispatch('notify', [
                'message' => 'Product "' . $product->name . '" deleted successfully',
                'type' => 'success'
            ]);

            $this->resetDeleteState();
            $this->calculateStats();
            $this->dispatch('productDeleted');

        } catch (\Exception $e) {
            $this->showDeleteModal = false;
            $this->resetDeleteState();
            $this->dispatch('notify', [
                'message' => 'Error deleting product: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    public function confirmBulkDelete()
    {
        if (empty($this->selectedProducts)) {
            $this->dispatch('notify', [
                'message' => 'No products selected',
                'type' => 'error'
            ]);
            return;
        }
        $this->showBulkDeleteModal = true;
    }

    public function bulkDelete()
    {
        if (empty($this->selectedProducts)) {
            $this->showBulkDeleteModal = false;
            return;
        }

        DB::beginTransaction();

        try {
            $products = Product::withCount('orderItems')->whereIn('id', $this->selectedProducts)->get();
            
            // Check if any product has orders
            $productsWithOrders = $products->filter(fn($product) => $product->order_items_count > 0);
            
            if ($productsWithOrders->isNotEmpty()) {
                $productNames = $productsWithOrders->pluck('name')->implode(', ');
                $this->dispatch('notify', [
                    'message' => 'Cannot delete products with orders: ' . $productNames . '. Remove or reassign orders first.',
                    'type' => 'error'
                ]);
                $this->showBulkDeleteModal = false;
                DB::rollBack();
                return;
            }
            
            foreach ($products as $product) {
                if ($product->main_image && Storage::disk('public')->exists($product->main_image)) {
                    Storage::disk('public')->delete($product->main_image);
                }
            }

            // Delete products
            $deletedCount = Product::whereIn('id', $this->selectedProducts)->delete();

            DB::commit();

            $this->selectedProducts = [];
            $this->selectAll = false;
            $this->showBulkDeleteModal = false;

            $this->dispatch('notify', [
                'message' => $deletedCount . ' products deleted successfully',
                'type' => 'success'
            ]);

            $this->calculateStats();
            $this->dispatch('productDeleted');

        } catch (\Exception $e) {
            DB::rollBack();

            $this->dispatch('notify', [
                'message' => 'Error deleting products: ' . $e->getMessage(),
                'type' => 'error'
            ]);
            $this->showBulkDeleteModal = false;
        }
    }

    public function toggleStatus($id)
    {
        $product = Product::find($id);
        
        if ($product) {
            $oldStatus = $product->is_active;
            $product->is_active = !$product->is_active;
            $product->save();

            // Log activity
            logActivity(
                'status_updated',
                $product,
                ['is_active' => $oldStatus],
                ['is_active' => $product->is_active]
            );

            $this->dispatch('notify', [
                'message' => 'Product "' . $product->name . '" ' . ($product->is_active ? 'activated' : 'deactivated'),
                'type' => 'success'
            ]);

            $this->calculateStats();
        }
    }

    public function toggleFeatured($id)
    {
        $product = Product::find($id);
        
        if ($product) {
            $oldFeatured = $product->is_featured;
            $product->is_featured = !$product->is_featured;
            $product->save();

            // Log activity
            logActivity(
                'featured_updated',
                $product,
                ['is_featured' => $oldFeatured],
                ['is_featured' => $product->is_featured]
            );

            $this->dispatch('notify', [
                'message' => 'Product "' . $product->name . '" ' . ($product->is_featured ? 'featured' : 'unfeatured'),
                'type' => 'success'
            ]);

            $this->calculateStats();
        }
    }

    public function duplicateProduct($id)
    {
        try {
            $product = Product::find($id);
            
            if (!$product) {
                $this->dispatch('notify', [
                    'message' => 'Product not found',
                    'type' => 'error'
                ]);
                return;
            }

            $newProduct = $product->replicate();
            $newProduct->name = $product->name . ' (Copy)';
            $newProduct->sku = $product->sku . '-COPY-' . strtoupper(substr(md5(uniqid()), 0, 4));
            $newProduct->slug = Str::slug($newProduct->name) . '-' . strtolower(substr(md5(uniqid()), 0, 4));
            $newProduct->created_by = auth()->id();
            $newProduct->main_image = null; 
            $newProduct->barcode = null; 
            $newProduct->save();

            // Log activity
            logActivity(
                'duplicated',
                $newProduct,
                [],
                ['original_product_id' => $id, 'original_name' => $product->name]
            );

            $this->dispatch('notify', [
                'message' => 'Product duplicated successfully',
                'type' => 'success'
            ]);

            $this->calculateStats();

        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'message' => 'Error duplicating product: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    private function resetDeleteState()
    {
        $this->deleteId = null;
        $this->productToDelete = null;
    }

    public function render()
    {
        $products = $this->getProductsQuery()
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.products.index', [
            'products' => $products,
            'categories' => Category::where('is_active', true)->orderBy('name')->get(),
            'brands' => Brand::where('is_active', true)->orderBy('name')->get(),
        ])->layout('layouts.app');
    }
}