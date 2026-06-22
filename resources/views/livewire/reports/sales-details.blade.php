<!-- ========== TOP SELLING PRODUCTS & SALES BY CATEGORY ========== -->
<div class="row g-3 mb-3">
    <!-- Top Selling Products -->
    <div class="col-12 col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-transparent border-0 pt-3">
                <h6 class="mb-0 fw-bold d-flex align-items-center gap-2">
                    <i class="bi bi-trophy"></i> Top Selling Products
                </h6>
            </div>
            <div class="card-body pt-0">
                <!-- Mobile Card View -->
                <div class="d-md-none">
                    @forelse($details['top_products'] ?? [] as $item)
                    <div class="border-bottom py-2">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-primary rounded-circle" style="width: 24px; height: 24px; padding: 0; display: flex; align-items: center; justify-content: center;">{{ $loop->iteration }}</span>
                                    <div>
                                        <div class="fw-semibold small">{{ $item->name }}</div>
                                        <div class="text-muted small">SKU: {{ $item->sku }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold">{{ amo($item->total_sales) }}</div>
                                <div class="text-muted small">{{ number_format($item->total_quantity) }} units</div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-box-seam fs-2 d-block mb-2"></i>
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
                                <th class="text-end small">Quantity</th>
                                <th class="text-end small">Sales</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($details['top_products'] ?? [] as $item)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge bg-primary rounded-circle" style="width: 24px; height: 24px; padding: 0; display: flex; align-items: center; justify-content: center;">{{ $loop->iteration }}</span>
                                        <div>
                                            <div class="fw-semibold">{{ $item->name }}</div>
                                            <small class="text-muted">SKU: {{ $item->sku }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-end">{{ number_format($item->total_quantity) }}</td>
                                <td class="text-end fw-bold text-primary">{{ amo($item->total_sales) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center py-4 text-muted">
                                    <i class="bi bi-box-seam fs-2 d-block mb-2"></i>
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

    <!-- Sales by Category -->
    <div class="col-12 col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-transparent border-0 pt-3">
                <h6 class="mb-0 fw-bold text-success d-flex align-items-center gap-2">
                    <i class="bi bi-pie-chart"></i> Sales by Category
                </h6>
            </div>
            <div class="card-body pt-0">
                @php
                    $totalSales = collect($details['by_category'] ?? [])->sum('total_sales');
                @endphp

                <!-- Mobile Card View -->
                <div class="d-md-none">
                    @forelse($details['by_category'] ?? [] as $item)
                    @php
                        $percentage = $totalSales > 0 ? ($item->total_sales / $totalSales) * 100 : 0;
                    @endphp
                    <div class="border-bottom py-2">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-success rounded-circle" style="width: 24px; height: 24px; padding: 0; display: flex; align-items: center; justify-content: center;">{{ $loop->iteration }}</span>
                                    <span class="fw-semibold small">{{ $item->name }}</span>
                                </div>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold">{{ amo($item->total_sales) }}</div>
                                <div class="text-muted small">{{ number_format($percentage, 1) }}%</div>
                            </div>
                        </div>
                        <div class="progress progress-xs mt-1" style="height: 4px;">
                            <div class="progress-bar bg-success" style="width: {{ $percentage }}%"></div>
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
                                <th class="text-end small">Quantity</th>
                                <th class="text-end small">Sales</th>
                                <th class="text-end small">%</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($details['by_category'] ?? [] as $item)
                            @php
                                $percentage = $totalSales > 0 ? ($item->total_sales / $totalSales) * 100 : 0;
                            @endphp
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge bg-success rounded-circle" style="width: 24px; height: 24px; padding: 0; display: flex; align-items: center; justify-content: center;">{{ $loop->iteration }}</span>
                                        <span>{{ $item->name }}</span>
                                    </div>
                                </td>
                                <td class="text-end">{{ number_format($item->total_quantity) }}</td>
                                <td class="text-end fw-bold text-success">{{ amo($item->total_sales) }}</td>
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
                                <td colspan="4" class="text-center py-4 text-muted">
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

<!-- ========== SALES BREAKDOWN ========== -->
@if(isset($details['daily']) && $details['daily']->count() > 0)
<div class="card border-0 shadow-sm">
    <div class="card-header bg-transparent border-0 pt-3">
        <h6 class="mb-0 fw-bold text-info d-flex align-items-center gap-2">
            <i class="bi bi-calendar3"></i> Sales Breakdown
        </h6>
    </div>
    <div class="card-body pt-0">
        <!-- Mobile Card View -->
        <div class="d-md-none">
            @foreach($details['daily'] as $day)
            <div class="border-bottom py-2">
                <div class="d-flex justify-content-between align-items-start mb-1">
                    <span class="fw-semibold">{{ \Carbon\Carbon::parse($day->date)->format('M d, Y') }}</span>
                    <span class="badge bg-primary-soft text-primary rounded-pill">{{ $day->order_count }} orders</span>
                </div>
                <div class="row g-1 small">
                    <div class="col-6">
                        <span class="text-muted">Revenue:</span>
                        <span class="fw-bold">{{ amo($day->revenue) }}</span>
                    </div>
                    <div class="col-6">
                        <span class="text-muted">Avg Order:</span>
                        <span class="fw-bold">{{ $day->order_count > 0 ? amo($day->revenue / $day->order_count) : amo(0) }}</span>
                    </div>
                    <div class="col-6">
                        <span class="text-muted">Tax:</span>
                        <span>{{ amo($day->tax ?? 0) }}</span>
                    </div>
                    <div class="col-6">
                        <span class="text-muted">Discount:</span>
                        <span>{{ amo($day->discount ?? 0) }}</span>
                    </div>
                </div>
            </div>
            @endforeach
            <!-- Mobile Total -->
            <div class="bg-light-soft rounded-3 p-2 mt-2">
                <div class="row g-1 small fw-bold">
                    <div class="col-6">Total Orders: {{ $details['daily']->sum('order_count') }}</div>
                    <div class="col-6">Total Revenue: {{ amo($details['daily']->sum('revenue')) }}</div>
                </div>
            </div>
        </div>

        <!-- Desktop Table View -->
        <div class="table-responsive d-none d-md-block">
            <table class="table table-bordered table-hover align-middle mb-0">
                <thead class="table">
                    <tr>
                        <th class="small">Date</th>
                        <th class="text-end small">Orders</th>
                        <th class="text-end small">Revenue</th>
                        <th class="text-end small">Tax</th>
                        <th class="text-end small">Discount</th>
                        <th class="text-end small">Avg Order</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($details['daily'] as $day)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($day->date)->format('M d, Y') }}</td>
                        <td class="text-end">{{ $day->order_count }}</td>
                        <td class="text-end fw-bold text-primary">{{ amo($day->revenue) }}</td>
                        <td class="text-end">{{ amo($day->tax ?? 0) }}</td>
                        <td class="text-end">{{ amo($day->discount ?? 0) }}</td>
                        <td class="text-end">{{ $day->order_count > 0 ? amo($day->revenue / $day->order_count) : amo(0) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="table">
                    <tr>
                        <th class="fw-bold">Total</th>
                        <th class="text-end fw-bold">{{ $details['daily']->sum('order_count') }}</th>
                        <th class="text-end fw-bold text-primary">{{ amo($details['daily']->sum('revenue')) }}</th>
                        <th class="text-end">{{ amo($details['daily']->sum('tax')) }}</th>
                        <th class="text-end">{{ amo($details['daily']->sum('discount')) }}</th>
                        <th class="text-end fw-bold">{{ amo($details['daily']->sum('revenue') / max($details['daily']->sum('order_count'), 1)) }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endif

 