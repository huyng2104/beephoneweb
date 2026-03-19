<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Product;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request, Product $product)
    {
        $rules = [
            'content' => 'required|string|max:2000',
            'rating' => 'nullable|integer|min:1|max:5',
            'parent_id' => 'nullable|exists:comments,id',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ];

        if (!$request->user()) {
            $rules['guest_name'] = 'required|string|max:255';
            $rules['guest_email'] = 'nullable|email|max:255';
        }

        $data = $request->validate($rules);

        $data['user_id'] = $request->user()?->id;
        $data['product_id'] = $product->id;

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('comments', 'public');
        }

        Comment::create($data);

        return back();
    }

    public function destroy(Request $request, Comment $comment)
    {
        // Only admin can delete
        if ($request->user() && $request->user()->role === 'admin') {
            $comment->deleteWithChildren();
            return back();
        }

        abort(403);
    }
}
