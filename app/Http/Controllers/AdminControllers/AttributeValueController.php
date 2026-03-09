<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use App\Models\Attribute;
use App\Models\AttributeValue;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AttributeValueController extends Controller
{
    // 1. Xem danh sách giá trị (Terms)
  public function index(Request $request, Attribute $attribute)
    {
        $query = $attribute->values();

        // Tính năng LỌC (TÌM KIẾM): Nếu có nhập từ khóa vào ô search
        if ($request->has('search') && $request->search != '') {
            $query->where('value', 'like', '%' . $request->search . '%');
        }

        // Tính năng SẮP XẾP: Ưu tiên sort_order nhỏ hiện trước, trùng sort_order thì cái nào mới tạo (id lớn) hiện trước
        $values = $query->orderBy('sort_order', 'asc')->orderBy('id', 'desc')->get();

        return view('admin.attributes.values', compact('attribute', 'values'));
    }

    // 2. Thêm giá trị mới
  

    // 3. Xóa mềm (Cho vào thùng rác) - CHỈ CÓ ĐÚNG 1 HÀM NÀY
    public function destroy(AttributeValue $attribute_value)
    {
        $attribute_value->delete(); // Xóa mềm
        return back()->with('success', 'Đã chuyển giá trị vào thùng rác!');
    }

    // Hiển thị form sửa
    public function edit(Attribute $attribute, AttributeValue $attribute_value)
    {
        return view('admin.attributes.values_edit', compact('attribute', 'attribute_value'));
    }

    // Xử lý lưu dữ liệu sửa
   // 1. HÀM THÊM MỚI (Vừa chống trùng tên, vừa chống trùng số thứ tự)
    public function store(Request $request, Attribute $attribute)
    {
        $request->validate([
            'value' => [
                'required',
                'string',
                'max:255',
                \Illuminate\Validation\Rule::unique('attribute_values', 'value')->where('attribute_id', $attribute->id)
            ],
            'sort_order' => [
                'nullable',
                'integer',
                // Rule chặn trùng lặp số thứ tự trong cùng 1 thuộc tính (Màu sắc)
                \Illuminate\Validation\Rule::unique('attribute_values', 'sort_order')->where('attribute_id', $attribute->id)
            ]
        ], [
            'value.unique' => 'LỖI: Giá trị "' . $request->value . '" đã tồn tại!',
            'sort_order.unique' => 'LỖI: Số thứ tự "' . $request->sort_order . '" đã có người xài. Bro hãy nhập số khác hoặc để trống!'
        ]);

        // Logic tự động tăng nếu bro để trống ô Thứ tự
        $sortOrder = $request->sort_order;
        if (is_null($sortOrder)) {
            $maxSortOrder = $attribute->values()->max('sort_order');
            $sortOrder = $maxSortOrder !== null ? $maxSortOrder + 1 : 1;
        }

        $attribute->values()->create([
            'value' => $request->value,
            'sort_order' => $sortOrder
        ]);

        return back()->with('success', 'Đã thêm giá trị cấu hình!');
    }

    // 2. HÀM SỬA (Cũng chống trùng y chang nhưng bỏ qua chính nó)
    public function update(Request $request, Attribute $attribute, AttributeValue $attribute_value)
    {
        $request->validate([
            'value' => [
                'required',
                'string',
                'max:255',
                \Illuminate\Validation\Rule::unique('attribute_values', 'value')
                    ->where('attribute_id', $attribute->id)
                    ->ignore($attribute_value->id)
            ],
            'sort_order' => [
                'nullable',
                'integer',
                // Chống trùng số thứ tự khi sửa
                \Illuminate\Validation\Rule::unique('attribute_values', 'sort_order')
                    ->where('attribute_id', $attribute->id)
                    ->ignore($attribute_value->id)
            ]
        ], [
            'value.unique' => 'LỖI: Giá trị "' . $request->value . '" đã bị trùng với một mục khác!',
            'sort_order.unique' => 'LỖI: Số thứ tự "' . $request->sort_order . '" đang bị mục khác chiếm dụng!'
        ]);

        $attribute_value->update([
            'value' => $request->value,
            // Nếu để trống lúc sửa thì giữ nguyên số cũ
            'sort_order' => $request->sort_order ?? $attribute_value->sort_order 
        ]);

        return redirect()->route('admin.attributes.values.index', $attribute->id)
                         ->with('success', 'Đã cập nhật giá trị thành công!');
    }

    // ==========================================
    // CÁC HÀM XỬ LÝ THÙNG RÁC
    // ==========================================

    // 4. Xem thùng rác
    public function trash(Attribute $attribute)
    {
        $trashedValues = $attribute->values()->onlyTrashed()->orderBy('deleted_at', 'desc')->get();
        return view('admin.attributes.values_trash', compact('attribute', 'trashedValues'));
    }

    // 5. Khôi phục từ thùng rác
    public function restore($id)
    {
        $value = AttributeValue::onlyTrashed()->findOrFail($id);
        $value->restore();
        
        return back()->with('success', 'Đã khôi phục giá trị: ' . $value->value);
    }

    // 6. Xóa vĩnh viễn
    public function forceDelete($id)
    {
        $value = AttributeValue::onlyTrashed()->findOrFail($id);
        
        // Gỡ liên kết với bảng biến thể trước khi xóa vĩnh viễn (Rất quan trọng)
        $value->variants()->detach(); 
        
        // Xóa tận gốc
        $value->forceDelete();
        
        return back()->with('success', 'Đã xóa vĩnh viễn giá trị này!');
    }
}