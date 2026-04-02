<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use App\Models\SupportFaq;
use Illuminate\Http\Request;

class ChatbotFaqController extends Controller
{
    private $categories = [
        'shipping' => 'Giao hàng',
        'warranty' => 'Bảo hành',
        'payment' => 'Thanh toán',
        'return' => 'Đổi trả',
    ];

    /**
     * Display a listing of FAQs
     */
    public function index()
    {
        $faqs = SupportFaq::orderBy('category')->orderBy('sort_order')->paginate(15);
        $categories = $this->categories;

        return view('admin.chatbot-faqs.index', compact('faqs', 'categories'));
    }

    /**
     * Show the form for creating a new FAQ
     */
    public function create()
    {
        $categories = $this->categories;

        return view('admin.chatbot-faqs.create', compact('categories'));
    }

    /**
     * Store a newly created FAQ
     */
    public function store(Request $request)
    {
        $request->validate([
            'category' => 'required|in:' . implode(',', array_keys($this->categories)),
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        SupportFaq::create([
            'category' => $request->category,
            'question' => $request->question,
            'answer' => $request->answer,
            'keywords' => $request->keywords,
            'sort_order' => $request->sort_order ?? 0,
            'is_active' => $request->has('is_active') ? true : false,
        ]);

        return redirect()
            ->route('admin.chatbot-faqs.index')
            ->with('success', 'Thêm FAQ thành công!');
    }

    /**
     * Show the form for editing the specified FAQ
     */
    public function edit($id)
    {
        $faq = SupportFaq::findOrFail($id);
        $categories = $this->categories;

        return view('admin.chatbot-faqs.edit', compact('faq', 'categories'));
    }

    /**
     * Update the specified FAQ
     */
    public function update(Request $request, $id)
    {
        $faq = SupportFaq::findOrFail($id);

        $request->validate([
            'category' => 'required|in:' . implode(',', array_keys($this->categories)),
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $faq->update([
            'category' => $request->category,
            'question' => $request->question,
            'answer' => $request->answer,
            'keywords' => $request->keywords,
            'sort_order' => $request->sort_order ?? 0,
            'is_active' => $request->has('is_active') ? true : false,
        ]);

        return redirect()
            ->route('admin.chatbot-faqs.index')
            ->with('success', 'Cập nhật FAQ thành công!');
    }

    /**
     * Delete the specified FAQ
     */
    public function destroy($id)
    {
        $faq = SupportFaq::findOrFail($id);
        $faq->delete();

        return redirect()
            ->route('admin.chatbot-faqs.index')
            ->with('success', 'Xóa FAQ thành công!');
    }
}
