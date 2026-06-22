<?php

namespace Database\Seeders;

use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@example.com')->first();

        $suppliers = [
            [
                'name' => 'John Smith',
                'company_name' => 'Tech Distributors Inc.',
                'email' => 'john.smith@techdist.com',
                'phone' => '+1-212-555-0123',
                'alternative_phone' => '+1-212-555-0124',
                'address' => '100 Technology Drive',
                'city' => 'New York',
                'state' => 'NY',
                'postal_code' => '10001',
                'country' => 'USA',
                'tax_number' => 'TX-12345-USA',
                'payment_terms' => 'Net 30',
                'notes' => 'Preferred supplier for electronics',
                'is_active' => true,
                'created_by' => $admin->id,
            ],
            [
                'name' => 'Sarah Johnson',
                'company_name' => 'Global Office Supplies',
                'email' => 'sarah.j@globaloffice.com',
                'phone' => '+1-312-555-0234',
                'alternative_phone' => '+1-312-555-0235',
                'address' => '500 Business Park Blvd',
                'city' => 'Chicago',
                'state' => 'IL',
                'postal_code' => '60601',
                'country' => 'USA',
                'tax_number' => 'TX-67890-USA',
                'payment_terms' => 'Net 15',
                'notes' => 'Office supplies and stationery',
                'is_active' => true,
                'created_by' => $admin->id,
            ],
            [
                'name' => 'Michael Chen',
                'company_name' => 'Furniture Wholesale Co.',
                'email' => 'm.chen@furniturewholesale.com',
                'phone' => '+1-213-555-0345',
                'alternative_phone' => '+1-213-555-0346',
                'address' => '750 Design Avenue',
                'city' => 'Los Angeles',
                'state' => 'CA',
                'postal_code' => '90001',
                'country' => 'USA',
                'tax_number' => 'TX-13579-USA',
                'payment_terms' => 'Net 45',
                'notes' => 'Office and home furniture',
                'is_active' => true,
                'created_by' => $admin->id,
            ],
            [
                'name' => 'Maria Garcia',
                'company_name' => 'Apparel Source International',
                'email' => 'maria.garcia@apparelsource.com',
                'phone' => '+1-305-555-0456',
                'alternative_phone' => '+1-305-555-0457',
                'address' => '200 Fashion Street',
                'city' => 'Miami',
                'state' => 'FL',
                'postal_code' => '33101',
                'country' => 'USA',
                'tax_number' => 'TX-24680-USA',
                'payment_terms' => 'Net 30',
                'notes' => 'Clothing and accessories',
                'is_active' => true,
                'created_by' => $admin->id,
            ],
            [
                'name' => 'David Williams',
                'company_name' => 'Sports Equipment Direct',
                'email' => 'd.williams@sportsequip.com',
                'phone' => '+1-713-555-0567',
                'alternative_phone' => '+1-713-555-0568',
                'address' => '300 Fitness Way',
                'city' => 'Houston',
                'state' => 'TX',
                'postal_code' => '77001',
                'country' => 'USA',
                'tax_number' => 'TX-11223-USA',
                'payment_terms' => 'Net 30',
                'notes' => 'Sports and fitness equipment',
                'is_active' => true,
                'created_by' => $admin->id,
            ],
            [
                'name' => 'Jennifer Lee',
                'company_name' => 'Book Distribution Services',
                'email' => 'j.lee@bookdist.com',
                'phone' => '+1-617-555-0678',
                'alternative_phone' => '+1-617-555-0679',
                'address' => '450 Literary Lane',
                'city' => 'Boston',
                'state' => 'MA',
                'postal_code' => '02101',
                'country' => 'USA',
                'tax_number' => 'TX-33445-USA',
                'payment_terms' => 'Net 60',
                'notes' => 'Books and educational materials',
                'is_active' => true,
                'created_by' => $admin->id,
            ],
            [
                'name' => 'Robert Taylor',
                'company_name' => 'Home Appliance Wholesalers',
                'email' => 'r.taylor@homeappliance.com',
                'phone' => '+1-214-555-0789',
                'alternative_phone' => '+1-214-555-0790',
                'address' => '600 Appliance Park',
                'city' => 'Dallas',
                'state' => 'TX',
                'postal_code' => '75201',
                'country' => 'USA',
                'tax_number' => 'TX-55667-USA',
                'payment_terms' => 'Net 30',
                'notes' => 'Home and kitchen appliances',
                'is_active' => true,
                'created_by' => $admin->id,
            ],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::create($supplier);
        }

       
    }
}