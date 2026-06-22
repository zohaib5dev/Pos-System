@if($showRefundModal && $selectedOrderForReceipt)
<div class="modal fade show" id="refundModal" style="display: block; background: rgba(0,0,0,0.6); backdrop-filter: blur(4px);" tabindex="-1" role="dialog" aria-modal="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content border-0 shadow-lg">
            <!-- Modal Header -->
            <div class="modal-header border-0 pb-0">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-warning-soft p-2" style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-undo-alt text-warning fa-lg"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0">
                            Process Refund
                        </h5>
                        <p class="text-muted small mb-0">
                            Order #{{ $selectedOrderForReceipt->order_number ?? 'N/A' }}
                        </p>
                    </div>
                </div>
                <button type="button" class="btn-close" wire:click="$set('showRefundModal', false)" aria-label="Close"></button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body px-4 py-3">
                <!-- Order Summary Cards -->
                <div class="row g-2 mb-4">
                    <div class="col-6 col-md-3">
                        <div class="card bg-primary-soft border-0 h-100">
                            <div class="card-body py-2 px-3">
                                <div class="small text-muted">Order Date</div>
                                <div class="fw-semibold small">{{ optional($selectedOrderForReceipt->created_at)->format('d M Y h:i A') ?? 'N/A' }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="card bg-info-soft border-0 h-100">
                            <div class="card-body py-2 px-3">
                                <div class="small text-muted">Customer</div>
                                <div class="fw-semibold small">{{ $selectedOrderForReceipt->customer->name ?? 'Walk-in Customer' }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="card bg-success-soft border-0 h-100">
                            <div class="card-body py-2 px-3">
                                <div class="small text-muted">Order Total</div>
                                <div class="fw-semibold small text-success">{{ $settings->currency_symbol ?? '$' }}{{ number_format($selectedOrderForReceipt->total_amount ?? 0, 2) }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="card bg-info-soft border-0 h-100">
                            <div class="card-body py-2 px-3">
                                <div class="small text-muted">Paid Amount</div>
                                <div class="fw-semibold small text-info">{{ $settings->currency_symbol ?? '$' }}{{ number_format($selectedOrderForReceipt->paid_amount ?? 0, 2) }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Items to Refund -->
                <div class="mb-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <label class="fw-semibold mb-0">
                            <i class="fas fa-boxes me-1"></i>
                            Select Items to Refund
                            <span class="badge bg-primary-soft text-primary ms-2">
                                {{ count($selectedRefundItems) }} selected
                            </span>
                        </label>
                        <div class="d-flex gap-1">
                            <button type="button" class="btn btn-sm btn-outline-primary" wire:click="selectAllItemsForRefund">
                                <i class="fas fa-check-double me-1"></i> All
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" wire:click="$set('selectedRefundItems', [])">
                                <i class="fas fa-times me-1"></i> None
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive" style="max-height: 320px; overflow-y: auto; border-radius: 8px; border: 1px solid var(--bs-border-color);">
                        <table class="table table-hover mb-0" style="min-width: 700px;">
                            <thead class="bg-light" style="position: sticky; top: 0; z-index: 10;">
                                <tr>
                                    <th width="40px" class="text-center">
                                        <input type="checkbox"
                                            wire:click="selectAllItemsForRefund"
                                            @if(count($selectedRefundItems) === count($refundItems) && count($refundItems) > 0) checked @endif
                                            class="form-check-input">
                                    </th>
                                    <th>Product</th>
                                    <th class="text-center">Price</th>
                                    <th class="text-center">Purchased</th>
                                    <th class="text-center" style="min-width: 100px;">Refund Qty</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($refundItems as $index => $item)
                                <tr class="{{ in_array($item['id'], $selectedRefundItems) ? 'table-active' : '' }}">
                                    <td class="text-center">
                                        <input type="checkbox"
                                            wire:model.live="selectedRefundItems"
                                            value="{{ $item['id'] }}"
                                            class="form-check-input"
                                            id="item_{{ $item['id'] }}">
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <strong class="small">{{ $item['product_name'] ?? 'N/A' }}</strong>
                                            @if(!empty($item['product_sku']))
                                            <small class="text-muted">SKU: {{ $item['product_sku'] }}</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="text-center">{{ $settings->currency_symbol ?? '$' }}{{ number_format($item['unit_price'] ?? 0, 2) }}</td>
                                    <td class="text-center">{{ $item['quantity'] ?? 0 }}</td>
                                    <td class="text-center">
                                        <input type="number"
                                            wire:model.live="refundItems.{{ $index }}.refund_quantity"
                                            wire:key="refund-qty-{{ $item['id'] }}"
                                            min="0"
                                            max="{{ $item['quantity'] ?? 0 }}"
                                            class="form-control form-control-sm text-center"
                                            style="width: 80px; margin: 0 auto;"
                                            {{ !in_array($item['id'], $selectedRefundItems) ? 'disabled' : '' }}>
                                    </td>
                                    <td class="text-end fw-bold">
                                        {{ $settings->currency_symbol ?? '$' }}{{ number_format(($item['unit_price'] * ($item['refund_quantity'] ?? 0)), 2) }}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <i class="fas fa-box-open fa-2x text-muted mb-2 d-block"></i>
                                        <p class="text-muted mb-0">No items found for refund</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Refund Summary -->
                <div class="card bg-light border-0">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12 col-md-4">
                                <label class="fw-semibold small text-muted mb-1">
                                    <i class="fas fa-money-bill-wave me-1"></i>
                                    Refund Amount
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0">{{ $settings->currency_symbol ?? '$' }}</span>
                                    <input type="text"
                                        id="refundAmount"
                                        wire:model="refundAmount"
                                        readonly
                                        class="form-control bg-white font-weight-bold border-start-0"
                                        style="font-size: 1.2rem;">
                                </div>
                            </div>
                            <div class="col-12 col-md-8">
                                <label class="fw-semibold small text-muted mb-1">
                                    <i class="fas fa-comment me-1"></i>
                                    Reason for Refund <span class="text-danger">*</span>
                                </label>
                                <textarea id="refundReason"
                                    wire:model="refundReason"
                                    rows="2"
                                    class="form-control @error('refundReason') is-invalid @enderror"
                                    placeholder="Enter reason for refund..."></textarea>
                                @error('refundReason')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer border-0 pt-0 px-4 pb-4">
                <button type="button" class="btn btn-light border" wire:click="$set('showRefundModal', false)">
                    <i class="fas fa-times me-1"></i>
                    Cancel
                </button>
                <button type="button" class="btn btn-warning px-4" 
                    wire:click="processRefund"
                    wire:loading.attr="disabled"
                    wire:target="processRefund"
                    @if(empty($selectedRefundItems) || $refundAmount <= 0) disabled @endif>
                    <span wire:loading.remove wire:target="processRefund">
                        <i class="fas fa-undo-alt me-1"></i>
                        Process Refund
                    </span>
                    <span wire:loading wire:target="processRefund">
                        <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                        Processing...
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>
@endif

<script>
    document.addEventListener('livewire:initialized', () => {
        // Listen for refund modal events
        Livewire.on('open-refund-modal', () => {
            document.body.classList.add('modal-open');
        });

        Livewire.on('close-refund-modal', () => {
            document.body.classList.remove('modal-open');
        });

        // Handle Escape key
        Livewire.on('close-refund-modal', () => {
            document.body.classList.remove('modal-open');
        });
    });

    // Handle escape key to close modal
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const modal = document.getElementById('refundModal');
            if (modal && modal.style.display !== 'none') {
                @this.set('showRefundModal', false);
            }
        }
    });
</script>


