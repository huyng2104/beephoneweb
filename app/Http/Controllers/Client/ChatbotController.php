<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Product; 

class ChatbotController extends Controller
{
    public function chat(Request $request)
    {
        $request->validate(['message' => 'required|string']);

        // Lấy tất cả Key trong .env vào 1 mảng
        $keys = [
            env('GEMINI_API_KEY_1'),
            env('GEMINI_API_KEY_2'),
            env('GEMINI_API_KEY_3')
        ];
        // Lọc bỏ những key rỗng (lỡ bro chỉ điền 2 key)
        $validKeys = array_filter($keys);
        
        // Random bốc đại 1 key ra để xài -> Tránh bị quá tải 1 key
        $apiKey = $validKeys[array_rand($validKeys)];
        $userMessage = $request->message;

        // ==========================================
        // 1. MÓC "TẤT TẦN TẬT" DỮ LIỆU (Giá, Tồn Kho, Cấu hình, Biến thể)
        // ==========================================
        $products = Product::with(['variants' => function($query) {
                $query->where('status', 'active')
                      ->with('attributeValues.attribute'); 
            }])
            ->where('status', 'active')
            // Lấy thêm 'stock' (tồn kho) và 'specifications' (Cấu hình)
            ->select('id', 'name', 'slug', 'type', 'price', 'sale_price', 'stock', 'specifications') 
            ->limit(30)
            ->get();

        $productListText = "";
        foreach ($products as $item) {
            $slug = $item->slug ? $item->slug : $item->id;
            $linkHtml = "<a href='/san-pham/{$slug}' target='_blank' style='color: #3b82f6; text-decoration: underline; font-weight: bold;'>{$item->name}</a>";

            $productListText .= "SẢN PHẨM: " . $item->name . "\n";
            $productListText .= "- Tên hiển thị (Kèm Link): " . $linkHtml . "\n";

            // Xử lý CẤU HÌNH (specifications) từ chuỗi JSON
            if (!empty($item->specifications)) {
                // Kiểm tra xem nó là chuỗi JSON hay đã được Laravel ép kiểu thành Mảng
                $specs = is_string($item->specifications) ? json_decode($item->specifications, true) : $item->specifications;
                if (is_array($specs) && count($specs) > 0) {
                    $specsArr = [];
                    foreach ($specs as $key => $val) {
                        $specsArr[] = "{$key}: {$val}";
                    }
                    $productListText .= "- Cấu hình chi tiết: " . implode(' | ', $specsArr) . "\n";
                }
            }

            // Xử lý BIẾN THỂ (Giá và Tồn kho từng loại)
            if ($item->type === 'variable' && $item->variants->isNotEmpty()) {
                $productListText .= "- Thông tin các phiên bản (Giá & Tồn kho):\n";
                $totalStock = 0; // Tính tổng tồn kho của dòng máy này

                foreach ($item->variants as $variant) {
                    $vPrice = ($variant->sale_price > 0 && $variant->sale_price < $variant->price) 
                                ? $variant->sale_price 
                                : $variant->price;
                    
                    $attrNames = [];
                    if ($variant->attributeValues && $variant->attributeValues->isNotEmpty()) {
                        foreach ($variant->attributeValues as $attrVal) {
                            $attrNames[] = $attrVal->value; 
                        }
                    }
                    
                    $variantName = !empty($attrNames) ? "Bản " . implode(' - ', $attrNames) : "Bản Tiêu Chuẩn";
                    $stockStatus = $variant->stock > 0 ? $variant->stock . " chiếc" : "Hết hàng";
                    $totalStock += $variant->stock;

                    $productListText .= "  + " . $variantName . " - Giá: " . number_format($vPrice, 0, ',', '.') . " VNĐ (Tồn kho: " . $stockStatus . ")\n";
                }
                $productListText .= "- Tổng tồn kho các phiên bản: " . $totalStock . " chiếc\n";
            } 
            // Xử lý MÁY THƯỜNG (Không có biến thể)
            else {
                $pPrice = ($item->sale_price > 0 && $item->sale_price < $item->price) 
                            ? $item->sale_price 
                            : $item->price;
                $stockStatus = $item->stock > 0 ? $item->stock . " chiếc" : "Hết hàng";

                $productListText .= "- Giá: " . number_format($pPrice, 0, ',', '.') . " VNĐ\n";
                $productListText .= "- Tồn kho: " . $stockStatus . "\n";
            }
            $productListText .= "\n";
        }

        // ==========================================
        // 2. PROMPT TƯ VẤN (Cập nhật độ thông minh)
        // ==========================================
        $prompt = "Bạn là chuyên viên tư vấn bán hàng của hệ thống điện thoại BeePhone. 
Dưới đây là DANH SÁCH SẢN PHẨM (Kèm Giá, Tồn Kho, Cấu Hình) đang bán tại cửa hàng:

" . $productListText . "

QUY TẮC TƯ VẤN (BẮT BUỘC):
1. BẮT BUỘC dùng đoạn mã HTML ở dòng 'Tên hiển thị (Kèm Link)' khi nhắc tên sản phẩm để khách click mua.
2. Nếu khách hỏi 'Còn hàng không?', hãy kiểm tra kỹ số lượng 'Tồn kho'. Nếu bằng 0 hoặc 'Hết hàng', hãy báo là 'Dạ bên em tạm hết, anh chị xem mẫu khác nhé'. Nếu còn, hãy báo 'Dạ bên em đang sẵn hàng ạ'.
3. Nếu khách hỏi về Cấu hình (Chip, RAM, Camera...), hãy đọc thông tin ở phần 'Cấu hình chi tiết' để tư vấn thật chuyên nghiệp.
4. Trả lời cực kỳ ngắn gọn, xưng 'em' và gọi khách là 'dạ anh/chị'. 
5. KHÔNG dùng ký tự markdown như ** hay * trong câu trả lời.

Câu hỏi của khách: " . $userMessage;

        // ==========================================
        // 3. GỌI API GEMINI 2.5 FLASH
        // ==========================================
        try {
          // Sửa lại thành gemini-2.5-flash nhé bro:
$response = Http::withoutVerifying()->withHeaders([
    'Content-Type' => 'application/json',
])->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}", [
                'contents' => [
                    ['parts' => [['text' => $prompt]]]
                ]
            ]);

            if ($response->failed()) {
                // Nếu dính lỗi 429 Quá tải của Google
                if ($response->status() == 429) {
                    return response()->json(['reply' => 'Dạ hiện tại lượng khách truy cập BeePhone đang quá đông, anh/chị vui lòng chờ em 10 giây rồi nhắn lại nhé ạ! 🥰']);
                }
                // Các lỗi khác
// Các lỗi khác
return response()->json(['reply' => 'Lỗi API thật sự là: ' . $response->body()]);            }
            $result = $response->json();
            
            if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
                $botReply = $result['candidates'][0]['content']['parts'][0]['text'];
                $botReply = str_replace(['**', '*'], '', $botReply);
                return response()->json(['reply' => $botReply]);
            }

            return response()->json(['reply' => 'Dạ em chưa hiểu ý anh/chị lắm ạ.']);

        } catch (\Exception $e) {
            return response()->json(['reply' => 'Hệ thống AI đang bảo trì: ' . $e->getMessage()]);
        }
    }
}