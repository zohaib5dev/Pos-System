<?php

namespace Database\Seeders;

use App\Models\StockAdjustment;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class StockAdjustmentSeeder extends Seeder
{
    public function run(): void
    {
        $products = Product::all();
        $users = User::all();

        // Create 30 stock adjustments
        for ($i = 0; $i < 30; $i++) {
            $product = $products->random();
            $adjustmentDate = now()->subDays(rand(1, 90));
            $adjustmentType = ['addition', 'deduction'][rand(0, 1)];
            
            $currentQuantity = $product->stock_quantity;
            $quantity = rand(1, 20);
            $newQuantity = $adjustmentType === 'addition' 
                ? $currentQuantity + $quantity 
                : max(0, $currentQuantity - $quantity);
            
            $reasons = [
                'Damaged goods',
                'Inventory count correction',
                'Return from customer',
                'Sample removed',
                'Expired products',
                'Theft/loss',
                'Quality control',
                'Promotional items',
                'Display units',
            ];
            
            StockAdjustment::create([
                'adjustment_number' => 'ADJ-' . date('Ymd', strtotime($adjustmentDate)) . '-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                'product_id' => $product->id,
                'adjustment_type' => $adjustmentType,
                'quantity' => $quantity,
                'current_quantity' => $currentQuantity,
                'new_quantity' => $newQuantity,
                'reason' => $reasons[array_rand($reasons)],
                'notes' => rand(0, 1) ? 'Additional notes about this adjustment' : null,
                'created_by' => $users->random()->id,
                'created_at' => $adjustmentDate,
                'updated_at' => $adjustmentDate,
            ]);
            
            // Update product stock quantity
            $product->update(['stock_quantity' => $newQuantity]);
        }
    }
}