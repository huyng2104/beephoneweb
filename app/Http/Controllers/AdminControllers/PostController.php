<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\PostCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PostController extends Controller
{

    public function index()
    {
        $posts = Post::with(['category', 'user'])->latest()->get();

        return view('admin.posts.index', compact('posts'));
    }

    public function create()
    {
        $categories = PostCategory::all();

        return view('admin.posts.create', compact('categories'));
    }

    public function store(Request $request)
    {

        $request->validate([
            'title' => 'required',
            'thumbnail' => 'required|image',
            'content' => 'required',
            'post_categories_id' => 'required'
        ]);

        $thumbnail = null;

        if ($request->hasFile('thumbnail')) {
            $file = $request->file('thumbnail');
            $name = time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/posts'), $name);
            $thumbnail = $name;
        }

        Post::create([
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'thumbnail' => $thumbnail,
            'content' => $request->content,
            'post_categories_id' => $request->post_categories_id,
            // 'user_id' => Auth::id(),
            'user_id' => 1,
            'views' => 0,
            'status' => 1
        ]);

        return redirect()
            ->route('admin.posts.index')
            ->with('success', 'Thêm bài viết thành công!');
    }

    public function edit($id)
    {
        $post = Post::findOrFail($id);

        $categories = PostCategory::all();

        return view('admin.posts.edit', compact('post', 'categories'));
    }

    // public function update(Request $request, $id)
    // {

    //     $post = Post::findOrFail($id);

    //     $thumbnail = $post->thumbnail;

    //     if ($request->hasFile('thumbnail')) {

    //         $file = $request->file('thumbnail');

    //         $name = time() . '.' . $file->getClientOriginalExtension();

    //         $file->move(public_path('uploads/posts'), $name);

    //         $thumbnail = $name;
    //     }

    //     $post->update([
    //         'title' => $request->title,
    //         'slug' => Str::slug($request->title),
    //         'thumbnail' => $thumbnail,
    //         'content' => $request->content,
    //         'post_categories_id' => $request->post_categories_id,
    //         'status' => $request->status
    //     ]);

    //     return redirect()
    //         ->route('admin.posts.index')
    //         ->with('success', 'Cập nhật bài viết thành công!');
    // }

    // public function update(Request $request, Post $post)
    // {
    //     $request->validate([
    //         'title' => 'required',
    //         'content' => 'required',
    //         'post_categories_id' => 'required',
    //         'thumbnail' => 'nullable|image|mimes:jpg,png,jpeg,webp|max:2048'
    //     ]);

    //     $data = $request->all();

    //     if ($request->hasFile('thumbnail')) {

    //         if ($post->thumbnail && file_exists(public_path('storage/' . $post->thumbnail))) {
    //             unlink(public_path('storage/' . $post->thumbnail));
    //         }

    //         $thumbnail = $request->file('thumbnail')->store('posts', 'public');
    //         $data['thumbnail'] = $thumbnail;
    //     }

    //     $post->update($data);

    //     return redirect()
    //         ->route('admin.posts.index')
    //         ->with('success', 'Cập nhật bài viết thành công');
    // }

    public function update(Request $request, $id)
    {
        $post = \App\Models\Post::findOrFail($id);

        $thumbnail = $post->thumbnail;

        if ($request->hasFile('thumbnail')) {

            // xóa ảnh cũ
            if ($post->thumbnail && file_exists(public_path('uploads/posts/' . $post->thumbnail))) {
                unlink(public_path('uploads/posts/' . $post->thumbnail));
            }

            $file = $request->file('thumbnail');

            $name = time() . '.' . $file->getClientOriginalExtension();

            $file->move(public_path('uploads/posts'), $name);

            $thumbnail = $name;
        }

        $post->update([
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'thumbnail' => $thumbnail,
            'content' => $request->content,
            'post_categories_id' => $request->post_categories_id,
            'status' => $request->status
        ]);

        return redirect()
            ->route('admin.posts.index')
            ->with('success', 'Cập nhật bài viết thành công');
    }

    public function destroy($id)
    {
        $post = Post::findOrFail($id);

        if ($post->thumbnail && file_exists(public_path('uploads/posts/' . $post->thumbnail))) {

            unlink(public_path('uploads/posts/' . $post->thumbnail));
        }

        $post->delete();

        return redirect()
            ->route('admin.posts.index')
            ->with('success', 'Xóa bài viết thành công!');
    }
}
