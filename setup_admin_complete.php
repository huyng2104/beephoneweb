<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Carbon\Carbon;

// Update user email verification and add permissions
$user = User::where('email', 'vultph35315@fpt.edu.vn')->first();

if ($user) {
    // Verify email
    $user->update([
        'email_verified_at' => Carbon::now(),
        'status' => 'active'
    ]);
    
    // Get admin role and all permissions
    $adminRole = Role::where('name', 'admin')->first();
    $permissions = Permission::all();
    
    // Assign all permissions to user
    $user->permissions()->sync($permissions->pluck('id')->toArray());
    
    echo "User updated successfully!\n";
    echo "Email: " . $user->email . "\n";
    echo "Email Verified: Yes\n";
    echo "Status: Active\n";
    echo "Role: " . $adminRole->name . "\n";
    echo "Permissions assigned: " . $permissions->count() . "\n";
    echo "\nYou can now login with:\n";
    echo "Email: vultph35315@fpt.edu.vn\n";
    echo "Password: 123456\n";
} else {
    echo "User not found!\n";
}
