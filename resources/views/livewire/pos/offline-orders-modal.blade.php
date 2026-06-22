<div x-show="showOfflineOrders" class="fixed inset-0 z-50 overflow-y-auto" style="display:none">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75" @click="showOfflineOrders = false"></div>
            <div class="relative bg-white dark:bg-gray-800 rounded-lg max-w-4xl w-full p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Offline Orders</h3>
                    <button @click="showOfflineOrders = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Order #</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Customer</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Total</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            <template x-for="order in offlineOrdersList" :key="order.id">
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white" x-text="new Date(order.created_at).toLocaleString()"></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white" x-text="order.order_number"></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white" x-text="order.customer_name"></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white" x-text="formatMoney(order.total)"></td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                            :class="order.synced ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'"
                                            x-text="order.synced ? 'Synced' : 'Pending'"></span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <button x-show="!order.synced && online" @click="syncSingleOrder(order)" class="text-green-600 hover:text-green-900 dark:text-green-400">Sync</button>
                                        <span x-show="order.synced" class="text-gray-400">—</span>
                                    </td>
                                </tr>
                            </template>
                            <template x-if="offlineOrdersList.length === 0">
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">No offline orders found</td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
                <div class="mt-4 flex justify-end space-x-3">
                    <button x-show="online && getOfflineStats().pendingSync > 0" @click="syncOfflineData()" :disabled="syncing"
                        class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 disabled:opacity-50 transition-colors">
                        <span x-show="!syncing">Sync All</span>
                        <span x-show="syncing">Syncing...</span>
                    </button>
                    <button @click="showOfflineOrders = false" class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 transition-colors">Close</button>
                </div>
            </div>
        </div>
    </div>
