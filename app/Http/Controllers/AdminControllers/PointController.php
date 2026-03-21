<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PointSetting;
use App\Models\User;

class PointController extends Controller
{
    // Hiển thị giao diện Quản lý điểm
    public function index()
    {
        // Lấy cấu hình điểm hiện tại, nếu chưa có thì tạo mặc định
        $setting = PointSetting::firstOrCreate(
            ['id' => 1],
            ['earn_rate' => 100000, 'redeem_rate' => 1000, 'is_active' => true]
        );

        // (Tùy chọn) Lấy top 3 user có điểm cao nhất để hiển thị bảng xếp hạng
        // Lưu ý: Nhớ thêm accessor getTotalPointsAttribute trong model User như tui nói ở bước trước nhé
        $topUsers = User::with('pointHistories')->get()->sortByDesc('total_points')->take(3);

        return view('admin.points.index', compact('setting', 'topUsers'));
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