@extends('admin.layouts.app')

@section('title', 'Phản hồi khách hàng')

@section('content')
    <div class="p-8 space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Phản hồi khách hàng</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400">Quản lý các yêu cầu hỗ trợ từ chatbot và khách hàng.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
            <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl p-4">
                <p class="text-xs text-slate-500 uppercase tracking-wide">Tổng yêu cầu</p>
                <p class="text-2xl font-bold text-slate-900 dark:text-white mt-1">{{ $totalTickets }}</p>
            </div>
            <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl p-4">
                <p class="text-xs text-orange-500 uppercase tracking-wide">Mới</p>
                <p class="text-2xl font-bold text-slate-900 dark:text-white mt-1">{{ $pendingTickets }}</p>
            </div>
            <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl p-4">
                <p class="text-xs text-amber-500 uppercase tracking-wide">Đang xử lý</p>
                <p class="text-2xl font-bold text-slate-900 dark:text-white mt-1">{{ $processingTickets }}</p>
            </div>
            <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl p-4">
                <p class="text-xs text-green-500 uppercase tracking-wide">Hoàn tất</p>
                <p class="text-2xl font-bold text-slate-900 dark:text-white mt-1">{{ $doneTickets }}</p>
            </div>
        </div>

        <form method="GET" class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl p-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <input type="text" name="keyword" value="{{ request('keyword') }}"
                    class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white"
                    placeholder="Tìm mã ticket, tên, email, tiêu đề...">
                <select name="status"
                    class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white">
                    <option value="">Tất cả trạng thái</option>
                    <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>Mới</option>
                    <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>Đang xử lý</option>
                    <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Đã xử lý</option>
                    <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Đóng</option>
                </select>
                <div class="flex gap-2">
                    <button type="submit"
                        class="px-4 py-2 rounded-lg bg-primary text-white font-semibold hover:opacity-90 transition">Lọc</button>
                    <a href="{{ route('admin.tickets.index') }}"
                        class="px-4 py-2 rounded-lg bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200 font-semibold">Reset</a>
                </div>
            </div>
        </form>

        <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 dark:bg-slate-700/50">
                        <tr>
                            <th class="text-left px-4 py-3 font-semibold text-slate-600 dark:text-slate-300">Mã ticket</th>
                            <th class="text-left px-4 py-3 font-semibold text-slate-600 dark:text-slate-300">Khách hàng</th>
                            <th class="text-left px-4 py-3 font-semibold text-slate-600 dark:text-slate-300">Tiêu đề</th>
                            <th class="text-left px-4 py-3 font-semibold text-slate-600 dark:text-slate-300">Trạng thái</th>
                            <th class="text-left px-4 py-3 font-semibold text-slate-600 dark:text-slate-300">Ưu tiên</th>
                            <th class="text-left px-4 py-3 font-semibold text-slate-600 dark:text-slate-300">Tạo lúc</th>
                            <th class="text-right px-4 py-3 font-semibold text-slate-600 dark:text-slate-300">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        @forelse($tickets as $ticket)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30">
                                <td class="px-4 py-3 font-semibold text-slate-900 dark:text-white">{{ $ticket->ticket_code }}</td>
                                <td class="px-4 py-3">
                                    <p class="font-medium text-slate-900 dark:text-white">{{ $ticket->customer_name }}</p>
                                    <p class="text-xs text-slate-500">{{ $ticket->customer_email }}</p>
                                </td>
                                <td class="px-4 py-3 text-slate-700 dark:text-slate-300">{{ $ticket->subject }}</td>
                                <td class="px-4 py-3">
                                    @if($ticket->status === 'open')
                                        <span class="px-2 py-1 rounded-full text-xs bg-orange-100 text-orange-700">Mới</span>
                                    @elseif($ticket->status === 'in_progress')
                                        <span class="px-2 py-1 rounded-full text-xs bg-amber-100 text-amber-700">Đang xử lý</span>
                                    @elseif($ticket->status === 'resolved')
                                        <span class="px-2 py-1 rounded-full text-xs bg-green-100 text-green-700">Đã xử lý</span>
                                    @else
                                        <span class="px-2 py-1 rounded-full text-xs bg-slate-200 text-slate-700">Đóng</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-slate-700 dark:text-slate-300">{{ strtoupper($ticket->priority) }}</td>
                                <td class="px-4 py-3 text-slate-600 dark:text-slate-300">{{ $ticket->created_at->format('d/m/Y H:i') }}</td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('admin.tickets.show', $ticket->id) }}"
                                        class="px-3 py-1.5 rounded-lg bg-blue-500 text-white text-xs font-semibold hover:brightness-110">Xem & trả lời</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-10 text-center text-slate-500">Chưa có ticket nào.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-4 py-3 border-t border-slate-100 dark:border-slate-700">
                {{ $tickets->withQueryString()->links() }}
            </div>
        </div>
    </div>
@endsection

