<div>
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Reports</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <div class="container-fluid"> 
        <div class="row mb-4">
            <div class="col-12">
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Quick Reports</h3>
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-wrap gap-2">
                            <button wire:click="$set('reportType', 'sales')"
                                class="btn {{ $reportType === 'sales' ? 'btn-primary' : 'btn-outline-primary' }} btn-sm">
                                <i class="fas fa-shopping-cart me-1"></i> Sales
                            </button>
                            <button wire:click="$set('reportType', 'purchases')"
                                class="btn {{ $reportType === 'purchases' ? 'btn-primary' : 'btn-outline-primary' }} btn-sm">
                                <i class="fas fa-truck me-1"></i> Purchases
                            </button>
                            <button wire:click="$set('reportType', 'inventory')"
                                class="btn {{ $reportType === 'inventory' ? 'btn-primary' : 'btn-outline-primary' }} btn-sm">
                                <i class="fas fa-boxes me-1"></i> Inventory
                            </button>
                            <button wire:click="$set('reportType', 'financial')"
                                class="btn {{ $reportType === 'financial' ? 'btn-primary' : 'btn-outline-primary' }} btn-sm">
                                <i class="fas fa-dollar-sign me-1"></i> Financial
                            </button>
                            <button wire:click="$set('reportType', 'customer')"
                                class="btn {{ $reportType === 'customer' ? 'btn-primary' : 'btn-outline-primary' }} btn-sm">
                                <i class="fas fa-users me-1"></i> Customers
                            </button>
                            <button wire:click="$set('reportType', 'supplier')"
                                class="btn {{ $reportType === 'supplier' ? 'btn-primary' : 'btn-outline-primary' }} btn-sm">
                                <i class="fas fa-user-tie me-1"></i> Suppliers
                            </button>
                            <button wire:click="$set('reportType', 'tax')"
                                class="btn {{ $reportType === 'tax' ? 'btn-primary' : 'btn-outline-primary' }} btn-sm">
                                <i class="fas fa-percent me-1"></i> Tax
                            </button>
                            <button wire:click="$set('reportType', 'profit')"
                                class="btn {{ $reportType === 'profit' ? 'btn-primary' : 'btn-outline-primary' }} btn-sm">
                                <i class="fas fa-chart-line me-1"></i> Profit & Loss
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card card-primary card-outline">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <h3 class="card-title">
                        <i class="fas fa-chart-bar me-2"></i>
                        {{ ucfirst($reportType) }} Report
                        <small class="ml-2 text-muted">{{ $startDate->format('M d, Y') }} - {{ $endDate->format('M d, Y') }}</small>
                    </h3>
                    <div class="d-flex gap-2">
                        <div class="btn-group" role="group" aria-label="Export options">
                            <button wire:click="exportReport('pdf')"
                                class="btn btn-danger"
                                style="background-color: #dc2626; border-color: #b91c1c; box-shadow: 0 2px 4px rgba(220, 38, 38, 0.2);">
                                <i class="fas fa-file-pdf me-2"></i>
                                PDF Document
                            </button>
                            <button wire:click="exportReport('csv')"
                                class="btn btn-success"
                                style="background-color: #059669; border-color: #047857; box-shadow: 0 2px 4px rgba(5, 150, 105, 0.2);">
                                <i class="fas fa-file-csv me-2"></i>
                                CSV File
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <div class="card card-light">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-filter me-2"></i>
                            Filter Options
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-3 col-md-6">
                                <div class="form-group">
                                    <label>Date Range</label>
                                    <select wire:model.live="dateRange" class="form-control form-control-sm">
                                        <option value="today">Today</option>
                                        <option value="yesterday">Yesterday</option>
                                        <option value="week">This Week</option>
                                        <option value="month">This Month</option>
                                        <option value="quarter">This Quarter</option>
                                        <option value="year">This Year</option>
                                        <option value="custom">Custom Range</option>
                                    </select>
                                </div>
                            </div>

                            @if($dateRange === 'custom')
                            <div class="col-lg-3 col-md-6">
                                <div class="form-group">
                                    <label>Start Date</label>
                                    <input type="date" wire:model.live="startDate" class="form-control form-control-sm">
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <div class="form-group">
                                    <label>End Date</label>
                                    <input type="date" wire:model.live="endDate" class="form-control form-control-sm">
                                </div>
                            </div>
                            @endif

                            @if($reportType === 'sales')
                            <div class="col-lg-3 col-md-6">
                                <div class="form-group">
                                    <label>Customer</label>
                                    <select wire:model.live="customerId" class="form-control form-control-sm">
                                        <option value="">All Customers</option>
                                        @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <div class="form-group">
                                    <label>Payment Method</label>
                                    <select wire:model.live="paymentMethod" class="form-control form-control-sm">
                                        <option value="">All Methods</option>
                                        <option value="cash">Cash</option>
                                        <option value="credit-card">Card</option>
                                        <option value="bank-transfer">Bank Transfer</option>
                                        <option value="mobile">Mobile Payment</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <div class="form-group">
                                    <label>Sales Person</label>
                                    <select wire:model.live="userId" class="form-control form-control-sm">
                                        <option value="">All Users</option>
                                        @foreach($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            @endif

                            @if($reportType === 'purchases')
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label>Supplier</label>
                                    <select wire:model.live="supplierId" class="form-control form-control-sm">
                                        <option value="">All Suppliers</option>
                                        @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label>Status</label>
                                    <select wire:model.live="status" class="form-control form-control-sm">
                                        <option value="">All Status</option>
                                        <option value="ordered">Ordered</option>
                                        <option value="received">Received</option>
                                        <option value="partial">Partial</option>
                                    </select>
                                </div>
                            </div>
                            @endif

                            @if($reportType === 'inventory')
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label>Category</label>
                                    <select wire:model.live="categoryId" class="form-control form-control-sm">
                                        <option value="">All Categories</option>
                                        @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label>Product</label>
                                    <select wire:model.live="productId" class="form-control form-control-sm">
                                        <option value="">All Products</option>
                                        @foreach($products as $product)
                                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                @if(count($summary) > 0)
                <div class="row mt-4">
                    @foreach($summary as $key => $value)
                    <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 mb-4">
                        <div class="card shadow h-100 border-left-{{ $this->getCardBorderColor($key, $value) }}"
                            style="border-left: 4px solid {{ $this->getCardBorderColorValue($key, $value) }};">
                            <div class="card-body">
                                <div class="row no-gutters align-items-start">
                                    <div class="col me-2">
                                        <div class="h3  mb-2">
                                            {{ $this->formatSummaryValue($key, $value) }}
                                        </div>
                                        <div class="text-xs font-weight-bold {{ $this->getCardTextColor($key, $value) }} text-uppercase mb-1">
                                            {{ $this->formatSummaryLabel($key) }}
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="{{ $this->getSummaryIcon($key) }} fa-2x {{ $this->getIconColorClass($key, $value) }}"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif

                <!-- Details Section -->
                <div class="mt-4">
                    @if($reportType === 'sales')
                    @include('livewire.reports.sales-details', ['details' => $details])
                    @elseif($reportType === 'purchases')
                    @include('livewire.reports.purchases-details', ['details' => $details])
                    @elseif($reportType === 'inventory')
                    @include('livewire.reports.inventory-details', ['details' => $details])
                    @elseif($reportType === 'customer')
                    @include('livewire.reports.customer-details', ['details' => $details])
                    @elseif($reportType === 'supplier')
                    @include('livewire.reports.supplier-details', ['details' => $details])
                    @elseif($reportType === 'tax')
                    @include('livewire.reports.tax-details', ['details' => $details])
                    @elseif($reportType === 'profit')
                    @include('livewire.reports.profit-details', ['details' => $details])
                    @endif
                </div>
            </div>
        </div>
    </div>

     
</div>