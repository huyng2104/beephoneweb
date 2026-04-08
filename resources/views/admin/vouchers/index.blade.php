@extends('admin.layouts.app')
@section('content')
    <!-- Header Section -->
    @include('popup_notify.index')
    <header class="bg-white dark:bg-gray-900 border-b border-[#e6e3db] px-8 py-6">
        <div class="flex flex-wrap justify-between items-end gap-4">
            <div class="flex flex-col gap-1">
                <h2 class="text-[#181611] dark:text-white text-3xl font-bold tracking-tight">Quản lý khuyến mãi
                </h2>
                <p class="text-[#8a8060] text-sm font-normal">Theo dõi và cấu hình các chương trình ưu đãi của
                    Bee Phone.</p>
            </div>
            <a href="{{ route('admin.vouchers.create') }}">
                <button
                    class="flex items-center gap-2 rounded-lg h-11 px-6 bg-primary text-[#181611] text-sm font-bold shadow-sm hover:scale-[1.02] active:scale-95 transition-all">
                    <span class="material-symbols-outlined">add_circle</span>
                    <span>Tạo mã mới</span>
                </button>
            </a>
        </div>
    </header>

    <div class="p-8 flex flex-col gap-8">
        <!-- Stats & Charts Section -->


        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 grid grid-cols-1 sm:grid-cols-3 gap-4">

                <div class="flex flex-col gap-2 rounded-xl p-6 bg-white dark:bg-gray-900 border border-[#e6e3db]">
                    <p class="text-[#8a8060] text-sm font-medium">Tổng mã đang chạy</p>
                    <p class="text-[#181611] dark:text-white text-3xl font-bold">{{ number_format($totalActive) }}</p>
                </div>

                <div class="flex flex-col gap-2 rounded-xl p-6 bg-white dark:bg-gray-900 border border-[#e6e3db]">
                    <p class="text-[#8a8060] text-sm font-medium">Lượt sử dụng (30 ngày)</p>
                    <p class="text-[#181611] dark:text-white text-3xl font-bold">{{ number_format($totalUsage30Days) }}</p>
                    <div class="flex items-center gap-1 text-xs font-bold px-2 py-0.5 rounded w-fit {{ $usageTrend >= 0 ? 'text-[#078812] bg-[#078812]/10' : 'text-[#e71408] bg-[#e71408]/10' }}">
                        <span class="material-symbols-outlined text-sm">{{ $usageTrend >= 0 ? 'trending_up' : 'trending_down' }}</span>
                        <span>{{ $usageTrend > 0 ? '+' : '' }}{{ number_format($usageTrend, 1) }}%</span>
                    </div>
                </div>

                <div class="flex flex-col gap-2 rounded-xl p-6 bg-white dark:bg-gray-900 border border-[#e6e3db]">
                    <p class="text-[#8a8060] text-sm font-medium">Tiết kiệm cho khách</p>
                    <p class="text-[#181611] dark:text-white text-3xl font-bold">
                        {{ $totalSaved >= 1000000 ? number_format($totalSaved / 1000000, 1) . 'M' : number_format($totalSaved, 0, ',', '.') . 'đ' }}
                    </p>
                    <div class="flex items-center gap-1 text-xs font-bold px-2 py-0.5 rounded w-fit {{ $savedTrend >= 0 ? 'text-[#078812] bg-[#078812]/10' : 'text-[#e71408] bg-[#e71408]/10' }}">
                        <span class="material-symbols-outlined text-sm">{{ $savedTrend >= 0 ? 'trending_up' : 'trending_down' }}</span>
                        <span>{{ $savedTrend > 0 ? '+' : '' }}{{ number_format($savedTrend, 1) }}%</span>
                    </div>
                </div>
            </div>

            <div class="flex flex-col gap-4 rounded-xl p-6 bg-white dark:bg-gray-900 border border-[#e6e3db] shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <div>
                        <h3 class="text-[#181611] dark:text-white text-base font-bold tracking-tight">Xu hướng sử dụng</h3>
                        <p class="text-[#8a8060] text-xs font-medium mt-1">Lượt dùng 7 ngày gần nhất</p>
                    </div>
                    <div class="p-2 bg-primary/10 rounded-lg">
                        <span class="material-symbols-outlined text-primary text-xl">bar_chart</span>
                    </div>
                </div>

                <div class="flex-1 flex items-end justify-between gap-2 pt-6 relative group z-0">
                    <!-- Background Grid Lines -->
                    <div class="absolute inset-x-0 top-0 bottom-6 flex flex-col justify-between pointer-events-none opacity-40 z-[-1]">
                        <div class="border-b border-dashed border-[#8a8060]/30 w-full"></div>
                        <div class="border-b border-dashed border-[#8a8060]/30 w-full"></div>
                        <div class="border-b border-dashed border-[#8a8060]/30 w-full"></div>
                    </div>

                    @foreach ($chartData as $data)
                        <div class="relative flex flex-col items-center flex-1 h-32 group/bar">
                            <!-- Custom Tooltip -->
                            <div class="absolute -top-10 bg-gray-800 dark:bg-gray-700 shadow-xl text-white text-[10px] font-bold px-2.5 py-1.5 rounded opacity-0 group-hover/bar:opacity-100 group-hover/bar:-translate-y-1 transition-all z-20 whitespace-nowrap pointer-events-none">
                                {{ $data['count'] }} lượt
                                <div class="absolute top-full left-1/2 -translate-x-1/2 border-4 border-transparent border-t-gray-800 dark:border-t-gray-700"></div>
                            </div>

                            <!-- Bar Track & Fill -->
                            <div class="w-full max-w-[28px] md:max-w-[32px] bg-[#f5f3f0] dark:bg-gray-800 rounded-t-md h-full flex items-end overflow-hidden cursor-pointer">
                                <div class="w-full bg-gradient-to-t from-primary/80 to-primary group-hover/bar:brightness-110 border-t border-white/20 transition-all duration-300"
                                    style="height: {{ $data['height'] }}%;">
                                </div>
                            </div>

                            <!-- Label -->
                            <span class="text-[10px] font-bold text-[#8a8060] mt-3 uppercase tracking-wider">{{ Str::limit($data['day_short'], 2, '') }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        <!-- Table Section -->
        <div class="bg-white dark:bg-gray-900 border border-[#e6e3db] rounded-xl overflow-hidden flex flex-col">
            <div class="p-6 border-b border-[#e6e3db] flex flex-wrap justify-between items-center gap-4">
                <h3 class="text-lg font-bold text-[#181611] dark:text-white mb-2 md:mb-0">Danh sách mã giảm giá</h3>
                
                <form action="{{ url()->current() }}" method="GET" class="flex flex-wrap items-center gap-3 w-full md:w-auto">

                    <div class="relative flex-grow min-w-[200px]">
                        <span
                            class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#8a8060] text-lg pointer-events-none">
                            search
                        </span>
                        <input type="text" name="search" value="{{ request('search') }}"
                            onchange="this.form.submit()"
                            class="w-full pl-10 pr-4 py-2 bg-background-light dark:bg-gray-800 border border-[#e6e3db] dark:border-gray-700 rounded-lg text-sm focus:ring-2 focus:ring-primary outline-none transition-shadow"
                            placeholder="Tìm mã voucher, tên..." />
                    </div>

                    <select name="status" onchange="this.form.submit()"
                        class="px-4 py-2 border rounded-lg text-sm focus:ring-primary outline-none cursor-pointer">
                        <option value="">Tất cả trạng thái</option>
                        <option value="paused" {{ request('status') == 'paused' ? 'selected' : '' }}>Tạm dừng</option>
                        <option value="out_of_usage" {{ request('status') == 'out_of_usage' ? 'selected' : '' }}>Hết lượt
                            dùng</option>
                        <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Đã hết hạn</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Đang hoạt động</option>
                    </select>

                    <select name="type" onchange="this.form.submit()"
                        class="px-4 py-2 border rounded-lg text-sm focus:ring-primary outline-none cursor-pointer">
                        <option value="">Tất cả phân loại</option>
                        <option value="percent" {{ request('type') == 'percent' ? 'selected' : '' }}>Giảm theo %</option>
                        <option value="fixed" {{ request('type') == 'fixed' ? 'selected' : '' }}>Giảm tiền mặt</option>
                    </select>

                    <select name="points" onchange="this.form.submit()"
                        class="px-4 py-2 border rounded-lg text-sm focus:ring-primary outline-none cursor-pointer">
                        <option value="">Tất cả điều kiện</option>
                        <option value="free" {{ request('points') == 'free' ? 'selected' : '' }}>Miễn phí</option>
                        <option value="points" {{ request('points') == 'points' ? 'selected' : '' }}>Đổi bằng điểm thưởng</option>
                    </select>

                    @if (request()->anyFilled(['search', 'status', 'type', 'points']))
                        <a href="{{ url()->current() }}"
                            class="flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-600 border border-gray-200 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors">
                            <span class="material-symbols-outlined text-sm">close</span>
                            <span>Xóa lọc</span>
                        </a>
                    @endif
                </form>
            </div>

            <!-- Tabs -->
            <div class="px-6 py-3 flex gap-4 border-b border-[#e6e3db] text-sm">

                {{-- <a href="{{ route('vouchers.index') }}" class="px-3 py-1 rounded-lg bg-black text-white">
                    Tất cả
                </a> --}}

                <a href="{{ route('admin.vouchers.index', ['deleted' => 'trash']) }}"
                    class="px-3 py-1 bg-red-500 text-white rounded-lg border hover:bg-red-700">
                    Đã xóa
                </a>

            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr
                            class="bg-background-light dark:bg-gray-800 text-[#8a8060] text-xs uppercase tracking-wider font-bold">
                            <th class="px-6 py-4">Mã Code</th>
                            <th class="px-6 py-4">Loại giảm giá</th>
                            <th class="px-6 py-4">Trạng thái</th>
                            <th class="px-6 py-4">Sử dụng / Tổng</th>
                            <th class="px-6 py-4 text-right">Hành động</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#e6e3db]">
                        @foreach ($vouchers as $voucher)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        @php
                                            $color = match ($voucher->voucher_status) {
                                                'Tạm dừng' => 'yellow',
                                                'Hết lượt dùng' => 'gray',
                                                'Đã Hết hạn' => 'red',
                                                default => 'green',
                                            };
                                        @endphp
                                        <span class="font-bold text-[#181611] dark:text-white">{{ $voucher->code }}</span>
                                        <span class="text-xs text-[#8a8060]">{{ $voucher->name }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    {{-- 1. Hiển thị loại giảm giá --}}
                                    @if ($voucher->discount_type == 'fixed')
                                        <span class="px-2 py-1 bg-purple-50 text-purple-600 text-xs font-bold rounded">
                                            Giảm {{ number_format($voucher->discount_value, 0, ',', '.') }}đ
                                        </span> {{-- Đã bổ sung thẻ đóng span bị thiếu ở đây --}}
                                    @else
                                        <span class="px-2 py-1 bg-blue-50 text-blue-600 text-xs font-bold rounded">
                                            Giảm {{ $voucher->discount_value }}%
                                        </span>
                                    @endif
                                    <br>
                                    {{-- 2. Hiển thị nhãn Quà tặng nếu cần đổi điểm --}}
                                    @if ($voucher->points_required > 0)
                                        {{-- Thêm class mt-2 và inline-block để nhãn rớt xuống dòng cho đẹp --}}
                                        <span
                                            class="inline-block mt-2 px-2 py-1 bg-orange-50 text-orange-600 text-xs font-bold rounded">
                                            Đổi thưởng
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <div class="size-2 rounded-full bg-{{ $color }}-500"></div>
                                        <span
                                            class="text-sm font-medium text-{{ $color }}-600">{{ $voucher->voucher_status }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col gap-1.5 w-40">

                                        <div class="flex justify-between text-xs font-bold">
                                            <span>{{ $voucher->used_count }} / {{ $voucher->usage_limit }}</span>
                                            <span>{{ $voucher->usage_percent }}%</span>
                                        </div>

                                        <div class="w-full bg-[#f5f3f0] h-1.5 rounded-full overflow-hidden">
                                            <div class="bg-primary h-full transition-all duration-500"
                                                style="width: {{ $voucher->usage_percent }}%">
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-2">

                                        <!-- Show -->
                                        <a href="{{ route('admin.vouchers.show', $voucher->id) }}">
                                            <button
                                                class="p-2 hover:bg-blue-50 rounded-lg text-[#8a8060] hover:text-blue-500 transition-colors"
                                                title="Xem">
                                                <span class="material-symbols-outlined text-lg">visibility</span>
                                            </button>
                                        </a>

                                        <!-- Edit -->
                                        <a href="{{ route('admin.vouchers.edit', $voucher->id) }}">
                                            <button
                                                class="p-2 hover:bg-gray-100 rounded-lg text-[#8a8060] hover:text-primary transition-colors"
                                                title="Sửa">
                                                <span class="material-symbols-outlined text-lg">edit</span>
                                            </button>
                                        </a>

                                        <!-- Delete -->
                                        <form action="{{ route('admin.vouchers.destroy', $voucher->id) }}"
                                            method="post">
                                            @csrf
                                            @method('DELETE')
                                            <button onclick="return confirm('Bạn có chắc muốn xóa?')"
                                                class="p-2 hover:bg-red-50 rounded-lg text-[#8a8060] hover:text-red-500 transition-colors"
                                                title="Xóa">
                                                <span class="material-symbols-outlined text-lg">delete</span>
                                            </button>
                                        </form>
                                        @if ($voucher->deleted_at)
                                            <!-- Restore -->
                                            <form action="{{ route('admin.vouchers.restore', $voucher->id) }}"
                                                method="POST">
                                                @csrf

                                                <button onclick="return confirm('Khôi phục voucher này?')"
                                                    class="p-2 hover:bg-green-50 rounded-lg text-[#8a8060] hover:text-green-500 transition-colors"
                                                    title="Khôi phục">
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
            <div class="p-4 border-t border-[#e6e3db] flex justify-between items-center bg-gray-50/50">
                @if ($vouchers->hasPages())
                    <div class="flex gap-1">

                        {{-- Previous --}}
                        @if ($vouchers->onFirstPage())
                            <button
                                class="size-8 flex items-center justify-center rounded border border-[#e6e3db] bg-white text-[#8a8060] opacity-50"
                                disabled>
                                <span class="material-symbols-outlined text-sm">chevron_left</span>
                            </button>
                        @else
                            <a href="{{ $vouchers->previousPageUrl() }}"
                                class="size-8 flex items-center justify-center rounded border border-[#e6e3db] bg-white text-[#8a8060] hover:bg-primary hover:text-[#181611] transition-all">
                                <span class="material-symbols-outlined text-sm">chevron_left</span>
                            </a>
                        @endif


                        {{-- Pages --}}
                        @foreach ($vouchers->getUrlRange(1, $vouchers->lastPage()) as $page => $url)
                            <a href="{{ $url }}"
                                class="size-8 flex items-center justify-center rounded border font-bold text-xs transition-all
                    {{ $page == $vouchers->currentPage()
                        ? 'border-primary bg-primary text-[#181611]'
                        : 'border-[#e6e3db] bg-white text-[#8a8060] hover:bg-primary hover:text-[#181611]' }}">
                                {{ $page }}
                            </a>
                        @endforeach


                        {{-- Next --}}
                        @if ($vouchers->hasMorePages())
                            <a href="{{ $vouchers->nextPageUrl() }}"
                                class="size-8 flex items-center justify-center rounded border border-[#e6e3db] bg-white text-[#8a8060] hover:bg-primary hover:text-[#181611] transition-all">
                                <span class="material-symbols-outlined text-sm">chevron_right</span>
                            </a>
                        @else
                            <button
                                class="size-8 flex items-center justify-center rounded border border-[#e6e3db] bg-white text-[#8a8060] opacity-50"
                                disabled>
                                <span class="material-symbols-outlined text-sm">chevron_right</span>
                            </button>
                        @endif

                    </div>
                @endif
            </div>
        </div>

    </div>
@endsection
