<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Attribute extends Model
{
    use SoftDeletes;

    protected $fillable = ['name'];

    // 1 Thuộc tính (Màu sắc) có nhiều Giá trị (Đỏ, Xanh)
    public function values(): HasMany
    {
        return $this->hasMany(AttributeValue::class, 'attribute_id');
    }
}   