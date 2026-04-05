<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function show($slug)
    {
        $product = Product::with(['brand', 'categories', 'variants.specifications', 'variants.attributeValues.attribute', 'images'])
            ->where('slug', $slug)
            ->orWhere('id', $slug)
            ->firstOrFail();

        // Lấy sản phẩm liên quan (cùng danh mục đầu tiên)
        $firstCatId = $product->categories->first()?->id;
        $relatedProducts = collect();
        if ($firstCatId) {
            $relatedProducts = Product::whereHas('categories', function($q) use ($firstCatId) {
                $q->where('category_id', $firstCatId);
            })
            ->where('id', '!=', $product->id)
            ->where('status', 'active')
            ->take(4)
            ->get();
        }

        // Comments
        $comments = Comment::query()
            ->where('product_id', $product->id)
            ->whereNull('parent_id')
            ->where('is_hidden', false)
            ->with([
                'user.role',
                'children.user.role',
                'children.children.user.role',
            ])
            ->latest()
            ->get();

        $rated = $comments->whereNotNull('rating');
        $totalRatings = $rated->count();
        $averageRating = $totalRatings > 0 ? round((float) $rated->avg('rating'), 1) : 0.0;

        $ratingBreakdown = collect(range(5, 1))
            ->mapWithKeys(fn (int $star) => [$star => $rated->where('rating', $star)->count()]);

        return view('client.product-detail', compact(
            'product',
            'relatedProducts',
            'comments',
            'totalRatings',
            'averageRating',
            'ratingBreakdown',
        ));
    }

    public function index(Request $request)
    {
        // 1. Khởi tạo query gốc
        $query = Product::with(['brand', 'categories', 'variants'])->where('status', 'active');

        // 2. Lọc theo Danh mục (Hỗ trợ cả slug và ID)
        if ($request->has('category') && $request->category != '') {
            $categoryIdentifier = $request->category;
            $query->whereHas('categories', function ($q) use ($categoryIdentifier) {
                if (is_numeric($categoryIdentifier)) {
                    $q->where('category_id', $categoryIdentifier);
                } else {
                    $q->where('slug', $categoryIdentifier);
                }
            });
        }

        // 3. Lọc theo Nổi bật
        if ($request->has('featured') && $request->featured == 1) {
            $query->where('is_featured', true);
        }

        // 4. Lọc theo Thương hiệu (Brand)
        if ($request->has('brands') && is_array($request->brands)) {
            $query->whereIn('brand_id', $request->brands);
        }

        // 5. LỌC THEO KHOẢNG GIÁ
        if ($request->has('price_range') && $request->price_range != '') {
            $range = $request->price_range;
            $query->where(function($q) use ($range) {
                $minPrice = 0; $maxPrice = 0; $isOver = false;
                switch($range) {
                    case 'under-5': $maxPrice = 5000000; break;
                    case '5-10': $minPrice = 5000000; $maxPrice = 10000000; break;
                    case '10-15': $minPrice = 10000000; $maxPrice = 15000000; break;
                    case 'over-15': $minPrice = 15000000; $isOver = true; break;
                }
                $q->whereHas('variants', function($variantQ) use ($minPrice, $maxPrice, $isOver) {
                     $variantPriceColumn = \DB::raw('COALESCE(NULLIF(sale_price, 0), price)');
                     if ($isOver) { $variantQ->where($variantPriceColumn, '>=', $minPrice); }
                     else { $variantQ->whereBetween($variantPriceColumn, [$minPrice, $maxPrice]); }
                });
            });
        }

        // 6. Sắp xếp (Sorting)
        $sort = $request->input('sort', 'newest');
        switch ($sort) {
            case 'price-asc':
                $query->orderBy(
                    \App\Models\ProductVariant::selectRaw('COALESCE(NULLIF(product_variants.sale_price, 0), product_variants.price)')
                        ->whereColumn('product_variants.product_id', 'products.id')
                        ->orderByRaw('COALESCE(NULLIF(product_variants.sale_price, 0), product_variants.price) ASC')
                        ->limit(1), 'ASC'
                );
                break;
            case 'price-desc':
                $query->orderBy(
                    \App\Models\ProductVariant::selectRaw('COALESCE(NULLIF(product_variants.sale_price, 0), product_variants.price)')
                        ->whereColumn('product_variants.product_id', 'products.id')
                        ->orderByRaw('COALESCE(NULLIF(product_variants.sale_price, 0), product_variants.price) DESC')
                        ->limit(1), 'DESC'
                );
                break;
            case 'bestseller': $query->orderBy('id', 'asc'); break;
            default: $query->latest(); break;
        }

        // 7. Phân trang (Pagination)
        $products = $query->paginate(12)->withQueryString();

        // 8. Lấy danh sách Filters
        $brands = \App\Models\Brand::where('is_active', 1)->get();
        $currentCategory = null;
        if($request->has('category')){
             $currentCategory = \App\Models\Category::where('id', $request->category)->orWhere('slug', $request->category)->first();
        }

        return view('client.products-list', compact('products', 'brands', 'currentCategory', 'sort'));
    }
}
