<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Order extends Model
{
    use LogsActivity;

    protected $fillable = [
        'order_code',
        'user_id',
        'customer_name',
        'customer_phone',
        'customer_email',
        'shipping_address',
        'ghn_district_id',
        'ghn_ward_code',
        'shipping_fee',
        'total_amount',
        'status',
        'note',
        'tracking_number',
        'cancellation_reason',
        'ordered_at',
        'cancelled_at',
        'payment_method',
        'payment_status',
        'paid_at',
    ];


    protected $casts = [
        'ordered_at'  => 'datetime',
        'cancelled_at' => 'datetime',
        'paid_at'     => 'datetime',
    ];

    // ── Trạng thái nội bộ (không phải từ GHN) ─────────────────────────────────────
    public const STATUS_PENDING   = 'pending';
    public const STATUS_RECEIVED  = 'received';
    public const STATUS_CANCELLED = 'cancelled';

    // ── Trạng thái GHN (lưu trực tiếp từ API) ─────────────────────────────────
    public const STATUS_READY_TO_PICK            = 'ready_to_pick';
    public const STATUS_PICKING                  = 'picking';
    public const STATUS_MONEY_COLLECT_PICKING    = 'money_collect_picking';
    public const STATUS_PICKED                   = 'picked';
    public const STATUS_STORING                  = 'storing';
    public const STATUS_TRANSPORTING             = 'transporting';
    public const STATUS_SORTING                  = 'sorting';
    public const STATUS_DELIVERING               = 'delivering';
    public const STATUS_MONEY_COLLECT_DELIVERING = 'money_collect_delivering';
    public const STATUS_DELIVERED                = 'delivered';
    public const STATUS_DELIVERY_FAIL            = 'delivery_fail';
    public const STATUS_WAITING_TO_RETURN        = 'waiting_to_return';
    public const STATUS_RETURN                   = 'return';
    public const STATUS_RETURN_TRANSPORTING      = 'return_transporting';
    public const STATUS_RETURN_SORTING           = 'return_sorting';
    public const STATUS_RETURNING                = 'returning';
    public const STATUS_RETURN_FAIL              = 'return_fail';
    public const STATUS_RETURNED                 = 'returned';
    public const STATUS_EXCEPTION                = 'exception';
    public const STATUS_DAMAGE                   = 'damage';
    public const STATUS_LOST                     = 'lost';
    public const STATUS_CANCEL                   = 'cancel';         // GHN hủy


    public static function statuses(): array
    {
        return [
            self::STATUS_PENDING,
            self::STATUS_READY_TO_PICK,
            self::STATUS_PICKING,
            self::STATUS_MONEY_COLLECT_PICKING,
            self::STATUS_PICKED,
            self::STATUS_STORING,
            self::STATUS_TRANSPORTING,
            self::STATUS_SORTING,
            self::STATUS_DELIVERING,
            self::STATUS_MONEY_COLLECT_DELIVERING,
            self::STATUS_DELIVERED,
            self::STATUS_DELIVERY_FAIL,
            self::STATUS_WAITING_TO_RETURN,
            self::STATUS_RETURN,
            self::STATUS_RETURN_TRANSPORTING,
            self::STATUS_RETURN_SORTING,
            self::STATUS_RETURNING,
            self::STATUS_RETURN_FAIL,
            self::STATUS_RETURNED,
            self::STATUS_EXCEPTION,
            self::STATUS_DAMAGE,
            self::STATUS_LOST,
            self::STATUS_CANCEL,
            self::STATUS_RECEIVED,
            self::STATUS_CANCELLED,
        ];
    }

    public static function statusLabels(): array
    {
        return [
            self::STATUS_PENDING                   => 'Chờ xử lý',
            self::STATUS_READY_TO_PICK             => 'Chờ lấy hàng',
            self::STATUS_PICKING                   => 'Đang lấy hàng',
            self::STATUS_MONEY_COLLECT_PICKING     => 'Đang lấy hàng',
            self::STATUS_PICKED                    => 'Đã lấy hàng',
            self::STATUS_STORING                   => 'Nhập kho trung chuyển',
            self::STATUS_TRANSPORTING              => 'Đang luân chuyển',
            self::STATUS_SORTING                   => 'Đang phân loại',
            self::STATUS_DELIVERING                => 'Đang giao hàng',
            self::STATUS_MONEY_COLLECT_DELIVERING  => 'Đang giao hàng',
            self::STATUS_DELIVERED                 => 'Giao thành công',
            self::STATUS_DELIVERY_FAIL             => 'Giao thất bại',
            self::STATUS_WAITING_TO_RETURN         => 'Đang chờ giao lại',
            self::STATUS_RETURN                    => 'Chờ hoàn hàng',
            self::STATUS_RETURN_TRANSPORTING       => 'Đang luân chuyển hoàn',
            self::STATUS_RETURN_SORTING            => 'Đang phân loại hoàn',
            self::STATUS_RETURNING                 => 'Đang hoàn hàng',
            self::STATUS_RETURN_FAIL               => 'Hoàn hàng thất bại',
            self::STATUS_RETURNED                  => 'Đã hoàn hàng',
            self::STATUS_EXCEPTION                 => 'Ngoại lệ',
            self::STATUS_DAMAGE                    => 'Hàng bị hỏng',
            self::STATUS_LOST                      => 'Hàng bị mất',
            self::STATUS_CANCEL                    => 'Đã hủy (đơn GHN)',
            self::STATUS_RECEIVED                  => 'Đã nhận hàng',
            self::STATUS_CANCELLED                 => 'Đã hủy',
        ];
    }



    public static function paymentMethodLabels(): array
    {
        return [
            'cod' => 'Thanh toán khi nhận hàng',
            'vnpay' => 'VNPAY',
            'wallet' => 'Ví Bee Pay',
        ];
    }

    public static function paymentStatusLabels(): array
    {
        return [
            'pending' => 'Chờ thanh toán',
            'paid' => 'Đã thanh toán',
            'failed' => 'Thanh toán thất bại',
            'cancelled' => 'Đã hủy',
            'refunded' => 'Đã hoàn tiền',
        ];
    }

    public static function nextStatusMap(): array
    {
        return [
            self::STATUS_PENDING   => [self::STATUS_READY_TO_PICK],
            self::STATUS_DELIVERED => [self::STATUS_RECEIVED],
            self::STATUS_RECEIVED  => [],
            self::STATUS_CANCELLED => [],
        ];
    }

    public function canMoveTo(string $nextStatus): bool
    {
        if ($this->status === $nextStatus) {
            return true;
        }

        if ($nextStatus === self::STATUS_CANCELLED) {
            return in_array($this->status, [
                self::STATUS_PENDING,
                self::STATUS_READY_TO_PICK,
            ], true);
        }

        return in_array($nextStatus, self::nextStatusMap()[$this->status] ?? [], true);
    }


    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class)->orderBy('id');
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class)->orderByDesc('created_at')->orderByDesc('id');
    }

    public function returnRequests(): HasMany
    {
        return $this->hasMany(ReturnRequest::class)->orderByDesc('created_at');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->useLogName('order')
            ->logOnlyDirty();
    }
}
