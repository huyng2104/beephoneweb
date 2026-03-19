<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;

// Check if user exists
$user = User::where('email', 'vultph35315@fpt.edu.vn')->first();

if ($user) {
    echo "User found!\n";
    echo "Email: " . $user->email . "\n";
    echo "Status: " . $user->status . "\n";
    echo "Email Verified: " . ($user->email_verified_at ? 'Yes' : 'No') . "\n";
    echo "Role ID: " . $user->role_id . "\n";
} else {
    echo "User not found!\n";
}
