<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class RolePermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $staffRoleId = DB::table('roles')->where('name', 'staff')->value('id');
        $permissions = DB::table('permissions')->pluck('id');
        // Staff chỉ có một số quyền
        $staffPermissions = DB::table('permissions')
            ->whereIn('slug', [
                'product.view',
                'product.create',
                'product.update',

                'category.view',

                'brand.view',

                'order.view',
                'order.update',

                'customer.view',

                'voucher.view'
            ])
            ->pluck('id');

        foreach ($staffPermissions as $permissionId) {
            DB::table('role_permissions')->insert([
                'role_id' => $staffRoleId,
                'permission_id' => $permissionId
            ]);
        }
    }
}
