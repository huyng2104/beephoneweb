<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * Store a new comment for a product.
     * Supports guest comments (no login required) for local dev/testing.
     */
    public function store(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'parent_id' => ['nullable', 'integer', 'exists:comments,id'],
            'rating' => ['nullable', 'integer', 'min:1', 'max:5'],
            'content' => ['required', 'string', 'max:2000'],
            'guest_name' => ['nullable', 'string', 'max:255'],
            'guest_email' => ['nullable', 'email', 'max:255'],
            'image' => ['nullable', 'image', 'max:5120'], // 5MB
        ]);

        $user = $request->user();

        // If guest: require name/email so admin can manage and display nicely.
        if (!$user) {
            $request->validate([
                'guest_name' => ['required', 'string', 'max:255'],
                'guest_email' => ['required', 'email', 'max:255'],
            ]);
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('comments', 'public');
        }

        Comment::create([
            'product_id' => $product->id,
            'user_id' => $user?->id,
            'parent_id' => $validated['parent_id'] ?? null,
            'rating' => $validated['rating'] ?? null,
            'content' => $validated['content'],
            'guest_name' => $user ? null : $request->input('guest_name'),
            'guest_email' => $user ? null : $request->input('guest_email'),
            'image_path' => $imagePath,
        ]);

        $redirectTo = $request->input('redirect_to');
        if (is_string($redirectTo) && $redirectTo !== '') {
            $appUrl = rtrim((string) config('app.url'), '/');

            // Allow only on-site redirects (absolute under app.url) or relative paths.
            if (str_starts_with($redirectTo, '/')) {
                return redirect($redirectTo)->with('success', 'Da gui comment thanh cong.');
            }

            if ($appUrl !== '' && str_starts_with($redirectTo, $appUrl . '/')) {
                $path = substr($redirectTo, strlen($appUrl));
                $path = $path === '' ? '/' : $path;

                return redirect($path)->with('success', 'Da gui comment thanh cong.');
            }
        }

        return back()->with('success', 'Da gui comment thanh cong.')->withFragment('comments');
    }
}
