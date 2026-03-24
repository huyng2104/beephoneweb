<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Banner extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'title',
        'link',
        'type',
        'image_url',
        'is_active',
        'sort_order',
    ];
}
