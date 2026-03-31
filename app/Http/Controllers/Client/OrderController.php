<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Models\PointHistory;
use App\Models\PointSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

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
                        'description' => 'Tích điểm hoàn thành đơn hàng ' . $order->order_code,
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

            $message = 'Cảm ơn bạn đã xác nhận. Đơn hàng đã hoàn thành.';
            if ($pointsEarned > 0) {
                $message .= ' Bạn được cộng thêm ' . $pointsEarned . ' Bee Point vào tài khoản.';
            }

            return redirect()->back()
                ->with('success', $msg)
                ->with('review_order_id', $order->id);
        }

        return redirect()->back()->with('error', 'Trạng thái đơn hàng không hợp lệ.');
    }

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

        return redirect()->back()->with('error', 'Đơn hàng này đang được xử lý, không thể hủy.');
    }

    public function requestReturn(Request $request, $id)
    {
        $validated = $request->validate([
            'return_note' => ['required', 'string', 'max:1000'],
            'return_image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);

        $order = Order::where('user_id', Auth::id())->findOrFail($id);

        if (! $order->canRequestReturn()) {
            throw ValidationException::withMessages([
                'return_note' => 'Đơn hàng này chưa đủ điều kiện hoàn hàng hoặc đã gửi yêu cầu trước đó.',
            ]);
        }

        $returnImageFile = $request->file('return_image');
        $returnImageName = uniqid('return_', true) . '.' . $returnImageFile->getClientOriginalExtension();
        $returnImageFile->move(public_path('uploads/returns'), $returnImageName);
        $returnImagePath = 'uploads/returns/' . $returnImageName;

        $order->update([
            'return_status' => Order::RETURN_REQUESTED,
            'return_note' => $validated['return_note'],
            'return_image' => $returnImagePath,
            'return_requested_at' => now(),
            'return_admin_note' => null,
            'return_approved_at' => null,
            'return_rejected_at' => null,
            'return_shipped_at' => null,
            'return_received_at' => null,
            'return_refunded_at' => null,
            'refund_amount' => null,
        ]);

        OrderStatusHistory::create([
            'order_id' => $order->id,
            'user_id' => Auth::id(),
            'status' => '(Hoàn hàng) ' . Order::RETURN_REQUESTED,
            'note' => 'Khách hàng gửi yêu cầu hoàn hàng: ' . $validated['return_note'],
        ]);

        return redirect()->back()->with('success', 'Đã gửi yêu cầu hoàn hàng. Cửa hàng sẽ phản hồi sớm nhất.');
    }

    public function markReturnShipped($id)
    {
        $order = Order::where('user_id', Auth::id())->findOrFail($id);

        if (! $order->canCustomerShipReturn()) {
            throw ValidationException::withMessages([
                'order' => 'Đơn hàng này chưa ở bước gửi hàng hoàn về cửa hàng.',
            ]);
        }

        $order->update([
            'return_status' => Order::RETURN_CUSTOMER_SHIPPED,
            'return_shipped_at' => now(),
        ]);

        OrderStatusHistory::create([
            'order_id' => $order->id,
            'user_id' => Auth::id(),
            'status' => '(Hoàn hàng) ' . Order::RETURN_CUSTOMER_SHIPPED,
            'note' => 'Khách hàng xác nhận đã gửi hàng hoàn về cửa hàng.',
        ]);

        return redirect()->back()->with('success', 'Đã cập nhật trạng thái: bạn đã gửi hàng hoàn về cửa hàng.');
    }
}
