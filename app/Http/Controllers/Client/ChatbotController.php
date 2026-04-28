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

        $userMessage = trim($request->message);

        $faq = $this->searchFaq($userMessage);
        if ($faq) {
            $payload = [
                'reply' => $faq->answer,
                'type' => 'faq',
            ];

            if ($this->isProductQuery($userMessage)) {
                $payload = array_merge($payload, $this->resolveSuggestedProductPayload($userMessage));
            } else {
                $payload['products'] = [];
                $payload['best_sellers'] = [];
            }

            return response()->json($payload);
        }

        if ($this->isProductQuery($userMessage)) {
            $products = $this->getSuggestedProducts($userMessage);
            
            if (empty($products)) {
                $similarProducts = $this->findSimilarProducts($userMessage);
                $reply = count($similarProducts) > 0
                    ? 'Dạ em chưa tìm thấy sản phẩm chính xác. Nhưng em tìm được một số sản phẩm tương tự bên dưới. Anh/chị có quan tâm không ạ?'
                    : 'Dạ em chưa tìm thấy sản phẩm anh/chị đang tìm. Anh/chị có thể thử mô tả rõ hơn hoặc liên hệ nhân viên để được tư vấn chi tiết nhé ạ.';
                
                return response()->json([
                    'reply' => $reply,
                    'type' => 'product-notfound',
                    'products' => $similarProducts,
                    'best_sellers' => count($similarProducts) ? $this->getBestSellingProducts() : [],
                ]);
            }

            return response()->json([
                'reply' => 'Dạ, em đã tìm được một số sản phẩm phù hợp. Anh/chị vui lòng bấm vào để xem chi tiết hoặc thử tiếp với tên mẫu/hãng rõ hơn nhé ạ.',
                'type' => 'product',
                'products' => $products,
                'best_sellers' => $this->getBestSellingProducts(),
            ]);
        }

        $reply = $this->callGemini($userMessage);
        if ($reply === null) {
            return response()->json([
                'reply' => 'Dạ em chưa hiểu ý anh/chị lắm ạ, anh/chị có thể hỏi rõ hơn về bảo hành, giao hàng hoặc sản phẩm được không ạ?',
                'type' => 'error',
                'products' => [],
                'best_sellers' => [],
            ]);
        }

        return response()->json([
            'reply' => $reply,
            'type' => $this->isPolicyQuery($userMessage) ? 'policy' : 'ai',
            'products' => [],
            'best_sellers' => [],
        ]);
    }

    private function callGemini(string $message): ?string
    {
        $keys = [
            env('GEMINI_API_KEY_1'),
            env('GEMINI_API_KEY_2'),
            env('GEMINI_API_KEY_3'),
            env('GEMINI_API_KEY_4'),
        ];

        $validKeys = array_values(array_filter($keys, fn($key) => !empty($key)));
        if (empty($validKeys)) {
            return null;
        }

        $apiKey = $validKeys[array_rand($validKeys)];
        $prompt = $this->buildGeminiPrompt($message);

        try {
            $response = Http::withoutVerifying()->withHeaders([
                'Content-Type' => 'application/json',
            ])->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}", [
                'contents' => [
                    ['parts' => [['text' => $prompt]]]
                ]
            ]);

            if ($response->failed()) {
                return null;
            }

            $result = $response->json();
            $reply = $result['candidates'][0]['content']['parts'][0]['text'] ?? null;
            if (!is_string($reply) || trim($reply) === '') {
                return null;
            }

            return trim(str_replace(['**', '*'], '', $reply));
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function buildGeminiPrompt(string $message): string
    {
        if ($this->isPolicyQuery($message)) {
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

            return "Bạn là trợ lý ảo chăm sóc khách hàng của BeePhone. Nhiệm vụ của bạn là giải đáp mọi thắc mắc liên quan đến chính sách, đổi trả, bảo hành, vận chuyển và thanh toán của BeePhone.\n\n"
                . "Dựa hoàn toàn vào cẩm nang chính sách dưới đây để trả lời. Không được tự bịa thêm thông tin ngoài phạm vi chính sách.\n\n"
                . "Cẩm nang chính sách:\n" . $storePolicies . "\n"
                . "Câu hỏi của khách: {$message}";
        }

        return "Bạn là trợ lý AI bán hàng của BeePhone. Nhiệm vụ của bạn là giúp khách hàng trả lời nhanh các câu hỏi về sản phẩm, mua hàng, thanh toán, giao hàng và dịch vụ.\n"
            . "Trả lời ngắn gọn, lịch sự, thân thiện, bằng tiếng Việt, không dùng markdown.\n"
            . "Nếu khách hỏi về sản phẩm, hãy trả lời bằng cách gợi ý cách tìm trên website hoặc cung cấp thông tin sơ bộ nhất có thể.\n"
            . "Nếu không biết câu trả lời, hãy mời khách liên hệ nhân viên hỗ trợ.\n\n"
            . "Câu hỏi của khách: {$message}";
    }

    private function isPolicyQuery(string $message): bool
    {
        $normalized = $this->normalizeText($message);
        if ($normalized === '') {
            return false;
        }

        $keywords = [
            'chính sách', 'bảo hành', 'đổi trả', 'hoàn tiền', 'vận chuyển', 'ship', 'giao hàng',
            'thanh toán', 'trả góp', 'bảo mật', 'hướng dẫn', 'phiếu', 'giá', 'trả lại', 'hủy', 'tư vấn',
            'khiếu nại', 'trường hợp', 'điều khoản', 'bảo hiểm', 'mua hàng', 'đơn hàng'
        ];

        foreach ($keywords as $keyword) {
            if (str_contains($normalized, $keyword)) {
                return true;
            }
        }

        return false;
    }

    // ==========================================
    // HELPER: TÌM KIẾM FAQ MATCH KEYWORD
    // ==========================================

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

    private function isProductQuery(string $message): bool
    {
        $normalized = $this->normalizeText($message);
        if ($normalized === '') {
            return false;
        }

        $keywords = [
            'iphone', 'samsung', 'xiaomi', 'oppo', 'vivo', 'realme', 'huawei', 'nokia',
            'điện thoại', 'smartphone', 'máy', 'giá', 'mua', 'tư vấn', 'đề xuất', 'hot',
            'xách tay', 'bán chạy', 'model', 'pro', 'plus', 'max', 'tai nghe', 'airpods', 'buds', 'headphone', 'headset', 'earbud', 'earbuds', 'earphone'
        ];

        foreach ($keywords as $keyword) {
            if (str_contains($normalized, $keyword)) {
                return true;
            }
        }

        return false;
    }

    private function detectProductType(string $message): ?string
    {
        $normalized = $this->normalizeText($message);

        $headphoneTerms = ['tai nghe', 'airpods', 'buds', 'earbud', 'earbuds', 'earphone', 'headphone', 'headset'];
        foreach ($headphoneTerms as $term) {
            if (str_contains($normalized, $term)) {
                return 'headphone';
            }
        }

        return null;
    }

    private function getSearchTokens(string $text): array
    {
        $normalized = $this->normalizeText($text);
        if ($normalized === '') {
            return [];
        }

        $stopwords = [
            'mua', 'giá', 'tư', 'vấn', 'đề', 'xuất', 'hot', 'bán', 'chạy',
            'xách', 'tay', 'có', 'không', 'giúp', 'anh', 'chị', 'em', 'nào',
            'gì', 'cần', 'muốn', 'như', 'nên', 'nhất', 'hàng', 'shop', 'mới',
            'cũ', 'phụ', 'kiện', 'sản', 'phẩm', 'mình', 'tôi', 'anhchị', 'anh/chị',
            'vui', 'lòng', 'xem', 'thêm', 'chi', 'tiết', 'website', 'bấm', 'vào', 'ạ', 'nhé'
        ];

        $tokens = array_values(array_filter(explode(' ', $normalized), function ($token) use ($stopwords) {
            return mb_strlen($token, 'UTF-8') >= 2 && !in_array($token, $stopwords, true);
        }));

        return array_unique($tokens);
    }

    private function findSimilarProducts(string $message, int $limit = 3): array
    {
        $normalized = $this->normalizeText($message);
        $productType = $this->detectProductType($message);

        if ($productType === 'headphone') {
            $query = Product::query()
                ->with(['brand:id,name', 'variants:id,product_id,price,sale_price', 'images:id,product_id,path'])
                ->where('status', 'active')
                ->where(function ($q) {
                    $q->where('name', 'like', '%tai nghe%')
                        ->orWhere('name', 'like', '%airpods%')
                        ->orWhere('name', 'like', '%buds%')
                        ->orWhere('name', 'like', '%headphone%');
                })
                ->where('name', 'not like', '%iphone%')
                ->where('name', 'not like', '%samsung%')
                ->where('name', 'not like', '%điện thoại%');

            $products = $query->latest('id')->limit($limit)->get();
        } else {
            $brands = ['iphone', 'samsung', 'xiaomi', 'oppo', 'vivo', 'realme', 'huawei', 'nokia'];
            $foundBrand = null;

            foreach ($brands as $brand) {
                if (str_contains($normalized, $brand)) {
                    $foundBrand = $brand;
                    break;
                }
            }

            if ($foundBrand) {
                $query = Product::query()
                    ->with(['brand:id,name', 'variants:id,product_id,price,sale_price', 'images:id,product_id,path'])
                    ->where('status', 'active')
                    ->where(function ($q) use ($foundBrand) {
                        $q->where('name', 'like', '%' . $foundBrand . '%')
                            ->orWhere('description', 'like', '%' . $foundBrand . '%');
                    });

                $products = $query->latest('id')->limit($limit)->get();
            } else {
                $products = Product::query()
                    ->with(['brand:id,name', 'variants:id,product_id,price,sale_price', 'images:id,product_id,path'])
                    ->where('status', 'active')
                    ->latest('id')
                    ->limit($limit)
                    ->get();
            }
        }

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

    private function getSuggestedProducts(string $message, int $limit = 3): array
    {
        $normalized = $this->normalizeText($message);
        $tokens = $this->getSearchTokens($message);

        if (empty($tokens)) {
            return [];
        }

        $query = Product::query()
            ->with(['brand:id,name', 'variants:id,product_id,price,sale_price', 'images:id,product_id,path'])
            ->where('status', 'active');

        $productType = $this->detectProductType($message);
        if ($productType === 'headphone') {
            $headphoneTerms = ['tai nghe', 'airpods', 'buds', 'earbud', 'earbuds', 'earphone', 'headphone', 'headset'];
            $query->where(function ($q) use ($headphoneTerms) {
                foreach ($headphoneTerms as $term) {
                    $q->orWhere('name', 'like', '%' . $term . '%')
                        ->orWhere('description', 'like', '%' . $term . '%');
                }
            });

            $phoneExcludeTerms = ['iphone', 'samsung', 'xiaomi', 'oppo', 'vivo', 'realme', 'huawei', 'nokia', 'điện thoại', 'smartphone', 'máy'];
            foreach ($phoneExcludeTerms as $term) {
                $query->where('name', 'not like', '%' . $term . '%')
                    ->where('description', 'not like', '%' . $term . '%');
            }
        } else {
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

    private function resolveSuggestedProductPayload(string $message): array
    {
        $products = $this->getSuggestedProducts($message);
        return [
            'products' => $products,
            'best_sellers' => count($products) ? $this->getBestSellingProducts() : [],
        ];
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