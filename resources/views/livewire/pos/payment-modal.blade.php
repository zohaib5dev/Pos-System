<div x-show="paymentModal.show" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display:none">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closePaymentModal()"></div>

        <div class="relative bg-white dark:bg-gray-800 rounded-2xl max-w-md w-full p-6 shadow-2xl transform transition-all">
            <div class="flex justify-between items-center mb-6">
                <div class="flex items-center gap-3">
                    <div class="flex items-center gap-2 px-3 py-1.5 rounded-full text-sm font-medium"
                        :class="paymentModal.isOnline ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400'">
                        <template x-if="paymentModal.isOnline">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </template>
                        <template x-if="!paymentModal.isOnline">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </template>
                        <span x-text="paymentModal.isOnline ? 'Online' : 'Offline'"></span>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Checkout</h3>
                </div>
                <button @click="closePaymentModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <div class="space-y-5">
                <!-- Order Summary Card -->
                <div class="bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-700/50 dark:to-gray-800 rounded-xl p-4 space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">Subtotal</span>
                        <span class="font-medium text-gray-900 dark:text-white" x-text="formatMoney(cartSubtotal)"></span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">Discount</span>
                        <span class="font-medium text-red-600 dark:text-red-400" x-text="'- ' + formatMoney(getDiscountAmount())"></span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">Tax (<span x-text="paymentModal.tax"></span>%)</span>
                        <span class="font-medium text-gray-900 dark:text-white" x-text="formatMoney(getTaxAmount())"></span>
                    </div>
                    <div class="flex justify-between font-bold pt-2 border-t border-gray-200 dark:border-gray-600">
                        <span class="text-gray-900 dark:text-white">Total</span>
                        <span class="text-2xl text-indigo-600 dark:text-indigo-400" x-text="formatMoney(cartTotal)"></span>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Payment Method</label>
                    <div class="grid grid-cols-4 gap-2">
                        <!-- Cash -->
                        <button @click="paymentModal.paymentMethod = 'cash'"
                            class="flex flex-col items-center p-3 rounded-xl border-2 transition-all duration-200"
                            :class="paymentModal.paymentMethod === 'cash' 
                                ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/20 text-indigo-700 dark:text-indigo-400' 
                                : 'border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600 text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700/50'">
                            <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            <span class="text-xs font-medium">Cash</span>
                        </button>

                        <!-- Card -->
                        <button @click="paymentModal.paymentMethod = 'credit-card'"
                            class="flex flex-col items-center p-3 rounded-xl border-2 transition-all duration-200"
                            :class="paymentModal.paymentMethod === 'credit-card' 
                                ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/20 text-indigo-700 dark:text-indigo-400' 
                                : 'border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600 text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700/50'">
                            <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                            </svg>
                            <span class="text-xs font-medium">Card</span>
                        </button>

                        <!-- Mobile Payment -->
                        <button @click="paymentModal.paymentMethod = 'mobile-payment'"
                            class="flex flex-col items-center p-3 rounded-xl border-2 transition-all duration-200"
                            :class="paymentModal.paymentMethod === 'mobile-payment' 
                                ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/20 text-indigo-700 dark:text-indigo-400' 
                                : 'border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600 text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700/50'">
                            <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            </svg>
                            <span class="text-xs font-medium">Mobile</span>
                        </button>

                        <!-- Bank Transfer -->
                        <button @click="paymentModal.paymentMethod = 'bank-transfer'"
                            class="flex flex-col items-center p-3 rounded-xl border-2 transition-all duration-200"
                            :class="paymentModal.paymentMethod === 'bank-transfer' 
                                ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/20 text-indigo-700 dark:text-indigo-400' 
                                : 'border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600 text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700/50'">
                            <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4v5H3V7z"></path>
                            </svg>
                            <span class="text-xs font-medium">Bank</span>
                        </button>
                    </div>
                </div>

                <!-- Discount Section - Enhanced -->
                <div class="border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden">
                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l5 5a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-5-5A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                            </svg>
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Discount</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <template x-if="paymentModal.discount > 0">
                                <span class="text-xs font-medium px-2 py-1 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 rounded-full"
                                    x-text="paymentModal.discountType === 'fixed' ? formatMoney(paymentModal.discount) : paymentModal.discount + '%'"></span>
                            </template>

                        </div>
                    </div>

                    <div                    
                        class="p-2 border-t border-gray-200 dark:border-gray-700">
                        <div class="flex gap-2 mb-3">
                            <button @click="paymentModal.discountType = 'fixed'"
                                class="flex-1 py-1 px-1 rounded-lg text-sm font-medium transition-all duration-200"
                                :class="paymentModal.discountType === 'fixed' 
                        ? 'bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-400 border-2 border-indigo-300 dark:border-indigo-700' 
                        : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 border-2 border-transparent hover:bg-gray-200 dark:hover:bg-gray-600'">
                                Fixed ($)
                            </button>
                            <button @click="paymentModal.discountType = 'percentage'"
                                class="flex-1 py-1 px-1 rounded-lg text-sm font-medium transition-all duration-200"
                                :class="paymentModal.discountType === 'percentage' 
                        ? 'bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-400 border-2 border-indigo-300 dark:border-indigo-700' 
                        : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 border-2 border-transparent hover:bg-gray-200 dark:hover:bg-gray-600'">
                                Percentage (%)
                            </button>

                            <div class="relative">
                                <template x-if="paymentModal.discountType === 'fixed'">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 dark:text-gray-400 font-medium">$</span>
                                </template>
                                <input type="number"
                                    x-model.number="paymentModal.discount"
                                    min="0"
                                    :max="paymentModal.discountType === 'fixed' ? cartSubtotal : 100"
                                    :step="paymentModal.discountType === 'fixed' ? '0.01' : '1'"
                                    class=" text-center w-25  border-2 border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-xl focus:border-indigo-400 focus:ring-indigo-200 transition-all"
                                    :class="{'pl-8': paymentModal.discountType === 'fixed'}"
                                    :placeholder="paymentModal.discountType === 'fixed' ? 'Enter amount' : 'Enter percentage'"
                                    @input="validateDiscount()">
                                <template x-if="paymentModal.discountType === 'percentage'">
                                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 dark:text-gray-400 font-medium">%</span>
                                </template>
                            </div>
                        </div>


                    </div>
                </div>

                <!-- Notes -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Notes (optional)</label>
                    <textarea x-model="paymentModal.notes" rows="2"
                        class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg text-sm resize-none"
                        placeholder="Add any notes..."></textarea>
                </div>

                <!-- Offline Warning -->
                <template x-if="!paymentModal.isOnline">
                    <div class="flex items-start gap-3 text-xs bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-xl p-3">
                        <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        <div class="text-yellow-700 dark:text-yellow-400">
                            <span class="font-medium">Offline Mode:</span> Order will be saved locally and synced when you're back online.
                        </div>
                    </div>
                </template>

                <!-- Action Buttons -->
                <div class="flex gap-3 pt-2">
                    <button @click="closePaymentModal()"
                        class="flex-1 px-4 py-3 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors font-medium">
                        Cancel
                    </button>
                    <button @click="processPayment()"
                        :disabled="paymentModal.amountTendered < cartTotal || paymentModal.processing"
                        class="flex-1 px-4 py-3 rounded-xl text-white font-medium transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed transform hover:scale-105 active:scale-95 flex items-center justify-center gap-2"
                        :class="paymentModal.isOnline 
                                ? 'bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 shadow-lg shadow-indigo-200 dark:shadow-indigo-900/30' 
                                : 'bg-gradient-to-r from-yellow-600 to-yellow-700 hover:from-yellow-700 hover:to-yellow-800 shadow-lg shadow-yellow-200 dark:shadow-yellow-900/30'">
                        <span x-show="!paymentModal.processing">

                            <span x-text="paymentModal.isOnline ? 'Complete Payment' : 'Save Offline'"></span>
                        </span>
                        <span x-show="paymentModal.processing" class="flex items-center gap-2">
                            <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span>Processing...</span>
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>