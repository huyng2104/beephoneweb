<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use App\Models\PostCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Gate;

class PostCategoryController extends Controller
{

    public function index(Request $request)
    {
        Gate::authorize('post_category.view');
        $query = PostCategory::query();

        // tìm kiếm theo tên danh mục
        if ($request->keyword) {
            $query->where('name', 'like', '%' . $request->keyword . '%');
        }

        $categories = $query->latest()->paginate(10);

        // thống kê
        $totalCategories = PostCategory::count();

        $topCategory = PostCategory::withCount('posts')
            ->orderByDesc('posts_count')
            ->first();

        $newCategoriesThisMonth = PostCategory::whereMonth('created_at', now()->month)
            ->count();

        return view('admin.post-categories.index', compact(
            'categories',
            'totalCategories',
            'topCategory',
            'newCategoriesThisMonth'
        ));
    }

    public function create()
    {
        Gate::authorize('post_category.create');
        return view('admin.post-categories.create');
    }

    public function store(Request $request)
    {
        Gate::authorize('post_category.create');
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
        Gate::authorize('post_category.update');
        $category = PostCategory::findOrFail($id);

        return view('admin.post-categories.edit', compact('category'));
    }

    public function update(Request $request, $id)
    {
        Gate::authorize('post_category.update');
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
        Gate::authorize('post_category.delete');
        $category = PostCategory::findOrFail($id);

        $category->delete();

        return redirect()
            ->route('admin.post-categories.index')
            ->with('success', 'Xóa danh mục thành công!');
    }
}
