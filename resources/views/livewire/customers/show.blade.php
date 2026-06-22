<div>
    <!-- ========== PAGE HEADER ========== -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <div>
            <h4 class="mb-0 fw-bold d-flex align-items-center gap-2">
                <i class="bi bi-person"></i> Customer Details
            </h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('customers.index') }}" class="text-decoration-none">Customers</a></li>
                    <li class="breadcrumb-item active">{{ Str::limit($customer->name, 20) }}</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex flex-wrap gap-1">
            <a href="{{ route('customers.edit', ['id' => $customer->id]) }}" class="btn btn-primary btn-sm shadow-sm">
                <i class="bi bi-pencil"></i> <span class="d-none d-sm-inline">Edit</span>
            </a>
            <a href="{{ route('customers.index') }}" class="btn btn-secondary btn-sm shadow-sm">
                <i class="bi bi-arrow-left"></i> <span class="d-none d-sm-inline">Back</span>
            </a>
        </div>
    </div>

    <!-- ========== CUSTOMER HEADER CARD ========== -->
    <div class="card border-0 shadow-lg mb-3">
        <div class="card-body p-3 p-sm-4">
            <div class="d-flex flex-wrap align-items-center gap-3">
                <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" 
                     style="width: 72px; height: 72px;">
                    <span class="text-white fw-bold" style="font-size: 1.5rem;">
                        {{ strtoupper(substr($customer->name, 0, 2)) }}
                    </span>
                </div>
                <div class="flex-grow-1">
                    <h4 class="mb-0 fw-bold">{{ $customer->name }}</h4>
                    <div class="d-flex flex-wrap gap-2 mt-1">
                        <span class="badge bg-primary-soft text-primary rounded-pill">
                            <i class="bi bi-tag me-1"></i> {{ $customer->customer_code }}
                        </span>
                        <span class="badge bg-secondary-soft text-secondary rounded-pill">
                            <i class="bi bi-hash me-1"></i> ID: #{{ $customer->id }}
                        </span>
                        @if($customer->is_active)
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
                    <div class="fw-semibold">{{ $customer->created_at }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- ========== MOBILE VIEW ========== -->
    <div class="d-md-none">
        <!-- Contact Information Card -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-transparent border-0 pt-3">
                <h6 class="mb-0 fw-bold text-info d-flex align-items-center gap-2">
                    <i class="bi bi-envelope"></i> Contact Information
                </h6>
            </div>
            <div class="card-body pt-0">
                <div class="d-flex flex-column gap-1">
                    <div class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-muted small">Email</span>
                        <span>{{ $customer->email ?? '-' }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-1">
                        <span class="text-muted small">Phone</span>
                        <span class="fw-semibold">{{ $customer->phone }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row g-2 mb-3">
            <div class="col-4">
                <div class="card border-0 shadow-sm stat-card stat-card-info text-center">
                    <div class="card-body p-2">
                        <h5 class="mb-0 fw-bold text-info">{{ $customer->orders_count }}</h5>
                        <p class="mb-0 text-muted small">Orders</p>
                    </div>
                </div>
            </div>
            <div class="col-4">
                <div class="card border-0 shadow-sm stat-card stat-card-success text-center">
                    <div class="card-body p-2">
                        <h5 class="mb-0 fw-bold text-success">{{ amo($customer->orders->sum('total_amount')) }}</h5>
                        <p class="mb-0 text-muted small">Purchases</p>
                    </div>
                </div>
            </div>
            <div class="col-4">
                <div class="card border-0 shadow-sm stat-card stat-card-warning text-center">
                    <div class="card-body p-2">
                        <h6 class="mb-0 fw-bold text-warning">{{ $customer->orders->first()?->created_at ?? 'N/A' }}</h6>
                        <p class="mb-0 text-muted small">Last Order</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Orders - Mobile -->
        @if($customer->orders->isNotEmpty())
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-transparent border-0 pt-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold text-secondary d-flex align-items-center gap-2">
                    <i class="bi bi-clock-history"></i> Recent Orders
                </h6>
                <span class="badge bg-secondary-soft text-secondary rounded-pill">{{ $customer->orders->count() }}</span>
            </div>
            <div class="card-body pt-0">
                @foreach($customer->orders->take(5) as $order)
                <div class="border-bottom py-2">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <a href="{{ route('orders.show', $order->id) }}" class="fw-semibold text-decoration-none small">
                                #{{ $order->order_number }}
                            </a>
                            <div class="text-muted small">{{ $order->created_at }}</div>
                        </div>
                        <div class="text-end">
                            <div class="fw-bold">{{ amo($order->total_amount) }}</div>
                            @if($order->status === 'completed')
                            <span class="badge bg-success-soft text-success rounded-pill px-2 py-0" style="font-size: 0.55rem;">Completed</span>
                            @elseif($order->status === 'processing')
                            <span class="badge bg-info-soft text-info rounded-pill px-2 py-0" style="font-size: 0.55rem;">Processing</span>
                            @elseif($order->status === 'pending')
                            <span class="badge bg-warning-soft text-warning rounded-pill px-2 py-0" style="font-size: 0.55rem;">Pending</span>
                            @else
                            <span class="badge bg-secondary-soft text-secondary rounded-pill px-2 py-0" style="font-size: 0.55rem;">{{ ucfirst($order->status) }}</span>
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
                        <span>{{ $customer->creator->name ?? 'System' }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-muted">Created At</span>
                        <span>{{ $customer->created_at }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-1">
                        <span class="text-muted">Last Updated</span>
                        <span>{{ $customer->updated_at }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ========== DESKTOP VIEW ========== -->
    <div class="d-none d-md-block">
        <div class="row g-3">
            <!-- Contact Information -->
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-transparent border-0 pt-3">
                        <h6 class="mb-0 fw-bold text-info d-flex align-items-center gap-2">
                            <i class="bi bi-envelope"></i> Contact Information
                        </h6>
                    </div>
                    <div class="card-body pt-0">
                        <div class="d-flex flex-column gap-1">
                            <div class="d-flex justify-content-between py-1 border-bottom">
                                <span class="text-muted small">Email</span>
                                <span>{{ $customer->email ?? '-' }}</span>
                            </div>
                            <div class="d-flex justify-content-between py-1">
                                <span class="text-muted small">Phone</span>
                                <span class="fw-semibold">{{ $customer->phone }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats -->
            <div class="col-md-6">
                <div class="row g-2 h-100">
                    <div class="col-4">
                        <div class="card border-0 shadow-sm stat-card stat-card-info h-100">
                            <div class="card-body text-center p-3">
                                <h3 class="mb-0 fw-bold text-info">{{ $customer->orders_count }}</h3>
                                <p class="mb-0 text-muted small">Total Orders</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="card border-0 shadow-sm stat-card stat-card-success h-100">
                            <div class="card-body text-center p-3">
                                <h3 class="mb-0 fw-bold text-success">{{ amo($customer->orders->sum('total_amount')) }}</h3>
                                <p class="mb-0 text-muted small">Total Purchases</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="card border-0 shadow-sm stat-card stat-card-warning h-100">
                            <div class="card-body text-center p-3">
                                <h6 class="mb-0 fw-bold text-warning">{{ $customer->orders->first()?->created_at ?? 'N/A' }}</h6>
                                <p class="mb-0 text-muted small">Last Order</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Orders Table -->
        @if($customer->orders->isNotEmpty())
        <div class="card border-0 shadow-sm mt-3">
            <div class="card-header bg-transparent border-0 pt-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold text-secondary d-flex align-items-center gap-2">
                    <i class="bi bi-clock-history"></i> Recent Orders
                </h6>
                <span class="badge bg-secondary-soft text-secondary rounded-pill">{{ $customer->orders->count() }}</span>
            </div>
            <div class="card-body pt-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle mb-0">
                        <thead class="table">
                            <tr>
                                <th class="small">Order #</th>
                                <th class="small">Date</th>
                                <th class="text-end small">Total</th>
                                <th class="text-center small">Status</th>
                                <th class="text-center small">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($customer->orders as $order)
                            <tr>
                                <td>
                                    <a href="{{ route('orders.show', $order->id) }}" class="fw-semibold text-decoration-none">
                                        #{{ $order->order_number }}
                                    </a>
                                </td>
                                <td>{{ $order->created_at }}</td>
                                <td class="text-end fw-bold">{{ amo($order->total_amount) }}</td>
                                <td class="text-center">
                                    @if($order->status === 'completed')
                                    <span class="badge bg-success-soft text-success rounded-pill px-3 py-2">Completed</span>
                                    @elseif($order->status === 'processing')
                                    <span class="badge bg-info-soft text-info rounded-pill px-3 py-2">Processing</span>
                                    @elseif($order->status === 'pending')
                                    <span class="badge bg-warning-soft text-warning rounded-pill px-3 py-2">Pending</span>
                                    @else
                                    <span class="badge bg-secondary-soft text-secondary rounded-pill px-3 py-2">{{ ucfirst($order->status) }}</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('orders.show', $order->id) }}" class="btn btn-info-soft text-info btn-sm rounded-circle shadow-sm" title="View" >
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
                            <span class="fw-semibold">{{ $customer->creator->name ?? 'System' }}</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-2 bg-light-soft rounded-3">
                            <span class="text-muted small d-block">Created At</span>
                            <span class="fw-semibold">{{ $customer->created_at}}</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-2 bg-light-soft rounded-3">
                            <span class="text-muted small d-block">Last Updated</span>
                            <span class="fw-semibold">{{ $customer->updated_at}}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

   
</div>