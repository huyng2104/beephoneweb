<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class User extends Authenticatable  implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'avatar',
        'status',
        'password',
        'role_id',
        'gender',
        'birthday',
        'address'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'birthday' => 'date',
        ];
    }

    public function isLocked()
    {
        return $this->status === 'locked'; // (Thay đổi logic cho phù hợp với DB của bạn)
    }
    public function role()
    {
        return $this->belongsTo(Role::class);
    }
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'user_permissions');
    }
    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }
    public function getUserStatusAttribute()
    {
        if ($this->status == 'inactive') {
            return 'Chưa kích hoạt';
        }
        if ($this->status == 'active') {
            return 'Hoạt động';
        }
        return 'Bị khóa';
    }
    protected static function booted()
    {
        static::created(function ($user) {
            activity_log('user.create', 'Tạo người dùng', $user);
        });

        static::updated(function ($user) {
            activity_log('user.update', 'Cập nhật người dùng', $user);
        });

        static::deleted(function ($user) {
            activity_log('user.delete', 'Xóa người dùng', $user);
        });
    }
    // Một User có 1 Ví tiền
    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
    public function pointHistories()
    {
        return $this->hasMany(PointHistory::class);
    }
    // 1. Mối quan hệ với bảng Lịch sử điểm

    public function userVouchers()
    {
        return $this->belongsToMany(Voucher::class, 'user_vouchers')->wherePivot('order_id');
    }
    // 2. Cột ảo tính Tổng điểm hiện tại của User
   public function getTotalPointsAttribute()
    {
        // Trỏ thẳng vào cột có sẵn trong DB, load nhanh như chớp!
        return $this->reward_points ?? 0;
    }
}
