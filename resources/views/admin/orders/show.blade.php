@extends('admin.layouts.app')

@section('content')
<!-- Google Fonts: Inter -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
<style>
    .font-inter { font-family: 'Inter', sans-serif; }
    .letter-spacing-widest { letter-spacing: 0.1em; }
</style>

@php
    $returnImageUrl = null;
    if ($order->return_image) {
        $returnImageUrl = \Illuminate\Support\Str::startsWith($order->return_image, ['http://', 'https://', 'uploads/'])
            ? asset($order->return_image)
            : asset('storage/' . $order->return_image);
    }
@endphp
<div class="p-8 space-y-6 font-inter text-slate-900 dark:text-slate-100">
    @php
        $headerStatus = $order->status;
        $headerBadge = match(true) {
            $headerStatus === 'pending'
                => ['color' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300 border border-amber-200 dark:border-amber-600/30', 'icon' => 'hourglass_empty'],
            $headerStatus === 'ready_to_pick'
                => ['color' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300 border border-blue-200 dark:border-blue-600/30', 'icon' => 'inventory_2'],
            in_array($headerStatus, ['picking','money_collect_picking','picked'])
                => ['color' => 'bg-sky-100 text-sky-800 dark:bg-sky-900/30 dark:text-sky-300 border border-sky-200 dark:border-sky-600/30', 'icon' => 'local_shipping'],
            in_array($headerStatus, ['storing','transporting','sorting'])
                => ['color' => 'bg-violet-100 text-violet-800 dark:bg-violet-900/30 dark:text-violet-300 border border-violet-200 dark:border-violet-600/30', 'icon' => 'package_2'],
            in_array($headerStatus, ['delivering','money_collect_delivering'])
                => ['color' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-600/30', 'icon' => 'local_shipping'],
            $headerStatus === 'delivered'
                => ['color' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-600/30', 'icon' => 'check_circle'],
            $headerStatus === 'received'
                => ['color' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300 border border-green-200 dark:border-green-600/30', 'icon' => 'verified'],
            in_array($headerStatus, ['cancel','cancelled'])
                => ['color' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300 border border-red-200 dark:border-red-600/30', 'icon' => 'cancel'],
            in_array($headerStatus, ['delivery_fail','waiting_to_return','return','return_transporting','return_sorting','returning','return_fail','returned'])
                => ['color' => 'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-300 border border-orange-200 dark:border-orange-600/30', 'icon' => 'assignment_return'],
            default => ['color' => 'bg-slate-100 text-slate-700 border border-slate-200', 'icon' => 'info'],
        };
        $isVnpayUnpaidHeader = in_array($order->payment_method, ['vnpay', 'vnp']) && $order->payment_status === 'pending' && $order->status !== \App\Models\Order::STATUS_CANCELLED;
        $terminalStatusesHeader = [\App\Models\Order::STATUS_DELIVERED, \App\Models\Order::STATUS_RECEIVED, \App\Models\Order::STATUS_CANCELLED, \App\Models\Order::STATUS_CANCEL, \App\Models\Order::STATUS_RETURNED, \App\Models\Order::STATUS_RETURN_FAIL, \App\Models\Order::STATUS_EXCEPTION, \App\Models\Order::STATUS_DAMAGE, \App\Models\Order::STATUS_LOST];
        $showApproveBtn = !$isVnpayUnpaidHeader && $order->status === \App\Models\Order::STATUS_PENDING;
        $showCancelBtn  = !$isVnpayUnpaidHeader && in_array($order->status, [\App\Models\Order::STATUS_PENDING, \App\Models\Order::STATUS_READY_TO_PICK, \App\Models\Order::STATUS_PICKING, \App\Models\Order::STATUS_MONEY_COLLECT_PICKING]);
        $showCancelBtnVnpay = $isVnpayUnpaidHeader && $order->status === \App\Models\Order::STATUS_PENDING;
    @endphp

    <div class="mb-6 flex flex-col lg:flex-row lg:items-center justify-between gap-4 border-b border-slate-200 dark:border-slate-800 pb-6">
        <div class="flex items-start lg:items-center gap-4">
            <a href="{{ route('admin.orders.index') }}" class="w-10 h-10 flex items-center justify-center rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-[#f4c025] hover:text-black transition-all text-slate-600 dark:text-slate-300">
                <span class="material-symbols-outlined">arrow_back</span>
            </a>
            <div>
                <h1 class="text-2xl font-black uppercase tracking-tight text-slate-900 dark:text-white">Chi tiết đơn hàng</h1>
                <div class="mt-1 flex flex-wrap items-center gap-2">
                    <p class="text-sm text-slate-500 dark:text-slate-400 flex items-center gap-2">
                        <span>Mã đơn: <span class="font-bold text-[#f4c025]">#{{ $order->order_code }}</span></span>
                        @if($order->tracking_number)
                            <span class="w-1 h-1 rounded-full bg-slate-300 dark:bg-slate-700"></span>
                            <span>Vận đơn: <span class="font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider">{{ $order->tracking_number }}</span></span>
                        @endif
                    </p>
                    <span class="w-1 h-1 rounded-full bg-slate-300 dark:bg-slate-700 hidden sm:inline-block"></span>
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-bold {{ $headerBadge['color'] }}">
                        <span class="material-symbols-outlined text-[14px]">{{ $headerBadge['icon'] }}</span>
                        {{ $statusLabels[$order->status] ?? $order->status }}
                    </span>
                </div>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            {{-- Sync Button --}}
            @if($order->tracking_number && $order->status !== 'received')
            <form action="{{ route('admin.orders.tracking.sync', $order) }}" method="POST">
                @csrf
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-cyan-50 dark:bg-cyan-900/30 text-cyan-700 dark:text-cyan-300 border border-cyan-200 dark:border-cyan-800 rounded-xl font-bold text-sm hover:bg-cyan-600 hover:text-white transition-all shadow-sm">
                    <span class="material-symbols-outlined text-[18px]">sync</span>
                    Đồng bộ GHN
                </button>
            </form>
            @endif

            {{-- In PDF --}}
            {{-- <a href="{{ route('admin.orders.print.pdf', $order) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 rounded-xl font-bold text-sm hover:bg-slate-200 dark:hover:bg-slate-700 transition-all shadow-sm">
                <span class="material-symbols-outlined text-[18px]">picture_as_pdf</span>
                In PDF
            </a> --}}

            @if($order->tracking_number)
            {{-- In GHN --}}
            <a href="{{ route('admin.orders.print.ghn', $order) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-[#f4c025]/10 text-[#d4a005] dark:text-[#f4c025] border border-[#f4c025]/30 rounded-xl font-bold text-sm hover:bg-[#f4c025] hover:text-black transition-all shadow-sm">
                <span class="material-symbols-outlined text-[18px]">print</span>
                In GHN
            </a>
            @endif

            {{-- Approve Button --}}
            @if($showApproveBtn)
            <button type="button" onclick="document.getElementById('modal-approve').classList.remove('hidden')"
                    class="inline-flex items-center gap-2 px-5 py-2 bg-emerald-600 text-white rounded-xl font-bold text-sm hover:bg-emerald-700 transition-all shadow-lg shadow-emerald-500/20 active:scale-95">
                <span class="material-symbols-outlined text-[18px]">check_circle</span>
                Chuẩn bị hàng
            </button>
            @endif

            {{-- Cancel Button --}}
            @if($showCancelBtn || $showCancelBtnVnpay)
            <button type="button" onclick="document.getElementById('modal-cancel').classList.remove('hidden')"
                    class="inline-flex items-center gap-2 px-5 py-2 bg-red-50 dark:bg-red-500/10 text-red-600 border border-red-100 dark:border-red-500/20 rounded-xl font-bold text-sm hover:bg-red-600 hover:text-white transition-all shadow-sm active:scale-95">
                <span class="material-symbols-outlined text-[18px]">cancel</span>
                Hủy đơn
            </button>
            @endif

        </div>
    </div>

    {{-- ===== MODAL DUYỆT ĐƠN ===== --}}
    @if($showApproveBtn)
    <div id="modal-approve" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="document.getElementById('modal-approve').classList.add('hidden')"></div>
        <div class="relative bg-white dark:bg-slate-900 rounded-2xl shadow-2xl w-full max-w-md p-6 space-y-5 border border-slate-100 dark:border-slate-800">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-emerald-600 text-[20px]">local_shipping</span>
                </div>
                <div>
                    <h3 class="font-black text-slate-900 dark:text-white text-base">Xác nhận</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Đơn hàng #{{ $order->order_code }}</p>
                </div>
                <button onclick="document.getElementById('modal-approve').classList.add('hidden')" class="ml-auto text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-100 dark:border-emerald-800/30">
                <p class="text-sm text-emerald-800 dark:text-emerald-300">Hệ thống sẽ <strong>tự động tạo vận đơn GHN</strong> và chuyển đơn sang trạng thái <strong>Chờ lấy hàng</strong>.</p>
            </div>
            <form action="{{ route('admin.orders.status.update', $order) }}" method="POST" class="flex gap-3">
                @csrf @method('PATCH')
                <input type="hidden" name="status" value="{{ \App\Models\Order::STATUS_READY_TO_PICK }}">
                <button type="button" onclick="document.getElementById('modal-approve').classList.add('hidden')"
                        class="flex-1 px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-sm font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                    Hủy bỏ
                </button>
                <button type="submit"
                        class="flex-1 px-4 py-2.5 bg-emerald-600 text-white rounded-xl font-black text-sm hover:bg-emerald-700 transition-all active:scale-[0.98] flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-[16px]">check_circle</span>
                    Xác nhận duyệt
                </button>
            </form>
        </div>
    </div>
    @endif

    {{-- ===== MODAL HỦY ĐƠN ===== --}}
    @if($showCancelBtn || $showCancelBtnVnpay)
    <div id="modal-cancel" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="document.getElementById('modal-cancel').classList.add('hidden')"></div>
        <div class="relative bg-white dark:bg-slate-900 rounded-2xl shadow-2xl w-full max-w-md p-6 space-y-5 border border-slate-100 dark:border-slate-800">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-red-600 text-[20px]">cancel</span>
                </div>
                <div>
                    <h3 class="font-black text-slate-900 dark:text-white text-base">Hủy đơn hàng</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Đơn hàng #{{ $order->order_code }}</p>
                </div>
                <button onclick="document.getElementById('modal-cancel').classList.add('hidden')" class="ml-auto text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            
            <form action="{{ route('admin.orders.cancel', $order) }}" method="POST" class="space-y-4">
                @csrf @method('PATCH')
                <div>
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1.5">Lý do hủy đơn <span class="text-red-500">*</span></label>
                    <textarea name="cancellation_reason" rows="3"
                              class="w-full rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm placeholder-slate-400 focus:ring-red-500 focus:border-red-500"
                              placeholder="Nhập lý do hủy đơn..." required></textarea>
                </div>
                <div class="flex gap-3">
                    <button type="button" onclick="document.getElementById('modal-cancel').classList.add('hidden')"
                            class="flex-1 px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-sm font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                        Đóng
                    </button>
                    <button type="submit"
                            class="flex-1 px-4 py-2.5 bg-red-600 text-white rounded-xl font-bold text-sm hover:bg-red-700 transition-all active:scale-[0.98] flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-[16px]">cancel</span>
                        Xác nhận hủy
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    @if (session('status'))
    <div class="p-3 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl text-sm font-semibold flex items-center gap-2">
        <span class="material-symbols-outlined text-[16px]">check_circle</span>
        {{ session('status') }}
    </div>
    @endif

    @if (session('warning'))
    <div class="p-3 bg-amber-50 border border-amber-200 text-amber-800 rounded-xl text-sm font-semibold flex items-center gap-2">
        <span class="material-symbols-outlined text-[16px]">warning</span>
        {{ session('warning') }}
    </div>
    @endif

    @if (session('error'))
    <div class="p-3 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm font-semibold flex items-center gap-2">
        <span class="material-symbols-outlined text-[16px]">error</span>
        {{ session('error') }}
    </div>
    @endif

    @if ($errors->any())
    <div class="p-3 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm">
        <ul class="list-disc pl-5 space-y-1">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    @php
        $step = 0;
        $s = $order->status;
        if(in_array($s, ['pending'])) $step = 1;
        if(in_array($s, ['ready_to_pick', 'picking', 'money_collect_picking', 'picked'])) $step = 2;
        if(in_array($s, ['storing', 'transporting', 'sorting'])) $step = 3;
        if(in_array($s, ['delivering', 'money_collect_delivering'])) $step = 4;
        if(in_array($s, ['delivered', 'received', 'completed'])) $step = 5;

        $progressWidth = '0%';
        if($step == 1) $progressWidth = '0%';
        if($step == 2) $progressWidth = '25%';
        if($step == 3) $progressWidth = '50%';
        if($step == 4) $progressWidth = '75%';
        if($step == 5) $progressWidth = '100%';

        $isFailed = in_array($order->status, ['cancel', 'cancelled', 'delivery_fail', 'waiting_to_return', 'return', 'return_transporting', 'return_sorting', 'returning', 'return_fail', 'returned', 'exception', 'damage', 'lost']);
        
        $histories = $order->statusHistories;
        $time1 = $order->ordered_at ?? $order->created_at;
        $time2 = $histories->whereIn('status', ['ready_to_pick', 'picking', 'money_collect_picking', 'picked'])->first()?->created_at;
        $time3 = $histories->whereIn('status', ['storing', 'transporting', 'sorting'])->first()?->created_at;
        $time4 = $histories->whereIn('status', ['delivering', 'money_collect_delivering'])->first()?->created_at;
        $time5 = $histories->whereIn('status', ['delivered', 'received', 'completed'])->first()?->created_at;
    @endphp

    <div class="bg-white dark:bg-slate-900 p-8 rounded-2xl mb-8 relative overflow-hidden shadow-sm border border-slate-100 dark:border-slate-800">
        <div class="absolute top-0 left-0 w-2 h-full {{ $isFailed ? 'bg-red-500' : 'bg-[#f4c025]' }}"></div>
        
        <div class="flex items-center justify-between mb-10">
            <h2 class="text-lg font-bold uppercase tracking-tight text-slate-900 dark:text-white flex items-center gap-2">
                <span class="material-symbols-outlined text-[#f4c025]">analytics</span> Tiến trình xử lý
            </h2>
            @if(!$isFailed && $order->statusHistories->isNotEmpty())
                <span class="text-[10px] font-bold uppercase tracking-widest text-slate-500 bg-slate-50 dark:bg-slate-800 px-3 py-1.5 rounded-full border border-slate-100 dark:border-slate-700">
                    Cập nhật: {{ $order->statusHistories->first()->created_at->diffForHumans() }}
                </span>
            @endif
        </div>

        <div id="cancelled-view" class="flex items-center gap-6 p-6 rounded-2xl bg-red-50 dark:bg-red-900/20 border border-red-100 dark:border-red-800/30 text-red-600 dark:text-red-400 {{ $isFailed ? '' : 'hidden' }}">
            <div class="w-16 h-16 rounded-full bg-white dark:bg-slate-900 flex items-center justify-center border-4 border-red-500 shadow-xl shadow-red-500/20">
                <span class="material-symbols-outlined text-4xl animate-bounce">error</span>
            </div>
            <div class="flex-1">
                <p class="font-bold text-xl mb-1">{{ in_array($order->status, ['cancel', 'cancelled']) ? 'ĐƠN HÀNG ĐÃ HỦY' : 'GIAO HÀNG THẤT BẠI / HOÀN HÀNG' }}</p>
                <p class="text-sm font-medium text-red-500/80">Lý do: {{ $order->cancellation_reason ?: 'Khách hàng không nhận hàng' }}</p>
            </div>
        </div>

        <div id="normal-progress-view" class="relative flex items-center justify-between py-4 {{ $isFailed ? 'hidden' : '' }}">
            <div class="absolute top-10 left-0 w-full h-2 bg-slate-100 dark:bg-slate-800 z-0 rounded-full">
                <div id="progress-bar-line" class="h-full bg-gradient-to-r from-[#f4c025] to-[#f4c025] transition-all duration-1000 ease-in-out rounded-full shadow-[0_0_15px_rgba(244,192,37,0.4)]" style="width: {{ $progressWidth }}"></div>
            </div>

            <div class="relative z-10 flex flex-col items-center w-1/5">
                <div class="w-14 h-14 rounded-full {{ $step >= 1 ? 'bg-[#f4c025] text-black shadow-[0_0_20px_rgba(244,192,37,0.4)]' : 'bg-slate-100 dark:bg-slate-800 text-slate-400' }} flex items-center justify-center border-4 border-white dark:border-slate-900 transition-all duration-500 mb-2">
                    <span class="material-symbols-outlined text-2xl {{ $step == 1 ? 'animate-pulse' : '' }}">description</span>
                </div>
                <span class="text-[10px] sm:text-[11px] font-bold uppercase tracking-tight {{ $step >= 1 ? 'text-slate-900 dark:text-white' : 'text-slate-400' }} text-center transition-colors duration-500">Đặt đơn</span>
                @if($time1)
                <span class="text-[9px] text-slate-400 mt-0.5">{{ \Carbon\Carbon::parse($time1)->format('d/m H:i') }}</span>
                @endif
            </div>

            <div class="relative z-10 flex flex-col items-center w-1/5">
                <div class="w-14 h-14 rounded-full {{ $step >= 2 ? 'bg-[#f4c025] text-black shadow-[0_0_20px_rgba(244,192,37,0.4)]' : 'bg-slate-100 dark:bg-slate-800 text-slate-400' }} flex items-center justify-center border-4 border-white dark:border-slate-900 transition-all duration-500 mb-2">
                    <span class="material-symbols-outlined text-2xl {{ $step == 2 ? 'animate-pulse' : '' }}">inventory_2</span>
                </div>
                <span class="text-[10px] sm:text-[11px] font-bold uppercase tracking-tight {{ $step >= 2 ? 'text-slate-900 dark:text-white' : 'text-slate-400' }} text-center transition-colors duration-500">Chờ lấy</span>
                @if($time2)
                <span class="text-[9px] text-slate-400 mt-0.5">{{ \Carbon\Carbon::parse($time2)->format('d/m H:i') }}</span>
                @endif
            </div>

            <div class="relative z-10 flex flex-col items-center w-1/5">
                <div class="w-14 h-14 rounded-full {{ $step >= 3 ? 'bg-[#f4c025] text-black shadow-[0_0_20px_rgba(244,192,37,0.4)]' : 'bg-slate-100 dark:bg-slate-800 text-slate-400' }} flex items-center justify-center border-4 border-white dark:border-slate-900 transition-all duration-500 mb-2">
                    <span class="material-symbols-outlined text-2xl {{ $step == 3 ? 'animate-pulse' : '' }}">package_2</span>
                </div>
                <span class="text-[10px] sm:text-[11px] font-bold uppercase tracking-tight {{ $step >= 3 ? 'text-slate-900 dark:text-white' : 'text-slate-400' }} text-center transition-colors duration-500">Vận chuyển</span>
                @if($time3)
                <span class="text-[9px] text-slate-400 mt-0.5">{{ \Carbon\Carbon::parse($time3)->format('d/m H:i') }}</span>
                @endif
            </div>

            <div class="relative z-10 flex flex-col items-center w-1/5">
                <div class="w-14 h-14 rounded-full {{ $step >= 4 ? 'bg-[#f4c025] text-black shadow-[0_0_20px_rgba(244,192,37,0.4)]' : 'bg-slate-100 dark:bg-slate-800 text-slate-400' }} flex items-center justify-center border-4 border-white dark:border-slate-900 transition-all duration-500 mb-2">
                    <span class="material-symbols-outlined text-2xl {{ $step == 4 ? 'animate-pulse' : '' }}">local_shipping</span>
                </div>
                <span class="text-[10px] sm:text-[11px] font-bold uppercase tracking-tight {{ $step >= 4 ? 'text-slate-900 dark:text-white' : 'text-slate-400' }} text-center transition-colors duration-500">Đang giao</span>
                @if($time4)
                <span class="text-[9px] text-slate-400 mt-0.5">{{ \Carbon\Carbon::parse($time4)->format('d/m H:i') }}</span>
                @endif
            </div>

            <div class="relative z-10 flex flex-col items-center w-1/5">
                <div class="w-14 h-14 rounded-full {{ $step >= 5 ? 'bg-[#f4c025] text-black shadow-[0_0_20px_rgba(244,192,37,0.4)]' : 'bg-slate-100 dark:bg-slate-800 text-slate-400' }} flex items-center justify-center border-4 border-white dark:border-slate-900 transition-all duration-500 mb-2">
                    <span class="material-symbols-outlined text-2xl {{ $step == 5 ? 'animate-bounce' : '' }}">check_circle</span>
                </div>
                <span class="text-[10px] sm:text-[11px] font-bold uppercase tracking-tight {{ $step >= 5 ? 'text-slate-900 dark:text-white' : 'text-slate-400' }} text-center transition-colors duration-500">Thành công</span>
                @if($time5)
                <span class="text-[9px] text-slate-400 mt-0.5">{{ \Carbon\Carbon::parse($time5)->format('d/m H:i') }}</span>
                @endif
            </div>
        </div>
    </div>

    <div class="flex flex-col gap-6">
        <div class="space-y-6 w-full">
            {{-- ===== THÔNG TIN ĐƠN HÀNG & NGƯỜI NHẬN (MỖI CARD 1 DÒNG) ===== --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- CARD 1: THÔNG TIN ĐƠN HÀNG --}}
                <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden shadow-sm p-5">
                    <p class="text-[11px] font-black uppercase tracking-widest text-slate-500 dark:text-slate-400 mb-4 pb-2 border-b border-slate-100 dark:border-slate-800">
                        Thông tin đơn hàng
                    </p>
                    <table class="w-full text-sm">
                        <tbody class="divide-y divide-slate-50 dark:divide-slate-800/50">
                            <tr>
                                <td class="py-2 pr-4 text-slate-500 dark:text-slate-400 whitespace-nowrap w-32">Mã đơn hàng:</td>
                                <td class="py-2 font-bold text-primary uppercase tracking-wide">{{ $order->order_code }}</td>
                            </tr>
                            <tr>
                                <td class="py-2 pr-4 text-slate-500 dark:text-slate-400">Ngày đặt hàng:</td>
                                <td class="py-2 font-bold text-slate-900 dark:text-white">
                                    {{ optional($order->ordered_at)->format('d/m/Y H:i') ?? $order->created_at->format('d/m/Y H:i') }}
                                </td>
                            </tr>
                            <tr>
                                <td class="py-2 pr-4 text-slate-500 dark:text-slate-400">Trạng thái đơn:</td>
                                <td class="py-2">
                                    @php
                                        $s = $order->status;
                                        $statusBadge = match(true) {
                                            $s === 'pending'
                                                => 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400',
                                            $s === 'ready_to_pick'
                                                => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
                                            in_array($s, ['picking','money_collect_picking','picked'])
                                                => 'bg-sky-100 text-sky-800 dark:bg-sky-900/30 dark:text-sky-400',
                                            in_array($s, ['storing','transporting','sorting'])
                                                => 'bg-violet-100 text-violet-800 dark:bg-violet-900/30 dark:text-violet-400',
                                            in_array($s, ['delivering','money_collect_delivering'])
                                                => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-400',
                                            $s === 'delivered'
                                                => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400',
                                            $s === 'received'
                                                => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
                                            in_array($s, ['delivery_fail','waiting_to_return','return','return_transporting','return_sorting','returning','return_fail','returned'])
                                                => 'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-400',
                                            in_array($s, ['exception','damage','lost'])
                                                => 'bg-rose-100 text-rose-800 dark:bg-rose-900/30 dark:text-rose-400',
                                            in_array($s, ['cancel','cancelled'])
                                                => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
                                            default => 'bg-slate-100 text-slate-700',
                                        };
                                    @endphp
                                    <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-bold {{ $statusBadge }}">
                                        {{ $statusLabels[$order->status] ?? $order->status }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td class="py-2 pr-4 text-slate-500 dark:text-slate-400">Phương thức thanh toán:</td>
                                <td class="py-2 font-bold text-slate-900 dark:text-white">
                                    @if($order->payment_method == 'cod')
                                        Thanh toán khi nhận (COD)
                                    @elseif($order->payment_method == 'vnpay')
                                        Ví VNPay
                                    @else
                                        {{ $order->payment_method }}
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="py-2 pr-4 text-slate-500 dark:text-slate-400">Tình trạng thanh toán:</td>
                                <td class="py-2">
                                    @php
                                        $payBadge = match($order->payment_status) {
                                            'paid'      => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400',
                                            'pending'   => 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400',
                                            'refunded'  => 'bg-purple-100 text-purple-800',
                                            'cancelled' => 'bg-slate-100 text-slate-600',
                                            default     => 'bg-slate-100 text-slate-700',
                                        };
                                    @endphp
                                    <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-bold {{ $payBadge }}">
                                        {{ $paymentStatusLabels[$order->payment_status] ?? $order->payment_status }}
                                    </span>
                                </td>
                            </tr>
                            @if($order->paid_at)
                            <tr>
                                <td class="py-2 pr-4 text-slate-500 dark:text-slate-400">Thanh toán lúc:</td>
                                <td class="py-2 font-bold text-slate-900 dark:text-white">
                                    {{ \Carbon\Carbon::parse($order->paid_at)->format('d/m/Y H:i') }}
                                </td>
                            </tr>
                            @endif
                            @if($order->tracking_number)
                            <tr>
                                <td class="py-2 pr-4 text-slate-500 dark:text-slate-400">Mã vận đơn:</td>
                                <td class="py-2 font-mono font-bold text-indigo-700 dark:text-indigo-400 text-xs tracking-widest">{{ $order->tracking_number }}</td>
                            </tr>
                            @endif
                            @if($order->note)
                            <tr>
                                <td class="py-2 pr-4 text-slate-500 dark:text-slate-400">Ghi chú:</td>
                                <td class="py-2 font-medium text-amber-700 dark:text-amber-400 italic">{{ $order->note }}</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                {{-- CARD 2: NGƯỜI NHẬN --}}
                <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden shadow-sm p-5">
                    <p class="text-[11px] font-black uppercase tracking-widest text-slate-500 dark:text-slate-400 mb-4 pb-2 border-b border-slate-100 dark:border-slate-800">
                        Người nhận
                    </p>
                    <table class="w-full text-sm">
                        <tbody class="divide-y divide-slate-50 dark:divide-slate-800/50">
                            <tr>
                                <td class="py-2 pr-4 text-slate-500 dark:text-slate-400 whitespace-nowrap w-32">Họ và tên:</td>
                                <td class="py-2 font-bold text-slate-900 dark:text-white">{{ $order->customer_name }}</td>
                            </tr>
                            <tr>
                                <td class="py-2 pr-4 text-slate-500 dark:text-slate-400">Điện thoại:</td>
                                <td class="py-2 font-bold text-slate-900 dark:text-white">{{ $order->customer_phone }}</td>
                            </tr>
                            @if($order->customer_email)
                            <tr>
                                <td class="py-2 pr-4 text-slate-500 dark:text-slate-400">Email:</td>
                                <td class="py-2 text-slate-700 dark:text-slate-300">{{ $order->customer_email }}</td>
                            </tr>
                            @endif
                            <tr>
                                <td class="py-2 pr-4 text-slate-500 dark:text-slate-400 align-top">Địa chỉ:</td>
                                <td class="py-2 font-semibold text-slate-900 dark:text-white leading-relaxed text-xs">{{ $order->shipping_address ?: 'Chưa có địa chỉ' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>


            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-100 dark:border-slate-800 p-6 space-y-4">
                <p class="text-[11px] font-black uppercase tracking-widest text-slate-500 dark:text-slate-400 mb-4 pb-2 border-b border-slate-100 dark:border-slate-800">
                    Thông tin chi tiết ({{ $order->items->sum('quantity') }} sản phẩm)
                </p>

                @if ($order->items->isNotEmpty())
                <div class="overflow-x-auto rounded-xl border border-slate-100 dark:border-slate-800">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-50/50 dark:bg-slate-800/30 border-b border-slate-100 dark:border-slate-800">
                            <tr>
                                <th class="px-4 py-2.5 text-left text-[10px] font-black uppercase tracking-widest text-slate-500">Sản phẩm</th>
                                <th class="px-4 py-2.5 text-left text-[10px] font-black uppercase tracking-widest text-slate-500">Phân loại</th>
                                <th class="px-4 py-2.5 text-center text-[10px] font-black uppercase tracking-widest text-slate-500">Số lượng</th>
                                <th class="px-4 py-2.5 text-right text-[10px] font-black uppercase tracking-widest text-slate-500">Giá</th>
                                <th class="px-4 py-2.5 text-right text-[10px] font-black uppercase tracking-widest text-slate-500">Thành tiền</th>
                            </tr>
                        </thead>
                        @foreach ($order->items as $item)
                        @php
                            $product = $item->product;
                            // Sử dụng tên lưu lúc mua
                            $fullProductName = $item->product_name ?: ($product ? $product->name : '-');
                            
                            $baseName = $fullProductName;
                            $variantInfo = '';
                            
                            // Tách phân loại từ tên (vì CheckoutController lưu dạng: Tên (Màu - Size))
                            if (preg_match('/^(.*?)\s*\((.*)\)$/', $fullProductName, $matches)) {
                                $baseName = $matches[1];
                                $variantInfo = $matches[2];
                            }

                            $productThumbnail = $item->thumbnail ?: ($product ? $product->thumbnail : null);
                            $productSku = $item->product_sku ?: ($product ? $product->sku : '-');
                        @endphp
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            <tr>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3 min-w-[220px]">
                                        <div class="size-12 rounded-lg bg-slate-100 dark:bg-slate-800 overflow-hidden flex items-center justify-center border border-slate-200 dark:border-slate-700 shrink-0">
                                            @if ($productThumbnail)
                                                <img src="{{ str_starts_with($productThumbnail, 'http') ? $productThumbnail : Storage::url($productThumbnail) }}" alt="{{ $baseName }}" class="w-full h-full object-cover" onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden');">
                                                <span class="material-symbols-outlined text-slate-400 text-[18px] hidden">inventory_2</span>
                                            @else
                                                <span class="material-symbols-outlined text-slate-400 text-[18px]">inventory_2</span>
                                            @endif
                                        </div>
                                        <div class="flex-1">
                                            <span class="font-semibold text-slate-900 dark:text-white block">{{ $baseName }}</span>
                                            {{-- <span class="text-xs text-slate-500 block mt-0.5">SKU: {{ $productSku }}</span> --}}
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-slate-600 dark:text-slate-300">
                                    @if($variantInfo)
                                        <span class="px-2 py-1 bg-slate-100 dark:bg-slate-800 rounded text-xs font-medium">{{ $variantInfo }}</span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-slate-700 dark:text-slate-200 font-bold text-center">x{{ $item->quantity }}</td>
                                <td class="px-4 py-3 text-right text-slate-600 dark:text-slate-400 font-medium">{{ number_format($item->unit_price) }} ₫</td>
                                <td class="px-4 py-3 text-right font-black text-slate-900 dark:text-white">{{ number_format($item->line_total) }} ₫</td>
                            </tr>
                         </tbody>
                         @endforeach
                    </table>
                </div>
                @else
                <div class="p-4 rounded-lg border border-dashed border-slate-200 dark:border-slate-700 text-sm text-slate-500">
                    Đơn hàng này chưa có dữ liệu chi tiết sản phẩm.
                </div>
                @endif

                @php
                    $subtotal = $order->items->sum('line_total');
                    $shippingFee = $order->shipping_fee ?? 0;
                    $discountAmount = ($subtotal + $shippingFee) - $order->total_amount;
                    if($discountAmount < 0) $discountAmount = 0;
                @endphp

                <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-800">
                    <table class="w-full text-sm">
                        <tbody class="divide-y divide-slate-50 dark:divide-slate-800/50">
                            <tr>
                                <td class="py-2 pr-4 text-slate-500 dark:text-slate-400 whitespace-nowrap w-44 uppercase text-[10px] font-black tracking-widest">Tiền hàng:</td>
                                <td class="py-2 font-bold text-slate-900 dark:text-white text-right">{{ number_format($subtotal) }} ₫</td>
                            </tr>
                            <tr>
                                <td class="py-2 pr-4 text-slate-500 dark:text-slate-400 uppercase text-[10px] font-black tracking-widest">Phí vận chuyển:</td>
                                <td class="py-2 font-bold text-right {{ $shippingFee <= 0 ? 'text-emerald-600' : 'text-slate-900 dark:text-white' }}">
                                    {{ $shippingFee <= 0 ? 'Miễn phí' : number_format($shippingFee) . ' ₫' }}
                                </td>
                            </tr>
                            @if($discountAmount > 0)
                            <tr>
                                <td class="py-2 pr-4 text-slate-500 dark:text-slate-400 uppercase text-[10px] font-black tracking-widest">Giảm giá:</td>
                                <td class="py-2 font-bold text-red-500 text-right">-{{ number_format($discountAmount) }} ₫</td>
                            </tr>
                            @endif
                            <tr class="bg-slate-50/50 dark:bg-slate-800/30">
                                <td class="py-3 pr-4 font-black text-slate-700 dark:text-slate-200 uppercase text-[11px] tracking-widest">Tổng thanh toán:</td>
                                <td class="py-3 font-black text-primary text-xl text-right">{{ number_format($order->total_amount) }} ₫</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- ===== KHU VỰC YÊU CẦU HOÀN TRẢ (SEPARATE CARD) ===== --}}
            @php $returnRequests = $order->returnRequests()->with(['items.orderItem'])->get(); @endphp
            @if($returnRequests->isNotEmpty())
            <div class="bg-white dark:bg-slate-900 rounded-xl border-2 border-amber-400/50 p-6 space-y-4 shadow-sm shadow-amber-100 dark:shadow-none">
                <p class="text-[11px] font-black uppercase tracking-widest text-amber-600 dark:text-amber-500 mb-4 pb-2 border-b border-amber-100 dark:border-amber-800/30 flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">assignment_return</span>
                    Yêu cầu hoàn trả ({{ $returnRequests->count() }})
                </p>
                <div class="space-y-4">
                        @foreach($returnRequests as $rr)
                        @php
                            $rrStatusColors = [
                                'pending'   => 'bg-amber-100 text-amber-800 border-amber-200 dark:bg-amber-900/30 dark:text-amber-400',
                                'approved'  => 'bg-blue-100 text-blue-800 border-blue-200 dark:bg-blue-900/30 dark:text-blue-400',
                                'rejected'  => 'bg-red-100 text-red-800 border-red-200 dark:bg-red-900/30 dark:text-red-400',
                                'picking'   => 'bg-indigo-100 text-indigo-800 border-indigo-200 dark:bg-indigo-900/30 dark:text-indigo-400',
                                'received'  => 'bg-cyan-100 text-cyan-800 border-cyan-200 dark:bg-cyan-900/30 dark:text-cyan-400',
                                'completed' => 'bg-green-100 text-green-800 border-green-200 dark:bg-green-900/30 dark:text-green-400',
                            ];
                            $rrColor = $rrStatusColors[$rr->status] ?? 'bg-gray-100 text-gray-800';
                        @endphp
                        <div class="border border-slate-200 dark:border-slate-700 rounded-xl overflow-hidden">
                            {{-- Header --}}
                            <div class="px-4 py-3 bg-slate-50 dark:bg-slate-800/50 border-b border-slate-100 dark:border-slate-800">
                                <div class="grid grid-cols-2 md:grid-cols-5 items-center gap-4">
                                    <div>
                                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Mã hoàn trả</p>
                                        <p class="font-bold text-sm text-slate-900 dark:text-white">{{ $rr->return_code }}</p>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Mã vận đơn GHN</p>
                                        @if($rr->tracking_number)
                                            <p class="text-xs font-mono font-bold text-indigo-600 dark:text-indigo-400">{{ $rr->tracking_number }}</p>
                                        @else
                                            <p class="text-xs text-slate-400 italic">Chưa có mã</p>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-0.5">Trạng thái</p>
                                        <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $rrColor }}">
                                            {{ \App\Models\ReturnRequest::statusLabels()[$rr->status] ?? $rr->status }}
                                        </span>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Ngày yêu cầu</p>
                                        <p class="text-xs text-slate-600 dark:text-slate-300 font-medium">{{ $rr->created_at->format('d/m/Y H:i') }}</p>
                                    </div>
                                    <div class="text-right">
                                        <button type="button" 
                                                onclick="document.getElementById('return-details-{{ $rr->id }}').classList.toggle('hidden')"
                                                class="px-3 py-1.5 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-lg text-xs font-bold text-slate-600 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-600 transition-colors flex items-center gap-1 ml-auto">
                                            <span class="material-symbols-outlined text-[16px]">visibility</span>
                                            Xem chi tiết
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div id="return-details-{{ $rr->id }}" class="hidden">

                            <div class="px-4 py-4 space-y-4">
                                {{-- Sản phẩm trong yêu cầu --}}
                                <div class="space-y-2">
                                    @foreach($rr->items as $ri)
                                    <div class="flex items-center justify-between text-sm bg-white dark:bg-slate-900/40 rounded-lg px-3 py-2 border border-slate-100 dark:border-slate-700">
                                        <span class="font-semibold text-slate-800 dark:text-white">{{ $ri->orderItem->product_name ?? '—' }}</span>
                                        <span class="text-slate-500">x{{ $ri->quantity }} &nbsp;|&nbsp; <span class="font-bold text-emerald-600">{{ number_format($ri->refund_amount) }} ₫</span></span>
                                    </div>
                                    @endforeach
                                    <div class="flex flex-col gap-1 px-3 text-xs text-slate-500 text-right">
                                        @php
                                            $rrOriginalTotal = 0;
                                            $rrRefundTotalItems = 0;
                                            foreach($rr->items as $ri) {
                                                // Tính lại giá trị gốc dựa trên line_total / quantity (vì có thể line_total khác unit_price)
                                                $unitPrice = $ri->orderItem->quantity > 0 ? ($ri->orderItem->line_total / $ri->orderItem->quantity) : 0;
                                                $rrOriginalTotal += ($unitPrice * $ri->quantity);
                                                $rrRefundTotalItems += $ri->refund_amount;
                                            }
                                            $rrDiscountDeducted = $rrOriginalTotal - $rrRefundTotalItems;
                                        @endphp

                                        @if($rrDiscountDeducted > 0)
                                        <div class="flex justify-between">
                                            <span>Trừ giảm giá phân bổ:</span>
                                            <span class="font-bold text-red-500">-{{ number_format($rrDiscountDeducted) }} ₫</span>
                                        </div>
                                        @endif

                                        @if($rr->return_shipping_fee > 0)
                                        <div class="flex justify-between">
                                            <span>Trừ phí ship hoàn:</span>
                                            <span class="font-bold text-red-500">-{{ number_format($rr->return_shipping_fee) }} ₫</span>
                                        </div>
                                        @endif
                                        <div class="flex justify-between border-t border-slate-100 dark:border-slate-700 pt-1">
                                            <span>Tổng hoàn dự kiến (Thực nhận):</span>
                                            <span class="font-bold text-emerald-600 text-sm">{{ number_format($rr->total_refund_amount) }} ₫</span>
                                        </div>
                                    </div>
                                </div>

                                {{-- Lý do & Ảnh --}}
                                <div class="flex flex-col gap-3 p-3 bg-white dark:bg-slate-900/40 rounded-lg border border-slate-100 dark:border-slate-700">
                                    <div class="flex flex-wrap gap-2">
                                        @if($rr->images && is_array($rr->images) && count($rr->images) > 0)
                                            @foreach($rr->images as $imgPath)
                                                <a href="{{ asset($imgPath) }}" target="_blank" class="shrink-0">
                                                    <img src="{{ asset($imgPath) }}" class="w-14 h-14 rounded object-cover border border-slate-200">
                                                </a>
                                            @endforeach
                                        @elseif($rr->image)
                                            <a href="{{ asset($rr->image) }}" target="_blank" class="shrink-0">
                                                <img src="{{ asset($rr->image) }}" class="w-14 h-14 rounded object-cover border border-slate-200">
                                            </a>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold text-slate-500 uppercase mb-1">Lý do khách hàng</p>
                                        <p class="text-sm text-slate-800 dark:text-white italic">"{{ $rr->reason }}"</p>
                                    </div>
                                </div>

                                @if($rr->admin_note)
                                <div class="p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800/30 text-sm">
                                    <p class="text-xs font-bold text-blue-600 uppercase mb-1">Ghi chú Admin</p>
                                    <p class="text-blue-900 dark:text-blue-100">{{ $rr->admin_note }}</p>
                                </div>
                                @endif

                                {{-- ===== TIẾN TRÌNH ĐƠN HOÀN ===== --}}
                                @php
                                    $rrIsRejected = $rr->status === 'rejected';

                                    // Tính step (1–5) cho trường hợp bình thường
                                    $rrStep = 1; // pending
                                    if ($rr->status === 'approved')  $rrStep = 2;
                                    if ($rr->status === 'picking')   $rrStep = 3;
                                    if ($rr->status === 'received')  $rrStep = 4;
                                    if ($rr->status === 'completed') $rrStep = 5;

                                    $rrProgressWidth = match($rrStep) {
                                        1 => '0%', 2 => '25%', 3 => '50%', 4 => '75%', 5 => '100%',
                                        default => '0%'
                                    };
                                @endphp
                                <div class="rounded-2xl border border-slate-200 dark:border-slate-700 overflow-hidden mt-3 bg-white dark:bg-slate-900 shadow-sm transition-all hover:shadow-md">
                                    <div class="px-5 py-3 bg-slate-50/80 dark:bg-slate-800/50 border-b border-slate-100 dark:border-slate-700 flex items-center justify-between backdrop-blur-sm">
                                        <div class="flex items-center gap-4">
                                            <div class="flex items-center gap-2">
                                                <div class="size-6 rounded-lg {{ $rrIsRejected ? 'bg-red-100 text-red-500' : 'bg-amber-100 text-amber-600' }} flex items-center justify-center">
                                                    <span class="material-symbols-outlined text-[16px]">{{ $rrIsRejected ? 'block' : 'analytics' }}</span>
                                                </div>
                                                <span class="text-[11px] font-black uppercase tracking-widest text-slate-600 dark:text-slate-300">Tiến trình đơn hoàn</span>
                                            </div>
                                            
                                            @if($rr->tracking_number && !in_array($rr->status, ['received', 'completed']))
                                            <div class="flex items-center gap-4 pl-4 border-l border-slate-200 dark:border-slate-700">
                                                <div class="flex items-center gap-2">
                                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-tight">Vận đơn:</span>
                                                    <span class="text-[10px] font-mono font-bold text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-900/40 px-2 py-1 rounded-md border border-indigo-100 dark:border-indigo-800/50">{{ $rr->tracking_number }}</span>
                                                </div>
                                                
                                                <form action="{{ route('admin.orders.return.tracking.sync', $rr->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="flex items-center gap-1.5 text-[10px] font-bold text-white bg-gradient-to-r from-cyan-600 to-sky-600 hover:from-cyan-700 hover:to-sky-700 px-3 py-1.5 rounded-lg shadow-sm shadow-cyan-200 dark:shadow-none transition-all active:scale-95 group">
                                                        <span class="material-symbols-outlined text-[14px] group-hover:rotate-180 transition-transform duration-500">sync</span>
                                                        ĐỒNG BỘ GHN
                                                    </button>
                                                </form>
                                            </div>
                                            @elseif($rr->tracking_number)
                                            <div class="flex items-center gap-4 pl-4 border-l border-slate-200 dark:border-slate-700">
                                                <div class="flex items-center gap-2">
                                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-tight">Vận đơn:</span>
                                                    <span class="text-[10px] font-mono font-bold text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-900/40 px-2 py-1 rounded-md border border-indigo-100 dark:border-indigo-800/50">{{ $rr->tracking_number }}</span>
                                                </div>
                                            </div>
                                            @endif
                                        </div>
                                        <div class="flex items-center gap-1.5 text-slate-400">
                                            <span class="material-symbols-outlined text-[14px]">calendar_today</span>
                                            <span class="text-[10px] font-medium">{{ $rr->created_at->format('d/m/Y H:i') }}</span>
                                        </div>
                                    </div>
                                    <div class="px-5 py-5">
                                        {{-- Trạng thái từ chối --}}
                                        @if($rrIsRejected)
                                        <div class="flex items-center gap-4 p-4 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-100 dark:border-red-800/30 text-red-600 dark:text-red-400">
                                            <div class="w-12 h-12 rounded-full bg-white dark:bg-slate-900 flex items-center justify-center border-4 border-red-500 shadow-lg shadow-red-500/20 shrink-0">
                                                <span class="material-symbols-outlined text-2xl">block</span>
                                            </div>
                                            <div>
                                                <p class="font-bold text-base">YÊU CẦU ĐÃ BỊ TỪ CHỐI</p>
                                                <p class="text-xs font-medium text-red-500/80 mt-0.5">
                                                    {{ $rr->rejected_at ? $rr->rejected_at->format('d/m/Y H:i') . ' — ' : '' }}
                                                    {{ $rr->admin_note ?: 'Không có ghi chú thêm' }}
                                                </p>
                                            </div>
                                        </div>
                                        @else
                                        {{-- Thanh tiến trình ngang --}}
                                        <div class="relative flex items-center justify-between py-2">
                                            {{-- Đường rail --}}
                                            <div class="absolute top-[28px] left-0 w-full h-2 bg-slate-100 dark:bg-slate-800 z-0 rounded-full">
                                                <div class="h-full bg-gradient-to-r from-[#f4c025] to-[#f4c025] transition-all duration-1000 ease-in-out rounded-full shadow-[0_0_12px_rgba(244,192,37,0.35)]"
                                                     style="width: {{ $rrProgressWidth }}"></div>
                                            </div>

                                            {{-- Bước 1: Yêu cầu --}}
                                            <div class="relative z-10 flex flex-col items-center w-1/5">
                                                <div class="w-12 h-12 rounded-full {{ $rrStep >= 1 ? 'bg-[#f4c025] text-black shadow-[0_0_16px_rgba(244,192,37,0.4)]' : 'bg-slate-100 dark:bg-slate-800 text-slate-400' }} flex items-center justify-center border-4 border-white dark:border-slate-900 transition-all duration-500 mb-2">
                                                    <span class="material-symbols-outlined text-xl {{ $rrStep == 1 ? 'animate-pulse' : '' }}">assignment_return</span>
                                                </div>
                                                <span class="text-[10px] font-bold uppercase tracking-tight {{ $rrStep >= 1 ? 'text-slate-900 dark:text-white' : 'text-slate-400' }} text-center">Yêu cầu</span>
                                                @if($rr->created_at)
                                                <span class="text-[9px] text-slate-400 mt-0.5">{{ $rr->created_at->format('d/m H:i') }}</span>
                                                @endif
                                            </div>

                                            {{-- Bước 2: Duyệt --}}
                                            <div class="relative z-10 flex flex-col items-center w-1/5">
                                                <div class="w-12 h-12 rounded-full {{ $rrStep >= 2 ? 'bg-[#f4c025] text-black shadow-[0_0_16px_rgba(244,192,37,0.4)]' : 'bg-slate-100 dark:bg-slate-800 text-slate-400' }} flex items-center justify-center border-4 border-white dark:border-slate-900 transition-all duration-500 mb-2">
                                                    <span class="material-symbols-outlined text-xl {{ $rrStep == 2 ? 'animate-pulse' : '' }}">how_to_reg</span>
                                                </div>
                                                <span class="text-[10px] font-bold uppercase tracking-tight {{ $rrStep >= 2 ? 'text-slate-900 dark:text-white' : 'text-slate-400' }} text-center">Đã duyệt</span>
                                                @if($rr->approved_at)
                                                <span class="text-[9px] text-slate-400 mt-0.5">{{ $rr->approved_at->format('d/m H:i') }}</span>
                                                @endif
                                            </div>

                                            {{-- Bước 3: Thu hồi --}}
                                            <div class="relative z-10 flex flex-col items-center w-1/5">
                                                <div class="w-12 h-12 rounded-full {{ $rrStep >= 3 ? 'bg-[#f4c025] text-black shadow-[0_0_16px_rgba(244,192,37,0.4)]' : 'bg-slate-100 dark:bg-slate-800 text-slate-400' }} flex items-center justify-center border-4 border-white dark:border-slate-900 transition-all duration-500 mb-2">
                                                    <span class="material-symbols-outlined text-xl {{ $rrStep == 3 ? 'animate-pulse' : '' }}">local_shipping</span>
                                                </div>
                                                <span class="text-[10px] font-bold uppercase tracking-tight {{ $rrStep >= 3 ? 'text-slate-900 dark:text-white' : 'text-slate-400' }} text-center">Thu hồi</span>
                                                @if($rr->tracking_number && $rrStep >= 3)
                                                <span class="text-[9px] text-indigo-500 font-mono mt-0.5">{{ $rr->tracking_number }}</span>
                                                @endif
                                            </div>

                                            {{-- Bước 4: Đã nhận --}}
                                            <div class="relative z-10 flex flex-col items-center w-1/5">
                                                <div class="w-12 h-12 rounded-full {{ $rrStep >= 4 ? 'bg-[#f4c025] text-black shadow-[0_0_16px_rgba(244,192,37,0.4)]' : 'bg-slate-100 dark:bg-slate-800 text-slate-400' }} flex items-center justify-center border-4 border-white dark:border-slate-900 transition-all duration-500 mb-2">
                                                    <span class="material-symbols-outlined text-xl {{ $rrStep == 4 ? 'animate-pulse' : '' }}">inventory_2</span>
                                                </div>
                                                <span class="text-[10px] font-bold uppercase tracking-tight {{ $rrStep >= 4 ? 'text-slate-900 dark:text-white' : 'text-slate-400' }} text-center">Đã nhận</span>
                                                @if($rr->received_at)
                                                <span class="text-[9px] text-slate-400 mt-0.5">{{ $rr->received_at->format('d/m H:i') }}</span>
                                                @endif
                                            </div>

                                            {{-- Bước 5: Hoàn tiền --}}
                                            <div class="relative z-10 flex flex-col items-center w-1/5">
                                                <div class="w-12 h-12 rounded-full {{ $rrStep >= 5 ? 'bg-[#f4c025] text-black shadow-[0_0_16px_rgba(244,192,37,0.4)]' : 'bg-slate-100 dark:bg-slate-800 text-slate-400' }} flex items-center justify-center border-4 border-white dark:border-slate-900 transition-all duration-500 mb-2">
                                                    <span class="material-symbols-outlined text-xl {{ $rrStep == 5 ? 'animate-bounce' : '' }}">account_balance_wallet</span>
                                                </div>
                                                <span class="text-[10px] font-bold uppercase tracking-tight {{ $rrStep >= 5 ? 'text-slate-900 dark:text-white' : 'text-slate-400' }} text-center">Hoàn tiền</span>
                                                @if($rr->completed_at)
                                                <span class="text-[9px] text-slate-400 mt-0.5">{{ $rr->completed_at->format('d/m H:i') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                </div>

                                @php
                                    $rrHistories = $rr->histories;
                                    $rrStatusLabels = \App\Models\ReturnRequestHistory::$statusLabels;
                                    
                                    // Nhóm theo ngày
                                    $rrGrouped = $rrHistories->groupBy(function ($h) {
                                        return $h->created_at->format('Y-m-d');
                                    });
                                    $rrFirstHistoryId = $rrHistories->first()?->id;
                                    
                                    // GHN labels (đã định nghĩa ở phần lịch sử chính, nhưng định nghĩa lại cho chắc ăn)
                                    $ghnStatusLabels = [
                                        'ready_to_pick'            => 'Chờ lấy hàng',
                                        'picking'                  => 'Đang lấy hàng',
                                        'cancel'                   => 'Đã hủy đơn',
                                        'money_collect_picking'    => 'Đang lấy hàng',
                                        'picked'                   => 'Đã lấy hàng',
                                        'storing'                  => 'Nhập kho trung chuyển',
                                        'transporting'             => 'Đang luân chuyển',
                                        'sorting'                  => 'Đang phân loại',
                                        'delivering'               => 'Đang giao hàng',
                                        'money_collect_delivering' => 'Đang giao hàng',
                                        'delivered'                => 'Giao thành công',
                                        'delivery_fail'            => 'Giao thất bại',
                                        'waiting_to_return'        => 'Đang chờ giao lại',
                                        'return'                   => 'Chờ hoàn hàng',
                                        'return_transporting'      => 'Đang luân chuyển hoàn',
                                        'return_sorting'           => 'Đang phân loại hoàn',
                                        'returning'                => 'Đang hoàn hàng',
                                        'return_fail'              => 'Hoàn hàng thất bại',
                                        'returned'                 => 'Đã hoàn hàng',
                                        'exception'                => 'Ngoại lệ',
                                        'damage'                   => 'Hàng bị hỏng',
                                        'lost'                     => 'Hàng bị mất',
                                    ];
                                @endphp
                                <div class="rounded-xl border border-slate-100 dark:border-slate-800 overflow-hidden mt-4">
                                    <div class="px-4 py-3 bg-slate-50 dark:bg-slate-800/40 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
                                        <span class="material-symbols-outlined text-[16px] text-slate-500">history</span>
                                        <span class="text-[10px] font-bold uppercase tracking-widest text-slate-500">Lịch sử xử lý đơn hoàn</span>
                                    </div>
                                    @if($rrHistories->isNotEmpty())
                                    <table class="w-full text-sm">
                                        @foreach ($rrGrouped as $dateStr => $histories)
                                        @php
                                            $dateCarbon = \Carbon\Carbon::parse($dateStr);
                                            $dateLabel  = ucfirst($dateCarbon->locale('vi')->isoFormat('dddd, DD/MM/YYYY'));
                                        @endphp
                                        <thead>
                                            <tr class="{{ !$loop->first ? 'border-t-2 border-slate-50 dark:border-slate-800' : '' }}">
                                                <th class="px-4 py-2.5 text-left font-bold text-slate-600 dark:text-slate-300 text-[11px] bg-slate-50/50 dark:bg-slate-800/20 w-32">{{ $dateLabel }}</th>
                                                <th class="px-3 py-2.5 text-left font-semibold text-slate-400 text-[10px] uppercase tracking-widest bg-slate-50/50 dark:bg-slate-800/20">Chi tiết</th>
                                                <th class="px-4 py-2.5 text-right font-semibold text-slate-400 text-[10px] uppercase tracking-widest bg-slate-50/50 dark:bg-slate-800/20 w-20">Thời gian</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-50 dark:divide-slate-800/40">
                                            @foreach ($histories as $rh)
                                            @php
                                                $isNewest = $rh->id === $rrFirstHistoryId;
                                                $ghnRaw = $rh->ghn_status_raw;
                                                $historyLabel = $ghnRaw 
                                                    ? ($ghnStatusLabels[$ghnRaw] ?? ($rrStatusLabels[$rh->status] ?? $rh->status))
                                                    : ($rrStatusLabels[$rh->status] ?? $rh->status);
                                                
                                                $detailText = $rh->note ?: $rh->ghn_description;
                                                $tc = $isNewest ? 'text-blue-600 dark:text-blue-400' : 'text-slate-600 dark:text-slate-400';
                                                $fw = $isNewest ? 'font-bold' : 'font-semibold';
                                            @endphp
                                            <tr class="bg-white dark:bg-slate-900">
                                                <td class="px-4 py-3 align-top">
                                                    <span class="{{ $tc }} {{ $fw }} text-[12px] uppercase tracking-wider">{{ $historyLabel }}</span>
                                                </td>
                                                <td class="px-3 py-3 align-top">
                                                    <p class="{{ $tc }} text-[12px] leading-relaxed">{{ $detailText }}</p>
                                                    @if($rh->user)
                                                    <p class="text-[10px] text-slate-400 mt-1.5 flex items-center gap-1 font-medium">
                                                        <span class="material-symbols-outlined text-[13px] text-slate-300">person</span>
                                                        {{ $rh->user->name }} (Admin)
                                                    </p>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-3 text-right align-top">
                                                    <span class="{{ $isNewest ? 'text-slate-900 dark:text-white font-bold' : 'text-slate-500 font-medium' }} text-[12px]">{{ $rh->created_at->format('H:i') }}</span>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                        @endforeach
                                    </table>
                                    @else
                                    <p class="px-4 py-3 text-xs text-slate-400 italic">Chưa có lịch sử xử lý.</p>
                                    @endif
                                </div>

                                {{-- Action Panel --}}
                                <div class="bg-white dark:bg-slate-800/80 rounded-xl border border-slate-200 dark:border-slate-700 p-4">
                                    <h5 class="text-sm font-bold text-slate-900 dark:text-white mb-3 flex items-center gap-2">
                                        <span class="material-symbols-outlined text-[16px]">verified_user</span> Xử lý yêu cầu
                                    </h5>

                                    @if($rr->canApprove())
                                        <form action="{{ route('admin.orders.return.approve', $rr->id) }}" method="POST" class="mb-3">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="w-full px-3 py-2 bg-blue-600 text-white rounded-lg font-semibold text-sm hover:bg-blue-700 flex items-center justify-center gap-1">
                                                <span class="material-symbols-outlined text-[16px]">how_to_reg</span> Duyệt đơn
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.orders.return.reject', $rr->id) }}" method="POST" class="pt-3 border-t border-slate-100 dark:border-slate-700" onsubmit="return confirm('Từ chối yêu cầu này?')">
                                            @csrf @method('PATCH')
                                            <textarea name="return_admin_note" rows="2" class="w-full text-sm rounded-lg border-slate-200 dark:border-slate-600 dark:bg-slate-900/50 dark:text-white placeholder-slate-400 mb-2 focus:ring-red-500 focus:border-red-500" placeholder="Lý do từ chối (bắt buộc)..." required></textarea>
                                            <button type="submit" class="w-full px-3 py-2 bg-red-50 text-red-600 border border-red-200 rounded-lg font-semibold text-sm hover:bg-red-100 flex items-center justify-center gap-1">
                                                <span class="material-symbols-outlined text-[16px]">cancel</span> Từ chối
                                            </button>
                                        </form>
                                    @elseif($rr->canMarkReceived())
                                        @php $isDelivered = $rr->isGhnDelivered(); @endphp

                                        @if(!$isDelivered)
                                        <div class="p-3 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800/30 rounded-lg mb-3">
                                            <div class="flex items-center gap-2 text-amber-800 dark:text-amber-400 font-bold mb-1">
                                                <span class="material-symbols-outlined text-[18px]">info</span>
                                                <span class="text-xs uppercase tracking-wider">Chờ GHN giao hàng</span>
                                            </div>
                                            <p class="text-[11px] text-amber-700 dark:text-amber-500 leading-relaxed">
                                                Hệ thống chưa ghi nhận trạng thái <b>Giao thành công</b>
                                            </p>
                                        </div>
                                        @endif

                                        {{-- <form action="{{ route('admin.orders.return.received', $rr->id) }}" method="POST">
                                            @csrf @method('PATCH')
                                            <textarea name="return_admin_note" rows="2" class="w-full text-sm rounded-lg border-slate-200 dark:border-slate-600 dark:bg-slate-900/50 dark:text-white placeholder-slate-400 mb-2 {{ !$isDelivered ? 'opacity-50' : '' }}" placeholder="Tình trạng hàng sau khi nhận..." {{ !$isDelivered ? 'disabled' : '' }}></textarea>
                                            <button type="submit" 
                                                {{ !$isDelivered ? 'disabled' : '' }}
                                                class="w-full px-3 py-2 {{ $isDelivered ? 'bg-cyan-600 hover:bg-cyan-700' : 'bg-slate-400 cursor-not-allowed' }} text-white rounded-lg font-semibold text-sm flex items-center justify-center gap-1 transition-all">
                                                <span class="material-symbols-outlined text-[16px]">inventory_2</span> 
                                                {{ $isDelivered ? 'Xác nhận đã nhận hàng hoàn' : 'Chờ giao hàng hoàn...' }}
                                            </button>
                                        </form> --}}
                                    @elseif($rr->canRefund())
                                        @php $isDelivered = $rr->isGhnDelivered(); @endphp
                                        <form action="{{ route('admin.orders.return.refund', $rr->id) }}" method="POST" onsubmit="return confirm('Hoàn tiền vào ví khách? Không thể hoàn tác.')">
                                            @csrf @method('PATCH')
                                            <div class="p-3 bg-emerald-50 dark:bg-emerald-900/20 rounded-lg border border-emerald-100 dark:border-emerald-800/30 mb-3">
                                                <p class="text-sm text-emerald-800 dark:text-emerald-300">Đã nhận hàng thành công. Vui lòng hoàn tiền cho khách.</p>
                                                @if($rr->return_shipping_fee > 0)
                                                <div class="flex justify-between items-end mt-2 pt-2 border-t border-emerald-200/50">
                                                    <span class="text-xs text-red-500 font-bold uppercase">Trừ phí ship hoàn</span>
                                                    <span class="text-sm font-bold text-red-500">-{{ number_format($rr->return_shipping_fee) }} ₫</span>
                                                </div>
                                                @endif
                                                <div class="flex justify-between items-end mt-2 pt-2 border-t border-emerald-200/50">
                                                    <span class="text-xs text-emerald-600 font-bold uppercase">Số tiền hoàn (thực tế)</span>
                                                    <span class="text-lg font-black text-emerald-700">{{ number_format($rr->total_refund_amount) }} ₫</span>
                                                </div>
                                            </div>

                                            @if(!$isDelivered)
                                                <div class="p-3 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800/30 rounded-lg mb-3">
                                                    <div class="flex items-center gap-2 text-amber-800 dark:text-amber-400 font-bold mb-1">
                                                        <span class="material-symbols-outlined text-[18px]">warning</span>
                                                        <span class="text-xs uppercase tracking-wider">Cảnh báo: Chưa giao tới</span>
                                                    </div>
                                                    <p class="text-[11px] text-amber-700 dark:text-amber-500 leading-relaxed">
                                                        Mặc dù đã xác nhận nhận hàng, nhưng GHN chưa báo trạng thái <b>Giao thành công</b>. Bạn vẫn có thể hoàn tiền nhưng nên kiểm tra kỹ.
                                                    </p>
                                                </div>
                                            @endif

                                            <button type="submit" class="w-full px-3 py-2 bg-emerald-600 text-white rounded-lg font-semibold text-sm hover:bg-emerald-700 flex items-center justify-center gap-1">
                                                <span class="material-symbols-outlined text-[16px]">account_balance_wallet</span> Hoàn tiền vào Ví Bee Pay
                                            </button>
                                        </form>
                                    @elseif($rr->status === 'rejected')
                                        <div class="text-sm text-red-600 dark:text-red-400 flex items-center gap-2 p-3 bg-red-50 dark:bg-red-900/20 rounded-lg">
                                            <span class="material-symbols-outlined shrink-0 text-[18px]">block</span>
                                            <span>Yêu cầu hoàn trả này đã bị từ chối.</span>
                                        </div>
                                    @elseif($rr->status === 'completed')
                                        <div class="text-sm text-emerald-700 dark:text-emerald-300 flex items-start gap-2 p-3 bg-emerald-50 dark:bg-emerald-900/20 rounded-lg border border-emerald-100">
                                            <span class="material-symbols-outlined shrink-0 text-[18px] text-emerald-500">check_circle</span>
                                            <div>
                                                <p class="font-bold">Đã hoàn tất. Tiền đã được hoàn vào ví.</p>
                                                @if($rr->return_shipping_fee > 0)
                                                <p class="text-xs text-red-500 mt-1">Trừ phí ship hoàn: <span class="font-bold">-{{ number_format($rr->return_shipping_fee) }} ₫</span></p>
                                                @endif
                                                <p class="text-xs mt-1">Thực nhận: <span class="font-bold text-emerald-600">{{ number_format($rr->total_refund_amount) }} ₫</span></p>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                    </div>
            </div>
            @endif

            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-100 dark:border-slate-800 overflow-hidden">
                <div class="p-5 border-b border-slate-100 dark:border-slate-800">
                    <p class="text-[11px] font-black uppercase tracking-widest text-slate-500 dark:text-slate-400">
                        Lịch sử đơn hàng
                    </p>
                </div>

                @php
                    // Map GHN status code → tên tiếng Việt chi tiết
                    $ghnStatusLabels = [
                        'ready_to_pick'            => 'Chờ lấy hàng',
                        'picking'                  => 'Đang lấy hàng',
                        'cancel'                   => 'Đã hủy đơn',
                        'money_collect_picking'    => 'Đang lấy hàng',
                        'picked'                   => 'Đã lấy hàng',
                        'storing'                  => 'Nhập kho trung chuyển',
                        'transporting'             => 'Đang luân chuyển',
                        'sorting'                  => 'Đang phân loại',
                        'delivering'               => 'Đang giao hàng',
                        'money_collect_delivering' => 'Đang giao hàng',
                        'delivered'                => 'Giao thành công',
                        'delivery_fail'            => 'Giao thất bại',
                        'waiting_to_return'        => 'Đang chờ giao lại',
                        'return'                   => 'Chờ hoàn hàng',
                        'return_transporting'      => 'Đang luân chuyển hoàn',
                        'return_sorting'           => 'Đang phân loại hoàn',
                        'returning'                => 'Đang hoàn hàng',
                        'return_fail'              => 'Hoàn hàng thất bại',
                        'returned'                 => 'Đã hoàn hàng',
                        'exception'                => 'Ngoại lệ',
                        'damage'                   => 'Hàng bị hỏng',
                        'lost'                     => 'Hàng bị mất',
                    ];

                    // Nhóm lịch sử theo ngày (giảm dần)
                    $grouped = $order->statusHistories->groupBy(function ($h) {
                        return $h->created_at->format('Y-m-d');
                    });
                    $firstHistoryId = $order->statusHistories->first()?->id;
                @endphp

                @if ($order->statusHistories->isNotEmpty())
                <table class="w-full text-sm">
                    @foreach ($grouped as $dateStr => $histories)
                    @php
                        $dateCarbon = \Carbon\Carbon::parse($dateStr);
                        $dateLabel  = $dateCarbon->locale('vi')->isoFormat('dddd, DD/MM/YYYY');
                        $dateLabel  = ucfirst($dateLabel);
                    @endphp
                    {{-- ── Hàng header ngày ── --}}
                    <thead>
                        <tr class="{{ !$loop->first ? 'border-t-4 border-slate-100 dark:border-slate-800' : '' }}">
                            <th class="px-6 py-3 text-left font-bold text-slate-700 dark:text-slate-200 text-[13px] bg-slate-50 dark:bg-slate-800/50 w-52">{{ $dateLabel }}</th>
                            <th class="px-4 py-3 text-left font-semibold text-slate-500 dark:text-slate-400 text-[13px] bg-slate-50 dark:bg-slate-800/50">Chi tiết</th>
                            <th class="px-6 py-3 text-right font-semibold text-slate-500 dark:text-slate-400 text-[13px] bg-slate-50 dark:bg-slate-800/50 w-28">Thời gian</th>
                        </tr>
                    </thead>
                    {{-- ── Các bước trong ngày ── --}}
                    <tbody class="divide-y divide-slate-50 dark:divide-slate-800/60">
                        @foreach ($histories as $history)
                        @php
                            $isNewest = $history->id === $firstHistoryId;

                            // Cột TIÊU ĐỀ (xanh): dùng ghnStatusLabels (tiêu đề ngắn) → fallback label nội bộ
                            $ghnRaw       = $history->ghn_status_raw;
                            $historyLabel = $ghnRaw
                                ? ($ghnStatusLabels[$ghnRaw] ?? ($statusLabels[$history->status] ?? $history->status))
                                : ($statusLabels[$history->status] ?? $history->status);

                            // Cột CHI TIẾT (giữa): ưu tiên note (câu thân thiện đã build) → fallback ghn_description thô
                            $detailText = $history->note ?: $history->ghn_description;

                            // Màu: chỉ entry mới nhất = xanh
                            $tc = $isNewest ? 'text-[#1a6fc4] dark:text-blue-400' : 'text-slate-600 dark:text-slate-400';
                            $fw = $isNewest ? 'font-semibold' : 'font-normal';
                        @endphp
                        <tr class="bg-white dark:bg-slate-900">
                            <td class="px-6 py-3 align-top">
                                <span class="{{ $tc }} {{ $fw }} text-[13px] leading-snug">{{ $historyLabel }}</span>
                            </td>
                            <td class="px-4 py-3 align-top">
                                @if ($detailText)
                                    <span class="{{ $tc }} text-[13px] leading-relaxed">{{ $detailText }}</span>
                                @else
                                    <span class="text-slate-400 text-[13px]">–</span>
                                @endif
                            </td>
                            <td class="px-6 py-3 text-right align-top whitespace-nowrap">
                                <span class="{{ $tc }} {{ $fw }} text-[13px]">{{ $history->created_at->format('H:i') }}</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    @endforeach
                </table>
                @else
                <p class="px-6 py-4 text-sm text-slate-500 italic">Chưa có lịch sử cập nhật.</p>
                @endif
            </div>


        </div>

        {{-- ===== CÁC THAO TÁC KHÁC ===== --}}
        <div class="space-y-6 w-full">
            {{-- CARD TẠO VẬN ĐƠN GHN THỦ CÔNG --}}
            @if($order->status === \App\Models\Order::STATUS_READY_TO_PICK && !$order->tracking_number)
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-orange-200 dark:border-orange-700/40 p-5 space-y-4">
                <p class="text-[11px] font-black uppercase tracking-widest text-orange-500 pb-2 border-b border-orange-100 dark:border-orange-800/30">
                    ⚠ Chưa có vận đơn GHN
                </p>
                <p class="text-xs text-slate-500 dark:text-slate-400">
                    Hệ thống không thể tự động tạo vận đơn GHN (thiếu mã vùng địa chỉ). Vui lòng tạo vận đơn thủ công trên GHN rồi nhập mã vận đơn vào đây.
                </p>
                <form action="{{ route('admin.orders.tracking.update', $order) }}" method="POST" class="space-y-3">
                    @csrf @method('PATCH')
                    <input type="text" name="tracking_number" placeholder="VD: GHN12345678"
                        class="w-full rounded-lg border-orange-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm font-mono placeholder-slate-400 focus:ring-orange-400 focus:border-orange-400">
                    <button type="submit" class="w-full px-4 py-2.5 bg-orange-500 text-white rounded-xl font-bold text-sm hover:bg-orange-600 transition-colors flex items-center justify-center gap-1.5">
                        <span class="material-symbols-outlined text-[16px]">save</span>
                        Lưu mã vận đơn
                    </button>
                </form>
            </div>
            @endif

            {{-- CARD XỬ LÝ BOM HÀNG (giao thất bại + đã thanh toán) --}}
            @if ($order->status === \App\Models\Order::STATUS_DELIVERY_FAIL && $order->payment_status === 'paid')
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-pink-200 dark:border-pink-800/30 p-5 space-y-4">
                <p class="text-[11px] font-black uppercase tracking-widest text-pink-500 mb-2 pb-2 border-b border-pink-100 dark:border-pink-800/30">
                    Xử lý giao thất bại
                </p>
                <div class="rounded-xl border border-pink-200 bg-pink-50 dark:bg-pink-500/10 p-4">
                    <p class="text-sm font-bold text-pink-800 dark:text-pink-300 mb-2">Giao thất bại (Đã thanh toán trước)</p>
                    <p class="text-xs text-pink-700 dark:text-pink-400 mt-1 mb-4">Nhấn nút bên dưới để xác nhận tự động nhập lại kho các sản phẩm, và hoàn <strong>{{ number_format($order->total_amount) }}₫</strong> vào Ví Bee Pay của khách.</p>
                    <form action="{{ route('admin.orders.refund.failed', $order) }}" method="POST">
                        @csrf @method('PATCH')
                        <button type="submit" class="w-full px-4 py-2 bg-pink-600 text-white rounded-lg font-semibold text-sm hover:bg-pink-700"
                                onclick="return confirm('Bạn chắc chắn muốn hoàn tiền {{ number_format($order->total_amount) }}₫ vào ví khách hàng và cộng lại tồn kho cho đơn hàng này?')">
                            Hoàn tiền &amp; Nhập kho
                        </button>
                    </form>
                </div>
            </div>
            @endif
        </div>
        {{-- ===== KẾT THÚC SIDEBAR ===== --}}

    </div>
</div>
@endsection
