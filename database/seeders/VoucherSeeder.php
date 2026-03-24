<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Voucher;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;

class VoucherSeeder extends Seeder
{
    public function run(): void
    {


        $voucherAll = Voucher::create([
            'name' => 'Giảm giá 1',
            'code' => 'SALE50K',
            'description' => 'Giảm giá tất cả sản phẩm',
            'discount_type' => 'fixed',
            'discount_value' => 50000,
            'max_discount' => null,
            'min_order_value' => 200000,
            'usage_limit' => 100,
            'used_count' => 0,
            'start_date' => now(),
            'end_date' => now()->addDays(30),
            'status' => 1,
        ]);



        $voucherProduct = Voucher::create([
            'name' => 'Giảm giá 2',
            'code' => 'PRODUCT10',
            'description' => 'Giảm giá tất cả sản phẩm',
            'discount_type' => 'percent',
            'discount_value' => 10,
            'max_discount' => 100000,
            'min_order_value' => null,
            'usage_limit' => 50,
            'used_count' => 0,
            'start_date' => now(),
            'end_date' => now()->addDays(15),
            'status' => 1,
        ]);


    }
}
