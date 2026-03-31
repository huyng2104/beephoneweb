<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\PointSetting;
use App\Models\PointHistory;

// Bổ sung 2 cái này để bắn thông báo cho Admin
use App\Notifications\SystemNotification;
use App\Events\StatusUpdated;

class OrderController extends Controller
{
    // Hiển thị danh sách đơn hàng
    public function index(Request $request)
    {
        if ($request->boolean('skip_review')) {
            $request->session()->forget('review_order_id');
            return redirect()->route('client.orders.index');
        }

        $orders = Order::with(['items', 'items.product'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $reviewOrder = null;
        $reviewOrderId = $request->session()->get('review_order_id');
        if (is_numeric($reviewOrderId)) {
            $reviewOrder = Order::with(['items', 'items.product'])
                ->where('user_id', Auth::id())
                ->find((int) $reviewOrderId);
            
            $request->session()->forget('review_order_id');
        }

        return view('client.profiles.orders', compact('orders', 'reviewOrder'));
    }

    // Hiển thị chi tiết 1 đơn hàng (Trang TechNoir)
    public function show($id)
    {
        $order = Order::with('items')->where('user_id', Auth::id())->findOrFail($id);
        return view('client.orders.show', compact('order'));
    }

    // Khách hàng xác nhận đã nhận được hàng
    public function confirmReceived($id)
    {
        $order = Order::where('user_id', Auth::id())->findOrFail($id);

        if ($order->status === Order::STATUS_DELIVERED) {
            
            $order->status = Order::STATUS_RECEIVED; 
            $order->payment_status = 'paid'; 
            $order->save();

            // ==========================================
            // LOGIC TÍCH ĐIỂM THƯỞNG BEE POINT
            // ==========================================
            $pointsEarned = 0;
            $setting = PointSetting::first();
            $earnRate = $setting ? $setting->earn_rate : 100000; 

            if ($earnRate > 0) {
                $pointsEarned = floor($order->total_amount / $earnRate);

                if ($pointsEarned > 0) {
                    PointHistory::create([
                        'user_id' => $order->user_id,
                        'order_id' => $order->id,
                        'points' => $pointsEarned,
                        'type' => 'earn',
                        'description' => 'Tích điểm hoàn thành đơn hàng ' . $order->order_code
                    ]);

                    $customer = \App\Models\User::find($order->user_id);
                    $customer->reward_points += $pointsEarned;
                    $customer->save();
                }
            }

            // ==========================================
            // BẮN THÔNG BÁO CHO TẤT CẢ ADMIN
            // ==========================================
            try {
                $admins = \App\Models\User::whereHas('role', function($q) { $q->where('name', 'admin'); })->get();
                if ($admins->count() > 0) {
                    $adminTitle = "Khách đã nhận hàng!";
                    $adminMsg = "Đơn #" . $order->order_code . " đã được khách hàng xác nhận nhận thành công.";
                    $adminUrl = route('admin.orders.show', $order->id);

                    foreach ($admins as $ad) {
                        $ad->notify(new SystemNotification($adminTitle, $adminMsg, $adminUrl));
                        broadcast(new StatusUpdated($ad->id, $adminTitle, $adminMsg, $adminUrl));
                    }
                }
            } catch (\Exception $e) {
                \Log::error('Lỗi báo Admin nhận hàng: ' . $e->getMessage());
            }

            $msg = 'Cảm ơn bạn đã xác nhận! Đơn hàng đã hoàn thành.';
            if ($pointsEarned > 0) {
                $msg .= ' Bạn được cộng thêm ' . $pointsEarned . ' Bee Point vào tài khoản!';
            }

            return redirect()->back()
                ->with('success', $msg)
                ->with('review_order_id', $order->id);
        }

        return redirect()->back()->with('error', 'Trạng thái đơn hàng không hợp lệ!');
    }

    // Khách hàng tự hủy đơn
    public function cancel($id)
    {
        $order = Order::where('user_id', Auth::id())->findOrFail($id);

        if ($order->status == Order::STATUS_PENDING) {
            
            $order->status = Order::STATUS_CANCELLED;
            $order->cancellation_reason = 'Khách hàng tự hủy đơn trên web';
            $order->cancelled_at = now();
            $order->save();

            // ==========================================
            // BẮN THÔNG BÁO CHO TẤT CẢ ADMIN
            // ==========================================
            try {
                $admins = \App\Models\User::whereHas('role', function($q) { $q->where('name', 'admin'); })->get();
                if ($admins->count() > 0) {
                    $adminTitle = "Khách vừa hủy đơn!";
                    $adminMsg = "Đơn #" . $order->order_code . " vừa bị khách hàng tự hủy trên web.";
                    $adminUrl = route('admin.orders.show', $order->id);

                    foreach ($admins as $ad) {
                        $ad->notify(new SystemNotification($adminTitle, $adminMsg, $adminUrl));
                        broadcast(new StatusUpdated($ad->id, $adminTitle, $adminMsg, $adminUrl));
                    }
                }
            } catch (\Exception $e) {
                \Log::error('Lỗi báo Admin hủy đơn: ' . $e->getMessage());
            }

            return redirect()->back()->with('success', 'Đã hủy đơn hàng thành công!');
        }

        return redirect()->back()->with('error', 'Đơn hàng này đang được xử lý, không thể hủy!');
    }
}