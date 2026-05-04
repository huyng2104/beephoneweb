@extends('admin.layouts.app')

@push('css')
<style>
.stat-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
    border-radius: 1rem;
    padding: 1.5rem;
    transition: transform 0.2s, box-shadow 0.2s;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}
.dark .stat-card {
    background: #1e293b;
    border-color: #334155;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.2);
}
.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -2px rgba(0, 0, 0, 0.025);
}
.dark .stat-card:hover {
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.3);
}
.icon-box {
    width: 3rem;
    height: 3rem;
    border-radius: 0.75rem;
    display: flex;
    align-items: center;
    justify-content: center;
}
.data-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    border-radius: 1.25rem;
    overflow: hidden;
}
.dark .data-card {
    background: #1e293b;
    border-color: #334155;
}
.table-hover tr:hover td {
    background: #f8fafc;
}
.dark .table-hover tr:hover td {
    background: #0f172a;
}
</style>
@endpush

@section('content')
<div class="flex-1 overflow-y-auto p-4 sm:p-8 bg-slate-50 dark:bg-[#0f172a] min-h-screen">
    <!-- Header & Filter -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">Thống kê Phân tích Doanh Thu</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Trang quản trị tài chính tổng quan ({{ $periodLabel }})</p>
        </div>
        
        <form id="filterForm" method="GET" action="{{ route('admin.dashboard') }}" class="flex flex-wrap items-center gap-3 bg-white dark:bg-slate-800 p-2 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm">
            <select name="period" id="periodSelect" onchange="toggleCustomDate(); document.getElementById('filterForm').submit()"
                class="bg-slate-100 dark:bg-slate-700 border-none text-sm font-semibold rounded-lg px-4 py-2.5 text-slate-700 dark:text-white focus:ring-2 focus:ring-primary/50 cursor-pointer outline-none">
                <option value="today" {{ $period == 'today' ? 'selected' : '' }}>Hôm nay</option>
                <option value="yesterday" {{ $period == 'yesterday' ? 'selected' : '' }}>Hôm qua</option>
                <option value="7days" {{ $period == '7days' ? 'selected' : '' }}>7 ngày qua</option>
                <option value="30days" {{ $period == '30days' ? 'selected' : '' }}>30 ngày qua</option>
                <option value="this_month" {{ $period == 'this_month' ? 'selected' : '' }}>Tháng này</option>
                <option value="last_month" {{ $period == 'last_month' ? 'selected' : '' }}>Tháng trước</option>
                <option value="custom" {{ $period == 'custom' ? 'selected' : '' }}>Tùy chỉnh</option>
            </select>

            <div id="customDateRange" class="flex items-center gap-2 {{ $period == 'custom' ? '' : 'hidden' }}">
                <input type="date" name="start_date" value="{{ $customStartDate }}" class="bg-slate-100 dark:bg-slate-700 border-none text-sm rounded-lg px-3 py-2.5 text-slate-700 dark:text-white focus:ring-2 focus:ring-primary/50 outline-none">
                <span class="text-slate-400 font-medium">-</span>
                <input type="date" name="end_date" value="{{ $customEndDate }}" class="bg-slate-100 dark:bg-slate-700 border-none text-sm rounded-lg px-3 py-2.5 text-slate-700 dark:text-white focus:ring-2 focus:ring-primary/50 outline-none">
                <button type="submit" class="bg-primary hover:bg-primary/90 text-white p-2.5 rounded-lg transition-colors flex items-center justify-center">
                    <span class="material-symbols-outlined text-[20px]">search</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Tổng Doanh Thu -->
        <div class="stat-card">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <p class="text-slate-500 dark:text-slate-400 text-xs font-bold uppercase tracking-wider mb-1">Tổng doanh thu</p>
                    <h3 class="text-2xl font-black text-slate-900 dark:text-white">{{ number_format($totalRevenue, 0, ',', '.') }} ₫</h3>
                </div>
                <div class="icon-box bg-emerald-100 dark:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400">
                    <span class="material-symbols-outlined text-2xl">account_balance_wallet</span>
                </div>
            </div>
            <div class="flex items-center gap-2 mt-2">
                <span class="{{ $revenueGrowth >= 0 ? 'text-emerald-600 bg-emerald-100 dark:bg-emerald-500/20 dark:text-emerald-400' : 'text-rose-600 bg-rose-100 dark:bg-rose-500/20 dark:text-rose-400' }} text-xs font-bold flex items-center px-2 py-1 rounded-md">
                    {{ $revenueGrowth > 0 ? '↑' : ($revenueGrowth < 0 ? '↓' : '-') }} {{ number_format(abs($revenueGrowth), 1) }}%
                </span>
                <span class="text-slate-400 text-xs font-medium">so với kỳ trước</span>
            </div>
        </div>
        
        <!-- Lượng Đơn -->
        <div class="stat-card">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <p class="text-slate-500 dark:text-slate-400 text-xs font-bold uppercase tracking-wider mb-1">Tổng Lượng Đơn</p>
                    <h3 class="text-2xl font-black text-slate-900 dark:text-white">{{ number_format($totalOrders) }}</h3>
                </div>
                <div class="icon-box bg-blue-100 dark:bg-blue-500/20 text-blue-600 dark:text-blue-400">
                    <span class="material-symbols-outlined text-2xl">shopping_bag</span>
                </div>
            </div>
            <div class="flex items-center gap-2 mt-2">
                <span class="{{ $ordersGrowth >= 0 ? 'text-emerald-600 bg-emerald-100 dark:bg-emerald-500/20 dark:text-emerald-400' : 'text-rose-600 bg-rose-100 dark:bg-rose-500/20 dark:text-rose-400' }} text-xs font-bold flex items-center px-2 py-1 rounded-md">
                    {{ $ordersGrowth > 0 ? '↑' : ($ordersGrowth < 0 ? '↓' : '-') }} {{ number_format(abs($ordersGrowth), 1) }}%
                </span>
                <span class="text-slate-400 text-xs font-medium">so với kỳ trước</span>
            </div>
        </div>
        
        <!-- AOV -->
        <div class="stat-card">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <p class="text-slate-500 dark:text-slate-400 text-xs font-bold uppercase tracking-wider mb-1">Trung bình Đơn (AOV)</p>
                    <h3 class="text-2xl font-black text-indigo-600 dark:text-indigo-400">{{ number_format($avgOrderValue, 0, ',', '.') }} ₫</h3>
                </div>
                <div class="icon-box bg-indigo-100 dark:bg-indigo-500/20 text-indigo-600 dark:text-indigo-400">
                    <span class="material-symbols-outlined text-2xl">receipt_long</span>
                </div>
            </div>
            <div class="mt-2">
                <div class="w-full bg-slate-100 dark:bg-slate-700 rounded-full h-1.5 mb-1">
                    <div class="bg-indigo-500 h-1.5 rounded-full" style="width: 100%"></div>
                </div>
                <span class="text-slate-400 text-xs font-medium">Chỉ số chất lượng tệp khách</span>
            </div>
        </div>

        <!-- Tổng Chiết Khấu -->
        <div class="stat-card">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <p class="text-slate-500 dark:text-slate-400 text-xs font-bold uppercase tracking-wider mb-1">Tổng Khuyến Mãi Cho Đi</p>
                    <h3 class="text-2xl font-black text-rose-600 dark:text-rose-400">{{ number_format($totalDiscount, 0, ',', '.') }} ₫</h3>
                </div>
                <div class="icon-box bg-rose-100 dark:bg-rose-500/20 text-rose-600 dark:text-rose-400">
                    <span class="material-symbols-outlined text-2xl">local_offer</span>
                </div>
            </div>
            <div class="flex items-center gap-2 mt-2">
                @php $discountRatio = $totalRevenue > 0 ? ($totalDiscount / $totalRevenue) * 100 : 0; @endphp
                <span class="text-slate-600 bg-slate-100 dark:bg-slate-700 dark:text-slate-300 text-xs font-bold flex items-center px-2 py-1 rounded-md">
                    Chiếm {{ number_format($discountRatio, 1) }}% doanh thu
                </span>
            </div>
        </div>
    </div>

    <!-- Charts Area (Mixed Chart & Payment Doughnut) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        
        <!-- Mixed Chart (Mở rộng 2 cột) -->
        <div class="lg:col-span-2 data-card p-6">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white">Dòng Tiền & Sản Lượng Từng Ngày</h2>
                    <p class="text-xs text-slate-500 mt-1">Xu hướng tăng trưởng qua biểu đồ Line/Bar kết hợp</p>
                </div>
                <div class="flex gap-4">
                    <div class="flex items-center gap-1.5"><div class="w-3 h-3 rounded bg-emerald-500"></div><span class="text-xs font-bold text-slate-600 dark:text-slate-300">Doanh thu</span></div>
                    <div class="flex items-center gap-1.5"><div class="w-3 h-3 rounded bg-blue-500"></div><span class="text-xs font-bold text-slate-600 dark:text-slate-300">Lượng đơn</span></div>
                </div>
            </div>
            <div class="w-full h-[320px] relative">
                <canvas id="mixedChart"></canvas>
            </div>
        </div>

        <!-- Doughnut Chart (Cổng thanh toán) -->
        <div class="data-card p-6 flex flex-col">
            <div class="mb-4">
                <h2 class="text-lg font-bold text-slate-900 dark:text-white">Tỷ Trọng Thanh Toán</h2>
                <p class="text-xs text-slate-500 mt-1">Hành vi chuyển khoản vs nhận hàng</p>
            </div>
            <div class="flex-1 relative flex items-center justify-center min-h-[220px]">
                <canvas id="paymentChart"></canvas>
            </div>
        </div>

    </div>

    <!-- Tables Area -->
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        
        <!-- Top Products -->
        <div class="data-card flex flex-col">
            <div class="p-5 border-b border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/50 flex justify-between items-center">
                <div class="flex items-center gap-2 text-indigo-600 dark:text-indigo-400">
                    <span class="material-symbols-outlined">stars</span>
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white">Sản Phẩm "Gánh" Doanh Thu</h2>
                </div>
            </div>
            <div class="flex-1 overflow-x-auto p-0">
                <table class="w-full text-left border-collapse table-hover">
                    <thead>
                        <tr class="border-b border-slate-200 dark:border-slate-700">
                            <th class="px-5 py-3 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase">Sản phẩm</th>
                            <th class="px-5 py-3 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase text-center">Đã Bán</th>
                            <th class="px-5 py-3 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase text-right">Thu Về</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/50">
                        @forelse($topProducts as $prod)
                            <tr class="transition-colors">
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ $prod->thumbnail ? asset('storage/'.$prod->thumbnail) : asset('theme/admin/assets/images/default-thumbnail.png') }}" class="w-12 h-12 rounded bg-slate-100 object-cover shadow-sm">
                                        <p class="text-sm font-bold text-slate-900 dark:text-white line-clamp-2" title="{{ $prod->name }}">{{ Str::limit($prod->name, 40) }}</p>
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <span class="px-2.5 py-1 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-md text-xs font-bold">{{ $prod->total_sold }}</span>
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <p class="text-sm font-black text-emerald-600 dark:text-emerald-400">{{ number_format($prod->total_revenue, 0, ',', '.') }} ₫</p>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="p-6 text-center text-slate-500 text-sm">Chưa có dữ liệu sản phẩm trong kỳ.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="data-card flex flex-col">
            <div class="p-5 border-b border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/50 flex justify-between items-center">
                <div class="flex items-center gap-2 text-blue-600 dark:text-blue-400">
                    <span class="material-symbols-outlined">schedule</span>
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white">Giao Dịch Gần Đây Nhất</h2>
                </div>
                <a href="{{ route('admin.orders.index') }}" class="text-xs font-bold text-primary hover:underline">Xem tất cả</a>
            </div>
            <div class="flex-1 overflow-x-auto p-0">
                <table class="w-full text-left border-collapse table-hover">
                    <thead>
                        <tr class="border-b border-slate-200 dark:border-slate-700">
                            <th class="px-5 py-3 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase">Mã Đơn</th>
                            <th class="px-5 py-3 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase">Khách & Tiền</th>
                            <th class="px-5 py-3 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase text-right">Thanh Toán</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/50">
                        @forelse($recentOrders as $order)
                            <tr class="transition-colors">
                                <td class="px-5 py-4">
                                    <a href="{{ route('admin.orders.show', $order->id) }}" class="text-sm font-bold text-primary hover:underline">#{{ $order->order_code }}</a>
                                    <p class="text-[11px] text-slate-500 mt-0.5">{{ $order->created_at->diffForHumans() }}</p>
                                </td>
                                <td class="px-5 py-4">
                                    <p class="text-sm font-bold text-slate-900 dark:text-white">{{ $order->customer_name ?? ($order->user->name ?? 'Khách lẻ') }}</p>
                                    <p class="text-xs font-black text-amber-600 mt-0.5">{{ number_format($order->total_amount, 0, ',', '.') }} ₫</p>
                                </td>
                                <td class="px-5 py-4 text-right">
                                    @if($order->payment_status == 'paid')
                                        <span class="inline-block px-2 py-1 bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-400 text-[10px] font-bold rounded uppercase">Đã Thanh Toán</span>
                                    @else
                                        <span class="inline-block px-2 py-1 bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-400 text-[10px] font-bold rounded uppercase">Chưa Thanh Toán</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="p-6 text-center text-slate-500 text-sm">Chưa có giao dịch.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    function toggleCustomDate() {
        const period = document.getElementById('periodSelect').value;
        const customDiv = document.getElementById('customDateRange');
        if (period === 'custom') customDiv.classList.remove('hidden');
        else customDiv.classList.add('hidden');
    }

    document.addEventListener('DOMContentLoaded', function() {
        // --- MIXED CHART (Doanh thu & Đơn hàng) ---
        const mixCtx = document.getElementById('mixedChart');
        if (mixCtx) {
            const gradientLine = mixCtx.getContext('2d').createLinearGradient(0, 0, 0, 320);
            gradientLine.addColorStop(0, 'rgba(16, 185, 129, 0.4)');
            gradientLine.addColorStop(1, 'rgba(16, 185, 129, 0)');

            const gradientBar = mixCtx.getContext('2d').createLinearGradient(0, 0, 0, 320);
            gradientBar.addColorStop(0, 'rgba(59, 130, 246, 0.7)');
            gradientBar.addColorStop(1, 'rgba(59, 130, 246, 0.2)');

            new Chart(mixCtx, {
                type: 'line',
                data: {
                    labels: @json($chartDates),
                    datasets: [
                        {
                            type: 'line', label: 'Doanh Thu', data: @json($chartRevenues),
                            borderColor: '#10b981', backgroundColor: gradientLine,
                            borderWidth: 3, tension: 0.4, fill: true,
                            yAxisID: 'yRevenue', pointRadius: 2, pointHoverRadius: 5, order: 1
                        },
                        {
                            type: 'bar', label: 'Đơn Hàng', data: @json($chartOrders),
                            backgroundColor: gradientBar, borderColor: '#3b82f6',
                            borderWidth: 1, borderRadius: 4, barPercentage: 0.6,
                            yAxisID: 'yOrders', order: 2
                        }
                    ]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    plugins: { legend: { display: false }, tooltip: { cornerRadius: 8, padding: 12 } },
                    scales: {
                        x: { grid: { display: false }, ticks: { font: { size: 10 } } },
                        yRevenue: {
                            type: 'linear', position: 'left',
                            grid: { color: 'rgba(148, 163, 184, 0.1)', drawBorder: false },
                            ticks: {
                                color: '#10b981', font: { size: 11 },
                                callback: function(v) { return v >= 1000000 ? (v / 1000000) + 'Tr' : v; }
                            }
                        },
                        yOrders: {
                            type: 'linear', position: 'right', grid: { display: false },
                            ticks: { color: '#3b82f6', font: { size: 11 }, stepSize: 1 }
                        }
                    }
                }
            });
        }

        // --- PAYMENT DOUGHNUT CHART ---
        const payCtx = document.getElementById('paymentChart');
        if (payCtx && @json(count($paymentData)) > 0) {
            new Chart(payCtx, {
                type: 'doughnut',
                data: {
                    labels: @json($paymentLabels),
                    datasets: [{
                        data: @json($paymentData),
                        backgroundColor: ['#3b82f6', '#ec4899', '#f59e0b', '#8b5cf6', '#10b981'],
                        borderWidth: 0, hoverOffset: 8
                    }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false, cutout: '70%',
                    plugins: {
                        legend: { position: 'bottom', labels: { usePointStyle: true, boxWidth: 8, font: { size: 11 } } },
                        tooltip: { callbacks: { label: function(ctx) { return ' ' + new Intl.NumberFormat('vi-VN').format(ctx.raw) + ' ₫'; } } }
                    }
                }
            });
        } else if(payCtx) {
            // Draw empty text if no data
            const ctx = payCtx.getContext("2d");
            ctx.font = "12px Arial";
            ctx.fillStyle = "#94a3b8";
            ctx.textAlign = "center";
            ctx.fillText("Không có giao dịch", payCtx.width/2, payCtx.height/2);
        }
    });
</script>
@endpush
