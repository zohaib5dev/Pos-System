<div>
    <!-- ========== PAGE HEADER ========== -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <div>
            <h4 class="mb-0 fw-bold  d-flex align-items-center gap-2">
                <i class="bi bi-box-seam"></i> Product Details
            </h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('products.index') }}" class="text-decoration-none">Products</a></li>
                    <li class="breadcrumb-item active">{{ Str::limit($product->name, 30) }}</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex flex-wrap gap-1">
            <a href="{{ route('products.edit', ['id' => $product->id]) }}" class="btn btn-primary btn-sm shadow-sm">
                <i class="bi bi-pencil"></i> <span class="d-none d-sm-inline">Edit</span>
            </a>
            <a href="{{ route('products.index') }}" class="btn btn-secondary btn-sm shadow-sm">
                <i class="bi bi-arrow-left"></i> <span class="d-none d-sm-inline">Back</span>
            </a>
        </div>
    </div>

    <!-- ========== PRODUCT NAME HEADER ========== -->
    <div class="card border-0 shadow-lg mb-3">
        <div class="card-body py-3">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                <div class="d-flex align-items-center gap-3">
                    @if($product->main_image)
                    <img src="{{ Storage::url($product->main_image) }}" 
                         class="rounded-3 shadow-sm"
                         style="width: 60px; height: 60px; object-fit: cover;">
                    @else
                    <div class="bg-secondary-soft rounded-3 d-flex align-items-center justify-content-center"
                         style="width: 60px; height: 60px;">
                        <i class="bi bi-box fs-2 text-secondary"></i>
                    </div>
                    @endif
                    <div>
                        <h5 class="mb-0 fw-bold">{{ $product->name }}</h5>
                        <div class="d-flex flex-wrap gap-2 mt-1">
                            <span class="badge bg-primary-soft text-primary rounded-pill">
                                <i class="bi bi-tag me-1"></i> {{ $product->sku }}
                            </span>
                            @if($product->is_featured)
                            <span class="badge bg-warning text-dark rounded-pill">
                                <i class="bi bi-star-fill me-1"></i> Featured
                            </span>
                            @endif
                            @if($product->is_active)
                            <span class="badge bg-success text-white rounded-pill">
                                <i class="bi bi-check-circle-fill me-1"></i> Active
                            </span>
                            @else
                            <span class="badge bg-danger text-white rounded-pill">
                                <i class="bi bi-x-circle-fill me-1"></i> Inactive
                            </span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <span class="badge bg-info-soft text-info rounded-pill px-3 py-2">
                        <i class="bi bi-clock me-1"></i> {{ $product->created_at }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- ========== MAIN DETAILS GRID ========== -->
    <div class="row g-3">
        <!-- Basic Information -->
        <div class="col-12 col-md-6">
            <div class="card border-0 shadow-lg h-100">
                <div class="card-header bg-transparent border-0 pt-3">
                    <h6 class="mb-0 fw-bold text-primary d-flex align-items-center gap-2">
                        <i class="bi bi-info-circle"></i> Basic Information
                    </h6>
                </div>
                <div class="card-body pt-0">
                    <div class="detail-item d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted small">Name</span>
                        <span class="fw-semibold">{{ $product->name }}</span>
                    </div>
                    <div class="detail-item d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted small">Slug</span>
                        <span class="fw-semibold">{{ $product->slug ?? '-' }}</span>
                    </div>
                    <div class="detail-item d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted small">SKU</span>
                        <span class="fw-semibold"><code>{{ $product->sku }}</code></span>
                    </div>
                    <div class="detail-item d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted small">Barcode</span>
                        <span class="fw-semibold">{{ $product->barcode ?? '-' }}</span>
                    </div>
                    <div class="detail-item d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted small">Category</span>
                        <span class="fw-semibold">{{ $product->category->name ?? '-' }}</span>
                    </div>
                    <div class="detail-item d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted small">Brand</span>
                        <span class="fw-semibold">{{ $product->brand->name ?? '-' }}</span>
                    </div>
                    <div class="detail-item d-flex justify-content-between py-2">
                        <span class="text-muted small">Unit</span>
                        <span class="fw-semibold">{{ $product->unit->name ?? '-' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pricing Information -->
        <div class="col-12 col-md-6">
            <div class="card border-0 shadow-lg h-100">
                <div class="card-header bg-transparent border-0 pt-3">
                    <h6 class="mb-0 fw-bold text-success d-flex align-items-center gap-2">
                        <i class="bi bi-currency-dollar"></i> Pricing Information
                    </h6>
                </div>
                <div class="card-body pt-0">
                    <div class="detail-item d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted small">Purchase Price</span>
                        <span class="fw-semibold text-primary">{{ amo($product->purchase_price) }}</span>
                    </div>
                    <div class="detail-item d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted small">Selling Price</span>
                        <span class="fw-bold text-success">{{ amo($product->selling_price) }}</span>
                    </div>
                    <div class="detail-item d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted small">Wholesale Price</span>
                        <span class="fw-semibold">{{ amo($product->wholesale_price ?? 0) }}</span>
                    </div>
                    <div class="detail-item d-flex justify-content-between py-2">
                        <span class="text-muted small">Tax</span>
                        <span class="fw-semibold">{{ $product->tax_rate }}% ({{ ucfirst($product->tax_type) }})</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stock Information -->
        <div class="col-12 col-md-6">
            <div class="card border-0 shadow-lg h-100">
                <div class="card-header bg-transparent border-0 pt-3">
                    <h6 class="mb-0 fw-bold text-warning d-flex align-items-center gap-2">
                        <i class="bi bi-box-seam"></i> Stock Information
                    </h6>
                </div>
                <div class="card-body pt-0">
                    <div class="detail-item d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted small">Stock Quantity</span>
                        <span>
                            @if($product->stock_quantity <= 0)
                                <span class="badge bg-danger text-white rounded-pill px-3 py-2">Out of Stock</span>
                            @elseif($product->stock_quantity <= $product->low_stock_threshold)
                                <span class="badge bg-warning text-dark rounded-pill px-3 py-2">{{ $product->stock_quantity }} (Low)</span>
                            @else
                                <span class="badge bg-success text-white rounded-pill px-3 py-2">{{ $product->stock_quantity }}</span>
                            @endif
                        </span>
                    </div>
                    <div class="detail-item d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted small">Low Stock Threshold</span>
                        <span class="fw-semibold">{{ $product->low_stock_threshold }}</span>
                    </div>
                    <div class="detail-item d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted small">Allow Out of Stock</span>
                        <span>
                            @if($product->allow_out_of_stock)
                            <span class="badge bg-success text-white rounded-pill">Yes</span>
                            @else
                            <span class="badge bg-danger text-white rounded-pill">No</span>
                            @endif
                        </span>
                    </div>
                    <div class="detail-item d-flex justify-content-between py-2">
                        <span class="text-muted small">Total Orders</span>
                        <span class="fw-semibold">{{ $product->order_items_count ?? 0 }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status Information -->
        <div class="col-12 col-md-6">
            <div class="card border-0 shadow-lg h-100">
                <div class="card-header bg-transparent border-0 pt-3">
                    <h6 class="mb-0 fw-bold text-secondary d-flex align-items-center gap-2">
                        <i class="bi bi-toggle-on"></i> Status
                    </h6>
                </div>
                <div class="card-body pt-0">
                    <div class="detail-item d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted small">Status</span>
                        <span>
                            @if($product->is_active)
                            <span class="badge bg-success text-white rounded-pill px-3 py-2">
                                <i class="bi bi-check-circle-fill me-1"></i> Active
                            </span>
                            @else
                            <span class="badge bg-danger text-white rounded-pill px-3 py-2">
                                <i class="bi bi-x-circle-fill me-1"></i> Inactive
                            </span>
                            @endif
                        </span>
                    </div>
                    <div class="detail-item d-flex justify-content-between py-2">
                        <span class="text-muted small">Featured</span>
                        <span>
                            @if($product->is_featured)
                            <span class="badge bg-warning text-dark rounded-pill px-3 py-2">
                                <i class="bi bi-star-fill me-1"></i> Featured
                            </span>
                            @else
                            <span class="badge bg-secondary text-white rounded-pill px-3 py-2">
                                <i class="bi bi-star me-1"></i> Not Featured
                            </span>
                            @endif
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Description -->
        @if($product->description)
        <div class="col-12">
            <div class="card border-0 shadow-lg">
                <div class="card-header bg-transparent border-0 pt-3">
                    <h6 class="mb-0 fw-bold text-info d-flex align-items-center gap-2">
                        <i class="bi bi-file-text"></i> Description
                    </h6>
                </div>
                <div class="card-body pt-0">
                    <p class="mb-0">{{ $product->description }}</p>
                </div>
            </div>
        </div>
        @endif

        <!-- Audit Information -->
        <div class="col-12">
            <div class="card border-0 shadow-lg">
                <div class="card-header bg-transparent border-0 pt-3">
                    <h6 class="mb-0 fw-bold text-secondary d-flex align-items-center gap-2">
                        <i class="bi bi-clock-history"></i> Audit Information
                    </h6>
                </div>
                <div class="card-body pt-0">
                    <div class="row g-3">
                        <div class="col-12 col-sm-4">
                            <div class="p-2 bg-light-soft rounded-3">
                                <span class="text-muted small d-block">Created By</span>
                                <span class="fw-semibold">{{ $product->creator->name ?? 'System' }}</span>
                            </div>
                        </div>
                        <div class="col-12 col-sm-4">
                            <div class="p-2 bg-light-soft rounded-3">
                                <span class="text-muted small d-block">Created At</span>
                                <span class="fw-semibold">{{ $product->created_at }}</span>
                            </div>
                        </div>
                        <div class="col-12 col-sm-4">
                            <div class="p-2 bg-light-soft rounded-3">
                                <span class="text-muted small d-block">Last Updated</span>
                                <span class="fw-semibold">{{ $product->updated_at }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
</div>