@extends('client.profiles.layouts.app')

@section('title', 'Bee Phone - Chi tiết đơn hàng #' . $order->order_code)

@section('profile_content')
    <section class="flex-1" data-purpose="user-main-section">

        <div class="mb-6 flex items-center justify-between border-b border-gray-100 dark:border-white/10 pb-4">
            <div class="flex items-center gap-3">
                <a href="{{ route('client.orders.index') }}" class="w-10 h-10 flex items-center justify-center rounded-xl bg-gray-100 dark:bg-white/5 hover:bg-[#f4c025] hover:text-black transition-colors text-gray-600 dark:text-gray-300">
                    <span class="material-symbols-outlined">arrow_back</span>
                </a>
                <div>
                    <h1 class="text-2xl font-bold uppercase tracking-tight text-[#181611] dark:text-white">Chi tiết đơn hàng</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Mã đơn: <span class="font-bold text-[#f4c025]">#{{ $order->order_code }}</span></p>
                </div>
            </div>

            <div id="order-status-badge">
                @if($order->status == 'pending')
                    <span class="text-yellow-700 bg-yellow-100 border border-yellow-200 px-4 py-2 rounded-lg text-sm font-bold uppercase tracking-wider">Chờ xác nhận</span>
                @elseif($order->status == 'packing')
                    <span class="text-blue-700 bg-blue-100 border border-blue-200 px-4 py-2 rounded-lg text-sm font-bold uppercase tracking-wider">Đang đóng gói</span>
                @elseif($order->status == 'shipping')
                    <span class="text-indigo-700 bg-indigo-100 border border-indigo-200 px-4 py-2 rounded-lg text-sm font-bold uppercase tracking-wider">Đang giao hàng</span>
                @elseif($order->status == 'delivered')
                    <span class="text-teal-700 bg-teal-100 border border-teal-200 px-4 py-2 rounded-lg text-sm font-bold uppercase tracking-wider flex items-center gap-1"><span class="material-symbols-outlined text-[18px]">verified</span> Đã giao hàng</span>
                @elseif($order->status == 'completed')
                    <span class="text-green-700 bg-green-100 border border-green-200 px-4 py-2 rounded-lg text-sm font-bold uppercase tracking-wider flex items-center gap-1"><span class="material-symbols-outlined text-[18px]">check_circle</span> Đã hoàn thành</span>
                @elseif($order->status == 'cancelled')
                    <span class="text-red-700 bg-red-100 border border-red-200 px-4 py-2 rounded-lg text-sm font-bold uppercase tracking-wider flex items-center gap-1"><span class="material-symbols-outlined text-[18px]">cancel</span> Đã hủy</span>
                @endif
            </div>
        </div>

        @php
            $step = 0;
            if(in_array($order->status, ['pending'])) $step = 1;
            if(in_array($order->status, ['packing'])) $step = 2;
            if(in_array($order->status, ['shipping'])) $step = 3;
            if(in_array($order->status, ['delivered', 'completed'])) $step = 4;

            $progressWidth = '0%';
            if($step == 1) $progressWidth = '0%';
            if($step == 2) $progressWidth = '33%';
            if($step == 3) $progressWidth = '66%';
            if($step == 4) $progressWidth = '100%';
        @endphp

        <section class="bg-white dark:bg-[#1a1a1a] p-8 rounded-2xl mb-8 relative overflow-hidden shadow-sm border border-gray-100 dark:border-white/10" id="timeline-section">
            <div class="absolute top-0 left-0 w-1.5 h-full {{ $order->status == 'cancelled' ? 'bg-red-500' : 'bg-[#f4c025]' }}" id="timeline-border-color"></div>
            <h2 class="text-lg font-bold mb-8 uppercase tracking-tight text-[#181611] dark:text-white">Tiến trình xử lý</h2>

            <div id="cancelled-view" class="flex items-center gap-4 text-red-500 {{ $order->status == 'cancelled' ? '' : 'hidden' }}">
                <div class="w-14 h-14 rounded-full bg-red-100 flex items-center justify-center border-4 border-white dark:border-[#1a1a1a] shadow-lg">
                    <span class="material-symbols-outlined text-3xl">cancel</span>
                </div>
                <div>
                    <p class="font-bold text-lg">Đơn hàng đã bị hủy</p>
                    <p class="text-sm text-gray-500 mt-1" id="cancel-reason-text">Lý do: {{ $order->cancellation_reason ?? 'Khách hàng tự hủy' }}</p>
                </div>
            </div>

            <div id="normal-progress-view" class="relative flex items-center justify-between mt-4 {{ $order->status == 'cancelled' ? 'hidden' : '' }}">
                <div class="absolute top-6 left-0 w-full h-1.5 bg-gray-100 dark:bg-white/5 z-0 rounded-full">
                    <div id="progress-bar-line" class="h-full bg-[#f4c025] transition-all duration-1000 ease-in-out rounded-full shadow-[0_0_10px_rgba(244,192,37,0.5)]" style="width: {{ $progressWidth }}"></div>
                </div>

                <div class="relative z-10 flex flex-col items-center w-1/4">
                    <div id="step-icon-1" class="step-icon w-12 h-12 rounded-full {{ $step >= 1 ? 'bg-[#f4c025] text-black shadow-[0_0_15px_rgba(244,192,37,0.4)]' : 'bg-gray-100 dark:bg-white/5 text-gray-400' }} flex items-center justify-center border-4 border-white dark:border-[#1a1a1a] transition-all duration-500 mb-2">
                        <span class="material-symbols-outlined {{ $step == 1 ? 'animate-pulse' : '' }}">receipt_long</span>
                    </div>
                    <span id="step-text-1" class="text-xs sm:text-sm font-bold {{ $step >= 1 ? 'text-[#181611] dark:text-white' : 'text-gray-400' }} text-center transition-colors duration-500">Chờ xác nhận</span>
                </div>

                <div class="relative z-10 flex flex-col items-center w-1/4">
                    <div id="step-icon-2" class="step-icon w-12 h-12 rounded-full {{ $step >= 2 ? 'bg-[#f4c025] text-black shadow-[0_0_15px_rgba(244,192,37,0.4)]' : 'bg-gray-100 dark:bg-white/5 text-gray-400' }} flex items-center justify-center border-4 border-white dark:border-[#1a1a1a] transition-all duration-500 mb-2">
                        <span class="material-symbols-outlined {{ $step == 2 ? 'animate-pulse' : '' }}">inventory_2</span>
                    </div>
                    <span id="step-text-2" class="text-xs sm:text-sm font-bold {{ $step >= 2 ? 'text-[#181611] dark:text-white' : 'text-gray-400' }} text-center transition-colors duration-500">Đang đóng gói</span>
                </div>

                <div class="relative z-10 flex flex-col items-center w-1/4">
                    <div id="step-icon-3" class="step-icon w-12 h-12 rounded-full {{ $step >= 3 ? 'bg-[#f4c025] text-black shadow-[0_0_15px_rgba(244,192,37,0.4)]' : 'bg-gray-100 dark:bg-white/5 text-gray-400' }} flex items-center justify-center border-4 border-white dark:border-[#1a1a1a] transition-all duration-500 mb-2">
                        <span class="material-symbols-outlined {{ $step == 3 ? 'animate-pulse' : '' }}">local_shipping</span>
                    </div>
                    <span id="step-text-3" class="text-xs sm:text-sm font-bold {{ $step >= 3 ? 'text-[#181611] dark:text-white' : 'text-gray-400' }} text-center transition-colors duration-500">Đang giao hàng</span>
                </div>

                <div class="relative z-10 flex flex-col items-center w-1/4">
                    <div id="step-icon-4" class="step-icon w-12 h-12 rounded-full {{ $step >= 4 ? 'bg-[#f4c025] text-black shadow-[0_0_15px_rgba(244,192,37,0.4)]' : 'bg-gray-100 dark:bg-white/5 text-gray-400' }} flex items-center justify-center border-4 border-white dark:border-[#1a1a1a] transition-all duration-500 mb-2">
                        <span class="material-symbols-outlined">verified</span>
                    </div>
                    <span id="step-text-4" class="text-xs sm:text-sm font-bold {{ $step >= 4 ? 'text-[#181611] dark:text-white' : 'text-gray-400' }} text-center transition-colors duration-500">Giao thành công</span>
                </div>
            </div>
        </section>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <div class="lg:col-span-2 bg-white dark:bg-[#1a1a1a] p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-white/10 relative overflow-hidden group hover:border-[#f4c025] transition-colors">
                <div class="absolute top-0 right-0 w-32 h-32 bg-[#f4c025]/5 rounded-bl-full -z-10 transition-transform group-hover:scale-110"></div>

                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 rounded-full bg-[#f4c025]/20 flex items-center justify-center text-[#f4c025]">
                        <span class="material-symbols-outlined">location_on</span>
                    </div>
                    <h2 class="text-lg font-bold uppercase tracking-tight text-[#181611] dark:text-white">Thông tin nhận hàng</h2>
                </div>

                <div class="grid sm:grid-cols-2 gap-6">
                    <div>
                        <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-1">Người nhận</p>
                        <p class="text-base font-bold text-[#181611] dark:text-white">{{ $order->customer_name }}</p>
                        <p class="text-gray-500 dark:text-gray-400 mt-1 flex items-center gap-2">
                            <span class="material-symbols-outlined text-[16px]">call</span> {{ $order->customer_phone }}
                        </p>
                    </div>
                    <div>
                        <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-1">Địa chỉ giao hàng</p>
                        <p class="text-[#181611] dark:text-white leading-relaxed text-sm">
                            {{ $order->shipping_address ?? 'Không có thông tin địa chỉ' }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 dark:bg-white/5 p-6 rounded-2xl border border-gray-100 dark:border-white/10 flex flex-col justify-between">
                <div class="mb-6">
                    <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-2">Phương thức thanh toán</p>
                    <div class="flex items-center gap-3 font-bold text-[#181611] dark:text-white bg-white dark:bg-black/20 p-3 rounded-xl border border-gray-100 dark:border-white/5">
                        <span class="material-symbols-outlined text-[#f4c025]">payments</span>
                        @if($order->payment_method == 'cod')
                            Thanh toán khi nhận (COD)
                        @elseif($order->payment_method == 'vnpay')
                            Ví VNPay
                        @else
                            {{ $order->payment_method }}
                        @endif
                    </div>
                </div>
                <div>
                    <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-2">Tình trạng thanh toán</p>
                    @if($order->payment_status == 'paid')
                        <div class="flex items-center gap-2 text-green-600 font-bold bg-green-50 dark:bg-green-500/10 p-3 rounded-xl border border-green-100 dark:border-green-500/20">
                            <span class="material-symbols-outlined">verified</span> Đã thanh toán
                        </div>
                    @else
                        <div class="flex items-center gap-2 text-orange-600 font-bold bg-orange-50 dark:bg-orange-500/10 p-3 rounded-xl border border-orange-100 dark:border-orange-500/20">
                            <span class="material-symbols-outlined">pending_actions</span> Chưa thanh toán
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <section class="bg-white dark:bg-[#1a1a1a] rounded-2xl shadow-sm border border-gray-100 dark:border-white/10 mb-8 overflow-hidden">
            <div class="p-6 border-b border-gray-100 dark:border-white/10 flex justify-between items-center bg-gray-50/50 dark:bg-white/5">
                <h2 class="text-lg font-bold uppercase tracking-tight text-[#181611] dark:text-white flex items-center gap-2">
                    <span class="material-symbols-outlined text-[#f4c025]">inventory_2</span> Sản phẩm đã mua
                </h2>
                <span class="bg-white dark:bg-black/20 border border-gray-200 dark:border-white/10 px-3 py-1 rounded-full text-xs font-black text-[#181611] dark:text-[#f4c025]">
                    {{ $order->items->count() }} MẶT HÀNG
                </span>
            </div>

            <div class="max-h-[400px] overflow-y-auto custom-scrollbar divide-y divide-gray-100 dark:divide-white/10 p-2">
                @foreach($order->items as $item)
                    @php
                        $imageUrl = Str::startsWith($item->thumbnail, ['http://', 'https://']) ? $item->thumbnail : asset('storage/' . $item->thumbnail);
                        $baseName = $item->product_name;
                        $variantInfo = '';
                        if (preg_match('/^(.*?)\s*\((.*?)\)$/', $item->product_name, $matches)) {
                            $baseName = trim($matches[1]);
                            $variantInfo = trim($matches[2]);
                        }
                    @endphp
                    <div class="p-4 flex flex-col sm:flex-row gap-6 items-start hover:bg-gray-50 dark:hover:bg-white/5 rounded-xl transition-colors border-b border-gray-100 dark:border-white/5 last:border-0">
                        <div class="w-24 h-24 bg-gray-50 dark:bg-black/20 rounded-xl p-2 border border-gray-100 dark:border-white/5 flex-shrink-0">
                            <img src="{{ $imageUrl }}" alt="{{ $baseName }}" class="w-full h-full object-contain mix-blend-multiply dark:mix-blend-normal">
                        </div>
                        <div class="flex-grow w-full text-left">
                            <div class="flex justify-between items-start gap-4 flex-col sm:flex-row">
                                <div>
                                    <h3 class="text-base font-bold text-[#181611] dark:text-white">{{ $baseName }}</h3>
                                    @if($variantInfo)
                                        <p class="text-xs text-gray-500 mt-1">Phân loại: {{ $variantInfo }}</p>
                                    @endif
                                    <p class="text-xs font-bold text-gray-500 uppercase tracking-widest mt-2 bg-gray-100 dark:bg-white/10 w-fit px-2 py-1 rounded-md">Số lượng: x{{ $item->quantity }}</p>
                                </div>
                                <div class="text-right flex flex-col justify-end items-end gap-2">
                                    <p class="text-lg font-bold text-red-500">{{ number_format($item->unit_price, 0, ',', '.') }}₫</p>
                                    @if(in_array($order->status, [\App\Models\Order::STATUS_DELIVERED, \App\Models\Order::STATUS_RECEIVED]) && $item->product)
                                        @php $prodParam = $item->product->slug ?: $item->product->id; @endphp
                                        <a href="{{ route('client.product.detail', ['id' => $prodParam]) }}" class="px-3 py-1.5 bg-blue-50 text-blue-600 border border-blue-200 font-bold rounded-lg hover:bg-blue-600 hover:text-white transition-colors text-xs whitespace-nowrap">
                                            Mua lại
                                        </a>
                                    @endif
                                </div>
                            </div>

                            {{-- Giao diện Hoàn trả cho riêng sản phẩm này --}}
                            <div class="mt-4 pt-4 border-t border-dashed border-gray-200 dark:border-white/10">
                                @if ($item->canRequestReturn())
                                    <form action="{{ route('client.orders.return', $item->id) }}" method="POST" enctype="multipart/form-data" class="w-full rounded-xl border border-amber-200 bg-amber-50 p-4">
                                        @csrf
                                        @method('PATCH')
                                        <label class="mb-2 block text-sm font-semibold text-amber-900">Yêu cầu đổi / trả cho sản phẩm này</label>
                                        <textarea name="return_note" rows="2" class="w-full rounded-lg border border-amber-200 bg-white px-3 py-2 text-sm text-slate-800" placeholder="Nhập lý do đổi trả..." required></textarea>
                                        <div class="mt-3">
                                            <input type="file" name="return_image" accept=".jpg,.jpeg,.png,.webp" class="w-full rounded-lg border border-amber-200 bg-white px-3 py-2 text-sm text-slate-800" required>
                                        </div>
                                        <div class="mt-3 flex justify-end">
                                            <button type="submit" class="rounded-lg bg-amber-500 px-5 py-2 text-sm font-semibold text-black transition hover:bg-amber-400">Gửi yêu cầu hoản trả</button>
                                        </div>
                                    </form>
                                @elseif ($item->return_status !== \App\Models\OrderItem::RETURN_NONE)
                                    <div class="space-y-2">
                                        @if ($item->return_status === \App\Models\OrderItem::RETURN_REQUESTED)
                                            <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-2 text-sm text-amber-800">Sản phẩm này đang chờ duyệt trả hàng.</div>
                                        @elseif ($item->return_status === \App\Models\OrderItem::RETURN_APPROVED)
                                            <form action="{{ route('client.orders.return.shipped', $item->id) }}" method="POST" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="rounded-lg bg-blue-600 px-5 py-2 text-sm font-semibold text-white transition hover:bg-blue-700">Xác nhận tôi đã gửi hàng hoàn trả</button>
                                            </form>
                                        @else
                                            @php
                                                $rtClasses = [
                                                    \App\Models\OrderItem::RETURN_REJECTED => 'text-red-700 bg-red-100 border-red-200',
                                                    \App\Models\OrderItem::RETURN_CUSTOMER_SHIPPED => 'text-indigo-700 bg-indigo-100 border-indigo-200',
                                                    \App\Models\OrderItem::RETURN_RECEIVED => 'text-cyan-700 bg-cyan-100 border-cyan-200',
                                                    \App\Models\OrderItem::RETURN_REFUNDED => 'text-green-700 bg-green-100 border-green-200',
                                                ];
                                                $rtClass = $rtClasses[$item->return_status] ?? 'text-gray-700 bg-gray-100';
                                            @endphp
                                            <div class="rounded-xl border px-4 py-2 text-sm font-semibold {{ $rtClass }}">
                                                Trạng thái hoàn trả: {{ \App\Models\OrderItem::returnStatusLabels()[$item->return_status] ?? $item->return_status }}
                                            </div>
                                        @endif

                                        {{-- Hiển thị lý do và phản hồi admin nếu có --}}
                                        @if($item->return_admin_note)
                                            <div class="rounded-xl border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-800 mt-2">
                                                <span class="font-semibold">Phản hồi của shop:</span>
                                                {{ $item->return_admin_note }}
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-start">

            <div class="flex flex-col gap-4">
                @if($order->status == \App\Models\Order::STATUS_PENDING)
                    <form action="{{ route('client.orders.cancel', $order->id) }}" method="POST" class="bg-gray-50 dark:bg-white/5 p-4 rounded-2xl border border-gray-100 dark:border-white/10">
                        @csrf
                        @method('PATCH')
                        <div class="mb-4">
                            <label class="block text-sm font-bold text-[#181611] dark:text-gray-300 mb-2">Vui lòng chọn lý do hủy đơn</label>
                            <select name="cancellation_reason" required class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm text-[#181611] focus:outline-none focus:ring-2 focus:ring-[#f4c025]/50 focus:border-[#f4c025] dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white transition-all shadow-sm">
                                <option value="">--- Chọn lý do hủy ---</option>
                                <option value="Thay đổi ý định">Thay đổi ý định (Không muốn mua nữa)</option>
                                <option value="Tìm thấy giá rẻ hơn ở nơi khác">TÌm thấy shop khác bán rẻ hơn</option>
                                <option value="Đổi địa chỉ/sđt nhận hàng">Muốn thay đổi địa chỉ hoặc SĐT nhận hàng</option>
                                <option value="Đặt nhầm sản phẩm/số lượng">Đặt nhầm sản phẩm hoặc sai số lượng</option>
                                <option value="Lý do khác">Lý do khác</option>
                            </select>
                        </div>
                        <button type="submit" class="w-full bg-red-50 dark:bg-red-500/10 text-red-600 border border-red-200 dark:border-red-500/20 font-bold py-3.5 rounded-xl flex items-center justify-center gap-2 hover:bg-red-500 hover:text-white transition-all active:scale-95 relative overflow-hidden group" onclick="return confirm('Bạn có chắc chắn muốn hủy đơn hàng này? Thao tác này KHÔNG THỂ hoàn tác.')">
                            <div class="absolute inset-0 bg-red-600 translate-y-full group-hover:translate-y-0 transition-transform duration-300 ease-in-out"></div>
                            <span class="material-symbols-outlined relative z-10">cancel</span>
                            <span class="relative z-10">Hủy đơn hàng</span>
                        </button>
                    </form>
                @endif

                @if($order->status == \App\Models\Order::STATUS_DELIVERED)
                    <form action="{{ route('client.orders.confirm', $order->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="w-full bg-green-500 text-white font-bold py-4 rounded-xl flex items-center justify-center gap-2 hover:bg-green-600 shadow-lg shadow-green-500/20 transition-all active:scale-95" onclick="return confirm('Bạn xác nhận đã nhận được hàng?')">
                            <span class="material-symbols-outlined">check_circle</span> Đã nhận được hàng
                        </button>
                    </form>
                @endif

                <a href="{{ route('client.products.index') }}" class="w-full bg-white dark:bg-transparent border border-gray-200 dark:border-white/20 font-bold py-4 rounded-xl flex items-center justify-center gap-2 text-[#181611] dark:text-white hover:border-[#f4c025] hover:text-[#f4c025] transition-colors">
                    <span class="material-symbols-outlined">shopping_bag</span> Tiếp tục mua sắm
                </a>
            </div>

            <div class="bg-[#181611] text-white p-6 rounded-2xl shadow-xl border border-gray-800 relative overflow-hidden">
                <span class="material-symbols-outlined absolute -bottom-4 -right-4 text-8xl text-white/5 rotate-12 pointer-events-none">receipt_long</span>

                <h3 class="text-base font-bold mb-5 uppercase tracking-widest text-[#f4c025]">Tóm tắt thanh toán</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between text-gray-400">
                        <span>Tạm tính</span>
                        <span class="text-white">{{ number_format($order->total_price ?? 0, 0, ',', '.') }}₫</span>
                    </div>
                    <div class="flex justify-between text-gray-400">
                        <span>Phí vận chuyển</span>
                        <span class="text-green-400">Miễn phí</span>
                    </div>
                    @if($order->total_price > $order->total_amount)
                    <div class="flex justify-between text-gray-400">
                        <span>Giảm giá (Voucher)</span>
                        <span class="text-red-400">-{{ number_format($order->total_price - $order->total_amount, 0, ',', '.') }}₫</span>
                    </div>
                    @endif

                    <div class="pt-4 mt-2 border-t border-gray-800 flex justify-between items-end">
                        <div>
                            <p class="text-[10px] text-gray-500 uppercase font-bold tracking-widest">Tổng cộng (Đã VAT)</p>
                            <p class="text-3xl font-bold text-[#f4c025] mt-1">{{ number_format($order->total_amount, 0, ',', '.') }}₫</p>
                        </div>
                    </div>
                </div>
            </div>
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

                            // Bắt chữ để xác định cấp độ
                            if (fullText.includes('chờ xác nhận')) {
                                newStep = 1;
                                badgeHtml = `<span class="text-yellow-700 bg-yellow-100 border border-yellow-200 px-4 py-2 rounded-lg text-sm font-bold uppercase tracking-wider">Chờ xác nhận</span>`;
                            }
                            else if (fullText.includes('đóng gói')) {
                                newStep = 2;
                                badgeHtml = `<span class="text-blue-700 bg-blue-100 border border-blue-200 px-4 py-2 rounded-lg text-sm font-bold uppercase tracking-wider">Đang đóng gói</span>`;
                            }
                            else if (fullText.includes('giao hàng') || fullText.includes('đang giao')) {
                                newStep = 3;
                                badgeHtml = `<span class="text-indigo-700 bg-indigo-100 border border-indigo-200 px-4 py-2 rounded-lg text-sm font-bold uppercase tracking-wider">Đang giao hàng</span>`;
                            }
                            else if (fullText.includes('thành công') || fullText.includes('đã giao') || fullText.includes('hoàn thành')) {
                                newStep = 4;
                                badgeHtml = `<span class="text-green-700 bg-green-100 border border-green-200 px-4 py-2 rounded-lg text-sm font-bold uppercase tracking-wider flex items-center gap-1"><span class="material-symbols-outlined text-[18px]">check_circle</span> Đã hoàn thành</span>`;
                            }
                            else if (fullText.includes('hủy')) {
                                isCancelled = true;
                                badgeHtml = `<span class="text-red-700 bg-red-100 border border-red-200 px-4 py-2 rounded-lg text-sm font-bold uppercase tracking-wider flex items-center gap-1"><span class="material-symbols-outlined text-[18px]">cancel</span> Đã hủy</span>`;
                            }

                            // TỰ ĐỘNG ĐỔI NHÃN TRẠNG THÁI BÊN TRÊN
                            if(badgeHtml !== '') {
                                document.getElementById('order-status-badge').innerHTML = badgeHtml;
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
                                if (newStep == 2) width = '33%';
                                if (newStep == 3) width = '66%';
                                if (newStep == 4) width = '100%';
                                document.getElementById('progress-bar-line').style.width = width;

                                // Bật/tắt đèn cho từng Icon
                                for (let i = 1; i <= 4; i++) {
                                    let iconDiv = document.getElementById('step-icon-' + i);
                                    let textSpan = document.getElementById('step-text-' + i);
                                    let iconSpan = iconDiv.querySelector('span');

                                    iconSpan.classList.remove('animate-pulse'); // Xóa chớp nháy cũ

                                    if (i <= newStep) {
                                        // Đã qua -> Sáng màu Vàng
                                        iconDiv.className = 'step-icon w-12 h-12 rounded-full bg-[#f4c025] text-black shadow-[0_0_15px_rgba(244,192,37,0.4)] flex items-center justify-center border-4 border-white dark:border-[#1a1a1a] transition-all duration-500 mb-2';
                                        textSpan.className = 'text-xs sm:text-sm font-bold text-[#181611] dark:text-white text-center transition-colors duration-500';
                                        // Cục hiện tại -> Nhấp nháy
                                        if(i == newStep && newStep < 4) iconSpan.classList.add('animate-pulse');
                                    } else {
                                        // Chưa tới -> Xám xịt
                                        iconDiv.className = 'step-icon w-12 h-12 rounded-full bg-gray-100 dark:bg-white/5 text-gray-400 flex items-center justify-center border-4 border-white dark:border-[#1a1a1a] transition-all duration-500 mb-2';
                                        textSpan.className = 'text-xs sm:text-sm font-bold text-gray-400 text-center transition-colors duration-500';
                                    }
                                }
                            }
                        }
                    });
            }
        }, 500); // Kiểm tra mỗi 0.5 giây
    });
</script>
@endpush
