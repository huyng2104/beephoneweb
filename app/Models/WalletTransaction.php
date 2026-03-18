<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WalletTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'wallet_id',
        'type',
        'amount',
        'balance_before',
        'balance_after',
        'description',
        'reference_id',
        'status'
    ];

    // Mối quan hệ: Một giao dịch thuộc về 1 Ví
    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }
    public function withdrawal_requests()
    {
        return $this->hasMany(WithdrawalRequest::class);
    }
    public function getTypeTransactionAttribute()
    {
        return match ($this->type) {
            'deposit'  => '<span class="text-green-600 font-medium text-sm">+ Nạp tiền vào ví</span>',
            'payment'  => '<span class="text-red-600 font-medium text-sm">- Thanh toán</span>',
            'withdraw' => '<span class="text-orange-500 font-medium text-sm">- Rút tiền</span>',
            default    => '<span class="text-blue-600 font-medium text-sm">+ Hoàn tiền</span>',
        };
    }
    public function getStatusTransactionAttribute()
    {
        return match ($this->status) {
            'pending' => 'Đang chờ',
            'completed' => 'Thành công',
            'failed' => 'Thất bại',
            'cancelled' => 'Hủy'
        };
    }
}
