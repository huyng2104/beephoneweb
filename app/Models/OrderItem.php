<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    public const RETURN_NONE = 'none';
    public const RETURN_REQUESTED = 'requested';
    public const RETURN_APPROVED = 'approved';
    public const RETURN_REJECTED = 'rejected';
    public const RETURN_CUSTOMER_SHIPPED = 'customer_shipped';
    public const RETURN_RECEIVED = 'received';
    public const RETURN_REFUNDED = 'refunded';

   protected $fillable = [
        'order_id',
        'product_id',
        'product_name',
        'product_sku',
        'thumbnail',
        'unit_price',
        'quantity',
        'line_total',
        'return_status',
        'return_note',
        'return_image',
        'return_admin_note',
        'refund_amount',
        'return_requested_at',
        'return_approved_at',
        'return_rejected_at',
        'return_shipped_at',
        'return_received_at',
        'return_refunded_at',
    ];

    protected $casts = [
        'return_requested_at' => 'datetime',
        'return_approved_at' => 'datetime',
        'return_rejected_at' => 'datetime',
        'return_shipped_at' => 'datetime',
        'return_received_at' => 'datetime',
        'return_refunded_at' => 'datetime',
    ];

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
            self::RETURN_NONE => 'Không có yêu cầu',
            self::RETURN_REQUESTED => 'Yêu cầu hoàn trả',
            self::RETURN_APPROVED => 'Đã duyệt yêu cầu',
            self::RETURN_REJECTED => 'Từ chối yêu cầu',
            self::RETURN_CUSTOMER_SHIPPED => 'Khách đã gửi hàng',
            self::RETURN_RECEIVED => 'Đã nhận hàng trả',
            self::RETURN_REFUNDED => 'Đã hoàn tiền (Đóng)',
        ];
    }

    public function canRequestReturn(): bool
    {
        // Điều kiện: đơn hàng đã hoàn thành, nằm trong thời gian cho phép hoàn hàng (VD: 3 ngày)
        // và sản phẩm này chưa từng yêu cầu (hoặc đã bị admin từ chối thì vẫn không cho lại)
        if (!$this->order || !in_array($this->order->status, [Order::STATUS_DELIVERED, Order::STATUS_RECEIVED])) {
            return false;
        }

        return $this->return_status === self::RETURN_NONE;
    }

    public function canCustomerShipReturn(): bool
    {
        return $this->return_status === self::RETURN_APPROVED;
    }

    public function canApproveReturn(): bool
    {
        return $this->return_status === self::RETURN_REQUESTED;
    }

    public function canRejectReturn(): bool
    {
        return $this->return_status === self::RETURN_REQUESTED;
    }

    public function canMarkReturnReceived(): bool
    {
        return in_array($this->return_status, [self::RETURN_APPROVED, self::RETURN_CUSTOMER_SHIPPED]);
    }

    public function canRefundReturn(): bool
    {
        return $this->return_status === self::RETURN_RECEIVED;
    }

    // TÍNH TOÁN SỐ TIỀN HOÀN TRẢ THEO TỈ LỆ
    public function calculateRefundAmount(): int
    {
        if (!$this->order) return 0;
        
        $totalPrice = $this->order->total_price;
        if ($totalPrice <= 0) return 0; // Tránh chia cho 0

        $totalAmount = $this->order->total_amount;
        $discountAmount = $totalPrice - $totalAmount;

        if ($discountAmount <= 0) {
            return (int) $this->line_total; // Không có voucher, hoàn đúng tiền sản phẩm
        }

        // Tỷ lệ = line_total / total_price
        // Tiền voucher tương ứng của sản phẩm = discountAmount * tỷ lệ
        $discountShare = $discountAmount * ($this->line_total / $totalPrice);
        $refundAmount = $this->line_total - $discountShare;

        return (int) max(0, $refundAmount);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
