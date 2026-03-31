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

        if (in_array($nextStatus, [Order::STATUS_DELIVERED, Order::STATUS_RECEIVED], true)) {
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

        return back()->with('status', 'Đã duyệt yêu cầu hoàn hàng. Chờ khách gửi hàng lại.');
    }

    public function rejectReturn(Request $request, Order $order): RedirectResponse
    {
        Gate::authorize('order.update');

        $validated = $request->validate([
            'return_admin_note' => ['required', 'string', 'max:1000'],
        ]);

        if (! $order->canRejectReturn()) {
            throw ValidationException::withMessages([
                'return_admin_note' => 'Đơn hàng này chưa ở bước chờ duyệt yêu cầu hoàn hàng.',
            ]);
        }

        $order->update([
            'return_status' => Order::RETURN_REJECTED,
            'return_admin_note' => $validated['return_admin_note'],
            'return_rejected_at' => now(),
        ]);

        OrderStatusHistory::create([
            'order_id' => $order->id,
            'user_id' => Auth::id(),
            'status' => '(Hoàn hàng) ' . Order::RETURN_REJECTED,
            'note' => 'Admin từ chối yêu cầu hoàn hàng: ' . $validated['return_admin_note'],
        ]);

        return back()->with('status', 'Đã từ chối yêu cầu hoàn hàng.');
    }

    public function markReturnReceived(Request $request, Order $order): RedirectResponse
    {
        Gate::authorize('order.update');

        $validated = $request->validate([
            'return_admin_note' => ['nullable', 'string', 'max:1000'],
        ]);

        if (! $order->canMarkReturnReceived()) {
            throw ValidationException::withMessages([
                'return_admin_note' => 'Đơn hàng này chưa ở bước khách gửi hàng hoàn.',
            ]);
        }

        $order->update([
            'return_status' => Order::RETURN_RECEIVED,
            'return_admin_note' => $validated['return_admin_note'] ?? $order->return_admin_note,
            'return_received_at' => now(),
        ]);

        OrderStatusHistory::create([
            'order_id' => $order->id,
            'user_id' => Auth::id(),
            'status' => '(Hoàn hàng) ' . Order::RETURN_RECEIVED,
            'note' => 'Admin đã nhận và kiểm tra hàng hoàn. ' . ($validated['return_admin_note'] ?? 'Không có ghi chú'),
        ]);

        return back()->with('status', 'Đã xác nhận nhận hàng hoàn từ khách.');
    }

    public function refundReturn(Order $order): RedirectResponse
    {
        Gate::authorize('order.update');

        if (! $order->canRefundReturn()) {
            throw ValidationException::withMessages([
                'order' => 'Đơn hàng này chưa đủ điều kiện hoàn tiền vào ví.',
            ]);
        }

        DB::transaction(function () use ($order) {
            $wallet = Wallet::firstOrCreate(
                ['user_id' => $order->user_id],
                ['balance' => 0, 'status' => 'active']
            );

            $wallet = Wallet::whereKey($wallet->id)->lockForUpdate()->first();
            $balanceBefore = $wallet->balance;
            $refundAmount = (int) ($order->total_amount ?? 0);

            $wallet->balance += $refundAmount;
            $wallet->save();

            WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'type' => 'refund',
                'amount' => $refundAmount,
                'balance_before' => $balanceBefore,
                'balance_after' => $wallet->balance,
                'description' => 'Hoàn tiền đơn hàng ' . $order->order_code . ' vào ví Bee Pay',
                'reference_type' => Order::class,
                'reference_id' => (string) $order->id,
                'status' => 'completed',
            ]);

            $order->update([
                'return_status' => Order::RETURN_REFUNDED,
                'return_refunded_at' => now(),
                'refund_amount' => $refundAmount,
            ]);

            OrderStatusHistory::create([
                'order_id' => $order->id,
                'user_id' => Auth::id(),
                'status' => '(Hoàn hàng) ' . Order::RETURN_REFUNDED,
                'note' => 'Đã hoàn ' . number_format($refundAmount) . ' vào ví Bee Pay của khách hàng.',
            ]);
        });

        return back()->with('status', 'Đã hoàn tiền vào ví khách hàng.');
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
