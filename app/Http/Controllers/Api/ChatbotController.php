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
}
