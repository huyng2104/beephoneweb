<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
class Product extends Model
{
    use SoftDeletes;
    use LogsActivity;
    protected $fillable = [
        'name', 'slug', 'description', 'type', 'status', 'is_featured', 'brand_id', 'thumbnail','sku'
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($product) {
            if (empty($product->slug) || $product->isDirty('name')) {
                $baseSlug = \Illuminate\Support\Str::slug($product->name);
                $slug = $baseSlug;
                $count = 1;

                // Ensure slug uniqueness (except for current model)
                while (static::where('slug', $slug)->where('id', '!=', $product->id)->exists()) {
                    $slug = $baseSlug . '-' . $count++;
                }

                $product->slug = $slug;
            }
        });
    }

    /**
     * Use 'id' for route model binding (Ensure admin/products/{id} works).
     */
    public function getRouteKeyName()
    {
        return 'id';
    }

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
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->useLogName('product')
            ->logOnlyDirty();
    }
}
