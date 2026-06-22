<div>
    <!-- ========== PAGE HEADER ========== -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <div>
            <h4 class="mb-0 fw-bold  d-flex align-items-center gap-2">
                <i class="bi bi-box-seam"></i> Products
            </h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item active">Products</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('products.create') }}" class="btn btn-primary btn-sm shadow-sm">
            <i class="bi bi-plus-lg"></i> <span class="d-none d-sm-inline">Add Product</span>
            <span class="d-sm-none">Add</span>
        </a>
    </div>

    <!-- ========== MAIN CARD ========== -->
    <div class="card border-0 shadow-lg">
        <div class="card-header bg-transparent border-0 d-flex flex-wrap align-items-center justify-content-between gap-2 pt-3 pb-0">
            <h5 class="mb-0 fw-bold d-flex align-items-center gap-2">
                <i class="bi bi-list-ul"></i> Product List
            </h5>
            <span class="badge bg-primary-soft text-primary rounded-pill">{{ $products->total() }}</span>
        </div>

        <div class="card-body pt-3">
            <!-- ========== MOBILE-FRIENDLY FILTERS ========== -->
            <div class="row g-2 mb-3">
                <!-- Search - Full width on mobile -->
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-0"><i class="bi bi-search"></i></span>
                        <input type="text"
                            wire:model.live.debounce.300ms="search"
                            class="form-control form-control-sm border-0 bg-light"
                            placeholder="Search...">
                    </div>
                </div>
                <!-- Filters - Stack on mobile -->
                <div class="col-6 col-sm-3 col-md-2">
                    <select wire:model.live="categoryFilter" class="form-select form-select-sm bg-light border-0">
                        <option value="">Category</option>
                        @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-sm-3 col-md-2">
                    <select wire:model.live="brandFilter" class="form-select form-select-sm bg-light border-0">
                        <option value="">Brand</option>
                        @foreach($brands as $brand)
                        <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-4 col-sm-2 col-md-2">
                    <select wire:model.live="stockFilter" class="form-select form-select-sm bg-light border-0">
                        <option value="">Stock</option>
                        <option value="in">In</option>
                        <option value="low">Low</option>
                        <option value="out">Out</option>
                    </select>
                </div>
                <div class="col-4 col-sm-2 col-md-2">
                    <select wire:model.live="statusFilter" class="form-select form-select-sm bg-light border-0">
                        <option value="">Status</option>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
                <div class="col-4 col-sm-2 col-md-1">
                    <select wire:model.live="perPage" class="form-select form-select-sm bg-light border-0">
                        <option value="15">15</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
            </div>

            <!-- ========== BULK ACTIONS ========== -->
            @if(count($selectedProducts) > 0)
            <div class="alert alert-info d-flex flex-wrap align-items-center justify-content-between gap-2 py-2 px-3">
                <span class="small"><i class="bi bi-check2-square me-1"></i><strong>{{ count($selectedProducts) }}</strong> selected</span>
                <div class="d-flex gap-1">
                    <button wire:click="confirmBulkDelete" class="btn btn-danger btn-sm shadow-sm">
                        <i class="bi bi-trash"></i> <span class="d-none d-sm-inline">Delete</span>
                    </button>
                    <button wire:click="$set('selectedProducts', [])" class="btn btn-secondary btn-sm shadow-sm">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            </div>
            @endif

            <!-- ========== MOBILE CARD VIEW ========== -->
            <div class="d-md-none">
                @forelse($products as $product)
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body p-3">
                        <!-- Header: Checkbox + Name + Status -->
                        <div class="d-flex align-items-start justify-content-between mb-2">
                            <div class="d-flex align-items-center gap-2 flex-grow-1">
                                <input type="checkbox" 
                                    wire:model.live="selectedProducts" 
                                    value="{{ $product->id }}" 
                                    class="form-check-input mt-0">
                                <div class="d-flex align-items-center gap-2">
                                    @if($product->main_image)
                                    <img src="{{ Storage::url($product->main_image) }}" 
                                         width="36" height="36" 
                                         class="rounded-3 object-fit-cover"
                                         style="object-fit: cover; min-width: 36px;">
                                    @else
                                    <div class="bg-secondary-soft rounded-3 d-flex align-items-center justify-content-center"
                                         style="width: 36px; height: 36px; min-width: 36px;">
                                        <i class="bi bi-box text-secondary"></i>
                                    </div>
                                    @endif
                                    <div>
                                        <div class="fw-semibold small">{{ Str::limit($product->name, 20) }}</div>
                                        <div class="text-muted small">{{ $product->sku }}</div>
                                    </div>
                                </div>
                            </div>
                            <button wire:click="toggleStatus({{ $product->id }})"
                                class="btn btn-sm {{ $product->is_active ? 'btn-success-soft text-success' : 'btn-secondary-soft text-secondary' }} rounded-pill px-2 py-0 shadow-sm" style="font-size: 0.6rem;">
                                <i class="bi {{ $product->is_active ? 'bi-check-circle-fill' : 'bi-x-circle-fill' }}"></i>
                                {{ $product->is_active ? 'Active' : 'Inactive' }}
                            </button>
                        </div>

                        <!-- Details Grid -->
                        <div class="row g-1 small mb-2">
                            <div class="col-6">
                                <span class="text-muted">Category:</span>
                                <span class="fw-semibold">{{ $product->category->name ?? '-' }}</span>
                            </div>
                            <div class="col-6">
                                <span class="text-muted">Price:</span>
                                <span class="fw-semibold text-primary">{{ amo($product->selling_price) }}</span>
                            </div>
                            <div class="col-6">
                                <span class="text-muted">Stock:</span>
                                @if($product->stock_quantity <= 0)
                                    <span class="badge bg-danger text-white rounded-pill px-2 py-0">Out</span>
                                @elseif($product->stock_quantity <= $product->low_stock_threshold)
                                    <span class="badge bg-warning text-dark rounded-pill px-2 py-0">{{ $product->stock_quantity }}</span>
                                @else
                                    <span class="badge bg-success text-white rounded-pill px-2 py-0">{{ $product->stock_quantity }}</span>
                                @endif
                            </div>
                           
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                            <div class="d-flex gap-1">
                                <a href="{{ route('products.show', ['id' => $product->id]) }}"
                                    class="btn btn-info-soft text-info btn-sm rounded-circle shadow-sm" title="View" >
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('products.edit', ['id' => $product->id]) }}"
                                    class="btn btn-primary-soft text-primary btn-sm rounded-circle shadow-sm" title="Edit" >
                                    <i class="fa fa-edit"></i>
                                </a>
                                <button wire:click="toggleFeatured({{ $product->id }})"
                                    class="btn btn-warning-soft text-warning btn-sm rounded-circle shadow-sm" title="Toggle Featured" >
                                    <i class="bi bi-star{{ $product->is_featured ? '-fill' : '' }}"></i>
                                </button>
                                <button wire:click="duplicateProduct({{ $product->id }})"
                                    class="btn btn-secondary-soft text-secondary btn-sm rounded-circle shadow-sm" title="Duplicate" >
                                    <i class="bi bi-copy"></i>
                                </button>
                                <button wire:click="confirmDelete({{ $product->id }})"
                                    class="btn btn-danger-soft text-danger btn-sm rounded-circle shadow-sm" title="Delete" >
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                            <span class="text-muted small">{{ $product->created_at }}</span>
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-5">
                    <i class="bi bi-box-seam fs-1 d-block mb-3 text-muted"></i>
                    <p class="text-muted">No products found</p>
                    <a href="{{ route('products.create') }}" class="btn btn-primary btn-sm shadow-sm">
                        <i class="bi bi-plus-lg"></i> Add Product
                    </a>
                </div>
                @endforelse
            </div>

            <!-- ========== DESKTOP TABLE VIEW ========== -->
            <div class="table-responsive d-none d-md-block">
                <table class="table table-bordered table-hover align-middle mb-0">
                    <thead class="table">
                        <tr>
                            <th width="40px">
                                <input type="checkbox" wire:model.live="selectAll" class="form-check-input">
                            </th>
                            <th>
                                <a href="#" wire:click.prevent="sortBy('name')" class=" text-decoration-none d-flex align-items-center gap-1 small">
                                    Product
                                    @if($sortField === 'name')
                                    <i class="bi bi-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                    @endif
                                </a>
                            </th>
                            <th class="small">SKU</th>
                            <th class="small">Category</th>
                            <th class="small">Price</th>
                            <th class="small">Stock</th>
                            <th class="small">Status</th>
                            <th class="text-center" width="180px">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                        <tr>
                            <td>
                                <input type="checkbox" wire:model.live="selectedProducts" value="{{ $product->id }}" class="form-check-input">
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    @if($product->main_image)
                                    <img src="{{ Storage::url($product->main_image) }}" 
                                         width="36" height="36" 
                                         class="rounded-3 object-fit-cover"
                                         style="object-fit: cover; min-width: 36px;">
                                    @else
                                    <div class="bg-secondary-soft rounded-3 d-flex align-items-center justify-content-center"
                                         style="width: 36px; height: 36px; min-width: 36px;">
                                        <i class="bi bi-box text-secondary"></i>
                                    </div>
                                    @endif
                                    <div>
                                        <div class="fw-semibold small">{{ $product->name }}</div>
                                     
                                    </div>
                                </div>
                            </td>
                            <td><code class="bg-light px-2 py-1 rounded small">{{ $product->sku }}</code></td>
                            <td class="small">{{ $product->category->name ?? '-' }}</td>
                            <td><span class="fw-semibold text-primary small">{{ amo($product->selling_price) }}</span></td>
                            <td>
                                @if($product->stock_quantity <= 0)
                                    <span class="badge bg-danger text-white rounded-pill px-2 py-1">Out</span>
                                @elseif($product->stock_quantity <= $product->low_stock_threshold)
                                    <span class="badge bg-warning text-dark rounded-pill px-2 py-1">{{ $product->stock_quantity }}</span>
                                @else
                                    <span class="badge bg-success text-white rounded-pill px-2 py-1">{{ $product->stock_quantity }}</span>
                                @endif
                            </td>
                            <td>
                                <button wire:click="toggleStatus({{ $product->id }})"
                                    class="btn btn-sm {{ $product->is_active ? 'btn-success-soft text-success' : 'btn-secondary-soft text-secondary' }} rounded-pill px-2 py-0 shadow-sm" style="font-size: 0.65rem;">
                                    <i class="bi {{ $product->is_active ? 'bi-check-circle-fill' : 'bi-x-circle-fill' }} me-1"></i>
                                    {{ $product->is_active ? 'Active' : 'Inactive' }}
                                </button>
                            </td>
                            <td>
                                <div class="d-flex justify-content-center gap-1">
                                    <a href="{{ route('products.show', ['id' => $product->id]) }}"
                                        class="btn btn-info-soft text-info btn-sm rounded-circle shadow-sm" title="View" >
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('products.edit', ['id' => $product->id]) }}"
                                        class="btn btn-primary-soft text-primary btn-sm rounded-circle shadow-sm" title="Edit" >
                                        <i class="fa fa-edit"></i>
                                    </a>
                                    <button wire:click="toggleFeatured({{ $product->id }})"
                                        class="btn btn-warning-soft text-warning btn-sm rounded-circle shadow-sm" title="Toggle Featured" >
                                        <i class="bi bi-star{{ $product->is_featured ? '-fill' : '' }}"></i>
                                    </button>
                                    <button wire:click="duplicateProduct({{ $product->id }})"
                                        class="btn btn-secondary-soft text-secondary btn-sm rounded-circle shadow-sm" title="Duplicate" >
                                        <i class="bi bi-copy"></i>
                                    </button>
                                    <button wire:click="confirmDelete({{ $product->id }})"
                                        class="btn btn-danger-soft text-danger btn-sm rounded-circle shadow-sm" title="Delete" >
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <i class="bi bi-box-seam fs-1 d-block mb-3 text-muted"></i>
                                <p class="text-muted">No products found</p>
                                <a href="{{ route('products.create') }}" class="btn btn-primary btn-sm shadow-sm">
                                    <i class="bi bi-plus-lg"></i> Add Product
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- ========== PAGINATION ========== -->
            <div class="mt-3">
                {{ $products->links('livewire::bootstrap') }}
            </div>
        </div>
    </div>

    <!-- ========== DELETE MODAL ========== -->
    @if($showDeleteModal)
    <div class="modal fade show" id="deleteModal" style="display: block; background: rgba(0,0,0,0.5);" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title text-danger d-flex align-items-center gap-2">
                        <i class="bi bi-exclamation-triangle-fill"></i> Delete
                    </h5>
                    <button type="button" class="btn-close" wire:click="$set('showDeleteModal', false)"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-1">Delete <strong>"{{ $productToDelete?->name }}"</strong>?</p>
                    <p class="text-danger small mb-0"><i class="bi bi-info-circle me-1"></i>This cannot be undone.</p>
                    
                    @if($productToDelete && $productToDelete->order_items_count > 0)
                        <div class="alert alert-warning d-flex align-items-center gap-2 mt-2 py-2 small">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                            Has <strong>{{ $productToDelete->order_items_count }}</strong> orders
                        </div>
                    @endif
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-secondary btn-sm" wire:click="$set('showDeleteModal', false)">
                        Cancel
                    </button>
                    <button type="button" class="btn btn-danger btn-sm" wire:click="deleteProduct">
                        <i class="bi bi-trash"></i> Delete
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- ========== BULK DELETE MODAL ========== -->
    @if($showBulkDeleteModal)
    <div class="modal fade show" id="bulkDeleteModal" style="display: block; background: rgba(0,0,0,0.5);" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title text-danger d-flex align-items-center gap-2">
                        <i class="bi bi-exclamation-triangle-fill"></i> Delete Selected
                    </h5>
                    <button type="button" class="btn-close" wire:click="$set('showBulkDeleteModal', false)"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-0">Delete <strong>{{ count($selectedProducts) }}</strong> products?</p>
                    <p class="text-danger small mb-0"><i class="bi bi-info-circle me-1"></i>This cannot be undone.</p>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-secondary btn-sm" wire:click="$set('showBulkDeleteModal', false)">
                        Cancel
                    </button>
                    <button type="button" class="btn btn-danger btn-sm" wire:click="bulkDelete">
                        <i class="bi bi-trash"></i> Delete All
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

 
</div>