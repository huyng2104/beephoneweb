<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

// Get admin role
$adminRole = \App\Models\Role::where('name', 'admin')->first();

if ($adminRole) {
    // Check if admin@local already exists
    $existing = User::where('email', 'admin@local')->first();
    
    if ($existing) {
        echo "User admin@local already exists!\n";
        echo "Email: admin@local\n";
        echo "Password: 123456\n";
    } else {
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
    }
} else {
    echo "Admin role not found!\n";
}
