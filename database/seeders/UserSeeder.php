<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        // 1. Lấy danh sách tất cả role_id hiện có trong DB (dưới dạng mảng)
        $roleIds = DB::table('roles')->pluck('id')->toArray();

        // Đảm bảo bảng roles đã có dữ liệu trước khi seed users
        if (empty($roleIds)) {
            $this->command->error('Bảng roles đang trống! Vui lòng chạy RoleSeeder trước.');
            return;
        }

        // Mảng chứa 3 trạng thái bạn yêu cầu
        $statuses = ['inactive', 'active', 'banned'];

        $users = [];

        // 2. Vòng lặp tạo 20 user giả
        for ($i = 0; $i < 20; $i++) {
            $users[] = [
                'name'              => $faker->name(),
                'email'             => $faker->unique()->safeEmail(),
                'phone'             => $faker->phoneNumber(),
                'avatar'            => $faker->imageUrl(200, 200, 'people', true),
                'status'            => $faker->randomElement($statuses), // Lấy random 1 trong 3 trạng thái
                'email_verified_at' => $faker->boolean(80) ? now() : null, // 80% user đã verify email
                'password'          => Hash::make('password123'), // Set mật khẩu mặc định là password123
                'role_id'           => $faker->randomElement($roleIds), // Lấy ngẫu nhiên 1 role_id hợp lệ
                'created_at'        => now(),
                'updated_at'        => now(),
            ];
        }
        DB::table('users')->insert($users);
    }
}
