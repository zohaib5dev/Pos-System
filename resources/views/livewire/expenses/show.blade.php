<div>
    <!-- ========== PAGE HEADER ========== -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <div>
            <h4 class="mb-0 fw-bold d-flex align-items-center gap-2">
                <i class="bi bi-receipt"></i> Expense #{{ $expense->expense_number }}
            </h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('expenses.index') }}" class="text-decoration-none">Expenses</a></li>
                    <li class="breadcrumb-item active">#{{ $expense->expense_number }}</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex flex-wrap gap-1">
            <a href="{{ route('expenses.edit', ['id' => $expense->id]) }}" class="btn btn-primary btn-sm shadow-sm">
                <i class="bi bi-pencil"></i> <span class="d-none d-sm-inline">Edit</span>
            </a>
            <a href="{{ route('expenses.index') }}" class="btn btn-secondary btn-sm shadow-sm">
                <i class="bi bi-arrow-left"></i> <span class="d-none d-sm-inline">Back</span>
            </a>
        </div>
    </div>

    <!-- ========== EXPENSE HEADER CARD ========== -->
    <div class="card border-0 shadow-lg mb-3">
        <div class="card-body p-3 p-sm-4">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div>
                    <span class="text-muted small">Expense #{{ $expense->expense_number }}</span>
                    <h2 class="mb-0 fw-bold text-primary">{{ amo($expense->amount) }}</h2>
                    <div class="d-flex flex-wrap gap-2 mt-1">
                        <span class="badge bg-primary-soft text-primary rounded-pill">
                            <i class="bi bi-tag me-1"></i> {{ $expense->category->name }}
                        </span>
                        <span class="badge bg-info-soft text-info rounded-pill">
                            <i class="bi bi-credit-card me-1"></i> {{ $expense->paymentMethod->name ?? 'Cash' }}
                        </span>
                        <span class="badge bg-secondary-soft text-secondary rounded-pill">
                            <i class="bi bi-calendar3 me-1"></i> {{ $expense->expense_date }}
                        </span>
                    </div>
                </div>
                <div class="text-end">
                    <div class="text-muted small">Created By</div>
                    <div class="fw-semibold">{{ $expense->creator->name ?? 'System' }}</div>
                    <div class="text-muted small">{{ $expense->created_at }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- ========== MOBILE VIEW ========== -->
    <div class="d-md-none">
        <!-- Expense Information Card -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-transparent border-0 pt-3">
                <h6 class="mb-0 fw-bold d-flex align-items-center gap-2">
                    <i class="bi bi-info-circle"></i> Expense Information
                </h6>
            </div>
            <div class="card-body pt-0">
                <div class="d-flex flex-column gap-1">
                    <div class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-muted small">Category</span>
                        <span class="badge bg-primary-soft text-primary rounded-pill">{{ $expense->category->name }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-muted small">Payment Method</span>
                        <span>{{ $expense->paymentMethod->name ?? 'Cash' }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-muted small">Reference Number</span>
                        <span>{{ $expense->reference_number ?? '-' }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-muted small">Created At</span>
                        <span>{{ $expense->created_at }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-1">
                        <span class="text-muted small">Last Updated</span>
                        <span>{{ $expense->updated_at }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Description Card -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-transparent border-0 pt-3">
                <h6 class="mb-0 fw-bold text-info d-flex align-items-center gap-2">
                    <i class="bi bi-file-text"></i> Description
                </h6>
            </div>
            <div class="card-body pt-0">
                <p class="mb-0">{{ $expense->description }}</p>
            </div>
        </div>

        <!-- Notes Card -->
        @if($expense->notes)
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-transparent border-0 pt-3">
                <h6 class="mb-0 fw-bold text-secondary d-flex align-items-center gap-2">
                    <i class="bi bi-sticky"></i> Additional Notes
                </h6>
            </div>
            <div class="card-body pt-0">
                <p class="mb-0">{{ $expense->notes }}</p>
            </div>
        </div>
        @endif

        <!-- Receipt -->
        @if($expense->receipt_image)
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-transparent border-0 pt-3">
                <h6 class="mb-0 fw-bold text-secondary d-flex align-items-center gap-2">
                    <i class="bi bi-image"></i> Receipt
                </h6>
            </div>
            <div class="card-body pt-0 text-center">
                <img src="{{ Storage::url($expense->receipt_image) }}" 
                     alt="Receipt" 
                     class="img-fluid rounded-3 shadow-sm mb-3"
                     style="max-height: 250px; width: auto;">
                <div class="d-flex justify-content-center gap-2">
                    <a href="{{ Storage::url($expense->receipt_image) }}" 
                       target="_blank" 
                       class="btn btn-primary btn-sm shadow-sm">
                        <i class="bi bi-eye"></i> View
                    </a>
                    <a href="{{ Storage::url($expense->receipt_image) }}" 
                       download 
                       class="btn btn-success btn-sm shadow-sm">
                        <i class="bi bi-download"></i> Download
                    </a>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- ========== DESKTOP VIEW ========== -->
    <div class="d-none d-md-block">
        <div class="row g-3">
            <!-- Expense Information -->
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-transparent border-0 pt-3">
                        <h6 class="mb-0 fw-bold d-flex align-items-center gap-2">
                            <i class="bi bi-info-circle"></i> Expense Information
                        </h6>
                    </div>
                    <div class="card-body pt-0">
                        <div class="d-flex flex-column gap-1">
                            <div class="d-flex justify-content-between py-1 border-bottom">
                                <span class="text-muted small">Category</span>
                                <span class="badge bg-primary-soft text-primary rounded-pill">{{ $expense->category->name }}</span>
                            </div>
                            <div class="d-flex justify-content-between py-1 border-bottom">
                                <span class="text-muted small">Payment Method</span>
                                <span>{{ $expense->paymentMethod->name ?? 'Cash' }}</span>
                            </div>
                            <div class="d-flex justify-content-between py-1 border-bottom">
                                <span class="text-muted small">Reference Number</span>
                                <span>{{ $expense->reference_number ?? '-' }}</span>
                            </div>
                            <div class="d-flex justify-content-between py-1 border-bottom">
                                <span class="text-muted small">Created At</span>
                                <span>{{ $expense->created_at }}</span>
                            </div>
                            <div class="d-flex justify-content-between py-1">
                                <span class="text-muted small">Last Updated</span>
                                <span>{{ $expense->updated_at }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Description & Notes -->
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-transparent border-0 pt-3">
                        <h6 class="mb-0 fw-bold text-info d-flex align-items-center gap-2">
                            <i class="bi bi-file-text"></i> Description
                        </h6>
                    </div>
                    <div class="card-body pt-0">
                        <p class="mb-0">{{ $expense->description }}</p>
                        
                        @if($expense->notes)
                            <hr>
                            <h6 class="fw-bold text-secondary d-flex align-items-center gap-2">
                                <i class="bi bi-sticky"></i> Additional Notes
                            </h6>
                            <p class="mb-0">{{ $expense->notes }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Receipt -->
        @if($expense->receipt_image)
        <div class="card border-0 shadow-sm mt-3">
            <div class="card-header bg-transparent border-0 pt-3">
                <h6 class="mb-0 fw-bold text-secondary d-flex align-items-center gap-2">
                    <i class="bi bi-image"></i> Receipt
                </h6>
            </div>
            <div class="card-body pt-0 text-center">
                <img src="{{ Storage::url($expense->receipt_image) }}" 
                     alt="Receipt" 
                     class="img-fluid rounded-3 shadow-sm mb-3"
                     style="max-height: 400px;">
                <div class="d-flex justify-content-center gap-2">
                    <a href="{{ Storage::url($expense->receipt_image) }}" 
                       target="_blank" 
                       class="btn btn-primary btn-sm shadow-sm">
                        <i class="bi bi-eye"></i> View Full Size
                    </a>
                    <a href="{{ Storage::url($expense->receipt_image) }}" 
                       download 
                       class="btn btn-success btn-sm shadow-sm">
                        <i class="bi bi-download"></i> Download
                    </a>
                </div>
            </div>
        </div>
        @endif
    </div>

    
</div>