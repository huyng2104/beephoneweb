<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Carbon\Carbon;

// Update the user to mark email as verified
$user = User::where('email', 'vultph35315@fpt.edu.vn')->first();

if ($user) {
    $user->update([
        'email_verified_at' => Carbon::now(),
        'status' => 'active'
    ]);
    echo "User updated successfully!\n";
    echo "Email Verified: Yes\n";
    echo "Status: Active\n";
    echo "\nYou can now login with:\n";
    echo "Email: vultph35315@fpt.edu.vn\n";
    echo "Password: 123456\n";
} else {
    echo "User not found!\n";
}
