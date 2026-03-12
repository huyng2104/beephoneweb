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

            // --- XỬ LÝ GOM NHÓM THÔNG SỐ KỸ THUẬT (JSON) ---
            $specs = [];
            if ($request->has('spec_keys') && $request->has('spec_values')) {
                $keys = $request->spec_keys;
                $values = $request->spec_values;
                
                foreach ($keys as $index => $key) {
                    if (!empty($key) && !empty($values[$index])) {
                        $specs[$key] = $values[$index];
                    }
                }
            }
            // ------------------------------------------------

            // 3. Lưu Sản phẩm chính
            $product = Product::create([
                'name' => $request->name,
                // 'slug' => \Illuminate\Support\Str::slug($request->name) . '-' . time(), 
                'description' => $request->description,
                'specifications' => empty($specs) ? null : $specs, // Lưu JSON thông số
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
            // 6. LƯU BIẾN THỂ
            // ==========================================
            if ($request->type == 'variable' && $request->has('variations')) {
                foreach ($request->variations as $varData) {
                    $variant = $product->variants()->create([
                        'sku' => ($varData['sku'] ?? $product->sku) . '-' . \Illuminate\Support\Str::random(4),
                        'price' => $varData['price'] ?? $product->price,
                        'sale_price' => $varData['sale_price'] ?? null,
                        'stock' => $varData['stock'] ?? 0,
                    ]);

                    if (isset($varData['attributes'])) {
                        $variant->attributeValues()->attach(array_values($varData['attributes']));
                    }
                }
            }

            return redirect()->route('admin.products.index')->with('success', 'Tuyệt vời! Đã đăng sản phẩm và thông số kỹ thuật thành công!');

        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => 'Lỗi hệ thống: ' . $e->getMessage()]);
        }
    }

    public function show(Product $product)
    {
        $product->load(['categories', 'brand', 'images', 'variants.attributeValues']);
        return view('admin.products.show', compact('product'));
    }

    // ==========================================
    // HÀM HIỂN THỊ FORM SỬA
    // ==========================================
    public function edit(Product $product)
    {
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
            // 2. Xử lý ảnh đại diện
            $thumbnailPath = $product->thumbnail;
            if ($request->hasFile('thumbnail')) {
                $thumbnailPath = $request->file('thumbnail')->store('products', 'public');
            }

            // --- XỬ LÝ GOM NHÓM THÔNG SỐ KỸ THUẬT (JSON) ---
            $specs = [];
            if ($request->has('spec_keys') && $request->has('spec_values')) {
                $keys = $request->spec_keys;
                $values = $request->spec_values;
                
                foreach ($keys as $index => $key) {
                    if (!empty($key) && !empty($values[$index])) {
                        $specs[$key] = $values[$index];
                    }
                }
            }
            // ------------------------------------------------

            // 3. Cập nhật thông tin Sản phẩm chính
            $product->update([
                'name' => $request->name,
                'description' => $request->description,
                'specifications' => empty($specs) ? null : $specs, // Cập nhật JSON thông số
                'type' => $request->type ?? 'simple',
                'price' => $request->price ?? 0,
                'sale_price' => $request->sale_price,
                'sku' => $request->sku,
                'stock' => $request->stock ?? 0,
                'status' => $request->status ?? 'active',
                'brand_id' => $request->brand_id,
                'thumbnail' => $thumbnailPath,
            ]);

            // 4. Đồng bộ danh mục
            $product->categories()->sync($request->category_ids);

            // =========================================================
            // 5. CẬP NHẬT BIẾN THỂ (Variations)
            // =========================================================
            if ($request->type == 'variable') {
                foreach ($product->variants as $oldVariant) {
                    $oldVariant->attributeValues()->detach(); 
                    $oldVariant->forceDelete(); 
                }

                if ($request->has('variations')) {
                    foreach ($request->variations as $varData) {
                        $variant = $product->variants()->create([
                            'sku' => $varData['sku'] ?? ($product->sku . '-' . \Illuminate\Support\Str::random(5)),
                            'price' => $varData['price'] ?? $product->price,
                            'sale_price' => $varData['sale_price'] ?? null,
                            'stock' => $varData['stock'] ?? 0,
                        ]);

                        if (isset($varData['attributes'])) {
                            $variant->attributeValues()->attach(array_values($varData['attributes']));
                        }
                    }
                }
            } else {
                foreach ($product->variants as $oldVariant) {
                    $oldVariant->attributeValues()->detach();
                    $oldVariant->forceDelete();
                }
            }

            return redirect()->route('admin.products.index')->with('success', 'Đã cập nhật sản phẩm và bộ thông số kỹ thuật mới thành công!');

        } catch (\Exception $e) {
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