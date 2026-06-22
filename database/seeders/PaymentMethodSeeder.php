<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PaymentMethodSeeder extends Seeder
{
    public function run(): void
    {
        $methods = [
            ['name' => 'Cash', 'description' => 'Cash payment', 'is_active' => true],
            ['name' => 'Credit Card', 'description' => 'Visa, Mastercard, American Express', 'is_active' => true],
            ['name' => 'Debit Card', 'description' => 'Debit card payment', 'is_active' => true],
            ['name' => 'Bank Transfer', 'description' => 'Direct bank transfer', 'is_active' => true],
            ['name' => 'Check', 'description' => 'Payment by check', 'is_active' => true],
            ['name' => 'Mobile Payment', 'description' => 'Apple Pay, Google Pay, Samsung Pay', 'is_active' => true],
            ['name' => 'PayPal', 'description' => 'Online payment via PayPal', 'is_active' => true],
            ['name' => 'Cryptocurrency', 'description' => 'Bitcoin, Ethereum, etc.', 'is_active' => false],
            ['name' => 'Gift Card', 'description' => 'Store gift card', 'is_active' => true],
            ['name' => 'Store Credit', 'description' => 'Customer store credit', 'is_active' => true],
        ];

        foreach ($methods as $method) {
            $method['slug'] = Str::slug($method['name']);
            PaymentMethod::create($method);
        }
    }
}