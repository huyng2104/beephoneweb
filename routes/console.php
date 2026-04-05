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

    DB::table('wallet_transactions')
        ->where('type', 'deposit')
        ->where('status', 'pending')
        ->where('created_at', '<', $expiredTime)
        ->update([
            'status'      => 'failed',
            'description' => 'Hết thời gian thanh toán VNPAY',
            'updated_at'  => Carbon::now()
        ]);
})->everyMinute();
