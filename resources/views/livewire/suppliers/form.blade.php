<div>
    <!-- ========== PAGE HEADER ========== -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <div>
            <h4 class="mb-0 fw-bold d-flex align-items-center gap-2">
                <i class="bi bi-truck"></i> 
                {{ $supplierId ? 'Edit Supplier' : 'Add New Supplier' }}
            </h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('suppliers.index') }}" class="text-decoration-none">Suppliers</a></li>
                    <li class="breadcrumb-item active">{{ $supplierId ? 'Edit' : 'Add' }}</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('suppliers.index') }}" class="btn btn-secondary btn-sm shadow-sm">
            <i class="bi bi-arrow-left"></i> <span class="d-none d-sm-inline">Back to List</span>
            <span class="d-sm-none">Back</span>
        </a>
    </div>

    <!-- ========== MAIN FORM CARD ========== -->
    <div class="card border-0 shadow-lg">
        <div class="card-header bg-transparent border-0 d-flex flex-wrap align-items-center justify-content-between pt-3">
            <h5 class="mb-0 fw-bold d-flex align-items-center gap-2">
                <i class="bi bi-pencil-square"></i>
                {{ $supplierId ? 'Edit Supplier' : 'Create New Supplier' }}
            </h5>
            <span class="badge bg-primary-soft text-primary rounded-pill">
                {{ $supplierId ? 'Editing' : 'New' }}
            </span>
        </div>

        <div class="card-body pt-0">
            <form wire:submit="saveSupplier">
                <!-- ========== BASIC INFORMATION ========== -->
                <div class="row g-3">
                    <div class="col-12">
                        <h6 class="fw-bold mb-0 d-flex align-items-center gap-2">
                            <i class="bi bi-info-circle"></i> Basic Information
                        </h6>
                        <hr class="mt-1">
                    </div>

                    <!-- Supplier Name -->
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold small">Supplier Name <span class="text-danger">*</span></label>
                        <input type="text" 
                               wire:model="name" 
                               class="form-control @error('name') is-invalid @enderror"
                               placeholder="Enter supplier name">
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <!-- Company Name -->
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold small">Company Name</label>
                        <input type="text" 
                               wire:model="company_name" 
                               class="form-control"
                               placeholder="Enter company name">
                    </div>

                    <!-- Email -->
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold small">Email Address</label>
                        <input type="email" 
                               wire:model="email" 
                               class="form-control @error('email') is-invalid @enderror"
                               placeholder="supplier@example.com">
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <!-- Phone -->
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold small">Phone Number <span class="text-danger">*</span></label>
                        <input type="text" 
                               wire:model="phone" 
                               class="form-control @error('phone') is-invalid @enderror"
                               placeholder="Enter phone number">
                        @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <!-- Alternative Phone -->
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold small">Alternative Phone</label>
                        <input type="text" 
                               wire:model="alternative_phone" 
                               class="form-control"
                               placeholder="Enter alternative phone">
                    </div>

                    <!-- Tax Number -->
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold small">Tax Number / VAT</label>
                        <input type="text" 
                               wire:model="tax_number" 
                               class="form-control"
                               placeholder="Enter tax number">
                    </div>
                </div>

                <!-- ========== ADDRESS INFORMATION ========== -->
                <div class="row g-3 mt-2">
                    <div class="col-12">
                        <h6 class="fw-bold text-success mb-0 d-flex align-items-center gap-2">
                            <i class="bi bi-geo-alt"></i> Address Information
                        </h6>
                        <hr class="mt-1">
                    </div>

                    <!-- Street Address -->
                    <div class="col-12">
                        <label class="form-label fw-semibold small">Street Address</label>
                        <input type="text" 
                               wire:model="address" 
                               class="form-control"
                               placeholder="Enter street address">
                    </div>

                    <!-- City -->
                    <div class="col-12 col-sm-6 col-md-3">
                        <label class="form-label fw-semibold small">City</label>
                        <input type="text" 
                               wire:model="city" 
                               class="form-control"
                               placeholder="Enter city">
                    </div>

                    <!-- State -->
                    <div class="col-12 col-sm-6 col-md-3">
                        <label class="form-label fw-semibold small">State / Province</label>
                        <input type="text" 
                               wire:model="state" 
                               class="form-control"
                               placeholder="Enter state">
                    </div>

                    <!-- Postal Code -->
                    <div class="col-12 col-sm-6 col-md-3">
                        <label class="form-label fw-semibold small">Postal Code</label>
                        <input type="text" 
                               wire:model="postal_code" 
                               class="form-control"
                               placeholder="Enter postal code">
                    </div>

                    <!-- Country -->
                    <div class="col-12 col-sm-6 col-md-3">
                        <label class="form-label fw-semibold small">Country</label>
                        <input type="text" 
                               wire:model="country" 
                               class="form-control"
                               placeholder="Enter country">
                    </div>
                </div>

                <!-- ========== PAYMENT INFORMATION ========== -->
                <div class="row g-3 mt-2">
                    <div class="col-12">
                        <h6 class="fw-bold text-warning mb-0 d-flex align-items-center gap-2">
                            <i class="bi bi-credit-card"></i> Payment Information
                        </h6>
                        <hr class="mt-1">
                    </div>

                    <!-- Payment Terms -->
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold small">Payment Terms</label>
                        <select wire:model="payment_terms" class="form-select">
                            <option value="">Select Payment Terms</option>
                            <option value="immediate">Immediate</option>
                            <option value="net_15">Net 15 Days</option>
                            <option value="net_30">Net 30 Days</option>
                            <option value="net_45">Net 45 Days</option>
                            <option value="net_60">Net 60 Days</option>
                            <option value="cod">Cash on Delivery</option>
                        </select>
                    </div>
                </div>

                <!-- ========== ADDITIONAL INFORMATION ========== -->
                <div class="row g-3 mt-2">
                    <div class="col-12">
                        <h6 class="fw-bold text-secondary mb-0 d-flex align-items-center gap-2">
                            <i class="bi bi-file-text"></i> Additional Information
                        </h6>
                        <hr class="mt-1">
                    </div>

                    <!-- Notes -->
                    <div class="col-12">
                        <label class="form-label fw-semibold small">Notes</label>
                        <textarea wire:model="notes" 
                                  rows="3" 
                                  class="form-control"
                                  placeholder="Enter any additional notes about the supplier"></textarea>
                    </div>

                    <!-- Status -->
                    <div class="col-12">
                        <div class="form-check form-switch">
                            <input type="checkbox" 
                                   wire:model="is_active" 
                                   class="form-check-input"
                                   id="is_active"
                                   style="width: 3rem; height: 1.5rem;">
                            <label class="form-check-label fw-semibold" for="is_active">
                                <span class="{{ $is_active ? 'text-success' : 'text-muted' }}">
                                    <i class="bi {{ $is_active ? 'bi-check-circle-fill' : 'bi-x-circle-fill' }} me-1"></i>
                                    {{ $is_active ? 'Active Supplier' : 'Inactive Supplier' }}
                                </span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- ========== FORM ACTIONS ========== -->
                <div class="row g-2 mt-4 pt-3 border-top">
                    <div class="col-12 d-flex flex-wrap gap-2">
                        <a href="{{ route('suppliers.index') }}" class="btn btn-secondary shadow-sm">
                            <i class="bi bi-x-lg"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary shadow-sm">
                            <i class="bi bi-check-lg"></i> 
                            {{ $supplierId ? 'Update Supplier' : 'Create Supplier' }}
                        </button>
                       
                    </div>
                </div>
            </form>
        </div>
    </div>

 
</div>