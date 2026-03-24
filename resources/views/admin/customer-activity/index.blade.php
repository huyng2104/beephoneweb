@extends('admin.layouts.app')

@section('content')
    <div class="flex-1 overflow-y-auto p-8 space-y-8">
        {{-- Header & Filter --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-[#181611] dark:text-white">Thống kê hoạt động khách hàng</h1>
                <p class="text-sm text-[#8a8060] mt-1">Thói quen và thời gian hoạt động trong {{ $days }} ngày qua</p>
            </div>
            <form action="{{ route('admin.customer-activity.index') }}" method="get" class="flex gap-2">
                <select name="days" onchange="this.form.submit()"
                    class="bg-[#f5f3f0] dark:bg-[#3d3725] border border-[#e6e3db] dark:border-[#3d3725] text-sm font-medium rounded-lg px-3 py-2 text-[#181611] dark:text-white focus:ring-primary/30">
                    <option value="7" {{ $days == 7 ? 'selected' : '' }}>7 ngày</option>
                    <option value="14" {{ $days == 14 ? 'selected' : '' }}>14 ngày</option>
                    <option value="30" {{ $days == 30 ? 'selected' : '' }}>30 ngày</option>
                    <option value="90" {{ $days == 90 ? 'selected' : '' }}>90 ngày</option>
                </select>
            </form>
        </div>

        {{-- Quick Stats --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white dark:bg-[#2d281a] p-6 rounded-xl border border-[#e6e3db] dark:border-[#3d3725] shadow-sm">
                <div class="flex justify-between items-start mb-4">
                    <p class="text-[#8a8060] text-sm font-medium">Giờ đăng nhập cao điểm</p>
                    <span class="material-symbols-outlined text-primary">login</span>
                </div>
                <h3 class="text-2xl font-bold text-[#181611] dark:text-white">
                    {{ $peakLoginHour !== null ? sprintf('%02d:00', $peakLoginHour) : '—' }}
                </h3>
                <p class="text-[#8a8060] text-xs mt-1">Khung giờ khách đăng nhập nhiều nhất</p>
            </div>
            <div class="bg-white dark:bg-[#2d281a] p-6 rounded-xl border border-[#e6e3db] dark:border-[#3d3725] shadow-sm">
                <div class="flex justify-between items-start mb-4">
                    <p class="text-[#8a8060] text-sm font-medium">Giờ đặt hàng cao điểm</p>
                    <span class="material-symbols-outlined text-blue-500">shopping_cart</span>
                </div>
                <h3 class="text-2xl font-bold text-[#181611] dark:text-white">
                    {{ $peakOrderHour !== null ? sprintf('%02d:00', $peakOrderHour) : '—' }}
                </h3>
                <p class="text-[#8a8060] text-xs mt-1">Khung giờ khách đặt hàng nhiều nhất</p>
            </div>
            <div class="bg-white dark:bg-[#2d281a] p-6 rounded-xl border border-[#e6e3db] dark:border-[#3d3725] shadow-sm">
                <div class="flex justify-between items-start mb-4">
                    <p class="text-[#8a8060] text-sm font-medium">Đang hoạt động</p>
                    <span class="material-symbols-outlined text-green-500">wifi</span>
                </div>
                <h3 class="text-2xl font-bold text-[#181611] dark:text-white">{{ $activeNowCount }}</h3>
                <p class="text-[#8a8060] text-xs mt-1">Khách online (session database)</p>
            </div>
            <div class="bg-white dark:bg-[#2d281a] p-6 rounded-xl border border-[#e6e3db] dark:border-[#3d3725] shadow-sm">
                <div class="flex justify-between items-start mb-4">
                    <p class="text-[#8a8060] text-sm font-medium">Tổng lượt đăng nhập</p>
                    <span class="material-symbols-outlined text-purple-500">groups</span>
                </div>
                <h3 class="text-2xl font-bold text-[#181611] dark:text-white">
                    {{ array_sum($loginByHour) }}
                </h3>
                <p class="text-[#8a8060] text-xs mt-1">Trong {{ $days }} ngày qua</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            {{-- Biểu đồ: Đăng nhập theo giờ --}}
            <div class="bg-white dark:bg-[#2d281a] p-6 rounded-xl border border-[#e6e3db] dark:border-[#3d3725] shadow-sm">
                <h2 class="text-lg font-bold text-[#181611] dark:text-white mb-4">Đăng nhập theo giờ trong ngày</h2>
                <div class="flex items-end gap-1 h-48">
                    @php $maxLogin = max(array_merge($loginByHour, [1])); @endphp
                    @for($h = 0; $h < 24; $h++)
                        <div class="flex-1 flex flex-col items-center gap-1">
                            <div class="w-full bg-[#f5f3f0] dark:bg-[#3d3725] rounded-t h-32 flex items-end">
                                <div class="w-full bg-primary rounded-t transition-all"
                                    style="height: {{ (($loginByHour[$h] ?? 0) / $maxLogin) * 100 }}%"></div>
                            </div>
                            <span class="text-[10px] font-medium text-[#8a8060]">{{ $h }}</span>
                        </div>
                    @endfor
                </div>
                <p class="text-xs text-[#8a8060] mt-2">Giờ (0–23h)</p>
            </div>

            {{-- Biểu đồ: Đặt hàng theo giờ --}}
            <div class="bg-white dark:bg-[#2d281a] p-6 rounded-xl border border-[#e6e3db] dark:border-[#3d3725] shadow-sm">
                <h2 class="text-lg font-bold text-[#181611] dark:text-white mb-4">Đặt hàng theo giờ trong ngày</h2>
                <div class="flex items-end gap-1 h-48">
                    @php $maxOrder = max(array_merge($orderByHour, [1])); @endphp
                    @for($h = 0; $h < 24; $h++)
                        <div class="flex-1 flex flex-col items-center gap-1">
                            <div class="w-full bg-[#f5f3f0] dark:bg-[#3d3725] rounded-t h-32 flex items-end">
                                <div class="w-full bg-blue-500 rounded-t transition-all"
                                    style="height: {{ (($orderByHour[$h] ?? 0) / $maxOrder) * 100 }}%"></div>
                            </div>
                            <span class="text-[10px] font-medium text-[#8a8060]">{{ $h }}</span>
                        </div>
                    @endfor
                </div>
                <p class="text-xs text-[#8a8060] mt-2">Giờ (0–23h)</p>
            </div>

            {{-- Biểu đồ: Đăng nhập theo ngày trong tuần --}}
            <div class="bg-white dark:bg-[#2d281a] p-6 rounded-xl border border-[#e6e3db] dark:border-[#3d3725] shadow-sm">
                <h2 class="text-lg font-bold text-[#181611] dark:text-white mb-4">Đăng nhập theo ngày trong tuần</h2>
                <div class="space-y-3">
                    @php $maxDowLogin = max(array_merge($loginByDayOfWeek, [1])); @endphp
                    @foreach($dayLabels as $dow => $label)
                        <div class="flex items-center gap-3">
                            <span class="text-sm w-20 text-[#5e5a4d] dark:text-gray-400">{{ $label }}</span>
                            <div class="flex-1 bg-[#f5f3f0] dark:bg-[#3d3725] h-6 rounded-full overflow-hidden">
                                <div class="bg-primary h-full rounded-full transition-all"
                                    style="width: {{ (($loginByDayOfWeek[$dow] ?? 0) / $maxDowLogin) * 100 }}%"></div>
                            </div>
                            <span class="text-sm font-bold text-[#181611] dark:text-white w-8">{{ $loginByDayOfWeek[$dow] ?? 0 }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Biểu đồ: Đặt hàng theo ngày trong tuần --}}
            <div class="bg-white dark:bg-[#2d281a] p-6 rounded-xl border border-[#e6e3db] dark:border-[#3d3725] shadow-sm">
                <h2 class="text-lg font-bold text-[#181611] dark:text-white mb-4">Đặt hàng theo ngày trong tuần</h2>
                <div class="space-y-3">
                    @php $maxDowOrder = max(array_merge($orderByDayOfWeek, [1])); @endphp
                    @foreach($dayLabels as $dow => $label)
                        <div class="flex items-center gap-3">
                            <span class="text-sm w-20 text-[#5e5a4d] dark:text-gray-400">{{ $label }}</span>
                            <div class="flex-1 bg-[#f5f3f0] dark:bg-[#3d3725] h-6 rounded-full overflow-hidden">
                                <div class="bg-blue-500 h-full rounded-full transition-all"
                                    style="width: {{ (($orderByDayOfWeek[$dow] ?? 0) / $maxDowOrder) * 100 }}%"></div>
                            </div>
                            <span class="text-sm font-bold text-[#181611] dark:text-white w-8">{{ $orderByDayOfWeek[$dow] ?? 0 }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Bảng: Top khách hàng --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <div class="bg-white dark:bg-[#2d281a] rounded-xl border border-[#e6e3db] dark:border-[#3d3725] shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-[#e6e3db] dark:border-[#3d3725]">
                    <h2 class="text-lg font-bold text-[#181611] dark:text-white">Top 10 khách đăng nhập nhiều nhất</h2>
                    <p class="text-sm text-[#8a8060] mt-1">Số lần đăng nhập trong {{ $days }} ngày</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-[#fcfcfc] dark:bg-[#342f1f] border-b border-[#e6e3db] dark:border-[#3d3725]">
                            <tr>
                                <th class="px-6 py-3 text-xs font-bold text-[#8a8060] uppercase">#</th>
                                <th class="px-6 py-3 text-xs font-bold text-[#8a8060] uppercase">Khách hàng</th>
                                <th class="px-6 py-3 text-xs font-bold text-[#8a8060] uppercase text-right">Lượt đăng nhập</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#e6e3db] dark:divide-[#3d3725]">
                            @forelse($topActiveByLogin as $i => $item)
                                <tr class="hover:bg-[#fcfcfc] dark:hover:bg-white/5">
                                    <td class="px-6 py-4 text-sm font-bold text-[#181611] dark:text-white">{{ $i + 1 }}</td>
                                    <td class="px-6 py-4">
                                        <p class="text-sm font-medium text-[#181611] dark:text-white">{{ $item->user?->name ?? 'N/A' }}</p>
                                        <p class="text-xs text-[#8a8060]">{{ $item->user?->email ?? '' }}</p>
                                    </td>
                                    <td class="px-6 py-4 text-sm font-bold text-right text-primary">{{ $item->login_count }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="px-6 py-8 text-center text-[#8a8060]">Chưa có dữ liệu</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white dark:bg-[#2d281a] rounded-xl border border-[#e6e3db] dark:border-[#3d3725] shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-[#e6e3db] dark:border-[#3d3725]">
                    <h2 class="text-lg font-bold text-[#181611] dark:text-white">Top 10 khách mua hàng nhiều nhất</h2>
                    <p class="text-sm text-[#8a8060] mt-1">Số đơn & tổng chi tiêu trong {{ $days }} ngày</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-[#fcfcfc] dark:bg-[#342f1f] border-b border-[#e6e3db] dark:border-[#3d3725]">
                            <tr>
                                <th class="px-6 py-3 text-xs font-bold text-[#8a8060] uppercase">#</th>
                                <th class="px-6 py-3 text-xs font-bold text-[#8a8060] uppercase">Khách hàng</th>
                                <th class="px-6 py-3 text-xs font-bold text-[#8a8060] uppercase text-right">Đơn hàng</th>
                                <th class="px-6 py-3 text-xs font-bold text-[#8a8060] uppercase text-right">Tổng chi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#e6e3db] dark:divide-[#3d3725]">
                            @forelse($topActiveByOrder as $i => $item)
                                <tr class="hover:bg-[#fcfcfc] dark:hover:bg-white/5">
                                    <td class="px-6 py-4 text-sm font-bold text-[#181611] dark:text-white">{{ $i + 1 }}</td>
                                    <td class="px-6 py-4">
                                        <p class="text-sm font-medium text-[#181611] dark:text-white">{{ $item->user?->name ?? 'N/A' }}</p>
                                        <p class="text-xs text-[#8a8060]">{{ $item->user?->email ?? '' }}</p>
                                    </td>
                                    <td class="px-6 py-4 text-sm font-bold text-right">{{ $item->order_count }}</td>
                                    <td class="px-6 py-4 text-sm font-bold text-right text-primary">{{ number_format($item->total_spent ?? 0) }} ₫</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="px-6 py-8 text-center text-[#8a8060]">Chưa có dữ liệu</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="bg-[#fcfcfc] dark:bg-[#342f1f] p-4 rounded-xl border border-[#e6e3db] dark:border-[#3d3725]">
            <p class="text-sm text-[#8a8060]">
                <span class="material-symbols-outlined align-middle text-lg">info</span>
                Dữ liệu thống kê từ: <strong>ActivityLog</strong> (đăng nhập/đăng xuất), <strong>Order</strong> (đơn hàng), và <strong>sessions</strong> (nếu dùng database driver).
            </p>
        </div>
    </div>
@endsection
