@extends('admin.layouts.app')

@section('title', 'Sửa Câu Hỏi Chatbot')

@section('content')
<div class="p-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-slate-900 dark:text-white">Sửa Câu Hỏi Chatbot</h1>
        <p class="text-slate-600 dark:text-slate-400 text-sm mt-1">Cập nhật câu hỏi và câu trả lời cho chatbot</p>
    </div>

    @if($errors->any())
        <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
            <p class="text-red-800 dark:text-red-200 font-semibold text-sm mb-2">Vui lòng sửa các lỗi sau:</p>
            <ul class="space-y-1">
                @foreach ($errors->all() as $error)
                    <li class="text-red-700 dark:text-red-300 text-sm">• {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm p-6">
        <form action="{{ route('admin.chatbot-faqs.update', $faq->id) }}" method="POST" class="space-y-6">
            @csrf @method('PUT')

            <div>
                <label class="block text-sm font-semibold text-slate-900 dark:text-white mb-2">
                    Câu Hỏi <span class="text-red-500">*</span>
                </label>
                <input type="text" name="question" class="w-full px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent @error('question') ring-2 ring-red-500 @enderror" placeholder="Nhập câu hỏi FAQ" value="{{ old('question', $faq->question) }}" required>
                @error('question')<span class="text-red-600 dark:text-red-400 text-sm mt-1 block">{{ $message }}</span>@enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-900 dark:text-white mb-2">
                    Loại Chuyên Mục <span class="text-red-500">*</span>
                </label>
                <select name="category" class="w-full px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent @error('category') ring-2 ring-red-500 @enderror" required>
                    <option value="">-- Chọn Chuyên Mục --</option>
                    @foreach($categories as $key => $label)
                        <option value="{{ $key }}" {{ old('category', $faq->category) == $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @error('category')<span class="text-red-600 dark:text-red-400 text-sm mt-1 block">{{ $message }}</span>@enderror
                <small class="text-slate-500 dark:text-slate-400 text-xs mt-1.5 block">Chọn nhóm câu hỏi, ví dụ: Giao hàng, Bảo hành, Thanh toán, Đổi trả.</small>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-900 dark:text-white mb-2">
                    Câu Trả Lời <span class="text-red-500">*</span>
                </label>
                <textarea name="answer" class="w-full px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent @error('answer') ring-2 ring-red-500 @enderror" rows="8" placeholder="Nhập câu trả lời chi tiết cho câu hỏi" required>{{ old('answer', $faq->answer) }}</textarea>
                @error('answer')<span class="text-red-600 dark:text-red-400 text-sm mt-1 block">{{ $message }}</span>@enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-900 dark:text-white mb-2">
                    Từ Khóa (không bắt buộc)
                </label>
                <input type="text" name="keywords" class="w-full px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="Ví dụ: bảo hành, lỗi, kỹ thuật, hỗ trợ" value="{{ old('keywords', $faq->keywords) }}">
                <small class="text-slate-500 dark:text-slate-400 text-xs mt-1.5 block">Nhập các từ khóa ngăn cách bằng dấu phẩy. Chatbot sẽ tự động nhận dạng câu hỏi dựa trên những từ khóa này</small>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-slate-900 dark:text-white mb-2">
                        Độ Ưu Tiên
                    </label>
                <input type="number" name="sort_order" class="w-full px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="0" value="{{ old('sort_order', $faq->sort_order) }}">
                <div class="flex items-end">
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_active" class="w-5 h-5 rounded border-slate-300 text-primary focus:ring-primary dark:border-slate-600 dark:bg-slate-700" value="1" {{ $faq->is_active ? 'checked' : '' }}>
                        <span class="ml-3 text-sm font-medium text-slate-900 dark:text-white">Kích hoạt</span>
                    </label>
                </div>
            </div>

            <div class="flex items-center gap-3 pt-4 border-t border-slate-200 dark:border-slate-700">
                <button type="submit" class="flex items-center gap-2 px-6 py-2.5 bg-primary text-black text-sm font-bold hover:brightness-105 transition-all rounded-lg shadow-sm">
                    <span class="material-symbols-outlined text-[18px]">check</span>
                    <span>Cập Nhật Câu Hỏi</span>
                </button>
                <a href="{{ route('admin.chatbot-faqs.index') }}" class="flex items-center gap-2 px-6 py-2.5 border border-slate-200 dark:border-slate-600 text-slate-700 dark:text-slate-300 text-sm font-medium hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors rounded-lg">
                    <span class="material-symbols-outlined text-[18px]">cancel</span>
                    <span>Hủy</span>
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
