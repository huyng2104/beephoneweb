<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class PointSetting extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'earn_rate', 
        'redeem_rate', 
        'is_active'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->useLogName('point')
            ->logOnlyDirty();
    }
}