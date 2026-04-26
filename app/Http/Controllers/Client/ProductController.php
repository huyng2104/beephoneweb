<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function searchSuggestions(Request $request)
    {
        $keyword = trim((string) $request->query('q', ''));

        $baseQuery = Product::query()->where('status', 'active');

        $suggestions = (clone $baseQuery)
            ->when($keyword !== '', function ($q) use ($keyword) {
                $q->where('name', 'like', '%' . $keyword . '%');
            })
            ->orderByDesc('id')
            ->limit(6)
            ->get(['id', 'name', 'slug'])
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'url' => route('client.product.detail', ['id' => $product->slug ?: $product->id]),
                ];
            })
            ->values();

        $trending = (clone $baseQuery)
            ->orderByDesc('id')
            ->limit(6)
            ->pluck('name')
            ->values();

        $bestSellers = (clone $baseQuery)
            ->with(['variants', 'images'])
            ->orderByDesc('id')
            ->limit(6)
            ->get()
            ->map(function ($product) {
                $variant = $product->variants->first();
                $thumbnail = optional($product->images->first())->image_url;

                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $variant ? ($variant->sale_price ?: $variant->price) : 0,
                    'thumbnail' => $thumbnail ? asset('storage/' . $thumbnail) : null,
                    'url' => route('client.product.detail', ['id' => $product->slug ?: $product->id]),
                ];
            })
            ->values();

        return response()->json([
            'trending' => $trending,
            'suggestions' => $suggestions,
            'best_sellers' => $bestSellers,
        ]);
    }

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

        $reviews = \App\Models\Review::with(['user', 'images', 'repliedBy'])
            ->where('product_id', $product->id)
            ->where(function ($query) {
                $query->where('status', \App\Models\Review::STATUS_APPROVED);
                if (auth()->check()) {
                    $query->orWhere('user_id', auth()->id());
                }
            })
            ->latest()
            ->get();

        $userReview = null;
        $canReview  = false;
        if (auth()->check()) {
            $userReview = \App\Models\Review::where('product_id', $product->id)
                ->where('user_id', auth()->id())
                ->first();

            // Chỉ được viết đánh giá nếu đã mua hàng (đơn đã giao nhận) và chưa review
            if (!$userReview) {
                $canReview = \App\Models\Order::where('user_id', auth()->id())
                    ->where('status', \App\Models\Order::STATUS_RECEIVED)
                    ->whereHas('items', fn ($q) => $q->where('product_id', $product->id))
                    ->exists();
            }
        }

        $ratingBreakdown = collect(range(5, 1))
            ->mapWithKeys(fn (int $star) => [
                $star => $reviews->where('rating', $star)->count(),
            ]);

        $comments = \App\Models\ProductComment::with(['user.role', 'replies' => function ($q) {
                $q->where('status', 1)->with('user.role');
            }])
            ->where('product_id', $product->id)
            ->where('status', 1)
            ->whereNull('parent_id')
            ->latest()
            ->get();

        return view('client.product-detail', compact(
            'product',
            'relatedProducts',
            'reviews',
            'userReview',
            'canReview',
            'ratingBreakdown',
            'comments'
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

        if ($request->has('categories') && is_array($request->categories)) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->whereIn('category_id', $request->categories);
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

        // 5.5 LỌC THEO TÌNH TRẠNG KHO
        if ($request->has('stock_status') && in_array($request->stock_status, ['in-stock', 'out-of-stock'])) {
            if ($request->stock_status == 'in-stock') {
                $query->whereHas('variants', function($q) {
                    $q->where('status', 'active')->where('stock', '>', 0);
                });
            } else {
                $query->whereDoesntHave('variants', function($q) {
                    $q->where('status', 'active')->where('stock', '>', 0);
                });
            }
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
        $categories = \App\Models\Category::where('is_active', 1)->get();
        
        $currentCategory = null;
        if($request->has('category')){
             $currentCategory = \App\Models\Category::where('id', $request->category)->orWhere('slug', $request->category)->first();
        }

        return view('client.products-list', compact('products', 'brands', 'categories', 'currentCategory', 'sort'));
    }
}
