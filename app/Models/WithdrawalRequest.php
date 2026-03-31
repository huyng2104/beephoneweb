<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WithdrawalRequest extends Model
{
    protected $table = 'withdrawal_requests';
    protected $fillable = [
        'user_id',
        'amount',
        'bank_name',
        'account_number',
        'account_name',
        'status',
        'admin_note',
        'approved_by',
        'transaction_id',
        'proof_image',
    ];
    public function walletTransaction()
    {
        return $this->belongsTo(WalletTransaction::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
