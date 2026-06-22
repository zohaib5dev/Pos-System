<div class="h-screen flex flex-col overflow-hidden bg-gray-100 dark:bg-gray-900 transition-colors duration-200"
    x-data="posOffline()"
    x-init="init()"
    @resize.window="if (window.innerWidth >= 768) cartOpen = false">

    <span id="pos-tax-rate" data-rate="{{ $tax }}" class="hidden"></span>
    <span data-business-name="{{ $settings->business_name ?? 'My POS' }}" class="hidden"></span>

    <!-- Offline Status Bar -->
    <div x-show="!online"
        class="bg-yellow-500 text-white p-2 text-center flex items-center justify-between px-4">
        <div class="flex items-center">
            <svg class="w-5 h-5 mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span>You are offline. Using cached products (<span x-text="cachedProducts.length"></span> items).</span>
        </div>
        <div class="flex items-center space-x-2">
            <span x-text="`${getOfflineStats().pendingSync} orders pending`" class="text-sm font-semibold"></span>
            <button @click="syncOfflineData()"
                :disabled="syncing || getOfflineStats().pendingSync === 0"
                class="px-3 py-1 bg-white text-yellow-600 rounded text-sm hover:bg-yellow-50 disabled:opacity-50 transition-colors">
                <span x-show="!syncing">Sync Now</span>
                <span x-show="syncing">Syncing...</span>
            </button>
        </div>
    </div>


    <button onclick="toggleCart()" id="cartToggleBtn"
        class="cart-toggle md:hidden bg-indigo-600 text-white p-3 rounded-full shadow-lg fixed bottom-4 right-4 z-50 hidden">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
        </svg>
        <span x-show="cart.length > 0"
            class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center"
            x-text="cart.length"></span>
    </button>

    <div class="flex-1 flex flex-col md:flex-row overflow-hidden">

        @include('livewire.pos.products-section')

        @include('livewire.pos.cart-section')

    </div>

    @include('livewire.pos.customer-modal')

    @include('livewire.pos.receipt-modal')

    @include('livewire.pos.offline-orders-modal')


</div>

<script>
    function toggleCart() {
        const cartSection = document.getElementById('cartSection');
        const toggleBtn = document.getElementById('cartToggleBtn');
        const productsSection = document.getElementById('productsSection');
        cartSection.classList.toggle('open');
        if (cartSection.classList.contains('open')) {
            toggleBtn.style.display = 'none';
            productsSection.style.display = 'none';
        } else {
            toggleBtn.style.display = 'flex';
            productsSection.style.display = 'block';
        }
    }

    function handleResize() {
        const cartSection = document.getElementById('cartSection');
        const toggleBtn = document.getElementById('cartToggleBtn');
        const productsSection = document.getElementById('productsSection');
        if (window.innerWidth >= 768) {
            cartSection.classList.remove('open');
            toggleBtn.style.display = 'none';
            productsSection.style.display = 'block';
        } else {
            if (!cartSection.classList.contains('open')) {
                toggleBtn.style.display = 'flex';
            }
        }
    }
    window.addEventListener('load', handleResize);
    window.addEventListener('resize', handleResize);
</script>

@push('scripts')
<script>
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('notify', (data) => {
            const eventData = Array.isArray(data) ? data[0] : data;
            const message = eventData?.message || 'Notification';
            const type = eventData?.type || 'info';
            document.querySelectorAll('.custom-notification').forEach(n => n.remove());
            const notification = document.createElement('div');
            notification.className = `custom-notification fixed top-4 left-4 px-4 sm:px-6 py-2 sm:py-3 rounded-lg shadow-lg text-white text-sm sm:text-base ${type === 'success' ? 'bg-green-500' : 'bg-red-500'} z-50 transform transition-all duration-300 translate-x-0`;
            notification.textContent = message;
            const icon = document.createElement('span');
            icon.className = 'mr-2 inline-block';
            icon.innerHTML = type === 'success' ? '✓' : '✗';
            notification.prepend(icon);
            document.body.appendChild(notification);
            setTimeout(() => {
                notification.classList.add('opacity-0', 'translate-x-full');
                setTimeout(() => notification.remove(), 300);
            }, 3000);
        });

        Livewire.on('print-receipt', (data) => {
            const orderId = data[0] || data.orderId;
            const receiptElement = document.getElementById(`receipt-content-${orderId}`);
            if (!receiptElement) {
                console.error('Receipt element not found for order:', orderId);
                return;
            }
            const receiptContent = receiptElement.innerHTML;
            const printWindow = window.open('', '_blank');
            printWindow.document.write(`<html><head><title>Receipt</title><style>*{margin:0;padding:0;box-sizing:border-box}body{font-family:'Courier New',monospace;background:white;padding:20px;width:500px;max-width:500px;margin:0 auto}.receipt-paper{font-family:'Courier New',monospace;font-size:15px;color:#000;background:white;border:1px dashed #000;padding:10px}.receipt-header{text-align:center;margin-bottom:5px;padding-bottom:10px;border-bottom:2px dashed #000}.receipt-business-name{font-weight:bold;font-size:17px}.receipt-title{text-align:center;margin-bottom:15px;font-weight:bold;font-size:16px;border-bottom:2px dashed #000;padding-bottom:1px}.receipt-info-grid{display:flex;gap:15px;margin-bottom:20px}.receipt-info-box{flex:1;border:1px dashed #000;padding:8px}.receipt-info-box-header{font-weight:bold;margin-bottom:8px;border-bottom:1px dashed #000;padding-bottom:5px}.receipt-order-details,.receipt-customer-details{font-size:11px}.receipt-order-row{display:flex;justify-content:space-between;padding:2px 0}.receipt-order-value,.receipt-customer-name{font-weight:bold}.receipt-items-table{width:100%;border-collapse:collapse;font-size:12px}.receipt-items-table th{border-bottom:2px solid #000;padding:4px}.receipt-items-table td{border-bottom:1px dashed #000;padding:4px}.receipt-text-left{text-align:left}.receipt-text-center{text-align:center}.receipt-text-right{text-align:right}.receipt-item-name{padding:4px;font-weight:bold}.receipt-item-sku{font-size:8px}.receipt-summary-table{width:100%;font-size:13px}.receipt-summary-table td{padding:2px 0}.receipt-discount{color:#28a745}.receipt-total-row{border-top:2px solid #000;font-weight:bold}.receipt-total{padding-top:5px}.receipt-payment-section{border-top:1px dashed #000;padding-top:10px;margin-bottom:15px}.receipt-payment-table{width:100%;font-size:13px}.receipt-payment-table td{padding:2px 0}.receipt-paid{font-weight:bold}.receipt-footer{text-align:center;padding-top:10px;border-top:2px dashed #000;font-size:10px}@media print{body{padding:0}}</style></head><body>${receiptContent}<script>window.onload=function(){setTimeout(function(){window.print();window.close();},100);};<\/script></body></html>`);
            printWindow.document.close();
        });
    });
</script>
@endpush