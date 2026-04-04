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

        $user = Auth::user();

        $statusParam = $request->query('status', 'all');

        $query = Order::with(['items', 'items.product'])
            ->where('user_id', Auth::id());

        // Áp dụng bộ lọc Tab
        if ($statusParam === 'pending_payment') {
            $query->where('payment_status', 'pending')
                  ->whereIn('payment_method', ['vnpay', 'vnp'])
                  ->where('status', '!=', 'cancelled');
        } elseif ($statusParam === 'processing') {
            $query->whereIn('status', ['pending', 'packing', 'shipping'])
                  ->whereNot(function ($q) {
                      $q->where('payment_status', 'pending')
                        ->whereIn('payment_method', ['vnpay', 'vnp']);
                  });
        } elseif ($statusParam === 'completed') {
            $query->whereIn('status', ['delivered', 'received']);
        } elseif ($statusParam === 'cancelled') {
            $query->where('status', 'cancelled');
        }

        $orders = $query->orderBy('created_at', 'desc')
            ->paginate(10)
            ->appends(['status' => $statusParam]);

        $reviewOrder = null;
        $reviewOrderId = $request->session()->get('review_order_id');

        if (is_numeric($reviewOrderId)) {
            $reviewOrder = Order::with(['items', 'items.product'])
                ->where('user_id', Auth::id())
                ->find((int) $reviewOrderId);
        }

        if ($reviewOrderId !== null) {
            $request->session()->forget('review_order_id');
        }

        return view('client.profiles.orders', compact('orders', 'reviewOrder', 'statusParam'));
    }

    // Hiển thị chi tiết 1 đơn hàng (Trang TechNoir)
    public function show($id)
    {
        $order = Order::with('items')->where('user_id', Auth::id())->findOrFail($id);
        return view('client.orders.show', compact('order'));
    }

    // Khách hàng xác nhận đã nhận hàng
    public function confirmReceived($id)
    {
        $order = Order::where('user_id', Auth::id())->findOrFail($id);

        if ($order->status === Order::STATUS_DELIVERED) {

            $order->status = Order::STATUS_RECEIVED;
            $order->payment_status = 'paid';
            $order->paid_at = $order->paid_at ?? now();
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
                ->with('success', $message)
                ->with('review_order_id', $order->id);
        }

        return redirect()->back()->with('error', 'Trạng thái đơn hàng không hợp lệ.');
    }

    // Khách hàng tự hủy đơn
    public function cancel(Request $request, $id)
    {
        $request->validate([
            'cancellation_reason' => 'required|string|max:255'
        ]);

        $order = Order::with('items')->where('user_id', Auth::id())->findOrFail($id);

        if ($order->status === Order::STATUS_PENDING) {
            
            \Illuminate\Support\Facades\DB::beginTransaction();
            try {
                $order->status = Order::STATUS_CANCELLED;
                $order->cancellation_reason = $request->cancellation_reason;
                $order->cancelled_at = now();
                $order->save();

                // 1. HOÀN VOUCHER (nếu có sử dụng)
                $userVoucher = \Illuminate\Support\Facades\DB::table('user_vouchers')
                    ->where('order_id', $order->id)
                    ->first();
                    
                if ($userVoucher) {
                    \App\Models\Voucher::where('id', $userVoucher->voucher_id)->decrement('used_count');
                    \Illuminate\Support\Facades\DB::table('user_vouchers')->where('order_id', $order->id)->delete();
                }

                // 2. HOÀN SỐ LƯỢNG SẢN PHẨM / BIẾN THỂ VÀO KHO
                foreach ($order->items as $item) {
                    $variant = \App\Models\ProductVariant::where('sku', $item->product_sku)->first();
                    if ($variant) {
                        $variant->increment('stock', $item->quantity);
                    } else {
                        $product = \App\Models\Product::where('sku', $item->product_sku)->first();
                        if ($product) {
                            $product->increment('stock', $item->quantity);
                        }
                    }
                }

                // 3. HOÀN TIỀN VÀO VÍ NẾU THANH TOÁN BẰNG VÍ HOẶC VNPAY
                if (in_array($order->payment_method, ['wallet', 'vnpay', 'vnp']) && $order->payment_status === 'paid') {
                    $wallet = \App\Models\Wallet::where('user_id', $order->user_id)->first();
                    if ($wallet) {
                        $balanceBefore = $wallet->balance;
                        $wallet->increment('balance', $order->total_amount);

                        \App\Models\WalletTransaction::create([
                            'wallet_id' => $wallet->id,
                            'type' => 'refund',
                            'amount' => $order->total_amount,
                            'balance_before' => $balanceBefore,
                            'balance_after' => $wallet->balance,
                            'description' => 'Hoàn tiền ví do hủy đơn: ' . $order->order_code,
                            'reference_type' => 'App\Models\Order',
                            'reference_id' => $order->id,
                            'status' => 'completed',
                        ]);
                    }
                }

                // ==========================================
                // BẮN THÔNG BÁO CHO TẤT CẢ ADMIN
                // ==========================================
                try {
                    $admins = \App\Models\User::whereHas('role', function($q) { $q->where('name', 'admin'); })->get();
                    if ($admins->count() > 0) {
                        $adminTitle = "Khách vừa hủy đơn!";
                        $adminMsg = "Đơn #" . $order->order_code . " vừa bị khách hàng tự hủy. Lý do: " . $request->cancellation_reason;
                        $adminUrl = route('admin.orders.show', $order->id);

                        foreach ($admins as $ad) {
                            $ad->notify(new SystemNotification($adminTitle, $adminMsg, $adminUrl));
                            broadcast(new StatusUpdated($ad->id, $adminTitle, $adminMsg, $adminUrl));
                        }
                    }
                } catch (\Exception $e) {
                    \Log::error('Lỗi báo Admin hủy đơn: ' . $e->getMessage());
                }

                \Illuminate\Support\Facades\DB::commit();
                return redirect()->back()->with('success', 'Đã hủy đơn hàng thành công và hoàn lại tiền, voucher, kho hàng!');
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\DB::rollBack();
                return redirect()->back()->with('error', 'Có lỗi xảy ra khi hủy đơn: ' . $e->getMessage());
            }
        }

        return redirect()->back()->with('error', 'Đơn hàng này đang được xử lý, không thể hủy.');
    }

    // Gửi yêu cầu hoàn hàng (Cho từng Order Item)
    public function requestReturn(Request $request, $itemId)
    {
        $validated = $request->validate([
            'return_note' => ['required', 'string', 'max:1000'],
            'return_image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);

        $orderItem = \App\Models\OrderItem::with('order')->findOrFail($itemId);

        // Đảm bảo đơn hàng này thuộc về user hiện tại
        if (!$orderItem->order || $orderItem->order->user_id !== Auth::id()) {
            abort(403, 'Bạn không có quyền thao tác trên sản phẩm này.');
        }

        if (! $orderItem->canRequestReturn()) {
            throw ValidationException::withMessages([
                'return_note' => 'Sản phẩm này chưa đủ điều kiện hoàn hàng hoặc đã gửi yêu cầu trước đó.',
            ]);
        }

        $returnImageFile = $request->file('return_image');
        $returnImageName = uniqid('return_', true) . '.' . $returnImageFile->getClientOriginalExtension();
        $returnImageFile->move(public_path('uploads/returns'), $returnImageName);
        $returnImagePath = 'uploads/returns/' . $returnImageName;

        $orderItem->update([
            'return_status' => \App\Models\OrderItem::RETURN_REQUESTED,
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
            'order_id' => $orderItem->order_id,
            'user_id' => Auth::id(),
            'status' => '(Hoàn hàng SP) ' . \App\Models\OrderItem::RETURN_REQUESTED,
            'note' => 'Khách hàng gửi yêu cầu hoàn sản phẩm "' . $orderItem->product_name . '": ' . $validated['return_note'],
        ]);

        // ==========================================
        // Bắt thông báo cho Admin
        // ==========================================
        try {
            $admins = \App\Models\User::whereHas('role', function($q) { $q->where('name', 'admin'); })->get();
            if ($admins->count() > 0) {
                $adminTitle = "Khách yêu cầu hoàn sản phẩm!";
                $adminMsg = "Đơn (#" . $orderItem->order->order_code . ") có 1 sản phẩm yêu cầu hoàn hàng: " . htmlspecialchars($orderItem->product_name);
                $adminUrl = route('admin.orders.show', $orderItem->order_id);

                foreach ($admins as $ad) {
                    $ad->notify(new SystemNotification($adminTitle, $adminMsg, $adminUrl));
                    broadcast(new StatusUpdated($ad->id, $adminTitle, $adminMsg, $adminUrl));
                }
            }
        } catch (\Exception $e) {
            \Log::error('Lỗi báo Admin có yêu cầu hoàn: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', 'Đã gửi yêu cầu hoàn hàng cho sản phẩm này. Cửa hàng sẽ phản hồi sớm nhất.');
    }

    // Xác nhận đã gửi hàng hoàn về shop (Cho từng Item)
    public function markReturnShipped($itemId)
    {
        $orderItem = \App\Models\OrderItem::with('order')->findOrFail($itemId);

        if (!$orderItem->order || $orderItem->order->user_id !== Auth::id()) {
            abort(403);
        }

        if (! $orderItem->canCustomerShipReturn()) {
            throw ValidationException::withMessages([
                'order' => 'Sản phẩm này chưa ở bước gửi hàng hoàn về cửa hàng.',
            ]);
        }

        $orderItem->update([
            'return_status' => \App\Models\OrderItem::RETURN_CUSTOMER_SHIPPED,
            'return_shipped_at' => now(),
        ]);

        OrderStatusHistory::create([
            'order_id' => $orderItem->order_id,
            'user_id' => Auth::id(),
            'status' => '(Hoàn hàng SP) ' . \App\Models\OrderItem::RETURN_CUSTOMER_SHIPPED,
            'note' => 'Khách hàng xác nhận đã gửi sản phẩm "' . $orderItem->product_name . '" hoàn về cửa hàng.',
        ]);

        return redirect()->back()->with('success', 'Đã cập nhật trạng thái: Bạn đã gửi hàng hoàn về cửa hàng.');
    }
}
