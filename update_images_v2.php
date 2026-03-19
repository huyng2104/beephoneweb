<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Product;

// Update products with real product images from different sources (public images)
$products = [
    [
        'id' => 1,
        'name' => 'iPhone 15 Pro Max',
        'thumbnail' => 'https://cdn.shopify.com/s/files/1/0969/9128/products/apple_iphone_15_pro_max_blue_titanium_1_1024x1024_2x_9e53e1bd-54a1-429f-bb9c-a36b00db448e.jpg?v=1695897606'
    ],
    [
        'id' => 2,
        'name' => 'Samsung Galaxy S24 Ultra',
        'thumbnail' => 'https://images.samsung.com/us/smartphones/galaxy-s24-ultra/buy/product_image_mobile_s24u_onyx.jpg'
    ],
    [
        'id' => 3,
        'name' => 'Xiaomi 14 Ultra',
        'thumbnail' => 'https://cdn.gsmarena.com/imgprocv3/800C/gsmarena_000.jpg'
    ]
];

foreach ($products as $productData) {
    $product = Product::find($productData['id']);
    if ($product) {
        $product->update(['thumbnail' => $productData['thumbnail']]);
        echo "✓ Updated {$productData['name']}\n";
        echo "  URL: {$productData['thumbnail']}\n\n";
    }
}

echo "✓ All products updated!\n";
