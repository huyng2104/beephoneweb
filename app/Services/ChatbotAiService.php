<?php

namespace App\Services;

use App\Models\SupportFaq;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatbotAiService
{
    public function ask(string $message): ?string
    {
        $keys = array_values(array_filter([
            config('services.gemini.key_1'),
            config('services.gemini.key_2'),
            config('services.gemini.key_3'),
        ]));

        if (empty($keys)) {
            return null;
        }

        $apiKey = $keys[array_rand($keys)];

        $prompt = $this->buildPrompt($message);

        try {
            $response = Http::timeout(15)
                ->acceptJson()
                ->post(
                    "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}",
                    [
                        'contents' => [
                            ['parts' => [['text' => $prompt]]],
                        ],
                    ]
                );

            if ($response->failed()) {
                Log::warning('Gemini request failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return null;
            }

            $reply = data_get($response->json(), 'candidates.0.content.parts.0.text');
            if (!is_string($reply) || trim($reply) === '') {
                return null;
            }

            return trim(str_replace(['**', '*'], '', $reply));
        } catch (\Throwable $e) {
            Log::error('Gemini request exception', [
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    private function buildPrompt(string $message): string
    {
        $faqContext = $this->buildFaqContext($message);

        return "Bạn là trợ lý CSKH của BeePhone.\n"
            . "YÊU CẦU:\n"
            . "- Trả lời ngắn gọn, lịch sự, dễ hiểu.\n"
            . "- Trả lời bằng tiếng Việt.\n"
            . "- CHỈ được trả lời dựa trên dữ liệu FAQ cung cấp bên dưới.\n"
            . "- Nếu dữ liệu FAQ không đủ để trả lời, hãy nói rõ chưa có thông tin và mời khách liên hệ nhân viên.\n"
            . "- Không được tự bịa nội dung ngoài FAQ.\n\n"
            . "DỮ LIỆU FAQ NỘI BỘ:\n"
            . $faqContext . "\n\n"
            . "Câu hỏi khách: {$message}";
    }

    private function buildFaqContext(string $message): string
    {
        $faqs = SupportFaq::active()
            ->ordered()
            ->get(['category', 'question', 'answer', 'keywords']);

        if ($faqs->isEmpty()) {
            return "- Không có dữ liệu FAQ trong hệ thống.";
        }

        $tokens = $this->tokenize($message);

        $ranked = $faqs->map(function (SupportFaq $faq) use ($tokens) {
            $questionTokens = $this->tokenize($faq->question);
            $keywordTokens = $this->tokenize((string) $faq->keywords);
            $haystack = array_unique(array_merge($questionTokens, $keywordTokens));

            $score = 0;
            foreach ($tokens as $token) {
                if ($token !== '' && in_array($token, $haystack, true)) {
                    $score++;
                }
            }

            return [
                'faq' => $faq,
                'score' => $score,
            ];
        })->sortByDesc('score')->values();

        $topFaqs = $ranked->take(6)->pluck('faq');

        $lines = [];
        foreach ($topFaqs as $faq) {
            $lines[] = "- [{$faq->category}] Q: {$faq->question}";
            $lines[] = "  A: {$faq->answer}";
        }

        return implode("\n", $lines);
    }

    private function tokenize(string $text): array
    {
        $normalized = mb_strtolower(trim($text), 'UTF-8');
        $normalized = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $normalized);
        $normalized = preg_replace('/\s+/u', ' ', $normalized);
        $parts = explode(' ', trim($normalized));

        return array_values(array_filter($parts));
    }
}

