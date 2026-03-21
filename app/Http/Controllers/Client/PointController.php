<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\PointHistory;
use App\Models\PointSetting;
use App\Models\Voucher;

class PointController extends Controller
{
    // 1. Hiển thị trang Bee Point
    public function index()
    {
        $user = Auth::user();
        
        // Lấy 5 giao dịch gần nhất
        $histories = PointHistory::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $setting = PointSetting::firstOrCreate(
            ['id' => 1],
            ['earn_rate' => 100000, 'redeem_rate' => 1000]
        );

        // Lấy danh sách Voucher đang hoạt động để làm quà tặng
        $vouchers = Voucher::where('status', 1)
            ->where(function($q) {
                $q->whereNull('usage_limit')->orWhereRaw('used_count < usage_limit');
            })
            ->get();

        return view('client.points.index', compact('user', 'histories', 'setting', 'vouchers'));
    }

    // 2. Xử lý đổi điểm lấy Mã giảm giá
   // 2. Xử lý đổi điểm lấy Mã giảm giá
    public function redeem(Request $request)
    {
        $request->validate(['voucher_id' => 'required|exists:vouchers,id']);
        
        $user = Auth::user();
        $voucher = Voucher::findOrFail($request->voucher_id);

        // ==========================================
        // BƯỚC CHẶN 1: KIỂM TRA KHÁCH ĐÃ CÓ VOUCHER NÀY CHƯA
        // ==========================================
        $alreadyHasVoucher = DB::table('user_vouchers')
            ->where('user_id', $user->id)
            ->where('voucher_id', $voucher->id)
            ->exists();

        if ($alreadyHasVoucher) {
            return back()->with('error', 'Bạn đã sở hữu mã giảm giá này rồi! Hãy vào kho Voucher để kiểm tra nhé.');
        }
        
        // ==========================================
        // BƯỚC 2: TÍNH ĐIỂM VÀ KIỂM TRA SỐ DƯ
        // ==========================================
        $setting = PointSetting::first();
        $redeemRate = $setting ? $setting->redeem_rate : 1000;

        $pointCost = 0;
        if ($voucher->discount_type === 'fixed') {
            $pointCost = ceil($voucher->discount_value / $redeemRate);
        } else {
            $pointCost = ceil(($voucher->max_discount ?: 50000) / $redeemRate);
        }

        if ($user->total_points < $pointCost) {
            return back()->with('error', 'Rất tiếc! Bạn không đủ Bee Point để đổi mã này.');
        }

        // ==========================================
        // BƯỚC 3: TRỪ ĐIỂM VÀ LƯU VOUCHER AN TOÀN
        // ==========================================
        DB::beginTransaction();
        try {
            // 1. Trừ điểm: Ghi lịch sử
            PointHistory::create([
                'user_id' => $user->id,
                'order_id' => null, 
                'points' => -$pointCost, // Số âm là trừ điểm
                'type' => 'redeem',
                'description' => 'Đổi ' . number_format($pointCost) . ' điểm lấy mã giảm giá: ' . $voucher->code
            ]);

            // 2. Lưu voucher vào kho của khách
            $voucher->users()->attach($user->id);

            DB::commit();
            return back()->with('success', 'Tuyệt vời! Bạn đã đổi thành công mã ' . $voucher->code . '. Mã đã được lưu vào kho của bạn.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Lỗi hệ thống: ' . $e->getMessage());
        }
    }
}