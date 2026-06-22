@if($showReceiptModal && $lastOrder)
<div class="modal fade show" id="receiptModal" style="display: block; background: rgba(0,0,0,0.6); backdrop-filter: blur(4px);" tabindex="-1" role="dialog" aria-modal="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg">

            <div class="modal-header border-0 pb-0">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-danger-soft p-2" style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-trash-alt text-danger fa-lg"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0">
                            Order Receipt
                        </h5>
                        <p class="text-muted small mb-0">
                            Order #{{ $lastOrder->order_number }}
                        </p>
                    </div>
                    <button type="button" class="btn btn-primary" wire:click="printReceipt({{ $lastOrder->id }})">
                        <i class="fas fa-print mr-1"></i> Print
                    </button>
                </div>
                <button type="button" class="btn-close" wire:click="$set('showReceiptModal', false)" aria-label="Close"></button>
            </div>


            <div class="modal-body p-3">
                <div id="receipt-content-{{ $lastOrder->id }}" style="font-family: 'Courier New', monospace; font-size: 12px; color: #000; background: white; border: 1px dashed #000; margin: 2px; padding:10px">

                    <div style="text-align: center; margin-bottom: 5px; padding-bottom: 10px; border-bottom: 2px dashed #000;">
                        <div style="font-weight: bold; font-size: 16px;">
                            <img src="{{getLogo()}}" alt="" style="max-width: 150px; max-height: 60px;">
                        </div>
                        <div style="font-weight: bold; font-size: 16px;">{{ $settings->business_name ?? 'My POS System' }}</div>
                        @if($settings && $settings->business_address)
                        <div style="font-size: 10px;">{{ $settings->business_address }}</div>
                        @endif
                        <div style="font-size: 10px;">{{ $settings->business_phone ?? '+1234567890' }}</div>
                        <div style="font-size: 10px;">{{ $settings->business_email ?? 'test@test.com' }}</div>
                    </div>

                    <div style="text-align: center; margin-bottom: 15px; padding-bottom: 1px; border-bottom: 2px dashed #000;">

                        <div style="font-weight: bold; font-size: 16px;">SALES RECEIPT</div>
                    </div>

                    <div style="display: flex; gap: 15px; margin-bottom: 20px;">
                        <div style="flex: 1; border: 1px dashed #000; padding: 8px;">
                            <div style="font-weight: bold; margin-bottom: 8px; border-bottom: 1px dashed #000; padding-bottom: 5px;">
                                <span>👤 CUSTOMER</span>
                            </div>

                            @if($lastOrder->customer)
                            <div style="font-size: 11px;">
                                <div><strong>{{ $lastOrder->customer->name }}</strong></div>
                                @if($lastOrder->customer->phone)
                                <div style="font-size: 10px; margin-top: 3px;">📞 {{ $lastOrder->customer->phone }}</div>
                                @endif
                                @if($lastOrder->customer->email)
                                <div style="font-size: 9px; margin-top: 2px;">✉️ {{ $lastOrder->customer->email }}</div>
                                @endif
                                @if($lastOrder->customer->address)
                                <div style="font-size: 9px; margin-top: 2px;">🏠 {{ $lastOrder->customer->address }}</div>
                                @endif
                                @if($lastOrder->customer->created_at)
                                <div style="font-size: 8px; margin-top: 5px; color: #666; border-top: 1px dotted #ccc; padding-top: 3px;">
                                    Since: {{ $lastOrder->customer->created_at }}
                                </div>
                                @endif
                            </div>
                            @else
                            <div style="text-align: center; padding: 10px; font-style: italic; font-size: 11px;">
                                Walk-in Customer
                            </div>
                            @endif
                        </div>

                        <div style="flex: 1; border: 1px dashed #000; padding: 8px;">
                            <div style="font-weight: bold; margin-bottom: 8px; border-bottom: 1px dashed #000; padding-bottom: 5px;">
                                <span>📋 ORDER INFO</span>
                            </div>

                            <table style="width: 100%; font-size: 11px;">
                                <tr>
                                    <td style="padding: 2px 0;">Order #:</td>
                                    <td style="font-weight: bold; text-align: right;">{{ $lastOrder->order_number }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 2px 0;">Date:</td>
                                    <td style="text-align: right;">{{ $lastOrder->created_at }}</td>
                                </tr>

                                <tr>
                                    <td style="padding: 2px 0;">Status:</td>
                                    <td style="text-align: right;">
                                        <span style="border: 1px solid #000; padding: 2px 5px; font-size: 9px; font-weight: bold;">
                                            {{ strtoupper($lastOrder->payment_status) }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div style="margin-bottom: 15px;">
                        <div style="font-weight: bold; margin-bottom: 5px;">ITEMS</div>
                        <table style="width: 100%; border-collapse: collapse; font-size: 10px;">
                            <thead>
                                <tr style="border-bottom: 2px solid #000;">
                                    <th style="text-align: left; padding: 4px;">Item</th>
                                    <th style="text-align: center; padding: 4px;">Qty</th>
                                    <th style="text-align: right; padding: 4px;">Price</th>
                                    <th style="text-align: right; padding: 4px;">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($lastOrder->items as $item)
                                <tr style="border-bottom: 1px dashed #000;">
                                    <td style="padding: 4px;">
                                        {{ $item->product_name }}
                                        @if($item->product_sku)
                                        <div style="font-size: 8px;">SKU: {{ $item->product_sku }}</div>
                                        @endif
                                    </td>
                                    <td style="text-align: center; padding: 4px;">{{ $item->quantity }}</td>
                                    <td style="text-align: right; padding: 4px;">{{ $settings->currency_symbol ?? '$' }}{{ number_format($item->unit_price, 2) }}</td>
                                    <td style="text-align: right; padding: 4px;">{{ $settings->currency_symbol ?? '$' }}{{ number_format($item->total, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div style="margin-bottom: 15px;">
                        <table style="width: 100%; font-size: 11px;">
                            <tr>
                                <td style="padding: 2px 0;">Subtotal:</td>
                                <td style="text-align: right; padding: 2px 0;">{{ $settings->currency_symbol ?? '$' }}{{ number_format($lastOrder->subtotal, 2) }}</td>
                            </tr>

                            @if($lastOrder->discount_amount > 0)
                            <tr>
                                <td style="padding: 2px 0;">Discount:</td>
                                <td style="text-align: right; padding: 2px 0; color: #28a745;">-{{ $settings->currency_symbol ?? '$' }}{{ number_format($lastOrder->discount_amount, 2) }}</td>
                            </tr>
                            @endif

                            @if($lastOrder->tax_amount > 0)
                            <tr>
                                <td style="padding: 2px 0;">Tax ({{ $lastOrder->tax_rate ?? 0 }}%):</td>
                                <td style="text-align: right; padding: 2px 0;">+{{ $settings->currency_symbol ?? '$' }}{{ number_format($lastOrder->tax_amount, 2) }}</td>
                            </tr>
                            @endif

                            <tr style="border-top: 2px solid #000; font-weight: bold;">
                                <td style="padding-top: 5px;">TOTAL:</td>
                                <td style="text-align: right; padding-top: 5px;">{{ $settings->currency_symbol ?? '$' }}{{ number_format($lastOrder->total_amount, 2) }}</td>
                            </tr>
                        </table>
                    </div>

                    <div style="margin-bottom: 15px; border-top: 1px dashed #000; padding-top: 10px;">
                        <div style="font-weight: bold; margin-bottom: 5px;">PAYMENT</div>
                        <table style="width: 100%; font-size: 11px;">
                            <tr>
                                <td style="padding: 2px 0;">Method:</td>
                                <td style="text-align: right; padding: 2px 0;">{{ ucfirst(str_replace('-', ' ', $lastOrder->payments->first()->method->name ?? 'Cash')) }}</td>
                            </tr>
                            <tr>
                                <td style="padding: 2px 0;">Paid:</td>
                                <td style="text-align: right; padding: 2px 0; font-weight: bold;">{{ $settings->currency_symbol ?? '$' }}{{ number_format($lastOrder->paid_amount, 2) }}</td>
                            </tr>
                            @if($lastOrder->due_amount > 0)
                            <tr>
                                <td style="padding: 2px 0;">Due:</td>
                                <td style="text-align: right; padding: 2px 0; color: #dc3545;">{{ $settings->currency_symbol ?? '$' }}{{ number_format($lastOrder->due_amount, 2) }}</td>
                            </tr>
                            @endif
                            @if($lastOrder->change_amount > 0)
                            <tr>
                                <td style="padding: 2px 0;">Change:</td>
                                <td style="text-align: right; padding: 2px 0;">{{ $settings->currency_symbol ?? '$' }}{{ number_format($lastOrder->change_amount, 2) }}</td>
                            </tr>
                            @endif
                        </table>
                    </div>

                    <div style="text-align: center; padding-top: 10px; border-top: 2px dashed #000; font-size: 10px;">
                        <div>{{ $settings->receipt_footer }}</div>
                        <div class="receipt-footer-time">{{ now()->format(dateFormat().' h:i:s A') }}</div>

                    </div>
                </div>
            </div>

     
        </div>
    </div>
</div>
@endif

<style>
    .modal.show {
        display: block;
        background: rgba(0, 0, 0, 0.5);
        padding-right: 0 !important;
    }

    .modal-dialog-centered {
        display: flex;
        align-items: center;
        min-height: calc(100% - 3.5rem);
    }

    .border-dashed {
        border-style: dashed !important;
    }


    @media print {

        .modal-header,
        .modal-footer {
            display: none !important;
        }

        .modal-content {
            border: none !important;
            box-shadow: none !important;
        }

        .modal-body {
            padding: 0 !important;
        }
    }
</style>

<script>
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('print-receipt', (data) => {
            const orderId = data[0] || data.orderId;

            const receiptElement = document.getElementById(`receipt-content-${orderId}`);

            if (!receiptElement) {
                console.error('Receipt element not found for order:', orderId);
                return;
            }

            const receiptContent = receiptElement.innerHTML;

            const printWindow = window.open('', '_blank');
            printWindow.document.write(`
            <html>
                <head>
                    <title>Receipt</title>
                    <style>
                        /* Hide everything by default */
                        html, body {
                            margin: 0;
                            padding: 0;
                            background: white;
                        }
                        
                        /* Only show the receipt content */
                        body > * {
                            display: none;
                        }
                        
                        #receipt-container {
                            display: block !important;
                            font-family: 'Courier New', monospace;
                            font-size: 12px;
                            line-height: 1.4;
                            color: #000;
                            max-width: 300px;
                            margin: 0 auto;
                            padding: 10px;
                        }
                        
                        /* Receipt specific styles */
                        .border-dashed {
                            border-style: dashed !important;
                            border-color: #000 !important;
                        }
                        
                        .border-bottom {
                            border-bottom: 1px solid #000 !important;
                        }
                        
                        .border-top {
                            border-top: 1px solid #000 !important;
                        }
                        
                        .bg-light, .card, .card-header {
                            background: white !important;
                            border: none !important;
                        }
                        
                        .card {
                            margin-bottom: 10px !important;
                        }
                        
                        .table {
                            width: 100%;
                            border-collapse: collapse;
                        }
                        
                        .table-bordered th,
                        .table-bordered td {
                            border: 1px solid #000 !important;
                            padding: 4px;
                        }
                        
                        .text-muted {
                            color: #000 !important;
                        }
                        
                        .badge {
                            border: 1px solid #000 !important;
                            background: white !important;
                            color: #000 !important;
                            padding: 2px 8px;
                        }
                        
                        /* Remove any background colors */
                        * {
                            background: white !important;
                            background-color: white !important;
                            box-shadow: none !important;
                            text-shadow: none !important;
                        }
                        
                        /* Print specific */
                        @media print {
                            body {
                                background: white;
                            }
                            #receipt-container {
                                padding: 0;
                            }
                        }
                    </style>
                </head>
                <body>
                    <div id="receipt-container">
                        ${receiptContent}
                    </div>
                    <script>
                        window.onload = function() {
                            setTimeout(function() {
                                window.print();
                                window.close();
                            }, 100);
                        };
                    <\/script>
                </body>
            </html>
        `);
            printWindow.document.close();
        });
    });
</script>