<div>
    <!-- ========== PAGE HEADER ========== -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <div>
            <h4 class="mb-0 fw-bold d-flex align-items-center gap-2">
                <i class="bi bi-boxes"></i> Inventory
            </h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item active">Inventory</li>
                </ol>
            </nav>
        </div>
        <div>
                   
                    <a href="{{ route('inventory.adjustments') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-sliders-h"></i> View Adjustments
                    </a>
                    <a href="{{ route('inventory.low-stock') }}" class="btn btn-warning btn-sm">
                        <i class="fas fa-exclamation-triangle"></i> Low Stock Alert
                    </a>
                   
                </div>
    </div>

    <!-- ========== SUMMARY CARDS ========== -->
    <div class="row g-2 g-sm-3 mb-3">
        <div class="col-6 col-sm-4 col-md-2">
            <div class="card border-0 shadow-sm stat-card stat-card-primary">
                <div class="card-body p-2 p-sm-3 text-center">
                    <h5 class="mb-0 fw-bold text-primary">{{ number_format($summary['total_products']) }}</h5>
                    <p class="mb-0 text-muted small">Total Items</p>
                </div>
            </div>
        </div>
        <div class="col-6 col-sm-4 col-md-2">
            <div class="card border-0 shadow-sm stat-card stat-card-success">
                <div class="card-body p-2 p-sm-3 text-center">
                    <h5 class="mb-0 fw-bold text-success">{{ number_format($summary['in_stock']) }}</h5>
                    <p class="mb-0 text-muted small">In Stock</p>
                </div>
            </div>
        </div>
        <div class="col-6 col-sm-4 col-md-2">
            <div class="card border-0 shadow-sm stat-card stat-card-warning">
                <div class="card-body p-2 p-sm-3 text-center">
                    <h5 class="mb-0 fw-bold text-warning">{{ number_format($summary['low_stock']) }}</h5>
                    <p class="mb-0 text-muted small">Low Stock</p>
                </div>
            </div>
        </div>
        <div class="col-6 col-sm-4 col-md-2">
            <div class="card border-0 shadow-sm stat-card stat-card-danger">
                <div class="card-body p-2 p-sm-3 text-center">
                    <h5 class="mb-0 fw-bold text-danger">{{ number_format($summary['out_of_stock']) }}</h5>
                    <p class="mb-0 text-muted small">Out of Stock</p>
                </div>
            </div>
        </div>
        <div class="col-6 col-sm-4 col-md-2">
            <div class="card border-0 shadow-sm stat-card stat-card-info">
                <div class="card-body p-2 p-sm-3 text-center">
                    <h5 class="mb-0 fw-bold text-info">{{ number_format($summary['total_value']) }}</h5>
                    <p class="mb-0 text-muted small">Total Value</p>
                </div>
            </div>
        </div>
        <div class="col-6 col-sm-4 col-md-2">
            <div class="card border-0 shadow-sm stat-card stat-card-secondary">
                <div class="card-body p-2 p-sm-3 text-center">
                    <h5 class="mb-0 fw-bold text-secondary">{{ number_format($summary['total_retail']) }}</h5>
                    <p class="mb-0 text-muted small">Retail Value</p>
                </div>
            </div>
        </div>
    </div>

    <!-- ========== MAIN CARD ========== -->
    <div class="card border-0 shadow-lg">
        <div class="card-header bg-transparent border-0 d-flex flex-wrap align-items-center justify-content-between pt-3">
            <h5 class="mb-0 fw-bold d-flex align-items-center gap-2">
                <i class="bi bi-list-ul"></i> Inventory List
            </h5>
            <span class="badge bg-primary-soft text-primary rounded-pill">{{ $products->total() }}</span>
        </div>

        <div class="card-body pt-0">
            <!-- ========== FILTERS ========== -->
            <div class="row g-2 mb-3">
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-0"><i class="bi bi-search"></i></span>
                        <input type="text"
                            wire:model.live.debounce.300ms="search"
                            class="form-control form-control-sm border-0 bg-light"
                            placeholder="Search products...">
                    </div>
                </div>
                <div class="col-6 col-sm-3 col-md-2">
                    <select wire:model.live="categoryFilter" class="form-select form-select-sm bg-light border-0">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-sm-3 col-md-2">
                    <select wire:model.live="brandFilter" class="form-select form-select-sm bg-light border-0">
                        <option value="">All Brands</option>
                        @foreach($brands as $brand)
                            <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-sm-3 col-md-2">
                    <select wire:model.live="stockStatus" class="form-select form-select-sm bg-light border-0">
                        <option value="all">All Stock</option>
                        <option value="in">In Stock</option>
                        <option value="low">Low Stock</option>
                        <option value="out">Out of Stock</option>
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
                @if(count($selectedProducts) > 0)
                <div class="col-12 col-sm-12 col-md-1">
                    <button wire:click="openAdjustmentModal" class="btn btn-success btn-sm w-100 shadow-sm">
                        <i class="bi bi-arrow-repeat"></i> Adjust
                    </button>
                </div>
                @endif
            </div>

            <!-- ========== BULK ACTIONS ========== -->
            @if(count($selectedProducts) > 0)
            <div class="alert alert-info d-flex flex-wrap align-items-center justify-content-between gap-2 py-2 px-3">
                <span class="small"><i class="bi bi-check2-square me-1"></i><strong>{{ count($selectedProducts) }}</strong> selected</span>
                <div class="d-flex gap-1">
                    <button wire:click="bulkAdjust" class="btn btn-primary btn-sm shadow-sm">
                        <i class="bi bi-arrow-repeat"></i> <span class="d-none d-sm-inline">Adjust Stock</span>
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
                        <div class="d-flex align-items-start justify-content-between mb-2">
                            <div class="d-flex align-items-center gap-2 flex-grow-1">
                                <input type="checkbox" 
                                    wire:model.live="selectedProducts" 
                                    value="{{ $product->id }}" 
                                    class="form-check-input mt-0">
                                <div>
                                    <div class="fw-semibold">{{ $product->name }}</div>
                                    <div class="text-muted small">SKU: {{ $product->sku }}</div>
                                </div>
                            </div>
                            <span class="badge 
                                @if($product->stock_quantity <= 0) bg-danger-soft text-danger
                                @elseif($product->stock_quantity <= $product->low_stock_threshold) bg-warning-soft text-warning
                                @else bg-success-soft text-success
                                @endif rounded-pill px-2 py-1">
                                {{ $product->stock_quantity }}
                            </span>
                        </div>

                        <div class="row g-1 small">
                            <div class="col-6">
                                <span class="text-muted">Category:</span>
                                <span>{{ $product->category->name ?? '-' }}</span>
                            </div>
                            <div class="col-6">
                                <span class="text-muted">Brand:</span>
                                <span>{{ $product->brand->name ?? '-' }}</span>
                            </div>
                            <div class="col-6">
                                <span class="text-muted">Purchase:</span>
                                <span class="fw-semibold">{{ number_format($product->purchase_price, 2) }}</span>
                            </div>
                            <div class="col-6">
                                <span class="text-muted">Selling:</span>
                                <span class="fw-semibold">{{ number_format($product->selling_price, 2) }}</span>
                            </div>
                            <div class="col-12">
                                <span class="text-muted">Low Stock Threshold:</span>
                                <span>{{ $product->low_stock_threshold }}</span>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-1 pt-2 border-top mt-2">
                            <button wire:click="openAdjustmentModal({{ $product->id }})"
                                class="btn btn-primary-soft text-primary btn-sm rounded-circle shadow-sm" title="Adjust Stock" >
                                <i class="bi bi-arrow-repeat"></i>
                            </button>
                            <a href="{{ route('products.show', $product->id) }}"
                                class="btn btn-info-soft text-info btn-sm rounded-circle shadow-sm" title="View" >
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('products.edit', $product->id) }}"
                                class="btn btn-primary-soft text-primary btn-sm rounded-circle shadow-sm" title="Edit" >
                                <i class="bi bi-pencil"></i>
                            </a>
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-5">
                    <i class="bi bi-boxes fs-1 d-block mb-3 text-muted"></i>
                    <p class="text-muted">No products found</p>
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
                            <th wire:click="sortBy('name')" style="cursor: pointer" class="small">
                                Product
                                @if($sortField === 'name')
                                <i class="bi bi-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </th>
                            <th class="small">SKU</th>
                            <th class="small">Category</th>
                            <th wire:click="sortBy('purchase_price')" style="cursor: pointer" class="text-end small">
                                Cost
                                @if($sortField === 'purchase_price')
                                <i class="bi bi-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </th>
                            <th wire:click="sortBy('selling_price')" style="cursor: pointer" class="text-end small">
                                Price
                                @if($sortField === 'selling_price')
                                <i class="bi bi-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </th>
                            <th wire:click="sortBy('stock_quantity')" style="cursor: pointer" class="text-end small">
                                Stock
                                @if($sortField === 'stock_quantity')
                                <i class="bi bi-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </th>
                            <th class="text-center small">Status</th>
                            <th class="text-center" width="120px">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                        <tr>
                            <td>
                                <input type="checkbox" wire:model.live="selectedProducts" value="{{ $product->id }}" class="form-check-input">
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $product->name }}</div>
                            </td>
                            <td><code class="bg-light px-2 py-1 rounded small">{{ $product->sku }}</code></td>
                            <td>{{ $product->category->name ?? '-' }}</td>
                            <td class="text-end">{{ number_format($product->purchase_price, 2) }}</td>
                            <td class="text-end">{{ number_format($product->selling_price, 2) }}</td>
                            <td class="text-end fw-bold">
                                <span class="badge 
                                    @if($product->stock_quantity <= 0) bg-danger-soft text-danger
                                    @elseif($product->stock_quantity <= $product->low_stock_threshold) bg-warning-soft text-warning
                                    @else bg-success-soft text-success
                                    @endif rounded-pill px-3 py-2">
                                    {{ $product->stock_quantity }}
                                </span>
                            </td>
                            <td class="text-center">
                                @if($product->stock_quantity <= 0)
                                    <span class="badge bg-danger-soft text-danger rounded-pill">Out of Stock</span>
                                @elseif($product->stock_quantity <= $product->low_stock_threshold)
                                    <span class="badge bg-warning-soft text-warning rounded-pill">Low Stock</span>
                                @else
                                    <span class="badge bg-success-soft text-success rounded-pill">In Stock</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex justify-content-center gap-1">
                                    <button wire:click="openAdjustmentModal({{ $product->id }})"
                                        class="btn btn-primary-soft text-primary btn-sm rounded-circle shadow-sm" title="Adjust Stock" >
                                        <i class="bi bi-arrow-repeat"></i>
                                    </button>
                                    <a href="{{ route('products.show', $product->id) }}"
                                        class="btn btn-info-soft text-info btn-sm rounded-circle shadow-sm" title="View" >
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('products.edit', $product->id) }}"
                                        class="btn btn-primary-soft text-primary btn-sm rounded-circle shadow-sm" title="Edit" >
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <i class="bi bi-boxes fs-1 d-block mb-3 text-muted"></i>
                                <p class="text-muted">No products found</p>
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

    <!-- ========== STOCK ADJUSTMENT MODAL ========== -->
    @if($showAdjustmentModal)
    <div class="modal fade show" id="adjustmentModal" style="display: block; background: rgba(0,0,0,0.5);" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header border-0">
                    <h5 class="modal-title text-primary d-flex align-items-center gap-2">
                        <i class="bi bi-arrow-repeat"></i> Stock Adjustment
                    </h5>
                    <button type="button" class="btn-close" wire:click="$set('showAdjustmentModal', false)"></button>
                </div>
                <div class="modal-body">
                    @if($productId)
                    <div class="bg-light-soft rounded-3 p-3 mb-3">
                        <div class="row g-1 small">
                            <div class="col-12">
                                <strong>Product:</strong> {{ \App\Models\Product::find($productId)?->name ?? 'N/A' }}
                            </div>
                            <div class="col-6">
                                <span class="text-muted">SKU:</span> {{ \App\Models\Product::find($productId)?->sku ?? 'N/A' }}
                            </div>
                            <div class="col-6">
                                <span class="text-muted">Current Stock:</span> 
                                <strong class="{{ $currentStock <= 0 ? 'text-danger' : 'text-success' }}">{{ $currentStock }}</strong>
                            </div>
                            @if($adjustmentQuantity > 0)
                            <div class="col-12">
                                <span class="text-muted">New Stock:</span> 
                                <strong class="{{ $newStock <= 0 ? 'text-danger' : 'text-success' }}">{{ $newStock }}</strong>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif

                    <div class="form-group mb-3">
                        <label class="form-label fw-semibold small">Adjustment Type <span class="text-danger">*</span></label>
                        <div class="d-flex gap-3">
                            <div class="form-check">
                                <input type="radio" wire:model="adjustmentType" value="addition" class="form-check-input" id="addition">
                                <label class="form-check-label" for="addition">Add Stock</label>
                            </div>
                            <div class="form-check">
                                <input type="radio" wire:model="adjustmentType" value="deduction" class="form-check-input" id="deduction">
                                <label class="form-check-label" for="deduction">Remove Stock</label>
                            </div>
                        </div>
                        @error('adjustmentType') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label fw-semibold small">Quantity <span class="text-danger">*</span></label>
                        <input type="number" 
                               wire:model.live="adjustmentQuantity" 
                               step="0.01" 
                               min="0.01"
                               class="form-control @error('adjustmentQuantity') is-invalid @enderror"
                               placeholder="Enter quantity">
                        @error('adjustmentQuantity') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label fw-semibold small">Reason <span class="text-danger">*</span></label>
                        <input type="text" 
                               wire:model="adjustmentReason" 
                               class="form-control @error('adjustmentReason') is-invalid @enderror"
                               placeholder="e.g., Stock count, Damaged, Return">
                        @error('adjustmentReason') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label fw-semibold small">Notes</label>
                        <textarea wire:model="adjustmentNotes" 
                                  rows="2" 
                                  class="form-control"
                                  placeholder="Additional notes"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary btn-sm shadow-sm" wire:click="$set('showAdjustmentModal', false)">
                        Cancel
                    </button>
                    <button type="button" class="btn btn-primary btn-sm shadow-sm" wire:click="saveAdjustment">
                        <i class="bi bi-check-lg"></i> Save Adjustment
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

   
</div>