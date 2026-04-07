<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\SupportFaq;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ChatbotController extends Controller
{
    public function chat(Request $request)
    {
        $request->validate(['message' => 'required|string']);

        $userMessage = $request->message;

        // ==========================================
        // 1. KIỂM TRA FAQ TRƯỚC (CÁCH 1)
        // ==========================================
        $faq = $this->searchFaq($userMessage);
        if ($faq) {
            return response()->json([
                'reply' => $faq->answer,
                'type' => 'faq',
                'products' => $this->getSuggestedProducts($userMessage),
                'best_sellers' => $this->getBestSellingProducts(),
            ]);
        }

        // ==========================================
        // 2. NẾU KHÔNG MATCH FAQ, GỌI GEMINI
        // ==========================================
        // Lấy tất cả Key trong .env vào 1 mảng
        $keys = [
            env('GEMINI_API_KEY_1'),
            env('GEMINI_API_KEY_2'),
            env('GEMINI_API_KEY_3')
        ];
        // Lọc bỏ các key rỗng
        $validKeys = array_filter($keys, fn($k) => !empty($k));

        // Nếu không có key nào, không gọi Gemini
        if (empty($validKeys)) {
            return response()->json([
                'reply' => 'Hệ thống AI chưa được cấu hình API chìa khóa. Vui lòng thử lại sau hoặc liên hệ quản trị.',
                'type' => 'error',
                'products' => $this->getSuggestedProducts($userMessage),
                'best_sellers' => $this->getBestSellingProducts(),
            ]);
        }

        // Random bốc đại 1 key ra để xài -> Tránh quá tải 1 key
        $apiKey = $validKeys[array_rand($validKeys)];

        // ==========================================
        // 1. CẨM NANG CHÍNH SÁCH CỬA HÀNG BEEPHONE
        // (Bro có thể tự sửa lại nội dung này cho khớp với đồ án)
        // ==========================================
        $storePolicies = "
CHÍNH SÁCH BẢO HÀNH:
- Máy mới chính hãng: Bảo hành 12 tháng tại các trung tâm bảo hành của hãng trên toàn quốc. Lỗi 1 đổi 1 trong 30 ngày đầu tiên nếu có lỗi từ nhà sản xuất.
- Máy cũ/Like New: Bảo hành 6 tháng tại BeePhone. Lỗi 1 đổi 1 trong 15 ngày đầu tiên.
- Phụ kiện (sạc, cáp, tai nghe): Bảo hành 3 tháng, 1 đổi 1.
- Không bảo hành các trường hợp: Rơi vỡ, cấn móp, vào nước, tự ý tháo máy hoặc can thiệp phần mềm (Root, Jailbreak).

CHÍNH SÁCH ĐỔI TRẢ & HOÀN TIỀN:
- Khách hàng được quyền trả hàng và hoàn tiền 100% trong 7 ngày đầu nếu sản phẩm chưa bóc seal (còn nguyên tem mác).
- Nếu đã bóc seal nhưng máy không lỗi mà khách muốn trả: Thu phí chiết khấu 20% giá trị máy.
- Thời gian hoàn tiền: Từ 3 - 5 ngày làm việc qua tài khoản ngân hàng.

CHÍNH SÁCH VẬN CHUYỂN (GIAO HÀNG):
- Giao hàng Hỏa tốc: Nhận hàng trong 2 giờ (Chỉ áp dụng nội thành Hà Nội và TP.HCM), phí ship 30.000đ.
- Giao hàng Tiêu chuẩn: Giao toàn quốc từ 2 - 4 ngày. Miễn phí vận chuyển (Freeship) cho mọi đơn hàng.
- Đồng kiểm: Khách hàng được quyền kiểm tra ngoại quan gói hàng trước khi thanh toán, KHÔNG được bóc seal hộp điện thoại nếu chưa thanh toán.

CHÍNH SÁCH THANH TOÁN & TRẢ GÓP:
- Các hình thức thanh toán: Tiền mặt khi nhận hàng (COD), Chuyển khoản VNPAY, Thanh toán qua Ví Bee Pay.
- Hỗ trợ trả góp 0% qua thẻ tín dụng hoặc trả góp qua công ty tài chính (Home Credit, HD Saison) chỉ cần CCCD.

CHƯƠNG TRÌNH KHUYẾN MÃI & TÍCH ĐIỂM (BEE POINT):
- Mua hàng tích điểm: Cứ 100.000đ thanh toán thành công sẽ được tích 1 Bee Point.
- Đổi điểm: Dùng Bee Point để đổi lấy các mã giảm giá (Voucher) cực xịn trong mục 'Ví Bee Point'.
";

        // ==========================================
        // 2. PROMPT "THIẾT QUÂN LUẬT" CHO AI
        // ==========================================
        $prompt = "Bạn là trợ lý ảo chăm sóc khách hàng của hệ thống điện thoại BeePhone. Nhiệm vụ duy nhất của bạn là giải đáp các thắc mắc của khách hàng về CHÍNH SÁCH của cửa hàng.

Dưới đây là Cẩm nang chính sách của BeePhone:
\"\"\"
" . $storePolicies . "
\"\"\"

QUY TẮC TRẢ LỜI (BẮT BUỘC PHẢI TUÂN THỦ):
1. Dựa hoàn toàn vào 'Cẩm nang chính sách' bên trên để trả lời. Không được tự bịa ra chính sách khác.
2. NẾU KHÁCH HỎI VỀ SẢN PHẨM CỤ THỂ (VD: iPhone 15 giá bao nhiêu, Samsung có hàng không, Tư vấn mua máy...): BẮT BUỘC phải từ chối khéo léo và đáp rằng: 'Dạ, em là trợ lý chuyên giải đáp chính sách cửa hàng. Để xem giá và tình trạng hàng hóa, anh/chị vui lòng gõ tên máy vào thanh tìm kiếm trên Website giúp em nhé ạ! 🥰'
3. Luôn xưng hô là 'em' và gọi khách là 'anh/chị'. Thái độ cực kỳ lịch sự, thân thiện và nhiệt tình.
4. Trả lời cực kỳ NGẮN GỌN, xúc tích, đi thẳng vào vấn đề khách hỏi.
5. Tuyệt đối KHÔNG dùng các ký tự markdown như ** hay * trong câu trả lời để tránh lỗi hiển thị.

Câu hỏi của khách: " . $userMessage;

        // ==========================================
        // 3. GỌI API GEMINI 2.5 FLASH
        // ==========================================
        try {
            $response = Http::withoutVerifying()->withHeaders([
                'Content-Type' => 'application/json',
            ])->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}", [
                'contents' => [
                    ['parts' => [['text' => $prompt]]]
                ]
            ]);

            if ($response->failed()) {
                if ($response->status() == 429) {
                    return response()->json([
                        'reply' => 'Dạ hiện tại lượng khách truy cập BeePhone đang quá đông, anh/chị vui lòng chờ em 10 giây rồi nhắn lại nhé ạ! 🥰',
                        'products' => $this->getSuggestedProducts($userMessage),
                        'best_sellers' => $this->getBestSellingProducts(),
                    ]);
                }
                return response()->json([
                    'reply' => 'Lỗi API thật sự là: ' . $response->body(),
                    'products' => $this->getSuggestedProducts($userMessage),
                    'best_sellers' => $this->getBestSellingProducts(),
                ]);
            }
            
            $result = $response->json();
            
            if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
                $botReply = $result['candidates'][0]['content']['parts'][0]['text'];
                // Dọn dẹp markdown nếu AI vẫn cố tình trả về
                $botReply = str_replace(['**', '*'], '', $botReply);
                return response()->json([
                    'reply' => $botReply,
                    'products' => $this->getSuggestedProducts($userMessage),
                    'best_sellers' => $this->getBestSellingProducts(),
                ]);
            }

            return response()->json([
                'reply' => 'Dạ em chưa hiểu ý anh/chị lắm ạ, anh chị có thể hỏi rõ hơn về bảo hành, giao hàng hay thanh toán không ạ?',
                'products' => $this->getSuggestedProducts($userMessage),
                'best_sellers' => $this->getBestSellingProducts(),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'reply' => 'Hệ thống AI đang bảo trì: ' . $e->getMessage(),
                'products' => $this->getSuggestedProducts($userMessage),
                'best_sellers' => $this->getBestSellingProducts(),
            ]);
        }
    }

    // ==========================================
    // HELPER: TÌM KIẾM FAQ MATCH KEYWORD
    // ==========================================
    private function searchFaq($userMessage)
    {
        $normalizedMessage = $this->normalizeText($userMessage);
        if ($normalizedMessage === '') {
            return null;
        }

        // Dùng cùng nguồn dữ liệu FAQ với trang admin
        $faqs = SupportFaq::active()
            ->ordered()
            ->get();

        $bestMatch = null;
        $bestScore = 0.0;

        foreach ($faqs as $faq) {
            $score = 0.0;
            $question = $this->normalizeText((string) $faq->question);

            // Ưu tiên match cụm từ đầy đủ trong keywords/question
            if ($question !== '' && (str_contains($question, $normalizedMessage) || str_contains($normalizedMessage, $question))) {
                $score += 2.5;
            }

            if (!empty($faq->keywords)) {
                $keywords = array_map('trim', explode(',', $faq->keywords));
                foreach ($keywords as $keyword) {
                    $kw = $this->normalizeText($keyword);
                    if ($kw === '') {
                        continue;
                    }

                    if (str_contains($kw, ' ')) {
                        if (str_contains($normalizedMessage, $kw)) {
                            $score += 3.0;
                        }
                    } else {
                        if (preg_match('/\b' . preg_quote($kw, '/') . '\b/u', $normalizedMessage)) {
                            $score += 2.0;
                        } elseif (str_contains($normalizedMessage, $kw)) {
                            $score += 1.0;
                        }
                    }
                }
            }

            if ($score > $bestScore) {
                $bestScore = $score;
                $bestMatch = $faq;
            }
        }

        return $bestScore >= 1.5 ? $bestMatch : null;
    }

    private function normalizeText(string $text): string
    {
        $text = mb_strtolower(trim($text), 'UTF-8');
        $text = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $text);
        $text = preg_replace('/\s+/u', ' ', $text);
        return trim($text);
    }

    private function getSuggestedProducts(string $message, int $limit = 3): array
    {
        $normalized = $this->normalizeText($message);
        $tokens = array_values(array_filter(explode(' ', $normalized), fn($t) => mb_strlen($t, 'UTF-8') >= 2));

        $query = Product::query()
            ->with(['brand:id,name', 'variants:id,product_id,price,sale_price', 'images:id,product_id,path'])
            ->where('status', 'active');

        if (!empty($tokens)) {
            $query->where(function ($q) use ($tokens) {
                foreach ($tokens as $token) {
                    $q->orWhere('name', 'like', '%' . $token . '%')
                        ->orWhere('description', 'like', '%' . $token . '%');
                }
            });
        }

        $products = $query->latest('id')->limit($limit)->get();

        return $products->map(function (Product $product) {
            $variant = $product->variants->first();
            $price = $variant ? ($variant->sale_price ?: $variant->price) : 0;
            $thumbnail = optional($product->images->first())->path ?: $product->thumbnail;

            return [
                'name' => $product->name,
                'url' => route('client.product.detail', ['id' => $product->slug ?: $product->id]),
                'price' => $price,
                'thumbnail' => $thumbnail ? asset('storage/' . ltrim($thumbnail, '/')) : null,
            ];
        })->values()->all();
    }

    private function getBestSellingProducts(int $limit = 3): array
    {
        $bestSellerIds = OrderItem::query()
            ->selectRaw('product_id, SUM(quantity) as total_qty')
            ->whereNotNull('product_id')
            ->whereHas('order', function ($q) {
                $q->whereIn('status', [Order::STATUS_DELIVERED, Order::STATUS_RECEIVED]);
            })
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->limit($limit)
            ->pluck('product_id')
            ->all();

        if (empty($bestSellerIds)) {
            return [];
        }

        $products = Product::query()
            ->with(['variants:id,product_id,price,sale_price', 'images:id,product_id,path'])
            ->whereIn('id', $bestSellerIds)
            ->where('status', 'active')
            ->get()
            ->keyBy('id');

        $sorted = [];
        foreach ($bestSellerIds as $id) {
            if (isset($products[$id])) {
                $sorted[] = $products[$id];
            }
        }

        return collect($sorted)->map(function (Product $product) {
            $variant = $product->variants->first();
            $price = $variant ? ($variant->sale_price ?: $variant->price) : 0;
            $thumbnail = optional($product->images->first())->path ?: $product->thumbnail;

            return [
                'name' => $product->name,
                'url' => route('client.product.detail', ['id' => $product->slug ?: $product->id]),
                'price' => $price,
                'thumbnail' => $thumbnail ? asset('storage/' . ltrim($thumbnail, '/')) : null,
            ];
        })->values()->all();
    }
}