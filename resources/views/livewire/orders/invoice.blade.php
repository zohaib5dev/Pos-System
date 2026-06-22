<div>

    <section class="content-header no-print">
        <div class="">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('orders.index') }}">Orders</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('orders.show', $order->id) }}">Order #{{ $order->order_number }}</a></li>
                        <li class="breadcrumb-item active">Invoice</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <div class="row no-print">
        <div class="col-md-12">
            <div class="card card-default color-palette-box">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title">INVOICE #{{ $order->order_number }}</h3>
                        <div>
                            <button wire:click="downloadInvoice({{ $order->id }})" class="btn btn-primary btn-sm">
                                <i class="fas fa-download"></i> Download Invoice
                            </button>
                            <a href="{{ route('orders.show', $order->id) }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left"></i> Back to Order
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Invoice Content -->
    <div class="card" id="invoice-content">
        <div class="card-body p-5">
            <!-- Header -->
            <div class="row mb-4">
                <div class="col-sm-6">
                    @if(getLogo() !== null)
                    <img src="{{getLogo()}}" class="mb-2" alt="">
                    @else
                    <h2 class="text-primary mb-2">{{ $settings->business_name ?? 'Your Business' }}</h2>
                    @endif
                    <address class="text-muted">
                        {{$settings->business_address ?? '123 Business St, City, State 12345' }}<br>
                        {{$settings->business_phone ?? '(123) 456-7890' }}<br>
                        {{$settings->business_email ?? 'info@business.com' }}
                    </address>
                </div>
                <div class="col-sm-6 text-end">
                    <h1 class="display-4 mb-2">INVOICE</h1>
                    <div class="text-muted text-end">
                        <p class="mb-1"><strong>Invoice #:</strong> {{ $order->invoice_number ?? $order->order_number }}</p>
                        <p class="mb-1"><strong>Date:</strong> {{ $order->created_at }}</p>
                        <p class="mb-0"><strong>Due Date:</strong> {{ $order->due_date ? $order->due_date->format(dateFormat()) : 'N/A' }}</p>
                    </div>
                </div>
            </div>

            <!-- Bill To & Order Info -->
            <div class="row mb-4">
                <div class="col-sm-6">
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
                </div>
                <div class="col-sm-6 ">

                    <div class="text-muted text-end">
                        <p class="mb-1"><strong>Payment Status:</strong> <span class="badge 
                                @if($order->payment_status === 'paid') badge-success
                                @elseif($order->payment_status === 'partial') badge-warning
                                @else badge-info
                                @endif">
                                {{ ucfirst($order->payment_status) }}
                            </span></p>
                        <p class="mb-1"><strong>Order Status:</strong> <span class="badge 
                                @if($order->status === 'completed') badge-success
                                @elseif($order->status === 'processing') badge-info
                                @elseif($order->status === 'pending') badge-warning
                                @else badge-secondary
                                @endif">
                                {{ ucfirst($order->status) }}
                            </span></p>
                        <p class="mb-0"><strong>Due Date:</strong> {{ $order->due_date ? $order->due_date->format(dateFormat()) : 'N/A' }}</p>
                    </div>

                </div>
            </div>

            <!-- Items Table -->
            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="bg-light">
                                <tr>
                                    <th>Item</th>
                                    <th>SKU</th>
                                    <th class="text-right">Price</th>
                                    <th class="text-right">Quantity</th>
                                    <th class="text-right">Discount</th>
                                    <th class="text-right">Tax</th>
                                    <th class="text-right">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                <tr>
                                    <td>
                                        <strong>{{ $item->product_name }}</strong>
                                        @if($item->variant)
                                        <br><small class="text-muted">{{ $item->variant->name }}: {{ $item->variant->value }}</small>
                                        @endif
                                    </td>
                                    <td>{{ $item->sku ?? '-' }}</td>
                                    <td class="text-right">{{ amo($item->unit_price) }}</td>
                                    <td class="text-right">{{ $item->quantity }}</td>
                                    <td class="text-right">{{ amo($item->discount_amount ?? 0) }}</td>
                                    <td class="text-right">{{ amo($item->tax_amount ?? 0) }}</td>
                                    <td class="text-right"><strong>{{ amo($item->total) }}</strong></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Summary -->
            <div class="row">
                <div class="col-md-6 offset-md-6">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td width="60%"><strong>Subtotal:</strong></td>
                            <td class="text-right">{{ amo($order->subtotal) }}</td>
                        </tr>
                        @if($order->discount_amount > 0)
                        <tr>
                            <td><strong>Discount:</strong></td>
                            <td class="text-right text-danger">-{{ amo($order->discount_amount) }}</td>
                        </tr>
                        @endif
                        @if($order->tax_amount > 0)
                        <tr>
                            <td><strong>Tax:</strong></td>
                            <td class="text-right">{{ amo($order->tax_amount) }}</td>
                        </tr>
                        @endif
                        <tr class="border-top">
                            <td>
                                <h4>Total:</h4>
                            </td>
                            <td class="text-right">
                                <h4 class="text-primary">{{ amo($order->total_amount) }}</h4>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Paid:</strong></td>
                            <td class="text-right text-success">{{ amo($order->paid_amount) }}</td>
                        </tr>
                        @if($order->due_amount > 0)
                        <tr>
                            <td><strong>Due:</strong></td>
                            <td class="text-right text-danger">{{ amo($order->due_amount) }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>

            <!-- Payment History -->
            @if($order->payments->isNotEmpty())
            <div class="row mt-4">
                <div class="col-md-12">
                    <h5 class="mb-3">Payment History</h5>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead class="bg-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Payment #</th>
                                    <th>Method</th>
                                    <th class="text-right">Amount</th>
                                    <th>Reference</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->payments as $payment)
                                <tr>
                                    <td>{{ $payment->created_at }}</td>
                                    <td>{{ $payment->payment_number }}</td>
                                    <td>{{ $payment->method->name ?? 'Cash' }}</td>
                                    <td class="text-right {{ $payment->amount < 0 ? 'text-danger' : 'text-success' }}">
                                        {{ amo(abs($payment->amount)) }}
                                        @if($payment->amount < 0)
                                            <small>(Refund)</small>
                                            @endif
                                    </td>
                                    <td>{{ $payment->reference_number ?? '-' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            <!-- Notes -->
            @if($order->notes)
            <div class="row mt-4">
                <div class="col-md-12">
                    <h5 class="mb-2">Notes</h5>
                    <p class="text-muted">{{ $order->notes }}</p>
                </div>
            </div>
            @endif

            <!-- Footer -->
            <div class="row mt-5">
                <div class="col-md-12 text-center">
                    <p class="text-muted border-top pt-3">
                        {{ $receipt_footer ?? 'Thank you for your business!' }}
                    </p>
                </div>
            </div>
        </div>
    </div>



   

</div>