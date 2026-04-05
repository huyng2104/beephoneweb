@extends('admin.layouts.app')

@section('title', 'Chi Tiết Yêu Cầu Hỗ Trợ')

@section('content')
<div class="p-8">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <a href="{{ route('admin.tickets.index') }}" class="text-slate-600 dark:text-slate-400 hover:text-primary transition-colors text-sm mb-2 inline-flex items-center gap-1">
                <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                <span>Quay lại danh sách</span>
            </a>
            <h1 class="text-3xl font-bold text-slate-900 dark:text-white mt-2">{{ $ticket->subject }}</h1>
            <p class="text-slate-600 dark:text-slate-400 text-sm">Mã: <strong>{{ $ticket->ticket_code }}</strong></p>
        </div>
        <div class="text-right">
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-2">Trạng thái</p>
            <form action="{{ route('admin.tickets.updateStatus', $ticket->id) }}" method="POST" class="inline-block">
                @csrf
                <select name="status" class="px-4 py-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-primary" onchange="this.form.submit()">
                    <option value="open" {{ $ticket->status === 'open' ? 'selected' : '' }}>🟠 Mới</option>
                    <option value="in_progress" {{ $ticket->status === 'in_progress' ? 'selected' : '' }}>🟡 Đang xử lý</option>
                    <option value="resolved" {{ $ticket->status === 'resolved' ? 'selected' : '' }}>✅ Đã xử lý</option>
                    <option value="closed" {{ $ticket->status === 'closed' ? 'selected' : '' }}>⚫ Đóng</option>
                </select>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg flex items-start gap-3">
            <span class="material-symbols-outlined text-green-600 dark:text-green-400 mt-0.5">check_circle</span>
            <p class="text-green-800 dark:text-green-200 text-sm">{{ session('success') }}</p>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main: Messages -->
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
                <div class="bg-slate-50 dark:bg-slate-700/50 border-b border-slate-200 dark:border-slate-700 p-4">
                    <h2 class="font-bold text-slate-900 dark:text-white">💬 Cuộc Trò Chuyện</h2>
                </div>

                <div class="h-96 overflow-y-auto p-4 space-y-4 bg-gray-50 dark:bg-slate-800/50">
                    @forelse($messages as $msg)
                        <div class="flex gap-3 {{ $msg->sender_type === 'admin' ? 'flex-row-reverse' : '' }}">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center shrink-0 {{ $msg->sender_type === 'admin' ? 'bg-blue-500 text-white' : 'bg-slate-300 dark:bg-slate-600 text-slate-800 dark:text-white' }}">
                                <span class="material-symbols-outlined text-[18px]">
                                    {{ $msg->sender_type === 'admin' ? 'admin_panel_settings' : 'person' }}
                                </span>
                            </div>
                            <div class="flex-1 {{ $msg->sender_type === 'admin' ? 'flex justify-end' : '' }}">
                                <div class="max-w-xs {{ $msg->sender_type === 'admin' ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-900 dark:text-blue-100' : 'bg-white dark:bg-slate-700 text-slate-900 dark:text-white' }} p-3 rounded-lg rounded-{{ $msg->sender_type === 'admin' ? 'tr' : 'tl' }}-none shadow-sm">
                                    <p class="text-xs font-semibold opacity-75 mb-1">{{ $msg->sender_name }} • {{ $msg->created_at->format('H:i d/m') }}</p>
                                    <p class="text-sm">{{ $msg->message }}</p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center y-12 flex flex-col items-center justify-center">
                            <span class="material-symbols-outlined text-4xl text-slate-300 dark:text-slate-600 mb-2">chat</span>
                            <p class="text-slate-500 dark:text-slate-400 text-sm">Chưa có tin nhắn nào</p>
                        </div>
                    @endforelse
                </div>

                <div class="border-t border-slate-200 dark:border-slate-700 p-4 bg-white dark:bg-slate-800">
                    <form action="{{ route('admin.tickets.addMessage', $ticket->id) }}" method="POST" class="flex gap-2">
                        @csrf
                        <textarea name="message" class="flex-1 px-4 py-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" rows="3" placeholder="Nhập phản hồi..." required></textarea>
                        <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-lg font-semibold hover:brightness-110 transition-all self-end">
                            <span class="material-symbols-outlined">send</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar: Ticket Info -->
        <div class="space-y-4">
            <!-- Thông tin khách -->
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm p-4">
                <h3 class="font-bold text-slate-900 dark:text-white mb-3 flex items-center gap-2">
                    <span class="material-symbols-outlined text-[20px]">person</span>
                    Thông Tin Khách Hàng
                </h3>
                <div class="space-y-2">
                    <div>
                        <p class="text-xs font-semibold text-slate-600 dark:text-slate-400">Tên</p>
                        <p class="text-sm font-medium text-slate-900 dark:text-white">{{ $ticket->customer_name }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-slate-600 dark:text-slate-400">Email</p>
                        <a href="mailto:{{ $ticket->customer_email }}" class="text-sm text-blue-500 hover:underline">{{ $ticket->customer_email }}</a>
                    </div>
                </div>
            </div>

            <!-- Độ ưu tiên -->
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm p-4">
                <h3 class="font-bold text-slate-900 dark:text-white mb-3 flex items-center gap-2">
                    <span class="material-symbols-outlined text-[20px]">priority_high</span>
                    Độ Ưu Tiên
                </h3>
                <div>
                    @if($ticket->priority === 'high')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300">🔴 Cao</span>
                    @elseif($ticket->priority === 'medium')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300">🟡 Trung bình</span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300">🟢 Thấp</span>
                    @endif
                </div>
            </div>

            <!-- Ngày tạo -->
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm p-4">
                <h3 class="font-bold text-slate-900 dark:text-white mb-3 flex items-center gap-2">
                    <span class="material-symbols-outlined text-[20px]">calendar_today</span>
                    Timeline
                </h3>
                <div class="space-y-2 text-sm">
                    <div>
                        <p class="text-xs text-slate-600 dark:text-slate-400">Tạo lúc</p>
                        <p class="font-medium text-slate-900 dark:text-white">{{ $ticket->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-600 dark:text-slate-400">Cập nhật lúc</p>
                        <p class="font-medium text-slate-900 dark:text-white">{{ $ticket->updated_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            </div>

            <!-- Số tin nhắn -->
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm p-4">
                <h3 class="font-bold text-slate-900 dark:text-white mb-3 flex items-center gap-2">
                    <span class="material-symbols-outlined text-[20px]">chat</span>
                    Thống Kê
                </h3>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-sm text-slate-600 dark:text-slate-400">Tổng tin nhắn</span>
                        <span class="font-bold text-slate-900 dark:text-white">{{ $messages->count() }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-slate-600 dark:text-slate-400">Tin từ khách</span>
                        <span class="font-bold text-slate-900 dark:text-white">{{ $messages->where('sender_type', 'customer')->count() }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-slate-600 dark:text-slate-400">Phản hồi admin</span>
                        <span class="font-bold text-slate-900 dark:text-white">{{ $messages->where('sender_type', 'admin')->count() }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Auto scroll to bottom on page load
    document.addEventListener('DOMContentLoaded', function() {
        const chatBox = document.querySelector('[class*="h-96"]');
        if (chatBox) {
            chatBox.scrollTop = chatBox.scrollHeight;
        }
    });
</script>
@endsection
