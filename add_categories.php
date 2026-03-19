<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Category;
use Illuminate\Support\Str;

// Create sample categories
$categories = [
    ['name' => 'iPhone', 'slug' => 'iphone', 'status' => 'active'],
    ['name' => 'Samsung', 'slug' => 'samsung', 'status' => 'active'],
    ['name' => 'Android', 'slug' => 'android', 'status' => 'active'],
    ['name' => 'Flagship', 'slug' => 'flagship', 'status' => 'active'],
];

foreach ($categories as $category) {
    Category::create($category);
    echo "✓ Category created: " . $category['name'] . "\n";
}

echo "\nTotal categories now: " . Category::count() . "\n";
