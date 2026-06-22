<div class="tab-pane active">
    <!-- ========== ADD/EDIT FORM ========== -->
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-header bg-transparent border-0 pt-3">
            <h6 class="mb-0 fw-bold text-primary d-flex align-items-center gap-2">
                <i class="bi bi-plus-circle"></i> {{ $editingPaymentMethodId ? 'Edit' : 'Add' }} Payment Method
            </h6>
        </div>
        <div class="card-body pt-0">
            <div class="row g-2">
                <div class="col-12 col-sm-6 col-md-5">
                    <input type="text"
                        wire:model="newPaymentMethod.name"
                        placeholder="Method Name"
                        class="form-control form-control-sm @error('newPaymentMethod.name') is-invalid @enderror">
                    @error('newPaymentMethod.name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-6 col-sm-3 col-md-3">
                    <div class="form-check form-switch mt-1">
                        <input type="checkbox"
                            wire:model="newPaymentMethod.is_active"
                            class="form-check-input"
                            id="paymentMethodActive"
                            style="width: 2.5rem; height: 1.25rem;">
                        <label class="form-check-label small" for="paymentMethodActive">Active</label>
                    </div>
                </div>
                <div class="col-6 col-sm-3 col-md-4">
                    <div class="d-flex gap-1">
                        @if($editingPaymentMethodId)
                            <button wire:click="updatePaymentMethod" class="btn btn-primary btn-sm shadow-sm">
                                <i class="bi bi-check-lg"></i> Update
                            </button>
                            <button wire:click="cancelEditPaymentMethod" class="btn btn-secondary btn-sm shadow-sm">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        @else
                            <button wire:click="addPaymentMethod" class="btn btn-success btn-sm shadow-sm">
                                <i class="bi bi-plus-lg"></i> Add
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ========== PAYMENT METHODS LIST ========== -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent border-0 pt-3 d-flex flex-wrap align-items-center justify-content-between">
            <h6 class="mb-0 fw-bold text-secondary d-flex align-items-center gap-2">
                <i class="bi bi-list-ul"></i> Payment Methods
            </h6>
            <span class="badge bg-secondary-soft text-secondary rounded-pill">{{ count($paymentMethods) }}</span>
        </div>
        <div class="card-body pt-0">
            <!-- Mobile Card View -->
            <div class="d-md-none">
                @foreach($paymentMethods as $method)
                <div class="border-bottom py-2">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="fw-semibold">{{ $method['name'] }}</div>
                            <div class="text-muted small">{{ $method['slug'] }}</div>
                        </div>
                        <div class="text-end">
                            @if($method['is_active'])
                                <span class="badge bg-success-soft text-success rounded-pill">Active</span>
                            @else
                                <span class="badge bg-secondary-soft text-secondary rounded-pill">Inactive</span>
                            @endif
                            <div class="mt-1">
                                <button wire:click="editPaymentMethod({{ $method['id'] }})"
                                    class="btn btn-info-soft text-info btn-sm rounded-circle shadow-sm" title="Edit" style="width: 28px; height: 28px; padding: 0; font-size: 0.6rem;">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button wire:click="confirmDeletePaymentMethod({{ $method['id'] }})"
                                    class="btn btn-danger-soft text-danger btn-sm rounded-circle shadow-sm" title="Delete" style="width: 28px; height: 28px; padding: 0; font-size: 0.6rem;">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Desktop Table View -->
            <div class="table-responsive d-none d-md-block">
                <table class="table table-bordered table-hover align-middle mb-0">
                    <thead class="table">
                        <tr>
                            <th class="small">Name</th>
                            <th class="small">Slug</th>
                            <th class="text-center small">Status</th>
                            <th class="text-center small" width="150px">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($paymentMethods as $method)
                        <tr>
                            <td>
                                <span class="fw-semibold">{{ $method['name'] }}</span>
                            </td>
                            <td><code class="bg px-2 py-1 rounded small">{{ $method['slug'] }}</code></td>
                            <td class="text-center">
                                @if($method['is_active'])
                                    <span class="badge bg-success-soft text-success rounded-pill px-3 py-2">Active</span>
                                @else
                                    <span class="badge bg-secondary-soft text-secondary rounded-pill px-3 py-2">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex justify-content-center gap-1">
                                    <button wire:click="editPaymentMethod({{ $method['id'] }})"
                                        class="btn btn-info-soft text-info btn-sm rounded-circle shadow-sm" title="Edit" style="width: 32px; height: 32px; padding: 0; font-size: 0.7rem;">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button wire:click="confirmDeletePaymentMethod({{ $method['id'] }})"
                                        class="btn btn-danger-soft text-danger btn-sm rounded-circle shadow-sm" title="Delete" style="width: 32px; height: 32px; padding: 0; font-size: 0.7rem;">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ========== DELETE PAYMENT METHOD CONFIRMATION MODAL ========== -->
    @if($showDeletePaymentMethodModal)
    <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-exclamation-triangle text-danger me-2"></i>
                        Delete Payment Method
                    </h5>
                    <button type="button" class="btn-close" wire:click="closeAllModals"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-3">
                        <i class="bi bi-credit-card text-danger" style="font-size: 3rem;"></i>
                    </div>
                    <p class="text-center mb-3">
                        Are you sure you want to delete the payment method:
                        <br>
                        <strong class="text-primary">{{ $deleteItemName }}</strong>
                    </p>
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-circle me-1"></i>
                        This action cannot be undone. All payments associated with this method will be affected.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeAllModals">
                        <i class="bi bi-x-circle me-1"></i> Cancel
                    </button>
                    <button type="button" class="btn btn-danger" wire:click="executeDelete">
                        <i class="bi bi-trash me-1"></i> Yes, Delete
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>