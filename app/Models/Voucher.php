<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Voucher extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'name',
        'code',
        'discount_type',
        'discount_value',
        'max_discount',
        'min_order_value',
        'usage_limit',
        'used_count',
        'usage_limit_per_user',
        'description',
        'start_date',
        'end_date',
        'status',
    ];
    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_vouchers')
            ->withPivot('order_id', 'used_at')
            ->withTimestamps();
    }
    public function brands()
    {
        return $this->belongsToMany(Brand::class, 'voucher_brand');
    }
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'voucher_category');
    }
    public function products()
    {
        return $this->belongsToMany(Product::class, 'voucher_product');
    }
     public function userVouchers()
    {
        return $this->belongsToMany(Voucher::class, 'user_vouchers')->wherePivot('order_id');
    }
    public function getVoucherStatusAttribute()
    {
        if ($this->status == 0) {
            return 'Tạm dừng';
        }
        if ($this->used_count == $this->usage_limit) {
            return 'Hết lượn dùng';
        }
        if ($this->start_date && now()->lt($this->start_date)) {
            return 'Chưa được sử dụng';
        }
        if ($this->end_date && now()->gt($this->end_date)) {
            return 'Đã Hết hạn';
        }

        return 'Hoạt động';
    }
    public function getUsagePercentAttribute()
    {
        if (!$this->usage_limit || $this->usage_limit == 0) {
            return 0;
        }

        return round(($this->used_count / $this->usage_limit) * 100);
    }
}
