@extends('admin.layouts.app')

@section('content')
<div class="p-8 space-y-6">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Order Detail</p>
            <h1 class="text-3xl font-bold text-slate-900 dark:text-white mt-1">Đơn hàng {{ $order->order_code }}</h1>
            <p class="text-sm text-slate-500 dark:text-slate-300 mt-1">Theo dõi thông tin người đặt, người nhận và xử lý trạng thái.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.orders.print.pdf', $order) }}" class="px-4 py-2 rounded-lg border border-slate-200 dark:border-slate-700 text-sm font-semibold hover:bg-slate-50 dark:hover:bg-slate-800">
                In PDF
            </a>
            <a href="{{ route('admin.orders.index') }}" class="px-4 py-2 rounded-lg border border-slate-200 dark:border-slate-700 text-sm font-semibold hover:bg-slate-50 dark:hover:bg-slate-800">
                Quay lại danh sách
            </a>
        </div>
    </div>

    @if (session('status'))
    <div class="p-3 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl text-sm">
        {{ session('status') }}
    </div>
    @endif

    @if ($errors->any())
    <div class="p-3 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm">
        <ul class="list-disc pl-5 space-y-1">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-100 dark:border-slate-800 p-4">
            <p class="text-xs text-slate-500">Tổng tiền</p>
            <p class="text-2xl font-bold text-slate-900 dark:text-white mt-1">{{ number_format($order->total_amount) }} ₫</p>
        </div>
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-100 dark:border-slate-800 p-4">
            <p class="text-xs text-slate-500">Trạng thái đơn</p>
            <p class="text-base font-semibold text-slate-900 dark:text-white mt-1">{{ $statusLabels[$order->status] ?? $order->status }}</p>
        </div>
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-100 dark:border-slate-800 p-4">
            <p class="text-xs text-slate-500">Đổi/Trả</p>
            <p class="text-base font-semibold text-slate-900 dark:text-white mt-1">{{ $returnStatusLabels[$order->return_status] ?? $order->return_status }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-100 dark:border-slate-800 p-6 space-y-4">
                <h2 class="text-lg font-bold text-slate-900 dark:text-white">Thông tin người đặt / người nhận</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div class="p-4 rounded-lg bg-slate-50 dark:bg-slate-800/50">
                        <p class="text-xs uppercase text-slate-500 font-semibold">Người đặt hàng</p>
                        <p class="font-semibold text-slate-900 dark:text-white mt-1">{{ $order->customer_name }}</p>
                        <p class="text-slate-600 dark:text-slate-300 text-sm mt-1">{{ $order->customer_phone }}</p>
                        <p class="text-slate-500 text-xs mt-1">{{ $order->customer_email ?: 'Chưa có email' }}</p>
                    </div>
                    <div class="p-4 rounded-lg bg-slate-50 dark:bg-slate-800/50">
                        <p class="text-xs uppercase text-slate-500 font-semibold">Người nhận hàng</p>
                        <p class="font-semibold text-slate-900 dark:text-white mt-1">{{ $order->recipient_name ?: $order->customer_name }}</p>
                        <p class="text-slate-600 dark:text-slate-300 text-sm mt-1">{{ $order->recipient_phone ?: $order->customer_phone }}</p>
                        <p class="text-slate-500 text-xs mt-1">{{ $order->recipient_address ?: $order->shipping_address ?: 'Chưa có địa chỉ' }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-slate-500">Ngày đặt</p>
                        <p class="font-semibold text-slate-900 dark:text-white">{{ optional($order->ordered_at)->format('d/m/Y H:i') ?? $order->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div>
                        <p class="text-slate-500">Mã đơn</p>
                        <p class="font-semibold text-primary">{{ $order->order_code }}</p>
                    </div>
                </div>

                <div>
                    <p class="text-slate-500 text-sm">Ghi chú đơn hàng</p>
                    <p class="font-semibold text-slate-900 dark:text-white">{{ $order->note ?: 'Không có ghi chú' }}</p>
                </div>
            </div>

            @if ($order->cancellation_reason || $order->return_note)
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-100 dark:border-slate-800 p-6 space-y-4">
                <h2 class="text-lg font-bold text-slate-900 dark:text-white">Thông tin phát sinh</h2>

                @if ($order->cancellation_reason)
                <div class="p-3 bg-red-50 border border-red-200 rounded-lg">
                    <p class="text-sm text-red-700 font-semibold">Lý do hủy: {{ $order->cancellation_reason }}</p>
                </div>
                @endif

                @if ($order->return_note)
                <div class="p-3 bg-amber-50 border border-amber-200 rounded-lg">
                    <p class="text-sm text-amber-700 font-semibold">Ghi chú đổi/trả: {{ $order->return_note }}</p>
                </div>
                @endif
            </div>
            @endif

            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-100 dark:border-slate-800 p-6 space-y-4">
                <div class="flex items-center justify-between gap-3">
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white">Sản phẩm đã mua</h2>
                    <span class="text-xs text-slate-500">{{ $order->items->sum('quantity') }} sản phẩm</span>
                </div>

                @if ($order->items->isNotEmpty())
                <div class="overflow-x-auto rounded-xl border border-slate-100 dark:border-slate-800">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-50 dark:bg-slate-800/50 text-slate-500">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold">Sản phẩm</th>
                                <th class="px-4 py-3 text-left font-semibold">SKU</th>
                                <th class="px-4 py-3 text-right font-semibold">Đơn giá</th>
                                <th class="px-4 py-3 text-right font-semibold">SL</th>
                                <th class="px-4 py-3 text-right font-semibold">Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @foreach ($order->items as $item)
                            <tr>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3 min-w-[220px]">
                                        <div class="size-10 rounded-lg bg-slate-100 dark:bg-slate-800 overflow-hidden flex items-center justify-center">
                                            @if ($item->thumbnail)
                                            <img src="{{ $item->thumbnail }}" alt="{{ $item->product_name }}" class="w-full h-full object-cover" onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden');">
                                            <span class="material-symbols-outlined text-slate-400 text-[18px] hidden">inventory_2</span>
                                            @else
                                            <span class="material-symbols-outlined text-slate-400 text-[18px]">inventory_2</span>
                                            @endif
                                        </div>
                                        <span class="font-semibold text-slate-900 dark:text-white">{{ $item->product_name }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-slate-600 dark:text-slate-300">{{ $item->product_sku ?: '-' }}</td>
                                <td class="px-4 py-3 text-right text-slate-700 dark:text-slate-200">{{ number_format($item->unit_price) }} ₫</td>
                                <td class="px-4 py-3 text-right text-slate-700 dark:text-slate-200">{{ $item->quantity }}</td>
                                <td class="px-4 py-3 text-right font-semibold text-slate-900 dark:text-white">{{ number_format($item->line_total) }} ₫</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="p-4 rounded-lg border border-dashed border-slate-200 dark:border-slate-700 text-sm text-slate-500">
                    Đơn hàng này chưa có dữ liệu chi tiết sản phẩm.
                </div>
                @endif
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-100 dark:border-slate-800 p-6 space-y-5 h-fit lg:sticky lg:top-20">
            <h2 class="text-lg font-bold text-slate-900 dark:text-white">Thao tác xử lý</h2>

            <form action="{{ route('admin.orders.status.update', $order) }}" method="POST" class="space-y-3">
                @csrf
                @method('PATCH')
                <label class="text-sm font-semibold text-slate-700 dark:text-slate-200">Cập nhật trạng thái</label>
                <select name="status" class="w-full rounded-lg border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                    @foreach ($availableStatuses as $status)
                    <option value="{{ $status }}" @selected($order->status === $status)>{{ $statusLabels[$status] }}</option>
                    @endforeach
                </select>
                <button type="submit" class="w-full px-4 py-2 bg-primary text-black rounded-lg font-semibold text-sm hover:brightness-105">Lưu trạng thái</button>
            </form>

            <form action="{{ route('admin.orders.cancel', $order) }}" method="POST" class="space-y-3 border-t border-slate-100 dark:border-slate-800 pt-4">
                @csrf
                @method('PATCH')
                <label class="text-sm font-semibold text-slate-700 dark:text-slate-200">Hủy đơn hàng</label>
                <textarea name="cancellation_reason" rows="3" class="w-full rounded-lg border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white" placeholder="Nhập lý do hủy đơn..."></textarea>
                <button type="submit" class="w-full px-4 py-2 bg-red-500 text-white rounded-lg font-semibold text-sm hover:bg-red-600">Hủy đơn</button>
            </form>

            <form action="{{ route('admin.orders.return.confirm', $order) }}" method="POST" class="space-y-3 border-t border-slate-100 dark:border-slate-800 pt-4">
                @csrf
                @method('PATCH')
                <label class="text-sm font-semibold text-slate-700 dark:text-slate-200">Xác nhận đổi/trả</label>
                <textarea name="return_note" rows="3" class="w-full rounded-lg border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white" placeholder="Ghi chú xác nhận đổi/trả (nếu có)...">{{ $order->return_note }}</textarea>
                <button type="submit" class="w-full px-4 py-2 bg-amber-500 text-black rounded-lg font-semibold text-sm hover:bg-amber-400">Xác nhận đổi/trả hàng</button>
            </form>
        </div>
    </div>
</div>
@endsection