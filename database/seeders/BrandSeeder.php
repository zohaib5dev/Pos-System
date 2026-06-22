<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BrandSeeder extends Seeder
{
    public function run(): void
    {
        $brands = [
            ['name' => 'Apple', 'description' => 'Apple Inc. - iPhone, Mac, iPad, and more', 'is_active' => true],
            ['name' => 'Samsung', 'description' => 'Samsung Electronics - TVs, phones, appliances', 'is_active' => true],
            ['name' => 'Sony', 'description' => 'Sony Corporation - Electronics, gaming, entertainment', 'is_active' => true],
            ['name' => 'LG', 'description' => 'LG Electronics - Appliances, TVs, phones', 'is_active' => true],
            ['name' => 'Microsoft', 'description' => 'Microsoft Corporation - Software, hardware, gaming', 'is_active' => true],
            ['name' => 'Dell', 'description' => 'Dell Technologies - Computers and peripherals', 'is_active' => true],
            ['name' => 'HP', 'description' => 'HP Inc. - Printers, computers, accessories', 'is_active' => true],
            ['name' => 'Lenovo', 'description' => 'Lenovo - Computers, tablets, smartphones', 'is_active' => true],
            ['name' => 'Asus', 'description' => 'ASUS - Computer hardware, electronics', 'is_active' => true],
            ['name' => 'Acer', 'description' => 'Acer Inc. - Computers, displays, peripherals', 'is_active' => true],
            ['name' => 'Canon', 'description' => 'Canon Inc. - Cameras, printers, optics', 'is_active' => true],
            ['name' => 'Nikon', 'description' => 'Nikon Corporation - Cameras and optics', 'is_active' => true],
            ['name' => 'Bose', 'description' => 'Bose Corporation - Audio equipment', 'is_active' => true],
            ['name' => 'JBL', 'description' => 'JBL - Audio equipment, speakers, headphones', 'is_active' => true],
            ['name' => 'Logitech', 'description' => 'Logitech - Computer peripherals, accessories', 'is_active' => true],
            ['name' => 'Cisco', 'description' => 'Cisco Systems - Networking equipment', 'is_active' => true],
            ['name' => 'Nike', 'description' => 'Nike, Inc. - Athletic footwear and apparel', 'is_active' => true],
            ['name' => 'Adidas', 'description' => 'Adidas - Sportswear and accessories', 'is_active' => true],
            ['name' => 'Whirlpool', 'description' => 'Whirlpool Corporation - Home appliances', 'is_active' => true],
            ['name' => 'KitchenAid', 'description' => 'KitchenAid - Kitchen appliances', 'is_active' => true],
            ['name' => 'IKEA', 'description' => 'IKEA - Furniture and home accessories', 'is_active' => true],
            ['name' => 'Herman Miller', 'description' => 'Herman Miller - Office furniture', 'is_active' => true],
            ['name' => 'Penguin Books', 'description' => 'Penguin Random House - Book publishing', 'is_active' => true],
            ['name' => 'Hasbro', 'description' => 'Hasbro - Toys and games', 'is_active' => true],
            ['name' => 'LEGO', 'description' => 'LEGO Group - Construction toys', 'is_active' => true],
        ];

        foreach ($brands as $brand) {
            $brand['slug'] = Str::slug($brand['name']);
            Brand::create($brand);
        }
    }
}