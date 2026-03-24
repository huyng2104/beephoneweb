<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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

        // 1.1 Tìm kiếm theo từ khóa (hỗ trợ gần đúng cơ bản bằng tách token)
        $search = trim((string) $request->input('search', ''));
        if ($search !== '') {
            $tokens = collect(preg_split('/\s+/u', Str::lower($search)))
                ->filter(fn($token) => mb_strlen($token) >= 2)
                ->values();

            $query->where(function ($q) use ($search, $tokens) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('slug', 'like', '%' . Str::slug($search) . '%')
                  ->orWhere('sku', 'like', '%' . $search . '%');

                foreach ($tokens as $token) {
                    $q->orWhere('name', 'like', '%' . $token . '%');
                }
            });
        }

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

       // 4. LỌC THEO KHOẢNG GIÁ (HỖ TRỢ CẢ BIẾN THỂ)
    if ($request->has('price_range') && $request->price_range != '') {
        $range = $request->price_range;
        
        $query->where(function($q) use ($range) {
            
            // Xử lý khoảng giá
            $minPrice = 0;
            $maxPrice = 0;
            $isOver = false;

            switch($range) {
                case 'under-5':
                    $maxPrice = 5000000;
                    break;
                case '5-10':
                    $minPrice = 5000000;
                    $maxPrice = 10000000;
                    break;
                case '10-15':
                    $minPrice = 10000000;
                    $maxPrice = 15000000;
                    break;
                case 'over-15':
                    $minPrice = 15000000;
                    $isOver = true;
                    break;
            }

            // Lọc cho cả Sản phẩm ĐƠN GIẢN (type = simple) VÀ Sản phẩm BIẾN THỂ (type = variable)
            $q->where(function($subQ) use ($minPrice, $maxPrice, $isOver) {
                
                // Trường hợp 1: Sản phẩm Đơn giản (Lấy giá từ bảng products)
                $subQ->where('type', 'simple')
                     ->where(function($simpleQ) use ($minPrice, $maxPrice, $isOver) {
                         $priceColumn = \DB::raw('COALESCE(NULLIF(sale_price, 0), price)');
                         if ($isOver) {
                             $simpleQ->where($priceColumn, '>=', $minPrice);
                         } else {
                             $simpleQ->whereBetween($priceColumn, [$minPrice, $maxPrice]);
                         }
                     });
                     
                // Trường hợp 2: HOẶC Sản phẩm Biến thể (Phải chui vào bảng product_variants để tìm)
                $subQ->orWhere(function($varQ) use ($minPrice, $maxPrice, $isOver) {
                    $varQ->where('type', 'variable')
                         ->whereHas('variants', function($variantQ) use ($minPrice, $maxPrice, $isOver) {
                             $variantPriceColumn = \DB::raw('COALESCE(NULLIF(sale_price, 0), price)');
                             if ($isOver) {
                                 $variantQ->where($variantPriceColumn, '>=', $minPrice);
                             } else {
                                 $variantQ->whereBetween($variantPriceColumn, [$minPrice, $maxPrice]);
                             }
                         });
                });
                
            });
        });
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

    public function searchSuggestions(Request $request)
    {
        $keyword = trim((string) $request->input('q', ''));

        $trending = OrderItem::query()
            ->selectRaw('product_name, SUM(quantity) as sold_qty')
            ->groupBy('product_name')
            ->orderByDesc('sold_qty')
            ->limit(6)
            ->pluck('product_name')
            ->values();

        $bestSellerIds = OrderItem::query()
            ->selectRaw('product_id, SUM(quantity) as sold_qty')
            ->whereNotNull('product_id')
            ->groupBy('product_id')
            ->orderByDesc('sold_qty')
            ->limit(4)
            ->pluck('product_id')
            ->toArray();

        $bestSellerProducts = Product::query()
            ->whereIn('id', $bestSellerIds)
            ->where('status', 'active')
            ->get(['id', 'name', 'slug', 'price', 'sale_price', 'thumbnail'])
            ->sortBy(function ($p) use ($bestSellerIds) {
                return array_search($p->id, $bestSellerIds, true);
            })
            ->values()
            ->map(function ($p) {
                return [
                    'name' => $p->name,
                    'url' => route('client.product.detail', ['slug' => $p->slug ?: $p->id]),
                    'price' => (int) ($p->sale_price > 0 ? $p->sale_price : $p->price),
                    'thumbnail' => $p->thumbnail ? asset('storage/' . ltrim($p->thumbnail, '/')) : null,
                ];
            });

        $suggestions = collect();
        if ($keyword !== '') {
            $tokens = collect(preg_split('/\s+/u', Str::lower($keyword)))
                ->filter(fn($token) => mb_strlen($token) >= 2)
                ->values();

            $candidateQuery = Product::query()
                ->where('status', 'active')
                ->where(function ($q) use ($keyword, $tokens) {
                    $q->where('name', 'like', '%' . $keyword . '%')
                      ->orWhere('slug', 'like', '%' . Str::slug($keyword) . '%')
                      ->orWhere('sku', 'like', '%' . $keyword . '%');
                    foreach ($tokens as $token) {
                        $q->orWhere('name', 'like', '%' . $token . '%');
                    }
                })
                ->limit(20)
                ->get(['id', 'name', 'slug']);

            $suggestions = $candidateQuery
                ->map(function ($p) use ($keyword) {
                    similar_text(Str::lower($keyword), Str::lower($p->name), $percent);
                    return [
                        'name' => $p->name,
                        'url' => route('client.product.detail', ['slug' => $p->slug ?: $p->id]),
                        'score' => (float) $percent,
                    ];
                })
                ->sortByDesc('score')
                ->take(6)
                ->values()
                ->map(fn($item) => [
                    'name' => $item['name'],
                    'url' => $item['url'],
                ]);
        }

        return response()->json([
            'query' => $keyword,
            'suggestions' => $suggestions,
            'trending' => $trending,
            'best_sellers' => $bestSellerProducts,
        ]);
    }
}