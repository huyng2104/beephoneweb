<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PointSetting;
use App\Models\User;
use App\Models\Order;
use App\Models\Voucher; // 🚀 GỌI THÊM THẰNG NÀY ĐỂ XỬ LÝ QUÀ TẶNG

class PointController extends Controller
{
    // Hiển thị giao diện Quản lý điểm
    public function index()
    {
        // 1. Lấy cấu hình điểm hiện tại
        $setting = PointSetting::firstOrCreate(
            ['id' => 1],
            ['earn_rate' => 100000, 'redeem_rate' => 1000, 'is_active' => true]
        );

        $users = User::with('pointHistories')->get();

        // 2. Tính toán Data cho 4 thẻ thống kê
        $totalPointsIssued = $users->sum('total_points');

        $activeCustomers = $users->filter(function($user) {
            return $user->total_points > 0;
        })->count();

        $totalPointsRedeemed = 0;
        if (\Schema::hasColumn('orders', 'points_used')) {
            $totalPointsRedeemed = Order::whereNotNull('points_used')->sum('points_used');
        }

        // 3. Bảng xếp hạng Top 10 khách hàng
        $topUsers = $users->filter(function($user) {
                            return $user->total_points > 0;
                        })
                        ->sortByDesc('total_points')
                        ->take(10);

        // 🚀 4. LẤY DANH SÁCH QUÀ TẶNG (VOUCHER ĐỔI ĐIỂM) CÓ PHÂN TRANG
        $vouchers = Voucher::where('points_required', '>', 0)
                           ->orderBy('created_at', 'desc')
                           ->paginate(5); // Hiển thị 5 món quà mỗi trang

        // Đẩy toàn bộ dữ liệu ra View
        return view('admin.points.index', compact(
            'setting',
            'topUsers',
            'totalPointsIssued',
            'activeCustomers',
            'totalPointsRedeemed',
            'vouchers' // 🚀 Truyền biến Vouchers ra đây
        ));
    }

    // Xử lý lưu cấu hình điểm
    public function updateSettings(Request $request)
    {
        $request->validate([
            'earn_rate' => 'required|numeric|min:1000',
            'redeem_rate' => 'required|numeric|min:1',
        ], [
            'earn_rate.min' => 'Giá trị chi tiêu tối thiểu phải từ 1.000đ',
            'redeem_rate.min' => 'Giá trị quy đổi tối thiểu phải từ 1đ',
        ]);

        $setting = PointSetting::first();
        $setting->update([
            'earn_rate' => $request->earn_rate,
            'redeem_rate' => $request->redeem_rate,
        ]);

        return back()->with('success', 'Đã cập nhật cấu hình điểm thưởng thành công!');
    }
}
