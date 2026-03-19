<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;

class PostController extends Controller
{
    // public function index()
    // {
    //     return view('client.posts.index'); // sửa đúng view của bạn
    // }

    public function index()
    {
        // bài viết mới nhất (phân trang)
        $posts = Post::with(['category', 'user'])
            ->where('status', 1)
            ->latest()
            ->paginate(6);

        // bài nổi bật (lấy 1 bài đầu)
        $featuredPost = Post::where('status', 1)
            ->latest()
            ->first();

        // bài xem nhiều
        $mostViewed = Post::orderBy('views', 'desc')
            ->take(3)
            ->get();

        return view('client.posts.index', compact('posts', 'featuredPost', 'mostViewed'));
    }

    public function show($slug)
    {
        $post = Post::with(['category', 'user'])
            ->where('slug', $slug)
            ->firstOrFail();

        // bài liên quan
        $relatedPosts = Post::where('post_categories_id', $post->post_categories_id)
            ->where('id', '!=', $post->id)
            ->latest()
            ->take(3)
            ->get();

        $post = Post::where('slug', $slug)->firstOrFail();

        // Tăng lượt xem
        $post->increment('views');

        return view('client.posts.show', compact('post', 'relatedPosts'));
    }
}
