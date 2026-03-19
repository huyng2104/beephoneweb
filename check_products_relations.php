<?php
require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$products = \App\Models\Product::with(['categories', 'brand'])->orderBy('id', 'desc')->get();

echo "Products returned by Query (with relationships):\n";
echo "Total: " . $products->count() . "\n\n";

foreach($products as $p) {
    echo "ID: " . $p->id . " | Name: " . $p->name . "\n";
    echo "  - Categories: " . $p->categories->count() . " categories\n";
    if($p->categories->count() > 0) {
        foreach($p->categories as $cat) {
            echo "    • " . $cat->name . "\n";
        }
    }
    echo "  - Brand: " . ($p->brand ? $p->brand->name : 'No brand') . "\n";
    echo "  - Thumbnail: " . ($p->thumbnail ? 'Yes' : 'No') . "\n";
    echo "\n";
}
