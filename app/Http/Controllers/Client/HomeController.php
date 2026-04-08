<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;
use App\Models\Post;
use App\Models\Brand;
use App\Models\Banner;
class HomeController extends Controller
{
    public function index()
    {
        // 1. Lấy danh mục nổi bật (Sắp xếp theo số lượng sản phẩm)
        $categories = Category::withCount(['products' => function($query) {
            $query->where('status', 'active');
        }])
        ->where('is_active', true)
        ->orderBy('products_count', 'desc')
        ->take(5) 
        ->get();

        // 2. Lấy 8 sản phẩm mới nhất
        $newProducts = Product::with(['variants.attributeValues.attribute', 'brand', 'categories'])
            ->where('status', 'active') 
            ->orderBy('created_at', 'desc')
            ->take(8) 
            ->get();

        // 3. Lấy 8 sản phẩm nổi bật
        $featuredProducts = Product::with(['variants.attributeValues.attribute', 'brand', 'categories'])
            ->where('status', 'active')
            ->where('is_featured', true)
            ->orderBy('created_at', 'desc')
            ->take(8)
            ->get();

        // 4. Lấy 3 bài viết tin tức mới nhất
        $news = Post::latest()->take(3)->get();

        // 5. Lấy thương hiệu (Sắp xếp theo số lượng sản phẩm)
        $brands = Brand::withCount(['products' => function($query) {
            $query->where('status', 'active');
        }])
        ->where('is_active', 1)
        ->orderBy('products_count', 'desc')
        ->take(5)
        ->get();

        // 6. Lấy Banner
        $banners = Banner::where('is_active', true)->latest()->take(5)->get();

        return view('client.home.index', compact(
            'categories', 
            'newProducts', 
            'featuredProducts', 
            'news', 
            'brands', 
            'banners'
        ));
    }
}