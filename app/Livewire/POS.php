<?php

namespace App\Livewire;

use App\Models\BusinessSetting;
use Livewire\Component;
use App\Models\Product;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\TaxRate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\PaymentMethod;
use Illuminate\Support\Facades\Log;

class POS extends Component
{
    public $cart = [];
    public $customerId = null;
    public $customerSearch = '';
    public $productSearch = '';
    public $discount = 0;
    public $discountType = 'fixed';
    public $tax = 0;
    public $showPaymentModal = false;
    public $showCustomerModal = false;
    public $showReceiptModal = false;
    public $lastOrder = null;
    public $categoryFilter = null;
    public $showCustomerSearch = false;
    public $showAddCustomerModal = false;
    public $newCustomerName = '';
    public $newCustomerPhone = '';
    public $newCustomerEmail = '';
    public $newCustomerAddress = '';
    public bool $darkMode = false;
 
    protected $listeners = [
        'cart-updated' => 'handleCartUpdated',
    ];

    public function mount($orderId = null): void
    {
        $this->darkMode = session('dark_mode', false);
        $this->loadDefaultTaxRate();

        if ($orderId) {
            $this->loadOrder($orderId);
        } else {
            $this->loadCartFromSession();
        }
    }

    public function loadDefaultTaxRate(): void
    {
        $defaultTax = TaxRate::where('is_default', true)
            ->where('is_active', true)
            ->first();
        $this->tax = $defaultTax ? (float) $defaultTax->rate : 0;
    }

    public function toggleDarkMode(): void
    {
        $this->darkMode = !$this->darkMode;
        session(['dark_mode' => $this->darkMode]);
        $this->dispatch('dark-mode-toggled', darkMode: $this->darkMode);
    }

    public function loadCartFromSession(): void
    {
        $this->cart       = session()->get('pos_cart', []);
        $this->customerId = session()->get('pos_customer_id');
        $this->discount   = session()->get('pos_discount', 0);
    }

    public function saveCartToSession(): void
    {
        session()->put('pos_cart', $this->cart);
        session()->put('pos_customer_id', $this->customerId);
        session()->put('pos_discount', $this->discount);
    }

    public function toggleCustomerSearch(): void
    {
        $this->showCustomerSearch = !$this->showCustomerSearch;
        if (!$this->showCustomerSearch) {
            $this->customerSearch = '';
        }
    }

    public function toggleAddCustomerModal(): void
    {
        $this->showAddCustomerModal = !$this->showAddCustomerModal;
    }

    public function clearCustomerSearch(): void
    {
        $this->customerSearch     = '';
        $this->showCustomerSearch = false;
    }

    public function toggleCustomerModal(): void
    {
        $this->showCustomerModal = !$this->showCustomerModal;
        if (!$this->showCustomerModal) {
            $this->customerSearch = '';
        }
    }

    public function saveNewCustomer(): void
    {
        try {
            $this->validate([
                'newCustomerName'  => 'required|min:3',
                'newCustomerPhone' => 'required|min:10',
                'newCustomerEmail' => 'nullable|email|unique:customers,email',
            ]);

            $customer = Customer::create([
                'customer_code' => 'CUS-' . date('Ymd') . '-' . rand(1000, 9999),
                'name'          => $this->newCustomerName,
                'phone'         => $this->newCustomerPhone,
                'email'         => $this->newCustomerEmail ?: null,
             ]);

            $this->selectCustomer($customer->id);
            $this->reset(['newCustomerName', 'newCustomerPhone', 'newCustomerEmail', 'newCustomerAddress']);
            $this->showAddCustomerModal = false;
            $this->dispatch('notify', message: 'Customer added successfully!', type: 'success');

        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Error saving customer: ' . $e->getMessage());
            $this->dispatch('notify', message: 'Error: ' . $e->getMessage(), type: 'error');
        }
    }

    public function toggleReceiptModal(): void
    {
        $this->showReceiptModal = !$this->showReceiptModal;
    }

    public function addToCart($productId, $quantity = 1): void
    {
        $product = Product::find($productId);

        if (!$product) {
            $this->dispatch('notify', message: 'Product not found', type: 'error');
            return;
        }

        if ($product->stock_quantity < $quantity && !$product->allow_out_of_stock) {
            $this->dispatch('notify', message: 'Insufficient stock', type: 'error');
            return;
        }

        $existingItemKey = null;
        foreach ($this->cart as $key => $item) {
            if ($item['product_id'] == $productId) {
                $existingItemKey = $key;
                break;
            }
        }

        if ($existingItemKey !== null) {
            $this->cart[$existingItemKey]['quantity'] += $quantity;
            $this->cart[$existingItemKey]['subtotal']  =
                $this->cart[$existingItemKey]['quantity'] * $this->cart[$existingItemKey]['price'];
        } else {
            $this->cart[] = [
                'product_id' => $product->id,
                'name'       => $product->name,
                'price'      => $product->selling_price,
                'quantity'   => $quantity,
                'subtotal'   => $product->selling_price * $quantity,
                'sku'        => $product->sku,
            ];
        }

        $this->saveCartToSession();
        $this->productSearch = '';
        $this->dispatch('cart-updated', cart: $this->cart);
        $this->dispatch('notify', message: 'Product added to cart', type: 'success');
    }

    public function updateQuantity($key, $quantity): void
    {
        if ($quantity <= 0) {
            $this->removeFromCart($key);
            return;
        }
        if (isset($this->cart[$key])) {
            $this->cart[$key]['quantity'] = $quantity;
            $this->cart[$key]['subtotal'] = $this->cart[$key]['quantity'] * $this->cart[$key]['price'];
            $this->saveCartToSession();
            $this->dispatch('cart-updated', cart: $this->cart);
        }
    }

    public function removeFromCart($key): void
    {
        unset($this->cart[$key]);
        $this->cart = array_values($this->cart);
        $this->saveCartToSession();
        $this->dispatch('cart-updated', cart: $this->cart);
        $this->dispatch('notify', message: 'Item removed from cart', type: 'success');
    }

    public function clearCart(): void
    {
        $this->cart           = [];
        $this->customerId     = null;
        $this->discount       = 0;
        $this->customerSearch = '';
        $this->productSearch  = '';
        $this->categoryFilter = null;
        $this->saveCartToSession();
        $this->dispatch('cart-updated', cart: $this->cart);
        $this->dispatch('notify', message: 'Cart cleared', type: 'success');
    }

    public function selectCustomer($customerId): void
    {
        $this->customerId     = $customerId;
        $this->customerSearch = '';
        $this->showCustomerSearch = false;
        $this->saveCartToSession();
        $this->dispatch('notify', message: 'Customer selected', type: 'success');
    }

    // ── Computed Properties ───────────────────────────────────────────────────

    public function getCustomersProperty()
    {
        if (strlen($this->customerSearch) < 2) {
            return collect([]);
        }
        return Customer::where('name',  'like', '%' . $this->customerSearch . '%')
            ->orWhere('phone', 'like', '%' . $this->customerSearch . '%')
            ->orWhere('email', 'like', '%' . $this->customerSearch . '%')
            ->limit(10)
            ->get();
    }

    public function getSearchResultsProperty()
    {
        if (strlen($this->productSearch) < 2) {
            return collect([]);
        }
        $query = Product::where('is_active', true)
            ->where(function ($q) {
                $q->where('name',    'like', '%' . $this->productSearch . '%')
                  ->orWhere('sku',     'like', '%' . $this->productSearch . '%')
                  ->orWhere('barcode', 'like', '%' . $this->productSearch . '%');
            });
        if ($this->categoryFilter) {
            $query->where('category_id', $this->categoryFilter);
        }
        return $query->limit(10)->get();
    }

    public function getQuickProductsProperty()
    {
        $query = Product::where('is_active', true)
            ->where('stock_quantity', '>', 0);
        if ($this->categoryFilter) {
            $query->where('category_id', $this->categoryFilter);
        }
        return $query->limit(20)->get();
    }

    public function getSubtotalProperty(): float
    {
        return (float) array_sum(array_column($this->cart, 'subtotal'));
    }

    public function getDiscountAmountProperty(): float
    {
        if ($this->discountType === 'percentage') {
            return ($this->subtotal * (float) $this->discount) / 100;
        }
        return (float) $this->discount;
    }

    public function getTaxAmountProperty(): float
    {
        $taxable = $this->subtotal - $this->discount_amount;
        return $taxable * ($this->tax / 100);
    }

    public function getTotalProperty(): float
    {
        return $this->subtotal - $this->discount_amount + $this->tax_amount;
    }

    // ── Payment ───────────────────────────────────────────────────────────────

    /**
     * Called directly from Alpine via $wire.processPayment(orderData).
     * NOT registered as a Livewire event listener — that causes
     * BindingResolutionException because the container tries to inject
     * the $paymentData parameter as a service dependency.
     *
     * @param  array  $paymentData  Full order payload sent from Alpine
     * @return array{success: bool, error?: string}
     */
    public function processPayment(array $paymentData = []): array
    {
        Log::info('processPayment called', ['keys' => array_keys($paymentData)]);

        try {
            if (empty($paymentData)) {
                return ['success' => false, 'error' => 'No payment data received'];
            }

            $cart          = $paymentData['cart']           ?? [];
            $customerId    = $paymentData['customer_id']    ?? $this->customerId;
            $paymentMethod = $paymentData['paymentMethod']  ?? $paymentData['payment_method'] ?? 'cash';
            $amountTendered = (float) ($paymentData['amountTendered'] ?? $paymentData['amount_tendered'] ?? 0);
            $discount      = (float) ($paymentData['discount']        ?? 0);
            $discountType  = $paymentData['discountType']   ?? $paymentData['discount_type'] ?? 'fixed';
            $notes         = $paymentData['notes']          ?? '';

            if (empty($cart)) {
                Log::error('Cart is empty in processPayment');
                $this->dispatch('notify', message: 'Cart is empty', type: 'error');
                return ['success' => false, 'error' => 'Cart is empty'];
            }

            // Recalculate totals server-side from the cart data (don't trust client totals)
            $subtotal = array_sum(array_column($cart, 'subtotal'));
            $discountAmount = $discountType === 'percentage'
                ? ($subtotal * $discount) / 100
                : $discount;
            $taxAmount = ($subtotal - $discountAmount) * ($this->tax / 100);
            $total     = $subtotal - $discountAmount + $taxAmount;
            $change    = max(0, $amountTendered - $total);

            Log::info('Payment totals', compact('subtotal', 'discountAmount', 'taxAmount', 'total'));

            if ($amountTendered < $total && $paymentMethod === 'cash') {
                $this->dispatch('notify', message: 'Insufficient amount', type: 'error');
                return ['success' => false, 'error' => 'Insufficient amount'];
            }

            DB::transaction(function () use (
                $cart, $customerId, $paymentMethod, $amountTendered,
                $discount, $discountType, $discountAmount, $taxAmount,
                $total, $change, $notes
            ) {
                $defaultTax = TaxRate::where('is_default', true)->where('is_active', true)->first();

                $order = Order::create([
                    'order_number'    => 'ORD-' . time() . '-' . rand(1000, 9999),
                    'customer_id'     => $customerId,
                    'order_date'      => now(),
                    'subtotal'        => $subtotal ?? 0,
                    'discount_type'   => $discountType,
                    'discount_value'  => $discount,
                    'discount_amount' => $discountAmount,
                    'tax_rate_id'     => $defaultTax->id ?? null,
                    'tax_amount'      => $taxAmount,
                    'total_amount'    => $total,
                    'paid_amount'     => $amountTendered,
                    'change_amount'   => $change,
                    'status'          => 'completed',
                    'payment_status'  => 'paid',
                    'notes'           => $notes,
                    'created_by'      => Auth::id(),
                ]);

                foreach ($cart as $item) {
                    OrderItem::create([
                        'order_id'     => $order->id,
                        'product_id'   => $item['product_id'],
                        'product_name' => $item['name'],
                        'sku'          => $item['sku'] ?? '',
                        'quantity'     => $item['quantity'],
                        'unit_price'   => $item['price'],
                        'subtotal'     => $item['subtotal'],
                        'total'        => $item['subtotal'],
                    ]);

                    Product::where('id', $item['product_id'])
                        ->decrement('stock_quantity', $item['quantity']);
                }

                Payment::create([
                    'payment_number'    => 'PAY-' . time() . '-' . rand(1000, 9999),
                    'order_id'          => $order->id,
                    'payment_method_id' => $this->getPaymentMethodId($paymentMethod),
                    'payment_type'      => 'sale',
                    'payment_date'      => now(),
                    'amount'            => $total,
                    'created_by'        => Auth::id(),
                ]);

                $this->lastOrder      = $order->load(['items', 'customer', 'payments.method', 'creator', 'taxRate']);
                $this->showReceiptModal = true;
            });

            // Clear server-side cart
            $this->cart = [];
            $this->saveCartToSession();

            $this->dispatch('notify', message: 'Payment completed successfully!', type: 'success');
            return ['success' => true];

        } catch (\Exception $e) {
            Log::error('processPayment failed: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            $this->dispatch('notify', message: 'Payment failed: ' . $e->getMessage(), type: 'error');
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function getPaymentMethodId(?string $method = null): int
    {
        $slug   = $method ?? 'cash';
        $record = PaymentMethod::where('slug', $slug)->first();
        return $record ? $record->id : 1;
    }

    public function handleCartUpdated($cart): void
    {
        if (is_array($cart)) {
            $this->cart = isset($cart['cart']) ? $cart['cart'] : $cart;
        }
    }

    public function getSettingsProperty()
    {
        return BusinessSetting::first();
    }

    public function printReceipt(int $id): void
    {
        $this->dispatch('print-receipt', orderId: $id);
    }

    public function render()
    {
        $customer = $this->customerId ? Customer::find($this->customerId) : null;

        return view('livewire.pos.index', [
            'customers'        => $this->customers,
            'searchResults'    => $this->searchResults,
            'selectedCustomer' => $customer,
            'quickProducts'    => $this->quickProducts,
            'settings'         => $this->settings,
        ]);
    }
}