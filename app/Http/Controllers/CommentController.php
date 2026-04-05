<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
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
            'order_id' => ['nullable', 'integer', 'exists:orders,id'],
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

        $verifiedPurchase = false;
        $orderId = $validated['order_id'] ?? null;
        $orderIdInt = is_numeric($orderId) ? (int) $orderId : null;
        if ($orderIdInt && $user && empty($validated['parent_id'])) {
            $order = Order::query()
                ->with('items')
                ->where('id', $orderIdInt)
                ->where('user_id', $user->id)
                ->where('status', Order::STATUS_RECEIVED)
                ->first();

            if ($order) {
                $verifiedPurchase = $order->items->contains(fn ($item) => (int) $item->product_id === (int) $product->id);
            }
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
            'verified_purchase' => $verifiedPurchase,
            'is_hidden' => false,
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

    public function destroy(Request $request, Comment $comment): RedirectResponse|JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            abort(403);
        }

        $roleNameRaw = $user->role?->name ?? $user->role_slug ?? null;
        $roleName = is_string($roleNameRaw) ? strtolower(trim($roleNameRaw)) : null;
        $isAdmin = in_array($roleName, ['admin', 'staff'], true);
        $isOwner = $comment->user_id !== null && (int) $comment->user_id === (int) $user->id;

        if (!app()->environment('local') && !$isAdmin && !$isOwner) {
            abort(403);
        }

        $comment->deleteWithChildren();

        if ($request->expectsJson()) {
            return response()->json(['ok' => true]);
        }

        return back()->with('success', 'Da xoa comment thanh cong.');
    }
}
