<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use App\Models\ProductComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductCommentController extends Controller
{
    public function index(Request $request)
    {
        $query = ProductComment::with(['product', 'user', 'replies.user'])->whereNull('parent_id');

        // Lọc theo trạng thái nếu có
        $status = $request->input('status');
        if ($status) {
            $query->where('status', $status);
        }

        $comments = $query->latest()->paginate(15);

        // Stats
        $stats = [
            'total' => ProductComment::whereNull('parent_id')->count(),
            'approved' => ProductComment::whereNull('parent_id')->where('status', 1)->count(),
            'hidden' => ProductComment::whereNull('parent_id')->where('status', 2)->count(),
        ];

        return view('admin.comments.index', compact('comments', 'stats'));
    }

    public function reply(Request $request, $id)
    {
        $request->validate([
            'content' => 'required|string|max:1000'
        ]);

        $parentComment = ProductComment::findOrFail($id);

        ProductComment::create([
            'product_id' => $parentComment->product_id,
            'user_id' => Auth::id(), // Admin
            'parent_id' => $parentComment->id,
            'content' => $request->content,
            'status' => 1 // Active
        ]);

        return redirect()->back()->with('success', 'Đã phản hồi bình luận.');
    }

    public function toggleStatus($id)
    {
        $comment = ProductComment::findOrFail($id);
        
        // 1 = active, 2 = hidden
        $comment->status = $comment->status == 1 ? 2 : 1;
        $comment->save();

        // Also update all its replies status if we are hiding the parent maybe? 
        // For simplicity, just update the target comment.
        if ($comment->status == 2 && $comment->parent_id == null) {
             ProductComment::where('parent_id', $comment->id)->update(['status' => 2]);
        } else if ($comment->status == 1 && $comment->parent_id == null) {
             ProductComment::where('parent_id', $comment->id)->update(['status' => 1]);
        }

        $msg = $comment->status == 1 ? 'Đã hiển thị bình luận.' : 'Đã ẩn bình luận.';
        return redirect()->back()->with('success', $msg);
    }

    public function destroy($id)
    {
        $comment = ProductComment::findOrFail($id);
        $comment->delete();
        // cascade delete will handle replies
        return redirect()->back()->with('success', 'Đã xóa bình luận.');
    }
}
