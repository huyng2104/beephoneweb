<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Order;
use Carbon\Carbon;

// Update existing orders with payment information
$orders = Order::all();

foreach ($orders as $order) {
    $order->update([
        'payment_method' => Order::PAYMENT_METHOD_COD,
        'payment_status' => Order::PAYMENT_STATUS_PENDING,
        'paid_at' => null,
    ]);
}

echo "✓ Updated " . $orders->count() . " orders with payment information.\n";
