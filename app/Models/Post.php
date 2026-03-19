<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use SoftDeletes;

    protected $dates = ['delete_at'];

    protected $fillable = [
        'title',
        'slug',
        'thumbnail',
        'content',
        'post_categories_id',
        'user_id',
        'views',
        'status'
    ];

    public function category()
    {
        return $this->belongsTo(PostCategory::class, 'post_categories_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
