<div class="tab-pane active">
    <!-- ========== ADD USER BUTTON ========== -->
    <div class="mb-3">
        <button wire:click="openUserModal()" class="btn btn-primary btn-sm shadow-sm">
            <i class="bi bi-plus-lg"></i> Add User
        </button>
    </div>

    <!-- ========== USERS LIST ========== -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent border-0 pt-3 d-flex flex-wrap align-items-center justify-content-between">
            <h6 class="mb-0 fw-bold text-secondary d-flex align-items-center gap-2">
                <i class="bi bi-people"></i> Users
            </h6>
            <span class="badge bg-secondary-soft text-secondary rounded-pill">{{ count($users) }}</span>
        </div>
        <div class="card-body pt-0">
            <!-- Mobile Card View -->
            <div class="d-md-none">
                @foreach($users as $user)
                <div class="border-bottom py-2">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="fw-semibold">{{ $user['name'] }}</div>
                            <div class="text-muted small">{{ $user['email'] }}</div>
                            <div class="d-flex gap-1 mt-1">
                                <span class="badge bg-warning-soft text-warning rounded-pill px-2 py-0" style="font-size: 0.55rem;">{{ $user['role'] }}</span>
                                @if($user['is_active'])
                                    <span class="badge bg-success-soft text-success rounded-pill px-2 py-0" style="font-size: 0.55rem;">Active</span>
                                @else
                                    <span class="badge bg-danger-soft text-danger rounded-pill px-2 py-0" style="font-size: 0.55rem;">Inactive</span>
                                @endif
                            </div>
                        </div>
                        <div class="text-end">
                            <div class="text-muted small">{{ $user['last_login'] }}</div>
                            <div class="mt-1">
                                <button wire:click="openUserModal({{ $user['id'] }})" 
                                        class="btn btn-info-soft text-info btn-sm rounded-circle shadow-sm" title="Edit" style="width: 28px; height: 28px; padding: 0; font-size: 0.6rem;">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                @if($user['id'] !== auth()->id())
                                    <button wire:click="confirmDeleteUser({{ $user['id'] }})" 
                                            class="btn btn-danger-soft text-danger btn-sm rounded-circle shadow-sm" title="Delete" style="width: 28px; height: 28px; padding: 0; font-size: 0.6rem;">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                @endif
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
                            <th class="small">Email</th>
                            <th class="text-center small">Role</th>
                            <th class="text-center small">Status</th>
                            <th class="text-center small">Last Login</th>
                            <th class="text-center small" width="120px">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                            <tr>
                                <td>
                                    <span class="fw-semibold">{{ $user['name'] }}</span>
                                </td>
                                <td>{{ $user['email'] }}</td>
                                <td class="text-center">
                                    <span class="badge bg-warning-soft text-warning rounded-pill px-3 py-2">{{ $user['role'] }}</span>
                                </td>
                                <td class="text-center">
                                    @if($user['is_active'])
                                        <span class="badge bg-success-soft text-success rounded-pill px-3 py-2">Active</span>
                                    @else
                                        <span class="badge bg-danger-soft text-danger rounded-pill px-3 py-2">Inactive</span>
                                    @endif
                                </td>
                                <td class="text-center text-muted">{{ $user['last_login'] }}</td>
                                <td>
                                    <div class="d-flex justify-content-center gap-1">
                                        <button wire:click="openUserModal({{ $user['id'] }})" 
                                                class="btn btn-info-soft text-info btn-sm rounded-circle shadow-sm" title="Edit" style="width: 32px; height: 32px; padding: 0; font-size: 0.7rem;">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        @if($user['id'] !== auth()->id())
                                            <button wire:click="confirmDeleteUser({{ $user['id'] }})" 
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

    <!-- ========== DELETE USER CONFIRMATION MODAL ========== -->
    @if($showDeleteUserModal)
    <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-exclamation-triangle text-danger me-2"></i>
                        Delete User
                    </h5>
                    <button type="button" class="btn-close" wire:click="closeAllModals"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-3">
                        <i class="bi bi-person-x text-danger" style="font-size: 3rem;"></i>
                    </div>
                    <p class="text-center mb-3">
                        Are you sure you want to delete the user:
                        <br>
                        <strong class="text-primary">{{ $deleteItemName }}</strong>
                        <br>
                        <span class="text-muted">{{ $users[array_search($deleteItemId, array_column($users, 'id'))]['email'] ?? '' }}</span>
                    </p>
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-circle me-1"></i>
                        This action cannot be undone. The user will lose access to the system.
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