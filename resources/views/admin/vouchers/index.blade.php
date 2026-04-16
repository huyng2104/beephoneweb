@extends('admin.layouts.app')
@section('content')
    <main class="flex-1 flex flex-col overflow-hidden">
        @include('popup_notify.index')

        <div class="flex-1 overflow-y-auto p-8 space-y-6">
            <!-- Header -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-slate-900 dark:text-slate-100 tracking-tight">Quản lý khuyến mãi</h2>
                    <p class="text-slate-500 text-sm mt-1">Theo dõi và cấu hình các chương trình ưu đãi của Bee Phone.</p>
                </div>
                <a href="{{ route('admin.vouchers.create') }}">
                    <button
                        class="bg-primary hover:bg-primary/90 text-slate-900 font-bold px-5 py-2.5 rounded-xl shadow-sm shadow-primary/20 flex items-center gap-2 transition-all">
                        <span class="material-symbols-outlined">add_circle</span>
                        Tạo mã mới
                    </button>
                </a>
            </div>

            <!-- Stats Section -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-white dark:bg-slate-800 p-4 rounded-xl border border-slate-200 dark:border-slate-700">
                    <p class="text-slate-500 text-xs font-bold uppercase tracking-wider">Tổng mã đang chạy</p>
                    <p class="text-2xl font-bold mt-1 text-slate-900 dark:text-slate-100">{{ number_format($totalActive) }}</p>
                </div>

                <div class="bg-white dark:bg-slate-800 p-4 rounded-xl border border-slate-200 dark:border-slate-700">
                    <p class="text-slate-500 text-xs font-bold uppercase tracking-wider">Lượt dùng (30 ngày)</p>
                    <div class="flex items-center justify-between mt-1">
                        <p class="text-2xl font-bold text-slate-900 dark:text-slate-100">{{ number_format($totalUsage30Days) }}</p>
                        <div class="flex items-center gap-1 text-xs font-bold px-2 py-0.5 rounded w-fit {{ $usageTrend >= 0 ? 'text-green-600 bg-green-100 dark:bg-green-900/30 dark:text-green-400' : 'text-red-600 bg-red-100 dark:bg-red-900/30 dark:text-red-400' }}">
                            <span class="material-symbols-outlined text-sm">{{ $usageTrend >= 0 ? 'trending_up' : 'trending_down' }}</span>
                            <span>{{ $usageTrend > 0 ? '+' : '' }}{{ number_format($usageTrend, 1) }}%</span>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-slate-800 p-4 rounded-xl border border-slate-200 dark:border-slate-700">
                    <p class="text-slate-500 text-xs font-bold uppercase tracking-wider">Tiết kiệm cho khách</p>
                    <div class="flex items-center justify-between mt-1">
                        <p class="text-2xl font-bold text-slate-900 dark:text-slate-100">
                            {{ $totalSaved >= 1000000 ? number_format($totalSaved / 1000000, 1) . 'M' : number_format($totalSaved, 0, ',', '.') . 'đ' }}
                        </p>
                        <div class="flex items-center gap-1 text-xs font-bold px-2 py-0.5 rounded w-fit {{ $savedTrend >= 0 ? 'text-green-600 bg-green-100 dark:bg-green-900/30 dark:text-green-400' : 'text-red-600 bg-red-100 dark:bg-red-900/30 dark:text-red-400' }}">
                            <span class="material-symbols-outlined text-sm">{{ $savedTrend >= 0 ? 'trending_up' : 'trending_down' }}</span>
                            <span>{{ $savedTrend > 0 ? '+' : '' }}{{ number_format($savedTrend, 1) }}%</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters & Table -->
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                <!-- Filter Row -->
                <form action="{{ url()->current() }}" method="GET" id="filter-form">
                    <div class="p-4 border-b border-slate-100 dark:border-slate-700 flex flex-wrap gap-4 items-center">
                        
                        <div class="flex-1 min-w-[300px] relative">
                            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">search</span>
                            <input name="search" value="{{ request('search') }}" onchange="this.form.submit()"
                                class="w-full bg-slate-50 dark:bg-slate-900 border-slate-200 dark:border-slate-700 rounded-lg pl-10 focus:ring-primary focus:border-primary text-sm"
                                placeholder="Tìm mã voucher, tên..." type="text" />
                        </div>

                        <div class="flex gap-2 flex-wrap">
                            <select name="status" onchange="this.form.submit()"
                                class="bg-slate-50 dark:bg-slate-900 border-slate-200 dark:border-slate-700 rounded-lg text-sm font-medium focus:ring-primary focus:border-primary max-w-[150px]">
                                <option value="">Tất cả trạng thái</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Đang hoạt động</option>
                                <option value="paused" {{ request('status') == 'paused' ? 'selected' : '' }}>Tạm dừng</option>
                                <option value="out_of_usage" {{ request('status') == 'out_of_usage' ? 'selected' : '' }}>Hết lượt dùng</option>
                                <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Đã hết hạn</option>
                            </select>

                            <select name="type" onchange="this.form.submit()"
                                class="bg-slate-50 dark:bg-slate-900 border-slate-200 dark:border-slate-700 rounded-lg text-sm font-medium focus:ring-primary focus:border-primary max-w-[150px]">
                                <option value="">Tất cả phân loại</option>
                                <option value="percent" {{ request('type') == 'percent' ? 'selected' : '' }}>Giảm theo %</option>
                                <option value="fixed" {{ request('type') == 'fixed' ? 'selected' : '' }}>Giảm tiền mặt</option>
                            </select>

                            <select name="points" onchange="this.form.submit()"
                                class="bg-slate-50 dark:bg-slate-900 border-slate-200 dark:border-slate-700 rounded-lg text-sm font-medium focus:ring-primary focus:border-primary max-w-[150px]">
                                <option value="">Tất cả điều kiện</option>
                                <option value="free" {{ request('points') == 'free' ? 'selected' : '' }}>Miễn phí</option>
                                <option value="points" {{ request('points') == 'points' ? 'selected' : '' }}>Đổi bằng điểm thưởng</option>
                            </select>

                            @if (request()->anyFilled(['search', 'status', 'type', 'points']))
                                <a href="{{ url()->current() }}"
                                    class="bg-slate-100 dark:bg-slate-900 p-2 rounded-lg border border-slate-200 dark:border-slate-700 flex items-center justify-center text-slate-500 hover:text-red-500 transition-colors"
                                    title="Xóa tất cả bộ lọc">
                                    <span class="material-symbols-outlined">filter_list_off</span>
                                </a>
                            @endif

                        </div>
                        
                        @if (request('deleted') == 'trash')
                            <a href="{{ route('admin.vouchers.index') }}"
                                class="bg-slate-100 dark:bg-slate-900 text-slate-600 dark:text-slate-400 p-2 rounded-lg border border-slate-200 dark:border-slate-700 flex items-center justify-center hover:bg-slate-200 transition-colors"
                                title="Quay lại danh sách">
                                <span class="material-symbols-outlined">arrow_back</span>
                            </a>
                        @else
                            <a href="{{ route('admin.vouchers.index', ['deleted' => 'trash']) }}"
                                class="bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 p-2 rounded-lg border border-red-200 dark:border-red-800 flex items-center justify-center hover:bg-red-100 transition-colors"
                                title="Thùng rác (Đã xóa)">
                                <span class="material-symbols-outlined">delete</span>
                            </a>
                        @endif
                    </div>
                </form>

                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-slate-50 dark:bg-slate-900/50 text-slate-500 text-xs font-bold uppercase tracking-wider">
                            <tr>
                                <th class="px-6 py-4">Mã Code</th>
                                <th class="px-6 py-4">Loại giảm giá</th>
                                <th class="px-6 py-4">Trạng thái</th>
                                <th class="px-6 py-4">Sử dụng / Tổng</th>
                                <th class="px-6 py-4 text-right">Hành động</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                            @foreach ($vouchers as $voucher)
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col">
                                            @php
                                                $color = match ($voucher->voucher_status) {
                                                    'Tạm dừng' => 'yellow',
                                                    'Hết lượt dùng' => 'slate',
                                                    'Đã Hết hạn' => 'red',
                                                    default => 'green',
                                                };
                                            @endphp
                                            <span class="font-bold text-slate-900 dark:text-slate-100">{{ $voucher->code }}</span>
                                            <span class="text-xs text-slate-500">{{ $voucher->name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if ($voucher->discount_type == 'fixed')
                                            <span class="px-2.5 py-1 bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400 text-xs font-bold rounded-full">
                                                Giảm {{ number_format($voucher->discount_value, 0, ',', '.') }}đ
                                            </span>
                                        @else
                                            <span class="px-2.5 py-1 bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400 text-xs font-bold rounded-full">
                                                Giảm {{ $voucher->discount_value }}%
                                            </span>
                                        @endif
                                        <br>
                                        @if ($voucher->points_required > 0)
                                            <span class="inline-block mt-2 px-2.5 py-1 bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400 text-xs font-bold rounded-full">
                                                Đổi thưởng
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-1.5 text-{{ $color }}-600 dark:text-{{ $color }}-500">
                                            <span class="size-1.5 rounded-full bg-{{ $color }}-500"></span>
                                            <span class="text-sm font-medium">{{ $voucher->voucher_status }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col gap-1.5 w-40">
                                            <div class="flex justify-between text-xs font-bold text-slate-500">
                                                <span>{{ $voucher->used_count }} / {{ $voucher->usage_limit }}</span>
                                                <span>{{ $voucher->usage_percent }}%</span>
                                            </div>
                                            <div class="w-full bg-slate-100 dark:bg-slate-700 h-1.5 rounded-full overflow-hidden">
                                                <div class="bg-primary h-full transition-all duration-500" style="width: {{ $voucher->usage_percent }}%"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex justify-end gap-2">
                                            <a href="{{ route('admin.vouchers.show', $voucher->id) }}">
                                                <button class="p-2 text-slate-400 hover:text-blue-500 transition-colors" title="Xem">
                                                    <span class="material-symbols-outlined text-lg">visibility</span>
                                                </button>
                                            </a>

                                            <a href="{{ route('admin.vouchers.edit', $voucher->id) }}">
                                                <button class="p-2 text-slate-400 hover:text-primary transition-colors" title="Sửa">
                                                    <span class="material-symbols-outlined text-lg">edit</span>
                                                </button>
                                            </a>

                                            @if ($voucher->deleted_at === null)
                                            <form action="{{ route('admin.vouchers.destroy', $voucher->id) }}" method="post">
                                                @csrf
                                                @method('DELETE')
                                                <button onclick="return confirm('Bạn có chắc muốn xóa?')" class="p-2 text-slate-400 hover:text-red-500 transition-colors" title="Xóa">
                                                    <span class="material-symbols-outlined text-lg">delete</span>
                                                </button>
                                            </form>
                                            @endif

                                            @if ($voucher->deleted_at)
                                                <form action="{{ route('admin.vouchers.restore', $voucher->id) }}" method="POST">
                                                    @csrf
                                                    <button onclick="return confirm('Khôi phục voucher này?')" class="p-2 text-slate-400 hover:text-green-500 transition-colors" title="Khôi phục">
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
                
                <!-- Pagination -->
                <div class="m-5 flex gap-2">
                    @if ($vouchers->hasPages())
                        {{-- Previous --}}
                        @if ($vouchers->onFirstPage())
                            <button class="size-9 flex items-center justify-center rounded-lg border border-slate-200 text-slate-400 opacity-50" disabled>
                                <span class="material-symbols-outlined">chevron_left</span>
                            </button>
                        @else
                            <a href="{{ $vouchers->previousPageUrl() }}" class="size-9 flex items-center justify-center rounded-lg border border-slate-200 text-slate-400 hover:bg-slate-50">
                                <span class="material-symbols-outlined">chevron_left</span>
                            </a>
                        @endif

                        {{-- Pages --}}
                        @foreach ($vouchers->getUrlRange(1, $vouchers->lastPage()) as $page => $url)
                            <a href="{{ $url }}" class="size-9 flex items-center justify-center rounded-lg border {{ $page == $vouchers->currentPage() ? 'border-primary bg-primary text-slate-900' : 'border-slate-200 text-slate-600 hover:bg-slate-50' }} font-bold text-sm">
                                {{ $page }}
                            </a>
                        @endforeach

                        {{-- Next --}}
                        @if ($vouchers->hasMorePages())
                            <a href="{{ $vouchers->nextPageUrl() }}" class="size-9 flex items-center justify-center rounded-lg border border-slate-200 text-slate-400 hover:bg-slate-50">
                                <span class="material-symbols-outlined">chevron_right</span>
                            </a>
                        @else
                            <button class="size-9 flex items-center justify-center rounded-lg border border-slate-200 text-slate-400 opacity-50" disabled>
                                <span class="material-symbols-outlined">chevron_right</span>
                            </button>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </main>
@endsection
