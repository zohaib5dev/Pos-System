<!-- ========== TOP PRODUCTS BY PROFIT & PROFIT BY CATEGORY ========== -->
<div class="row g-3">
    <!-- Top Products by Profit -->
    <div class="col-12 col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-transparent border-0 pt-3">
                <h6 class="mb-0 fw-bold text-success d-flex align-items-center gap-2">
                    <i class="bi bi-cube"></i> Top Products by Profit
                </h6>
            </div>
            <div class="card-body pt-0">
                <!-- Mobile Card View -->
                <div class="d-md-none">
                    @forelse($details['by_product'] ?? [] as $item)
                    @php
                        $margin = $item->revenue > 0 ? ($item->profit / $item->revenue) * 100 : 0;
                    @endphp
                    <div class="border-bottom py-2">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="fw-semibold small">{{ $item->name }}</div>
                                <div class="text-muted small">SKU: {{ $item->sku }}</div>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold {{ $item->profit >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ amo($item->profit) }}
                                </div>
                                <span class="badge {{ $margin >= 20 ? 'bg-success-soft text-success' : ($margin >= 10 ? 'bg-warning-soft text-warning' : 'bg-danger-soft text-danger') }} rounded-pill px-2 py-0" style="font-size: 0.55rem;">
                                    {{ number_format($margin, 1) }}%
                                </span>
                            </div>
                        </div>
                        <div class="row g-1 small mt-1">
                            <div class="col-4">
                                <span class="text-muted">Qty:</span>
                                <span>{{ $item->quantity_sold }}</span>
                            </div>
                            <div class="col-4">
                                <span class="text-muted">Revenue:</span>
                                <span>{{ amo($item->revenue) }}</span>
                            </div>
                            <div class="col-4">
                                <span class="text-muted">Cost:</span>
                                <span>{{ amo($item->cost) }}</span>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-bar-chart-line fs-2 d-block mb-2"></i>
                        <p class="small">No data available</p>
                    </div>
                    @endforelse
                </div>

                <!-- Desktop Table View -->
                <div class="table-responsive d-none d-md-block">
                    <table class="table table-bordered table-hover align-middle mb-0">
                        <thead class="table">
                            <tr>
                                <th class="small">Product</th>
                                <th class="text-end small">Qty Sold</th>
                                <th class="text-end small">Revenue</th>
                                <th class="text-end small">Cost</th>
                                <th class="text-end small">Profit</th>
                                <th class="text-end small">Margin</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($details['by_product'] ?? [] as $item)
                            @php
                                $margin = $item->revenue > 0 ? ($item->profit / $item->revenue) * 100 : 0;
                            @endphp
                            <tr>
                                <td>
                                    <div>
                                        <div class="fw-semibold">{{ $item->name }}</div>
                                        <small class="text-muted">{{ $item->sku }}</small>
                                    </div>
                                </td>
                                <td class="text-end">{{ $item->quantity_sold }}</td>
                                <td class="text-end">{{ amo($item->revenue) }}</td>
                                <td class="text-end">{{ amo($item->cost) }}</td>
                                <td class="text-end fw-bold {{ $item->profit >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ amo($item->profit) }}
                                </td>
                                <td class="text-end">
                                    <span class="badge {{ $margin >= 20 ? 'bg-success-soft text-success' : ($margin >= 10 ? 'bg-warning-soft text-warning' : 'bg-danger-soft text-danger') }} rounded-pill px-3 py-2">
                                        {{ number_format($margin, 1) }}%
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    <i class="bi bi-bar-chart-line fs-2 d-block mb-2"></i>
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

    <!-- Profit by Category -->
    <div class="col-12 col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-transparent border-0 pt-3">
                <h6 class="mb-0 fw-bold text-info d-flex align-items-center gap-2">
                    <i class="bi bi-folder"></i> Profit by Category
                </h6>
            </div>
            <div class="card-body pt-0">
                <!-- Mobile Card View -->
                <div class="d-md-none">
                    @forelse($details['by_category'] ?? [] as $item)
                    @php
                        $margin = $item->revenue > 0 ? ($item->profit / $item->revenue) * 100 : 0;
                    @endphp
                    <div class="border-bottom py-2">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <span class="fw-semibold small">{{ $item->name }}</span>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold {{ $item->profit >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ amo($item->profit) }}
                                </div>
                                <span class="badge {{ $margin >= 20 ? 'bg-success-soft text-success' : ($margin >= 10 ? 'bg-warning-soft text-warning' : 'bg-danger-soft text-danger') }} rounded-pill px-2 py-0" style="font-size: 0.55rem;">
                                    {{ number_format($margin, 1) }}%
                                </span>
                            </div>
                        </div>
                        <div class="progress progress-xs mt-1" style="height: 4px;">
                            <div class="progress-bar {{ $margin >= 20 ? 'bg-success' : ($margin >= 10 ? 'bg-warning' : 'bg-danger') }}" style="width: {{ min($margin, 100) }}%"></div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-folder fs-2 d-block mb-2"></i>
                        <p class="small">No data available</p>
                    </div>
                    @endforelse
                </div>

                <!-- Desktop Table View -->
                <div class="table-responsive d-none d-md-block">
                    <table class="table table-bordered table-hover align-middle mb-0">
                        <thead class="table">
                            <tr>
                                <th class="small">Category</th>
                                <th class="text-end small">Revenue</th>
                                <th class="text-end small">Cost</th>
                                <th class="text-end small">Profit</th>
                                <th class="text-end small">Margin</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($details['by_category'] ?? [] as $item)
                            @php
                                $margin = $item->revenue > 0 ? ($item->profit / $item->revenue) * 100 : 0;
                            @endphp
                            <tr>
                                <td>
                                    <span class="fw-semibold">{{ $item->name }}</span>
                                </td>
                                <td class="text-end">{{ amo($item->revenue) }}</td>
                                <td class="text-end">{{ amo($item->cost) }}</td>
                                <td class="text-end fw-bold {{ $item->profit >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ amo($item->profit) }}
                                </td>
                                <td class="text-end">
                                    <div class="d-flex align-items-center justify-content-end gap-2">
                                        <div class="progress progress-xs" style="width: 60px; height: 4px;">
                                            <div class="progress-bar {{ $margin >= 20 ? 'bg-success' : ($margin >= 10 ? 'bg-warning' : 'bg-danger') }}" style="width: {{ min($margin, 100) }}%"></div>
                                        </div>
                                        <small class="text-muted">{{ number_format($margin, 1) }}%</small>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    <i class="bi bi-folder fs-2 d-block mb-2"></i>
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

<!-- ========== PROFIT SUMMARY CARDS ========== -->
<div class="row g-2 g-sm-3 mt-1">
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm stat-card stat-card-info">
            <div class="card-body p-2 p-sm-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h5 class="mb-0 fw-bold text-info">{{ amo($summary['total_revenue'] ?? 0) }}</h5>
                        <p class="mb-0 text-muted small">Total Revenue</p>
                    </div>
                    <div class="bg-info-soft rounded-3 p-2">
                        <i class="bi bi-currency-dollar fs-4 text-info"></i>
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
                        <h5 class="mb-0 fw-bold text-danger">{{ amo($summary['cost_of_goods_sold'] ?? 0) }}</h5>
                        <p class="mb-0 text-muted small">Cost of Goods Sold</p>
                    </div>
                    <div class="bg-danger-soft rounded-3 p-2">
                        <i class="bi bi-cart3 fs-4 text-danger"></i>
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
                        <h5 class="mb-0 fw-bold text-warning">{{ amo($summary['expenses'] ?? 0) }}</h5>
                        <p class="mb-0 text-muted small">Expenses</p>
                    </div>
                    <div class="bg-warning-soft rounded-3 p-2">
                        <i class="bi bi-receipt fs-4 text-warning"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm stat-card stat-card-{{ ($summary['net_profit'] ?? 0) >= 0 ? 'success' : 'danger' }}">
            <div class="card-body p-2 p-sm-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h5 class="mb-0 fw-bold {{ ($summary['net_profit'] ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">
                            {{ amo($summary['net_profit'] ?? 0) }}
                        </h5>
                        <p class="mb-0 text-muted small">Net Profit</p>
                    </div>
                    <div class="bg-{{ ($summary['net_profit'] ?? 0) >= 0 ? 'success' : 'danger' }}-soft rounded-3 p-2">
                        <i class="bi bi-graph-up-arrow fs-4 text-{{ ($summary['net_profit'] ?? 0) >= 0 ? 'success' : 'danger' }}"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ========== MARGIN ANALYSIS ========== -->
<div class="row g-3 mt-1">
    <div class="col-12 col-md-6">
        <div class="card border-0 shadow-sm stat-card stat-card-success">
            <div class="card-body p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-success-soft rounded-3 p-3">
                        <i class="bi bi-percent fs-3 text-success"></i>
                    </div>
                    <div class="flex-grow-1">
                        <p class="mb-0 text-muted small">Gross Margin</p>
                        <h4 class="mb-0 fw-bold text-success">{{ number_format($summary['gross_margin'] ?? 0, 1) }}%</h4>
                        <div class="progress progress-sm mt-1" style="height: 6px;">
                            <div class="progress-bar bg-success" style="width: {{ min(($summary['gross_margin'] ?? 0), 100) }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-6">
        <div class="card border-0 shadow-sm stat-card stat-card-primary">
            <div class="card-body p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-primary-soft rounded-3 p-3">
                        <i class="bi bi-pie-chart fs-3 text-primary"></i>
                    </div>
                    <div class="flex-grow-1">
                        <p class="mb-0 text-muted small">Net Margin</p>
                        <h4 class="mb-0 fw-bold text-primary">{{ number_format($summary['net_margin'] ?? 0, 1) }}%</h4>
                        <div class="progress progress-sm mt-1" style="height: 6px;">
                            <div class="progress-bar bg-primary" style="width: {{ min(($summary['net_margin'] ?? 0), 100) }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

 