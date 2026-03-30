<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Illuminate\Support\Facades\Gate;
class OrderController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('order.view');
        $status = $request->string('status')->toString();
        $returnStatus = $request->string('return_status')->toString();
        $search = $request->string('q')->toString();

        $orders = Order::query()
            ->when(in_array($status, Order::statuses(), true), fn($query) => $query->where('status', $status))
            ->when(in_array($returnStatus, Order::returnStatuses(), true), fn($query) => $query->where('return_status', $returnStatus))
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
        $order->load('items');

        $statusLabels = Order::statusLabels();
        $returnStatusLabels = Order::returnStatusLabels();

        return view('admin.orders.show', [
            'order' => $order,
            'statuses' => Order::statuses(),
            'statusLabels' => $statusLabels,
            'returnStatuses' => Order::returnStatuses(),
            'returnStatusLabels' => $returnStatusLabels,
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

        // THÊM ĐOẠN NÀY VÀO: Báo lỗi nếu Admin cố tình truyền trạng thái Đã nhận
        if ($nextStatus === Order::STATUS_RECEIVED) {
            throw ValidationException::withMessages([
                'status' => 'Trạng thái này là đặc quyền của Khách hàng. Admin chỉ được cập nhật đến Đã Giao Hàng!',
            ]);
        }

        if (!$order->canMoveTo($nextStatus)) {
            throw ValidationException::withMessages([
                'status' => 'Không thể chuyển trạng thái theo luồng hiện tại.',
            ]);
        }

        $order->update(['status' => $nextStatus]);

        return back()->with('status', 'Đã cập nhật trạng thái đơn hàng.');
    }

    public function cancel(Request $request, Order $order): RedirectResponse
    {
        Gate::authorize('order.cancel');
        $validated = $request->validate([
            'cancellation_reason' => ['required', 'string', 'max:1000'],
        ]);

        if (!$order->canMoveTo(Order::STATUS_CANCELLED)) {
            throw ValidationException::withMessages([
                'cancellation_reason' => 'Đơn hàng này không thể hủy ở trạng thái hiện tại.',
            ]);
        }

        $order->update([
            'status' => Order::STATUS_CANCELLED,
            'cancellation_reason' => $validated['cancellation_reason'],
            'cancelled_at' => now(),
        ]);

        return back()->with('status', 'Đã hủy đơn hàng.');
    }

    public function confirmReturn(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'return_note' => ['nullable', 'string', 'max:1000'],
        ]);

        if (!in_array($order->status, [Order::STATUS_DELIVERED, Order::STATUS_RECEIVED], true)) {
            throw ValidationException::withMessages([
                'return_note' => 'Chỉ xác nhận đổi/trả cho đơn đã giao thành công hoặc đã nhận hàng.',
            ]);
        }

        $order->update([
            'return_status' => Order::RETURN_CONFIRMED,
            'return_note' => $validated['return_note'] ?? null,
            'return_requested_at' => $order->return_requested_at ?? now(),
            'return_confirmed_at' => now(),
        ]);

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
            // Lọc ra các trạng thái hợp lệ, NHƯNG cấm Admin chọn Hủy và Đã Nhận Hàng
            if (
                $order->canMoveTo($status) &&
                !in_array($status, $statuses, true) &&
                $status !== Order::STATUS_CANCELLED &&
                $status !== Order::STATUS_RECEIVED // THÊM DÒNG NÀY ĐỂ CHẶN ADMIN
            ) {
                $statuses[] = $status;
            }
        }

        return $statuses;
    }
}
