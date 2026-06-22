<?php

namespace Database\Seeders;

use App\Models\Payment;
use App\Models\Order;
use App\Models\Purchase;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\PaymentMethod;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PaymentSeeder extends Seeder
{
    public function run(): void
    {
        $orders = Order::all();
        $purchases = Purchase::all();
        $customers = Customer::all();
        $suppliers = Supplier::all();
        $paymentMethods = PaymentMethod::where('is_active', true)->get();
        $users = User::all();

        // Create payments for orders
        foreach ($orders as $order) {
            if ($order->paid_amount > 0) {
                $paymentCount = $order->payment_status === 'partial' ? rand(1, 3) : 1;
                $remainingAmount = $order->paid_amount;
                
                for ($i = 0; $i < $paymentCount; $i++) {
                    $amount = $i === $paymentCount - 1 ? $remainingAmount : rand(1, $remainingAmount - ($paymentCount - $i - 1));
                    $remainingAmount -= $amount;
                    
                    $paymentDate = $order->order_date->copy()->addMinutes(rand(1, 60 * 24));
                    
                    Payment::create([
                        'payment_number' => 'PAY-' . date('Ymd', strtotime($paymentDate)) . '-' . Str::random(6),
                        'order_id' => $order->id,
                        'purchase_id' => null,
                        'customer_id' => $order->customer_id,
                        'supplier_id' => null,
                        'payment_method_id' => $paymentMethods->random()->id,
                        'payment_type' => 'sale',
                        'payment_date' => $paymentDate,
                        'amount' => $amount,
                        'reference_number' => rand(0, 1) ? 'REF' . rand(10000, 99999) : null,
                        'notes' => rand(0, 1) ? 'Payment for order ' . $order->order_number : null,
                        'created_by' => $order->created_by,
                        'created_at' => $paymentDate,
                        'updated_at' => $paymentDate,
                    ]);
                }
            }
        }

        // Create payments for purchases
        foreach ($purchases as $purchase) {
            if ($purchase->paid_amount > 0) {
                $paymentCount = $purchase->payment_status === 'partial' ? rand(1, 3) : 1;
                $remainingAmount = $purchase->paid_amount;
                
                for ($i = 0; $i < $paymentCount; $i++) {
                    $amount = $i === $paymentCount - 1 ? $remainingAmount : rand(1, $remainingAmount - ($paymentCount - $i - 1));
                    $remainingAmount -= $amount;
                    
                    $paymentDate = $purchase->purchase_date->copy()->addDays(rand(0, 30));
                    
                    Payment::create([
                        'payment_number' => 'PAY-' . date('Ymd', strtotime($paymentDate)) . '-' . Str::random(6),
                        'order_id' => null,
                        'purchase_id' => $purchase->id,
                        'customer_id' => null,
                        'supplier_id' => $purchase->supplier_id,
                        'payment_method_id' => $paymentMethods->random()->id,
                        'payment_type' => 'purchase',
                        'payment_date' => $paymentDate,
                        'amount' => $amount,
                        'reference_number' => rand(0, 1) ? 'REF' . rand(10000, 99999) : null,
                        'notes' => rand(0, 1) ? 'Payment for purchase ' . $purchase->purchase_number : null,
                        'created_by' => $users->random()->id,
                        'created_at' => $paymentDate,
                        'updated_at' => $paymentDate,
                    ]);
                }
            }
        }

        // Create customer payments (direct payments not linked to orders)
        for ($i = 0; $i < 20; $i++) {
            $customer = $customers->random();
            $paymentDate = now()->subDays(rand(1, 90));
            
            Payment::create([
                'payment_number' => 'PAY-' . date('Ymd', strtotime($paymentDate)) . '-' . Str::random(6),
                'order_id' => null,
                'purchase_id' => null,
                'customer_id' => $customer->id,
                'supplier_id' => null,
                'payment_method_id' => $paymentMethods->random()->id,
                'payment_type' => 'customer_payment',
                'payment_date' => $paymentDate,
                'amount' => rand(50, 1000),
                'reference_number' => 'REF' . rand(10000, 99999),
                'notes' => 'Direct customer payment',
                'created_by' => $users->random()->id,
                'created_at' => $paymentDate,
                'updated_at' => $paymentDate,
            ]);
        }

        // Create supplier payments (direct payments not linked to purchases)
        for ($i = 0; $i < 15; $i++) {
            $supplier = $suppliers->random();
            $paymentDate = now()->subDays(rand(1, 90));
            
            Payment::create([
                'payment_number' => 'PAY-' . date('Ymd', strtotime($paymentDate)) . '-' . Str::random(6),
                'order_id' => null,
                'purchase_id' => null,
                'customer_id' => null,
                'supplier_id' => $supplier->id,
                'payment_method_id' => $paymentMethods->random()->id,
                'payment_type' => 'supplier_payment',
                'payment_date' => $paymentDate,
                'amount' => rand(100, 2000),
                'reference_number' => 'REF' . rand(10000, 99999),
                'notes' => 'Direct supplier payment',
                'created_by' => $users->random()->id,
                'created_at' => $paymentDate,
                'updated_at' => $paymentDate,
            ]);
        }
    }
}