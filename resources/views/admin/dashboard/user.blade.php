@extends('admin.layouts.app')

@push('css')
<style>
.stat-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    border-radius: 1rem;
    padding: 1.5rem;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}
.dark .stat-card { background: #1e293b; border-color: #334155; }
.data-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    border-radius: 1.25rem;
    overflow: hidden;
}
.dark .data-card { background: #1e293b; border-color: #334155; }

.table-hover tr:hover td { background: #f8fafc; }
.dark .table-hover tr:hover td { background: #0f172a; }

.rank-badge {
    width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center;
    font-weight: 900; font-size: 13px; margin: 0 auto;
}
.rank-1 { background: linear-gradient(135deg, #fbbf24, #f59e0b); color: white; box-shadow: 0 0 10px rgba(245, 158, 11, 0.4); border: 2px solid #fef3c7; }
.rank-2 { background: linear-gradient(135deg, #94a3b8, #64748b); color: white; border: 2px solid #f1f5f9; }
.rank-3 { background: linear-gradient(135deg, #d97706, #b45309); color: white; border: 2px solid #fef3c7; }
.rank-other { background: #f1f5f9; color: #64748b; font-weight: 700; }
.dark .rank-other { background: #334155; color: #94a3b8; }

.avatar-placeholder {
    width: 2.5rem; height: 2.5rem; border-radius: 0.5rem;
    display: flex; align-items: center; justify-content: center;
    font-weight: 800; color: white; font-size: 1rem;
}
</style>
@endpush

@section('content')
<div class="flex-1 overflow-y-auto p-4 sm:p-8 bg-slate-50 dark:bg-[#0f172a] min-h-screen">
    <!-- Header & Filter -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">Phân Tích Khách Hàng (CRM)</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Chỉ số tăng trưởng và hành vi mua sắm của User</p>
        </div>
        
        <form id="filterForm" method="GET" action="{{ route('admin.dashboard.users') }}" class="flex flex-wrap items-center gap-3 bg-white dark:bg-slate-800 p-2 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm">
            <select name="period" id="periodSelect" onchange="toggleCustomDate(); document.getElementById('filterForm').submit()"
                class="bg-slate-100 dark:bg-slate-700 border-none text-sm font-semibold rounded-lg px-4 py-2.5 text-slate-700 dark:text-white focus:ring-2 focus:ring-primary/50 cursor-pointer outline-none">
                <option value="today" {{ $period == 'today' ? 'selected' : '' }}>Hôm nay</option>
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

    <!-- 4 CRM Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="stat-card">
            <p class="text-slate-500 dark:text-slate-400 text-xs font-bold uppercase mb-2 flex items-center gap-2"><span class="material-symbols-outlined text-[18px]">group</span> Tổng Khách Hàng</p>
            <h3 class="text-3xl font-black text-slate-900 dark:text-white">{{ number_format($totalUsers) }}</h3>
            <div class="mt-3 bg-slate-100 dark:bg-slate-700/50 p-2 rounded-lg text-xs text-slate-500 font-medium text-center">Toàn bộ tệp User hệ thống</div>
        </div>
        
        <div class="stat-card border-t-4 border-t-indigo-500">
            <p class="text-slate-500 dark:text-slate-400 text-xs font-bold uppercase mb-2 flex items-center gap-2"><span class="material-symbols-outlined text-[18px]">person_add</span> User Mới Tạo</p>
            <h3 class="text-3xl font-black text-indigo-600 dark:text-indigo-400">{{ number_format($newUsers) }}</h3>
            <div class="mt-3 flex items-center gap-2 text-xs font-bold">
                <span class="{{ $newUsersGrowth >= 0 ? 'text-emerald-600 bg-emerald-100 dark:bg-emerald-500/20' : 'text-rose-600 bg-rose-100 dark:bg-rose-500/20' }} px-2 py-1 rounded">
                    {{ $newUsersGrowth > 0 ? '↑' : ($newUsersGrowth < 0 ? '↓' : '-') }} {{ number_format(abs($newUsersGrowth), 1) }}%
                </span>
                <span class="text-slate-400">so với kỳ trước</span>
            </div>
        </div>
        
        <div class="stat-card border-t-4 border-t-emerald-500">
            <p class="text-slate-500 dark:text-slate-400 text-xs font-bold uppercase mb-2 flex items-center gap-2"><span class="material-symbols-outlined text-[18px]">shopping_cart_checkout</span> Tỷ Lệ Chuyển Đổi Mua Hàng</p>
            <h3 class="text-3xl font-black text-emerald-600 dark:text-emerald-400">{{ $buyerRate }}%</h3>
            <div class="mt-3 bg-slate-100 dark:bg-slate-700/50 p-2 rounded-lg text-xs text-slate-500 font-medium text-center">Tỷ lệ User có phát sinh đơn</div>
        </div>

        <div class="stat-card border-t-4 border-t-rose-500">
            <p class="text-slate-500 dark:text-slate-400 text-xs font-bold uppercase mb-2 flex items-center gap-2"><span class="material-symbols-outlined text-[18px]">favorite</span> Tỷ Lệ Khách Cũ Quay Lại</p>
            <h3 class="text-3xl font-black text-rose-600 dark:text-rose-400">{{ $retentionRate }}%</h3>
            <div class="mt-3 bg-slate-100 dark:bg-slate-700/50 p-2 rounded-lg text-xs text-slate-500 font-medium text-center">User đã mua từ 2 lần trở lên</div>
        </div>
    </div>

    <!-- Chart: Signups -->
    <div class="data-card p-6 mb-8">
        <div class="mb-6">
            <h2 class="text-lg font-bold text-slate-900 dark:text-white">Tốc độ tăng trưởng User Mới</h2>
            <p class="text-xs text-slate-500 mt-1">Theo dõi lượng đăng ký tài khoản theo từng ngày</p>
        </div>
        <div class="w-full h-[280px] relative">
            <canvas id="signupChart"></canvas>
        </div>
    </div>

    <!-- Tables Area -->
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        
        <!-- TOP VIP -->
        <div class="data-card flex flex-col">
            <div class="p-5 border-b border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/50 flex justify-between items-center">
                <div class="flex items-center gap-2 text-amber-500">
                    <span class="material-symbols-outlined">workspace_premium</span>
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white">Bảng Xếp Hạng VIP</h2>
                </div>
            </div>
            <div class="flex-1 overflow-x-auto p-0">
                <table class="w-full text-left border-collapse table-hover">
                    <thead>
                        <tr class="border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/30">
                            <th class="px-5 py-3 text-xs font-bold text-slate-500 text-center w-16">#</th>
                            <th class="px-5 py-3 text-xs font-bold text-slate-500">Khách Hàng</th>
                            <th class="px-5 py-3 text-xs font-bold text-slate-500 text-center">Gia Nhập</th>
                            <th class="px-5 py-3 text-xs font-bold text-slate-500 text-center">Số Đơn</th>
                            <th class="px-5 py-3 text-xs font-bold text-slate-500 text-right">Tổng Chi Tiêu</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/50">
                        @forelse($topBuyers as $idx => $buyer)
                            <tr class="transition-colors">
                                <td class="px-5 py-4">
                                    @if($idx == 0) <div class="rank-badge rank-1">1</div>
                                    @elseif($idx == 1) <div class="rank-badge rank-2">2</div>
                                    @elseif($idx == 2) <div class="rank-badge rank-3">3</div>
                                    @else <div class="rank-badge rank-other">{{ $idx + 1 }}</div>
                                    @endif
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        @php $colors = ['bg-blue-500', 'bg-emerald-500', 'bg-violet-500', 'bg-rose-500', 'bg-amber-500']; @endphp
                                        <div class="avatar-placeholder {{ $colors[$idx % count($colors)] }} shadow-sm">
                                            {{ mb_substr($buyer->name ?? 'U', 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-slate-900 dark:text-white">{{ $buyer->name }}</p>
                                            <p class="text-xs text-slate-500 mt-0.5">{{ $buyer->email ?? $buyer->phone }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <span class="text-xs font-bold text-slate-600 dark:text-slate-400">{{ $buyer->days_since_joined }} ngày</span>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <span class="inline-block px-2.5 py-1 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold rounded-md text-xs">{{ $buyer->total_orders }}</span>
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <p class="text-sm font-black text-amber-600">{{ number_format($buyer->total_spent, 0, ',', '.') }} ₫</p>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="p-6 text-center text-slate-500 text-sm">Chưa có dữ liệu giao dịch thành công.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- NEW USERS -->
        <div class="data-card flex flex-col">
            <div class="p-5 border-b border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/50 flex justify-between items-center">
                <div class="flex items-center gap-2 text-emerald-500">
                    <span class="material-symbols-outlined">fiber_new</span>
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white">Khách Mới Đăng Ký Gần Đây</h2>
                </div>
                <a href="{{ route('admin.users.index') }}" class="text-xs font-bold text-primary hover:underline">Xem tất cả</a>
            </div>
            <div class="flex-1 overflow-x-auto p-0">
                <table class="w-full text-left border-collapse table-hover">
                    <thead>
                        <tr class="border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/30">
                            <th class="px-5 py-3 text-xs font-bold text-slate-500">Thông tin User</th>
                            <th class="px-5 py-3 text-xs font-bold text-slate-500 text-right">Thời gian tạo</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/50">
                        @forelse($newRegisteredUsers as $user)
                            <tr class="transition-colors">
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="avatar-placeholder bg-slate-200 text-slate-500 dark:bg-slate-700 dark:text-slate-400">
                                            {{ mb_substr($user->name ?? 'U', 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-slate-900 dark:text-white">{{ $user->name }}</p>
                                            <p class="text-xs text-slate-500 mt-0.5">{{ $user->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <p class="text-sm font-bold text-slate-700 dark:text-slate-300">{{ $user->created_at->format('d/m/Y') }}</p>
                                    <p class="text-xs text-slate-500 mt-0.5">{{ $user->created_at->diffForHumans() }}</p>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="2" class="p-6 text-center text-slate-500 text-sm">Chưa có người dùng mới nào.</td></tr>
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
        const ctx = document.getElementById('signupChart');
        if (ctx) {
            const gradientLine = ctx.getContext('2d').createLinearGradient(0, 0, 0, 280);
            gradientLine.addColorStop(0, 'rgba(79, 70, 229, 0.4)');
            gradientLine.addColorStop(1, 'rgba(79, 70, 229, 0)');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: @json($signupDates),
                    datasets: [{
                        label: 'Lượng đăng ký',
                        data: @json($signupCounts),
                        borderColor: '#4f46e5',
                        backgroundColor: gradientLine,
                        borderWidth: 3, tension: 0.4, fill: true,
                        pointRadius: 3, pointHoverRadius: 6, pointBackgroundColor: '#4f46e5'
                    }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    plugins: { legend: { display: false }, tooltip: { cornerRadius: 8, padding: 12 } },
                    scales: {
                        x: { grid: { display: false }, ticks: { font: { size: 10 } } },
                        y: { 
                            beginAtZero: true, 
                            grid: { color: 'rgba(148, 163, 184, 0.1)' },
                            ticks: { stepSize: 1 } 
                        }
                    }
                }
            });
        }
    });
</script>
@endpush
