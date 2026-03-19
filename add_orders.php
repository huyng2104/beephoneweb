<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Str;
use Carbon\Carbon;

// Get products and users
$products = Product::all();
$users = User::where('role_id', 3)->get(); // Get regular users

// If no regular users, create one
if ($users->count() == 0) {
    $user = User::create([
        'name' => 'John Doe',
        'email' => 'customer@example.com',
        'password' => bcrypt('123456'),
        'phone' => '0912345678',
        'status' => 'active',
        'email_verified_at' => Carbon::now(),
        'role_id' => 3 // user role
    ]);
    $users = collect([$user]);
}

if ($products->count() >= 3) {
    // Order 1 - Pending
    $order1 = Order::create([
        'order_code' => 'ORD-' . date('Ymd') . '-001',
        'user_id' => $users->first()->id,
        'customer_name' => 'Nguyễn Văn A',
        'phone' => '0912345678',
        'customer_phone' => '0912345678',
        'customer_email' => 'customer@example.com',
        'recipient_name' => 'Nguyễn Văn A',
        'recipient_phone' => '0912345678',
        'recipient_address' => '123 Đường Lê Lợi, Quận 1, TP HCM',
        'shipping_address' => '123 Đường Lê Lợi, Quận 1, TP HCM',
        'address' => '123 Đường Lê Lợi, Quận 1, TP HCM',
        'total_amount' => 49980000,
        'total_price' => 49980000,
        'status' => 'pending',
        'return_status' => 'none',
        'note' => 'Giao hàng vào giờ hành chính',
        'ordered_at' => Carbon::now()->subDays(2)
    ]);

    // Add order items for order 1
    OrderItem::create([
        'order_id' => $order1->id,
        'product_id' => $products[0]->id,
        'product_name' => $products[0]->name,
        'product_sku' => $products[0]->sku,
        'thumbnail' => $products[0]->thumbnail,
        'unit_price' => $products[0]->sale_price,
        'quantity' => 1,
        'line_total' => $products[0]->sale_price
    ]);

    OrderItem::create([
        'order_id' => $order1->id,
        'product_id' => $products[1]->id,
        'product_name' => $products[1]->name,
        'product_sku' => $products[1]->sku,
        'thumbnail' => $products[1]->thumbnail,
        'unit_price' => $products[1]->sale_price,
        'quantity' => 1,
        'line_total' => $products[1]->sale_price
    ]);

    echo "✓ Order 1 created (Pending): " . $order1->order_code . "\n";

    // Order 2 - Packing
    $order2 = Order::create([
        'order_code' => 'ORD-' . date('Ymd') . '-002',
        'user_id' => $users->first()->id,
        'customer_name' => 'Trần Thị B',
        'phone' => '0987654321',
        'customer_phone' => '0987654321',
        'customer_email' => 'customerb@example.com',
        'recipient_name' => 'Trần Thị B',
        'recipient_phone' => '0987654321',
        'recipient_address' => '456 Đường Nguyễn Huệ, Quận 3, TP HCM',
        'shipping_address' => '456 Đường Nguyễn Huệ, Quận 3, TP HCM',
        'address' => '456 Đường Nguyễn Huệ, Quận 3, TP HCM',
        'total_amount' => 17990000,
        'total_price' => 17990000,
        'status' => 'packing',
        'return_status' => 'none',
        'note' => 'Thanh toán khi nhận hàng',
        'ordered_at' => Carbon::now()->subDays(1)
    ]);

    // Add order items for order 2
    OrderItem::create([
        'order_id' => $order2->id,
        'product_id' => $products[2]->id,
        'product_name' => $products[2]->name,
        'product_sku' => $products[2]->sku,
        'thumbnail' => $products[2]->thumbnail,
        'unit_price' => $products[2]->sale_price,
        'quantity' => 1,
        'line_total' => $products[2]->sale_price
    ]);

    echo "✓ Order 2 created (Packing): " . $order2->order_code . "\n";

    // Order 3 - Shipping
    $order3 = Order::create([
        'order_code' => 'ORD-' . date('Ymd') . '-003',
        'user_id' => $users->first()->id,
        'customer_name' => 'Phạm Văn C',
        'phone' => '0933333333',
        'customer_phone' => '0933333333',
        'customer_email' => 'customerc@example.com',
        'recipient_name' => 'Phạm Văn C',
        'recipient_phone' => '0933333333',
        'recipient_address' => '789 Đường Trần Hưng Đạo, Quận 5, TP HCM',
        'shipping_address' => '789 Đường Trần Hưng Đạo, Quận 5, TP HCM',
        'address' => '789 Đường Trần Hưng Đạo, Quận 5, TP HCM',
        'total_amount' => 24990000,
        'total_price' => 24990000,
        'status' => 'shipping',
        'return_status' => 'none',
        'note' => '',
        'ordered_at' => Carbon::now()->subHours(12)
    ]);

    // Add order items for order 3
    OrderItem::create([
        'order_id' => $order3->id,
        'product_id' => $products[0]->id,
        'product_name' => $products[0]->name,
        'product_sku' => $products[0]->sku,
        'thumbnail' => $products[0]->thumbnail,
        'unit_price' => $products[0]->sale_price,
        'quantity' => 1,
        'line_total' => $products[0]->sale_price
    ]);

    echo "✓ Order 3 created (Shipping): " . $order3->order_code . "\n";

    echo "\n✓ Successfully added 3 orders with items!\n";
    echo "Total orders: " . Order::count() . "\n";
    echo "Total order items: " . OrderItem::count() . "\n";
} else {
    echo "Error: Not enough products in the database.\n";
    echo "Products: " . $products->count() . "\n";
}
