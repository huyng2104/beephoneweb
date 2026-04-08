<?php

namespace Database\Seeders;

use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\VariantSpecification;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Lấy dữ liệu cần thiết
        $brands = Brand::pluck('id', 'name')->toArray();
        $categories = Category::pluck('id', 'slug')->toArray();
        $attrValues = AttributeValue::with('attribute')->get()->groupBy('attribute.name');

        // Hàm hỗ trợ tìm ID giá trị thuộc tính
        $getAttrId = function($attrName, $valueName) use ($attrValues) {
            return $attrValues[$attrName]->where('value', $valueName)->first()?->id;
        };

        $productsData = [
            [
                'name' => 'iPhone 15 Pro Max',
                'brand' => 'Apple',
                'category' => 'iphone-15-series',
                'type' => 'variable',
                'variants' => [
                    [
                        'sku' => 'IP15PM-256-TITAN', 'price' => 34990000, 'sale_price' => 31990000, 'stock' => 50,
                        'attrs' => ['Màu sắc' => 'Titan Tự Nhiên', 'Dung lượng' => '256GB'],
                        'specs' => ['Màn hình' => '6.7 inch', 'Chip' => 'A17 Pro', 'Pin' => '4422 mAh']
                    ],
                    [
                        'sku' => 'IP15PM-512-BLACK', 'price' => 40990000, 'sale_price' => 37990000, 'stock' => 30,
                        'attrs' => ['Màu sắc' => 'Đen', 'Dung lượng' => '512GB'],
                        'specs' => ['Màn hình' => '6.7 inch', 'Chip' => 'A17 Pro', 'Pin' => '4422 mAh']
                    ]
                ]
            ],
            [
                'name' => 'Samsung Galaxy S24 Ultra',
                'brand' => 'Samsung',
                'category' => 'galaxy-s-series',
                'type' => 'variable',
                'variants' => [
                    [
                        'sku' => 'S24U-256-GRAY', 'price' => 33990000, 'sale_price' => 29990000, 'stock' => 40,
                        'attrs' => ['Màu sắc' => 'Xám Space', 'Dung lượng' => '256GB'],
                        'specs' => ['Chip' => 'Snapdragon 8 Gen 3', 'Camera' => '200MP', 'Bút' => 'S-Pen kèm máy']
                    ]
                ]
            ],
            [
                'name' => 'MacBook Air M2 13-inch',
                'brand' => 'Apple',
                'category' => 'macbook',
                'type' => 'variable',
                'variants' => [
                    [
                        'sku' => 'MBA-M2-8-256', 'price' => 27990000, 'sale_price' => 24990000, 'stock' => 20,
                        'attrs' => ['RAM' => '8GB', 'Dung lượng' => '256GB'],
                        'specs' => ['CPU' => 'Apple M2 8-core', 'GPU' => '8-core', 'Màn hình' => '13.6 inch Liquid Retina']
                    ]
                ]
            ],
            [
                'name' => 'Xiaomi 14 Ultra',
                'brand' => 'Xiaomi',
                'category' => 'dien-thoai',
                'type' => 'variable',
                'variants' => [
                    [
                        'sku' => 'XI14U-12-512', 'price' => 32990000, 'sale_price' => 29990000, 'stock' => 15,
                        'attrs' => ['Màu sắc' => 'Đen', 'RAM' => '12GB', 'Dung lượng' => '512GB'],
                        'specs' => ['Ống kính' => 'Leica Summilux', 'Sạc' => '90W HyperCharge']
                    ]
                ]
            ],
            [
                'name' => 'Tai nghe AirPods Pro 2 MagSafe (USB-C)',
                'brand' => 'Apple',
                'category' => 'am-thanh',
                'type' => 'simple',
                'variants' => [
                    [
                        'sku' => 'AIRPODS-PRO2-C', 'price' => 6190000, 'sale_price' => 5790000, 'stock' => 100,
                        'attrs' => [],
                        'specs' => ['Chống ồn' => 'ANC thế hệ 2', 'Kết nối' => 'Bluetooth 5.3', 'Cổng sạc' => 'USB-C']
                    ]
                ]
            ],
            [
                'name' => 'Củ sạc nhanh Anker 511 Nano 3 30W',
                'brand' => 'Anker',
                'category' => 'cu-sac',
                'type' => 'simple',
                'variants' => [
                    [
                        'sku' => 'ANKER-NANO-30W', 'price' => 450000, 'sale_price' => 350000, 'stock' => 200,
                        'attrs' => [],
                        'specs' => ['Công suất' => '30W', 'Công nghệ' => 'GaN 3', 'Kích thước' => 'Siêu nhỏ gọn']
                    ]
                ]
            ],
            [
                'name' => 'Apple Watch Series 9 GPS',
                'brand' => 'Apple',
                'category' => 'apple-watch',
                'type' => 'variable',
                'variants' => [
                    [
                        'sku' => 'AW9-41-PINK', 'price' => 10490000, 'sale_price' => 9690000, 'stock' => 25,
                        'attrs' => ['Màu sắc' => 'Hồng', 'Phiên bản' => 'Chính hãng VN/A'],
                        'specs' => ['Kích thước' => '41mm', 'Tính năng' => 'Double Tap', 'Màn hình' => 'Always-on 2000 nits']
                    ]
                ]
            ],
            [
                'name' => 'Loa Bluetooth Marshall Emberton II',
                'brand' => 'Sony', // Tạm dùng Brand có sẵn
                'category' => 'loa-bluetooth',
                'type' => 'simple',
                'variants' => [
                    [
                        'sku' => 'MARSHALL-EMB-2', 'price' => 4490000, 'sale_price' => 4250000, 'stock' => 12,
                        'attrs' => [],
                        'specs' => ['Thời lượng pin' => '30+ giờ', 'Kháng nước' => 'IP67', 'Âm thanh' => 'True Stereophonic']
                    ]
                ]
            ],
            [
                'name' => 'Laptop ASUS Vivobook 15 OLED',
                'brand' => 'Asus',
                'category' => 'asus',
                'type' => 'variable',
                'variants' => [
                    [
                        'sku' => 'ASUS-VIVO-OLED-8-512', 'price' => 18990000, 'sale_price' => 16490000, 'stock' => 10,
                        'attrs' => ['RAM' => '8GB', 'Dung lượng' => '512GB'],
                        'specs' => ['CPU' => 'Ryzen 5 7000 Series', 'Màn hình' => '15.6 inch OLED 100% DCI-P3']
                    ]
                ]
            ],
            [
                'name' => 'Chuột Gaming Logitech G502 X Plus',
                'brand' => 'Sony',
                'category' => 'phu-kien',
                'type' => 'simple',
                'variants' => [
                    [
                        'sku' => 'LOGI-G502X-WH', 'price' => 3890000, 'sale_price' => 3490000, 'stock' => 18,
                        'attrs' => [],
                        'specs' => ['Cảm biến' => 'HERO 25K', 'Switch' => 'LIGHTFORCE Hybrid', 'LED' => 'LIGHTSYNC RGB']
                    ]
                ]
            ]
        ];

        foreach ($productsData as $p) {
            $product = Product::create([
                'name' => $p['name'],
                'slug' => Str::slug($p['name']),
                'brand_id' => $brands[$p['brand']] ?? null,
                'type' => $p['type'],
                'status' => 'active',
                'is_featured' => rand(0, 1),
                'description' => "Đây là mô tả chi tiết cho sản phẩm {$p['name']}. Sản phẩm chất lượng cao, bảo hành chính hãng.",
                'thumbnail' => null, // Bạn có thể thêm link ảnh placeholder
            ]);

            // Gắn danh mục
            if (isset($categories[$p['category']])) {
                $product->categories()->attach($categories[$p['category']]);
            }

            // Tạo các biến thể
            foreach ($p['variants'] as $v) {
                $variant = ProductVariant::create([
                    'product_id' => $product->id,
                    'sku' => $v['sku'],
                    'price' => $v['price'],
                    'sale_price' => $v['sale_price'],
                    'stock' => $v['stock'],
                    'status' => 'active'
                ]);

                // Gắn giá trị thuộc tính cho biến thể
                $atNames = [];
                foreach ($v['attrs'] as $atName => $atValue) {
                    $valId = $getAttrId($atName, $atValue);
                    if ($valId) {
                        $atNames[] = $valId;
                    }
                }
                if (!empty($atNames)) {
                    $variant->attributeValues()->attach($atNames);
                }

                // Gắn thông số kỹ thuật (Specifications)
                foreach ($v['specs'] as $sk => $sv) {
                    VariantSpecification::create([
                        'variant_id' => $variant->id,
                        'spec_key' => $sk,
                        'spec_value' => $sv
                    ]);
                }
            }
        }
    }
}
