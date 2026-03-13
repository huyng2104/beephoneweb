<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WalletTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'wallet_id', 'type', 'amount', 'balance_before', 
        'balance_after', 'description', 'reference_id', 'status'
    ];

    // Mối quan hệ: Một giao dịch thuộc về 1 Ví
    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }
}