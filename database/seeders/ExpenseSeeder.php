<?php

namespace Database\Seeders;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\PaymentMethod;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ExpenseSeeder extends Seeder
{
    public function run(): void
    {
        $expenseCategories = ExpenseCategory::all();
        $paymentMethods = PaymentMethod::where('is_active', true)->get();
        $users = User::all();

        // Create 50 expenses over the last 6 months
        for ($i = 0; $i < 50; $i++) {
            $expenseDate = now()->subDays(rand(1, 180));
            $category = $expenseCategories->random();
            
            $amounts = [
                'Rent' => rand(2000, 5000),
                'Utilities' => rand(200, 800),
                'Salaries' => rand(3000, 10000),
                'Marketing' => rand(500, 3000),
                'Office Supplies' => rand(50, 500),
                'Equipment' => rand(500, 2000),
                'Transportation' => rand(100, 600),
                'Insurance' => rand(300, 1200),
                'Taxes' => rand(1000, 5000),
                'Professional Services' => rand(500, 2500),
                'Maintenance' => rand(200, 1000),
                'Software' => rand(100, 800),
                'Training' => rand(300, 1500),
                'Travel' => rand(200, 2000),
                'Meals & Entertainment' => rand(50, 400),
            ];
            
            $amount = $amounts[$category->name] ?? rand(100, 2000);
            
            Expense::create([
                'expense_number' => 'EXP-' . date('Ymd', strtotime($expenseDate)) . '-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                'expense_category_id' => $category->id,
                'expense_date' => $expenseDate,
                'amount' => $amount,
                'payment_method_id' => $paymentMethods->random()->id,
                'reference_number' => rand(0, 1) ? 'REF' . rand(10000, 99999) : null,
                'description' => $category->description,
                'notes' => rand(0, 1) ? 'Additional notes for this expense' : null,
                'receipt_image' => null,
                'created_by' => $users->random()->id,
                'created_at' => $expenseDate,
                'updated_at' => $expenseDate,
            ]);
        }
    }
}