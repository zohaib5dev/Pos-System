<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #10b981;
            padding-bottom: 20px;
        }
        .header h1 {
            color: #10b981;
            margin: 0 0 10px 0;
            font-size: 24px;
        }
        .header h2 {
            color: #666;
            margin: 0;
            font-size: 18px;
            font-weight: normal;
        }
        .business-info {
            margin-bottom: 20px;
            text-align: center;
            color: #666;
            font-size: 11px;
        }
        .period {
            text-align: center;
            font-weight: bold;
            margin-bottom: 30px;
            padding: 10px;
            background: #f3f4f6;
            border-radius: 5px;
        }
        .summary {
            margin-bottom: 30px;
        }
        .summary h3 {
            color: #10b981;
            border-bottom: 1px solid #ddd;
            padding-bottom: 8px;
            margin-bottom: 20px;
            font-size: 16px;
        }
        
        /* Summary Table Layout */
        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .summary-table td {
            width: 25%;
            padding: 10px;
            vertical-align: top;
        }
        .summary-item {
            background: #f9fafb;
            padding: 15px;
            border-radius: 5px;
            border: 1px solid #e5e7eb;
            min-height: 80px;
        }
        .summary-label {
            font-size: 11px;
            color: #6b7280;
            text-transform: uppercase;
            margin-bottom: 5px;
            font-weight: 600;
        }
        .summary-value {
            font-size: 18px;
            font-weight: bold;
            color: #1f2937;
        }
        .summary-value.positive {
            color: #10b981;
        }
        .summary-value.negative {
            color: #ef4444;
        }
        .summary-value.warning {
            color: #f59e0b;
        }
        
        /* Data Tables */
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table.data-table th {
            background: #f3f4f6;
            padding: 10px;
            text-align: left;
            font-size: 11px;
            font-weight: 600;
            color: #374151;
            text-transform: uppercase;
            border-bottom: 2px solid #e5e7eb;
        }
        table.data-table td {
            padding: 8px 10px;
            border-bottom: 1px solid #e5e7eb;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .font-bold {
            font-weight: bold;
        }
        .text-success {
            color: #10b981;
        }
        .text-danger {
            color: #ef4444;
        }
        .text-warning {
            color: #f59e0b;
        }
        .text-primary {
            color: #10b981;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            color: #9ca3af;
            font-size: 10px;
            border-top: 1px solid #e5e7eb;
            padding-top: 20px;
        }
        .page-break {
            page-break-after: always;
        }
        .badge {
            display: inline-block;
            padding: 3px 6px;
            border-radius: 9999px;
            font-size: 10px;
            font-weight: 600;
        }
        .badge-success {
            background: #d1fae5;
            color: #065f46;
        }
        .badge-warning {
            background: #fed7aa;
            color: #92400e;
        }
        .badge-danger {
            background: #fee2e2;
            color: #991b1b;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $business_name ?? 'Business Name' }}</h1>
        <h2>Customer Report</h2>
        <div class="business-info">
            <p>{{ $business_address ?? '' }}</p>
            <p>Tel: {{ $business_phone ?? '' }} | Email: {{ $business_email ?? '' }}</p>
        </div>
    </div>

    <div class="period">
        Period: {{ date('F d, Y', strtotime($startDate)) }} - {{ date('F d, Y', strtotime($endDate)) }}
    </div>

    <!-- Summary Section - Using Table Layout -->
    <div class="summary">
        <h3>Customer Summary</h3>
        <table class="summary-table" cellpadding="0" cellspacing="0">
            <tr>
                <td>
                    <div class="summary-item">
                        <div class="summary-label">Total Customers</div>
                        <div class="summary-value">{{ number_format($summary['total_customers'] ?? 0) }}</div>
                    </div>
                </td>
                <td>
                    <div class="summary-item">
                        <div class="summary-label">Active Customers</div>
                        <div class="summary-value">{{ number_format($summary['active_customers'] ?? 0) }}</div>
                    </div>
                </td>
                <td>
                    <div class="summary-item">
                        <div class="summary-label">Inactive Customers</div>
                        <div class="summary-value">{{ number_format($summary['inactive_customers'] ?? 0) }}</div>
                    </div>
                </td>
                <td>
                    <div class="summary-item">
                        <div class="summary-label">Customers with Credit</div>
                        <div class="summary-value warning">{{ number_format($summary['customers_with_credit'] ?? 0) }}</div>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="summary-item">
                        <div class="summary-label">Total Credit</div>
                        <div class="summary-value negative">${{ number_format($summary['total_credit'] ?? 0, 2) }}</div>
                    </div>
                </td>
                <td>
                    <div class="summary-item">
                        <div class="summary-label">Average Credit</div>
                        <div class="summary-value">${{ number_format($summary['average_credit'] ?? 0, 2) }}</div>
                    </div>
                </td>
                <td>
                    <div class="summary-item">
                        <div class="summary-label">Total Orders</div>
                        <div class="summary-value">{{ number_format($summary['total_orders'] ?? 0) }}</div>
                    </div>
                </td>
                <td>
                    <div class="summary-item">
                        <div class="summary-label">Total Spent</div>
                        <div class="summary-value positive">${{ number_format($summary['total_spent'] ?? 0, 2) }}</div>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="summary-item">
                        <div class="summary-label">Average per Customer</div>
                        <div class="summary-value">${{ number_format($summary['average_spent_per_customer'] ?? 0, 2) }}</div>
                    </div>
                </td>
                <td>
                    <div class="summary-item">
                        <div class="summary-label">Credit Utilization</div>
                        <div class="summary-value">
                            {{ number_format((($summary['total_credit'] ?? 0) / max(($summary['total_spent'] ?? 1), 1)) * 100, 1) }}%
                        </div>
                    </div>
                </td>
                <td>
                    <div class="summary-item">
                        <div class="summary-label">Avg Orders/Customer</div>
                        <div class="summary-value">
                            {{ number_format(($summary['total_orders'] ?? 0) / max(($summary['total_customers'] ?? 1), 1), 1) }}
                        </div>
                    </div>
                </td>
                <td>
                    <div class="summary-item">
                        <div class="summary-label">Active Rate</div>
                        <div class="summary-value">
                            {{ number_format((($summary['active_customers'] ?? 0) / max(($summary['total_customers'] ?? 1), 1)) * 100, 1) }}%
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Top Customers -->
    @if(!empty($details['top_customers']) && count($details['top_customers']) > 0)
    <div class="summary">
        <h3>Top Customers by Spending</h3>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Customer</th>
                    <th>Contact</th>
                    <th class="text-right">Orders</th>
                    <th class="text-right">Total Spent</th>
                    <th class="text-right">Average Order</th>
                </tr>
            </thead>
            <tbody>
                @foreach($details['top_customers'] as $customer)
                <tr>
                    <td><strong>{{ $customer->name }}</strong></td>
                    <td>{{ $customer->email ?? $customer->phone ?? 'N/A' }}</td>
                    <td class="text-right">{{ number_format($customer->orders_count ?? 0) }}</td>
                    <td class="text-right">${{ number_format($customer->orders_sum_total_amount ?? 0, 2) }}</td>
                    <td class="text-right">
                        ${{ number_format(($customer->orders_sum_total_amount ?? 0) / max(($customer->orders_count ?? 1), 1), 2) }}
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot style="font-weight: bold; background: #f9fafb;">
                <tr>
                    <td colspan="2">Total / Average</td>
                    <td class="text-right">{{ number_format(collect($details['top_customers'])->sum('orders_count')) }}</td>
                    <td class="text-right">${{ number_format(collect($details['top_customers'])->sum('orders_sum_total_amount'), 2) }}</td>
                    <td class="text-right">
                        ${{ number_format(collect($details['top_customers'])->sum('orders_sum_total_amount') / max(collect($details['top_customers'])->sum('orders_count'), 1), 2) }}
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
    @endif

    <!-- Customers with Balance -->
    @if(!empty($details['customers_with_balance']) && count($details['customers_with_balance']) > 0)
    <div class="summary">
        <h3>Customers with Credit Balance</h3>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Customer</th>
                    <th>Phone</th>
                    <th class="text-right">Current Balance</th>
                    <th class="text-right">Credit Limit</th>
                    <th class="text-right">Available Credit</th>
                    <th class="text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($details['customers_with_balance'] as $customer)
                @php
                    $availableCredit = $customer->credit_limit - $customer->current_balance;
                @endphp
                <tr>
                    <td><strong>{{ $customer->name }}</strong></td>
                    <td>{{ $customer->phone ?? 'N/A' }}</td>
                    <td class="text-right {{ $customer->current_balance > $customer->credit_limit ? 'text-danger' : 'text-warning' }}">
                        ${{ number_format($customer->current_balance, 2) }}
                    </td>
                    <td class="text-right">${{ number_format($customer->credit_limit, 2) }}</td>
                    <td class="text-right {{ $availableCredit >= 0 ? 'text-success' : 'text-danger' }}">
                        ${{ number_format($availableCredit, 2) }}
                    </td>
                    <td class="text-center">
                        @if($customer->current_balance > $customer->credit_limit)
                            <span class="badge badge-danger">Over Limit</span>
                        @elseif($customer->current_balance > 0)
                            <span class="badge badge-warning">Has Balance</span>
                        @else
                            <span class="badge badge-success">No Balance</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot style="font-weight: bold; background: #f9fafb;">
                <tr>
                    <td colspan="2">Total</td>
                    <td class="text-right">${{ number_format(collect($details['customers_with_balance'])->sum('current_balance'), 2) }}</td>
                    <td class="text-right">${{ number_format(collect($details['customers_with_balance'])->sum('credit_limit'), 2) }}</td>
                    <td class="text-right">${{ number_format(collect($details['customers_with_balance'])->sum('credit_limit') - collect($details['customers_with_balance'])->sum('current_balance'), 2) }}</td>
                    <td class="text-center">{{ count($details['customers_with_balance']) }} customers</td>
                </tr>
            </tfoot>
        </table>
    </div>
    @endif

    <!-- Customer Status Distribution -->
    <div class="summary">
        <h3>Customer Status Distribution</h3>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Status</th>
                    <th class="text-right">Count</th>
                    <th class="text-right">Percentage</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><span style="color: #10b981;">●</span> Active Customers</td>
                    <td class="text-right">{{ number_format($summary['active_customers'] ?? 0) }}</td>
                    <td class="text-right">
                        {{ number_format((($summary['active_customers'] ?? 0) / max(($summary['total_customers'] ?? 1), 1)) * 100, 1) }}%
                    </td>
                </tr>
                <tr>
                    <td><span style="color: #6b7280;">●</span> Inactive Customers</td>
                    <td class="text-right">{{ number_format($summary['inactive_customers'] ?? 0) }}</td>
                    <td class="text-right">
                        {{ number_format((($summary['inactive_customers'] ?? 0) / max(($summary['total_customers'] ?? 1), 1)) * 100, 1) }}%
                    </td>
                </tr>
                <tr>
                    <td><span style="color: #f59e0b;">●</span> Customers with Credit</td>
                    <td class="text-right">{{ number_format($summary['customers_with_credit'] ?? 0) }}</td>
                    <td class="text-right">
                        {{ number_format((($summary['customers_with_credit'] ?? 0) / max(($summary['total_customers'] ?? 1), 1)) * 100, 1) }}%
                    </td>
                </tr>
            </tbody>
            <tfoot style="font-weight: bold; background: #f9fafb;">
                <tr>
                    <td>Total Customers</td>
                    <td class="text-right">{{ number_format($summary['total_customers'] ?? 0) }}</td>
                    <td class="text-right">100%</td>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="footer">
        <p>Generated on {{ date(dateFormat().' H:i:s') }}</p> 
    </div>
</body>
</html>