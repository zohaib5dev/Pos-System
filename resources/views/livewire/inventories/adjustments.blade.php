<div>
    <!-- ========== PAGE HEADER ========== -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <div>
            <h4 class="mb-0 fw-bold d-flex align-items-center gap-2">
                <i class="bi bi-sliders2"></i> Stock Adjustments
            </h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('inventory.index') }}" class="text-decoration-none">Inventory</a></li>
                    <li class="breadcrumb-item active">Adjustments</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex flex-wrap gap-1">
            <!-- <button wire:click="openAdjustmentModal" class="btn btn-primary btn-sm shadow-sm">
                <i class="bi bi-plus-lg"></i> <span class="d-none d-sm-inline">New Adjustment</span>
                <span class="d-sm-none">New</span>
            </button> -->
            <a href="{{ route('inventory.index') }}" class="btn btn-secondary btn-sm shadow-sm">
                <i class="bi bi-arrow-left"></i> <span class="d-none d-sm-inline">Back</span>
            </a>
        </div>
    </div>

    <!-- ========== MAIN CARD ========== -->
    <div class="card border-0 shadow-lg">
        <div class="card-header bg-transparent border-0 d-flex flex-wrap align-items-center justify-content-between pt-3">
            <h5 class="mb-0 fw-bold d-flex align-items-center gap-2">
                <i class="bi bi-list-ul"></i> Adjustment History
            </h5>
            <span class="badge bg-primary-soft text-primary rounded-pill">{{ $adjustments->total() }}</span>
        </div>

        <div class="card-body pt-0">
            <!-- ========== FILTERS ========== -->
            <div class="row g-2 mb-3">
                <div class="col-12 col-sm-6 col-md-5">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-0"><i class="bi bi-search"></i></span>
                        <input type="text"
                            wire:model.live.debounce.300ms="search"
                            class="form-control form-control-sm border-0 bg-light"
                            placeholder="Search by product or SKU...">
                    </div>
                </div>
                <div class="col-6 col-sm-3 col-md-3">
                    <select wire:model.live="dateRange" class="form-select form-select-sm bg-light border-0">
                        <option value="all">All Time</option>
                        <option value="today">Today</option>
                        <option value="week">This Week</option>
                        <option value="month">This Month</option>
                        <option value="custom">Custom Range</option>
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

            <!-- Custom Date Range -->
            @if($dateRange === 'custom')
            <div class="row g-2 mb-3">
                <div class="col-6 col-sm-6 col-md-3">
                    <label class="form-label small fw-semibold">Start Date</label>
                    <input type="date" 
                           wire:model.live="startDate" 
                           class="form-control form-control-sm bg-light border-0">
                </div>
                <div class="col-6 col-sm-6 col-md-3">
                    <label class="form-label small fw-semibold">End Date</label>
                    <input type="date" 
                           wire:model.live="endDate" 
                           class="form-control form-control-sm bg-light border-0">
                </div>
            </div>
            @endif

            <!-- ========== MOBILE CARD VIEW ========== -->
            <div class="d-md-none">
                @forelse($adjustments as $adjustment)
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <div class="fw-bold text-primary">{{ $adjustment->adjustment_number }}</div>
                                <div class="text-muted small">{{ $adjustment->created_at }}</div>
                            </div>
                            <span class="badge {{ $adjustment->adjustment_type === 'addition' ? 'bg-success-soft text-success' : 'bg-danger-soft text-danger' }} rounded-pill px-3 py-2">
                                {{ $adjustment->adjustment_type === 'addition' ? 'Addition' : 'Deduction' }}
                            </span>
                        </div>

                        <div class="mb-2">
                            <div class="fw-semibold">{{ $adjustment->product->name ?? 'Deleted Product' }}</div>
                            <div class="text-muted small">SKU: {{ $adjustment->product->sku ?? 'N/A' }}</div>
                        </div>

                        <div class="row g-1 small mb-2">
                            <div class="col-4">
                                <span class="text-muted">Qty:</span>
                                <span class="fw-bold {{ $adjustment->adjustment_type === 'addition' ? 'text-success' : 'text-danger' }}">
                                    {{ $adjustment->adjustment_type === 'addition' ? '+' : '-' }}{{ $adjustment->quantity }}
                                </span>
                            </div>
                            <div class="col-4">
                                <span class="text-muted">Previous:</span>
                                <span>{{ $adjustment->current_quantity }}</span>
                            </div>
                            <div class="col-4">
                                <span class="text-muted">New:</span>
                                <span class="fw-bold">{{ $adjustment->new_quantity }}</span>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                            <div>
                                <span class="badge bg-secondary-soft text-secondary rounded-pill px-2 py-0" style="font-size: 0.55rem;">
                                    {{ ucfirst(str_replace('_', ' ', $adjustment->reason)) }}
                                </span>
                                <div class="text-muted small mt-1">{{ $adjustment->user->name ?? 'System' }}</div>
                            </div>
                            @if($adjustment->notes)
                            <span class="text-muted small" title="{{ $adjustment->notes }}">
                                <i class="bi bi-file-text"></i>
                            </span>
                            @endif
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-5">
                    <i class="bi bi-sliders2 fs-1 d-block mb-3 text-muted"></i>
                    <p class="text-muted">No stock adjustments found</p>
                </div>
                @endforelse
            </div>

            <!-- ========== DESKTOP TABLE VIEW ========== -->
            <div class="table-responsive d-none d-md-block">
                <table class="table table-bordered table-hover align-middle mb-0">
                    <thead class="table">
                        <tr>
                            <th class="small">Adjustment #</th>
                            <th class="small">Date</th>
                            <th class="small">Product</th>
                            <th class="small">Type</th>
                            <th class="text-end small">Qty</th>
                            <th class="text-end small">Previous</th>
                            <th class="text-end small">New</th>
                            <th class="small">Reason</th>
                            <th class="small">User</th>
                            <th class="small">Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($adjustments as $adjustment)
                        <tr>
                            <td>
                                <span class="fw-semibold text-primary">{{ $adjustment->adjustment_number }}</span>
                            </td>
                            <td class="text-nowrap small">{{ $adjustment->created_at }}</td>
                            <td>
                                <div class="fw-semibold">{{ $adjustment->product->name ?? 'Deleted Product' }}</div>
                                <small class="text-muted">{{ $adjustment->product->sku ?? 'N/A' }}</small>
                            </td>
                            <td>
                                @if($adjustment->adjustment_type === 'addition')
                                    <span class="badge bg-success-soft text-success rounded-pill px-3 py-2">Addition</span>
                                @else
                                    <span class="badge bg-danger-soft text-danger rounded-pill px-3 py-2">Deduction</span>
                                @endif
                            </td>
                            <td class="text-end fw-bold {{ $adjustment->adjustment_type === 'addition' ? 'text-success' : 'text-danger' }}">
                                {{ $adjustment->adjustment_type === 'addition' ? '+' : '-' }}{{ $adjustment->quantity }}
                            </td>
                            <td class="text-end">{{ $adjustment->current_quantity }}</td>
                            <td class="text-end fw-bold">{{ $adjustment->new_quantity }}</td>
                            <td>
                                <span class="badge bg-secondary-soft text-secondary rounded-pill px-3 py-2">
                                    {{ ucfirst(str_replace('_', ' ', $adjustment->reason)) }}
                                </span>
                            </td>
                            <td>{{ $adjustment->user->name ?? 'System' }}</td>
                            <td>
                                @if($adjustment->notes)
                                    <span class="text-muted" title="{{ $adjustment->notes }}">
                                        {{ Str::limit($adjustment->notes, 30) }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center py-5">
                                <i class="bi bi-sliders2 fs-1 d-block mb-3 text-muted"></i>
                                <p class="text-muted">No stock adjustments found</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- ========== PAGINATION ========== -->
            <div class="mt-3">
                {{ $adjustments->links('livewire::bootstrap') }}
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
                        <i class="bi bi-sliders2"></i> New Stock Adjustment
                    </h5>
                    <button type="button" class="btn-close" wire:click="$set('showAdjustmentModal', false)"></button>
                </div>
                <div class="modal-body">
                    <form wire:submit="saveAdjustment">
                        <div class="form-group mb-3">
                            <label class="form-label fw-semibold small">Product <span class="text-danger">*</span></label>
                            <select wire:model.live="product_id" class="form-select @error('product_id') is-invalid @enderror" required>
                                <option value="">Select Product</option>
                                @foreach(\App\Models\Product::where('is_active', true)->orderBy('name')->get() as $product)
                                    <option value="{{ $product->id }}">{{ $product->name }} ({{ $product->sku }}) - Stock: {{ $product->stock_quantity }}</option>
                                @endforeach
                            </select>
                            @error('product_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        @if($selectedProduct)
                        <div class="bg-light-soft rounded-3 p-3 mb-3">
                            <div class="row g-1 small">
                                <div class="col-6"><span class="text-muted">Product:</span> <strong>{{ $selectedProduct->name }}</strong></div>
                                <div class="col-6"><span class="text-muted">SKU:</span> {{ $selectedProduct->sku }}</div>
                                <div class="col-12"><span class="text-muted">Current Stock:</span> <strong class="{{ $selectedProduct->stock_quantity <= 0 ? 'text-danger' : 'text-success' }}">{{ $selectedProduct->stock_quantity }}</strong></div>
                            </div>
                        </div>
                        @endif
                        
                        <div class="form-group mb-3">
                            <label class="form-label fw-semibold small">Adjustment Type <span class="text-danger">*</span></label>
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
                        
                        @if($selectedProduct && $adjustmentQuantity > 0)
                        <div class="bg-info-soft rounded-3 p-3 mb-3">
                            <div class="d-flex justify-content-between small">
                                <span>Current Stock:</span>
                                <span class="fw-bold">{{ $selectedProduct->stock_quantity }}</span>
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
                        <button type="submit" class="btn btn-primary btn-sm shadow-sm">
                            <i class="bi bi-check-lg"></i> Save Adjustment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

   
</div>