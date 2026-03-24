<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class OrderItemsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Order::doesntHave('items')->chunk(100, function ($orders) {
            foreach ($orders as $order) {
                $itemCount = rand(1, 3);

                for ($i = 1; $i <= $itemCount; $i++) {
                    $quantity = rand(1, 5);
                    $unitPrice = rand(100000, 500000);

                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => null,
                        'product_name' => 'Sản phẩm ' . Str::upper(Str::random(5)),
                        'product_sku' => 'SKU-' . strtoupper(Str::random(5)),
                        'thumbnail' => null,
                        'unit_price' => $unitPrice,
                        'quantity' => $quantity,
                        'line_total' => $unitPrice * $quantity,
                    ]);
                }
            }
        });
    }
}
