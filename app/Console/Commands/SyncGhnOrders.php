<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Services\GhnService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SyncGhnOrders extends Command
{
    /**
     * Chạy thủ công 1 đơn:  php artisan ghn:sync --code=GHN123456
     * Chạy hàng loạt:        php artisan ghn:sync
     * Đồng bộ tất cả đơn:   php artisan ghn:sync --all
     */
    protected $signature = 'ghn:sync
                            {--code= : Chỉ đồng bộ 1 đơn hàng theo mã vận đơn GHN}
                            {--all   : Đồng bộ TẤT CẢ đơn có tracking (kể cả đã giao/hủy)}';

    protected $description = 'Lấy toàn bộ lịch sử vận chuyển từ GHN API và đồng bộ vào hệ thống BeePhone.';

    public function __construct(protected GhnService $ghn)
    {
        parent::__construct();
    }

    // ══════════════════════════════════════════════════════════════════════════
    //  HANDLE
    // ══════════════════════════════════════════════════════════════════════════

    public function handle(): int
    {
        // ── Đồng bộ 1 đơn cụ thể theo mã vận đơn GHN ──
        if ($code = $this->option('code')) {
            $this->info("[ghn:sync] Đang đồng bộ đơn: {$code}");

            $order = Order::where('tracking_number', $code)->first();
            if (!$order) {
                $this->warn("[ghn:sync] Không tìm thấy đơn với tracking_number = {$code}");
                return Command::FAILURE;
            }

            $this->syncSingleOrder($order);
            $this->info("[ghn:sync] Hoàn tất đồng bộ đơn {$code}");
            return Command::SUCCESS;
        }

        // ── Đồng bộ hàng loạt ──
        $this->info('[ghn:sync] Bắt đầu đồng bộ từ GHN...');

        $query = Order::whereNotNull('tracking_number');

        if ($this->option('all')) {
            // --all: kể cả delivered, cancelled (để backfill history)
            $this->info('[ghn:sync] Chế độ: TẤT CẢ đơn có tracking');
        } else {
            // Mặc định: chỉ đơn đang trên đường (active GHN statuses)
            $query->whereNotIn('status', [
                Order::STATUS_DELIVERED,
                Order::STATUS_RECEIVED,
                Order::STATUS_CANCELLED,
                Order::STATUS_CANCEL,
                Order::STATUS_RETURNED,
                Order::STATUS_RETURN_FAIL,
            ]);
        }

        $orders = $query->get();

        if ($orders->isEmpty()) {
            $this->info('[ghn:sync] Không có đơn hàng nào cần đồng bộ.');
            return Command::SUCCESS;
        }

        $this->info("[ghn:sync] Tìm thấy {$orders->count()} đơn hàng...");
        $updated = 0;
        $failed  = 0;

        foreach ($orders as $order) {
            $changed = $this->syncSingleOrder($order);
            if ($changed === null) {
                $failed++;
            } elseif ($changed) {
                $updated++;
                $this->line("  ✓ #{$order->order_code} → cập nhật trạng thái");
            } else {
                $this->line("    #{$order->order_code} → không đổi ({$order->status})");
            }
            usleep(200_000); // 0.2s tránh rate-limit
        }

        $this->info("[ghn:sync] Hoàn tất: Cập nhật {$updated}, thất bại {$failed}.");
        Log::info("[ghn:sync] Done: updated={$updated}, failed={$failed}");

        return Command::SUCCESS;
    }

    // ══════════════════════════════════════════════════════════════════════════
    //  SYNC 1 ĐƠN
    // ══════════════════════════════════════════════════════════════════════════

    /**
     * Đồng bộ toàn bộ lịch sử GHN cho 1 đơn hàng.
     *
     * @return bool|null  true = status đổi, false = không đổi, null = lỗi
     */
    public function syncSingleOrder(Order $order): ?bool
    {
        $code = $order->tracking_number;

        // ── Bước 1: Lấy detail (có status hiện tại + log[]) ──
        $detail = $this->ghn->getOrderDetail($code);
        if (!$detail) {
            $this->warn("[ghn:sync] ⚠ Không lấy được detail GHN: #{$order->order_code} ({$code})");
            return null;
        }

        $ghnStatus = $detail['status'] ?? null;
        if (!$ghnStatus) {
            return null;
        }

        // ── Bước 2: Lấy tracking log đầy đủ ──
        // Ưu tiên endpoint /tracking (chi tiết hơn), fallback sang log[] trong detail
        $trackingEvents = $this->ghn->getOrderTracking($code);

        if (!empty($trackingEvents)) {
            // /tracking trả về mảng các event
            $this->syncTrackingEvents($order, $trackingEvents);
        } elseif (!empty($detail['log'])) {
            // Fallback: parse log[] từ detail
            $this->syncFromDetailLog($order, $detail['log']);
        }

        // ── Bước 3: Cập nhật trạng thái đơn nếu thay đổi ──
        return $this->applyStatusChange($order, $ghnStatus);
    }

    // ══════════════════════════════════════════════════════════════════════════
    //  ĐỒNG BỘ TỪ /tracking API
    // ══════════════════════════════════════════════════════════════════════════

    /**
     * Xử lý mảng events từ endpoint /shipping-order/tracking.
     *
     * GHN trả về mảng: [
     *   {
     *     "status": "ready_to_pick",
     *     "description": "P. Trịnh Văn Bô...",
     *     "updated_date": 1714377600    ← Unix timestamp (s hoặc ms)
     *   }, ...
     * ]
     * Thứ tự: mới nhất ở ĐẦU mảng (cần reverse để insert đúng).
     */
    protected function syncTrackingEvents(Order $order, array $events): void
    {
        // Đảo ngược để xử lý từ cũ → mới
        $events = array_reverse($events);

        foreach ($events as $event) {
            if (!is_array($event)) continue;

            $ghnStatusRaw = $event['status'] ?? $event['Status'] ?? null;
            if (!$ghnStatusRaw) continue;

            // Map sang local status — nếu không map được, giữ nguyên status hiện tại của đơn
            $localStatus = $this->ghn->mapStatus($ghnStatusRaw) ?? $order->status;

            // Description y chang từ GHN
            $description = $event['description'] ?? $event['Description'] ?? $event['note'] ?? null;

            // Timestamp thực tế
            $occurredAt = $this->parseGhnTimestamp(
                $event['updated_date'] ?? $event['date'] ?? $event['Date'] ?? null
            );

            $this->upsertHistoryRecord($order, $ghnStatusRaw, $localStatus, $description, $occurredAt);
        }
    }

    // ══════════════════════════════════════════════════════════════════════════
    //  ĐỒNG BỘ TỪ log[] TRONG /detail (FALLBACK)
    // ══════════════════════════════════════════════════════════════════════════

    /**
     * Xử lý mảng log[] từ /shipping-order/detail (fallback nếu /tracking lỗi).
     */
    protected function syncFromDetailLog(Order $order, array $logs): void
    {
        // log[] trong detail: mới nhất ở ĐẦU → reverse
        $logs = array_reverse($logs);

        foreach ($logs as $logEntry) {
            if (!is_array($logEntry)) continue;

            $ghnStatusRaw = $logEntry['status'] ?? $logEntry['Status'] ?? null;
            if (!$ghnStatusRaw) continue;

            // Map sang local status — nếu không map được, giữ nguyên status hiện tại của đơn
            $localStatus = $this->ghn->mapStatus($ghnStatusRaw) ?? $order->status;

            $description = $logEntry['description'] ?? $logEntry['Description'] ?? null;

            $occurredAt = $this->parseGhnTimestamp(
                $logEntry['updated_date'] ?? $logEntry['date'] ?? $logEntry['Date'] ?? null
            );

            $this->upsertHistoryRecord($order, $ghnStatusRaw, $localStatus, $description, $occurredAt);
        }
    }

    // ══════════════════════════════════════════════════════════════════════════
    //  MAP TIÊU ĐỀ & MÔ TẢ THÂN THIỆN THEO MÃ GHN STATUS
    // ══════════════════════════════════════════════════════════════════════════

    /**
     * Tiêu đề hiển thị (cột xanh) cho từng mã GHN status.
     */
    protected array $ghnTitles = [
        'ready_to_pick'            => 'Chờ lấy hàng',
        'picking'                  => 'Đang lấy hàng',
        'cancel'                   => 'Đã hủy đơn',
        'money_collect_picking'    => 'Đang lấy hàng',
        'picked'                   => 'Đã lấy hàng',
        'storing'                  => 'Nhập kho trung chuyển',
        'transporting'             => 'Đang luân chuyển',
        'sorting'                  => 'Đang phân loại',
        'delivering'               => 'Đang giao hàng',
        'money_collect_delivering' => 'Đang giao hàng',
        'delivered'                => 'Giao thành công',
        'delivery_fail'            => 'Giao thất bại',
        'waiting_to_return'        => 'Đang chờ giao lại',
        'return'                   => 'Chờ hoàn hàng',
        'return_transporting'      => 'Đang luân chuyển hoàn',
        'return_sorting'           => 'Đang phân loại hoàn',
        'returning'                => 'Đang hoàn hàng',
        'return_fail'              => 'Hoàn hàng thất bại',
        'returned'                 => 'Đã hoàn hàng',
        'exception'                => 'Ngoại lệ',
        'damage'                   => 'Hàng bị hỏng',
        'lost'                     => 'Hàng bị mất',
    ];

    /**
     * Template mô tả chi tiết (cột giữa). {address} sẽ được thay bằng địa chỉ từ GHN.
     */
    protected array $ghnDescTemplates = [
        'ready_to_pick'            => 'Đơn hàng vừa được tạo thành công.',
        'picking'                  => 'Shipper đang trên đường đến lấy hàng.',
        'cancel'                   => 'Đơn hàng đã bị hủy.',
        'money_collect_picking'    => 'Shipper đang tương tác với người bán.',
        'picked'                   => 'Shipper đã lấy hàng thành công.',
        'storing'                  => 'Hàng đã được chuyển đến kho phân loại của GHN.',
        'transporting'             => 'Hàng đang được luân chuyển giữa các kho.',
        'sorting'                  => 'Hàng đang được phân loại tại kho.',
        'delivering'               => 'Shipper đang giao hàng đến khách hàng.',
        'money_collect_delivering' => 'Shipper đang tương tác với người mua.',
        'delivered'                => 'Hàng đã được giao đến khách hàng.',
        'delivery_fail'            => 'Hàng chưa được giao đến khách hàng.',
        'waiting_to_return'        => 'Hàng đang chờ giao lại (có thể giao trong 24/48h tiếp theo).',
        'return'                   => 'Hàng đang chờ hoàn về người bán sau 3 lần giao thất bại.',
        'return_transporting'      => 'Hàng đang được luân chuyển trên đường hoàn.',
        'return_sorting'           => 'Hàng đang được phân loại tại kho hoàn.',
        'returning'                => 'Shipper đang trên đường hoàn hàng về cho người bán.',
        'return_fail'              => 'Hoàn hàng thất bại.',
        'returned'                 => 'Hàng đã được hoàn về người bán thành công.',
        'exception'                => 'Xử lý ngoại lệ (trường hợp ngoài quy trình bình thường).',
        'damage'                   => 'Hàng bị hư hỏng trong quá trình vận chuyển.',
        'lost'                     => 'Hàng bị mất trong quá trình vận chuyển.',
    ];

    /**
     * Build note thân thiện từ template + địa chỉ thực (nếu có).
     */
    protected function buildFriendlyNote(string $ghnStatusRaw, ?string $rawAddress): string
    {
        $key      = strtolower($ghnStatusRaw);
        $title    = $this->ghnTitles[$key]    ?? $ghnStatusRaw;
        $template = $this->ghnDescTemplates[$key] ?? '';

        if ($template && $rawAddress && str_contains($template, '{address}')) {
            $detail = str_replace('{address}', $rawAddress, $template);
        } elseif ($template) {
            $detail = str_replace(' tại: {address}', '.', $template);
            $detail = str_replace('{address}', '', $detail);
        } else {
            $detail = '';
        }

        return $detail ? "{$title} — {$detail}" : $title;
    }

    // ══════════════════════════════════════════════════════════════════════════
    //  UPSERT BẢN GHI LỊCH SỬ
    // ══════════════════════════════════════════════════════════════════════════

    /**
     * Tạo 1 bản ghi lịch sử nếu chưa tồn tại (check trùng theo status + thời gian ±60s).
     * note = câu mô tả thân thiện theo template.
     * ghn_description = raw description gốc từ GHN API.
     */
    protected function upsertHistoryRecord(
        Order  $order,
        string $ghnStatusRaw,
        string $localStatus,
        ?string $description,
        Carbon $occurredAt
    ): void {
        $alreadyExists = OrderStatusHistory::where('order_id', $order->id)
            ->where('ghn_status_raw', $ghnStatusRaw)
            ->whereBetween('created_at', [
                $occurredAt->copy()->subSeconds(60),
                $occurredAt->copy()->addSeconds(60),
            ])
            ->exists();

        if ($alreadyExists) return;

        $friendlyNote = $this->buildFriendlyNote($ghnStatusRaw, $description);

        $history = new OrderStatusHistory([
            'order_id'        => $order->id,
            'user_id'         => null,
            'status'          => $localStatus,
            'note'            => $friendlyNote,
            'ghn_description' => $description,   // giữ raw để debug
            'ghn_status_raw'  => $ghnStatusRaw,
        ]);
        $history->created_at = $occurredAt;
        $history->updated_at = $occurredAt;
        $history->save();
    }

    // ══════════════════════════════════════════════════════════════════════════
    //  CẬP NHẬT TRẠNG THÁI ĐƠN HÀNG
    // ══════════════════════════════════════════════════════════════════════════

    protected function applyStatusChange(Order $order, string $ghnStatus): bool
    {
        $newStatus = $this->ghn->mapStatus($ghnStatus);
        if (!$newStatus || $order->status === $newStatus) {
            return false;
        }

        $order->update(['status' => $newStatus]);

        // COD: giao thành công → đánh dấu đã thu tiền
        if ($newStatus === Order::STATUS_DELIVERED && $order->payment_method === 'cod') {
            $order->update(['payment_status' => 'paid', 'paid_at' => now()]);
        }

        // GHN hủy đơn
        if ($newStatus === Order::STATUS_CANCEL) {
            $order->update([
                'cancelled_at'        => now(),
                'cancellation_reason' => 'GHN: đơn hàng đã bị hủy',
            ]);
        }

        Log::info('[ghn:sync] Cập nhật trạng thái đơn', [
            'order_code' => $order->order_code,
            'tracking'   => $order->tracking_number,
            'ghn_status' => $ghnStatus,
            'new_status' => $newStatus,
        ]);

        return true;
    }

    // ══════════════════════════════════════════════════════════════════════════
    //  PARSE TIMESTAMP TỪ GHN
    // ══════════════════════════════════════════════════════════════════════════

    /**
     * Parse timestamp từ GHN — hỗ trợ:
     * - Unix seconds:       1714377600        (UTC)
     * - Unix milliseconds:  1714377600000     (UTC, 13 chữ số)
     * - ISO string:         "2026-04-30T17:22:00.000Z"
     * - Date string:        "2026-04-30 17:22:00"
     *
     * GHN luôn trả về timestamp theo UTC. Cần chỉ định rõ timezone UTC
     * khi parse để Carbon tự convert sang app timezone (Asia/Ho_Chi_Minh).
     */
    protected function parseGhnTimestamp(mixed $date): Carbon
    {
        $appTz = config('app.timezone', 'Asia/Ho_Chi_Minh');

        if (!$date) {
            return now();
        }

        if (is_numeric($date)) {
            $ts = (int) $date;
            // Milliseconds nếu > năm 2286 tính bằng giây (10 chữ số)
            if ($ts > 9_999_999_999) {
                $ts = (int) ($ts / 1000);
            }
            // createFromTimestampUTC → parse đúng UTC → tự convert sang app timezone
            return Carbon::createFromTimestampUTC($ts)->setTimezone($appTz);
        }

        try {
            // ISO string có thể mang timezone (Z = UTC) hoặc không
            return Carbon::parse($date, 'UTC')->setTimezone($appTz);
        } catch (\Throwable) {
            return now();
        }
    }
}
