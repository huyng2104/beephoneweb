<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Support\Facades\Gate;

class CommentController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('comment.view');
        $query = Comment::with(['user.role', 'product', 'parent'])->latest();

        if ($request->filled('product_id')) {
            $query->where('product_id', (int) $request->input('product_id'));
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

    public function destroy(Comment $comment): RedirectResponse
    {
        Gate::authorize('comment.delete');
        $comment->deleteWithChildren();

        return back()->with('success', 'Da xoa comment thanh cong.');
    }

    public function reply(Request $request, Comment $comment): RedirectResponse
    {
        Gate::authorize('comment.reply');
        $validated = $request->validate([
            'content' => ['required', 'string', 'max:2000'],
        ], [
            'content.required' => 'Vui lòng nhập nội dung trả lời.',
        ]);

        Comment::create([
            'product_id' => $comment->product_id,
            'user_id' => Auth::id(),
            'parent_id' => $comment->id,
            'rating' => null,
            'content' => $validated['content'],
            'guest_name' => null,
            'guest_email' => null,
            'image_path' => null,
        ]);

        return back()->with('success', 'Đã trả lời comment.');
    }
}
