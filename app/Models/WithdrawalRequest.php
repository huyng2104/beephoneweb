<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class WithdrawalRequest extends Model
{
    use LogsActivity;
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
        return $this->belongsTo(User::class, 'user_id')->withTrashed();
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->useLogName('withdrawal request')
            ->logOnlyDirty();
    }
}
