@extends('admin.layouts.app')

@section('content')
<div class="p-8 space-y-6">
    <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Order Management</p>
            <h1 class="text-3xl font-bold text-slate-900 dark:text-white mt-1">Danh sách đơn hàng</h1>
            <p class="text-sm text-slate-500 dark:text-slate-300 mt-1">Theo dõi nhanh trạng thái đơn và mở chi tiết để xử lý.</p>
        </div>
        <div class="inline-flex items-center gap-2 text-xs text-slate-500">
            <span class="px-2.5 py-1 rounded-full bg-slate-100 dark:bg-slate-800">Tổng: {{ $orders->total() }}</span>
            <span class="px-2.5 py-1 rounded-full bg-slate-100 dark:bg-slate-800">Trang: {{ $orders->currentPage() }}/{{ $orders->lastPage() }}</span>
        </div>
    </div>

    @if (session('status'))
    <div class="p-3 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl text-sm">
        {{ session('status') }}
    </div>
    @endif
    
    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-100 dark:border-slate-800 p-4 lg:p-5">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-3 items-end">
            <div class="md:col-span-3">
                <label class="block text-xs font-semibold text-slate-500 mb-1">Tìm kiếm</label>
                <input
                    type="text"
                    name="q"
                    value="{{ $search }}"
                    placeholder="Mã đơn, tên người đặt, số điện thoại..."
                    class="w-full rounded-lg border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1">Trạng thái</label>
                <select name="status" class="w-full rounded-lg border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                    <option value="">Tất cả trạng thái</option>
                    @foreach ($statuses as $status)
                    <option value="{{ $status }}" @selected($activeStatus===$status)>{{ $statusLabels[$status] }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="flex-1 px-4 py-2 bg-primary text-black rounded-lg font-semibold text-sm hover:brightness-105">Lọc</button>
                <a href="{{ route('admin.orders.index') }}" class="px-4 py-2 rounded-lg border border-slate-200 dark:border-slate-700 text-sm font-semibold text-slate-600 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800">Reset</a>
            </div>
        </form>
    </div>

    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-100 dark:border-slate-800 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-slate-50 dark:bg-slate-800/50">
                    <tr>
                        <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wide">Mã đơn</th>
                        <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wide">Người đặt / Người nhận</th>
                        <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wide">Ngày đặt</th>
                        <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wide text-right">Tổng tiền</th>
                        <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wide">Thanh toán</th> 
                        <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wide">Trạng thái</th>
                        <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wide text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse ($orders as $order)
                    @php
                    $statusClass = match($order->status) {
                        \App\Models\Order::STATUS_PENDING => 'bg-slate-100 text-slate-700',
                        \App\Models\Order::STATUS_PACKING => 'bg-amber-100 text-amber-700',
                        \App\Models\Order::STATUS_SHIPPING => 'bg-blue-100 text-blue-700',
                        \App\Models\Order::STATUS_DELIVERED => 'bg-emerald-100 text-emerald-700',
                        \App\Models\Order::STATUS_RECEIVED => 'bg-green-100 text-green-700',
                        \App\Models\Order::STATUS_CANCELLED => 'bg-red-100 text-red-700',
                        default => 'bg-slate-100 text-slate-700',
                    };
                    @endphp
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                        <td class="px-5 py-4 align-top">
                            <p class="font-semibold text-primary">{{ $order->order_code }}</p>
                        </td>
                        <td class="px-5 py-4 align-top">
                            <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $order->customer_name }} - {{ $order->customer_phone }}</p>
                            <p class="text-xs text-slate-500 mt-1">Nhận: {{ $order->recipient_name ?: $order->customer_name }} - {{ $order->recipient_phone ?: $order->customer_phone }}</p>
                        </td>
                        <td class="px-5 py-4 align-top">
                            <p class="text-sm text-slate-700 dark:text-slate-200">{{ optional($order->ordered_at)->format('d/m/Y') ?? $order->created_at->format('d/m/Y') }}</p>
                            <p class="text-xs text-slate-500">{{ optional($order->ordered_at)->format('H:i') ?? $order->created_at->format('H:i') }}</p>
                        </td>
                        <td class="px-5 py-4 text-sm font-bold text-slate-900 dark:text-white text-right align-top">{{ number_format($order->total_amount) }} ₫</td>
                        
                        <td class="px-5 py-4 align-top">
                            <p class="font-bold text-sm text-slate-900 dark:text-white uppercase">{{ $order->payment_method ?? 'COD' }}</p>
                            
                            {{-- LOGIC MỚI: Nếu payment_status = paid HOẶC đơn đã giao/đã nhận thì hiển thị "Đã thanh toán" --}}
                            @if(($order->payment_status ?? 'pending') == 'paid' || in_array($order->status, [\App\Models\Order::STATUS_DELIVERED, \App\Models\Order::STATUS_RECEIVED]))
                                <span class="inline-flex mt-1 text-[11px] px-2 py-0.5 rounded bg-green-100 text-green-700 font-bold">Đã thanh toán</span>
                            @else
                                <span class="inline-flex mt-1 text-[11px] px-2 py-0.5 rounded bg-amber-100 text-amber-700 font-bold">Chưa thanh toán</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 align-top">
                            <span class="inline-flex text-xs px-2.5 py-1 rounded-lg font-semibold {{ $statusClass }}">
                                {{ $statusLabels[$order->status] ?? $order->status }}
                            </span>
                        </td>
                        <td class="px-5 py-4 text-right align-top">
                            <a href="{{ route('admin.orders.show', $order) }}" class="inline-flex items-center px-3 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 text-sm font-semibold text-primary hover:bg-primary/10">
                                Chi tiết
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-5 py-10 text-center"> 
                            <p class="text-sm font-semibold text-slate-700 dark:text-slate-200">Chưa có đơn hàng nào</p>
                            <p class="text-xs text-slate-500 mt-1">Hãy seed dữ liệu hoặc tạo đơn hàng mới để bắt đầu.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100 dark:border-slate-800">
            {{ $orders->links() }}
        </div>
    </div>
</div>
@endsection