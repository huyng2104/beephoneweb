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

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-100 dark:border-slate-800 p-4">
            <p class="text-xs text-slate-500">Tổng tiền</p>
            <p class="text-2xl font-bold text-slate-900 dark:text-white mt-1">{{ number_format($order->total_amount) }} ₫</p>
        </div>
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-100 dark:border-slate-800 p-4">
            <p class="text-xs text-slate-500">Trạng thái đơn</p>
            <p class="text-base font-semibold text-slate-900 dark:text-white mt-1">{{ $statusLabels[$order->status] ?? $order->status }}</p>
        </div>
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-100 dark:border-slate-800 p-4">
            <p class="text-xs text-slate-500">Hoàn hàng</p>
            <p class="text-base font-semibold text-slate-900 dark:text-white mt-1">{{ $returnStatusLabels[$order->return_status] ?? $order->return_status }}</p>
        </div>
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-100 dark:border-slate-800 p-4">
            <p class="text-xs text-slate-500">Hoàn tiền</p>
            <p class="text-base font-semibold text-slate-900 dark:text-white mt-1">{{ $order->refund_amount ? number_format($order->refund_amount) . ' ₫' : 'Chưa hoàn tiền' }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-100 dark:border-slate-800 p-6 space-y-4">
                <h2 class="text-lg font-bold text-slate-900 dark:text-white">Thông tin người đặt / người nhận</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div class="p-4 rounded-lg bg-slate-50 dark:bg-slate-800/50">
                        <p class="text-xs uppercase text-slate-500 font-semibold">Người đặt hàng</p>
                        <p class="font-semibold text-slate-900 dark:text-white mt-1">{{ $order->customer_name }}</p>
                        <p class="text-slate-600 dark:text-slate-300 text-sm mt-1">{{ $order->customer_phone }}</p>
                        <p class="text-slate-500 text-xs mt-1">{{ $order->customer_email ?: 'Chưa có email' }}</p>
                    </div>
                    <div class="p-4 rounded-lg bg-slate-50 dark:bg-slate-800/50">
                        <p class="text-xs uppercase text-slate-500 font-semibold">Người nhận hàng</p>
                        <p class="font-semibold text-slate-900 dark:text-white mt-1">{{ $order->recipient_name ?: $order->customer_name }}</p>
                        <p class="text-slate-600 dark:text-slate-300 text-sm mt-1">{{ $order->recipient_phone ?: $order->customer_phone }}</p>
                        <p class="text-slate-500 text-xs mt-1">{{ $order->recipient_address ?: $order->shipping_address ?: 'Chưa có địa chỉ' }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-slate-500">Ngày đặt</p>
                        <p class="font-semibold text-slate-900 dark:text-white">{{ optional($order->ordered_at)->format('d/m/Y H:i') ?? $order->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div>
                        <p class="text-slate-500">Mã đơn</p>
                        <p class="font-semibold text-primary">{{ $order->order_code }}</p>
                    </div>
                </div>

                <div>
                    <p class="text-slate-500 text-sm">Ghi chú đơn hàng</p>
                    <p class="font-semibold text-slate-900 dark:text-white">{{ $order->note ?: 'Không có ghi chú' }}</p>
                </div>
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
                                <th class="px-4 py-3 text-left font-semibold">SKU</th>
                                <th class="px-4 py-3 text-right font-semibold">Đơn giá</th>
                                <th class="px-4 py-3 text-right font-semibold">SL</th>
                                <th class="px-4 py-3 text-right font-semibold">Thành tiền</th>
                            </tr>
                        </thead>
                        @foreach ($order->items as $item)
                        @php
                            $product = $item->product;
                            $productName = $product ? $product->name : $item->product_name;
                            $productThumbnail = $product ? $product->thumbnail : $item->thumbnail;
                            $productSku = $product ? $product->sku : $item->product_sku;
                        @endphp
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            <tr>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3 min-w-[220px]">
                                        <div class="size-10 rounded-lg bg-slate-100 dark:bg-slate-800 overflow-hidden flex items-center justify-center border border-slate-200 dark:border-slate-700">
                                            @if ($item->thumbnail)
                                                <img src="{{ $item->thumbnail }}" alt="{{ $item->product_name }}" class="w-full h-full object-cover" onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden');">
                                                <span class="material-symbols-outlined text-slate-400 text-[18px] hidden">inventory_2</span>
                                            @elseif ($productThumbnail)
                                                @if(str_starts_with($productThumbnail, 'http'))
                                                    <img src="{{ $productThumbnail }}" alt="{{ $productName }}" class="w-full h-full object-cover" onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden');">
                                                @else
                                                    <img src="{{ Storage::url($productThumbnail) }}" alt="{{ $productName }}" class="w-full h-full object-cover" onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden');">
                                                @endif
                                                <span class="material-symbols-outlined text-slate-400 text-[18px] hidden">inventory_2</span>
                                            @else
                                                <span class="material-symbols-outlined text-slate-400 text-[18px]">inventory_2</span>
                                            @endif
                                        </div>
                                        <div class="flex-1">
                                            <span class="font-semibold text-slate-900 dark:text-white block">{{ $productName }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-slate-600 dark:text-slate-300">{{ $productSku ?: '-' }}</td>
                                <td class="px-4 py-3 text-right text-slate-700 dark:text-slate-200">{{ number_format($item->unit_price) }} ₫</td>
                                <td class="px-4 py-3 text-right text-slate-700 dark:text-slate-200">{{ $item->quantity }}</td>
                                <td class="px-4 py-3 text-right font-semibold text-slate-900 dark:text-white">{{ number_format($item->line_total) }} ₫</td>
                            </tr>
                            
                            {{-- Giao diện thao tác Hoàn Trả cho riêng từng Item --}}
                            @if ($item->return_status !== \App\Models\OrderItem::RETURN_NONE)
                            <tr class="bg-amber-50/30 dark:bg-amber-900/10">
                                <td colspan="5" class="px-4 py-4">
                                    <div class="flex flex-col xl:flex-row gap-6 items-start w-full">
                                        <div class="flex-1 space-y-3">
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
                                        
                                        <div class="w-full xl:w-72 xl:shrink-0 bg-white dark:bg-slate-800/80 rounded-xl border border-slate-200 dark:border-slate-700 p-4 shadow-sm">
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

            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-100 dark:border-slate-800 p-6 space-y-4">
                <div class="border-t border-slate-200 dark:border-slate-700 pt-6 mt-6">
                    <h3 class="text-base font-bold text-slate-900 dark:text-white mb-4">Thông tin thanh toán</h3>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
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

        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-100 dark:border-slate-800 p-6 space-y-5 h-fit lg:sticky lg:top-20">
            <h2 class="text-lg font-bold text-slate-900 dark:text-white">Thao tác xử lý</h2>

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

            <form action="{{ route('admin.orders.cancel', $order) }}" method="POST" class="space-y-3 border-t border-slate-100 dark:border-slate-800 pt-4">
                @csrf
                @method('PATCH')
                <label class="text-sm font-semibold text-slate-700 dark:text-slate-200">Hủy đơn hàng</label>
                <textarea name="cancellation_reason" rows="3" class="w-full rounded-lg border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white" placeholder="Nhập lý do hủy đơn..."></textarea>
                <button type="submit" class="w-full px-4 py-2 bg-red-500 text-white rounded-lg font-semibold text-sm hover:bg-red-600">Hủy đơn</button>
            </form>

            {{-- Đã loại bỏ khối xử lý yêu cầu hoàn hàng cho tổng đơn --}}
        </div>
    </div>
</div>
@endsection
