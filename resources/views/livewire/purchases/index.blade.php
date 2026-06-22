<div>
    <!-- ========== PAGE HEADER ========== -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <div>
            <h4 class="mb-0 fw-bold d-flex align-items-center gap-2">
                <i class="bi bi-cart-plus"></i> Purchases
            </h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item active">Purchases</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('purchases.create') }}" class="btn btn-primary btn-sm shadow-sm">
            <i class="bi bi-plus-lg"></i> <span class="d-none d-sm-inline">Add Purchase</span>
            <span class="d-sm-none">Add</span>
        </a>
    </div>

    <!-- ========== MAIN CARD ========== -->
    <div class="card border-0 shadow-lg">
        <div class="card-header bg-transparent border-0 d-flex flex-wrap align-items-center justify-content-between pt-3">
            <h5 class="mb-0 fw-bold d-flex align-items-center gap-2">
                <i class="bi bi-list-ul"></i> Purchase List
            </h5>
            <span class="badge bg-primary-soft text-primary rounded-pill">{{ $purchases->total() }}</span>
        </div>

        <div class="card-body pt-0">
            <!-- ========== MOBILE-FRIENDLY FILTERS ========== -->
            <div class="row g-2 mb-3">
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-0"><i class="bi bi-search"></i></span>
                        <input type="text"
                            wire:model.live.debounce.300ms="search"
                            class="form-control form-control-sm border-0 bg-light"
                            placeholder="Search PO #, supplier...">
                    </div>
                </div>
                <div class="col-6 col-sm-3 col-md-2">
                    <select wire:model.live="supplierFilter" class="form-select form-select-sm bg-light border-0">
                        <option value="">All Suppliers</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-sm-3 col-md-2">
                    <select wire:model.live="statusFilter" class="form-select form-select-sm bg-light border-0">
                        <option value="">All Status</option>
                        <option value="draft">Draft</option>
                        <option value="ordered">Ordered</option>
                        <option value="partial">Partial</option>
                        <option value="received">Received</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
                <div class="col-6 col-sm-3 col-md-2">
                    <select wire:model.live="paymentStatusFilter" class="form-select form-select-sm bg-light border-0">
                        <option value="">All Payment</option>
                        <option value="pending">Pending</option>
                        <option value="partial">Partial</option>
                        <option value="paid">Paid</option>
                    </select>
                </div>
                <div class="col-6 col-sm-3 col-md-2">
                    <select wire:model.live="dateRange" class="form-select form-select-sm bg-light border-0">
                        <option value="all">All Dates</option>
                        <option value="today">Today</option>
                        <option value="week">This Week</option>
                        <option value="month">This Month</option>
                        <option value="year">This Year</option>
                    </select>
                </div>
                <div class="col-12 col-sm-3 col-md-1">
                    <select wire:model.live="perPage" class="form-select form-select-sm bg-light border-0">
                        <option value="15">15</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
            </div>

            <!-- ========== BULK ACTIONS ========== -->
            @if(count($selectedPurchases) > 0)
            <div class="alert alert-info d-flex flex-wrap align-items-center justify-content-between gap-2 py-2 px-3">
                <span class="small"><i class="bi bi-check2-square me-1"></i><strong>{{ count($selectedPurchases) }}</strong> selected</span>
                <div class="d-flex gap-1">
                    <button wire:click="confirmBulkDelete" class="btn btn-danger btn-sm shadow-sm">
                        <i class="bi bi-trash"></i> <span class="d-none d-sm-inline">Delete</span>
                    </button>
                    <button wire:click="$set('selectedPurchases', [])" class="btn btn-secondary btn-sm shadow-sm">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            </div>
            @endif

            <!-- ========== MOBILE CARD VIEW ========== -->
            <div class="d-md-none">
                @forelse($purchases as $purchase)
                @php
                    $dueAmount = $purchase->total_amount - $purchase->paid_amount;
                @endphp
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body p-3">
                        <!-- Header: Checkbox + PO # + Status -->
                        <div class="d-flex align-items-start justify-content-between mb-2">
                            <div class="d-flex align-items-center gap-2 flex-grow-1">
                                <input type="checkbox" 
                                    wire:model.live="selectedPurchases" 
                                    value="{{ $purchase->id }}" 
                                    class="form-check-input mt-0">
                                <div>
                                    <div class="fw-bold text-primary">{{ $purchase->purchase_number }}</div>
                                    <div class="text-muted small">{{ $purchase->purchase_date->format('M d, Y') }}</div>
                                </div>
                            </div>
                            <div class="d-flex flex-column align-items-end gap-1">
                                @if($purchase->status === 'received')
                                <span class="badge bg-success-soft text-success rounded-pill px-2 py-0">Received</span>
                                @elseif($purchase->status === 'ordered')
                                <span class="badge bg-info-soft text-info rounded-pill px-2 py-0">Ordered</span>
                                @elseif($purchase->status === 'partial')
                                <span class="badge bg-warning-soft text-warning rounded-pill px-2 py-0">Partial</span>
                                @elseif($purchase->status === 'draft')
                                <span class="badge bg-secondary-soft text-secondary rounded-pill px-2 py-0">Draft</span>
                                @elseif($purchase->status === 'cancelled')
                                <span class="badge bg-danger-soft text-danger rounded-pill px-2 py-0">Cancelled</span>
                                @endif
                            </div>
                        </div>

                        <!-- Supplier & Amounts -->
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <div class="fw-semibold small">{{ $purchase->supplier->name }}</div>
                                @if($purchase->supplier->company_name)
                                <div class="text-muted small">{{ $purchase->supplier->company_name }}</div>
                                @endif
                            </div>
                            <div class="text-end">
                                <div class="fw-bold">{{ amo($purchase->total_amount) }}</div>
                                <div class="text-muted small">Due: <span class="{{ $dueAmount > 0 ? 'text-danger' : 'text-success' }}">{{ amo($dueAmount) }}</span></div>
                            </div>
                        </div>

                        <!-- Payment Status -->
                        <div class="mb-2">
                            @if($purchase->payment_status === 'paid')
                            <span class="badge bg-success-soft text-success rounded-pill">Paid</span>
                            @elseif($purchase->payment_status === 'partial')
                            <span class="badge bg-warning-soft text-warning rounded-pill">Partial</span>
                            @else
                            <span class="badge bg-danger-soft text-danger rounded-pill">Pending</span>
                            @endif
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                            <div class="d-flex gap-1">
                                <a href="{{ route('purchases.show', ['id' => $purchase->id]) }}"
                                    class="btn btn-info-soft text-info btn-sm rounded-circle shadow-sm" title="View" >
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('purchases.edit', ['id' => $purchase->id]) }}"
                                    class="btn btn-primary-soft text-primary btn-sm rounded-circle shadow-sm" title="Edit" >
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @if($purchase->status !== 'received' && $purchase->status !== 'cancelled')
                                <a href="{{ route('purchases.receive', ['id' => $purchase->id]) }}"
                                    class="btn btn-success-soft text-success btn-sm rounded-circle shadow-sm" title="Receive" >
                                    <i class="bi bi-box-seam"></i>
                                </a>
                                @endif
                                @if($dueAmount > 0 && $purchase->status !== 'cancelled')
                                <button wire:click="openPaymentModal({{ $purchase->id }})"
                                    class="btn btn-warning-soft text-warning btn-sm rounded-circle shadow-sm" title="Payment" >
                                    <i class="bi bi-credit-card"></i>
                                </button>
                                @endif
                                <button wire:click="confirmDelete({{ $purchase->id }})"
                                    class="btn btn-danger-soft text-danger btn-sm rounded-circle shadow-sm" title="Delete" >
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                            <span class="text-muted small">Paid: {{ amo($purchase->paid_amount) }}</span>
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-5">
                    <i class="bi bi-cart-plus fs-1 d-block mb-3 text-muted"></i>
                    <p class="text-muted">No purchases found</p>
                    <a href="{{ route('purchases.create') }}" class="btn btn-primary btn-sm shadow-sm">
                        <i class="bi bi-plus-lg"></i> Add Purchase
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
                            <th wire:click="sortBy('purchase_number')" style="cursor: pointer" class="small">
                                PO #
                                @if($sortField === 'purchase_number')
                                <i class="bi bi-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </th>
                            <th wire:click="sortBy('purchase_date')" style="cursor: pointer" class="small">
                                Date
                                @if($sortField === 'purchase_date')
                                <i class="bi bi-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </th>
                            <th class="small">Supplier</th>
                            <th wire:click="sortBy('total_amount')" style="cursor: pointer" class="text-end small">
                                Total
                                @if($sortField === 'total_amount')
                                <i class="bi bi-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </th>
                            <th class="text-end small">Paid</th>
                            <th class="text-end small">Due</th>
                            <th class="text-center small">Status</th>
                            <th class="text-center small">Payment</th>
                            <th class="text-center" width="180px">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($purchases as $purchase)
                        @php
                            $dueAmount = $purchase->total_amount - $purchase->paid_amount;
                        @endphp
                        <tr>
                            <td>
                                <input type="checkbox" wire:model.live="selectedPurchases" value="{{ $purchase->id }}" class="form-check-input">
                            </td>
                            <td>
                                <span class="fw-semibold">{{ $purchase->purchase_number }}</span>
                            </td>
                            <td>{{ $purchase->purchase_date->format('M d, Y') }}</td>
                            <td>
                                <div class="fw-semibold">{{ $purchase->supplier->name }}</div>
                                @if($purchase->supplier->company_name)
                                <small class="text-muted">{{ $purchase->supplier->company_name }}</small>
                                @endif
                            </td>
                            <td class="text-end fw-bold">{{ amo($purchase->total_amount) }}</td>
                            <td class="text-end">{{ amo($purchase->paid_amount) }}</td>
                            <td class="text-end fw-bold {{ $dueAmount > 0 ? 'text-danger' : 'text-success' }}">
                                {{ amo($dueAmount) }}
                            </td>
                            <td class="text-center">
                                @if($purchase->status === 'received')
                                <span class="badge bg-success-soft text-success rounded-pill px-3 py-2">Received</span>
                                @elseif($purchase->status === 'ordered')
                                <span class="badge bg-info-soft text-info rounded-pill px-3 py-2">Ordered</span>
                                @elseif($purchase->status === 'partial')
                                <span class="badge bg-warning-soft text-warning rounded-pill px-3 py-2">Partial</span>
                                @elseif($purchase->status === 'draft')
                                <span class="badge bg-secondary-soft text-secondary rounded-pill px-3 py-2">Draft</span>
                                @elseif($purchase->status === 'cancelled')
                                <span class="badge bg-danger-soft text-danger rounded-pill px-3 py-2">Cancelled</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($purchase->payment_status === 'paid')
                                <span class="badge bg-success-soft text-success rounded-pill px-3 py-2">Paid</span>
                                @elseif($purchase->payment_status === 'partial')
                                <span class="badge bg-warning-soft text-warning rounded-pill px-3 py-2">Partial</span>
                                @else
                                <span class="badge bg-danger-soft text-danger rounded-pill px-3 py-2">Pending</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex justify-content-center gap-1">
                                    <a href="{{ route('purchases.show', ['id' => $purchase->id]) }}"
                                        class="btn btn-info-soft text-info btn-sm rounded-circle shadow-sm" title="View" >
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('purchases.edit', ['id' => $purchase->id]) }}"
                                        class="btn btn-primary-soft text-primary btn-sm rounded-circle shadow-sm" title="Edit" >
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    @if($purchase->status !== 'received' && $purchase->status !== 'cancelled')
                                    <a href="{{ route('purchases.receive', ['id' => $purchase->id]) }}"
                                        class="btn btn-success-soft text-success btn-sm rounded-circle shadow-sm" title="Receive" >
                                        <i class="bi bi-box-seam"></i>
                                    </a>
                                    @endif
                                    @if($dueAmount > 0 && $purchase->status !== 'cancelled')
                                    <button wire:click="openPaymentModal({{ $purchase->id }})"
                                        class="btn btn-warning-soft text-warning btn-sm rounded-circle shadow-sm" title="Payment" >
                                        <i class="bi bi-credit-card"></i>
                                    </button>
                                    @endif
                                    <button wire:click="confirmDelete({{ $purchase->id }})"
                                        class="btn btn-danger-soft text-danger btn-sm rounded-circle shadow-sm" title="Delete" >
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center py-5">
                                <i class="bi bi-cart-plus fs-1 d-block mb-3 text-muted"></i>
                                <p class="text-muted">No purchases found</p>
                                <a href="{{ route('purchases.create') }}" class="btn btn-primary btn-sm shadow-sm">
                                    <i class="bi bi-plus-lg"></i> Add Purchase
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- ========== PAGINATION ========== -->
            <div class="mt-3">
                {{ $purchases->links('livewire::bootstrap') }}
            </div>
        </div>
    </div>

    <!-- ========== PAYMENT MODAL ========== -->
    @if($showPaymentModal)
    <div class="modal fade show" id="paymentModal" style="display: block; background: rgba(0,0,0,0.5);" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header border-0">
                    <h5 class="modal-title text-primary d-flex align-items-center gap-2">
                        <i class="bi bi-credit-card"></i> Record Payment
                    </h5>
                    <button type="button" class="btn-close" wire:click="$set('showPaymentModal', false)"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label fw-semibold small">Payment Amount <span class="text-danger">*</span></label>
                        <input type="number" 
                               wire:model.live="paymentAmount" 
                               step="0.01" 
                               min="0.01"
                               class="form-control @error('paymentAmount') is-invalid @enderror"
                               placeholder="Enter payment amount">
                        @error('paymentAmount') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label fw-semibold small">Payment Method <span class="text-danger">*</span></label>
                        <select wire:model="paymentMethod" class="form-select">
                            <option value="cash">Cash</option>
                            <option value="credit-card">Credit/Debit Card</option>
                            <option value="bank">Bank Transfer</option>
                            <option value="mobile">Mobile Payment</option>
                            <option value="check">Check</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label fw-semibold small">Reference Number</label>
                        <input type="text" 
                               wire:model="paymentReference" 
                               class="form-control"
                               placeholder="e.g., Transaction ID, Check #">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label fw-semibold small">Notes</label>
                        <textarea wire:model="paymentNotes" 
                                  rows="2" 
                                  class="form-control"
                                  placeholder="Optional notes about this payment"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary btn-sm shadow-sm" wire:click="$set('showPaymentModal', false)">
                        Cancel
                    </button>
                    <button type="button" class="btn btn-primary btn-sm shadow-sm" wire:click="processPayment">
                        <i class="bi bi-check-lg"></i> Record Payment
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- ========== DELETE MODAL ========== -->
    @if($showDeleteModal)
    <div class="modal fade show" id="deleteModal" style="display: block; background: rgba(0,0,0,0.5);" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header border-0">
                    <h5 class="modal-title text-danger d-flex align-items-center gap-2">
                        <i class="bi bi-exclamation-triangle-fill"></i> Delete Purchase
                    </h5>
                    <button type="button" class="btn-close" wire:click="$set('showDeleteModal', false)"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete purchase <strong>"{{ $purchaseToDelete?->purchase_number }}"</strong>?</p>
                    <p class="text-danger small"><i class="bi bi-info-circle me-1"></i>This action cannot be undone.</p>
                    
                    @if($purchaseToDelete && $purchaseToDelete->payments_count > 0)
                        <div class="alert alert-warning d-flex align-items-center gap-2 mt-2 py-2 small">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                            This purchase has <strong>{{ $purchaseToDelete->payments_count }}</strong> payments
                        </div>
                    @endif
                    
                    @if($purchaseToDelete && $purchaseToDelete->items_count > 0)
                        <div class="alert alert-info d-flex align-items-center gap-2 mt-2 py-2 small">
                            <i class="bi bi-info-circle"></i>
                            This purchase has <strong>{{ $purchaseToDelete->items_count }}</strong> items
                        </div>
                    @endif
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary btn-sm shadow-sm" wire:click="$set('showDeleteModal', false)">
                        Cancel
                    </button>
                    <button type="button" class="btn btn-danger btn-sm shadow-sm" wire:click="deletePurchase">
                        <i class="bi bi-trash"></i> Delete Purchase
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
                <div class="modal-header border-0">
                    <h5 class="modal-title text-danger d-flex align-items-center gap-2">
                        <i class="bi bi-exclamation-triangle-fill"></i> Delete Selected
                    </h5>
                    <button type="button" class="btn-close" wire:click="$set('showBulkDeleteModal', false)"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete <strong>{{ count($selectedPurchases) }}</strong> selected purchases?</p>
                    <p class="text-danger small"><i class="bi bi-info-circle me-1"></i>This action cannot be undone.</p>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary btn-sm shadow-sm" wire:click="$set('showBulkDeleteModal', false)">
                        Cancel
                    </button>
                    <button type="button" class="btn btn-danger btn-sm shadow-sm" wire:click="bulkDelete">
                        <i class="bi bi-trash"></i> Delete All Selected
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

 
</div>