<!-- Backup Modal -->
@if($showBackupModal)
<div class="modal fade show" style="display: block; background: rgba(0,0,0,0.6); backdrop-filter: blur(4px);" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content border-0 shadow-lg">
            <!-- Modal Header -->
            <div class="modal-header border-0 pb-0">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-primary-soft p-2" style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-database text-primary fa-lg"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0">Create Database Backup</h5>
                        <p class="text-muted small mb-0">Secure your data with a full database backup</p>
                    </div>
                </div>
                <button type="button" class="btn-close" wire:click="$set('showBackupModal', false)" aria-label="Close"></button>
            </div>

            <form wire:submit.prevent="createBackup">
                <div class="modal-body px-4 py-3">
                    <!-- File Name Input -->
                    <div class="mb-4">
                        <label for="backupFileName" class="form-label fw-semibold">
                            <i class="fas fa-file-signature me-1"></i>
                            File Name <span class="text-muted fw-normal">(Optional)</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="fas fa-file text-muted"></i>
                            </span>
                            <input type="text"
                                id="backupFileName"
                                wire:model="backupFileName"
                                class="form-control border-start-0 @error('backupFileName') is-invalid @enderror"
                                placeholder="backup-2024-01-01"
                                autocomplete="off">
                            <span class="input-group-text bg-light">.sql</span>
                        </div>
                        @error('backupFileName') 
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted mt-1 d-flex align-items-center gap-1">
                            <i class="fas fa-info-circle"></i>
                            Only letters, numbers, hyphens (-), and underscores (_) allowed. 
                            Leave empty for auto-generated name.
                        </small>
                    </div>

                    <!-- Backup Information Cards -->
                    <div class="row g-2 mb-3">
                        <div class="col-12 col-sm-6">
                            <div class="card bg-primary-soft border-0 h-100">
                                <div class="card-body py-2 px-3">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="fas fa-server text-primary"></i>
                                        <div>
                                            <div class="small text-muted">Database</div>
                                            <div class="fw-semibold small">{{ config('database.connections.mysql.database') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <div class="card bg-info-soft border-0 h-100">
                                <div class="card-body py-2 px-3">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="fas fa-table text-info"></i>
                                        <div>
                                            <div class="small text-muted">Tables</div>
                                            <div class="fw-semibold small">All Tables</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Warning Alert -->
                    <div class="alert alert-warning d-flex align-items-start gap-3 mb-0" style="border-left: 4px solid #ffc107;">
                        <i class="fas fa-exclamation-triangle text-warning mt-1"></i>
                        <div>
                            <h6 class="alert-heading mb-1 fw-semibold">Before You Proceed</h6>
                            <p class="mb-0 small">
                                This process may take a few moments depending on your database size. 
                                The backup will be stored in the <strong>app/backups</strong> directory.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer border-0 pt-0 px-4 pb-4">
                    <button type="button" class="btn btn-light border" wire:click="$set('showBackupModal', false)">
                        <i class="fas fa-times me-1"></i>
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-primary px-4" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="createBackup">
                            <i class="fas fa-database me-1"></i>
                            Create Backup
                        </span>
                        <span wire:loading wire:target="createBackup">
                            <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                            Creating...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
 