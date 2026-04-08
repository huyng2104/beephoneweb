<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Models\ReviewImage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ReviewController extends Controller
{
    /**
     * Gửi đánh giá mới cho sản phẩm.
     */
    public function store(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'rating'  => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['required', 'string', 'min:10', 'max:3000'],
            'images'  => ['nullable', 'array', 'max:5'],
            'images.*'=> ['image', 'mimes:jpeg,png,jpg,webp', 'max:4096'],
        ], [
            'rating.required'  => 'Vui lòng chọn số sao.',
            'comment.required' => 'Vui lòng nhập nội dung nhận xét.',
            'comment.min'      => 'Nhận xét phải có ít nhất 10 ký tự.',
        ]);

        // Kiểm tra đã mua hàng chưa — bắt buộc phải mua mới được review
        $user = $request->user();
        $isPurchased = false;
        if ($user) {
            $isPurchased = Order::query()
                ->where('user_id', $user->id)
                ->where('status', Order::STATUS_RECEIVED)
                ->whereHas('items', fn ($q) => $q->where('product_id', $product->id))
                ->exists();
        }

        if (!$isPurchased) {
            return back()->withErrors(['review' => 'Bạn cần mua và nhận hàng thành công để có thể đánh giá sản phẩm này.']);
        }

        // Kiểm tra đã review sản phẩm này chưa (1 user = 1 review/sản phẩm)
        if ($user) {
            $alreadyReviewed = Review::where('user_id', $user->id)
                ->where('product_id', $product->id)
                ->exists();

            if ($alreadyReviewed) {
                return back()->withErrors(['review' => 'Bạn đã đánh giá sản phẩm này rồi.']);
            }
        }

        $review = Review::create([
            'user_id'      => $user?->id,
            'product_id'   => $product->id,
            'rating'       => $validated['rating'],
            'comment'      => $validated['comment'],
            'status'       => Review::STATUS_PENDING, // chờ Admin duyệt
            'is_purchased' => $isPurchased,
        ]);

        // Lưu ảnh đính kèm
        if ($request->hasFile('images')) {
            $order = 0;
            foreach ($request->file('images') as $image) {
                $path = $image->store('reviews', 'public');
                ReviewImage::create([
                    'review_id'  => $review->id,
                    'image_path' => $path,
                    'sort_order' => $order++,
                ]);
            }
        }

        return back()->with('success', 'Cảm ơn bạn đã đánh giá! Đánh giá của bạn đang chờ duyệt.');
    }

    /**
     * Bình chọn "Hữu ích" cho một đánh giá (AJAX).
     */
    public function helpful(Request $request, Review $review): JsonResponse
    {
        // Chỉ cho bình chọn review đã được duyệt
        if (!$review->isApproved()) {
            return response()->json(['ok' => false, 'message' => 'Không tìm thấy đánh giá.'], 404);
        }

        $sessionKey = 'liked_review_' . $review->id;
        
        // Nếu đã like rồi thì chặn (chống spam F5 click lại)
        if ($request->session()->has($sessionKey)) {
            return response()->json(['ok' => false, 'message' => 'Bạn đã đánh giá hữu ích cho mục này rồi.']);
        }

        // Tăng đếm và lưu session
        $review->increment('helpful_count');
        $request->session()->put($sessionKey, true);

        return response()->json([
            'ok'    => true,
            'count' => $review->helpful_count,
        ]);
    }

    /**
     * Khách hàng xóa đánh giá của chính mình.
     */
    public function destroy(Request $request, Review $review): RedirectResponse
    {
        // Kiểm tra quyền sở hữu
        if ($review->user_id !== $request->user()->id) {
            abort(403, 'Bạn không có quyền xóa đánh giá này.');
        }

        // Kiểm tra thời gian (chỉ cho phép xóa trong vòng 15 phút)
        if ($review->created_at->diffInMinutes(now()) > 15) {
            return back()->with('error', 'Bạn chỉ có thể xóa đánh giá trong vòng 15 phút sau khi đăng.');
        }

        // Kiểm tra trạng thái duyệt (đã duyệt không cho xóa)
        if ($review->isApproved()) {
            return back()->with('error', 'Đánh giá đã được phê duyệt không thể xóa.');
        }

        // Xóa ảnh đính kèm khỏi storage
        foreach ($review->images as $image) {
            Storage::disk('public')->delete($image->image_path);
        }

        $review->delete();

        return back()->with('success', 'Đã xóa đánh giá của bạn.');
    }
}
