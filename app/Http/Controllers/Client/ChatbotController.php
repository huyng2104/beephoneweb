<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
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

        // 0. KIỂM TRA KHÁCH HỎI VỀ SẢN PHẨM KHÔNG BÁN

        $notAllowedResponse = $this->checkNotAllowedProduct($userMessage);
        if ($notAllowedResponse) {
            return response()->json([
                'reply' => $notAllowedResponse,
                'type' => 'product-notfound',
                'products' => [],
                'best_sellers' => [],
            ]);
        }

        // ==========================================
        // 0.5. KIỂM TRA CÓ PHẢI HỎI SẢN PHẨM CỤ THỂ KHÔNG
        // ==========================================
        if ($this->isProductQuery($userMessage)) {
            $products = $this->getSuggestedProducts($userMessage);
            
            if (!empty($products)) {
                return response()->json([
                    'reply' => 'Dạ, em đã tìm được một số sản phẩm phù hợp. Anh/chị vui lòng bấm vào để xem chi tiết nhé ạ!',
                    'type' => 'product',
                    'products' => $products,
                    'best_sellers' => $this->getBestSellingProducts(),
                ]);
            }
            // Nếu không tìm được, cứ tiếp tục kiểm tra FAQ & Gemini
        }

        // ==========================================
        // 1. KIỂM TRA FAQ TRƯỚC
        // ==========================================
        $faq = $this->findBestFaqMatch($userMessage);
        if ($faq) {
            // Chỉ thêm sản phẩm nếu hỏi về sản phẩm cụ thể, KHÔNG phải chính sách
            $products = [];
            $bestSellers = [];
            if ($this->isProductQuery($userMessage) && !$this->isPolicyQuery($userMessage)) {
                $products = $this->getSuggestedProducts($userMessage);
                $bestSellers = $this->getBestSellingProducts();
            }
            
            return response()->json([
                'reply' => $faq->answer,
                'type' => 'faq',
                'products' => $products,
                'best_sellers' => $bestSellers,
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
        // 3. GỌI API GEMINI VỚI RETRY MECHANISM
        // ==========================================
        $reply = $this->callGeminiWithRetry($userMessage, $prompt, $validKeys);
        
        if ($reply) {
            // Chỉ thêm sản phẩm nếu hỏi về sản phẩm cụ thể, KHÔNG phải chính sách
            $products = [];
            $bestSellers = [];
            if ($this->isProductQuery($userMessage) && !$this->isPolicyQuery($userMessage)) {
                $products = $this->getSuggestedProducts($userMessage);
                $bestSellers = $this->getBestSellingProducts();
            }
            
            return response()->json([
                'reply' => $reply,
                'type' => $this->isPolicyQuery($userMessage) ? 'policy' : 'ai',
                'products' => $products,
                'best_sellers' => $bestSellers,
            ]);
        }

        // ==========================================
        // 4. NẾU GEMINI THẤT BẠI, FALLBACK TỚI FAQ SEARCH
        // ==========================================
        $fallbackFaq = $this->findBestFaqMatch($userMessage);
        if ($fallbackFaq) {
            // Chỉ thêm sản phẩm nếu hỏi về sản phẩm cụ thể, KHÔNG phải chính sách
            $products = [];
            $bestSellers = [];
            if ($this->isProductQuery($userMessage) && !$this->isPolicyQuery($userMessage)) {
                $products = $this->getSuggestedProducts($userMessage);
                $bestSellers = $this->getBestSellingProducts();
            }
            
            return response()->json([
                'reply' => $fallbackFaq->answer,
                'type' => 'faq',
                'products' => $products,
                'best_sellers' => $bestSellers,
            ]);
        }

        // ==========================================
        // 5. NẾU HẾT CÁC CÁCH, TRẢ LỖI THÂN THIỆN
        // ==========================================
        return response()->json([
            'reply' => 'Dạ em chưa hiểu ý anh/chị lắm ạ, anh chị có thể hỏi rõ hơn về bảo hành, giao hàng hay thanh toán không ạ?',
            'type' => 'error',
            'products' => [],
            'best_sellers' => [],
        ]);
    }

    // ==========================================
    // HELPER: GỌI GEMINI VỚI TỰ ĐỘNG RETRY
    // ==========================================
    private function callGeminiWithRetry(string $userMessage, string $prompt, array $validKeys, int $maxRetries = 3): ?string
    {
        for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
            $apiKey = $validKeys[array_rand($validKeys)];
            
            try {
                $response = Http::withoutVerifying()
                    ->timeout(10)
                    ->withHeaders(['Content-Type' => 'application/json'])
                    ->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}", [
                        'contents' => [
                            ['parts' => [['text' => $prompt]]]
                        ]
                    ]);

                // Nếu thành công
                if ($response->successful()) {
                    $result = $response->json();
                    
                    if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
                        $botReply = $result['candidates'][0]['content']['parts'][0]['text'];
                        // Dọn dẹp markdown
                        $botReply = str_replace(['**', '*'], '', $botReply);
                        return $botReply;
                    }
                }

                // Nếu lỗi 429 hoặc 503, thử lại
                if ($response->status() == 429 || $response->status() == 503) {
                    if ($attempt < $maxRetries) {
                        sleep(1); // Chờ 1 giây rồi thử lại
                        continue;
                    }
                }

                // Nếu lỗi auth, không cần retry
                if ($response->status() == 401 || $response->status() == 403) {
                    return null;
                }

            } catch (\Exception $e) {
                // Nếu timeout hoặc lỗi connection, thử lại
                if ($attempt < $maxRetries) {
                    sleep(1);
                    continue;
                }
            }
        }

        return null;
    }

    // ==========================================
    // HELPER: TÌM FAQ TỐT NHẤT CÓ SẴN (FALLBACK)
    // ==========================================
    private function findBestFaqMatch(string $userMessage): ?SupportFaq
    {
        $normalizedMessage = $this->normalizeText($userMessage);
        if ($normalizedMessage === '') {
            return null;
        }

        $faqs = SupportFaq::active()->ordered()->get();
        $bestMatch = null;
        $bestScore = 0;

        foreach ($faqs as $faq) {
            $score = 0;
            $normalizedQuestion = $this->normalizeText($faq->question);

            // So sánh trực tiếp
            similar_text($normalizedMessage, $normalizedQuestion, $percent);
            $score += $percent / 100 * 5;

            // Kiểm tra từ khóa
            if (!empty($faq->keywords)) {
                $keywords = array_map('trim', explode(',', $faq->keywords));
                foreach ($keywords as $keyword) {
                    $kw = $this->normalizeText($keyword);
                    if (!empty($kw) && str_contains($normalizedMessage, $kw)) {
                        $score += 2;
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

    // ==========================================
    // HELPER: KIỂM TRA SẢN PHẨM KHÔNG BÁN (DỰA VÀO DATABASE)
    // ==========================================
    private function checkNotAllowedProduct(string $message): ?string
    {
        $normalized = $this->normalizeText($message);
        
        // Lấy tất cả categories có sản phẩm active trong hệ thống
        $validCategories = Category::query()
            ->whereHas('products', function ($q) {
                $q->where('status', 'active');
            })
            ->pluck('name')
            ->map(fn($name) => $this->normalizeText($name))
            ->toArray();

        // Lấy tất cả brand name có sản phẩm active
        $validBrands = Brand::query()
            ->whereHas('products', function ($q) {
                $q->where('status', 'active');
            })
            ->pluck('name')
            ->map(fn($name) => $this->normalizeText($name))
            ->toArray();

        // Tổng hợp từ khóa sản phẩm được bán
        $allowedKeywords = array_merge($validCategories, $validBrands, [
            'điện thoại', 'smartphone', 'tai nghe', 'headphone', 'phụ kiện'
        ]);

        // Kiểm tra từ khóa không được phép
        $notAllowedKeywords = [
            'tủ lạnh', 'máy lạnh', 'máy giặt', 'máy sấy',
            'lò nướng', 'ti vi', 'tivi', 'truyền hình',
            'laptop', 'máy tính', 'desktop',
            'loa', 'loa phát thanh', 'speaker', 'âm thanh'
        ];

        foreach ($notAllowedKeywords as $keyword) {
            if (str_contains($normalized, $keyword)) {
                return 'Dạ, cửa hàng em chuyên bán điện thoại, tai nghe và các phụ kiện. Em chưa bán ' . $keyword . '. Anh/chị có thể tìm hiểu các sản phẩm khác trên Website của em nhé ạ! 🥰';
            }
        }

        return null;
    }

    private function normalizeText(string $text): string
    {
        $text = mb_strtolower(trim($text), 'UTF-8');
        $text = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $text);
        $text = preg_replace('/\s+/u', ' ', $text);
        return trim($text);
    }

    // ==========================================
    // HELPER: KIỂM TRA CÓ PHẢI HỎI VỀ SẢN PHẨM KHÔNG
    // ==========================================
    private function isProductQuery(string $message): bool
    {
        $normalized = $this->normalizeText($message);
        if ($normalized === '') {
            return false;
        }

        // Từ khóa sản phẩm được bán
        $keywords = [
            'iphone', 'samsung', 'xiaomi', 'oppo', 'vivo', 'realme', 'huawei', 'nokia',
            'điện thoại', 'smartphone', 'mua', 'tư vấn', 'đề xuất', 'gợi ý', 'hot', 'bán chạy',
            'tai nghe', 'airpods', 'buds', 'headphone', 'headset', 'earbud', 'earbuds', 'earphone',
            'giá', 'bao nhiêu', 'hàng', 'có', 'stock'
        ];

        foreach ($keywords as $keyword) {
            if (str_contains($normalized, $keyword)) {
                return true;
            }
        }

        return false;
    }


    private function getSuggestedProducts(string $message, int $limit = 3): array
    {
        $normalized = $this->normalizeText($message);
        $tokens = array_values(array_filter(explode(' ', $normalized), fn($t) => mb_strlen($t, 'UTF-8') >= 2));

        if (empty($tokens)) {
            return [];
        }

        // 1. Tìm kiếm theo category name (ưu tiên)
        $categories = Category::query()
            ->where(function ($q) use ($tokens) {
                foreach ($tokens as $token) {
                    $q->orWhere('name', 'like', '%' . $token . '%');
                }
            })
            ->pluck('id')
            ->toArray();

        if (!empty($categories)) {
            $products = Product::query()
                ->with(['brand:id,name', 'variants:id,product_id,price,sale_price', 'images:id,product_id,path'])
                ->where('status', 'active')
                ->whereHas('categories', function ($q) use ($categories) {
                    $q->whereIn('category_id', $categories);
                })
                ->latest('id')
                ->limit($limit)
                ->get();

            if (!$products->isEmpty()) {
                return $this->formatProducts($products);
            }
        }

        // 2. Fallback: Tìm kiếm theo brand name
        $brands = Brand::query()
            ->where(function ($q) use ($tokens) {
                foreach ($tokens as $token) {
                    $q->orWhere('name', 'like', '%' . $token . '%');
                }
            })
            ->pluck('id')
            ->toArray();

        if (!empty($brands)) {
            $products = Product::query()
                ->with(['brand:id,name', 'variants:id,product_id,price,sale_price', 'images:id,product_id,path'])
                ->where('status', 'active')
                ->whereIn('brand_id', $brands)
                ->latest('id')
                ->limit($limit)
                ->get();

            if (!$products->isEmpty()) {
                return $this->formatProducts($products);
            }
        }

        // 3. Cuối cùng: Tìm theo product name (fallback cuối)
        $products = Product::query()
            ->with(['brand:id,name', 'variants:id,product_id,price,sale_price', 'images:id,product_id,path'])
            ->where('status', 'active')
            ->where(function ($q) use ($tokens) {
                foreach ($tokens as $token) {
                    $q->orWhere('name', 'like', '%' . $token . '%');
                }
            })
            ->latest('id')
            ->limit($limit)
            ->get();

        return !$products->isEmpty() ? $this->formatProducts($products) : [];
    }

    // ==========================================
    // HELPER: Format danh sách sản phẩm
    // ==========================================
    private function formatProducts($products): array
    {
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

    // ==========================================
    // HELPER: KIỂM TRA CÓ PHẢI CÂUHỎI CHÍNH SÁCH KHÔNG
    // ==========================================
    private function isPolicyQuery(string $message): bool
    {
        $normalized = $this->normalizeText($message);

        $policyKeywords = [
            'bảo hành', 'đổi trả', 'hoàn tiền', 'giao hàng', 'vận chuyển',
            'thanh toán', 'trả góp', 'điều khoản', 'chính sách', 'freeship',
            'miễn phí', 'bao lâu', 'mất', 'thời gian'
        ];

        foreach ($policyKeywords as $keyword) {
            if (str_contains($normalized, $keyword)) {
                return true;
            }
        }

        return false;
    }
}