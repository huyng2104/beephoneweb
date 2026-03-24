<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PointHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 
        'order_id',    // Có thể null (nếu điểm dùng để đổi Voucher)
        'voucher_id',  // Có thể null (nếu điểm từ việc mua Order)
        'points',      // Số điểm (+ hoặc -)
        'type',        // 'earn' (tích điểm), 'redeem' (đổi điểm), 'refund' (hoàn điểm)
        'description'  // Ví dụ: "Tích điểm từ đơn ORD-123", "Đổi 500 điểm lấy Voucher freeship"
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function voucher()
    {
        return $this->belongsTo(Voucher::class);
    }
}