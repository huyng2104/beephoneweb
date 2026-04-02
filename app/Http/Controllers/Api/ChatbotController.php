<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SupportFaq;
use Illuminate\Http\Request;

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

        $userMessage = strtolower(trim($request->message));

        // Get all active FAQs with keywords
        $faqs = SupportFaq::active()
            ->whereNotNull('keywords')
            ->get();

        $bestMatch = null;
        $bestScore = 0;

        // Check for keyword matches
        foreach ($faqs as $faq) {
            if (!$faq->keywords) continue;

            $keywords = explode(',', $faq->keywords);
            $keywords = array_map('trim', $keywords);
            $keywords = array_map('strtolower', $keywords);

            $score = 0;
            foreach ($keywords as $keyword) {
                if (strpos($userMessage, $keyword) !== false) {
                    $score += 1;
                }
            }

            // Also check if the question itself matches
            $questionWords = explode(' ', strtolower($faq->question));
            foreach ($questionWords as $word) {
                if (strlen($word) > 2 && strpos($userMessage, $word) !== false) {
                    $score += 0.5;
                }
            }

            if ($score > $bestScore) {
                $bestScore = $score;
                $bestMatch = $faq;
            }
        }

        // If we found a good match (score > 0), return the answer
        if ($bestMatch && $bestScore > 0) {
            return response()->json([
                'reply' => $bestMatch->answer,
                'matched_question' => $bestMatch->question,
                'category' => $this->categoryNames[$bestMatch->category] ?? ucfirst($bestMatch->category)
            ]);
        }

        // Default responses for common greetings
        if (preg_match('/^(hi|hello|chào|chao|alo|hey)/i', $userMessage)) {
            return response()->json([
                'reply' => 'Xin chào! Tôi là trợ lý AI của BeePhone. Tôi có thể giúp bạn về thông tin sản phẩm, chính sách bảo hành, giao hàng, thanh toán và đổi trả. Bạn cần hỗ trợ gì ạ?'
            ]);
        }

        if (preg_match('/(cảm ơn|thank|thanks|cam on)/i', $userMessage)) {
            return response()->json([
                'reply' => 'Không có gì! Rất vui được giúp đỡ bạn. Nếu có thêm câu hỏi nào khác, hãy hỏi tôi nhé!'
            ]);
        }

        if (preg_match('/(tạm biệt|bye|goodbye|tam biet)/i', $userMessage)) {
            return response()->json([
                'reply' => 'Tạm biệt! Chúc bạn có một ngày tốt lành. Nếu cần hỗ trợ, hãy quay lại với tôi nhé!'
            ]);
        }

        // Default response when no match found
        return response()->json([
            'reply' => 'Xin lỗi, tôi chưa hiểu rõ câu hỏi của bạn. Bạn có thể hỏi về các chủ đề sau: giao hàng, bảo hành, thanh toán, đổi trả, hoặc sản phẩm. Hoặc bạn có thể liên hệ trực tiếp với nhân viên hỗ trợ của chúng tôi.',
            'suggestions' => [
                'Bảo hành như thế nào?',
                'Phí giao hàng bao nhiêu?',
                'Có thể đổi trả không?',
                'Cách thanh toán online?'
            ]
        ]);
    }
}
