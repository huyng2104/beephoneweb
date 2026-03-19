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

    // ==========================================
    // HÀM STORE (TẠO SẢN PHẨM MỚI + ẢNH BIẾN THỂ)
    // ==========================================
    public function store(Request $request)
    {
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
            // 1. Lưu ảnh đại diện
            $thumbnailPath = null;
            if ($request->hasFile('thumbnail')) {
                $thumbnailPath = $request->file('thumbnail')->store('products', 'public');
            }

            // 2. Xử lý thông số kỹ thuật
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

            // 3. Lưu Sản phẩm chính
            $product = Product::create([
                'name' => $request->name,
                'description' => $request->description,
                'specifications' => empty($specs) ? null : $specs,
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
                    $product->images()->create(['path' => $imagePath]);
                }
            }

            // 6. LƯU BIẾN THỂ KÈM ẢNH
            if ($request->type == 'variable' && $request->has('variations')) {
                foreach ($request->variations as $varData) {

                    // Xử lý upload ảnh cho từng dòng biến thể
                    $variantThumbnail = null;
                    if (isset($varData['thumbnail']) && $varData['thumbnail'] instanceof \Illuminate\Http\UploadedFile) {
                        $variantThumbnail = $varData['thumbnail']->store('products/variants', 'public');
                    }

                    $variant = $product->variants()->create([
                        'sku' => ($varData['sku'] ?? $product->sku) . '-' . Str::random(4),
                        'price' => $varData['price'] ?? $product->price,
                        'sale_price' => $varData['sale_price'] ?? null,
                        'stock' => $varData['stock'] ?? 0,
                        'thumbnail' => $variantThumbnail, // Lưu đường dẫn ảnh biến thể
                    ]);

                    if (isset($varData['attributes'])) {
                        $variant->attributeValues()->attach(array_values($varData['attributes']));
                    }
                }
            }

            return redirect()->route('admin.products.index')->with('success', 'Tuyệt vời! Đã đăng sản phẩm và các biến thể thành công!');

        } catch (\Exception $e) {
            'Log'::error("Store Product Error: " . $e->getMessage());
            return back()->withInput()->withErrors(['error' => 'Lỗi hệ thống: ' . $e->getMessage()]);
        }
    }

    public function show(Product $product)
    {
        $product->load(['categories', 'brand', 'images', 'variants.attributeValues']);
        return view('admin.products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $product->load(['categories', 'images', 'variants.attributeValues']);

        $categories = Category::all();
        $brands = Brand::all();
        $attributes = Attribute::with('values')->get();

        return view('admin.products.edit', compact('product', 'categories', 'brands', 'attributes'));
    }

    // ==========================================
    // HÀM UPDATE (CẬP NHẬT SP + ẢNH BIẾN THỂ CŨ/MỚI)
    // ==========================================
    public function update(Request $request, Product $product)
    {
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
            $thumbnailPath = $product->thumbnail;
            if ($request->hasFile('thumbnail')) {
                // Kiểm tra và xóa ảnh cũ để tránh rác server
                if ($product->thumbnail && Storage::disk('public')->exists($product->thumbnail)) {
                    Storage::disk('public')->delete($product->thumbnail);
                }
                $thumbnailPath = $request->file('thumbnail')->store('products', 'public');
            }

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

            $product->update([
                'name' => $request->name,
                'description' => $request->description,
                'specifications' => empty($specs) ? null : $specs,
                'type' => $request->type ?? 'simple',
                'price' => $request->price ?? 0,
                'sale_price' => $request->sale_price,
                'sku' => $request->sku,
                'stock' => $request->stock ?? 0,
                'status' => $request->status ?? 'active',
                'brand_id' => $request->brand_id,
                'thumbnail' => $thumbnailPath,
            ]);

            $product->categories()->sync($request->category_ids);

            // CẬP NHẬT BIẾN THỂ
            if ($request->type == 'variable' && $request->has('variations')) {

                // Lấy mảng ID các biến thể được gửi lên từ Form
                $incomingVariantIds = collect($request->variations)->pluck('id')->filter()->toArray();

                // Xóa các biến thể cũ không còn tồn tại trong form
                $product->variants()->whereNotIn('id', $incomingVariantIds)->get()->each(function($var) {
                    if ($var->thumbnail && Storage::disk('public')->exists($var->thumbnail)) {
                        Storage::disk('public')->delete($var->thumbnail);
                    }
                    $var->attributeValues()->detach();
                    $var->forceDelete();
                });

                foreach ($request->variations as $varData) {
                    $variantThumbnail = null;

                    // Nếu là cập nhật biến thể ĐÃ CÓ (có gửi ID lên)
                    if (isset($varData['id'])) {
                        $existingVariant = $product->variants()->find($varData['id']);
                        $variantThumbnail = $existingVariant->thumbnail; // Giữ nguyên ảnh cũ

                        // Nếu Admin có chọn ảnh mới -> Ghi đè ảnh cũ
                        if (isset($varData['thumbnail']) && $varData['thumbnail'] instanceof \Illuminate\Http\UploadedFile) {
                            if ($existingVariant->thumbnail && Storage::disk('public')->exists($existingVariant->thumbnail)) {
                                Storage::disk('public')->delete($existingVariant->thumbnail);
                            }
                            $variantThumbnail = $varData['thumbnail']->store('products/variants', 'public');
                        }

                        $existingVariant->update([
                            'sku' => $varData['sku'] ?? $existingVariant->sku,
                            'price' => $varData['price'] ?? $existingVariant->price,
                            'sale_price' => $varData['sale_price'] ?? null,
                            'stock' => $varData['stock'] ?? 0,
                            'thumbnail' => $variantThumbnail,
                        ]);

                        if (isset($varData['attributes'])) {
                            $existingVariant->attributeValues()->sync(array_values($varData['attributes']));
                        }
                    }
                    // Nếu là biến thể MỚI TINH (không có ID)
                    else {
                        if (isset($varData['thumbnail']) && $varData['thumbnail'] instanceof \Illuminate\Http\UploadedFile) {
                            $variantThumbnail = $varData['thumbnail']->store('products/variants', 'public');
                        }

                        $newVariant = $product->variants()->create([
                            'sku' => $varData['sku'] ?? ($product->sku . '-' . Str::random(5)),
                            'price' => $varData['price'] ?? $product->price,
                            'sale_price' => $varData['sale_price'] ?? null,
                            'stock' => $varData['stock'] ?? 0,
                            'thumbnail' => $variantThumbnail,
                        ]);

                        if (isset($varData['attributes'])) {
                            $newVariant->attributeValues()->attach(array_values($varData['attributes']));
                        }
                    }
                }
            } else {
                // Nếu đổi từ Variable -> Simple: Xóa trắng biến thể
                foreach ($product->variants as $oldVariant) {
                    if ($oldVariant->thumbnail && Storage::disk('public')->exists($oldVariant->thumbnail)) {
                        Storage::disk('public')->delete($oldVariant->thumbnail);
                    }
                    $oldVariant->attributeValues()->detach();
                    $oldVariant->forceDelete();
                }
            }

            return redirect()->route('admin.products.index')->with('success', 'Đã cập nhật sản phẩm và ảnh biến thể thành công!');

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
