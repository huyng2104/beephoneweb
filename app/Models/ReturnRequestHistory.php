<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReturnRequestHistory extends Model
{
    protected $fillable = [
        'return_request_id',
        'user_id',
        'status',
        'ghn_status_raw',
        'ghn_description',
        'note',
    ];

    public static $statusLabels = [
        'pending'   => 'Chờ duyệt',
        'approved'  => 'Đã duyệt',
        'rejected'  => 'Từ chối',
        'picking'   => 'Đang thu hồi',
        'received'  => 'Đã nhận hàng',
        'completed' => 'Hoàn tiền',
    ];

    public function returnRequest()
    {
        return $this->belongsTo(ReturnRequest::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
