<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Financial Report</title>
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
            border-bottom: 2px solid #3b82f6;
            padding-bottom: 20px;
        }
        .header h1 {
            color: #3b82f6;
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
            color: #3b82f6;
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
            border-top: 2px solid #3b82f6;
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
            color: #3b82f6;
        }
        .text-sm {
            font-size: 11px;
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
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $business_name ?? 'Business Name' }}</h1>
        <h2>Financial Report</h2>
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
                        <div class="summary-value positive">${{ number_format($summary['revenue'] ?? 0, 2) }}</div>
                    </div>
                </td>
                <td>
                    <div class="summary-item">
                        <div class="summary-label">Total Expenses</div>
                        <div class="summary-value negative">${{ number_format($summary['expenses'] ?? 0, 2) }}</div>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="summary-item">
                        <div class="summary-label">Total Purchases</div>
                        <div class="summary-value">${{ number_format($summary['purchases'] ?? 0, 2) }}</div>
                    </div>
                </td>
                <td>
                    <div class="summary-item">
                        <div class="summary-label">Total Refunds</div>
                        <div class="summary-value negative">${{ number_format($summary['refunds'] ?? 0, 2) }}</div>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="summary-item">
                        <div class="summary-label">Tax Collected</div>
                        <div class="summary-value">${{ number_format($summary['tax_collected'] ?? 0, 2) }}</div>
                    </div>
                </td>
                <td>
                    <div class="summary-item">
                        <div class="summary-label">Tax Paid</div>
                        <div class="summary-value">${{ number_format($summary['tax_paid'] ?? 0, 2) }}</div>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="summary-item">
                        <div class="summary-label">Tax Due</div>
                        <div class="summary-value {{ ($summary['tax_due'] ?? 0) > 0 ? 'negative' : 'positive' }}">
                            ${{ number_format($summary['tax_due'] ?? 0, 2) }}
                        </div>
                    </div>
                </td>
                <td>
                    <div class="summary-item">
                        <div class="summary-label">Net Income</div>
                        <div class="summary-value {{ ($summary['net_income'] ?? 0) >= 0 ? 'positive' : 'negative' }}">
                            ${{ number_format($summary['net_income'] ?? 0, 2) }}
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Profit & Loss Statement -->
    <div class="profit-loss">
        <h4>Profit & Loss Statement</h4>
        
        <div class="profit-loss-item">
            <span>Revenue</span>
            <span class="positive">${{ number_format($summary['revenue'] ?? 0, 2) }}</span>
        </div>
        
        <div class="profit-loss-item">
            <span style="margin-left: 20px;">Less: Cost of Goods Sold (Purchases)</span>
            <span class="negative">(${{ number_format($summary['purchases'] ?? 0, 2) }})</span>
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
            <span style="margin-left: 20px;">Less: Operating Expenses</span>
            <span class="negative">(${{ number_format($summary['expenses'] ?? 0, 2) }})</span>
        </div>
        
        <div class="profit-loss-item">
            <span style="margin-left: 20px;">Less: Refunds</span>
            <span class="negative">(${{ number_format($summary['refunds'] ?? 0, 2) }})</span>
        </div>
        
        <div class="profit-loss-total">
            <span>Net Profit / (Loss)</span>
            <span class="{{ ($summary['net_income'] ?? 0) >= 0 ? 'positive' : 'negative' }} font-bold">
                ${{ number_format($summary['net_income'] ?? 0, 2) }}
            </span>
        </div>
        
        <!-- Key Ratios -->
        <div style="margin-top: 20px; display: flex; justify-content: space-between; padding-top: 15px; border-top: 1px dashed #bae6fd;">
            <div>
                <span class="text-sm text-primary">Gross Margin:</span>
                <span class="font-bold">{{ number_format($summary['gross_margin'] ?? 0, 1) }}%</span>
            </div>
            <div>
                <span class="text-sm text-primary">Net Margin:</span>
                <span class="font-bold">{{ number_format($summary['net_margin'] ?? 0, 1) }}%</span>
            </div>
            <div>
                <span class="text-sm text-primary">Expense Ratio:</span>
                <span class="font-bold">{{ ($summary['revenue'] ?? 0) > 0 ? number_format((($summary['expenses'] ?? 0) / ($summary['revenue'] ?? 1)) * 100, 1) : 0 }}%</span>
            </div>
        </div>
    </div>

    <!-- Optional: Daily Breakdown if available -->
    @if(!empty($details['daily']) && count($details['daily']) > 0)
    <div class="summary" style="margin-top: 30px;">
        <h3>Daily Financial Summary</h3>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th class="text-right">Revenue</th>
                    <th class="text-right">Expenses</th>
                    <th class="text-right">Purchases</th>
                    <th class="text-right">Net</th>
                </tr>
            </thead>
            <tbody>
                @foreach($details['daily'] as $day)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($day->date)->format('M d, Y') }}</td>
                    <td class="text-right positive">${{ number_format($day->revenue ?? 0, 2) }}</td>
                    <td class="text-right negative">${{ number_format($day->expense ?? 0, 2) }}</td>
                    <td class="text-right">${{ number_format($day->purchase ?? 0, 2) }}</td>
                    <td class="text-right {{ (($day->revenue ?? 0) - ($day->expense ?? 0) - ($day->purchase ?? 0)) >= 0 ? 'positive' : 'negative' }}">
                        ${{ number_format(($day->revenue ?? 0) - ($day->expense ?? 0) - ($day->purchase ?? 0), 2) }}
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot style="font-weight: bold; background: #f9fafb;">
                <tr>
                    <td>Total</td>
                    <td class="text-right">${{ number_format(collect($details['daily'])->sum('revenue'), 2) }}</td>
                    <td class="text-right">${{ number_format(collect($details['daily'])->sum('expense'), 2) }}</td>
                    <td class="text-right">${{ number_format(collect($details['daily'])->sum('purchase'), 2) }}</td>
                    <td class="text-right {{ collect($details['daily'])->sum(function($d) { return ($d->revenue ?? 0) - ($d->expense ?? 0) - ($d->purchase ?? 0); }) >= 0 ? 'positive' : 'negative' }}">
                        ${{ number_format(collect($details['daily'])->sum(function($d) { return ($d->revenue ?? 0) - ($d->expense ?? 0) - ($d->purchase ?? 0); }), 2) }}
                    </td>
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