<!-- ========== TAX BY CATEGORY & TAX SUMMARY ========== -->
<div class="row g-3">
    <!-- Tax by Category -->
    <div class="col-12 col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-transparent border-0 pt-3">
                <h6 class="mb-0 fw-bold text-warning d-flex align-items-center gap-2">
                    <i class="bi bi-tags"></i> Tax Collected by Category
                </h6>
            </div>
            <div class="card-body pt-0">
                <!-- Mobile Card View -->
                <div class="d-md-none">
                    @forelse($details['by_category'] ?? [] as $item)
                    @php
                        $taxRate = $item->total_sales > 0 ? ($item->total_tax / $item->total_sales) * 100 : 0;
                    @endphp
                    <div class="border-bottom py-2">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="fw-semibold small">{{ $item->name }}</div>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold text-warning">{{ amo($item->total_tax) }}</div>
                                <div class="text-muted small">{{ number_format($taxRate, 1) }}% rate</div>
                            </div>
                        </div>
                        <div class="row g-1 small mt-1">
                            <div class="col-6">
                                <span class="text-muted">Sales:</span>
                                <span>{{ amo($item->total_sales) }}</span>
                            </div>
                            <div class="col-6">
                                <span class="badge bg-warning-soft text-warning rounded-pill px-2 py-0">{{ number_format($taxRate, 1) }}%</span>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-tags fs-2 d-block mb-2"></i>
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
                                <th class="text-end small">Sales</th>
                                <th class="text-end small">Tax</th>
                                <th class="text-end small">Rate</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($details['by_category'] ?? [] as $item)
                            @php
                                $taxRate = $item->total_sales > 0 ? ($item->total_tax / $item->total_sales) * 100 : 0;
                            @endphp
                            <tr>
                                <td>
                                    <span class="fw-semibold">{{ $item->name }}</span>
                                </td>
                                <td class="text-end">{{ amo($item->total_sales) }}</td>
                                <td class="text-end fw-bold text-warning">{{ amo($item->total_tax) }}</td>
                                <td class="text-end">
                                    <span class="badge bg-warning-soft text-warning rounded-pill px-3 py-2">{{ number_format($taxRate, 2) }}%</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">
                                    <i class="bi bi-tags fs-2 d-block mb-2"></i>
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

    <!-- Tax Summary Cards -->
    <div class="col-12 col-md-6">
        <div class="row g-2 g-sm-3">
            <!-- Sales Tax -->
            <div class="col-6">
                <div class="card border-0 shadow-sm stat-card stat-card-success">
                    <div class="card-body p-2 p-sm-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h5 class="mb-0 fw-bold text-success">{{ amo($summary['sales_tax_collected'] ?? 0) }}</h5>
                                <p class="mb-0 text-muted small">Sales Tax</p>
                                <small class="text-muted">{{ $summary['sales_transactions'] ?? 0 }} transactions</small>
                            </div>
                            <div class="bg-success-soft rounded-3 p-2">
                                <i class="bi bi-arrow-up-circle fs-4 text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Purchase Tax -->
            <div class="col-6">
                <div class="card border-0 shadow-sm stat-card stat-card-danger">
                    <div class="card-body p-2 p-sm-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h5 class="mb-0 fw-bold text-danger">{{ amo($summary['purchase_tax_paid'] ?? 0) }}</h5>
                                <p class="mb-0 text-muted small">Purchase Tax</p>
                                <small class="text-muted">{{ $summary['purchase_transactions'] ?? 0 }} transactions</small>
                            </div>
                            <div class="bg-danger-soft rounded-3 p-2">
                                <i class="bi bi-arrow-down-circle fs-4 text-danger"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Net Tax Payable -->
            <div class="col-12">
                <div class="card border-0 shadow-sm stat-card stat-card-{{ ($summary['net_tax_payable'] ?? 0) >= 0 ? 'warning' : 'info' }}">
                    <div class="card-body p-2 p-sm-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h5 class="mb-0 fw-bold {{ ($summary['net_tax_payable'] ?? 0) >= 0 ? 'text-warning' : 'text-info' }}">
                                    {{ amo(abs($summary['net_tax_payable'] ?? 0)) }}
                                </h5>
                                <p class="mb-0 text-muted small">Net Tax {{ ($summary['net_tax_payable'] ?? 0) >= 0 ? 'Payable' : 'Refundable' }}</p>
                            </div>
                            <div class="bg-{{ ($summary['net_tax_payable'] ?? 0) >= 0 ? 'warning' : 'info' }}-soft rounded-3 p-2">
                                <i class="bi bi-scale fs-4 text-{{ ($summary['net_tax_payable'] ?? 0) >= 0 ? 'warning' : 'info' }}"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

 