<?php

namespace Database\Seeders;

use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Supplier;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PurchaseSeeder extends Seeder
{
    public function run(): void
    {
        $suppliers = Supplier::all();
        $products = Product::all();

        // Create 30 purchases over the last 6 months
        for ($i = 0; $i < 30; $i++) {
            $supplier = $suppliers->random();
            $purchaseDate = now()->subDays(rand(1, 180));
            $status = ['draft', 'ordered', 'partial', 'received', 'cancelled'][rand(0, 4)];
            $paymentStatus = ['pending', 'partial', 'paid'][rand(0, 2)];
            
            $subtotal = 0;
            $taxAmount = 0;
            $discountAmount = 0;
            $shippingCost = rand(0, 100);
            $otherCost = rand(0, 50);
            
            $purchase = Purchase::create([
                'purchase_number' => 'PO-' . date('Ymd', strtotime($purchaseDate)) . '-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                'supplier_id' => $supplier->id,
                'purchase_date' => $purchaseDate,
                'expected_delivery_date' => $status !== 'received' ? $purchaseDate->copy()->addDays(rand(7, 30)) : null,
                'delivery_date' => $status === 'received' ? $purchaseDate->copy()->addDays(rand(1, 10)) : null,
                'status' => $status,
                'payment_status' => $paymentStatus,
                'subtotal' => 0,
                'tax_amount' => 0,
                'discount_type' => rand(0, 1) ? 'percentage' : 'fixed',
                'discount_value' => rand(0, 100),
                'discount_amount' => 0,
                'shipping_cost' => $shippingCost,
                'other_cost' => $otherCost,
                'total_amount' => 0,
                'paid_amount' => 0,
                'created_at' => $purchaseDate,
                'updated_at' => $purchaseDate,
            ]);

            // Add 1-10 items to each purchase
            $itemCount = rand(1, 10);
            for ($j = 0; $j < $itemCount; $j++) {
                $product = $products->random();
                $quantity = rand(5, 100);
                $unitCost = $product->purchase_price * (1 + rand(-5, 5) / 100); // Slight variation
                
                $taxRate = rand(0, 1) ? 8.00 : 0.00;
                $taxAmount = $quantity * $unitCost * ($taxRate / 100);
                
                $discountType = rand(0, 1) ? 'percentage' : 'fixed';
                $discountValue = $discountType === 'percentage' ? rand(0, 15) : rand(0, 50);
                $discountAmount = $discountType === 'percentage' 
                    ? ($quantity * $unitCost * $discountValue / 100)
                    : $discountValue;
                
                $totalCost = ($quantity * $unitCost) + $taxAmount - $discountAmount;
                
                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'received_quantity' => $status === 'received' ? $quantity : ($status === 'partial' ? rand(1, $quantity - 1) : 0),
                    'unit_cost' => $unitCost,
                    'discount_type' => $discountType,
                    'discount_value' => $discountValue,
                    'discount_amount' => $discountAmount,
                    'tax_rate' => $taxRate,
                    'tax_amount' => $taxAmount,
                    'total_cost' => $totalCost,
                    'created_at' => $purchaseDate,
                    'updated_at' => $purchaseDate,
                ]);
                
                $subtotal += $quantity * $unitCost;
            }
            
            // Calculate purchase totals
            $discountAmount = $purchase->discount_type === 'percentage'
                ? ($subtotal * $purchase->discount_value / 100)
                : $purchase->discount_value;
            
            $totalAmount = $subtotal + $taxAmount + $shippingCost + $otherCost - $discountAmount;
            $paidAmount = $paymentStatus === 'paid' ? $totalAmount : ($paymentStatus === 'partial' ? $totalAmount * rand(1, 99) / 100 : 0);
            
            $purchase->update([
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'discount_amount' => $discountAmount,
                'total_amount' => $totalAmount,
                'paid_amount' => $paidAmount,
            ]);
        }
    }
}