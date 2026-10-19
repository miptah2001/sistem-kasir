<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategoryProductSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            'Minuman' => [
                'color' => '#0ea5e9',
                'icon'  => 'cup-hot',
                'products' => [
                    ['sku' => 'MNM-001', 'name' => 'Aqua 600ml', 'cost' => 2000, 'price' => 3500, 'stock' => 50],
                    ['sku' => 'MNM-002', 'name' => 'Teh Botol 350ml', 'cost' => 3000, 'price' => 5000, 'stock' => 40],
                    ['sku' => 'MNM-003', 'name' => 'Coca Cola 390ml', 'cost' => 4000, 'price' => 6500, 'stock' => 30],
                ],
            ],
            'Makanan' => [
                'color' => '#10b981',
                'icon'  => 'egg-fried',
                'products' => [
                    ['sku' => 'MKN-001', 'name' => 'Indomie Goreng', 'cost' => 2500, 'price' => 4000, 'stock' => 100],
                    ['sku' => 'MKN-002', 'name' => 'Biskuit Roma 300g', 'cost' => 7000, 'price' => 10000, 'stock' => 25],
                ],
            ],
            'Snack' => [
                'color' => '#f59e0b',
                'icon'  => 'bag',
                'products' => [
                    ['sku' => 'SNK-001', 'name' => 'Chitato Sapi 68g', 'cost' => 8000, 'price' => 12000, 'stock' => 20],
                    ['sku' => 'SNK-002', 'name' => 'Tango Wafer 100g', 'cost' => 6000, 'price' => 9000, 'stock' => 35],
                    ['sku' => 'SNK-003', 'name' => 'Oreo Original', 'cost' => 7000, 'price' => 10000, 'stock' => 3],
                ],
            ],
            'Kebutuhan Rumah' => [
                'color' => '#8b5cf6',
                'icon'  => 'house-door',
                'products' => [
                    ['sku' => 'KBR-001', 'name' => 'Sabun Lifebuoy 85g', 'cost' => 3500, 'price' => 5000, 'stock' => 60],
                    ['sku' => 'KBR-002', 'name' => 'Pasta Gigi Pepsodent', 'cost' => 9000, 'price' => 13000, 'stock' => 4],
                ],
            ],
        ];

        foreach ($data as $categoryName => $config) {
            // ✅ firstOrCreate = aman dijalankan berkali-kali
            $category = Category::firstOrCreate(
                ['slug' => Str::slug($categoryName)],
                [
                    'name'  => $categoryName,
                    'color' => $config['color'],
                    'icon'  => $config['icon'],
                ]
            );

            foreach ($config['products'] as $p) {
                Product::firstOrCreate(
                    ['sku' => $p['sku']],
                    [
                        'category_id' => $category->id,
                        'name'        => $p['name'],
                        'cost_price'  => $p['cost'],
                        'price'       => $p['price'],
                        'stock'       => $p['stock'],
                        'is_active'   => true,
                    ]
                );
            }
        }

        $this->command->info('✅ Sample kategori & produk berhasil dibuat!');
    }
}