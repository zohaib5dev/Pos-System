<div class="tab-pane active">
    <!-- ========== ADD/EDIT FORM ========== -->
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-header bg-transparent border-0 pt-3">
            <h6 class="mb-0 fw-bold text-primary d-flex align-items-center gap-2">
                <i class="bi bi-plus-circle"></i> {{ $editingUnitId ? 'Edit' : 'Add' }} Unit
            </h6>
        </div>
        <div class="card-body pt-0">
            <div class="row g-2">
                <div class="col-12 col-sm-5 col-md-4">
                    <input type="text" 
                           wire:model="newUnit.name" 
                           placeholder="Unit Name" 
                           class="form-control form-control-sm @error('newUnit.name') is-invalid @enderror">
                    @error('newUnit.name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-6 col-sm-4 col-md-3">
                    <input type="text" 
                           wire:model="newUnit.short_name" 
                           placeholder="Short Name" 
                           class="form-control form-control-sm @error('newUnit.short_name') is-invalid @enderror">
                    @error('newUnit.short_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-6 col-sm-3 col-md-2">
                    <div class="form-check form-switch mt-1">
                        <input type="checkbox" 
                               wire:model="newUnit.is_active" 
                               class="form-check-input"
                               id="unitActive"
                               style="width: 2.5rem; height: 1.25rem;">
                        <label class="form-check-label small" for="unitActive">Active</label>
                    </div>
                </div>
                <div class="col-12 col-sm-12 col-md-3">
                    <div class="d-flex gap-1">
                        @if($editingUnitId)
                            <button wire:click="updateUnit" class="btn btn-primary btn-sm shadow-sm">
                                <i class="bi bi-check-lg"></i> Update
                            </button>
                            <button wire:click="cancelEditUnit" class="btn btn-secondary btn-sm shadow-sm">
                                <i class="bi bi-x-lg"></i> Cancel
                            </button>
                        @else
                            <button wire:click="addUnit" class="btn btn-success btn-sm shadow-sm">
                                <i class="bi bi-plus-lg"></i> Add Unit
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ========== UNITS LIST ========== -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent border-0 pt-3 d-flex flex-wrap align-items-center justify-content-between">
            <h6 class="mb-0 fw-bold text-secondary d-flex align-items-center gap-2">
                <i class="bi bi-list-ul"></i> Units
            </h6>
            <span class="badge bg-secondary-soft text-secondary rounded-pill">{{ count($units) }}</span>
        </div>
        <div class="card-body pt-0">
            <!-- Mobile Card View -->
            <div class="d-md-none">
                @foreach($units as $unit)
                <div class="border-bottom py-2">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="fw-semibold">{{ $unit['name'] }}</div>
                            <div class="text-muted small">{{ $unit['short_name'] }}</div>
                        </div>
                        <div class="text-end">
                            @if($unit['is_active'])
                                <span class="badge bg-success-soft text-success rounded-pill">Active</span>
                            @else
                                <span class="badge bg-secondary-soft text-secondary rounded-pill">Inactive</span>
                            @endif
                            <div class="mt-1">
                                <button wire:click="editUnit({{ $unit['id'] }})" 
                                        class="btn btn-info-soft text-info btn-sm rounded-circle shadow-sm" title="Edit" style="width: 28px; height: 28px; padding: 0; font-size: 0.6rem;">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button wire:click="confirmDeleteUnit({{ $unit['id'] }})" 
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
                            <th class="small">Short Name</th>
                            <th class="text-center small">Status</th>
                            <th class="text-center small" width="120px">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($units as $unit)
                            <tr>
                                <td>
                                    <span class="fw-semibold">{{ $unit['name'] }}</span>
                                </td>
                                <td><code class="bg px-2 py-1 rounded small">{{ $unit['short_name'] }}</code></td>
                                <td class="text-center">
                                    @if($unit['is_active'])
                                        <span class="badge bg-success-soft text-success rounded-pill px-3 py-2">Active</span>
                                    @else
                                        <span class="badge bg-secondary-soft text-secondary rounded-pill px-3 py-2">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex justify-content-center gap-1">
                                        <button wire:click="editUnit({{ $unit['id'] }})" 
                                                class="btn btn-info-soft text-info btn-sm rounded-circle shadow-sm" title="Edit" style="width: 32px; height: 32px; padding: 0; font-size: 0.7rem;">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button wire:click="confirmDeleteUnit({{ $unit['id'] }})" 
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

    <!-- ========== DELETE UNIT CONFIRMATION MODAL ========== -->
    @if($showDeleteUnitModal)
    <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-exclamation-triangle text-danger me-2"></i>
                        Delete Unit
                    </h5>
                    <button type="button" class="btn-close" wire:click="closeAllModals"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-3">
                        <i class="bi bi-rulers text-danger" style="font-size: 3rem;"></i>
                    </div>
                    <p class="text-center mb-3">
                        Are you sure you want to delete the unit:
                        <br>
                        <strong class="text-primary">{{ $deleteItemName }}</strong>
                        <br>
                        <span class="text-muted">({{ $units[array_search($deleteItemId, array_column($units, 'id'))]['short_name'] ?? '' }})</span>
                    </p>
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-circle me-1"></i>
                        This action cannot be undone. Products using this unit will be affected.
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