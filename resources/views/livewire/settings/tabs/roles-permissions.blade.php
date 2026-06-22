<div class="tab-pane active">
    <!-- ========== ROLES & PERMISSIONS ========== -->
    <div class="row g-3">
        <!-- Roles -->
        <div class="col-12 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0 d-flex flex-wrap align-items-center justify-content-between pt-3">
                    <h6 class="mb-0 fw-bold text-primary d-flex align-items-center gap-2">
                        <i class="bi bi-person-badge"></i> Roles
                    </h6>
                    <button wire:click="openRoleModal()" class="btn btn-primary btn-sm shadow-sm">
                        <i class="bi bi-plus-lg"></i> Add Role
                    </button>
                </div>
                <div class="card-body pt-0">
                    <!-- Mobile Card View -->
                    <div class="d-md-none">
                        @foreach($rolesList as $role)
                        <div class="border-bottom py-2">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="fw-semibold">{{ $role['name'] }}</div>
                                    <div class="text-muted small">
                                        {{ $role['permissions_count'] }} permissions · {{ $role['users_count'] }} users
                                    </div>
                                </div>
                                <div class="d-flex gap-1">
                                    <button wire:click="openRoleModal({{ $role['id'] }})" 
                                            class="btn btn-info-soft text-info btn-sm rounded-circle shadow-sm" title="Edit" style="width: 28px; height: 28px; padding: 0; font-size: 0.6rem;">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    @if($role['name'] !== 'Super Admin')
                                        <button wire:click="confirmDeleteRole({{ $role['id'] }})" 
                                                class="btn btn-danger-soft text-danger btn-sm rounded-circle shadow-sm" title="Delete" style="width: 28px; height: 28px; padding: 0; font-size: 0.6rem;">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    @endif
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
                                    <th class="small">Role</th>
                                    <th class="text-center small">Permissions</th>
                                    <th class="text-center small">Users</th>
                                    <th class="text-center small" width="100px">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($rolesList as $role)
                                    <tr>
                                        <td>
                                            <span class="fw-semibold">{{ $role['name'] }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-primary-soft text-primary rounded-pill px-3 py-2">{{ $role['permissions_count'] }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-info-soft text-info rounded-pill px-3 py-2">{{ $role['users_count'] }}</span>
                                        </td>
                                        <td>
                                            <div class="d-flex justify-content-center gap-1">
                                                <button wire:click="openRoleModal({{ $role['id'] }})" 
                                                        class="btn btn-info-soft text-info btn-sm rounded-circle shadow-sm" title="Edit" style="width: 32px; height: 32px; padding: 0; font-size: 0.7rem;">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                @if($role['name'] !== 'Super Admin')
                                                    <button wire:click="confirmDeleteRole({{ $role['id'] }})" 
                                                            class="btn btn-danger-soft text-danger btn-sm rounded-circle shadow-sm" title="Delete" style="width: 32px; height: 32px; padding: 0; font-size: 0.7rem;">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Permissions -->
        <div class="col-12 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0 d-flex flex-wrap align-items-center justify-content-between pt-3">
                    <h6 class="mb-0 fw-bold text-success d-flex align-items-center gap-2">
                        <i class="bi bi-shield-check"></i> Permissions
                    </h6>
                    <button wire:click="openPermissionModal()" class="btn btn-success btn-sm shadow-sm">
                        <i class="bi bi-plus-lg"></i> Add Permission
                    </button>
                </div>
                <div class="card-body pt-0">
                    <!-- Mobile Card View -->
                    <div class="d-md-none">
                        @foreach($permissionsList as $permission)
                        <div class="border-bottom py-2">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="fw-semibold">{{ $permission['name'] }}</div>
                                    <div class="text-muted small">{{ $permission['roles_count'] }} roles</div>
                                </div>
                                <div class="d-flex gap-1">
                                    <button wire:click="openPermissionModal({{ $permission['id'] }})" 
                                            class="btn btn-info-soft text-info btn-sm rounded-circle shadow-sm" title="Edit" style="width: 28px; height: 28px; padding: 0; font-size: 0.6rem;">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button wire:click="confirmDeletePermission({{ $permission['id'] }})" 
                                            class="btn btn-danger-soft text-danger btn-sm rounded-circle shadow-sm" title="Delete" style="width: 28px; height: 28px; padding: 0; font-size: 0.6rem;">
                                        <i class="bi bi-trash"></i>
                                    </button>
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
                                    <th class="small">Permission</th>
                                    <th class="text-center small">Roles</th>
                                    <th class="text-center small" width="100px">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($permissionsList as $permission)
                                    <tr>
                                        <td>
                                            <span class="fw-semibold">{{ $permission['name'] }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-primary-soft text-primary rounded-pill px-3 py-2">{{ $permission['roles_count'] }}</span>
                                        </td>
                                        <td>
                                            <div class="d-flex justify-content-center gap-1">
                                                <button wire:click="openPermissionModal({{ $permission['id'] }})" 
                                                        class="btn btn-info-soft text-info btn-sm rounded-circle shadow-sm" title="Edit" style="width: 32px; height: 32px; padding: 0; font-size: 0.7rem;">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <button wire:click="confirmDeletePermission({{ $permission['id'] }})" 
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
        </div>
    </div>

    <!-- ========== DELETE ROLE CONFIRMATION MODAL ========== -->
    @if($showDeleteRoleModal)
    <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-exclamation-triangle text-danger me-2"></i>
                        Delete Role
                    </h5>
                    <button type="button" class="btn-close" wire:click="closeAllModals"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-3">
                        <i class="bi bi-person-x text-danger" style="font-size: 3rem;"></i>
                    </div>
                    <p class="text-center mb-3">
                        Are you sure you want to delete the role:
                        <br>
                        <strong class="text-primary">{{ $deleteItemName }}</strong>
                    </p>
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-circle me-1"></i>
                        This action cannot be undone. Users with this role will lose their permissions.
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

    <!-- ========== DELETE PERMISSION CONFIRMATION MODAL ========== -->
    @if($showDeletePermissionModal)
    <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-exclamation-triangle text-danger me-2"></i>
                        Delete Permission
                    </h5>
                    <button type="button" class="btn-close" wire:click="closeAllModals"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-3">
                        <i class="bi bi-shield-x text-danger" style="font-size: 3rem;"></i>
                    </div>
                    <p class="text-center mb-3">
                        Are you sure you want to delete the permission:
                        <br>
                        <strong class="text-primary">{{ $deleteItemName }}</strong>
                    </p>
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-circle me-1"></i>
                        This action cannot be undone. Roles using this permission will lose access.
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