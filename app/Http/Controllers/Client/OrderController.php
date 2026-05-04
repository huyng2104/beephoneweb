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

        $user = Auth::user();

        $statusParam = $request->query('status', 'all');

        $query = Order::with(['items', 'items.product', 'returnRequests'])
            ->where('user_id', Auth::id());

        // Áp dụng bộ lọc Tab
        if ($statusParam === 'pending_payment') {
            $query->where('payment_status', 'pending')
                  ->whereIn('payment_method', ['vnpay', 'vnp'])
                  ->where('status', '!=', 'cancelled');
        } elseif ($statusParam === 'processing') {
            $query->whereIn('status', ['pending', 'ready_to_pick', 'picking', 'money_collect_picking', 'picked'])
                  ->whereNot(function ($q) {
                      $q->where('payment_status', 'pending')
                        ->whereIn('payment_method', ['vnpay', 'vnp']);
                  });
        } elseif ($statusParam === 'delivering') {
            $query->whereIn('status', ['storing', 'transporting', 'sorting', 'delivering', 'money_collect_delivering']);
        } elseif ($statusParam === 'completed') {
            $query->whereIn('status', ['delivered', 'received', 'completed']);
        } elseif ($statusParam === 'cancelled') {
            $query->whereIn('status', ['cancel', 'cancelled']);
        } elseif ($statusParam === 'return') {
            $query->has('returnRequests');
        }

        $orders = $query->orderBy('created_at', 'desc')
            ->paginate(10)
            ->appends(['status' => $statusParam]);

        return view('client.profiles.orders', compact('orders', 'statusParam'));
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

            // Ghi lịch sử hoạt động: Xác nhận nhận hàng
            activity('order')
                ->causedBy(Auth::user())
                ->performedOn($order)
                ->withProperties(['order_code' => $order->order_code])
                ->log('Xác nhận đã nhận đơn hàng #' . $order->order_code);

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
                ->with('success', $message);
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

        if (in_array($order->status, [Order::STATUS_PENDING, Order::STATUS_READY_TO_PICK, Order::STATUS_PICKING, Order::STATUS_MONEY_COLLECT_PICKING])) {
            
            $ghn = null;
            // Kiểm tra trạng thái thực tế bên GHN nếu đã có mã vận đơn
            if ($order->tracking_number) {
                $ghn = app(\App\Services\GhnService::class);
                $ghnOrder = $ghn->getOrderDetail($order->tracking_number);
                
                if ($ghnOrder) {
                    $ghnStatus = strtolower(trim($ghnOrder['status'] ?? ''));
                    // Chỉ cho phép hủy nếu trạng thái GHN là ready_to_pick, picking, money_collect_picking hoặc đã hủy bên GHN
                    if (!in_array($ghnStatus, ['ready_to_pick', 'picking', 'money_collect_picking', 'cancel', 'cancelled'])) {
                        return redirect()->back()->with('error', 'Đơn hàng đang trong quá trình vận chuyển không thể hủy.');
                    }
                } else {
                    // Nếu không lấy được thông tin từ GHN, tạm thời chặn để an toàn
                    return redirect()->back()->with('error', 'Không thể xác thực trạng thái đơn hàng từ GHN. Vui lòng thử lại sau.');
                }
            }

            \Illuminate\Support\Facades\DB::beginTransaction();
            try {
                // NẾU ĐƠN ĐÃ CÓ MÃ VẬN ĐƠN THÌ PHẢI GỌI GHN ĐỂ HỦY
                if ($order->tracking_number && $ghn) {
                    $ghn->cancelOrder($order->tracking_number);
                }
                $order->status = Order::STATUS_CANCELLED;
                $order->cancellation_reason = $request->cancellation_reason;
                $order->cancelled_at = now();
                $order->save();

                // Ghi lịch sử hoạt động: Hủy đơn hàng
                activity('order')
                    ->causedBy(Auth::user())
                    ->performedOn($order)
                    ->withProperties([
                        'order_code'          => $order->order_code,
                        'cancellation_reason' => $request->cancellation_reason,
                    ])
                    ->log('Hủy đơn hàng #' . $order->order_code);

                $isRefunded = false;
                $refundAmount = 0;

                // 1. HOÀN VOUCHER (nếu có sử dụng)
                $userVoucher = \Illuminate\Support\Facades\DB::table('user_vouchers')
                    ->where('order_id', $order->id)
                    ->first();
                    
                if ($userVoucher) {
                    \App\Models\Voucher::where('id', $userVoucher->voucher_id)->decrement('used_count');
                    \Illuminate\Support\Facades\DB::table('user_vouchers')
                        ->where('order_id', $order->id)
                        ->update([
                            'order_id' => null,
                            'used_at' => null
                        ]);
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

                        $isRefunded = true;
                        $refundAmount = $order->total_amount;
                    }
                }

                $historyNote = 'Khách hàng đã tự hủy đơn. Lý do: ' . $request->cancellation_reason;
                if ($isRefunded) {
                    $historyNote .= '. Đã hoàn ' . number_format($refundAmount) . ' ₫ vào ví.';
                }

                \App\Models\OrderStatusHistory::create([
                    'order_id' => $order->id,
                    'user_id' => Auth::id(),
                    'status' => Order::STATUS_CANCELLED,
                    'note' => $historyNote,
                ]);

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

    // Khách hàng yêu cầu giao lại đơn bom hàng
    public function requestRedelivery(Request $request, $id)
    {
        $order = Order::where('user_id', Auth::id())->findOrFail($id);

        if ($order->status === 'delivery_fail') {
            $order->status = Order::STATUS_READY_TO_PICK;
            $order->save();

            OrderStatusHistory::create([
                'order_id' => $order->id,
                'user_id' => Auth::id(),
                'status' => Order::STATUS_READY_TO_PICK,
                'note' => 'Khách hàng yêu cầu giao lại đơn hàng.',
            ]);

            try {
                $admins = \App\Models\User::whereHas('role', function($q) { $q->where('name', 'admin'); })->get();
                if ($admins->count() > 0) {
                    $adminTitle = "Khách yêu cầu giao lại đơn!";
                    $adminMsg = "Đơn #" . $order->order_code . " vừa được khách yêu cầu giao lại.";
                    $adminUrl = route('admin.orders.show', $order->id);

                    foreach ($admins as $ad) {
                        $ad->notify(new SystemNotification($adminTitle, $adminMsg, $adminUrl));
                        broadcast(new StatusUpdated($ad->id, $adminTitle, $adminMsg, $adminUrl));
                    }
                }
            } catch (\Exception $e) {
                \Log::error('Lỗi báo Admin giao lại đơn: ' . $e->getMessage());
            }

            return redirect()->back()->with('success', 'Đã gửi yêu cầu giao lại. Cửa hàng sẽ chuẩn bị đơn hàng cho bạn sớm nhất!');
        }

        return redirect()->back()->with('error', 'Không thể yêu cầu giao lại cho đơn hàng này.');
    }

    /**
     * Tạo yêu cầu hoàn hàng mới (cho 1 hoặc nhiều sản phẩm trong đơn, hỗ trợ chọn số lượng)
     */
    public function storeReturnRequest(Request $request, $id)
    {
        $order = Order::with('items')->where('user_id', Auth::id())->findOrFail($id);

        // Chỉ cho phép hoàn khi đơn đã giao/đã nhận
        if (!in_array($order->status, [Order::STATUS_DELIVERED, Order::STATUS_RECEIVED])) {
            return redirect()->back()->with('error', 'Đơn hàng chưa đủ điều kiện để hoàn trả.');
        }

        $validated = $request->validate([
            'reason'          => ['required', 'string', 'max:1000'],
            'images'          => ['required', 'array', 'min:1', 'max:5'],
            'images.*'        => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'items'           => ['required', 'array', 'min:1'],
            'items.*.id'      => ['required', 'integer'],
            'items.*.qty'     => ['required', 'integer', 'min:1'],
        ]);

        // Validate từng item thuộc đơn hàng này và qty <= qty mua
        $selectedItems = [];
        foreach ($validated['items'] as $row) {
            $orderItem = $order->items->firstWhere('id', $row['id']);
            if (!$orderItem) {
                return redirect()->back()->with('error', 'Sản phẩm không hợp lệ.');
            }
            // Kiểm tra đã có yêu cầu nào đang active chưa
            $hasActive = \App\Models\ReturnRequestItem::whereHas('returnRequest', function ($q) {
                    $q->whereNotIn('status', [\App\Models\ReturnRequest::STATUS_REJECTED]);
                })
                ->where('order_item_id', $orderItem->id)
                ->exists();
            if ($hasActive) {
                return redirect()->back()->with('error', 'Sản phẩm "' . $orderItem->product_name . '" đã có yêu cầu hoàn trả đang xử lý.');
            }
            $qty = min((int) $row['qty'], $orderItem->quantity);
            $selectedItems[] = ['item' => $orderItem, 'qty' => $qty];
        }

        // Lưu nhiều ảnh bằng chứng
        $uploadedImages = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $imgName = uniqid('ret_', true) . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/returns'), $imgName);
                $uploadedImages[] = 'uploads/returns/' . $imgName;
            }
        }

        // Tính discount_amount thực tế (nếu có) để phân bổ
        $subtotal = $order->items->sum('line_total'); // Tổng tiền đơn hàng (tiền hàng)
        $shippingFee = $order->shipping_fee ?? 0;
        $discountAmount = ($subtotal + $shippingFee) - $order->total_amount; // Số tiền được giảm
        if ($discountAmount < 0) $discountAmount = 0;

        // Lấy tổng tiền tạm tính của sản phẩm hoàn
        $returnSubtotal = 0;
        foreach ($selectedItems as $s) {
            $lineUnit = $s['item']->line_total / $s['item']->quantity;
            $returnSubtotal += ($lineUnit * $s['qty']);
        }

        // Tính số tiền hoàn: lấy tổng tiền tạm tính của SP hoàn chia cho tổng tiền đơn hàng nhân với số tiền được giảm
        $allocatedDiscount = 0;
        if ($subtotal > 0) {
            $allocatedDiscount = ($returnSubtotal / $subtotal) * $discountAmount;
        }

        // Tính phí ship trả hàng (từ khách về kho)
        $returnShippingFee = 0;
        if ($order->ghn_district_id && $order->ghn_ward_code) {
            $ghnService = app(\App\Services\GhnService::class);
            $totalWeight = 0;
            foreach ($selectedItems as $s) {
                $totalWeight += $s['qty'] * 200; // Ước tính 200g/sp
            }
            $returnShippingFee = $ghnService->calculateFee(
                (int) $order->ghn_district_id,
                (string) $order->ghn_ward_code,
                3440, // Quận Nam Từ Liêm (kho BeePhone)
                '13010', // Phường Phương Canh
                $totalWeight
            );
        } else {
            $returnShippingFee = 30000; // Mặc định nếu thiếu địa chỉ
        }

        // Phân bổ lại refund_amount cho từng item để lưu vào DB (xử lý làm tròn)
        $totalRefundBeforeShip = $returnSubtotal - $allocatedDiscount;
        $remainingRefund = round($totalRefundBeforeShip);
        
        $totalItems = count($selectedItems);
        $i = 0;
        
        foreach ($selectedItems as &$s) {
            $i++;
            $lineUnit = $s['item']->line_total / $s['item']->quantity;
            $itemTotal = $lineUnit * $s['qty'];
            
            if ($i == $totalItems) {
                // Item cuối cùng nhận phần tiền hoàn còn lại để khớp chính xác tổng
                $s['refund_amount'] = max(0, $remainingRefund);
            } else {
                $itemDiscount = 0;
                if ($returnSubtotal > 0) {
                    $itemDiscount = ($itemTotal / $returnSubtotal) * $allocatedDiscount;
                }
                $itemRefund = round($itemTotal - $itemDiscount);
                $s['refund_amount'] = max(0, $itemRefund);
                $remainingRefund -= $s['refund_amount'];
            }
        }
        unset($s);

        // Tổng tiền hoàn = (Tổng tiền SP hoàn - Giảm giá phân bổ) - Phí ship
        $totalRefund = round($totalRefundBeforeShip) - $returnShippingFee;
        $deductedShippingFee = $returnShippingFee;
        if ($totalRefund < 0) $totalRefund = 0;

        // Tạo ReturnRequest
        $returnCode = 'RET-' . strtoupper(\Illuminate\Support\Str::random(8));
        $returnRequest = \App\Models\ReturnRequest::create([
            'order_id'            => $order->id,
            'user_id'             => Auth::id(),
            'return_code'         => $returnCode,
            'status'              => \App\Models\ReturnRequest::STATUS_PENDING,
            'reason'              => $validated['reason'],
            'image'               => !empty($uploadedImages) ? $uploadedImages[0] : null, // keep first image for compatibility
            'images'              => $uploadedImages,
            'total_refund_amount' => (int) $totalRefund,
            'return_shipping_fee' => (int) $deductedShippingFee,
        ]);

        // Tạo ReturnRequestItems
        foreach ($selectedItems as $s) {
            \App\Models\ReturnRequestItem::create([
                'return_request_id' => $returnRequest->id,
                'order_item_id'     => $s['item']->id,
                'quantity'          => $s['qty'],
                'refund_amount'     => (int) $s['refund_amount'],
            ]);
        }

        OrderStatusHistory::create([
            'order_id' => $order->id,
            'user_id'  => Auth::id(),
            'status'   => 'Hoàn trả',
            'note'     => 'Khách gửi yêu cầu hoàn hàng [' . $returnCode . ']: ' . 'lý do:' . $validated['reason'],
        ]);

        // Ghi lịch sử hoạt động: Yêu cầu hoàn hàng
        activity('order')
            ->causedBy(Auth::user())
            ->performedOn($returnRequest)
            ->withProperties([
                'return_code'   => $returnCode,
                'order_code'    => $order->order_code,
                'reason'        => $validated['reason'],
                'total_refund'  => (int) $totalRefund,
            ])
            ->log('Gửi yêu cầu hoàn hàng [' . $returnCode . '] cho đơn #' . $order->order_code);

        // Ghi lịch sử tạo yêu cầu hoàn trả
        \App\Models\ReturnRequestHistory::create([
            'return_request_id' => $returnRequest->id,
            'user_id'           => Auth::id(),
            'status'            => \App\Models\ReturnRequest::STATUS_PENDING,
            'note'              => 'Khách hàng tạo yêu cầu hoàn trả. Lý do: ' . $validated['reason'],
        ]);

        // Thông báo Admin
        try {
            $admins = \App\Models\User::whereHas('role', fn($q) => $q->where('name', 'admin'))->get();
            foreach ($admins as $ad) {
                $ad->notify(new SystemNotification(
                    "Yêu cầu hoàn hàng mới #{$returnCode}",
                    "Đơn #{$order->order_code} có yêu cầu hoàn trả " . count($selectedItems) . " sản phẩm.",
                    route('admin.orders.show', $order->id)
                ));
                broadcast(new StatusUpdated($ad->id,
                    "Yêu cầu hoàn hàng mới #{$returnCode}",
                    "Đơn #{$order->order_code} cần xử lý.",
                    route('admin.orders.show', $order->id)
                ));
            }
        } catch (\Exception $e) {
            \Log::error('Lỗi thông báo Admin (return): ' . $e->getMessage());
        }

        return redirect()->back()->with('success', "Đã gửi yêu cầu hoàn hàng [{$returnCode}] thành công. Cửa hàng sẽ phản hồi sớm nhất.");
    }

    /**
     * Khách xác nhận đã gửi hàng hoàn về shop (khi Admin yêu cầu tự gửi)
     */
    public function markReturnShipped($requestId)
    {
        $returnRequest = \App\Models\ReturnRequest::with('order')->findOrFail($requestId);

        if (!$returnRequest->order || $returnRequest->order->user_id !== Auth::id()) {
            abort(403);
        }

        if ($returnRequest->status !== \App\Models\ReturnRequest::STATUS_APPROVED) {
            return redirect()->back()->with('error', 'Yêu cầu này chưa được duyệt.');
        }

        $returnRequest->update(['status' => \App\Models\ReturnRequest::STATUS_PICKING]);

        OrderStatusHistory::create([
            'order_id' => $returnRequest->order_id,
            'user_id'  => Auth::id(),
            'status'   => '(Hoàn hàng) picking',
            'note'     => 'Khách xác nhận đã gửi hàng hoàn [' . $returnRequest->return_code . '] về cửa hàng.',
        ]);

        // Ghi lịch sử hoạt động: Xác nhận gửi hàng hoàn
        activity('order')
            ->causedBy(Auth::user())
            ->performedOn($returnRequest)
            ->withProperties(['return_code' => $returnRequest->return_code])
            ->log('Xác nhận đã gửi hàng hoàn [' . $returnRequest->return_code . '] về cửa hàng.');

        return redirect()->back()->with('success', 'Đã cập nhật: Bạn đã gửi hàng hoàn về cửa hàng.');
    }

    /**
     * Khách hủy yêu cầu hoàn hàng (chỉ khi còn ở Pending)
     */
    public function cancelReturnRequest($requestId)
    {
        $returnRequest = \App\Models\ReturnRequest::with('order')->findOrFail($requestId);

        if (!$returnRequest->order || $returnRequest->order->user_id !== Auth::id()) {
            abort(403);
        }

        if ($returnRequest->status !== \App\Models\ReturnRequest::STATUS_PENDING) {
            return redirect()->back()->with('error', 'Không thể hủy yêu cầu này vì nó đã được xử lý.');
        }

        $returnRequest->delete();

        OrderStatusHistory::create([
            'order_id' => $returnRequest->order_id,
            'user_id'  => Auth::id(),
            'status'   => 'Hoàn trả',
            'note'     => 'Khách hủy yêu cầu hoàn hàng [' . $returnRequest->return_code . '].',
        ]);

        // Ghi lịch sử hoạt động: Hủy yêu cầu hoàn hàng
        activity('order')
            ->causedBy(Auth::user())
            ->withProperties(['return_code' => $returnRequest->return_code])
            ->log('Hủy yêu cầu hoàn hàng [' . $returnRequest->return_code . '].');

        return redirect()->back()->with('success', 'Đã hủy yêu cầu hoàn hàng thành công.');
    }

    // Chức năng Mua lại (Repurchase)
    public function repurchase(Request $request, $id)
    {
        $order = Order::with('items')->where('user_id', Auth::id())->findOrFail($id);
        
        $sessionToken = \Illuminate\Support\Facades\Session::getId();
        $cart = \App\Models\Cart::firstOrCreate(
            Auth::check() ? ['user_id' => Auth::id()] : ['session_id' => $sessionToken]
        );

        $addedCount = 0;
        $addedItemIds = [];

        foreach ($order->items as $item) {
            $product = \App\Models\Product::where('sku', $item->product_sku)->first();
            $variant = \App\Models\ProductVariant::where('sku', $item->product_sku)->first();

            $productId = $product ? $product->id : ($variant ? $variant->product_id : null);
            $variantId = $variant ? $variant->id : null;

            if ($productId) {
                // Kiểm tra stock thực tế hiện tại
                $stock = $variant ? $variant->stock : ($product ? $product->stock : 0);
                if ($stock <= 0) continue; // Hết hàng thì bỏ qua sản phẩm này

                $cartItem = \App\Models\CartItem::where('cart_id', $cart->id)
                    ->where('product_id', $productId)
                    ->when($variantId, function ($q) use ($variantId) {
                        return $q->where('product_variant_id', $variantId);
                    }, function ($q) {
                        return $q->whereNull('product_variant_id');
                    })
                    ->first();

                if ($cartItem) {
                    $newQty = $cartItem->quantity + $item->quantity;
                    if ($newQty <= $stock) {
                        $cartItem->increment('quantity', $item->quantity);
                    } else {
                        // Nếu vượt quá tồn kho thì set bằng tồn kho tối đa
                        $cartItem->update(['quantity' => $stock]);
                    }
                    $addedCount++;
                    $addedItemIds[] = $cartItem->id;
                } else {
                    $qtyToAdd = min($item->quantity, $stock);
                    $newCartItem = \App\Models\CartItem::create([
                        'cart_id' => $cart->id,
                        'product_id' => $productId,
                        'product_variant_id' => $variantId,
                        'quantity' => $qtyToAdd,
                    ]);
                    $addedCount++;
                    $addedItemIds[] = $newCartItem->id;
                }
            }
        }

        if ($addedCount > 0) {
            session(['selected_cart_items' => $addedItemIds]);
            return redirect()->route('client.checkout.index')->with('success', 'Đã chuẩn bị đơn hàng! Bạn có thể thanh toán ngay.');
        } else {
            return redirect()->route('client.orders.index')->with('error', 'Sản phẩm này hiện đã hết hàng hoặc không tồn tại.');
        }
    }
}
