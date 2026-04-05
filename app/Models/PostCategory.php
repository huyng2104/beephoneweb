<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
class PostCategory extends Model
{
        use LogsActivity;

    protected $fillable = [
        'name',
        'slug',
        'status'
    ];

    public function posts()
    {
        return $this->hasMany(Post::class, 'post_categories_id');
    }
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->useLogName('post category')
            ->logOnlyDirty();
    }
}
