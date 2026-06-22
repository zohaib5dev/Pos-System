<div>
    <!-- ========== PAGE HEADER ========== -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <div>
            <h4 class="mb-0 fw-bold d-flex align-items-center gap-2">
                <i class="bi bi-receipt"></i> Order #{{ $order->order_number }}
            </h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('orders.index') }}" class="text-decoration-none">Orders</a></li>
                    <li class="breadcrumb-item active">#{{ $order->order_number }}</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex flex-wrap gap-1">
            <a href="{{ route('orders.invoice', $order->id) }}" class="btn btn-primary btn-sm shadow-sm">
                <i class="bi bi-file-earmark-pdf"></i> <span class="d-none d-sm-inline">Invoice</span>
            </a>
            <button wire:click="goBack" class="btn btn-secondary btn-sm shadow-sm">
                <i class="bi bi-arrow-left"></i> <span class="d-none d-sm-inline">Back</span>
            </button>
        </div>
    </div>

    <!-- ========== ORDER STATUS BAR ========== -->
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body p-3 p-sm-4">
            <div class="row g-2 align-items-center">
                <div class="col-12 col-md-6">
                    <span class="text-muted small">Order #{{ $order->order_number }}</span>
                    <h2 class="mb-0 fw-bold text-primary">{{ amo($order->total_amount) }}</h2>
                    <span class="text-muted small">{{ $order->created_at }}</span>
                </div>
                <div class="col-12 col-md-6">
                    <div class="d-flex flex-wrap gap-2 justify-content-md-end">
                        <div>
                            <span class="text-muted d-block small">Status</span>
                            <select wire:change="updateStatus({{ $order->id }}, $event.target.value)" 
                                    class="form-select form-select-sm border-0 fw-semibold
                                        @if($order->status === 'completed') text-success bg-success-soft
                                        @elseif($order->status === 'pending') text-warning bg-warning-soft
                                        @elseif($order->status === 'processing') text-info bg-info-soft
                                        @elseif($order->status === 'cancelled') text-danger bg-danger-soft
                                        @else text-secondary bg-secondary-soft
                                        @endif"
                                    style="min-width: 120px;">
                                <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>⏳ Pending</option>
                                <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>⚙️ Processing</option>
                                <option value="completed" {{ $order->status === 'completed' ? 'selected' : '' }}>✅ Completed</option>
                                <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>❌ Cancelled</option>
                                <option value="refunded" {{ $order->status === 'refunded' ? 'selected' : '' }}>↩️ Refunded</option>
                            </select>
                        </div>
                        <div>
                            <span class="text-muted d-block small">Payment</span>
                            <select wire:change="updatePaymentStatus({{ $order->id }}, $event.target.value)" 
                                    class="form-select form-select-sm border-0 fw-semibold
                                        @if($order->payment_status === 'paid') text-success bg-success-soft
                                        @elseif($order->payment_status === 'partial') text-warning bg-warning-soft
                                        @elseif($order->payment_status === 'refunded') text-secondary bg-secondary-soft
                                        @else text-info bg-info-soft
                                        @endif"
                                    style="min-width: 120px;">
                                <option value="pending" {{ $order->payment_status === 'pending' ? 'selected' : '' }}>⏳ Pending</option>
                                <option value="partial" {{ $order->payment_status === 'partial' ? 'selected' : '' }}>🔄 Partial</option>
                                <option value="paid" {{ $order->payment_status === 'paid' ? 'selected' : '' }}>✅ Paid</option>
                                <option value="refunded" {{ $order->payment_status === 'refunded' ? 'selected' : '' }}>↩️ Refunded</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ========== MOBILE CARD VIEW ========== -->
    <div class="d-md-none">
        <!-- Customer Card -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-transparent border-0 pt-3">
                <h6 class="mb-0 fw-bold text-info d-flex align-items-center gap-2">
                    <i class="bi bi-person"></i> Customer Information
                </h6>
            </div>
            <div class="card-body pt-0">
                @if($order->customer)
                <div class="d-flex flex-column gap-1">
                    <div class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-muted small">Name</span>
                        <span class="fw-semibold">{{ $order->customer->name }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-muted small">Email</span>
                        <span>{{ $order->customer->email ?? '-' }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-muted small">Phone</span>
                        <span>{{ $order->customer->phone ?? '-' }}</span>
                    </div>
                    @if($order->customer->address)
                    <div class="d-flex justify-content-between py-1">
                        <span class="text-muted small">Address</span>
                        <span class="text-end">{{ $order->customer->address }}</span>
                    </div>
                    @endif
                </div>
                @else
                <p class="text-center text-muted mb-0">Walk-in Customer</p>
                @endif
            </div>
        </div>

        <!-- Order Info Card -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-transparent border-0 pt-3">
                <h6 class="mb-0 fw-bold d-flex align-items-center gap-2">
                    <i class="bi bi-info-circle"></i> Order Information
                </h6>
            </div>
            <div class="card-body pt-0">
                <div class="d-flex flex-column gap-1">
                    <div class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-muted small">Order Date</span>
                        <span class="fw-semibold">{{ $order->created_at }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-muted small">Order Type</span>
                        <span class="badge bg-info-soft text-info rounded-pill">{{ ucfirst($order->order_type) }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-muted small">Payment Method</span>
                        <span>{{ $order->payments->first()->method->name ?? 'Cash' }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-1">
                        <span class="text-muted small">Processed By</span>
                        <span>{{ $order->creator->name ?? 'System' }}</span>
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
                        <span>{{ amo($order->subtotal) }}</span>
                    </div>
                    @if($order->discount_amount > 0)
                    <div class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-muted small">Discount</span>
                        <span class="text-danger">-{{ amo($order->discount_amount) }}</span>
                    </div>
                    @endif
                    @if($order->tax_amount > 0)
                    <div class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-muted small">Tax</span>
                        <span>{{ amo($order->tax_amount) }}</span>
                    </div>
                    @endif
                    <div class="d-flex justify-content-between py-1 border-bottom">
                        <span class="fw-semibold">Total</span>
                        <span class="fw-bold text-primary">{{ amo($order->total_amount) }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-muted small">Paid</span>
                        <span class="text-success">{{ amo($order->paid_amount) }}</span>
                    </div>
                    @if($order->due_amount > 0)
                    <div class="d-flex justify-content-between py-1">
                        <span class="text-muted small">Due</span>
                        <span class="text-danger fw-bold">{{ amo($order->due_amount) }}</span>
                    </div>
                    @endif
                </div>
                @if($order->due_amount > 0)
                <button wire:click="openPaymentModal({{ $order->id }})" class="btn btn-primary btn-sm w-100 mt-3 shadow-sm">
                    <i class="bi bi-credit-card me-1"></i> Record Payment
                </button>
                @endif
            </div>
        </div>

        <!-- Order Items Card -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-transparent border-0 pt-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold text-secondary d-flex align-items-center gap-2">
                    <i class="bi bi-list-ul"></i> Order Items
                </h6>
                <span class="badge bg-secondary-soft text-secondary rounded-pill">{{ $order->items->count() }}</span>
            </div>
            <div class="card-body pt-0">
                @foreach($order->items as $item)
                <div class="border-bottom py-2">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="fw-semibold small">{{ $item->product_name }}</div>
                            @if($item->variant)
                            <div class="text-muted small">{{ $item->variant->name }}: {{ $item->variant->value }}</div>
                            @endif
                            <div class="text-muted small">{{ $item->quantity }} × {{ amo($item->unit_price) }}</div>
                        </div>
                        <div class="text-end">
                            <span class="fw-bold">{{ amo($item->total) }}</span>
                        </div>
                    </div>
                </div>
                @endforeach
                
                <!-- Mobile Totals -->
                <div class="border-top pt-2 mt-2">
                    <div class="d-flex justify-content-between small">
                        <span class="text-muted">Subtotal</span>
                        <span>{{ amo($order->subtotal) }}</span>
                    </div>
                    @if($order->discount_amount > 0)
                    <div class="d-flex justify-content-between small">
                        <span class="text-muted">Discount</span>
                        <span class="text-danger">-{{ amo($order->discount_amount) }}</span>
                    </div>
                    @endif
                    @if($order->tax_amount > 0)
                    <div class="d-flex justify-content-between small">
                        <span class="text-muted">Tax</span>
                        <span>{{ amo($order->tax_amount) }}</span>
                    </div>
                    @endif
                    <div class="d-flex justify-content-between fw-bold">
                        <span>Total</span>
                        <span class="text-primary">{{ amo($order->total_amount) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment History -->
        @if($order->payments->isNotEmpty())
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-transparent border-0 pt-3">
                <h6 class="mb-0 fw-bold text-info d-flex align-items-center gap-2">
                    <i class="bi bi-clock-history"></i> Payment History
                </h6>
            </div>
            <div class="card-body pt-0">
                @foreach($order->payments as $payment)
                <div class="border-bottom py-2">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="fw-semibold small">{{ $payment->payment_number }}</div>
                            <div class="text-muted small">{{ $payment->method->name ?? 'Cash' }}</div>
                            <div class="text-muted small">{{ $payment->created_at }}</div>
                        </div>
                        <div class="text-end">
                            <span class="fw-bold {{ $payment->amount < 0 ? 'text-danger' : 'text-success' }}">
                                {{ amo(abs($payment->amount)) }}
                                @if($payment->amount < 0)
                                <span class="text-danger small">(Refund)</span>
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Notes -->
        @if($order->notes)
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-transparent border-0 pt-3">
                <h6 class="mb-0 fw-bold text-secondary d-flex align-items-center gap-2">
                    <i class="bi bi-file-text"></i> Notes
                </h6>
            </div>
            <div class="card-body pt-0">
                <p class="mb-0 small">{{ $order->notes }}</p>
            </div>
        </div>
        @endif
    </div>

    <!-- ========== DESKTOP VIEW ========== -->
    <div class="d-none d-md-block">
        <div class="row g-3">
            <!-- Customer Information -->
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-transparent border-0 pt-3">
                        <h6 class="mb-0 fw-bold text-info d-flex align-items-center gap-2">
                            <i class="bi bi-person"></i> Customer Information
                        </h6>
                    </div>
                    <div class="card-body pt-0">
                        @if($order->customer)
                        <div class="d-flex flex-column gap-1">
                            <div class="d-flex justify-content-between py-1 border-bottom">
                                <span class="text-muted small">Name</span>
                                <span class="fw-semibold">{{ $order->customer->name }}</span>
                            </div>
                            <div class="d-flex justify-content-between py-1 border-bottom">
                                <span class="text-muted small">Email</span>
                                <span>{{ $order->customer->email ?? '-' }}</span>
                            </div>
                            <div class="d-flex justify-content-between py-1 border-bottom">
                                <span class="text-muted small">Phone</span>
                                <span>{{ $order->customer->phone ?? '-' }}</span>
                            </div>
                            @if($order->customer->address)
                            <div class="d-flex justify-content-between py-1">
                                <span class="text-muted small">Address</span>
                                <span>{{ $order->customer->address }}</span>
                            </div>
                            @endif
                        </div>
                        @else
                        <p class="text-center text-muted mb-0">Walk-in Customer</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Order Information -->
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-transparent border-0 pt-3">
                        <h6 class="mb-0 fw-bold d-flex align-items-center gap-2">
                            <i class="bi bi-info-circle"></i> Order Information
                        </h6>
                    </div>
                    <div class="card-body pt-0">
                        <div class="d-flex flex-column gap-1">
                            <div class="d-flex justify-content-between py-1 border-bottom">
                                <span class="text-muted small">Order Date</span>
                                <span class="fw-semibold">{{ $order->created_at }}</span>
                            </div>
                            <div class="d-flex justify-content-between py-1 border-bottom">
                                <span class="text-muted small">Order Type</span>
                                <span class="badge bg-info-soft text-info rounded-pill">{{ ucfirst($order->order_type) }}</span>
                            </div>
                            <div class="d-flex justify-content-between py-1 border-bottom">
                                <span class="text-muted small">Payment Method</span>
                                <span>{{ $order->payments->first()->method->name ?? 'Cash' }}</span>
                            </div>
                            <div class="d-flex justify-content-between py-1">
                                <span class="text-muted small">Processed By</span>
                                <span>{{ $order->creator->name ?? 'System' }}</span>
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
                                <span>{{ amo($order->subtotal) }}</span>
                            </div>
                            @if($order->discount_amount > 0)
                            <div class="d-flex justify-content-between py-1 border-bottom">
                                <span class="text-muted small">Discount</span>
                                <span class="text-danger">-{{ amo($order->discount_amount) }}</span>
                            </div>
                            @endif
                            @if($order->tax_amount > 0)
                            <div class="d-flex justify-content-between py-1 border-bottom">
                                <span class="text-muted small">Tax</span>
                                <span>{{ amo($order->tax_amount) }}</span>
                            </div>
                            @endif
                            <div class="d-flex justify-content-between py-1 border-bottom">
                                <span class="fw-semibold">Total</span>
                                <span class="fw-bold text-primary">{{ amo($order->total_amount) }}</span>
                            </div>
                            <div class="d-flex justify-content-between py-1 border-bottom">
                                <span class="text-muted small">Paid</span>
                                <span class="text-success">{{ amo($order->paid_amount) }}</span>
                            </div>
                            @if($order->due_amount > 0)
                            <div class="d-flex justify-content-between py-1">
                                <span class="text-muted small">Due</span>
                                <span class="text-danger fw-bold">{{ amo($order->due_amount) }}</span>
                            </div>
                            @endif
                        </div>
                        @if($order->due_amount > 0)
                        <button wire:click="openPaymentModal({{ $order->id }})" class="btn btn-primary btn-sm w-100 mt-3 shadow-sm">
                            <i class="bi bi-credit-card me-1"></i> Record Payment
                        </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Items Table -->
        <div class="card border-0 shadow-sm mt-3">
            <div class="card-header bg-transparent border-0 pt-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold text-secondary d-flex align-items-center gap-2">
                    <i class="bi bi-list-ul"></i> Order Items
                </h6>
                <span class="badge bg-secondary-soft text-secondary rounded-pill">{{ $order->items->count() }}</span>
            </div>
            <div class="card-body pt-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle mb-0">
                        <thead class="table-">
                            <tr>
                                <th class="small">Product</th>
                                <th class="small">SKU</th>
                                <th class="text-end small">Price</th>
                                <th class="text-end small">Qty</th>
                                <th class="text-end small">Discount</th>
                                <th class="text-end small">Tax</th>
                                <th class="text-end small">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $item->product_name }}</div>
                                    @if($item->variant)
                                    <div class="text-muted small">{{ $item->variant->name }}: {{ $item->variant->value }}</div>
                                    @endif
                                </td>
                                <td><code class="bg- px-2 py-1 rounded small">{{ $item->sku ?? '-' }}</code></td>
                                <td class="text-end">{{ amo($item->unit_price) }}</td>
                                <td class="text-end">{{ $item->quantity }}</td>
                                <td class="text-end">{{ amo($item->discount_amount ?? 0) }}</td>
                                <td class="text-end">{{ amo($item->tax_amount ?? 0) }}</td>
                                <td class="text-end fw-bold">{{ amo($item->total) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table">
                            <tr>
                                <td colspan="6" class="text-end fw-semibold">Subtotal</td>
                                <td class="text-end">{{ amo($order->subtotal) }}</td>
                            </tr>
                            @if($order->discount_amount > 0)
                            <tr>
                                <td colspan="6" class="text-end text-danger fw-semibold">Discount</td>
                                <td class="text-end text-danger">-{{ amo($order->discount_amount) }}</td>
                            </tr>
                            @endif
                            @if($order->tax_amount > 0)
                            <tr>
                                <td colspan="6" class="text-end fw-semibold">Tax</td>
                                <td class="text-end">{{ amo($order->tax_amount) }}</td>
                            </tr>
                            @endif
                            <tr>
                                <td colspan="6" class="text-end fw-bold">Total</td>
                                <td class="text-end fw-bold text-primary">{{ amo($order->total_amount) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- Payment History -->
        @if($order->payments->isNotEmpty())
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
                                <th class="small">Processed By</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->payments as $payment)
                            <tr>
                                <td>{{ $payment->created_at }}</td>
                                <td><span class="fw-semibold">{{ $payment->payment_number }}</span></td>
                                <td>{{ $payment->method->name ?? 'Cash' }}</td>
                                <td class="text-end {{ $payment->amount < 0 ? 'text-danger' : 'text-success' }}">
                                    {{ amo(abs($payment->amount)) }}
                                    @if($payment->amount < 0)
                                    <span class="text-danger small">(Refund)</span>
                                    @endif
                                </td>
                                <td>{{ $payment->reference_number ?? '-' }}</td>
                                <td>{{ $payment->creator->name ?? 'System' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        <!-- Notes -->
        @if($order->notes)
        <div class="card border-0 shadow-sm mt-3">
            <div class="card-header bg-transparent border-0 pt-3">
                <h6 class="mb-0 fw-bold text-secondary d-flex align-items-center gap-2">
                    <i class="bi bi-file-text"></i> Notes
                </h6>
            </div>
            <div class="card-body pt-0">
                <p class="mb-0">{{ $order->notes }}</p>
            </div>
        </div>
        @endif
    </div>

    <!-- Payment Modal -->
    @if($showPaymentModal)
    <div class="modal fade show" id="paymentModal" style="display: block; background: rgba(0,0,0,0.5);" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header border-0">
                    <h5 class="modal-title text-primary d-flex align-items-center gap-2">
                        <i class="bi bi-credit-card"></i> Process Payment
                    </h5>
                    <button type="button" class="btn-close" wire:click="$set('showPaymentModal', false)"></button>
                </div>
                <div class="modal-body">
                    <div class="bg-light-soft rounded-3 p-3 mb-3">
                        <div class="row g-1 small">
                            <div class="col-6">Total: <strong>{{ amo($order->total_amount) }}</strong></div>
                            <div class="col-6">Paid: <strong class="text-success">{{ amo($order->paid_amount) }}</strong></div>
                            <div class="col-12">Due: <strong class="text-danger">{{ amo($order->due_amount) }}</strong></div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label fw-semibold small">Payment Amount <span class="text-danger">*</span></label>
                        <input type="number" 
                               wire:model.live="paymentAmount" 
                               step="0.01" 
                               min="0.01"
                               max="{{ $order->due_amount }}" 
                               class="form-control @error('paymentAmount') is-invalid @enderror"
                               placeholder="Enter amount">
                        @error('paymentAmount') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label fw-semibold small">Payment Method</label>
                        <select wire:model.live="paymentMethod" class="form-select">
                            <option value="cash">Cash</option>
                            <option value="credit-card">Credit/Debit Card</option>
                            <option value="bank">Bank Transfer</option>
                            <option value="mobile">Mobile Payment</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label fw-semibold small">Reference Number</label>
                        <input type="text" 
                               wire:model="paymentReference" 
                               class="form-control"
                               placeholder="e.g., Transaction ID">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label fw-semibold small">Notes</label>
                        <textarea wire:model="paymentNotes" 
                                  rows="2" 
                                  class="form-control"
                                  placeholder="Optional notes"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary btn-sm shadow-sm" wire:click="$set('showPaymentModal', false)">
                        Cancel
                    </button>
                    <button type="button" class="btn btn-primary btn-sm shadow-sm" wire:click="processPayment">
                        <i class="bi bi-check-lg"></i> Process Payment
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Refund Modal -->
    @if($showRefundModal)
    <div class="modal fade show" id="refundModal" style="display: block; background: rgba(0,0,0,0.5);" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header border-0">
                    <h5 class="modal-title text-warning d-flex align-items-center gap-2">
                        <i class="bi bi-arrow-counterclockwise"></i> Process Refund
                    </h5>
                    <button type="button" class="btn-close" wire:click="$set('showRefundModal', false)"></button>
                </div>
                <div class="modal-body">
                    <div class="bg-light-soft rounded-3 p-3 mb-3">
                        <div class="row g-1 small">
                            <div class="col-6">Order: <strong>#{{ $order->order_number }}</strong></div>
                            <div class="col-6">Date: <strong>{{ $order->created_at }}</strong></div>
                            <div class="col-6">Customer: <strong>{{ $order->customer_name ?? 'Walk-in' }}</strong></div>
                            <div class="col-6">Total: <strong>{{ amo($order->total_amount) }}</strong></div>
                        </div>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead class="table">
                                <tr>
                                    <th width="40px">
                                        <input type="checkbox" wire:click="selectAllItems" {{ count($selectedRefundItems) === count($refundItems) ? 'checked' : '' }}>
                                    </th>
                                    <th class="small">Product</th>
                                    <th class="text-end small">Price</th>
                                    <th class="text-end small">Qty</th>
                                    <th class="text-end small">Refund Qty</th>
                                    <th class="text-end small">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($refundItems as $index => $item)
                                <tr>
                                    <td>
                                        <input type="checkbox" 
                                               wire:model.live="selectedRefundItems" 
                                               value="{{ $item['id'] }}">
                                    </td>
                                    <td>{{ $item['product_name'] }}</td>
                                    <td class="text-end">{{ amo($item['unit_price']) }}</td>
                                    <td class="text-end">{{ $item['quantity'] }}</td>
                                    <td>
                                        <input type="number" 
                                               wire:model.live="refundItems.{{ $index }}.refund_quantity" 
                                               min="0" 
                                               max="{{ $item['quantity'] }}" 
                                               class="form-control form-control-sm"
                                               style="width: 70px;"
                                               {{ !in_array($item['id'], $selectedRefundItems) ? 'disabled' : '' }}>
                                    </td>
                                    <td class="text-end">
                                        {{ amo($item['unit_price'] * ($item['refund_quantity'] ?? 0)) }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label fw-semibold small">Refund Amount</label>
                        <input type="text" 
                               wire:model="refundAmount" 
                               readonly
                               class="form-control bg-light fw-bold">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label fw-semibold small">Reason for Refund <span class="text-danger">*</span></label>
                        <textarea wire:model="refundReason" 
                                  rows="2" 
                                  class="form-control @error('refundReason') is-invalid @enderror"
                                  placeholder="Enter reason for refund"></textarea>
                        @error('refundReason') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary btn-sm shadow-sm" wire:click="$set('showRefundModal', false)">
                        Cancel
                    </button>
                    <button type="button" class="btn btn-warning btn-sm shadow-sm" wire:click="processRefund">
                        <i class="bi bi-arrow-counterclockwise"></i> Process Refund
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

   
</div>