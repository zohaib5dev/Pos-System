<div>
    <!-- ========== PAGE HEADER ========== -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <div>
            <h4 class="mb-0 fw-bold text-primary d-flex align-items-center gap-2">
                <i class="bi bi-truck"></i> Supplier Details
            </h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('suppliers.index') }}" class="text-decoration-none">Suppliers</a></li>
                    <li class="breadcrumb-item active">{{ Str::limit($supplier->name, 20) }}</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex flex-wrap gap-1">
            <a href="{{ route('suppliers.actions', ['action' => 'edit', 'id' => $supplier->id]) }}" class="btn btn-primary btn-sm shadow-sm">
                <i class="bi bi-pencil"></i> <span class="d-none d-sm-inline">Edit</span>
            </a>
            <a href="{{ route('suppliers.ledger', $supplier->id) }}" class="btn btn-info btn-sm shadow-sm">
                <i class="bi bi-book"></i> <span class="d-none d-sm-inline">Ledger</span>
            </a>
            <button wire:click="goBack" class="btn btn-secondary btn-sm shadow-sm">
                <i class="bi bi-arrow-left"></i> <span class="d-none d-sm-inline">Back</span>
            </button>
        </div>
    </div>

    <!-- ========== SUPPLIER HEADER CARD ========== -->
    <div class="card border-0 shadow-lg mb-3">
        <div class="card-body p-3 p-sm-4">
            <div class="d-flex flex-wrap align-items-center gap-3">
                <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" 
                     style="width: 72px; height: 72px;">
                    <span class="text-white fw-bold" style="font-size: 1.5rem;">
                        {{ strtoupper(substr($supplier->name, 0, 2)) }}
                    </span>
                </div>
                <div class="flex-grow-1">
                    <h4 class="mb-0 fw-bold">{{ $supplier->name }}</h4>
                    <div class="d-flex flex-wrap gap-2 mt-1">
                        @if($supplier->company_name)
                        <span class="badge bg-primary-soft text-primary rounded-pill">
                            <i class="bi bi-building me-1"></i> {{ $supplier->company_name }}
                        </span>
                        @endif
                        <span class="badge bg-secondary-soft text-secondary rounded-pill">
                            <i class="bi bi-hash me-1"></i> ID: #{{ $supplier->id }}
                        </span>
                        @if($supplier->is_active)
                        <span class="badge bg-success text-white rounded-pill">
                            <i class="bi bi-check-circle-fill me-1"></i> Active
                        </span>
                        @else
                        <span class="badge bg-danger text-white rounded-pill">
                            <i class="bi bi-x-circle-fill me-1"></i> Inactive
                        </span>
                        @endif
                    </div>
                </div>
                <div class="text-end">
                    <div class="text-muted small">Joined</div>
                    <div class="fw-semibold">{{ $supplier->created_at }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- ========== MOBILE VIEW ========== -->
    <div class="d-md-none">
        <!-- Basic Information Card -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-transparent border-0 pt-3">
                <h6 class="mb-0 fw-bold text-primary d-flex align-items-center gap-2">
                    <i class="bi bi-info-circle"></i> Basic Information
                </h6>
            </div>
            <div class="card-body pt-0">
                <div class="d-flex flex-column gap-1">
                    <div class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-muted small">Company</span>
                        <span>{{ $supplier->company_name ?? '-' }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-muted small">Email</span>
                        <span>{{ $supplier->email ?? '-' }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-muted small">Phone</span>
                        <span class="fw-semibold">{{ $supplier->phone }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-muted small">Alternative Phone</span>
                        <span>{{ $supplier->alternative_phone ?? '-' }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-1">
                        <span class="text-muted small">Tax Number</span>
                        <span>{{ $supplier->tax_number ?? '-' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Address Information Card -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-transparent border-0 pt-3">
                <h6 class="mb-0 fw-bold text-info d-flex align-items-center gap-2">
                    <i class="bi bi-geo-alt"></i> Address Information
                </h6>
            </div>
            <div class="card-body pt-0">
                <div class="d-flex flex-column gap-1">
                    <div class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-muted small">Address</span>
                        <span>{{ $supplier->address ?? '-' }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-muted small">City</span>
                        <span>{{ $supplier->city ?? '-' }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-muted small">State</span>
                        <span>{{ $supplier->state ?? '-' }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-muted small">Postal Code</span>
                        <span>{{ $supplier->postal_code ?? '-' }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-1">
                        <span class="text-muted small">Country</span>
                        <span>{{ $supplier->country ?? '-' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Information Card -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-transparent border-0 pt-3">
                <h6 class="mb-0 fw-bold text-success d-flex align-items-center gap-2">
                    <i class="bi bi-credit-card"></i> Payment Information
                </h6>
            </div>
            <div class="card-body pt-0">
                <div class="d-flex justify-content-between py-1">
                    <span class="text-muted small">Payment Terms</span>
                    <span class="fw-semibold">
                        @if($supplier->payment_terms)
                        {{ ucfirst(str_replace('_', ' ', $supplier->payment_terms)) }}
                        @else
                        -
                        @endif
                    </span>
                </div>
                @if($supplier->notes)
                <div class="d-flex justify-content-between py-1 border-top mt-1 pt-1">
                    <span class="text-muted small">Notes</span>
                    <span class="text-end">{{ $supplier->notes }}</span>
                </div>
                @endif
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row g-2 mb-3">
            <div class="col-6">
                <div class="card border-0 shadow-sm stat-card stat-card-info text-center">
                    <div class="card-body p-2">
                        <h5 class="mb-0 fw-bold text-info">{{ $supplier->purchases_count }}</h5>
                        <p class="mb-0 text-muted small">Purchases</p>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="card border-0 shadow-sm stat-card stat-card-success text-center">
                    <div class="card-body p-2">
                        <h5 class="mb-0 fw-bold text-success">{{ amo($supplier->purchases->sum('total_amount')) }}</h5>
                        <p class="mb-0 text-muted small">Total Spent</p>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="card border-0 shadow-sm stat-card stat-card-danger text-center">
                    <div class="card-body p-2">
                        <h5 class="mb-0 fw-bold text-danger">{{ amo($supplier->purchases->where('payment_status', '!=', 'paid')->sum('due_amount')) }}</h5>
                        <p class="mb-0 text-muted small">Outstanding</p>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="card border-0 shadow-sm stat-card stat-card-warning text-center">
                    <div class="card-body p-2">
                        <h6 class="mb-0 fw-bold text-warning">{{ $supplier->purchases->first()?->purchase_date->format('M d') ?? 'N/A' }}</h6>
                        <p class="mb-0 text-muted small">Last Purchase</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Purchases - Mobile -->
        @if($supplier->purchases->isNotEmpty())
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-transparent border-0 pt-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold text-secondary d-flex align-items-center gap-2">
                    <i class="bi bi-clock-history"></i> Recent Purchases
                </h6>
                <span class="badge bg-secondary-soft text-secondary rounded-pill">{{ $supplier->purchases->count() }}</span>
            </div>
            <div class="card-body pt-0">
                @foreach($supplier->purchases->take(5) as $purchase)
                <div class="border-bottom py-2">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <a href="{{ route('purchases.show', $purchase->id) }}" class="fw-semibold text-primary text-decoration-none small">
                                {{ $purchase->purchase_number }}
                            </a>
                            <div class="text-muted small">{{ $purchase->purchase_date->format('M d, Y') }}</div>
                        </div>
                        <div class="text-end">
                            <div class="fw-bold">{{ amo($purchase->total_amount) }}</div>
                            <div class="text-muted small">Due: <span class="{{ $purchase->due_amount > 0 ? 'text-danger' : 'text-success' }}">{{ amo($purchase->due_amount) }}</span></div>
                            @if($purchase->status === 'received')
                            <span class="badge bg-success-soft text-success rounded-pill px-2 py-0" style="font-size: 0.55rem;">Received</span>
                            @elseif($purchase->status === 'ordered')
                            <span class="badge bg-info-soft text-info rounded-pill px-2 py-0" style="font-size: 0.55rem;">Ordered</span>
                            @elseif($purchase->status === 'partial')
                            <span class="badge bg-warning-soft text-warning rounded-pill px-2 py-0" style="font-size: 0.55rem;">Partial</span>
                            @else
                            <span class="badge bg-secondary-soft text-secondary rounded-pill px-2 py-0" style="font-size: 0.55rem;">{{ ucfirst($purchase->status) }}</span>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Audit - Mobile -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-transparent border-0 pt-3">
                <h6 class="mb-0 fw-bold text-secondary d-flex align-items-center gap-2">
                    <i class="bi bi-clock"></i> Audit Information
                </h6>
            </div>
            <div class="card-body pt-0">
                <div class="d-flex flex-column gap-1 small">
                    <div class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-muted">Created By</span>
                        <span>{{ $supplier->creator->name ?? 'System' }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-muted">Created At</span>
                        <span>{{ $supplier->created_at }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-1">
                        <span class="text-muted">Last Updated</span>
                        <span>{{ $supplier->updated_at }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ========== DESKTOP VIEW ========== -->
    <div class="d-none d-md-block">
        <div class="row g-3">
            <!-- Basic Information -->
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-transparent border-0 pt-3">
                        <h6 class="mb-0 fw-bold text-primary d-flex align-items-center gap-2">
                            <i class="bi bi-info-circle"></i> Basic Information
                        </h6>
                    </div>
                    <div class="card-body pt-0">
                        <div class="d-flex flex-column gap-1">
                            <div class="d-flex justify-content-between py-1 border-bottom">
                                <span class="text-muted small">Company</span>
                                <span>{{ $supplier->company_name ?? '-' }}</span>
                            </div>
                            <div class="d-flex justify-content-between py-1 border-bottom">
                                <span class="text-muted small">Email</span>
                                <span>{{ $supplier->email ?? '-' }}</span>
                            </div>
                            <div class="d-flex justify-content-between py-1 border-bottom">
                                <span class="text-muted small">Phone</span>
                                <span class="fw-semibold">{{ $supplier->phone }}</span>
                            </div>
                            <div class="d-flex justify-content-between py-1 border-bottom">
                                <span class="text-muted small">Alternative Phone</span>
                                <span>{{ $supplier->alternative_phone ?? '-' }}</span>
                            </div>
                            <div class="d-flex justify-content-between py-1">
                                <span class="text-muted small">Tax Number</span>
                                <span>{{ $supplier->tax_number ?? '-' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Address Information -->
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-transparent border-0 pt-3">
                        <h6 class="mb-0 fw-bold text-info d-flex align-items-center gap-2">
                            <i class="bi bi-geo-alt"></i> Address Information
                        </h6>
                    </div>
                    <div class="card-body pt-0">
                        <div class="d-flex flex-column gap-1">
                            <div class="d-flex justify-content-between py-1 border-bottom">
                                <span class="text-muted small">Address</span>
                                <span>{{ $supplier->address ?? '-' }}</span>
                            </div>
                            <div class="d-flex justify-content-between py-1 border-bottom">
                                <span class="text-muted small">City</span>
                                <span>{{ $supplier->city ?? '-' }}</span>
                            </div>
                            <div class="d-flex justify-content-between py-1 border-bottom">
                                <span class="text-muted small">State</span>
                                <span>{{ $supplier->state ?? '-' }}</span>
                            </div>
                            <div class="d-flex justify-content-between py-1 border-bottom">
                                <span class="text-muted small">Postal Code</span>
                                <span>{{ $supplier->postal_code ?? '-' }}</span>
                            </div>
                            <div class="d-flex justify-content-between py-1">
                                <span class="text-muted small">Country</span>
                                <span>{{ $supplier->country ?? '-' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Information -->
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-transparent border-0 pt-3">
                        <h6 class="mb-0 fw-bold text-success d-flex align-items-center gap-2">
                            <i class="bi bi-credit-card"></i> Payment Information
                        </h6>
                    </div>
                    <div class="card-body pt-0">
                        <div class="d-flex flex-column gap-1">
                            <div class="d-flex justify-content-between py-1 border-bottom">
                                <span class="text-muted small">Payment Terms</span>
                                <span class="fw-semibold">
                                    @if($supplier->payment_terms)
                                    {{ ucfirst(str_replace('_', ' ', $supplier->payment_terms)) }}
                                    @else
                                    -
                                    @endif
                                </span>
                            </div>
                            @if($supplier->notes)
                            <div class="d-flex justify-content-between py-1">
                                <span class="text-muted small">Notes</span>
                                <span>{{ $supplier->notes }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row g-3 mt-0">
            <div class="col-3">
                <div class="card border-0 shadow-sm stat-card stat-card-info">
                    <div class="card-body text-center p-3">
                        <h3 class="mb-0 fw-bold text-info">{{ $supplier->purchases_count }}</h3>
                        <p class="mb-0 text-muted small">Total Purchases</p>
                    </div>
                </div>
            </div>
            <div class="col-3">
                <div class="card border-0 shadow-sm stat-card stat-card-success">
                    <div class="card-body text-center p-3">
                        <h3 class="mb-0 fw-bold text-success">{{ amo($supplier->purchases->sum('total_amount')) }}</h3>
                        <p class="mb-0 text-muted small">Total Spent</p>
                    </div>
                </div>
            </div>
            <div class="col-3">
                <div class="card border-0 shadow-sm stat-card stat-card-danger">
                    <div class="card-body text-center p-3">
                        <h3 class="mb-0 fw-bold text-danger">{{ amo($supplier->purchases->where('payment_status', '!=', 'paid')->sum('due_amount')) }}</h3>
                        <p class="mb-0 text-muted small">Outstanding</p>
                    </div>
                </div>
            </div>
            <div class="col-3">
                <div class="card border-0 shadow-sm stat-card stat-card-warning">
                    <div class="card-body text-center p-3">
                        <h6 class="mb-0 fw-bold text-warning">{{ $supplier->purchases->first()?->purchase_date->format('d M Y') ?? 'N/A' }}</h6>
                        <p class="mb-0 text-muted small">Last Purchase</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Purchases Table -->
        @if($supplier->purchases->isNotEmpty())
        <div class="card border-0 shadow-sm mt-3">
            <div class="card-header bg-transparent border-0 pt-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold text-secondary d-flex align-items-center gap-2">
                    <i class="bi bi-clock-history"></i> Recent Purchases
                </h6>
                <span class="badge bg-secondary-soft text-secondary rounded-pill">{{ $supplier->purchases->count() }}</span>
            </div>
            <div class="card-body pt-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle mb-0">
                        <thead class="table">
                            <tr>
                                <th class="small">Purchase #</th>
                                <th class="small">Date</th>
                                <th class="text-end small">Total</th>
                                <th class="text-end small">Paid</th>
                                <th class="text-end small">Due</th>
                                <th class="text-center small">Status</th>
                                <th class="text-center small">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($supplier->purchases as $purchase)
                            <tr>
                                <td>
                                    <a href="{{ route('purchases.show', $purchase->id) }}" class="fw-semibold text-primary text-decoration-none">
                                        {{ $purchase->purchase_number }}
                                    </a>
                                </td>
                                <td>{{ $purchase->purchase_date->format('M d, Y') }}</td>
                                <td class="text-end fw-bold">{{ amo($purchase->total_amount) }}</td>
                                <td class="text-end">{{ amo($purchase->paid_amount) }}</td>
                                <td class="text-end {{ $purchase->due_amount > 0 ? 'text-danger' : 'text-success' }} fw-bold">
                                    {{ amo($purchase->due_amount) }}
                                </td>
                                <td class="text-center">
                                    @if($purchase->status === 'received')
                                    <span class="badge bg-success-soft text-success rounded-pill px-3 py-2">Received</span>
                                    @elseif($purchase->status === 'ordered')
                                    <span class="badge bg-info-soft text-info rounded-pill px-3 py-2">Ordered</span>
                                    @elseif($purchase->status === 'partial')
                                    <span class="badge bg-warning-soft text-warning rounded-pill px-3 py-2">Partial</span>
                                    @else
                                    <span class="badge bg-secondary-soft text-secondary rounded-pill px-3 py-2">{{ ucfirst($purchase->status) }}</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('purchases.show', $purchase->id) }}" class="btn btn-info-soft text-info btn-sm rounded-circle shadow-sm" title="View" >
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        <!-- Audit Information -->
        <div class="card border-0 shadow-sm mt-3">
            <div class="card-header bg-transparent border-0 pt-3">
                <h6 class="mb-0 fw-bold text-secondary d-flex align-items-center gap-2">
                    <i class="bi bi-clock"></i> Audit Information
                </h6>
            </div>
            <div class="card-body pt-0">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="p-2 bg-light-soft rounded-3">
                            <span class="text-muted small d-block">Created By</span>
                            <span class="fw-semibold">{{ $supplier->creator->name ?? 'System' }}</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-2 bg-light-soft rounded-3">
                            <span class="text-muted small d-block">Created At</span>
                            <span class="fw-semibold">{{ $supplier->created_at }}</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-2 bg-light-soft rounded-3">
                            <span class="text-muted small d-block">Last Updated</span>
                            <span class="fw-semibold">{{ $supplier->updated_at }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
</div>