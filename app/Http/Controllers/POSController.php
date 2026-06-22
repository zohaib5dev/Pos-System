<?php

// Add this method to your POS controller (e.g. App\Http\Controllers\POSController)
// AND register the route in routes/web.php:
//
//   Route::post('/pos/sync-offline-order', [POSController::class, 'syncOfflineOrder'])
//       ->middleware(['auth']);
//
// This replaces the broken Livewire.dispatch() approach.

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\TaxRate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use function Symfony\Component\Clock\now;

class POSController extends Controller
{
    public function categoriesCache()
    {
        $categories = Category::where('is_active', true)
            ->get()
            ->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'slug' => $category->slug,
                    'updated_at' => $category->updated_at->toISOString()
                ];
            });

        return response()->json($categories);
    }


    public function productsCache()
    {
        $products = Product::with('category')
            ->where('is_active', true)
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'barcode' => $product->barcode,
                    'selling_price' => $product->selling_price,
                    'stock_quantity' => $product->stock_quantity,
                    'category_id' => $product->category_id,
                    'category_name' => $product->category->name ?? null,
                    'image' => $product->main_image,
                    'updated_at' => $product->updated_at->toISOString()
                ];
            });

        return response()->json($products);
    }


    public function customersCache()
    {
        $customers = Customer::latest()
            ->limit(500)
            ->get()
            ->map(function ($customer) {
                return [
                    'id' => $customer->id,
                    'name' => $customer->name,
                    'phone' => $customer->phone,
                    'email' => $customer->email,
                    'address' => $customer->address,
                ];
            });

        return response()->json($customers);
    }

    public function createCustomer(Request $request)
    {
        try {
            $customer = Customer::create([
                'customer_code' => 'CUS-' . date('Ymd') . '-' . rand(1000, 9999),
                'name' => $request->name,
                'phone' => $request->phone,
                'email' => $request->email,
                'tempId' => $request->tempId,
                //'is_active' => true,
            ]);

            return response()->json([
                'success' => true,
                'id' => $customer->id,
                'customer' => [
                    'id' => $customer->id,
                    'name' => $customer->name,
                    'phone' => $customer->phone,
                    'email' => $customer->email,
                    //'address' => $customer->address,
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Create customer error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to create customer: ' . $e->getMessage()
            ], 500);
        }
    }


    public function syncOfflineOrder(Request $request)
    {
        $orderData = $request->all();

        // Basic validation
        if (empty($orderData['cart'])) {
            return response()->json(['success' => false, 'error' => 'Cart is empty'], 422);
        }

        $customer = null;

        if($orderData['customer_id'])
        $customer = Customer::where('tempId', $orderData['customer_id'])->orWhere('id', $orderData['customer_id'])->first();
       
        $defaultTax = TaxRate::where('is_default', true)
            ->where('is_active', true)
            ->first();


        $order = Order::create([
            'order_number'    => 'OFF-' . time() . '-' . rand(1000, 9999),
            'customer_id'     => $customer ? $customer->id : null,
            'order_date'      => $orderData['created_at'] ?? now(),
            'subtotal'        => $orderData['subtotal'] ?? 0,
            'discount_type'   => $orderData['discountType'] ?? 'fixed',
            'discount_value'  => $orderData['discount'] ?? 0,
            'discount_amount' => $orderData['discount_amount'] ?? 0,
            'tax_rate_id'     => $defaultTax?->id,
            'tax_amount'      => $orderData['tax_amount'] ?? 0,
            'total_amount'    => $orderData['total'] ?? 0,
            'paid_amount'     => $orderData['amount_tendered'] ?? 0,
            'change_amount'   => $orderData['change'] ?? 0,
            'status'          => 'completed',
            'payment_status'  => 'paid',
            'notes'           => trim(($orderData['notes'] ?? '') . ' (Synced Offline Order)'),
            'created_by'      => Auth::id(),
            'is_offline_order' => true,
        ]);

        foreach ($orderData['cart'] as $item) {
            OrderItem::create([
                'order_id'     => $order->id,
                'product_id'   => $item['product_id'] ?? $item['id'],
                'product_name' => $item['name'],
                'sku'          => $item['sku'] ?? '',
                'quantity'     => $item['quantity'],
                'unit_price'   => $item['price'],
                'subtotal'     => $item['subtotal'],
                'total'        => $item['subtotal'],
            ]);

            // Decrement stock safely
            Product::where('id', $item['product_id'] ?? $item['id'])
                ->decrement('stock_quantity', $item['quantity']);
        }

        $paymentMethod = PaymentMethod::where('slug', $orderData['payment_method'] ?? 'cash')->first();

        Payment::create([
            'payment_number'    => 'PAY-' . time() . '-' . rand(1000, 9999),
            'order_id'          => $order->id,
            'payment_method_id' => $paymentMethod?->id ?? 1,
            'payment_type'      => 'sale',
            'payment_date'      => $orderData['created_at'] ?? now(),
            'amount'            => $orderData['total'] ?? 0,
            'created_by'        => Auth::id(),
        ]);


        return response()->json([
            'success' => true,
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'customer_name' =>$customer ? $customer->name : null,
            'total' => $orderData['total'],
            'created_at' => now(),
        ]);
    }
}
