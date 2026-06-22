<!-- Users Modal -->
@if($showUserModal)
<div class="modal fade show" style="display: block; background: rgba(0,0,0,0.6); backdrop-filter: blur(4px);" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg">
            <!-- Modal Header -->
            <div class="modal-header border-0 pb-0">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-primary-soft p-2" style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center;">
                        <i class="bi bi-person-plus text-primary fa-lg"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0">
                            {{ $editingUserId ? 'Edit User' : 'Add New User' }}
                        </h5>
                        <p class="text-muted small mb-0">
                            {{ $editingUserId ? 'Update user details and permissions' : 'Create a new user account' }}
                        </p>
                    </div>
                </div>
                <button type="button" class="btn-close" wire:click="$set('showUserModal', false)" aria-label="Close"></button>
            </div>

            <form wire:submit.prevent="saveUser">
                <div class="modal-body px-4 py-3">
                    <!-- Name Field -->
                    <div class="mb-3">
                        <label for="userName" class="form-label fw-semibold">
                            <i class="bi bi-person me-1"></i>
                            Full Name <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-person text-muted"></i>
                            </span>
                            <input type="text" 
                                   id="userName"
                                   wire:model="userName" 
                                   class="form-control border-start-0 @error('userName') is-invalid @enderror"
                                   placeholder="Enter full name"
                                   autocomplete="off"
                                   autofocus>
                        </div>
                        @error('userName') 
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- Email Field -->
                    <div class="mb-3">
                        <label for="userEmail" class="form-label fw-semibold">
                            <i class="bi bi-envelope me-1"></i>
                            Email Address <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-envelope text-muted"></i>
                            </span>
                            <input type="email" 
                                   id="userEmail"
                                   wire:model="userEmail" 
                                   class="form-control border-start-0 @error('userEmail') is-invalid @enderror"
                                   placeholder="Enter email address"
                                   autocomplete="off">
                        </div>
                        @error('userEmail') 
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- Role Selection -->
                    <div class="mb-3">
                        <label for="userRole" class="form-label fw-semibold">
                            <i class="bi bi-shield me-1"></i>
                            Role <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-person-badge text-muted"></i>
                            </span>
                            <select id="userRole" 
                                    wire:model="userRole" 
                                    class="form-select border-start-0 @error('userRole') is-invalid @enderror">
                                <option value="">-- Select Role --</option>
                                @foreach($roles as $roleId => $roleName)
                                    <option value="{{ $roleId }}">{{ $roleName }}</option>
                                @endforeach
                            </select>
                        </div>
                        @error('userRole') 
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- Password Field -->
                    <div class="mb-3">
                        <label for="userPassword" class="form-label fw-semibold">
                            <i class="bi bi-lock me-1"></i>
                            {{ $editingUserId ? 'New Password' : 'Password' }}
                            @if(!$editingUserId)<span class="text-danger">*</span>@endif
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-lock text-muted"></i>
                            </span>
                            <input type="password" 
                                   id="userPassword"
                                   wire:model="userPassword" 
                                   class="form-control border-start-0 @error('userPassword') is-invalid @enderror"
                                   placeholder="{{ $editingUserId ? 'Enter new password (optional)' : 'Enter password' }}">
                            <button type="button" class="btn btn-outline-secondary" onclick="togglePasswordVisibility('userPassword')">
                                <i class="bi bi-eye" id="togglePasswordIcon"></i>
                            </button>
                        </div>
                        @error('userPassword') 
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        @if($editingUserId)
                            <small class="form-text text-muted mt-1">
                                <i class="bi bi-info-circle me-1"></i>
                                Leave blank to keep current password
                            </small>
                        @endif
                    </div>
                    
                    <!-- Password Confirmation -->
                    @if($userPassword || !$editingUserId)
                        <div class="mb-3">
                            <label for="userPasswordConfirmation" class="form-label fw-semibold">
                                <i class="bi bi-lock-fill me-1"></i>
                                Confirm Password
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="bi bi-lock-fill text-muted"></i>
                                </span>
                                <input type="password" 
                                       id="userPasswordConfirmation"
                                       wire:model="userPasswordConfirmation" 
                                       class="form-control border-start-0 @error('userPasswordConfirmation') is-invalid @enderror"
                                       placeholder="Confirm password">
                            </div>
                            @error('userPasswordConfirmation') 
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    @endif
                    
                    <!-- Active Status Switch -->
                    <div class="mb-3">
                        <div class="card bg-light border-0">
                            <div class="card-body py-2 px-3">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="bi bi-toggle-{{ $userIsActive ? 'on' : 'off' }} text-{{ $userIsActive ? 'success' : 'secondary' }} fs-4"></i>
                                        <div>
                                            <div class="fw-semibold">{{ $userIsActive ? 'Active' : 'Inactive' }}</div>
                                            <div class="small text-muted">User can {{ $userIsActive ? '' : 'not ' }}login to the system</div>
                                        </div>
                                    </div>
                                    <div class="form-check form-switch mb-0">
                                        <input type="checkbox" 
                                               wire:model="userIsActive" 
                                               class="form-check-input"
                                               id="userIsActive"
                                               style="width: 3rem; height: 1.5rem; cursor: pointer;">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer border-0 pt-0 px-4 pb-4">
                    <button type="button" class="btn btn-light border" wire:click="$set('showUserModal', false)">
                        <i class="bi bi-x-lg me-1"></i>
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-primary px-4" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="saveUser">
                            <i class="bi bi-{{ $editingUserId ? 'check-lg' : 'plus-lg' }} me-1"></i>
                            {{ $editingUserId ? 'Update' : 'Create' }} User
                        </span>
                        <span wire:loading wire:target="saveUser">
                            <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                            {{ $editingUserId ? 'Updating...' : 'Creating...' }}
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

