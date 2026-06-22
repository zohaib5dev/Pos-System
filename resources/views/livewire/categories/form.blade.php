<div>
    <!-- ========== PAGE HEADER ========== -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <div>
            <h4 class="mb-0 fw-bold d-flex align-items-center gap-2">
                <i class="bi bi-tag"></i> 
                {{ $categoryId ? 'Edit Category' : 'Add New Category' }}
            </h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('categories.index') }}" class="text-decoration-none">Categories</a></li>
                    <li class="breadcrumb-item active">{{ $categoryId ? 'Edit' : 'Add' }}</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('categories.index') }}" class="btn btn-secondary btn-sm shadow-sm">
            <i class="bi bi-arrow-left"></i> <span class="d-none d-sm-inline">Back to List</span>
            <span class="d-sm-none">Back</span>
        </a>
    </div>

    <!-- ========== MAIN FORM CARD ========== -->
    <div class="card border-0 shadow-lg">
        <div class="card-header bg-transparent border-0 d-flex flex-wrap align-items-center justify-content-between pt-3">
            <h5 class="mb-0 fw-bold d-flex align-items-center gap-2">
                <i class="bi bi-pencil-square"></i>
                {{ $categoryId ? 'Edit Category' : 'Create New Category' }}
            </h5>
            <span class="badge bg-primary-soft text-primary rounded-pill">
                {{ $categoryId ? 'Editing' : 'New' }}
            </span>
        </div>

        <div class="card-body pt-0">
            <form wire:submit="saveCategory">
                <!-- ========== CATEGORY DETAILS ========== -->
                <div class="row g-3">
                    <div class="col-12">
                        <h6 class="fw-bold mb-0 d-flex align-items-center gap-2">
                            <i class="bi bi-info-circle"></i> Category Information
                        </h6>
                        <hr class="mt-1">
                    </div>

                    <!-- Category Name -->
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold small">Category Name <span class="text-danger">*</span></label>
                        <input type="text" 
                               id="name"
                               wire:model.live="name" 
                               class="form-control @error('name') is-invalid @enderror"
                               placeholder="Enter category name"
                               x-data
                               @input="$wire.generateSlugFromName($event.target.value)">
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <!-- Slug -->
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold small">Slug <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="text" 
                                   id="slug"
                                   wire:model="slug" 
                                   class="form-control @error('slug') is-invalid @enderror"
                                   placeholder="category-url-slug"
                                   x-data
                                   @input="
                                       const slug = $event.target.value
                                           .toLowerCase()
                                           .replace(/[^a-z0-9]+/g, '-')
                                           .replace(/^-+|-+$/g, '');
                                       if (slug !== $event.target.value) {
                                           $event.target.value = slug;
                                       }
                                       $wire.set('slug', slug, true);
                                   ">
                            <button type="button" 
                                    class="btn btn-outline-secondary"
                                    x-data
                                    @click="
                                        const name = document.getElementById('name')?.value || '';
                                        if (name) {
                                            const slug = name
                                                .toLowerCase()
                                                .replace(/[^a-z0-9]+/g, '-')
                                                .replace(/^-+|-+$/g, '');
                                            $wire.set('slug', slug, true);
                                        }
                                    ">
                                <i class="bi bi-arrow-repeat"></i>
                            </button>
                            @error('slug') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <small class="text-muted">URL-friendly version of the name</small>
                    </div>

                    <!-- Sort Order -->
                    <div class="col-12 col-md-4">
                        <label class="form-label fw-semibold small">Sort Order</label>
                        <input type="number" 
                               wire:model="sort_order" 
                               class="form-control @error('sort_order') is-invalid @enderror"
                               placeholder="0"
                               min="0">
                        @error('sort_order') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        <small class="text-muted">Lower numbers appear first</small>
                    </div>

                    <!-- Status -->
                    <div class="col-12 col-md-8">
                        <div class="form-check form-switch mt-3 mt-md-4">
                            <input type="checkbox" 
                                   wire:model="is_active" 
                                   class="form-check-input"
                                   id="is_active"
                                   style="width: 3rem; height: 1.5rem;">
                            <label class="form-check-label fw-semibold" for="is_active">
                                <span class="{{ $is_active ? 'text-success' : 'text-muted' }}">
                                    <i class="bi {{ $is_active ? 'bi-check-circle-fill' : 'bi-x-circle-fill' }} me-1"></i>
                                    {{ $is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </label>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="col-12">
                        <label class="form-label fw-semibold small">Description</label>
                        <textarea wire:model="description" 
                                  rows="4" 
                                  class="form-control @error('description') is-invalid @enderror"
                                  placeholder="Enter category description"></textarea>
                        @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <!-- ========== FORM ACTIONS ========== -->
                <div class="row g-2 mt-4 pt-3 border-top">
                    <div class="col-12 d-flex flex-wrap gap-2">
                        <a href="{{ route('categories.index') }}" class="btn btn-secondary shadow-sm">
                            <i class="bi bi-x-lg"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary shadow-sm">
                            <i class="bi bi-check-lg"></i> 
                            {{ $categoryId ? 'Update Category' : 'Create Category' }}
                        </button>
                     
                    </div>
                </div>
            </form>
        </div>
    </div>

  
</div>