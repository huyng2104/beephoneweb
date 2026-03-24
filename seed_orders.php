<?php

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Str;

$users = User::all();
$products = Product::all();

if ($users->isEmpty() || $products->isEmpty()) {
    echo "Cần ít nhất 1 User và 1 Product để tạo đơn hàng.";
    return;
}

for ($i = 1; $i <= 5; $i++) {
    $user = $users->random();
    $product = $products->random();
    $quantity = rand(1, 3);
    $price = $product->price ?? 1000000;
    $totalAmount = $price * $quantity;
    
    try {
        $order = Order::create([
            'order_code' => 'BEE' . strtoupper(Str::random(8)),
            'user_id' => $user->id,
            'customer_name' => $user->name,
            'phone' => $user->phone ?? '0123456789',
            'address' => 'Hà Nội, Việt Nam',
            'total_price' => $totalAmount,
            'status' => 'pending',
            'customer_phone' => $user->phone ?? '0123456789',
            'customer_email' => $user->email,
            'recipient_name' => $user->name,
            'recipient_phone' => $user->phone ?? '0123456789',
            'recipient_address' => 'Hà Nội, Việt Nam',
            'shipping_address' => 'Hà Nội, Việt Nam',
            'total_amount' => $totalAmount,
            'return_status' => 'none',
            'discount_amount' => 0,
            'points_earned' => 0,
            'ordered_at' => now(),
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'product_sku' => $product->sku ?? 'SKU' . rand(100, 999),
            'thumbnail' => $product->thumbnail,
            'unit_price' => $price,
            'quantity' => $quantity,
            'line_total' => $totalAmount,
        ]);
        
        echo "Đơn hàng mới: {$order->order_code}\n";
    } catch (\Exception $e) {
        file_put_contents('seed_error.txt', $e->getMessage() . "\n" . $e->getTraceAsString());
        echo "Lỗi khi tạo đơn hàng $i: " . $e->getMessage() . "\n";
        break;
    }
}
