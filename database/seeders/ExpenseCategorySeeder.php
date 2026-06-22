<?php

namespace Database\Seeders;

use App\Models\ExpenseCategory;
use Illuminate\Database\Seeder;

class ExpenseCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Rent', 'description' => 'Monthly rent payments', 'is_active' => true],
            ['name' => 'Utilities', 'description' => 'Electricity, water, gas, internet', 'is_active' => true],
            ['name' => 'Salaries', 'description' => 'Employee salaries and wages', 'is_active' => true],
            ['name' => 'Marketing', 'description' => 'Advertising and marketing expenses', 'is_active' => true],
            ['name' => 'Office Supplies', 'description' => 'Stationery and office supplies', 'is_active' => true],
            ['name' => 'Equipment', 'description' => 'Equipment purchases and maintenance', 'is_active' => true],
            ['name' => 'Transportation', 'description' => 'Shipping and delivery costs', 'is_active' => true],
            ['name' => 'Insurance', 'description' => 'Business insurance premiums', 'is_active' => true],
            ['name' => 'Taxes', 'description' => 'Tax payments', 'is_active' => true],
            ['name' => 'Professional Services', 'description' => 'Legal, accounting, consulting fees', 'is_active' => true],
            ['name' => 'Maintenance', 'description' => 'Repairs and maintenance', 'is_active' => true],
            ['name' => 'Software', 'description' => 'Software subscriptions and licenses', 'is_active' => true],
            ['name' => 'Training', 'description' => 'Employee training and development', 'is_active' => true],
            ['name' => 'Travel', 'description' => 'Business travel expenses', 'is_active' => true],
            ['name' => 'Meals & Entertainment', 'description' => 'Client meals and entertainment', 'is_active' => true],
        ];

        foreach ($categories as $category) {
            ExpenseCategory::create($category);
        }
    }
}