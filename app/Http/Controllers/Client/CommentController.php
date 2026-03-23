<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store(Request $request, string $slug): RedirectResponse
    {
        $product = Product::query()
            ->where('slug', $slug)
            ->orWhere('id', $slug)
            ->firstOrFail();

        $validated = $request->validate([
            'content' => ['required', 'string', 'min:3', 'max:2000'],
            'rating' => ['nullable', 'integer', 'min:1', 'max:5'],
            'parent_id' => ['nullable', 'integer', 'exists:comments,id'],
            'guest_name' => ['nullable', 'string', 'max:255'],
            'guest_email' => ['nullable', 'email', 'max:255'],
        ]);

        $parentId = $request->integer('parent_id');
        if ($parentId) {
            $parent = Comment::query()->whereKey($parentId)->first();
            if (!$parent || (int) $parent->product_id !== (int) $product->id) {
                return back()->withErrors(['parent_id' => 'Invalid parent comment.'])->withInput();
            }
        }

        $userId = Auth::id();
        if (!$userId) {
            $request->validate([
                'guest_name' => ['required', 'string', 'max:255'],
                'guest_email' => ['required', 'email', 'max:255'],
            ]);
        }

        Comment::create([
            'product_id' => $product->id,
            'user_id' => $userId,
            'guest_name' => $userId ? null : $validated['guest_name'],
            'guest_email' => $userId ? null : $validated['guest_email'],
            'parent_id' => $parentId ?: null,
            'content' => $validated['content'],
            'rating' => $parentId ? null : ($validated['rating'] ?? null),
        ]);

        return back()->with('success', 'Comment posted successfully.');
    }
}

