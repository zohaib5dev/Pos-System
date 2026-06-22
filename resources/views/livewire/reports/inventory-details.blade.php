<!-- ========== LOW STOCK ALERT & STOCK BY CATEGORY ========== -->
<div class="row g-3">
    <!-- Low Stock Alert -->
    <div class="col-12 col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-transparent border-0 pt-3">
                <h6 class="mb-0 fw-bold text-warning d-flex align-items-center gap-2">
                    <i class="bi bi-exclamation-triangle"></i> Low Stock Alert
                </h6>
            </div>
            <div class="card-body pt-0">
                <!-- Mobile Card View -->
                <div class="d-md-none">
                    @forelse($details['low_stock'] ?? [] as $item)
                    <div class="border-bottom py-2">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="fw-semibold small">{{ $item->name }}</div>
                                <div class="text-muted small">SKU: {{ $item->sku }}</div>
                            </div>
                            <div class="text-end">
                                <span class="badge {{ $item->stock_quantity <= 0 ? 'bg-danger-soft text-danger' : 'bg-warning-soft text-warning' }} rounded-pill px-2 py-1">
                                    {{ $item->stock_quantity <= 0 ? 'Out of Stock' : 'Low Stock' }}
                                </span>
                            </div>
                        </div>
                        <div class="row g-1 small mt-1">
                            <div class="col-6">
                                <span class="text-muted">Stock:</span>
                                <span class="fw-bold {{ $item->stock_quantity <= 0 ? 'text-danger' : 'text-warning' }}">{{ $item->stock_quantity }}</span>
                            </div>
                            <div class="col-6">
                                <span class="text-muted">Threshold:</span>
                                <span>{{ $item->low_stock_threshold }}</span>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4 text-success">
                        <i class="bi bi-check-circle fs-2 d-block mb-2"></i>
                        <p class="small">No low stock items</p>
                    </div>
                    @endforelse
                </div>

                <!-- Desktop Table View -->
                <div class="table-responsive d-none d-md-block">
                    <table class="table table-bordered table-hover align-middle mb-0">
                        <thead class="table">
                            <tr>
                                <th class="small">Product</th>
                                <th class="text-end small">Stock</th>
                                <th class="text-end small">Threshold</th>
                                <th class="text-center small">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($details['low_stock'] ?? [] as $item)
                            <tr>
                                <td>
                                    <div>
                                        <div class="fw-semibold">{{ $item->name }}</div>
                                        <small class="text-muted">{{ $item->sku }}</small>
                                    </div>
                                </td>
                                <td class="text-end fw-bold {{ $item->stock_quantity <= 0 ? 'text-danger' : 'text-warning' }}">
                                    {{ $item->stock_quantity }}
                                </td>
                                <td class="text-end">{{ $item->low_stock_threshold }}</td>
                                <td class="text-center">
                                    @if($item->stock_quantity <= 0)
                                        <span class="badge bg-danger-soft text-danger rounded-pill px-3 py-2">
                                            <i class="bi bi-x-circle me-1"></i> Out of Stock
                                        </span>
                                    @else
                                        <span class="badge bg-warning-soft text-warning rounded-pill px-3 py-2">
                                            <i class="bi bi-exclamation-circle me-1"></i> Low Stock
                                        </span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-success">
                                    <i class="bi bi-check-circle fs-2 d-block mb-2"></i>
                                    <p class="small">No low stock items</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Stock Value by Category -->
    <div class="col-12 col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-transparent border-0 pt-3">
                <h6 class="mb-0 fw-bold text-info d-flex align-items-center gap-2">
                    <i class="bi bi-folder"></i> Stock Value by Category
                </h6>
            </div>
            <div class="card-body pt-0">
                @php
                    $totalValue = collect($details['by_category'] ?? [])->sum('stock_value');
                @endphp

                <!-- Mobile Card View -->
                <div class="d-md-none">
                    @forelse($details['by_category'] ?? [] as $item)
                    @php
                        $percentage = $totalValue > 0 ? ($item->stock_value / $totalValue) * 100 : 0;
                    @endphp
                    <div class="border-bottom py-2">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <span class="fw-semibold small">{{ $item->name }}</span>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold text-info">{{ amo($item->stock_value) }}</div>
                                <div class="text-muted small">{{ $item->product_count }} products</div>
                            </div>
                        </div>
                        <div class="progress progress-xs mt-1" style="height: 4px;">
                            <div class="progress-bar bg-info" style="width: {{ $percentage }}%"></div>
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
                                <th class="text-end small">Products</th>
                                <th class="text-end small">Stock Value</th>
                                <th class="text-end small">% of Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($details['by_category'] ?? [] as $item)
                            @php
                                $percentage = $totalValue > 0 ? ($item->stock_value / $totalValue) * 100 : 0;
                            @endphp
                            <tr>
                                <td>
                                    <span class="fw-semibold">{{ $item->name }}</span>
                                </td>
                                <td class="text-end">{{ $item->product_count }}</td>
                                <td class="text-end fw-bold text-info">{{ amo($item->stock_value) }}</td>
                                <td class="text-end">
                                    <div class="d-flex align-items-center justify-content-end gap-2">
                                        <div class="progress progress-xs" style="width: 60px; height: 4px;">
                                            <div class="progress-bar bg-info" style="width: {{ $percentage }}%"></div>
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

<!-- ========== TOP VALUE PRODUCTS ========== -->
<div class="card border-0 shadow-sm mt-3">
    <div class="card-header bg-transparent border-0 pt-3">
        <h6 class="mb-0 fw-bold text-success d-flex align-items-center gap-2">
            <i class="bi bi-star"></i> Top Value Products
        </h6>
    </div>
    <div class="card-body pt-0">
        <!-- Mobile Card View -->
        <div class="d-md-none">
            @forelse($details['top_products'] ?? [] as $item)
            <div class="border-bottom py-2">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="fw-semibold small">{{ $item->name }}</div>
                        <div class="text-muted small">SKU: {{ $item->sku }}</div>
                        <div class="text-muted small">{{ $item->category->name ?? 'N/A' }}</div>
                    </div>
                    <div class="text-end">
                        <div class="fw-bold text-success">{{ amo($item->stock_quantity * $item->purchase_price) }}</div>
                        <div class="text-muted small">{{ number_format($item->stock_quantity) }} units</div>
                    </div>
                </div>
                <div class="row g-1 small mt-1">
                    <div class="col-4">
                        <span class="text-muted">Cost:</span>
                        <span>{{ amo($item->purchase_price) }}</span>
                    </div>
                    <div class="col-4">
                        <span class="text-muted">Price:</span>
                        <span>{{ amo($item->selling_price) }}</span>
                    </div>
                    <div class="col-4">
                        <span class="text-muted">Profit:</span>
                        <span class="text-success">{{ amo($item->stock_quantity * ($item->selling_price - $item->purchase_price)) }}</span>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-4 text-muted">
                <i class="bi bi-box-open fs-2 d-block mb-2"></i>
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
                        <th class="small">Category</th>
                        <th class="text-end small">Stock</th>
                        <th class="text-end small">Unit Cost</th>
                        <th class="text-end small">Unit Price</th>
                        <th class="text-end small">Total Value</th>
                        <th class="text-end small">Potential Profit</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($details['top_products'] ?? [] as $item)
                    <tr>
                        <td>
                            <div>
                                <div class="fw-semibold">{{ $item->name }}</div>
                                <small class="text-muted">{{ $item->sku }}</small>
                            </div>
                        </td>
                        <td>{{ $item->category->name ?? 'N/A' }}</td>
                        <td class="text-end">{{ number_format($item->stock_quantity) }}</td>
                        <td class="text-end">{{ amo($item->purchase_price) }}</td>
                        <td class="text-end">{{ amo($item->selling_price) }}</td>
                        <td class="text-end fw-bold text-primary">{{ amo($item->stock_quantity * $item->purchase_price) }}</td>
                        <td class="text-end text-success fw-bold">{{ amo($item->stock_quantity * ($item->selling_price - $item->purchase_price)) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            <i class="bi bi-box-open fs-2 d-block mb-2"></i>
                            <p class="small">No data available</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

 