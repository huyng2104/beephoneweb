@extends('admin.layouts.app')

@section('content')
<div class="p-8 space-y-6">
    <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Return Management</p>
            <h1 class="text-3xl font-bold text-slate-900 dark:text-white mt-1">Danh sách yêu cầu hoàn hàng</h1>
            <p class="text-sm text-slate-500 dark:text-slate-300 mt-1">Theo dõi và xử lý các yêu cầu trả/hoàn tiền của khách hàng.</p>
        </div>
        <div class="inline-flex items-center gap-2 text-xs text-slate-500">
            <span class="px-2.5 py-1 rounded-full bg-slate-100 dark:bg-slate-800">Tổng: {{ $returnRequests->total() }}</span>
            <span class="px-2.5 py-1 rounded-full bg-slate-100 dark:bg-slate-800">Trang: {{ $returnRequests->currentPage() }}/{{ $returnRequests->lastPage() }}</span>
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
                <input type="text" name="q" value="{{ $search }}"
                    placeholder="Mã hoàn, mã đơn, tên KH..."
                    class="w-full rounded-lg border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
            </div>
            <div class="md:col-span-2">
                <label class="block text-xs font-semibold text-slate-500 mb-1">Trạng thái</label>
                <select name="return_status" class="w-full rounded-lg border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                    <option value="">Tất cả trạng thái</option>
                    @foreach ($returnStatuses as $rs)
                        <option value="{{ $rs }}" @selected($activeReturnStatus === $rs)>{{ $returnStatusLabels[$rs] ?? $rs }}</option>
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
                        <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wide">Mã hoàn / Đơn hàng</th>
                        <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wide">Sản phẩm</th>
                        <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wide text-right">Hoàn dự kiến</th>
                        <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wide">Trạng thái</th>
                        <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wide text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse ($returnRequests as $rr)
                    @php
                        $rrStatusColors = [
                            'pending'   => 'bg-amber-100 text-amber-700',
                            'approved'  => 'bg-blue-100 text-blue-700',
                            'rejected'  => 'bg-red-100 text-red-700',
                            'picking'   => 'bg-indigo-100 text-indigo-700',
                            'received'  => 'bg-cyan-100 text-cyan-700',
                            'completed' => 'bg-green-100 text-green-700',
                        ];
                        $returnClass = $rrStatusColors[$rr->status] ?? 'bg-slate-100 text-slate-700';
                    @endphp
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                        <td class="px-5 py-4 align-middle">
                            <p class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wide">{{ $rr->return_code }}</p>
                            <p class="text-xs font-medium text-slate-500 mt-0.5">Đơn: <span class="font-bold text-slate-700 dark:text-slate-300">{{ optional($rr->order)->order_code }}</span></p>
                            <p class="text-xs font-medium text-slate-400 mt-0.5 flex items-center gap-1">
                                <span class="material-symbols-outlined text-[12px]">schedule</span>
                                {{ $rr->created_at->format('d/m/Y H:i') }}
                            </p>
                        </td>
                        <td class="px-5 py-4 align-middle max-w-xs">
                            <div class="space-y-0.5">
                                @foreach($rr->items as $ri)
                                    <p class="text-sm font-bold text-slate-900 dark:text-white">
                                        {{ $ri->orderItem->product_name ?? '—' }}
                                        <span class="text-xs font-medium text-slate-400">x{{ $ri->quantity }}</span>
                                    </p>
                                @endforeach
                            </div>
                            @if($rr->reason)
                                <p class="text-xs font-medium text-slate-500 mt-1 italic line-clamp-1 cursor-help" title="{{ $rr->reason }}">“{{ $rr->reason }}”</p>
                            @endif
                        </td>
                        <td class="px-5 py-4 align-middle text-right whitespace-nowrap">
                            <p class="text-sm font-bold text-emerald-600 dark:text-emerald-400">{{ number_format($rr->total_refund_amount) }} ₫</p>
                        </td>
                        <td class="px-5 py-4 align-middle">
                            <span class="inline-flex items-center gap-1.5 text-xs px-2.5 py-1.5 rounded-lg font-bold border {{ $returnClass }}">
                                {{ $returnStatusLabels[$rr->status] ?? $rr->status }}
                            </span>
                            @if($rr->tracking_number)
                                <p class="text-xs font-medium text-indigo-500 mt-1.5 flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[13px]">local_shipping</span>
                                    {{ $rr->tracking_number }}
                                </p>
                            @endif
                        </td>
                        <td class="px-5 py-4 align-middle text-right">
                            @if($rr->order)
                            <a href="{{ route('admin.orders.show', $rr->order->id) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                                <span class="material-symbols-outlined text-[14px]">open_in_new</span>
                                Xử lý
                            </a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-5 py-10 text-center">
                            <p class="text-sm font-semibold text-slate-700 dark:text-slate-200">Chưa có yêu cầu hoàn hàng nào</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100 dark:border-slate-800">
            {{ $returnRequests->links() }}
        </div>
    </div>
</div>
@endsection
