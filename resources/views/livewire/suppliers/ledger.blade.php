<div>
    <!-- ========== PAGE HEADER ========== -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <div>
            <h4 class="mb-0 fw-bold d-flex align-items-center gap-2">
                <i class="bi bi-book"></i> Supplier Ledger
            </h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('suppliers.index') }}" class="text-decoration-none">Suppliers</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('suppliers.show', $supplier->id) }}" class="text-decoration-none">{{ Str::limit($supplier->name, 15) }}</a></li>
                    <li class="breadcrumb-item active">Ledger</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex flex-wrap gap-1">
            <a href="{{ route('suppliers.show', $supplier->id) }}" class="btn btn-primary btn-sm shadow-sm">
                <i class="bi bi-person"></i> <span class="d-none d-sm-inline">Supplier</span>
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
                     style="width: 64px; height: 64px;">
                    <span class="text-white fw-bold" style="font-size: 1.3rem;">
                        {{ strtoupper(substr($supplier->name, 0, 2)) }}
                    </span>
                </div>
                <div class="flex-grow-1">
                    <h5 class="mb-0 fw-bold">{{ $supplier->name }}</h5>
                    <div class="d-flex flex-wrap gap-2 mt-1">
                        @if($supplier->company_name)
                        <span class="badge bg-primary-soft text-primary rounded-pill">
                            <i class="bi bi-building me-1"></i> {{ $supplier->company_name }}
                        </span>
                        @endif
                        <span class="badge bg-secondary-soft text-secondary rounded-pill">
                            <i class="bi bi-telephone me-1"></i> {{ $supplier->phone }}
                        </span>
                        @if($supplier->email)
                        <span class="badge bg-info-soft text-info rounded-pill">
                            <i class="bi bi-envelope me-1"></i> {{ $supplier->email }}
                        </span>
                        @endif
                    </div>
                </div>
                <div class="text-end">
                    <div class="text-muted small">Total Purchases</div>
                    <div class="fw-bold text-success">{{ amo($purchases->sum('total_amount')) }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- ========== MOBILE VIEW ========== -->
    <div class="d-md-none">
        <!-- Stats Cards -->
        <div class="row g-2 mb-3">
            <div class="col-4">
                <div class="card border-0 shadow-sm stat-card stat-card-danger text-center">
                    <div class="card-body p-2">
                        <h5 class="mb-0 fw-bold text-danger">{{ amo($purchases->sum('total_amount')) }}</h5>
                        <p class="mb-0 text-muted small">Purchases</p>
                    </div>
                </div>
            </div>
            <div class="col-4">
                <div class="card border-0 shadow-sm stat-card stat-card-success text-center">
                    <div class="card-body p-2">
                        <h5 class="mb-0 fw-bold text-success">{{ amo($payments->sum('amount')) }}</h5>
                        <p class="mb-0 text-muted small">Payments</p>
                    </div>
                </div>
            </div>
            <div class="col-4">
                <div class="card border-0 shadow-sm stat-card stat-card-warning text-center">
                    <div class="card-body p-2">
                        <h5 class="mb-0 fw-bold text-warning">{{ amo($purchases->sum('total_amount') - $payments->sum('amount')) }}</h5>
                        <p class="mb-0 text-muted small">Balance</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Date Filter -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-transparent border-0 pt-3">
                <h6 class="mb-0 fw-bold d-flex align-items-center gap-2">
                    <i class="bi bi-funnel"></i> Filter by Date
                </h6>
            </div>
            <div class="card-body pt-0">
                <div class="d-flex flex-column gap-2">
                    <div>
                        <label class="form-label small fw-semibold">Start Date</label>
                        <input type="date"
                            wire:model.live="ledgerStartDate"
                            class="form-control form-control-sm">
                    </div>
                    <div>
                        <label class="form-label small fw-semibold">End Date</label>
                        <input type="date"
                            wire:model.live="ledgerEndDate"
                            class="form-control form-control-sm">
                    </div>
                </div>
            </div>
        </div>

        <!-- Transactions - Mobile -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-transparent border-0 pt-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold text-secondary d-flex align-items-center gap-2">
                    <i class="bi bi-clock-history"></i> Transactions
                </h6>
                <span class="badge bg-secondary-soft text-secondary rounded-pill">{{ count($transactions) }}</span>
            </div>
            <div class="card-body pt-0">
                @forelse($transactions as $transaction)
                <div class="border-bottom py-2">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="d-flex align-items-center gap-1">
                                @if($transaction['type'] === 'purchase')
                                <span class="badge bg-danger-soft text-danger rounded-pill px-2 py-0" style="font-size: 0.55rem;">Purchase</span>
                                @else
                                <span class="badge bg-success-soft text-success rounded-pill px-2 py-0" style="font-size: 0.55rem;">Payment</span>
                                @endif
                                <span class="small fw-semibold">{{ $transaction['reference'] }}</span>
                            </div>
                            <div class="text-muted small">{{ \Carbon\Carbon::parse($transaction['date'])->format('M d, Y') }}</div>
                            @if($transaction['notes'])
                            <div class="text-muted small">{{ $transaction['notes'] }}</div>
                            @endif
                        </div>
                        <div class="text-end">
                            @if($transaction['debit'] > 0)
                            <div class="text-danger fw-semibold">-{{ amo($transaction['debit']) }}</div>
                            @endif
                            @if($transaction['credit'] > 0)
                            <div class="text-success fw-semibold">+{{ amo($transaction['credit']) }}</div>
                            @endif
                            <div class="fw-bold small">Balance: {{ amo($transaction['balance']) }}</div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-book fs-2 d-block mb-2"></i>
                    <p class="small">No transactions found</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- ========== DESKTOP VIEW ========== -->
    <div class="d-none d-md-block">
        <!-- Stats Cards -->
        <div class="row g-3 mb-3">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm stat-card stat-card-danger">
                    <div class="card-body d-flex align-items-center gap-3 p-3">
                        <div class="bg-danger-soft rounded-3 p-3">
                            <i class="bi bi-cart3 fs-3 text-danger"></i>
                        </div>
                        <div>
                            <p class="mb-0 text-muted small">Total Purchases</p>
                            <h4 class="mb-0 fw-bold text-danger">{{ amo($purchases->sum('total_amount')) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm stat-card stat-card-success">
                    <div class="card-body d-flex align-items-center gap-3 p-3">
                        <div class="bg-success-soft rounded-3 p-3">
                            <i class="bi bi-credit-card fs-3 text-success"></i>
                        </div>
                        <div>
                            <p class="mb-0 text-muted small">Total Payments</p>
                            <h4 class="mb-0 fw-bold text-success">{{ amo($payments->sum('amount')) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm stat-card stat-card-warning">
                    <div class="card-body d-flex align-items-center gap-3 p-3">
                        <div class="bg-warning-soft rounded-3 p-3">
                            <i class="bi bi-scale fs-3 text-warning"></i>
                        </div>
                        <div>
                            <p class="mb-0 text-muted small">Outstanding Balance</p>
                            <h4 class="mb-0 fw-bold {{ $purchases->sum('total_amount') - $payments->sum('amount') > 0 ? 'text-danger' : 'text-success' }}">
                                {{ amo($purchases->sum('total_amount') - $payments->sum('amount')) }}
                            </h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Date Filter -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-transparent border-0 pt-3">
                <h6 class="mb-0 fw-bold d-flex align-items-center gap-2">
                    <i class="bi bi-funnel"></i> Filter by Date
                </h6>
            </div>
            <div class="card-body pt-0">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Start Date</label>
                        <input type="date"
                            wire:model.live="ledgerStartDate"
                            class="form-control form-control-sm">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">End Date</label>
                        <input type="date"
                            wire:model.live="ledgerEndDate"
                            class="form-control form-control-sm">
                    </div>
                </div>
            </div>
        </div>

        <!-- Transactions Table -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0 pt-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold text-secondary d-flex align-items-center gap-2">
                    <i class="bi bi-clock-history"></i> Transaction History
                </h6>
                <span class="badge bg-secondary-soft text-secondary rounded-pill">{{ count($transactions) }}</span>
            </div>
            <div class="card-body pt-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle mb-0">
                        <thead class="table">
                            <tr>
                                <th class="small">Date</th>
                                <th class="small">Type</th>
                                <th class="small">Reference</th>
                                <th class="text-end small">Purchase</th>
                                <th class="text-end small">Payment</th>
                                <th class="text-end small">Balance</th>
                                <th class="small">Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transactions as $transaction)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($transaction['date'])->format('M d, Y') }}</td>
                                <td>
                                    @if($transaction['type'] === 'purchase')
                                    <span class="badge bg-danger-soft text-danger rounded-pill px-3 py-2">Purchase</span>
                                    @else
                                    <span class="badge bg-success-soft text-success rounded-pill px-3 py-2">Payment</span>
                                    @endif
                                </td>
                                <td class="fw-semibold">{{ $transaction['reference'] }}</td>
                                <td class="text-end">
                                    @if($transaction['debit'] > 0)
                                    <span class="text-danger fw-semibold">{{ amo($transaction['debit']) }}</span>
                                    @else
                                    <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    @if($transaction['credit'] > 0)
                                    <span class="text-success fw-semibold">{{ amo($transaction['credit']) }}</span>
                                    @else
                                    <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-end fw-bold">{{ amo($transaction['balance']) }}</td>
                                <td>{{ $transaction['notes'] ?? '-' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <i class="bi bi-book fs-1 d-block mb-3 text-muted"></i>
                                    <p class="text-muted">No transactions found for the selected period</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    
</div>