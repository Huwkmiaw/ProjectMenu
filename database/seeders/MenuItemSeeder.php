<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\MenuItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MenuItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $menus = [
            // Makanan Utama
            'makanan-utama' => [
                ['name' => 'Nasi Goreng Spesial', 'description' => 'Nasi goreng dengan telur, ayam, dan sayuran segar', 'price' => 20000, 'sort_order' => 1],
                ['name' => 'Mie Ayam Bakso', 'description' => 'Mie ayam dengan bakso sapi dan kuah gurih', 'price' => 18000, 'sort_order' => 2],
                ['name' => 'Nasi Ayam Geprek', 'description' => 'Ayam goreng geprek pedas dengan nasi putih', 'price' => 22000, 'sort_order' => 3],
                ['name' => 'Gado-Gado', 'description' => 'Sayuran segar dengan bumbu kacang khas', 'price' => 15000, 'sort_order' => 4],
                ['name' => 'Soto Ayam', 'description' => 'Soto ayam bening dengan bihun dan perkedel', 'price' => 18000, 'sort_order' => 5],
                ['name' => 'Nasi Uduk Komplit', 'description' => 'Nasi uduk dengan lauk lengkap dan sambal', 'price' => 25000, 'sort_order' => 6],
            ],

            // Cemilan & Gorengan
            'cemilan-gorengan' => [
                ['name' => 'Pisang Goreng Crispy', 'description' => 'Pisang goreng tepung renyah (3 pcs)', 'price' => 8000, 'sort_order' => 1],
                ['name' => 'Tempe Mendoan', 'description' => 'Tempe mendoan khas dengan tepung bumbu (5 pcs)', 'price' => 7000, 'sort_order' => 2],
                ['name' => 'Tahu Isi Goreng', 'description' => 'Tahu isi sayuran digoreng garing (4 pcs)', 'price' => 8000, 'sort_order' => 3],
                ['name' => 'Kentang Goreng', 'description' => 'Kentang goreng renyah dengan saus sambal', 'price' => 12000, 'sort_order' => 4],
                ['name' => 'Cireng Bumbu Rujak', 'description' => 'Aci goreng dengan saus rujak pedas manis', 'price' => 10000, 'sort_order' => 5],
            ],

            // Minuman Dingin
            'minuman-dingin' => [
                ['name' => 'Es Teh Manis', 'description' => 'Teh manis dingin segar', 'price' => 5000, 'sort_order' => 1],
                ['name' => 'Es Jeruk', 'description' => 'Jeruk peras segar dengan es', 'price' => 8000, 'sort_order' => 2],
                ['name' => 'Es Cincau', 'description' => 'Cincau hitam dengan santan dan gula merah', 'price' => 8000, 'sort_order' => 3],
                ['name' => 'Jus Alpukat', 'description' => 'Jus alpukat segar dengan susu coklat', 'price' => 15000, 'sort_order' => 4],
                ['name' => 'Thai Tea', 'description' => 'Teh thailand dengan susu evaporasi', 'price' => 15000, 'sort_order' => 5],
                ['name' => 'Lemon Tea', 'description' => 'Teh lemon segar dengan es', 'price' => 12000, 'sort_order' => 6],
            ],

            // Minuman Panas
            'minuman-panas' => [
                ['name' => 'Kopi Hitam', 'description' => 'Kopi hitam robusta pilihan', 'price' => 8000, 'sort_order' => 1],
                ['name' => 'Kopi Susu', 'description' => 'Kopi dengan susu segar', 'price' => 12000, 'sort_order' => 2],
                ['name' => 'Teh Manis Panas', 'description' => 'Teh manis hangat', 'price' => 5000, 'sort_order' => 3],
                ['name' => 'Wedang Jahe', 'description' => 'Jahe hangat dengan gula merah', 'price' => 8000, 'sort_order' => 4],
                ['name' => 'Susu Coklat Panas', 'description' => 'Susu full cream dengan coklat premium', 'price' => 12000, 'sort_order' => 5],
            ],

            // Dessert & Kue
            'dessert-kue' => [
                ['name' => 'Es Krim Vanilla', 'description' => 'Es krim vanilla premium 1 scoop', 'price' => 12000, 'sort_order' => 1],
                ['name' => 'Puding Coklat', 'description' => 'Puding coklat dengan saus karamel', 'price' => 8000, 'sort_order' => 2],
                ['name' => 'Kue Lapis', 'description' => 'Kue lapis legit tradisional (per potong)', 'price' => 10000, 'sort_order' => 3],
                ['name' => 'Martabak Manis Mini', 'description' => 'Martabak manis coklat keju ukuran mini', 'price' => 15000, 'sort_order' => 4],
            ],
        ];

        foreach ($menus as $categorySlug => $items) {
            $category = Category::where('slug', $categorySlug)->first();

            if (! $category) {
                continue;
            }

            foreach ($items as $item) {
                MenuItem::firstOrCreate(
                    ['slug' => Str::slug($item['name'])],
                    array_merge($item, [
                        'category_id' => $category->id,
                        'slug' => Str::slug($item['name']),
                        'image' => null,
                        'is_available' => true,
                    ])
                );
            }
        }
    }
}
