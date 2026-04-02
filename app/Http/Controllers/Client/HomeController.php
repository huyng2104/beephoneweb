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
        // 1. Lấy danh mục nổi bật (Lấy 4 cái để xếp vừa giao diện)
        $categories = Category::where('is_active', true)->take(4)->get();

        // 2. Lấy 8 sản phẩm mới nhất 
        // 💡 TINH TẾ: Dùng 'with' để kéo theo biến thể (variants) giúp View không bị giật lag (N+1 query) khi tìm giá Min!
       $newProducts = Product::with(['variants.attributeValues.attribute', 'brand', 'categories'])
            ->where('status', 'active') 
            ->orderBy('created_at', 'desc')
            ->take(8) 
            ->get();

        // 3. Lấy 3 bài viết tin tức mới nhất
        $news = Post::latest()->take(3)->get();

        // 4. Lấy thương hiệu
        $brands = Brand::where('is_active', 1)->orderBy('sort_order')->get();

        $banners = Banner::where('is_active', true)->latest()->take(5)->get();

        // Trả về view trang chủ (Sửa lại tên view nếu máy bro tên khác nha)

        // 5. Đẩy data ra ĐÚNG cái file index mới của bro
// Đã nhét thêm 'banners' vào vali để gửi ra ngoài
        return view('client.home.index', compact('categories', 'newProducts', 'news', 'brands', 'banners'));    }
}