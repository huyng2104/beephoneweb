<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SupportFaq;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ChatbotController extends Controller
{
    private $categoryNames = [
        'shipping' => 'Giao hàng',
        'warranty' => 'Bảo hành',
        'payment' => 'Thanh toán',
        'return' => 'Đổi trả',
    ];

    /**
     * Get all available categories
     */
    public function getCategories()
    {
        $categories = SupportFaq::active()
            ->select('category')
            ->distinct()
            ->get()
            ->map(function ($item) {
                return [
                    'key' => $item->category,
                    'name' => $this->categoryNames[$item->category] ?? ucfirst($item->category),
                ];
            });

        return response()->json($categories);
    }

    /**
     * Get questions for a specific category
     */
    public function getQuestions($category)
    {
        $questions = SupportFaq::active()
            ->byCategory($category)
            ->ordered()
            ->get(['question', 'answer'])
            ->mapWithKeys(function ($faq) {
                return [$faq->question => $faq->answer];
            });

        return response()->json($questions);
    }

    /**
     * Handle chat messages and return appropriate responses
     */
    public function chat(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000'
        ]);

        $rawMessage = (string) $request->message;
        $userMessage = mb_strtolower(trim($rawMessage), 'UTF-8');

        $cacheKey = 'chatbot:' . md5($this->normalizeForCache($userMessage));
        if (Cache::has($cacheKey)) {
            return response()->json(Cache::get($cacheKey));
        }

        // Get all active FAQs with keywords
        $faqs = SupportFaq::active()
            ->whereNotNull('keywords')
            ->get();

        $bestMatch = null;
        $bestScore = 0;

        // Check for keyword matches
        foreach ($faqs as $faq) {
            if (!$faq->keywords) continue;

            $score = $this->calculateScore($userMessage, $faq);

            if ($score > $bestScore) {
                $bestScore = $score;
                $bestMatch = $faq;
            }
        }

        // If we found a good match (score > 0), return the answer
        if ($bestMatch && $bestScore >= 1) {
            $payload = [
                'reply' => $bestMatch->answer,
                'source' => 'faq',
                'matched_question' => $bestMatch->question,
                'category' => $this->categoryNames[$bestMatch->category] ?? ucfirst($bestMatch->category)
            ];

            Cache::put($cacheKey, $payload, now()->addSeconds(60));
            return response()->json($payload);
        }

        // Default responses for common greetings
        if (preg_match('/^(hi|hello|chào|chao|alo|hey)/i', $userMessage)) {
            $payload = [
                'reply' => 'Xin chào! Tôi là trợ lý AI của BeePhone. Tôi có thể giúp bạn về thông tin sản phẩm, chính sách bảo hành, giao hàng, thanh toán và đổi trả. Bạn cần hỗ trợ gì ạ?',
                'source' => 'rule',
            ];
            Cache::put($cacheKey, $payload, now()->addSeconds(60));
            return response()->json($payload);
        }

        if (preg_match('/(cảm ơn|thank|thanks|cam on)/i', $userMessage)) {
            $payload = [
                'reply' => 'Không có gì! Rất vui được giúp đỡ bạn. Nếu có thêm câu hỏi nào khác, hãy hỏi tôi nhé!',
                'source' => 'rule',
            ];
            Cache::put($cacheKey, $payload, now()->addSeconds(60));
            return response()->json($payload);
        }

        if (preg_match('/(tạm biệt|bye|goodbye|tam biet)/i', $userMessage)) {
            $payload = [
                'reply' => 'Tạm biệt! Chúc bạn có một ngày tốt lành. Nếu cần hỗ trợ, hãy quay lại với tôi nhé!',
                'source' => 'rule',
            ];
            Cache::put($cacheKey, $payload, now()->addSeconds(60));
            return response()->json($payload);
        }

        // Strict mode: only answer from existing FAQ data
        $payload = [
            'reply' => 'Xin lỗi, tôi chưa có thông tin này trong hệ thống hỗ trợ hiện tại. Bạn vui lòng hỏi theo các chủ đề: giao hàng, bảo hành, thanh toán, đổi trả, hoặc liên hệ nhân viên để được hỗ trợ thêm.',
            'source' => 'strict_faq',
            'suggestions' => [
                'Bảo hành như thế nào?',
                'Phí giao hàng bao nhiêu?',
                'Có thể đổi trả không?',
                'Cách thanh toán online?'
            ]
        ];
        Cache::put($cacheKey, $payload, now()->addSeconds(30));
        return response()->json($payload);
    }

    private function normalizeForCache(string $text): string
    {
        $text = mb_strtolower(trim($text), 'UTF-8');
        $text = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $text);
        $text = preg_replace('/\s+/u', ' ', $text);
        return trim($text);
    }

    private function normalizeWords(string $text): array
    {
        $text = $this->normalizeForCache($text);
        if ($text === '') return [];
        return explode(' ', $text);
    }

    private function calculateScore(string $userMessage, SupportFaq $faq): float
    {
        $score = 0.0;

        $normalizedMessage = $this->normalizeForCache($userMessage);
        $inputWords = $this->normalizeWords($userMessage);

        $keywords = array_filter(array_map('trim', explode(',', (string) $faq->keywords)));
        foreach ($keywords as $keyword) {
            $kw = mb_strtolower($keyword, 'UTF-8');
            $kwNorm = $this->normalizeForCache($kw);
            if ($kwNorm === '') continue;

            if (str_contains($kwNorm, ' ')) {
                if ($kwNorm !== '' && str_contains($normalizedMessage, $kwNorm)) {
                    $score += 2.0;
                }
            } else {
                if (in_array($kwNorm, $inputWords, true)) {
                    $score += 1.0;
                } elseif ($kwNorm !== '' && str_contains($normalizedMessage, $kwNorm)) {
                    $score += 0.5;
                }
            }
        }

        $questionWords = $this->normalizeWords((string) $faq->question);
        foreach ($questionWords as $word) {
            if (mb_strlen($word, 'UTF-8') <= 2) continue;
            if (in_array($word, $inputWords, true)) {
                $score += 0.5;
            }
        }

        return $score;
    }
}
