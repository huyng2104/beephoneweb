<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\PostCategory;

class PostController extends Controller
{
    // public function index()
    // {
    //     return view('client.posts.index'); // sửa đúng view của bạn
    // }

    public function index(Request $request)
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

        $query = Post::with(['category', 'user'])
            ->where('status', 1);

        // 👉 lọc theo danh mục
        if ($request->category) {
            $query->where('post_categories_id', $request->category);
        }

        $posts = $query->latest()->paginate(6);

        // giữ query khi phân trang
        $posts->appends($request->all());

        // danh mục
        $categories = PostCategory::all();


        return view('client.posts.index', compact('posts', 'featuredPost', 'mostViewed', 'categories'));
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
