<div>
    <!-- ========== PAGE HEADER ========== -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <div>
            <h4 class="mb-0 fw-bold d-flex align-items-center gap-2">
                <i class="bi bi-cart-plus"></i> 
                {{ $purchaseId ? 'Edit Purchase Order' : 'Add New Purchase Order' }}
            </h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('purchases.index') }}" class="text-decoration-none">Purchases</a></li>
                    <li class="breadcrumb-item active">{{ $purchaseId ? 'Edit' : 'Add' }}</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('purchases.index') }}" class="btn btn-secondary btn-sm shadow-sm">
            <i class="bi bi-arrow-left"></i> <span class="d-none d-sm-inline">Back to List</span>
            <span class="d-sm-none">Back</span>
        </a>
    </div>

    <!-- ========== MAIN FORM CARD ========== -->
    <div class="card border-0 shadow-lg">
        <div class="card-header bg-transparent border-0 d-flex flex-wrap align-items-center justify-content-between pt-3">
            <h5 class="mb-0 fw-bold d-flex align-items-center gap-2">
                <i class="bi bi-pencil-square"></i>
                {{ $purchaseId ? 'Edit Purchase Order' : 'Create New Purchase Order' }}
            </h5>
            <span class="badge bg-primary-soft text-primary rounded-pill">
                {{ $purchaseId ? 'Editing' : 'New' }}
            </span>
        </div>

        <div class="card-body pt-0">
            <form wire:submit="savePurchase">
                <!-- ========== PURCHASE ORDER INFORMATION ========== -->
                <div class="row g-3">
                    <div class="col-12">
                        <h6 class="fw-bold mb-0 d-flex align-items-center gap-2">
                            <i class="bi bi-info-circle"></i> Purchase Order Information
                        </h6>
                        <hr class="mt-1">
                    </div>

                    <!-- PO Number -->
                    <div class="col-12 col-sm-6 col-md-4">
                        <label class="form-label fw-semibold small">PO Number <span class="text-danger">*</span></label>
                        <input type="text" 
                               wire:model="purchase_number" 
                               class="form-control @error('purchase_number') is-invalid @enderror"
                               placeholder="PO-20240001">
                        @error('purchase_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <!-- Supplier -->
                    <div class="col-12 col-sm-6 col-md-4">
                        <label class="form-label fw-semibold small">Supplier <span class="text-danger">*</span></label>
                        <select wire:model="supplier_id" class="form-select @error('supplier_id') is-invalid @enderror">
                            <option value="">Select Supplier</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}">{{ $supplier->name }} {{ $supplier->company_name ? '(' . $supplier->company_name . ')' : '' }}</option>
                            @endforeach
                        </select>
                        @error('supplier_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <!-- Purchase Date -->
                    <div class="col-12 col-sm-6 col-md-4">
                        <label class="form-label fw-semibold small">Purchase Date <span class="text-danger">*</span></label>
                        <input type="date" 
                               wire:model="purchase_date" 
                               class="form-control @error('purchase_date') is-invalid @enderror">
                        @error('purchase_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <!-- Expected Delivery -->
                    <div class="col-12 col-sm-6 col-md-4">
                        <label class="form-label fw-semibold small">Expected Delivery</label>
                        <input type="date" 
                               wire:model="expected_delivery_date" 
                               class="form-control">
                    </div>

                    <!-- Status -->
                    <div class="col-12 col-sm-6 col-md-4">
                        <label class="form-label fw-semibold small">Status</label>
                        <select wire:model="status" class="form-select">
                            <option value="draft">Draft</option>
                            <option value="ordered">Ordered</option>
                            <option value="partial">Partial Received</option>
                            <option value="received">Received</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                </div>

                <!-- ========== ITEMS SECTION ========== -->
                <div class="row g-3 mt-2">
                    <div class="col-12">
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                            <h6 class="fw-bold text-secondary mb-0 d-flex align-items-center gap-2">
                                <i class="bi bi-list-ul"></i> Items
                            </h6>
                            <button type="button" 
                                    wire:click="openProductModal" 
                                    class="btn btn-primary btn-sm shadow-sm">
                                <i class="bi bi-plus-lg"></i> Add Item
                            </button>
                        </div>
                        <hr class="mt-1">
                    </div>

                    @if(empty($items))
                        <div class="col-12">
                            <div class="text-center py-4 bg-light-soft rounded-3">
                                <i class="bi bi-inbox fs-1 d-block mb-2 text-muted"></i>
                                <p class="text-muted mb-0">No items added yet. Click "Add Item" to add products.</p>
                            </div>
                        </div>
                    @else
                        <!-- ========== MOBILE ITEMS VIEW ========== -->
                        <div class="col-12 d-md-none">
                            @foreach($items as $index => $item)
                            <div class="card border-0 shadow-sm mb-2">
                                <div class="card-body p-2">
                                    <div class="d-flex justify-content-between align-items-start mb-1">
                                        <div>
                                            <div class="fw-semibold small">{{ $item['product_name'] }}</div>
                                            <div class="text-muted small">SKU: {{ $item['sku'] }}</div>
                                        </div>
                                        <button type="button" 
                                                wire:click="removeItem({{ $index }})"
                                                class="btn btn-danger-soft text-danger btn-sm rounded-circle shadow-sm" style="width: 28px; height: 28px; padding: 0; font-size: 0.6rem;">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    </div>
                                    <div class="row g-1 small">
                                        <div class="col-6">
                                            <span class="text-muted">Qty:</span>
                                            <input type="number" 
                                                   wire:input="updateItemQuantity({{ $index }}, $event.target.value)"
                                                   value="{{ $item['quantity'] }}"
                                                   min="1"
                                                   class="form-control form-control-sm d-inline-block" style="width: 60px;">
                                        </div>
                                        <div class="col-6">
                                            <span class="text-muted">Cost:</span>
                                            <input type="number" 
                                                   wire:input="updateItemCost({{ $index }}, $event.target.value)"
                                                   value="{{ $item['unit_cost'] }}"
                                                   step="0.01"
                                                   min="0"
                                                   class="form-control form-control-sm d-inline-block" style="width: 80px;">
                                        </div>
                                        <div class="col-12">
                                            <span class="text-muted">Total:</span>
                                            <span class="fw-bold">{{ amo($item['total_cost']) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <!-- ========== DESKTOP ITEMS TABLE ========== -->
                        <div class="col-12 d-none d-md-block">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover align-middle mb-0">
                                    <thead class="table">
                                        <tr>
                                            <th class="small">Product</th>
                                            <th class="text-end small" style="width: 100px;">Qty</th>
                                            <th class="text-end small" style="width: 140px;">Unit Cost</th>
                                            <th class="text-end small">Discount</th>
                                            <th class="text-end small">Tax</th>
                                            <th class="text-end small">Total</th>
                                            <th class="text-center small" style="width: 50px;"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($items as $index => $item)
                                        <tr>
                                            <td>
                                                <div class="fw-semibold">{{ $item['product_name'] }}</div>
                                                <small class="text-muted">SKU: {{ $item['sku'] }}</small>
                                            </td>
                                            <td>
                                                <input type="number" 
                                                       wire:input="updateItemQuantity({{ $index }}, $event.target.value)"
                                                       value="{{ $item['quantity'] }}"
                                                       min="1"
                                                       class="form-control form-control-sm text-end">
                                            </td>
                                            <td>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text">$</span>
                                                    <input type="number" 
                                                           wire:input="updateItemCost({{ $index }}, $event.target.value)"
                                                           value="{{ $item['unit_cost'] }}"
                                                           step="0.01"
                                                           min="0"
                                                           class="form-control form-control-sm text-end">
                                                </div>
                                            </td>
                                            <td>
                                                <div class="input-group input-group-sm">
                                                    <select wire:model="items.{{ $index }}.discount_type" class="form-select form-select-sm" style="width: 50px;">
                                                        <option value="fixed">$</option>
                                                        <option value="percentage">%</option>
                                                    </select>
                                                    <input type="number" 
                                                           wire:model.live="items.{{ $index }}.discount_value"
                                                           step="0.01"
                                                           min="0"
                                                           class="form-control form-control-sm text-end"
                                                           style="width: 70px;">
                                                </div>
                                            </td>
                                            <td class="text-end">
                                                {{ $item['tax_rate'] }}%
                                                <div class="small text-muted">{{ amo($item['tax_amount']) }}</div>
                                            </td>
                                            <td class="text-end fw-bold">{{ amo($item['total_cost']) }}</td>
                                            <td>
                                                <button type="button" 
                                                        wire:click="removeItem({{ $index }})"
                                                        class="btn btn-danger-soft text-danger btn-sm rounded-circle shadow-sm" style="width: 30px; height: 30px; padding: 0; font-size: 0.7rem;">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- ========== SUMMARY ========== -->
                <div class="row g-3 mt-2">
                    <div class="col-12">
                        <h6 class="fw-bold text-success mb-0 d-flex align-items-center gap-2">
                            <i class="bi bi-calculator"></i> Summary
                        </h6>
                        <hr class="mt-1">
                    </div>

                    <div class="col-12 col-md-6 offset-md-6">
                        <div class="bg-light-soft rounded-3 p-3">
                            <div class="d-flex justify-content-between py-1">
                                <span>Subtotal</span>
                                <span>{{ amo($subtotal) }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center py-1">
                                <span>Discount</span>
                                <div class="d-flex gap-1">
                                    <select wire:model.live="discount_type" class="form-select form-select-sm" style="width: 50px;">
                                        <option value="fixed">$</option>
                                        <option value="percentage">%</option>
                                    </select>
                                    <input type="number" 
                                           wire:model.live="discount_value"
                                           step="0.01"
                                           min="0"
                                           class="form-control form-control-sm text-end"
                                           style="width: 80px;">
                                </div>
                            </div>
                            <div class="d-flex justify-content-between py-1">
                                <span>Discount Amount</span>
                                <span class="text-danger">-{{ amo($discount_amount) }}</span>
                            </div>
                            <div class="d-flex justify-content-between py-1">
                                <span>Tax</span>
                                <span>{{ amo($tax_amount) }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center py-1">
                                <span>Shipping</span>
                                <input type="number" 
                                       wire:model.live="shipping_cost"
                                       step="0.01"
                                       min="0"
                                       class="form-control form-control-sm text-end"
                                       style="width: 100px;">
                            </div>
                            <div class="d-flex justify-content-between align-items-center py-1">
                                <span>Other Costs</span>
                                <input type="number" 
                                       wire:model.live="other_cost"
                                       step="0.01"
                                       min="0"
                                       class="form-control form-control-sm text-end"
                                       style="width: 100px;">
                            </div>
                            <div class="d-flex justify-content-between border-top pt-2 mt-1">
                                <h6 class="mb-0 fw-bold">Total</h6>
                                <h5 class="mb-0 fw-bold text-primary">{{ amo($total_amount) }}</h5>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ========== NOTES ========== -->
                <div class="row g-3 mt-2">
                    <div class="col-12">
                        <h6 class="fw-bold text-secondary mb-0 d-flex align-items-center gap-2">
                            <i class="bi bi-file-text"></i> Notes
                        </h6>
                        <hr class="mt-1">
                    </div>
                    <div class="col-12">
                        <textarea wire:model="notes" 
                                  rows="3" 
                                  class="form-control"
                                  placeholder="Enter any additional notes"></textarea>
                    </div>
                </div>

                <!-- ========== FORM ACTIONS ========== -->
                <div class="row g-2 mt-4 pt-3 border-top">
                    <div class="col-12 d-flex flex-wrap gap-2">
                        <a href="{{ route('purchases.index') }}" class="btn btn-secondary shadow-sm">
                            <i class="bi bi-x-lg"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary shadow-sm">
                            <i class="bi bi-check-lg"></i> 
                            {{ $purchaseId ? 'Update Purchase' : 'Create Purchase' }}
                        </button>
                       
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Product Selection Modal -->
    @if($showProductModal)
    <div class="modal fade show" id="productModal" style="display: block; background: rgba(0,0,0,0.5);" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header border-0">
                    <h5 class="modal-title text-primary d-flex align-items-center gap-2">
                        <i class="bi bi-box"></i> Select Product
                    </h5>
                    <button type="button" class="btn-close" wire:click="$set('showProductModal', false)"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <input type="text" 
                               wire:model.live.debounce.300ms="productSearch" 
                               class="form-control" 
                               placeholder="Search products..."
                               autofocus>
                    </div>
                    
                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                        @if(strlen($productSearch) >= 2)
                            <table class="table table-bordered table-hover align-middle mb-0">
                                <thead class="table">
                                    <tr>
                                        <th class="small">Product</th>
                                        <th class="small">SKU</th>
                                        <th class="text-end small">Purchase Price</th>
                                        <th class="text-end small">Stock</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($this->products as $product)
                                        <tr style="cursor: pointer;" wire:click="addItem({{ $product->id }})" class="product-hover">
                                            <td>
                                                <span class="fw-semibold">{{ $product->name }}</span>
                                            </td>
                                            <td><code class="bg-light px-2 py-1 rounded small">{{ $product->sku }}</code></td>
                                            <td class="text-end">{{ amo($product->purchase_price) }}</td>
                                            <td class="text-end">
                                                @if($product->stock_quantity <= 0)
                                                    <span class="badge bg-danger-soft text-danger rounded-pill">Out</span>
                                                @elseif($product->stock_quantity <= $product->low_stock_threshold)
                                                    <span class="badge bg-warning-soft text-warning rounded-pill">{{ $product->stock_quantity }}</span>
                                                @else
                                                    <span class="badge bg-success-soft text-success rounded-pill">{{ $product->stock_quantity }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-4 text-muted">
                                                <i class="bi bi-box-seam fs-3 d-block mb-2"></i>
                                                <p>No products found</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        @else
                            <div class="text-center py-4 text-muted">
                                <i class="bi bi-search fs-3 d-block mb-2"></i>
                                <p>Type at least 2 characters to search for products</p>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary btn-sm shadow-sm" wire:click="$set('showProductModal', false)">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    
</div>