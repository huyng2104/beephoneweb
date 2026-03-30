<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
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
        'ordered_at',
        'cancelled_at',
        'return_requested_at',
        'return_confirmed_at',
        'payment_method',
        'payment_status',
        'paid_at',
    ];

    protected $casts = [
        'ordered_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'return_requested_at' => 'datetime',
        'return_confirmed_at' => 'datetime',
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
    public const RETURN_CONFIRMED = 'confirmed';

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
            self::RETURN_CONFIRMED,
        ];
    }

    public static function returnStatusLabels(): array
    {
        return [
            self::RETURN_NONE => 'Không hoàn hàng',
            self::RETURN_REQUESTED => 'Đã gửi yêu cầu hoàn hàng',
            self::RETURN_CONFIRMED => 'Đã xác nhận hoàn hàng',
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
        return in_array($this->status, [self::STATUS_DELIVERED, self::STATUS_RECEIVED], true)
            && $this->return_status === self::RETURN_NONE;
    }

    public function canConfirmReturn(): bool
    {
        return in_array($this->status, [self::STATUS_DELIVERED, self::STATUS_RECEIVED], true)
            && $this->return_status === self::RETURN_REQUESTED;
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
}
