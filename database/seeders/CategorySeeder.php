<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // --- 1. NHÓM ĐIỆN THOẠI ---
        $phone = Category::updateOrCreate(['slug' => 'dien-thoai'], [
            'name' => 'Điện thoại',
            'parent_id' => null,
            'is_active' => true,
            'sort_order' => 1
        ]);

        // iPhone & Series
        $iphone = Category::updateOrCreate(['slug' => 'iphone'], [
            'name' => 'iPhone',
            'parent_id' => $phone->id,
            'is_active' => true,
            'sort_order' => 1
        ]);
        
        $iphone_series = ['iPhone 15 Series', 'iPhone 14 Series', 'iPhone 13 Series', 'iPhone 11 Series'];
        foreach ($iphone_series as $idx => $name) {
            Category::updateOrCreate(['slug' => Str::slug($name)], [
                'name' => $name,
                'parent_id' => $iphone->id,
                'is_active' => true,
                'sort_order' => $idx + 1
            ]);
        }

        // Samsung & Dòng
        $samsung = Category::updateOrCreate(['slug' => 'samsung'], [
            'name' => 'Samsung',
            'parent_id' => $phone->id,
            'is_active' => true,
            'sort_order' => 2
        ]);
        
        $samsung_series = ['Galaxy S Series', 'Galaxy Z Series (Fold/Flip)', 'Galaxy A Series'];
        foreach ($samsung_series as $idx => $name) {
            Category::updateOrCreate(['slug' => Str::slug($name)], [
                'name' => $name,
                'parent_id' => $samsung->id,
                'is_active' => true,
                'sort_order' => $idx + 1
            ]);
        }

        // --- 2. NHÓM LAPTOP ---
        $laptop = Category::updateOrCreate(['slug' => 'laptop'], [
            'name' => 'Laptop',
            'parent_id' => null,
            'is_active' => true,
            'sort_order' => 2
        ]);

        $laptop_brands = ['MacBook', 'Asus', 'HP', 'Dell', 'Lenovo'];
        foreach ($laptop_brands as $idx => $name) {
            Category::updateOrCreate(['slug' => Str::slug($name)], [
                'name' => $name,
                'parent_id' => $laptop->id,
                'is_active' => true,
                'sort_order' => $idx + 1
            ]);
        }

        // --- 3. NHÓM PHỤ KIỆN (NHIỀU CẤP ĐỘ) ---
        $accessory = Category::updateOrCreate(['slug' => 'phu-kien'], [
            'name' => 'Phụ kiện',
            'parent_id' => null,
            'is_active' => true,
            'sort_order' => 3
        ]);

        // Cáp & Sạc
        $charging = Category::updateOrCreate(['slug' => 'cap-sac'], [
            'name' => 'Cáp Sạc',
            'parent_id' => $accessory->id,
            'is_active' => true,
            'sort_order' => 1
        ]);
        Category::updateOrCreate(['slug' => 'cu-sac'], ['name' => 'Củ sạc', 'parent_id' => $charging->id, 'is_active' => true]);
        Category::updateOrCreate(['slug' => 'cap-lightning'], ['name' => 'Cáp Lightning', 'parent_id' => $charging->id, 'is_active' => true]);
        Category::updateOrCreate(['slug' => 'cap-usb-c'], ['name' => 'Cáp USB-C', 'parent_id' => $charging->id, 'is_active' => true]);

        // Âm thanh
        $audio = Category::updateOrCreate(['slug' => 'am-thanh'], [
            'name' => 'Âm thanh',
            'parent_id' => $accessory->id,
            'is_active' => true,
            'sort_order' => 2
        ]);
        Category::updateOrCreate(['slug' => 'tai-nghe-bluetooth'], ['name' => 'Tai nghe Bluetooth', 'parent_id' => $audio->id, 'is_active' => true]);
        Category::updateOrCreate(['slug' => 'loa-bluetooth'], ['name' => 'Loa Bluetooth', 'parent_id' => $audio->id, 'is_active' => true]);

        // --- 4. ĐỒNG HỒ THÔNG MINH ---
        $watch = Category::updateOrCreate(['slug' => 'dong-ho'], [
            'name' => 'Đồng hồ',
            'parent_id' => null,
            'is_active' => true,
            'sort_order' => 4
        ]);
        Category::updateOrCreate(['slug' => 'apple-watch'], ['name' => 'Apple Watch', 'parent_id' => $watch->id, 'is_active' => true]);
        Category::updateOrCreate(['slug' => 'samsung-galaxy-watch'], ['name' => 'Samsung Galaxy Watch', 'parent_id' => $watch->id, 'is_active' => true]);
    }
}
