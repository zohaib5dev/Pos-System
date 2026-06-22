<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    public function run(): void
    {
        $units = [
            ['name' => 'Piece', 'short_name' => 'pc', 'is_active' => true],
            ['name' => 'Box', 'short_name' => 'box', 'is_active' => true],
            ['name' => 'Carton', 'short_name' => 'ctn', 'is_active' => true],
            ['name' => 'Dozen', 'short_name' => 'dz', 'is_active' => true],
            ['name' => 'Kilogram', 'short_name' => 'kg', 'is_active' => true],
            ['name' => 'Gram', 'short_name' => 'g', 'is_active' => true],
            ['name' => 'Liter', 'short_name' => 'L', 'is_active' => true],
            ['name' => 'Milliliter', 'short_name' => 'ml', 'is_active' => true],
            ['name' => 'Meter', 'short_name' => 'm', 'is_active' => true],
            ['name' => 'Centimeter', 'short_name' => 'cm', 'is_active' => true],
            ['name' => 'Square Meter', 'short_name' => 'm²', 'is_active' => true],
            ['name' => 'Cubic Meter', 'short_name' => 'm³', 'is_active' => true],
            ['name' => 'Pair', 'short_name' => 'pr', 'is_active' => true],
            ['name' => 'Set', 'short_name' => 'set', 'is_active' => true],
            ['name' => 'Pack', 'short_name' => 'pk', 'is_active' => true],
            ['name' => 'Bottle', 'short_name' => 'btl', 'is_active' => true],
            ['name' => 'Can', 'short_name' => 'can', 'is_active' => true],
            ['name' => 'Bag', 'short_name' => 'bag', 'is_active' => true],
            ['name' => 'Roll', 'short_name' => 'roll', 'is_active' => true],
            ['name' => 'Sheet', 'short_name' => 'sht', 'is_active' => true],
        ];

        foreach ($units as $unit) {
            Unit::create($unit);
        }
    }
}