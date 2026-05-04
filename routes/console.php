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
    DB::table('wallets')
        ->where('status', 'locked')
        ->where('locked_until', '<=', Carbon::now())
        ->update([
            'status'       => 'active',
            'pin_attempts' => 0,
            'locked_until' => null,
            'lock_reason'  => null,
            'updated_at'   => Carbon::now()
        ]);
})->everyMinute();

// Tự động hủy yêu cầu hoàn hàng quá 7 ngày chưa được duyệt
Schedule::command('returns:expire')->daily()->at('02:00');

// ==========================================
// ĐỒNG BỘ TRẠNG THÁI ĐƠN HÀNG & ĐƠN HOÀN TỪ GHN API
// Thay thế Webhook: poll API GHN mỗi 1 giây
// ==========================================
Schedule::command('ghn:sync')->everyMinute()->withoutOverlapping();
Schedule::command('ghn:sync-returns')->everyMinute()->withoutOverlapping();
