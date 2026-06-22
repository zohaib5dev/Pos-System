<!-- Permissions Modal -->
@if($showPermissionModal)
<div class="modal fade show" style="display: block; background: rgba(0,0,0,0.6); backdrop-filter: blur(4px);" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered  modal-lg" role="document">
        <div class="modal-content border-0 shadow-lg">
            <!-- Modal Header -->
            <div class="modal-header border-0 pb-0">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-success-soft p-2" style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center;">
                        <i class="bi bi-shield-check text-success fa-lg"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0">
                            {{ $editingPermissionId ? 'Edit Permission' : 'Add New Permission' }}
                        </h5>
                        <p class="text-muted small mb-0">
                            {{ $editingPermissionId ? 'Update permission details' : 'Create a new permission' }}
                        </p>
                    </div>
                </div>
                <button type="button" class="btn-close" wire:click="$set('showPermissionModal', false)" aria-label="Close"></button>
            </div>

            <form wire:submit.prevent="savePermission">
                <div class="modal-body px-4 py-3">
                    <!-- Permission Name Input -->
                    <div class="mb-3">
                        <label for="permissionName" class="form-label fw-semibold">
                            <i class="bi bi-tag me-1"></i>
                            Permission Name <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-shield text-muted"></i>
                            </span>
                            <input type="text"
                                id="permissionName"
                                wire:model="permissionName"
                                class="form-control border-start-0 @error('permissionName') is-invalid @enderror"
                                placeholder="e.g., view users, create posts, edit settings"
                                autocomplete="off"
                                autofocus>
                        </div>
                        @error('permissionName') 
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted mt-1 d-flex align-items-center gap-1">
                            <i class="bi bi-info-circle"></i>
                            Use lowercase letters and spaces. Example: <strong>"view users"</strong> or <strong>"create posts"</strong>
                        </small>
                    </div>

                    <!-- Tips Card -->
                    <div class="card bg-info-soft border-0">
                        <div class="card-body py-2 px-3">
                            <div class="d-flex align-items-start gap-2">
                                <i class="bi bi-lightbulb text-info mt-1"></i>
                                <div>
                                    <h6 class="mb-1 fw-semibold small">Permission Naming Tips</h6>
                                    <ul class="mb-0 small ps-3" style="color: #666;">
                                        <li>Use <strong>lowercase</strong> letters only</li>
                                        <li>Use <strong>spaces</strong> between words (e.g., "view users")</li>
                                        <li>Be <strong>descriptive</strong> and specific</li>
                                        <li>Follow the pattern: <strong>action + resource</strong></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer border-0 pt-0 px-4 pb-4">
                    <button type="button" class="btn btn-light border" wire:click="$set('showPermissionModal', false)">
                        <i class="bi bi-x-lg me-1"></i>
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-success px-4" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="savePermission">
                            <i class="bi bi-{{ $editingPermissionId ? 'check-lg' : 'plus-lg' }} me-1"></i>
                            {{ $editingPermissionId ? 'Update' : 'Create' }} Permission
                        </span>
                        <span wire:loading wire:target="savePermission">
                            <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                            {{ $editingPermissionId ? 'Updating...' : 'Creating...' }}
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
 