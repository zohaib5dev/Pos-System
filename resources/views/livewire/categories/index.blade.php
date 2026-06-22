<div>
    <!-- ========== PAGE HEADER ========== -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <div>
            <h4 class="mb-0 fw-bold d-flex align-items-center gap-2">
                <i class="bi bi-tags"></i> Categories
            </h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item active">Categories</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('categories.actions', 'create') }}" class="btn btn-primary btn-sm shadow-sm">
            <i class="bi bi-plus-lg"></i> <span class="d-none d-sm-inline">Add Category</span>
            <span class="d-sm-none">Add</span>
        </a>
    </div>

    <!-- ========== MAIN CARD ========== -->
    <div class="card border-0 shadow-lg">
        <div class="card-header bg-transparent border-0 d-flex flex-wrap align-items-center justify-content-between pt-3">
            <h5 class="mb-0 fw-bold d-flex align-items-center gap-2">
                <i class="bi bi-list-ul"></i> Category List
            </h5>
            <span class="badge bg-primary-soft text-primary rounded-pill">{{ $categories->total() }}</span>
        </div>

        <div class="card-body pt-0">
            <!-- ========== MOBILE-FRIENDLY FILTERS ========== -->
            <div class="row g-2 mb-3">
                <div class="col-12 col-sm-6 col-md-5">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-0"><i class="bi bi-search"></i></span>
                        <input type="text"
                            wire:model.live.debounce.300ms="search"
                            class="form-control form-control-sm border-0 bg-light"
                            placeholder="Search categories...">
                    </div>
                </div>
                <div class="col-6 col-sm-3 col-md-3">
                    <select wire:model.live="statusFilter" class="form-select form-select-sm bg-light border-0">
                        <option value="">All Status</option>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
                <div class="col-6 col-sm-3 col-md-2">
                    <select wire:model.live="perPage" class="form-select form-select-sm bg-light border-0">
                        <option value="15">15</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
            </div>

            <!-- ========== BULK ACTIONS ========== -->
            @if(count($selectedCategories) > 0)
            <div class="alert alert-info d-flex flex-wrap align-items-center justify-content-between gap-2 py-2 px-3">
                <span class="small"><i class="bi bi-check2-square me-1"></i><strong>{{ count($selectedCategories) }}</strong> selected</span>
                <div class="d-flex gap-1">
                    <button wire:click="confirmBulkDelete" class="btn btn-danger btn-sm shadow-sm">
                        <i class="bi bi-trash"></i> <span class="d-none d-sm-inline">Delete</span>
                    </button>
                    <button wire:click="$set('selectedCategories', [])" class="btn btn-secondary btn-sm shadow-sm">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            </div>
            @endif

            <!-- ========== MOBILE CARD VIEW ========== -->
            <div class="d-md-none">
                @forelse($categories as $category)
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body p-3">
                        <!-- Header: Checkbox + Name + Status -->
                        <div class="d-flex align-items-start justify-content-between mb-2">
                            <div class="d-flex align-items-center gap-2 flex-grow-1">
                                <input type="checkbox" 
                                    wire:model.live="selectedCategories" 
                                    value="{{ $category->id }}" 
                                    class="form-check-input mt-0">
                                <div>
                                    <div class="fw-semibold">{{ $category->name }}</div>
                                    <div class="text-muted small">{{ $category->slug }}</div>
                                </div>
                            </div>
                            <button wire:click="toggleStatus({{ $category->id }})"
                                class="btn btn-sm {{ $category->is_active ? 'btn-success-soft text-success' : 'btn-secondary-soft text-secondary' }} rounded-pill px-2 py-0 shadow-sm" style="font-size: 0.6rem;">
                                <i class="bi {{ $category->is_active ? 'bi-check-circle-fill' : 'bi-x-circle-fill' }}"></i>
                                {{ $category->is_active ? 'Active' : 'Inactive' }}
                            </button>
                        </div>

                        <!-- Details Grid -->
                        <div class="row g-1 small mb-2">
                            <div class="col-6">
                                <span class="text-muted">Products:</span>
                                <span class="badge bg-primary-soft text-primary rounded-pill">{{ $category->products_count }}</span>
                            </div>
                            <div class="col-6">
                                <span class="text-muted">Sort Order:</span>
                                <span class="fw-semibold">{{ $category->sort_order }}</span>
                            </div>
                            @if($category->description)
                            <div class="col-12">
                                <span class="text-muted">Description:</span>
                                <span class="small">{{ Str::limit($category->description, 60) }}</span>
                            </div>
                            @endif
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-end gap-1 pt-2 border-top">
                            <a href="{{ route('categories.edit', ['id' => $category->id]) }}"
                                class="btn btn-primary-soft text-primary btn-sm rounded-circle shadow-sm" title="Edit" >
                                <i class="bi bi-pencil"></i>
                            </a>
                            <button wire:click="confirmDelete({{ $category->id }})"
                                class="btn btn-danger-soft text-danger btn-sm rounded-circle shadow-sm" title="Delete" >
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-5">
                    <i class="bi bi-tags fs-1 d-block mb-3 text-muted"></i>
                    <p class="text-muted">No categories found</p>
                    <a href="{{ route('categories.actions', 'create') }}" class="btn btn-primary btn-sm shadow-sm">
                        <i class="bi bi-plus-lg"></i> Add Category
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
                                    Category
                                    @if($sortField === 'name')
                                    <i class="bi bi-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                    @endif
                                </a>
                            </th>
                            <th class="small">Slug</th>
                            <th class="small">Products</th>
                            <th class="small">Sort</th>
                            <th class="small">Status</th>
                            <th class="text-center" width="120px">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $category)
                        <tr>
                            <td>
                                <input type="checkbox" wire:model.live="selectedCategories" value="{{ $category->id }}" class="form-check-input">
                            </td>
                            <td>
                                <div>
                                    <div class="fw-semibold">{{ $category->name }}</div>
                                    @if($category->description)
                                    <small class="text-muted">{{ Str::limit($category->description, 40) }}</small>
                                    @endif
                                </div>
                            </td>
                            <td><code class="bg-light px-2 py-1 rounded small">{{ $category->slug }}</code></td>
                            <td>
                                <span class="badge bg-primary-soft text-primary rounded-pill">{{ $category->products_count }}</span>
                            </td>
                            <td><span class="fw-semibold">{{ $category->sort_order }}</span></td>
                            <td>
                                <button wire:click="toggleStatus({{ $category->id }})"
                                    class="btn btn-sm {{ $category->is_active ? 'btn-success-soft text-success' : 'btn-secondary-soft text-secondary' }} rounded-pill px-2 py-0 shadow-sm" style="font-size: 0.65rem;">
                                    <i class="bi {{ $category->is_active ? 'bi-check-circle-fill' : 'bi-x-circle-fill' }} me-1"></i>
                                    {{ $category->is_active ? 'Active' : 'Inactive' }}
                                </button>
                            </td>
                            <td>
                                <div class="d-flex justify-content-center gap-1">
                                    <a href="{{ route('categories.edit', ['id' => $category->id]) }}"
                                        class="btn btn-primary-soft text-primary btn-sm rounded-circle shadow-sm" title="Edit" >
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button wire:click="confirmDelete({{ $category->id }})"
                                        class="btn btn-danger-soft text-danger btn-sm rounded-circle shadow-sm" title="Delete" >
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <i class="bi bi-tags fs-1 d-block mb-3 text-muted"></i>
                                <p class="text-muted">No categories found</p>
                                <a href="{{ route('categories.actions', 'create') }}" class="btn btn-primary btn-sm shadow-sm">
                                    <i class="bi bi-plus-lg"></i> Add Category
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- ========== PAGINATION ========== -->
            <div class="mt-3">
                {{ $categories->links('livewire::bootstrap') }}
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
                        <i class="bi bi-exclamation-triangle-fill"></i> Delete Category
                    </h5>
                    <button type="button" class="btn-close" wire:click="$set('showDeleteModal', false)"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-1">Delete <strong>"{{ $categoryToDelete?->name }}"</strong>?</p>
                    <p class="text-danger small mb-0"><i class="bi bi-info-circle me-1"></i>This cannot be undone.</p>
                    
                    @if($categoryToDelete && $categoryToDelete->products_count > 0)
                        <div class="alert alert-warning d-flex align-items-center gap-2 mt-2 py-2 small">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                            Has <strong>{{ $categoryToDelete->products_count }}</strong> products
                        </div>
                    @endif
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-secondary btn-sm" wire:click="$set('showDeleteModal', false)">
                        Cancel
                    </button>
                    <button type="button" class="btn btn-danger btn-sm" wire:click="deleteCategory">
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
                    <p class="mb-0">Delete <strong>{{ count($selectedCategories) }}</strong> categories?</p>
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