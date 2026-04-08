<?php

namespace Database\Seeders;

use App\Models\Attribute;
use App\Models\AttributeValue;
use Illuminate\Database\Seeder;

class AttributeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $attributes = [
            'Màu sắc' => [
                'Đen', 'Trắng', 'Xám Space', 'Bạc', 'Vàng (Gold)', 'Hồng', 'Xanh Dương', 'Xanh Lá', 'Tím', 'Titan Tự Nhiên'
            ],
            'Dung lượng' => [
                '64GB', '128GB', '256GB', '512GB', '1TB'
            ],
            'RAM' => [
                '4GB', '6GB', '8GB', '12GB', '16GB', '32GB'
            ],
            'Kích thước màn hình' => [
                '6.1 inch', '6.7 inch', '13-inch', '14-inch', '16-inch'
            ],
            'Phiên bản' => [
                'Chính hãng VN/A', 'Xách tay', 'Likenew 99%', 'Bản giới hạn'
            ]
        ];

        foreach ($attributes as $attrName => $values) {
            $attribute = Attribute::firstOrCreate(['name' => $attrName]);

            foreach ($values as $index => $val) {
                AttributeValue::updateOrCreate(
                    [
                        'attribute_id' => $attribute->id,
                        'value' => $val
                    ],
                    [
                        'sort_order' => $index + 1
                    ]
                );
            }
        }
    }
}
