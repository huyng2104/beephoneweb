<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Product;

// Update products with real product images from Unsplash (high quality free images)
$products = [
    [
        'id' => 1,
        'thumbnail' => 'https://images.unsplash.com/photo-1592286927505-1def25115558?w=500&h=500&fit=crop', // iPhone
        'name' => 'iPhone 15 Pro Max'
    ],
    [
        'id' => 2,
        'thumbnail' => 'https://images.unsplash.com/photo-1610945415295-d9bbf7ce3f1d?w=500&h=500&fit=crop', // Samsung
        'name' => 'Samsung Galaxy S24 Ultra'
    ],
    [
        'id' => 3,
        'thumbnail' => 'https://images.unsplash.com/photo-1511707267537-b85faf00021e?w=500&h=500&fit=crop', // Xiaomi/Generic Android
        'name' => 'Xiaomi 14 Ultra'
    ]
];

foreach ($products as $productData) {
    $product = Product::find($productData['id']);
    if ($product) {
        $product->update(['thumbnail' => $productData['thumbnail']]);
        echo "✓ Updated {$productData['name']} with real image\n";
    }
}

echo "\n✓ All products updated with real images!\n";
