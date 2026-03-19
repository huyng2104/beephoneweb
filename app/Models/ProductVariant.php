<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ProductVariant extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'product_id', 'sku', 'price', 'sale_price', 'stock', 'thumbnail', 'status'
    ];

    // Biến thể này thuộc về Sản phẩm nào?
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    // [PIVOT] Biến thể này sở hữu những Giá trị thuộc tính nào? (Đỏ, 128GB...)
    public function attributeValues(): BelongsToMany
    {
        return $this->belongsToMany(AttributeValue::class, 'variant_attribute_values', 'variant_id', 'attribute_value_id')
                    ->withTimestamps();
    }

    
}