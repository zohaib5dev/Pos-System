<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supplier Report</title>
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
            border-bottom: 2px solid #6b7280;
            padding-bottom: 20px;
        }
        .header h1 {
            color: #6b7280;
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
            color: #6b7280;
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
        .text-secondary {
            color: #6b7280;
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
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $business_name ?? 'Business Name' }}</h1>
        <h2>Supplier Report</h2>
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
        <h3>Supplier Summary</h3>
        <table class="summary-table" cellpadding="0" cellspacing="0">
            <tr>
                <td>
                    <div class="summary-item">
                        <div class="summary-label">Total Suppliers</div>
                        <div class="summary-value">{{ number_format($summary['total_suppliers'] ?? 0) }}</div>
                    </div>
                </td>
                <td>
                    <div class="summary-item">
                        <div class="summary-label">Active Suppliers</div>
                        <div class="summary-value">{{ number_format($summary['active_suppliers'] ?? 0) }}</div>
                    </div>
                </td>
                <td>
                    <div class="summary-item">
                        <div class="summary-label">Inactive Suppliers</div>
                        <div class="summary-value">{{ number_format($summary['inactive_suppliers'] ?? 0) }}</div>
                    </div>
                </td>
                <td>
                    <div class="summary-item">
                        <div class="summary-label">Total Purchases</div>
                        <div class="summary-value">{{ number_format($summary['total_purchases'] ?? 0) }}</div>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="summary-item">
                        <div class="summary-label">Total Spent</div>
                        <div class="summary-value">${{ number_format($summary['total_spent'] ?? 0, 2) }}</div>
                    </div>
                </td>
                <td>
                    <div class="summary-item">
                        <div class="summary-label">Average Purchase</div>
                        <div class="summary-value">${{ number_format($summary['average_purchase'] ?? 0, 2) }}</div>
                    </div>
                </td>
                <td>
                    <div class="summary-item">
                        <div class="summary-label">Pending Purchases</div>
                        <div class="summary-value">${{ number_format($summary['pending_purchases'] ?? 0, 2) }}</div>
                    </div>
                </td>
                <td>
                    <div class="summary-item">
                        <div class="summary-label">Avg per Supplier</div>
                        <div class="summary-value">
                            ${{ number_format(($summary['total_spent'] ?? 0) / max(($summary['active_suppliers'] ?? 1), 1), 2) }}
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Top Suppliers -->
    @if(!empty($details['top_suppliers']) && count($details['top_suppliers']) > 0)
    <div class="summary">
        <h3>Top Suppliers by Purchase Volume</h3>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Supplier</th>
                    <th>Contact Person</th>
                    <th>Phone</th>
                    <th class="text-right">Purchases</th>
                    <th class="text-right">Total Spent</th>
                    <th class="text-right">Avg per Purchase</th>
                </tr>
            </thead>
            <tbody>
                @foreach($details['top_suppliers'] as $supplier)
                <tr>
                    <td><strong>{{ $supplier->name }}</strong></td>
                    <td>{{ $supplier->contact_person ?? '-' }}</td>
                    <td>{{ $supplier->phone ?? '-' }}</td>
                    <td class="text-right">{{ number_format($supplier->purchases_count ?? 0) }}</td>
                    <td class="text-right">${{ number_format($supplier->purchases_sum_total_amount ?? 0, 2) }}</td>
                    <td class="text-right">
                        ${{ number_format(($supplier->purchases_sum_total_amount ?? 0) / max(($supplier->purchases_count ?? 1), 1), 2) }}
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot style="font-weight: bold; background: #f9fafb;">
                <tr>
                    <td colspan="3">Total</td>
                    <td class="text-right">{{ number_format(collect($details['top_suppliers'])->sum('purchases_count')) }}</td>
                    <td class="text-right">${{ number_format(collect($details['top_suppliers'])->sum('purchases_sum_total_amount'), 2) }}</td>
                    <td class="text-right">${{ number_format(collect($details['top_suppliers'])->sum('purchases_sum_total_amount') / max(collect($details['top_suppliers'])->sum('purchases_count'), 1), 2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
    @endif

    <!-- Purchases by Supplier -->
    @if(!empty($details['by_supplier']) && count($details['by_supplier']) > 0)
    <div class="summary">
        <h3>Purchases by Supplier ({{ date('F d, Y', strtotime($startDate)) }} - {{ date('F d, Y', strtotime($endDate)) }})</h3>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Supplier</th>
                    <th class="text-right">Number of Purchases</th>
                    <th class="text-right">Total Spent</th>
                    <th class="text-right">Average per Purchase</th>
                    <th class="text-right">% of Total</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalSpent = collect($details['by_supplier'])->sum('total_spent');
                @endphp
                @foreach($details['by_supplier'] as $supplier)
                <tr>
                    <td><strong>{{ $supplier->supplier->name ?? 'Unknown' }}</strong></td>
                    <td class="text-right">{{ number_format($supplier->purchase_count) }}</td>
                    <td class="text-right">${{ number_format($supplier->total_spent, 2) }}</td>
                    <td class="text-right">${{ number_format($supplier->total_spent / $supplier->purchase_count, 2) }}</td>
                    <td class="text-right">{{ $totalSpent > 0 ? number_format(($supplier->total_spent / $totalSpent) * 100, 1) : 0 }}%</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot style="font-weight: bold; background: #f9fafb;">
                <tr>
                    <td>Total</td>
                    <td class="text-right">{{ number_format(collect($details['by_supplier'])->sum('purchase_count')) }}</td>
                    <td class="text-right">${{ number_format($totalSpent, 2) }}</td>
                    <td class="text-right">${{ number_format($totalSpent / max(collect($details['by_supplier'])->sum('purchase_count'), 1), 2) }}</td>
                    <td class="text-right">100%</td>
                </tr>
            </tfoot>
        </table>
    </div>
    @endif

    <!-- Supplier Status Summary -->
    <div class="summary">
        <h3>Supplier Status</h3>
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
                    <td><span style="color: #10b981;">●</span> Active Suppliers</td>
                    <td class="text-right">{{ number_format($summary['active_suppliers'] ?? 0) }}</td>
                    <td class="text-right">
                        {{ (($summary['total_suppliers'] ?? 0) > 0) ? number_format((($summary['active_suppliers'] ?? 0) / ($summary['total_suppliers'] ?? 1)) * 100, 1) : 0 }}%
                    </td>
                </tr>
                <tr>
                    <td><span style="color: #6b7280;">●</span> Inactive Suppliers</td>
                    <td class="text-right">{{ number_format($summary['inactive_suppliers'] ?? 0) }}</td>
                    <td class="text-right">
                        {{ (($summary['total_suppliers'] ?? 0) > 0) ? number_format((($summary['inactive_suppliers'] ?? 0) / ($summary['total_suppliers'] ?? 1)) * 100, 1) : 0 }}%
                    </td>
                </tr>
            </tbody>
            <tfoot style="font-weight: bold; background: #f9fafb;">
                <tr>
                    <td>Total</td>
                    <td class="text-right">{{ number_format($summary['total_suppliers'] ?? 0) }}</td>
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