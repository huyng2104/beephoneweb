<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
// Gọi các Model tương ứng (Bro nhớ tạo Model nhé)
use App\Models\Category;
use App\Models\Product;
use App\Models\Post;
use App\Models\Brand;
class HomeController extends Controller
{
    public function index()
    {
        // 1. Lấy 4 danh mục nổi bật
        $categories = Category::where('is_active', true)->take(4)->get();

        // 2. Lấy 4 sản phẩm mới nhất
      $newProducts = Product::where('status', 1)
                      ->orderBy('created_at', 'desc')
                      ->take(10)
                      ->get();

// HOẶC NẾU CHƯA CÓ cột trạng thái nào, thì xóa luôn lệnh where đi cho nhanh:
$newProducts = Product::orderBy('created_at', 'desc')
                      ->take(10)
                      ->get();
        // 3. Lấy 3 bài viết tin tức mới nhất
        $news = Post::latest()->take(3)->get();
$brands = Brand::where('is_active', 1)->orderBy('sort_order')->get();
        // Đẩy data ra view 'client.home'
        return view('client.home', compact('categories', 'newProducts', 'news', 'brands'));
    }
}
