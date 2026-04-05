<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Attribute;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('product.view'); // kiểm tra Quyền xem sản phẩm
        
        $query = Product::with(['categories', 'brand', 'variants']);

        // 1. Tìm kiếm (Theo tên, ID, hoặc SKU)
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('id', $searchTerm)
                  ->orWhere('sku', 'like', "%{$searchTerm}%");
            });
        }

        // 2. Lọc theo loại sản phẩm
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // 3. Lọc theo trạng thái
        if ($request->filled('status')) {
            if ($request->status === 'out_of_stock') {
                $query->whereDoesntHave('variants', function($q) {
                    $q->where('stock', '>', 0);
                });
            } else {
                $query->where('status', $request->status);
            }
        }

        // 3. Lọc theo danh mục
        if ($request->filled('category')) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('categories.id', $request->category);
            });
        }

        // 4. Lọc theo thương hiệu (giữ lại từ code HTML cũ)
        if ($request->filled('brand')) {
            $query->where('brand_id', $request->brand);
        }

        // 5. Sắp xếp
        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'price-asc':
                    $query->select('products.*')
                          ->selectRaw('(SELECT MIN(CASE WHEN sale_price IS NOT NULL AND sale_price > 0 THEN sale_price ELSE price END) FROM product_variants WHERE product_variants.product_id = products.id) as actual_price')
                          ->orderBy('actual_price', 'asc');
                    break;
                case 'price-desc':
                    $query->select('products.*')
                          ->selectRaw('(SELECT MIN(CASE WHEN sale_price IS NOT NULL AND sale_price > 0 THEN sale_price ELSE price END) FROM product_variants WHERE product_variants.product_id = products.id) as actual_price')
                          ->orderBy('actual_price', 'desc');
                    break;
                case 'stock-asc':
                    $query->withSum('variants', 'stock')->orderBy('variants_sum_stock', 'asc');
                    break;
                case 'stock-desc':
                    $query->withSum('variants', 'stock')->orderBy('variants_sum_stock', 'desc');
                    break;
                case 'newest':
                default:
                    $query->orderBy('products.id', 'desc');
                    break;
            }
        } else {
            $query->orderBy('products.id', 'desc'); // Mặc định
        }

        // Lấy danh mục để hiển thị ở bộ lọc
        $categories = Category::all();

        $products = $query->paginate(10)->appends($request->all());

        // Lấy thống kê chung (Global Stats)
        $totalProducts = Product::count();
        $activeProducts = Product::where('status', 'active')->count();
        $outOfStockProducts = Product::whereDoesntHave('variants', function($q) {
            $q->where('stock', '>', 0);
        })->count();
        
        $lowStockCount = \Illuminate\Support\Facades\DB::table('product_variants')
            ->select('product_id', \Illuminate\Support\Facades\DB::raw('SUM(stock) as total_stock'))
            ->groupBy('product_id')
            ->having('total_stock', '>', 0)
            ->having('total_stock', '<=', 5)
            ->get()
            ->count();

        return view('admin.products.index', compact('products', 'categories', 'totalProducts', 'activeProducts', 'outOfStockProducts', 'lowStockCount'));
    }

    public function create()
    {
        Gate::authorize('product.create'); // Kiểm tra quyền thêm sản phẩm
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
        Gate::authorize('product.create'); // Kiểm tra quyền thêm sản phẩm
        $request->validate([
            'name' => 'required|string|max:255',
            'category_ids' => 'required|array',
            'thumbnail' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
            'price' => 'nullable|required_if:type,simple|numeric|min:0',
            'variations.*.price' => 'nullable|required_if:type,variable|numeric|min:0',
        ], [
            'name.required' => 'Vui lòng nhập tên sản phẩm!',
            'category_ids.required' => 'Bro chưa chọn danh mục kìa!',
            'thumbnail.required' => 'Phải có ảnh đại diện mới cho đăng nhé!',
            'thumbnail.image' => 'File tải lên phải là hình ảnh!',
            'price.required_if' => 'Vui lòng nhập giá bán!',
            'price.numeric' => 'Giá phải là số hợp lệ!',
            'price.min' => 'Giá không thể nhỏ hơn 0!',
            'variations.*.price.required_if' => 'Vui lòng nhập giá cho biến thể!',
            'variations.*.price.numeric' => 'Giá biến thể phải là số hợp lệ!',
            'variations.*.price.min' => 'Giá biến thể không thể nhỏ hơn 0!',
        ]);

        try {
            // 1. Lưu ảnh đại diện
            $thumbnailPath = null;
            if ($request->hasFile('thumbnail')) {
                $thumbnailPath = $request->file('thumbnail')->store('products', 'public');
            }

            $sku = $request->sku ?: strtoupper(Str::slug($request->name, ''));

            // Lưu Sản phẩm chính
            $product = Product::create([
                'name' => $request->name,
                'description' => $request->description,
                'type' => $request->type ?? 'simple',
                'status' => $request->status ?? 'active',
                'is_featured' => $request->has('is_featured') ? 1 : 0,
                'brand_id' => $request->brand_id,
                'thumbnail' => $thumbnailPath,
                'sku' => $sku,
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

            // 6. LƯU BIẾN THỂ KÈM ẢNH & THÔNG SỐ
            if ($request->type == 'simple') {
                // Sản phẩm đơn
                $variant = $product->variants()->create([
                    'sku' => $sku,
                    'price' => $request->price ?? 0,
                    'sale_price' => $request->sale_price ?? null,
                    'stock' => $request->stock ?? 0,
                    'thumbnail' => $thumbnailPath,
                ]);

                // Lưu thông số cho sản phẩm đơn
                if ($request->has('spec_keys') && $request->has('spec_values')) {
                    $keys = $request->spec_keys;
                    $values = $request->spec_values;
                    foreach ($keys as $index => $key) {
                        if (!empty($key) && !empty($values[$index])) {
                            $variant->specifications()->create([
                                'spec_key' => $key,
                                'spec_value' => $values[$index],
                            ]);
                        }
                    }
                }
            } elseif ($request->type == 'variable' && $request->has('variations')) {
                foreach ($request->variations as $varData) {

                    $variantThumbnail = null;
                    if (isset($varData['thumbnail']) && $varData['thumbnail'] instanceof \Illuminate\Http\UploadedFile) {
                        $variantThumbnail = $varData['thumbnail']->store('products/variants', 'public');
                    }

                    $variant = $product->variants()->create([
                        'sku' => !empty($varData['sku']) ? $varData['sku'] : ($sku . '-' . strtoupper(Str::random(4))),
                        'price' => $varData['price'] ?? 0,
                        'sale_price' => $varData['sale_price'] ?? null,
                        'stock' => $varData['stock'] ?? 0,
                        'thumbnail' => $variantThumbnail,
                        'status' => $varData['status'] ?? 'active',
                    ]);

                    if (isset($varData['attributes'])) {
                        $variant->attributeValues()->attach(array_values($varData['attributes']));
                    }

                    // Lưu thông số cho mỗi biến thể
                    if (isset($varData['spec_keys']) && isset($varData['spec_values'])) {
                        $vKeys = $varData['spec_keys'];
                        $vValues = $varData['spec_values'];
                        foreach ($vKeys as $idx => $vKey) {
                            if (!empty($vKey) && !empty($vValues[$idx])) {
                                $variant->specifications()->create([
                                    'spec_key' => $vKey,
                                    'spec_value' => $vValues[$idx],
                                ]);
                            }
                        }
                    }
                }
            }

            return redirect()->route('admin.products.index')->with('success', 'Tuyệt vời! Đã đăng sản phẩm và các biến thể thành công!');

        } catch (\Exception $e) {
            dd($e->getMessage());
            return back()->withInput()->withErrors(['error' => 'Lỗi hệ thống: ' . $e->getMessage()]);
        }
    }

    public function show(Product $product)
    {
        $product->load(['categories', 'brand', 'images', 'variants.attributeValues']);
        $categories = \App\Models\Category::orderBy('name', 'asc')->get();
        return view('admin.products.show', compact('product', 'categories'));
    }

    public function comments(Product $product): View
    {
        // Keep it consistent with com/showcom: only root comments in the list, with nested replies.
        $comments = Comment::query()
            ->where('product_id', $product->id)
            ->whereNull('parent_id')
            ->with([
                'user',
                'children.user',
                'children.children.user',
                'children.children.children.user',
                'children.children.children.children.user',
            ])
            ->latest()
            ->get();

        $rated = $comments->whereNotNull('rating');
        $totalRatings = $rated->count();
        $averageRating = $totalRatings > 0 ? round((float) $rated->avg('rating'), 1) : 0.0;

        $ratingBreakdown = collect(range(5, 1))
            ->mapWithKeys(fn (int $star) => [$star => $rated->where('rating', $star)->count()]);

        // Load product relations used by the UI (thumbnail/status/description already on model).
        $product->loadMissing(['brand', 'categories']);

        // Render exactly like the existing com/showcom layout (admin & product comment pages look different).
        return view('com.showcom', compact(
            'product',
            'comments',
            'totalRatings',
            'averageRating',
            'ratingBreakdown',
        ));
    }

    public function edit(Product $product)
    {
        Gate::authorize('product.update'); // Kiểm tra quyền cập nhập
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
        Gate::authorize('product.update'); // Kiểm tra quyền cập nhập
        $request->validate([
            'name' => 'required|string|max:255',
            'category_ids' => 'required|array',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,webp',
            'price' => 'nullable|required_if:type,simple|numeric|min:0',
            'variations.*.price' => 'nullable|required_if:type,variable|numeric|min:0',
        ], [
            'name.required' => 'Tên sản phẩm không được để trống!',
            'category_ids.required' => 'Chọn ít nhất một danh mục nhé bro!',
            'thumbnail.image' => 'File tải lên phải là hình ảnh!',
            'price.required_if' => 'Vui lòng nhập giá bán!',
            'price.numeric' => 'Giá phải là số hợp lệ!',
            'price.min' => 'Giá không thể nhỏ hơn 0!',
            'variations.*.price.required_if' => 'Vui lòng nhập giá cho biến thể!',
            'variations.*.price.numeric' => 'Giá biến thể phải là số hợp lệ!',
            'variations.*.price.min' => 'Giá biến thể không thể nhỏ hơn 0!',
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

            // Lưu Sản phẩm chính
            $product->update([
                'name' => $request->name,
                'description' => $request->description,
                'type' => $request->type ?? 'simple',
                'status' => $request->status ?? 'active',
                'is_featured' => $request->has('is_featured') ? 1 : 0,
                'brand_id' => $request->brand_id,
                'thumbnail' => $thumbnailPath,
                'sku' => $request->sku ?? $product->sku,
            ]);

            $product->categories()->sync($request->category_ids);

            // 5. Lưu Album ảnh mới (Gallery)
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $imagePath = $image->store('products/gallery', 'public');
                    $product->images()->create(['path' => $imagePath]);
                }
            }

            // Xóa ảnh cũ nếu có yêu cầu
            if ($request->has('deleted_image_ids')) {
                foreach ($request->deleted_image_ids as $imgId) {
                    $img = $product->images()->find($imgId);
                    if ($img) {
                        if (Storage::disk('public')->exists($img->path)) {
                            Storage::disk('public')->delete($img->path);
                        }
                        $img->delete();
                    }
                }
            }

            // CẬP NHẬT BIẾN THỂ
            if ($request->type == 'simple') {
                // Xóa tất cả các liên kết giá trị thuộc tính của các biến thể hiện tại để dọn dẹp nếu đổi từ variable -> simple
                $product->variants->each(function($v) {
                    $v->attributeValues()->detach();
                });

                // Xóa tất cả biến thể ngoại trừ 1 biến thể đầu tiên để dùng cho simple
                $firstVariant = $product->variants()->first();
                
                if ($firstVariant) {
                    // Xóa các biến thể thừa
                    $product->variants()->where('id', '!=', $firstVariant->id)->get()->each(function($var) {
                        if ($var->thumbnail && Storage::disk('public')->exists($var->thumbnail)) {
                            Storage::disk('public')->delete($var->thumbnail);
                        }
                        $var->forceDelete();
                    });

                    // Cập nhật biến thể đầu tiên
                    $firstVariant->update([
                        'sku' => $request->sku ?? $firstVariant->sku,
                        'price' => $request->price ?? 0,
                        'sale_price' => $request->sale_price ?? null,
                        'stock' => $request->stock ?? 0,
                        'thumbnail' => $thumbnailPath,
                    ]);

                    $firstVariant->specifications()->delete(); // Xóa cũ
                    // Lưu thông số cho sản phẩm đơn
                    if ($request->has('spec_keys') && $request->has('spec_values')) {
                        $keys = $request->spec_keys;
                        $values = $request->spec_values;
                        foreach ($keys as $index => $key) {
                            if (!empty($key) && !empty($values[$index])) {
                                $firstVariant->specifications()->create([
                                    'spec_key' => $key,
                                    'spec_value' => $values[$index],
                                ]);
                            }
                        }
                    }
                } else {
                    // Nếu chưa có biến thể nào thì tạo mới
                    $firstVariant = $product->variants()->create([
                        'sku' => $request->sku ?? ($product->slug . '-' . Str::random(4)),
                        'price' => $request->price ?? 0,
                        'sale_price' => $request->sale_price ?? null,
                        'stock' => $request->stock ?? 0,
                        'thumbnail' => $thumbnailPath,
                    ]);

                    if ($request->has('spec_keys') && $request->has('spec_values')) {
                        $keys = $request->spec_keys;
                        $values = $request->spec_values;
                        foreach ($keys as $index => $key) {
                            if (!empty($key) && !empty($values[$index])) {
                                $firstVariant->specifications()->create([
                                    'spec_key' => $key,
                                    'spec_value' => $values[$index],
                                ]);
                            }
                        }
                    }
                }
            } elseif ($request->type == 'variable' && $request->has('variations')) {

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
                            'price' => $varData['price'] ?? 0,
                            'sale_price' => $varData['sale_price'] ?? null,
                            'stock' => $varData['stock'] ?? 0,
                            'thumbnail' => $variantThumbnail,
                            'status' => $varData['status'] ?? 'active',
                        ]);

                        if (isset($varData['attributes'])) {
                            $existingVariant->attributeValues()->sync(array_values($varData['attributes']));
                        }

                        // Cập nhật thông số kỹ thuật (Xóa cũ, Thêm mới)
                        $existingVariant->specifications()->delete();
                        if (isset($varData['spec_keys']) && isset($varData['spec_values'])) {
                            $vKeys = $varData['spec_keys'];
                            $vValues = $varData['spec_values'];
                            foreach ($vKeys as $idx => $vKey) {
                                if (!empty($vKey) && !empty($vValues[$idx])) {
                                    $existingVariant->specifications()->create([
                                        'spec_key' => $vKey,
                                        'spec_value' => $vValues[$idx],
                                    ]);
                                }
                            }
                        }
                    }
                    // Nếu là biến thể MỚI TINH (không có ID)
                    else {
                        if (isset($varData['thumbnail']) && $varData['thumbnail'] instanceof \Illuminate\Http\UploadedFile) {
                            $variantThumbnail = $varData['thumbnail']->store('products/variants', 'public');
                        }

                        $newVariant = $product->variants()->create([
                            'sku' => $varData['sku'] ?? ($request->sku ?? $product->slug) . '-' . Str::random(5),
                            'price' => $varData['price'] ?? 0,
                            'sale_price' => $varData['sale_price'] ?? null,
                            'stock' => $varData['stock'] ?? 0,
                            'thumbnail' => $variantThumbnail,
                            'status' => $varData['status'] ?? 'active',
                        ]);

                        if (isset($varData['attributes'])) {
                            $newVariant->attributeValues()->attach(array_values($varData['attributes']));
                        }

                        if (isset($varData['spec_keys']) && isset($varData['spec_values'])) {
                            $vKeys = $varData['spec_keys'];
                            $vValues = $varData['spec_values'];
                            foreach ($vKeys as $idx => $vKey) {
                                if (!empty($vKey) && !empty($vValues[$idx])) {
                                    $newVariant->specifications()->create([
                                        'spec_key' => $vKey,
                                        'spec_value' => $vValues[$idx],
                                    ]);
                                }
                            }
                        }
                    }
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
        Gate::authorize('product.delete'); // Kiêm tra quyền xóa
        $product->delete();
        return back()->with('success', 'Đã chuyển sản phẩm vào thùng rác!');
    }

    public function trash()
    {
        Gate::authorize('product.delete'); // Kiêm tra quyền xóa
        $trashedProducts = Product::onlyTrashed()->with(['categories', 'brand'])->orderBy('deleted_at', 'desc')->get();
        return view('admin.products.trash', compact('trashedProducts'));
    }

    public function restore($id)
    {
        Gate::authorize('product.delete'); // Kiêm tra quyền xóa
        $product = Product::onlyTrashed()->findOrFail($id);
        $product->restore();
        return back()->with('success', 'Đã khôi phục sản phẩm: ' . $product->name);
    }

    public function forceDelete($id)
    {
        Gate::authorize('product.delete'); // Kiêm tra quyền xóa
        $product = Product::onlyTrashed()->findOrFail($id);
        $product->forceDelete();
        return back()->with('success', 'Đã xóa vĩnh viễn sản phẩm!');
    }
}
