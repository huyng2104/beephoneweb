<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'user_id',
        'parent_id',
        'guest_name',
        'guest_email',
        'content',
        'status',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function parent()
    {
        return $this->belongsTo(ProductComment::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(ProductComment::class, 'parent_id');
    }
}
