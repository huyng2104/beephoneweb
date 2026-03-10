<?php

namespace Database\Seeders;

use App\Models\Order;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = ['pending', 'packing', 'shipping', 'delivered', 'received', 'cancelled'];
        $returnStatuses = ['none', 'requested', 'confirmed'];

        for ($i = 1; $i <= 10; $i++) {
            $phone = '0' . rand(900000000, 999999999);
            Order::create([
                'order_code' => 'ORD' . str_pad($i, 6, '0', STR_PAD_LEFT),
                'user_id' => rand(1, 5),
                'customer_name' => 'Khách hàng ' . $i,
                'phone' => $phone,
                'address' => 'Địa chỉ ' . $i,
                'customer_phone' => $phone,
                'customer_email' => 'customer' . $i . '@example.com',
                'recipient_name' => 'Người nhận ' . $i,
                'recipient_phone' => '0' . rand(900000000, 999999999),
                'recipient_address' => 'Địa chỉ nhận hàng ' . $i,
                'shipping_address' => 'Địa chỉ giao hàng ' . $i,
                'total_price' => rand(100000, 5000000),
                'total_amount' => rand(100000, 5000000),
                'status' => $statuses[array_rand($statuses)],
                'return_status' => $returnStatuses[array_rand($returnStatuses)],
                'note' => 'Ghi chú đơn hàng ' . $i,
                'ordered_at' => now()->subDays(rand(1, 30)),
            ]);
        }
    }
}
