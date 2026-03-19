<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CommentController extends Controller
{
    public function index(): View
    {
        $comments = Comment::with(['user', 'product', 'parent'])
            ->latest()
            ->get();

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

    public function destroy(Comment $comment): RedirectResponse
    {
        $comment->deleteWithChildren();

        return back()->with('success', 'Da xoa comment thanh cong.');
    }
}
