<?php

namespace App\Console\Commands;

use App\Models\ReturnRequest;
use App\Models\ReturnRequestHistory;
use App\Services\GhnService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SyncReturnOrders extends Command
{
    /**
     * Chạy thủ công 1 đơn hoàn:  php artisan ghn:sync-returns --code=GHN123456
     * Chạy hàng loạt:             php artisan ghn:sync-returns
     */
    protected $signature = 'ghn:sync-returns
                            {--code= : Chỉ đồng bộ 1 đơn hoàn theo mã vận đơn GHN}
                            {--all   : Đồng bộ TẤT CẢ đơn hoàn có tracking (kể cả đã hoàn tất)}';

    protected $description = 'Lấy lịch sử vận chuyển từ GHN cho đơn hoàn trả và cập nhật trạng thái ReturnRequest.';

    // Map GHN status → ReturnRequest status
    protected array $ghnToReturnStatus = [
        'ready_to_pick'            => ReturnRequest::STATUS_APPROVED,
        'picking'                  => ReturnRequest::STATUS_PICKING,
        'money_collect_picking'    => ReturnRequest::STATUS_PICKING,
        'picked'                   => ReturnRequest::STATUS_PICKING,
        'storing'                  => ReturnRequest::STATUS_PICKING,
        'transporting'             => ReturnRequest::STATUS_PICKING,
        'sorting'                  => ReturnRequest::STATUS_PICKING,
        'delivering'               => ReturnRequest::STATUS_PICKING,
        'money_collect_delivering' => ReturnRequest::STATUS_PICKING,
        'delivered'                => ReturnRequest::STATUS_RECEIVED,  // Shipper giao về kho → đã nhận
        'returned'                 => ReturnRequest::STATUS_RECEIVED,
        'delivery_fail'            => ReturnRequest::STATUS_PICKING,
        'waiting_to_return'        => ReturnRequest::STATUS_PICKING,
        'return'                   => ReturnRequest::STATUS_PICKING,
        'return_transporting'      => ReturnRequest::STATUS_PICKING,
        'return_sorting'           => ReturnRequest::STATUS_PICKING,
        'returning'                => ReturnRequest::STATUS_PICKING,
        'exception'                => ReturnRequest::STATUS_PICKING,
    ];

    // Nhãn tiếng Việt cho từng GHN status (hiển thị trong lịch sử)
    protected array $ghnLabels = [
        'ready_to_pick'            => 'Chờ lấy hàng hoàn',
        'picking'                  => 'Đang lấy hàng hoàn',
        'money_collect_picking'    => 'Đang lấy hàng hoàn',
        'picked'                   => 'Đã lấy hàng hoàn',
        'storing'                  => 'Nhập kho trung chuyển',
        'transporting'             => 'Đang luân chuyển',
        'sorting'                  => 'Đang phân loại',
        'delivering'               => 'Đang giao về kho',
        'money_collect_delivering' => 'Đang giao về kho',
        'delivered'                => 'Đã giao về kho — Chờ xác nhận',
        'returned'                 => 'Đã hoàn về kho',
        'delivery_fail'            => 'Giao về kho thất bại',
        'waiting_to_return'        => 'Chờ giao lại',
        'return'                   => 'Chờ hoàn',
        'return_transporting'      => 'Đang luân chuyển hoàn',
        'return_sorting'           => 'Đang phân loại hoàn',
        'returning'                => 'Đang hoàn',
        'exception'                => 'Ngoại lệ vận chuyển',
        'damage'                   => 'Hàng bị hỏng',
        'lost'                     => 'Hàng bị mất',
        'cancel'                   => 'Vận đơn bị hủy',
    ];

    public function __construct(protected GhnService $ghn)
    {
        parent::__construct();
    }

    // ══════════════════════════════════════════════════════════════════════════
    //  HANDLE
    // ══════════════════════════════════════════════════════════════════════════

    public function handle(): int
    {
        // ── Đồng bộ 1 đơn cụ thể ──
        if ($code = $this->option('code')) {
            $this->info("[ghn:sync-returns] Đang đồng bộ đơn hoàn: {$code}");

            $rr = ReturnRequest::where('tracking_number', $code)->first();
            if (!$rr) {
                $this->warn("[ghn:sync-returns] Không tìm thấy đơn hoàn với tracking_number = {$code}");
                return Command::FAILURE;
            }

            $this->syncSingleReturn($rr);
            $this->info("[ghn:sync-returns] Hoàn tất đồng bộ đơn hoàn {$code}");
            return Command::SUCCESS;
        }

        // ── Đồng bộ hàng loạt ──
        $this->info('[ghn:sync-returns] Bắt đầu đồng bộ đơn hoàn từ GHN...');

        $query = ReturnRequest::whereNotNull('tracking_number');

        if (!$this->option('all')) {
            // Chỉ đồng bộ đơn đang active (chưa hoàn tất / chưa từ chối)
            $query->whereNotIn('status', [
                ReturnRequest::STATUS_COMPLETED,
                ReturnRequest::STATUS_REJECTED,
            ]);
        }

        $returns = $query->get();

        if ($returns->isEmpty()) {
            $this->info('[ghn:sync-returns] Không có đơn hoàn nào cần đồng bộ.');
            return Command::SUCCESS;
        }

        $this->info("[ghn:sync-returns] Tìm thấy {$returns->count()} đơn hoàn...");
        $updated = 0;
        $failed  = 0;

        foreach ($returns as $rr) {
            $changed = $this->syncSingleReturn($rr);
            if ($changed === null) {
                $failed++;
            } elseif ($changed) {
                $updated++;
                $this->line("  ✓ #{$rr->return_code} → cập nhật trạng thái");
            } else {
                $this->line("    #{$rr->return_code} → không đổi ({$rr->status})");
            }
            usleep(200_000); // 0.2s tránh rate-limit
        }

        $this->info("[ghn:sync-returns] Hoàn tất: Cập nhật {$updated}, thất bại {$failed}.");
        Log::info("[ghn:sync-returns] Done: updated={$updated}, failed={$failed}");

        return Command::SUCCESS;
    }

    // ══════════════════════════════════════════════════════════════════════════
    //  SYNC 1 ĐƠN HOÀN
    // ══════════════════════════════════════════════════════════════════════════

    /**
     * Đồng bộ lịch sử GHN cho 1 ReturnRequest.
     *
     * @return bool|null  true = status đổi, false = không đổi, null = lỗi
     */
    public function syncSingleReturn(ReturnRequest $rr): ?bool
    {
        $code = $rr->tracking_number;
        if (!$code) return null;

        // ── Bước 1: Lấy detail ──
        $detail = $this->ghn->getOrderDetail($code);
        if (!$detail) {
            Log::warning("[ghn:sync-returns] Không lấy được detail: #{$rr->return_code} ({$code})");
            return null;
        }

        $ghnStatus = $detail['status'] ?? null;
        if (!$ghnStatus) return null;

        // ── Bước 2: Lấy tracking events và lưu vào return_request_histories ──
        $trackingEvents = $this->ghn->getOrderTracking($code);

        if (!empty($trackingEvents)) {
            $this->syncTrackingEvents($rr, $trackingEvents);
        } elseif (!empty($detail['log'])) {
            $this->syncFromDetailLog($rr, $detail['log']);
        }

        // ── Bước 3: Cập nhật trạng thái ReturnRequest ──
        return $this->applyStatusChange($rr, $ghnStatus);
    }

    // ══════════════════════════════════════════════════════════════════════════
    //  XỬ LÝ EVENTS
    // ══════════════════════════════════════════════════════════════════════════

    protected function syncTrackingEvents(ReturnRequest $rr, array $events): void
    {
        $events = array_reverse($events);

        foreach ($events as $event) {
            if (!is_array($event)) continue;

            $ghnStatusRaw = $event['status'] ?? $event['Status'] ?? null;
            if (!$ghnStatusRaw) continue;

            $localStatus = $this->mapGhnToReturnStatus($ghnStatusRaw) ?? $rr->status;
            $description = $event['description'] ?? $event['Description'] ?? $event['note'] ?? null;
            $occurredAt  = $this->parseGhnTimestamp(
                $event['updated_date'] ?? $event['date'] ?? $event['Date'] ?? null
            );

            $this->upsertHistoryRecord($rr, $ghnStatusRaw, $localStatus, $description, $occurredAt);
        }
    }

    protected function syncFromDetailLog(ReturnRequest $rr, array $logs): void
    {
        $logs = array_reverse($logs);

        foreach ($logs as $logEntry) {
            if (!is_array($logEntry)) continue;

            $ghnStatusRaw = $logEntry['status'] ?? $logEntry['Status'] ?? null;
            if (!$ghnStatusRaw) continue;

            $localStatus = $this->mapGhnToReturnStatus($ghnStatusRaw) ?? $rr->status;
            $description = $logEntry['description'] ?? $logEntry['Description'] ?? null;
            $occurredAt  = $this->parseGhnTimestamp(
                $logEntry['updated_date'] ?? $logEntry['date'] ?? $logEntry['Date'] ?? null
            );

            $this->upsertHistoryRecord($rr, $ghnStatusRaw, $localStatus, $description, $occurredAt);
        }
    }

    // ══════════════════════════════════════════════════════════════════════════
    //  UPSERT
    // ══════════════════════════════════════════════════════════════════════════

    protected function upsertHistoryRecord(
        ReturnRequest $rr,
        string $ghnStatusRaw,
        string $localStatus,
        ?string $description,
        Carbon $occurredAt
    ): void {
        // Chống trùng: cùng ghn_status_raw trong khoảng ±60 giây
        $exists = ReturnRequestHistory::where('return_request_id', $rr->id)
            ->where('ghn_status_raw', $ghnStatusRaw)
            ->whereBetween('created_at', [
                $occurredAt->copy()->subSeconds(60),
                $occurredAt->copy()->addSeconds(60),
            ])
            ->exists();

        if ($exists) return;

        $label       = $this->ghnLabels[strtolower($ghnStatusRaw)] ?? $ghnStatusRaw;
        $friendlyNote = $description
            ? "{$label} — {$description}"
            : $label;

        $history = new ReturnRequestHistory([
            'return_request_id' => $rr->id,
            'user_id'           => null, // GHN event, không có user
            'status'            => $localStatus,
            'ghn_status_raw'    => $ghnStatusRaw,
            'ghn_description'   => $description,
            'note'              => $friendlyNote,
        ]);
        $history->created_at = $occurredAt;
        $history->updated_at = $occurredAt;
        $history->save();
    }

    // ══════════════════════════════════════════════════════════════════════════
    //  CẬP NHẬT TRẠNG THÁI RETURN REQUEST
    // ══════════════════════════════════════════════════════════════════════════

    protected function applyStatusChange(ReturnRequest $rr, string $ghnStatus): bool
    {
        $newStatus = $this->mapGhnToReturnStatus($ghnStatus);

        if (!$newStatus) return false;

        // Không downgrade trạng thái (ví dụ: đã "received" không lùi về "picking")
        $statusOrder = [
            ReturnRequest::STATUS_PENDING   => 1,
            ReturnRequest::STATUS_APPROVED  => 2,
            ReturnRequest::STATUS_PICKING   => 3,
            ReturnRequest::STATUS_RECEIVED  => 4,
            ReturnRequest::STATUS_COMPLETED => 5,
        ];

        $currentWeight = $statusOrder[$rr->status] ?? 0;
        $newWeight     = $statusOrder[$newStatus] ?? 0;

        if ($newWeight <= $currentWeight) return false;

        $updateData = ['status' => $newStatus];

        // Tự động set timestamp tương ứng nếu chưa có
        if ($newStatus === ReturnRequest::STATUS_PICKING && !$rr->approved_at) {
            $updateData['approved_at'] = now();
        }
        if ($newStatus === ReturnRequest::STATUS_RECEIVED && !$rr->received_at) {
            $updateData['received_at'] = now();
        }

        $rr->update($updateData);

        Log::info('[ghn:sync-returns] Cập nhật trạng thái đơn hoàn', [
            'return_code' => $rr->return_code,
            'tracking'    => $rr->tracking_number,
            'ghn_status'  => $ghnStatus,
            'new_status'  => $newStatus,
        ]);

        return true;
    }

    // ══════════════════════════════════════════════════════════════════════════
    //  HELPERS
    // ══════════════════════════════════════════════════════════════════════════

    protected function mapGhnToReturnStatus(string $ghnStatus): ?string
    {
        return $this->ghnToReturnStatus[strtolower(trim($ghnStatus))] ?? null;
    }

    protected function parseGhnTimestamp(mixed $date): Carbon
    {
        $appTz = config('app.timezone', 'Asia/Ho_Chi_Minh');

        if (!$date) return now();

        if (is_numeric($date)) {
            $ts = (int) $date;
            if ($ts > 9_999_999_999) $ts = (int) ($ts / 1000);
            return Carbon::createFromTimestampUTC($ts)->setTimezone($appTz);
        }

        try {
            return Carbon::parse($date, 'UTC')->setTimezone($appTz);
        } catch (\Throwable) {
            return now();
        }
    }
}
