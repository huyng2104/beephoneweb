<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Comment;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function show(Product $product)
    {
        $product->load(['images']);

        // Load top-level comments with user and children
        $comments = Comment::with(['user', 'children.user'])
            ->where('product_id', $product->id)
            ->whereNull('parent_id')
            ->orderBy('created_at', 'desc')
            ->get();

        $ratedComments = $comments->whereNotNull('rating');
        $totalRatings = $ratedComments->count();
        $averageRating = $totalRatings > 0 ? round((float) $ratedComments->avg('rating'), 1) : 0;
        $ratingBreakdown = collect(range(5, 1))
            ->mapWithKeys(fn (int $star) => [$star => $ratedComments->where('rating', $star)->count()]);

        return view('product.show', compact(
            'product',
            'comments',
            'totalRatings',
            'averageRating',
            'ratingBreakdown',
        ));
    }
}
