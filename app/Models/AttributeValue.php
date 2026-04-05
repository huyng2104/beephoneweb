<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class AttributeValue extends Model
{
    use SoftDeletes, LogsActivity;

protected $fillable = ['attribute_id', 'value', 'sort_order'];
    // Giá trị này thuộc về Thuộc tính nào?
    public function attribute(): BelongsTo
    {
        return $this->belongsTo(Attribute::class, 'attribute_id');
    }

    // [QUAN TRỌNG] Giá trị này đang nằm trong các Biến thể nào? (Thông qua bảng trung gian)
    public function variants(): BelongsToMany
    {
        return $this->belongsToMany(ProductVariant::class, 'variant_attribute_values', 'attribute_value_id', 'variant_id')
                    ->withTimestamps();
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->useLogName('attribute value')
            ->logOnlyDirty();
    }
}