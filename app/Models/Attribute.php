<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Attribute extends Model
{
    use SoftDeletes, LogsActivity;

    protected $fillable = ['name'];

    // 1 Thuộc tính (Màu sắc) có nhiều Giá trị (Đỏ, Xanh)
    public function values(): HasMany
    {
        return $this->hasMany(AttributeValue::class, 'attribute_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->useLogName('attribute')
            ->logOnlyDirty();
    }
}