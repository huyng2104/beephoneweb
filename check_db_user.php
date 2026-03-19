<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

// Check user directly from database
$user = DB::table('users')->where('email', 'vultph35315@fpt.edu.vn')->first();

if ($user) {
    echo "User: " . $user->email . "\n";
    echo "Email Verified At: " . $user->email_verified_at . "\n";
    echo "Status: " . $user->status . "\n";
    echo "Role ID: " . $user->role_id . "\n";
} else {
    echo "User not found!\n";
}
