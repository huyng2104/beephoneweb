<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Comment extends Model
{
    protected $fillable = [
        'product_id',
        'user_id',
        'parent_id',
        'rating',
        'content',
        'guest_name',
        'guest_email',
        'image_path',
        'verified_purchase',
        'is_hidden',
    ];

    protected $casts = [
        'verified_purchase' => 'bool',
        'is_hidden' => 'bool',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }

    public function deleteWithChildren(): void
    {
        $this->loadMissing('children');

        foreach ($this->children as $child) {
            $child->deleteWithChildren();
        }

        if ($this->image_path) {
            Storage::disk('public')->delete($this->image_path);
        }

        $this->delete();
    }

    public function setHiddenWithChildren(bool $hidden): void
    {
        $this->loadMissing('children');

        foreach ($this->children as $child) {
            $child->setHiddenWithChildren($hidden);
        }

        $this->is_hidden = $hidden;
        $this->save();
    }
}
