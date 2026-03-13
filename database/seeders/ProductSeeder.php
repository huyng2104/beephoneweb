<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'name' => 'iPhone 14 128GB',
                'description' => 'iPhone 14 128GB chinh hang, bao hanh 12 thang.',
                'type' => 'simple',
                'price' => 14990000,
                'sale_price' => 13990000,
                'sku' => 'IP14-128-BL',
                'stock' => 24,
                'status' => 'active',
                'is_featured' => true,
                'thumbnail' => null,
            ],
            [
                'name' => 'iPhone 15 128GB',
                'description' => 'iPhone 15 128GB voi Dynamic Island.',
                'type' => 'simple',
                'price' => 18990000,
                'sale_price' => 17990000,
                'sku' => 'IP15-128-BK',
                'stock' => 18,
                'status' => 'active',
                'is_featured' => true,
                'thumbnail' => null,
            ],
            [
                'name' => 'Samsung Galaxy S24 256GB',
                'description' => 'Flagship Samsung man hinh AMOLED sac net.',
                'type' => 'simple',
                'price' => 19990000,
                'sale_price' => 18990000,
                'sku' => 'SS-S24-256',
                'stock' => 20,
                'status' => 'active',
                'is_featured' => true,
                'thumbnail' => null,
            ],
            [
                'name' => 'Xiaomi 14 256GB',
                'description' => 'Xiaomi 14 camera Leica, hieu nang manh.',
                'type' => 'simple',
                'price' => 14990000,
                'sale_price' => 14490000,
                'sku' => 'XM14-256',
                'stock' => 15,
                'status' => 'active',
                'is_featured' => false,
                'thumbnail' => null,
            ],
            [
                'name' => 'OPPO Reno 11 5G',
                'description' => 'OPPO Reno 11 5G thiet ke mong nhe.',
                'type' => 'simple',
                'price' => 10990000,
                'sale_price' => 9990000,
                'sku' => 'OP-R11-256',
                'stock' => 30,
                'status' => 'active',
                'is_featured' => false,
                'thumbnail' => null,
            ],
            [
                'name' => 'realme 12 Pro 5G',
                'description' => 'realme 12 Pro 5G camera zoom tot trong tam gia.',
                'type' => 'simple',
                'price' => 8990000,
                'sale_price' => 8490000,
                'sku' => 'RM12P-256',
                'stock' => 27,
                'status' => 'active',
                'is_featured' => false,
                'thumbnail' => null,
            ],
            [
                'name' => 'iPhone 13 128GB',
                'description' => 'iPhone 13 pin tot, hieu nang on dinh.',
                'type' => 'simple',
                'price' => 12990000,
                'sale_price' => 11990000,
                'sku' => 'IP13-128-WH',
                'stock' => 22,
                'status' => 'active',
                'is_featured' => false,
                'thumbnail' => null,
            ],
            [
                'name' => 'Samsung Galaxy A55 5G',
                'description' => 'Galaxy A55 pin 5000mAh, man hinh dep.',
                'type' => 'simple',
                'price' => 10990000,
                'sale_price' => 10490000,
                'sku' => 'SS-A55-256',
                'stock' => 35,
                'status' => 'active',
                'is_featured' => false,
                'thumbnail' => null,
            ],
            [
                'name' => 'Xiaomi Redmi Note 13 Pro',
                'description' => 'Redmi Note 13 Pro camera 200MP.',
                'type' => 'simple',
                'price' => 8990000,
                'sale_price' => 8290000,
                'sku' => 'RN13P-256',
                'stock' => 40,
                'status' => 'active',
                'is_featured' => false,
                'thumbnail' => null,
            ],
            [
                'name' => 'iPhone 15 Pro Max 256GB',
                'description' => 'iPhone 15 Pro Max khung titan cao cap.',
                'type' => 'simple',
                'price' => 31990000,
                'sale_price' => 30490000,
                'sku' => 'IP15PM-256',
                'stock' => 12,
                'status' => 'active',
                'is_featured' => true,
                'thumbnail' => null,
            ],
        ];

        foreach ($products as $data) {
            Product::query()->updateOrCreate(
                ['sku' => $data['sku']],
                [
                    'name' => $data['name'],
                    'slug' => Str::slug($data['name']),
                    'description' => $data['description'],
                    'type' => $data['type'],
                    'price' => $data['price'],
                    'sale_price' => $data['sale_price'],
                    'stock' => $data['stock'],
                    'status' => $data['status'],
                    'is_featured' => $data['is_featured'],
                    'thumbnail' => $data['thumbnail'],
                ]
            );
        }
    }
}
