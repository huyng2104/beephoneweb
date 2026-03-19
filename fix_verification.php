<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

// Direct SQL update to set email verification
DB::table('users')
    ->where('email', 'vultph35315@fpt.edu.vn')
    ->update([
        'email_verified_at' => DB::raw("datetime('now')"),
        'status' => 'active'
    ]);

// Verify it was set
$user = DB::table('users')->where('email', 'vultph35315@fpt.edu.vn')->first();

if ($user && $user->email_verified_at) {
    echo "✓ User successfully verified!\n";
    echo "Email: " . $user->email . "\n";
    echo "Email Verified At: " . $user->email_verified_at . "\n";
    echo "Status: " . $user->status . "\n";
    echo "\nYou can now login with:\n";
    echo "Email: vultph35315@fpt.edu.vn\n";
    echo "Password: 123456\n";
} else {
    echo "Failed to verify user!\n";
}
