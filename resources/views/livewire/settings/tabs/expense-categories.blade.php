<div class="tab-pane active">
    <!-- ========== ADD/EDIT FORM ========== -->
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-header bg-transparent border-0 pt-3">
            <h6 class="mb-0 fw-bold text-primary d-flex align-items-center gap-2">
                <i class="bi bi-plus-circle"></i> {{ $editingExpenseCategoryId ? 'Edit' : 'Add' }} Expense Category
            </h6>
        </div>
        <div class="card-body pt-0">
            <div class="row g-2">
                <div class="col-12 col-sm-5 col-md-4">
                    <input type="text" 
                           wire:model="newExpenseCategory.name" 
                           placeholder="Category Name" 
                           class="form-control form-control-sm @error('newExpenseCategory.name') is-invalid @enderror">
                    @error('newExpenseCategory.name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-12 col-sm-5 col-md-5">
                    <input type="text" 
                           wire:model="newExpenseCategory.description" 
                           placeholder="Description" 
                           class="form-control form-control-sm">
                </div>
                <div class="col-6 col-sm-2 col-md-3">
                    <div class="form-check form-switch mt-1">
                        <input type="checkbox" 
                               wire:model="newExpenseCategory.is_active" 
                               class="form-check-input"
                               id="expenseCategoryActive"
                               style="width: 2.5rem; height: 1.25rem;">
                        <label class="form-check-label small" for="expenseCategoryActive">Active</label>
                    </div>
                </div>
            </div>
            <div class="mt-2">
                @if($editingExpenseCategoryId)
                    <button wire:click="updateExpenseCategory" class="btn btn-primary btn-sm shadow-sm">
                        <i class="bi bi-check-lg"></i> Update
                    </button>
                    <button wire:click="cancelEditExpenseCategory" class="btn btn-secondary btn-sm shadow-sm">
                        <i class="bi bi-x-lg"></i> Cancel
                    </button>
                @else
                    <button wire:click="addExpenseCategory" class="btn btn-success btn-sm shadow-sm">
                        <i class="bi bi-plus-lg"></i> Add Category
                    </button>
                @endif
            </div>
        </div>
    </div>

    <!-- ========== EXPENSE CATEGORIES LIST ========== -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent border-0 pt-3 d-flex flex-wrap align-items-center justify-content-between">
            <h6 class="mb-0 fw-bold text-secondary d-flex align-items-center gap-2">
                <i class="bi bi-list-ul"></i> Expense Categories
            </h6>
            <span class="badge bg-secondary-soft text-secondary rounded-pill">{{ count($expenseCategories) }}</span>
        </div>
        <div class="card-body pt-0">
            <!-- Mobile Card View -->
            <div class="d-md-none">
                @foreach($expenseCategories as $category)
                <div class="border-bottom py-2">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="fw-semibold">{{ $category['name'] }}</div>
                            <div class="text-muted small">{{ $category['description'] ?? '-' }}</div>
                        </div>
                        <div class="text-end">
                            @if($category['is_active'])
                                <span class="badge bg-success-soft text-success rounded-pill">Active</span>
                            @else
                                <span class="badge bg-secondary-soft text-secondary rounded-pill">Inactive</span>
                            @endif
                            <div class="mt-1">
                                <button wire:click="editExpenseCategory({{ $category['id'] }})" 
                                        class="btn btn-info-soft text-info btn-sm rounded-circle shadow-sm" title="Edit" style="width: 28px; height: 28px; padding: 0; font-size: 0.6rem;">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button wire:click="confirmDeleteExpenseCategory({{ $category['id'] }})" 
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
                            <th class="small">Description</th>
                            <th class="text-center small">Status</th>
                            <th class="text-center small" width="120px">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($expenseCategories as $category)
                            <tr>
                                <td>
                                    <span class="fw-semibold">{{ $category['name'] }}</span>
                                </td>
                                <td class="text-muted">{{ $category['description'] ?? '-' }}</td>
                                <td class="text-center">
                                    @if($category['is_active'])
                                        <span class="badge bg-success-soft text-success rounded-pill px-3 py-2">Active</span>
                                    @else
                                        <span class="badge bg-secondary-soft text-secondary rounded-pill px-3 py-2">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex justify-content-center gap-1">
                                        <button wire:click="editExpenseCategory({{ $category['id'] }})" 
                                                class="btn btn-info-soft text-info btn-sm rounded-circle shadow-sm" title="Edit" style="width: 32px; height: 32px; padding: 0; font-size: 0.7rem;">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button wire:click="confirmDeleteExpenseCategory({{ $category['id'] }})" 
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

    <!-- ========== DELETE EXPENSE CATEGORY CONFIRMATION MODAL ========== -->
    @if($showDeleteExpenseCategoryModal)
    <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-exclamation-triangle text-danger me-2"></i>
                        Delete Expense Category
                    </h5>
                    <button type="button" class="btn-close" wire:click="closeAllModals"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-3">
                        <i class="bi bi-folder-x text-danger" style="font-size: 3rem;"></i>
                    </div>
                    <p class="text-center mb-3">
                        Are you sure you want to delete the expense category:
                        <br>
                        <strong class="text-primary">{{ $deleteItemName }}</strong>
                    </p>
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-circle me-1"></i>
                        This action cannot be undone. All expenses associated with this category will be affected.
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