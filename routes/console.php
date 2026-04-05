<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(function () {
    $expiredTime = Carbon::now()->subMinutes(15);

    DB::table('wallets')
        ->where('status', 'locked')
        ->where('locked_until', '<', $expiredTime)
        ->update([
            'status' => 'active',
            'pin_attempts'      => 0,
            'locked_until' => null,
            'lock_reason' => null,
            'updated_at'  => Carbon::now()
        ]);
})->everyMinute();
