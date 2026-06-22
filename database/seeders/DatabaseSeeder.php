<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            RolePermissionSeeder::class,
            BusinessSettingSeeder::class,
            CategorySeeder::class,
            BrandSeeder::class,
            UnitSeeder::class,
            PaymentMethodSeeder::class,
            ExpenseCategorySeeder::class,
            SupplierSeeder::class,
            CustomerSeeder::class,
            ProductSeeder::class,
            PurchaseSeeder::class,
            OrderSeeder::class,
            PaymentSeeder::class,
            ExpenseSeeder::class,
            StockAdjustmentSeeder::class,
            ActivityLogSeeder::class,
        ]);
    }
}