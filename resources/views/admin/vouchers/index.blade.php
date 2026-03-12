@extends('admin.layouts.app')

@section('content')
<div class="w-full p-4 sm:p-8 space-y-6 font-display">

    <div class="bg-white dark:bg-gray-900 p-6 rounded-xl border border-slate-100 dark:border-gray-800 shadow-sm">
        <div class="flex flex-wrap justify-between items-end gap-4">
            <div class="flex flex-col gap-1">
                <div class="flex items-center gap-2 mb-1">
                    <span class="text-slate-400 text-xs uppercase font-bold tracking-wider">Quản lý Bán hàng</span>
                    <span class="material-symbols-outlined text-sm text-slate-400">chevron_right</span>
                    <span class="text-primary font-bold text-xs uppercase tracking-wider">Khuyến mãi</span>
                </div>
                <h2 class="text-slate-900 dark:text-white text-3xl font-black tracking-tight">Quản lý khuyến mãi</h2>
                <p class="text-slate-500 dark:text-gray-400 text-sm font-normal">Theo dõi và cấu hình các chương trình ưu đãi của Bee Phone.</p>
            </div>
            
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.vouchers.index', ['deleted' => 'trash']) }}" class="flex items-center gap-2 rounded-lg h-11 px-4 bg-white border border-gray-200 text-gray-700 text-sm font-bold shadow-sm hover:text-red-600 hover:border-red-200 hover:bg-red-50 transition-all">
                    <span class="material-symbols-outlined">delete</span>
                    <span>Thùng rác</span>
                </a>
                <a href="{{ route('admin.vouchers.create') }}" class="flex items-center gap-2 rounded-lg h-11 px-6 bg-primary text-[#181611] text-sm font-bold shadow-sm hover:scale-[1.02] active:scale-95 transition-all">
                    <span class="material-symbols-outlined">add_circle</span>
                    <span>Tạo mã mới</span>
                </a>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="flex items-start gap-3 p-4 rounded-xl bg-green-50 border border-green-200 text-green-700 shadow-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <div>
                <p class="font-semibold text-sm">Thành công</p>
                <p class="text-sm">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="flex items-start gap-3 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 shadow-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
            <div>
                <p class="font-semibold text-sm">Lỗi</p>
                <p class="text-sm">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    {{-- <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="flex flex-col gap-2 rounded-xl p-6 bg-white dark:bg-gray-900 border border-[#e6e3db]">
                <p class="text-[#8a8060] text-sm font-medium">Tổng mã đang chạy</p>
                <p class="text-[#181611] dark:text-white text-3xl font-bold">12</p>
                <div class="flex items-center gap-1 text-[#078812] text-xs font-bold bg-[#078812]/10 px-2 py-0.5 rounded w-fit">
                    <span class="material-symbols-outlined text-sm">trending_up</span>
                    <span>+5%</span>
                </div>
            </div>
            <div class="flex flex-col gap-2 rounded-xl p-6 bg-white dark:bg-gray-900 border border-[#e6e3db]">
                <p class="text-[#8a8060] text-sm font-medium">Lượt sử dụng (30 ngày)</p>
                <p class="text-[#181611] dark:text-white text-3xl font-bold">1,250</p>
                <div class="flex items-center gap-1 text-[#078812] text-xs font-bold bg-[#078812]/10 px-2 py-0.5 rounded w-fit">
                    <span class="material-symbols-outlined text-sm">trending_up</span>
                    <span>+12%</span>
                </div>
            </div>
            <div class="flex flex-col gap-2 rounded-xl p-6 bg-white dark:bg-gray-900 border border-[#e6e3db]">
                <p class="text-[#8a8060] text-sm font-medium">Tiết kiệm cho khách</p>
                <p class="text-[#181611] dark:text-white text-3xl font-bold">45.0M</p>
                <div class="flex items-center gap-1 text-[#e71408] text-xs font-bold bg-[#e71408]/10 px-2 py-0.5 rounded w-fit">
                    <span class="material-symbols-outlined text-sm">trending_down</span>
                    <span>-2%</span>
                </div>
            </div>
        </div>
        <div class="flex flex-col gap-4 rounded-xl p-6 bg-white dark:bg-gray-900 border border-[#e6e3db]">
            <div class="flex justify-between items-start">
                <p class="text-[#181611] dark:text-white text-sm font-bold">Lượt dùng theo ngày</p>
                <p class="text-[#8a8060] text-xs">7 ngày qua</p>
            </div>
            <div class="flex items-end justify-between h-32 gap-2 px-1">
                <div class="bg-primary/20 hover:bg-primary transition-colors w-full rounded-t" style="height: 40%;" title="Thứ 2"></div>
                <div class="bg-primary/20 hover:bg-primary transition-colors w-full rounded-t" style="height: 60%;" title="Thứ 3"></div>
                <div class="bg-primary/20 hover:bg-primary transition-colors w-full rounded-t" style="height: 30%;" title="Thứ 4"></div>
                <div class="bg-primary/20 hover:bg-primary transition-colors w-full rounded-t" style="height: 95%;" title="Thứ 5"></div>
                <div class="bg-primary/20 hover:bg-primary transition-colors w-full rounded-t" style="height: 20%;" title="Thứ 6"></div>
                <div class="bg-primary/20 hover:bg-primary transition-colors w-full rounded-t" style="height: 70%;" title="Thứ 7"></div>
                <div class="bg-primary/20 hover:bg-primary transition-colors w-full rounded-t" style="height: 85%;" title="Chủ nhật"></div>
            </div>
            <div class="flex justify-between text-[10px] text-[#8a8060] font-bold">
                <span>T2</span><span>T3</span><span>T4</span><span>T5</span><span>T6</span><span>T7</span><span>CN</span>
            </div>
        </div>
    </div> --}}

    <div class="bg-white dark:bg-gray-900 border border-slate-100 dark:border-gray-800 rounded-xl overflow-hidden shadow-sm flex flex-col">
        <div class="p-6 border-b border-slate-100 dark:border-gray-800 flex flex-wrap justify-between items-center gap-4 bg-slate-50/50 dark:bg-gray-800/50">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white">Danh sách mã giảm giá</h3>

            <div class="flex gap-2">
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-lg">search</span>
                    <input class="pl-10 pr-4 py-2.5 bg-white dark:bg-gray-800 border border-slate-200 dark:border-gray-700 shadow-sm rounded-lg text-sm w-64 focus:ring-2 focus:ring-primary transition-all" placeholder="Tìm mã..." type="text" />
                </div>

                <button class="flex items-center gap-2 px-4 py-2.5 bg-white dark:bg-gray-800 border border-slate-200 dark:border-gray-700 shadow-sm rounded-lg text-sm font-bold text-slate-600 dark:text-gray-300 hover:bg-gray-50 transition-colors">
                    <span class="material-symbols-outlined text-sm">filter_list</span>
                    <span>Bộ lọc</span>
                </button>
            </div>
        </div>

        {{-- <div class="px-6 py-3 flex gap-4 border-b border-[#e6e3db] text-sm">
            <a href="{{ route('vouchers.index') }}" class="px-3 py-1 rounded-lg bg-black text-white"> Tất cả </a>
            <a href="{{ route('admin.vouchers.index', ['deleted' => 'trash']) }}" class="px-3 py-1 bg-red-500 text-white rounded-lg border hover:bg-red-700"> Đã xóa </a>
        </div> --}}

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-50 dark:bg-gray-800 border-b border-slate-200 dark:border-gray-700">
                    <tr class="text-slate-500 dark:text-gray-400 text-[11px] uppercase tracking-wider font-black">
                        <th class="px-6 py-4">Mã Code</th>
                        <th class="px-6 py-4">Loại giảm giá</th>
                        <th class="px-6 py-4">Trạng thái</th>
                        <th class="px-6 py-4">Sử dụng / Tổng</th>
                        <th class="px-6 py-4 text-right">Hành động</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-gray-800">
                    @foreach ($vouchers as $voucher)
                        <tr class="hover:bg-slate-50 dark:hover:bg-gray-800/50 transition-colors group">
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    @php
                                        $color = match ($voucher->voucher_status) {
                                            'Tạm dừng' => 'yellow',
                                            'Hết lượn dùng' => 'gray',
                                            'Đã Hết hạn' => 'red',
                                            default => 'green',
                                        };
                                    @endphp
                                    <span class="font-black text-slate-900 dark:text-white uppercase tracking-wide text-sm">{{ $voucher->code }}</span>
                                    <span class="text-xs font-semibold text-slate-500 mt-1">{{ $voucher->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if ($voucher->discount_type == 'fixed')
                                    <span class="px-2.5 py-1 bg-purple-50 text-purple-700 border border-purple-200 text-[11px] font-black uppercase tracking-wider rounded">Giảm
                                        {{ number_format($voucher->discount_value, 0, ',', '.') }}đ
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 bg-blue-50 text-blue-600 border border-blue-200 text-[11px] font-black uppercase tracking-wider rounded">Giảm
                                        {{ $voucher->discount_value }}%
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-1.5 px-2.5 py-1 rounded border border-{{ $color }}-200 bg-{{ $color }}-50 w-fit">
                                    <div class="size-1.5 rounded-full bg-{{ $color }}-500"></div>
                                    <span class="text-[11px] font-black uppercase tracking-wider text-{{ $color }}-600">{{ $voucher->voucher_status }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-1.5 w-40">
                                    <div class="flex justify-between text-[11px] font-bold text-slate-700 dark:text-slate-300">
                                        <span>{{ $voucher->used_count }} / {{ $voucher->usage_limit }}</span>
                                        <span>{{ $voucher->usage_percent }}%</span>
                                    </div>
                                    <div class="w-full bg-slate-100 dark:bg-gray-700 h-1.5 rounded-full overflow-hidden border border-slate-200 dark:border-gray-600">
                                        <div class="bg-primary h-full transition-all duration-500" style="width: {{ $voucher->usage_percent }}%"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <a href="{{ route('admin.vouchers.show', $voucher->id) }}">
                                        <button class="p-2 hover:bg-blue-50 rounded-lg text-slate-400 hover:text-blue-500 transition-colors" title="Xem">
                                            <span class="material-symbols-outlined text-lg">visibility</span>
                                        </button>
                                    </a>

                                    <a href="{{ route('admin.vouchers.edit', $voucher->id) }}">
                                        <button class="p-2 hover:bg-slate-100 rounded-lg text-slate-400 hover:text-slate-700 transition-colors" title="Sửa">
                                            <span class="material-symbols-outlined text-lg">edit</span>
                                        </button>
                                    </a>

                                    @if (!$voucher->deleted_at)
                                    <form action="{{ route('admin.vouchers.destroy', $voucher->id) }}" method="post" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button onclick="return confirm('Bạn có chắc muốn xóa?')" type="submit" class="p-2 hover:bg-red-50 rounded-lg text-slate-400 hover:text-red-500 transition-colors" title="Xóa">
                                            <span class="material-symbols-outlined text-lg">delete</span>
                                        </button>
                                    </form>
                                    @else
                                    <form action="{{ route('admin.vouchers.restore', $voucher->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button onclick="return confirm('Khôi phục voucher này?')" type="submit" class="p-2 hover:bg-green-50 rounded-lg text-slate-400 hover:text-green-500 transition-colors" title="Khôi phục">
                                            <span class="material-symbols-outlined text-lg">restore</span>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="p-4 border-t border-slate-100 dark:border-gray-800 flex justify-between items-center bg-slate-50/50 dark:bg-gray-800/50">
            @if ($vouchers->hasPages())
                <div class="flex gap-1.5 ml-auto">
                    {{-- Previous --}}
                    @if ($vouchers->onFirstPage())
                        <button class="size-8 flex items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-300 opacity-50 cursor-not-allowed" disabled>
                            <span class="material-symbols-outlined text-sm">chevron_left</span>
                        </button>
                    @else
                        <a href="{{ $vouchers->previousPageUrl() }}" class="size-8 flex items-center justify-center rounded-lg border border-gray-200 bg-white text-slate-500 hover:border-primary hover:text-primary shadow-sm transition-all">
                            <span class="material-symbols-outlined text-sm">chevron_left</span>
                        </a>
                    @endif

                    {{-- Pages --}}
                    @foreach ($vouchers->getUrlRange(1, $vouchers->lastPage()) as $page => $url)
                        <a href="{{ $url }}" class="size-8 flex items-center justify-center rounded-lg border font-bold text-xs transition-all shadow-sm {{ $page == $vouchers->currentPage() ? 'border-primary bg-primary text-slate-900' : 'border-gray-200 bg-white text-slate-500 hover:border-primary hover:text-primary' }}">
                            {{ $page }}
                        </a>
                    @endforeach

                    {{-- Next --}}
                    @if ($vouchers->hasMorePages())
                        <a href="{{ $vouchers->nextPageUrl() }}" class="size-8 flex items-center justify-center rounded-lg border border-gray-200 bg-white text-slate-500 hover:border-primary hover:text-primary shadow-sm transition-all">
                            <span class="material-symbols-outlined text-sm">chevron_right</span>
                        </a>
                    @else
                        <button class="size-8 flex items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-300 opacity-50 cursor-not-allowed" disabled>
                            <span class="material-symbols-outlined text-sm">chevron_right</span>
                        </button>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
@endsection