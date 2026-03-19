<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Product;
use App\Models\Brand;
use App\Models\Category;
use Illuminate\Support\Str;

// Get or create brands
$brands = Brand::all();
if ($brands->count() == 0) {
    echo "Creating sample brands...\n";
    Brand::create(['name' => 'Apple', 'slug' => 'apple']);
    Brand::create(['name' => 'Samsung', 'slug' => 'samsung']);
    Brand::create(['name' => 'Xiaomi', 'slug' => 'xiaomi']);
    $brands = Brand::all();
}

// Get categories
$categories = Category::all();

if ($brands->count() > 0 && $categories->count() > 0) {
    // Product 1
    $product1 = Product::create([
        'name' => 'iPhone 15 Pro Max',
        'slug' => Str::slug('iPhone 15 Pro Max'),
        'description' => 'Điện thoại thông minh cao cấp với màn hình 6.7 inch, chip A17 Pro, camera 48MP',
        'type' => 'simple',
        'price' => 28990000,
        'sale_price' => 24990000,
        'sku' => 'IPHONE15PM-001',
        'stock' => 50,
        'status' => 'active',
        'is_featured' => 1,
        'brand_id' => $brands->where('name', 'Apple')->first()->id,
        'thumbnail' => 'https://via.placeholder.com/400?text=iPhone+15+Pro+Max',
        'specifications' => [
            'display' => '6.7\" ProMotion OLED',
            'processor' => 'A17 Pro',
            'ram' => '8GB',
            'storage' => '256GB',
            'camera' => '48MP Main + 12MP Ultra Wide + 12MP Telephoto'
        ]
    ]);
    $product1->categories()->attach($categories->random(2)->pluck('id')->toArray());
    echo "✓ Product 1 created: iPhone 15 Pro Max\n";

    // Product 2
    $product2 = Product::create([
        'name' => 'Samsung Galaxy S24 Ultra',
        'slug' => Str::slug('Samsung Galaxy S24 Ultra'),
        'description' => 'Flagship Samsung với màn hình 6.9 inch QHD+, chip Snapdragon 8 Gen 3',
        'type' => 'simple',
        'price' => 27990000,
        'sale_price' => 23990000,
        'sku' => 'SAMSUNG-S24U-001',
        'stock' => 45,
        'status' => 'active',
        'is_featured' => 1,
        'brand_id' => $brands->where('name', 'Samsung')->first()->id,
        'thumbnail' => 'https://via.placeholder.com/400?text=Samsung+S24+Ultra',
        'specifications' => [
            'display' => '6.9\" QHD+ Dynamic AMOLED',
            'processor' => 'Snapdragon 8 Gen 3',
            'ram' => '12GB',
            'storage' => '512GB',
            'camera' => '200MP Main + 50MP Ultra Wide + 10MP + 10MP Telephoto'
        ]
    ]);
    $product2->categories()->attach($categories->random(2)->pluck('id')->toArray());
    echo "✓ Product 2 created: Samsung Galaxy S24 Ultra\n";

    // Product 3
    $product3 = Product::create([
        'name' => 'Xiaomi 14 Ultra',
        'slug' => Str::slug('Xiaomi 14 Ultra'),
        'description' => 'Smartphone flagship Xiaomi với camera Leica, chip Snapdragon 8 Gen 2 Leading',
        'type' => 'simple',
        'price' => 19990000,
        'sale_price' => 17990000,
        'sku' => 'XIAOMI-14U-001',
        'stock' => 60,
        'status' => 'active',
        'is_featured' => 1,
        'brand_id' => $brands->where('name', 'Xiaomi')->first()->id,
        'thumbnail' => 'https://via.placeholder.com/400?text=Xiaomi+14+Ultra',
        'specifications' => [
            'display' => '6.73\" AMOLED 1440x3200',
            'processor' => 'Snapdragon 8 Gen 2 Leading Version',
            'ram' => '16GB',
            'storage' => '512GB',
            'camera' => '50MP Main (Leica) + 50MP Ultra Wide + 50MP Telephoto + 50MP Macro'
        ]
    ]);
    $product3->categories()->attach($categories->random(2)->pluck('id')->toArray());
    echo "✓ Product 3 created: Xiaomi 14 Ultra\n";

    echo "\n✓ Successfully added 3 products!\n";
} else {
    echo "Error: Not enough brands or categories in the database.\n";
    echo "Brands: " . $brands->count() . "\n";
    echo "Categories: " . $categories->count() . "\n";
}
