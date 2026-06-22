<!-- ========== TOP PURCHASED PRODUCTS & PURCHASES BY SUPPLIER ========== -->
<div class="row g-3">
    <!-- Top Purchased Products -->
    <div class="col-12 col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-transparent border-0 pt-3">
                <h6 class="mb-0 fw-bold text-warning d-flex align-items-center gap-2">
                    <i class="bi bi-cart3"></i> Top Purchased Products
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
                                    <span class="badge bg-warning rounded-circle" style="width: 24px; height: 24px; padding: 0; display: flex; align-items: center; justify-content: center; color: #000;">{{ $loop->iteration }}</span>
                                    <div>
                                        <div class="fw-semibold small">{{ $item->name }}</div>
                                        <div class="text-muted small">SKU: {{ $item->sku }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold text-warning">{{ amo($item->total_cost) }}</div>
                                <div class="text-muted small">{{ number_format($item->total_quantity) }} units</div>
                            </div>
                        </div>
                        <div class="row g-1 small mt-1">
                            <div class="col-6">
                                <span class="text-muted">Avg Price:</span>
                                <span>{{ $item->total_quantity > 0 ? amo($item->total_cost / $item->total_quantity) : amo(0) }}</span>
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
                                <th class="text-end small">Total Cost</th>
                                <th class="text-end small">Avg Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($details['top_products'] ?? [] as $item)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge bg-warning rounded-circle" style="width: 24px; height: 24px; padding: 0; display: flex; align-items: center; justify-content: center; color: #000;">{{ $loop->iteration }}</span>
                                        <div>
                                            <div class="fw-semibold">{{ $item->name }}</div>
                                            <small class="text-muted">SKU: {{ $item->sku }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-end">{{ number_format($item->total_quantity) }}</td>
                                <td class="text-end fw-bold text-warning">{{ amo($item->total_cost) }}</td>
                                <td class="text-end">{{ $item->total_quantity > 0 ? amo($item->total_cost / $item->total_quantity) : amo(0) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">
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

    <!-- Purchases by Supplier -->
    <div class="col-12 col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-transparent border-0 pt-3">
                <h6 class="mb-0 fw-bold text-danger d-flex align-items-center gap-2">
                    <i class="bi bi-truck"></i> Purchases by Supplier
                </h6>
            </div>
            <div class="card-body pt-0">
                @php
                    $totalCost = collect($details['by_supplier'] ?? [])->sum('total_cost');
                @endphp

                <!-- Mobile Card View -->
                <div class="d-md-none">
                    @forelse($details['by_supplier'] ?? [] as $item)
                    @php
                        $percentage = $totalCost > 0 ? ($item->total_cost / $totalCost) * 100 : 0;
                    @endphp
                    <div class="border-bottom py-2">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-danger rounded-circle" style="width: 24px; height: 24px; padding: 0; display: flex; align-items: center; justify-content: center;">{{ $loop->iteration }}</span>
                                    <span class="fw-semibold small">{{ $item->supplier->name ?? 'Unknown' }}</span>
                                </div>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold text-danger">{{ amo($item->total_cost) }}</div>
                                <div class="text-muted small">{{ $item->purchase_count }} purchases</div>
                            </div>
                        </div>
                        <div class="progress progress-xs mt-1" style="height: 4px;">
                            <div class="progress-bar bg-danger" style="width: {{ $percentage }}%"></div>
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
                    <table class="table table-bordered table-hover align-middle mb-0">
                        <thead class="table">
                            <tr>
                                <th class="small">Supplier</th>
                                <th class="text-end small">Purchases</th>
                                <th class="text-end small">Total Cost</th>
                                <th class="text-end small">% of Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($details['by_supplier'] ?? [] as $item)
                            @php
                                $percentage = $totalCost > 0 ? ($item->total_cost / $totalCost) * 100 : 0;
                            @endphp
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge bg-danger rounded-circle" style="width: 24px; height: 24px; padding: 0; display: flex; align-items: center; justify-content: center;">{{ $loop->iteration }}</span>
                                        <span>{{ $item->supplier->name ?? 'Unknown' }}</span>
                                    </div>
                                </td>
                                <td class="text-end">{{ $item->purchase_count }}</td>
                                <td class="text-end fw-bold text-danger">{{ amo($item->total_cost) }}</td>
                                <td class="text-end">
                                    <div class="d-flex align-items-center justify-content-end gap-2">
                                        <div class="progress progress-xs" style="width: 60px; height: 4px;">
                                            <div class="progress-bar bg-danger" style="width: {{ $percentage }}%"></div>
                                        </div>
                                        <small class="text-muted">{{ number_format($percentage, 1) }}%</small>
                                    </div>
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
</div>

 