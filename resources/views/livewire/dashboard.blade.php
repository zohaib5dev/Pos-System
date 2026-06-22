<div>
<!-- ========== STATS CARDS (Row 1 - With Enhanced Shadows) ========== -->
<div class="row g-3 mb-4">
    <!-- Today's Sales -->
    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
        <div class="card h-100 border-0 shadow-lg stat-card-primary">
            <div class="card-body p-3 p-xl-4">
                <div class="d-flex align-items-start justify-content-between">
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="badge bg-primary text-white px-2 py-1 small text-uppercase">Today</span>
                            <span class="text-success small fw-semibold">
                                <i class="bi bi-arrow-up-short"></i> 
                                {{ $stats['week_sales'] > 0 ? round(($stats['today_sales'] / $stats['week_sales'] * 100), 1) : 0 }}%
                            </span>
                        </div>
                        <h2 class="mb-0 fw-bold  text-primary">{{ amo($stats['today_sales']) }}</h2>
                        <span class="text-muted small">Sales Revenue</span>
                    </div>
                    <div class="stat-icon-wrapper bg-primary rounded-3 p-3 flex-shrink-0">
                        <i class="bi bi-cash-stack fs-2 text-white"></i>
                    </div>
                </div>
                <div class="mt-3 pt-2 border-top border-light">
                    <a href="{{ route('orders.index') }}?dateRange=today" class="text-decoration-none text-primary small fw-semibold d-flex align-items-center gap-1">
                        View details <i class="bi bi-chevron-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Products -->
    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
        <div class="card h-100 border-0 shadow-lg stat-card-success">
            <div class="card-body p-3 p-xl-4">
                <div class="d-flex align-items-start justify-content-between">
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="badge bg-success text-white px-2 py-1 small text-uppercase">Inventory</span>
                            <span class="text-muted small">items</span>
                        </div>
                        <h2 class="mb-0 fw-bold  text-success">{{ $stats['total_products'] }}</h2>
                        <span class="text-muted small">Total Products</span>
                    </div>
                    <div class="stat-icon-wrapper bg-success rounded-3 p-3 flex-shrink-0">
                        <i class="bi bi-box-seam fs-2 text-white"></i>
                    </div>
                </div>
                <div class="mt-3 pt-2 border-top border-light">
                    <a href="{{ route('products.index') }}" class="text-decoration-none text-success small fw-semibold d-flex align-items-center gap-1">
                        Manage products <i class="bi bi-chevron-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Low Stock Alert -->
    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
        <div class="card h-100 border-0 shadow-lg stat-card-warning">
            <div class="card-body p-3 p-xl-4">
                <div class="d-flex align-items-start justify-content-between">
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="badge bg-warning text-dark px-2 py-1 small text-uppercase">Alert</span>
                            <span class="text-danger small fw-semibold">
                                <i class="bi bi-exclamation-circle"></i> needs restock
                            </span>
                        </div>
                        <h2 class="mb-0 fw-bold  text-warning">{{ $stats['low_stock_products'] }}</h2>
                        <span class="text-muted small">Low Stock Items</span>
                    </div>
                    <div class="stat-icon-wrapper bg-warning rounded-3 p-3 flex-shrink-0">
                        <i class="bi bi-exclamation-triangle fs-2 text-dark"></i>
                    </div>
                </div>
                <div class="mt-3 pt-2 border-top border-light">
                    <a href="{{ route('inventory.low-stock') }}" class="text-decoration-none text-warning small fw-semibold d-flex align-items-center gap-1">
                        View alerts <i class="bi bi-chevron-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Customers -->
    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
        <div class="card h-100 border-0 shadow-lg stat-card-danger">
            <div class="card-body p-3 p-xl-4">
                <div class="d-flex align-items-start justify-content-between">
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="badge bg-danger text-white px-2 py-1 small text-uppercase">People</span>
                            <span class="text-muted small">active clients</span>
                        </div>
                        <h2 class="mb-0 fw-bold  text-danger">{{ $stats['total_customers'] }}</h2>
                        <span class="text-muted small">Total Customers</span>
                    </div>
                    <div class="stat-icon-wrapper bg-danger rounded-3 p-3 flex-shrink-0">
                        <i class="bi bi-people fs-2 text-white"></i>
                    </div>
                </div>
                <div class="mt-3 pt-2 border-top border-light">
                    <a href="{{ route('customers.index') }}" class="text-decoration-none text-danger small fw-semibold d-flex align-items-center gap-1">
                        View all <i class="bi bi-chevron-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ========== MINI STATS (Row 2 - With Enhanced Shadows) ========== -->
<div class="row g-3 mb-4">
    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
        <div class="card h-100 border-0 shadow-lg hover-lift">
            <div class="card-body p-3 p-xl-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="mini-icon bg-info text-white rounded-3 p-3 flex-shrink-0">
                        <i class="bi bi-calendar3 fs-4"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h4 class="mb-0 fw-bold text-info">{{ amo($stats['month_sales']) }}</h4>
                        <small class="text-muted">Monthly Sales</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
        <div class="card h-100 border-0 shadow-lg hover-lift">
            <div class="card-body p-3 p-xl-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="mini-icon bg-warning text-dark rounded-3 p-3 flex-shrink-0">
                        <i class="bi bi-clock-history fs-4"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h4 class="mb-0 fw-bold text-warning">{{ $stats['pending_orders'] }}</h4>
                        <small class="text-muted">Pending Orders</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
        <div class="card h-100 border-0 shadow-lg hover-lift">
            <div class="card-body p-3 p-xl-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="mini-icon bg-secondary text-white rounded-3 p-3 flex-shrink-0">
                        <i class="bi bi-receipt fs-4"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h4 class="mb-0 fw-bold text-secondary">{{ amo($stats['today_expenses']) }}</h4>
                        <small class="text-muted">Today's Expenses</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
        <div class="card h-100 border-0 shadow-lg hover-lift">
            <div class="card-body p-3 p-xl-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="mini-icon bg-primary text-white rounded-3 p-3 flex-shrink-0">
                        <i class="bi bi-graph-up-arrow fs-4"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h4 class="mb-0 fw-bold text-primary">{{ amo($stats['average_order_value']) }}</h4>
                        <small class="text-muted">Avg Order Value</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ========== DATE RANGE FILTER (With Shadow) ========== -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-lg">
            <div class="card-body py-3">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <i class="bi bi-calendar-range text-primary fs-5"></i>
                        <span class="fw-semibold me-2 d-none d-sm-inline">Date Range:</span>
                        <div class="btn-group btn-group-sm flex-wrap" role="group">
                            <button type="button" wire:click="$set('dateRange', 'today')" wire:loading.attr="disabled"
                                class="btn {{ $dateRange === 'today' ? 'btn-primary shadow-sm' : 'btn-outline-primary' }}">Today</button>
                            <button type="button" wire:click="$set('dateRange', 'week')" wire:loading.attr="disabled"
                                class="btn {{ $dateRange === 'week' ? 'btn-primary shadow-sm' : 'btn-outline-primary' }}">Week</button>
                            <button type="button" wire:click="$set('dateRange', 'month')" wire:loading.attr="disabled"
                                class="btn {{ $dateRange === 'month' ? 'btn-primary shadow-sm' : 'btn-outline-primary' }}">Month</button>
                            <button type="button" wire:click="$set('dateRange', 'year')" wire:loading.attr="disabled"
                                class="btn {{ $dateRange === 'year' ? 'btn-primary shadow-sm' : 'btn-outline-primary' }}">Year</button>
                        </div>
                    </div>
                    <div class="text-muted small">
                        <i class="bi bi-info-circle me-1"></i>
                        <span class="d-none d-sm-inline">Showing data from </span>
                        <strong>{{ $startDate ? $startDate->format('M d, Y') : 'N/A' }}</strong>
                        <span class="d-none d-sm-inline"> to </span>
                        <strong>{{ $endDate ? $endDate->format('M d, Y') : 'N/A' }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ========== CHART + TOP PRODUCTS ========== -->
<div class="row g-3 mb-4">
    <!-- Chart -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-lg h-100">
            <div class="card-header bg-transparent border-0 pt-3">
                <h6 class="m-0 fw-bold text-primary"><i class="bi bi-graph-up me-2"></i>Sales Overview</h6>
                <small class="text-muted" id="chartDataInfo"></small>
            </div>
            <div class="card-body pt-0">
                <div id="chartContainer" style="height: 280px; width: 100%; position: relative;" wire:ignore>
                    <canvas id="salesChart" style="height: 100%; width: 100%;"></canvas>
                    <div id="chartLoading" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); display: none;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Products -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-lg h-100">
            <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center pt-3">
                <h6 class="m-0 fw-bold text-primary"><i class="bi bi-trophy me-2"></i>Top Products</h6>
                <span class="badge bg-primary text-white rounded-pill">{{ count($topProducts) }}</span>
            </div>
            <div class="card-body pt-0">
                @forelse($topProducts as $index => $product)
                <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                    <div>
                        <span class="badge bg-secondary text-white rounded-circle me-2">{{ $index + 1 }}</span>
                        <span class="fw-semibold">{{ $product->name }}</span>
                        <div class="small text-muted">{{ $product->total_quantity }} units sold</div>
                    </div>
                    <span class="fw-bold text-primary">{{ amo($product->total_sales) }}</span>
                </div>
                @empty
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-box-seam fs-1 d-block mb-3"></i>
                    <p>No sales data for this period</p>
                </div>
                @endforelse
            </div>
            <div class="card-footer bg-transparent border-0 text-center py-2">
                <a href="{{ route('reports.index') }}?dateRange={{ $dateRange }}" class="small text-primary fw-semibold">
                    View Full Report <i class="bi bi-arrow-right-short"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- ========== RECENT ORDERS (Mobile Optimized) ========== -->
<div class="row g-3 mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-lg">
            <div class="card-header bg-transparent border-0 d-flex flex-wrap align-items-center justify-content-between pt-3">
                <h6 class="m-0 fw-bold text-primary"><i class="bi bi-cart3 me-2"></i>Recent Orders</h6>
                <a href="{{ route('orders.index') }}" class="btn btn-primary btn-sm shadow-sm">View All</a>
            </div>
            <div class="card-body pt-0">
                <!-- Desktop Table -->
                <div class="table-responsive d-none d-md-block">
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table">
                            <tr>
                                <th>Order #</th>
                                <th>Customer</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th class="text-end">Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentOrders as $order)
                            <tr>
                                <td><span class="fw-bold">#{{ $order->order_number }}</span></td>
                                <td>{{ $order->customer->name ?? 'Walk-in Customer' }}</td>
                                <td><span class="fw-semibold">{{ amo($order->total_amount) }}</span></td>
                                <td>
                                    @php
                                        $statusClasses = ['completed' => 'success', 'pending' => 'warning', 'processing' => 'info', 'cancelled' => 'danger'];
                                        $class = $statusClasses[$order->status] ?? 'secondary';
                                    @endphp
                                    <span class="badge bg-{{ $class }} text-white rounded-pill px-3 py-2">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td class="text-end text-muted small">{{ $order->created_at }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    <i class="bi bi-cart-x fs-1 d-block mb-3"></i>
                                    <p>No orders found</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Cards -->
                <div class="d-md-none">
                    @forelse($recentOrders as $order)
                    <div class="card border-0 shadow-sm mb-3">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <span class="fw-bold fs-6">#{{ $order->order_number }}</span>
                                    <div class="text-muted small">{{ $order->customer->name ?? 'Walk-in Customer' }}</div>
                                </div>
                                @php
                                    $statusClasses = ['completed' => 'success', 'pending' => 'warning', 'processing' => 'info', 'cancelled' => 'danger'];
                                    $class = $statusClasses[$order->status] ?? 'secondary';
                                @endphp
                                <span class="badge bg-{{ $class }} text-white rounded-pill px-3 py-2">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-bold fs-5">{{ amo($order->total_amount) }}</span>
                                <span class="text-muted small">{{ $order->created_at }}</span>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-cart-x fs-1 d-block mb-3"></i>
                        <p>No orders found</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ========== RECENT CUSTOMERS (Mobile Optimized) ========== -->
<div class="row g-3">
    <div class="col-12">
        <div class="card border-0 shadow-lg">
            <div class="card-header bg-transparent border-0 d-flex flex-wrap align-items-center justify-content-between pt-3">
                <h6 class="m-0 fw-bold text-primary"><i class="bi bi-people me-2"></i>Recent Customers</h6>
                <a href="{{ route('customers.index') }}" class="btn btn-primary btn-sm shadow-sm">View All</a>
            </div>
            <div class="card-body pt-0">
                <!-- Desktop Table -->
                <div class="table-responsive d-none d-md-block">
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table">
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th class="text-end">Joined</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentCustomers as $customer)
                            <tr>
                                <td><span class="fw-semibold">{{ $customer->name }}</span></td>
                                <td>{{ $customer->email ?? '—' }}</td>
                                <td>{{ $customer->phone ?? '—' }}</td>
                                <td class="text-end text-muted small">{{ $customer->created_at }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">
                                    <i class="bi bi-people fs-1 d-block mb-3"></i>
                                    <p>No customers found</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Cards -->
                <div class="d-md-none">
                    @forelse($recentCustomers as $customer)
                    <div class="card border-0 shadow-sm mb-3">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-start mb-1">
                                <div>
                                    <span class="fw-bold fs-6">{{ $customer->name }}</span>
                                    <div class="text-muted small">{{ $customer->email ?? 'No email' }}</div>
                                </div>
                                <span class="text-muted small">{{ $customer->created_at }}</span>
                            </div>
                            <div class="text-muted small">
                                <i class="bi bi-telephone me-1"></i> {{ $customer->phone ?? 'No phone' }}
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-people fs-1 d-block mb-3"></i>
                        <p>No customers found</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ========== STYLES ========== -->




<script>
    document.addEventListener('DOMContentLoaded', function() {
        let chartInstance = null;
        const canvas = document.getElementById('salesChart');
        const infoElement = document.getElementById('chartDataInfo');
        
        // Function to clean data and get fresh arrays
        function getCleanData(data) {            
            // Default fallback
            let labels = ['No Data'];
            let values = [0];
            
            if (!data) {
                return { labels, values };
            }
            
            // If data is an array with one item, extract it
            if (Array.isArray(data) && data.length === 1) {
                data = data[0];
            }
            
            // If data is still an array with multiple items, try to use first item
            if (Array.isArray(data) && data.length > 1) {
                data = data[0];
            }
                            
                // Extract labels
                if (data.labels && Array.isArray(data.labels) && data.labels.length > 0) {
                    labels = [...data.labels];
                } 
                
                // Extract values
                if (data.values && Array.isArray(data.values) && data.values.length > 0) {
                    // Filter out any non-numeric values and create fresh array
                    values = data.values
                        .filter(v => typeof v === 'number' || !isNaN(parseFloat(v)))
                        .map(v => parseFloat(v) || 0);
                    
                } 
           
            
            // If all values are 0 or empty, use default
            if (values.length === 0 || values.every(v => v === 0)) {
                values = [0];
            }
            
            // Ensure labels and values have same length
            if (labels.length !== values.length) {
                if (labels.length === 0) {
                    labels = ['No Data'];
                    values = [0];
                } else {
                    // Pad or truncate values to match labels
                    while (values.length < labels.length) {
                        values.push(0);
                    }
                    values = values.slice(0, labels.length);
                }
            }
            
            return { labels, values };
        }
        
        // Function to create/update chart
        function renderChart(data) {
            
            if (!canvas) {
                return;
            }
            
            const ctx = canvas.getContext('2d');
            
            // Get clean data
            const cleanData = getCleanData(data);
            const labels = cleanData.labels;
            const values = cleanData.values;
            
            
            // Update info
            if (infoElement) {
                infoElement.textContent = `Data points: ${labels.length} | Range: ${values.length > 0 ? '$' + Math.min(...values).toFixed(0) + ' - $' + Math.max(...values).toFixed(0) : '$0'}`;
            }
            
            // Destroy existing chart if any
            if (chartInstance) {
                chartInstance.destroy();
                chartInstance = null;
            }
            
            try {
                // Create chart with fresh data
                chartInstance = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Sales ($)',
                            data: values,
                            borderColor: '#4e73df',
                            backgroundColor: 'rgba(78, 115, 223, 0.05)',
                            tension: 0.3,
                            fill: true,
                            borderWidth: 2,
                            pointRadius: 3,
                            pointBackgroundColor: '#4e73df'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: function(ctx) {
                                        return 'Sales: $' + (ctx.parsed.y || 0).toLocaleString();
                                    }
                                }
                            }
                        },
                        scales: {
                            y: { 
                                beginAtZero: true, 
                                ticks: { 
                                    callback: function(v) {
                                        return '$' + (v || 0).toLocaleString();
                                    }
                                }
                            },
                            x: {
                                ticks: {
                                    maxTicksLimit: 20,
                                    autoSkip: true
                                }
                            }
                        }
                    }
                });
                
                
            } catch (error) {
            }
        }
        
        // Try to get initial data from the page
        function getInitialData() {
            try {
                // Look for the chart data in the page
                const dataElement = document.getElementById('chart-data');
                if (dataElement) {
                    const data = JSON.parse(dataElement.textContent);
                    return data;
                }
            } catch (e) {
            }
            
            // Fallback: try to use the PHP variable
            try {
                const data = @json($chartData);
                return data;
            } catch (e) {
                return null;
            }
        }
        
        // Initial render
        const initialData = getInitialData();
        renderChart(initialData);
        
        // Listen for chart updates from Livewire
        window.addEventListener('update-chart', function(event) {
            renderChart(event.detail);
        });
        
        // Also listen for Livewire events directly
        if (typeof Livewire !== 'undefined') {
            Livewire.on('update-chart', function(data) {
                renderChart(data);
            });
        }
        
        // Handle window resize
        let resizeTimer;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function() {
                if (chartInstance) {
                    chartInstance.resize();
                }
            }, 250);
        });
        
    });
</script>

</div>