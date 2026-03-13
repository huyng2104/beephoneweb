<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'balance', 'status'];

    // Mối quan hệ: Một ví thuộc về 1 User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Mối quan hệ: Một ví có nhiều Giao dịch
    public function transactions()
    {
        return $this->hasMany(WalletTransaction::class)->orderBy('created_at', 'desc');
    }
}