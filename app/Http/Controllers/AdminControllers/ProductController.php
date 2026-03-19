<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Attribute;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with(['categories', 'brand'])->orderBy('id', 'desc')->get();
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::all();
        $brands = Brand::all();
        $attributes = Attribute::with('values')->get();

        return view('admin.products.create', compact('categories', 'brands', 'attributes'));
    }

   public function store(Request $request)
{
    // 1. Kiểm tra dữ liệu
    $request->validate([
        'name' => 'required|string|max:255',
        'category_ids' => 'required|array',
        'brand_id' => 'required|exists:brands,id',
        'thumbnail' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
    ], [
        'name.required' => 'Vui lòng nhập tên sản phẩm!',
        'category_ids.required' => 'Bro chưa chọn danh mục kìa!',
        'brand_id.required' => 'Bro chưa chọn thương hiệu!',
        'thumbnail.required' => 'Phải có ảnh đại diện mới cho đăng nhé!',
        'thumbnail.image' => 'File tải lên phải là hình ảnh!',
    ]);

    try {
        // 2. Lưu ảnh đại diện
        $thumbnailPath = null;
        if ($request->hasFile('thumbnail')) {
            $thumbnailPath = $request->file('thumbnail')->store('products', 'public');
        }

        // 3. Lưu Sản phẩm chính
        $product = Product::create([
            'name' => $request->name,
            // 'slug' => \Illuminate\Support\Str::slug($request->name) . '-' . time(), 
            'description' => $request->description,
            'type' => $request->type ?? 'simple',
            'price' => $request->price ?? 0,
            'sale_price' => $request->sale_price,
            'sku' => $request->sku,
            'stock' => $request->stock ?? 0,
            'status' => $request->status ?? 'active',
            'is_featured' => $request->has('is_featured') ? 1 : 0,
            'brand_id' => $request->brand_id,
            'thumbnail' => $thumbnailPath,
        ]);

        // 4. Lưu Danh mục
        if ($request->has('category_ids')) {
            $product->categories()->attach($request->category_ids);
        }

        // 5. Lưu Album ảnh (Gallery)
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imagePath = $image->store('products/gallery', 'public');
                $product->images()->create([
                    'path' => $imagePath 
                ]);
            }
        }

        // ==========================================
        // 6. LƯU BIẾN THỂ (PHẢI NẰM Ở ĐÂY)
        // ==========================================
        if ($request->type == 'variable' && $request->has('variations')) {
            foreach ($request->variations as $varData) {
                // Tạo biến thể vào bảng product_variants
              // Trong vòng lặp foreach ($request->variations as $varData)
$variant = $product->variants()->create([
    // Nếu người dùng có nhập SKU thì lấy SKU đó + chuỗi random 
    // Hoặc nếu không nhập thì lấy SKU cha + chuỗi random
    'sku' => ($varData['sku'] ?? $product->sku) . '-' . \Illuminate\Support\Str::random(4),
    
    'price' => $varData['price'] ?? $product->price,
    'sale_price' => $varData['sale_price'] ?? null,
    'stock' => $varData['stock'] ?? 0,
]);

                // Gắn Màu sắc / Dung lượng vào biến thể thông qua bảng trung gian
                if (isset($varData['attributes'])) {
                    // Lấy ra các ID giá trị (Đỏ, Xanh...) để lưu vào bảng variant_attribute_value
                    $variant->attributeValues()->attach(array_values($varData['attributes']));
                }
            }
        }

        // CHỈ RETURN KHI ĐÃ XỬ LÝ XONG TẤT CẢ (Kể cả biến thể)
        return redirect()->route('admin.products.index')->with('success', 'Tuyệt vời! Đã đăng sản phẩm và biến thể thành công!');

    } catch (\Exception $e) {
        // Nếu có lỗi, nó sẽ nhảy vào đây chứ không bị trắng trang
        return back()->withInput()->withErrors(['error' => 'Lỗi hệ thống: ' . $e->getMessage()]);
    }
}

    public function show(Product $product)
    {
        // Load sẵn các liên kết để hiển thị
$product->load(['categories', 'brand', 'images', 'variants.attributeValues']);
        return view('admin.products.show', compact('product'));
    }

    // ==========================================
    // HÀM HIỂN THỊ FORM SỬA (Đã fix lỗi trắng trang)
    // ==========================================
    public function edit(Product $product)
    {
        // Xóa 'variants.attributeValues' đi để không bị lỗi trắng màn hình
        $product->load(['categories', 'images']); 
        
        $categories = Category::all();
        $brands = Brand::all();
        $attributes = Attribute::with('values')->get();

        return view('admin.products.edit', compact('product', 'categories', 'brands', 'attributes'));
    }

    // ==========================================
    // HÀM XỬ LÝ CẬP NHẬT DATABASE
    // ==========================================
   public function update(Request $request, Product $product)
{
    // 1. Kiểm tra dữ liệu đầu vào
    $request->validate([
        'name' => 'required|string|max:255',
        'category_ids' => 'required|array',
        'brand_id' => 'required|exists:brands,id',
        'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
    ], [
        'name.required' => 'Tên sản phẩm không được để trống!',
        'category_ids.required' => 'Chọn ít nhất một danh mục nhé bro!',
    ]);

    try {
        // 2. Xử lý ảnh đại diện (Thay ảnh mới nếu có, không thì giữ ảnh cũ)
        $thumbnailPath = $product->thumbnail;
        if ($request->hasFile('thumbnail')) {
            $thumbnailPath = $request->file('thumbnail')->store('products', 'public');
        }

        // 3. Cập nhật thông tin Sản phẩm chính
        $product->update([
            'name' => $request->name,
            'description' => $request->description,
            'type' => $request->type ?? 'simple',
            'price' => $request->price ?? 0,
            'sale_price' => $request->sale_price,
            'sku' => $request->sku,
            'stock' => $request->stock ?? 0,
            'status' => $request->status ?? 'active',
            'brand_id' => $request->brand_id,
            'thumbnail' => $thumbnailPath,
        ]);

        // 4. Đồng bộ danh mục (Sync giúp tự xóa cái cũ, thêm cái mới vào bảng trung gian)
        $product->categories()->sync($request->category_ids);

        // =========================================================
        // 5. CẬP NHẬT BIẾN THỂ (Variations) - ĐOẠN NÀY LÀ QUAN TRỌNG NHẤT
        // =========================================================
        
        if ($request->type == 'variable') {
            // Bước A: Dọn dẹp "bãi chiến trường" cũ
            // Phải xóa liên kết ở bảng trung gian trước, sau đó xóa VĨNH VIỄN biến thể cũ
            foreach ($product->variants as $oldVariant) {
                $oldVariant->attributeValues()->detach(); // Gỡ Màu sắc, Dung lượng cũ
                $oldVariant->forceDelete(); // Xóa sạch dấu vết SKU cũ khỏi Database
            }

            // Bước B: Lưu bộ biến thể mới từ Form gửi lên
            if ($request->has('variations')) {
                foreach ($request->variations as $varData) {
                    // Tạo mới từng dòng biến thể
                    $variant = $product->variants()->create([
                        'sku' => $varData['sku'] ?? ($product->sku . '-' . \Illuminate\Support\Str::random(5)),
                        'price' => $varData['price'] ?? $product->price,
                        'sale_price' => $varData['sale_price'] ?? null,
                        'stock' => $varData['stock'] ?? 0,
                    ]);

                    // Gắn lại các giá trị thuộc tính (Màu sắc, Dung lượng...)
                    if (isset($varData['attributes'])) {
                        $variant->attributeValues()->attach(array_values($varData['attributes']));
                    }
                }
            }
        } else {
            // Nếu đổi từ Biến thể về Đơn giản thì cũng nên dọn dẹp các biến thể con cũ cho sạch DB
            foreach ($product->variants as $oldVariant) {
                $oldVariant->attributeValues()->detach();
                $oldVariant->forceDelete();
            }
        }

        return redirect()->route('admin.products.index')->with('success', 'Đã cập nhật sản phẩm và bộ biến thể mới thành công!');

    } catch (\Exception $e) {
        // Ghi log lỗi để bro dễ debug nếu có vấn đề sâu bên trong
        \Log::error("Update Product Error: " . $e->getMessage());
        return back()->withInput()->withErrors(['error' => 'Có lỗi xảy ra: ' . $e->getMessage()]);
    }
}
    public function destroy(Product $product)
    {
        $product->delete();
        return back()->with('success', 'Đã chuyển sản phẩm vào thùng rác!');
    }

    public function trash()
    {
        $trashedProducts = Product::onlyTrashed()->with(['categories', 'brand'])->orderBy('deleted_at', 'desc')->get();
        return view('admin.products.trash', compact('trashedProducts'));
    }

    public function restore($id)
    {
        $product = Product::onlyTrashed()->findOrFail($id);
        $product->restore();
        return back()->with('success', 'Đã khôi phục sản phẩm: ' . $product->name);
    }

    public function forceDelete($id)
    {
        $product = Product::onlyTrashed()->findOrFail($id);
        $product->forceDelete();
        return back()->with('success', 'Đã xóa vĩnh viễn sản phẩm!');
    }
}