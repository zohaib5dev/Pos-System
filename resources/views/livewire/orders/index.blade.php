<div>
    <!-- ========== PAGE HEADER ========== -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <div>
            <h4 class="mb-0 fw-bold d-flex align-items-center gap-2">
                <i class="bi bi-cart3"></i> Orders
            </h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item active">Orders</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('pos.index') }}" target="_blank" class="btn btn-primary btn-sm shadow-sm">
            <i class="bi bi-plus-lg"></i> <span class="d-none d-sm-inline">New Order</span>
            <span class="d-sm-none">New</span>
        </a>
    </div>

    <!-- ========== STATS CARDS - Mobile Optimized ========== -->
    <div class="row g-2 g-sm-3 mb-3">
        <div class="col-4 col-md-2">
            <div class="card border-0 shadow-sm stat-card stat-card-primary">
                <div class="card-body p-2 p-sm-3 text-center">
                    <h5 class="mb-0 fw-bold text-primary">{{ number_format($stats['total']) }}</h5>
                    <p class="mb-0 text-muted small">Total</p>
                </div>
            </div>
        </div>
        <div class="col-4 col-md-2">
            <div class="card border-0 shadow-sm stat-card stat-card-warning">
                <div class="card-body p-2 p-sm-3 text-center">
                    <h5 class="mb-0 fw-bold text-warning">{{ number_format($stats['pending']) }}</h5>
                    <p class="mb-0 text-muted small">Pending</p>
                </div>
            </div>
        </div>
        <div class="col-4 col-md-2">
            <div class="card border-0 shadow-sm stat-card stat-card-info">
                <div class="card-body p-2 p-sm-3 text-center">
                    <h5 class="mb-0 fw-bold text-info">{{ number_format($stats['processing']) }}</h5>
                    <p class="mb-0 text-muted small">Processing</p>
                </div>
            </div>
        </div>
        <div class="col-4 col-md-2">
            <div class="card border-0 shadow-sm stat-card stat-card-success">
                <div class="card-body p-2 p-sm-3 text-center">
                    <h5 class="mb-0 fw-bold text-success">{{ number_format($stats['completed']) }}</h5>
                    <p class="mb-0 text-muted small">Completed</p>
                </div>
            </div>
        </div>
        <div class="col-4 col-md-2">
            <div class="card border-0 shadow-sm stat-card stat-card-secondary">
                <div class="card-body p-2 p-sm-3 text-center">
                    <h5 class="mb-0 fw-bold text-secondary">{{ number_format($stats['today']) }}</h5>
                    <p class="mb-0 text-muted small">Today</p>
                </div>
            </div>
        </div>
        <div class="col-4 col-md-2">
            <div class="card border-0 shadow-sm stat-card stat-card-danger">
                <div class="card-body p-2 p-sm-3 text-center">
                    <h5 class="mb-0 fw-bold text-danger">{{ $stats['revenue'] }}</h5>
                    <p class="mb-0 text-muted small">Revenue</p>
                </div>
            </div>
        </div>
    </div>

    <!-- ========== FILTERS - Mobile Optimized ========== -->
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body p-2 p-sm-3">
            <!-- Mobile: Collapsible Filters -->
            <div class="d-md-none">
                <button class="btn btn-light btn-sm w-100 d-flex justify-content-between align-items-center" 
                        type="button" 
                        data-bs-toggle="collapse" 
                        data-bs-target="#mobileFilters" 
                        aria-expanded="false">
                    <span><i class="bi bi-funnel me-1"></i> Filters</span>
                    <i class="bi bi-chevron-down"></i>
                </button>
                <div class="collapse mt-2" id="mobileFilters">
                    <div class="d-flex flex-column gap-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light border-0"><i class="bi bi-search"></i></span>
                            <input type="text"
                                wire:model.live.debounce.300ms="search"
                                class="form-control form-control-sm border-0 bg-light"
                                placeholder="Search orders...">
                        </div>
                        <select wire:model.live="statusFilter" class="form-select form-select-sm bg-light border-0">
                            <option value="">All Status</option>
                            <option value="pending">⏳ Pending</option>
                            <option value="processing">⚙️ Processing</option>
                            <option value="completed">✅ Completed</option>
                            <option value="cancelled">❌ Cancelled</option>
                            <option value="refunded">↩️ Refunded</option>
                        </select>
                        <select wire:model.live="paymentStatusFilter" class="form-select form-select-sm bg-light border-0">
                            <option value="">All Payments</option>
                            <option value="pending">⏳ Pending</option>
                            <option value="partial">🔄 Partial</option>
                            <option value="paid">✅ Paid</option>
                            <option value="refunded">↩️ Refunded</option>
                        </select>
                        <select wire:model.live="dateRange" class="form-select form-select-sm bg-light border-0">
                            <option value="all">📅 All Time</option>
                            <option value="today">📆 Today</option>
                            <option value="week">📊 This Week</option>
                            <option value="month">📈 This Month</option>
                            <option value="year">📉 This Year</option>
                        </select>
                        <select wire:model.live="customerFilter" class="form-select form-select-sm bg-light border-0">
                            <option value="">All Customers</option>
                            @foreach($customers as $customer)
                            <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                            @endforeach
                        </select>
                        <div class="d-flex align-items-center gap-2">
                            <select wire:model.live="perPage" class="form-select form-select-sm bg-light border-0" style="width: 70px;">
                                <option value="15">15</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Desktop Filters -->
            <div class="d-none d-md-block">
                <div class="row g-2">
                    <div class="col-md-2 col-sm-6">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light border-0"><i class="bi bi-search"></i></span>
                            <input type="text"
                                wire:model.live.debounce.300ms="search"
                                class="form-control form-control-sm border-0 bg-light"
                                placeholder="Search orders...">
                        </div>
                    </div>
                    <div class="col-md-2 col-sm-6">
                        <select wire:model.live="statusFilter" class="form-select form-select-sm bg-light border-0">
                            <option value="">All Status</option>
                            <option value="pending">⏳ Pending</option>
                            <option value="processing">⚙️ Processing</option>
                            <option value="completed">✅ Completed</option>
                            <option value="cancelled">❌ Cancelled</option>
                            <option value="refunded">↩️ Refunded</option>
                        </select>
                    </div>
                    <div class="col-md-2 col-sm-6">
                        <select wire:model.live="paymentStatusFilter" class="form-select form-select-sm bg-light border-0">
                            <option value="">All Payments</option>
                            <option value="pending">⏳ Pending</option>
                            <option value="partial">🔄 Partial</option>
                            <option value="paid">✅ Paid</option>
                            <option value="refunded">↩️ Refunded</option>
                        </select>
                    </div>
                    <div class="col-md-2 col-sm-6">
                        <select wire:model.live="dateRange" class="form-select form-select-sm bg-light border-0">
                            <option value="all">📅 All Time</option>
                            <option value="today">📆 Today</option>
                            <option value="week">📊 This Week</option>
                            <option value="month">📈 This Month</option>
                            <option value="year">📉 This Year</option>
                        </select>
                    </div>
                    <div class="col-md-2 col-sm-6">
                        <select wire:model.live="customerFilter" class="form-select form-select-sm bg-light border-0">
                            <option value="">All Customers</option>
                            @foreach($customers as $customer)
                            <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 col-sm-6">
                        <div class="d-flex align-items-center gap-2">
                            <select wire:model.live="perPage" class="form-select form-select-sm bg-light border-0" style="width: 70px;">
                                <option value="15">15</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Active Filters -->
            @if($search || $statusFilter || $paymentStatusFilter || $customerFilter || $dateRange != 'all')
            <div class="mt-2 d-flex flex-wrap gap-1">
                <span class="text-muted small me-1">Active:</span>
                @if($search)
                <span class="badge bg-primary-soft text-primary d-inline-flex align-items-center gap-1 px-2 py-1" style="font-size: 0.65rem;">
                    "{{ Str::limit($search, 10) }}"
                    <a href="#" wire:click="$set('search', '')" class="text-decoration-none text-primary">
                        <i class="bi bi-x-lg"></i>
                    </a>
                </span>
                @endif
                @if($statusFilter)
                <span class="badge bg-warning-soft text-warning d-inline-flex align-items-center gap-1 px-2 py-1" style="font-size: 0.65rem;">
                    {{ ucfirst($statusFilter) }}
                    <a href="#" wire:click="$set('statusFilter', '')" class="text-decoration-none text-warning">
                        <i class="bi bi-x-lg"></i>
                    </a>
                </span>
                @endif
                @if($paymentStatusFilter)
                <span class="badge bg-info-soft text-info d-inline-flex align-items-center gap-1 px-2 py-1" style="font-size: 0.65rem;">
                    {{ ucfirst($paymentStatusFilter) }}
                    <a href="#" wire:click="$set('paymentStatusFilter', '')" class="text-decoration-none text-info">
                        <i class="bi bi-x-lg"></i>
                    </a>
                </span>
                @endif
            </div>
            @endif
        </div>
    </div>

    <!-- ========== BULK ACTIONS ========== -->
    @if(count($selectedOrders) > 0)
    <div class="alert alert-info d-flex flex-wrap align-items-center justify-content-between gap-2 py-2 px-3 mb-3">
        <span class="small"><i class="bi bi-check2-square me-1"></i><strong>{{ count($selectedOrders) }}</strong> selected</span>
        <div class="d-flex gap-1">
            <button wire:click="bulkDelete"
                wire:confirm="Are you sure you want to delete {{ count($selectedOrders) }} orders?"
                class="btn btn-danger btn-sm shadow-sm">
                <i class="bi bi-trash"></i> <span class="d-none d-sm-inline">Delete</span>
            </button>
            <button wire:click="$set('selectedOrders', [])" class="btn btn-secondary btn-sm shadow-sm">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
    </div>
    @endif

    <!-- ========== MOBILE CARD VIEW ========== -->
    <div class="d-md-none">
        @forelse($orders as $order)
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body p-3">
                <!-- Header: Checkbox + Order # + Status -->
                <div class="d-flex align-items-start justify-content-between mb-2">
                    <div class="d-flex align-items-center gap-2 flex-grow-1">
                        <input type="checkbox" 
                            wire:model.live="selectedOrders" 
                            value="{{ $order->id }}" 
                            class="form-check-input mt-0">
                        <div>
                            <a href="{{ route('orders.show', $order->id) }}" class="fw-bold text-decoration-none">
                                #{{ $order->order_number }}
                            </a>
                            <div class="text-muted small">{{ $order->created_at }}</div>
                        </div>
                    </div>
                    <div class="d-flex flex-column align-items-end gap-1">
                        <select wire:change="updateStatus({{ $order->id }}, $event.target.value)"
                            class="form-select form-select-sm border-0 fw-semibold
                                @if($order->status === 'completed') text-success bg-success-soft
                                @elseif($order->status === 'pending') text-warning bg-warning-soft
                                @elseif($order->status === 'processing') text-info bg-info-soft
                                @elseif($order->status === 'cancelled') text-danger bg-danger-soft
                                @else text-secondary bg-secondary-soft
                                @endif"
                            style="width: auto; min-width: 90px; font-size: 0.65rem; padding: 0.1rem 0.3rem;">
                            <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>⏳ Pending</option>
                            <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>⚙️ Processing</option>
                            <option value="completed" {{ $order->status === 'completed' ? 'selected' : '' }}>✅ Completed</option>
                            <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>❌ Cancelled</option>
                            <option value="refunded" {{ $order->status === 'refunded' ? 'selected' : '' }}>↩️ Refunded</option>
                        </select>
                        <span class="badge bg-{{ $order->payment_status === 'paid' ? 'success' : ($order->payment_status === 'partial' ? 'warning' : ($order->payment_status === 'refunded' ? 'secondary' : 'info')) }}-soft text-{{ $order->payment_status === 'paid' ? 'success' : ($order->payment_status === 'partial' ? 'warning' : ($order->payment_status === 'refunded' ? 'secondary' : 'info')) }} rounded-pill px-2 py-0" style="font-size: 0.55rem;">
                            {{ ucfirst($order->payment_status) }}
                        </span>
                    </div>
                </div>

                <!-- Customer & Total -->
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <div class="fw-semibold small">{{ $order->customer->name ?? 'Walk-in Customer' }}</div>
                        @if($order->customer_phone)
                        <div class="text-muted small"><i class="bi bi-telephone me-1"></i>{{ $order->customer_phone }}</div>
                        @endif
                    </div>
                    <div class="text-end">
                        <div class="fw-bold text-primary">{{ amo($order->total_amount) }}</div>
                        <div class="text-muted small">Due: {{ amo($order->due_amount) }}</div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                    <div class="d-flex gap-1">
                        <a href="{{ route('orders.show', $order->id) }}" class="btn btn-info-soft text-info btn-sm rounded-circle shadow-sm" title="View" >
                            <i class="bi bi-eye"></i>
                        </a>
                        <a href="{{ route('orders.invoice', $order->id) }}" class="btn btn-success-soft text-success btn-sm rounded-circle shadow-sm" title="Invoice" >
                            <i class="bi bi-file-earmark-pdf"></i>
                        </a>
                        @if($order->due_amount > 0 && $order->status !== 'refunded')
                        <button wire:click="openPaymentModal({{ $order->id }})" class="btn btn-primary-soft text-primary btn-sm rounded-circle shadow-sm" title="Payment" >
                            <i class="bi bi-credit-card"></i>
                        </button>
                        @endif
                        <button wire:click="viewReceipt({{ $order->id }})" class="btn btn-secondary-soft text-secondary btn-sm rounded-circle shadow-sm" title="Print" >
                            <i class="bi bi-printer"></i>
                        </button>
                        <button wire:click="confirmDelete({{ $order->id }})" class="btn btn-danger-soft text-danger btn-sm rounded-circle shadow-sm" title="Delete" >
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                    <span class="text-muted small">ID: {{ $order->id }}</span>
                </div>
            </div>
        </div>
        @empty
        <div class="text-center py-5">
            <i class="bi bi-box-seam fs-1 d-block mb-3 text-muted"></i>
            <h6 class="text-muted">No orders found</h6>
            <p class="text-muted small mb-3">Try adjusting your filters</p>
            <a href="{{ route('pos.index') }}" class="btn btn-primary btn-sm shadow-sm">
                <i class="bi bi-plus-lg"></i> Create Order
            </a>
        </div>
        @endforelse

        <!-- Mobile Pagination Info -->
        <div class="text-center text-muted small mt-3">
            Showing {{ $orders->firstItem() ?? 0 }} to {{ $orders->lastItem() ?? 0 }} of {{ $orders->total() }} orders
        </div>
        @if($orders->hasPages())
        <div class="mt-2">
            {{ $orders->links('livewire::bootstrap') }}
        </div>
        @endif
    </div>

    <!-- ========== DESKTOP TABLE VIEW ========== -->
    <div class="card border-0 shadow-sm d-none d-md-block">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table  table-bordered table-hover align-middle mb-0">
                    <thead class="table">
                        <tr>
                            <th width="40px">
                                <input type="checkbox" wire:model.live="selectAll" class="form-check-input">
                            </th>
                            <th wire:click="sortBy('order_number')" style="cursor: pointer" class="small">
                                Order #
                                @if($sortField === 'order_number')
                                <i class="bi bi-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </th>
                            <th wire:click="sortBy('created_at')" style="cursor: pointer" class="small">
                                Date
                                @if($sortField === 'created_at')
                                <i class="bi bi-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </th>
                            <th class="small">Customer</th>
                            <th wire:click="sortBy('total_amount')" style="cursor: pointer" class="text-end small">
                                Total
                                @if($sortField === 'total_amount')
                                <i class="bi bi-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </th>
                            <th class="text-center small">Status</th>
                            <th class="text-center small">Payment</th>
                            <th class="text-center" width="180px">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                        <tr>
                            <td>
                                <input type="checkbox" wire:model.live="selectedOrders" value="{{ $order->id }}" class="form-check-input">
                            </td>
                            <td>
                                <a href="{{ route('orders.show', $order->id) }}" class="fw-bold text-decoration-none">
                                    #{{ $order->order_number }}
                                </a>
                            </td>
                            <td>
                                <div class="small">
                                    {{ $order->created_at }}
                                    <br>
                                    <span class="text-muted">{{ $order->created_at }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="bg-primary-soft rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; min-width: 32px;">
                                        <i class="bi bi-person text-primary"></i>
                                    </div>
                                    <div>
                                        <div class="fw-semibold small">{{ $order->customer->name ?? 'Walk-in Customer' }}</div>
                                        @if($order->customer_phone)
                                        <div class="text-muted small"><i class="bi bi-telephone me-1"></i>{{ $order->customer_phone }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="text-end">
                                <div class="fw-bold">{{ amo($order->total_amount) }}</div>
                                <div class="text-muted small">Due: {{ amo($order->due_amount) }}</div>
                            </td>
                            <td>
                                <select wire:change="updateStatus({{ $order->id }}, $event.target.value)"
                                    class="form-select form-select-sm border-0 fw-semibold
                                        @if($order->status === 'completed') text-success bg-success-soft
                                        @elseif($order->status === 'pending') text-warning bg-warning-soft
                                        @elseif($order->status === 'processing') text-info bg-info-soft
                                        @elseif($order->status === 'cancelled') text-danger bg-danger-soft
                                        @else text-secondary bg-secondary-soft
                                        @endif"
                                    style="width: auto; min-width: 100px;">
                                    <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>⏳ Pending</option>
                                    <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>⚙️ Processing</option>
                                    <option value="completed" {{ $order->status === 'completed' ? 'selected' : '' }}>✅ Completed</option>
                                    <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>❌ Cancelled</option>
                                    <option value="refunded" {{ $order->status === 'refunded' ? 'selected' : '' }}>↩️ Refunded</option>
                                </select>
                            </td>
                            <td>
                                <select wire:change="updatePaymentStatus({{ $order->id }}, $event.target.value)"
                                    class="form-select form-select-sm border-0 fw-semibold
                                        @if($order->payment_status === 'paid') text-success bg-success-soft
                                        @elseif($order->payment_status === 'partial') text-warning bg-warning-soft
                                        @elseif($order->payment_status === 'refunded') text-secondary bg-secondary-soft
                                        @else text-info bg-info-soft
                                        @endif"
                                    style="width: auto; min-width: 100px;">
                                    <option value="pending" {{ $order->payment_status === 'pending' ? 'selected' : '' }}>⏳ Pending</option>
                                    <option value="partial" {{ $order->payment_status === 'partial' ? 'selected' : '' }}>🔄 Partial</option>
                                    <option value="paid" {{ $order->payment_status === 'paid' ? 'selected' : '' }}>✅ Paid</option>
                                    <option value="refunded" {{ $order->payment_status === 'refunded' ? 'selected' : '' }}>↩️ Refunded</option>
                                </select>
                            </td>
                            <td>
                                <div class="d-flex justify-content-center gap-1 flex-wrap">
                                    <a href="{{ route('orders.show', $order->id) }}" class="btn btn-info-soft text-info btn-sm rounded-circle shadow-sm" title="View" >
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('orders.invoice', $order->id) }}" class="btn btn-success-soft text-success btn-sm rounded-circle shadow-sm" title="Invoice" >
                                        <i class="bi bi-file-earmark-pdf"></i>
                                    </a>
                                    @if($order->due_amount > 0 && $order->status !== 'refunded')
                                    <button wire:click="openPaymentModal({{ $order->id }})" class="btn btn-primary-soft text-primary btn-sm rounded-circle shadow-sm" title="Payment" >
                                        <i class="bi bi-credit-card"></i>
                                    </button>
                                    @endif
                                    <button wire:click="viewReceipt({{ $order->id }})" class="btn btn-secondary-soft text-secondary btn-sm rounded-circle shadow-sm" title="Print" >
                                        <i class="bi bi-printer"></i>
                                    </button>
                                    <button wire:click="confirmDelete({{ $order->id }})" class="btn btn-danger-soft text-danger btn-sm rounded-circle shadow-sm" title="Delete" >
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <i class="bi bi-box-seam fs-1 d-block mb-3 text-muted"></i>
                                <h6 class="text-muted">No orders found</h6>
                                <p class="text-muted small mb-3">Try adjusting your filters</p>
                                <a href="{{ route('pos.index') }}" class="btn btn-primary btn-sm shadow-sm">
                                    <i class="bi bi-plus-lg"></i> Create Order
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Desktop Pagination -->
            <div class="d-flex flex-wrap justify-content-between align-items-center p-3 border-top">
                <div class="text-muted small mb-2 mb-sm-0">
                    Showing <strong>{{ $orders->firstItem() ?? 0 }}</strong> to <strong>{{ $orders->lastItem() ?? 0 }}</strong> of <strong>{{ $orders->total() }}</strong> orders
                </div>
                @if($orders->hasPages())
                <div>
                    {{ $orders->links('livewire::bootstrap') }}
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Include Modals -->
    @include('livewire.orders.modals.payment')
    @include('livewire.orders.modals.delete')
    @include('livewire.orders.modals.receipt')
    @include('livewire.orders.modals.refund')

   
</div>