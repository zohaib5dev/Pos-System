<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Customer;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $customers = Customer::all();
        $products = Product::all();
        $users = User::all();

        // Create 100 orders over the last 6 months
        for ($i = 0; $i < 100; $i++) {
            $customer = $customers->random();
            $user = $users->random();
            $orderDate = now()->subDays(rand(1, 180))->setTime(rand(9, 20), rand(0, 59), rand(0, 59));
            
            $status = ['pending', 'processing', 'completed', 'cancelled', 'refunded'][rand(0, 4)];
            $paymentStatus = ['pending', 'partial', 'paid', 'overdue', 'refunded'][rand(0, 4)];
            $orderType = ['pos', 'online', 'wholesale'][rand(0, 2)];
            
            $subtotal = 0;
            $taxAmount = 0;
            $discountAmount = 0;
            $shippingCost = $orderType === 'online' ? rand(0, 20) : 0;
            $otherCharges = rand(0, 10);
            
            $order = Order::create([
                'order_number' => 'ORD-' . date('Ymd', strtotime($orderDate)) . '-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                'invoice_number' => 'INV-' . date('Ymd', strtotime($orderDate)) . '-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                'customer_id' => $customer->id,
                'tax_rate_id' => null,
                'customer_name' => $customer->name,
                'customer_phone' => $customer->phone,
                'customer_email' => $customer->email,
                'order_date' => $orderDate,
                'due_date' => $paymentStatus === 'pending' || $paymentStatus === 'partial' ? $orderDate->copy()->addDays(rand(7, 30)) : null,
                'order_type' => $orderType,
                'status' => $status,
                'payment_status' => $paymentStatus,
                'shipping_status' => $orderType === 'online' ? ['pending', 'shipped', 'delivered', 'returned'][rand(0, 3)] : null,
                'subtotal' => 0,
                'tax_amount' => 0,
                'discount_type' => rand(0, 1) ? 'percentage' : 'fixed',
                'discount_value' => rand(0, 50),
                'discount_amount' => 0,
                'shipping_cost' => $shippingCost,
                'other_charges' => $otherCharges,
                'total_amount' => 0,
                'paid_amount' => 0,
                'due_amount' => 0,
                'change_amount' => 0,
                'notes' => rand(0, 1) ? 'Customer notes for this order' : null,
                'staff_notes' => rand(0, 1) ? 'Internal staff notes' : null,
                'created_by' => $user->id,
                'created_at' => $orderDate,
                'updated_at' => $orderDate,
            ]);

            // Add 1-5 items to each order
            $itemCount = rand(1, 5);
            for ($j = 0; $j < $itemCount; $j++) {
                $product = $products->random();
                $quantity = rand(1, 3);
                $unitPrice = $orderType === 'wholesale' ? $product->wholesale_price : $product->selling_price;
                
                $taxRate = $product->tax_rate;
                $taxAmount = $quantity * $unitPrice * ($taxRate / 100);
                
                $discountType = rand(0, 1) ? 'percentage' : 'fixed';
                $discountValue = $discountType === 'percentage' ? rand(0, 15) : rand(0, 20);
                $discountAmount = $discountType === 'percentage' 
                    ? ($quantity * $unitPrice * $discountValue / 100)
                    : $discountValue;
                
                $subtotalItem = $quantity * $unitPrice;
                $totalItem = $subtotalItem + $taxAmount - $discountAmount;
                
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'sku' => $product->sku,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'discount_type' => $discountType,
                    'discount_value' => $discountValue,
                    'discount_amount' => $discountAmount,
                    'tax_rate' => $taxRate,
                    'tax_amount' => $taxAmount,
                    'subtotal' => $subtotalItem,
                    'total' => $totalItem,
                    'created_at' => $orderDate,
                    'updated_at' => $orderDate,
                ]);
                
                $subtotal += $subtotalItem;
            }
            
            // Calculate order totals
            $discountAmount = $order->discount_type === 'percentage'
                ? ($subtotal * $order->discount_value / 100)
                : $order->discount_value;
            
            $totalAmount = $subtotal + $taxAmount + $shippingCost + $otherCharges - $discountAmount;
            $paidAmount = $paymentStatus === 'paid' ? $totalAmount : ($paymentStatus === 'partial' ? $totalAmount * rand(1, 99) / 100 : 0);
            $dueAmount = $totalAmount - $paidAmount;
            $changeAmount = $paidAmount > $totalAmount ? $paidAmount - $totalAmount : 0;
            
            $order->update([
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'discount_amount' => $discountAmount,
                'total_amount' => $totalAmount,
                'paid_amount' => $paidAmount,
                'due_amount' => $dueAmount,
                'change_amount' => $changeAmount,
            ]);
        }
    }
}