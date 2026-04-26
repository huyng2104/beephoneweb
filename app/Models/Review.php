<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Review extends Model
{
    // ===== TRẠNG THÁI =====
    const STATUS_PENDING  = 0; // Chờ duyệt
    const STATUS_APPROVED = 1; // Hiển thị
    const STATUS_HIDDEN   = 2; // Ẩn / từ chối

    protected $fillable = [
        'user_id',
        'order_id',
        'product_id',
        'rating',
        'comment',
        'status',
        'is_purchased',
        'helpful_count',
        'reply_comment',
        'replied_by',
        'replied_at',
    ];

    protected $casts = [
        'is_purchased' => 'boolean',
        'replied_at'   => 'datetime',
        'rating'       => 'integer',
        'status'       => 'integer',
        'helpful_count' => 'integer',
    ];

    // ===== RELATIONS =====

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /** Ảnh đính kèm đánh giá */
    public function images(): HasMany
    {
        return $this->hasMany(ReviewImage::class)->orderBy('sort_order');
    }

    /** Admin/nhân viên đã phản hồi */
    public function repliedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'replied_by');
    }

    // ===== SCOPES =====

    /** Chỉ lấy các đánh giá đã duyệt */
    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    /** Chỉ lấy các đánh giá chờ duyệt */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    // ===== HELPERS =====

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function hasReply(): bool
    {
        return !empty($this->reply_comment);
    }

    /** Label hiển thị trạng thái */
    public function statusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING  => 'Chờ duyệt',
            self::STATUS_APPROVED => 'Hiển thị',
            self::STATUS_HIDDEN   => 'Đã ẩn',
            default               => 'Không xác định',
        };
    }
}
