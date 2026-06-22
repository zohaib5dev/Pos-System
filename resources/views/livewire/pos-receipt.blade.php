<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt - Order #{{ $order->order_number }}</title>
    <style>
        @page {
            margin: 0;
            size: auto;
        }
        
        body {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            width: 300px; /* 80mm width for thermal printer */
            margin: 0 auto;
            padding: 10px;
            background: white;
        }
        
        .receipt {
            max-width: 100%;
        }
        
        .header {
            text-align: center;
            margin-bottom: 10px;
        }
        
        .business-name {
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .business-info {
            font-size: 10px;
            line-height: 1.2;
        }
        
        .receipt-title {
            font-size: 14px;
            font-weight: bold;
            text-align: center;
            margin: 10px 0;
            text-transform: uppercase;
            border-top: 1px dashed #000;
            border-bottom: 1px dashed #000;
            padding: 5px 0;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            font-size: 10px;
            margin: 2px 0;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
            font-size: 10px;
        }
        
        .items-table th {
            text-align: left;
            border-bottom: 1px solid #000;
            padding: 3px 0;
        }
        
        .items-table td {
            padding: 3px 0;
        }
        
        .item-name {
            max-width: 150px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        
        .text-right {
            text-align: right;
        }
        
        .summary-table {
            width: 100%;
            margin: 10px 0;
            border-top: 1px solid #000;
            padding-top: 5px;
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            margin: 2px 0;
        }
        
        .total-row {
            font-size: 14px;
            font-weight: bold;
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            padding: 5px 0;
            margin: 5px 0;
        }
        
        .payment-info {
            margin: 10px 0;
            font-size: 10px;
            border-top: 1px dashed #000;
            border-bottom: 1px dashed #000;
            padding: 5px 0;
        }
        
        .footer {
            text-align: center;
            margin-top: 15px;
            font-size: 10px;
        }
        
        .qr-code {
            text-align: center;
            margin: 10px 0;
        }
        
        .divider {
            border-top: 1px dashed #000;
            margin: 10px 0;
        }
        
        @media print {
            .no-print {
                display: none;
            }
            body {
                width: 300px;
                margin: 0;
                padding: 5px;
            }
        }
        
        .print-btn {
            font-family: 'Arial', sans-serif;
            background: #4f46e5;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            margin-bottom: 20px;
            width: 100%;
        }
        
        .print-btn:hover {
            background: #4338ca;
        }
        
        .customer-info {
            background: #f3f4f6;
            padding: 5px;
            margin: 5px 0;
            font-size: 10px;
        }
    </style>
</head>
<body>
    <div class="receipt">
        <!-- Print Button (only visible on screen) -->
        <button class="print-btn no-print" onclick="window.print()">🖨️ Print Receipt</button>
        
        <!-- Header -->
        <div class="header">
            <div class="business-name">{{ $business_name ?? 'Your Store' }}</div>
            <div class="business-info">{{ $business_address ?? '123 Main St, City' }}</div>
            <div class="business-info">{{ $business_phone ?? '(123) 456-7890' }}</div>
            <div class="business-info">{{ $business_email ?? 'info@store.com' }}</div>
        </div>
        
        <!-- Receipt Title -->
        <div class="receipt-title">SALES RECEIPT</div>
        
        <!-- Receipt Info -->
        <div class="info-row">
            <span>Receipt #:</span>
            <span><strong>{{ $order->order_number }}</strong></span>
        </div>
        <div class="info-row">
            <span>Date:</span>
            <span>{{ $order->created_at }}</span>
        </div>
        <div class="info-row">
            <span>Cashier:</span>
            <span>{{ $order->creator->name ?? 'System' }}</span>
        </div>
        
        <!-- Customer Info -->
        @if($order->customer)
        <div class="customer-info">
            <div class="info-row">
                <span>Customer:</span>
                <span><strong>{{ $order->customer->name }}</strong></span>
            </div>
            @if($order->customer->phone)
            <div class="info-row">
                <span>Phone:</span>
                <span>{{ $order->customer->phone }}</span>
            </div>
            @endif
        </div>
        @else
        <div class="customer-info">
            <div class="info-row">
                <span>Customer:</span>
                <span>Walk-in Customer</span>
            </div>
        </div>
        @endif
        
        <div class="divider"></div>
        
        <!-- Items -->
        <table class="items-table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th class="text-right">Qty</th>
                    <th class="text-right">Price</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                <tr>
                    <td class="item-name">{{ $item->product_name }}</td>
                    <td class="text-right">{{ $item->quantity }}</td>
                    <td class="text-right">${{ number_format($item->unit_price, 2) }}</td>
                    <td class="text-right">${{ number_format($item->total, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        <div class="divider"></div>
        
        <!-- Summary -->
        <div class="summary-table">
            <div class="summary-row">
                <span>Subtotal:</span>
                <span>${{ number_format($order->subtotal, 2) }}</span>
            </div>
            
            @if($order->discount_amount > 0)
            <div class="summary-row">
                <span>Discount:</span>
                <span>-${{ number_format($order->discount_amount, 2) }}</span>
            </div>
            @endif
            
            @if($order->tax_amount > 0)
            <div class="summary-row">
                <span>Tax ({{ $tax_rate ?? 0 }}%):</span>
                <span>${{ number_format($order->tax_amount, 2) }}</span>
            </div>
            @endif
            
            <div class="total-row">
                <span>TOTAL:</span>
                <span>${{ number_format($order->total_amount, 2) }}</span>
            </div>
        </div>
        
        <!-- Payment Info -->
        <div class="payment-info">
            <div class="info-row">
                <span>Payment Method:</span>
                <span>{{ ucfirst(str_replace('-', ' ', $order->payments->first()->method->name ?? 'Cash')) }}</span>
            </div>
            <div class="info-row">
                <span>Amount Paid:</span>
                <span>${{ number_format($order->paid_amount, 2) }}</span>
            </div>
            <div class="info-row">
                <span>Change:</span>
                <span>${{ number_format($order->change_amount ?? 0, 2) }}</span>
            </div>
            <div class="info-row">
                <span>Payment Status:</span>
                <span>{{ ucfirst($order->payment_status) }}</span>
            </div>
        </div>
        
        @if($order->notes)
        <div class="info-row">
            <span>Notes:</span>
            <span>{{ $order->notes }}</span>
        </div>
        @endif
        
        <div class="divider"></div>
        
        <!-- Footer -->
        <div class="footer">
            <div>Thank you for your purchase!</div>
            <div>Please come again</div>
            <div style="margin-top: 5px;">{{ $receipt_footer ?? 'Returns accepted within 7 days with receipt' }}</div>
            
           
            
            <div style="margin-top: 10px;">{{ date('Y-m-d H:i:s') }}</div>
            <div>------------------------</div>
        </div>
    </div>
    
    <script>
        // Auto print when page loads (optional)
        document.addEventListener('DOMContentLoaded', function() {
            // Uncomment the line below if you want to auto-print
            // window.print();
        });
    </script>
</body>
</html>