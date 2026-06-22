<!-- ========== TOP CUSTOMERS & CREDIT BALANCE ========== -->
<div class="row g-3">
    <!-- Top Customers by Spending -->
    <div class="col-12 col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-transparent border-0 pt-3">
                <h6 class="mb-0 fw-bold text-primary d-flex align-items-center gap-2">
                    <i class="bi bi-trophy"></i> Top Customers by Spending
                </h6>
            </div>
            <div class="card-body pt-0">
                <!-- Mobile Card View -->
                <div class="d-md-none">
                    @forelse($details['top_customers'] ?? [] as $customer)
                    @php
                        $avg = ($customer->orders_count ?? 0) > 0 ? ($customer->orders_sum_total_amount ?? 0) / ($customer->orders_count ?? 1) : 0;
                    @endphp
                    <div class="border-bottom py-2">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-primary rounded-circle" style="width: 24px; height: 24px; padding: 0; display: flex; align-items: center; justify-content: center;">{{ $loop->iteration }}</span>
                                    <div>
                                        <div class="fw-semibold small">{{ $customer->name }}</div>
                                        <div class="text-muted small">{{ $customer->phone }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold text-primary">{{ amo($customer->orders_sum_total_amount ?? 0) }}</div>
                                <div class="text-muted small">{{ $customer->orders_count ?? 0 }} orders</div>
                            </div>
                        </div>
                        <div class="row g-1 small mt-1">
                            <div class="col-12">
                                <span class="text-muted">Avg Order:</span>
                                <span>{{ amo($avg) }}</span>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-people fs-2 d-block mb-2"></i>
                        <p class="small">No data available</p>
                    </div>
                    @endforelse
                </div>

                <!-- Desktop Table View -->
                <div class="table-responsive d-none d-md-block">
                    <table class="table table-bordered table-hover align-middle mb-0">
                        <thead class="table">
                            <tr>
                                <th class="small">Customer</th>
                                <th class="text-end small">Total Spent</th>
                                <th class="text-end small">Orders</th>
                                <th class="text-end small">Avg Order</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($details['top_customers'] ?? [] as $customer)
                            @php
                                $avg = ($customer->orders_count ?? 0) > 0 ? ($customer->orders_sum_total_amount ?? 0) / ($customer->orders_count ?? 1) : 0;
                            @endphp
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge bg-primary rounded-circle" style="width: 24px; height: 24px; padding: 0; display: flex; align-items: center; justify-content: center;">{{ $loop->iteration }}</span>
                                        <div>
                                            <div class="fw-semibold">{{ $customer->name }}</div>
                                            <small class="text-muted">{{ $customer->phone }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-end fw-bold text-primary">{{ amo($customer->orders_sum_total_amount ?? 0) }}</td>
                                <td class="text-end">{{ $customer->orders_count ?? 0 }}</td>
                                <td class="text-end">{{ amo($avg) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">
                                    <i class="bi bi-people fs-2 d-block mb-2"></i>
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

    <!-- Customers with Credit Balance -->
    <div class="col-12 col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-transparent border-0 pt-3">
                <h6 class="mb-0 fw-bold text-warning d-flex align-items-center gap-2">
                    <i class="bi bi-credit-card"></i> Customers with Credit Balance
                </h6>
            </div>
            <div class="card-body pt-0">
                <!-- Mobile Card View -->
                <div class="d-md-none">
                    @forelse($details['customers_with_balance'] ?? [] as $customer)
                    <div class="border-bottom py-2">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="fw-semibold small">{{ $customer->name }}</div>
                                <div class="text-muted small">{{ $customer->phone }}</div>
                            </div>
                            <div class="text-end">
                                <span class="badge {{ $customer->current_balance > $customer->credit_limit ? 'bg-danger-soft text-danger' : 'bg-warning-soft text-warning' }} rounded-pill px-2 py-1">
                                    {{ $customer->current_balance > $customer->credit_limit ? 'Over Limit' : 'Has Balance' }}
                                </span>
                            </div>
                        </div>
                        <div class="row g-1 small mt-1">
                            <div class="col-6">
                                <span class="text-muted">Balance:</span>
                                <span class="fw-bold {{ $customer->current_balance > $customer->credit_limit ? 'text-danger' : 'text-warning' }}">
                                    {{ amo($customer->current_balance) }}
                                </span>
                            </div>
                            <div class="col-6">
                                <span class="text-muted">Credit Limit:</span>
                                <span>{{ amo($customer->credit_limit) }}</span>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4 text-success">
                        <i class="bi bi-check-circle fs-2 d-block mb-2"></i>
                        <p class="small">No customers with credit balance</p>
                    </div>
                    @endforelse
                </div>

                <!-- Desktop Table View -->
                <div class="table-responsive d-none d-md-block">
                    <table class="table table-bordered table-hover align-middle mb-0">
                        <thead class="table">
                            <tr>
                                <th class="small">Customer</th>
                                <th class="text-end small">Balance</th>
                                <th class="text-end small">Credit Limit</th>
                                <th class="text-center small">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($details['customers_with_balance'] ?? [] as $customer)
                            <tr>
                                <td>
                                    <div>
                                        <div class="fw-semibold">{{ $customer->name }}</div>
                                        <small class="text-muted">{{ $customer->phone }}</small>
                                    </div>
                                </td>
                                <td class="text-end fw-bold {{ $customer->current_balance > $customer->credit_limit ? 'text-danger' : 'text-warning' }}">
                                    {{ amo($customer->current_balance) }}
                                </td>
                                <td class="text-end">{{ amo($customer->credit_limit) }}</td>
                                <td class="text-center">
                                    @if($customer->current_balance > $customer->credit_limit)
                                        <span class="badge bg-danger-soft text-danger rounded-pill px-3 py-2">
                                            <i class="bi bi-exclamation-circle me-1"></i> Over Limit
                                        </span>
                                    @elseif($customer->current_balance > 0)
                                        <span class="badge bg-warning-soft text-warning rounded-pill px-3 py-2">
                                            <i class="bi bi-clock me-1"></i> Has Balance
                                        </span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-success">
                                    <i class="bi bi-check-circle fs-2 d-block mb-2"></i>
                                    <p class="small">No customers with credit balance</p>
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

 