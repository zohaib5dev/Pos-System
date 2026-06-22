@if($showPaymentModal)
<div class="modal fade show" id="paymentModal" style="display: block; background: rgba(0,0,0,0.6); backdrop-filter: blur(4px);" tabindex="-1" role="dialog" aria-modal="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg">
            <!-- Modal Header -->
            <div class="modal-header border-0 pb-0">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-success-soft p-2" style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-money-bill-wave text-success fa-lg"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0">
                            Process Payment
                        </h5>
                        <p class="text-muted small mb-0">
                            Complete the payment transaction securely
                        </p>
                    </div>
                </div>
                <button type="button" class="btn-close" wire:click="$set('showPaymentModal', false)" aria-label="Close"></button>
            </div>

            <!-- Modal Body -->
            <form wire:submit.prevent="processPayment">
                <div class="modal-body px-4 py-3">
                    <!-- Payment Amount -->
                    <div class="mb-3">
                        <label for="paymentAmount" class="form-label fw-semibold">
                            <i class="fas fa-dollar-sign me-1"></i>
                            Payment Amount <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="fas fa-coins text-muted"></i>
                            </span>
                            <input type="number" 
                                   id="paymentAmount"
                                   wire:model="paymentAmount" 
                                   step="0.01" 
                                   min="0.01"
                                   class="form-control border-start-0 @error('paymentAmount') is-invalid @enderror"
                                   placeholder="0.00"
                                   autofocus>
                        </div>
                        @error('paymentAmount') 
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted mt-1">
                            <i class="fas fa-info-circle me-1"></i>
                            Enter the exact amount to be charged
                        </small>
                    </div>
                    
                    <!-- Payment Method -->
                    <div class="mb-3">
                        <label for="paymentMethod" class="form-label fw-semibold">
                            <i class="fas fa-credit-card me-1"></i>
                            Payment Method <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="fas fa-wallet text-muted"></i>
                            </span>
                            <select id="paymentMethod" 
                                    wire:model="paymentMethod" 
                                    class="form-select border-start-0 @error('paymentMethod') is-invalid @enderror">
                                <option value="">-- Select Payment Method --</option>
                                <option value="cash">💵 Cash</option>
                                <option value="credit-card">💳 Credit/Debit Card</option>
                                <option value="bank">🏦 Bank Transfer</option>
                                <option value="mobile">📱 Mobile Payment</option>
                                <option value="check">📝 Check</option>
                            </select>
                        </div>
                        @error('paymentMethod') 
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- Reference Number -->
                    <div class="mb-3">
                        <label for="paymentReference" class="form-label fw-semibold">
                            <i class="fas fa-hashtag me-1"></i>
                            Reference Number
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="fas fa-tag text-muted"></i>
                            </span>
                            <input type="text" 
                                   id="paymentReference"
                                   wire:model="paymentReference" 
                                   class="form-control border-start-0"
                                   placeholder="e.g., Transaction ID, Check #">
                        </div>
                        <small class="form-text text-muted mt-1">
                            <i class="fas fa-info-circle me-1"></i>
                            Optional reference for tracking this payment
                        </small>
                    </div>
                    
                    <!-- Notes -->
                    <div class="mb-0">
                        <label for="paymentNotes" class="form-label fw-semibold">
                            <i class="fas fa-sticky-note me-1"></i>
                            Notes
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="fas fa-pen text-muted"></i>
                            </span>
                            <textarea id="paymentNotes"
                                      wire:model="paymentNotes" 
                                      rows="2" 
                                      class="form-control border-start-0"
                                      placeholder="Optional notes about this payment..."></textarea>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer border-0 pt-0 px-4 pb-4">
                    <button type="button" class="btn btn-light border" wire:click="$set('showPaymentModal', false)">
                        <i class="fas fa-times me-1"></i>
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-success px-4" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="processPayment">
                            <i class="fas fa-check me-1"></i>
                            Process Payment
                        </span>
                        <span wire:loading wire:target="processPayment">
                            <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                            Processing...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<script>
    // Close modal on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const modal = document.getElementById('paymentModal');
            if (modal && modal.style.display !== 'none') {
                @this.set('showPaymentModal', false);
            }
        }
    });

    // Prevent body scroll when modal is open
    document.addEventListener('livewire:load', function() {
        Livewire.on('showPaymentModal', function() {
            document.body.style.overflow = 'hidden';
        });
        Livewire.on('hidePaymentModal', function() {
            document.body.style.overflow = '';
        });
    });
</script>

 