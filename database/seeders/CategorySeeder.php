<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Makanan Utama', 'slug' => 'makanan-utama', 'sort_order' => 1],
            ['name' => 'Cemilan & Gorengan', 'slug' => 'cemilan-gorengan', 'sort_order' => 2],
            ['name' => 'Minuman Dingin', 'slug' => 'minuman-dingin', 'sort_order' => 3],
            ['name' => 'Minuman Panas', 'slug' => 'minuman-panas', 'sort_order' => 4],
            ['name' => 'Dessert & Kue', 'slug' => 'dessert-kue', 'sort_order' => 5],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['slug' => $category['slug']],
                array_merge($category, ['is_active' => true])
            );
        }
    }
}
