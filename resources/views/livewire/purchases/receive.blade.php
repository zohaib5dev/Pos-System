<div>
    <!-- ========== PAGE HEADER ========== -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <div>
            <h4 class="mb-0 fw-bold d-flex align-items-center gap-2">
                <i class="bi bi-box-seam"></i> Receive Items
            </h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('purchases.index') }}" class="text-decoration-none">Purchases</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('purchases.show', $purchase->id) }}" class="text-decoration-none">PO #{{ $purchase->purchase_number }}</a></li>
                    <li class="breadcrumb-item active">Receive</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('purchases.show', $purchase->id) }}" class="btn btn-secondary btn-sm shadow-sm">
            <i class="bi bi-arrow-left"></i> <span class="d-none d-sm-inline">Back to PO</span>
            <span class="d-sm-none">Back</span>
        </a>
    </div>

    <!-- ========== PO SUMMARY CARDS ========== -->
    <div class="row g-2 g-sm-3 mb-3">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm stat-card stat-card-info">
                <div class="card-body p-2 p-sm-3">
                    <div class="d-flex align-items-center gap-2">
                        <div class="bg-info-soft rounded-3 p-2">
                            <i class="bi bi-truck fs-5 text-info"></i>
                        </div>
                        <div>
                            <p class="mb-0 text-muted small">Supplier</p>
                            <h6 class="mb-0 fw-semibold">{{ $purchase->supplier->name }}</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm stat-card stat-card-primary">
                <div class="card-body p-2 p-sm-3">
                    <div class="d-flex align-items-center gap-2">
                        <div class="bg-primary-soft rounded-3 p-2">
                            <i class="bi bi-calendar3 fs-5 text-primary"></i>
                        </div>
                        <div>
                            <p class="mb-0 text-muted small">PO Date</p>
                            <h6 class="mb-0 fw-semibold">{{ $purchase->purchase_date->format('M d, Y') }}</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm stat-card stat-card-success">
                <div class="card-body p-2 p-sm-3">
                    <div class="d-flex align-items-center gap-2">
                        <div class="bg-success-soft rounded-3 p-2">
                            <i class="bi bi-check-circle fs-5 text-success"></i>
                        </div>
                        <div>
                            <p class="mb-0 text-muted small">Status</p>
                            <h6 class="mb-0 fw-semibold">
                                @if($purchase->status === 'received')
                                    <span class="text-success">Received</span>
                                @elseif($purchase->status === 'ordered')
                                    <span class="text-info">Ordered</span>
                                @elseif($purchase->status === 'partial')
                                    <span class="text-warning">Partial</span>
                                @elseif($purchase->status === 'draft')
                                    <span class="text-secondary">Draft</span>
                                @else
                                    <span class="text-danger">Cancelled</span>
                                @endif
                            </h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm stat-card stat-card-warning">
                <div class="card-body p-2 p-sm-3">
                    <div class="d-flex align-items-center gap-2">
                        <div class="bg-warning-soft rounded-3 p-2">
                            <i class="bi bi-box fs-5 text-warning"></i>
                        </div>
                        <div>
                            <p class="mb-0 text-muted small">Total Items</p>
                            <h6 class="mb-0 fw-semibold">{{ count($receiveItems) }}</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ========== RECEIVE FORM ========== -->
    <div class="card border-0 shadow-lg">
        <div class="card-header bg-transparent border-0 pt-3">
            <h6 class="mb-0 fw-bold d-flex align-items-center gap-2">
                <i class="bi bi-list-ul"></i> Items to Receive
            </h6>
        </div>
        <div class="card-body pt-0">
            <form wire:submit="processReceive">
                <!-- ========== MOBILE VIEW ========== -->
                <div class="d-md-none">
                    @foreach($receiveItems as $index => $item)
                    <div class="card border-0 shadow-sm mb-2">
                        <div class="card-body p-2">
                            <div class="d-flex justify-content-between align-items-start mb-1">
                                <div>
                                    <div class="fw-semibold small">{{ $item['product_name'] }}</div>
                                    <div class="text-muted small">SKU: {{ $item['sku'] }}</div>
                                </div>
                                <span class="badge bg-{{ $item['remaining_quantity'] > 0 ? 'warning' : 'success' }}-soft text-{{ $item['remaining_quantity'] > 0 ? 'warning' : 'success' }} rounded-pill">
                                    {{ $item['remaining_quantity'] }} remaining
                                </span>
                            </div>
                            <div class="row g-1 small mt-1">
                                <div class="col-4">
                                    <span class="text-muted">Ordered:</span>
                                    <span class="fw-semibold">{{ $item['ordered_quantity'] }}</span>
                                </div>
                                <div class="col-4">
                                    <span class="text-muted">Received:</span>
                                    <span class="fw-semibold">{{ $item['received_quantity'] }}</span>
                                </div>
                                <div class="col-4">
                                    <span class="text-muted">Receive:</span>
                                    <input type="number"
                                        wire:model="receiveItems.{{ $index }}.receive_quantity"
                                        min="0"
                                        max="{{ $item['remaining_quantity'] }}"
                                        class="form-control form-control-sm text-end"
                                        style="width: 70px; display: inline-block;"
                                        {{ $item['remaining_quantity'] == 0 ? 'disabled' : '' }}>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- ========== DESKTOP VIEW ========== -->
                <div class="table-responsive d-none d-md-block">
                    <table class="table table-bordered table-hover align-middle mb-0">
                        <thead class="table">
                            <tr>
                                <th class="small">Product</th>
                                <th class="text-end small">Ordered</th>
                                <th class="text-end small">Received</th>
                                <th class="text-end small">Remaining</th>
                                <th class="text-end small" style="width: 150px;">Receive Qty</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($receiveItems as $index => $item)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $item['product_name'] }}</div>
                                    <small class="text-muted">SKU: {{ $item['sku'] }}</small>
                                </td>
                                <td class="text-end">{{ $item['ordered_quantity'] }}</td>
                                <td class="text-end">{{ $item['received_quantity'] }}</td>
                                <td class="text-end fw-bold {{ $item['remaining_quantity'] > 0 ? 'text-warning' : 'text-success' }}">
                                    {{ $item['remaining_quantity'] }}
                                </td>
                                <td>
                                    <input type="number"
                                        wire:model="receiveItems.{{ $index }}.receive_quantity"
                                        min="0"
                                        max="{{ $item['remaining_quantity'] }}"
                                        class="form-control form-control-sm text-end"
                                        {{ $item['remaining_quantity'] == 0 ? 'disabled' : '' }}>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- ========== FORM ACTIONS ========== -->
                <div class="row g-2 mt-4 pt-3 border-top">
                    <div class="col-12 d-flex flex-wrap gap-2">
                        <a href="{{ route('purchases.show', $purchase->id) }}" class="btn btn-secondary shadow-sm">
                            <i class="bi bi-x-lg"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-success shadow-sm">
                            <i class="bi bi-check-circle"></i> Receive Items
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
 
</div>