<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $email = 'admin@example.com';

        $existing = DB::table('users')->where('email', $email)->first();
        if ($existing) {
            DB::table('users')->where('email', $email)->update([
                'password' => Hash::make('Admin@123'),
                'status' => 'active',
                'email_verified_at' => now(),
                'updated_at' => now(),
            ]);
            $this->command->info('Admin user updated: ' . $email . ' (password reset to Admin@123)');
            return;
        }

        $role = DB::table('roles')->where('name', 'admin')->first();
        if (! $role) {
            $this->command->error('Admin role not found. Run RolesSeeder first.');
            return;
        }

        DB::table('users')->insert([
            'name' => 'Administrator',
            'email' => $email,
            'phone' => '0123456789',
            'avatar' => null,
            'status' => 'active',
            'email_verified_at' => now(),
            'password' => Hash::make('Admin@123'),
            'role_id' => $role->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->command->info('Admin user created: ' . $email . ' (password: Admin@123)');
    }
}
