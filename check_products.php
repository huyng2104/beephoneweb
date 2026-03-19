<?php
require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$products = \App\Models\Product::all();

echo "Total products: " . $products->count() . "\n";
echo "\nProduct List:\n";
echo str_repeat("=", 80) . "\n";

foreach($products as $p) {
    echo "ID: " . $p->id . "\n";
    echo "Name: " . $p->name . "\n";
    echo "SKU: " . ($p->sku ?? 'N/A') . "\n";
    echo "Thumbnail: " . ($p->thumbnail ?? 'N/A') . "\n";
    echo "Price: " . $p->price . " / Sale: " . $p->sale_price . "\n";
    echo "Stock: " . $p->stock . "\n";
    echo "Status: " . $p->status . "\n";
    echo "---\n";
}
