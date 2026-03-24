<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
    'name', 'slug', 'description', 'type', 'price', 'sale_price', 
    'sku', 'stock', 'status', 'is_featured', 'brand_id', 'thumbnail','specifications'
];
protected $casts = [
    // ... các casts cũ nếu có
    'specifications' => 'array', // Ép kiểu tự động cực kỳ quan trọng
];

    // Sản phẩm thuộc 1 Thương hiệu
    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    // [PIVOT] Sản phẩm có thể thuộc NHIỀU Danh mục
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'product_categories', 'product_id', 'category_id')
                    ->withTimestamps();
    }

    // Sản phẩm có nhiều Biến thể (Variants)
    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class, 'product_id');
    }

    // Sản phẩm có nhiều Ảnh (Gallery)
    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }
}