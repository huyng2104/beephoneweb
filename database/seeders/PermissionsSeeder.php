<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [

            // Product
            ['name' => 'Xem sản phẩm', 'slug' => 'product.view'],
            ['name' => 'Thêm sản phẩm', 'slug' => 'product.create'],
            ['name' => 'Sửa sản phẩm', 'slug' => 'product.update'],
            ['name' => 'Xóa sản phẩm', 'slug' => 'product.delete'],

            // Attribute
            ['name' => 'Xem thuộc tính', 'slug' => 'attribute.view'],
            ['name' => 'Thêm thuộc tính', 'slug' => 'attribute.create'],
            ['name' => 'Sửa thuộc tính', 'slug' => 'attribute.update'],
            ['name' => 'Xóa thuộc tính', 'slug' => 'attribute.delete'],

            // Category
            ['name' => 'Xem danh mục', 'slug' => 'category.view'],
            ['name' => 'Thêm danh mục', 'slug' => 'category.create'],
            ['name' => 'Sửa danh mục', 'slug' => 'category.update'],
            ['name' => 'Xóa danh mục', 'slug' => 'category.delete'],

            // Brand
            ['name' => 'Xem thương hiệu', 'slug' => 'brand.view'],
            ['name' => 'Thêm thương hiệu', 'slug' => 'brand.create'],
            ['name' => 'Sửa thương hiệu', 'slug' => 'brand.update'],
            ['name' => 'Xóa thương hiệu', 'slug' => 'brand.delete'],

            // Order
            ['name' => 'Xem đơn hàng', 'slug' => 'order.view'],
            ['name' => 'Cập nhật đơn hàng', 'slug' => 'order.update'],
            ['name' => 'Hủy đơn hàng', 'slug' => 'order.cancel'],

            // Customer
            ['name' => 'Xem khách hàng', 'slug' => 'customer.view'],
            ['name' => 'Cập nhật khách hàng', 'slug' => 'customer.update'],
            ['name' => 'Khóa khách hàng', 'slug' => 'customer.lock'],

            // Voucher
            ['name' => 'Xem voucher', 'slug' => 'voucher.view'],
            ['name' => 'Thêm voucher', 'slug' => 'voucher.create'],
            ['name' => 'Sửa voucher', 'slug' => 'voucher.update'],
            ['name' => 'Xóa voucher', 'slug' => 'voucher.delete'],
        ];

        foreach ($permissions as $permission) {
            DB::table('permissions')->insert([
                'name' => $permission['name'],
                'slug' => $permission['slug'],
                'created_at' => now(),
            ]);
        }
    }
}
