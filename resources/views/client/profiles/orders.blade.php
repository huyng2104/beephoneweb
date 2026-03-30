@extends('client.profiles.layouts.app')

@section('title', 'Bee Phone - Lịch sử đơn hàng')

@section('profile_content')
    <section class="flex-1" data-purpose="user-main-section">
        @php
            $statusLabels = \App\Models\Order::statusLabels();
            $returnStatusLabels = \App\Models\Order::returnStatusLabels();
            $statusClasses = [
                \App\Models\Order::STATUS_PENDING => 'text-yellow-700 bg-yellow-100',
                \App\Models\Order::STATUS_PACKING => 'text-blue-700 bg-blue-100',
                \App\Models\Order::STATUS_SHIPPING => 'text-indigo-700 bg-indigo-100',
                \App\Models\Order::STATUS_DELIVERED => 'text-emerald-700 bg-emerald-100',
                \App\Models\Order::STATUS_RECEIVED => 'text-green-700 bg-green-100',
                \App\Models\Order::STATUS_CANCELLED => 'text-red-700 bg-red-100',
            ];
            $returnClasses = [
                \App\Models\Order::RETURN_NONE => 'text-slate-600 bg-slate-100',
                \App\Models\Order::RETURN_REQUESTED => 'text-amber-700 bg-amber-100',
                \App\Models\Order::RETURN_CONFIRMED => 'text-green-700 bg-green-100',
            ];
        @endphp

        @if(!empty($reviewOrder) && $reviewOrder->items && $reviewOrder->items->count())
            <div id="review-modal" class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/60 p-4">
                <div class="w-full max-w-4xl max-h-[86vh] overflow-y-auto rounded-3xl bg-white dark:bg-[#1a1a1a] border border-gray-100 dark:border-white/10 shadow-2xl">
                    <div class="sticky top-0 z-10 flex items-center justify-between gap-4 px-6 py-4 bg-white/90 dark:bg-[#1a1a1a]/90 backdrop-blur border-b border-gray-100 dark:border-white/10">
                        <div>
                            <h2 class="text-lg sm:text-xl font-black text-[#181611] dark:text-white">Đánh giá sản phẩm</h2>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                Sau khi đánh giá xong, đánh giá sẽ hiển thị ở phần comment của sản phẩm và được gắn tag <span class="font-black text-[#f4c025]">Đã mua</span>.
                            </p>
                        </div>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('client.orders.index', ['skip_review' => 1]) }}"
                                class="px-4 h-10 inline-flex items-center justify-center rounded-xl bg-gray-100 dark:bg-white/5 border border-gray-200 dark:border-white/10 hover:border-[#f4c025] hover:text-[#f4c025] text-sm font-black transition-colors">
                                Bỏ qua
                            </a>
                            <button type="button" id="review-modal-close"
                                class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-gray-100 dark:bg-white/5 border border-gray-200 dark:border-white/10 hover:border-[#f4c025] transition-colors">
                                <span class="material-symbols-outlined text-[22px] text-gray-700 dark:text-gray-200">close</span>
                            </button>
                        </div>
                    </div>

                    <div class="px-6 py-6">
                        <div class="text-xs font-bold uppercase tracking-widest text-gray-400">
                            Mã đơn: {{ $reviewOrder->order_code }}
                        </div>

                        <div class="mt-5 space-y-5">
                            @foreach($reviewOrder->items as $item)
                                @php $p = $item->product; @endphp
                                @if($p)
                                    @php $productParam = $p->slug ?: $p->id; @endphp
                                    <div class="rounded-2xl border border-gray-100 dark:border-white/10 bg-gray-50/60 dark:bg-white/5 p-5">
                                        <div class="flex gap-4 items-start">
                                            <div class="w-20 h-20 rounded-2xl overflow-hidden bg-white dark:bg-black/20 border border-gray-100 dark:border-white/10 shrink-0">
                                                <img src="{{ asset('storage/' . ($p->thumbnail ?? '')) }}" alt="{{ $p->name }}" class="w-full h-full object-cover">
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <div class="flex items-start justify-between gap-3 flex-wrap">
                                                    <div class="min-w-0">
                                                        <p class="font-black text-[#181611] dark:text-white line-clamp-2">{{ $item->product_name ?? $p->name }}</p>
                                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Số lượng: {{ $item->quantity }}</p>
                                                    </div>
                                                    <a href="{{ route('client.product.detail', ['slug' => $productParam]) }}#comments" class="text-sm font-black text-[#f4c025] hover:underline">
                                                        Xem sản phẩm
                                                    </a>
                                                </div>

                                                <form action="{{ route('products.comments.store', $p) }}" method="POST" enctype="multipart/form-data" class="mt-4 grid gap-3">
                                                    @csrf
                                                    <input type="hidden" name="redirect_to" value="/don-mua">

                                                    <div class="grid gap-3 sm:grid-cols-2">
                                                        <div>
                                                            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400">Số sao</label>
                                                            <select name="rating" class="mt-1 w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-black/20 px-4 py-3 text-sm text-[#181611] dark:text-white">
                                                                <option value="">Chọn</option>
                                                                @for($i=5;$i>=1;$i--)
                                                                    <option value="{{ $i }}">{{ $i }} sao</option>
                                                                @endfor
                                                            </select>
                                                        </div>
                                                        <div>
                                                            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400">Ảnh (tùy chọn)</label>
                                                            <input type="file" name="image" accept=".jpg,.jpeg,.png,.webp" class="mt-1 w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-black/20 px-4 py-3 text-sm text-gray-600 dark:text-gray-300 file:mr-4 file:rounded-lg file:border-0 file:bg-[#f4c025]/30 file:px-4 file:py-2 file:text-xs file:font-black file:text-[#181611] hover:file:bg-[#f4c025]/40">
                                                        </div>
                                                    </div>

                                                    <div>
                                                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400">Nội dung</label>
                                                        <textarea name="content" rows="3" required class="mt-1 w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-black/20 px-4 py-3 text-sm text-[#181611] dark:text-white" placeholder="Chia sẻ cảm nhận của bạn..."></textarea>
                                                    </div>

                                                    <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-[#f4c025] px-6 py-3 text-sm font-black text-black hover:brightness-105 active:scale-95 transition-all shadow-[0_14px_30px_-14px_rgba(244,192,37,0.45)]">
                                                        GỬI ĐÁNH GIÁ
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const modal = document.getElementById('review-modal');
                    const closeBtn = document.getElementById('review-modal-close');
                    if (!modal) return;

                    function closeModal() {
                        modal.remove();
                    }

                    if (closeBtn) closeBtn.addEventListener('click', closeModal);
                    modal.addEventListener('click', function (e) {
                        if (e.target === modal) closeModal();
                    });
                });
            </script>
        @endif

        <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-gray-100 dark:border-white/10 pb-4">
            <div>
                <h1 class="text-2xl font-bold uppercase tracking-tight text-[#181611] dark:text-white">Lịch sử đơn hàng</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Quản lý, theo dõi đơn hàng và gửi yêu cầu hoàn hàng khi cần.</p>
            </div>
        </div>

        @if (session('success'))
            <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                <ul class="space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (isset($orders) && $orders->count() > 0)
            <div class="space-y-6">
                @foreach ($orders as $order)
                    @php
                        $statusClass = $statusClasses[$order->status] ?? 'text-slate-700 bg-slate-100';
                        $returnClass = $returnClasses[$order->return_status] ?? 'text-slate-700 bg-slate-100';
                        $totalAmount = $order->total_amount ?? $order->total_price ?? 0;
                    @endphp

                    <div class="group overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm transition-colors hover:border-[#f4c025] dark:border-white/10 dark:bg-white/5">
                        <div class="flex flex-wrap items-center justify-between gap-4 border-b border-gray-100 bg-gray-50/50 px-6 py-4 dark:border-white/10 dark:bg-white/5">
                            <div class="flex items-center gap-4">
                                <span class="flex items-center gap-2 font-bold text-[#181611] dark:text-white">
                                    <span class="material-symbols-outlined text-[20px] text-[#f4c025]">receipt_long</span>
                                    {{ $order->order_code }}
                                </span>
                                <span class="border-l border-gray-300 pl-4 text-sm text-gray-500">
                                    {{ optional($order->ordered_at ?? $order->created_at)->format('d/m/Y H:i') }}
                                </span>
                            </div>

                            <div class="flex flex-wrap items-center gap-2">
                                <span class="rounded-md px-3 py-1 text-xs font-bold uppercase tracking-wider {{ $statusClass }}">
                                    {{ $statusLabels[$order->status] ?? $order->status }}
                                </span>
                                <span class="rounded-md px-3 py-1 text-xs font-semibold {{ $returnClass }}">
                                    {{ $returnStatusLabels[$order->return_status] ?? $order->return_status }}
                                </span>
                            </div>
                        </div>

                        <div class="space-y-4 px-6 py-4">
                            @foreach ($order->items as $item)
                                @php
                                    $thumbnail = $item->thumbnail ?? null;
                                    $imageUrl = $thumbnail
                                        ? (\Illuminate\Support\Str::startsWith($thumbnail, ['http://', 'https://']) ? $thumbnail : asset('storage/' . $thumbnail))
                                        : 'https://placehold.co/160x160?text=Bee+Phone';
                                @endphp
                                <div class="flex items-center gap-4">
                                    <div class="h-20 w-20 flex-shrink-0 rounded-xl border border-gray-100 bg-gray-50 p-2 dark:border-white/5 dark:bg-black/20">
                                        <img src="{{ $imageUrl }}" alt="{{ $item->product_name }}" class="h-full w-full object-contain">
                                    </div>
                                    <div class="flex-grow">
                                        <h3 class="line-clamp-1 font-bold text-[#181611] transition-colors group-hover:text-[#f4c025] dark:text-white">
                                            {{ $item->product_name }}
                                        </h3>
                                        <p class="mt-1 text-sm text-gray-500">Số lượng: {{ $item->quantity }}</p>
                                    </div>
                                    <div class="text-right">
                                        <span class="font-bold text-[#181611] dark:text-white">{{ number_format($item->unit_price, 0, ',', '.') }}₫</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @if ($order->return_note)
                            <div class="mx-6 mb-4 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                                <span class="font-semibold">Lý do/ghi chú hoàn hàng:</span> {{ $order->return_note }}
                            </div>
                        @endif

                        <div class="flex flex-wrap items-start justify-between gap-4 border-t border-gray-100 px-6 py-4 dark:border-white/10">
                            <div>
                                <span class="text-sm text-gray-500">Thành tiền:</span>
                                <span class="ml-2 text-xl font-black text-red-500">{{ number_format($totalAmount, 0, ',', '.') }}₫</span>
                            </div>

                            <div class="flex max-w-full flex-wrap justify-end gap-3">
                                @if ($order->status === \App\Models\Order::STATUS_PENDING)
                                    <form action="{{ route('client.orders.cancel', $order->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="rounded-lg border border-red-200 bg-red-50 px-6 py-2 text-sm font-semibold text-red-600 transition hover:bg-red-500 hover:text-white" onclick="return confirm('Bạn có chắc chắn muốn hủy đơn hàng này?')">
                                            Hủy đơn
                                        </button>
                                    </form>
                                @endif

                                @if ($order->status === \App\Models\Order::STATUS_DELIVERED)
                                    <form action="{{ route('client.orders.confirm', $order->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="rounded-lg bg-green-500 px-6 py-2 text-sm font-semibold text-white shadow-md transition hover:bg-green-600" onclick="return confirm('Bạn xác nhận đã nhận được hàng và sản phẩm không có vấn đề gì?')">
                                            Đã nhận được hàng
                                        </button>
                                    </form>
                                @endif

                                @if ($order->canRequestReturn())
                                    <form action="{{ route('client.orders.return', $order->id) }}" method="POST" class="w-full rounded-xl border border-amber-200 bg-amber-50 p-4 sm:min-w-[320px]">
                                        @csrf
                                        @method('PATCH')
                                        <label for="return_note_{{ $order->id }}" class="mb-2 block text-sm font-semibold text-amber-900">
                                            Yêu cầu hoàn hàng
                                        </label>
                                        <textarea id="return_note_{{ $order->id }}" name="return_note" rows="3" class="w-full rounded-lg border border-amber-200 bg-white px-3 py-2 text-sm text-slate-800 focus:border-amber-400 focus:outline-none focus:ring-0" placeholder="Mô tả lý do hoàn hàng, tình trạng sản phẩm, phụ kiện đi kèm..." required>{{ old('return_note') }}</textarea>
                                        <div class="mt-3 flex justify-end">
                                            <button type="submit" class="rounded-lg bg-amber-500 px-5 py-2 text-sm font-semibold text-black transition hover:bg-amber-400">
                                                Gửi yêu cầu
                                            </button>
                                        </div>
                                    </form>
                                @elseif ($order->return_status === \App\Models\Order::RETURN_REQUESTED)
                                    <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                                        Yêu cầu hoàn hàng đã được gửi lúc {{ optional($order->return_requested_at)->format('d/m/Y H:i') ?? 'gần đây' }}.
                                    </div>
                                @elseif ($order->return_status === \App\Models\Order::RETURN_CONFIRMED)
                                    <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                                        Cửa hàng đã xác nhận hoàn hàng lúc {{ optional($order->return_confirmed_at)->format('d/m/Y H:i') ?? 'gần đây' }}.
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-8 flex justify-center">
                {{ $orders->links() }}
            </div>
        @else
            <div class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-gray-200 bg-white py-20 dark:border-white/10 dark:bg-white/5">
                <div class="mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-gray-50 dark:bg-white/5">
                    <span class="material-symbols-outlined text-4xl text-gray-300 dark:text-gray-600">receipt_long</span>
                </div>
                <h2 class="mb-2 text-xl font-bold text-[#181611] dark:text-white">Bạn chưa có đơn hàng nào</h2>
                <p class="mb-6 text-sm text-gray-500 dark:text-gray-400">Hãy ghé danh mục sản phẩm và bắt đầu đơn đầu tiên.</p>
                <a href="{{ route('client.products.index') }}" class="rounded-lg bg-[#f4c025] px-8 py-2.5 font-bold text-black shadow-sm transition-transform hover:scale-105">
                    Tiếp tục mua sắm
                </a>
            </div>
        @endif
    </section>

    <style>
        .custom-scrollbar::-webkit-scrollbar { height: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 10px; }
        .custom-scrollbar:hover::-webkit-scrollbar-thumb { background: #f4c025; }
    </style>
@endsection
