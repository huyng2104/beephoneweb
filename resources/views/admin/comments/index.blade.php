@extends('admin.layouts.app')
@section('content')

@include('popup_notify.index')

{{-- ===== HEADER ===== --}}
<header class="dark:bg-gray-900 px-8 mt-5">
    <div class="flex flex-wrap justify-between items-end gap-4">
        <div class="flex flex-col gap-1">
            <h2 class="text-[#181611] dark:text-white text-3xl font-bold tracking-tight">Hỏi đáp sản phẩm</h2>
            <p class="text-[#8a8060] text-sm">Quản lý, ẩn hiện và phản hồi các bình luận hỏi đáp từ khách hàng.</p>
        </div>
    </div>
</header>

<div class="p-8 flex flex-col gap-8">

    {{-- ===== STATS ===== --}}
    <div class="grid grid-cols-2 lg:grid-cols-3 gap-4">
        <a href="{{ route('admin.comments.index') }}" class="flex flex-col gap-1 rounded-xl p-5 bg-white dark:bg-gray-900 border {{ request()->missing('status') ? 'border-primary ring-1 ring-primary' : 'border-[#e6e3db] hover:border-gray-400' }} transition-all shadow-sm hover:shadow-md cursor-pointer">
            <p class="text-[#8a8060] text-xs font-medium uppercase tracking-wide">Tổng bình luận</p>
            <p class="text-[#181611] dark:text-white text-3xl font-bold">{{ number_format($stats['total'] ?? 0) }}</p>
        </a>

        <a href="{{ route('admin.comments.index', ['status' => 1]) }}" class="flex flex-col gap-1 rounded-xl p-5 bg-green-50 border {{ request('status') === '1' ? 'border-green-500 ring-1 ring-green-500' : 'border-green-200 hover:border-green-400' }} transition-all shadow-sm hover:shadow-md cursor-pointer">
            <p class="text-green-600 text-xs font-medium uppercase tracking-wide">Đang hiển thị</p>
            <p class="text-green-700 text-3xl font-bold">{{ number_format($stats['approved'] ?? 0) }}</p>
        </a>
        <a href="{{ route('admin.comments.index', ['status' => 2]) }}" class="flex flex-col gap-1 rounded-xl p-5 bg-gray-50 border {{ request('status') === '2' ? 'border-gray-500 ring-1 ring-gray-500' : 'border-gray-200 hover:border-gray-400' }} transition-all shadow-sm hover:shadow-md cursor-pointer">
            <p class="text-gray-500 text-xs font-medium uppercase tracking-wide">Đã ẩn</p>
            <p class="text-gray-700 text-3xl font-bold">{{ number_format($stats['hidden'] ?? 0) }}</p>
        </a>
    </div>
    {{-- ===== TABLE ===== --}}
    <div class="bg-white dark:bg-gray-900 border border-[#e6e3db] rounded-xl overflow-hidden flex flex-col">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-[#f9f8f5] dark:bg-gray-800 text-[#8a8060] text-xs uppercase tracking-wider font-bold border-b border-[#e6e3db]">
                        <th class="px-6 py-4 w-[25%]">Sản phẩm</th>
                        <th class="px-6 py-4 w-[25%]">Thông tin Khách hàng</th>
                        <th class="px-6 py-4 w-[35%]">Nội dung</th>
                        <th class="px-6 py-4 text-center">Trạng thái</th>
                        <th class="px-6 py-4 text-right">Hành động</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#e6e3db]">
                    @forelse($comments as $comment)
                    <tr class="hover:bg-[#f9f8f5] dark:hover:bg-gray-800/50 transition-colors group">
                        
                        {{-- Sản phẩm --}}
                        <td class="px-6 py-4 align-top">
                            <div class="flex items-start gap-3">
                                @if($comment->product?->thumbnail)
                                <img src="{{ asset('storage/' . $comment->product->thumbnail) }}"
                                     alt="{{ $comment->product->name }}"
                                     class="w-12 h-12 rounded-lg object-cover border border-[#e6e3db] flex-shrink-0">
                                @endif
                                <div class="min-w-0">
                                    <p class="font-bold text-[#181611] dark:text-white text-sm truncate max-w-[150px]" title="{{ $comment->product?->name }}">
                                        {{ $comment->product?->name ?? '(Đã xóa)' }}
                                    </p>
                                    <p class="text-[10px] text-gray-400 mt-1">{{ $comment->created_at->format('d/m/Y H:i') }}</p>
                                </div>
                            </div>
                        </td>

                        {{-- Khách hàng --}}
                        <td class="px-6 py-4 align-top">
                            @if($comment->user)
                                <p class="font-bold text-[#181611] dark:text-white text-sm">{{ $comment->user->name }}</p>
                                <p class="text-xs text-gray-500">{{ $comment->user->email }}</p>
                                <span class="px-2 py-0.5 mt-1 bg-green-100 text-green-700 text-[10px] rounded inline-block">Thành viên</span>
                            @else
                                <p class="font-bold text-[#181611] dark:text-white text-sm">{{ $comment->guest_name }} <span class="text-xs font-normal text-gray-400">(Khách)</span></p>
                                <p class="text-xs text-gray-500">{{ $comment->guest_email }}</p>
                            @endif
                        </td>

                        {{-- Nội dung --}}
                        <td class="px-6 py-4 align-top">
                            <p class="text-sm text-[#181611] dark:text-gray-200 mb-2 leading-relaxed font-medium">
                                <span class="text-blue-600 font-bold">Hỏi:</span> {{ $comment->content }}
                            </p>

                            {{-- Phản hồi --}}
                            @if($comment->replies->count() > 0)
                                <div class="mt-3 pl-3 border-l-2 border-[#f4c025]">
                                    @foreach($comment->replies as $reply)
                                        <div class="mb-2 last:mb-0">
                                            <p class="text-xs font-bold {{ $reply->user_id ? 'text-[#8a8060]' : 'text-gray-700' }} mb-0.5">
                                                {{ $reply->user?->name ?? $reply->guest_name }}
                                                @if($reply->user && $reply->user->role && $reply->user->role->name === 'admin') <span class="bg-[#f4c025] text-black text-[10px] px-1 py-0.5 rounded ml-1">QTV</span> @endif
                                                <span class="text-gray-400 font-normal ml-1">{{ $reply->created_at->format('d/m H:i') }}</span>
                                            </p>
                                            <p class="text-sm text-gray-600 dark:text-gray-300">{{ $reply->content }}</p>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </td>

                        {{-- Trạng thái --}}
                        <td class="text-center align-top px-6 py-4">
                            @if($comment->status == 1)
                                <span class="px-3 py-1 rounded-full text-xs font-bold border bg-green-100 text-green-700 border-green-200 whitespace-nowrap">Hiển thị</span>
                            @else
                                <span class="px-3 py-1 rounded-full text-xs font-bold border bg-gray-100 text-gray-600 border-gray-200 whitespace-nowrap">Đã ẩn</span>
                            @endif
                        </td>

                        {{-- Hành động --}}
                        <td class="px-6 py-4 align-top">
                            <div class="flex justify-end items-center gap-1">
                                {{-- Ẩn/Hiện --}}
                                <form action="{{ route('admin.comments.toggle', $comment->id) }}" method="POST" class="inline">
                                    @csrf @method('PATCH')
                                    @if($comment->status == 1)
                                    <button type="submit" title="Ẩn bình luận" class="p-2 hover:bg-orange-50 rounded-lg text-[#8a8060] hover:text-orange-500 transition-colors">
                                        <span class="material-symbols-outlined text-lg">visibility_off</span>
                                    </button>
                                    @else
                                    <button type="submit" title="Hiển thị bình luận" class="p-2 hover:bg-green-50 rounded-lg text-[#8a8060] hover:text-green-600 transition-colors">
                                        <span class="material-symbols-outlined text-lg">visibility</span>
                                    </button>
                                    @endif
                                </form>

                                {{-- Phản hồi --}}
                                <button type="button" title="Trả lời"
                                        onclick="openReplyModal({{ $comment->id }})"
                                        class="p-2 hover:bg-blue-50 rounded-lg text-[#8a8060] hover:text-blue-500 transition-colors">
                                    <span class="material-symbols-outlined text-lg">reply</span>
                                </button>

                                {{-- Xóa --}}
                                <form action="{{ route('admin.comments.destroy', $comment->id) }}" method="POST" class="inline"
                                      onsubmit="return confirm('Xóa bình luận này sẽ xóa cả các câu trả lời bên trong. Bạn chắc chưa?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" title="Xóa"
                                            class="p-2 hover:bg-red-50 rounded-lg text-[#8a8060] hover:text-red-500 transition-colors">
                                        <span class="material-symbols-outlined text-lg">delete</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center gap-3 text-[#8a8060]">
                                <span class="material-symbols-outlined text-5xl text-gray-300">forum</span>
                                <p class="font-medium">Chưa có bình luận hỏi đáp nào.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($comments->hasPages())
        <div class="p-4 border-t border-[#e6e3db] flex justify-between items-center bg-gray-50/50 flex-wrap gap-3">
             {{ $comments->links() }}
        </div>
        @endif
    </div>
</div>

{{-- ===== REPLY MODAL ===== --}}
<div id="reply-modal"
     class="fixed inset-0 z-[9999] flex items-center justify-center px-4 hidden"
     aria-hidden="true">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeReplyModal()"></div>

    <div class="relative w-full max-w-lg bg-white dark:bg-gray-900 rounded-2xl shadow-2xl border border-[#e6e3db] overflow-hidden">
        {{-- Header --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-[#e6e3db]">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-[#f4c025]">reply</span>
                <h3 class="text-base font-bold text-[#181611] dark:text-white">Trả lời khách hàng</h3>
            </div>
            <button onclick="closeReplyModal()" class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                <span class="material-symbols-outlined text-[#8a8060]">close</span>
            </button>
        </div>

        {{-- Body --}}
        <form id="reply-form" method="POST" action="">
            @csrf
            <div class="px-6 py-5 flex flex-col gap-4">
                <label for="reply_content" class="block text-sm font-bold text-[#181611] dark:text-white">
                    Nội dung trả lời
                </label>
                <textarea id="reply_content" name="content" rows="5" required
                          class="w-full border border-[#e6e3db] rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-[#f4c025] outline-none transition-shadow resize-none dark:bg-gray-800 dark:text-white"
                          placeholder="BeePhone xin chào bạn..."></textarea>
            </div>
            <div class="px-6 py-4 border-t border-[#e6e3db] flex justify-end gap-3 bg-gray-50/50">
                <button type="button" onclick="closeReplyModal()"
                        class="px-5 py-2.5 border border-[#e6e3db] rounded-xl text-sm font-bold text-[#8a8060] hover:bg-gray-100 transition-colors">
                    Hủy
                </button>
                <button type="submit"
                        class="px-5 py-2.5 bg-[#f4c025] text-[#181611] rounded-xl text-sm font-bold hover:brightness-105 transition-all shadow-sm">
                    Gửi trả lời
                </button>
            </div>
        </form>
    </div>
</div>

@push('js')
<script>
    function openReplyModal(commentId) {
        const form = document.getElementById('reply-form');
        form.action = `/admin/comments/${commentId}/reply`;

        document.getElementById('reply_content').value = ''; // clear

        const modal = document.getElementById('reply-modal');
        modal.classList.remove('hidden');
        modal.removeAttribute('aria-hidden');
        document.getElementById('reply_content').focus();
    }

    function closeReplyModal() {
        const modal = document.getElementById('reply-modal');
        modal.classList.add('hidden');
        modal.setAttribute('aria-hidden', 'true');
    }

    // Đóng bằng phím ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeReplyModal();
    });
</script>
@endpush

@endsection
