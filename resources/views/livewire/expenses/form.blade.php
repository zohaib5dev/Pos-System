<div>
    <!-- ========== PAGE HEADER ========== -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <div>
            <h4 class="mb-0 fw-bold d-flex align-items-center gap-2">
                <i class="bi bi-receipt"></i> 
                {{ $id ? 'Edit Expense' : 'Add New Expense' }}
            </h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('expenses.index') }}" class="text-decoration-none">Expenses</a></li>
                    <li class="breadcrumb-item active">{{ $id ? 'Edit' : 'Add' }}</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('expenses.index') }}" class="btn btn-secondary btn-sm shadow-sm">
            <i class="bi bi-arrow-left"></i> <span class="d-none d-sm-inline">Back to List</span>
            <span class="d-sm-none">Back</span>
        </a>
    </div>

    <!-- ========== MAIN FORM CARD ========== -->
    <div class="card border-0 shadow-lg">
        <div class="card-header bg-transparent border-0 d-flex flex-wrap align-items-center justify-content-between pt-3">
            <h5 class="mb-0 fw-bold d-flex align-items-center gap-2">
                <i class="bi bi-pencil-square"></i>
                {{ $id ? 'Edit Expense' : 'Create New Expense' }}
            </h5>
            <span class="badge bg-primary-soft text-primary rounded-pill">
                {{ $id ? 'Editing' : 'New' }}
            </span>
        </div>

        <div class="card-body pt-0">
            <form wire:submit="saveExpense" enctype="multipart/form-data">
                <!-- ========== EXPENSE INFORMATION ========== -->
                <div class="row g-3">
                    <div class="col-12">
                        <h6 class="fw-bold mb-0 d-flex align-items-center gap-2">
                            <i class="bi bi-info-circle"></i> Expense Information
                        </h6>
                        <hr class="mt-1">
                    </div>

                    <!-- Expense Number -->
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold small">Expense Number <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="text" 
                                   wire:model="expense_number" 
                                   class="form-control @error('expense_number') is-invalid @enderror"
                                   readonly>
                            <button type="button" 
                                    wire:click="generateExpenseNumber" 
                                    class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-repeat"></i>
                            </button>
                            @error('expense_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <!-- Expense Date -->
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold small">Expense Date <span class="text-danger">*</span></label>
                        <input type="date" 
                               wire:model="expense_date" 
                               class="form-control @error('expense_date') is-invalid @enderror">
                        @error('expense_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <!-- Category -->
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold small">Category <span class="text-danger">*</span></label>
                        <select wire:model="expense_category_id" class="form-select @error('expense_category_id') is-invalid @enderror">
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                        @error('expense_category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <!-- Amount -->
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold small">Amount <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" 
                                   wire:model="amount" 
                                   step="0.01"
                                   min="0.01"
                                   class="form-control @error('amount') is-invalid @enderror"
                                   placeholder="0.00">
                        </div>
                        @error('amount') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <!-- Payment Method -->
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold small">Payment Method <span class="text-danger">*</span></label>
                        <select wire:model="payment_method_id" class="form-select @error('payment_method_id') is-invalid @enderror">
                            <option value="">Select Payment Method</option>
                            @foreach($paymentMethods as $method)
                                <option value="{{ $method->id }}">{{ $method->name }}</option>
                            @endforeach
                        </select>
                        @error('payment_method_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <!-- Reference Number -->
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold small">Reference Number</label>
                        <input type="text" 
                               wire:model="reference_number" 
                               class="form-control @error('reference_number') is-invalid @enderror"
                               placeholder="e.g., Invoice #, Receipt #">
                        @error('reference_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <!-- Description -->
                    <div class="col-12">
                        <label class="form-label fw-semibold small">Description <span class="text-danger">*</span></label>
                        <textarea wire:model="description" 
                                  rows="3" 
                                  class="form-control @error('description') is-invalid @enderror"
                                  placeholder="Describe the expense..."></textarea>
                        @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <!-- Additional Notes -->
                    <div class="col-12">
                        <label class="form-label fw-semibold small">Additional Notes</label>
                        <textarea wire:model="notes" 
                                  rows="2" 
                                  class="form-control"
                                  placeholder="Any additional information..."></textarea>
                    </div>
                </div>

                <!-- ========== RECEIPT IMAGE ========== -->
                <div class="row g-3 mt-2">
                    <div class="col-12">
                        <h6 class="fw-bold text-info mb-0 d-flex align-items-center gap-2">
                            <i class="bi bi-image"></i> Receipt Image
                        </h6>
                        <hr class="mt-1">
                    </div>

                    <!-- Existing Receipt -->
                    @if($existing_receipt)
                    <div class="col-12">
                        <label class="form-label fw-semibold small">Current Receipt</label>
                        <div class="position-relative d-inline-block">
                            <img src="{{ Storage::url($existing_receipt) }}" 
                                 class="rounded-3 shadow-sm"
                                 style="height: 120px; width: 120px; object-fit: cover;">
                            <button type="button" 
                                    wire:click="removeReceipt" 
                                    wire:confirm="Are you sure you want to remove this receipt?"
                                    class="btn btn-danger btn-sm position-absolute top-0 end-0 rounded-circle shadow-sm"
                                    style="width: 28px; height: 28px; padding: 0; font-size: 0.7rem;">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </div>
                    </div>
                    @endif

                    <!-- Upload New Receipt -->
                    <div class="col-12">
                        <label class="form-label fw-semibold small">{{ $existing_receipt ? 'Change Receipt' : 'Upload Receipt' }}</label>
                        <div class="dropzone-wrapper border-2 border-dashed rounded-3 p-4 text-center @error('receipt_image') border-danger @enderror" 
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
                                   wire:model.live="receipt_image" 
                                   accept="image/*" 
                                   class="d-none"
                                   id="receipt_image">
                            <button type="button" class="btn btn-primary btn-sm mt-2 shadow-sm" @click="$refs.fileInput.click()">
                                <i class="bi bi-upload"></i> Choose File
                            </button>
                        </div>
                        @error('receipt_image') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        
                        <!-- Preview -->
                        @if($receipt_image && !$errors->has('receipt_image'))
                        <div class="mt-3 position-relative d-inline-block">
                            <img src="{{ $receipt_image->temporaryUrl() }}" 
                                 class="rounded-3 shadow-sm"
                                 style="height: 120px; width: 120px; object-fit: cover;">
                            <button type="button" 
                                    wire:click="$set('receipt_image', null)" 
                                    class="btn btn-danger btn-sm position-absolute top-0 end-0 rounded-circle shadow-sm"
                                    style="width: 28px; height: 28px; padding: 0; font-size: 0.7rem;">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- ========== FORM ACTIONS ========== -->
                <div class="row g-2 mt-4 pt-3 border-top">
                    <div class="col-12 d-flex flex-wrap gap-2">
                        <a href="{{ route('expenses.index') }}" class="btn btn-secondary shadow-sm">
                            <i class="bi bi-x-lg"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary shadow-sm">
                            <i class="bi bi-check-lg"></i> 
                            {{ $id ? 'Update Expense' : 'Create Expense' }}
                        </button>
                      
                    </div>
                </div>
            </form>
        </div>
    </div>

  
</div>