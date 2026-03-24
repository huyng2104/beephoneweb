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
        'phone',                // Đã thêm
        'customer_phone',
        'customer_email',
        'recipient_name',
        'recipient_phone',
        'recipient_address',
        'shipping_address',
        'address',              // Đã thêm
        'total_price',          // Đã thêm
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
        'payment_method',       // Đã thêm
        'payment_status',
    ];
    
    protected $casts = [
        'ordered_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'return_requested_at' => 'datetime',
        'return_confirmed_at' => 'datetime',
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
            self::STATUS_RECEIVED => 'Đã nhận được hàng',
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
            self::RETURN_NONE => 'Không đổi/trả',
            self::RETURN_REQUESTED => 'Đã yêu cầu đổi/trả',
            self::RETURN_CONFIRMED => 'Đã xác nhận đổi/trả',
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