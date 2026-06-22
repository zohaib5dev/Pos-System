<div>
    <!-- ========== PAGE HEADER ========== -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <div>
            <h4 class="mb-0 fw-bold d-flex align-items-center gap-2">
                <i class="bi bi-receipt"></i> PO #{{ $purchase->purchase_number }}
            </h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('purchases.index') }}" class="text-decoration-none">Purchases</a></li>
                    <li class="breadcrumb-item active">#{{ $purchase->purchase_number }}</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex flex-wrap gap-1">
            @if($purchase->status === 'draft')
                <a href="{{ route('purchases.actions', ['action' => 'edit', 'id' => $purchase->id]) }}" class="btn btn-primary btn-sm shadow-sm">
                    <i class="bi bi-pencil"></i> <span class="d-none d-sm-inline">Edit</span>
                </a>
            @endif
            @if($purchase->status !== 'received' && $purchase->status !== 'cancelled')
                <a href="{{ route('purchases.actions', ['action' => 'receive', 'id' => $purchase->id]) }}" class="btn btn-success btn-sm shadow-sm">
                    <i class="bi bi-box-seam"></i> <span class="d-none d-sm-inline">Receive</span>
                </a>
            @endif
            <button wire:click="goBack" class="btn btn-secondary btn-sm shadow-sm">
                <i class="bi bi-arrow-left"></i> <span class="d-none d-sm-inline">Back</span>
            </button>
        </div>
    </div>

    <!-- ========== PO STATUS BAR ========== -->
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body p-3 p-sm-4">
            <div class="row g-2 align-items-center">
                <div class="col-12 col-md-6">
                    <span class="text-muted small">PO #{{ $purchase->purchase_number }}</span>
                    <h2 class="mb-0 fw-bold text-primary">{{ amo($purchase->total_amount) }}</h2>
                    <span class="text-muted small">{{ $purchase->purchase_date->format('M d, Y') }}</span>
                </div>
                <div class="col-12 col-md-6">
                    <div class="d-flex flex-wrap gap-2 justify-content-md-end">
                        <div>
                            <span class="text-muted d-block small">Status</span>
                            <select wire:change="updateStatus({{ $purchase->id }}, $event.target.value)" 
                                    class="form-select form-select-sm border-0 fw-semibold
                                        @if($purchase->status === 'received') text-success bg-success-soft
                                        @elseif($purchase->status === 'ordered') text-info bg-info-soft
                                        @elseif($purchase->status === 'partial') text-warning bg-warning-soft
                                        @elseif($purchase->status === 'draft') text-secondary bg-secondary-soft
                                        @else text-danger bg-danger-soft
                                        @endif"
                                    style="min-width: 120px;">
                                <option value="draft" {{ $purchase->status === 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="ordered" {{ $purchase->status === 'ordered' ? 'selected' : '' }}>Ordered</option>
                                <option value="partial" {{ $purchase->status === 'partial' ? 'selected' : '' }}>Partial</option>
                                <option value="received" {{ $purchase->status === 'received' ? 'selected' : '' }}>Received</option>
                                <option value="cancelled" {{ $purchase->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>
                        <div>
                            <span class="text-muted d-block small">Payment</span>
                            <span class="badge 
                                @if($purchase->payment_status === 'paid') bg-success-soft text-success
                                @elseif($purchase->payment_status === 'partial') bg-warning-soft text-warning
                                @else bg-secondary-soft text-secondary
                                @endif rounded-pill px-3 py-2">
                                {{ ucfirst($purchase->payment_status) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ========== MOBILE VIEW ========== -->
    <div class="d-md-none">
        <!-- Supplier Card -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-transparent border-0 pt-3">
                <h6 class="mb-0 fw-bold d-flex align-items-center gap-2">
                    <i class="bi bi-truck"></i> Supplier Information
                </h6>
            </div>
            <div class="card-body pt-0">
                <div class="d-flex flex-column gap-1">
                    <div class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-muted small">Name</span>
                        <span class="fw-semibold">{{ $purchase->supplier->name }}</span>
                    </div>
                    @if($purchase->supplier->company_name)
                    <div class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-muted small">Company</span>
                        <span>{{ $purchase->supplier->company_name }}</span>
                    </div>
                    @endif
                    <div class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-muted small">Email</span>
                        <span>{{ $purchase->supplier->email ?? '-' }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-muted small">Phone</span>
                        <span>{{ $purchase->supplier->phone }}</span>
                    </div>
                    @if($purchase->supplier->payment_terms)
                    <div class="d-flex justify-content-between py-1">
                        <span class="text-muted small">Payment Terms</span>
                        <span>{{ ucfirst(str_replace('_', ' ', $purchase->supplier->payment_terms)) }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Order Info Card -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-transparent border-0 pt-3">
                <h6 class="mb-0 fw-bold text-info d-flex align-items-center gap-2">
                    <i class="bi bi-info-circle"></i> Order Information
                </h6>
            </div>
            <div class="card-body pt-0">
                <div class="d-flex flex-column gap-1">
                    <div class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-muted small">PO Date</span>
                        <span class="fw-semibold">{{ $purchase->purchase_date->format('M d, Y') }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-muted small">Expected Delivery</span>
                        <span>{{ $purchase->expected_delivery_date?->format('M d, Y') ?? 'Not set' }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-muted small">Delivery Date</span>
                        <span>{{ $purchase->delivery_date?->format('M d, Y') ?? 'Not received' }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-1">
                        <span class="text-muted small">Created By</span>
                        <span>{{ $purchase->creator->name ?? 'System' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Summary Card -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-transparent border-0 pt-3">
                <h6 class="mb-0 fw-bold text-success d-flex align-items-center gap-2">
                    <i class="bi bi-credit-card"></i> Payment Summary
                </h6>
            </div>
            <div class="card-body pt-0">
                <div class="d-flex flex-column gap-1">
                    <div class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-muted small">Subtotal</span>
                        <span>{{ amo($purchase->subtotal) }}</span>
                    </div>
                    @if($purchase->discount_amount > 0)
                    <div class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-muted small">Discount</span>
                        <span class="text-danger">-{{ amo($purchase->discount_amount) }}</span>
                    </div>
                    @endif
                    @if($purchase->tax_amount > 0)
                    <div class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-muted small">Tax</span>
                        <span>{{ amo($purchase->tax_amount) }}</span>
                    </div>
                    @endif
                    @if($purchase->shipping_cost > 0)
                    <div class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-muted small">Shipping</span>
                        <span>{{ amo($purchase->shipping_cost) }}</span>
                    </div>
                    @endif
                    @if($purchase->other_cost > 0)
                    <div class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-muted small">Other Costs</span>
                        <span>{{ amo($purchase->other_cost) }}</span>
                    </div>
                    @endif
                    <div class="d-flex justify-content-between py-1 border-bottom">
                        <span class="fw-semibold">Total</span>
                        <span class="fw-bold text-primary">{{ amo($purchase->total_amount) }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-muted small">Paid</span>
                        <span class="text-success">{{ amo($purchase->paid_amount) }}</span>
                    </div>
                    @if($purchase->due_amount > 0)
                    <div class="d-flex justify-content-between py-1">
                        <span class="text-muted small">Due</span>
                        <span class="text-danger fw-bold">{{ amo($purchase->due_amount) }}</span>
                    </div>
                    @endif
                </div>
                @if($purchase->due_amount > 0)
                <button wire:click="openPaymentModal({{ $purchase->id }})" class="btn btn-primary btn-sm w-100 mt-3 shadow-sm">
                    <i class="bi bi-credit-card me-1"></i> Record Payment
                </button>
                @endif
            </div>
        </div>

        <!-- Items Card -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-transparent border-0 pt-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold text-secondary d-flex align-items-center gap-2">
                    <i class="bi bi-list-ul"></i> Items
                </h6>
                <span class="badge bg-secondary-soft text-secondary rounded-pill">{{ $purchase->items->count() }}</span>
            </div>
            <div class="card-body pt-0">
                @foreach($purchase->items as $item)
                <div class="border-bottom py-2">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="fw-semibold small">{{ $item->product->name ?? $item->product_name }}</div>
                            <div class="text-muted small">{{ $item->product->sku ?? $item->sku ?? '-' }}</div>
                        </div>
                        <div class="text-end">
                            <div class="fw-bold">{{ amo($item->total_cost) }}</div>
                            <div class="text-muted small">{{ $item->quantity }} × {{ amo($item->unit_cost) }}</div>
                            <span class="badge bg-{{ $item->received_quantity >= $item->quantity ? 'success' : ($item->received_quantity > 0 ? 'warning' : 'secondary') }}-soft text-{{ $item->received_quantity >= $item->quantity ? 'success' : ($item->received_quantity > 0 ? 'warning' : 'secondary') }} rounded-pill px-2 py-0" style="font-size: 0.55rem;">
                                Received: {{ $item->received_quantity }}
                            </span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Payment History -->
        @if($purchase->payments->isNotEmpty())
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-transparent border-0 pt-3">
                <h6 class="mb-0 fw-bold text-info d-flex align-items-center gap-2">
                    <i class="bi bi-clock-history"></i> Payment History
                </h6>
            </div>
            <div class="card-body pt-0">
                @foreach($purchase->payments as $payment)
                <div class="border-bottom py-2">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="fw-semibold small">{{ $payment->payment_number }}</div>
                            <div class="text-muted small">{{ $payment->method->name ?? 'Cash' }}</div>
                            <div class="text-muted small">{{ $payment->payment_date->format('M d, Y') }}</div>
                        </div>
                        <div class="text-end">
                            <span class="fw-bold text-success">{{ amo($payment->amount) }}</span>
                            @if($payment->reference_number)
                            <div class="text-muted small">Ref: {{ $payment->reference_number }}</div>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Notes -->
        @if($purchase->notes)
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-transparent border-0 pt-3">
                <h6 class="mb-0 fw-bold text-secondary d-flex align-items-center gap-2">
                    <i class="bi bi-file-text"></i> Notes
                </h6>
            </div>
            <div class="card-body pt-0">
                <p class="mb-0 small">{{ $purchase->notes }}</p>
            </div>
        </div>
        @endif
    </div>

    <!-- ========== DESKTOP VIEW ========== -->
    <div class="d-none d-md-block">
        <div class="row g-3">
            <!-- Supplier Information -->
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-transparent border-0 pt-3">
                        <h6 class="mb-0 fw-bold d-flex align-items-center gap-2">
                            <i class="bi bi-truck"></i> Supplier Information
                        </h6>
                    </div>
                    <div class="card-body pt-0">
                        <div class="d-flex flex-column gap-1">
                            <div class="d-flex justify-content-between py-1 border-bottom">
                                <span class="text-muted small">Name</span>
                                <span class="fw-semibold">{{ $purchase->supplier->name }}</span>
                            </div>
                            @if($purchase->supplier->company_name)
                            <div class="d-flex justify-content-between py-1 border-bottom">
                                <span class="text-muted small">Company</span>
                                <span>{{ $purchase->supplier->company_name }}</span>
                            </div>
                            @endif
                            <div class="d-flex justify-content-between py-1 border-bottom">
                                <span class="text-muted small">Email</span>
                                <span>{{ $purchase->supplier->email ?? '-' }}</span>
                            </div>
                            <div class="d-flex justify-content-between py-1 border-bottom">
                                <span class="text-muted small">Phone</span>
                                <span>{{ $purchase->supplier->phone }}</span>
                            </div>
                            @if($purchase->supplier->payment_terms)
                            <div class="d-flex justify-content-between py-1">
                                <span class="text-muted small">Payment Terms</span>
                                <span>{{ ucfirst(str_replace('_', ' ', $purchase->supplier->payment_terms)) }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Information -->
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-transparent border-0 pt-3">
                        <h6 class="mb-0 fw-bold text-info d-flex align-items-center gap-2">
                            <i class="bi bi-info-circle"></i> Order Information
                        </h6>
                    </div>
                    <div class="card-body pt-0">
                        <div class="d-flex flex-column gap-1">
                            <div class="d-flex justify-content-between py-1 border-bottom">
                                <span class="text-muted small">PO Date</span>
                                <span class="fw-semibold">{{ $purchase->purchase_date->format('M d, Y') }}</span>
                            </div>
                            <div class="d-flex justify-content-between py-1 border-bottom">
                                <span class="text-muted small">Expected Delivery</span>
                                <span>{{ $purchase->expected_delivery_date?->format('M d, Y') ?? 'Not set' }}</span>
                            </div>
                            <div class="d-flex justify-content-between py-1 border-bottom">
                                <span class="text-muted small">Delivery Date</span>
                                <span>{{ $purchase->delivery_date?->format('M d, Y') ?? 'Not received' }}</span>
                            </div>
                            <div class="d-flex justify-content-between py-1">
                                <span class="text-muted small">Created By</span>
                                <span>{{ $purchase->creator->name ?? 'System' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Summary -->
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-transparent border-0 pt-3">
                        <h6 class="mb-0 fw-bold text-success d-flex align-items-center gap-2">
                            <i class="bi bi-credit-card"></i> Payment Summary
                        </h6>
                    </div>
                    <div class="card-body pt-0">
                        <div class="d-flex flex-column gap-1">
                            <div class="d-flex justify-content-between py-1 border-bottom">
                                <span class="text-muted small">Subtotal</span>
                                <span>{{ amo($purchase->subtotal) }}</span>
                            </div>
                            @if($purchase->discount_amount > 0)
                            <div class="d-flex justify-content-between py-1 border-bottom">
                                <span class="text-muted small">Discount</span>
                                <span class="text-danger">-{{ amo($purchase->discount_amount) }}</span>
                            </div>
                            @endif
                            @if($purchase->tax_amount > 0)
                            <div class="d-flex justify-content-between py-1 border-bottom">
                                <span class="text-muted small">Tax</span>
                                <span>{{ amo($purchase->tax_amount) }}</span>
                            </div>
                            @endif
                            @if($purchase->shipping_cost > 0)
                            <div class="d-flex justify-content-between py-1 border-bottom">
                                <span class="text-muted small">Shipping</span>
                                <span>{{ amo($purchase->shipping_cost) }}</span>
                            </div>
                            @endif
                            @if($purchase->other_cost > 0)
                            <div class="d-flex justify-content-between py-1 border-bottom">
                                <span class="text-muted small">Other Costs</span>
                                <span>{{ amo($purchase->other_cost) }}</span>
                            </div>
                            @endif
                            <div class="d-flex justify-content-between py-1 border-bottom">
                                <span class="fw-semibold">Total</span>
                                <span class="fw-bold text-primary">{{ amo($purchase->total_amount) }}</span>
                            </div>
                            <div class="d-flex justify-content-between py-1 border-bottom">
                                <span class="text-muted small">Paid</span>
                                <span class="text-success">{{ amo($purchase->paid_amount) }}</span>
                            </div>
                            @if($purchase->due_amount > 0)
                            <div class="d-flex justify-content-between py-1">
                                <span class="text-muted small">Due</span>
                                <span class="text-danger fw-bold">{{ amo($purchase->due_amount) }}</span>
                            </div>
                            @endif
                        </div>
                        @if($purchase->due_amount > 0)
                        <button wire:click="openPaymentModal({{ $purchase->id }})" class="btn btn-primary btn-sm w-100 mt-3 shadow-sm">
                            <i class="bi bi-credit-card me-1"></i> Record Payment
                        </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Items Table -->
        <div class="card border-0 shadow-sm mt-3">
            <div class="card-header bg-transparent border-0 pt-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold text-secondary d-flex align-items-center gap-2">
                    <i class="bi bi-list-ul"></i> Items
                </h6>
                <span class="badge bg-secondary-soft text-secondary rounded-pill">{{ $purchase->items->count() }}</span>
            </div>
            <div class="card-body pt-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle mb-0">
                        <thead class="table">
                            <tr>
                                <th class="small">Product</th>
                                <th class="small">SKU</th>
                                <th class="text-end small">Qty</th>
                                <th class="text-end small">Received</th>
                                <th class="text-end small">Unit Cost</th>
                                <th class="text-end small">Discount</th>
                                <th class="text-end small">Tax</th>
                                <th class="text-end small">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($purchase->items as $item)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $item->product->name ?? $item->product_name }}</div>
                                </td>
                                <td><code class="bg- px-2 py-1 rounded small">{{ $item->product->sku ?? $item->sku ?? '-' }}</code></td>
                                <td class="text-end">{{ $item->quantity }}</td>
                                <td class="text-end">
                                    <span class="badge bg-{{ $item->received_quantity >= $item->quantity ? 'success' : ($item->received_quantity > 0 ? 'warning' : 'secondary') }}-soft text-{{ $item->received_quantity >= $item->quantity ? 'success' : ($item->received_quantity > 0 ? 'warning' : 'secondary') }} rounded-pill px-3 py-2">
                                        {{ $item->received_quantity }}
                                    </span>
                                </td>
                                <td class="text-end">{{ amo($item->unit_cost) }}</td>
                                <td class="text-end">{{ amo($item->discount_amount) }}</td>
                                <td class="text-end">{{ amo($item->tax_amount) }}</td>
                                <td class="text-end fw-bold">{{ amo($item->total_cost) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Payment History -->
        @if($purchase->payments->isNotEmpty())
        <div class="card border-0 shadow-sm mt-3">
            <div class="card-header bg-transparent border-0 pt-3">
                <h6 class="mb-0 fw-bold text-info d-flex align-items-center gap-2">
                    <i class="bi bi-clock-history"></i> Payment History
                </h6>
            </div>
            <div class="card-body pt-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle mb-0">
                        <thead class="table">
                            <tr>
                                <th class="small">Date</th>
                                <th class="small">Payment #</th>
                                <th class="small">Method</th>
                                <th class="text-end small">Amount</th>
                                <th class="small">Reference</th>
                                <th class="small">Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($purchase->payments as $payment)
                            <tr>
                                <td>{{ $payment->payment_date->format('M d, Y') }}</td>
                                <td><span class="fw-semibold">{{ $payment->payment_number }}</span></td>
                                <td>{{ $payment->method->name ?? 'Cash' }}</td>
                                <td class="text-end text-success fw-bold">{{ amo($payment->amount) }}</td>
                                <td>{{ $payment->reference_number ?? '-' }}</td>
                                <td>{{ $payment->notes ?? '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        <!-- Notes -->
        @if($purchase->notes)
        <div class="card border-0 shadow-sm mt-3">
            <div class="card-header bg-transparent border-0 pt-3">
                <h6 class="mb-0 fw-bold text-secondary d-flex align-items-center gap-2">
                    <i class="bi bi-file-text"></i> Notes
                </h6>
            </div>
            <div class="card-body pt-0">
                <p class="mb-0">{{ $purchase->notes }}</p>
            </div>
        </div>
        @endif
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

   
</div>