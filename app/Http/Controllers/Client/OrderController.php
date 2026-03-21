<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\PointSetting;
use App\Models\PointHistory;

class OrderController extends Controller
{
    // Hiển thị danh sách đơn hàng
    public function index()
    {
        // 1. Lấy thông tin user đang đăng nhập
        $user = Auth::user(); 

        // 2. Lấy danh sách đơn hàng
        $orders = Order::with('items')
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // 3. Nhớ truyền thêm biến $user vào compact() nhé
        return view('client.profiles.orders', compact('orders', 'user'));
    }

    // Hiển thị chi tiết 1 đơn hàng (Sẽ làm sau nếu bro cần)
    public function show($id)
    {
        $order = Order::with('items')->where('user_id', Auth::id())->findOrFail($id);
        return view('client.orders.show', compact('order'));
    }

    // Khách hàng xác nhận đã nhận được hàng
    public function confirmReceived($id)
    {
        $order = Order::where('user_id', Auth::id())->findOrFail($id);

        // Thay vì 'shipping', mình dùng STATUS_DELIVERED (Đã giao đến nơi)
        if ($order->status === Order::STATUS_DELIVERED) {
            
            // 1. Chuyển sang trạng thái STATUS_RECEIVED (Khách đã nhận)
            $order->status = Order::STATUS_RECEIVED; 
            $order->save();

            // ==========================================
            // 2. LOGIC TÍCH ĐIỂM THƯỞNG BEE POINT
            // ==========================================
            $pointsEarned = 0;
            // Lấy tỷ lệ tích điểm từ Database
            $setting = PointSetting::first();
            $earnRate = $setting ? $setting->earn_rate : 100000; // Mặc định 100k = 1 điểm nếu chưa cấu hình

            if ($earnRate > 0) {
                // Tính số điểm (Làm tròn xuống. VD: 150k / 100k = 1 điểm)
                $pointsEarned = floor($order->total_amount / $earnRate);

                if ($pointsEarned > 0) {
                    // Lưu vào lịch sử điểm
                    PointHistory::create([
                        'user_id' => $order->user_id,
                        'order_id' => $order->id,
                        'points' => $pointsEarned,
                        'type' => 'earn',
                        'description' => 'Tích điểm hoàn thành đơn hàng ' . $order->order_code
                    ]);
                }
            }
            // ==========================================

            $msg = 'Cảm ơn bạn đã xác nhận! Đơn hàng đã hoàn thành.';
            if ($pointsEarned > 0) {
                $msg .= ' Bạn được cộng thêm ' . $pointsEarned . ' Bee Point vào tài khoản!';
            }

            return redirect()->back()->with('success', $msg);
        }

        return redirect()->back()->with('error', 'Trạng thái đơn hàng không hợp lệ!');
    }

    // Khách hàng tự hủy đơn
    public function cancel($id)
    {
        // 1. Tìm đơn hàng của user này
        $order = Order::where('user_id', Auth::id())->findOrFail($id);

        // 2. Chỉ cho phép hủy khi đơn ở trạng thái PENDING (chờ xác nhận)
        if ($order->status == Order::STATUS_PENDING) {
            
            // Cập nhật trạng thái thành CANCELLED (hủy)
            $order->status = Order::STATUS_CANCELLED;
            // Ghi chú lý do hủy giống với cấu trúc Admin của bro
            $order->cancellation_reason = 'Khách hàng tự hủy đơn trên web';
            $order->cancelled_at = now();
            $order->save();

            return redirect()->back()->with('success', 'Đã hủy đơn hàng thành công!');
        }

        return redirect()->back()->with('error', 'Đơn hàng này đang được xử lý, không thể hủy!');
    }
}