@extends('admin.layouts.app')

@section('title', 'Lịch sử đặt hàng')

@section('content')
    <main class="max-w-[1200px] mx-auto w-full p-4 md:p-8">
        
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-slate-900 dark:text-slate-100">
                Lịch sử đặt hàng của: {{ $user->name }}
            </h1>
            <a href="{{ route('admin.users.show', $user->id) }}" class="flex items-center gap-2 text-primary hover:underline font-medium">
                <span class="material-symbols-outlined text-sm">arrow_back</span>
                Danh sách chi tiết
            </a>
        </div>

        {{-- BỘ LỌC --}}
        <form method="GET" action="{{ route('admin.users.orders', $user->id) }}" class="mb-6 bg-white dark:bg-slate-900 rounded-xl p-4 border border-primary/10 shadow-sm flex flex-wrap items-end gap-3">
            <div class="flex-1 min-w-[160px]">
                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">Mã đơn hàng</label>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Nhập mã đơn..." class="w-full text-sm border-slate-200 dark:border-slate-700 rounded-md dark:bg-slate-800 dark:text-white pb-1.5 pt-1.5 px-3 focus:ring-primary focus:border-primary transition-colors">
            </div>

            <div class="flex-1 min-w-[160px]">
                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">Trạng thái</label>
                <select name="status" onchange="this.form.submit()" class="w-full text-sm border-slate-200 dark:border-slate-700 rounded-md dark:bg-slate-800 dark:text-white pb-1.5 pt-1.5 px-3 focus:ring-primary focus:border-primary transition-colors">
                    <option value="">Tất cả</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Chờ xác nhận</option>
                    <option value="shipping" {{ request('status') === 'shipping' ? 'selected' : '' }}>Đang giao</option>
                    <option value="received" {{ request('status') === 'received' ? 'selected' : '' }}>Đã nhận</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Thành công</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                </select>
            </div>

            <div class="flex-1 min-w-[160px]">
                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">Hoàn hàng</label>
                <select name="return_status" onchange="this.form.submit()" class="w-full text-sm border-slate-200 dark:border-slate-700 rounded-md dark:bg-slate-800 dark:text-white pb-1.5 pt-1.5 px-3 focus:ring-primary focus:border-primary transition-colors">
                    <option value="">Tất cả</option>
                    <option value="none" {{ request('return_status') === 'none' ? 'selected' : '' }}>Không hoàn hàng</option>
                    <option value="requested" {{ request('return_status') === 'requested' ? 'selected' : '' }}>Đã gửi yêu cầu</option>
                    <option value="approved" {{ request('return_status') === 'approved' ? 'selected' : '' }}>Admin đã duyệt</option>
                    <option value="rejected" {{ request('return_status') === 'rejected' ? 'selected' : '' }}>Admin từ chối</option>
                    <option value="customer_shipped" {{ request('return_status') === 'customer_shipped' ? 'selected' : '' }}>Khách đã gửi hàng hoàn</option>
                    <option value="received" {{ request('return_status') === 'received' ? 'selected' : '' }}>Admin đã nhận hàng hoàn</option>
                    <option value="refunded" {{ request('return_status') === 'refunded' ? 'selected' : '' }}>Đã hoàn tiền vào ví</option>
                </select>
            </div>
            
            <div class="flex-1 min-w-[130px]">
                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">Từ ngày</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" onchange="this.form.submit()" class="w-full text-sm border-slate-200 dark:border-slate-700 rounded-md dark:bg-slate-800 dark:text-white pb-1.5 pt-1.5 px-3 focus:ring-primary focus:border-primary transition-colors">
            </div>
            
            <div class="flex-1 min-w-[130px]">
                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">Đến ngày</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" onchange="this.form.submit()" class="w-full text-sm border-slate-200 dark:border-slate-700 rounded-md dark:bg-slate-800 dark:text-white pb-1.5 pt-1.5 px-3 focus:ring-primary focus:border-primary transition-colors">
            </div>

            <div class="flex-1 min-w-[110px]">
                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">Sắp xếp</label>
                <select name="sort" onchange="this.form.submit()" class="w-full text-sm border-slate-200 dark:border-slate-700 rounded-md dark:bg-slate-800 dark:text-white pb-1.5 pt-1.5 px-3 focus:ring-primary focus:border-primary transition-colors">
                    <option value="desc" {{ request('sort', 'desc') === 'desc' ? 'selected' : '' }}>Mới nhất</option>
                    <option value="asc" {{ request('sort') === 'asc' ? 'selected' : '' }}>Cũ nhất</option>
                </select>
            </div>
            
            @if(request()->anyFilled(['status', 'return_status', 'date_from', 'date_to', 'q']) || request('sort') === 'asc')
            <div class="min-w-[100px] mb-[2px]">
                <a href="{{ route('admin.users.orders', $user->id) }}" class="flex items-center justify-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-red-600 bg-red-50 hover:bg-red-100 rounded-md transition-colors w-full border border-red-200 dark:bg-red-900/20 dark:text-red-400 dark:border-red-900/40">
                    <span class="material-symbols-outlined text-[16px]">close</span> Xóa lọc
                </a>
            </div>
            @endif
        </form>

        <div class="bg-white dark:bg-slate-900 rounded-xl p-6 mb-8 border border-primary/10 shadow-sm">
            <div class="flex-1">
                <div class="flex items-center justify-between mb-4">
                    <p class="text-sm font-bold text-slate-900 dark:text-slate-100">Danh sách đơn hàng</p>
                    <span class="text-xs font-medium bg-blue-50 text-blue-600 px-2 py-1 rounded-md dark:bg-blue-900/30 dark:text-blue-400">
                        Tổng: {{ $orders->total() }} đơn hàng
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-800 text-sm">
                        <thead class="bg-slate-50 dark:bg-slate-800/50">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold text-slate-600 dark:text-slate-300">Mã Đơn</th>
                                <th class="px-4 py-3 text-left font-semibold text-slate-600 dark:text-slate-300">Ngày đặt</th>
                                <th class="px-4 py-3 text-left font-semibold text-slate-600 dark:text-slate-300">Tổng tiền</th>
                                <th class="px-4 py-3 text-left font-semibold text-slate-600 dark:text-slate-300">Trạng thái</th>
                                <th class="px-4 py-3 text-left font-semibold text-slate-600 dark:text-slate-300">Hoàn hàng</th>
                                <th class="px-4 py-3 text-center font-semibold text-slate-600 dark:text-slate-300">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @forelse ($orders as $order)
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                                    <td class="px-4 py-4 font-medium text-slate-900 dark:text-slate-100">
                                        #{{ $order->order_code ?? $order->id }}</td>
                                    <td class="px-4 py-4 text-slate-500 dark:text-slate-400">
                                        {{ $order->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="px-4 py-4 font-semibold text-slate-900 dark:text-slate-100">
                                        {{ number_format($order->total_price ?? 0) }}đ</td>
                                    <td class="px-4 py-4">
                                        @php
                                            $status_class = match ($order->status ?? 'pending') {
                                                'completed', 'received' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
                                                'shipping' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
                                                'cancelled' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                                                'pending' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
                                                default => 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-400',
                                            };

                                            $status_text = match ($order->status ?? 'pending') {
                                                'pending' => 'Chờ xác nhận',
                                                'shipping' => 'Đang giao',
                                                'received' => 'Đã nhận',
                                                'completed' => 'Thành công',
                                                'cancelled' => 'Đã hủy',
                                                default => 'Chờ xử lý',
                                            };
                                        @endphp
                                        <span class="px-2.5 py-1 text-xs font-bold rounded-full {{ $status_class }}">
                                            {{ $status_text }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4">
                                        @php
                                            $return_status_class = match ($order->return_status ?? 'none') {
                                                'none' => 'text-slate-500',
                                                'requested' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400 px-2.5 py-1 text-xs font-bold rounded-full',
                                                'approved' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400 px-2.5 py-1 text-xs font-bold rounded-full',
                                                'rejected' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400 px-2.5 py-1 text-xs font-bold rounded-full',
                                                'customer_shipped' => 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400 px-2.5 py-1 text-xs font-bold rounded-full',
                                                'received' => 'bg-teal-100 text-teal-700 dark:bg-teal-900/30 dark:text-teal-400 px-2.5 py-1 text-xs font-bold rounded-full',
                                                'refunded' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400 px-2.5 py-1 text-xs font-bold rounded-full',
                                                default => 'text-slate-500',
                                            };

                                            $return_status_text = match ($order->return_status ?? 'none') {
                                                'none' => '-',
                                                'requested' => 'Đã gửi yêu cầu',
                                                'approved' => 'Đã duyệt',
                                                'rejected' => 'Từ chối',
                                                'customer_shipped' => 'Đã gửi hoàn',
                                                'received' => 'Đã nhận hoàn',
                                                'refunded' => 'Đã hoàn tiền',
                                                default => '-',
                                            };
                                        @endphp
                                        <span class="{{ $return_status_class }}">
                                            {{ $return_status_text }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 text-center">
                                        <a href="{{ route('admin.orders.show', $order->id) }}"
                                            class="text-primary hover:underline text-xs font-bold flex items-center justify-center gap-1">
                                            Xem chi tiết
                                            <span class="material-symbols-outlined text-sm">chevron_right</span>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-slate-400 dark:text-slate-500 italic">
                                        Người dùng này chưa có đơn hàng nào hoặc không có dữ liệu phù hợp với bộ lọc.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($orders->hasPages())
                    <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-800">
                        {{ $orders->links() }}
                    </div>
                @endif
            </div>
        </div>
    </main>
@endsection
