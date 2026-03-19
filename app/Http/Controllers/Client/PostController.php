<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PostController extends Controller
{
    // public function index()
    // {
    //     return view('client.posts.index'); // sửa đúng view của bạn
    // }

    public function index()
    {
        // bài viết mới nhất (phân trang)
        $posts = \App\Models\Post::with(['category', 'user'])
            ->where('status', 1)
            ->latest()
            ->paginate(6);

        // bài nổi bật (lấy 1 bài đầu)
        $featuredPost = \App\Models\Post::where('status', 1)
            ->latest()
            ->first();

        // bài xem nhiều
        $mostViewed = \App\Models\Post::orderBy('views', 'desc')
            ->take(3)
            ->get();

        return view('client.posts.index', compact('posts', 'featuredPost', 'mostViewed'));
    }

    public function show($slug)
    {
        $post = \App\Models\Post::with(['category', 'user'])
            ->where('slug', $slug)
            ->firstOrFail();

        // bài liên quan
        $relatedPosts = \App\Models\Post::where('post_categories_id', $post->post_categories_id)
            ->where('id', '!=', $post->id)
            ->latest()
            ->take(3)
            ->get();

        return view('client.posts.show', compact('post', 'relatedPosts'));
    }
}
