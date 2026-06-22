<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tax Report</title>
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

        .summary-value.warning {
            color: #f59e0b;
        }

        /* Tax Summary Box */
        .tax-summary {
            margin-top: 20px;
            margin-bottom: 30px;
            padding: 20px;
            background: #f0f9ff;
            border-radius: 5px;
            border: 1px solid #bae6fd;
        }

        .tax-summary h4 {
            color: #0369a1;
            margin: 0 0 15px 0;
            font-size: 14px;
            font-weight: bold;
        }

        .tax-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #bae6fd;
        }

        .tax-item:last-child {
            border-bottom: none;
        }

        .tax-total {
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

        .text-warning {
            color: #f59e0b;
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
        <h2>Tax Report</h2>
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
        <h3>Tax Summary</h3>
        <table class="summary-table" cellpadding="0" cellspacing="0">
            <tr>
                <td>
                    <div class="summary-item">
                        <div class="summary-label">Sales Tax Collected</div>
                        <div class="summary-value positive">${{ number_format($summary['sales_tax_collected'] ?? 0, 2) }}</div>
                    </div>
                </td>
                <td>
                    <div class="summary-item">
                        <div class="summary-label">Taxable Sales Transactions</div>
                        <div class="summary-value">{{ number_format($summary['sales_transactions'] ?? 0) }}</div>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="summary-item">
                        <div class="summary-label">Purchase Tax Paid</div>
                        <div class="summary-value negative">${{ number_format($summary['purchase_tax_paid'] ?? 0, 2) }}</div>
                    </div>
                </td>
                <td>
                    <div class="summary-item">
                        <div class="summary-label">Taxable Purchase Transactions</div>
                        <div class="summary-value">{{ number_format($summary['purchase_transactions'] ?? 0) }}</div>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="summary-item">
                        <div class="summary-label">Average Tax per Sale</div>
                        <div class="summary-value">
                            ${{ number_format(($summary['sales_tax_collected'] ?? 0) / max(($summary['sales_transactions'] ?? 1), 1), 2) }}
                        </div>
                    </div>
                </td>
                <td>
                    <div class="summary-item">
                        <div class="summary-label">Average Tax per Purchase</div>
                        <div class="summary-value">
                            ${{ number_format(($summary['purchase_tax_paid'] ?? 0) / max(($summary['purchase_transactions'] ?? 1), 1), 2) }}
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Tax Summary Box -->
    <div class="tax-summary">
        <h4>Tax Liability Summary</h4>

        <div class="tax-item">
            <span>Sales Tax Collected</span>
            <span class="text-success">${{ number_format($summary['sales_tax_collected'] ?? 0, 2) }}</span>
        </div>

        <div class="tax-item">
            <span>Number of Taxable Sales</span>
            <span>{{ number_format($summary['sales_transactions'] ?? 0) }}</span>
        </div>

        <div class="tax-item">
            <span style="margin-left: 20px;">Average Tax per Sale</span>
            <span>${{ number_format(($summary['sales_tax_collected'] ?? 0) / max(($summary['sales_transactions'] ?? 1), 1), 2) }}</span>
        </div>

        <div class="tax-item" style="margin-top: 10px;">
            <span>Purchase Tax Paid</span>
            <span class="text-danger">${{ number_format($summary['purchase_tax_paid'] ?? 0, 2) }}</span>
        </div>

        <div class="tax-item">
            <span>Number of Taxable Purchases</span>
            <span>{{ number_format($summary['purchase_transactions'] ?? 0) }}</span>
        </div>

        <div class="tax-item">
            <span style="margin-left: 20px;">Average Tax per Purchase</span>
            <span>${{ number_format(($summary['purchase_tax_paid'] ?? 0) / max(($summary['purchase_transactions'] ?? 1), 1), 2) }}</span>
        </div>

        <div class="tax-total">
            <span>Net Tax {{ ($summary['net_tax_payable'] ?? 0) > 0 ? 'Payable' : 'Receivable' }}</span>
            <span class="{{ ($summary['net_tax_payable'] ?? 0) > 0 ? 'text-danger' : 'text-success' }} font-bold">
                ${{ number_format(abs($summary['net_tax_payable'] ?? 0), 2) }}
            </span>
        </div>

        @php
        $totalTransactions = ($summary['sales_transactions'] ?? 0) + ($summary['purchase_transactions'] ?? 0);
        $effectiveRate = $totalTransactions > 0
        ? (abs($summary['net_tax_payable'] ?? 0) / $totalTransactions)
        : 0;
        @endphp
        <div style="margin-top: 15px; text-align: right; font-size: 11px; color: #6b7280;">
            Effective Tax per Transaction: ${{ number_format($effectiveRate, 2) }}
        </div>
    </div>

    <!-- Tax by Category -->
    @if(!empty($details['by_category']) && count($details['by_category']) > 0)
    <div class="summary">
        <h3>Tax by Product Category</h3>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Category</th>
                    <th class="text-right">Sales</th>
                    <th class="text-right">Tax Collected</th>
                    <th class="text-right">Effective Tax Rate</th>
                    <th class="text-right">% of Total Tax</th>
                </tr>
            </thead>
            <tbody>
                @php
                $totalTax = collect($details['by_category'])->sum('total_tax');
                @endphp
                @foreach($details['by_category'] as $category)
                @php
                $taxRate = $category->total_sales > 0 ? ($category->total_tax / $category->total_sales) * 100 : 0;
                @endphp
                <tr>
                    <td><strong>{{ $category->name }}</strong></td>
                    <td class="text-right">${{ number_format($category->total_sales, 2) }}</td>
                    <td class="text-right">${{ number_format($category->total_tax, 2) }}</td>
                    <td class="text-right">{{ number_format($taxRate, 2) }}%</td>
                    <td class="text-right">{{ $totalTax > 0 ? number_format(($category->total_tax / $totalTax) * 100, 1) : 0 }}%</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot style="font-weight: bold; background: #f9fafb;">
                <tr>
                    <td>Total</td>
                    <td class="text-right">${{ number_format(collect($details['by_category'])->sum('total_sales'), 2) }}</td>
                    <td class="text-right">${{ number_format($totalTax, 2) }}</td>
                    <td class="text-right">
                        {{ number_format(($totalTax / max(collect($details['by_category'])->sum('total_sales'), 1)) * 100, 2) }}%
                    </td>
                    <td class="text-right">100%</td>
                </tr>
            </tfoot>
        </table>
    </div>
    @endif

    <!-- Monthly Tax Trend (if available) -->
    @if(!empty($chartData['labels']) && count($chartData['labels']) > 0)
    <div class="summary">
        <h3>Monthly Tax Collection</h3>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Month</th>
                    <th class="text-right">Tax Collected</th>
                </tr>
            </thead>
            <tbody>
                @foreach($chartData['labels'] as $index => $month)
                <tr>
                    <td>{{ $month }}</td>
                    <td class="text-right">${{ number_format($chartData['values'][$index] ?? 0, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot style="font-weight: bold; background: #f9fafb;">
                <tr>
                    <td>Total</td>
                    <td class="text-right">${{ number_format(collect($chartData['values'])->sum(), 2) }}</td>
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