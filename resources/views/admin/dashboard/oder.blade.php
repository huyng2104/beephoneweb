@extends('admin.layouts.app')

@push('css')
<style>
.data-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    border-radius: 1.25rem;
    overflow: hidden;
    position: relative;
}
.dark .data-card {
    background: #1e293b;
    border-color: #334155;
}

.funnel-card {
    padding: 1.5rem;
    border-radius: 1rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    position: relative;
    overflow: hidden;
    background: #fff;
    border: 1px solid #e2e8f0;
    box-shadow: 0 2px 4px -1px rgba(0,0,0,0.02);
}
.dark .funnel-card {
    background: #1e293b;
    border-color: #334155;
}
.funnel-icon {
    width: 3.5rem; height: 3.5rem; border-radius: 1rem;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.5rem;
}

.refresh-badge {
    position: absolute;
    top: 1rem; right: 1rem;
    display: flex; align-items: center; gap: 0.5rem;
    background: #f1f5f9;
    padding: 0.25rem 0.75rem; border-radius: 999px;
    font-size: 0.7rem; font-weight: 700; color: #64748b;
    z-index: 10;
}
.dark .refresh-badge { background: #334155; color: #cbd5e1; }
.pulse-dot { width: 6px; height: 6px; background: #10b981; border-radius: 50%; animation: pulse 2s infinite; }
@keyframes pulse { 0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.4); } 70% { box-shadow: 0 0 0 6px rgba(16, 185, 129, 0); } 100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); } }
.spin { animation: spin 1s linear infinite; }
@keyframes spin { to { transform: rotate(360deg); } }

.table-hover tr:hover td { background: #f8fafc; }
.dark .table-hover tr:hover td { background: #0f172a; }
</style>
@endpush

@section('content')
<div class="flex-1 overflow-y-auto p-4 sm:p-8 bg-slate-50 dark:bg-[#0f172a] min-h-screen flex flex-col">
    <!-- Header & Filter -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">Trung Tâm Điều Phối Đơn</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Hệ thống Radar theo dõi luồng đơn hàng realtime</p>
        </div>
        
        <form id="filterForm" method="GET" action="{{ route('admin.dashboard.orders') }}" class="flex flex-wrap items-center gap-3 bg-white dark:bg-slate-800 p-2 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm">
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
                <input type="date" name="start_date" id="startDate" value="{{ $customStartDate }}" class="bg-slate-100 dark:bg-slate-700 border-none text-sm rounded-lg px-3 py-2.5 text-slate-700 dark:text-white focus:ring-2 focus:ring-primary/50 outline-none">
                <span class="text-slate-400 font-medium">-</span>
                <input type="date" name="end_date" id="endDate" value="{{ $customEndDate }}" class="bg-slate-100 dark:bg-slate-700 border-none text-sm rounded-lg px-3 py-2.5 text-slate-700 dark:text-white focus:ring-2 focus:ring-primary/50 outline-none">
                <button type="submit" class="bg-primary hover:bg-primary/90 text-white p-2.5 rounded-lg transition-colors flex items-center justify-center">
                    <span class="material-symbols-outlined text-[20px]">search</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Funnel Pipeline Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="funnel-card border-l-4 border-l-amber-500">
            <div class="funnel-icon bg-amber-100 text-amber-600 dark:bg-amber-500/20 dark:text-amber-400"><span class="material-symbols-outlined">pending_actions</span></div>
            <div>
                <p class="text-xs font-bold text-slate-500 uppercase">Chờ Xác Nhận</p>
                <h3 class="text-2xl font-black text-slate-900 dark:text-white" id="count-pending">{{ number_format($statusGroups['Chờ xác nhận']) }}</h3>
            </div>
        </div>
        <div class="funnel-card border-l-4 border-l-blue-500">
            <div class="funnel-icon bg-blue-100 text-blue-600 dark:bg-blue-500/20 dark:text-blue-400"><span class="material-symbols-outlined">local_shipping</span></div>
            <div>
                <p class="text-xs font-bold text-slate-500 uppercase">Đang Giao Hàng</p>
                <h3 class="text-2xl font-black text-slate-900 dark:text-white" id="count-processing">{{ number_format($statusGroups['Đang giao']) }}</h3>
            </div>
        </div>
        <div class="funnel-card border-l-4 border-l-emerald-500">
            <div class="funnel-icon bg-emerald-100 text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400"><span class="material-symbols-outlined">task_alt</span></div>
            <div>
                <p class="text-xs font-bold text-slate-500 uppercase">Giao Thành Công</p>
                <h3 class="text-2xl font-black text-slate-900 dark:text-white" id="count-completed">{{ number_format($statusGroups['Thành công']) }}</h3>
            </div>
        </div>
        <div class="funnel-card border-l-4 border-l-rose-500">
            <div class="funnel-icon bg-rose-100 text-rose-600 dark:bg-rose-500/20 dark:text-rose-400"><span class="material-symbols-outlined">cancel</span></div>
            <div>
                <p class="text-xs font-bold text-slate-500 uppercase">Hủy / Thất Bại</p>
                <h3 class="text-2xl font-black text-slate-900 dark:text-white" id="count-cancelled">{{ number_format($statusGroups['Đã hủy']) }}</h3>
            </div>
        </div>
    </div>

    <!-- Main Content Area: Chart + Live Table -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 flex-1">
        
        <!-- Doughnut Chart Area -->
        <div class="data-card p-6 flex flex-col">
            <div class="refresh-badge">
                <div class="pulse-dot"></div>
                <span id="countdownText">Làm mới sau 10s</span>
                <span class="material-symbols-outlined text-[14px] ml-1 cursor-pointer hover:text-primary" onclick="fetchData(true)" id="refreshIcon" title="Làm mới ngay">refresh</span>
            </div>
            
            <div class="mb-4">
                <h2 class="text-lg font-bold text-slate-900 dark:text-white">Tỷ Lệ Trạng Thái</h2>
                <p class="text-xs text-slate-500 mt-1">Phễu đơn hàng tổng quan</p>
            </div>

            <div class="flex-1 relative flex items-center justify-center min-h-[250px]">
                <canvas id="orderStatusChart"></canvas>
            </div>
            
            <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-800 flex justify-between items-center">
                <span class="text-xs font-bold text-slate-500 uppercase">Tổng số đơn</span>
                <span class="text-xl font-black text-slate-900 dark:text-white" id="totalOrdersCount">{{ number_format(array_sum($statusCounts)) }}</span>
            </div>

            <!-- Cancellation Reasons -->
            <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-800">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white mb-3">Lý do hủy đơn phổ biến</h3>
                <div id="cancelReasonsContainer" class="space-y-2">
                    @forelse($cancelReasons as $reason)
                        <div class="flex justify-between items-center bg-rose-50 dark:bg-rose-500/10 p-2 rounded">
                            <span class="text-xs font-medium text-slate-700 dark:text-slate-300">{{ $reason->cancellation_reason ?? 'Không rõ lý do' }}</span>
                            <span class="text-xs font-bold text-rose-600 dark:text-rose-400">{{ $reason->total }} đơn</span>
                        </div>
                    @empty
                        <div class="text-xs text-slate-500 text-center py-2">Chưa có đơn hủy trong kỳ</div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Live Feed Table -->
        <div class="lg:col-span-2 data-card flex flex-col">
            <div class="p-5 border-b border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/50 flex justify-between items-center">
                <div class="flex items-center gap-2 text-indigo-600 dark:text-indigo-400">
                    <span class="material-symbols-outlined spin">radar</span>
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white">Live Feed Đơn Hàng Mới Nhất</h2>
                </div>
                <span class="text-xs font-bold bg-indigo-100 text-indigo-600 dark:bg-indigo-500/20 dark:text-indigo-400 px-2 py-1 rounded">Auto-sync</span>
            </div>
            <div class="flex-1 overflow-x-auto p-0">
                <table class="w-full text-left border-collapse table-hover">
                    <thead>
                        <tr class="border-b border-slate-200 dark:border-slate-700">
                            <th class="px-5 py-3 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase">Mã Đơn</th>
                            <th class="px-5 py-3 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase">Khách Hàng & Giá Trị</th>
                            <th class="px-5 py-3 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase">Thanh Toán</th>
                            <th class="px-5 py-3 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase text-right">Trạng Thái</th>
                        </tr>
                    </thead>
                    <tbody id="liveFeedTable" class="divide-y divide-slate-100 dark:divide-slate-800/50">
                        <!-- Render qua server side lần đầu, sau đó JS sẽ đè -->
                        @forelse($latestOrders as $order)
                            <tr class="transition-colors">
                                <td class="px-5 py-3">
                                    <a href="{{ route('admin.orders.show', $order['id']) }}" class="text-sm font-bold text-primary hover:underline">#{{ $order['order_code'] }}</a>
                                    <p class="text-[10px] font-medium text-slate-400 mt-0.5">{{ $order['created_at'] }} ({{ $order['time_ago'] }})</p>
                                </td>
                                <td class="px-5 py-3">
                                    <p class="text-sm font-bold text-slate-900 dark:text-white">{{ $order['customer_name'] }}</p>
                                    <p class="text-xs font-black text-amber-600 mt-0.5">{{ $order['total_amount'] }}</p>
                                </td>
                                <td class="px-5 py-3">
                                    <div class="flex flex-col gap-1 items-start">
                                        <span class="text-[10px] font-bold bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-300 px-2 py-0.5 rounded">{{ $order['payment_method'] }}</span>
                                        @if($order['payment_status'] == 'paid')
                                            <span class="text-[10px] font-bold text-emerald-600"><span class="inline-block w-2 h-2 rounded-full bg-emerald-500 mr-1"></span>Đã TT</span>
                                        @else
                                            <span class="text-[10px] font-bold text-amber-600"><span class="inline-block w-2 h-2 rounded-full bg-amber-500 mr-1"></span>Chưa TT</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-5 py-3 text-right">
                                    <span class="inline-block px-2.5 py-1 text-[11px] font-bold rounded bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-600">{{ $order['status_label'] }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="p-6 text-center text-slate-500 text-sm">Chưa có đơn hàng mới.</td></tr>
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
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
    function toggleCustomDate() {
        const period = document.getElementById('periodSelect').value;
        const customDiv = document.getElementById('customDateRange');
        if (period === 'custom') customDiv.classList.remove('hidden');
        else customDiv.classList.add('hidden');
    }

    let doughnutChart = null;
    let refreshInterval = 10;
    let currentCountdown = refreshInterval;
    let countdownTimer = null;

    document.addEventListener('DOMContentLoaded', function() {
        initChart();
        startCountdown();
    });

    function initChart() {
        const ctx = document.getElementById('orderStatusChart');
        if (ctx) {
            doughnutChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: @json($statusLabels),
                    datasets: [{
                        data: @json($statusCounts),
                        backgroundColor: @json($statusColors),
                        borderWidth: 0, hoverOffset: 6
                    }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false, cutout: '70%',
                    plugins: {
                        legend: { position: 'bottom', labels: { usePointStyle: true, boxWidth: 8, font: { size: 11 } } },
                        tooltip: { callbacks: { label: function(c) { 
                            let total = c.dataset.data.reduce((a, b) => a + b, 0);
                            let pct = total > 0 ? Math.round((c.raw / total) * 100) : 0;
                            return ' ' + c.raw + ' đơn (' + pct + '%)'; 
                        } } }
                    }
                }
            });
        }
    }

    function startCountdown() {
        if (countdownTimer) clearInterval(countdownTimer);
        currentCountdown = refreshInterval;
        updateCountdownText();
        
        countdownTimer = setInterval(() => {
            currentCountdown--;
            if (currentCountdown <= 0) {
                fetchData(false);
                currentCountdown = refreshInterval;
            }
            updateCountdownText();
        }, 1000);
    }

    function updateCountdownText() {
        document.getElementById('countdownText').innerText = `Làm mới sau ${currentCountdown}s`;
    }

    function fetchData(manual = false) {
        if (countdownTimer) clearInterval(countdownTimer);
        const rIcon = document.getElementById('refreshIcon');

        if (manual) {
            document.getElementById('countdownText').innerText = 'Đang tải...';
            rIcon.classList.add('spin');
        } else {
            document.getElementById('countdownText').innerText = 'Auto sync...';
        }

        const period = document.getElementById('periodSelect').value;
        let url = `{{ route('admin.dashboard.orders') }}?period=${period}`;
        if (period === 'custom') {
            url += `&start_date=${document.getElementById('startDate').value}&end_date=${document.getElementById('endDate').value}`;
        }

        axios.get(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(res => {
                const data = res.data;
                // Update Pipeline Cards
                document.getElementById('count-pending').innerText = new Intl.NumberFormat('vi-VN').format(data.pipeline.pending);
                document.getElementById('count-processing').innerText = new Intl.NumberFormat('vi-VN').format(data.pipeline.processing);
                document.getElementById('count-completed').innerText = new Intl.NumberFormat('vi-VN').format(data.pipeline.completed);
                document.getElementById('count-cancelled').innerText = new Intl.NumberFormat('vi-VN').format(data.pipeline.cancelled);

                // Update Chart
                if (doughnutChart) {
                    doughnutChart.data.datasets[0].data = data.statusCounts;
                    doughnutChart.update();
                }
                
                let total = data.statusCounts.reduce((a, b) => a + b, 0);
                document.getElementById('totalOrdersCount').innerText = new Intl.NumberFormat('vi-VN').format(total);

                // Update Cancellation Reasons
                const cancelContainer = document.getElementById('cancelReasonsContainer');
                cancelContainer.innerHTML = '';
                if(data.cancelReasons && data.cancelReasons.length > 0) {
                    data.cancelReasons.forEach(reason => {
                        let text = reason.cancellation_reason || 'Không rõ lý do';
                        cancelContainer.innerHTML += `
                            <div class="flex justify-between items-center bg-rose-50 dark:bg-rose-500/10 p-2 rounded">
                                <span class="text-xs font-medium text-slate-700 dark:text-slate-300">${text}</span>
                                <span class="text-xs font-bold text-rose-600 dark:text-rose-400">${reason.total} đơn</span>
                            </div>
                        `;
                    });
                } else {
                    cancelContainer.innerHTML = `<div class="text-xs text-slate-500 text-center py-2">Chưa có đơn hủy trong kỳ</div>`;
                }

                // Update Table
                const tbody = document.getElementById('liveFeedTable');
                tbody.innerHTML = '';
                if(data.latestOrders.length === 0) {
                    tbody.innerHTML = `<tr><td colspan="4" class="p-6 text-center text-slate-500 text-sm">Chưa có đơn hàng mới.</td></tr>`;
                } else {
                    data.latestOrders.forEach(order => {
                        const baseUrl = "{{ url('admin/orders') }}";
                        const paymentStatusHtml = order.payment_status === 'paid' 
                            ? `<span class="text-[10px] font-bold text-emerald-600"><span class="inline-block w-2 h-2 rounded-full bg-emerald-500 mr-1"></span>Đã TT</span>`
                            : `<span class="text-[10px] font-bold text-amber-600"><span class="inline-block w-2 h-2 rounded-full bg-amber-500 mr-1"></span>Chưa TT</span>`;
                            
                        tbody.innerHTML += `
                            <tr class="transition-colors animate-fade-in">
                                <td class="px-5 py-3">
                                    <a href="${baseUrl}/${order.id}" class="text-sm font-bold text-primary hover:underline">#${order.order_code}</a>
                                    <p class="text-[10px] font-medium text-slate-400 mt-0.5">${order.created_at} (${order.time_ago})</p>
                                </td>
                                <td class="px-5 py-3">
                                    <p class="text-sm font-bold text-slate-900 dark:text-white">${order.customer_name}</p>
                                    <p class="text-xs font-black text-amber-600 mt-0.5">${order.total_amount}</p>
                                </td>
                                <td class="px-5 py-3">
                                    <div class="flex flex-col gap-1 items-start">
                                        <span class="text-[10px] font-bold bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-300 px-2 py-0.5 rounded">${order.payment_method}</span>
                                        ${paymentStatusHtml}
                                    </div>
                                </td>
                                <td class="px-5 py-3 text-right">
                                    <span class="inline-block px-2.5 py-1 text-[11px] font-bold rounded bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-600">${order.status_label}</span>
                                </td>
                            </tr>
                        `;
                    });
                }
            })
            .catch(err => console.error("Error fetching live feed:", err))
            .finally(() => {
                rIcon.classList.remove('spin');
                startCountdown();
            });
    }
</script>
<style>
.animate-fade-in { animation: fadeIn 0.5s ease-in-out; }
@keyframes fadeIn { from { opacity: 0; background-color: #f1f5f9; } to { opacity: 1; background-color: transparent; } }
</style>
@endpush
