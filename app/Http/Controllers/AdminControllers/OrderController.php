<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderStatusHistory;
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

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('order.view');

        $status = $request->string('status')->toString();
        $returnStatus = $request->string('return_status')->toString();
        $search = $request->string('q')->toString();

        $orders = Order::query()
            ->when(in_array($status, Order::statuses(), true), fn ($query) => $query->where('status', $status))
            ->when(in_array($returnStatus, Order::returnStatuses(), true), fn ($query) => $query->where('return_status', $returnStatus))
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery
                        ->where('order_code', 'like', '%' . $search . '%')
                        ->orWhere('customer_name', 'like', '%' . $search . '%')
                        ->orWhere('customer_phone', 'like', '%' . $search . '%');
                });
            })
            ->orderByDesc('ordered_at')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.orders.index', [
            'orders' => $orders,
            'statuses' => Order::statuses(),
            'statusLabels' => Order::statusLabels(),
            'returnStatuses' => Order::returnStatuses(),
            'returnStatusLabels' => Order::returnStatusLabels(),
            'activeStatus' => $status,
            'activeReturnStatus' => $returnStatus,
            'search' => $search,
        ]);
    }

    public function show(Order $order): View
    {
        $order->load(['items', 'statusHistories.user']);

        return view('admin.orders.show', [
            'order' => $order,
            'statuses' => Order::statuses(),
            'statusLabels' => Order::statusLabels(),
            'returnStatuses' => Order::returnStatuses(),
            'returnStatusLabels' => Order::returnStatusLabels(),
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

        return back()->with('status', 'Đã cập nhật trạng thái đơn hàng.');
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

    public function approveReturn(Request $request, $itemId): RedirectResponse
    {
        Gate::authorize('order.update');

        $validated = $request->validate([
            'return_admin_note' => ['nullable', 'string', 'max:1000'],
        ]);

        $orderItem = \App\Models\OrderItem::with('order')->findOrFail($itemId);

        if (! $orderItem->canApproveReturn()) {
            throw ValidationException::withMessages([
                'return_admin_note' => 'Sản phẩm này chưa ở bước chờ duyệt yêu cầu hoàn hàng.',
            ]);
        }

        $orderItem->update([
            'return_status' => \App\Models\OrderItem::RETURN_APPROVED,
            'return_admin_note' => $validated['return_admin_note'] ?? null,
            'return_approved_at' => now(),
            'return_rejected_at' => null,
        ]);

        OrderStatusHistory::create([
            'order_id' => $orderItem->order_id,
            'user_id' => Auth::id(),
            'status' => '(Hoàn hàng SP) ' . \App\Models\OrderItem::RETURN_APPROVED,
            'note' => 'Admin duyệt yêu cầu hoàn sản phẩm "' . $orderItem->product_name . '". ' . ($validated['return_admin_note'] ?? 'Không có ghi chú'),
        ]);

        // ==========================================
        try {
            if ($orderItem->order && $orderItem->order->user_id) {
                $title = "Xác nhận đổi/trả sản phẩm #" . $orderItem->order->order_code;
                $message = "Yêu cầu đổi/trả sản phẩm của bạn đã được duyệt.";
                $url = route('client.orders.show', $orderItem->order_id);

                $orderItem->order->user->notify(new SystemNotification($title, $message, $url));
                broadcast(new StatusUpdated($orderItem->order->user_id, $title, $message, $url));
            }

            $admins = \App\Models\User::whereHas('role', function($q) { $q->where('name', 'admin'); })->get();
            if ($admins->count() > 0) {
                foreach ($admins as $ad) {
                    $ad->notify(new SystemNotification(
                        "Đã duyệt hoàn sản phẩm đơn #" . $orderItem->order->order_code,
                        "Đã xác nhận yêu cầu đổi/trả sản phẩm.",
                        route('admin.orders.show', $orderItem->order_id)
                    ));
                    broadcast(new StatusUpdated($ad->id, "Đã duyệt hoàn sản phẩm đơn #" . $orderItem->order->order_code, "Xác nhận đổi/trả.", route('admin.orders.show', $orderItem->order_id)));
                }
            }
        } catch (\Exception $e) {
            \Log::error('Lỗi gửi thông báo đổi trả: ' . $e->getMessage());
        }
        // ==========================================

        return back()->with('status', 'Đã duyệt yêu cầu hoàn hàng cho sản phẩm này. Chờ khách gửi lại.');
    }

    public function rejectReturn(Request $request, $itemId): RedirectResponse
    {
        Gate::authorize('order.update');

        $validated = $request->validate([
            'return_admin_note' => ['required', 'string', 'max:1000'],
        ]);

        $orderItem = \App\Models\OrderItem::with('order')->findOrFail($itemId);

        if (! $orderItem->canRejectReturn()) {
            throw ValidationException::withMessages([
                'return_admin_note' => 'Sản phẩm này chưa ở bước chờ duyệt yêu cầu hoàn hàng.',
            ]);
        }

        $orderItem->update([
            'return_status' => \App\Models\OrderItem::RETURN_REJECTED,
            'return_admin_note' => $validated['return_admin_note'],
            'return_rejected_at' => now(),
        ]);

        OrderStatusHistory::create([
            'order_id' => $orderItem->order_id,
            'user_id' => Auth::id(),
            'status' => '(Hoàn hàng SP) ' . \App\Models\OrderItem::RETURN_REJECTED,
            'note' => 'Admin từ chối yêu cầu hoàn sản phẩm "' . $orderItem->product_name . '": ' . $validated['return_admin_note'],
        ]);

        return back()->with('status', 'Đã từ chối yêu cầu hoàn hàng sản phẩm này.');
    }

    public function markReturnReceived(Request $request, $itemId): RedirectResponse
    {
        Gate::authorize('order.update');

        $validated = $request->validate([
            'return_admin_note' => ['nullable', 'string', 'max:1000'],
        ]);

        $orderItem = \App\Models\OrderItem::with('order')->findOrFail($itemId);

        if (! $orderItem->canMarkReturnReceived()) {
            throw ValidationException::withMessages([
                'return_admin_note' => 'Sản phẩm này chưa ở bước khách gửi hàng hoàn.',
            ]);
        }

        $orderItem->update([
            'return_status' => \App\Models\OrderItem::RETURN_RECEIVED,
            'return_admin_note' => tap($validated['return_admin_note'] ?? $orderItem->return_admin_note, function($val) {}),
            'return_received_at' => now(),
        ]);

        OrderStatusHistory::create([
            'order_id' => $orderItem->order_id,
            'user_id' => Auth::id(),
            'status' => '(Hoàn hàng SP) ' . \App\Models\OrderItem::RETURN_RECEIVED,
            'note' => 'Admin đã nhận/kiểm tra sản phẩm "' . $orderItem->product_name . '" hoàn về. ' . ($validated['return_admin_note'] ?? 'Không có ghi chú'),
        ]);

        return back()->with('status', 'Đã xác nhận nhận hàng hoàn từ khách.');
    }

    public function refundReturn($itemId): RedirectResponse
    {
        Gate::authorize('order.update');

        $orderItem = \App\Models\OrderItem::with('order')->findOrFail($itemId);

        if (! $orderItem->canRefundReturn()) {
            throw ValidationException::withMessages([
                'order' => 'Sản phẩm này chưa đủ điều kiện hoàn tiền vào ví.',
            ]);
        }

        DB::transaction(function () use ($orderItem) {
            $wallet = Wallet::firstOrCreate(
                ['user_id' => $orderItem->order->user_id],
                ['balance' => 0, 'status' => 'active']
            );

            $wallet = Wallet::whereKey($wallet->id)->lockForUpdate()->first();
            $balanceBefore = $wallet->balance;
            
            // Sử dụng hàm tính toán số tiền chuẩn của OrderItem
            $refundAmount = $orderItem->calculateRefundAmount();

            $wallet->balance += $refundAmount;
            $wallet->save();

            WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'type' => 'refund',
                'amount' => $refundAmount,
                'balance_before' => $balanceBefore,
                'balance_after' => $wallet->balance,
                'description' => 'Hoàn tiền sản phẩm "' . $orderItem->product_name . '" (Đơn ' . $orderItem->order->order_code . ') vào ví',
                'reference_type' => Order::class,
                'reference_id' => (string) $orderItem->order_id,
                'status' => 'completed',
            ]);

            $orderItem->update([
                'return_status' => \App\Models\OrderItem::RETURN_REFUNDED,
                'return_refunded_at' => now(),
                'refund_amount' => $refundAmount,
            ]);

            OrderStatusHistory::create([
                'order_id' => $orderItem->order_id,
                'user_id' => Auth::id(),
                'status' => '(Hoàn hàng SP) ' . \App\Models\OrderItem::RETURN_REFUNDED,
                'note' => 'Đã hoàn ' . number_format($refundAmount) . '₫ vào ví Bee Pay của khách hàng cho sản phẩm "' . $orderItem->product_name . '".',
            ]);
        });

        return back()->with('status', 'Đã hoàn tiền vào ví khách hàng cho sản phẩm này.');
    }

    public function printPdf(Order $order)
    {
        $pdf = Pdf::loadView('admin.orders.print', [
            'order' => $order,
            'statusLabels' => Order::statusLabels(),
            'returnStatusLabels' => Order::returnStatusLabels(),
        ]);

        return $pdf->download('don-hang-' . $order->order_code . '.pdf');
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
}
