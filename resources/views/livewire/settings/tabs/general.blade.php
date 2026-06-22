<div class="tab-pane active">
    <form wire:submit="saveBusinessSettings">
        <!-- ========== BUSINESS INFORMATION ========== -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-transparent border-0 pt-3">
                <h6 class="mb-0 fw-bold text-primary d-flex align-items-center gap-2">
                    <i class="bi bi-building"></i> Business Information
                </h6>
            </div>
            <div class="card-body pt-0">
                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold small">Business Name <span class="text-danger">*</span></label>
                        <input type="text"
                            wire:model="business_name"
                            class="form-control @error('business_name') is-invalid @enderror">
                        @error('business_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold small">Business Email</label>
                        <input type="email"
                            wire:model="business_email"
                            class="form-control @error('business_email') is-invalid @enderror">
                        @error('business_email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="form-label fw-semibold small">Business Phone</label>
                        <input type="text"
                            wire:model="business_phone"
                            class="form-control">
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="form-label fw-semibold small">Tax Number / VAT</label>
                        <input type="text"
                            wire:model="tax_number"
                            class="form-control"
                            placeholder="e.g., VAT-123456789">
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="form-label fw-semibold small">Registration Number</label>
                        <input type="text"
                            wire:model="registration_number"
                            class="form-control"
                            placeholder="e.g., CRN-2024-001">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold small">Business Address</label>
                        <textarea wire:model="business_address"
                            rows="3"
                            class="form-control"></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========== LOCALIZATION ========== -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-transparent border-0 pt-3">
                <h6 class="mb-0 fw-bold text-success d-flex align-items-center gap-2">
                    <i class="bi bi-globe2"></i> Localization
                </h6>
            </div>
            <div class="card-body pt-0">
                <div class="row g-3">
                    <div class="col-12 col-md-4">
                        <label class="form-label fw-semibold small">Timezone</label>
                        <select wire:model="timezone" class="form-select">
                            <option value="UTC">UTC</option>
                            <option value="America/New_York">Eastern Time</option>
                            <option value="America/Chicago">Central Time</option>
                            <option value="America/Denver">Mountain Time</option>
                            <option value="America/Los_Angeles">Pacific Time</option>
                            <option value="Europe/London">London</option>
                            <option value="Europe/Paris">Paris</option>
                            <option value="Asia/Tokyo">Tokyo</option>
                            <option value="Asia/Dubai">Dubai</option>
                            <option value="Asia/Singapore">Singapore</option>
                            <option value="Australia/Sydney">Sydney</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="form-label fw-semibold small">Date Format</label>
                        <select wire:model="date_format" class="form-select">
                            <option value="Y-m-d">2024-12-31</option>
                            <option value="m/d/Y">12/31/2024</option>
                            <option value="d/m/Y">31/12/2024</option>
                            <option value="d M Y">31 Dec 2024</option>
                            <option value="M d, Y">Dec 31, 2024</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-4">
    <label class="form-label fw-semibold small">Time Format</label>
    <select wire:model="time_format" class="form-select">
        <option value="H:i">14:30 (24-hour)</option>
        <option value="H:i:s">14:30:00 (24-hour with seconds)</option>
        <option value="h:i A">02:30 PM (12-hour)</option>
        <option value="h:i:s A">02:30:00 PM (12-hour with seconds)</option>
        <option value="g:i A">2:30 PM (12-hour no leading zero)</option>
        <option value="g:i:s A">2:30:00 PM (12-hour with seconds, no leading zero)</option>
    </select>
</div>
                    <div class="col-12 col-md-4">
                        <label class="form-label fw-semibold small">Currency Code <span class="text-danger">*</span></label>
                        <select wire:model="currency_code" class="form-select @error('currency_code') is-invalid @enderror">
                            <option value="USD">USD - US Dollar</option>
                            <option value="EUR">EUR - Euro</option>
                            <option value="GBP">GBP - British Pound</option>
                            <option value="JPY">JPY - Japanese Yen</option>
                            <option value="CAD">CAD - Canadian Dollar</option>
                            <option value="AUD">AUD - Australian Dollar</option>
                            <option value="SGD">SGD - Singapore Dollar</option>
                            <option value="MYR">MYR - Malaysian Ringgit</option>
                            <option value="IDR">IDR - Indonesian Rupiah</option>
                            <option value="PHP">PHP - Philippine Peso</option>
                        </select>
                        @error('currency_code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="form-label fw-semibold small">Currency Symbol <span class="text-danger">*</span></label>
                        <input type="text"
                            wire:model="currency_symbol"
                            class="form-control @error('currency_symbol') is-invalid @enderror"
                            placeholder="$">
                        @error('currency_symbol') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- ========== RECEIPT & LOGO ========== -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-transparent border-0 pt-3">
                <h6 class="mb-0 fw-bold text-info d-flex align-items-center gap-2">
                    <i class="bi bi-receipt"></i> Receipt & Branding
                </h6>
            </div>
            <div class="card-body pt-0">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-semibold small">Receipt Footer</label>
                        <input type="text"
                            wire:model="receipt_footer"
                            class="form-control"
                            placeholder="Receipt footer text">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold small">Business Logo</label>

                        <!-- Current Logo Preview -->
                        @if($existing_logo)
                        <div class="bg-light-soft rounded-3 p-3 mb-3">
                            <div class="d-flex flex-wrap align-items-center gap-3">
                                <img src="{{ getLogo() }}"
                                    class="rounded-3 shadow-sm"
                                    style="max-height: 80px; max-width: 200px; object-fit: contain;"
                                    alt="Current Logo">
                                <div>
                                    <p class="mb-0 fw-semibold">Current Logo</p>
                                    <small class="text-muted">Upload a new logo to replace</small>
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
                            <p class="text-muted small mb-0">PNG, JPG, GIF, SVG up to 1MB</p>
                            <input type="file" 
                                   x-ref="fileInput"
                                   wire:model="logo" 
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
                                     style="height: 80px; width: auto; max-width: 200px; object-fit: contain;">
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- ========== FORM ACTIONS ========== -->
        <div class="row g-2 mt-3">
            <div class="col-12">
                <button type="submit" class="btn btn-primary shadow-sm">
                    <i class="bi bi-check-lg"></i> Save Settings
                </button>
            </div>
        </div>
    </form>
</div>

 