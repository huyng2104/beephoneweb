<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

// Check order items with products
$orderItems = DB::table('order_items')
    ->join('orders', 'order_items.order_id', '=', 'orders.id')
    ->select('order_items.*', 'orders.order_code')
    ->get();

echo "Total order items: " . $orderItems->count() . "\n\n";

foreach ($orderItems as $item) {
    echo "Order: " . $item->order_code . "\n";
    echo "  - Product ID: " . $item->product_id . "\n";
    echo "  - Product Name: " . $item->product_name . "\n";
    echo "  - Unit Price: " . $item->unit_price . "\n";
    echo "  - Quantity: " . $item->quantity . "\n";
    echo "\n";
}

// Check products in database
echo "\n=== Available Products ===\n";
$products = DB::table('products')->get();
foreach ($products as $product) {
    echo "ID: " . $product->id . " | Name: " . $product->name . " | Price: " . $product->price . "\n";
}
