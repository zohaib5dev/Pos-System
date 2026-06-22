<div>
    <!-- ========== PAGE HEADER ========== -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <div>
            <h4 class="mb-0 fw-bold d-flex align-items-center gap-2">
                <i class="bi bi-box-seam"></i> 
                {{ $productId ? 'Edit Product' : 'Add New Product' }}
            </h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('products.index') }}" class="text-decoration-none">Products</a></li>
                    <li class="breadcrumb-item active">{{ $productId ? 'Edit' : 'Add' }}</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('products.index') }}" class="btn btn-secondary btn-sm shadow-sm">
            <i class="bi bi-arrow-left"></i> <span class="d-none d-sm-inline">Back to List</span>
            <span class="d-sm-none">Back</span>
        </a>
    </div>

    <!-- ========== MAIN FORM CARD ========== -->
    <div class="card border-0 shadow-lg">
        <div class="card-header bg-transparent border-0 d-flex flex-wrap align-items-center justify-content-between pt-3">
            <h5 class="mb-0 fw-bold d-flex align-items-center gap-2">
                <i class="bi bi-pencil-square"></i>
                {{ $productId ? 'Edit Product' : 'Create New Product' }}
            </h5>
            <span class="badge bg-primary-soft text-primary rounded-pill">
                {{ $productId ? 'Editing' : 'New' }}
            </span>
        </div>

        <div class="card-body pt-0">
            <form wire:submit="saveProduct" enctype="multipart/form-data">
                <!-- ========== BASIC INFORMATION ========== -->
                <div class="row g-3 mb-3">
                    <div class="col-12">
                        <h6 class="fw-bold mb-0 d-flex align-items-center gap-2">
                            <i class="bi bi-info-circle"></i> Basic Information
                        </h6>
                        <hr class="mt-1">
                    </div>

                    <!-- Product Name -->
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold small">Product Name <span class="text-danger">*</span></label>
                        <input type="text" 
                               wire:model.live="name" 
                               class="form-control @error('name') is-invalid @enderror"
                               placeholder="Enter product name"
                               x-data
                               @input="$wire.generateSlugFromName($event.target.value)">
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <!-- Slug -->
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold small">Slug</label>
                        <div class="input-group">
                            <input type="text" 
                                   wire:model="slug" 
                                   class="form-control @error('slug') is-invalid @enderror"
                                   placeholder="product-url-slug"
                                   x-data
                                   @input="
                                       const slug = $event.target.value
                                           .toLowerCase()
                                           .replace(/[^a-z0-9]+/g, '-')
                                           .replace(/^-+|-+$/g, '');
                                       if (slug !== $event.target.value) {
                                           $event.target.value = slug;
                                       }
                                       $wire.set('slug', slug, true);
                                   ">
                            <button type="button" 
                                    class="btn btn-outline-secondary"
                                    x-data
                                    @click="
                                        const name = document.querySelector('[wire\\:model\\.live=\"name\"]')?.value || '';
                                        if (name) {
                                            const slug = name
                                                .toLowerCase()
                                                .replace(/[^a-z0-9]+/g, '-')
                                                .replace(/^-+|-+$/g, '');
                                            $wire.set('slug', slug, true);
                                        }
                                    ">
                                <i class="bi bi-arrow-repeat"></i>
                            </button>
                            @error('slug') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <small class="text-muted">URL-friendly version of the name</small>
                    </div>

                    <!-- SKU -->
                    <div class="col-12 col-md-4">
                        <label class="form-label fw-semibold small">SKU <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="text" 
                                   wire:model="sku" 
                                   class="form-control @error('sku') is-invalid @enderror"
                                   placeholder="Enter SKU">
                            <button type="button" 
                                    wire:click="generateSku" 
                                    class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-repeat"></i>
                            </button>
                            @error('sku') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <!-- Barcode -->
                    <div class="col-12 col-md-4">
                        <label class="form-label fw-semibold small">Barcode</label>
                        <input type="text" 
                               wire:model="barcode" 
                               class="form-control @error('barcode') is-invalid @enderror"
                               placeholder="Enter barcode">
                        @error('barcode') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <!-- Category -->
                    <div class="col-12 col-md-4">
                        <label class="form-label fw-semibold small">Category</label>
                        <select wire:model="category_id" class="form-select @error('category_id') is-invalid @enderror">
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <!-- Brand -->
                    <div class="col-12 col-md-4">
                        <label class="form-label fw-semibold small">Brand</label>
                        <select wire:model="brand_id" class="form-select @error('brand_id') is-invalid @enderror">
                            <option value="">Select Brand</option>
                            @foreach($brands as $brand)
                                <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                            @endforeach
                        </select>
                        @error('brand_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <!-- Unit -->
                    <div class="col-12 col-md-4">
                        <label class="form-label fw-semibold small">Unit</label>
                        <select wire:model="unit_id" class="form-select @error('unit_id') is-invalid @enderror">
                            <option value="">Select Unit</option>
                            @foreach($units as $unit)
                                <option value="{{ $unit->id }}">{{ $unit->name }} ({{ $unit->short_name }})</option>
                            @endforeach
                        </select>
                        @error('unit_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <!-- Description -->
                    <div class="col-12">
                        <label class="form-label fw-semibold small">Description</label>
                        <textarea wire:model="description" 
                                  rows="3" 
                                  class="form-control @error('description') is-invalid @enderror"
                                  placeholder="Enter product description"></textarea>
                        @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <!-- ========== PRICING ========== -->
                <div class="row g-3 mb-3">
                    <div class="col-12">
                        <h6 class="fw-bold text-success mb-0 d-flex align-items-center gap-2">
                            <i class="bi bi-currency-dollar"></i> Pricing
                        </h6>
                        <hr class="mt-1">
                    </div>

                    <div class="col-6 col-md-3">
                        <label class="form-label fw-semibold small">Purchase Price <span class="text-danger">*</span></label>
                        <input type="number" 
                               wire:model="purchase_price" 
                               step="0.01" 
                               min="0"
                               class="form-control @error('purchase_price') is-invalid @enderror"
                               placeholder="0.00">
                        @error('purchase_price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-6 col-md-3">
                        <label class="form-label fw-semibold small">Selling Price <span class="text-danger">*</span></label>
                        <input type="number" 
                               wire:model="selling_price" 
                               step="0.01" 
                               min="0"
                               class="form-control @error('selling_price') is-invalid @enderror"
                               placeholder="0.00">
                        @error('selling_price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-6 col-md-3">
                        <label class="form-label fw-semibold small">Wholesale Price</label>
                        <input type="number" 
                               wire:model="wholesale_price" 
                               step="0.01" 
                               min="0"
                               class="form-control"
                               placeholder="0.00">
                    </div>

                    <div class="col-6 col-md-3">
                        <label class="form-label fw-semibold small">Tax Rate (%)</label>
                        <input type="number" 
                               wire:model="tax_rate" 
                               step="0.01" 
                               min="0" 
                               max="100"
                               class="form-control"
                               placeholder="0">
                    </div>

                    <div class="col-12 col-md-3">
                        <label class="form-label fw-semibold small">Tax Type</label>
                        <select wire:model="tax_type" class="form-select">
                            <option value="exclusive">Exclusive</option>
                            <option value="inclusive">Inclusive</option>
                        </select>
                    </div>
                </div>

                <!-- ========== STOCK MANAGEMENT ========== -->
                <div class="row g-3 mb-3">
                    <div class="col-12">
                        <h6 class="fw-bold text-warning mb-0 d-flex align-items-center gap-2">
                            <i class="bi bi-box-seam"></i> Stock Management
                        </h6>
                        <hr class="mt-1">
                    </div>

                    <div class="col-6 col-md-4">
                        <label class="form-label fw-semibold small">Stock Quantity <span class="text-danger">*</span></label>
                        <input type="number" 
                               wire:model="stock_quantity" 
                               min="0"
                               class="form-control @error('stock_quantity') is-invalid @enderror"
                               placeholder="0">
                        @error('stock_quantity') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-6 col-md-4">
                        <label class="form-label fw-semibold small">Low Stock Threshold</label>
                        <input type="number" 
                               wire:model="low_stock_threshold" 
                               min="0"
                               class="form-control"
                               placeholder="0">
                    </div>

                    <div class="col-12 col-md-4">
                        <div class="form-check mt-3 mt-md-4">
                            <input type="checkbox" 
                                   wire:model="allow_out_of_stock" 
                                   class="form-check-input"
                                   id="allow_out_of_stock">
                            <label class="form-check-label" for="allow_out_of_stock">
                                Allow out of stock purchases
                            </label>
                        </div>
                    </div>
                </div>

                <!-- ========== PRODUCT IMAGE ========== -->
                <div class="row g-3 mb-3">
                    <div class="col-12">
                        <h6 class="fw-bold text-info mb-0 d-flex align-items-center gap-2">
                            <i class="bi bi-image"></i> Product Image
                        </h6>
                        <hr class="mt-1">
                    </div>

                    <!-- Existing Image -->
                    @if($existingImage)
                    <div class="col-12">
                        <label class="form-label fw-semibold small">Current Image</label>
                        <div class="position-relative d-inline-block">
                            <img src="{{ Storage::url($existingImage) }}" 
                                 class="rounded-3 shadow-sm"
                                 style="height: 120px; width: 120px; object-fit: cover;">
                            <button type="button" 
                                    wire:click="deleteImage"
                                    wire:confirm="Are you sure you want to delete this image?"
                                    class="btn btn-danger btn-sm position-absolute top-0 end-0 rounded-circle shadow-sm"
                                    style="width: 28px; height: 28px; padding: 0; font-size: 0.7rem;">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </div>
                    </div>
                    @endif

                    <!-- Upload New Image -->
                    <div class="col-12">
                        <label class="form-label fw-semibold small">{{ $existingImage ? 'Change Image' : 'Upload Image' }}</label>
                        <div class="dropzone-wrapper border-2 border-dashed rounded-3 p-4 text-center @error('tempImage') border-danger @enderror" 
                             style="border-color: var(--bs-border-color); background: var(--bs-tertiary-bg);"
                             x-data="{ dragging: false }"
                             @dragover.prevent="dragging = true"
                             @dragleave.prevent="dragging = false"
                             @drop.prevent="dragging = false; $refs.fileInput.files = $event.dataTransfer.files; $refs.fileInput.dispatchEvent(new Event('change'))">
                            <i class="bi bi-cloud-upload fs-1 d-block mb-2 text-muted"></i>
                            <p class="mb-1 small">
                                <span class="fw-semibold text-primary">Click to upload</span> or drag and drop
                            </p>
                            <p class="text-muted small mb-0">PNG, JPG, GIF up to 2MB</p>
                            <input type="file" 
                                   x-ref="fileInput"
                                   wire:model.live="tempImage" 
                                   accept="image/*" 
                                   class="d-none"
                                   id="image">
                            <button type="button" class="btn btn-primary btn-sm mt-2 shadow-sm" @click="$refs.fileInput.click()">
                                <i class="bi bi-upload"></i> Choose File
                            </button>
                        </div>
                        @error('tempImage') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        
                        <!-- Preview -->
                        @if($tempImage)
                        <div class="mt-3 position-relative d-inline-block">
                            <img src="{{ $tempImage->temporaryUrl() }}" 
                                 class="rounded-3 shadow-sm"
                                 style="height: 120px; width: 120px; object-fit: cover;">
                            <button type="button" 
                                    wire:click="removeTempImage" 
                                    class="btn btn-danger btn-sm position-absolute top-0 end-0 rounded-circle shadow-sm"
                                    style="width: 28px; height: 28px; padding: 0; font-size: 0.7rem;">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- ========== STATUS ========== -->
                <div class="row g-3 mb-3">
                    <div class="col-12">
                        <h6 class="fw-bold text-secondary mb-0 d-flex align-items-center gap-2">
                            <i class="bi bi-toggle-on"></i> Status
                        </h6>
                        <hr class="mt-1">
                    </div>

                    <div class="col-6 col-md-6">
                        <div class="form-check">
                            <input type="checkbox" 
                                   wire:model="is_active" 
                                   class="form-check-input"
                                   id="is_active">
                            <label class="form-check-label" for="is_active">
                                <i class="bi bi-check-circle-fill text-success me-1"></i> Active
                            </label>
                        </div>
                    </div>

                    <div class="col-6 col-md-6">
                        <div class="form-check">
                            <input type="checkbox" 
                                   wire:model="is_featured" 
                                   class="form-check-input"
                                   id="is_featured">
                            <label class="form-check-label" for="is_featured">
                                <i class="bi bi-star-fill text-warning me-1"></i> Featured Product
                            </label>
                        </div>
                    </div>
                </div>

                <!-- ========== FORM ACTIONS ========== -->
                <div class="row g-2 mt-4 pt-3 border-top">
                    <div class="col-12 d-flex flex-wrap gap-2">
                        <a href="{{ route('products.index') }}" class="btn btn-secondary shadow-sm">
                            <i class="bi bi-x-lg"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary shadow-sm">
                            <i class="bi bi-check-lg"></i> 
                            {{ $productId ? 'Update Product' : 'Create Product' }}
                        </button>
                      
                        
                    </div>
                </div>
            </form>
        </div>
    </div>

  
</div>