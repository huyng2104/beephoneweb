<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Role;

// Check the user and their role
$user = User::where('email', 'vultph35315@fpt.edu.vn')->first();

if ($user) {
    echo "User: " . $user->email . "\n";
    echo "Role ID: " . $user->role_id . "\n";
    echo "Email Verified: " . ($user->email_verified_at ? 'Yes' : 'No') . "\n";
    echo "Status: " . $user->status . "\n";
    
    $role = Role::find($user->role_id);
    if ($role) {
        echo "Role Name: " . $role->name . "\n";
    }
    
    echo "\nPermissions:\n";
    $permissions = $user->permissions;
    if ($permissions->count() > 0) {
        foreach ($permissions as $permission) {
            echo "- " . $permission->slug . "\n";
        }
    } else {
        echo "No permissions assigned\n";
    }
} else {
    echo "User not found!\n";
}

// Check all roles
echo "\n\nAll Roles:\n";
$roles = Role::all();
foreach ($roles as $role) {
    echo "- ID: " . $role->id . ", Name: " . $role->name . "\n";
}
