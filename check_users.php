<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

// Check existing users
$users = User::all();
echo "Current users: " . $users->count() . "\n";

if ($users->count() > 0) {
    echo "Users:\n";
    foreach ($users as $user) {
        echo "- " . $user->email . " (role_id: " . $user->role_id . ")\n";
    }
} else {
    echo "No users found. Creating an admin user...\n";
    
    // Get admin role
    $adminRole = \App\Models\Role::where('name', 'admin')->first();
    
    if ($adminRole) {
        $user = User::create([
            'name' => 'Admin',
            'email' => 'admin@local',
            'password' => Hash::make('123456'),
            'phone' => '0123456789',
            'status' => 'active',
            'email_verified_at' => now(),
            'role_id' => $adminRole->id
        ]);
        
        echo "Admin user created successfully!\n";
        echo "Email: admin@local\n";
        echo "Password: 123456\n";
    } else {
        echo "Admin role not found!\n";
    }
}
