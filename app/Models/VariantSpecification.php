<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VariantSpecification extends Model
{
    protected $fillable = ['variant_id', 'spec_key', 'spec_value'];

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }
}
