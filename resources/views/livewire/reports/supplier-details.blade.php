<!-- ========== TOP SUPPLIERS & PURCHASE DISTRIBUTION ========== -->
<div class="row g-3 mb-3">
    <!-- Top Suppliers by Purchase Volume -->
    <div class="col-12 col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-transparent border-0 pt-3">
                <h6 class="mb-0 fw-bold text-primary d-flex align-items-center gap-2">
                    <i class="bi bi-trophy"></i> Top Suppliers
                </h6>
            </div>
            <div class="card-body pt-0">
                <!-- Mobile Card View -->
                <div class="d-md-none">
                    @forelse($details['top_suppliers'] ?? [] as $supplier)
                    <div class="border-bottom py-2">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-primary rounded-circle" style="width: 24px; height: 24px; padding: 0; display: flex; align-items: center; justify-content: center;">{{ $loop->iteration }}</span>
                                    <div>
                                        <div class="fw-semibold small">{{ $supplier->name }}</div>
                                        <div class="text-muted small">{{ $supplier->phone }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold text-primary">{{ amo($supplier->purchases_sum_total_amount ?? 0) }}</div>
                                <div class="text-muted small">{{ $supplier->purchases_count ?? 0 }} purchases</div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-truck fs-2 d-block mb-2"></i>
                        <p class="small">No data available</p>
                    </div>
                    @endforelse
                </div>

                <!-- Desktop Table View -->
                <div class="table-responsive d-none d-md-block">
                    <table class="table  table-bordered table-hover align-middle mb-0">
                        <thead class="table">
                            <tr>
                                <th class="small">Supplier</th>
                                <th class="text-end small">Purchases</th>
                                <th class="text-end small">Total Spent</th>
                                <th class="text-end small">Avg Purchase</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($details['top_suppliers'] ?? [] as $supplier)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge bg-primary rounded-circle" style="width: 24px; height: 24px; padding: 0; display: flex; align-items: center; justify-content: center;">{{ $loop->iteration }}</span>
                                        <div>
                                            <div class="fw-semibold">{{ $supplier->name }}</div>
                                            <small class="text-muted">{{ $supplier->phone }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-end">{{ $supplier->purchases_count ?? 0 }}</td>
                                <td class="text-end fw-bold text-primary">{{ amo($supplier->purchases_sum_total_amount ?? 0) }}</td>
                                <td class="text-end">
                                    @php
                                    $avg = ($supplier->purchases_count ?? 0) > 0 ? ($supplier->purchases_sum_total_amount ?? 0) / ($supplier->purchases_count ?? 1) : 0;
                                    @endphp
                                    {{ amo($avg) }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">
                                    <i class="bi bi-truck fs-2 d-block mb-2"></i>
                                    <p class="small">No data available</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Purchase Distribution by Supplier -->
    <div class="col-12 col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-transparent border-0 pt-3">
                <h6 class="mb-0 fw-bold text-success d-flex align-items-center gap-2">
                    <i class="bi bi-pie-chart"></i> Purchase Distribution
                </h6>
            </div>
            <div class="card-body pt-0">
                @php
                    $totalSpent = collect($details['by_supplier'] ?? [])->sum('total_spent');
                @endphp

                <!-- Mobile Card View -->
                <div class="d-md-none">
                    @forelse($details['by_supplier'] ?? [] as $item)
                    @php
                        $percentage = $totalSpent > 0 ? ($item->total_spent / $totalSpent) * 100 : 0;
                    @endphp
                    <div class="border-bottom py-2">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <span class="fw-semibold small">{{ $item->supplier->name ?? 'Unknown' }}</span>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold text-success">{{ amo($item->total_spent) }}</div>
                                <div class="text-muted small">{{ number_format($percentage, 1) }}%</div>
                            </div>
                        </div>
                        <div class="progress progress-xs mt-1" style="height: 4px;">
                            <div class="progress-bar bg-success" style="width: {{ $percentage }}%"></div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-pie-chart fs-2 d-block mb-2"></i>
                        <p class="small">No data available</p>
                    </div>
                    @endforelse
                </div>

                <!-- Desktop Table View -->
                <div class="table-responsive d-none d-md-block">
                    <table class="table table-bordered table-hover align-middle mb-0">
                        <thead class="table">
                            <tr>
                                <th class="small">Supplier</th>
                                <th class="text-end small">Total Spent</th>
                                <th class="text-end small">% of Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($details['by_supplier'] ?? [] as $item)
                            @php
                                $percentage = $totalSpent > 0 ? ($item->total_spent / $totalSpent) * 100 : 0;
                            @endphp
                            <tr>
                                <td>{{ $item->supplier->name ?? 'Unknown' }}</td>
                                <td class="text-end fw-bold text-success">{{ amo($item->total_spent) }}</td>
                                <td class="text-end">
                                    <div class="d-flex align-items-center justify-content-end gap-2">
                                        <div class="progress progress-xs" style="width: 60px; height: 4px;">
                                            <div class="progress-bar bg-success" style="width: {{ $percentage }}%"></div>
                                        </div>
                                        <small class="text-muted">{{ number_format($percentage, 1) }}%</small>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center py-4 text-muted">
                                    <i class="bi bi-pie-chart fs-2 d-block mb-2"></i>
                                    <p class="small">No data available</p>
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

<!-- ========== SUMMARY STATS CARDS ========== -->
<div class="row g-2 g-sm-3">
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm stat-card stat-card-info">
            <div class="card-body p-2 p-sm-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h5 class="mb-0 fw-bold text-info">{{ $summary['total_suppliers'] ?? 0 }}</h5>
                        <p class="mb-0 text-muted small">Total Suppliers</p>
                    </div>
                    <div class="bg-info-soft rounded-3 p-2">
                        <i class="bi bi-truck fs-4 text-info"></i>
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
                        <h5 class="mb-0 fw-bold text-success">{{ $summary['active_suppliers'] ?? 0 }}</h5>
                        <p class="mb-0 text-muted small">Active Suppliers</p>
                    </div>
                    <div class="bg-success-soft rounded-3 p-2">
                        <i class="bi bi-check-circle fs-4 text-success"></i>
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
                        <h5 class="mb-0 fw-bold text-warning">{{ amo($summary['pending_purchases'] ?? 0) }}</h5>
                        <p class="mb-0 text-muted small">Pending Purchases</p>
                    </div>
                    <div class="bg-warning-soft rounded-3 p-2">
                        <i class="bi bi-clock fs-4 text-warning"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm stat-card stat-card-primary">
            <div class="card-body p-2 p-sm-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h5 class="mb-0 fw-bold text-primary">{{ amo($summary['total_spent'] ?? 0) }}</h5>
                        <p class="mb-0 text-muted small">Total Spent</p>
                    </div>
                    <div class="bg-primary-soft rounded-3 p-2">
                        <i class="bi bi-currency-dollar fs-4 text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

 