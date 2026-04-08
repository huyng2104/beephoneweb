<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $brands = [
            'Apple', 'Samsung', 'Xiaomi', 'Oppo', 'Realme',
            'Vivo', 'Asus', 'HP', 'Dell', 'Lenovo',
            'Microsoft', 'Sony', 'Anker', 'Baseus', 'JBL'
        ];

        foreach ($brands as $idx => $name) {
            Brand::updateOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'name' => $name,
                    'logo_url' => null, // Bạn có thể thêm link ảnh sau
                    'sort_order' => $idx + 1,
                    'is_active' => true,
                ]
            );
        }
    }
}
