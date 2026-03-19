<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

// Get admin role
$adminRole = \App\Models\Role::where('name', 'admin')->first();

if ($adminRole) {
    $email = 'vultph35315@fpt.edu.vn';
    
    // Check if user already exists
    $existing = User::where('email', $email)->first();
    
    if ($existing) {
        // Update the existing user
        $existing->update([
            'password' => Hash::make('123456'),
            'email_verified_at' => now(),
            'status' => 'active'
        ]);
        echo "User updated successfully!\n";
    } else {
        $user = User::create([
            'name' => 'Admin',
            'email' => $email,
            'password' => Hash::make('123456'),
            'phone' => '0123456789',
            'status' => 'active',
            'email_verified_at' => now(),
            'role_id' => $adminRole->id
        ]);
        echo "User created successfully!\n";
    }
    
    echo "Email: " . $email . "\n";
    echo "Password: 123456\n";
} else {
    echo "Admin role not found!\n";
}
