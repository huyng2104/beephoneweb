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
        'reference_type',
        'reference_id',
        'status',
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
            'deposit'  => '<span class="px-2 py-1 bg-green-100 text-green-600 font-medium text-sm rounded-xl text-xs">Nạp tiền</span>',
            'payment'  => '<span class="px-2 py-1 bg-red-100 text-red-600 font-medium text-sm rounded-xl text-xs">Thanh toán</span>',
            'withdraw' => '<span class="px-2 py-1 bg-orange-100 text-orange-500 font-medium text-sm rounded-xl text-xs">Rút tiền</span>',
            default    => '<span class="px-2 py-1 bg-blue-100 text-blue-600 font-medium text-sm rounded-xl text-xs">Hoàn tiền</span>',
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
    public function reference()
    {
        return $this->morphTo();
    }
}
