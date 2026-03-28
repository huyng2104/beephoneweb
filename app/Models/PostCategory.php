<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PostCategory extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'status'
    ];

    protected $dates = ['deleted_at'];

    public function posts()
    {
        return $this->hasMany(Post::class, 'post_categories_id');
    }
}
