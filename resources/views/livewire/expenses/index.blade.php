<div>
    <!-- ========== PAGE HEADER ========== -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <div>
            <h4 class="mb-0 fw-bold d-flex align-items-center gap-2">
                <i class="bi bi-receipt"></i> Expenses
            </h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" wire:navigate class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item active">Expenses</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('expenses.create') }}" wire:navigate class="btn btn-primary btn-sm shadow-sm">
            <i class="bi bi-plus-lg"></i> <span class="d-none d-sm-inline">Add Expense</span>
            <span class="d-sm-none">Add</span>
        </a>
    </div>

    <!-- ========== STATS CARDS ========== -->
    <div class="row g-2 g-sm-3 mb-3">
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm stat-card stat-card-info">
                <div class="card-body p-2 p-sm-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h5 class="mb-0 fw-bold text-info">{{ number_format($stats['total_expenses']) }}</h5>
                            <p class="mb-0 text-muted small">Total Expenses</p>
                        </div>
                        <div class="bg-info-soft rounded-3 p-2">
                            <i class="bi bi-receipt fs-4 text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm stat-card stat-card-success">
                <div class="card-body p-2 p-sm-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h5 class="mb-0 fw-bold text-success">{{ amo($stats['total_amount']) }}</h5>
                            <p class="mb-0 text-muted small">Total Amount</p>
                        </div>
                        <div class="bg-success-soft rounded-3 p-2">
                            <i class="bi bi-currency-dollar fs-4 text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm stat-card stat-card-warning">
                <div class="card-body p-2 p-sm-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h5 class="mb-0 fw-bold text-warning">{{ amo($stats['average_amount'] ?? 0) }}</h5>
                            <p class="mb-0 text-muted small">Average</p>
                        </div>
                        <div class="bg-warning-soft rounded-3 p-2">
                            <i class="bi bi-bar-chart-line fs-4 text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm stat-card stat-card-danger">
                <div class="card-body p-2 p-sm-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h5 class="mb-0 fw-bold text-danger">{{ amo($stats['max_amount'] ?? 0) }}</h5>
                            <p class="mb-0 text-muted small">Highest</p>
                        </div>
                        <div class="bg-danger-soft rounded-3 p-2">
                            <i class="bi bi-arrow-up-circle fs-4 text-danger"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ========== CATEGORY BREAKDOWN ========== -->
    @if(count($stats['by_category'] ?? []) > 0)
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body p-3">
            <h6 class="fw-bold text-secondary mb-2 d-flex align-items-center gap-2">
                <i class="bi bi-tags"></i> Expenses by Category
            </h6>
            <div class="row g-2">
                @foreach($stats['by_category'] as $category)
                @if(($category->expenses_sum_amount ?? 0) > 0)
                <div class="col-6 col-sm-4 col-md-3">
                    <div class="bg-light-soft rounded-3 p-2 p-sm-3 text-center">
                        <div class="fw-semibold small">{{ $category->name }}</div>
                        <div class="fw-bold text-primary">{{ amo($category->expenses_sum_amount ?? 0) }}</div>
                        <small class="text-muted">{{ $category->expenses_count ?? 0 }} expenses</small>
                    </div>
                </div>
                @endif
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- ========== MAIN CARD ========== -->
    <div class="card border-0 shadow-lg">
        <div class="card-header bg-transparent border-0 d-flex flex-wrap align-items-center justify-content-between pt-3">
            <h5 class="mb-0 fw-bold d-flex align-items-center gap-2">
                <i class="bi bi-list-ul"></i> Expense List
            </h5>
            <span class="badge bg-primary-soft text-primary rounded-pill">{{ $expenses->total() }}</span>
        </div>

        <div class="card-body pt-0">
            <!-- ========== MOBILE-FRIENDLY FILTERS ========== -->
            <div class="row g-2 mb-3">
                <div class="col-12 col-sm-6 col-md-2">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-0"><i class="bi bi-search"></i></span>
                        <input type="text"
                            wire:model.live.debounce.300ms="search"
                            class="form-control form-control-sm border-0 bg-light"
                            placeholder="Search...">
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
                    <select wire:model.live="paymentMethodFilter" class="form-select form-select-sm bg-light border-0">
                        <option value="">All Methods</option>
                        @foreach($paymentMethods as $method)
                        <option value="{{ $method->id }}">{{ $method->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-sm-3 col-md-1">
                    <input type="number" 
                           placeholder="Min" 
                           wire:model.live="minAmount" 
                           step="0.01" 
                           min="0" 
                           class="form-control form-control-sm bg-light border-0">
                </div>
                <div class="col-6 col-sm-3 col-md-1">
                    <input type="number" 
                           placeholder="Max" 
                           wire:model.live="maxAmount" 
                           step="0.01" 
                           min="0" 
                           class="form-control form-control-sm bg-light border-0">
                </div>
                <div class="col-6 col-sm-3 col-md-2">
                    <select wire:model.live="dateRange" class="form-select form-select-sm bg-light border-0">
                        <option value="today">Today</option>
                        <option value="week">This Week</option>
                        <option value="month">This Month</option>
                        <option value="quarter">Quarter</option>
                        <option value="year">Year</option>
                        <option value="all">All Time</option>
                        <option value="custom">Custom</option>
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
                    <input type="date" wire:model.live="startDate" class="form-control form-control-sm bg-light border-0">
                </div>
                <div class="col-6 col-sm-6 col-md-3">
                    <label class="form-label small fw-semibold">End Date</label>
                    <input type="date" wire:model.live="endDate" class="form-control form-control-sm bg-light border-0">
                </div>
            </div>
            @endif

            <!-- ========== BULK ACTIONS ========== -->
            @if(count($selectedExpenses) > 0)
            <div class="alert alert-info d-flex flex-wrap align-items-center justify-content-between gap-2 py-2 px-3">
                <span class="small"><i class="bi bi-check2-square me-1"></i><strong>{{ count($selectedExpenses) }}</strong> selected</span>
                <div class="d-flex gap-1">
                    <button wire:click="confirmBulkDelete" class="btn btn-danger btn-sm shadow-sm">
                        <i class="bi bi-trash"></i> <span class="d-none d-sm-inline">Delete</span>
                    </button>
                    <button wire:click="$set('selectedExpenses', [])" class="btn btn-secondary btn-sm shadow-sm">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            </div>
            @endif

            <!-- ========== MOBILE CARD VIEW ========== -->
            <div class="d-md-none">
                @forelse($expenses as $expense)
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-start justify-content-between mb-2">
                            <div class="d-flex align-items-center gap-2 flex-grow-1">
                                <input type="checkbox" 
                                    wire:model.live="selectedExpenses" 
                                    value="{{ $expense->id }}" 
                                    class="form-check-input mt-0">
                                <div>
                                    <a href="{{ route('expenses.show', $expense->id) }}" 
                                       wire:navigate
                                       class="fw-bold text-decoration-none">
                                        {{ $expense->expense_number }}
                                    </a>
                                    <div class="text-muted small">{{ $expense->expense_date->format('M d, Y') }}</div>
                                </div>
                            </div>
                            <span class="badge bg-primary-soft text-primary rounded-pill">{{ $expense->category->name ?? 'N/A' }}</span>
                        </div>

                        <div class="mb-2">
                            <div class="small">{{ Str::limit($expense->description, 60) }}</div>
                            @if($expense->reference_number)
                            <div class="text-muted small">Ref: {{ $expense->reference_number }}</div>
                            @endif
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-muted small">Method:</span>
                                <span class="small">{{ $expense->paymentMethod->name ?? 'Cash' }}</span>
                            </div>
                            <div class="fw-bold fs-5">{{ amo($expense->amount) }}</div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center pt-2 border-top mt-2">
                            <div class="d-flex gap-1">
                                <a href="{{ route('expenses.show', $expense->id) }}" 
                                   wire:navigate
                                   class="btn btn-info-soft text-info btn-sm rounded-circle shadow-sm" title="View" >
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('expenses.edit', $expense->id) }}" 
                                   wire:navigate
                                   class="btn btn-primary-soft text-primary btn-sm rounded-circle shadow-sm" title="Edit" >
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <button wire:click="confirmDelete({{ $expense->id }})"
                                    class="btn btn-danger-soft text-danger btn-sm rounded-circle shadow-sm" title="Delete" >
                                    <i class="bi bi-trash"></i>
                                </button>
                                @if($expense->receipt_image)
                                <a href="{{ Storage::url($expense->receipt_image) }}" 
                                   download
                                   class="btn btn-success-soft text-success btn-sm rounded-circle shadow-sm" title="Download" >
                                    <i class="bi bi-download"></i>
                                </a>
                                @endif
                            </div>
                            @if($expense->receipt_image)
                            <a href="{{ Storage::url($expense->receipt_image) }}" 
                               target="_blank" 
                               class="btn btn-info btn-sm shadow-sm">
                                <i class="bi bi-file-image"></i>
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-5">
                    <i class="bi bi-receipt fs-1 d-block mb-3 text-muted"></i>
                    <p class="text-muted">No expenses found</p>
                    <p class="text-muted small">Try adjusting your filters</p>
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
                            <th wire:click="sortBy('expense_number')" style="cursor: pointer" class="small">
                                Expense #
                                @if($sortField === 'expense_number')
                                <i class="bi bi-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </th>
                            <th wire:click="sortBy('expense_date')" style="cursor: pointer" class="small">
                                Date
                                @if($sortField === 'expense_date')
                                <i class="bi bi-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </th>
                            <th class="small">Category</th>
                            <th class="small">Description</th>
                            <th class="small">Method</th>
                            <th wire:click="sortBy('amount')" style="cursor: pointer" class="text-end small">
                                Amount
                                @if($sortField === 'amount')
                                <i class="bi bi-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </th>
                            <th class="text-center small">Receipt</th>
                            <th class="text-center" width="150px">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($expenses as $expense)
                        <tr>
                            <td>
                                <input type="checkbox" wire:model.live="selectedExpenses" value="{{ $expense->id }}" class="form-check-input">
                            </td>
                            <td>
                                <a href="{{ route('expenses.show', $expense->id) }}" 
                                   wire:navigate
                                   class="fw-semibold text-decoration-none">
                                    {{ $expense->expense_number }}
                                </a>
                            </td>
                            <td>{{ $expense->expense_date->format('M d, Y') }}</td>
                            <td>
                                <span class="badge bg-primary-soft text-primary rounded-pill px-3 py-2">{{ $expense->category->name ?? 'N/A' }}</span>
                            </td>
                            <td>
                                <div>{{ Str::limit($expense->description, 30) }}</div>
                                @if($expense->reference_number)
                                <small class="text-muted">Ref: {{ $expense->reference_number }}</small>
                                @endif
                            </td>
                            <td>{{ $expense->paymentMethod->name ?? 'Cash' }}</td>
                            <td class="text-end fw-bold text-primary">{{ amo($expense->amount) }}</td>
                            <td class="text-center">
                                @if($expense->receipt_image)
                                <a href="{{ Storage::url($expense->receipt_image) }}" 
                                   target="_blank" 
                                   class="btn btn-info-soft text-info btn-sm rounded-circle shadow-sm" title="View Receipt" >
                                    <i class="bi bi-file-image"></i>
                                </a>
                                @else
                                <span class="text-muted"><i class="bi bi-x-lg"></i></span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex justify-content-center gap-1">
                                    <a href="{{ route('expenses.show', $expense->id) }}" 
                                       wire:navigate
                                       class="btn btn-info-soft text-info btn-sm rounded-circle shadow-sm" title="View" >
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('expenses.edit', $expense->id) }}" 
                                       wire:navigate
                                       class="btn btn-primary-soft text-primary btn-sm rounded-circle shadow-sm" title="Edit" >
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button wire:click="confirmDelete({{ $expense->id }})"
                                        class="btn btn-danger-soft text-danger btn-sm rounded-circle shadow-sm" title="Delete" >
                                        <i class="bi bi-trash"></i>
                                    </button>
                                    @if($expense->receipt_image)
                                    <a href="{{ Storage::url($expense->receipt_image) }}" 
                                       download
                                       class="btn btn-success-soft text-success btn-sm rounded-circle shadow-sm" title="Download" >
                                        <i class="bi bi-download"></i>
                                    </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <i class="bi bi-receipt fs-1 d-block mb-3 text-muted"></i>
                                <p class="text-muted">No expenses found</p>
                                <p class="text-muted small">Try adjusting your filters</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- ========== PAGINATION ========== -->
            <div class="mt-3">
                {{ $expenses->links('livewire::bootstrap') }}
            </div>
        </div>
    </div>

    <!-- ========== DELETE MODAL ========== -->
    @if($showDeleteModal)
    <div class="modal fade show" style="display: block; background: rgba(0,0,0,0.5);" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title text-danger d-flex align-items-center gap-2">
                        <i class="bi bi-exclamation-triangle-fill"></i> Delete Expense
                    </h5>
                    <button type="button" class="btn-close" wire:click="$set('showDeleteModal', false)"></button>
                </div>
                <div class="modal-body text-center py-3">
                    <p class="mb-0">Are you sure you want to delete this expense?</p>
                    <p class="text-danger small"><i class="bi bi-info-circle me-1"></i>This action cannot be undone.</p>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-secondary btn-sm shadow-sm" wire:click="$set('showDeleteModal', false)">
                        Cancel
                    </button>
                    <button type="button" class="btn btn-danger btn-sm shadow-sm" wire:click="delete">
                        <i class="bi bi-trash"></i> Delete
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- ========== BULK DELETE MODAL ========== -->
    @if($showBulkDeleteModal)
    <div class="modal fade show" style="display: block; background: rgba(0,0,0,0.5);" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title text-danger d-flex align-items-center gap-2">
                        <i class="bi bi-exclamation-triangle-fill"></i> Delete Selected
                    </h5>
                    <button type="button" class="btn-close" wire:click="$set('showBulkDeleteModal', false)"></button>
                </div>
                <div class="modal-body text-center py-3">
                    <p class="mb-0">Delete <strong>{{ count($selectedExpenses) }}</strong> expenses?</p>
                    <p class="text-danger small"><i class="bi bi-info-circle me-1"></i>This cannot be undone.</p>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-secondary btn-sm shadow-sm" wire:click="$set('showBulkDeleteModal', false)">
                        Cancel
                    </button>
                    <button type="button" class="btn btn-danger btn-sm shadow-sm" wire:click="bulkDelete">
                        <i class="bi bi-trash"></i> Delete All
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

   

    <script>
        document.addEventListener('livewire:init', function() {
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    @this.set('showDeleteModal', false);
                    @this.set('showBulkDeleteModal', false);
                }
            });
        });
    </script>
</div>