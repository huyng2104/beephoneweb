<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FilterAttribute extends Model
{
    protected $fillable = [
        'name',
        'code',
        'input_type',
        'suggested_values',
        'is_filterable',
    ];

    protected $casts = [
        'is_filterable' => 'boolean',
    ];

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class)
            ->withPivot(['sort_order', 'is_required'])
            ->withTimestamps();
    }

    public function productValues(): HasMany
    {
        return $this->hasMany(ProductAttributeValue::class);
    }

    public function suggestedValuesArray(): array
    {
        if (!$this->suggested_values) {
            return [];
        }

        return collect(explode(',', $this->suggested_values))
            ->map(fn($value) => trim($value))
            ->filter()
            ->values()
            ->all();
    }
}
