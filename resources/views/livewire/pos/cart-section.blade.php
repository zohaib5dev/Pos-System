<div class="cart-section md:relative md:w-96 bg-white dark:bg-gray-800 border-l border-gray-200 dark:border-gray-700 flex flex-col transition-all duration-300" id="cartSection">

    <!-- Mobile header -->
    <div class="md:hidden p-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
        <h2 class="font-semibold text-gray-700 dark:text-gray-300">Cart</h2>
        <button onclick="toggleCart()" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>

    <div class="p-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 transition-colors">
        <div class="flex items-center justify-between mb-2">
            <h2 class="font-semibold text-gray-700 dark:text-gray-300">Customer</h2>
            <button @click="openAddCustomerModal()"
                class="text-xs px-2 py-1 bg-indigo-100 dark:bg-indigo-900 text-indigo-700 dark:text-indigo-300 rounded hover:bg-indigo-200 transition-colors flex items-center gap-1">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                </svg>
                Add New
            </button>
        </div>

        <!-- Customer Display Area -->
        <template x-if="!selectedCustomer">
            <div>
                <div class="bg-white dark:bg-gray-800 rounded-lg p-3 border border-gray-200 dark:border-gray-700 shadow-sm mb-2">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <div>
                                <div class="font-semibold text-gray-900 dark:text-white">Walk-in Customer</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">No customer selected</div>
                            </div>
                        </div>
                        <button @click="showCustomerSearch = !showCustomerSearch"
                            class="p-2 text-gray-500 hover:text-indigo-600 dark:text-gray-400 dark:hover:text-indigo-400 transition-colors"
                            :class="{'text-indigo-600 dark:text-indigo-400': showCustomerSearch}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <div x-show="showCustomerSearch" x-collapse class="mt-2">
                    <div class="relative">
                        <div class="relative">
                            <input type="text"
                                x-model="customerSearchQuery"
                                @input.debounce.300ms="searchCustomers()"
                                @keydown.escape="showCustomerSearch = false"
                                placeholder="Search by name or phone..."
                                class="w-full pl-10 pr-10 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                x-ref="customerSearchInput">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            <button @click="showCustomerSearch = false; customerSearchQuery = ''; customerSearchResults = []"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>

                        <div x-show="customerSearchResults.length > 0 && customerSearchQuery.length >= 2"
                            class="absolute z-20 mt-1 w-full bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 max-h-60 overflow-y-auto">
                            <template x-for="customer in customerSearchResults" :key="customer.id">
                                <div @click="selectCustomer(customer)"
                                    class="p-3 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer border-b dark:border-gray-700 last:border-b-0 transition-colors">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-8 h-8 bg-indigo-100 dark:bg-indigo-900 rounded-full flex items-center justify-center flex-shrink-0">
                                            <span class="text-indigo-800 dark:text-indigo-200 font-medium text-sm" x-text="customer.name.substring(0,2).toUpperCase()"></span>
                                        </div>
                                        <div class="flex-1">
                                            <div class="font-medium text-gray-900 dark:text-white" x-text="customer.name"></div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400" x-text="customer.phone || 'No phone'"></div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                            <div x-show="customerSearchResults.length === 0 && customerSearchQuery.length >= 2"
                                class="p-4 text-center text-gray-500 dark:text-gray-400">
                                No customers found.
                                <button @click="openAddCustomerModal(); showCustomerSearch = false" class="text-indigo-600 hover:underline font-medium">Add new customer</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        <template x-if="selectedCustomer">
            <div class="bg-white dark:bg-gray-800 rounded-lg p-3 border border-indigo-200 dark:border-indigo-800 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-indigo-100 dark:bg-indigo-900 rounded-full flex items-center justify-center">
                            <span class="text-indigo-800 dark:text-indigo-200 font-bold text-lg" x-text="selectedCustomer.name.substring(0,2).toUpperCase()"></span>
                        </div>
                        <div>
                            <div class="font-semibold text-gray-900 dark:text-white" x-text="selectedCustomer.name"></div>
                            <div class="text-xs text-gray-500 dark:text-gray-400" x-text="selectedCustomer.phone"></div>
                        </div>
                    </div>
                    <button @click="clearSelectedCustomer()"
                        class="p-1 text-gray-400 hover:text-red-500 transition-colors"
                        title="Clear customer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </template>
    </div>
    <div class="flex-1 overflow-y-auto p-4">
        <h2 class="font-semibold text-gray-700 dark:text-gray-300 mb-3">Cart Items</h2>
        <template x-if="cart.length === 0">
            <div class="text-center text-gray-500 dark:text-gray-400 py-8">
                <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                <p class="mt-2">Cart is empty</p>
            </div>
        </template>
        <template x-for="(item, index) in cart" :key="index">
            <div class="flex items-center space-x-3 mb-3 p-2 bg-gray-50 dark:bg-gray-700 rounded-lg">
                <div class="flex-1 min-w-0">
                    <div class="font-medium text-sm text-gray-900 dark:text-white truncate" x-text="item.name"></div>
                    <div class="text-xs text-gray-500 dark:text-gray-400" x-text="formatMoney(item.price) + ' each'"></div>
                </div>
                <div class="flex items-center space-x-2">
                    <button @click="updateQuantity(index, item.quantity - 1)" class="w-6 h-6 rounded-full bg-gray-200 dark:bg-gray-600 flex items-center justify-center hover:bg-gray-300 transition-colors">-</button>
                    <span class="w-8 text-center text-gray-900 dark:text-white" x-text="item.quantity"></span>
                    <button @click="updateQuantity(index, item.quantity + 1)" class="w-6 h-6 rounded-full bg-gray-200 dark:bg-gray-600 flex items-center justify-center hover:bg-gray-300 transition-colors">+</button>
                </div>
                <div class="w-20 text-right font-medium text-gray-900 dark:text-white" x-text="formatMoney(item.price * item.quantity)"></div>
                <button @click="removeFromCart(index)" class="text-red-500 hover:text-red-700 dark:text-red-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </button>
            </div>
        </template>
    </div>

    <div class="border-t border-gray-200 dark:border-gray-700 p-4 bg-gray-50 dark:bg-gray-900 transition-colors">
        <div class="space-y-2">
            <div class="flex justify-between text-sm">
                <span class="text-gray-600 dark:text-gray-400">Subtotal:</span>
                <span class="font-medium text-gray-900 dark:text-white" x-text="formatMoney(cartSubtotal)"></span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="text-gray-600 dark:text-gray-400">Tax ({{ $tax }}%):</span>
                <span class="font-medium text-gray-900 dark:text-white" x-text="formatMoney(cartTaxAmount)"></span>
            </div>
            <div class="flex justify-between text-lg font-bold pt-2 border-t border-gray-200 dark:border-gray-700">
                <span class="text-gray-900 dark:text-white">Total:</span>
                <span class="text-indigo-600 dark:text-indigo-400" x-text="formatMoney(cartTotal)"></span>
            </div>
        </div>
        <div class="flex space-x-2 mt-4">
            <button @click="clearCart()" class="flex-1 px-3 sm:px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm sm:text-base transition-colors">Clear</button>
            <button @click="handleCheckout()" class="flex-1 px-3 sm:px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm sm:text-base transition-colors">Checkout</button>
        </div>
    </div>

      @include('livewire.pos.payment-modal')

   

</div>