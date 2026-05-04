@extends('client.profiles.layouts.app')

@section('title', 'Bee Phone - Chi tiết đơn hàng #' . $order->order_code)

@section('profile_content')
    <section class="flex-1" data-purpose="user-main-section">

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="mb-6 p-4 rounded-xl bg-green-50 dark:bg-green-500/10 border border-green-200 dark:border-green-500/20 flex items-start gap-3 animate-fade-in-up">
                <span class="material-symbols-outlined text-green-500 mt-0.5">check_circle</span>
                <div>
                    <h4 class="font-bold text-green-800 dark:text-green-400 text-sm">Thành công</h4>
                    <p class="text-sm text-green-700 dark:text-green-500 mt-0.5">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 rounded-xl bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/20 flex items-start gap-3 animate-fade-in-up">
                <span class="material-symbols-outlined text-red-500 mt-0.5">error</span>
                <div>
                    <h4 class="font-bold text-red-800 dark:text-red-400 text-sm">Có lỗi xảy ra</h4>
                    <p class="text-sm text-red-700 dark:text-red-500 mt-0.5">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 p-4 rounded-xl bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/20 flex items-start gap-3 animate-fade-in-up">
                <span class="material-symbols-outlined text-red-500 mt-0.5">error</span>
                <div>
                    <h4 class="font-bold text-red-800 dark:text-red-400 text-sm">Vui lòng kiểm tra lại thông tin</h4>
                    <ul class="text-sm text-red-700 dark:text-red-500 list-disc list-inside mt-1 space-y-0.5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <div class="mb-6 flex items-center justify-between border-b border-gray-100 dark:border-white/10 pb-4">
            <div class="flex items-center gap-3">
                <a href="{{ route('client.orders.index') }}" class="w-10 h-10 flex items-center justify-center rounded-xl bg-gray-100 dark:bg-white/5 hover:bg-[#f4c025] hover:text-black transition-colors text-gray-600 dark:text-gray-300">
                    <span class="material-symbols-outlined">arrow_back</span>
                </a>
                <div>
                    <h1 class="text-2xl font-bold uppercase tracking-tight text-[#181611] dark:text-white">Chi tiết đơn hàng</h1>
                    @php
                        $sBadge = match(true) {
                            $order->status === 'pending' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400',
                            $order->status === 'ready_to_pick' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
                            in_array($order->status, ['picking','money_collect_picking','picked']) => 'bg-sky-100 text-sky-800 dark:bg-sky-900/30 dark:text-sky-400',
                            in_array($order->status, ['storing','transporting','sorting']) => 'bg-violet-100 text-violet-800 dark:bg-violet-900/30 dark:text-violet-400',
                            in_array($order->status, ['delivering','money_collect_delivering']) => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-400',
                            $order->status === 'delivered' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400',
                            $order->status === 'received' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
                            in_array($order->status, ['delivery_fail','waiting_to_return','return','return_transporting','return_sorting','returning','return_fail','returned']) => 'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-400',
                            in_array($order->status, ['exception','damage','lost']) => 'bg-rose-100 text-rose-800 dark:bg-rose-900/30 dark:text-rose-400',
                            in_array($order->status, ['cancel','cancelled']) => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
                            default => 'bg-gray-100 text-gray-700 dark:bg-white/10 dark:text-gray-300',
                        };
                    @endphp
                    <div class="mt-1 flex flex-wrap items-center gap-2">
                        <p class="text-sm text-gray-500 dark:text-gray-400 flex items-center gap-2">
                            <span>Mã đơn: <span class="font-bold text-[#f4c025]">#{{ $order->order_code }}</span></span>
                            @if($order->tracking_number)
                                <span class="w-1 h-1 rounded-full bg-gray-300 dark:bg-white/20"></span>
                                <span>Vận đơn: <span class="font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider">{{ $order->tracking_number }}</span></span>
                            @endif
                        </p>
                        <span class="w-1 h-1 rounded-full bg-gray-300 dark:bg-white/20 hidden sm:inline-block"></span>
                        <span class="inline-block px-2 py-0.5 rounded-md text-[11px] font-bold {{ $sBadge }}">
                            {{ \App\Models\Order::statusLabels()[$order->status] ?? $order->status }}
                        </span>
                    </div>
                </div>
            </div>

            <div id="order-status-badge" class="flex flex-wrap items-center gap-3">
                @php $s = $order->status; @endphp
                
                @if($s !== 'cancelled' && $s !== 'cancel' && $order->payment_status === 'pending' && in_array($order->payment_method, ['vnpay', 'vnp']))
                    <a href="{{ route('client.checkout.retry', $order->id) }}" class="px-5 py-2.5 rounded-xl bg-blue-50 dark:bg-blue-500/10 text-blue-600 border border-blue-100 dark:border-blue-500/20 hover:bg-blue-600 hover:text-white transition-all font-bold text-sm flex items-center gap-2 shadow-sm inline-flex">
                        <span class="material-symbols-outlined text-[20px]">payment</span> Thanh toán lại
                    </a>
                @endif

                @if(in_array($s, ['pending', 'ready_to_pick', 'picking', 'money_collect_picking', 'picked']))
                    <button type="button" onclick="document.getElementById('order-action-modal').classList.remove('hidden'); document.getElementById('order-action-modal').classList.add('flex');" class="px-5 py-2.5 rounded-xl bg-red-50 dark:bg-red-500/10 text-red-600 border border-red-100 dark:border-red-500/20 hover:bg-red-600 hover:text-white transition-all font-bold text-sm flex items-center gap-2 shadow-sm">
                        <span class="material-symbols-outlined text-[20px]">cancel</span> Hủy đơn hàng
                    </button>
                @elseif($s == 'delivered')
                    <form action="{{ route('client.orders.confirm', $order->id) }}" method="POST" class="inline"
                          onsubmit="return confirm('Bạn xác nhận đã nhận được hàng?')">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-green-500 text-white hover:bg-green-600 transition-all font-bold text-sm flex items-center gap-2 shadow-lg shadow-green-500/20">
                            <span class="material-symbols-outlined text-[20px]">check_circle</span> Xác nhận đã nhận
                        </button>
                    </form>
                @endif
            </div>
        </div>

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

        <section class="bg-white dark:bg-[#1a1a1a] p-8 rounded-3xl mb-8 relative overflow-hidden shadow-sm border border-gray-100 dark:border-white/10" id="timeline-section">
            <div class="absolute top-0 left-0 w-2 h-full {{ $isFailed ? 'bg-red-500' : 'bg-[#f4c025]' }}" id="timeline-border-color"></div>
            
            <div class="flex items-center justify-between mb-10">
                <h2 class="text-lg font-bold uppercase tracking-tight text-[#181611] dark:text-white flex items-center gap-2">
                    <span class="material-symbols-outlined text-[#f4c025]">analytics</span> Tiến trình xử lý
                </h2>
                @if(!$isFailed && $order->statusHistories->isNotEmpty())
                    <span class="text-[10px] font-bold uppercase tracking-widest text-gray-400 bg-gray-50 dark:bg-white/5 px-3 py-1.5 rounded-full border border-gray-100 dark:border-white/10">
                        Cập nhật: {{ $order->statusHistories->first()->created_at->diffForHumans() }}
                    </span>
                @endif
            </div>

            <div id="cancelled-view" class="flex items-center gap-6 p-6 rounded-2xl bg-red-50/50 dark:bg-red-500/5 border border-red-100 dark:border-red-500/10 text-red-500 {{ $isFailed ? '' : 'hidden' }}">
                <div class="w-16 h-16 rounded-full bg-white dark:bg-[#1a1a1a] flex items-center justify-center border-4 border-red-500 shadow-xl shadow-red-500/20">
                    <span class="material-symbols-outlined text-4xl animate-bounce">error</span>
                </div>
                <div class="flex-1">
                    <p class="font-bold text-xl mb-1">{{ in_array($order->status, ['cancel', 'cancelled']) ? 'ĐƠN HÀNG ĐÃ HỦY' : 'GIAO HÀNG THẤT BẠI' }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400 font-medium" id="cancel-reason-text">Lý do: {{ $order->cancellation_reason ?: '' }}</p>
                    
                    @if(in_array($order->status, ['delivery_fail']))
                        <form action="{{ route('client.orders.redeliver', $order->id) }}" method="POST" class="mt-4">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="px-6 py-3 bg-red-500 text-white font-bold rounded-xl text-sm hover:bg-red-600 shadow-lg shadow-red-500/20 inline-flex items-center gap-2 transition active:scale-95" onclick="return confirm('Bạn có chắc muốn yêu cầu giao lại đơn hàng này?')">
                                <span class="material-symbols-outlined text-[18px]">local_shipping</span> Yêu cầu giao lại ngay
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <div id="normal-progress-view" class="relative flex items-center justify-between py-4 {{ $isFailed ? 'hidden' : '' }}">
                <div class="absolute top-10 left-0 w-full h-2 bg-gray-100 dark:bg-white/5 z-0 rounded-full">
                    <div id="progress-bar-line" class="h-full bg-gradient-to-r from-[#f4c025] to-[#f4c025] transition-all duration-1000 ease-in-out rounded-full shadow-[0_0_15px_rgba(244,192,37,0.4)]" style="width: {{ $progressWidth }}"></div>
                </div>

                <div class="relative z-10 flex flex-col items-center w-1/5">
                    <div id="step-icon-1" class="step-icon w-14 h-14 rounded-full {{ $step >= 1 ? 'bg-[#f4c025] text-black shadow-[0_0_20px_rgba(244,192,37,0.4)]' : 'bg-gray-100 dark:bg-white/5 text-gray-400' }} flex items-center justify-center border-4 border-white dark:border-[#1a1a1a] transition-all duration-500 mb-2">
                        <span class="material-symbols-outlined text-2xl {{ $step == 1 ? 'animate-pulse' : '' }}">description</span>
                    </div>
                    <span id="step-text-1" class="text-[10px] sm:text-[11px] font-bold uppercase tracking-tight {{ $step >= 1 ? 'text-[#181611] dark:text-white' : 'text-gray-400' }} text-center transition-colors duration-500">Đặt đơn</span>
                    @if($time1)
                    <span class="text-[9px] text-gray-400 mt-0.5">{{ \Carbon\Carbon::parse($time1)->format('d/m H:i') }}</span>
                    @endif
                </div>

                <div class="relative z-10 flex flex-col items-center w-1/5">
                    <div id="step-icon-2" class="step-icon w-14 h-14 rounded-full {{ $step >= 2 ? 'bg-[#f4c025] text-black shadow-[0_0_20px_rgba(244,192,37,0.4)]' : 'bg-gray-100 dark:bg-white/5 text-gray-400' }} flex items-center justify-center border-4 border-white dark:border-[#1a1a1a] transition-all duration-500 mb-2">
                        <span class="material-symbols-outlined text-2xl {{ $step == 2 ? 'animate-pulse' : '' }}">inventory_2</span>
                    </div>
                    <span id="step-text-2" class="text-[10px] sm:text-[11px] font-bold uppercase tracking-tight {{ $step >= 2 ? 'text-[#181611] dark:text-white' : 'text-gray-400' }} text-center transition-colors duration-500">Chờ lấy</span>
                    @if($time2)
                    <span class="text-[9px] text-gray-400 mt-0.5">{{ \Carbon\Carbon::parse($time2)->format('d/m H:i') }}</span>
                    @endif
                </div>

                <div class="relative z-10 flex flex-col items-center w-1/5">
                    <div id="step-icon-3" class="step-icon w-14 h-14 rounded-full {{ $step >= 3 ? 'bg-[#f4c025] text-black shadow-[0_0_20px_rgba(244,192,37,0.4)]' : 'bg-gray-100 dark:bg-white/5 text-gray-400' }} flex items-center justify-center border-4 border-white dark:border-[#1a1a1a] transition-all duration-500 mb-2">
                        <span class="material-symbols-outlined text-2xl {{ $step == 3 ? 'animate-pulse' : '' }}">package_2</span>
                    </div>
                    <span id="step-text-3" class="text-[10px] sm:text-[11px] font-bold uppercase tracking-tight {{ $step >= 3 ? 'text-[#181611] dark:text-white' : 'text-gray-400' }} text-center transition-colors duration-500">Vận chuyển</span>
                    @if($time3)
                    <span class="text-[9px] text-gray-400 mt-0.5">{{ \Carbon\Carbon::parse($time3)->format('d/m H:i') }}</span>
                    @endif
                </div>

                <div class="relative z-10 flex flex-col items-center w-1/5">
                    <div id="step-icon-4" class="step-icon w-14 h-14 rounded-full {{ $step >= 4 ? 'bg-[#f4c025] text-black shadow-[0_0_20px_rgba(244,192,37,0.4)]' : 'bg-gray-100 dark:bg-white/5 text-gray-400' }} flex items-center justify-center border-4 border-white dark:border-[#1a1a1a] transition-all duration-500 mb-2">
                        <span class="material-symbols-outlined text-2xl {{ $step == 4 ? 'animate-pulse' : '' }}">local_shipping</span>
                    </div>
                    <span id="step-text-4" class="text-[10px] sm:text-[11px] font-bold uppercase tracking-tight {{ $step >= 4 ? 'text-[#181611] dark:text-white' : 'text-gray-400' }} text-center transition-colors duration-500">Đang giao</span>
                    @if($time4)
                    <span class="text-[9px] text-gray-400 mt-0.5">{{ \Carbon\Carbon::parse($time4)->format('d/m H:i') }}</span>
                    @endif
                </div>

                <div class="relative z-10 flex flex-col items-center w-1/5">
                    <div id="step-icon-5" class="step-icon w-14 h-14 rounded-full {{ $step >= 5 ? 'bg-[#f4c025] text-black shadow-[0_0_20px_rgba(244,192,37,0.4)]' : 'bg-gray-100 dark:bg-white/5 text-gray-400' }} flex items-center justify-center border-4 border-white dark:border-[#1a1a1a] transition-all duration-500 mb-2">
                        <span class="material-symbols-outlined text-2xl {{ $step == 5 ? 'animate-bounce' : '' }}">check_circle</span>
                    </div>
                    <span id="step-text-5" class="text-[10px] sm:text-[11px] font-bold uppercase tracking-tight {{ $step >= 5 ? 'text-[#181611] dark:text-white' : 'text-gray-400' }} text-center transition-colors duration-500">Thành công</span>
                    @if($time5)
                    <span class="text-[9px] text-gray-400 mt-0.5">{{ \Carbon\Carbon::parse($time5)->format('d/m H:i') }}</span>
                    @endif
                </div>
            </div>
        </section>


        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">

            <div class="lg:col-span-3 grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- THÔNG TIN ĐƠN HÀNG --}}
                <div class="bg-white dark:bg-[#1a1a1a] rounded-2xl shadow-sm border border-gray-100 dark:border-white/10 overflow-hidden p-6 h-full">
                    <p class="text-[11px] font-bold text-gray-500 uppercase tracking-widest mb-4 pb-4 border-b border-gray-100 dark:border-white/10 flex items-center gap-2">
                        <span class="material-symbols-outlined text-[18px] text-[#f4c025]">info</span>
                        Thông tin đơn hàng
                    </p>
                    <table class="w-full text-sm">
                        <tbody class="divide-y divide-gray-50 dark:divide-white/5">
                            <tr>
                                <td class="py-3 pr-4 text-gray-500 dark:text-gray-400 whitespace-nowrap w-32">Mã đơn hàng:</td>
                                <td class="py-3 font-bold text-[#f4c025] uppercase tracking-wide">{{ $order->order_code }}</td>
                            </tr>
                            @if($order->tracking_number)
                            <tr>
                                <td class="py-3 pr-4 text-gray-500 dark:text-gray-400">Mã vận đơn:</td>
                                <td class="py-3 font-mono font-bold text-indigo-700 dark:text-indigo-400 text-xs tracking-widest">{{ $order->tracking_number }}</td>
                            </tr>
                            @endif
                            <tr>
                                <td class="py-3 pr-4 text-gray-500 dark:text-gray-400">Ngày đặt hàng:</td>
                                <td class="py-3 font-bold text-[#181611] dark:text-white">
                                    {{ optional($order->ordered_at)->format('d/m/Y H:i') ?? $order->created_at->format('d/m/Y H:i') }}
                                </td>
                            </tr>
                            <tr>
                                <td class="py-3 pr-4 text-gray-500 dark:text-gray-400">Trạng thái đơn:</td>
                                <td class="py-3">
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
                                            default => 'bg-gray-100 text-gray-700 dark:bg-white/10 dark:text-gray-300',
                                        };
                                        $statusLabels = \App\Models\Order::statusLabels();
                                    @endphp
                                    <span class="inline-block px-2.5 py-1 rounded-md text-xs font-bold {{ $statusBadge }}">
                                        {{ $statusLabels[$order->status] ?? $order->status }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td class="py-3 pr-4 text-gray-500 dark:text-gray-400">Phương thức thanh toán:</td>
                                <td class="py-3 font-bold text-[#181611] dark:text-white">
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
                                <td class="py-3 pr-4 text-gray-500 dark:text-gray-400">Tình trạng thanh toán:</td>
                                <td class="py-3">
                                    <span class="inline-block px-2.5 py-1 rounded-md text-xs font-bold {{ $order->payment_status == 'paid' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400' }}">
                                        {{ $order->payment_status == 'paid' ? 'Đã thanh toán' : 'Chưa thanh toán' }}
                                    </span>
                                </td>
                            </tr>
                            @if($order->note)
                            <tr>
                                <td class="py-3 pr-4 text-gray-500 dark:text-gray-400">Ghi chú:</td>
                                <td class="py-3 font-medium text-amber-600 dark:text-amber-400 italic">
                                    "{{ $order->note }}"
                                </td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                {{-- NGƯỜI NHẬN --}}
                <div class="bg-white dark:bg-[#1a1a1a] rounded-2xl shadow-sm border border-gray-100 dark:border-white/10 overflow-hidden p-6 h-full">
                    <p class="text-[11px] font-bold text-gray-500 uppercase tracking-widest mb-4 pb-4 border-b border-gray-100 dark:border-white/10 flex items-center gap-2">
                        <span class="material-symbols-outlined text-[18px] text-[#f4c025]">person</span>
                        Người nhận
                    </p>
                    <table class="w-full text-sm">
                        <tbody class="divide-y divide-gray-50 dark:divide-white/5">
                            <tr>
                                <td class="py-3 pr-4 text-gray-500 dark:text-gray-400 whitespace-nowrap w-32">Họ và tên:</td>
                                <td class="py-3 font-bold text-[#181611] dark:text-white">{{ $order->customer_name }}</td>
                            </tr>
                            <tr>
                                <td class="py-3 pr-4 text-gray-500 dark:text-gray-400">Điện thoại:</td>
                                <td class="py-3 font-bold text-[#181611] dark:text-white">{{ $order->customer_phone }}</td>
                            </tr>
                            @if($order->customer_email)
                            <tr>
                                <td class="py-3 pr-4 text-gray-500 dark:text-gray-400">Email:</td>
                                <td class="py-3 text-gray-700 dark:text-gray-300">{{ $order->customer_email }}</td>
                            </tr>
                            @endif
                            <tr>
                                <td class="py-3 pr-4 text-gray-500 dark:text-gray-400 align-top">Địa chỉ:</td>
                                <td class="py-3 font-semibold text-[#181611] dark:text-white leading-relaxed">{{ $order->shipping_address ?: 'Chưa có địa chỉ' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-[#1a1a1a] rounded-2xl border border-gray-100 dark:border-white/10 p-6 space-y-4 mb-8 shadow-sm">
            <p class="text-[11px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-4 pb-4 border-b border-gray-100 dark:border-white/10">
                Thông tin chi tiết ({{ $order->items->sum('quantity') }} sản phẩm)
            </p>

            @if ($order->items->isNotEmpty())
            <div class="overflow-x-auto rounded-xl border border-gray-100 dark:border-white/10">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-white/5 border-b border-gray-100 dark:border-white/10">
                        <tr>
                            <th class="px-4 py-3 text-left text-[10px] font-bold uppercase tracking-widest text-gray-500">Sản phẩm</th>
                            <th class="px-4 py-3 text-left text-[10px] font-bold uppercase tracking-widest text-gray-500">Phân loại</th>
                            <th class="px-4 py-3 text-center text-[10px] font-bold uppercase tracking-widest text-gray-500">Số lượng</th>
                            <th class="px-4 py-3 text-right text-[10px] font-bold uppercase tracking-widest text-gray-500">Giá</th>
                            <th class="px-4 py-3 text-right text-[10px] font-bold uppercase tracking-widest text-gray-500">Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-white/10">
                        @foreach ($order->items as $item)
                        @php
                            $imageUrl = \Illuminate\Support\Str::startsWith($item->thumbnail, ['http://', 'https://']) ? $item->thumbnail : asset('storage/' . $item->thumbnail);
                            $baseName = $item->product_name;
                            $variantInfo = '';
                            if (preg_match('/^(.*?)\s*\((.*?)\)$/', $item->product_name, $matches)) {
                                $baseName = trim($matches[1]);
                                $variantInfo = trim($matches[2]);
                            }
                        @endphp
                        <tr>
                            <td class="px-4 py-4">
                                <div class="flex items-center gap-3 min-w-[220px]">
                                    <div class="size-12 rounded-lg bg-gray-50 dark:bg-black/20 overflow-hidden flex items-center justify-center border border-gray-100 dark:border-white/10 shrink-0">
                                        <img src="{{ $imageUrl }}" alt="{{ $baseName }}" class="w-full h-full object-contain mix-blend-multiply dark:mix-blend-normal">
                                    </div>
                                    <div class="flex-1">
                                        <span class="font-bold text-[#181611] dark:text-white block">{{ $baseName }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4 text-gray-600 dark:text-gray-300">
                                @if($variantInfo)
                                    <span class="px-2 py-1 bg-gray-100 dark:bg-white/5 rounded text-xs font-semibold">{{ $variantInfo }}</span>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-4 py-4 text-[#181611] dark:text-white font-bold text-center">x{{ $item->quantity }}</td>
                            <td class="px-4 py-4 text-right text-gray-600 dark:text-gray-400 font-semibold">{{ number_format($item->unit_price) }} ₫</td>
                            <td class="px-4 py-4 text-right font-bold text-[#181611] dark:text-white">{{ number_format($item->line_total) }} ₫</td>
                        </tr>

                        {{-- DÒNG CHỨA NÚT THAO TÁC CỦA SẢN PHẨM (Đánh giá, Mua lại, Hoàn trả) --}}
                        <tr class="bg-gray-50/50 dark:bg-white/5">
                            <td colspan="5" class="px-4 py-3">
                                <div class="flex flex-wrap items-center justify-between gap-4">
                                     {{-- PHẦN THAO TÁC: MUA LẠI + ĐÁNH GIÁ --}}
                                     <div class="flex items-center gap-2">
                                         @if($item->product)
                                             @php $prodParam = $item->product->slug ?: $item->product->id; @endphp
                                             <a href="{{ route('client.product.detail', ['id' => $prodParam]) }}" class="px-3 py-1.5 bg-blue-50 text-blue-600 border border-blue-200 font-bold rounded-lg hover:bg-blue-600 hover:text-white transition-colors text-xs inline-flex items-center gap-1">
                                                 <span class="material-symbols-outlined text-[13px]">shopping_cart</span> Mua lại
                                             </a>

                                             @if($order->status === \App\Models\Order::STATUS_RECEIVED)
                                                 @php
                                                     $userReview = \App\Models\Review::with(['images', 'repliedBy'])
                                                         ->where('user_id', auth()->id())
                                                         ->where('product_id', $item->product_id)
                                                         ->where('order_id', $order->id)
                                                         ->first();
                                                     $hasReviewed = $userReview !== null;
                                                 @endphp
                                                 @if(!$hasReviewed)
                                                     <button type="button" onclick="document.getElementById('review-modal-{{ $item->product_id }}').classList.remove('hidden'); document.getElementById('review-modal-{{ $item->product_id }}').classList.add('flex');" class="px-3 py-1.5 bg-[#f4c025] text-black shadow-[0_4px_10px_-2px_rgba(244,192,37,0.4)] border border-[#f4c025] font-bold rounded-lg hover:brightness-105 transition-all text-xs inline-flex items-center gap-1">
                                                         <span class="material-symbols-outlined text-[14px]">rate_review</span> Đánh giá
                                                     </button>
                                                 @elseif($hasReviewed)
                                                     <button type="button" onclick="document.getElementById('view-review-modal-{{ $item->product_id }}').classList.remove('hidden'); document.getElementById('view-review-modal-{{ $item->product_id }}').classList.add('flex');" class="px-3 py-1.5 bg-green-50 text-green-600 border border-green-200 font-bold rounded-lg hover:bg-green-600 hover:text-white transition-colors text-xs inline-flex items-center gap-1">
                                                         <span class="material-symbols-outlined text-[13px]">check_circle</span> Xem đánh giá
                                                     </button>
                                                 @endif
                                             @endif
                                         @endif
                                     </div>
                                </div>
                            </td>
                        </tr>

                        {{-- ===================== MODALS CHO TỪNG SẢN PHẨM ===================== --}}
                        {{-- 1. MODAL ĐÁNH GIÁ (NẾU ĐÃ ĐÁNH GIÁ) --}}
                        @if(isset($hasReviewed) && $hasReviewed)
                        <div id="view-review-modal-{{ $item->product_id }}" class="fixed inset-0 z-[110] hidden items-center justify-center p-4 bg-black/60 transition-opacity">
                            <div class="w-full max-w-lg rounded-2xl bg-white shadow-2xl dark:bg-[#1a1a1a] text-left p-6 relative animate-[scaleIn_0.2s_ease-out]">
                                <button type="button" onclick="document.getElementById('view-review-modal-{{ $item->product_id }}').classList.add('hidden'); document.getElementById('view-review-modal-{{ $item->product_id }}').classList.remove('flex');" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 dark:hover:text-white transition-colors">
                                    <span class="material-symbols-outlined text-[20px]">close</span>
                                </button>
                                
                                <h2 class="text-lg font-bold text-[#181611] dark:text-white flex items-center gap-2 mb-4 border-b border-gray-100 dark:border-white/10 pb-3">
                                    <span class="material-symbols-outlined text-green-500">rate_review</span> Đánh giá của bạn
                                </h2>
                                <div class="space-y-4">
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Đánh giá:</span>
                                        <div class="flex gap-0.5">
                                            @for($i = 1; $i <= 5; $i++)
                                                <span class="material-symbols-outlined text-[16px] {{ $i <= $userReview->rating ? 'text-[#f4c025]' : 'text-gray-200 dark:text-gray-600' }}" style="font-variation-settings:'FILL' 1">star</span>
                                            @endfor
                                        </div>
                                        <span class="text-xs text-gray-400">({{ $userReview->created_at->format('d/m/Y H:i') }})</span>
                                    </div>
                                    <div class="bg-gray-50 dark:bg-white/5 p-4 rounded-xl border border-gray-100 dark:border-white/10 text-sm text-gray-700 dark:text-gray-300">
                                        {{ $userReview->comment }}
                                    </div>
                                    @if($userReview->images->isNotEmpty())
                                        <div>
                                            <div class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-2">Hình ảnh:</div>
                                            <div class="flex gap-2 flex-wrap">
                                                @foreach($userReview->images as $img)
                                                    <a href="{{ asset('storage/' . $img->image_path) }}" target="_blank">
                                                        <img src="{{ asset('storage/' . $img->image_path) }}" class="w-16 h-16 object-cover rounded-xl border border-gray-100 dark:border-white/10 hover:opacity-80 transition-opacity">
                                                    </a>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                    @if($userReview->hasReply())
                                        <div class="mt-4 pl-4 border-l-2 border-[#f4c025]/50 bg-[#f4c025]/5 dark:bg-[#f4c025]/10 rounded-r-xl py-3 pr-4">
                                            <p class="text-xs font-bold text-[#f4c025] flex items-center gap-1 mb-1">
                                                <span class="material-symbols-outlined text-[14px]">support_agent</span> Phản hồi từ Bee Phone
                                            </p>
                                            <p class="text-sm text-gray-700 dark:text-gray-300">{{ $userReview->reply_comment }}</p>
                                        </div>
                                    @endif
                                </div>
                                <div class="mt-6 pt-4 border-t border-gray-100 dark:border-white/10 flex justify-end">
                                    <button type="button" onclick="document.getElementById('view-review-modal-{{ $item->product_id }}').classList.add('hidden'); document.getElementById('view-review-modal-{{ $item->product_id }}').classList.remove('flex');" class="px-5 py-2.5 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-bold transition-colors dark:bg-white/5 dark:text-gray-300">Đóng</button>
                                </div>
                            </div>
                        </div>
                        @endif

                        {{-- 2. MODAL ĐÁNH GIÁ MỚI --}}
                        @if(isset($hasReviewed) && !$hasReviewed && $order->status == \App\Models\Order::STATUS_RECEIVED)
                        <div id="review-modal-{{ $item->product_id }}" class="fixed inset-0 z-[110] hidden items-center justify-center p-4 bg-black/60 transition-opacity">
                            <div class="w-full max-w-lg rounded-2xl bg-white shadow-2xl dark:bg-[#1a1a1a] text-left p-6 relative animate-[scaleIn_0.2s_ease-out]">
                                <button type="button" onclick="document.getElementById('review-modal-{{ $item->product_id }}').classList.add('hidden'); document.getElementById('review-modal-{{ $item->product_id }}').classList.remove('flex');" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 dark:hover:text-white transition-colors">
                                    <span class="material-symbols-outlined text-[20px]">close</span>
                                </button>
                                <h2 class="text-lg font-bold text-[#181611] dark:text-white flex items-center gap-2 mb-1 border-b border-gray-100 dark:border-white/10 pb-3">
                                    <span class="material-symbols-outlined text-[#f4c025]">rate_review</span> Đánh giá sản phẩm
                                </h2>
                                <div class="flex items-center gap-4 mb-5 p-3 mt-4 rounded-xl bg-gray-50 dark:bg-white/5 border border-gray-100 dark:border-white/10">
                                    <div class="w-14 h-14 shrink-0 bg-white dark:bg-black/20 rounded-lg border border-gray-100 dark:border-white/5 p-1">
                                        <img src="{{ $imageUrl }}" alt="{{ $baseName }}" class="w-full h-full object-contain mix-blend-multiply dark:mix-blend-normal">
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-[#181611] dark:text-white line-clamp-2 leading-tight">{{ $baseName }}</p>
                                        @if($variantInfo)<p class="text-[11px] text-gray-500 mt-1 uppercase tracking-wider font-semibold">{{ $variantInfo }}</p>@endif
                                    </div>
                                </div>
                                <form action="{{ route('products.reviews.store', $item->product_id) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="order_id" value="{{ $order->id }}">
                                    <div class="mb-4">
                                        <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-2">Chất lượng sản phẩm <span class="text-red-500">*</span></label>
                                        <select name="rating" class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm font-semibold focus:border-[#f4c025] focus:ring-1 focus:ring-[#f4c025] outline-none" required>
                                            <option value="">-- Chọn số sao --</option>
                                            <option value="5">5 Sao - Tuyệt vời</option>
                                            <option value="4">4 Sao - Tốt</option>
                                            <option value="3">3 Sao - Bình thường</option>
                                            <option value="2">2 Sao - Tệ</option>
                                            <option value="1">1 Sao - Rất tệ</option>
                                        </select>
                                    </div>
                                    <div class="mb-4">
                                        <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-2">Đánh giá chi tiết <span class="text-red-500">*</span></label>
                                        <textarea name="comment" rows="3" class="w-full rounded-xl border border-gray-200 focus:border-[#f4c025] outline-none p-3 text-sm resize-none" placeholder="Hãy chia sẻ trải nghiệm (tối thiểu 10 ký tự)..." required minlength="10"></textarea>
                                    </div>
                                    <div class="mb-6">
                                        <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-2">Thêm hình ảnh (Tùy chọn, tối đa 5 ảnh)</label>
                                        <input type="file" name="images[]" multiple accept="image/jpeg,image/png,image/webp" class="w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-[#f4c025]/20 file:text-[#181611] hover:file:bg-[#f4c025]/40 transition cursor-pointer">
                                    </div>
                                    <div class="flex gap-3 justify-end items-center pt-4 border-t border-gray-100 dark:border-white/10">
                                        <button type="button" onclick="document.getElementById('review-modal-{{ $item->product_id }}').classList.add('hidden'); document.getElementById('review-modal-{{ $item->product_id }}').classList.remove('flex');" class="px-5 py-2.5 rounded-xl text-sm font-bold text-gray-600 bg-gray-100 hover:bg-gray-200 transition-colors">Hủy bỏ</button>
                                        <button type="submit" class="px-6 py-2.5 rounded-xl bg-[#f4c025] text-[#181611] text-sm font-bold shadow-[0_4px_14px_0_rgba(244,192,37,0.39)] hover:brightness-105 transition-all">Hoàn tất</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        @endif

                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif

            @php
                $subtotal = $order->items->sum('line_total');
                $shippingFee = $order->shipping_fee ?? 0;
                $discountAmount = ($subtotal + $shippingFee) - $order->total_amount;
                if($discountAmount < 0) $discountAmount = 0;
            @endphp

            <div class="mt-6 pt-4 border-t border-gray-100 dark:border-white/10">
                <table class="w-full text-sm">
                    <tbody class="divide-y divide-gray-50 dark:divide-white/5">
                        <tr>
                            <td class="py-3 pr-4 text-gray-500 dark:text-gray-400 whitespace-nowrap w-44 uppercase text-[10px] font-bold tracking-widest">Tiền hàng:</td>
                            <td class="py-3 font-bold text-[#181611] dark:text-white text-right">{{ number_format($subtotal) }} ₫</td>
                        </tr>
                        <tr>
                            <td class="py-3 pr-4 text-gray-500 dark:text-gray-400 uppercase text-[10px] font-bold tracking-widest">Phí vận chuyển:</td>
                            <td class="py-3 font-bold text-right {{ $shippingFee <= 0 ? 'text-green-600 dark:text-green-400' : 'text-[#181611] dark:text-white' }}">
                                {{ $shippingFee <= 0 ? 'Miễn phí' : number_format($shippingFee) . ' ₫' }}
                            </td>
                        </tr>
                        @if($discountAmount > 0)
                        <tr>
                            <td class="py-3 pr-4 text-gray-500 dark:text-gray-400 uppercase text-[10px] font-bold tracking-widest">Giảm giá:</td>
                            <td class="py-3 font-bold text-red-500 text-right">-{{ number_format($discountAmount) }} ₫</td>
                        </tr>
                        @endif
                        <tr class="bg-gray-50/50 dark:bg-white/5">
                            <td class="py-4 pr-4 font-bold text-[#181611] dark:text-white uppercase text-[11px] tracking-widest">Tổng thanh toán:</td>
                            <td class="py-4 font-bold text-[#f4c025] text-xl text-right">{{ number_format($order->total_amount) }} ₫</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- <div class="mt-8 flex justify-center pb-8">
            <a href="{{ route('client.products.index') }}" class="px-6 py-3 rounded-xl bg-white dark:bg-white/5 border border-gray-100 dark:border-white/10 text-sm font-bold text-gray-500 hover:text-[#f4c025] hover:border-[#f4c025] transition-all flex items-center gap-2 shadow-sm">
                <span class="material-symbols-outlined text-[20px]">shopping_bag</span> Tiếp tục mua sắm
            </a>
        </div> --}}

        {{-- ======= KHU VỰC YÊU CẦU HOÀN TRẢ (ORDER-LEVEL) ======= --}}
        @if($order->status === \App\Models\Order::STATUS_RECEIVED)
        @php
            $returnRequests = $order->returnRequests()->with(['items.orderItem'])->get();
            $returnDeadline = $order->updated_at ? $order->updated_at->copy()->addDays(7) : null;
            $daysLeftReturn  = $returnDeadline ? max(0, (int) ceil(now()->diffInHours($returnDeadline, false) / 24)) : null;
            $canNewReturn    = $returnDeadline && now()->lt($returnDeadline);
        @endphp

        <div class="bg-white dark:bg-[#1a1a1a] rounded-2xl border border-gray-100 dark:border-white/10 overflow-hidden mb-8 shadow-sm">
            <div class="px-5 py-4 flex items-center justify-between border-b border-gray-100 dark:border-white/10 bg-amber-50/50 dark:bg-amber-500/5">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-amber-500">assignment_return</span>
                    <p class="font-bold text-[#181611] dark:text-white text-sm">Yêu cầu hoàn trả</p>
                    @if($daysLeftReturn !== null && $returnDeadline)
                        <div class="flex flex-col">
                           
                            <span class="text-[10px] text-gray-400 dark:text-gray-500 font-medium italic">
                                Hỗ trợ đến {{ $returnDeadline->format('d/m/Y') }}
                            </span>
                        </div>
                    @endif
                </div>
                @if($canNewReturn)
                    <button type="button" onclick="openReturnModal()" class="px-4 py-2 bg-amber-500 text-white text-xs font-bold rounded-xl hover:bg-amber-600 transition-colors flex items-center gap-1.5 shadow-md shadow-amber-500/20">
                        <span class="material-symbols-outlined text-[14px]">add</span> Tạo yêu cầu trả hàng
                    </button>
                @endif
            </div>

            @if($returnRequests->isEmpty())
                <div class="px-6 py-8 text-center">
                    <span class="material-symbols-outlined text-gray-300 text-4xl mb-2 block">inventory_2</span>
                    <p class="text-sm text-gray-400">Chưa có yêu cầu hoàn trả nào.</p>
                </div>
            @else
                <div class="divide-y divide-gray-50 dark:divide-white/5">
                    @foreach($returnRequests as $rr)
                    @php
                        $rrStatusColors = [
                            'pending'   => 'bg-amber-100 text-amber-800 border-amber-200',
                            'approved'  => 'bg-blue-100 text-blue-800 border-blue-200',
                            'rejected'  => 'bg-red-100 text-red-800 border-red-200',
                            'picking'   => 'bg-indigo-100 text-indigo-800 border-indigo-200',
                            'received'  => 'bg-cyan-100 text-cyan-800 border-cyan-200',
                            'completed' => 'bg-green-100 text-green-800 border-green-200',
                        ];
                        $rrColor = $rrStatusColors[$rr->status] ?? 'bg-gray-100 text-gray-800';
                    @endphp
                    <div class="px-0 py-0 flex flex-col">
                        {{-- Header Summary --}}
                        <div class="px-5 py-4 flex items-center justify-between gap-4">
                            <div class="grid grid-cols-2 md:grid-cols-4 items-center gap-4 flex-1">
                                <div>
                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Mã hoàn trả</p>
                                    <p class="font-bold text-sm text-[#181611] dark:text-white">{{ $rr->return_code }}</p>
                                </div>
                                <div>
                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Mã vận đơn GHN</p>
                                    @if($rr->tracking_number)
                                        <p class="text-xs font-mono font-bold text-indigo-600 dark:text-indigo-400">{{ $rr->tracking_number }}</p>
                                    @else
                                        <p class="text-xs text-gray-400 italic">Chưa có mã</p>
                                    @endif
                                </div>
                                <div>
                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Trạng thái</p>
                                    <span class="inline-block border px-2 py-0.5 rounded-full text-[10px] font-bold {{ $rrColor }}">
                                        {{ \App\Models\ReturnRequest::statusLabels()[$rr->status] ?? $rr->status }}
                                    </span>
                                </div>
                                <div>
                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Ngày yêu cầu</p>
                                    <p class="text-xs text-gray-600 dark:text-gray-300 font-medium">{{ $rr->created_at->format('d/m/Y H:i') }}</p>
                                </div>
                            </div>
                            <div class="text-right shrink-0">
                                <button type="button" 
                                        onclick="document.getElementById('client-return-details-{{ $rr->id }}').classList.toggle('hidden')"
                                        class="px-3 py-1.5 bg-gray-50 hover:bg-gray-100 dark:bg-white/5 dark:hover:bg-white/10 rounded-lg text-xs font-bold text-gray-600 dark:text-gray-300 transition-colors flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[16px]">visibility</span> Xem chi tiết
                                </button>
                            </div>
                        </div>

                        {{-- Details --}}
                        <div id="client-return-details-{{ $rr->id }}" class="hidden px-5 pb-4 pt-0 border-t border-gray-50 dark:border-white/5 mt-2">
                            {{-- Client Actions --}}
                            @if($rr->status === 'pending' || $rr->status === 'approved')
                            <div class="flex items-center gap-2 mb-4 pt-4">
                                @if($rr->status === 'pending')
                                    <form action="{{ route('client.orders.return.cancel', $rr->id) }}" method="POST" onsubmit="return confirm('Hủy yêu cầu này?')">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="px-3 py-1.5 rounded-lg bg-red-50 text-red-600 dark:bg-red-500/10 dark:text-red-400 text-[11px] font-bold hover:bg-red-100 dark:hover:bg-red-500/20 transition-colors flex items-center gap-1">
                                            <span class="material-symbols-outlined text-[14px]">cancel</span> Hủy yêu cầu
                                        </button>
                                    </form>
                                @elseif($rr->status === 'approved')
                                    <form action="{{ route('client.orders.return.shipped', $rr->id) }}" method="POST">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="px-3 py-1.5 rounded-lg bg-indigo-600 text-white text-[11px] font-bold hover:bg-indigo-700 transition-colors flex items-center gap-1">
                                            <span class="material-symbols-outlined text-[14px]">local_shipping</span> Đã gửi hàng đi
                                        </button>
                                    </form>
                                    <div class="px-3 py-1.5 rounded-lg bg-gray-100 text-gray-400 dark:bg-white/5 dark:text-gray-500 text-[11px] font-bold flex items-center gap-1 cursor-not-allowed" title="Yêu cầu đã được duyệt, không thể hủy">
                                        <span class="material-symbols-outlined text-[14px]">cancel</span> Không thể hủy (Đã duyệt)
                                    </div>
                                @endif
                            </div>
                            @endif

                            {{-- Danh sách sản phẩm trong request --}}
                            <div class="space-y-1.5 mt-4">
                                @foreach($rr->items as $ri)
                                    <div class="flex items-center justify-between text-xs bg-gray-50 dark:bg-white/5 rounded-lg px-3 py-2 border border-gray-100 dark:border-white/5">
                                        <span class="font-semibold text-[#181611] dark:text-white">{{ $ri->orderItem->product_name ?? '—' }}</span>
                                        <span class="text-gray-500">x{{ $ri->quantity }} &nbsp;|&nbsp; <span class="text-green-600 font-bold">{{ number_format($ri->refund_amount) }}₫</span></span>
                                    </div>
                                @endforeach
                            </div>
                            
                            <div class="flex flex-col gap-1 mt-2 text-xs text-gray-500 text-right">
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
                                <div class="flex justify-between border-t border-gray-100 dark:border-white/5 pt-1 mt-1">
                                    <span>Tổng hoàn dự kiến (Thực nhận):</span>
                                    <span class="font-bold text-green-600 text-sm">{{ number_format($rr->total_refund_amount) }} ₫</span>
                                </div>
                            </div>

                            {{-- Lý do & Ảnh --}}
                            <div class="mt-4 flex flex-col gap-3 p-3 bg-gray-50 dark:bg-white/5 rounded-xl border border-gray-100 dark:border-white/10">
                                <div class="flex flex-wrap gap-2">
                                    @if($rr->images && is_array($rr->images) && count($rr->images) > 0)
                                        @foreach($rr->images as $imgPath)
                                            <a href="{{ asset($imgPath) }}" target="_blank" class="shrink-0">
                                                <img src="{{ asset($imgPath) }}" class="w-14 h-14 rounded-lg object-cover border border-gray-200 dark:border-white/10 hover:opacity-80 transition-opacity">
                                            </a>
                                        @endforeach
                                    @elseif($rr->image)
                                        <a href="{{ asset($rr->image) }}" target="_blank" class="shrink-0">
                                            <img src="{{ asset($rr->image) }}" class="w-14 h-14 rounded-lg object-cover border border-gray-200 dark:border-white/10 hover:opacity-80 transition-opacity">
                                        </a>
                                    @endif
                                </div>
                                <div>
                                    <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-1">Lý do hoàn trả</p>
                                    <p class="text-sm text-[#181611] dark:text-gray-300 font-medium leading-relaxed">"{{ $rr->reason }}"</p>
                                </div>
                            </div>

                            {{-- ===== TIẾN TRÌNH ĐƠN HOÀN ===== --}}
                            @php
                                $rrIsRejected = $rr->status === 'rejected';
                                $rrStep = 1;
                                if ($rr->status === 'approved')  $rrStep = 2;
                                if ($rr->status === 'picking')   $rrStep = 3;
                                if ($rr->status === 'received')  $rrStep = 4;
                                if ($rr->status === 'completed') $rrStep = 5;
                                $rrProgressWidth = match($rrStep) {
                                    1 => '0%', 2 => '25%', 3 => '50%', 4 => '75%', 5 => '100%',
                                    default => '0%'
                                };
                            @endphp
                            <div class="rounded-xl border border-gray-100 dark:border-white/10 overflow-hidden mt-4">
                                <div class="px-4 py-3 bg-gray-50 dark:bg-white/5 border-b border-gray-100 dark:border-white/10 flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <span class="material-symbols-outlined text-[16px] {{ $rrIsRejected ? 'text-red-500' : 'text-[#f4c025]' }}">analytics</span>
                                        <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Tiến trình đơn hoàn</span>
                                    </div>
                                    @if($rr->tracking_number)
                                    <div class="flex items-center gap-1.5">
                                        <span class="text-[10px] font-bold text-gray-400 uppercase">Vận đơn:</span>
                                        <span class="text-[10px] font-mono font-bold text-indigo-600 dark:text-indigo-400">{{ $rr->tracking_number }}</span>
                                    </div>
                                    @endif
                                </div>
                                <div class="px-5 py-6 bg-white dark:bg-[#1a1a1a]">
                                    @if($rrIsRejected)
                                    <div class="flex items-center gap-4 p-4 rounded-xl bg-red-50 dark:bg-red-500/10 border border-red-100 dark:border-red-500/20 text-red-600 dark:text-red-400">
                                        <div class="w-12 h-12 rounded-full bg-white dark:bg-[#1a1a1a] flex items-center justify-center border-4 border-red-500 shadow-lg shadow-red-500/20 shrink-0">
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
                                    <div class="relative flex items-center justify-between py-2">
                                        <div class="absolute top-[28px] left-0 w-full h-2 bg-gray-100 dark:bg-white/5 z-0 rounded-full">
                                            <div class="h-full bg-gradient-to-r from-[#f4c025] to-[#f4c025] transition-all duration-1000 ease-in-out rounded-full shadow-[0_0_12px_rgba(244,192,37,0.35)]" style="width: {{ $rrProgressWidth }}"></div>
                                        </div>
                                        @php
                                            $stepsData = [
                                                1 => ['icon' => 'assignment_return', 'label' => 'Yêu cầu', 'time' => $rr->created_at ? $rr->created_at->format('d/m H:i') : null],
                                                2 => ['icon' => 'how_to_reg', 'label' => 'Đã duyệt', 'time' => $rr->approved_at ? $rr->approved_at->format('d/m H:i') : null],
                                                3 => ['icon' => 'local_shipping', 'label' => 'Thu hồi', 'time' => ($rr->tracking_number && $rrStep >= 3) ? $rr->tracking_number : null, 'isMono' => true],
                                                4 => ['icon' => 'inventory_2', 'label' => 'Đã nhận', 'time' => $rr->received_at ? $rr->received_at->format('d/m H:i') : null],
                                                5 => ['icon' => 'account_balance_wallet', 'label' => 'Hoàn tiền', 'time' => $rr->completed_at ? $rr->completed_at->format('d/m H:i') : null],
                                            ];
                                        @endphp
                                        @foreach($stepsData as $sNum => $sInfo)
                                        <div class="relative z-10 flex flex-col items-center w-1/5">
                                            <div class="w-12 h-12 rounded-full {{ $rrStep >= $sNum ? 'bg-[#f4c025] text-black shadow-[0_0_16px_rgba(244,192,37,0.4)]' : 'bg-gray-100 dark:bg-white/5 text-gray-400' }} flex items-center justify-center border-4 border-white dark:border-[#1a1a1a] transition-all duration-500 mb-2">
                                                <span class="material-symbols-outlined text-xl {{ $rrStep == $sNum ? 'animate-pulse' : '' }}">{{ $sInfo['icon'] }}</span>
                                            </div>
                                            <span class="text-[10px] font-bold uppercase tracking-tight {{ $rrStep >= $sNum ? 'text-[#181611] dark:text-white' : 'text-gray-400' }} text-center">{{ $sInfo['label'] }}</span>
                                            @if($sInfo['time'])
                                            <span class="text-[9px] mt-0.5 {{ !empty($sInfo['isMono']) ? 'text-indigo-500 font-mono' : 'text-gray-400' }}">{{ $sInfo['time'] }}</span>
                                            @endif
                                        </div>
                                        @endforeach
                                    </div>
                                    @endif
                                </div>
                            </div>

                            @php
                                $rrHistories = $rr->histories;
                                $rrStatusLabels = \App\Models\ReturnRequestHistory::$statusLabels;
                                $rrGrouped = $rrHistories->groupBy(function ($h) {
                                    return $h->created_at->format('Y-m-d');
                                });
                                $rrFirstHistoryId = $rrHistories->first()?->id;
                                $ghnStatusLabels = [
                                    'ready_to_pick' => 'Chờ lấy hàng', 'picking' => 'Đang lấy hàng', 'cancel' => 'Đã hủy đơn',
                                    'money_collect_picking' => 'Đang lấy hàng', 'picked' => 'Đã lấy hàng', 'storing' => 'Nhập kho trung chuyển',
                                    'transporting' => 'Đang luân chuyển', 'sorting' => 'Đang phân loại', 'delivering' => 'Đang giao hàng',
                                    'money_collect_delivering' => 'Đang giao hàng', 'delivered' => 'Giao thành công', 'delivery_fail' => 'Giao thất bại',
                                    'waiting_to_return' => 'Đang chờ giao lại', 'return' => 'Chờ hoàn hàng', 'return_transporting' => 'Đang luân chuyển hoàn',
                                    'return_sorting' => 'Đang phân loại hoàn', 'returning' => 'Đang hoàn hàng', 'return_fail' => 'Hoàn hàng thất bại',
                                    'returned' => 'Đã hoàn hàng', 'exception' => 'Ngoại lệ', 'damage' => 'Hàng bị hỏng', 'lost' => 'Hàng bị mất',
                                ];
                            @endphp
                            @if($rrHistories->isNotEmpty())
                            <div class="rounded-xl border border-gray-100 dark:border-white/10 overflow-hidden mt-4">
                                <div class="px-4 py-3 bg-gray-50 dark:bg-white/5 border-b border-gray-100 dark:border-white/10 flex items-center gap-2">
                                    <span class="material-symbols-outlined text-[16px] text-gray-500">history</span>
                                    <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Lịch sử xử lý đơn hoàn</span>
                                </div>
                                <table class="w-full text-sm">
                                    @foreach ($rrGrouped as $dateStr => $histories)
                                    @php
                                        $dateCarbon = \Carbon\Carbon::parse($dateStr);
                                        $dateLabel  = ucfirst($dateCarbon->locale('vi')->isoFormat('dddd, DD/MM/YYYY'));
                                    @endphp
                                    <thead>
                                        <tr class="{{ !$loop->first ? 'border-t-2 border-gray-50 dark:border-white/5' : '' }}">
                                            <th class="px-4 py-2.5 text-left font-bold text-gray-600 dark:text-gray-300 text-[11px] bg-gray-50/50 dark:bg-white/5 w-32">{{ $dateLabel }}</th>
                                            <th class="px-3 py-2.5 text-left font-semibold text-gray-400 text-[10px] uppercase tracking-widest bg-gray-50/50 dark:bg-white/5">Chi tiết</th>
                                            <th class="px-4 py-2.5 text-right font-semibold text-gray-400 text-[10px] uppercase tracking-widest bg-gray-50/50 dark:bg-white/5 w-20">Thời gian</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-50 dark:divide-white/5">
                                        @foreach ($histories as $rh)
                                        @php
                                            $isNewest = $rh->id === $rrFirstHistoryId;
                                            $ghnRaw = $rh->ghn_status_raw;
                                            $historyLabel = $ghnRaw 
                                                ? ($ghnStatusLabels[$ghnRaw] ?? ($rrStatusLabels[$rh->status] ?? $rh->status))
                                                : ($rrStatusLabels[$rh->status] ?? $rh->status);
                                            $detailText = $rh->note ?: $rh->ghn_description;
                                            $tc = $isNewest ? 'text-blue-600 dark:text-blue-400' : 'text-gray-600 dark:text-gray-400';
                                            $fw = $isNewest ? 'font-bold' : 'font-semibold';
                                        @endphp
                                        <tr class="bg-white dark:bg-[#1a1a1a]">
                                            <td class="px-4 py-3 align-top">
                                                <span class="{{ $tc }} {{ $fw }} text-[12px] uppercase tracking-wider">{{ $historyLabel }}</span>
                                            </td>
                                            <td class="px-3 py-3 align-top">
                                                <p class="{{ $tc }} text-[12px] leading-relaxed">{{ $detailText }}</p>
                                            </td>
                                            <td class="px-4 py-3 text-right align-top">
                                                <span class="{{ $isNewest ? 'text-[#181611] dark:text-white font-bold' : 'text-gray-500 font-medium' }} text-[12px]">{{ $rh->created_at->format('H:i') }}</span>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    @endforeach
                                </table>
                            </div>
                            @endif

                            {{-- Note --}}
                            @if($rr->admin_note)
                                <div class="mt-4 p-3 bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300 text-xs rounded-xl border border-blue-100 dark:border-blue-800/30 flex gap-2">
                                    <span class="material-symbols-outlined text-[16px] shrink-0 mt-0.5">info</span>
                                    <div>
                                        <span class="font-bold uppercase tracking-widest text-[10px] block mb-0.5">Phản hồi từ Admin</span> 
                                        <span class="text-[13px]">{{ $rr->admin_note }}</span>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- MODAL TẠO YÊU CẦU HOÀN --}}
        <div id="return-request-modal" class="fixed inset-0 z-[150] hidden items-center justify-center p-4 bg-black/60 backdrop-blur-sm transition-all duration-300">
            <div class="w-full max-w-lg rounded-3xl bg-white dark:bg-[#1a1a1a] shadow-2xl relative animate-[scaleIn_0.2s_ease-out] flex flex-col max-h-[90vh]">
                <div class="p-6 border-b border-gray-100 dark:border-white/10 flex items-center justify-between shrink-0">
                    <div>
                        <h2 class="text-xl font-bold text-[#181611] dark:text-white flex items-center gap-2">
                            <span class="material-symbols-outlined text-amber-500">assignment_return</span> Tạo yêu cầu hoàn trả
                        </h2>
                        <p class="text-xs text-gray-500 mt-1">Hệ thống sẽ tính toán số tiền hoàn lại dựa trên sản phẩm bạn chọn.</p>
                    </div>
                    <button type="button" onclick="closeReturnModal()" class="size-10 flex items-center justify-center rounded-full text-gray-400 hover:text-gray-600 hover:bg-gray-100 dark:hover:bg-white/5 transition-colors">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <form action="{{ route('client.orders.return', $order->id) }}" method="POST" enctype="multipart/form-data" class="flex-1 overflow-y-auto custom-scrollbar p-6 space-y-5">
                    @csrf

                    {{-- Chọn sản phẩm --}}
                    <div class="space-y-3">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest">Sản phẩm muốn trả <span class="text-red-500">*</span></label>
                        <div class="grid gap-2">
                            @foreach($order->items as $item)
                            @php
                                $itemHasActiveReturn = $order->returnRequests->flatMap->items->where('order_item_id', $item->id)->filter(fn($ri) => $ri->returnRequest && $ri->returnRequest->status !== 'rejected')->isNotEmpty();
                            @endphp
                            <label class="flex items-center gap-4 p-4 rounded-2xl border border-gray-100 dark:border-white/10 cursor-pointer hover:border-amber-400 hover:bg-amber-50/30 dark:hover:bg-amber-500/5 transition-all {{ $itemHasActiveReturn ? 'opacity-50 pointer-events-none grayscale' : '' }}">
                                <input type="checkbox" name="items[{{ $loop->index }}][id]" value="{{ $item->id }}" onchange="toggleQty(this, 'qty-{{ $item->id }}')"
                                    class="size-5 rounded border-gray-300 text-amber-500 focus:ring-amber-400" {{ $itemHasActiveReturn ? 'disabled' : '' }}>
                                <div class="flex-1 min-w-0">
                                    <p class="text-[13px] font-bold text-[#181611] dark:text-white truncate">{{ $item->product_name }}</p>
                                    <p class="text-xs text-gray-500 font-medium">{{ number_format($item->unit_price) }}₫ × {{ $item->quantity }}</p>
                                </div>
                                <div id="qty-{{ $item->id }}" class="hidden">
                                    <input type="number" name="items[{{ $loop->index }}][qty]" min="1" max="{{ $item->quantity }}" value="{{ $item->quantity }}"
                                        class="w-16 text-center rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-black/30 px-2 py-1.5 text-sm font-bold focus:ring-2 focus:ring-amber-400/50 outline-none" disabled>
                                </div>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Lý do --}}
                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest">Lý do hoàn trả <span class="text-red-500">*</span></label>
                        <select name="reason_type" id="reason_type" required onchange="handleReasonChange(this)"
                            class="w-full rounded-2xl border border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-black/20 px-4 py-3.5 text-sm font-semibold focus:ring-2 focus:ring-amber-400/50 outline-none transition-all">
                            <option value="">-- Chọn lý do --</option>
                            <option value="Sản phẩm lỗi, không hoạt động">Sản phẩm lỗi, không hoạt động</option>
                            <option value="Giao sai sản phẩm">Giao sai sản phẩm</option>
                            <option value="Sản phẩm không giống mô tả">Sản phẩm không giống mô tả</option>
                            <option value="Hàng bị hư hỏng khi vận chuyển">Hàng bị hư hỏng khi vận chuyển</option>
                            <option value="Đổi ý, không còn nhu cầu">Đổi ý, không còn nhu cầu</option>
                            <option value="Lý do khác">Lý do khác (Vui lòng mô tả bên dưới)</option>
                        </select>
                        <div id="other_reason_container" class="hidden animate-slideDown">
                            <textarea name="reason" id="reason_textarea" rows="3" class="w-full rounded-2xl border border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-black/20 px-4 py-3.5 text-sm focus:ring-2 focus:ring-amber-400/50 outline-none mt-2" placeholder="Vui lòng mô tả chi tiết lý do..."></textarea>
                        </div>
                    </div>

                    {{-- Ảnh bằng chứng --}}
                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest">Ảnh bằng chứng (Tối đa 5 ảnh) <span class="text-red-500">*</span></label>
                        <div class="relative group">
                            <input type="file" name="images[]" id="return_image" required accept="image/*" multiple onchange="previewReturnImages(this)"
                                class="hidden">
                            <label for="return_image" class="flex flex-col items-center justify-center w-full py-8 border-2 border-dashed border-gray-200 dark:border-white/10 rounded-3xl hover:border-amber-400 hover:bg-amber-50/30 transition-all cursor-pointer">
                                <span class="material-symbols-outlined text-gray-400 group-hover:text-amber-500 text-4xl mb-2">add_a_photo</span>
                                <span class="text-xs font-bold text-gray-500 group-hover:text-amber-600">Bấm để tải ảnh lên (chọn nhiều ảnh)</span>
                            </label>
                        </div>
                        <div id="image-preview-container" class="hidden mt-3 grid grid-cols-2 sm:grid-cols-3 gap-3">
                            <!-- JS will append images here -->
                        </div>
                    </div>

                    <div class="pt-2 sticky bottom-0 bg-white dark:bg-[#1a1a1a]">
                        <button type="submit" class="w-full bg-amber-500 hover:bg-amber-600 text-white font-bold py-4 rounded-2xl transition-all shadow-xl shadow-amber-500/25 flex items-center justify-center gap-2 active:scale-95">
                            <span class="material-symbols-outlined text-[20px]">send</span> Gửi yêu cầu hoàn tiền
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @endif

        {{-- Modal Thao Tác Đơn Hàng --}}
        <div id="order-action-modal" class="fixed inset-0 z-[120] hidden items-center justify-center p-4 bg-black/60 transition-opacity">
            <div class="w-full max-w-md rounded-3xl bg-white shadow-2xl dark:bg-[#1a1a1a] text-left p-6 relative animate-[scaleIn_0.2s_ease-out]">
                <button type="button" onclick="document.getElementById('order-action-modal').classList.add('hidden'); document.getElementById('order-action-modal').classList.remove('flex');" class="absolute top-5 right-5 text-gray-400 hover:text-gray-600 dark:hover:text-white transition-colors">
                    <span class="material-symbols-outlined text-[24px]">close</span>
                </button>
                
                <h2 class="text-xl font-bold text-[#181611] dark:text-white flex items-center gap-2 mb-6 border-b border-gray-100 dark:border-white/10 pb-4">
                    <span class="material-symbols-outlined text-[#f4c025]">settings</span> Thao tác đơn hàng
                </h2>

                <div class="space-y-6">
                    @if(in_array($order->status, [\App\Models\Order::STATUS_PENDING, \App\Models\Order::STATUS_READY_TO_PICK, \App\Models\Order::STATUS_PICKING]))
                        <form action="{{ route('client.orders.cancel', $order->id) }}" method="POST" class="space-y-4">
                            @csrf
                            @method('PATCH')
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Lý do hủy đơn hàng <span class="text-red-500">*</span></label>
                                <select name="cancellation_reason" required class="w-full rounded-xl border border-gray-200 bg-gray-50 dark:bg-black/20 px-4 py-3.5 text-sm font-semibold text-[#181611] focus:outline-none focus:ring-2 focus:ring-[#f4c025]/50 focus:border-[#f4c025] dark:border-white/10 dark:text-white transition-all">
                                    <option value="">--- Chọn lý do hủy ---</option>
                                    <option value="Thay đổi ý định">Thay đổi ý định (Không muốn mua nữa)</option>
                                    <option value="Tìm thấy giá rẻ hơn ở nơi khác">Tìm thấy shop khác bán rẻ hơn</option>
                                    <option value="Đổi địa chỉ/sđt nhận hàng">Muốn thay đổi địa chỉ hoặc SĐT nhận hàng</option>
                                    <option value="Đặt nhầm sản phẩm/số lượng">Đặt nhầm sản phẩm hoặc sai số lượng</option>
                                    <option value="Lý do khác">Lý do khác</option>
                                </select>
                            </div>
                            
                            @if(in_array($order->payment_method, ['wallet', 'vnpay', 'vnp']) && $order->payment_status === 'paid')
                                <div class="p-4 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800/30 rounded-xl text-amber-700 dark:text-amber-400 text-sm flex gap-3">
                                    <span class="material-symbols-outlined shrink-0">info</span>
                                    <p>Số tiền <strong>{{ number_format($order->total_amount) }} ₫</strong> sẽ được hoàn trả tự động vào ví của bạn ngay sau khi hủy đơn thành công.</p>
                                </div>
                            @endif

                            <button type="submit" class="w-full bg-red-500 text-white font-bold py-4 rounded-2xl flex items-center justify-center gap-2 hover:bg-red-600 transition-all active:scale-95 shadow-lg shadow-red-500/20">
                                <span class="material-symbols-outlined">cancel</span> Xác nhận hủy đơn
                            </button>
                        </form>
                    @endif

                    @if($order->status == \App\Models\Order::STATUS_DELIVERED)
                        <div class="space-y-4">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Vui lòng chỉ xác nhận khi bạn đã nhận được hàng và hài lòng với sản phẩm.</p>
                            <form action="{{ route('client.orders.confirm', $order->id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="w-full bg-green-500 text-white font-bold py-4 rounded-2xl flex items-center justify-center gap-2 hover:bg-green-600 shadow-lg shadow-green-500/20 transition-all active:scale-95" onclick="return confirm('Bạn xác nhận đã nhận được hàng?')">
                                    <span class="material-symbols-outlined">check_circle</span> Đã nhận được hàng
                                </button>
                            </form>
                        </div>
                    @endif

                    @if(!in_array($order->status, [\App\Models\Order::STATUS_PENDING, \App\Models\Order::STATUS_READY_TO_PICK, \App\Models\Order::STATUS_PICKING, \App\Models\Order::STATUS_DELIVERED]))
                        <div class="py-8 text-center">
                            <span class="material-symbols-outlined text-gray-300 text-5xl mb-2">info</span>
                            <p class="text-sm text-gray-500">Hiện tại không có thao tác nào khả dụng cho trạng thái này.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- LỊCH SỬ CHI TIẾT HÀNH TRÌNH ĐƠN HÀNG GIỐNG ADMIN --}}
        <div class="bg-white dark:bg-[#1a1a1a] rounded-2xl border border-gray-100 dark:border-white/10 overflow-hidden mb-8 shadow-sm">
            <div class="p-5 border-b border-gray-100 dark:border-white/10 bg-gray-50/50 dark:bg-white/5">
                <p class="text-[11px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400">
                    Chi tiết hành trình đơn hàng
                </p>
            </div>

            @php
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

                $statusLabels = \App\Models\Order::statusLabels();

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
                <thead>
                    <tr class="{{ !$loop->first ? 'border-t-4 border-gray-50 dark:border-white/5' : '' }}">
                        <th class="px-6 py-3 text-left font-bold text-[#181611] dark:text-white text-[13px] bg-gray-50 dark:bg-white/5 w-52">{{ $dateLabel }}</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-500 dark:text-gray-400 text-[13px] bg-gray-50 dark:bg-white/5">Chi tiết</th>
                        <th class="px-6 py-3 text-right font-semibold text-gray-500 dark:text-gray-400 text-[13px] bg-gray-50 dark:bg-white/5 w-28">Thời gian</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-white/5">
                    @foreach ($histories as $history)
                    @php
                        $isNewest = $history->id === $firstHistoryId;

                        $ghnRaw       = $history->ghn_status_raw;
                        $historyLabel = $ghnRaw
                            ? ($ghnStatusLabels[$ghnRaw] ?? ($statusLabels[$history->status] ?? $history->status))
                            : ($statusLabels[$history->status] ?? $history->status);

                        $detailText = $history->note ?: $history->ghn_description;

                        $tc = $isNewest ? 'text-blue-600 dark:text-blue-400' : 'text-gray-600 dark:text-gray-400';
                        $fw = $isNewest ? 'font-bold' : 'font-medium';
                    @endphp
                    <tr class="bg-white dark:bg-[#1a1a1a]">
                        <td class="px-6 py-4 align-top">
                            <span class="{{ $tc }} {{ $fw }} text-[13px] leading-snug">{{ $historyLabel }}</span>
                        </td>
                        <td class="px-4 py-4 align-top">
                            @if ($detailText)
                                <span class="{{ $tc }} text-[13px] leading-relaxed">{{ $detailText }}</span>
                            @else
                                <span class="text-gray-400 text-[13px]">–</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right align-top whitespace-nowrap">
                            <span class="{{ $tc }} {{ $fw }} text-[13px]">{{ $history->created_at->format('H:i') }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                @endforeach
            </table>
            @else
            <p class="px-6 py-4 text-sm text-gray-500 italic">Chưa có lịch sử cập nhật.</p>
            @endif
        </div>
    </section>

    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        .dark .custom-scrollbar::-webkit-scrollbar-thumb { background: #475569; }
        .custom-scrollbar:hover::-webkit-scrollbar-thumb { background: #f4c025; }
    </style>
@endsection

@push('js')
<script type="module">
    document.addEventListener('DOMContentLoaded', function () {
        const currentUserId = {{ auth()->id() ?? 'null' }};
        const orderCode = '{{ $order->order_code }}'.toLowerCase();

        // 🚀 Dùng Interval để chờ Echo khởi tạo xong (Tránh lỗi load file JS chậm)
        const initEcho = setInterval(() => {
            if (window.Echo) {
                clearInterval(initEcho); // Dừng việc hỏi lại khi đã tìm thấy Echo
                console.log("✅ Timeline Real-time đã kết nối! Đang chờ lệnh...");

                window.Echo.channel('order-tracker')
                    .listen('.status-updated', (e) => {
                        console.log("🔥 CÓ BIẾN: ", e);

                        let fullText = (e.title + " " + e.message).toLowerCase();

                        // Kiểm tra đúng User và đúng Đơn hàng
                        if (e.targetUserId == currentUserId && fullText.includes(orderCode)) {

                            let newStep = 0;
                            let isCancelled = false;
                            let badgeHtml = '';

                            // Bắt chữ để xác định cấp độ (5 bước)
                            if (fullText.includes('chờ xác nhận')) {
                                newStep = 1;
                            }
                            else if (fullText.includes('lấy hàng') || fullText.includes('chuẩn bị') || fullText.includes('đóng gói')) {
                                newStep = 2;
                            }
                            else if (fullText.includes('kho') || fullText.includes('luân chuyển') || fullText.includes('phân loại')) {
                                newStep = 3;
                            }
                            else if (fullText.includes('đang giao') || fullText.includes('giao hàng')) {
                                newStep = 4;
                            }
                            else if (fullText.includes('thành công') || fullText.includes('đã giao') || fullText.includes('hoàn thành')) {
                                newStep = 5;
                            }

                            if (fullText.includes('hủy')) {
                                isCancelled = true;
                            }
                            else if (fullText.includes('thất bại') || fullText.includes('hoàn') || fullText.includes('ngoại lệ') || fullText.includes('hỏng') || fullText.includes('mất')) {
                                isCancelled = true;
                            }

                            // TỰ ĐỘNG ĐỔI NHÃN TRẠNG THÁI BÊN TRÊN (NẾU CÓ)
                            if(badgeHtml !== '') {
                                const badgeEl = document.getElementById('order-status-badge');
                                if(badgeEl) badgeEl.innerHTML = badgeHtml;
                            }

                            // TỰ ĐỘNG CHẠY THANH TIẾN TRÌNH BÊN DƯỚI
                            const normalView = document.getElementById('normal-progress-view');
                            const cancelView = document.getElementById('cancelled-view');
                            const borderColor = document.getElementById('timeline-border-color');

                            if (isCancelled) {
                                normalView.classList.add('hidden');
                                cancelView.classList.remove('hidden');
                                borderColor.classList.remove('bg-[#f4c025]');
                                borderColor.classList.add('bg-red-500');
                                document.getElementById('cancel-reason-text').innerText = "Lý do: " + e.message;
                            } else if (newStep > 0) {
                                normalView.classList.remove('hidden');
                                cancelView.classList.add('hidden');
                                borderColor.classList.remove('bg-red-500');
                                borderColor.classList.add('bg-[#f4c025]');

                                // Tính % kéo thanh Vàng
                                let width = '0%';
                                if (newStep == 1) width = '0%';
                                if (newStep == 2) width = '25%';
                                if (newStep == 3) width = '50%';
                                if (newStep == 4) width = '75%';
                                if (newStep == 5) width = '100%';
                                const barLine = document.getElementById('progress-bar-line');
                                if(barLine) barLine.style.width = width;

                                // Bật/tắt đèn cho từng Icon
                                const labels = ["ĐẶT ĐƠN", "CHỜ LẤY", "VẬN CHUYỂN", "ĐANG GIAO", "THÀNH CÔNG"];
                                for (let i = 1; i <= 5; i++) {
                                    let iconDiv = document.getElementById('step-icon-' + i);
                                    let textSpan = document.getElementById('step-text-' + i);
                                    if(!iconDiv || !textSpan) continue;

                                    let iconSpan = iconDiv.querySelector('span');
                                    iconSpan.classList.remove('animate-pulse', 'animate-bounce');

                                    if (i <= newStep) {
                                        iconDiv.className = 'step-icon w-14 h-14 rounded-full bg-[#f4c025] text-black shadow-[0_0_20px_rgba(244,192,37,0.4)] flex items-center justify-center border-4 border-white dark:border-[#1a1a1a] transition-all duration-500 mb-3';
                                        textSpan.className = 'text-[10px] sm:text-[11px] font-bold uppercase tracking-tight text-[#181611] dark:text-white text-center transition-colors duration-500';
                                        textSpan.innerText = labels[i-1];
                                        
                                        if(i == newStep) {
                                            if(newStep < 5) iconSpan.classList.add('animate-pulse');
                                            else iconSpan.classList.add('animate-bounce');
                                        }
                                    } else {
                                        iconDiv.className = 'step-icon w-14 h-14 rounded-full bg-gray-100 dark:bg-white/5 text-gray-400 flex items-center justify-center border-4 border-white dark:border-[#1a1a1a] transition-all duration-500 mb-3';
                                        textSpan.className = 'text-[10px] sm:text-[11px] font-bold uppercase tracking-tight text-gray-400 text-center transition-colors duration-500';
                                    }
                                }
                            }
                        }
                    });
            }
        }, 500); // Kiểm tra mỗi 0.5 giây
    });

    // --- GLOBAL FUNCTIONS FOR MODAL ---
    window.openReturnModal = function() {
        const modal = document.getElementById('return-request-modal');
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';
        }
    };

    window.closeReturnModal = function() {
        const modal = document.getElementById('return-request-modal');
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = '';
        }
    };

    window.toggleQty = function(checkbox, qtyId) {
        const qtyDiv = document.getElementById(qtyId);
        if (!qtyDiv) return;
        const qtyInput = qtyDiv.querySelector('input');
        if (checkbox.checked) {
            qtyDiv.classList.remove('hidden');
            qtyInput.disabled = false;
            qtyInput.focus();
        } else {
            qtyDiv.classList.add('hidden');
            qtyInput.disabled = true;
        }
    };

    window.handleReasonChange = function(select) {
        const otherContainer = document.getElementById('other_reason_container');
        const textarea = document.getElementById('reason_textarea');
        
        // Xóa input ẩn cũ để tránh trùng lặp
        const oldHidden = document.getElementById('hidden_reason_input');
        if (oldHidden) oldHidden.remove();

        if (select.value === 'Lý do khác') {
            otherContainer.classList.remove('hidden');
            textarea.required = true;
            textarea.name = 'reason'; 
            textarea.focus();
        } else {
            otherContainer.classList.add('hidden');
            textarea.required = false;
            textarea.name = 'reason_detail';
            
            if (select.value !== "") {
                const hiddenReason = document.createElement('input');
                hiddenReason.type = 'hidden';
                hiddenReason.id = 'hidden_reason_input';
                hiddenReason.name = 'reason';
                hiddenReason.value = select.value;
                select.parentNode.appendChild(hiddenReason);
            }
        }
    };

    // Đóng modal khi click ra ngoài
    document.addEventListener('click', function(e) {
        const returnModal = document.getElementById('return-request-modal');
        if (e.target === returnModal) {
            closeReturnModal();
        }
    });

    window.previewReturnImages = function(input) {
        const container = document.getElementById('image-preview-container');
        container.innerHTML = '';
        if (input.files && input.files.length > 0) {
            if (input.files.length > 5) {
                alert('Bạn chỉ có thể tải lên tối đa 5 ảnh!');
                input.value = ''; // clear
                container.classList.add('hidden');
                return;
            }
            container.classList.remove('hidden');
            Array.from(input.files).forEach(file => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const imgDiv = document.createElement('div');
                    imgDiv.className = 'relative group aspect-square rounded-2xl overflow-hidden border border-gray-200 dark:border-white/10 shadow-sm';
                    imgDiv.innerHTML = `
                        <img src="${e.target.result}" class="w-full h-full object-cover">
                    `;
                    container.appendChild(imgDiv);
                }
                reader.readAsDataURL(file);
            });
        } else {
            container.classList.add('hidden');
        }
    };

    window.removePreviewImage = function() {
        const input = document.getElementById('return_image');
        const container = document.getElementById('image-preview-container');
        input.value = '';
        container.classList.add('hidden');
    };
</script>
<style>
    @keyframes slideDown {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-slideDown {
        animation: slideDown 0.3s ease-out;
    }
</style>
@endpush
