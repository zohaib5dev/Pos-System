<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Report</title>
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
        .summary h3.warning {
            color: #f59e0b;
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
        <h2>Inventory Report</h2>
        <div class="business-info">
            <p>{{ $business_address ?? '' }}</p>
            <p>Tel: {{ $business_phone ?? '' }} | Email: {{ $business_email ?? '' }}</p>
        </div>
    </div>

    <div class="period">
        As of {{ date('F d, Y') }}
    </div>

    <!-- Summary Section - Using Table Layout -->
    <div class="summary">
        <h3>Summary</h3>
        <table class="summary-table" cellpadding="0" cellspacing="0">
            <tr>
                <td>
                    <div class="summary-item">
                        <div class="summary-label">Total Products</div>
                        <div class="summary-value">{{ number_format($summary['total_products'] ?? 0) }}</div>
                    </div>
                </td>
                <td>
                    <div class="summary-item">
                        <div class="summary-label">Total Value (Cost)</div>
                        <div class="summary-value">${{ number_format($summary['total_value'] ?? 0, 2) }}</div>
                    </div>
                </td>
                <td>
                    <div class="summary-item">
                        <div class="summary-label">Retail Value</div>
                        <div class="summary-value">${{ number_format($summary['total_retail_value'] ?? 0, 2) }}</div>
                    </div>
                </td>
                <td>
                    <div class="summary-item">
                        <div class="summary-label">Potential Profit</div>
                        <div class="summary-value positive">${{ number_format($summary['potential_profit'] ?? 0, 2) }}</div>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="summary-item">
                        <div class="summary-label">In Stock Items</div>
                        <div class="summary-value">{{ number_format($summary['in_stock'] ?? 0) }}</div>
                    </div>
                </td>
                <td>
                    <div class="summary-item">
                        <div class="summary-label">Low Stock Items</div>
                        <div class="summary-value warning">{{ number_format($summary['low_stock_count'] ?? 0) }}</div>
                    </div>
                </td>
                <td>
                    <div class="summary-item">
                        <div class="summary-label">Out of Stock</div>
                        <div class="summary-value negative">{{ number_format($summary['out_of_stock'] ?? 0) }}</div>
                    </div>
                </td>
                <td>
                    <div class="summary-item">
                        <div class="summary-label">Avg Value/Product</div>
                        <div class="summary-value">${{ number_format(($summary['total_value'] ?? 0) / max(($summary['total_products'] ?? 1), 1), 2) }}</div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Low Stock Alert -->
    @if(!empty($details['low_stock']) && count($details['low_stock']) > 0)
    <div class="summary">
        <h3 class="warning">Low Stock Alert</h3>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>SKU</th>
                    <th>Category</th>
                    <th class="text-right">Current Stock</th>
                    <th class="text-right">Threshold</th>
                    <th class="text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($details['low_stock'] as $product)
                <tr>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->sku }}</td>
                    <td>{{ $product->category->name ?? 'N/A' }}</td>
                    <td class="text-right {{ $product->stock_quantity <= 0 ? 'text-danger' : 'text-warning' }}">
                        {{ number_format($product->stock_quantity) }}
                    </td>
                    <td class="text-right">{{ number_format($product->low_stock_threshold) }}</td>
                    <td class="text-center">
                        @if($product->stock_quantity <= 0)
                            <span class="badge badge-danger">Out of Stock</span>
                        @else
                            <span class="badge badge-warning">Low Stock</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot style="font-weight: bold; background: #f9fafb;">
                <tr>
                    <td colspan="3">Total Low Stock Items</td>
                    <td class="text-right">{{ number_format(collect($details['low_stock'])->sum('stock_quantity')) }}</td>
                    <td colspan="2" class="text-right">{{ count($details['low_stock']) }} products</td>
                </tr>
            </tfoot>
        </table>
    </div>
    @endif

    <!-- Stock by Category -->
    @if(!empty($details['by_category']) && count($details['by_category']) > 0)
    <div class="summary">
        <h3>Stock Value by Category</h3>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Category</th>
                    <th class="text-right">Products</th>
                    <th class="text-right">Total Stock</th>
                    <th class="text-right">Stock Value</th>
                    <th class="text-right">% of Total</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalValue = collect($details['by_category'])->sum('stock_value');
                @endphp
                @foreach($details['by_category'] as $category)
                <tr>
                    <td>{{ $category->name }}</td>
                    <td class="text-right">{{ number_format($category->product_count) }}</td>
                    <td class="text-right">{{ number_format($category->total_stock) }}</td>
                    <td class="text-right">${{ number_format($category->stock_value, 2) }}</td>
                    <td class="text-right">{{ $totalValue > 0 ? number_format(($category->stock_value / $totalValue) * 100, 1) : 0 }}%</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot style="font-weight: bold; background: #f9fafb;">
                <tr>
                    <td>Total</td>
                    <td class="text-right">{{ number_format(collect($details['by_category'])->sum('product_count')) }}</td>
                    <td class="text-right">{{ number_format(collect($details['by_category'])->sum('total_stock')) }}</td>
                    <td class="text-right">${{ number_format($totalValue, 2) }}</td>
                    <td class="text-right">100%</td>
                </tr>
            </tfoot>
        </table>
    </div>
    @endif

    <!-- Top Products by Value -->
    @if(!empty($details['top_products']) && count($details['top_products']) > 0)
    <div class="summary">
        <h3>Top Products by Stock Value</h3>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>SKU</th>
                    <th>Category</th>
                    <th class="text-right">Stock</th>
                    <th class="text-right">Unit Cost</th>
                    <th class="text-right">Unit Price</th>
                    <th class="text-right">Total Value</th>
                </tr>
            </thead>
            <tbody>
                @foreach($details['top_products'] as $product)
                <tr>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->sku }}</td>
                    <td>{{ $product->category->name ?? 'N/A' }}</td>
                    <td class="text-right">{{ number_format($product->stock_quantity) }}</td>
                    <td class="text-right">${{ number_format($product->purchase_price, 2) }}</td>
                    <td class="text-right">${{ number_format($product->selling_price, 2) }}</td>
                    <td class="text-right">${{ number_format($product->stock_quantity * $product->purchase_price, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot style="font-weight: bold; background: #f9fafb;">
                <tr>
                    <td colspan="4">Total Value of Top Products</td>
                    <td colspan="3" class="text-right">${{ number_format(collect($details['top_products'])->sum(function($p) { return $p->stock_quantity * $p->purchase_price; }), 2) }}</td>
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