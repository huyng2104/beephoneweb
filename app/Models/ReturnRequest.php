<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReturnRequest extends Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_PICKING = 'picking';
    public const STATUS_RECEIVED = 'received';
    public const STATUS_COMPLETED = 'completed';

    protected $fillable = [
        'order_id',
        'user_id',
        'return_code',
        'status',
        'reason',
        'image', // keep for old records/compatibility
        'images', // new JSON column
        'admin_note',
        'tracking_number',
        'total_refund_amount',
        'return_shipping_fee',
        'approved_at',
        'rejected_at',
        'received_at',
        'completed_at',
    ];

    protected $casts = [
        'images' => 'array',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'received_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(ReturnRequestItem::class);
    }

    public function histories()
    {
        return $this->hasMany(ReturnRequestHistory::class)->orderByDesc('created_at')->orderByDesc('id');
    }


    public static function statusLabels(): array
    {
        return [
            self::STATUS_PENDING   => 'Chờ duyệt',
            self::STATUS_APPROVED  => 'Đã duyệt',
            self::STATUS_REJECTED  => 'Từ chối',
            self::STATUS_PICKING   => 'Đang thu hồi',
            self::STATUS_RECEIVED  => 'Đã nhận hàng về',
            self::STATUS_COMPLETED => 'Hoàn tất',
        ];
    }

    public function getItemReturnStatus(): string
    {
        if (in_array($this->status, [self::STATUS_PENDING, self::STATUS_REJECTED])) {
            return 'delivered';
        }
        if (in_array($this->status, [self::STATUS_APPROVED, self::STATUS_PICKING])) {
            return 'returning';
        }
        if (in_array($this->status, [self::STATUS_RECEIVED, self::STATUS_COMPLETED])) {
            return 'returned';
        }
        return 'delivered';
    }

    public function canApprove(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function canReject(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function canMarkReceived(): bool
    {
        return in_array($this->status, [self::STATUS_APPROVED, self::STATUS_PICKING]);
    }

    public function canRefund(): bool
    {
        return $this->status === self::STATUS_RECEIVED;
    }

    /**
     * Kiểm tra xem GHN đã báo giao thành công/hoàn về kho chưa
     */
    public function isGhnDelivered(): bool
    {
        // Kiểm tra trong lịch sử có trạng thái 'delivered' hoặc 'returned' từ GHN không
        return $this->histories()
            ->whereNotNull('ghn_status_raw')
            ->get()
            ->contains(function($h) {
                $status = strtolower(trim($h->ghn_status_raw));
                return in_array($status, ['delivered', 'returned']);
            });
    }
}