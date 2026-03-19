<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;

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
            
            // Chuyển sang trạng thái STATUS_RECEIVED (Khách đã nhận)
            $order->status = Order::STATUS_RECEIVED; 
            $order->save();

            return redirect()->back()->with('success', 'Cảm ơn bạn đã xác nhận! Đơn hàng đã hoàn thành.');
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

            // BONUS: Nếu muốn xịn, bro có thể viết thêm vòng lặp cộng lại số lượng (stock) cho bảng Product ở đây nha

            return redirect()->back()->with('success', 'Đã hủy đơn hàng thành công!');
        }

        return redirect()->back()->with('error', 'Đơn hàng này đang được xử lý, không thể hủy!');
    }
}