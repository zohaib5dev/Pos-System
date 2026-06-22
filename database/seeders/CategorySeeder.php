<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@example.com')->first();

        $categories = [
            [
                'name' => 'Electronics',
                'description' => 'Electronic devices, gadgets, and accessories',
                'is_active' => true,
                'sort_order' => 1,
                'created_by' => $admin->id,
            ],
            [
                'name' => 'Computers & Laptops',
                'description' => 'Desktop computers, laptops, and computer components',
                'is_active' => true,
                'sort_order' => 2,
                'created_by' => $admin->id,
            ],
            [
                'name' => 'Smartphones & Tablets',
                'description' => 'Mobile phones, tablets, and accessories',
                'is_active' => true,
                'sort_order' => 3,
                'created_by' => $admin->id,
            ],
            [
                'name' => 'Audio & Headphones',
                'description' => 'Speakers, headphones, microphones, and audio equipment',
                'is_active' => true,
                'sort_order' => 4,
                'created_by' => $admin->id,
            ],
            [
                'name' => 'Cameras & Photography',
                'description' => 'Digital cameras, lenses, and photography accessories',
                'is_active' => true,
                'sort_order' => 5,
                'created_by' => $admin->id,
            ],
            [
                'name' => 'TV & Home Theater',
                'description' => 'Televisions, home theater systems, and accessories',
                'is_active' => true,
                'sort_order' => 6,
                'created_by' => $admin->id,
            ],
            [
                'name' => 'Gaming',
                'description' => 'Video games, consoles, and gaming accessories',
                'is_active' => true,
                'sort_order' => 7,
                'created_by' => $admin->id,
            ],
            [
                'name' => 'Wearable Technology',
                'description' => 'Smartwatches, fitness trackers, and wearable devices',
                'is_active' => true,
                'sort_order' => 8,
                'created_by' => $admin->id,
            ],
            [
                'name' => 'Office Supplies',
                'description' => 'Office equipment, stationery, and supplies',
                'is_active' => true,
                'sort_order' => 9,
                'created_by' => $admin->id,
            ],
            [
                'name' => 'Home Appliances',
                'description' => 'Small and large home appliances',
                'is_active' => true,
                'sort_order' => 10,
                'created_by' => $admin->id,
            ],
            [
                'name' => 'Furniture',
                'description' => 'Office and home furniture',
                'is_active' => true,
                'sort_order' => 11,
                'created_by' => $admin->id,
            ],
            [
                'name' => 'Clothing & Apparel',
                'description' => 'Men, women, and children clothing',
                'is_active' => true,
                'sort_order' => 12,
                'created_by' => $admin->id,
            ],
            [
                'name' => 'Sports & Outdoors',
                'description' => 'Sports equipment, outdoor gear, and fitness accessories',
                'is_active' => true,
                'sort_order' => 13,
                'created_by' => $admin->id,
            ],
            [
                'name' => 'Books & Media',
                'description' => 'Books, movies, music, and media',
                'is_active' => true,
                'sort_order' => 14,
                'created_by' => $admin->id,
            ],
            [
                'name' => 'Toys & Games',
                'description' => 'Toys, board games, and educational games',
                'is_active' => true,
                'sort_order' => 15,
                'created_by' => $admin->id,
            ],
        ];

        foreach ($categories as $category) {
            $category['slug'] = Str::slug($category['name']);
            Category::create($category);
        }
    }
}