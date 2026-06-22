<div>
<section class="content-header">
    <div class="">
        <div class="row mb-2">
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('inventory.index') }}">Inventory</a></li>
                    <li class="breadcrumb-item active">Physical Stock Count</li>
                </ol>
            </div>
        </div>
    </div>
</section>

<div class="card card-default color-palette-box">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h3 class="card-title">PHYSICAL STOCK COUNT</h3>
            <a href="{{ route('inventory.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Back to Inventory
            </a>
        </div>
    </div>
    
    <div class="card-body">
       

        <!-- Count Information -->
        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label for="countDate">Count Date</label>
                    <input type="date" 
                           id="countDate"
                           wire:model="countDate" 
                           class="form-control">
                </div>
            </div>
            <div class="col-md-9">
                <div class="form-group">
                    <label for="countNotes">Notes</label>
                    <input type="text" 
                           id="countNotes"
                           wire:model="countNotes" 
                           class="form-control"
                           placeholder="Enter any notes about this stock count">
                </div>
            </div>
        </div>

        <!-- Count Summary -->
        <div class="alert alert-info">
            <div class="row">
                <div class="col-md-3">
                    <strong>Total Products:</strong>
                    <h3>{{ count($countItems) }}</h3>
                </div>
                <div class="col-md-3">
                    <strong>Items with Discrepancy:</strong>
                    <h3 class="text-warning">
                        {{ collect($countItems)->filter(fn($item) => $item['difference'] != 0)->count() }}
                    </h3>
                </div>
                <div class="col-md-3">
                    <strong>Total Difference:</strong>
                    <h3 class="{{ collect($countItems)->sum('difference') > 0 ? 'text-success' : 'text-danger' }}">
                        {{ collect($countItems)->sum('difference') > 0 ? '+' : '' }}{{ number_format(collect($countItems)->sum('difference')) }}
                    </h3>
                </div>
                <div class="col-md-3">
                    <strong>Value Difference:</strong>
                    <h3 class="{{ collect($countItems)->sum(function($item) { 
                        return $item['difference'] * (\App\Models\Product::find($item['id'])?->purchase_price ?? 0); 
                    }) > 0 ? 'text-success' : 'text-danger' }}">
                        {{ amo(abs(collect($countItems)->sum(function($item) { 
                            return $item['difference'] * (\App\Models\Product::find($item['id'])?->purchase_price ?? 0); 
                        }))) }}
                    </h3>
                </div>
            </div>
        </div>

        <!-- Count Table -->
        <div class="card mt-3">
            <div class="card-header">
                <h3 class="card-title">Enter Counted Quantities</h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Product</th>
                                <th>SKU</th>
                                <th class="text-right">System Qty</th>
                                <th class="text-right">Counted Qty</th>
                                <th class="text-right">Difference</th>
                                <th>Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($countItems as $index => $item)
                                <tr>
                                    <td><strong>{{ $item['name'] }}</strong></td>
                                    <td>{{ $item['sku'] }}</td>
                                    <td class="text-right">{{ $item['system_quantity'] }}</td>
                                    <td class="text-right" style="width: 120px;">
                                        <input type="number" 
                                               wire:model.live="countItems.{{ $index }}.counted_quantity" 
                                               min="0"
                                               class="form-control form-control-sm text-right">
                                    </td>
                                    <td class="text-right">
                                        <span class="font-weight-bold {{ $item['difference'] > 0 ? 'text-success' : ($item['difference'] < 0 ? 'text-danger' : 'text-muted') }}">
                                            {{ $item['difference'] > 0 ? '+' : '' }}{{ $item['difference'] }}
                                        </span>
                                    </td>
                                    <td style="width: 200px;">
                                        <input type="text" 
                                               wire:model="countItems.{{ $index }}.notes" 
                                               class="form-control form-control-sm"
                                               placeholder="Notes">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="row mt-4">
            <div class="col-md-12 text-right">
                <a href="{{ route('inventory.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
                <button wire:click="saveStockCount" 
                        wire:confirm="Are you sure you want to save this stock count? This will adjust inventory levels."
                        class="btn btn-success">
                    <i class="fas fa-save"></i> Save Stock Count
                </button>
            </div>
        </div>
    </div>
</div>
 
<script>
 

    let unsavedChanges = false;
    
    document.addEventListener('livewire:init', function() {
        Livewire.on('count-items-updated', function() {
            unsavedChanges = true;
        });
    });

    window.addEventListener('beforeunload', function(e) {
        if (unsavedChanges) {
            e.preventDefault();
            e.returnValue = 'You have unsaved changes. Are you sure you want to leave?';
        }
    });

    Livewire.on('saved', function() {
        unsavedChanges = false;
    });
</script>
</div>