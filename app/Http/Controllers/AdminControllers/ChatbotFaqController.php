<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use App\Models\ChatbotFaq;
use Illuminate\Http\Request;

class ChatbotFaqController extends Controller
{
    public function index()
    {
        $faqs = ChatbotFaq::orderBy('priority', 'desc')->paginate(20);
        return view('admin.chatbot-faqs.index', compact('faqs'));
    }

    public function create()
    {
        return view('admin.chatbot-faqs.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'question' => 'required|string|unique:chatbot_faqs,question',
            'answer' => 'required|string',
            'keywords' => 'nullable|string',
            'priority' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $data['is_active'] = $request->has('is_active') ? true : false;
        ChatbotFaq::create($data);

        return redirect()->route('admin.chatbot-faqs.index')->with('success', 'Thêm FAQ thành công!');
    }

    public function edit(ChatbotFaq $faq)
    {
        return view('admin.chatbot-faqs.edit', compact('faq'));
    }

    public function update(Request $request, ChatbotFaq $faq)
    {
        $data = $request->validate([
            'question' => 'required|string|unique:chatbot_faqs,question,' . $faq->id,
            'answer' => 'required|string',
            'keywords' => 'nullable|string',
            'priority' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $data['is_active'] = $request->has('is_active') ? true : false;
        $faq->update($data);

        return redirect()->route('admin.chatbot-faqs.index')->with('success', 'Cập nhật FAQ thành công!');
    }

    public function destroy(ChatbotFaq $faq)
    {
        $faq->delete();
        return redirect()->route('admin.chatbot-faqs.index')->with('success', 'Xóa FAQ thành công!');
    }
}
