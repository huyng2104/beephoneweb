@extends('admin.layouts.app')

@section('content')
<div class="p-8 space-y-6">
    <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-slate-900 dark:text-white mt-1">Danh sách đơn hàng</h1>
            <p class="text-sm text-slate-500 dark:text-slate-300 mt-1">Theo dõi nhanh trạng thái đơn và mở chi tiết để xử lý.</p>
        </div>
        <div class="inline-flex items-center gap-2 text-xs text-slate-500">
            <span class="px-2.5 py-1 rounded-full bg-slate-100 dark:bg-slate-800">Tổng: {{ $orders->total() }}</span>
            <span class="px-2.5 py-1 rounded-full bg-slate-100 dark:bg-slate-800">Trang: {{ $orders->currentPage() }}/{{ $orders->lastPage() }}</span>
        </div>
    </div>

    @if (session('status'))
    <div class="p-3 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl text-sm">
        {{ session('status') }}
    </div>
    @endif

    {{-- THỐNG KÊ NHANH --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Tổng doanh thu --}}
        <a href="{{ route('admin.orders.index', ['status' => 'received']) }}"
            class="group bg-white dark:bg-slate-900 rounded-xl border border-slate-100 dark:border-slate-800 p-5 flex items-center gap-4 hover:border-emerald-300 hover:shadow-md transition-all duration-200">
            <div class="flex-shrink-0 h-12 w-12 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 flex items-center justify-center group-hover:bg-emerald-100 transition-colors">
                <span class="material-symbols-outlined text-[24px] text-emerald-500">payments</span>
            </div>
            <div class="min-w-0">
                <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide">Tổng doanh thu</p>
                <p class="text-lg font-bold text-emerald-600 mt-0.5 truncate">{{ number_format($stats['total_revenue'], 0, ',', '.') }}₫</p>
                <p class="text-[11px] text-slate-400 mt-0.5">Đơn đã hoàn thành</p>
            </div>
        </a>

        {{-- Đơn mới --}}
        <a href="{{ route('admin.orders.index', ['status' => 'pending']) }}"
            class="group bg-white dark:bg-slate-900 rounded-xl border border-slate-100 dark:border-slate-800 p-5 flex items-center gap-4 hover:border-amber-300 hover:shadow-md transition-all duration-200">
            <div class="flex-shrink-0 h-12 w-12 rounded-xl bg-amber-50 dark:bg-amber-500/10 flex items-center justify-center group-hover:bg-amber-100 transition-colors">
                <span class="material-symbols-outlined text-[24px] text-amber-500">inbox</span>
            </div>
            <div class="min-w-0">
                <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide">Đơn hàng mới</p>
                <p class="text-2xl font-bold text-amber-500 mt-0.5">{{ $stats['new_orders'] }}</p>
                <p class="text-[11px] text-slate-400 mt-0.5">Chờ xử lý</p>
            </div>
        </a>

        {{-- Đang giao --}}
        <a href="{{ route('admin.orders.index', ['status' => 'delivering']) }}"
            class="group bg-white dark:bg-slate-900 rounded-xl border border-slate-100 dark:border-slate-800 p-5 flex items-center gap-4 hover:border-indigo-300 hover:shadow-md transition-all duration-200">
            <div class="flex-shrink-0 h-12 w-12 rounded-xl bg-indigo-50 dark:bg-indigo-500/10 flex items-center justify-center group-hover:bg-indigo-100 transition-colors">
                <span class="material-symbols-outlined text-[24px] text-indigo-500">local_shipping</span>
            </div>
            <div class="min-w-0">
                <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide">Đang giao hàng</p>
                <p class="text-2xl font-bold text-indigo-500 mt-0.5">{{ $stats['delivering'] }}</p>
                <p class="text-[11px] text-slate-400 mt-0.5">Đang vận chuyển</p>
            </div>
        </a>

        {{-- Đã hủy --}}
        <a href="{{ route('admin.orders.index', ['status' => 'cancelled']) }}"
            class="group bg-white dark:bg-slate-900 rounded-xl border border-slate-100 dark:border-slate-800 p-5 flex items-center gap-4 hover:border-red-300 hover:shadow-md transition-all duration-200">
            <div class="flex-shrink-0 h-12 w-12 rounded-xl bg-red-50 dark:bg-red-500/10 flex items-center justify-center group-hover:bg-red-100 transition-colors">
                <span class="material-symbols-outlined text-[24px] text-red-500">cancel</span>
            </div>
            <div class="min-w-0">
                <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide">Đã hủy đơn</p>
                <p class="text-2xl font-bold text-red-500 mt-0.5">{{ $stats['cancelled'] }}</p>
                <p class="text-[11px] text-slate-400 mt-0.5">Tổng số đơn đã hủy</p>
            </div>
        </a>
    </div>

    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-100 dark:border-slate-800 p-4 lg:p-5">
        <form method="GET" id="filter-form" class="grid grid-cols-1 md:grid-cols-4 gap-3 items-end">
            {{-- Tìm kiếm --}}
            <div class="md:col-span-1">
                <label class="block text-xs font-semibold text-slate-500 mb-1">Tìm kiếm</label>
                <input type="text" name="q" value="{{ $search }}"
                    placeholder="Mã đơn, tên KH, SĐT..."
                    class="w-full rounded-lg border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
            </div>

            {{-- Trạng thái --}}
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1">Trạng thái đơn</label>
                <select name="status" onchange="document.getElementById('filter-form').submit()"
                    class="w-full rounded-lg border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                    <option value="">Tất cả trạng thái</option>
                    @foreach ($statuses as $status)
                    <option value="{{ $status }}" @selected($activeStatus === $status)>{{ $statusLabels[$status] }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Hoàn hàng --}}
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1">Hoàn trả</label>
                <select name="return_status" onchange="document.getElementById('filter-form').submit()"
                    class="w-full rounded-lg border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                    <option value="">Tất cả</option>
                    <option value="has_return" @selected($activeReturnStatus === 'has_return')>Có hoàn trả</option>
                    <option value="no_return" @selected($activeReturnStatus === 'no_return')>Không hoàn trả</option>
                </select>
            </div>

            {{-- Thanh toán --}}
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1">Thanh toán</label>
                <select name="payment_status" onchange="document.getElementById('filter-form').submit()"
                    class="w-full rounded-lg border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                    <option value="">Tất cả</option>
                    <option value="pending" @selected(request('payment_status') === 'pending')>Chưa thanh toán</option>
                    <option value="paid" @selected(request('payment_status') === 'paid')>Đã thanh toán</option>
                </select>
            </div>

            {{-- Từ ngày --}}
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1">Từ ngày</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}"
                    onchange="document.getElementById('filter-form').submit()"
                    class="w-full rounded-lg border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
            </div>

            {{-- Đến ngày --}}
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1">Đến ngày</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}"
                    onchange="document.getElementById('filter-form').submit()"
                    class="w-full rounded-lg border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
            </div>

            {{-- Sắp xếp --}}
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1">Sắp xếp</label>
                <select name="sort" onchange="document.getElementById('filter-form').submit()"
                    class="w-full rounded-lg border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                    <option value="newest" @selected(request('sort', 'newest') === 'newest')>Mới nhất</option>
                    <option value="oldest" @selected(request('sort') === 'oldest')>Cũ nhất</option>
                    <option value="total_high" @selected(request('sort') === 'total_high')>Giá trị cao nhất</option>
                    <option value="total_low" @selected(request('sort') === 'total_low')>Giá trị thấp nhất</option>
                </select>
            </div>

            {{-- Nút hành động --}}
            <div class="flex gap-2">
                <button type="submit" class="flex-1 px-4 py-2 bg-primary text-black rounded-lg font-semibold text-sm hover:brightness-105">Lọc</button>
                <a href="{{ route('admin.orders.index') }}" class="px-4 py-2 rounded-lg border border-slate-200 dark:border-slate-700 text-sm font-semibold text-slate-600 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800">Reset</a>
            </div>
        </form>
    </div>


    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-100 dark:border-slate-800 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-50 dark:bg-slate-900/80 border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest whitespace-nowrap">Mã đơn hàng</th>
                        <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest whitespace-nowrap">Khách hàng</th>
                        <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest whitespace-nowrap">Tổng đơn</th>
                        <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest whitespace-nowrap">Thanh toán</th>
                        <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest whitespace-nowrap">Trạng thái</th>
                        <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest whitespace-nowrap">Hoàn trả</th>
                        <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest whitespace-nowrap text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody id="realtime-order-list" class="divide-y divide-slate-100 dark:divide-slate-800/60">
                    @forelse ($orders as $order)
                    @php
                    $s = $order->status;
                    $statusClass = match(true) {
                        $s === 'pending' => 'bg-amber-50 text-amber-600 border-amber-200 dark:bg-amber-500/10 dark:border-amber-500/20',
                        $s === 'ready_to_pick' => 'bg-blue-50 text-blue-600 border-blue-200 dark:bg-blue-500/10 dark:border-blue-500/20',
                        in_array($s, ['picking','money_collect_picking','picked']) => 'bg-sky-50 text-sky-600 border-sky-200 dark:bg-sky-500/10 dark:border-sky-500/20',
                        in_array($s, ['storing','transporting','sorting']) => 'bg-violet-50 text-violet-600 border-violet-200 dark:bg-violet-500/10 dark:border-violet-500/20',
                        in_array($s, ['delivering','money_collect_delivering']) => 'bg-indigo-50 text-indigo-600 border-indigo-200 dark:bg-indigo-500/10 dark:border-indigo-500/20',
                        in_array($s, ['delivered', 'received']) => 'bg-emerald-50 text-emerald-600 border-emerald-200 dark:bg-emerald-500/10 dark:border-emerald-500/20',
                        in_array($s, ['delivery_fail','waiting_to_return','return','return_transporting','return_sorting','returning','return_fail','returned']) => 'bg-orange-50 text-orange-600 border-orange-200 dark:bg-orange-500/10 dark:border-orange-500/20',
                        in_array($s, ['exception','damage','lost']) => 'bg-rose-50 text-rose-600 border-rose-200 dark:bg-rose-500/10 dark:border-rose-500/20',
                        in_array($s, ['cancel','cancelled']) => 'bg-red-50 text-red-600 border-red-200 dark:bg-red-500/10 dark:border-red-500/20',
                        default => 'bg-slate-50 text-slate-600 border-slate-200 dark:bg-slate-800 dark:border-slate-700',
                    };
                    // Badge hoàn trả từ ReturnRequest
                    $latestReturn = $order->returnRequests->first();
                    $rrStatusColors = [
                        'pending'   => 'bg-amber-100 text-amber-700',
                        'approved'  => 'bg-blue-100 text-blue-700',
                        'rejected'  => 'bg-red-100 text-red-700',
                        'picking'   => 'bg-indigo-100 text-indigo-700',
                        'received'  => 'bg-cyan-100 text-cyan-700',
                        'completed' => 'bg-green-100 text-green-700',
                    ];
                    $returnClass = $latestReturn ? ($rrStatusColors[$latestReturn->status] ?? 'bg-slate-100 text-slate-700') : '';
                    @endphp
                    <tr class="group hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors duration-200">
                        <td class="px-5 py-4 align-middle">
                            <div>
                                <p class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wide">{{ $order->order_code }}</p>
                                <p class="text-xs font-medium text-slate-400 mt-1 flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[13px]">schedule</span>
                                    {{ optional($order->created_at)->format('d/m/Y H:i') }}
                                </p>
                            </div>
                        </td>
                        <td class="px-5 py-4 align-middle">
                            <div class="flex flex-col gap-1">
                                <p class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-1.5">
                                    <span class="material-symbols-outlined text-[15px] text-slate-400">person</span>
                                    {{ $order->customer_name }}
                                </p>
                                <p class="text-xs font-medium text-slate-500 flex items-center gap-1.5">
                                    <span class="material-symbols-outlined text-[13px]">call</span>
                                    {{ $order->customer_phone }}
                                </p>
                            </div>
                        </td>
                        <td class="px-5 py-4 align-middle">
                            <div class="flex flex-col gap-0.5">
                                <p class="text-sm font-bold text-emerald-600 dark:text-emerald-400">{{ number_format($order->total_amount) }} ₫</p>
                                <p class="text-xs font-medium text-slate-400">{{ $order->items->sum('quantity') }} sản phẩm</p>
                            </div>
                        </td>
                        <td class="px-5 py-4 align-middle whitespace-nowrap">
                            <div class="flex flex-col items-start gap-1.5">
                                <span class="bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 text-xs font-bold px-2.5 py-1 rounded-lg border border-slate-200 dark:border-slate-700 uppercase tracking-wide">
                                    {{ $order->payment_method ?? 'COD' }}
                                </span>
                                @if(($order->payment_status ?? 'pending') === 'paid')
                                    <span class="flex items-center gap-1.5 text-xs text-emerald-700 font-bold bg-emerald-50 border border-emerald-200 dark:bg-emerald-500/10 dark:border-emerald-500/20 px-2.5 py-1 rounded-lg">
                                        <span class="material-symbols-outlined text-[14px]">check_circle</span> Đã thanh toán
                                    </span>
                                @else
                                    <span class="flex items-center gap-1.5 text-xs text-amber-700 font-bold bg-amber-50 border border-amber-200 dark:bg-amber-500/10 dark:border-amber-500/20 px-2.5 py-1 rounded-lg">
                                        <span class="material-symbols-outlined text-[14px]">hourglass_empty</span> Chờ thanh toán
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td class="px-5 py-4 align-middle">
                            <span class="inline-flex items-center gap-1.5 text-xs px-2.5 py-1.5 rounded-lg font-bold border {{ $statusClass }}">
                                @if(in_array($order->status, [\App\Models\Order::STATUS_DELIVERED, \App\Models\Order::STATUS_RECEIVED]))
                                    <span class="material-symbols-outlined text-[16px]">done_all</span>
                                @elseif(in_array($order->status, [\App\Models\Order::STATUS_CANCELLED, \App\Models\Order::STATUS_CANCEL]))
                                    <span class="material-symbols-outlined text-[16px]">cancel</span>
                                @elseif(in_array($order->status, [\App\Models\Order::STATUS_READY_TO_PICK, \App\Models\Order::STATUS_PICKING, \App\Models\Order::STATUS_MONEY_COLLECT_PICKING, \App\Models\Order::STATUS_PICKED]))
                                    <span class="material-symbols-outlined text-[16px]">inventory_2</span>
                                @elseif(in_array($order->status, [\App\Models\Order::STATUS_STORING, \App\Models\Order::STATUS_TRANSPORTING, \App\Models\Order::STATUS_SORTING, \App\Models\Order::STATUS_DELIVERING, \App\Models\Order::STATUS_MONEY_COLLECT_DELIVERING]))
                                    <span class="material-symbols-outlined text-[16px]">local_shipping</span>
                                @elseif(in_array($order->status, [\App\Models\Order::STATUS_DELIVERY_FAIL, \App\Models\Order::STATUS_WAITING_TO_RETURN, \App\Models\Order::STATUS_RETURN, \App\Models\Order::STATUS_RETURN_TRANSPORTING, \App\Models\Order::STATUS_RETURN_SORTING, \App\Models\Order::STATUS_RETURNING, \App\Models\Order::STATUS_RETURN_FAIL, \App\Models\Order::STATUS_RETURNED]))
                                    <span class="material-symbols-outlined text-[16px]">assignment_return</span>
                                @elseif(in_array($order->status, [\App\Models\Order::STATUS_EXCEPTION, \App\Models\Order::STATUS_DAMAGE, \App\Models\Order::STATUS_LOST]))
                                    <span class="material-symbols-outlined text-[16px]">warning</span>
                                @else
                                    <span class="material-symbols-outlined text-[16px]">pending_actions</span>
                                @endif
                                {{ $statusLabels[$order->status] ?? $order->status }}
                            </span>
                        </td>
                        {{-- Cột Hoàn hàng --}}
                        <td class="px-5 py-4 align-middle">
                            @if($order->returnRequests->isNotEmpty())
                                <a href="{{ route('admin.orders.show', $order) }}"
                                   class="inline-flex items-center gap-1.5 text-xs px-2.5 py-1.5 rounded-lg font-bold border bg-orange-50 text-orange-600 border-orange-200 dark:bg-orange-500/10 dark:border-orange-500/20 hover:opacity-80 transition-opacity">
                                    <span class="material-symbols-outlined text-[14px]">assignment_return</span>
                                    Hoàn trả
                                </a>
                            @else
                                <span class="text-slate-300 dark:text-slate-600 text-sm">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 align-middle text-right">
                            <a href="{{ route('admin.orders.show', $order) }}" class="inline-flex items-center justify-center size-9 rounded-full bg-slate-50 text-slate-500 hover:text-[#f4c025] hover:bg-[#f4c025]/10 dark:bg-slate-800 transition-colors shadow-sm" title="Xem chi tiết">
                                <span class="material-symbols-outlined text-[20px]">chevron_right</span>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-5 py-16 text-center">
                            <div class="inline-flex items-center justify-center size-16 rounded-full bg-slate-50 dark:bg-slate-800 text-slate-400 mb-4">
                                <span class="material-symbols-outlined text-3xl">receipt_long</span>
                            </div>
                            <p class="text-base font-bold text-slate-900 dark:text-white">Không có đơn hàng nào!</p>
                            <p class="text-sm text-slate-500 mt-1">Chưa có dữ liệu hoặc không tìm thấy kết quả phù hợp với bộ lọc.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100 dark:border-slate-800">
            {{ $orders->links() }}
        </div>
    </div>
</div>
@endsection

@push('js')
<script type="module">
    document.addEventListener('DOMContentLoaded', function () {
        const currentUserId = {{ auth()->id() ?? 'null' }};

        // Chờ Echo khởi tạo xong
        const initEcho = setInterval(() => {
            if (window.Echo) {
                clearInterval(initEcho);
                console.log("✅ Bảng Đơn Hàng Admin đã gắn mắt thần Real-time!");

                window.Echo.channel('order-tracker')
                    .listen('.status-updated', (e) => {
                        
                        // Bắt chữ "đơn hàng mới" từ tiêu đề của thông báo
                        let title = e.title.toLowerCase();

                        // Nếu đúng là gửi cho Admin này VÀ là thông báo Đơn Mới
                        if (e.targetUserId == currentUserId && title.includes('đơn hàng mới')) {
                            console.log("🔥 Đang tải dữ liệu đơn mới lén lút trong nền...");

                            // Bí kíp: Fetch lại chính trang hiện tại ngầm trong background
                            fetch(window.location.href)
                                .then(res => res.text())
                                .then(html => {
                                    // Phân tích HTML lấy được thành một trang web ảo (DOM ảo)
                                    let doc = new DOMParser().parseFromString(html, 'text/html');
                                    
                                    // Bốc cái danh sách đơn hàng mới thay cho cái danh sách cũ
                                    let newList = doc.querySelector('#realtime-order-list');
                                    let currentList = document.querySelector('#realtime-order-list');

                                    if (newList && currentList) {
                                        currentList.innerHTML = newList.innerHTML;
                                        
                                        // TẠO HIỆU ỨNG ĐẲNG CẤP: Nhá đèn màu vàng cho đơn đầu tiên vừa rớt xuống
                                        let firstRow = currentList.firstElementChild;
                                        if (firstRow) {
                                            // Đổi nền vàng nhạt để báo hiệu
                                            firstRow.style.backgroundColor = 'rgba(244, 192, 37, 0.2)'; // Màu vàng Bee Phone nhạt
                                            firstRow.style.transition = 'background-color 2s ease';
                                            
                                            // Nửa giây sau tự mờ dần về bình thường
                                            setTimeout(() => {
                                                firstRow.style.backgroundColor = 'transparent';
                                            }, 2000);
                                        }
                                    }
                                })
                                .catch(err => console.error("Lỗi khi tải ngầm danh sách đơn hàng: ", err));
                        }
                    });
            }
        }, 500); // Kiểm tra mỗi 0.5s
    });
</script>
@endpush 