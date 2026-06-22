<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchases Report</title>
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
            border-bottom: 2px solid #f59e0b;
            padding-bottom: 20px;
        }
        .header h1 {
            color: #f59e0b;
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
            color: #f59e0b;
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
        .text-success {
            color: #10b981;
        }
        .text-danger {
            color: #ef4444;
        }
        .text-warning {
            color: #f59e0b;
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
        <h2>Purchases Report</h2>
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
        <h3>Summary</h3>
        <table class="summary-table" cellpadding="0" cellspacing="0">
            <tr>
                <td>
                    <div class="summary-item">
                        <div class="summary-label">Total Purchases</div>
                        <div class="summary-value">{{ number_format($summary['total_purchases'] ?? 0) }}</div>
                    </div>
                </td>
                <td>
                    <div class="summary-item">
                        <div class="summary-label">Total Cost</div>
                        <div class="summary-value text-success">${{ number_format($summary['total_cost'] ?? 0, 2) }}</div>
                    </div>
                </td>
                <td>
                    <div class="summary-item">
                        <div class="summary-label">Total Tax</div>
                        <div class="summary-value">${{ number_format($summary['total_tax'] ?? 0, 2) }}</div>
                    </div>
                </td>
                <td>
                    <div class="summary-item">
                        <div class="summary-label">Total Discount</div>
                        <div class="summary-value">${{ number_format($summary['total_discount'] ?? 0, 2) }}</div>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="summary-item">
                        <div class="summary-label">Average Purchase</div>
                        <div class="summary-value">${{ number_format($summary['average_purchase'] ?? 0, 2) }}</div>
                    </div>
                </td>
                <td>
                    <div class="summary-item">
                        <div class="summary-label">Pending Purchases</div>
                        <div class="summary-value text-warning">{{ number_format($summary['pending_purchases'] ?? 0) }}</div>
                    </div>
                </td>
                <td>
                    <div class="summary-item">
                        <div class="summary-label">Received Purchases</div>
                        <div class="summary-value text-success">{{ number_format($summary['received_purchases'] ?? 0) }}</div>
                    </div>
                </td>
                <td>
                    <div class="summary-item">
                        <div class="summary-label">Net Spend</div>
                        <div class="summary-value">${{ number_format(($summary['total_cost'] ?? 0) - ($summary['total_discount'] ?? 0) + ($summary['total_tax'] ?? 0), 2) }}</div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Top Products Purchased -->
    @if(!empty($details['top_products']) && count($details['top_products']) > 0)
    <div class="summary">
        <h3>Top Purchased Products</h3>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>SKU</th>
                    <th class="text-right">Quantity</th>
                    <th class="text-right">Unit Cost</th>
                    <th class="text-right">Total Cost</th>
                </tr>
            </thead>
            <tbody>
                @foreach($details['top_products'] as $product)
                <tr>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->sku }}</td>
                    <td class="text-right">{{ number_format($product->total_quantity) }}</td>
                    <td class="text-right">${{ number_format($product->total_cost / $product->total_quantity, 2) }}</td>
                    <td class="text-right">${{ number_format($product->total_cost, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot style="font-weight: bold; background: #f9fafb;">
                <tr>
                    <td colspan="2">Total</td>
                    <td class="text-right">{{ number_format(collect($details['top_products'])->sum('total_quantity')) }}</td>
                    <td class="text-right"></td>
                    <td class="text-right">${{ number_format(collect($details['top_products'])->sum('total_cost'), 2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
    @endif

    <!-- Purchases by Supplier -->
    @if(!empty($details['by_supplier']) && count($details['by_supplier']) > 0)
    <div class="summary">
        <h3>Purchases by Supplier</h3>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Supplier</th>
                    <th class="text-right">Number of Purchases</th>
                    <th class="text-right">Total Cost</th>
                    <th class="text-right">Average per Purchase</th>
                </tr>
            </thead>
            <tbody>
                @foreach($details['by_supplier'] as $supplier)
                <tr>
                    <td>{{ $supplier->supplier->name ?? 'Unknown' }}</td>
                    <td class="text-right">{{ number_format($supplier->purchase_count) }}</td>
                    <td class="text-right">${{ number_format($supplier->total_cost, 2) }}</td>
                    <td class="text-right">${{ number_format($supplier->total_cost / $supplier->purchase_count, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot style="font-weight: bold; background: #f9fafb;">
                <tr>
                    <td>Total</td>
                    <td class="text-right">{{ number_format(collect($details['by_supplier'])->sum('purchase_count')) }}</td>
                    <td class="text-right">${{ number_format(collect($details['by_supplier'])->sum('total_cost'), 2) }}</td>
                    <td class="text-right"></td>
                </tr>
            </tfoot>
        </table>
    </div>
    @endif

    <div class="footer">
        <p>Generated on {{ date(dateFormat().' H:i:s') }}</p>
    </div>
</body>
</html>