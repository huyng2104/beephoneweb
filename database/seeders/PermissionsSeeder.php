<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
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
            ['name' => 'Thêm khách hàng', 'slug' => 'customer.create'],
            ['name' => 'Cập nhật khách hàng', 'slug' => 'customer.update'],
            ['name' => 'Khóa khách hàng', 'slug' => 'customer.lock'],
            ['name' => 'Xóa khách hàng', 'slug' => 'customer.delete'],

            // Voucher
            ['name' => 'Xem voucher', 'slug' => 'voucher.view'],
            ['name' => 'Thêm voucher', 'slug' => 'voucher.create'],
            ['name' => 'Sửa voucher', 'slug' => 'voucher.update'],
            ['name' => 'Xóa voucher', 'slug' => 'voucher.delete'],

            // Bài viết
            ['name' => 'Xem bài viết', 'slug' => 'posts.view'],
            ['name' => 'Thêm bài viết', 'slug' => 'posts.create'],
            ['name' => 'Sửa bài viết', 'slug' => 'posts.update'],
            ['name' => 'Xóa bài viết', 'slug' => 'posts.delete'],

            // Banner
            ['name' => 'Xem banner', 'slug' => 'banner.view'],
            ['name' => 'Thêm banner', 'slug' => 'banner.create'],
            ['name' => 'Sửa banner', 'slug' => 'banner.update'],
            ['name' => 'Xóa banner', 'slug' => 'banner.delete'],

            // Ví tiền (Wallet)
            ['name' => 'Xem ví', 'slug' => 'wallet.view'],
            ['name' => 'Khóa/Mở khóa ví', 'slug' => 'wallet.update'],
            // Điểm thưởng
            ['name' => 'Xem điểm thưởng', 'slug' => 'point.view'],
            ['name' => 'Sửa điểm thưởng', 'slug' => 'point.update'],

            // Yêu cầu rút tiền
            ['name' => 'Xem yêu cầu rút tiền', 'slug' => 'withdrawal.view'],
            ['name' => 'Phê duyệt rút tiền', 'slug' => 'withdrawal.approve'],
            ['name' => 'Từ chối rút tiền', 'slug' => 'withdrawal.reject'],
            // Yêu cầu hỗ trợ (Tickets)
            ['name' => 'Xem yêu cầu hỗ trợ', 'slug' => 'support.view'],
            ['name' => 'Sửa yêu cầu hỗ trợ', 'slug' => 'support.update'],
            ['name' => 'Xóa yêu cầu hỗ trợ', 'slug' => 'support.delete'],

            // Cài đặt chung / Hệ thống
            ['name' => 'Xem cài đặt hệ thống', 'slug' => 'settings.view'],
            ['name' => 'Cập nhật cài đặt hệ thống', 'slug' => 'settings.update'],

            // Nhân viên (Staff / Member)
            ['name' => 'Xem nhân viên', 'slug' => 'member.view'],
            ['name' => 'Thêm nhân viên', 'slug' => 'member.create'],
            ['name' => 'Sửa nhân viên', 'slug' => 'member.update'],
            ['name' => 'Xóa nhân viên', 'slug' => 'member.delete'],

            // Bình luận
            ['name' => 'Xem bình luận', 'slug' => 'comment.view'],
            ['name' => 'Trả lời bình luận', 'slug' => 'comment.reply'],
            ['name' => 'Xóa bình luận', 'slug' => 'comment.delete'],

            // Danh mục bài viết
            ['name' => 'Xem danh mục bài viết', 'slug' => 'post_category.view'],
            ['name' => 'Thêm danh mục bài viết', 'slug' => 'post_category.create'],
            ['name' => 'Sửa danh mục bài viết', 'slug' => 'post_category.update'],
            ['name' => 'Xóa danh mục bài viết', 'slug' => 'post_category.delete'],

            // Lịch sử hoạt động
            ['name' => 'Xem lịch sử hoạt động', 'slug' => 'activity.view'],

            // Vai trò & Quyền hạn (Roles & Permissions)
            ['name' => 'Xem vai trò & quyền', 'slug' => 'roles.view'],
            ['name' => 'Thêm vai trò & quyền', 'slug' => 'roles.create'],
            ['name' => 'Sửa vai trò & quyền', 'slug' => 'roles.update'],
            ['name' => 'Xóa vai trò & quyền', 'slug' => 'roles.delete'],

            // Thống kê tối giản
            ['name' => 'Xem thống kê tổng quan', 'slug' => 'reports.view'],
            ['name' => 'Tải file báo cáo dữ liệu', 'slug' => 'reports.export'],

        ];


         // Nhớ use Model ở trên đầu file

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['slug' => $permission['slug']], // Tìm theo slug
                ['name' => $permission['name']]  // Nếu không thấy thì tạo mới kèm name
            );
        }
    }
}
