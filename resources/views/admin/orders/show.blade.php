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

            @if ($order->cancellation_reason || $order->return_note || $order->return_image || $order->return_admin_note)
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-100 dark:border-slate-800 p-6 space-y-4">
                <h2 class="text-lg font-bold text-slate-900 dark:text-white">Yêu cầu hoàn hàng</h2>

                @if ($order->cancellation_reason)
                <div class="p-3 bg-red-50 border border-red-200 rounded-lg">
                    <p class="text-sm text-red-700 font-semibold">Lý do hủy: {{ $order->cancellation_reason }}</p>
                </div>
                @endif

                @if ($order->return_note)
                <div class="p-3 bg-amber-50 border border-amber-200 rounded-lg">
                    <p class="text-sm text-amber-700 font-semibold">Lý do khách yêu cầu hoàn: {{ $order->return_note }}</p>
                </div>
                @endif

                @if ($order->return_image && $returnImageUrl)
                <div class="p-3 bg-amber-50 border border-amber-200 rounded-lg">
                    <p class="text-sm text-amber-700 font-semibold mb-3">Ảnh khách gửi kèm</p>
                    <a href="{{ $returnImageUrl }}" target="_blank" rel="noopener" class="inline-block">
                        <img src="{{ $returnImageUrl }}" alt="Ảnh hoàn hàng {{ $order->order_code }}" class="h-28 w-28 rounded-lg border border-amber-200 object-cover">
                    </a>
                </div>
                @endif

                @if ($order->return_admin_note)
                <div class="p-3 bg-blue-50 border border-blue-200 rounded-lg">
                    <p class="text-sm text-blue-700 font-semibold">Phản hồi admin: {{ $order->return_admin_note }}</p>
                </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                    <div>
                        <p class="text-slate-500">Khách gửi yêu cầu</p>
                        <p class="font-semibold text-slate-900 dark:text-white">{{ optional($order->return_requested_at)->format('d/m/Y H:i') ?: 'Chưa có' }}</p>
                    </div>
                    <div>
                        <p class="text-slate-500">Admin duyệt</p>
                        <p class="font-semibold text-slate-900 dark:text-white">{{ optional($order->return_approved_at)->format('d/m/Y H:i') ?: 'Chưa có' }}</p>
                    </div>
                    <div>
                        <p class="text-slate-500">Admin từ chối</p>
                        <p class="font-semibold text-slate-900 dark:text-white">{{ optional($order->return_rejected_at)->format('d/m/Y H:i') ?: 'Chưa có' }}</p>
                    </div>
                    <div>
                        <p class="text-slate-500">Khách gửi hàng hoàn</p>
                        <p class="font-semibold text-slate-900 dark:text-white">{{ optional($order->return_shipped_at)->format('d/m/Y H:i') ?: 'Chưa có' }}</p>
                    </div>
                    <div>
                        <p class="text-slate-500">Admin nhận hàng hoàn</p>
                        <p class="font-semibold text-slate-900 dark:text-white">{{ optional($order->return_received_at)->format('d/m/Y H:i') ?: 'Chưa có' }}</p>
                    </div>
                    <div>
                        <p class="text-slate-500">Hoàn tiền vào ví</p>
                        <p class="font-semibold text-slate-900 dark:text-white">{{ optional($order->return_refunded_at)->format('d/m/Y H:i') ?: 'Chưa có' }}</p>
                    </div>
                </div>
            </div>
            @endif

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
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @foreach ($order->items as $item)
                            @php
                                $product = $item->product;
                                $productName = $product ? $product->name : $item->product_name;
                                $productThumbnail = $product ? $product->thumbnail : $item->thumbnail;
                                $productSku = $product ? $product->sku : $item->product_sku;
                            @endphp

                            <tr>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3 min-w-[220px]">
                                        <div class="size-10 rounded-lg bg-slate-100 dark:bg-slate-800 overflow-hidden flex items-center justify-center">
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
                            @endforeach
                        </tbody>
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

            <div class="space-y-3 border-t border-slate-100 dark:border-slate-800 pt-4">
                <label class="text-sm font-semibold text-slate-700 dark:text-slate-200">Xử lý hoàn hàng</label>
                @if ($order->canApproveReturn())
                <form action="{{ route('admin.orders.return.approve', $order) }}" method="POST" class="space-y-3">
                    @csrf
                    @method('PATCH')
                    <textarea name="return_admin_note" rows="3" class="w-full rounded-lg border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white" placeholder="Ghi chú duyệt yêu cầu (nếu có)...">{{ $order->return_admin_note }}</textarea>
                    <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg font-semibold text-sm hover:bg-blue-700">Duyệt yêu cầu hoàn hàng</button>
                </form>

                <form action="{{ route('admin.orders.return.reject', $order) }}" method="POST" class="space-y-3">
                    @csrf
                    @method('PATCH')
                    <textarea name="return_admin_note" rows="3" class="w-full rounded-lg border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white" placeholder="Nhập lý do từ chối..." required></textarea>
                    <button type="submit" class="w-full px-4 py-2 bg-red-500 text-white rounded-lg font-semibold text-sm hover:bg-red-600">Từ chối yêu cầu</button>
                </form>
                @elseif ($order->return_status === \App\Models\Order::RETURN_APPROVED)
                <div class="rounded-lg border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-700">
                    Đã duyệt yêu cầu. Chờ khách gửi hàng lại.
                </div>
                @elseif ($order->return_status === \App\Models\Order::RETURN_CUSTOMER_SHIPPED)
                <form action="{{ route('admin.orders.return.received', $order) }}" method="POST" class="space-y-3">
                    @csrf
                    @method('PATCH')
                    <textarea name="return_admin_note" rows="3" class="w-full rounded-lg border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white" placeholder="Ghi chú kiểm tra hàng hoàn (nếu có)...">{{ $order->return_admin_note }}</textarea>
                    <button type="submit" class="w-full px-4 py-2 bg-cyan-600 text-white rounded-lg font-semibold text-sm hover:bg-cyan-700">Xác nhận đã nhận hàng hoàn</button>
                </form>
                @elseif ($order->canRefundReturn())
                <form action="{{ route('admin.orders.return.refund', $order) }}" method="POST" class="space-y-3">
                    @csrf
                    @method('PATCH')
                    <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                        Sau khi bấm nút bên dưới, hệ thống sẽ hoàn {{ number_format($order->total_amount) }} ₫ vào ví Bee Pay của khách hàng.
                    </div>
                    <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white rounded-lg font-semibold text-sm hover:bg-green-700">Hoàn tiền vào ví</button>
                </form>
                @elseif ($order->return_status === \App\Models\Order::RETURN_REJECTED)
                <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    Yêu cầu hoàn hàng đã bị từ chối.
                </div>
                @elseif ($order->return_status === \App\Models\Order::RETURN_REFUNDED)
                <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                    Đơn hàng đã hoàn tiền xong vào ví Bee Pay.
                </div>
                @else
                <div class="rounded-lg border border-dashed border-slate-200 px-4 py-3 text-sm text-slate-500 dark:border-slate-700 dark:text-slate-400">
                    Chức năng này chỉ mở khi khách hàng đã gửi yêu cầu hoàn hàng.
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
