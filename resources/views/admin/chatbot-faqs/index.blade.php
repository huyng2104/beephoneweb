@extends('admin.layouts.app')

@section('title', 'Quản Lý Chatbot FAQ')

@section('content')
<div class="p-8">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-slate-900 dark:text-white">Quản Lý Câu Hỏi Chatbot</h1>
            <p class="text-slate-600 dark:text-slate-400 text-sm mt-1">Tạo và cấu hình câu hỏi thường gặp cho chatbot</p>
        </div>
        <a href="{{ route('admin.chatbot-faqs.create') }}" class="flex items-center gap-2 px-4 py-2.5 bg-primary text-black text-sm font-bold hover:brightness-105 transition-all rounded-lg shadow-sm">
            <span class="material-symbols-outlined text-[20px]">add</span>
            <span>Thêm Câu Hỏi</span>
        </a>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg flex items-start gap-3">
            <span class="material-symbols-outlined text-green-600 dark:text-green-400 mt-0.5">check_circle</span>
            <p class="text-green-800 dark:text-green-200 text-sm">{{ session('success') }}</p>
        </div>
    @endif

    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 dark:bg-slate-700/50 border-b border-slate-200 dark:border-slate-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider">Câu Hỏi</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider">Từ Khóa</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider">Độ Ưu Tiên</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider">Trạng Thái</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider">Hành Động</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                    @forelse($faqs as $faq)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                            <td class="px-6 py-4 text-sm text-slate-900 dark:text-white font-medium">{{ Str::limit($faq->question, 60) }}</td>
                            <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400">{{ Str::limit($faq->keywords ?? '—', 40) }}</td>
                            <td class="px-6 py-4 text-sm">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300">
                                    {{ $faq->priority }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                @if($faq->is_active)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300">
                                        ✓ Kích Hoạt
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300">
                                        ✕ Tắt
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.chatbot-faqs.edit', $faq->id) }}" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg border border-slate-200 dark:border-slate-600 text-slate-700 dark:text-slate-300 text-sm font-medium hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                                        <span class="material-symbols-outlined text-[16px]">edit</span>
                                        <span>Sửa</span>
                                    </a>
                                    <form action="{{ route('admin.chatbot-faqs.destroy', $faq->id) }}" method="POST" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg border border-red-200 dark:border-red-900 text-red-700 dark:text-red-400 text-sm font-medium hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors" onclick="return confirm('Bạn chắc chắn muốn xóa câu hỏi này?')">
                                            <span class="material-symbols-outlined text-[16px]">delete</span>
                                            <span>Xóa</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <span class="material-symbols-outlined text-5xl text-slate-300 dark:text-slate-600 mb-2">help</span>
                                    <p class="text-slate-600 dark:text-slate-400 font-medium">Chưa có câu hỏi FAQ nào</p>
                                    <p class="text-slate-500 dark:text-slate-500 text-sm">Nhấn nút "Thêm Câu Hỏi" ở trên để tạo câu hỏi đầu tiên</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($faqs->hasPages())
        <div class="mt-6">
            {{ $faqs->links() }}
        </div>
    @endif
</div>
@endsection
