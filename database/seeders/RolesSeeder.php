<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('roles')->insert([
            [
                'name' => 'admin',
                'description' => 'Quản trị viên',
                'created_at' => now(),
            ],
            [
                'name' => 'staff',
                'description' => 'Nhân viên',
                'created_at' => now(),
            ],
            [
                'name' => 'user',
                'description' => 'Người dùng',
                'created_at' => now(),
            ],
        ]);
    }
}
