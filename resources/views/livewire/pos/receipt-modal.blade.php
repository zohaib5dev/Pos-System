  <div x-show="receiptModal.show" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display:none">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75" @click="closeReceiptModal()"></div>
            <div class="relative bg-white dark:bg-gray-800 rounded-lg max-w-md w-full shadow-xl">
                <div class="flex justify-between items-center p-4 border-b border-gray-200 dark:border-gray-700 bg-primary" style="background-color: #007bff; color: white;">
                    <h5 class="text-lg font-medium flex items-center gap-2">
                        <i class="fas fa-receipt"></i>
                        Order Receipt - <span x-text="receiptModal.order?.order_number"></span>
                    </h5>
                    <button @click="closeReceiptModal()" class="text-white hover:text-gray-200 text-2xl">&times;</button>
                </div>

                <div class="p-4 max-h-[70vh] overflow-y-auto" x-html="getReceiptHtml()"></div>

                <div class="flex justify-end gap-3 p-4 border-t border-gray-200 dark:border-gray-700">
                    <button @click="printReceipt()" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors flex items-center gap-2">
                        <i class="fas fa-print"></i> Print Receipt
                    </button>
                    <button @click="closeReceiptModal()" class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 transition-colors">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>