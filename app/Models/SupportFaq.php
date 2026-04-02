<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupportFaq extends Model
{
    protected $fillable = [
        'category',
        'question',
        'answer',
        'keywords',
        'sort_order',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('created_at', 'desc');
    }
}
