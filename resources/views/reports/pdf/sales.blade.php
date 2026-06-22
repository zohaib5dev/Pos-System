<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Report</title>
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
            border-bottom: 2px solid #4f46e5;
            padding-bottom: 20px;
        }
        .header h1 {
            color: #4f46e5;
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
            color: #4f46e5;
            border-bottom: 1px solid #ddd;
            padding-bottom: 8px;
            margin-bottom: 20px;
            font-size: 16px;
        }
        
        .summary-cards {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .summary-cards td {
            padding: 8px;
            vertical-align: top;
        }
        .summary-item {
            background: #f9fafb;
            padding: 15px;
            border-radius: 5px;
            border: 1px solid #e5e7eb;
            height: 80px;
        }
        .summary-label {
            font-size: 11px;
            color: #6b7280;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        .summary-value {
            font-size: 16px;
            font-weight: bold;
            color: #1f2937;
        }
        .summary-value.positive {
            color: #10b981;
        }
        .summary-value.negative {
            color: #ef4444;
        }
         
        .row {
            width: 100%;
            overflow: hidden;
            margin-bottom: 20px;
        }
        .col-md-6 {
            width: 48%;
            float: left;
            margin-right: 4%;
        }
        .col-md-6:last-child {
            margin-right: 0;
        }
        .clearfix {
            overflow: auto;
            clear: both;
        }
        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
        
        /* Tables */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th {
            background: #f3f4f6;
            padding: 10px;
            text-align: left;
            font-size: 11px;
            font-weight: 600;
            color: #374151;
            text-transform: uppercase;
            border-bottom: 2px solid #e5e7eb;
        }
        td {
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
        .text-primary {
            color: #4f46e5;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            color: #9ca3af;
            font-size: 10px;
            border-top: 1px solid #e5e7eb;
            padding-top: 20px;
            clear: both;
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
        
        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .summary-table td {
            width: 25%;
            padding: 10px;
            vertical-align: top;
        }
        .card {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 5px;
            padding: 15px;
        }
        .card-label {
            font-size: 11px;
            color: #6b7280;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        .card-value {
            font-size: 18px;
            font-weight: bold;
            color: #1f2937;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $business_name ?? 'Sales Report' }}</h1>
        <h2>Sales Report</h2>
        <div class="business-info">
            <p>{{ $business_address ?? '' }}</p>
            <p>Tel: {{ $business_phone ?? '' }} | Email: {{ $business_email ?? '' }}</p>
        </div>
    </div>

    <div class="period">
        Period: {{ date('F d, Y', strtotime($startDate)) }} - {{ date('F d, Y', strtotime($endDate)) }}
    </div>

    <div class="summary">
        <h3>Summary</h3>
        <table class="summary-table" cellpadding="0" cellspacing="0">
            <tr>
                <td>
                    <div class="card">
                        <div class="card-label">Total Orders</div>
                        <div class="card-value">{{ number_format($summary['total_orders'] ?? 0) }}</div>
                    </div>
                </td>
                <td>
                    <div class="card">
                        <div class="card-label">Total Revenue</div>
                        <div class="card-value text-success">${{ number_format($summary['total_revenue'] ?? 0, 2) }}</div>
                    </div>
                </td>
                <td>
                    <div class="card">
                        <div class="card-label">Total Tax</div>
                        <div class="card-value">${{ number_format($summary['total_tax'] ?? 0, 2) }}</div>
                    </div>
                </td>
                <td>
                    <div class="card">
                        <div class="card-label">Total Discount</div>
                        <div class="card-value">${{ number_format($summary['total_discount'] ?? 0, 2) }}</div>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="card">
                        <div class="card-label">Average Order</div>
                        <div class="card-value">${{ number_format($summary['average_order'] ?? 0, 2) }}</div>
                    </div>
                </td>
                <td>
                    <div class="card">
                        <div class="card-label">Cash Payments</div>
                        <div class="card-value">${{ number_format($summary['cash_payments'] ?? 0, 2) }}</div>
                    </div>
                </td>
                <td>
                    <div class="card">
                        <div class="card-label">Card Payments</div>
                        <div class="card-value">${{ number_format($summary['card_payments'] ?? 0, 2) }}</div>
                    </div>
                </td>
                <td>
                    <div class="card">
                        <div class="card-label">Bank Payments</div>
                        <div class="card-value">${{ number_format($summary['bank_payments'] ?? 0, 2) }}</div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <div class="row clearfix">
        @if(!empty($details['top_products']) && count($details['top_products']) > 0)
        <div class="col-md-6">
            <div class="summary">
                <h3>Top Selling Products</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>SKU</th>
                            <th class="text-right">Qty</th>
                            <th class="text-right">Sales</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($details['top_products'] as $product)
                        <tr>
                            <td>{{ $product->name }}</td>
                            <td>{{ $product->sku }}</td>
                            <td class="text-right">{{ number_format($product->total_quantity) }}</td>
                            <td class="text-right">${{ number_format($product->total_sales, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        @if(!empty($details['by_category']) && count($details['by_category']) > 0)
        <div class="col-md-6">
            <div class="summary">
                <h3>Sales by Category</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Category</th>
                            <th class="text-right">Qty</th>
                            <th class="text-right">Sales</th>
                            <th class="text-right">%</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $totalSales = collect($details['by_category'])->sum('total_sales');
                        @endphp
                        @foreach($details['by_category'] as $category)
                        <tr>
                            <td>{{ $category->name }}</td>
                            <td class="text-right">{{ number_format($category->total_quantity) }}</td>
                            <td class="text-right">${{ number_format($category->total_sales, 2) }}</td>
                            <td class="text-right">{{ $totalSales > 0 ? number_format(($category->total_sales / $totalSales) * 100, 1) : 0 }}%</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>

    @if(!empty($details['daily']) && count($details['daily']) > 0)
    <div class="summary" style="clear: both;">
        <h3>Sales Breakdown</h3>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th class="text-right">Orders</th>
                    <th class="text-right">Revenue</th>
                    <th class="text-right">Tax</th>
                    <th class="text-right">Discount</th>
                    <th class="text-right">Avg Order</th>
                </tr>
            </thead>
            <tbody>
                @foreach($details['daily'] as $day)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($day->date)->format('M d, Y') }}</td>
                    <td class="text-right">{{ $day->order_count }}</td>
                    <td class="text-right">${{ number_format($day->revenue, 2) }}</td>
                    <td class="text-right">${{ number_format($day->tax ?? 0, 2) }}</td>
                    <td class="text-right">${{ number_format($day->discount ?? 0, 2) }}</td>
                    <td class="text-right">${{ $day->order_count > 0 ? number_format($day->revenue / $day->order_count, 2) : '0.00' }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot style="font-weight: bold; background: #f9fafb;">
                <tr>
                    <td>Total</td>
                    <td class="text-right">{{ $details['daily']->sum('order_count') }}</td>
                    <td class="text-right">${{ number_format($details['daily']->sum('revenue'), 2) }}</td>
                    <td class="text-right">${{ number_format($details['daily']->sum('tax'), 2) }}</td>
                    <td class="text-right">${{ number_format($details['daily']->sum('discount'), 2) }}</td>
                    <td class="text-right">${{ number_format($details['daily']->sum('revenue') / max($details['daily']->sum('order_count'), 1), 2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
    @endif

    <div class="footer">
        <p>Generated on {{ date('F d, Y H:i:s') }}</p>
    </div>
</body>
</html>