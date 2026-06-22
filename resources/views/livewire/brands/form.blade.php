<div>
    <!-- ========== PAGE HEADER ========== -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <div>
            <h4 class="mb-0 fw-bold d-flex align-items-center gap-2">
                <i class="bi bi-building"></i> 
                {{ $brandId ? 'Edit Brand' : 'Add New Brand' }}
            </h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('brands.index') }}" class="text-decoration-none">Brands</a></li>
                    <li class="breadcrumb-item active">{{ $brandId ? 'Edit' : 'Add' }}</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('brands.index') }}" class="btn btn-secondary btn-sm shadow-sm">
            <i class="bi bi-arrow-left"></i> <span class="d-none d-sm-inline">Back to List</span>
            <span class="d-sm-none">Back</span>
        </a>
    </div>

    <!-- ========== MAIN FORM CARD ========== -->
    <div class="card border-0 shadow-lg">
        <div class="card-header bg-transparent border-0 d-flex flex-wrap align-items-center justify-content-between pt-3">
            <h5 class="mb-0 fw-bold d-flex align-items-center gap-2">
                <i class="bi bi-pencil-square"></i>
                {{ $brandId ? 'Edit Brand' : 'Create New Brand' }}
            </h5>
            <span class="badge bg-primary-soft text-primary rounded-pill">
                {{ $brandId ? 'Editing' : 'New' }}
            </span>
        </div>

        <div class="card-body pt-0">
            <form wire:submit="saveBrand" enctype="multipart/form-data">
                <!-- ========== BRAND DETAILS ========== -->
                <div class="row g-3">
                    <div class="col-12">
                        <h6 class="fw-bold mb-0 d-flex align-items-center gap-2">
                            <i class="bi bi-info-circle"></i> Brand Information
                        </h6>
                        <hr class="mt-1">
                    </div>

                    <!-- Brand Name -->
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold small">Brand Name <span class="text-danger">*</span></label>
                        <input type="text" 
                               id="name"
                               wire:model.live="name" 
                               class="form-control @error('name') is-invalid @enderror"
                               placeholder="Enter brand name"
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
                                   placeholder="brand-url-slug"
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

                    <!-- Logo Upload -->
                    <div class="col-12">
                        <label class="form-label fw-semibold small">Brand Logo</label>
                        
                        <!-- Existing Logo -->
                        @if($existing_logo)
                        <div class="mb-3">
                            <div class="d-flex align-items-center gap-3">
                                <img src="{{ Storage::url($existing_logo) }}" 
                                     class="rounded-3 shadow-sm"
                                     style="width: 100px; height: 100px; object-fit: cover;">
                                <div>
                                    <span class="badge bg-success-soft text-success">Current Logo</span>
                                    <button type="button" 
                                            wire:click="removeLogo"
                                            wire:confirm="Are you sure you want to remove this logo?"
                                            class="btn btn-danger btn-sm ms-2 shadow-sm">
                                        <i class="bi bi-x-lg"></i> Remove
                                    </button>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Upload New Logo -->
                        <div class="dropzone-wrapper border-2 border-dashed rounded-3 p-4 text-center @error('logo') border-danger @enderror" 
                             style="border-color: var(--bs-border-color); background: var(--bs-tertiary-bg);"
                             x-data="{ dragging: false }"
                             @dragover.prevent="dragging = true"
                             @dragleave.prevent="dragging = false"
                             @drop.prevent="dragging = false; $refs.fileInput.files = $event.dataTransfer.files; $refs.fileInput.dispatchEvent(new Event('change'))">
                            <i class="bi bi-cloud-upload fs-1 d-block mb-2 text-muted"></i>
                            <p class="mb-1 small">
                                <span class="fw-semibold text-primary">Click to upload</span> or drag and drop
                            </p>
                            <p class="text-muted small mb-0">PNG, JPG, GIF up to 1MB</p>
                            <input type="file" 
                                   x-ref="fileInput"
                                   wire:model.live="logo" 
                                   accept="image/*" 
                                   class="d-none"
                                   id="logo">
                            <button type="button" class="btn btn-primary btn-sm mt-2 shadow-sm" @click="$refs.fileInput.click()">
                                <i class="bi bi-upload"></i> Choose File
                            </button>
                        </div>
                        @error('logo') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        
                        <!-- Preview -->
                        @if($logo && !$errors->has('logo'))
                        <div class="mt-3">
                            <label class="fw-semibold small">Preview</label>
                            <div class="position-relative d-inline-block">
                                <img src="{{ $logo->temporaryUrl() }}" 
                                     class="rounded-3 shadow-sm"
                                     style="width: 120px; height: 120px; object-fit: cover;">
                                <button type="button" 
                                        wire:click="removeTempLogo" 
                                        class="btn btn-danger btn-sm position-absolute top-0 end-0 rounded-circle shadow-sm"
                                        style="width: 28px; height: 28px; padding: 0; font-size: 0.7rem;">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </div>
                        </div>
                        @endif
                    </div>

                    <!-- Description -->
                    <div class="col-12">
                        <label class="form-label fw-semibold small">Description</label>
                        <textarea wire:model="description" 
                                  rows="4" 
                                  class="form-control @error('description') is-invalid @enderror"
                                  placeholder="Enter brand description"></textarea>
                        @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <!-- Status -->
                    <div class="col-12">
                        <div class="form-check form-switch">
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
                </div>

                <!-- ========== FORM ACTIONS ========== -->
                <div class="row g-2 mt-4 pt-3 border-top">
                    <div class="col-12 d-flex flex-wrap gap-2">
                        <a href="{{ route('brands.index') }}" class="btn btn-secondary shadow-sm">
                            <i class="bi bi-x-lg"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary shadow-sm">
                            <i class="bi bi-check-lg"></i> 
                            {{ $brandId ? 'Update Brand' : 'Create Brand' }}
                        </button>
                 
                    </div>
                </div>
            </form>
        </div>
    </div>

 
</div>