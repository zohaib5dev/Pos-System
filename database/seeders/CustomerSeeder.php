<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $customers = [
            [
                'customer_code' => 'CUST-' . Str::random(8),
                'name' => 'James Wilson',
                'email' => 'james.wilson@email.com',
                'phone' => '+1-212-555-1001',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'customer_code' => 'CUST-' . Str::random(8),
                'name' => 'Emily Brown',
                'email' => 'emily.brown@email.com',
                'phone' => '+1-312-555-1002',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'customer_code' => 'CUST-' . Str::random(8),
                'name' => 'Michael Davis',
                'email' => 'michael.davis@email.com',
                'phone' => '+1-213-555-1003',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'customer_code' => 'CUST-' . Str::random(8),
                'name' => 'Jessica Martinez',
                'email' => 'jessica.martinez@email.com',
                'phone' => '+1-305-555-1004',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'customer_code' => 'CUST-' . Str::random(8),
                'name' => 'Christopher Anderson',
                'email' => 'chris.anderson@email.com',
                'phone' => '+1-713-555-1005',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'customer_code' => 'CUST-' . Str::random(8),
                'name' => 'Amanda Thompson',
                'email' => 'amanda.t@email.com',
                'phone' => '+1-617-555-1006',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'customer_code' => 'CUST-' . Str::random(8),
                'name' => 'Daniel White',
                'email' => 'daniel.white@email.com',
                'phone' => '+1-214-555-1007',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'customer_code' => 'CUST-' . Str::random(8),
                'name' => 'Sarah Harris',
                'email' => 'sarah.harris@email.com',
                'phone' => '+1-206-555-1008',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'customer_code' => 'CUST-' . Str::random(8),
                'name' => 'Kevin Martin',
                'email' => 'kevin.martin@email.com',
                'phone' => '+1-303-555-1009',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'customer_code' => 'CUST-' . Str::random(8),
                'name' => 'Lisa Garcia',
                'email' => 'lisa.garcia@email.com',
                'phone' => '+1-602-555-1010',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($customers as $customer) {
            Customer::create($customer);
        }

    
    }
}