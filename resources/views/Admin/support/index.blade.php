@extends('admin.layouts.app')

@section('title', 'Quản lý yêu cầu hỗ trợ')

@section('content')
<main class="flex-1 flex flex-col overflow-hidden">
    <div id="toast-container" class="fixed top-5 right-5 z-[9999] flex flex-col gap-3">

        @if (session('success') || session('error') || $errors->any())

            <div id="custom-sweet-alert"
                class="fixed inset-0 z-[9999] flex items-center justify-center bg-slate-900/60 backdrop-blur-sm transition-opacity duration-300 opacity-0">

                <div id="alert-box"
                    class="bg-white dark:bg-slate-800 rounded-3xl p-8 max-w-sm w-full mx-4 shadow-2xl transform transition-all duration-300 scale-75 opacity-0 flex flex-col items-center text-center">

                    @if (session('success'))
                        <div
                            class="w-20 h-20 bg-green-100 dark:bg-green-500/20 text-green-500 rounded-full flex items-center justify-center mb-5 animate-[bounce_1s_ease-in-out]">
                            <span class="material-symbols-outlined text-5xl">check_circle</span>
                        </div>
                        <h3 class="text-2xl font-black text-slate-800 dark:text-white mb-2">Thành công!</h3>
                        <p class="text-slate-600 dark:text-slate-400 mb-6 font-medium">{{ session('success') }}</p>

                        <button onclick="closeAlert()"
                            class="w-full bg-green-500 hover:bg-green-600 text-white font-bold py-3.5 rounded-xl transition-all active:scale-95 shadow-lg shadow-green-500/30">
                            Tuyệt vời
                        </button>
                    @endif

                    @if (session('error') || $errors->any())
                        <div
                            class="w-20 h-20 bg-red-100 dark:bg-red-500/20 text-red-500 rounded-full flex items-center justify-center mb-5 animate-[pulse_1s_ease-in-out]">
                            <span class="material-symbols-outlined text-5xl">warning</span>
                        </div>
                        <h3 class="text-2xl font-black text-slate-800 dark:text-white mb-2">Ôi hỏng!</h3>
                        <p class="text-slate-600 dark:text-slate-400 mb-6 font-medium">
                            @if (session('error'))
                                {{ session('error') }}
                            @else
                                Thông tin nhập vào chưa chính xác. Vui lòng kiểm tra lại!
                            @endif
                        </p>

                        <button onclick="closeAlert()"
                            class="w-full bg-red-500 hover:bg-red-600 text-white font-bold py-3.5 rounded-xl transition-all active:scale-95 shadow-lg shadow-red-500/30">
                            Đóng lại
                        </button>
                    @endif

                </div>
            </div>

            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    const overlay = document.getElementById('custom-sweet-alert');
                    const alertBox = document.getElementById('alert-box');

                    if (overlay && alertBox) {
                        requestAnimationFrame(() => {
                            overlay.classList.remove('opacity-0');
                            overlay.classList.add('opacity-100');

                            alertBox.classList.remove('scale-75', 'opacity-0');
                            alertBox.classList.add('scale-100', 'opacity-100');
                        });

                        setTimeout(() => {
                            closeAlert();
                        }, 5000);
                    }
                });

                function closeAlert() {
                    const overlay = document.getElementById('custom-sweet-alert');
                    const alertBox = document.getElementById('alert-box');

                    if (overlay && alertBox) {
                        overlay.classList.remove('opacity-100');
                        overlay.classList.add('opacity-0');

                        alertBox.classList.remove('scale-100', 'opacity-100');
                        alertBox.classList.add('scale-75', 'opacity-0');

                        setTimeout(() => {
                            overlay.remove();
                        }, 300);
                    }
                }
            </script>
        @endif

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toasts = document.querySelectorAll('.toast-message');

            toasts.forEach(function(toast) {
                setTimeout(() => {
                    toast.classList.add('translate-x-full', 'opacity-0');

                    setTimeout(() => {
                        toast.remove();
                    }, 300);
                }, 3000);
            });
        });
    </script>

    <!-- Body Content -->
    <div class="flex-1 overflow-y-auto p-8 space-y-6">
        <!-- Breadcrumbs & Actions -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-black text-slate-900 dark:text-slate-100 tracking-tight">Quản lý yêu cầu hỗ trợ</h2>
                <p class="text-slate-500 text-sm mt-1">Theo dõi, phân loại và phản hồi các yêu cầu từ khách hàng Bee Phone</p>
            </div>
            <a href="{{ route('admin.support.create') }}">
                <button
                    class="bg-primary hover:bg-primary/90 text-slate-900 font-bold px-5 py-2.5 rounded-xl shadow-sm shadow-primary/20 flex items-center gap-2 transition-all">
                    <span class="material-symbols-outlined">add</span>
                    Tạo vé mới
                </button>
            </a>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white dark:bg-slate-800 p-4 rounded-xl border border-slate-200 dark:border-slate-700">
                <p class="text-slate-500 text-xs font-bold uppercase tracking-wider">Tổng vé hỗ trợ</p>
                <p class="text-2xl font-black mt-1">{{ $stats['total'] }}</p>
            </div>
            <div class="bg-white dark:bg-slate-800 p-4 rounded-xl border border-slate-200 dark:border-slate-700">
                <p class="text-slate-500 text-xs font-bold uppercase tracking-wider">Vé mới</p>
                <p class="text-2xl font-black mt-1 text-blue-500">{{ $stats['open'] }}</p>
            </div>
            <div class="bg-white dark:bg-slate-800 p-4 rounded-xl border border-slate-200 dark:border-slate-700">
                <p class="text-slate-500 text-xs font-bold uppercase tracking-wider">Đang xử lý</p>
                <p class="text-2xl font-black mt-1 text-yellow-500">{{ $stats['in_progress'] }}</p>
            </div>
            <div class="bg-white dark:bg-slate-800 p-4 rounded-xl border border-slate-200 dark:border-slate-700">
                <p class="text-slate-500 text-xs font-bold uppercase tracking-wider">Hài lòng (CSAT)</p>
                <p class="text-2xl font-black mt-1 text-green-500">{{ number_format($stats['avg_satisfaction'], 1) }}/5</p>
            </div>
        </div>

        <!-- Filters & Table -->
        <div
            class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
            <!-- Filter Row -->
            <div class="p-4 border-b border-slate-100 dark:border-slate-700 flex flex-wrap gap-4 items-center">
                <form action="{{ route('admin.support.index') }}" method="GET" class="flex flex-wrap gap-4 items-center flex-1">
                    <div class="flex-1 min-w-[300px] relative">
                        <span
                            class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">search</span>
                        <input
                            class="w-full bg-slate-50 dark:bg-slate-900 border-slate-200 dark:border-slate-700 rounded-lg pl-10 focus:ring-primary focus:border-primary text-sm"
                            placeholder="Tìm mã vé, khách hàng..." type="text" name="search" value="{{ request('search') }}" />
                    </div>
                    <div class="flex gap-2">
                        <select name="status"
                            class="bg-slate-50 dark:bg-slate-900 border-slate-200 dark:border-slate-700 rounded-lg text-sm font-medium focus:ring-primary focus:border-primary" onchange="this.form.submit()">
                            <option value="">Tất cả trạng thái</option>
                            <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>Mới</option>
                            <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>Đang xử lý</option>
                            <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Đã giải quyết</option>
                            <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Đã đóng</option>
                        </select>
                        <select name="priority"
                            class="bg-slate-50 dark:bg-slate-900 border-slate-200 dark:border-slate-700 rounded-lg text-sm font-medium focus:ring-primary focus:border-primary" onchange="this.form.submit()">
                            <option value="">Mức độ ưu tiên</option>
                            <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>Thấp</option>
                            <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>Trung bình</option>
                            <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>Cao</option>
                            <option value="urgent" {{ request('priority') === 'urgent' ? 'selected' : '' }}>Khẩn cấp</option>
                        </select>
                        <select name="assigned_to"
                            class="bg-slate-50 dark:bg-slate-900 border-slate-200 dark:border-slate-700 rounded-lg text-sm font-medium focus:ring-primary focus:border-primary" onchange="this.form.submit()">
                            <option value="">Mọi nhân viên</option>
                            @foreach($staff as $member)
                                <option value="{{ $member->id }}" {{ request('assigned_to') == $member->id ? 'selected' : '' }}>{{ $member->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead
                        class="bg-slate-50 dark:bg-slate-900/50 text-slate-500 text-xs font-bold uppercase tracking-wider">
                        <tr>
                            <th class="px-6 py-4">Mã vé</th>
                            <th class="px-6 py-4">Khách hàng</th>
                            <th class="px-6 py-4">Chủ đề</th>
                            <th class="px-6 py-4">Ưu tiên</th>
                            <th class="px-6 py-4">Trạng thái</th>
                            <th class="px-6 py-4">Nhân viên</th>
                            <th class="px-6 py-4">Ngày gửi</th>
                            <th class="px-6 py-4 text-right">Hành động</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        @forelse($tickets as $ticket)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                                <td class="px-6 py-4 text-sm font-bold text-primary">{{ $ticket->ticket_code }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-slate-200 flex items-center justify-center flex-shrink-0 text-slate-600 font-bold text-xs">
                                            {{ strtoupper(substr($ticket->customer_name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-slate-900 dark:text-slate-100">{{ $ticket->customer_name }}</p>
                                            <p class="text-xs text-slate-500">{{ $ticket->customer_email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm font-medium text-slate-900 dark:text-slate-100 truncate max-w-[250px]">{{ $ticket->subject }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold
                                        @if($ticket->priority === 'urgent') bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400
                                        @elseif($ticket->priority === 'high') bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400
                                        @elseif($ticket->priority === 'medium') bg-primary/20 text-yellow-700 dark:text-primary
                                        @else bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400
                                        @endif">
                                        @if($ticket->priority === 'urgent') Khẩn cấp
                                        @elseif($ticket->priority === 'high') Cao
                                        @elseif($ticket->priority === 'medium') Trung bình
                                        @else Thấp
                                        @endif
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $status_color = match ($ticket->status) {
                                            'open' => 'blue',
                                            'in_progress' => 'yellow',
                                            'resolved' => 'green',
                                            default => 'slate',
                                        };
                                    @endphp
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-{{ $status_color }}-100 text-{{ $status_color }}-700 dark:bg-{{ $status_color }}-900/30 dark:text-{{ $status_color }}-400">
                                        @if($ticket->status === 'open') Mới
                                        @elseif($ticket->status === 'in_progress') Đang xử lý
                                        @elseif($ticket->status === 'resolved') Đã giải quyết
                                        @else Đã đóng
                                        @endif
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    @if($ticket->assignedTo)
                                        <span class="text-sm font-medium text-slate-900 dark:text-slate-100">{{ $ticket->assignedTo->name }}</span>
                                    @else
                                        <span class="text-sm text-slate-400">Chưa gán</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-500">{{ $ticket->created_at->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('admin.support.show', $ticket->id) }}">
                                            <button class="p-2 text-slate-400 hover:text-primary transition-colors"
                                                title="Xem chi tiết">
                                                <span class="material-symbols-outlined text-lg">chat_bubble</span>
                                            </button>
                                        </a>

                                        <a href="{{ route('admin.support.edit', $ticket->id) }}">
                                            <button class="p-2 text-slate-400 hover:text-primary transition-colors"
                                                title="Chỉnh sửa">
                                                <span class="material-symbols-outlined text-lg">edit</span>
                                            </button>
                                        </a>

                                        <form action="{{ route('admin.support.destroy', $ticket->id) }}"
                                            method="post" style="display: inline;">
                                            @csrf
                                            @method('delete')
                                            <button onclick="return(confirm('Xóa vé hỗ trợ'))"
                                                class="p-2 text-slate-400 hover:text-red-600 transition-colors"
                                                title="Xóa vé">
                                                <span class="material-symbols-outlined text-lg">delete</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center text-slate-500">Không có yêu cầu hỗ trợ nào</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="m-5 flex gap-2">

                {{-- Previous --}}
                <a href="{{ $tickets->previousPageUrl() }}"
                    class="size-9 flex items-center justify-center rounded-lg border border-slate-200 text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-700">
                    <span class="material-symbols-outlined">chevron_left</span>
                </a>

                {{-- Page numbers --}}
                @foreach ($tickets->links()->elements[0] ?? [] as $page => $url)
                    <a href="{{ $url }}"
                        class="size-9 flex items-center justify-center rounded-lg border
                        {{ $tickets->currentPage() == $page ? 'border-primary bg-primary text-white' : 'border-slate-200 text-slate-600 hover:bg-slate-50 dark:hover:bg-slate-700' }}
                        font-bold text-sm">
                        {{ $page }}
                    </a>
                @endforeach

                {{-- Next --}}
                <a href="{{ $tickets->nextPageUrl() }}"
                    class="size-9 flex items-center justify-center rounded-lg border border-slate-200 text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-700">
                    <span class="material-symbols-outlined">chevron_right</span>
                </a>

            </div>
        </div>
    </div>
</main>
@endsection
