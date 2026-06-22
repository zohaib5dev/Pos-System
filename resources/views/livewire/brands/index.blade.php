<div>
    <!-- ========== PAGE HEADER ========== -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <div>
            <h4 class="mb-0 fw-bold d-flex align-items-center gap-2">
                <i class="bi bi-building"></i> Brands
            </h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item active">Brands</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('brands.actions', 'create') }}" class="btn btn-primary btn-sm shadow-sm">
            <i class="bi bi-plus-lg"></i> <span class="d-none d-sm-inline">Add Brand</span>
            <span class="d-sm-none">Add</span>
        </a>
    </div>

    <!-- ========== MAIN CARD ========== -->
    <div class="card border-0 shadow-lg">
        <div class="card-header bg-transparent border-0 d-flex flex-wrap align-items-center justify-content-between pt-3">
            <h5 class="mb-0 fw-bold d-flex align-items-center gap-2">
                <i class="bi bi-list-ul"></i> Brand List
            </h5>
            <span class="badge bg-primary-soft text-primary rounded-pill">{{ $brands->total() }}</span>
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
                            placeholder="Search brands...">
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
            @if(count($selectedBrands) > 0)
            <div class="alert alert-info d-flex flex-wrap align-items-center justify-content-between gap-2 py-2 px-3">
                <span class="small"><i class="bi bi-check2-square me-1"></i><strong>{{ count($selectedBrands) }}</strong> selected</span>
                <div class="d-flex gap-1">
                    <button wire:click="confirmBulkDelete" class="btn btn-danger btn-sm shadow-sm">
                        <i class="bi bi-trash"></i> <span class="d-none d-sm-inline">Delete</span>
                    </button>
                    <button wire:click="$set('selectedBrands', [])" class="btn btn-secondary btn-sm shadow-sm">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            </div>
            @endif

            <!-- ========== MOBILE CARD VIEW ========== -->
            <div class="d-md-none">
                @forelse($brands as $brand)
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body p-3">
                        <!-- Header: Checkbox + Logo + Name + Status -->
                        <div class="d-flex align-items-start justify-content-between mb-2">
                            <div class="d-flex align-items-center gap-2 flex-grow-1">
                                <input type="checkbox" 
                                    wire:model.live="selectedBrands" 
                                    value="{{ $brand->id }}" 
                                    class="form-check-input mt-0">
                                @if($brand->logo)
                                <img src="{{ Storage::url($brand->logo) }}" 
                                     class="rounded-circle shadow-sm"
                                     style="width: 40px; height: 40px; object-fit: cover; min-width: 40px;">
                                @else
                                <div class="bg-secondary-soft rounded-circle d-flex align-items-center justify-content-center"
                                     style="width: 40px; height: 40px; min-width: 40px;">
                                    <i class="bi bi-building text-secondary"></i>
                                </div>
                                @endif
                                <div>
                                    <div class="fw-semibold">{{ $brand->name }}</div>
                                    <div class="text-muted small">{{ $brand->slug }}</div>
                                </div>
                            </div>
                            <button wire:click="toggleStatus({{ $brand->id }})"
                                class="btn btn-sm {{ $brand->is_active ? 'btn-success-soft text-success' : 'btn-secondary-soft text-secondary' }} rounded-pill px-2 py-0 shadow-sm" style="font-size: 0.6rem;">
                                <i class="bi {{ $brand->is_active ? 'bi-check-circle-fill' : 'bi-x-circle-fill' }}"></i>
                                {{ $brand->is_active ? 'Active' : 'Inactive' }}
                            </button>
                        </div>

                        <!-- Details Grid -->
                        <div class="row g-1 small mb-2">
                            <div class="col-6">
                                <span class="text-muted">Products:</span>
                                <span class="badge bg-primary-soft text-primary rounded-pill">{{ $brand->products_count }}</span>
                            </div>
                            @if($brand->description)
                            <div class="col-12 mt-1">
                                <span class="text-muted">Description:</span>
                                <span class="small">{{ Str::limit($brand->description, 60) }}</span>
                            </div>
                            @endif
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-end gap-1 pt-2 border-top">
                            <a href="{{ route('brands.edit', ['id' => $brand->id]) }}"
                                class="btn btn-primary-soft text-primary btn-sm rounded-circle shadow-sm" title="Edit" >
                                <i class="bi bi-pencil"></i>
                            </a>
                            <button wire:click="confirmDelete({{ $brand->id }})"
                                class="btn btn-danger-soft text-danger btn-sm rounded-circle shadow-sm" title="Delete" >
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-5">
                    <i class="bi bi-building fs-1 d-block mb-3 text-muted"></i>
                    <p class="text-muted">No brands found</p>
                    <a href="{{ route('brands.actions', 'create') }}" class="btn btn-primary btn-sm shadow-sm">
                        <i class="bi bi-plus-lg"></i> Add Brand
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
                                    Brand
                                    @if($sortField === 'name')
                                    <i class="bi bi-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                    @endif
                                </a>
                            </th>
                            <th class="small">Slug</th>
                            <th class="small">Products</th>
                            <th class="small">Status</th>
                            <th class="text-center" width="120px">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($brands as $brand)
                        <tr>
                            <td>
                                <input type="checkbox" wire:model.live="selectedBrands" value="{{ $brand->id }}" class="form-check-input">
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    @if($brand->logo)
                                    <img src="{{ Storage::url($brand->logo) }}" 
                                         class="rounded-circle shadow-sm"
                                         style="width: 36px; height: 36px; object-fit: cover; min-width: 36px;">
                                    @else
                                    <div class="bg-secondary-soft rounded-circle d-flex align-items-center justify-content-center"
                                         style="width: 36px; height: 36px; min-width: 36px;">
                                        <i class="bi bi-building text-secondary"></i>
                                    </div>
                                    @endif
                                    <div>
                                        <div class="fw-semibold">{{ $brand->name }}</div>
                                        @if($brand->description)
                                        <small class="text-muted">{{ Str::limit($brand->description, 40) }}</small>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td><code class="bg-light px-2 py-1 rounded small">{{ $brand->slug }}</code></td>
                            <td>
                                <span class="badge bg-primary-soft text-primary rounded-pill">{{ $brand->products_count }}</span>
                            </td>
                            <td>
                                <button wire:click="toggleStatus({{ $brand->id }})"
                                    class="btn btn-sm {{ $brand->is_active ? 'btn-success-soft text-success' : 'btn-secondary-soft text-secondary' }} rounded-pill px-2 py-0 shadow-sm" style="font-size: 0.65rem;">
                                    <i class="bi {{ $brand->is_active ? 'bi-check-circle-fill' : 'bi-x-circle-fill' }} me-1"></i>
                                    {{ $brand->is_active ? 'Active' : 'Inactive' }}
                                </button>
                            </td>
                            <td>
                                <div class="d-flex justify-content-center gap-1">
                                    <a href="{{ route('brands.edit', ['id' => $brand->id]) }}"
                                        class="btn btn-primary-soft text-primary btn-sm rounded-circle shadow-sm" title="Edit" >
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button wire:click="confirmDelete({{ $brand->id }})"
                                        class="btn btn-danger-soft text-danger btn-sm rounded-circle shadow-sm" title="Delete" >
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="bi bi-building fs-1 d-block mb-3 text-muted"></i>
                                <p class="text-muted">No brands found</p>
                                <a href="{{ route('brands.actions', 'create') }}" class="btn btn-primary btn-sm shadow-sm">
                                    <i class="bi bi-plus-lg"></i> Add Brand
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- ========== PAGINATION ========== -->
            <div class="mt-3">
                {{ $brands->links('livewire::bootstrap') }}
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
                        <i class="bi bi-exclamation-triangle-fill"></i> Delete Brand
                    </h5>
                    <button type="button" class="btn-close" wire:click="$set('showDeleteModal', false)"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-1">Delete <strong>"{{ $brandToDelete?->name }}"</strong>?</p>
                    <p class="text-danger small mb-0"><i class="bi bi-info-circle me-1"></i>This cannot be undone.</p>
                    
                    @if($brandToDelete && $brandToDelete->products_count > 0)
                        <div class="alert alert-warning d-flex align-items-center gap-2 mt-2 py-2 small">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                            Has <strong>{{ $brandToDelete->products_count }}</strong> products
                        </div>
                    @endif
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-secondary btn-sm" wire:click="$set('showDeleteModal', false)">
                        Cancel
                    </button>
                    <button type="button" class="btn btn-danger btn-sm" wire:click="deleteBrand">
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
                    <p class="mb-0">Delete <strong>{{ count($selectedBrands) }}</strong> brands?</p>
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