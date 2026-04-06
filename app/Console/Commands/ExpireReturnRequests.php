<?php

namespace App\Console\Commands;

use App\Models\OrderItem;
use App\Models\OrderStatusHistory;
use Illuminate\Console\Command;

class ExpireReturnRequests extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'returns:expire';

    /**
     * The console command description.
     */
    protected $description = 'Tự động từ chối các yêu cầu hoàn hàng quá 7 ngày chưa được xử lý bởi admin.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Tìm tất cả order items có return_status = 'requested' và đã quá 7 ngày từ khi gửi yêu cầu
        $expiredItems = OrderItem::with('order')
            ->where('return_status', OrderItem::RETURN_REQUESTED)
            ->where('return_requested_at', '<=', now()->subDays(7))
            ->get();

        $count = 0;

        foreach ($expiredItems as $item) {
            // Cập nhật trạng thái về NONE (hoặc REJECTED nếu muốn)
            $item->update([
                'return_status'       => OrderItem::RETURN_NONE,
                'return_note'         => null,
                'return_image'        => null,
                'return_requested_at' => null,
                'return_admin_note'   => 'Yêu cầu hoàn hàng đã tự động hết hạn sau 7 ngày không được xử lý.',
            ]);

            // Ghi lịch sử
            if ($item->order) {
                OrderStatusHistory::create([
                    'order_id' => $item->order_id,
                    'user_id'  => null, // Hệ thống tự xử lý
                    'status'   => 'Hết hạn yêu cầu hoàn',
                    'note'     => 'Yêu cầu hoàn trả sản phẩm "' . $item->product_name . '" đã tự động hết hạn vì quá 7 ngày không được cửa hàng xử lý.',
                ]);
            }

            $count++;
        }

        $this->info("[returns:expire] Đã xử lý {$count} yêu cầu hoàn hàng hết hạn.");

        return Command::SUCCESS;
    }
}
