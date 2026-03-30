<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
class BannerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $banners = Banner::orderBy('sort_order', 'asc')->get();
        return view('admin.banners.index', compact('banners'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.banners.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'type' => 'required',
            'title' => 'nullable|string|max:255',
        ]);

        $imageUrl = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $name = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/banners'), $name);
            $imageUrl = asset('uploads/banners/' . $name);
        }

        Banner::create([
            'title' => $request->title,
            'link' => $request->link,
            'type' => $request->type,
            'image_url' => $imageUrl,
            'is_active' => $request->has('is_active'),
            'sort_order' => $request->sort_order ?? 0,
        ]);

        return redirect()->route('admin.banners.index')->with('success', 'Thêm banner thành công!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Banner $banner)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Banner $banner)
    {
        return view('admin.banners.edit', compact('banner'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Banner $banner)
    {
        $request->validate([
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'type' => 'required',
            'title' => 'nullable|string|max:255',
        ]);

        $imageUrl = $banner->image_url;
        if ($request->hasFile('image')) {
            // Delete old file if exists
            if ($banner->image_url) {
                $oldPath = public_path(str_replace(asset(''), '', $banner->image_url));
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }

            $file = $request->file('image');
            $name = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/banners'), $name);
            $imageUrl = asset('uploads/banners/' . $name);
        }

        $banner->update([
            'title' => $request->title,
            'link' => $request->link,
            'type' => $request->type,
            'image_url' => $imageUrl,
            'is_active' => $request->has('is_active'),
            'sort_order' => $request->sort_order ?? 0,
        ]);

        return redirect()->route('admin.banners.index')->with('success', 'Cập nhật banner thành công!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Banner $banner)
    {
        $banner->delete();
        return redirect()->route('admin.banners.index')->with('success', 'Đã chuyển banner vào thùng rác!');
    }

    /**
     * Display a listing of trashed resources.
     */
    public function trash()
    {
        $banners = Banner::onlyTrashed()->orderBy('deleted_at', 'desc')->get();
        return view('admin.banners.trash', compact('banners'));
    }

    /**
     * Restore the specified resource from trash.
     */
    public function restore($id)
    {
        $banner = Banner::onlyTrashed()->findOrFail($id);
        $banner->restore();
        return redirect()->route('admin.banners.trash')->with('success', 'Khôi phục banner thành công!');
    }

    /**
     * Permanently remove the specified resource from storage.
     */
    public function forceDelete($id)
    {
        $banner = Banner::onlyTrashed()->findOrFail($id);
        if ($banner->image_url) {
            $path = public_path(str_replace(asset(''), '', $banner->image_url));
            if (file_exists($path)) {
                unlink($path);
            }
        }
        $banner->forceDelete();
        return redirect()->route('admin.banners.trash')->with('success', 'Đã xóa vĩnh viễn banner!');
    }
}
