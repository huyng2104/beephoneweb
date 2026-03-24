<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Order;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CustomerActivityController extends Controller
{
    /**
     * Thống kê thói quen và thời gian hoạt động của khách hàng
     */
    public function index(Request $request)
    {
        $customerRole = Role::where('name', 'user')->first();
        $adminStaffRoleIds = Role::whereIn('name', ['admin', 'staff'])->pluck('id')->toArray();
        $customerIds = $customerRole
            ? User::where('role_id', $customerRole->id)->pluck('id')->toArray()
            : User::whereNotIn('role_id', $adminStaffRoleIds)->pluck('id')->toArray();

        $days = (int) ($request->get('days', 30));
        $startDate = Carbon::now()->subDays($days);

        // 1. Thống kê giờ đăng nhập cao điểm (từ ActivityLog - action = login)
        $loginByHour = ActivityLog::query()
            ->where('action', 'login')
            ->where('user_id', '!=', null)
            ->when(count($customerIds) > 0, fn($q) => $q->whereIn('user_id', $customerIds))
            ->where('created_at', '>=', $startDate)
            ->selectRaw('HOUR(created_at) as hour, COUNT(*) as count')
            ->groupBy('hour')
            ->orderBy('hour')
            ->pluck('count', 'hour')
            ->toArray();

        // 2. Thống kê đăng nhập theo ngày trong tuần
        $loginByDayOfWeek = ActivityLog::query()
            ->where('action', 'login')
            ->where('user_id', '!=', null)
            ->when(count($customerIds) > 0, fn($q) => $q->whereIn('user_id', $customerIds))
            ->where('created_at', '>=', $startDate)
            ->selectRaw('DAYOFWEEK(created_at) as day_of_week, COUNT(*) as count')
            ->groupBy('day_of_week')
            ->pluck('count', 'day_of_week')
            ->toArray();

        // 3. Thống kê giờ đặt hàng cao điểm (từ Order)
        $orderByHour = Order::query()
            ->where('user_id', '!=', null)
            ->when(count($customerIds) > 0, fn($q) => $q->whereIn('user_id', $customerIds))
            ->where(function ($q) use ($startDate) {
                $q->where('ordered_at', '>=', $startDate)
                    ->orWhere(function ($q2) use ($startDate) {
                        $q2->whereNull('ordered_at')->where('created_at', '>=', $startDate);
                    });
            })
            ->selectRaw('HOUR(COALESCE(ordered_at, created_at)) as hour, COUNT(*) as count')
            ->groupBy('hour')
            ->orderBy('hour')
            ->pluck('count', 'hour')
            ->toArray();

        // 4. Thống kê đặt hàng theo ngày trong tuần
        $orderByDayOfWeek = Order::query()
            ->where('user_id', '!=', null)
            ->when(count($customerIds) > 0, fn($q) => $q->whereIn('user_id', $customerIds))
            ->where(function ($q) use ($startDate) {
                $q->where('ordered_at', '>=', $startDate)
                    ->orWhere(function ($q2) use ($startDate) {
                        $q2->whereNull('ordered_at')->where('created_at', '>=', $startDate);
                    });
            })
            ->selectRaw('DAYOFWEEK(COALESCE(ordered_at, created_at)) as day_of_week, COUNT(*) as count')
            ->groupBy('day_of_week')
            ->pluck('count', 'day_of_week')
            ->toArray();

        // 5. Top khách hàng hoạt động nhiều nhất (số lần đăng nhập)
        $topActiveByLogin = ActivityLog::query()
            ->where('action', 'login')
            ->where('user_id', '!=', null)
            ->when(count($customerIds) > 0, fn($q) => $q->whereIn('user_id', $customerIds))
            ->where('created_at', '>=', $startDate)
            ->select('user_id', DB::raw('COUNT(*) as login_count'))
            ->groupBy('user_id')
            ->orderByDesc('login_count')
            ->limit(10)
            ->with('user:id,name,email')
            ->get();

        $topActiveByLogin->load('user:id,name,email');

        // 6. Top khách hàng mua nhiều nhất
        $topActiveByOrder = Order::query()
            ->where('user_id', '!=', null)
            ->when(count($customerIds) > 0, fn($q) => $q->whereIn('user_id', $customerIds))
            ->whereNotIn('status', [Order::STATUS_CANCELLED])
            ->where(function ($q) use ($startDate) {
                $q->where('ordered_at', '>=', $startDate)
                    ->orWhere(function ($q2) use ($startDate) {
                        $q2->whereNull('ordered_at')->where('created_at', '>=', $startDate);
                    });
            })
            ->select('user_id', DB::raw('COUNT(*) as order_count'), DB::raw('SUM(total_amount) as total_spent'))
            ->groupBy('user_id')
            ->orderByDesc('order_count')
            ->limit(10)
            ->get();

        foreach ($topActiveByOrder as $o) {
            $o->user = User::find($o->user_id);
        }

        // 7. Thời gian hoạt động trung bình (giờ đăng nhập nhiều nhất)
        $peakLoginHour = collect($loginByHour)->sortDesc()->keys()->first();
        $peakOrderHour = collect($orderByHour)->sortDesc()->keys()->first();

        // 8. Sessions - last_activity (nếu dùng session database)
        $activeNowCount = 0;
        if (config('session.driver') === 'database') {
            $activeThreshold = time() - (config('session.lifetime', 120) * 60);
            $activeNowCount = DB::table('sessions')
                ->where('user_id', '!=', null)
                ->when(count($customerIds) > 0, fn($q) => $q->whereIn('user_id', $customerIds))
                ->where('last_activity', '>=', $activeThreshold)
                ->count();
        }

        $dayLabels = [
            1 => 'Chủ nhật',
            2 => 'Thứ 2',
            3 => 'Thứ 3',
            4 => 'Thứ 4',
            5 => 'Thứ 5',
            6 => 'Thứ 6',
            7 => 'Thứ 7',
        ];

        return view('admin.customer-activity.index', compact(
            'loginByHour',
            'loginByDayOfWeek',
            'orderByHour',
            'orderByDayOfWeek',
            'topActiveByLogin',
            'topActiveByOrder',
            'peakLoginHour',
            'peakOrderHour',
            'activeNowCount',
            'dayLabels',
            'days'
        ));
    }
}
