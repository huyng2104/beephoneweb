<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CommentController extends Controller
{
    public function index(Request $request): View
    {
        $query = Comment::with(['user', 'product', 'parent'])->latest();

        $productId = $request->integer('product_id');
        if ($productId) {
            $query->where('product_id', $productId);
        }

        $comments = $query->get();

        $ratedComments = $comments->whereNotNull('rating');
        $totalRatings = $ratedComments->count();
        $averageRating = $totalRatings > 0 ? round((float) $ratedComments->avg('rating'), 1) : 0;

        $ratingBreakdown = collect(range(5, 1))
            ->mapWithKeys(fn (int $star) => [$star => $ratedComments->where('rating', $star)->count()]);

        return view('admin.comments.index', compact(
            'comments',
            'totalRatings',
            'averageRating',
            'ratingBreakdown',
        ));
    }

    public function show(Comment $comment): View
    {
        $comment->load([
            'user',
            'product',
            'parent.user',
            'parent.product',
            'children.user',
            'children.product',
        ]);

        return view('admin.comments.show', compact('comment'));
    }

    public function destroy(Comment $comment): RedirectResponse
    {
        $comment->deleteWithChildren();

        return back()->with('success', 'Comment deleted successfully.');
    }
}
