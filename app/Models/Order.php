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
        'phone',
        'customer_phone',
        'customer_email',
        'recipient_name',
        'recipient_phone',
        'recipient_address',
        'shipping_address',
        'address',
        'total_price',
        'total_amount',
        'status',
        'return_status',
        'note',
        'cancellation_reason',
        'return_note',
        'return_image',
        'return_admin_note',
        'ordered_at',
        'cancelled_at',
        'return_requested_at',
        'return_approved_at',
        'return_rejected_at',
        'return_shipped_at',
        'return_received_at',
        'return_refunded_at',
        'payment_method',
        'payment_status',
        'paid_at',
        'refund_amount',
    ];

    protected $casts = [
        'ordered_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'return_requested_at' => 'datetime',
        'return_approved_at' => 'datetime',
        'return_rejected_at' => 'datetime',
        'return_shipped_at' => 'datetime',
        'return_received_at' => 'datetime',
        'return_refunded_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    public const STATUS_PENDING = 'pending';
    public const STATUS_PACKING = 'packing';
    public const STATUS_SHIPPING = 'shipping';
    public const STATUS_DELIVERED = 'delivered';
    public const STATUS_RECEIVED = 'received';
    public const STATUS_CANCELLED = 'cancelled';

    public const RETURN_NONE = 'none';
    public const RETURN_REQUESTED = 'requested';
    public const RETURN_APPROVED = 'approved';
    public const RETURN_REJECTED = 'rejected';
    public const RETURN_CUSTOMER_SHIPPED = 'customer_shipped';
    public const RETURN_RECEIVED = 'received';
    public const RETURN_REFUNDED = 'refunded';

    public static function statuses(): array
    {
        return [
            self::STATUS_PENDING,
            self::STATUS_PACKING,
            self::STATUS_SHIPPING,
            self::STATUS_DELIVERED,
            self::STATUS_RECEIVED,
            self::STATUS_CANCELLED,
        ];
    }

    public static function statusLabels(): array
    {
        return [
            self::STATUS_PENDING => 'Chờ xử lý',
            self::STATUS_PACKING => 'Đang đóng hàng',
            self::STATUS_SHIPPING => 'Đang giao',
            self::STATUS_DELIVERED => 'Giao thành công',
            self::STATUS_RECEIVED => 'Đã nhận hàng',
            self::STATUS_CANCELLED => 'Đã hủy',
        ];
    }

    public static function returnStatuses(): array
    {
        return [
            self::RETURN_NONE,
            self::RETURN_REQUESTED,
            self::RETURN_APPROVED,
            self::RETURN_REJECTED,
            self::RETURN_CUSTOMER_SHIPPED,
            self::RETURN_RECEIVED,
            self::RETURN_REFUNDED,
        ];
    }

    public static function returnStatusLabels(): array
    {
        return [
            self::RETURN_NONE => 'Không hoàn hàng',
            self::RETURN_REQUESTED => 'Đã gửi yêu cầu',
            self::RETURN_APPROVED => 'Admin đã duyệt',
            self::RETURN_REJECTED => 'Admin từ chối',
            self::RETURN_CUSTOMER_SHIPPED => 'Khách đã gửi hàng hoàn',
            self::RETURN_RECEIVED => 'Admin đã nhận hàng hoàn',
            self::RETURN_REFUNDED => 'Đã hoàn tiền vào ví',
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
        ];
    }

    public static function nextStatusMap(): array
    {
        return [
            self::STATUS_PENDING => [self::STATUS_PACKING],
            self::STATUS_PACKING => [self::STATUS_SHIPPING],
            self::STATUS_SHIPPING => [self::STATUS_DELIVERED],
            self::STATUS_DELIVERED => [self::STATUS_RECEIVED],
            self::STATUS_RECEIVED => [],
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
                self::STATUS_PACKING,
                self::STATUS_SHIPPING,
            ], true);
        }

        return in_array($nextStatus, self::nextStatusMap()[$this->status] ?? [], true);
    }

    public function canRequestReturn(): bool
    {
        return $this->status === self::STATUS_RECEIVED
            && $this->return_status === self::RETURN_NONE;
    }

    public function canApproveReturn(): bool
    {
        return in_array($this->status, [self::STATUS_DELIVERED, self::STATUS_RECEIVED], true)
            && $this->return_status === self::RETURN_REQUESTED;
    }

    public function canRejectReturn(): bool
    {
        return $this->canApproveReturn();
    }

    public function canCustomerShipReturn(): bool
    {
        return $this->return_status === self::RETURN_APPROVED;
    }

    public function canMarkReturnReceived(): bool
    {
        return $this->return_status === self::RETURN_CUSTOMER_SHIPPED;
    }

    public function canRefundReturn(): bool
    {
        return $this->return_status === self::RETURN_RECEIVED
            && $this->payment_status === 'paid';
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class)->orderBy('id');
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class)->orderByDesc('id');
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
