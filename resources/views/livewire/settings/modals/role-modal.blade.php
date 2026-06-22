<!-- Roles Modal -->
@if($showRoleModal)
<div class="modal fade show" style="display: block; background: rgba(0,0,0,0.6); backdrop-filter: blur(4px);" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content border-0 shadow-lg">
            <!-- Modal Header -->
            <div class="modal-header border-0 pb-0">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-primary-soft p-2" style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center;">
                        <i class="bi bi-person-badge text-primary fa-lg"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0">
                            {{ $editingRoleId ? 'Edit Role' : 'Create New Role' }}
                        </h5>
                        <p class="text-muted small mb-0">
                            {{ $editingRoleId ? 'Update role details and permissions' : 'Define a new role with specific permissions' }}
                        </p>
                    </div>
                </div>
                <button type="button" class="btn-close" wire:click="$set('showRoleModal', false)" aria-label="Close"></button>
            </div>

            <form wire:submit.prevent="saveRole">
                <div class="modal-body px-4 py-3">
                    <!-- Role Name Field -->
                    <div class="mb-4">
                        <label for="roleName" class="form-label fw-semibold">
                            <i class="bi bi-tag me-1"></i>
                            Role Name <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-person text-muted"></i>
                            </span>
                            <input type="text"
                                id="roleName"
                                wire:model="roleName"
                                class="form-control border-start-0 @error('roleName') is-invalid @enderror"
                                placeholder="Enter role name (e.g., Admin, Manager, Staff)"
                                autocomplete="off"
                                autofocus>
                        </div>
                        @error('roleName')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted mt-1 d-flex align-items-center gap-1">
                            <i class="bi bi-info-circle"></i>
                            Choose a descriptive name for the role. Use title case for better readability.
                        </small>
                    </div>

                    <!-- Permissions Section -->
                    <div class="form-group">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <label class="form-label fw-semibold mb-0">
                                <i class="bi bi-shield-check me-1"></i>
                                Permissions
                                <span class="badge bg-primary-soft text-primary ms-2">
                                    {{ count($rolePermissions) }} selected
                                </span>
                            </label>
                            <div class="d-flex gap-1">
                                <button type="button" class="btn btn-sm btn-outline-primary"
                                    wire:click="$set('rolePermissions', {{ json_encode(array_column($permissionsList, 'name')) }})">
                                    <i class="bi bi-check-all me-1"></i> All
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary"
                                    wire:click="$set('rolePermissions', [])">
                                    <i class="bi bi-x-circle me-1"></i> None
                                </button>
                            </div>
                        </div>

                        <!-- Permissions List -->
                        <div class="border rounded-3 p-3" style="max-height: 320px; overflow-y: auto; background: var(--bs-body-bg);">
                            @if(count($permissionsList) > 0)
                                <div class="row g-2">
                                    @foreach($permissionsList as $permission)
                                    <div class="col-12 col-sm-6 col-lg-4">
                                        <div class="form-check permission-item">
                                            <input type="checkbox"
                                                wire:model="rolePermissions"
                                                value="{{ $permission['name'] }}"
                                                class="form-check-input @error('rolePermissions') is-invalid @enderror"
                                                id="perm_{{ Str::slug($permission['name']) }}">
                                            <label class="form-check-label" for="perm_{{ Str::slug($permission['name']) }}">
                                                <i class="bi bi-shield text-muted me-1"></i>
                                                {{ $permission['name'] }}
                                            </label>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <i class="bi bi-shield-slash text-muted" style="font-size: 2rem;"></i>
                                    <p class="text-muted mt-2 mb-0">No permissions found.</p>
                                    <button type="button" class="btn btn-sm btn-link" 
                                        wire:click="$set('activeTab', 'roles')" 
                                        wire:click="$set('showPermissionModal', true)">
                                        <i class="bi bi-plus-circle me-1"></i> Create a permission
                                    </button>
                                </div>
                            @endif
                        </div>
                        @error('rolePermissions')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer border-0 pt-0 px-4 pb-4">
                    <button type="button" class="btn btn-light border" wire:click="$set('showRoleModal', false)">
                        <i class="bi bi-x-lg me-1"></i>
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-primary px-4" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="saveRole">
                            <i class="bi bi-{{ $editingRoleId ? 'check-lg' : 'plus-lg' }} me-1"></i>
                            {{ $editingRoleId ? 'Update' : 'Create' }} Role
                        </span>
                        <span wire:loading wire:target="saveRole">
                            <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                            {{ $editingRoleId ? 'Updating...' : 'Creating...' }}
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

 