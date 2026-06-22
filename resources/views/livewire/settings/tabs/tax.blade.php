<div class="tab-pane active">
    <!-- ========== ADD/EDIT FORM ========== -->
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-header bg-transparent border-0 pt-3">
            <h6 class="mb-0 fw-bold text-primary d-flex align-items-center gap-2">
                <i class="bi bi-plus-circle"></i> {{ $editingTaxRateId ? 'Edit' : 'Add' }} Tax Rate
            </h6>
        </div>
        <div class="card-body pt-0">
            <form wire:submit.prevent="{{ $editingTaxRateId ? 'updateTaxRate' : 'saveTaxRate' }}">
                <div class="row g-2">
                    <div class="col-12 col-sm-4 col-md-3">
                        <input type="text"
                            wire:model="newTaxRate.name"
                            placeholder="e.g., VAT, GST"
                            class="form-control form-control-sm @error('newTaxRate.name') is-invalid @enderror">
                        @error('newTaxRate.name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-6 col-sm-3 col-md-3">
                        <div class="input-group input-group-sm">
                            <input type="number"
                                wire:model="newTaxRate.rate"
                                step="0.01"
                                min="0"
                                max="100"
                                placeholder="Rate"
                                class="form-control @error('newTaxRate.rate') is-invalid @enderror">
                            <span class="input-group-text">%</span>
                            @error('newTaxRate.rate') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="col-6 col-sm-5 col-md-4">
                        <div class="d-flex flex-wrap gap-3 mt-1 mt-sm-0">
                            <div class="form-check form-switch">
                                <input type="checkbox"
                                    wire:model="newTaxRate.is_active"
                                    class="form-check-input"
                                    id="is_active"
                                    style="width: 2.5rem; height: 1.25rem;">
                                <label class="form-check-label small" for="is_active">Active</label>
                            </div>
                            <div class="form-check form-switch">
                                <input type="checkbox"
                                    wire:model="newTaxRate.is_default"
                                    class="form-check-input"
                                    id="is_default"
                                    style="width: 2.5rem; height: 1.25rem;">
                                <label class="form-check-label small" for="is_default">Default</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-12 col-md-2">
                        <div class="d-flex gap-1">
                            @if($editingTaxRateId)
                                <button type="submit" class="btn btn-primary btn-sm shadow-sm">
                                    <i class="bi bi-check-lg"></i> Update
                                </button>
                                <button type="button" wire:click="cancelEditTaxRate" class="btn btn-secondary btn-sm shadow-sm">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            @else
                                <button type="submit" class="btn btn-success btn-sm shadow-sm">
                                    <i class="bi bi-plus-lg"></i> Add
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- ========== TAX RATES LIST ========== -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent border-0 pt-3 d-flex flex-wrap align-items-center justify-content-between">
            <h6 class="mb-0 fw-bold text-secondary d-flex align-items-center gap-2">
                <i class="bi bi-list-ul"></i> Tax Rates
            </h6>
            <span class="badge bg-secondary-soft text-secondary rounded-pill">{{ count($taxRates) }}</span>
        </div>
        <div class="card-body pt-0">
            <!-- Mobile Card View -->
            <div class="d-md-none">
                @forelse($taxRates as $tax)
                <div class="border-bottom py-2">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="fw-semibold">{{ $tax['name'] }}</div>
                            <div class="text-muted small">{{ number_format($tax['rate'], 2) }}%</div>
                        </div>
                        <div class="text-end">
                            @if($tax['is_active'])
                                <span class="badge bg-success-soft text-success rounded-pill">Active</span>
                            @else
                                <span class="badge bg-secondary-soft text-secondary rounded-pill">Inactive</span>
                            @endif
                            @if($tax['is_default'])
                                <span class="badge bg-primary-soft text-primary rounded-pill">Default</span>
                            @endif
                        </div>
                    </div>
                    <div class="row g-1 small mt-1">
                        <div class="col-6">
                            <span class="text-muted">Used in:</span>
                            <span>{{ $tax['orders_count'] ?? 0 }} orders</span>
                        </div>
                        <div class="col-6 text-end">
                            <div class="d-flex justify-content-end gap-1">
                                @if(!$tax['is_default'])
                                <button wire:click="setDefaultTaxRate({{ $tax['id'] }})"
                                    class="btn btn-secondary-soft text-secondary btn-sm rounded-circle shadow-sm" title="Set Default" style="width: 28px; height: 28px; padding: 0; font-size: 0.6rem;">
                                    <i class="bi bi-star"></i>
                                </button>
                                @endif
                                <button wire:click="toggleTaxRateStatus({{ $tax['id'] }})"
                                    class="btn {{ $tax['is_active'] ? 'btn-warning-soft text-warning' : 'btn-success-soft text-success' }} btn-sm rounded-circle shadow-sm" title="{{ $tax['is_active'] ? 'Deactivate' : 'Activate' }}" style="width: 28px; height: 28px; padding: 0; font-size: 0.6rem;">
                                    <i class="bi {{ $tax['is_active'] ? 'bi-slash-circle' : 'bi-check-circle' }}"></i>
                                </button>
                                <button wire:click="editTaxRate({{ $tax['id'] }})"
                                    class="btn btn-info-soft text-info btn-sm rounded-circle shadow-sm" title="Edit" style="width: 28px; height: 28px; padding: 0; font-size: 0.6rem;">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button wire:click="confirmDeleteTaxRate({{ $tax['id'] }})"
                                    class="btn btn-danger-soft text-danger btn-sm rounded-circle shadow-sm" title="Delete" style="width: 28px; height: 28px; padding: 0; font-size: 0.6rem;"
                                    {{ $tax['orders_count'] > 0 || $tax['is_default'] ? 'disabled' : '' }}>
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-info-circle fs-2 d-block mb-2"></i>
                    <p class="small">No tax rates found. Add your first tax rate above.</p>
                </div>
                @endforelse
            </div>

            <!-- Desktop Table View -->
            <div class="table-responsive d-none d-md-block">
                <table class="table table-bordered table-hover align-middle mb-0">
                    <thead class="table">
                        <tr>
                            <th class="small">Name</th>
                            <th class="text-end small">Rate</th>
                            <th class="text-center small">Status</th>
                            <th class="text-center small">Default</th>
                            <th class="text-center small">Used In</th>
                            <th class="text-center small">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($taxRates as $tax)
                        <tr>
                            <td>
                                <span class="fw-semibold">{{ $tax['name'] }}</span>
                            </td>
                            <td class="text-end">{{ number_format($tax['rate'], 2) }}%</td>
                            <td class="text-center">
                                @if($tax['is_active'])
                                    <span class="badge bg-success-soft text-success rounded-pill px-3 py-2">Active</span>
                                @else
                                    <span class="badge bg-secondary-soft text-secondary rounded-pill px-3 py-2">Inactive</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($tax['is_default'])
                                    <span class="badge bg-primary-soft text-primary rounded-pill px-3 py-2">
                                        <i class="bi bi-star-fill me-1"></i> Default
                                    </span>
                                @else
                                    <button wire:click="setDefaultTaxRate({{ $tax['id'] }})"
                                        class="btn btn-secondary-soft text-secondary btn-sm rounded-circle shadow-sm" title="Set as default" style="width: 32px; height: 32px; padding: 0; font-size: 0.7rem;">
                                        <i class="bi bi-star"></i>
                                    </button>
                                @endif
                            </td>
                            <td class="text-center">{{ $tax['orders_count'] ?? 0 }}</td>
                            <td>
                                <div class="d-flex justify-content-center gap-1">
                                    <button wire:click="toggleTaxRateStatus({{ $tax['id'] }})"
                                        class="btn {{ $tax['is_active'] ? 'btn-warning-soft text-warning' : 'btn-success-soft text-success' }} btn-sm rounded-circle shadow-sm" title="{{ $tax['is_active'] ? 'Deactivate' : 'Activate' }}" style="width: 32px; height: 32px; padding: 0; font-size: 0.7rem;">
                                        <i class="bi {{ $tax['is_active'] ? 'bi-slash-circle' : 'bi-check-circle' }}"></i>
                                    </button>
                                    <button wire:click="editTaxRate({{ $tax['id'] }})"
                                        class="btn btn-info-soft text-info btn-sm rounded-circle shadow-sm" title="Edit" style="width: 32px; height: 32px; padding: 0; font-size: 0.7rem;">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button wire:click="confirmDeleteTaxRate({{ $tax['id'] }})"
                                        class="btn btn-danger-soft text-danger btn-sm rounded-circle shadow-sm" title="Delete" style="width: 32px; height: 32px; padding: 0; font-size: 0.7rem;"
                                        {{ $tax['orders_count'] > 0 || $tax['is_default'] ? 'disabled' : '' }}>
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                <i class="bi bi-info-circle fs-2 d-block mb-2"></i>
                                <p class="small">No tax rates found. Add your first tax rate above.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ========== DELETE TAX RATE CONFIRMATION MODAL ========== -->
    @if($showDeleteTaxRateModal)
    <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-exclamation-triangle text-danger me-2"></i>
                        Delete Tax Rate
                    </h5>
                    <button type="button" class="btn-close" wire:click="closeAllModals"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-3">
                        <i class="bi bi-percent text-danger" style="font-size: 3rem;"></i>
                    </div>
                    <p class="text-center mb-3">
                        Are you sure you want to delete the tax rate:
                        <br>
                        <strong class="text-primary">{{ $deleteItemName }}</strong>
                        <br>
                        <span class="text-muted">({{ number_format($taxRates[array_search($deleteItemId, array_column($taxRates, 'id'))]['rate'] ?? 0, 2) }}%)</span>
                    </p>
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-circle me-1"></i>
                        This action cannot be undone. Orders using this tax rate may be affected.
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