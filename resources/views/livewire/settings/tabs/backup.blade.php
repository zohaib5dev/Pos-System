<div class="tab-pane active">
    <!-- ========== ADD/EDIT FORM ========== -->
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-header bg-transparent border-0 pt-3">
            <button wire:click="$set('showBackupModal', true)" class="btn btn-primary">
                <i class="fas fa-database"></i> Create Backup
            </button>
        </div>
    </div>

    <!-- ========== BACKUPS LIST ========== -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent border-0 pt-3 d-flex flex-wrap align-items-center justify-content-between">
            <h6 class="mb-0 fw-bold text-secondary d-flex align-items-center gap-2">
                <i class="bi bi-list-ul"></i> Backups List
            </h6>
            <span class="badge bg-secondary-soft text-secondary rounded-pill">{{ count($backupFiles) }}</span>
        </div>
        <div class="card-body pt-0">
            
            <div class="d-md-none">
                @foreach($backupFiles as $file)
                <div class="border-bottom py-2">
                    <div class="">{{ $file['name'] }}
                        <span class="text-muted small">{{ $file['size_formatted'] }}</span>
                    </div>

                    <div class="text-center">
                        <span class="my-1 badge bg-success-soft text-success rounded-pill">{{$file['date_formatted']}}</span>

                        <div class="mt-1">
                            <button wire:click="downloadBackup('{{ $file['name'] }}')"
                                class="btn btn-info-soft text-info btn-sm rounded-circle shadow-sm" title="Download" style="width: 28px; height: 28px; padding: 0; font-size: 0.6rem;">
                                <i class="bi bi-download"></i>
                            </button>
                            <button wire:click="confirmRestore('{{ $file['name'] }}')"
                                class="btn btn-warning-soft text-warning btn-sm rounded-circle shadow-sm" title="Restore" style="width: 28px; height: 28px; padding: 0; font-size: 0.6rem;">
                                <i class="bi bi-arrow-counterclockwise"></i>
                            </button>
                            <button wire:click="confirmDeleteBackup('{{ $file['name'] }}')"
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
                            <th>File Name</th>
                            <th>Size</th>
                            <th>Date</th>
                            <th width="200px">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($backupFiles as $file)
                        <tr>
                            <td class="font-monospace">{{ $file['name'] }}</td>
                            <td>{{ $file['size_formatted'] }}</td>
                            <td>{{ $file['date_formatted'] }}</td>
                            <td>
                                <div class="d-flex justify-content-center gap-1">
                                    <button wire:click="downloadBackup('{{ $file['name'] }}')"
                                        class="btn btn-info-soft text-info btn-sm rounded-circle shadow-sm" title="Download" style="width: 32px; height: 32px; padding: 0; font-size: 0.7rem;">
                                        <i class="bi bi-download"></i>
                                    </button>
                                    <button wire:click="confirmRestore('{{ $file['name'] }}')"
                                        class="btn btn-warning-soft text-warning btn-sm rounded-circle shadow-sm" title="Restore" style="width: 32px; height: 32px; padding: 0; font-size: 0.7rem;">
                                        <i class="bi bi-arrow-counterclockwise"></i>
                                    </button>
                                    <button wire:click="confirmDeleteBackup('{{ $file['name'] }}')"
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

    <!-- ========== RESTORE CONFIRMATION MODAL ========== -->
    @if($showRestoreModal)
    <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-exclamation-triangle text-warning me-2"></i>
                        Restore Backup
                    </h5>
                    <button type="button" class="btn-close" wire:click="closeAllModals"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-3">
                        <i class="bi bi-arrow-counterclockwise text-warning" style="font-size: 3rem;"></i>
                    </div>
                    <p class="text-center mb-3">
                        Are you sure you want to restore the backup file:
                        <br>
                        <strong class="text-primary">{{ $restoreFile }}</strong>
                    </p>
                    <div class="alert alert-warning">
                        <i class="bi bi-info-circle me-1"></i>
                        This action will replace your current data with the backup data.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeAllModals">
                        <i class="bi bi-x-circle me-1"></i> Cancel
                    </button>
                    <button type="button" class="btn btn-warning" wire:click="restoreBackup('{{ $restoreFile }}')">
                        <i class="bi bi-arrow-counterclockwise me-1"></i> Yes, Restore
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- ========== DELETE CONFIRMATION MODAL ========== -->
    @if($showDeleteModal)
    <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-exclamation-triangle text-danger me-2"></i>
                        Delete Backup
                    </h5>
                    <button type="button" class="btn-close" wire:click="closeAllModals"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-3">
                        <i class="bi bi-trash text-danger" style="font-size: 3rem;"></i>
                    </div>
                    <p class="text-center mb-3">
                        Are you sure you want to delete the backup file:
                        <br>
                        <strong class="text-primary">{{ $deleteFile }}</strong>
                    </p>
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-circle me-1"></i>
                        This action cannot be undone!
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeAllModals">
                        <i class="bi bi-x-circle me-1"></i> Cancel
                    </button>
                    <button type="button" class="btn btn-danger" wire:click="deleteBackup('{{ $deleteFile }}')">
                        <i class="bi bi-trash me-1"></i> Yes, Delete
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>