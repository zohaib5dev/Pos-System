<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'phone' => '+1234567890',
                'address' => '123 Admin Street, New York, NY 10001',
                'profile_photo_path' => null,
                'is_active' => true,
                'email_verified_at' => now(),
                'last_login_at' => now(),
                'last_login_ip' => '127.0.0.1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Store Manager',
                'email' => 'manager@example.com',
                'password' => Hash::make('password'),
                'phone' => '+1234567891',
                'address' => '456 Manager Avenue, Los Angeles, CA 90001',
                'profile_photo_path' => null,
                'is_active' => true,
                'email_verified_at' => now(),
                'last_login_at' => now(),
                'last_login_ip' => '127.0.0.1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Cashier User',
                'email' => 'cashier@example.com',
                'password' => Hash::make('password'),
                'phone' => '+1234567892',
                'address' => '789 Cashier Lane, Chicago, IL 60007',
                'profile_photo_path' => null,
                'is_active' => true,
                'email_verified_at' => now(),
                'last_login_at' => now(),
                'last_login_ip' => '127.0.0.1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Inventory Clerk',
                'email' => 'inventory@example.com',
                'password' => Hash::make('password'),
                'phone' => '+1234567893',
                'address' => '321 Warehouse Blvd, Houston, TX 77001',
                'profile_photo_path' => null,
                'is_active' => true,
                'email_verified_at' => now(),
                'last_login_at' => now(),
                'last_login_ip' => '127.0.0.1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Sales Representative',
                'email' => 'sales@example.com',
                'password' => Hash::make('password'),
                'phone' => '+1234567894',
                'address' => '555 Sales Road, Miami, FL 33101',
                'profile_photo_path' => null,
                'is_active' => true,
                'email_verified_at' => now(),
                'last_login_at' => now(),
                'last_login_ip' => '127.0.0.1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
 
    }
}