<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\PostCategory;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $query = Post::with(['category', 'user'])
            ->where('status', 1);

        // lọc theo danh mục
        if ($request->category) {
            $query->where('post_categories_id', $request->category);
        }

        // bài viết
        // $posts = $query->latest()->paginate(6);
        // $posts->appends($request->all());
        $posts = $query->latest()
            ->paginate(4)
            ->withQueryString();

        // bài nổi bật
        $featuredPost = Post::where('status', 1)
            ->latest()
            ->first();

        // bài xem nhiều
        $mostViewed = Post::where('status', 1)
            ->orderByDesc('views')
            ->take(3)
            ->get();

        // danh mục
        $categories = PostCategory::all();

        return view('client.posts.index', compact(
            'posts',
            'featuredPost',
            'mostViewed',
            'categories'
        ));
    }

    public function show($slug)
    {
        $post = Post::with(['category', 'user'])
            ->where('slug', $slug)
            ->where('status', 1) // ✅ tránh truy cập bài bị ẩn
            ->firstOrFail();

        // tăng view
        $post->increment('views');

        // bài liên quan
        $relatedPosts = Post::where('post_categories_id', $post->post_categories_id)
            ->where('id', '!=', $post->id)
            ->where('status', 1)
            ->latest()
            ->take(3)
            ->get();

        // bài xem nhiều
        $mostViewed = Post::where('status', 1)
            ->orderByDesc('views')
            ->take(5)
            ->get();

        return view('client.posts.show', compact('post', 'relatedPosts', 'mostViewed'));
    }
}
