@extends('admin.layouts.app')

@section('content')
@php
    $returnImageUrl = null;
    if ($order->return_image) {
        $returnImageUrl = \Illuminate\Support\Str::startsWith($order->return_image, ['http://', 'https://', 'uploads/'])
            ? asset($order->return_image)
            : asset('storage/' . $order->return_image);
    }
@endphp
<div class="p-8 space-y-6">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Order Detail</p>
            <h1 class="text-3xl font-bold text-slate-900 dark:text-white mt-1">Đơn hàng {{ $order->order_code }}</h1>
            <p class="text-sm text-slate-500 dark:text-slate-300 mt-1">Theo dõi thông tin người đặt, người nhận và xử lý trạng thái.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.orders.print.pdf', $order) }}" class="px-4 py-2 rounded-lg border border-slate-200 dark:border-slate-700 text-sm font-semibold hover:bg-slate-50 dark:hover:bg-slate-800">
                In PDF
            </a>
            <a href="{{ route('admin.orders.index') }}" class="px-4 py-2 rounded-lg border border-slate-200 dark:border-slate-700 text-sm font-semibold hover:bg-slate-50 dark:hover:bg-slate-800">
                Quay lại danh sách
            </a>
        </div>
    </div>

    @if (session('status'))
    <div class="p-3 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl text-sm">
        {{ session('status') }}
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

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Tổng tiền -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-emerald-100 dark:border-emerald-800/30 p-5 shadow-sm relative overflow-hidden group">
            <div class="absolute right-0 top-0 w-24 h-full bg-gradient-to-l from-emerald-50 dark:from-emerald-900/10 to-transparent"></div>
            <div class="relative z-10 flex flex-col justify-between h-full">
                <div class="flex items-center gap-2 mb-3">
                    <span class="material-symbols-outlined text-emerald-500 text-[20px] bg-emerald-50 dark:bg-emerald-900/30 p-1.5 rounded-lg">payments</span>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Tổng doanh thu</p>
                </div>
                <p class="text-2xl font-black text-emerald-600 dark:text-emerald-400 group-hover:scale-105 origin-left transition-transform duration-300">{{ number_format($order->total_amount) }} <span class="text-base font-semibold">₫</span></p>
            </div>
        </div>

        <!-- Trạng thái đơn -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-blue-100 dark:border-blue-800/30 p-5 shadow-sm relative overflow-hidden group">
            <div class="absolute right-0 top-0 w-24 h-full bg-gradient-to-l from-blue-50 dark:from-blue-900/10 to-transparent"></div>
            <div class="relative z-10 flex flex-col justify-between h-full">
                <div class="flex items-center gap-2 mb-3">
                    <span class="material-symbols-outlined text-blue-500 text-[20px] bg-blue-50 dark:bg-blue-900/30 p-1.5 rounded-lg">local_shipping</span>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Trạng thái đơn</p>
                </div>
                <p class="text-[15px] leading-tight font-bold text-slate-800 dark:text-slate-100 uppercase">{{ $statusLabels[$order->status] ?? $order->status }}</p>
            </div>
        </div>

        <!-- Hoàn hàng -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-amber-100 dark:border-amber-800/30 p-5 shadow-sm relative overflow-hidden group">
            <div class="absolute right-0 top-0 w-24 h-full bg-gradient-to-l from-amber-50 dark:from-amber-900/10 to-transparent"></div>
            <div class="relative z-10 flex flex-col justify-between h-full">
                <div class="flex items-center gap-2 mb-3">
                    <span class="material-symbols-outlined text-amber-500 text-[20px] bg-amber-50 dark:bg-amber-900/30 p-1.5 rounded-lg">assignment_return</span>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Trạng thái trả</p>
                </div>
                <p class="text-[15px] leading-tight font-bold text-slate-800 dark:text-slate-100 uppercase">{{ $returnStatusLabels[$order->return_status] ?? $order->return_status }}</p>
            </div>
        </div>

        <!-- Hoàn tiền -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-purple-100 dark:border-purple-800/30 p-5 shadow-sm relative overflow-hidden group">
            <div class="absolute right-0 top-0 w-24 h-full bg-gradient-to-l from-purple-50 dark:from-purple-900/10 to-transparent"></div>
            <div class="relative z-10 flex flex-col justify-between h-full">
                <div class="flex items-center gap-2 mb-3">
                    <span class="material-symbols-outlined text-purple-500 text-[20px] bg-purple-50 dark:bg-purple-900/30 p-1.5 rounded-lg">account_balance_wallet</span>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Tổng hoàn tiền</p>
                </div>
                <p class="text-xl font-black text-purple-600 dark:text-purple-400 group-hover:scale-105 origin-left transition-transform duration-300">{{ $order->refund_amount ? number_format($order->refund_amount) . ' ₫' : '0 ₫' }}</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-100 dark:border-slate-800 p-6 space-y-4">
                <div class="flex items-center gap-2 mb-2">
                    <span class="material-symbols-outlined text-primary text-[22px]">account_box</span>
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white">Thông tin khách hàng & Giao hàng</h2>
                </div>

                <div class="p-5 rounded-xl bg-slate-50 border border-slate-100 dark:bg-slate-800/50 dark:border-slate-800 relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-1 h-full bg-blue-500"></div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-3">
                            <p class="text-[11px] uppercase text-slate-400 font-bold tracking-wider mb-2">Thông tin liên hệ</p>
                            <p class="font-bold text-slate-900 dark:text-white text-base flex items-center gap-2">
                                <span class="material-symbols-outlined text-[16px] text-slate-400">person</span>
                                {{ $order->customer_name }}
                            </p>
                            <p class="text-slate-600 dark:text-slate-300 text-sm font-medium flex items-center gap-2">
                                <span class="material-symbols-outlined text-[16px] text-slate-400">call</span>
                                {{ $order->customer_phone }}
                            </p>
                            <p class="text-slate-500 text-sm flex items-center gap-2">
                                <span class="material-symbols-outlined text-[16px] text-slate-400">mail</span>
                                {{ $order->customer_email ?: 'Chưa có email' }}
                            </p>
                        </div>
                        <div class="space-y-3">
                            <p class="text-[11px] uppercase text-slate-400 font-bold tracking-wider mb-2">Địa chỉ nhận hàng</p>
                            <div class="text-slate-600 dark:text-slate-300 text-sm flex items-start gap-2 bg-white dark:bg-slate-800 p-3 rounded-lg border border-slate-100 dark:border-slate-700 h-[calc(100%-2rem)]">
                                <span class="material-symbols-outlined text-[16px] text-emerald-500 mt-0.5">location_on</span>
                                <span class="flex-1 leading-relaxed">{{ $order->shipping_address ?: 'Chưa có địa chỉ' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-slate-50 dark:bg-slate-800/30 rounded-xl p-4 mt-2">
                    <div class="flex items-center gap-3">
                        <div class="bg-primary/20 p-2.5 rounded-lg text-[#d4a010] dark:text-primary">
                            <span class="material-symbols-outlined text-[20px]">calendar_month</span>
                        </div>
                        <div>
                            <p class="text-[11px] text-slate-500 font-bold uppercase tracking-wider">Ngày đặt</p>
                            <p class="font-bold text-slate-900 dark:text-white mt-0.5 text-sm">{{ optional($order->ordered_at)->format('d/m/Y H:i') ?? $order->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="bg-primary/20 p-2.5 rounded-lg text-[#d4a010] dark:text-primary">
                            <span class="material-symbols-outlined text-[20px]">tag</span>
                        </div>
                        <div>
                            <p class="text-[11px] text-slate-500 font-bold uppercase tracking-wider">Mã đơn hàng</p>
                            <p class="font-bold text-primary mt-0.5 text-sm uppercase tracking-wide">{{ $order->order_code }}</p>
                        </div>
                    </div>
                </div>

                @if($order->note)
                <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-100 dark:border-amber-800/50 rounded-xl p-4 mt-2">
                    <p class="text-[11px] text-amber-600 dark:text-amber-500 font-bold uppercase tracking-wider flex items-center gap-1.5 mb-1.5">
                        <span class="material-symbols-outlined text-[14px]">edit_note</span> Ghi chú đơn hàng từ khách
                    </p>
                    <p class="text-[15px] font-semibold text-amber-900 dark:text-amber-100 italic">"{{ $order->note }}"</p>
                </div>
                @endif
            </div>

            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-100 dark:border-slate-800 p-6 space-y-4">
                <div class="flex items-center justify-between gap-3">
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white">Sản phẩm đã mua</h2>
                    <span class="text-xs text-slate-500">{{ $order->items->sum('quantity') }} sản phẩm</span>
                </div>

                @if ($order->items->isNotEmpty())
                <div class="overflow-x-auto rounded-xl border border-slate-100 dark:border-slate-800">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-50 dark:bg-slate-800/50 text-slate-500">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold">Sản phẩm</th>
                                <th class="px-4 py-3 text-left font-semibold">Phân loại</th>
                                <th class="px-4 py-3 text-center font-semibold">Số lượng</th>
                                <th class="px-4 py-3 text-right font-semibold">Giá (lúc mua)</th>
                                <th class="px-4 py-3 text-right font-semibold">Thành tiền</th>
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
                                <td class="px-4 py-3 text-center text-slate-700 dark:text-slate-200 font-medium">x{{ $item->quantity }}</td>
                                <td class="px-4 py-3 text-right text-slate-700 dark:text-slate-200">{{ number_format($item->unit_price) }} ₫</td>
                                <td class="px-4 py-3 text-right font-semibold text-slate-900 dark:text-white">{{ number_format($item->line_total) }} ₫</td>
                            </tr>
                            
                            {{-- Giao diện thao tác Hoàn Trả cho riêng từng Item --}}
                            @if ($item->return_status !== \App\Models\OrderItem::RETURN_NONE)
                            <tr class="bg-amber-50/30 dark:bg-amber-900/10">
                                <td colspan="5" class="px-4 py-4">
                                    <div class="flex flex-col gap-6 items-start w-full">
                                        <div class="w-full space-y-3">
                                            <div class="flex items-center gap-2">
                                                <span class="px-2.5 py-1 rounded-md text-xs font-bold uppercase tracking-wider bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-500 border border-amber-200 dark:border-amber-800/50">
                                                    Yêu cầu hoàn trả
                                                </span>
                                                <span class="text-sm font-semibold text-slate-700 dark:text-slate-300">
                                                    Trạng thái: <span class="text-primary">{{ \App\Models\OrderItem::returnStatusLabels()[$item->return_status] ?? $item->return_status }}</span>
                                                </span>
                                            </div>
                                            
                                            <div class="bg-white dark:bg-slate-800/50 rounded-lg p-3 border border-slate-200 dark:border-slate-700 flex gap-4 items-start">
                                                @if($item->return_image)
                                                    @php
                                                        $rImgUrl = \Illuminate\Support\Str::startsWith($item->return_image, ['http://', 'https://', 'uploads/'])
                                                            ? asset($item->return_image)
                                                            : asset('storage/' . $item->return_image);
                                                    @endphp
                                                    <a href="{{ $rImgUrl }}" target="_blank" rel="noopener" class="shrink-0 block mt-1 hover:opacity-80 transition-opacity">
                                                        <img src="{{ $rImgUrl }}" alt="Ảnh bằng chứng" class="w-16 h-16 rounded border border-slate-200 dark:border-slate-600 object-cover">
                                                    </a>
                                                @else
                                                    <div class="w-16 h-16 shrink-0 rounded border border-dashed border-slate-300 dark:border-slate-600 flex flex-col items-center justify-center text-slate-400 bg-slate-50 dark:bg-slate-800">
                                                        <span class="material-symbols-outlined text-[20px]">no_photography</span>
                                                        <span class="text-[9px] mt-1">Không có ảnh</span>
                                                    </div>
                                                @endif
                                                <div class="flex-1 min-w-0">
                                                    <h4 class="text-xs font-semibold text-slate-500 uppercase">Lý do khách hàng</h4>
                                                    <p class="text-sm text-slate-900 dark:text-white mt-1 italic whitespace-pre-wrap break-words leading-relaxed">"{{ $item->return_note }}"</p>
                                                    <div class="mt-2 text-xs text-slate-500 flex items-center gap-1">
                                                        <span class="material-symbols-outlined text-[14px]">schedule</span>
                                                        Ngày yêu cầu: {{ optional($item->return_requested_at)->format('d/m/Y H:i') }}
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            @if($item->return_admin_note)
                                                <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-3 border border-blue-200 dark:border-blue-800/30">
                                                    <h4 class="text-xs font-semibold text-blue-600 dark:text-blue-400 uppercase">Phản hồi của Shop</h4>
                                                    <p class="text-sm text-blue-900 dark:text-blue-100 mt-1">{{ $item->return_admin_note }}</p>
                                                </div>
                                            @endif
                                        </div>
                                        
                                        <div class="w-full bg-white dark:bg-slate-800/80 rounded-xl border border-slate-200 dark:border-slate-700 p-4 shadow-sm">
                                            <h4 class="text-sm font-bold text-slate-900 dark:text-white mb-3 flex items-center gap-2">
                                                <span class="material-symbols-outlined text-[18px]">verified_user</span> Xử lý yêu cầu
                                            </h4>
                                            
                                            @if ($item->canApproveReturn())
                                                <form action="{{ route('admin.orders.return.approve', $item->id) }}" method="POST" class="mb-3">
                                                    @csrf
                                                    @method('PATCH')
                                                    <textarea name="return_admin_note" rows="2" class="w-full text-sm rounded-lg border-slate-200 dark:border-slate-600 dark:bg-slate-900/50 dark:text-white placeholder-slate-400 mb-2 focus:ring-primary focus:border-primary transition-shadow" placeholder="Ghi chú duyệt (mặc định trống)..."></textarea>
                                                    <button type="submit" class="w-full px-3 py-2 bg-blue-600 text-white rounded-lg font-semibold text-sm hover:bg-blue-700 transition-colors shadow-sm shadow-blue-500/20 flex items-center justify-center gap-1">
                                                        <span class="material-symbols-outlined text-[16px]">how_to_reg</span> Duyệt yêu cầu
                                                    </button>
                                                </form>
                                                
                                                <form action="{{ route('admin.orders.return.reject', $item->id) }}" method="POST" class="pt-3 border-t border-slate-100 dark:border-slate-700" onsubmit="return confirm('Bạn có chắc chắn muốn TỪ CHỐI hoàn sản phẩm này?');">
                                                    @csrf
                                                    @method('PATCH')
                                                    <textarea name="return_admin_note" rows="2" class="w-full text-sm rounded-lg border-slate-200 dark:border-slate-600 dark:bg-slate-900/50 dark:text-white placeholder-slate-400 mb-2 focus:ring-red-500 focus:border-red-500 transition-shadow" placeholder="Lý do từ chối (bắt buộc)..." required></textarea>
                                                    <button type="submit" class="w-full px-3 py-2 bg-red-50 text-red-600 border border-red-200 rounded-lg font-semibold text-sm hover:bg-red-50 transition-colors flex items-center justify-center gap-1">
                                                        <span class="material-symbols-outlined text-[16px]">cancel</span> Từ chối hoàn
                                                    </button>
                                                </form>
                                            @elseif ($item->return_status === \App\Models\OrderItem::RETURN_APPROVED)
                                                <div class="text-sm text-blue-600 dark:text-blue-400 flex items-center gap-2 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                                                    <span class="material-symbols-outlined shrink-0 text-[18px]">local_shipping</span> 
                                                    <span>Đã duyệt. Đang chờ khách gửi hàng về kho.</span>
                                                </div>
                                            @elseif ($item->canMarkReturnReceived())
                                                <form action="{{ route('admin.orders.return.received', $item->id) }}" method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <div class="p-3 bg-indigo-50 dark:bg-indigo-900/20 rounded-lg border border-indigo-100 dark:border-indigo-800/30 mb-3 text-sm text-indigo-700 dark:text-indigo-300">
                                                        Khách đã gửi hàng hoàn qua DV vận chuyển.
                                                    </div>
                                                    <textarea name="return_admin_note" rows="2" class="w-full text-sm rounded-lg border-slate-200 dark:border-slate-600 dark:bg-slate-900/50 dark:text-white placeholder-slate-400 mb-2 focus:ring-cyan-500 focus:border-cyan-500 transition-shadow" placeholder="Tình trạng SP sau khi nhận..."></textarea>
                                                    <button type="submit" class="w-full px-3 py-2 bg-cyan-600 text-white rounded-lg font-semibold text-sm hover:bg-cyan-700 transition-colors shadow-sm shadow-cyan-500/20 flex items-center justify-center gap-1">
                                                        <span class="material-symbols-outlined text-[16px]">inventory_2</span> Xác nhận đã thu hồi hàng
                                                    </button>
                                                </form>
                                            @elseif ($item->canRefundReturn())
                                                @php $itemRefundAmount = $item->calculateRefundAmount(); @endphp
                                                <form action="{{ route('admin.orders.return.refund', $item->id) }}" method="POST" onsubmit="return confirm('Bạn xác nhận sẽ hoàn số tiền này vào Ví người dùng? Hành động này không thể hoàn tác!');">
                                                    @csrf
                                                    @method('PATCH')
                                                    <div class="p-3 bg-emerald-50 dark:bg-emerald-900/20 rounded-lg border border-emerald-100 dark:border-emerald-800/30 mb-3 text-sm">
                                                        <p class="text-emerald-800 dark:text-emerald-300">Đã nhận hàng thành công. Vui lòng hoàn tiền cho khách.</p>
                                                        <div class="flex justify-between items-end mt-2 pt-2 border-t border-emerald-200/50 dark:border-emerald-800/50">
                                                            <span class="text-xs uppercase text-emerald-600 dark:text-emerald-400 font-bold">Số tiền hoàn (trừ Voucher)</span>
                                                            <span class="text-lg font-bold text-emerald-700 dark:text-emerald-300">{{ number_format($itemRefundAmount) }} ₫</span>
                                                        </div>
                                                    </div>
                                                    <button type="submit" class="w-full px-3 py-2 bg-emerald-600 text-white rounded-lg font-semibold text-sm hover:bg-emerald-700 transition-colors shadow-sm shadow-emerald-500/20 flex items-center justify-center gap-1">
                                                        <span class="material-symbols-outlined text-[16px]">account_balance_wallet</span> Hoàn tiền vào Ví Bee Pay
                                                    </button>
                                                </form>
                                            @elseif ($item->return_status === \App\Models\OrderItem::RETURN_REJECTED)
                                                <div class="text-sm text-red-600 dark:text-red-400 flex items-center gap-2 p-3 bg-red-50 dark:bg-red-900/20 rounded-lg">
                                                    <span class="material-symbols-outlined shrink-0 text-[18px]">block</span> 
                                                    <span>Yêu cầu hoàn sản phẩm này đã bị từ chối.</span>
                                                </div>
                                            @elseif ($item->return_status === \App\Models\OrderItem::RETURN_REFUNDED)
                                                <div class="text-sm text-emerald-700 dark:text-emerald-300 flex items-start gap-2 p-3 bg-emerald-50 dark:bg-emerald-900/20 rounded-lg border border-emerald-100 dark:border-emerald-800/30">
                                                    <span class="material-symbols-outlined shrink-0 text-[18px] text-emerald-500">check_circle</span> 
                                                    <div>
                                                        <p class="font-bold">Đã hoàn tất quá trình hoàn trả.</p>
                                                        <p class="text-xs mt-1 text-emerald-600 dark:text-emerald-400 flex justify-between">
                                                            <span>Thực nhận ví:</span>
                                                            <span class="font-bold">{{ number_format($item->refund_amount) }} ₫</span>
                                                        </p>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @endif
                        </tbody>
                        @endforeach
                    </table>
                </div>
                @else
                <div class="p-4 rounded-lg border border-dashed border-slate-200 dark:border-slate-700 text-sm text-slate-500">
                    Đơn hàng này chưa có dữ liệu chi tiết sản phẩm.
                </div>
                @endif

                {{-- Tóm tắt thanh toán --}}
                @php
                    $subtotal = $order->items->sum('line_total');
                    $shippingFee = $order->shipping_fee ?? 0;
                    $discountAmount = ($subtotal + $shippingFee) - $order->total_amount;
                    if($discountAmount < 0) $discountAmount = 0;
                @endphp
                <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-800 space-y-3">
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-slate-500 font-medium tracking-tight">Tạm tính (Tiền hàng):</span>
                        <span class="text-slate-900 dark:text-white font-bold">{{ number_format($subtotal) }} ₫</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-slate-500 font-medium tracking-tight">Phí vận chuyển:</span>
                        <span class="{{ $shippingFee <= 0 ? 'text-emerald-500' : 'text-slate-900 dark:text-white' }} font-bold">
                            {{ $shippingFee <= 0 ? 'Miễn phí' : number_format($shippingFee) . ' ₫' }}
                        </span>
                    </div>
                    @if($discountAmount > 0)
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-slate-500 font-medium tracking-tight flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[16px] text-red-500">redeem</span> Giảm giá (Voucher):
                        </span>
                        <span class="text-red-500 font-bold">-{{ number_format($discountAmount) }} ₫</span>
                    </div>
                    @endif
                    <div class="flex justify-between items-center pt-3 border-t border-slate-100 dark:border-slate-800">
                        <span class="text-slate-900 dark:text-white font-black uppercase tracking-wider">Tổng thanh toán:</span>
                        <span class="text-xl font-black text-primary">{{ number_format($order->total_amount) }} ₫</span>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-100 dark:border-slate-800 p-6 space-y-4">
                <h2 class="text-lg font-bold text-slate-900 dark:text-white">Lịch sử cập nhật trạng thái</h2>

                <div class="relative pl-6 space-y-6 before:absolute before:left-2 before:top-2 before:bottom-2 before:w-0.5 before:bg-slate-100 dark:before:bg-slate-800">
                    @forelse ($order->statusHistories as $history)
                    @php
                        $historyStatus = $history->status;
                        $historyLabel = $statusLabels[$historyStatus] ?? $returnStatusLabels[$historyStatus] ?? $historyStatus;

                        if (str_starts_with($historyStatus, '(Hoàn hàng) ')) {
                            $historyReturnStatus = trim(str_replace('(Hoàn hàng)', '', $historyStatus));
                            $historyLabel = 'Hoàn hàng - ' . ($returnStatusLabels[$historyReturnStatus] ?? $historyReturnStatus);
                        }
                    @endphp
                    <div class="relative">
                        <div class="absolute -left-[22px] top-1.5 size-3 rounded-full border-2 border-white dark:border-slate-900 bg-primary shadow-sm"></div>
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1">
                            <span class="text-sm font-bold text-slate-900 dark:text-white">
                                {{ $historyLabel }}
                            </span>
                            <span class="text-xs text-slate-500">
                                {{ $history->created_at->format('d/m/Y H:i:s') }}
                            </span>
                        </div>
                        <p class="text-xs text-slate-600 dark:text-slate-400 mt-1">
                            Người cập nhật: <span class="font-semibold">{{ $history->user->name ?? 'Hệ thống' }}</span>
                        </p>
                        @if ($history->note)
                        <p class="text-xs text-slate-500 italic mt-1 bg-slate-50 dark:bg-slate-800/50 p-2 rounded">
                            {{ $history->note }}
                        </p>
                        @endif
                    </div>
                    @empty
                    <p class="text-sm text-slate-500 italic">Chưa có lịch sử cập nhật.</p>
                    @endforelse
                </div>
            </div>

        </div>

        <div class="space-y-6 lg:sticky lg:top-20 h-fit">
            @php
                $isVnpayUnpaid = in_array($order->payment_method, ['vnpay', 'vnp']) && $order->payment_status === 'pending' && $order->status !== \App\Models\Order::STATUS_CANCELLED;
            @endphp

            @if (!in_array($order->status, [\App\Models\Order::STATUS_DELIVERED, \App\Models\Order::STATUS_RECEIVED, \App\Models\Order::STATUS_CANCELLED, \App\Models\Order::STATUS_FAILED_DELIVERY]))
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-100 dark:border-slate-800 p-6 space-y-5">
                <h2 class="text-lg font-bold text-slate-900 dark:text-white">Thao tác xử lý</h2>

                @if ($isVnpayUnpaid)
                    {{-- CHẶN: Khách chưa thanh toán VNPAY --}}
                    <div class="rounded-xl border border-amber-200 bg-amber-50 dark:bg-amber-500/10 dark:border-amber-500/30 p-4 flex gap-3 items-start">
                        <span class="material-symbols-outlined text-amber-500 shrink-0 mt-0.5">warning</span>
                        <div>
                            <p class="text-sm font-bold text-amber-800 dark:text-amber-300">Chưa thể xử lý đơn hàng</p>
                            <p class="text-xs text-amber-700 dark:text-amber-400 mt-1">Đơn hàng thanh toán qua <strong>VNPAY</strong> nhưng khách hàng <strong>chưa hoàn tất thanh toán</strong>. Vui lòng chờ khách thanh toán hoặc hủy đơn trước khi xử lý tiếp.</p>
                        </div>
                    </div>
                    {{-- Vẫn cho phép HỦY đơn nếu đang pending --}}
                    @if ($order->status === \App\Models\Order::STATUS_PENDING)
                    <form action="{{ route('admin.orders.cancel', $order) }}" method="POST" class="space-y-3 border-t border-slate-100 dark:border-slate-800 pt-4">
                        @csrf
                        @method('PATCH')
                        <label class="text-sm font-semibold text-slate-700 dark:text-slate-200">Hủy đơn hàng</label>
                        <textarea name="cancellation_reason" rows="3" class="w-full rounded-lg border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white" placeholder="Nhập lý do hủy đơn..."></textarea>
                        <button type="submit" class="w-full px-4 py-2 bg-red-500 text-white rounded-lg font-semibold text-sm hover:bg-red-600">Hủy đơn</button>
                    </form>
                    @endif
                @else
                    <form action="{{ route('admin.orders.status.update', $order) }}" method="POST" class="space-y-3">
                        @csrf
                        @method('PATCH')
                        <label class="text-sm font-semibold text-slate-700 dark:text-slate-200">Cập nhật trạng thái</label>
                        <select name="status" class="w-full rounded-lg border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                            @foreach ($availableStatuses as $status)
                            <option value="{{ $status }}" @selected($order->status === $status)>{{ $statusLabels[$status] }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="w-full px-4 py-2 bg-primary text-black rounded-lg font-semibold text-sm hover:brightness-105">Lưu trạng thái</button>
                    </form>

                    @if ($order->status === \App\Models\Order::STATUS_PENDING)
                    <form action="{{ route('admin.orders.cancel', $order) }}" method="POST" class="space-y-3 border-t border-slate-100 dark:border-slate-800 pt-4">
                        @csrf
                        @method('PATCH')
                        <label class="text-sm font-semibold text-slate-700 dark:text-slate-200">Hủy đơn hàng</label>
                        <textarea name="cancellation_reason" rows="3" class="w-full rounded-lg border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white" placeholder="Nhập lý do hủy đơn..."></textarea>
                        <button type="submit" class="w-full px-4 py-2 bg-red-500 text-white rounded-lg font-semibold text-sm hover:bg-red-600">Hủy đơn</button>
                    </form>
                    @endif
                @endif
            </div>
            @endif
            
            @if ($order->status === \App\Models\Order::STATUS_FAILED_DELIVERY && $order->payment_status === 'paid')
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-100 dark:border-slate-800 p-6 space-y-5">
                <h2 class="text-lg font-bold text-slate-900 dark:text-white">Thao tác xử lý Bom hàng</h2>
                <div class="rounded-xl border border-pink-200 bg-pink-50 dark:bg-pink-500/10 p-4">
                    <p class="text-sm font-bold text-pink-800 dark:text-pink-300 mb-2">Giao thất bại (Đã thanh toán trước)</p>
                    <p class="text-xs text-pink-700 dark:text-pink-400 mt-1 mb-4">Nhấn nút bên dưới để xác nhận tự động nhập lại kho các sản phẩm, và hoàn <strong>{{ number_format($order->total_amount) }}₫</strong> vào Ví Bee Pay của khách.</p>
                    <form action="{{ route('admin.orders.refund.failed', $order) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="w-full px-4 py-2 bg-pink-600 text-white rounded-lg font-semibold text-sm hover:bg-pink-700" onclick="return confirm('Bạn chắc chắn muốn hoàn tiền {{ number_format($order->total_amount) }}₫ vào ví khách hàng và cộng lại tồn kho cho đơn hàng này?')">Hoàn tiền & Nhập kho</button>
                    </form>
                </div>
            </div>
            @endif


            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-100 dark:border-slate-800 p-6 space-y-4">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-2">Thông tin thanh toán</h3>

                <div class="grid grid-cols-1 gap-4">
                    <div class="p-4 rounded-lg bg-slate-50 dark:bg-slate-800/50">
                        <p class="text-xs text-slate-500 font-semibold">Phương thức thanh toán</p>
                        <p class="text-base font-bold text-slate-900 dark:text-white mt-2">
                            {{ $paymentMethodLabels[$order->payment_method] ?? $order->payment_method }}
                        </p>
                    </div>
                    <div class="p-4 rounded-lg bg-slate-50 dark:bg-slate-800/50">
                        <p class="text-xs text-slate-500 font-semibold">Trạng thái thanh toán</p>
                        <div class="mt-2">
                            @php
                                $statusColor = match($order->payment_status) {
                                    'paid' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                    'pending' => 'bg-amber-50 text-amber-700 border-amber-200',
                                    'failed' => 'bg-red-50 text-red-700 border-red-200',
                                    'refunded' => 'bg-purple-50 text-purple-700 border-purple-200',
                                    'cancelled' => 'bg-slate-100 text-slate-700 border-slate-200',
                                    default => 'bg-slate-100 text-slate-700 border-slate-200'
                                };
                            @endphp
                            <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold border {{ $statusColor }}">
                                {{ $paymentStatusLabels[$order->payment_status] ?? $order->payment_status }}
                            </span>
                        </div>
                    </div>
                    <div class="p-4 rounded-lg bg-slate-50 dark:bg-slate-800/50">
                        <p class="text-xs text-slate-500 font-semibold">Thời gian thanh toán</p>
                        <p class="text-base font-bold text-slate-900 dark:text-white mt-2">
                            @if ($order->payment_status === 'paid')
                                {{ $order->paid_at?->format('d/m/Y H:i') }}
                            @elseif ($order->payment_status === 'pending')
                                Chờ thanh toán
                            @else
                                Chưa thanh toán
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
