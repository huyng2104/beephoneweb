@extends('admin.layouts.app')

@section('content')
<div class="p-8 space-y-6">
    <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Return Management</p>
            <h1 class="text-3xl font-bold text-slate-900 dark:text-white mt-1">Danh sách yêu cầu hoàn hàng</h1>
            <p class="text-sm text-slate-500 dark:text-slate-300 mt-1">Theo dõi và xử lý các sản phẩm khách hàng yêu cầu trả/hoàn tiền.</p>
        </div>
        <div class="inline-flex items-center gap-2 text-xs text-slate-500">
            <span class="px-2.5 py-1 rounded-full bg-slate-100 dark:bg-slate-800">Tổng: {{ $returnItems->total() }}</span>
            <span class="px-2.5 py-1 rounded-full bg-slate-100 dark:bg-slate-800">Trang: {{ $returnItems->currentPage() }}/{{ $returnItems->lastPage() }}</span>
        </div>
    </div>

    @if (session('status'))
    <div class="p-3 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl text-sm">
        {{ session('status') }}
    </div>
    @endif

    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-100 dark:border-slate-800 p-4 lg:p-5">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-6 gap-3 items-end">
            <div class="md:col-span-3">
                <label class="block text-xs font-semibold text-slate-500 mb-1">Tìm kiếm</label>
                <input
                    type="text"
                    name="q"
                    value="{{ $search }}"
                    placeholder="Tên sản phẩm, mã đơn, khách hàng..."
                    class="w-full rounded-lg border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
            </div>
            <div class="md:col-span-2">
                <label class="block text-xs font-semibold text-slate-500 mb-1">Trạng thái hoàn hàng</label>
                <select name="return_status" class="w-full rounded-lg border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                    <option value="">Tất cả trạng thái</option>
                    @foreach ($returnStatuses as $returnStatus)
                        @if($returnStatus !== 'none')
                            <option value="{{ $returnStatus }}" @selected($activeReturnStatus === $returnStatus)>{{ $returnStatusLabels[$returnStatus] }}</option>
                        @endif
                    @endforeach
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="flex-1 px-4 py-2 bg-primary text-black rounded-lg font-semibold text-sm hover:brightness-105">Lọc</button>
                <a href="{{ route('admin.returns.index') }}" class="px-4 py-2 rounded-lg border border-slate-200 dark:border-slate-700 text-sm font-semibold text-slate-600 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800">Reset</a>
            </div>
        </form>
    </div>

    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-100 dark:border-slate-800 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-slate-50 dark:bg-slate-800/50">
                    <tr>
                        <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wide">Mã đơn</th>
                        <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wide">Sản phẩm</th>
                        <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wide text-center">Số lượng</th>
                        <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wide text-right">Tổng tiền</th>
                        <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wide">Trạng thái hoàn</th>
                        <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wide text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse ($returnItems as $item)
                    @php
                        $returnClass = match($item->return_status) {
                            \App\Models\OrderItem::RETURN_REQUESTED => 'bg-amber-100 text-amber-700',
                            \App\Models\OrderItem::RETURN_APPROVED => 'bg-blue-100 text-blue-700',
                            \App\Models\OrderItem::RETURN_REJECTED => 'bg-red-100 text-red-700',
                            \App\Models\OrderItem::RETURN_CUSTOMER_SHIPPED => 'bg-indigo-100 text-indigo-700',
                            \App\Models\OrderItem::RETURN_RECEIVED => 'bg-cyan-100 text-cyan-700',
                            \App\Models\OrderItem::RETURN_REFUNDED => 'bg-green-100 text-green-700',
                            default => 'bg-slate-100 text-slate-700',
                        };
                        
                        $productName = $item->product_name ?: ($item->product ? $item->product->name : 'N/A');
                        $productThumbnail = $item->thumbnail ?: ($item->product ? $item->product->thumbnail : null);
                    @endphp
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                        <td class="px-5 py-4 align-top">
                            <p class="font-semibold text-primary">{{ optional($item->order)->order_code }}</p>
                            <p class="text-xs text-slate-500 mt-1">{{ optional($item->return_requested_at)->format('d/m/Y H:i') }}</p>
                        </td>
                        <td class="px-5 py-4 align-top">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded border border-slate-200 dark:border-slate-700 bg-slate-100 shrink-0 overflow-hidden flex items-center justify-center">
                                    @if ($productThumbnail)
                                        <img src="{{ str_starts_with($productThumbnail, 'http') ? $productThumbnail : Storage::url($productThumbnail) }}" alt="Thumbnail" class="w-full h-full object-cover">
                                    @else
                                        <span class="material-symbols-outlined text-[18px] text-slate-400">inventory_2</span>
                                    @endif
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-slate-900 dark:text-white line-clamp-2">{{ $productName }}</p>
                                    <p class="text-xs text-slate-500 mt-1 italic line-clamp-1 cursor-help" title="{{ $item->return_note }}">"{{ $item->return_note }}"</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4 align-top text-center font-medium">x{{ $item->quantity }}</td>
                        <td class="px-5 py-4 text-sm font-bold text-slate-900 dark:text-white text-right align-top">{{ number_format($item->line_total) }} ₫</td>
                        <td class="px-5 py-4 align-top">
                            <span class="inline-flex text-xs px-2.5 py-1 rounded-lg font-semibold {{ $returnClass }}">
                                {{ \App\Models\OrderItem::returnStatusLabels()[$item->return_status] ?? $item->return_status }}
                            </span>
                        </td>
                        <td class="px-5 py-4 text-right align-top">
                            @if($item->order)
                            <a href="{{ route('admin.orders.show', $item->order->id) }}" class="inline-flex items-center px-3 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 text-sm font-semibold text-primary hover:bg-primary/10 transition-colors">
                                Xử lý
                            </a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-5 py-10 text-center">
                            <p class="text-sm font-semibold text-slate-700 dark:text-slate-200">Chưa có yêu cầu hoàn hàng nào</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100 dark:border-slate-800">
            {{ $returnItems->links() }}
        </div>
    </div>
</div>
@endsection
