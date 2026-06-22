<?php

namespace Database\Seeders;

use App\Models\BusinessSetting;
use Illuminate\Database\Seeder;

class BusinessSettingSeeder extends Seeder
{
    public function run(): void
    {
        BusinessSetting::create([
            'business_name' => 'Posta Mart',
            'business_logo' => null,
            'business_address' => '123 Business Avenue, Suite 100, New York, NY 10001',
            'business_phone' => '+1-800-555-0123',
            'business_email' => 'info@Postamart.com',
            'tax_number' => 'TAX-12345-67890',
            'registration_number' => 'REG-98765-43210',
            'currency_code' => 'USD',
            'currency_symbol' => '$',
            'timezone' => 'America/New_York',
            'date_format' => 'Y-m-d',
            'receipt_footer' => 'Thank you for your business! Visit us again at www.indabamart.com',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}