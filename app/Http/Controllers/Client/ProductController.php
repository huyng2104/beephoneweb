<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
   public function show($slug)
    {
        // Đổi chữ 'category' thành 'categories'
        $product = Product::with(['brand', 'categories', 'variants'])
            ->where('slug', $slug)
            ->orWhere('id', $slug)
            ->firstOrFail();

        return view('client.product-detail', compact('product'));
    }

    public function index(Request $request)
    {
        // 1. Khởi tạo query gốc
        $query = Product::with(['brand', 'categories'])->where('status', 1);

        // 2. Lọc theo Danh mục (Nếu URL có chứa ?category=id)
        if ($request->has('category') && $request->category != '') {
            $categoryId = $request->category;
            $query->whereHas('categories', function ($q) use ($categoryId) {
                $q->where('category_id', $categoryId);
            });
        }

        // 3. Lọc theo Thương hiệu (Brand)
        if ($request->has('brands') && is_array($request->brands)) {
            $query->whereIn('brand_id', $request->brands);
        }

        // 4. Lọc theo Khoảng giá
        if ($request->has('price_range')) {
            switch ($request->price_range) {
                case 'under-5':
                    $query->where(function($q) {
                        $q->where('sale_price', '<', 5000000)->where('sale_price', '>', 0)
                          ->orWhere(function($sub) { $sub->where('price', '<', 5000000)->where('sale_price', 0); });
                    });
                    break;
                case '5-10':
                    $query->where(function($q) {
                        $q->whereBetween('sale_price', [5000000, 10000000])->where('sale_price', '>', 0)
                          ->orWhere(function($sub) { $sub->whereBetween('price', [5000000, 10000000])->where('sale_price', 0); });
                    });
                    break;
                case '10-15':
                    $query->where(function($q) {
                        $q->whereBetween('sale_price', [10000000, 15000000])->where('sale_price', '>', 0)
                          ->orWhere(function($sub) { $sub->whereBetween('price', [10000000, 15000000])->where('sale_price', 0); });
                    });
                    break;
                case 'over-15':
                    $query->where(function($q) {
                        $q->where('sale_price', '>', 15000000)
                          ->orWhere(function($sub) { $sub->where('price', '>', 15000000)->where('sale_price', 0); });
                    });
                    break;
            }
        }

        // 5. Sắp xếp (Sorting)
        $sort = $request->input('sort', 'newest');
        switch ($sort) {
            case 'price-asc':
                $query->orderByRaw('COALESCE(NULLIF(sale_price, 0), price) ASC');
                break;
            case 'price-desc':
                $query->orderByRaw('COALESCE(NULLIF(sale_price, 0), price) DESC');
                break;
            case 'bestseller':
                // Chỗ này nếu có cột luot_ban thì order, tạm thời mình cứ order theo ID
                $query->orderBy('id', 'asc');
                break;
            default: // newest
                $query->latest();
                break;
        }

        // 6. Phân trang (Pagination)
        $products = $query->paginate(12)->withQueryString(); // 12 SP/trang, giữ nguyên param trên URL

        // 7. Lấy danh sách Filters để hiển thị ra cột trái
        $brands = \App\Models\Brand::where('is_active', 1)->get();
        // Lấy category hiện tại nếu có lọc
        $currentCategory = null;
        if($request->has('category')){
             $currentCategory = \App\Models\Category::find($request->category);
        }

        return view('client.products-list', compact('products', 'brands', 'currentCategory', 'sort'));
    }
}