<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use App\Models\PostCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PostCategoryController extends Controller
{

    public function index()
    {
        $categories = PostCategory::latest()->get();

        return view('admin.post-categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.post-categories.create');
    }

    public function store(Request $request)
    {

        PostCategory::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            // 'status' => $request->status
            'status' => 1
        ]);

        return redirect()
            ->route('admin.post-categories.index')
            ->with('success', 'Thêm danh mục thành công!');
    }

    public function edit($id)
    {
        $category = PostCategory::findOrFail($id);

        return view('admin.post-categories.edit', compact('category'));
    }

    public function update(Request $request, $id)
    {

        $category = PostCategory::findOrFail($id);

        $category->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'status' => $request->status ?? 1
        ]);

        return redirect()
            ->route('admin.post-categories.index')
            ->with('success', 'Cập nhật danh mục thành công!');
    }

    public function destroy($id)
    {

        $category = PostCategory::findOrFail($id);

        $category->delete();

        return redirect()
            ->route('admin.post-categories.index')
            ->with('success', 'Xóa danh mục thành công!');
    }
}
