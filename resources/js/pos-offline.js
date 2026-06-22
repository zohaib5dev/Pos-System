// resources/js/pos-offline.js

export function posOffline() {


    return {

        darkMode: localStorage.getItem('darkMode') === 'true',
        displayProducts: [],
        // Core state
        showCustomerSearch: false,
        customerSearchQuery: '',
        customerSearchResults: [],

        online: navigator.onLine,
        syncing: false,
        syncInProgress: false,
        cartOpen: false,
        cachedProducts: [],
        cachedCategories: [],
        cachedCustomers: [],
        showOfflineOrders: false,
        offlineOrdersList: [],
        cart: [],
        searchResults: [],
        productSearch: '',
        selectedCategoryId: null,
        stats: {
            pendingOrdersCount: 0,
            totalOffline: 0
        },
        initCompleted: false,

        // Selected customer
        selectedCustomer: null,

        // Customer modal state
        customerModal: {
            show: false,
            mode: 'select',
            searchQuery: '',
            searchResults: [],
            name: '',
            phone: '',
            email: '',
            saving: false,
        },

        // Receipt modal state (unified for both online and offline)
        receiptModal: {
            show: false,
            order: null,
        },

        // Business settings
        businessSettings: {
            name: 'My POS System',
            address: '',
            phone: '+1234567890',
            email: 'test@test.com',
            currency_symbol: '$',
            receipt_footer: 'Thank you for your purchase!'
        },

        // Unified payment modal state
        paymentModal: {
            show: false,
            paymentMethod: 'cash',
            amountTendered: 0,
            discount: 0,
            discountType: 'fixed',
            tax: 0,
            notes: '',
            isOnline: true,
            processing: false,
        },

        async init() {
            if (this.initCompleted) return;

            await this.checkOnlineStatus();

            this.loadCachedProducts();
            this.loadCachedCategories();
            this.filterProducts();
            this.loadCachedCustomers();
            this.loadOfflineOrders();
            this.loadCart();
            this.loadSelectedCustomer();
            this.loadBusinessSettings();
            this.updateStats();
            this.setupConnectionListeners();

            const taxEl = document.getElementById('pos-tax-rate');
            if (taxEl) {
                this.paymentModal.tax = parseFloat(taxEl.dataset.rate || 0);
            }

            if (this.online && !this.syncInProgress) {
                await this.syncOfflineData();
                await this.cacheAllData();
            } else if (!this.online) {
                this.showNotification('You are offline. Using cached data.', 'warning');
            }

            this.initCompleted = true;
        },

        loadBusinessSettings() {
            const nameEl = document.querySelector('[data-business-name]');
            if (nameEl) {
                this.businessSettings.name = nameEl.dataset.businessName || 'My POS System';
            }

            const addressEl = document.querySelector('[data-business-address]');
            if (addressEl) {
                this.businessSettings.address = addressEl.dataset.businessAddress || '';
            }

            const phoneEl = document.querySelector('[data-business-phone]');
            if (phoneEl) {
                this.businessSettings.phone = phoneEl.dataset.businessPhone || '+1234567890';
            }

            const emailEl = document.querySelector('[data-business-email]');
            if (emailEl) {
                this.businessSettings.email = emailEl.dataset.businessEmail || 'test@test.com';
            }

            const currencyEl = document.querySelector('[data-currency-symbol]');
            if (currencyEl) {
                this.businessSettings.currency_symbol = currencyEl.dataset.currencySymbol || '$';
            }

            const footerEl = document.querySelector('[data-receipt-footer]');
            if (footerEl) {
                this.businessSettings.receipt_footer = footerEl.dataset.receiptFooter || 'Thank you for your purchase!';
            }
        },

        // ── Connectivity ──────────────────────────────────────────────────────

        async checkOnlineStatus() {
            //this.online = navigator.onLine;
            try {
                await fetch('/favicon.ico', { method: 'HEAD', cache: 'no-cache' });
                this.online = true;
            } catch {
                this.online = false;
            }
        },

        setupConnectionListeners() {
            window.removeEventListener('online', this.handleOnline);
            window.removeEventListener('offline', this.handleOffline);

            this.handleOnline = this.handleOnline.bind(this);
            this.handleOffline = this.handleOffline.bind(this);

            window.addEventListener('online', this.handleOnline);
            window.addEventListener('offline', this.handleOffline);
        },

        async handleOnline() {
            this.online = true; // set optimistically first
            this.paymentModal.isOnline = true;
            try {
                await fetch('/favicon.ico', { method: 'HEAD', cache: 'no-cache' });

                if (!this.online) {
                    this.online = true;
                    this.paymentModal.isOnline = true;
                    this.showNotification('Back online! Syncing data...', 'success');

                    setTimeout(async () => {
                        await this.syncOfflineData();
                        await this.cacheAllData();
                    }, 1000);
                }
            } catch {
                this.online = false;
                this.paymentModal.isOnline = false;
            }
        },

        handleOffline() {
            this.online = false;
            this.paymentModal.isOnline = false;
            this.showNotification('You are offline. Using cached data.', 'warning');
        },

        // ── Customer Management ───────────────────────────────────────────────

        openCustomerModal(mode = 'select') {
            this.customerModal.show = true;
            this.customerModal.mode = mode;
            this.customerModal.searchQuery = '';
            this.customerModal.name = '';
            this.customerModal.phone = '';
            this.customerModal.email = '';
            this.customerModal.saving = false;

            if (mode === 'select') {
                this.searchCustomers();
            }
        },

        closeCustomerModal() {
            this.customerModal.show = false;
        },

        searchCustomers() {
            const q = this.customerModal.searchQuery.toLowerCase().trim();
            if (!q) {
                this.customerModal.searchResults = [...this.cachedCustomers].slice(0, 10);
                return;
            }

            this.customerModal.searchResults = this.cachedCustomers.filter(c =>
                c.name.toLowerCase().includes(q) ||
                (c.phone && c.phone.toLowerCase().includes(q)) ||
                (c.email && c.email.toLowerCase().includes(q))
            ).slice(0, 10);
        },

        selectCustomer(customer) {
            this.selectedCustomer = {
                id: customer.id,
                name: customer.name,
                phone: customer.phone || '',
                email: customer.email || '',
                address: customer.address || '',
                created_at: customer.created_at || new Date().toISOString()
            };

            localStorage.setItem('pos_selected_customer', JSON.stringify(this.selectedCustomer));

            // Close search and clear results
            this.showCustomerSearch = false;
            this.customerSearchQuery = '';
            this.customerSearchResults = [];

            this.showNotification(`Customer: ${customer.name}`, 'success');

            if (this.online && window.Livewire) {
                try {
                    const wireElement = document.querySelector('[wire\\:id]');
                    if (wireElement) {
                        const wireId = wireElement.getAttribute('wire:id');
                        const component = Livewire.find(wireId);
                        if (component) {
                            component.set('customerId', customer.id).catch(e => console.log('Livewire sync optional'));
                        }
                    }
                } catch (e) {
                    console.error('Failed to sync customer with Livewire:', e);
                }
            }
        },

        clearSelectedCustomer() {
            this.selectedCustomer = null;
            localStorage.removeItem('pos_selected_customer');
            this.showCustomerSearch = false;
            this.customerSearchQuery = '';
            this.customerSearchResults = [];

            if (this.online && window.Livewire) {
                try {
                    const wireElement = document.querySelector('[wire\\:id]');
                    if (wireElement) {
                        const wireId = wireElement.getAttribute('wire:id');
                        const component = Livewire.find(wireId);
                        if (component) {
                            component.set('customerId', null).catch(e => console.log('Livewire sync optional'));
                        }
                    }
                } catch (e) {
                    console.error('Failed to clear customer in Livewire:', e);
                }
            }
        },

        loadSelectedCustomer() {
            try {
                const saved = localStorage.getItem('pos_selected_customer');
                if (saved) {
                    this.selectedCustomer = JSON.parse(saved);
                }
            } catch (e) {
                console.error('Failed to load selected customer', e);
            }
        },

        async saveNewCustomer() {
            const name = this.customerModal.name.trim();
            const phone = this.customerModal.phone.trim();

            if (!name || name.length < 3) {
                this.showNotification('Name must be at least 3 characters', 'error');
                return;
            }
            if (!phone || phone.length < 10) {
                this.showNotification('Phone must be at least 10 characters', 'error');
                return;
            }

            this.customerModal.saving = true;

            if (this.online) {
                await this.saveCustomerOnline();
            } else {
                this.saveCustomerOffline();
            }

            this.customerModal.saving = false;
            this.customerModal = false;
        },

        async saveCustomerOnline() {
            try {
                const customerData = {
                    name: this.customerModal.name.trim(),
                    phone: this.customerModal.phone.trim(),
                    email: this.customerModal.email.trim() || null,
                };

                const result = await this.callApi('/pos/customers/create', customerData);

                if (result && result.success) {
                    const newCustomer = {
                        id: result.id,
                        ...customerData,
                        created_at: new Date().toISOString()
                    };

                    this.cachedCustomers.unshift(newCustomer);
                    localStorage.setItem('pos_cached_customers', JSON.stringify(this.cachedCustomers));

                    await this.selectCustomer(newCustomer);

                    this.showNotification('Customer added successfully!', 'success');
                } else {
                    throw new Error(result?.error || 'Failed to save customer');
                }
            } catch (error) {
                console.error('Failed to save online customer:', error);
                this.showNotification('Failed to save customer: ' + error.message, 'error');
            }
        },

        saveCustomerOffline() {
            const tempId = 'temp_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);

            const newCustomer = {
                id: tempId,
                name: this.customerModal.name.trim(),
                phone: this.customerModal.phone.trim(),
                email: this.customerModal.email.trim() || null,
                temp: true,
                created_at: new Date().toISOString()
            };

            this.cachedCustomers.unshift(newCustomer);
            localStorage.setItem('pos_cached_customers', JSON.stringify(this.cachedCustomers));

            const offlineCustomers = JSON.parse(localStorage.getItem('pos_offline_new_customers') || '[]');
            offlineCustomers.push(newCustomer);
            localStorage.setItem('pos_offline_new_customers', JSON.stringify(offlineCustomers));

            this.selectCustomer(newCustomer);

            this.showNotification(`Customer "${newCustomer.name}" added locally. Will sync when online.`, 'success');
        },

        // ── Sync Methods ──────────────────────────────────────────────────────

        async syncOfflineCustomers() {
            if (!this.online || this.syncInProgress) return;

            const offlineCustomers = JSON.parse(localStorage.getItem('pos_offline_new_customers') || '[]');
            if (offlineCustomers.length === 0) return;

            this.syncInProgress = true;
            this.showNotification(`Syncing ${offlineCustomers.length} offline customer(s)...`, 'info');

            let successCount = 0;
            let failCount = 0;
            const remainingCustomers = [];

            for (const customer of offlineCustomers) {
                const existingCustomer = this.cachedCustomers.find(c =>
                    !c.temp && c.phone === customer.phone
                );

                if (existingCustomer) {
                    const cachedIndex = this.cachedCustomers.findIndex(c => c.id === customer.id);
                    if (cachedIndex !== -1) {
                        this.cachedCustomers[cachedIndex].id = existingCustomer.id;
                        this.cachedCustomers[cachedIndex].temp = false;
                    }
                    successCount++;
                    continue;
                }

                try {
                    const customerData = {
                        name: customer.name,
                        phone: customer.phone,
                        email: customer.email || '',
                    };

                    const result = await this.callApi('/pos/customers/create', customerData);

                    if (result && result.success) {
                        const cachedIndex = this.cachedCustomers.findIndex(c => c.id === customer.id);
                        if (cachedIndex !== -1) {
                            this.cachedCustomers[cachedIndex].id = result.id;
                            this.cachedCustomers[cachedIndex].temp = false;
                        }

                        if (this.selectedCustomer && this.selectedCustomer.id === customer.id) {
                            this.selectedCustomer.id = result.id;
                            localStorage.setItem('pos_selected_customer', JSON.stringify(this.selectedCustomer));
                        }

                        successCount++;
                    } else {
                        throw new Error(result?.error || 'Sync failed');
                    }
                } catch (error) {
                    console.error('Failed to sync customer:', customer, error);
                    failCount++;
                    remainingCustomers.push(customer);
                }
            }

            localStorage.setItem('pos_offline_new_customers', JSON.stringify(remainingCustomers));
            localStorage.setItem('pos_cached_customers', JSON.stringify(this.cachedCustomers));

            this.syncInProgress = false;

            if (failCount === 0) {
                this.showNotification(`✓ ${successCount} customer(s) synced!`, 'success');
            } else {
                this.showNotification(
                    `${successCount} synced, ${failCount} failed.`,
                    failCount === offlineCustomers.length ? 'error' : 'warning'
                );
            }
        },

        async syncOfflineData() {
            if (!this.online || this.syncing) return;

            this.syncing = true;

            try {
                await this.syncOfflineCustomers();

                const pending = this.offlineOrdersList.filter(o => !o.synced);
                if (pending.length === 0) {
                    this.syncing = false;
                    return;
                }

                this.showNotification(`Syncing ${pending.length} offline order(s)...`, 'info');

                let successCount = 0;
                let failCount = 0;

                for (const order of pending) {
                    try {
                        if (order.synced) continue;

                        const result = await this.callApi('/pos/sync-offline-order', order.data);

                        if (result && result.success) {
                            const idx = this.offlineOrdersList.findIndex(o => o.id === order.id);
                            if (idx !== -1) {
                                this.offlineOrdersList[idx].synced = true;
                                this.offlineOrdersList[idx].synced_at = new Date().toISOString();
                            }
                            successCount++;
                        } else {
                            throw new Error(result?.error || 'Sync failed');
                        }
                    } catch (error) {
                        console.error('Sync failed for order:', order.id, error);
                        failCount++;
                    }
                }

                this.saveOfflineOrders();
                this.updateStats();

                if (failCount === 0) {
                    this.showNotification(`✓ ${successCount} order(s) synced!`, 'success');
                } else {
                    this.showNotification(
                        `${successCount} synced, ${failCount} failed.`,
                        failCount === pending.length ? 'error' : 'warning'
                    );
                }
            } catch (error) {
                console.error('Sync error:', error);
                this.showNotification('Sync failed: ' + error.message, 'error');
            } finally {
                this.syncing = false;
            }
        },

        async syncSingleOrder(order) {
            if (!this.online) {
                this.showNotification('Offline — cannot sync now.', 'warning');
                return;
            }

            if (this.syncing) {
                this.showNotification('Sync already in progress.', 'info');
                return;
            }

            this.syncing = true;

            try {
                const result = await this.callApi('/pos/sync-offline-order', order.data);

                if (result && result.success) {
                    const idx = this.offlineOrdersList.findIndex(o => o.id === order.id);
                    if (idx !== -1) {
                        this.offlineOrdersList[idx].synced = true;
                        this.offlineOrdersList[idx].synced_at = new Date().toISOString();
                    }
                    this.saveOfflineOrders();
                    this.updateStats();
                    this.showNotification('Order synced!', 'success');
                } else {
                    throw new Error(result?.error || 'Sync failed');
                }
            } catch (error) {
                console.error('Single sync failed:', error);
                this.showNotification('Sync failed: ' + error.message, 'error');
            }

            this.syncing = false;
        },

        // ── Checkout ──────────────────────────────────────────────────────────

        handleCheckout() {
            if (this.cart.length === 0) {
                this.showNotification('Cart is empty', 'error');
                return;
            }
            this.paymentModal.show = true;
            this.paymentModal.isOnline = this.online;
            this.paymentModal.amountTendered = this.getCartTotal();
            this.paymentModal.processing = false;
        },

        closePaymentModal() {
            this.paymentModal.show = false;
            this.paymentModal.processing = false;
        },

        // ── Cart Calculations ─────────────────────────────────────────────────

        getCartSubtotal() {
            return this.cart.reduce(
                (sum, item) => sum + (parseFloat(item.price) * parseInt(item.quantity)), 0
            );
        },

        getDiscountAmount() {
            const subtotal = this.getCartSubtotal();
            const discount = parseFloat(this.paymentModal.discount) || 0;
            return this.paymentModal.discountType === 'percentage'
                ? (subtotal * discount) / 100
                : discount;
        },

        getTaxAmount() {
            const taxRate = parseFloat(this.paymentModal.tax || 0);
            const taxable = this.getCartSubtotal() - this.getDiscountAmount();
            return taxable * (taxRate / 100);
        },

        getCartTotal() {
            return this.getCartSubtotal() - this.getDiscountAmount() + this.getTaxAmount();
        },

      

        get cartSubtotal() { return this.getCartSubtotal(); },
        get cartDiscountAmount() { return this.getDiscountAmount(); },
        get cartTaxAmount() { return this.getTaxAmount(); },
        get cartTotal() { return this.getCartTotal(); },

        // ── Process Payment ───────────────────────────────────────────────────

        async processPayment() {
            if (this.cart.length === 0) {
                this.showNotification('Cart is empty', 'error');
                return;
            }

            const total = this.getCartTotal();
            const tendered = parseFloat(this.paymentModal.amountTendered) || 0;

            if (this.paymentModal.paymentMethod === 'cash' && tendered < total) {
                this.showNotification('Amount tendered is less than total', 'error');
                return;
            }

            const customerId = this.selectedCustomer?.id ?? null;
            const customerName = this.selectedCustomer?.name ?? 'Walk-in Customer';

            const discountAmount = this.getDiscountAmount();
            const taxAmount = this.getTaxAmount();

            const orderData = {
                cart: JSON.parse(JSON.stringify(this.cart)),
                customer_id: customerId,
                payment_method: this.paymentModal.paymentMethod,
                amount_tendered: tendered - discountAmount,
                discount: parseFloat(this.paymentModal.discount) || 0,
                discountType: this.paymentModal.discountType || 'fixed',
                discount_amount: discountAmount,
                tax: parseFloat(this.paymentModal.tax) || 0,
                tax_amount: taxAmount,
                subtotal: this.getCartSubtotal(),
                total: total,
                 notes: this.paymentModal.notes || '',
                created_at: new Date().toISOString(),
            };

            if (!this.online) {
                // OFFLINE: save locally, then show receipt
                const offlineOrder = this.saveOfflineOrder(orderData, customerName);
                this.closePaymentModal();
                this.receiptModal.order = offlineOrder;
                this.receiptModal.show = true;
                return;
            }

            // ONLINE: send to server via API
            this.paymentModal.processing = true;
            this.showNotification('Processing payment...', 'info');

            try {
                const result = await this.callApi('/pos/sync-offline-order', orderData);

                if (result && result.success) {
                    this.clearCart();
                    this.closePaymentModal();
                    this.showNotification('Payment completed successfully!', 'success');

                    // Create receipt order object for online payment
                    const onlineReceiptOrder = {
                        id: result.order_id || 'order_' + Date.now(),
                        order_number: result.order_number || 'ORD-' + Date.now(),
                        data: orderData,
                        created_at: new Date().toISOString(),
                        customer_name: customerName,
                        customer_phone: this.selectedCustomer?.phone || '',
                        customer_email: this.selectedCustomer?.email || '',
                        customer_created_at: this.selectedCustomer?.created_at,
                        subtotal: this.getCartSubtotal(),
                        discount_amount: discountAmount,
                        tax_amount: taxAmount,
                        total: total,
                        paid_amount: tendered - discountAmount,
                         payment_method: this.paymentModal.paymentMethod,
                        items: orderData.cart.map(item => ({
                            product_name: item.name,
                            product_sku: item.sku,
                            quantity: item.quantity,
                            unit_price: item.price,
                            total: item.subtotal
                        }))
                    };

                    // Show the receipt modal
                    this.receiptModal.order = onlineReceiptOrder;
                    this.receiptModal.show = true;

                    await this.cacheAllData();
                } else {
                    throw new Error(result?.error || 'Payment failed');
                }
            } catch (error) {
                console.error('Payment error:', error);
                this.showNotification('Payment failed: ' + error.message, 'error');
            } finally {
                this.paymentModal.processing = false;
            }
        },

        closeReceiptModal() {
            this.receiptModal.show = false;
            this.receiptModal.order = null;
        },

        printReceipt() {
            const order = this.receiptModal.order;
            if (!order) return;

            const itemsHtml = (order.items || order.data?.cart || []).map(item => `
                <tr>
                    <td class="receipt-text-left receipt-item-name">
                        ${item.product_name || item.name}
                        ${(item.product_sku || item.sku) ? `<div class="receipt-item-sku">SKU: ${item.product_sku || item.sku}</div>` : ''}
                    </td>
                    <td class="receipt-text-center">${item.quantity}</td>
                    <td class="receipt-text-right">${this.businessSettings.currency_symbol}${this.formatNumber(item.unit_price || item.price)}</td>
                    <td class="receipt-text-right">${this.businessSettings.currency_symbol}${this.formatNumber(item.total || item.subtotal)}</td>
                </tr>
            `).join('');

            const customerSince = order.customer_created_at
                ? new Date(order.customer_created_at).toLocaleDateString()
                : '';

            const receiptHtml = `
                <div class="receipt-modal-overlay" style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; z-index: 1050; display: flex; align-items: center; justify-content: center; background-color: rgba(0,0,0,0.5);">
                    <div class="receipt-modal-dialog" style="width: 500px; margin: 1.75rem auto;">
                        <div class="receipt-modal-content" style="background: white; border-radius: 0.3rem; box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15);">
                            <div class="receipt-modal-header bg-primary" style="display: flex; align-items: center; justify-content: space-between; padding: 1rem; border-bottom: 1px solid #dee2e6; background-color: #007bff; color: white; border-top-left-radius: 0.3rem; border-top-right-radius: 0.3rem;">
                                <h5 class="receipt-modal-title" style="margin: 0; font-size: 1.25rem; font-weight: 500;">
                                    <i class="fas fa-receipt" style="margin-right: 0.5rem;"></i>
                                    Order Receipt - #${order.order_number}
                                </h5>
                                <button type="button" class="receipt-modal-close" onclick="document.querySelector('[x-data]').__x.$data.closeReceiptModal()" style="background: transparent; border: 0; font-size: 1.5rem; color: white; cursor: pointer;">
                                    <span>&times;</span>
                                </button>
                            </div>

                            <div class="receipt-modal-body" style="padding: 1rem;">
                                <div class="receipt-paper" style="font-family: 'Courier New', monospace; font-size: 12px; color: #000; background: white; border: 1px dashed #000; padding: 10px;">
                                    
                                    <!-- Header with Logo -->
                                    <div class="receipt-header" style="text-align: center; margin-bottom: 5px; padding-bottom: 10px; border-bottom: 2px dashed #000;">
                                        <div class="receipt-business-name" style="font-weight: bold; font-size: 16px;">${this.businessSettings.name}</div>
                                        ${this.businessSettings.address ? `<div class="receipt-business-info" style="font-size: 10px;">${this.businessSettings.address}</div>` : ''}
                                        <div class="receipt-business-info" style="font-size: 10px;">${this.businessSettings.phone}</div>
                                        <div class="receipt-business-info" style="font-size: 10px;">${this.businessSettings.email}</div>
                                    </div>

                                    <!-- Receipt Title -->
                                    <div class="receipt-title" style="text-align: center; margin-bottom: 15px; padding-bottom: 1px; border-bottom: 2px dashed #000; font-weight: bold; font-size: 16px;">
                                        <div>SALES RECEIPT</div>
                                    </div>

                                    <!-- Customer & Order Info -->
                                    <div class="receipt-info-grid" style="display: flex; gap: 15px; margin-bottom: 20px;">
                                        <!-- Customer Info -->
                                        <div class="receipt-info-box" style="flex: 1; border: 1px dashed #000; padding: 8px;">
                                            <div class="receipt-info-box-header" style="font-weight: bold; margin-bottom: 8px; border-bottom: 1px dashed #000; padding-bottom: 5px;">
                                                <span>👤 CUSTOMER</span>
                                            </div>
                                            
                                            ${order.customer_name !== 'Walk-in Customer' ? `
                                                <div class="receipt-customer-details" style="font-size: 11px;">
                                                    <div class="receipt-customer-name" style="font-weight: bold;">${order.customer_name}</div>
                                                    ${order.customer_phone ? `<div class="receipt-customer-phone" style="font-size: 10px; margin-top: 3px;">📞 ${order.customer_phone}</div>` : ''}
                                                    ${order.customer_email ? `<div class="receipt-customer-email" style="font-size: 9px; margin-top: 2px;">✉️ ${order.customer_email}</div>` : ''}
                                                     ${customerSince ? `<div class="receipt-customer-since" style="font-size: 8px; margin-top: 5px; color: #666; border-top: 1px dotted #ccc; padding-top: 3px;">Since: ${customerSince}</div>` : ''}
                                                </div>
                                            ` : `
                                                <div class="receipt-walkin-customer" style="text-align: center; padding: 10px; font-style: italic; font-size: 11px;">
                                                    Walk-in Customer
                                                </div>
                                            `}
                                        </div>

                                        <!-- Order Info -->
                                        <div class="receipt-info-box" style="flex: 1; border: 1px dashed #000; padding: 8px;">
                                            <div class="receipt-info-box-header" style="font-weight: bold; margin-bottom: 8px; border-bottom: 1px dashed #000; padding-bottom: 5px;">
                                                <span>📋 ORDER INFO</span>
                                            </div>
                                            
                                            <div class="receipt-order-details" style="font-size: 11px;">
                                                <div class="receipt-order-row" style="display: flex; justify-content: space-between; padding: 2px 0;">
                                                    <span>Order #:</span>
                                                    <span class="receipt-order-value" style="font-weight: bold;">${order.order_number}</span>
                                                </div>
                                                <div class="receipt-order-row" style="display: flex; justify-content: space-between; padding: 2px 0;">
                                                    <span>Date:</span>
                                                    <span>${new Date(order.created_at).toLocaleDateString()}</span>
                                                </div>
                                                <div class="receipt-order-row" style="display: flex; justify-content: space-between; padding: 2px 0;">
                                                    <span>Time:</span>
                                                    <span>${new Date(order.created_at).toLocaleTimeString()}</span>
                                                </div>
                                                <div class="receipt-order-row" style="display: flex; justify-content: space-between; padding: 2px 0;">
                                                    <span>Status:</span>
                                                    <span class="receipt-status-badge" style="border: 1px solid #000; padding: 2px 5px; font-size: 9px; font-weight: bold;">PAID</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Items Table -->
                                    <div class="receipt-items-section" style="margin-bottom: 15px;">
                                        <div class="receipt-section-title" style="font-weight: bold; margin-bottom: 5px;">ITEMS</div>
                                        <table class="receipt-items-table" style="width: 100%; border-collapse: collapse; font-size: 10px;">
                                            <thead>
                                                <tr>
                                                    <th class="receipt-text-left" style="border-bottom: 2px solid #000; padding: 4px; text-align: left;">Item</th>
                                                    <th class="receipt-text-center" style="border-bottom: 2px solid #000; padding: 4px; text-align: center;">Qty</th>
                                                    <th class="receipt-text-right" style="border-bottom: 2px solid #000; padding: 4px; text-align: right;">Price</th>
                                                    <th class="receipt-text-right" style="border-bottom: 2px solid #000; padding: 4px; text-align: right;">Total</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                ${itemsHtml}
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- Summary -->
                                    <div class="receipt-summary-section" style="margin-bottom: 15px;">
                                        <table class="receipt-summary-table" style="width: 100%; font-size: 11px;">
                                            <tr>
                                                <td style="padding: 2px 0;">Subtotal:</td>
                                                <td class="receipt-text-right" style="padding: 2px 0; text-align: right;">${this.businessSettings.currency_symbol}${this.formatNumber(order.subtotal || order.data?.subtotal)}</td>
                                            </tr>
                                            
                                            ${(order.discount_amount > 0) ? `
                                            <tr>
                                                <td style="padding: 2px 0;">Discount:</td>
                                                <td class="receipt-text-right receipt-discount" style="padding: 2px 0; text-align: right; color: #28a745;">-${this.businessSettings.currency_symbol}${this.formatNumber(order.discount_amount)}</td>
                                            </tr>
                                            ` : ''}
                                            
                                            ${(order.tax_amount > 0) ? `
                                            <tr>
                                                <td style="padding: 2px 0;">Tax:</td>
                                                <td class="receipt-text-right" style="padding: 2px 0; text-align: right;">+${this.businessSettings.currency_symbol}${this.formatNumber(order.tax_amount)}</td>
                                            </tr>
                                            ` : ''}
                                            
                                            <tr class="receipt-total-row" style="border-top: 2px solid #000; font-weight: bold;">
                                                <td class="receipt-total" style="padding-top: 5px;">TOTAL:</td>
                                                <td class="receipt-text-right receipt-total" style="padding-top: 5px; text-align: right;">${this.businessSettings.currency_symbol}${this.formatNumber(order.total || order.data?.total)}</td>
                                            </tr>
                                        </table>
                                    </div>
                                    
                                    <!-- Payment Info -->
                                    <div class="receipt-payment-section" style="margin-bottom: 15px; border-top: 1px dashed #000; padding-top: 10px;">
                                        <div class="receipt-section-title" style="font-weight: bold; margin-bottom: 5px;">PAYMENT</div>
                                        <table class="receipt-payment-table" style="width: 100%; font-size: 11px;">
                                            <tr>
                                                <td style="padding: 2px 0;">Method:</td>
                                                <td class="receipt-text-right" style="padding: 2px 0; text-align: right;">${order.payment_method || order.data?.payment_method || 'Cash'}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 2px 0;">Paid:</td>
                                                <td class="receipt-text-right receipt-paid" style="padding: 2px 0; text-align: right; font-weight: bold;">${this.businessSettings.currency_symbol}${this.formatNumber(order.paid_amount || order.data?.amount_tendered)}</td>
                                            </tr>
                                        
                                        </table>
                                    </div>

                                    <!-- Footer -->
                                    <div class="receipt-footer" style="text-align: center; padding-top: 10px; border-top: 2px dashed #000; font-size: 10px;">
                                        <div>${this.businessSettings.receipt_footer}</div>
                                        <div class="receipt-footer-time" style="margin-top: 2px;">${new Date().toLocaleString()}</div>
                                    </div>
                                </div>
                            </div>

                            <div class="receipt-modal-footer" style="display: flex; justify-content: flex-end; padding: 0.75rem; border-top: 1px solid #dee2e6; gap: 0.5rem;">
                                <button type="button" class="receipt-btn receipt-btn-primary" onclick="window.print()" style="display: inline-block; font-weight: 400; text-align: center; vertical-align: middle; cursor: pointer; padding: 0.375rem 0.75rem; font-size: 1rem; line-height: 1.5; border-radius: 0.25rem; color: #fff; background-color: #007bff; border-color: #007bff;">
                                    <i class="fas fa-print" style="margin-right: 0.25rem;"></i> Print Receipt
                                </button>
                                <button type="button" class="receipt-btn receipt-btn-secondary" onclick="document.querySelector('[x-data]').__x.$data.closeReceiptModal()" style="display: inline-block; font-weight: 400; text-align: center; vertical-align: middle; cursor: pointer; padding: 0.375rem 0.75rem; font-size: 1rem; line-height: 1.5; border-radius: 0.25rem; color: #fff; background-color: #6c757d; border-color: #6c757d;">
                                    <i class="fas fa-times" style="margin-right: 0.25rem;"></i> Close
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            const printWindow = window.open('', '_blank');
            printWindow.document.write(`
                <html>
                <head>
                    <title>Receipt ${order.order_number}</title>
                    <style>
                        @media print {
                            .receipt-modal-overlay {
                                background: none !important;
                                position: static !important;
                            }
                            .receipt-modal-dialog {
                                margin: 0 !important;
                                max-width: 100% !important;
                            }
                            .receipt-modal-header,
                            .receipt-modal-footer {
                                display: none !important;
                            }
                            .receipt-modal-content {
                                border: none !important;
                                box-shadow: none !important;
                            }
                            .receipt-modal-body {
                                padding: 0 !important;
                            }
                        }
                    </style>
                </head>
                <body style="margin: 0; padding: 20px; background: white;">
                    ${receiptHtml}
                    <script>
                        window.onload = function() {
                            setTimeout(function() {
                                window.print();
                                window.close();
                            }, 100);
                        };
                    <\/script>
                </body>
                </html>
            `);
            printWindow.document.close();
        },

        formatNumber(amount) {
            return parseFloat(amount || 0).toFixed(2);
        },

        // ── API Helper ────────────────────────────────────────────────────────

        async callApi(endpoint, data) {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

            const response = await fetch(endpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken || '',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(data),
            });

            if (!response.ok) {
                const error = await response.json().catch(() => ({}));
                throw new Error(error.error || `HTTP ${response.status}`);
            }

            return await response.json();
        },

        // ── Offline Orders ────────────────────────────────────────────────────

        saveOfflineOrder(orderData, customerName = 'Walk-in Customer') {
            if (this.selectedCustomer) {
                orderData.customer_id = this.selectedCustomer.id;
                orderData.customer_name = this.selectedCustomer.name;
                orderData.customer_phone = this.selectedCustomer.phone;
                orderData.customer_email = this.selectedCustomer.email || '';
                orderData.customer_created_at = this.selectedCustomer.created_at;
            } else {
                orderData.customer_id = null;
                orderData.customer_name = 'Walk-in Customer';
                orderData.customer_phone = '';
                orderData.customer_email = '';
                orderData.customer_created_at = null;
            }

            const offlineOrder = {
                id: 'offline_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9),
                order_number: 'OFF-' + new Date().getTime().toString().slice(-8),
                data: orderData,
                created_at: new Date().toISOString(),
                synced: false,
                customer_name: orderData.customer_name,
                customer_phone: orderData.customer_phone,
                customer_email: orderData.customer_email,
                customer_created_at: orderData.customer_created_at,
                subtotal: orderData.subtotal,
                discount_amount: orderData.discount_amount,
                tax_amount: orderData.tax_amount,
                total: orderData.total,
                paid_amount: orderData.amount_tendered,
                 payment_method: orderData.payment_method,
                items: orderData.cart.map(item => ({
                    product_name: item.name,
                    product_sku: item.sku,
                    quantity: item.quantity,
                    unit_price: item.price,
                    total: item.subtotal
                }))
            };

            this.offlineOrdersList.push(offlineOrder);
            this.saveOfflineOrders();
            this.updateStats();
            this.clearCart();
            this.selectedCustomer = null;
            localStorage.removeItem('pos_selected_customer');

            return offlineOrder;
        },

        // ── Customers Caching ─────────────────────────────────────────────────

        loadCachedCustomers() {
            try {
                const cached = localStorage.getItem('pos_cached_customers');
                this.cachedCustomers = cached ? JSON.parse(cached) : [];
            } catch {
                this.cachedCustomers = [];
            }
        },

        // ── Product Search ────────────────────────────────────────────────────

        async handleProductSearch(query) {
            this.productSearch = query;
            if (!query || query.length < 2) {
                this.searchResults = [];
                return;
            }

            if (this.online) {
                try {
                    const res = await fetch(
                        `/pos/products/search?q=${encodeURIComponent(query)}`,
                        { headers: { 'Accept': 'application/json' } }
                    );
                    if (res.ok) {
                        this.searchResults = await res.json();
                        return;
                    }
                } catch {
                    // Fall through to cached search
                }
            }

            const term = query.toLowerCase();
            this.searchResults = this.cachedProducts.filter(p =>
                p.name.toLowerCase().includes(term) ||
                (p.sku && p.sku.toLowerCase().includes(term)) ||
                (p.barcode && p.barcode.toLowerCase().includes(term))
            );
        },

        clearSearch() {
            this.productSearch = '';
            this.searchResults = [];
        },

        get filteredCachedProducts() {
            if (!this.selectedCategoryId) return this.cachedProducts;
            return this.cachedProducts.filter(p => p.category_id == this.selectedCategoryId);
        },

        // ── Product & Data Caching ────────────────────────────────────────────

        loadCachedProducts() {
            try {
                const cached = localStorage.getItem('pos_cached_products');
                this.cachedProducts = cached ? JSON.parse(cached) : [];
            } catch {
                this.cachedProducts = [];
            }
        },

        loadCachedCategories() {
            try {
                const cached = localStorage.getItem('pos_cached_categories');
                this.cachedCategories = cached ? JSON.parse(cached) : [];
            } catch {
                this.cachedCategories = [];
            }
        },

        async cacheAllData() {
            if (!this.online) return;

            try {
                const [products, categories, customers] = await Promise.all([
                    this.callApi('/pos/products/cache', {}),
                    this.callApi('/pos/categories/cache', {}),
                    this.callApi('/pos/customers/cache', {}),
                ]);

                if (products) {
                    localStorage.setItem('pos_cached_products', JSON.stringify(products));
                    this.cachedProducts = products;
                    this.filterProducts(); // ADD THIS
                }

                if (categories) {
                    localStorage.setItem('pos_cached_categories', JSON.stringify(categories));
                    this.cachedCategories = categories;
                }

                if (customers) {
                    localStorage.setItem('pos_cached_customers', JSON.stringify(customers));
                    this.cachedCustomers = customers;
                }

                localStorage.setItem('pos_last_sync', new Date().toISOString());
                this.updateStats();

            } catch (error) {
                console.error('Failed to cache data:', error);
            }
        },

        // ── Cart ──────────────────────────────────────────────────────────────

        loadCart() {
            try {
                const saved = localStorage.getItem('pos_cart');
                this.cart = saved ? JSON.parse(saved) : [];
            } catch {
                this.cart = [];
            }
        },

        saveCart() {
            localStorage.setItem('pos_cart', JSON.stringify(this.cart));
            this.updateStats();
        },

        addToCart(product) {
            const productId = parseInt(product.id || product.product_id || 0);
            const productName = String(product.name || '');
            const productPrice = parseFloat(product.selling_price || product.price || 0);
            const productSku = String(product.sku || '');

            if (!productId) {
                console.error('Invalid product ID', product);
                return;
            }

            const existingIndex = this.cart.findIndex(item => item.product_id === productId);

            if (existingIndex !== -1) {
                this.cart[existingIndex].quantity += 1;
                this.cart[existingIndex].subtotal = this.cart[existingIndex].price * this.cart[existingIndex].quantity;
            } else {
                this.cart.push({
                    product_id: productId,
                    id: productId,
                    name: productName,
                    price: productPrice,
                    quantity: 1,
                    subtotal: productPrice,
                    sku: productSku
                });
            }

            this.saveCart();
            this.cart = [...this.cart];
            this.clearSearch();
            this.showNotification(`${productName} added to cart`, 'success');
        },

        updateQuantity(index, newQuantity) {
            if (newQuantity <= 0) {
                this.removeFromCart(index);
                return;
            }
            if (this.cart[index]) {
                this.cart[index].quantity = newQuantity;
                this.cart[index].subtotal = this.cart[index].price * newQuantity;
                this.saveCart();
                this.cart = [...this.cart];
            }
        },

        removeFromCart(index) {
            this.cart.splice(index, 1);
            this.saveCart();
            this.cart = [...this.cart];
            this.showNotification('Item removed from cart', 'success');
        },

        clearCart() {
            this.cart = [];
            localStorage.removeItem('pos_cart');
            localStorage.removeItem('pos_discount');
            localStorage.removeItem('pos_discount_type');
            this.updateStats();
            this.cart = [...this.cart];
        },

        // ── Orders ────────────────────────────────────────────────────────────

        loadOfflineOrders() {
            try {
                const saved = localStorage.getItem('pos_offline_orders');
                this.offlineOrdersList = saved ? JSON.parse(saved) : [];
            } catch {
                this.offlineOrdersList = [];
            }
        },

        saveOfflineOrders() {
            localStorage.setItem('pos_offline_orders', JSON.stringify(this.offlineOrdersList));
            this.updateStats();
        },

        // ── Stats ─────────────────────────────────────────────────────────────

        updateStats() {
            const pending = this.offlineOrdersList.filter(o => !o.synced).length;
            this.stats = {
                pendingOrdersCount: pending,
                totalOffline: this.offlineOrdersList.length,
                cartCount: this.cart.length,
                lastSync: localStorage.getItem('pos_last_sync'),
            };
        },

        getOfflineStats() {
            return {
                pendingSync: this.stats.pendingOrdersCount,
                totalOffline: this.stats.totalOffline,
                cartCount: this.cart.length,
                lastSync: this.stats.lastSync,
            };
        },

        // ── Helpers ───────────────────────────────────────────────────────────

        formatMoney(amount) {
            return this.businessSettings.currency_symbol + this.formatNumber(amount || 0);
        },

        showNotification(message, type = 'info') {
            const colors = {
                success: 'bg-green-500',
                error: 'bg-red-500',
                warning: 'bg-yellow-500',
                info: 'bg-blue-500'
            };

            document.querySelectorAll('.pos-notification').forEach(el => el.remove());

            const el = document.createElement('div');
            el.className = `pos-notification fixed top-4 left-4 px-6 py-3 rounded-lg shadow-lg text-white z-50 flex items-center gap-2 ${colors[type] || colors.info}`;
            el.innerHTML = `<span>${type === 'success' ? '✓' : type === 'error' ? '✗' : 'ℹ'}</span><span>${message}</span>`;
            document.body.appendChild(el);

            setTimeout(() => {
                el.style.opacity = '0';
                el.style.transition = 'opacity 0.3s';
                setTimeout(() => el.remove(), 300);
            }, 3000);
        },

        getReceiptHtml() {
            const order = this.receiptModal.order;
            if (!order) return '';

            const items = order.items || order.data?.cart || [];
            const itemsHtml = items.map(item => `
        <tr>
            <td class="receipt-text-left receipt-item-name">
                ${item.product_name || item.name}
                ${(item.product_sku || item.sku) ? `<div class="receipt-item-sku">SKU: ${item.product_sku || item.sku}</div>` : ''}
            </td>
            <td class="receipt-text-center">${item.quantity}</td>
            <td class="receipt-text-right">${this.businessSettings.currency_symbol}${this.formatNumber(item.unit_price || item.price)}</td>
            <td class="receipt-text-right">${this.businessSettings.currency_symbol}${this.formatNumber(item.total || item.subtotal)}</td>
        </tr>
    `).join('');

            const customerSince = order.customer_created_at
                ? new Date(order.customer_created_at).toLocaleDateString()
                : '';

            return `
        <div class="receipt-paper" style="font-family: 'Courier New', monospace; font-size: 12px; color: #000; background: white; border: 1px dashed #000; padding: 10px;">
            <!-- Header -->
            <div class="receipt-header" style="text-align: center; margin-bottom: 5px; padding-bottom: 10px; border-bottom: 2px dashed #000;">
                <div class="receipt-business-name" style="font-weight: bold; font-size: 16px;">${this.businessSettings.name}</div>
                ${this.businessSettings.address ? `<div class="receipt-business-info" style="font-size: 10px;">${this.businessSettings.address}</div>` : ''}
                <div class="receipt-business-info" style="font-size: 10px;">${this.businessSettings.phone}</div>
                <div class="receipt-business-info" style="font-size: 10px;">${this.businessSettings.email}</div>
            </div>

            <!-- Title -->
            <div class="receipt-title" style="text-align: center; margin-bottom: 15px; padding-bottom: 1px; border-bottom: 2px dashed #000; font-weight: bold; font-size: 16px;">
                SALES RECEIPT
            </div>

            <!-- Customer & Order Info -->
            <div class="receipt-info-grid" style="display: flex; gap: 15px; margin-bottom: 20px;">
                <!-- Customer Info -->
                <div class="receipt-info-box" style="flex: 1; border: 1px dashed #000; padding: 8px;">
                    <div class="receipt-info-box-header" style="font-weight: bold; margin-bottom: 8px; border-bottom: 1px dashed #000; padding-bottom: 5px;">
                        <span>👤 CUSTOMER</span>
                    </div>
                    
                    ${order.customer_name !== 'Walk-in Customer' ? `
                        <div class="receipt-customer-details" style="font-size: 11px;">
                            <div class="receipt-customer-name" style="font-weight: bold;">${order.customer_name}</div>
                            ${order.customer_phone ? `<div class="receipt-customer-phone" style="font-size: 10px; margin-top: 3px;">📞 ${order.customer_phone}</div>` : ''}
                            ${order.customer_email ? `<div class="receipt-customer-email" style="font-size: 9px; margin-top: 2px;">✉️ ${order.customer_email}</div>` : ''}
                            ${customerSince ? `<div class="receipt-customer-since" style="font-size: 8px; margin-top: 5px; color: #666; border-top: 1px dotted #ccc; padding-top: 3px;">Since: ${customerSince}</div>` : ''}
                        </div>
                    ` : `
                        <div class="receipt-walkin-customer" style="text-align: center; padding: 10px; font-style: italic; font-size: 11px;">
                            Walk-in Customer
                        </div>
                    `}
                </div>

                <!-- Order Info -->
                <div class="receipt-info-box" style="flex: 1; border: 1px dashed #000; padding: 8px;">
                    <div class="receipt-info-box-header" style="font-weight: bold; margin-bottom: 8px; border-bottom: 1px dashed #000; padding-bottom: 5px;">
                        <span>📋 ORDER INFO</span>
                    </div>
                    
                    <div class="receipt-order-details" style="font-size: 11px;">
                        <div class="receipt-order-row" style="display: flex; justify-content: space-between; padding: 2px 0;">
                            <span>Order #:</span>
                            <span class="receipt-order-value" style="font-weight: bold;">${order.order_number}</span>
                        </div>
                        <div class="receipt-order-row" style="display: flex; justify-content: space-between; padding: 2px 0;">
                            <span>Date:</span>
                            <span>${new Date(order.created_at).toLocaleDateString()}</span>
                        </div>
                        <div class="receipt-order-row" style="display: flex; justify-content: space-between; padding: 2px 0;">
                            <span>Time:</span>
                            <span>${new Date(order.created_at).toLocaleTimeString()}</span>
                        </div>
                        <div class="receipt-order-row" style="display: flex; justify-content: space-between; padding: 2px 0;">
                            <span>Status:</span>
                            <span class="receipt-status-badge" style="border: 1px solid #000; padding: 2px 5px; font-size: 9px; font-weight: bold;">PAID</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Items Table -->
            <div class="receipt-items-section" style="margin-bottom: 15px;">
                <div class="receipt-section-title" style="font-weight: bold; margin-bottom: 5px;">ITEMS</div>
                <table class="receipt-items-table" style="width: 100%; border-collapse: collapse; font-size: 10px;">
                    <thead>
                        <tr>
                            <th class="receipt-text-left" style="border-bottom: 2px solid #000; padding: 4px; text-align: left;">Item</th>
                            <th class="receipt-text-center" style="border-bottom: 2px solid #000; padding: 4px; text-align: center;">Qty</th>
                            <th class="receipt-text-right" style="border-bottom: 2px solid #000; padding: 4px; text-align: right;">Price</th>
                            <th class="receipt-text-right" style="border-bottom: 2px solid #000; padding: 4px; text-align: right;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${itemsHtml}
                    </tbody>
                </table>
            </div>

            <!-- Summary -->
            <div class="receipt-summary-section" style="margin-bottom: 15px;">
                <table class="receipt-summary-table" style="width: 100%; font-size: 11px;">
                    <tr>
                        <td style="padding: 2px 0;">Subtotal:</td>
                        <td class="receipt-text-right" style="padding: 2px 0; text-align: right;">${this.businessSettings.currency_symbol}${this.formatNumber(order.subtotal || order.data?.subtotal)}</td>
                    </tr>
                    
                    ${(order.discount_amount > 0) ? `
                    <tr>
                        <td style="padding: 2px 0;">Discount:</td>
                        <td class="receipt-text-right receipt-discount" style="padding: 2px 0; text-align: right; color: #28a745;">-${this.businessSettings.currency_symbol}${this.formatNumber(order.discount_amount)}</td>
                    </tr>
                    ` : ''}
                    
                    ${(order.tax_amount > 0) ? `
                    <tr>
                        <td style="padding: 2px 0;">Tax:</td>
                        <td class="receipt-text-right" style="padding: 2px 0; text-align: right;">+${this.businessSettings.currency_symbol}${this.formatNumber(order.tax_amount)}</td>
                    </tr>
                    ` : ''}
                    
                    <tr class="receipt-total-row" style="border-top: 2px solid #000; font-weight: bold;">
                        <td class="receipt-total" style="padding-top: 5px;">TOTAL:</td>
                        <td class="receipt-text-right receipt-total" style="padding-top: 5px; text-align: right;">${this.businessSettings.currency_symbol}${this.formatNumber(order.total || order.data?.total)}</td>
                    </tr>
                </table>
            </div>
            
            <!-- Payment Info -->
            <div class="receipt-payment-section" style="margin-bottom: 15px; border-top: 1px dashed #000; padding-top: 10px;">
                <div class="receipt-section-title" style="font-weight: bold; margin-bottom: 5px;">PAYMENT</div>
                <table class="receipt-payment-table" style="width: 100%; font-size: 11px;">
                    <tr>
                        <td style="padding: 2px 0;">Method:</td>
                        <td class="receipt-text-right" style="padding: 2px 0; text-align: right;">${order.payment_method || order.data?.payment_method || 'Cash'}</td>
                    </tr>
                    <tr>
                        <td style="padding: 2px 0;">Paid:</td>
                        <td class="receipt-text-right receipt-paid" style="padding: 2px 0; text-align: right; font-weight: bold;">${this.businessSettings.currency_symbol}${this.formatNumber(order.paid_amount || order.data?.amount_tendered)}</td>
                    </tr>
               
                </table>
            </div>

            <!-- Footer -->
            <div class="receipt-footer" style="text-align: center; padding-top: 10px; border-top: 2px dashed #000; font-size: 10px;">
                <div>${this.businessSettings.receipt_footer}</div>
                <div class="receipt-footer-time" style="margin-top: 2px;">${new Date().toLocaleString()}</div>
            </div>
        </div>
    `;
        },


        searchCustomers() {
            const q = this.customerSearchQuery.toLowerCase().trim();
            if (!q || q.length < 2) {
                this.customerSearchResults = [];
                return;
            }

            // Search in cached customers
            this.customerSearchResults = this.cachedCustomers.filter(c =>
                c.name.toLowerCase().includes(q) ||
                (c.phone && c.phone.toLowerCase().includes(q)) ||
                (c.email && c.email.toLowerCase().includes(q))
            ).slice(0, 10);

            console.log('Search results:', this.customerSearchResults.length);
        },

        openAddCustomerModal() {
            this.customerModal.show = true;
            this.customerModal.mode = 'add';
            this.customerModal.name = '';
            this.customerModal.phone = '';
            this.customerModal.email = '';
            this.customerModal.saving = false;
        },

        closeCustomerModal() {
            this.customerModal.show = false;
        },

        async selectCustomer(customer) {
            this.selectedCustomer = {
                id: customer.id,
                name: customer.name,
                phone: customer.phone || '',
                email: customer.email || '',
                created_at: customer.created_at || new Date().toISOString()
            };

            localStorage.setItem('pos_selected_customer', JSON.stringify(this.selectedCustomer));
            this.customerSearchQuery = '';
            this.customerSearchResults = [];
            this.showNotification(`Customer: ${customer.name}`, 'success');

            if (this.online && window.Livewire) {
                try {
                    const wireElement = document.querySelector('[wire\\:id]');
                    if (wireElement) {
                        const wireId = wireElement.getAttribute('wire:id');
                        const component = Livewire.find(wireId);
                        if (component) {
                            await component.set('customerId', customer.id);
                        }
                    }
                } catch (e) {
                    console.error('Failed to sync customer with Livewire:', e);
                }
            }
        },

        clearSelectedCustomer() {
            this.selectedCustomer = null;
            localStorage.removeItem('pos_selected_customer');
            this.customerSearchQuery = '';
            this.customerSearchResults = [];

            if (this.online && window.Livewire) {
                try {
                    const wireElement = document.querySelector('[wire\\:id]');
                    if (wireElement) {
                        const wireId = wireElement.getAttribute('wire:id');
                        const component = Livewire.find(wireId);
                        if (component) {
                            component.set('customerId', null);
                        }
                    }
                } catch (e) {
                    console.error('Failed to clear customer in Livewire:', e);
                }
            }
        },

        toggleCustomerSearch() {
            this.showCustomerSearch = !this.showCustomerSearch;
            if (this.showCustomerSearch) {
                // Focus the input after it's visible
                this.$nextTick(() => {
                    if (this.$refs.customerSearchInput) {
                        this.$refs.customerSearchInput.focus();
                    }
                });
            } else {
                // Clear search when closing
                this.customerSearchQuery = '';
                this.customerSearchResults = [];
            }
        },

        // Add this method:
        filterProducts() {
            if (this.selectedCategoryId === null) {
                this.displayProducts = [...this.cachedProducts];
            } else {
                this.displayProducts = this.cachedProducts.filter(p => p.category_id == this.selectedCategoryId);
            }
        },

    };
}

window.posOffline = posOffline;