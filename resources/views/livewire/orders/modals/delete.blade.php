@if($showDeleteModal && $selectedOrderForReceipt)
<div class="modal fade show" id="deleteModal" style="display: block; background: rgba(0,0,0,0.6); backdrop-filter: blur(4px);" tabindex="-1" role="dialog" aria-modal="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg">
            <!-- Modal Header -->
            <div class="modal-header border-0 pb-0">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-danger-soft p-2" style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-trash-alt text-danger fa-lg"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0">
                            Delete Order
                        </h5>
                        <p class="text-muted small mb-0">
                            Order #{{ $selectedOrderForReceipt->order_number }}
                        </p>
                    </div>
                </div>
                <button type="button" class="btn-close" wire:click="$set('showDeleteModal', false)" aria-label="Close"></button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body px-4 py-3">
                <!-- Warning Icon & Message -->
                <div class="text-center mb-4">
                    <div class="mb-3">
                        <div class="rounded-circle bg-warning-soft d-inline-flex p-3" style="width: 80px; height: 80px; align-items: center; justify-content: center;">
                            <i class="fas fa-exclamation-triangle text-warning" style="font-size: 2.5rem;"></i>
                        </div>
                    </div>
                    <h5 class="fw-bold mb-2">Are you sure?</h5>
                    <p class="text-muted mb-0">
                        You are about to delete order <strong>#{{ $selectedOrderForReceipt->order_number }}</strong>.
                        <br>
                        <span class="text-danger">This action cannot be undone.</span>
                    </p>
                </div>

                <!-- Order Summary Cards -->
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <div class="card bg-primary-soft border-0 h-100">
                            <div class="card-body py-2 px-3">
                                <div class="small text-muted">Customer</div>
                                <div class="fw-semibold small">{{ $selectedOrderForReceipt->customer->name ?? 'Walk-in Customer' }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="card bg-info-soft border-0 h-100">
                            <div class="card-body py-2 px-3">
                                <div class="small text-muted">Order Date</div>
                                <div class="fw-semibold small">{{ $selectedOrderForReceipt->created_at }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="card bg-success-soft border-0 h-100">
                            <div class="card-body py-2 px-3">
                                <div class="small text-muted">Order Total</div>
                                <div class="fw-semibold small text-success">{{ $settings->currency_symbol ?? '$' }}{{ number_format($selectedOrderForReceipt->total_amount, 2) }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="card bg-secondary-soft border-0 h-100">
                            <div class="card-body py-2 px-3">
                                <div class="small text-muted">Items</div>
                                <div class="fw-semibold small">{{ $selectedOrderForReceipt->items->count() }} items</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Warning Alert for Payments -->
                @if($selectedOrderForReceipt->payments->count() > 0)
                <div class="alert alert-danger d-flex align-items-start gap-3 mb-0" style="border-left: 4px solid #dc3545;">
                    <i class="fas fa-exclamation-circle text-danger mt-1"></i>
                    <div>
                        <h6 class="alert-heading mb-1 fw-semibold">Cannot Delete This Order</h6>
                        <p class="mb-0 small">
                            This order has <strong>{{ $selectedOrderForReceipt->payments->count() }}</strong> payment(s) associated with it. 
                            Orders with payments cannot be deleted.
                        </p>
                    </div>
                </div>
                @else
                <div class="alert alert-warning d-flex align-items-start gap-3 mb-0" style="border-left: 4px solid #ffc107;">
                    <i class="fas fa-info-circle text-warning mt-1"></i>
                    <div>
                        <h6 class="alert-heading mb-1 fw-semibold">Before You Delete</h6>
                        <p class="mb-0 small">
                            This action will permanently remove this order and all associated data from the system.
                            Stock quantities will be <strong>restored</strong> for all products in this order.
                        </p>
                    </div>
                </div>
                @endif
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer border-0 pt-0 px-4 pb-4">
                <button type="button" class="btn btn-light border" wire:click="$set('showDeleteModal', false)">
                    <i class="fas fa-times me-1"></i>
                    Cancel
                </button>
                <button type="button" class="btn btn-danger px-4"
                    wire:click="deleteOrder"
                    wire:loading.attr="disabled"
                    wire:target="deleteOrder"
                    @if($selectedOrderForReceipt->payments->count() > 0) disabled @endif>
                    <span wire:loading.remove wire:target="deleteOrder">
                        <i class="fas fa-trash-alt me-1"></i>
                        Yes, Delete Order
                    </span>
                    <span wire:loading wire:target="deleteOrder">
                        <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                        Deleting...
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>
@endif

<script>
    document.addEventListener('livewire:initialized', () => {
        // Listen for delete modal events
        Livewire.on('open-delete-modal', () => {
            document.body.classList.add('modal-open');
        });

        Livewire.on('close-delete-modal', () => {
            document.body.classList.remove('modal-open');
        });
    });

    // Handle escape key to close modal
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const modal = document.getElementById('deleteModal');
            if (modal && modal.style.display !== 'none') {
                @this.set('showDeleteModal', false);
            }
        }
    });
</script>

