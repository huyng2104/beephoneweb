<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Models\ReturnRequestHistory;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

use App\Notifications\SystemNotification;
use App\Events\StatusUpdated;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('order.view');

        $status        = $request->string('status')->toString();
        $returnStatus  = $request->string('return_status')->toString();
        $search        = $request->string('q')->toString();
        $paymentStatus = $request->string('payment_status')->toString();
        $dateFrom      = $request->string('date_from')->toString();
        $dateTo        = $request->string('date_to')->toString();
        $sort          = $request->string('sort', 'newest')->toString();

        $orders = Order::with(['items', 'returnRequests.items'])
            ->when(in_array($status, Order::statuses(), true), fn ($q) => $q->where('status', $status))
            ->when($returnStatus === 'has_return', fn ($q) => $q->has('returnRequests'))
            ->when($returnStatus === 'no_return',  fn ($q) => $q->doesntHave('returnRequests'))
            ->when($paymentStatus !== '', fn ($q) => $q->where('payment_status', $paymentStatus))
            ->when($dateFrom !== '', fn ($q) => $q->whereDate('created_at', '>=', $dateFrom))
            ->when($dateTo !== '', fn ($q) => $q->whereDate('created_at', '<=', $dateTo))
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($sub) use ($search) {
                    $sub->where('order_code', 'like', '%' . $search . '%')
                        ->orWhere('customer_name', 'like', '%' . $search . '%')
                        ->orWhere('customer_phone', 'like', '%' . $search . '%');
                });
            })
            ->when($sort === 'newest',     fn ($q) => $q->orderByDesc('created_at'))
            ->when($sort === 'oldest',     fn ($q) => $q->orderBy('created_at'))
            ->when($sort === 'total_high', fn ($q) => $q->orderByDesc('total_amount'))
            ->when($sort === 'total_low',  fn ($q) => $q->orderBy('total_amount'))
            ->when(!in_array($sort, ['newest','oldest','total_high','total_low']), fn ($q) => $q->orderByDesc('created_at'))
            ->paginate(15)
            ->withQueryString();

        // Thống kê nhanh (toàn bộ, không bị ảnh hưởng bởi filter)
        $stats = [
            'total_revenue' => Order::whereIn('status', [Order::STATUS_DELIVERED, Order::STATUS_RECEIVED])
                                    ->where('payment_status', 'paid')
                                    ->sum('total_amount'),
            'new_orders'    => Order::where('status', Order::STATUS_PENDING)->count(),
            'cancelled'     => Order::where('status', Order::STATUS_CANCELLED)->count(),
            'delivering'      => Order::where('status', 'delivering')->count(),
        ];

        return view('admin.orders.index', [
            'orders'              => $orders,
            'statuses'            => Order::statuses(),
            'statusLabels'        => Order::statusLabels(),
            'returnStatuses'      => array_keys(\App\Models\ReturnRequest::statusLabels()),
            'returnStatusLabels'  => \App\Models\ReturnRequest::statusLabels(),
            'activeStatus'        => $status,
            'activeReturnStatus'  => $returnStatus,
            'search'              => $search,
            'stats'               => $stats,
        ]);
    }


    public function show(Order $order): View
    {
        Gate::authorize('order.view');

        $order->load(['items', 'statusHistories.user', 'returnRequests.items.orderItem', 'returnRequests.histories.user']);

        return view('admin.orders.show', [
            'order' => $order,
            'statuses' => Order::statuses(),
            'statusLabels' => Order::statusLabels(),
            'returnStatusLabels' => \App\Models\ReturnRequest::statusLabels(),
            'paymentMethodLabels' => Order::paymentMethodLabels(),
            'paymentStatusLabels' => Order::paymentStatusLabels(),
            'availableStatuses' => $this->availableStatusesFor($order),
        ]);
    }

    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        Gate::authorize('order.update');

        $validated = $request->validate([
            'status' => ['required', 'string', 'in:' . implode(',', Order::statuses())],
        ]);

        $nextStatus = $validated['status'];

        if ($nextStatus === Order::STATUS_CANCELLED) {
            throw ValidationException::withMessages([
                'status' => 'Vui lòng dùng chức năng hủy đơn để nhập lý do hủy.',
            ]);
        }

        if ($nextStatus === Order::STATUS_RECEIVED) {
            throw ValidationException::withMessages([
                'status' => 'Trạng thái này chỉ khách hàng mới được xác nhận.',
            ]);
        }

        if (! $order->canMoveTo($nextStatus)) {
            throw ValidationException::withMessages([
                'status' => 'Không thể chuyển trạng thái theo luồng hiện tại.',
            ]);
        }

        $updateData = ['status' => $nextStatus];
        if (in_array($nextStatus, [Order::STATUS_DELIVERED, Order::STATUS_RECEIVED])) {
            $updateData['payment_status'] = 'paid';
            $updateData['paid_at'] = $order->paid_at ?? now();
        }
        $order->update($updateData);

        OrderStatusHistory::create([
            'order_id' => $order->id,
            'user_id' => Auth::id(),
            'status' => $nextStatus,
            'note' => 'Cập nhật trạng thái bởi quản trị viên',
        ]);

        // ==========================================
        // TẠO VẬN ĐƠN GHN KHI DUYỆT ĐƠN (pending → packing)
        // ==========================================
        // TẠO VẬN ĐƠN GHN KHI DUYỆT ĐƠN (pending → ready_to_pick)
        // ==========================================
        $ghnWarning = null;
        if ($nextStatus === Order::STATUS_READY_TO_PICK && !$order->tracking_number) {
            try {
                $order->load('items'); // Đảm bảo items đã được load
                /** @var \App\Services\GhnService $ghn */
                $ghn = app(\App\Services\GhnService::class);
                $ghnOrderCode = $ghn->createOrder($order);

                if ($ghnOrderCode) {
                    $order->update(['tracking_number' => $ghnOrderCode]);
                    OrderStatusHistory::create([
                        'order_id' => $order->id,
                        'user_id'  => Auth::id(),
                        'status'   => Order::STATUS_READY_TO_PICK,
                        'note'     => 'Đã tạo vận đơn GHN thành công. Mã vận đơn: ' . $ghnOrderCode,
                    ]);
                } else {
                    $ghnWarning = 'Đã duyệt đơn nhưng tạo vận đơn GHN thất bại. Vui lòng tạo thủ công và nhập mã vận đơn.';
                    Log::warning('[Admin] Duyệt đơn thành công nhưng GHN createOrder trả về null', [
                        'order_code' => $order->order_code,
                    ]);
                }
            } catch (\Throwable $e) {
                $ghnWarning = 'Đã duyệt đơn nhưng gặp lỗi khi tạo vận đơn GHN: ' . $e->getMessage();
                Log::error('[Admin] GHN createOrder Exception sau duyệt đơn', [
                    'order_code' => $order->order_code,
                    'error'      => $e->getMessage(),
                ]);
            }
        }
        // ==========================================

        // ==========================================
        // LƯU VÀ BẮN THÔNG BÁO CHO KHÁCH & TẤT CẢ ADMIN
        // ==========================================
        try {
            $statusLabels = Order::statusLabels();
            $statusName = $statusLabels[$nextStatus] ?? $nextStatus;

            // Khách hàng
            if ($order->user_id) {
                $titleClient = "Cập nhật đơn hàng #" . $order->order_code;
                $messageClient = "Đơn hàng của bạn đã chuyển sang trạng thái: " . $statusName;
                $urlClient = route('client.orders.show', $order->id);

                $order->user->notify(new SystemNotification($titleClient, $messageClient, $urlClient));
                broadcast(new StatusUpdated($order->user_id, $titleClient, $messageClient, $urlClient));
            }

            // Gửi cho TẤT CẢ tài khoản Admin
            $admins = \App\Models\User::whereHas('role', function($q) {
                $q->where('name', 'admin');
            })->get();

            if ($admins->count() > 0) {
                $adminTitle = "Đơn #" . $order->order_code . " vừa được cập nhật";
                $adminMsg = "Trạng thái mới: " . $statusName . " (Bởi: " . Auth::user()->name . ")";
                $adminUrl = route('admin.orders.show', $order->id);

                foreach ($admins as $ad) {
                    $ad->notify(new SystemNotification($adminTitle, $adminMsg, $adminUrl));
                    broadcast(new StatusUpdated($ad->id, $adminTitle, $adminMsg, $adminUrl));
                }
            }
        } catch (\Exception $e) {
            \Log::error('Lỗi gửi thông báo: ' . $e->getMessage());
        }
        // ==========================================

        $redirect = back()->with('status', 'Đã cập nhật trạng thái đơn hàng.');
        if ($ghnWarning) {
            $redirect = $redirect->with('warning', $ghnWarning);
        }
        return $redirect;
    }

    public function cancel(Request $request, Order $order): RedirectResponse
    {
        Gate::authorize('order.cancel');

        $validated = $request->validate([
            'cancellation_reason' => ['required', 'string', 'max:1000'],
        ]);

        if (! $order->canMoveTo(Order::STATUS_CANCELLED)) {
            throw ValidationException::withMessages([
                'cancellation_reason' => 'Đơn hàng này không thể hủy ở trạng thái hiện tại.',
            ]);
        }

        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            // NẾU ĐƠN ĐÃ CÓ MÃ VẬN ĐƠN THÌ PHẢI GỌI GHN ĐỂ HỦY
            if ($order->tracking_number) {
                $ghn = app(\App\Services\GhnService::class);
                $ghn->cancelOrder($order->tracking_number);
            }

            $order->update([
                'status' => Order::STATUS_CANCELLED,
                'cancellation_reason' => $validated['cancellation_reason'],
                'cancelled_at' => now(),
            ]);

            OrderStatusHistory::create([
                'order_id' => $order->id,
                'user_id' => Auth::id(),
                'status' => Order::STATUS_CANCELLED,
                'note' => 'Lý do hủy: ' . $validated['cancellation_reason'],
            ]);

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
                        'description' => 'Hoàn tiền ví do Admin hủy đơn: ' . $order->order_code,
                        'reference_type' => 'App\Models\Order',
                        'reference_id' => $order->id,
                        'status' => 'completed',
                    ]);
                }
            }

            \Illuminate\Support\Facades\DB::commit();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra khi hủy đơn: ' . $e->getMessage());
        }

        // ==========================================
        try {
            if ($order->user_id) {
                $title = "Đơn hàng #" . $order->order_code . " đã bị hủy";
                $message = "Lý do: " . $validated['cancellation_reason'];
                $url = route('client.orders.show', $order->id);

                $order->user->notify(new SystemNotification($title, $message, $url));
                broadcast(new StatusUpdated($order->user_id, $title, $message, $url));
            }

            $admins = \App\Models\User::whereHas('role', function($q) { $q->where('name', 'admin'); })->get();
            if ($admins->count() > 0) {
                foreach ($admins as $ad) {
                    $ad->notify(new SystemNotification(
                        "Đã hủy đơn #" . $order->order_code,
                        "Lý do: " . $validated['cancellation_reason'],
                        route('admin.orders.show', $order->id)
                    ));
                    broadcast(new StatusUpdated($ad->id, "Đã hủy đơn #" . $order->order_code, "Lý do: " . $validated['cancellation_reason'], route('admin.orders.show', $order->id)));
                }
            }
        } catch (\Exception $e) {
            \Log::error('Lỗi gửi thông báo hủy đơn: ' . $e->getMessage());
        }
        // ==========================================

        return back()->with('status', 'Đã hủy đơn hàng.');
    }

    public function refundFailedDelivery(Request $request, Order $order): RedirectResponse
    {
        Gate::authorize('order.update');

        if ($order->status !== Order::STATUS_DELIVERY_FAIL) {
            return back()->withErrors(['state' => 'Chỉ có thể hoàn tiền cho đơn hàng Giao thất bại (Bom hàng).']);
        }

        if ($order->payment_status !== 'paid') {
            return back()->withErrors(['state' => 'Đơn hàng này chưa được thanh toán nên không thể hoàn tiền.']);
        }

        DB::transaction(function () use ($order) {
            // 1. Hoàn mức tồn kho
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

            // 2. Chuyển tiền vào ví
            if (in_array($order->payment_method, ['wallet', 'vnpay', 'vnp'])) {
                $wallet = Wallet::firstOrCreate(
                    ['user_id' => $order->user_id],
                    ['balance' => 0, 'status' => 'active']
                );

                $wallet = Wallet::whereKey($wallet->id)->lockForUpdate()->first();
                $balanceBefore = $wallet->balance;

                $wallet->balance += $order->total_amount;
                $wallet->save();

                WalletTransaction::create([
                    'wallet_id' => $wallet->id,
                    'type' => 'refund',
                    'amount' => $order->total_amount,
                    'balance_before' => $balanceBefore,
                    'balance_after' => $wallet->balance,
                    'description' => 'Hoàn tiền vào ví do giao thất bại (Bom hàng) đơn ' . $order->order_code,
                    'reference_type' => Order::class,
                    'reference_id' => (string) $order->id,
                    'status' => 'completed',
                ]);
            }

            // 3. Đổi trạng thái thanh toán
            $order->update([
                'payment_status' => 'refunded',
            ]);

            OrderStatusHistory::create([
                'order_id' => $order->id,
                'user_id' => Auth::id(),
                'status' => Order::STATUS_DELIVERY_FAIL,
                'note' => 'Admin xác nhận nhập lại kho và hoàn tiền ' . number_format($order->total_amount) . '₫ vào ví cho đơn giao thất bại.',
            ]);
        });

        // ==========================================
        try {
            if ($order->user_id) {
                $title = "Hoàn tiền đơn hàng #" . $order->order_code;
                $message = "Đơn hàng giao thất bại đã được xử lý hoàn tiền toàn bộ vào Ví Bee Pay của bạn.";
                $url = route('client.orders.show', $order->id);

                $order->user->notify(new SystemNotification($title, $message, $url));
                broadcast(new StatusUpdated($order->user_id, $title, $message, $url));
            }
        } catch (\Exception $e) {
            \Log::error('Lỗi gửi thông báo hoàn tiền bom hàng: ' . $e->getMessage());
        }
        // ==========================================

        return back()->with('status', 'Đã lưu kho thẻ và hoàn tiền thành công vào ví khách hàng.');
    }

    public function approveReturn(Request $request, $requestId): RedirectResponse
    {
        Gate::authorize('order.update');

        $validated = $request->validate([
            'return_admin_note' => ['nullable', 'string', 'max:1000'],
        ]);

        $returnRequest = \App\Models\ReturnRequest::with(['order', 'items.orderItem'])->findOrFail($requestId);

        if (! $returnRequest->canApprove()) {
            throw ValidationException::withMessages([
                'return_admin_note' => 'Yêu cầu này chưa ở bước chờ duyệt.',
            ]);
        }

        // Tạo vận đơn thu hồi qua GHN
        $ghn = app(\App\Services\GhnService::class);
        $trackingNumber = $ghn->createReturnOrder($returnRequest);
        $returnStatus = \App\Models\ReturnRequest::STATUS_APPROVED;
        
        $msgAdmin = 'Admin duyệt yêu cầu hoàn trả' ;
        
        if ($trackingNumber) {
            
            // Cập nhật trạng thái thành Đang thu hồi (Khách đã gửi hàng/Bưu tá đang lấy)
            $returnStatus = \App\Models\ReturnRequest::STATUS_PICKING; 
        } else {
            $msgAdmin .= '. Cảnh báo: Tạo vận đơn GHN thu hồi thất bại.';
        }

        $returnRequest->update([
            'status' => $returnStatus,
            'tracking_number' => $trackingNumber,
            'admin_note' => $validated['return_admin_note'] ?? null,
            'approved_at' => now(),
            'rejected_at' => null,
        ]);

        ReturnRequestHistory::create([
            'return_request_id' => $returnRequest->id,
            'user_id' => Auth::id(),
            'status' => $returnStatus,
            'note' => $msgAdmin,
        ]);

        // ==========================================
        try {
            if ($returnRequest->order && $returnRequest->order->user_id) {
                $title = "Xác nhận đổi/trả yêu cầu #" . $returnRequest->return_code;
                $message = "Yêu cầu đổi/trả sản phẩm của bạn đã được duyệt.";
                $url = route('client.orders.show', $returnRequest->order_id);

                $returnRequest->order->user->notify(new SystemNotification($title, $message, $url));
                broadcast(new StatusUpdated($returnRequest->order->user_id, $title, $message, $url));
            }
        } catch (\Exception $e) {
            \Log::error('Lỗi gửi thông báo đổi trả: ' . $e->getMessage());
        }
        // ==========================================

        $flashMsg = 'Đã duyệt yêu cầu hoàn hàng.';
        if (isset($trackingNumber) && $trackingNumber) {

        } else {
            $flashMsg .= ' Cảnh báo: Không thể tạo vận đơn thu hồi tự động. Khách hàng sẽ phải tự gửi lại.';
        }
        return back()->with('status', $flashMsg);
    }

    public function rejectReturn(Request $request, $requestId): RedirectResponse
    {
        Gate::authorize('order.update');

        $validated = $request->validate([
            'return_admin_note' => ['required', 'string', 'max:1000'],
        ]);

        $returnRequest = \App\Models\ReturnRequest::with('order')->findOrFail($requestId);

        if (! $returnRequest->canReject()) {
            throw ValidationException::withMessages([
                'return_admin_note' => 'Yêu cầu này chưa ở bước chờ duyệt.',
            ]);
        }

        $returnRequest->update([
            'status' => \App\Models\ReturnRequest::STATUS_REJECTED,
            'admin_note' => $validated['return_admin_note'],
            'rejected_at' => now(),
        ]);

        ReturnRequestHistory::create([
            'return_request_id' => $returnRequest->id,
            'user_id' => Auth::id(),
            'status' => \App\Models\ReturnRequest::STATUS_REJECTED,
            'note' => 'Admin từ chối yêu cầu "' . $returnRequest->return_code . '": ' . $validated['return_admin_note'],
        ]);

        return back()->with('status', 'Đã từ chối yêu cầu hoàn hàng này.');
    }

    public function markReturnReceived(Request $request, $requestId): RedirectResponse
    {
        Gate::authorize('order.update');

        $validated = $request->validate([
            'return_admin_note' => ['nullable', 'string', 'max:1000'],
        ]);

        $returnRequest = \App\Models\ReturnRequest::with('order')->findOrFail($requestId);

        if (! $returnRequest->canMarkReceived()) {
            throw ValidationException::withMessages([
                'return_admin_note' => 'Yêu cầu này chưa ở bước khách gửi hàng hoàn.',
            ]);
        }

        $returnRequest->update([
            'status' => \App\Models\ReturnRequest::STATUS_RECEIVED,
            'admin_note' => tap($validated['return_admin_note'] ?? $returnRequest->admin_note, function($val) {}),
            'received_at' => now(),
        ]);

        ReturnRequestHistory::create([
            'return_request_id' => $returnRequest->id,
            'user_id' => Auth::id(),
            'status' => \App\Models\ReturnRequest::STATUS_RECEIVED,
            'note' => 'Admin đã nhận/kiểm tra hàng hoàn về của yêu cầu "' . $returnRequest->return_code . '". ' . ($validated['return_admin_note'] ?? 'Không có ghi chú'),
        ]);

        return back()->with('status', 'Đã xác nhận nhận hàng hoàn từ khách.');
    }

    public function refundReturn($requestId): RedirectResponse
    {
        Gate::authorize('order.update');

        $returnRequest = \App\Models\ReturnRequest::with(['order', 'items.orderItem'])->findOrFail($requestId);

        if (! $returnRequest->canRefund()) {
            throw ValidationException::withMessages([
                'order' => 'Yêu cầu này chưa đủ điều kiện hoàn tiền vào ví.',
            ]);
        }

        if (! $returnRequest->isGhnDelivered()) {
            throw ValidationException::withMessages([
                'order' => 'Vui lòng đợi đơn vị vận chuyển giao hàng hoàn về kho (Giao thành công) trước khi thực hiện hoàn tiền.',
            ]);
        }

        DB::transaction(function () use ($returnRequest) {
            $wallet = Wallet::firstOrCreate(
                ['user_id' => $returnRequest->order->user_id],
                ['balance' => 0, 'status' => 'active']
            );

            $wallet = Wallet::whereKey($wallet->id)->lockForUpdate()->first();
            $balanceBefore = $wallet->balance;
            
            $refundAmount = $returnRequest->total_refund_amount;

            $wallet->balance += $refundAmount;
            $wallet->save();

            WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'type' => 'refund',
                'amount' => $refundAmount,
                'balance_before' => $balanceBefore,
                'balance_after' => $wallet->balance,
                'description' => 'Hoàn tiền yêu cầu "' . $returnRequest->return_code . '" (Đơn ' . $returnRequest->order->order_code . ') vào ví',
                'reference_type' => Order::class,
                'reference_id' => (string) $returnRequest->order_id,
                'status' => 'completed',
            ]);

            $returnRequest->update([
                'status' => \App\Models\ReturnRequest::STATUS_COMPLETED,
                'completed_at' => now(),
            ]);

            ReturnRequestHistory::create([
                'return_request_id' => $returnRequest->id,
                'user_id' => Auth::id(),
                'status' => \App\Models\ReturnRequest::STATUS_COMPLETED,
                'note' => 'Đã hoàn ' . number_format($refundAmount) . '₫ vào ví Bee Pay của khách hàng.',
            ]);
        });

        return back()->with('status', 'Đã hoàn tiền vào ví khách hàng.');
    }

    public function printPdf(Order $order)
    {
        Gate::authorize('order.view');

        $pdf = Pdf::loadView('admin.orders.print', [
            'order' => $order,
            'statusLabels' => Order::statusLabels(),
            'returnStatusLabels' => \App\Models\ReturnRequest::statusLabels(),
        ]);

        return $pdf->download('don-hang-' . $order->order_code . '.pdf');
    }

    public function printGhn(Order $order)
    {
        Gate::authorize('order.view');

        if (!$order->tracking_number) {
            return back()->withErrors(['tracking' => 'Đơn hàng này chưa có mã vận đơn GHN.']);
        }

        try {
            /** @var \App\Services\GhnService $ghn */
            $ghn = app(\App\Services\GhnService::class);
            $token = $ghn->generatePrintToken([$order->tracking_number]);

            if ($token) {
                $printUrl = $ghn->getPrintUrl($token, 'A5');
                return redirect()->away($printUrl);
            }

            return back()->withErrors(['tracking' => 'Không thể tạo token in đơn từ GHN.']);
        } catch (\Throwable $e) {
            Log::error('[Admin printGhn] Exception: ' . $e->getMessage(), [
                'order_code'      => $order->order_code,
                'tracking_number' => $order->tracking_number,
            ]);
            return back()->withErrors(['tracking' => 'Lỗi khi kết nối GHN: ' . $e->getMessage()]);
        }
    }

    private function availableStatusesFor(Order $order): array
    {
        $statuses = [$order->status];
        foreach (Order::statuses() as $status) {
            if (
                $order->canMoveTo($status)
                && ! in_array($status, $statuses, true)
                && $status !== Order::STATUS_CANCELLED
                && $status !== Order::STATUS_RECEIVED
            ) {
                $statuses[] = $status;
            }
        }
        return $statuses;
    }

    /**
     * Lưu mã vận đơn GHN cho đơn hàng.
     * Admin nhập mã này để hệ thống polling API GHN tự cập nhật trạng thái.
     */
    public function updateTracking(Request $request, Order $order): RedirectResponse
    {
        Gate::authorize('order.update');

        $validated = $request->validate([
            'tracking_number' => ['nullable', 'string', 'max:100'],
        ]);

        $order->update([
            'tracking_number' => $validated['tracking_number'] ? trim($validated['tracking_number']) : null,
        ]);

        return back()->with('status', $validated['tracking_number']
            ? 'Đã lưu mã vận đơn GHN: ' . $validated['tracking_number'] . '. Hệ thống sẽ tự đồng bộ trạng thái mỗi 5 phút.'
            : 'Đã xóa mã vận đơn GHN.'
        );
    }

    /**
     * Đồng bộ trạng thái ngay lập tức từ GHN API cho 1 đơn hàng.
     * Gọi trực tiếp GhnService thay vì đi qua Artisan để có feedback rõ hơn.
     */
    public function syncNow(Order $order): RedirectResponse
    {
        Gate::authorize('order.update');

        if (!$order->tracking_number) {
            return back()->withErrors(['tracking' => 'Đơn hàng này chưa có mã vận đơn GHN.']);
        }

        try {
            /** @var \App\Services\GhnService $ghn */
            $ghn = app(\App\Services\GhnService::class);

            // ── Lấy detail để kiểm tra status GHN ──
            $detail = $ghn->getOrderDetail($order->tracking_number);

            if (!$detail) {
                Log::warning('[Admin syncNow] GHN API không trả về dữ liệu', [
                    'order_code'      => $order->order_code,
                    'tracking_number' => $order->tracking_number,
                ]);
                return back()->withErrors(['tracking' =>
                    'GHN API không trả về dữ liệu cho mã vận đơn: ' . $order->tracking_number .
                    '. Kiểm tra mã vận đơn hoặc xem log để biết chi tiết.'
                ]);
            }

            $ghnStatus = $detail['status'] ?? null;

            if (!$ghnStatus) {
                return back()->withErrors(['tracking' =>
                    'GHN trả về dữ liệu nhưng không có trường status. Xem log để biết chi tiết.'
                ]);
            }

            $newLocalStatus = $ghn->mapStatus($ghnStatus);
            if (!$newLocalStatus) {
                return back()->with('status',
                    "Đã kết nối GHN. Trạng thái GHN: \"{$ghnStatus}\" — Chưa có mapping, vui lòng liên hệ kỹ thuật."
                );
            }

            // ── Gọi SyncGhnOrders để đồng bộ toàn bộ tracking log ──
            // Dùng lại đúng logic: lấy /tracking → fallback log[] → lưu từng bước với timestamp thực
            /** @var \App\Console\Commands\SyncGhnOrders $syncer */
            $syncer = app(\App\Console\Commands\SyncGhnOrders::class);
            $syncer->syncSingleOrder($order);

            // Reload lại order để lấy status mới nhất
            $order->refresh();

            $statusLabels   = Order::statusLabels();
            $newStatusLabel = $statusLabels[$order->status] ?? $order->status;

            Log::info('[Admin syncNow] Đồng bộ thành công', [
                'order_code'      => $order->order_code,
                'tracking_number' => $order->tracking_number,
                'ghn_status'      => $ghnStatus,
                'local_status'    => $order->status,
            ]);

            return back()->with('status',
                "Đồng bộ thành công! Trạng thái hiện tại: \"{$newStatusLabel}\""
            );

        } catch (\Throwable $e) {
            Log::error('[Admin syncNow] Exception: ' . $e->getMessage(), [
                'order_code'      => $order->order_code,
                'tracking_number' => $order->tracking_number,
            ]);
            return back()->withErrors(['tracking' => 'Lỗi khi đồng bộ GHN: ' . $e->getMessage()]);
        }
    }

    /**
     * Đồng bộ trạng thái đơn hoàn trả từ GHN API.
     */
    public function syncReturnNow($requestId): RedirectResponse
    {
        Gate::authorize('order.update');

        $rr = \App\Models\ReturnRequest::findOrFail($requestId);

        if (!$rr->tracking_number) {
            return back()->withErrors(['tracking' => 'Đơn hoàn này chưa có mã vận đơn GHN thu hồi.']);
        }

        try {
            /** @var \App\Console\Commands\SyncReturnOrders $syncer */
            $syncer = app(\App\Console\Commands\SyncReturnOrders::class);
            $changed = $syncer->syncSingleReturn($rr);

            $rr->refresh();
            $statusLabels = \App\Models\ReturnRequest::statusLabels();
            $newStatusLabel = $statusLabels[$rr->status] ?? $rr->status;

            if ($changed) {
                return back()->with('status', "Đồng bộ thành công! Trạng thái đơn hoàn hiện tại: \"{$newStatusLabel}\"");
            }

            return back()->with('status', "Đã kiểm tra GHN. Trạng thái đơn hoàn không đổi: \"{$newStatusLabel}\"");

        } catch (\Throwable $e) {
            Log::error('[Admin syncReturnNow] Exception: ' . $e->getMessage(), [
                'return_code'     => $rr->return_code,
                'tracking_number' => $rr->tracking_number,
            ]);
            return back()->withErrors(['tracking' => 'Lỗi khi đồng bộ GHN đơn hoàn: ' . $e->getMessage()]);
        }
    }
}

