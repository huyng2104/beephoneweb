<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use App\Models\Attribute;
use Illuminate\Http\Request;

class AttributeController extends Controller
{
    public function index()
    {
        $attributes = Attribute::with('values')->orderBy('id', 'desc')->get();
        return view('admin.attributes.index', compact('attributes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:attributes,name'
        ]);

        Attribute::create([
            'name' => $request->name
        ]);

        return back()->with('success', 'Đã thêm thuộc tính mới!');
    }

    public function update(Request $request, Attribute $attribute)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:attributes,name,' . $attribute->id
        ]);

        $attribute->update([
            'name' => $request->name
        ]);

        return back()->with('success', 'Cập nhật thành công!');
    }

    public function destroy(Attribute $attribute)
    {
        $attribute->delete(); // Soft delete do bro đã khai báo trong migration
        return back()->with('success', 'Đã xóa thuộc tính!');
    }

    public function trash()
    {
        // Lấy các thuộc tính đã bị xóa mềm (chỉ những cái có deleted_at)
        $trashedAttributes = Attribute::onlyTrashed()->orderBy('deleted_at', 'desc')->get();
        return view('admin.attributes.trash', compact('trashedAttributes'));
    }

    // Khôi phục thuộc tính
    public function restore($id)
    {
        $attribute = Attribute::onlyTrashed()->findOrFail($id);
        $attribute->restore(); // Phục hồi

        return back()->with('success', 'Đã khôi phục thuộc tính: ' . $attribute->name);
    }

    // Xóa vĩnh viễn
    public function forceDelete($id)
    {
        $attribute = Attribute::onlyTrashed()->findOrFail($id);
        
        // Cẩn thận: Phải xóa vĩnh viễn các giá trị con (Đỏ, Xanh...) trước để tránh mồ côi data
        $attribute->values()->forceDelete(); 
        
        // Xóa thuộc tính
        $attribute->forceDelete();

        return back()->with('success', 'Đã xóa vĩnh viễn thuộc tính và các giá trị liên quan!');
    }

    // =======================================================
    // API LẤY GIÁ TRỊ THUỘC TÍNH (Dành cho Javascript gọi)
    // =======================================================
    public function getValues($id)
    {
        try {
            // Tận dụng luôn relationship 'values' mà bro đã định nghĩa trong Model Attribute
            $attribute = Attribute::with('values')->findOrFail($id);

            // Phải return ra chuẩn JSON thì Javascript mới đọc được
            return response()->json([
                'success' => true,
                'data' => $attribute->values
            ], 200);

        } catch (\Exception $e) {
            // Lỡ có lỗi thì báo về cho JS biết
            return response()->json([
                'success' => false,
                'message' => 'Lỗi Server: ' . $e->getMessage()
            ], 500);
        }
    }
}