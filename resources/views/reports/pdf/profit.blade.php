<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profit & Loss Report</title>
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
            width: 50%;
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
        
        /* Profit & Loss Statement */
        .profit-loss {
            margin-top: 20px;
            margin-bottom: 30px;
            padding: 20px;
            background: #f0f9ff;
            border-radius: 5px;
            border: 1px solid #bae6fd;
        }
        .profit-loss h4 {
            color: #0369a1;
            margin: 0 0 15px 0;
            font-size: 14px;
            font-weight: bold;
        }
        .profit-loss-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #bae6fd;
        }
        .profit-loss-item:last-child {
            border-bottom: none;
        }
        .profit-loss-total {
            display: flex;
            justify-content: space-between;
            padding: 15px 0 0 0;
            font-weight: bold;
            font-size: 16px;
            border-top: 2px solid #0369a1;
            margin-top: 10px;
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
        .text-success {
            color: #10b981;
        }
        .text-danger {
            color: #ef4444;
        }
        .text-primary {
            color: #10b981;
        }
        .font-bold {
            font-weight: bold;
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
        <h2>Profit & Loss Report</h2>
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
        <h3>Financial Summary</h3>
        <table class="summary-table" cellpadding="0" cellspacing="0">
            <tr>
                <td>
                    <div class="summary-item">
                        <div class="summary-label">Total Revenue</div>
                        <div class="summary-value positive">${{ number_format($summary['total_revenue'] ?? 0, 2) }}</div>
                    </div>
                </td>
                <td>
                    <div class="summary-item">
                        <div class="summary-label">Cost of Goods Sold</div>
                        <div class="summary-value negative">${{ number_format($summary['cost_of_goods_sold'] ?? 0, 2) }}</div>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="summary-item">
                        <div class="summary-label">Gross Profit</div>
                        <div class="summary-value {{ ($summary['gross_profit'] ?? 0) >= 0 ? 'positive' : 'negative' }}">
                            ${{ number_format($summary['gross_profit'] ?? 0, 2) }}
                        </div>
                    </div>
                </td>
                <td>
                    <div class="summary-item">
                        <div class="summary-label">Gross Margin</div>
                        <div class="summary-value">{{ number_format($summary['gross_margin'] ?? 0, 1) }}%</div>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="summary-item">
                        <div class="summary-label">Total Expenses</div>
                        <div class="summary-value negative">${{ number_format($summary['expenses'] ?? 0, 2) }}</div>
                    </div>
                </td>
                <td>
                    <div class="summary-item">
                        <div class="summary-label">Total Discounts</div>
                        <div class="summary-value">${{ number_format($summary['total_discount'] ?? 0, 2) }}</div>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="summary-item">
                        <div class="summary-label">Net Profit</div>
                        <div class="summary-value {{ ($summary['net_profit'] ?? 0) >= 0 ? 'positive' : 'negative' }}">
                            ${{ number_format($summary['net_profit'] ?? 0, 2) }}
                        </div>
                    </div>
                </td>
                <td>
                    <div class="summary-item">
                        <div class="summary-label">Net Margin</div>
                        <div class="summary-value">{{ number_format($summary['net_margin'] ?? 0, 1) }}%</div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Profit & Loss Statement -->
    <div class="profit-loss">
        <h4>Profit & Loss Statement</h4>
        
        <div class="profit-loss-item">
            <span><strong>Revenue</strong></span>
            <span class="positive">${{ number_format($summary['total_revenue'] ?? 0, 2) }}</span>
        </div>
        
        <div class="profit-loss-item" style="margin-left: 20px;">
            <span>Total Sales</span>
            <span>${{ number_format($summary['total_revenue'] ?? 0, 2) }}</span>
        </div>
        
        <div class="profit-loss-item" style="margin-top: 10px;">
            <span><strong>Less: Cost of Goods Sold</strong></span>
            <span class="negative">(${{ number_format($summary['cost_of_goods_sold'] ?? 0, 2) }})</span>
        </div>
        
        <div class="profit-loss-item" style="margin-left: 20px;">
            <span>Product Costs</span>
            <span>${{ number_format($summary['cost_of_goods_sold'] ?? 0, 2) }}</span>
        </div>
        
        <div class="profit-loss-item" style="font-weight: bold; background: #e6f7e6; padding: 10px; border-radius: 3px;">
            <span>Gross Profit</span>
            <span class="{{ ($summary['gross_profit'] ?? 0) >= 0 ? 'positive' : 'negative' }}">
                ${{ number_format($summary['gross_profit'] ?? 0, 2) }}
                <span style="font-size: 11px; font-weight: normal; margin-left: 10px;">
                    ({{ number_format($summary['gross_margin'] ?? 0, 1) }}% margin)
                </span>
            </span>
        </div>
        
        <div class="profit-loss-item" style="margin-top: 10px;">
            <span><strong>Less: Operating Expenses</strong></span>
            <span class="negative">(${{ number_format($summary['expenses'] ?? 0, 2) }})</span>
        </div>
        
        <div class="profit-loss-item">
            <span><strong>Less: Discounts</strong></span>
            <span class="negative">(${{ number_format($summary['total_discount'] ?? 0, 2) }})</span>
        </div>
        
        <div class="profit-loss-item" style="margin-left: 20px;">
            <span>Other Expenses</span>
            <span>${{ number_format(($summary['expenses'] ?? 0) + ($summary['total_discount'] ?? 0), 2) }}</span>
        </div>
        
        <div class="profit-loss-total">
            <span>Net Profit / (Loss)</span>
            <span class="{{ ($summary['net_profit'] ?? 0) >= 0 ? 'positive' : 'negative' }} font-bold">
                ${{ number_format($summary['net_profit'] ?? 0, 2) }}
            </span>
        </div>
    </div>

    <!-- Profit by Product -->
    @if(!empty($details['by_product']) && count($details['by_product']) > 0)
    <div class="summary">
        <h3>Profit by Product</h3>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>SKU</th>
                    <th class="text-right">Qty Sold</th>
                    <th class="text-right">Revenue</th>
                    <th class="text-right">Cost</th>
                    <th class="text-right">Profit</th>
                    <th class="text-right">Margin</th>
                </tr>
            </thead>
            <tbody>
                @foreach($details['by_product'] as $product)
                @php
                    $margin = $product->revenue > 0 ? ($product->profit / $product->revenue) * 100 : 0;
                @endphp
                <tr>
                    <td><strong>{{ $product->name }}</strong></td>
                    <td>{{ $product->sku }}</td>
                    <td class="text-right">{{ number_format($product->quantity_sold) }}</td>
                    <td class="text-right">${{ number_format($product->revenue, 2) }}</td>
                    <td class="text-right">${{ number_format($product->cost, 2) }}</td>
                    <td class="text-right {{ $product->profit >= 0 ? 'text-success' : 'text-danger' }}">
                        ${{ number_format($product->profit, 2) }}
                    </td>
                    <td class="text-right">{{ number_format($margin, 1) }}%</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot style="font-weight: bold; background: #f9fafb;">
                <tr>
                    <td colspan="2">Total</td>
                    <td class="text-right">{{ number_format(collect($details['by_product'])->sum('quantity_sold')) }}</td>
                    <td class="text-right">${{ number_format(collect($details['by_product'])->sum('revenue'), 2) }}</td>
                    <td class="text-right">${{ number_format(collect($details['by_product'])->sum('cost'), 2) }}</td>
                    <td class="text-right {{ collect($details['by_product'])->sum('profit') >= 0 ? 'text-success' : 'text-danger' }}">
                        ${{ number_format(collect($details['by_product'])->sum('profit'), 2) }}
                    </td>
                    <td class="text-right">
                        {{ number_format((collect($details['by_product'])->sum('profit') / max(collect($details['by_product'])->sum('revenue'), 1)) * 100, 1) }}%
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
    @endif

    <!-- Profit by Category -->
    @if(!empty($details['by_category']) && count($details['by_category']) > 0)
    <div class="summary">
        <h3>Profit by Category</h3>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Category</th>
                    <th class="text-right">Revenue</th>
                    <th class="text-right">Cost</th>
                    <th class="text-right">Profit</th>
                    <th class="text-right">Margin</th>
                    <th class="text-right">% of Total</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalProfit = collect($details['by_category'])->sum('profit');
                @endphp
                @foreach($details['by_category'] as $category)
                @php
                    $margin = $category->revenue > 0 ? ($category->profit / $category->revenue) * 100 : 0;
                @endphp
                <tr>
                    <td><strong>{{ $category->name }}</strong></td>
                    <td class="text-right">${{ number_format($category->revenue, 2) }}</td>
                    <td class="text-right">${{ number_format($category->cost, 2) }}</td>
                    <td class="text-right {{ $category->profit >= 0 ? 'text-success' : 'text-danger' }}">
                        ${{ number_format($category->profit, 2) }}
                    </td>
                    <td class="text-right">{{ number_format($margin, 1) }}%</td>
                    <td class="text-right">{{ $totalProfit > 0 ? number_format(($category->profit / $totalProfit) * 100, 1) : 0 }}%</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot style="font-weight: bold; background: #f9fafb;">
                <tr>
                    <td>Total</td>
                    <td class="text-right">${{ number_format(collect($details['by_category'])->sum('revenue'), 2) }}</td>
                    <td class="text-right">${{ number_format(collect($details['by_category'])->sum('cost'), 2) }}</td>
                    <td class="text-right {{ $totalProfit >= 0 ? 'text-success' : 'text-danger' }}">
                        ${{ number_format($totalProfit, 2) }}
                    </td>
                    <td class="text-right">{{ number_format(($totalProfit / max(collect($details['by_category'])->sum('revenue'), 1)) * 100, 1) }}%</td>
                    <td class="text-right">100%</td>
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