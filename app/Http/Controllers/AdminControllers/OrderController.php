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

            // Gửi cho TẤT CẢ tài khoản Admin (kể cả khi Shipper thao tác)
            // LƯU Ý: Nếu role admin của bà tên là chữ khác (vd: 'Admin' in hoa), nhớ sửa chữ 'admin' ở dưới
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

    public function approveReturn(Request $request, Order $order): RedirectResponse
    {
        Gate::authorize('order.update');

        $validated = $request->validate([
            'return_admin_note' => ['nullable', 'string', 'max:1000'],
        ]);

        if (! $order->canApproveReturn()) {
            throw ValidationException::withMessages([
                'return_admin_note' => 'Đơn hàng này chưa ở bước chờ duyệt yêu cầu hoàn hàng.',
            ]);
        }

        $order->update([
            'return_status' => Order::RETURN_APPROVED,
            'return_admin_note' => $validated['return_admin_note'] ?? null,
            'return_approved_at' => now(),
            'return_rejected_at' => null,
        ]);

        OrderStatusHistory::create([
            'order_id' => $order->id,
            'user_id' => Auth::id(),
            'status' => '(Hoàn hàng) ' . Order::RETURN_APPROVED,
            'note' => 'Admin duyệt yêu cầu hoàn hàng. ' . ($validated['return_admin_note'] ?? 'Không có ghi chú'),
        ]);

        try {
            if ($order->user_id) {
                $title = "Xác nhận đổi/trả đơn #" . $order->order_code;
                $message = "Yêu cầu đổi/trả hàng của bạn đã được xác nhận.";
                $url = route('client.orders.show', $order->id);

                $order->user->notify(new SystemNotification($title, $message, $url));
                broadcast(new StatusUpdated($order->user_id, $title, $message, $url));
            }

            $admins = \App\Models\User::whereHas('role', function($q) { $q->where('name', 'admin'); })->get();
            if ($admins->count() > 0) {
                foreach ($admins as $ad) {
                    $ad->notify(new SystemNotification(
                        "Xác nhận đổi/trả đơn #" . $order->order_code,
                        "Bạn vừa xác nhận yêu cầu đổi/trả.",
                        route('admin.orders.show', $order->id)
                    ));
                    broadcast(new StatusUpdated($ad->id, "Xác nhận đổi/trả đơn #" . $order->order_code, "Xác nhận đổi/trả.", route('admin.orders.show', $order->id)));
                }
            }
        } catch (\Exception $e) {
            \Log::error('Lỗi gửi thông báo đổi trả: ' . $e->getMessage());
        }
        // ==========================================

        return back()->with('status', 'Đã xác nhận đổi/trả hàng.');
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
