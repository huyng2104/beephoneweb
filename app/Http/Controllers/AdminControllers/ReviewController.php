<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function index(Request $request): View
    {
        $query = Comment::with([
                'user.role',
                'product',
                'children' => fn ($q) => $q->with(['user.role'])->latest(),
            ])
            ->whereNull('parent_id')
            ->whereNotNull('rating')
            ->where('verified_purchase', true)
            ->latest();

        if ($request->filled('product_id')) {
            $query->where('product_id', (int) $request->input('product_id'));
        }

        $reviews = $query->get();

        $visibleReviews = $reviews->where('is_hidden', false);
        $totalRatings = $visibleReviews->count();
        $averageRating = $totalRatings > 0 ? round((float) $visibleReviews->avg('rating'), 1) : 0;

        $ratingBreakdown = collect(range(5, 1))
            ->mapWithKeys(fn (int $star) => [$star => $visibleReviews->where('rating', $star)->count()]);

        return view('admin.reviews.index', compact(
            'reviews',
            'totalRatings',
            'averageRating',
            'ratingBreakdown',
        ));
    }
}
