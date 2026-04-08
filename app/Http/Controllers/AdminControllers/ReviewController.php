<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\ReviewImage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ReviewController extends Controller
{
    /**
     * Danh sách tất cả đánh giá (có lọc, tìm kiếm, phân trang).
     */
    public function index(Request $request): View
    {
        $query = Review::with(['user', 'product', 'images', 'repliedBy']);

        // Xử lý sắp xếp
        $sort = $request->input('sort', 'newest');
        if ($sort === 'oldest') {
            $query->oldest();
        } elseif ($sort === 'likes') {
            $query->orderByDesc('helpful_count')->latest();
        } else {
            $query->latest();
        }

        // Lọc theo trạng thái
        if ($request->has('status') && $request->status !== '') {
            $status = (int) $request->status;
            if (in_array($status, [0, 1, 2], true)) {
                $query->where('status', $status);
            }
        }

        // Lọc theo số sao
        if ($request->filled('rating') && in_array((int) $request->rating, range(1, 5), true)) {
            $query->where('rating', (int) $request->rating);
        }

        // Lọc "Đã mua hàng"
        if ($request->filled('is_purchased')) {
            $query->where('is_purchased', (bool) $request->is_purchased);
        }

        // Tìm kiếm theo tên sản phẩm hoặc nội dung
        if ($request->filled('search')) {
            $kw = $request->search;
            $query->where(function ($q) use ($kw) {
                $q->where('comment', 'like', "%{$kw}%")
                  ->orWhereHas('product', fn ($p) => $p->where('name', 'like', "%{$kw}%"))
                  ->orWhereHas('user',    fn ($u) => $u->where('name', 'like', "%{$kw}%"));
            });
        }

        $reviews = $query->paginate(15)->withQueryString();

        // Thống kê nhanh
        $stats = [
            'total'    => Review::count(),
            'pending'  => Review::where('status', Review::STATUS_PENDING)->count(),
            'approved' => Review::where('status', Review::STATUS_APPROVED)->count(),
            'hidden'   => Review::where('status', Review::STATUS_HIDDEN)->count(),
            'average'  => round((float) Review::where('status', Review::STATUS_APPROVED)->avg('rating'), 1),
        ];

        $ratingBreakdown = collect(range(5, 1))
            ->mapWithKeys(fn (int $star) => [
                $star => Review::where('status', Review::STATUS_APPROVED)
                               ->where('rating', $star)
                               ->count(),
            ]);

        return view('admin.reviews.index', compact('reviews', 'stats', 'ratingBreakdown'));
    }

    /**
     * Duyệt đánh giá (status → 1).
     */
    public function approve(Review $review): RedirectResponse|JsonResponse
    {
        $review->update(['status' => Review::STATUS_APPROVED]);

        if (request()->expectsJson()) {
            return response()->json(['ok' => true, 'status' => Review::STATUS_APPROVED]);
        }

        return back()->with('success', 'Đã duyệt đánh giá.');
    }

    /**
     * Ẩn đánh giá (status → 2).
     */
    public function hide(Review $review): RedirectResponse|JsonResponse
    {
        $review->update(['status' => Review::STATUS_HIDDEN]);

        if (request()->expectsJson()) {
            return response()->json(['ok' => true, 'status' => Review::STATUS_HIDDEN]);
        }

        return back()->with('success', 'Đã ẩn đánh giá.');
    }

    /**
     * Chuyển về chờ duyệt (status → 0).
     */
    public function pending(Review $review): RedirectResponse|JsonResponse
    {
        $review->update(['status' => Review::STATUS_PENDING]);

        if (request()->expectsJson()) {
            return response()->json(['ok' => true, 'status' => Review::STATUS_PENDING]);
        }

        return back()->with('success', 'Đã chuyển đánh giá về chờ duyệt.');
    }

    /**
     * Admin phản hồi đánh giá.
     */
    public function reply(Request $request, Review $review): RedirectResponse
    {
        $request->validate([
            'reply_comment' => ['required', 'string', 'max:2000'],
        ], [
            'reply_comment.required' => 'Vui lòng nhập nội dung phản hồi.',
        ]);

        $review->update([
            'reply_comment' => $request->reply_comment,
            'replied_by'    => $request->user()->id,
            'replied_at'    => now(),
        ]);

        return back()->with('success', 'Đã gửi phản hồi cho đánh giá.');
    }

    /**
     * Xóa phản hồi của admin.
     */
    public function deleteReply(Review $review): RedirectResponse
    {
        $review->update([
            'reply_comment' => null,
            'replied_by'    => null,
            'replied_at'    => null,
        ]);

        return back()->with('success', 'Đã xóa phản hồi.');
    }

    /**
     * Xóa đánh giá và ảnh đính kèm.
     */
    public function destroy(Review $review): RedirectResponse|JsonResponse
    {
        // Xóa ảnh khỏi storage
        foreach ($review->images as $image) {
            Storage::disk('public')->delete($image->image_path);
        }

        $review->delete(); // cascade xóa review_images qua DB

        if (request()->expectsJson()) {
            return response()->json(['ok' => true]);
        }

        return back()->with('success', 'Đã xóa đánh giá.');
    }
}
