<div>
    <!-- ========== PAGE HEADER ========== -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <div>
            <h4 class="mb-0 fw-bold d-flex align-items-center gap-2">
                <i class="bi bi-person-plus"></i> 
                {{ $customerId ? 'Edit Customer' : 'Add New Customer' }}
            </h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('customers.index') }}" class="text-decoration-none">Customers</a></li>
                    <li class="breadcrumb-item active">{{ $customerId ? 'Edit' : 'Add' }}</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('customers.index') }}" class="btn btn-secondary btn-sm shadow-sm">
            <i class="bi bi-arrow-left"></i> <span class="d-none d-sm-inline">Back to List</span>
            <span class="d-sm-none">Back</span>
        </a>
    </div>

    <!-- ========== MAIN FORM CARD ========== -->
    <div class="card border-0 shadow-lg">
        <div class="card-header bg-transparent border-0 d-flex flex-wrap align-items-center justify-content-between pt-3">
            <h5 class="mb-0 fw-bold d-flex align-items-center gap-2">
                <i class="bi bi-pencil-square"></i>
                {{ $customerId ? 'Edit Customer' : 'Create New Customer' }}
            </h5>
            <span class="badge bg-primary-soft text-primary rounded-pill">
                {{ $customerId ? 'Editing' : 'New' }}
            </span>
        </div>

        <div class="card-body pt-0">
            <form wire:submit="saveCustomer">
                <!-- ========== CUSTOMER INFORMATION ========== -->
                <div class="row g-3">
                    <div class="col-12">
                        <h6 class="fw-bold mb-0 d-flex align-items-center gap-2">
                            <i class="bi bi-info-circle"></i> Customer Information
                        </h6>
                        <hr class="mt-1">
                    </div>

                    <!-- Customer Name -->
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold small">Customer Name <span class="text-danger">*</span></label>
                        <input type="text" 
                               wire:model="name" 
                               class="form-control @error('name') is-invalid @enderror"
                               placeholder="Enter customer name">
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <!-- Customer Code -->
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold small">Customer Code <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="text" 
                                   wire:model="customer_code" 
                                   class="form-control @error('customer_code') is-invalid @enderror"
                                   placeholder="Enter code">
                            <button type="button" 
                                    wire:click="generateCustomerCode" 
                                    class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-repeat"></i>
                            </button>
                            @error('customer_code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <small class="text-muted">Unique identifier for this customer</small>
                    </div>

                    <!-- Email -->
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold small">Email Address</label>
                        <input type="email" 
                               wire:model="email" 
                               class="form-control @error('email') is-invalid @enderror"
                               placeholder="customer@example.com">
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

                    <!-- Address (optional - you can add if needed) -->
                    <div class="col-12">
                        <label class="form-label fw-semibold small">Address</label>
                        <textarea wire:model="address" 
                                  rows="2" 
                                  class="form-control @error('address') is-invalid @enderror"
                                  placeholder="Enter customer address"></textarea>
                        @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <!-- Status -->
                    <div class="col-12">
                        <h6 class="fw-bold text-secondary mb-0 d-flex align-items-center gap-2 mt-2">
                            <i class="bi bi-toggle-on"></i> Status
                        </h6>
                        <hr class="mt-1">
                    </div>

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
                                    {{ $is_active ? 'Active Customer' : 'Inactive Customer' }}
                                </span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- ========== FORM ACTIONS ========== -->
                <div class="row g-2 mt-4 pt-3 border-top">
                    <div class="col-12 d-flex flex-wrap gap-2">
                        <a href="{{ route('customers.index') }}" class="btn btn-secondary shadow-sm">
                            <i class="bi bi-x-lg"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary shadow-sm">
                            <i class="bi bi-check-lg"></i> 
                            {{ $customerId ? 'Update Customer' : 'Create Customer' }}
                        </button>
                       
                    </div>
                </div>
            </form>
        </div>
    </div>

    
</div>