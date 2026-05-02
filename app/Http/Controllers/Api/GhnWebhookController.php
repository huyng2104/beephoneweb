<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use Illuminate\Support\Facades\Log;

class GhnWebhookController extends Controller
{
    /**
     * Xử lý webhook từ Giao Hàng Nhanh gửi về
     * Phương thức: POST
     */
    public function handle(Request $request)
    {
        // Ghi log lại dữ liệu GHN gửi về để debug
        Log::channel('daily')->info('GHN Webhook Data:', $request->all());

        // GHN gửi mã vận đơn ở trường OrderCode
        $ghnOrderCode = $request->input('OrderCode');
        $ghnStatus = $request->input('Status');
        $ghnDescription = $request->input('Description') ?? $request->input('Reason') ?? null;
        
        if (!$ghnOrderCode || !$ghnStatus) {
            return response()->json(['message' => 'Missing OrderCode or Status'], 400);
        }

        // Tìm đơn hàng nội bộ có mã vận đơn tương ứng
        $order = Order::where('tracking_number', $ghnOrderCode)->first();
        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $newLocalStatus = null;
        
        // Hệ thống mới đã hỗ trợ trực tiếp các trạng thái GHN
        $newLocalStatus = $ghnStatus === 'cancel' ? Order::STATUS_CANCELLED : $ghnStatus;

        if ($newLocalStatus && $order->status !== $newLocalStatus) {
            $order->update(['status' => $newLocalStatus]);
            
            // Ghi lại lịch sử cập nhật trạng thái
            OrderStatusHistory::create([
                'order_id'        => $order->id,
                'user_id'         => null,
                'status'          => $newLocalStatus,
                'note'            => null,
                'ghn_description' => $ghnDescription,
                'ghn_status_raw'  => $ghnStatus,
            ]);
            
            // Nếu đơn hàng giao thành công và là COD, tự động cập nhật thanh toán
            if ($newLocalStatus === Order::STATUS_DELIVERED && $order->payment_method === 'cod') {
                $order->update(['payment_status' => 'paid', 'paid_at' => now()]);
            }
            
            // Nếu đơn hàng bị hủy, xử lý ngày hủy
            if ($newLocalStatus === Order::STATUS_CANCELLED) {
                $order->update(['cancelled_at' => now(), 'cancellation_reason' => 'Đã hủy trên hệ thống GHN']);
            }
        }

        // Luôn trả về 200 OK để GHN biết đã nhận được, nếu không GHN sẽ gửi lại nhiều lần
        return response()->json(['message' => 'Webhook received successfully'], 200);
    }
}
