<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\ProductComment;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductCommentController extends Controller
{
    public function store(Request $request, Product $product)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
            'parent_id' => 'nullable|exists:product_comments,id',
        ]);

        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để bình luận!');
        }

        $commentData = [
            'product_id' => $product->id,
            'content' => $request->content,
            'parent_id' => $request->parent_id,
            'status' => 1, // Default to approved
            'user_id' => Auth::id(),
        ];

        ProductComment::create($commentData);

        return redirect()->back()->with('success', 'Đã gửi câu hỏi / bình luận thành công!');
    }

    public function destroy(ProductComment $comment)
    {
        $user = auth()->user();
        $isAdmin = $user && $user->role && $user->role->name === 'admin';

        if ($user->id !== $comment->user_id && !$isAdmin) {
            return redirect()->back()->with('error', 'Bạn không có quyền xóa bình luận này!');
        }

        $comment->delete();

        return redirect()->back()->with('success', 'Đã xóa bình luận thành công!');
    }
}
