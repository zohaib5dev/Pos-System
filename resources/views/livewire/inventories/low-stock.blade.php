<div>
    <!-- ========== PAGE HEADER ========== -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <div>
            <h4 class="mb-0 fw-bold d-flex align-items-center gap-2">
                <i class="bi bi-exclamation-triangle"></i> Low Stock Alert
            </h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('inventory.index') }}" class="text-decoration-none">Inventory</a></li>
                    <li class="breadcrumb-item active">Low Stock</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('inventory.index') }}" class="btn btn-secondary btn-sm shadow-sm">
            <i class="bi bi-arrow-left"></i> <span class="d-none d-sm-inline">Back to Inventory</span>
            <span class="d-sm-none">Back</span>
        </a>
    </div>

    <!-- ========== ALERT SUMMARY ========== -->
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body p-3 p-sm-4">
            <div class="d-flex flex-wrap align-items-center gap-3">
                <div class="bg-warning-soft rounded-3 p-3">
                    <i class="bi bi-exclamation-triangle-fill fs-2 text-warning"></i>
                </div>
                <div class="flex-grow-1">
                    <h5 class="mb-0 fw-bold">
                        <span class="text-warning">{{ $products->total() }}</span> products are running low on stock
                    </h5>
                    <p class="mb-0 text-muted small">These items need to be reordered to maintain healthy inventory levels.</p>
                </div>
                @if($products->total() > 0)
                <a href="{{ route('purchases.create') }}" class="btn btn-primary btn-sm shadow-sm">
                    <i class="bi bi-plus-lg"></i> Create Purchase Order
                </a>
                @endif
            </div>
        </div>
    </div>

    <!-- ========== MAIN CARD ========== -->
    <div class="card border-0 shadow-lg">
        <div class="card-header bg-transparent border-0 d-flex flex-wrap align-items-center justify-content-between pt-3">
            <h5 class="mb-0 fw-bold d-flex align-items-center gap-2">
                <i class="bi bi-list-ul"></i> Low Stock Products
            </h5>
            <span class="badge bg-warning-soft text-warning rounded-pill">{{ $products->total() }}</span>
        </div>

        <div class="card-body pt-0">
            <!-- ========== FILTERS ========== -->
            <div class="row g-2 mb-3">
                <div class="col-12 col-sm-6 col-md-4">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-0"><i class="bi bi-search"></i></span>
                        <input type="text"
                            wire:model.live.debounce.300ms="search"
                            class="form-control form-control-sm border-0 bg-light"
                            placeholder="Search products...">
                    </div>
                </div>
                <div class="col-6 col-sm-3 col-md-3">
                    <select wire:model.live="categoryFilter" class="form-select form-select-sm bg-light border-0">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
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

            <!-- ========== MOBILE CARD VIEW ========== -->
            <div class="d-md-none">
                @forelse($products as $product)
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-start justify-content-between mb-2">
                            <div class="d-flex align-items-center gap-2 flex-grow-1">
                                @if($product->main_image)
                                    <img src="{{ Storage::url($product->main_image) }}" 
                                         class="rounded-3 shadow-sm"
                                         style="width: 40px; height: 40px; object-fit: cover; min-width: 40px;">
                                @else
                                    <div class="bg-secondary-soft rounded-3 d-flex align-items-center justify-content-center"
                                         style="width: 40px; height: 40px; min-width: 40px;">
                                        <i class="bi bi-box text-secondary"></i>
                                    </div>
                                @endif
                                <div>
                                    <div class="fw-semibold">{{ $product->name }}</div>
                                    <div class="text-muted small">{{ $product->sku }}</div>
                                </div>
                            </div>
                            <span class="badge {{ $product->stock_quantity <= 0 ? 'bg-danger-soft text-danger' : 'bg-warning-soft text-warning' }} rounded-pill px-3 py-2">
                                {{ $product->stock_quantity }}
                            </span>
                        </div>

                        <div class="row g-1 small mb-2">
                            <div class="col-6">
                                <span class="text-muted">Category:</span>
                                <span>{{ $product->category->name ?? '-' }}</span>
                            </div>
                            <div class="col-6">
                                <span class="text-muted">Threshold:</span>
                                <span class="fw-semibold">{{ $product->low_stock_threshold }}</span>
                            </div>
                            <div class="col-6">
                                <span class="text-muted">Status:</span>
                                @if($product->stock_quantity <= 0)
                                    <span class="badge bg-danger-soft text-danger rounded-pill px-2 py-0">Out of Stock</span>
                                @else
                                    <span class="badge bg-warning-soft text-warning rounded-pill px-2 py-0">Low Stock</span>
                                @endif
                            </div>
                            <div class="col-6">
                                <span class="text-muted">To Order:</span>
                                <span class="badge bg-info-soft text-info rounded-pill px-2 py-0">{{ max(0, $product->low_stock_threshold * 2 - $product->stock_quantity) }}</span>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-1 pt-2 border-top">
                            <button wire:click="openAdjustmentModal({{ $product->id }})"
                                class="btn btn-primary-soft text-primary btn-sm rounded-circle shadow-sm" title="Adjust Stock" style="width: 32px; height: 32px; padding: 0; font-size: 0.7rem;">
                                <i class="bi bi-arrow-repeat"></i>
                            </button>
                            <a href="{{ route('purchases.create', ['product' => $product->id]) }}"
                                class="btn btn-success-soft text-success btn-sm rounded-circle shadow-sm" title="Reorder" style="width: 32px; height: 32px; padding: 0; font-size: 0.7rem;">
                                <i class="bi bi-cart-plus"></i>
                            </a>
                            <a href="{{ route('products.actions', ['action' => 'show', 'id' => $product->id]) }}"
                                class="btn btn-info-soft text-info btn-sm rounded-circle shadow-sm" title="View" style="width: 32px; height: 32px; padding: 0; font-size: 0.7rem;">
                                <i class="bi bi-eye"></i>
                            </a>
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-5">
                    <i class="bi bi-check-circle-fill fs-1 d-block mb-3 text-success"></i>
                    <h6 class="text-success">All inventory levels are healthy!</h6>
                    <p class="text-muted small">No low stock products found</p>
                </div>
                @endforelse
            </div>

            <!-- ========== DESKTOP TABLE VIEW ========== -->
            <div class="table-responsive d-none d-md-block">
                <table class="table table-bordered table-hover align-middle mb-0">
                    <thead class="table">
                        <tr>
                            <th class="small">Product</th>
                            <th class="small">SKU</th>
                            <th class="small">Category</th>
                            <th class="text-end small">Stock</th>
                            <th class="text-end small">Threshold</th>
                            <th class="text-end small">Status</th>
                            <th class="text-end small">To Order</th>
                            <th class="text-center small" width="160px">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    @if($product->main_image)
                                        <img src="{{ Storage::url($product->main_image) }}" 
                                             class="rounded-3 shadow-sm"
                                             style="width: 32px; height: 32px; object-fit: cover; min-width: 32px;">
                                    @else
                                        <div class="bg-secondary-soft rounded-3 d-flex align-items-center justify-content-center"
                                             style="width: 32px; height: 32px; min-width: 32px;">
                                            <i class="bi bi-box text-secondary"></i>
                                        </div>
                                    @endif
                                    <div class="fw-semibold">{{ $product->name }}</div>
                                </div>
                            </td>
                            <td><code class="bg-light px-2 py-1 rounded small">{{ $product->sku }}</code></td>
                            <td>{{ $product->category->name ?? '-' }}</td>
                            <td class="text-end">
                                <span class="badge {{ $product->stock_quantity <= 0 ? 'bg-danger-soft text-danger' : 'bg-warning-soft text-warning' }} rounded-pill px-3 py-2">
                                    {{ $product->stock_quantity }}
                                </span>
                            </td>
                            <td class="text-end">{{ $product->low_stock_threshold }}</td>
                            <td class="text-end">
                                @if($product->stock_quantity <= 0)
                                    <span class="badge bg-danger-soft text-danger rounded-pill px-3 py-2">Out of Stock</span>
                                @else
                                    <span class="badge bg-warning-soft text-warning rounded-pill px-3 py-2">Low Stock</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <span class="badge bg-info-soft text-info rounded-pill px-3 py-2">
                                    {{ max(0, $product->low_stock_threshold * 2 - $product->stock_quantity) }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex justify-content-center gap-1">
                                    <button wire:click="openAdjustmentModal({{ $product->id }})"
                                        class="btn btn-primary-soft text-primary btn-sm rounded-circle shadow-sm" title="Adjust Stock" style="width: 32px; height: 32px; padding: 0; font-size: 0.7rem;">
                                        <i class="bi bi-arrow-repeat"></i>
                                    </button>
                                    <a href="{{ route('purchases.create', ['product' => $product->id]) }}"
                                        class="btn btn-success-soft text-success btn-sm rounded-circle shadow-sm" title="Reorder" style="width: 32px; height: 32px; padding: 0; font-size: 0.7rem;">
                                        <i class="bi bi-cart-plus"></i>
                                    </a>
                                    <a href="{{ route('products.actions', ['action' => 'show', 'id' => $product->id]) }}"
                                        class="btn btn-info-soft text-info btn-sm rounded-circle shadow-sm" title="View" style="width: 32px; height: 32px; padding: 0; font-size: 0.7rem;">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <i class="bi bi-check-circle-fill fs-1 d-block mb-3 text-success"></i>
                                <h6 class="text-success">All inventory levels are healthy!</h6>
                                <p class="text-muted small">No low stock products found</p>
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
                        @php
                            $product = \App\Models\Product::find($productId);
                        @endphp
                        @if($product)
                        <div class="bg-light-soft rounded-3 p-3 mb-3">
                            <div class="row g-1 small">
                                <div class="col-12"><strong>{{ $product->name }}</strong></div>
                                <div class="col-6"><span class="text-muted">SKU:</span> {{ $product->sku }}</div>
                                <div class="col-6"><span class="text-muted">Current Stock:</span> <strong class="{{ $product->stock_quantity <= 0 ? 'text-danger' : 'text-warning' }}">{{ $product->stock_quantity }}</strong></div>
                            </div>
                        </div>
                        @endif
                    @endif
                    
                    <div class="form-group mb-3">
                        <label class="form-label fw-semibold small">Adjustment Type</label>
                        <div class="d-flex gap-3">
                            <div class="form-check">
                                <input type="radio" wire:model.live="adjustmentType" value="addition" class="form-check-input" id="addition">
                                <label class="form-check-label" for="addition">Addition (+)</label>
                            </div>
                            <div class="form-check">
                                <input type="radio" wire:model.live="adjustmentType" value="deduction" class="form-check-input" id="deduction">
                                <label class="form-check-label" for="deduction">Deduction (-)</label>
                            </div>
                        </div>
                        @error('adjustmentType') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    
                    <div class="form-group mb-3">
                        <label class="form-label fw-semibold small">Quantity <span class="text-danger">*</span></label>
                        <input type="number" 
                               wire:model.live="adjustmentQuantity" 
                               min="1"
                               class="form-control @error('adjustmentQuantity') is-invalid @enderror"
                               placeholder="Enter quantity">
                        @error('adjustmentQuantity') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    
                    @if($productId && $adjustmentQuantity > 0)
                    <div class="bg-info-soft rounded-3 p-3 mb-3">
                        <div class="d-flex justify-content-between small">
                            <span>Current Stock:</span>
                            <span class="fw-bold">{{ $currentStock }}</span>
                        </div>
                        <div class="d-flex justify-content-between fw-bold">
                            <span>New Stock:</span>
                            <span class="text-primary">{{ $newStock }}</span>
                        </div>
                    </div>
                    @endif
                    
                    <div class="form-group mb-3">
                        <label class="form-label fw-semibold small">Reason <span class="text-danger">*</span></label>
                        <select wire:model="adjustmentReason" class="form-select @error('adjustmentReason') is-invalid @enderror">
                            <option value="">Select Reason</option>
                            <option value="damaged">Damaged Goods</option>
                            <option value="expired">Expired</option>
                            <option value="count">Stock Count Adjustment</option>
                            <option value="return">Customer Return</option>
                            <option value="supplier">Supplier Return</option>
                            <option value="other">Other</option>
                        </select>
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