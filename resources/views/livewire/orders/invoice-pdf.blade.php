<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $order->order_number }}</title>
    <style>
        /* Reset and Base Styles */
        * {
            margin: 0;
            padding: 0;
        }
        
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            background: white;
            padding: 30px;
            color: #333;
            line-height: 1.5;
            font-size: 14px;
        }
        
        .invoice-container {
            max-width: 1100px;
            margin: 0 auto;
            background: white;
        }
        
        .card {
            background: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .card-body {
            padding: 30px;
        }
        
        /* Typography */
        h1, h2, h3, h4, h5, h6 {
            margin-bottom: 10px;
            font-weight: 500;
            line-height: 1.2;
        }
        
        h1.display-4 {
            font-size: 48px;
            font-weight: 300;
        }
        
        h4 {
            font-size: 24px;
        }
        
        h5 {
            font-size: 18px;
        }
        
        /* Text Colors */
        .text-primary {
            color: #007bff;
        }
        
        .text-muted {
            color: #6c757d;
        }
        
        .text-success {
            color: #28a745;
        }
        
        .text-danger {
            color: #dc3545;
        }
        
        .text-end {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-left {
            text-align: left;
        }
        
        /* Margins and Padding */
        .mb-0 { margin-bottom: 0; }
        .mb-1 { margin-bottom: 5px; }
        .mb-2 { margin-bottom: 10px; }
        .mb-3 { margin-bottom: 15px; }
        .mb-4 { margin-bottom: 20px; }
        .mt-2 { margin-top: 10px; }
        .mt-4 { margin-top: 20px; }
        .mt-5 { margin-top: 30px; }
        .p-5 { padding: 30px; }
        .pt-3 { padding-top: 15px; }
        
        /* Table-based Grid System - PDF Friendly */
        .grid-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        .grid-table td {
            padding: 0 15px;
            vertical-align: top;
        }
        
        .col-6 {
            width: 50%;
        }
        
        .col-12 {
            width: 100%;
        }
        
        /* Tables */
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        
        .table-bordered {
            border: 1px solid #dee2e6;
        }
        
        .table-bordered th,
        .table-bordered td {
            border: 1px solid #dee2e6;
            padding: 10px;
            vertical-align: top;
        }
        
        .table-sm th,
        .table-sm td {
            padding: 5px;
        }
        
        .table-borderless th,
        .table-borderless td {
            border: none;
        }
        
        .bg-light {
            background-color: #f8f9fa;
        }
        
        /* Border */
        .border-top {
            border-top: 2px solid #dee2e6;
        }
        
        /* Badges */
        .badge {
            display: inline-block;
            padding: 3px 6px;
            font-size: 12px;
            font-weight: 700;
            line-height: 1;
            text-align: center;
            white-space: nowrap;
            vertical-align: middle;
            border-radius: 4px;
        }
        
        .badge-success {
            color: #fff;
            background-color: #28a745;
        }
        
        .badge-warning {
            color: #212529;
            background-color: #ffc107;
        }
        
        .badge-info {
            color: #fff;
            background-color: #17a2b8;
        }
        
        .badge-secondary {
            color: #fff;
            background-color: #6c757d;
        }
        
        /* Address */
        address {
            margin-bottom: 15px;
            font-style: normal;
            line-height: 1.5;
        }
        
        /* Images */
        img {
            max-width: 100%;
            height: auto;
            vertical-align: middle;
        }
        
        /* Print Styles */
        @media print {
            body {
                padding: 0.25in;
                background: white;
            }
            
            .card {
                border: 1px solid #000;
                box-shadow: none;
            }
            
            .badge {
                border: 1px solid #000;
                color: #000 !important;
                background: transparent !important;
            }
            
            .text-primary, .text-success, .text-danger {
                color: #000 !important;
            }
            
            .bg-light {
                background-color: #f0f0f0 !important;
            }
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <div class="card">
            <div class="card-body p-5">
                <!-- Header - Using Tables -->
                <table class="grid-table mb-4" style="width: 100%;">
                    <tr>
                        <td class="col-6" style="width: 50%; padding-right: 20px;">
                            @if(getLogo() !== null)
                                <img src="{{ public_path('assets/img/'.$settings->business_logo) }}" class="mb-2" alt="Business Logo" style="max-height: 80px;">
                            @else
                                <h2 class="text-primary mb-2">{{ $settings->business_name ?? 'Your Business' }}</h2>
                            @endif
                            <address class="text-muted">
                                {{ $settings->business_address ?? '123 Business St, City, State 12345' }}<br>
                                {{ $settings->business_phone ?? '(123) 456-7890' }}<br>
                                {{ $settings->business_email ?? 'info@business.com' }}
                            </address>
                        </td>
                        <td class="col-6 text-end" style="width: 50%; text-align: right;">
                            <h1 class="display-4 mb-2">INVOICE</h1>
                            <div class="text-muted">
                                <p class="mb-1"><strong>Invoice #:</strong> {{ $order->invoice_number ?? $order->order_number }}</p>
                                <p class="mb-1"><strong>Date:</strong> {{ $order->created_at }}</p>
                                <p class="mb-0"><strong>Due Date:</strong> {{ $order->due_date ? $order->due_date->format(dateFormat()) : 'N/A' }}</p>
                            </div>
                        </td>
                    </tr>
                </table>

                <!-- Bill To & Order Info - Using Tables -->
                <table class="grid-table mb-4" style="width: 100%;">
                    <tr>
                        <td class="col-6" style="width: 50%; padding-right: 20px;">
                            <h5 class="mb-3">Bill To:</h5>
                            @if($order->customer)
                                <address class="text-muted">
                                    <strong>{{ $order->customer->name }}</strong><br>
                                    {{ $order->customer->address ?? '' }}<br>
                                    {{ $order->customer->phone ?? '' }}<br>
                                    {{ $order->customer->email ?? '' }}
                                </address>
                            @else
                                <p class="text-muted">Walk-in Customer</p>
                            @endif
                        </td>
                        <td class="col-6 text-end" style="width: 50%; text-align: right;">
                            <div class="text-muted">
                                <p class="mb-1">
                                    <strong>Payment Status:</strong> 
                                    <span class="badge 
                                        @if($order->payment_status === 'paid') badge-success
                                        @elseif($order->payment_status === 'partial') badge-warning
                                        @else badge-info
                                        @endif">
                                        {{ ucfirst($order->payment_status) }}
                                    </span>
                                </p>
                                <p class="mb-1">
                                    <strong>Order Status:</strong> 
                                    <span class="badge 
                                        @if($order->status === 'completed') badge-success
                                        @elseif($order->status === 'processing') badge-info
                                        @elseif($order->status === 'pending') badge-warning
                                        @else badge-secondary
                                        @endif">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </p>
                                <p class="mb-0"><strong>Due Date:</strong> {{ $order->due_date ? $order->due_date->format(dateFormat()) : 'N/A' }}</p>
                            </div>
                        </td>
                    </tr>
                </table>

                <!-- Items Table -->
                <table class="grid-table" style="width: 100%;">
                    <tr>
                        <td class="col-12">
                            <table class="table table-bordered table-hover">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="text-left" style="text-align: left;">Item</th>
                                        <th class="text-right" style="text-align: right;">Price</th>
                                        <th class="text-right" style="text-align: right;">Qty</th>
                                        <th class="text-right" style="text-align: right;">Discount</th>
                                        <th class="text-right" style="text-align: right;">Tax</th>
                                        <th class="text-right" style="text-align: right;">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($order->items as $item)
                                        <tr>
                                            <td class="text-left">
                                                <strong>{{ $item->product_name }}</strong>
                                                @if($item->variant)
                                                    <br><small class="text-muted">{{ $item->variant->name }}: {{ $item->variant->value }}</small>
                                                @endif
                                            </td>
                                            <td class="text-right">{{ $settings->currency_symbol ?? '$' }}{{ number_format($item->unit_price, 2) }}</td>
                                            <td class="text-right">{{ $item->quantity }}</td>
                                            <td class="text-right">{{ $settings->currency_symbol ?? '$' }}{{ number_format($item->discount_amount ?? 0, 2) }}</td>
                                            <td class="text-right">{{ $settings->currency_symbol ?? '$' }}{{ number_format($item->tax_amount ?? 0, 2) }}</td>
                                            <td class="text-right"><strong>{{ $settings->currency_symbol ?? '$' }}{{ number_format($item->total, 2) }}</strong></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </td>
                    </tr>
                </table>

                <!-- Summary -->
                <table class="grid-table" style="width: 100%;">
                    <tr>
                        <td class="col-6" style="width: 50%;"></td>
                        <td class="col-6" style="width: 50%;">
                            <table class="table table-sm table-borderless" style="width: 100%;">
                                <tr>
                                    <td style="width: 60%;"><strong>Subtotal:</strong></td>
                                    <td class="text-right">{{ $settings->currency_symbol ?? '$' }}{{ number_format($order->subtotal, 2) }}</td>
                                </tr>
                                @if($order->discount_amount > 0)
                                    <tr>
                                        <td><strong>Discount:</strong></td>
                                        <td class="text-right text-danger">-{{ $settings->currency_symbol ?? '$' }}{{ number_format($order->discount_amount, 2) }}</td>
                                    </tr>
                                @endif
                                @if($order->tax_amount > 0)
                                    <tr>
                                        <td><strong>Tax:</strong></td>
                                        <td class="text-right">{{ $settings->currency_symbol ?? '$' }}{{ number_format($order->tax_amount, 2) }}</td>
                                    </tr>
                                @endif
                                <tr>
                                    <td style="padding-top: 10px;"><h4>Total:</h4></td>
                                    <td class="text-right" style="padding-top: 10px;"><h4 class="text-primary">{{ $settings->currency_symbol ?? '$' }}{{ number_format($order->total_amount, 2) }}</h4></td>
                                </tr>
                                <tr>
                                    <td><strong>Paid:</strong></td>
                                    <td class="text-right text-success">{{ $settings->currency_symbol ?? '$' }}{{ number_format($order->paid_amount, 2) }}</td>
                                </tr>
                                @if($order->due_amount > 0)
                                    <tr>
                                        <td><strong>Due:</strong></td>
                                        <td class="text-right text-danger">{{ $settings->currency_symbol ?? '$' }}{{ number_format($order->due_amount, 2) }}</td>
                                    </tr>
                                @endif
                            </table>
                        </td>
                    </tr>
                </table>

                <!-- Payment History -->
                @if($order->payments->isNotEmpty())
                    <table class="grid-table mt-4" style="width: 100%;">
                        <tr>
                            <td class="col-12">
                                <h5 class="mb-3">Payment History</h5>
                                <table class="table table-sm table-bordered">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="text-left">Date</th>
                                            <th class="text-left">Payment #</th>
                                            <th class="text-left">Method</th>
                                            <th class="text-right">Amount</th>
                                            <th class="text-left">Reference</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($order->payments as $payment)
                                            <tr>
                                                <td>{{ $payment->created_at }}</td>
                                                <td>{{ $payment->payment_number }}</td>
                                                <td>{{ $payment->method->name ?? 'Cash' }}</td>
                                                <td class="text-right {{ $payment->amount < 0 ? 'text-danger' : 'text-success' }}">
                                                    {{ $settings->currency_symbol ?? '$' }}{{ number_format(abs($payment->amount), 2) }}
                                                    @if($payment->amount < 0)
                                                        <small>(Refund)</small>
                                                    @endif
                                                </td>
                                                <td>{{ $payment->reference_number ?? '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    </table>
                @endif

                <!-- Notes -->
                @if($order->notes)
                    <table class="grid-table mt-4" style="width: 100%;">
                        <tr>
                            <td class="col-12">
                                <h5 class="mb-2">Notes</h5>
                                <p class="text-muted">{{ $order->notes }}</p>
                            </td>
                        </tr>
                    </table>
                @endif

                <!-- Footer -->
                <table class="grid-table mt-5" style="width: 100%;">
                    <tr>
                        <td class="col-12 text-center">
                            <p class="text-muted border-top pt-3">
                                {{ $settings->receipt_footer ?? 'Thank you for your business!' }}
                            </p>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</body>
</html>