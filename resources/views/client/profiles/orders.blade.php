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
                \App\Models\Order::STATUS_FAILED_DELIVERY => 'text-pink-700 bg-pink-100',
            ];
            $returnClasses = [
                \App\Models\Order::RETURN_NONE => 'hidden',
                \App\Models\Order::RETURN_REQUESTED => 'text-amber-700 bg-amber-100',
                \App\Models\Order::RETURN_APPROVED => 'text-blue-700 bg-blue-100',
                \App\Models\Order::RETURN_REJECTED => 'text-red-700 bg-red-100',
                \App\Models\Order::RETURN_CUSTOMER_SHIPPED => 'text-indigo-700 bg-indigo-100',
                \App\Models\Order::RETURN_RECEIVED => 'text-cyan-700 bg-cyan-100',
                \App\Models\Order::RETURN_REFUNDED => 'text-green-700 bg-green-100',
            ];
        @endphp

        {{-- MODAL ĐÁNH GIÁ SẢN PHẨM --}}
        @if (!empty($reviewOrder) && $reviewOrder->items && $reviewOrder->items->count())
            <div id="review-modal" class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/60 p-4">
                <div class="max-h-[86vh] w-full max-w-4xl overflow-y-auto rounded-3xl border border-gray-100 bg-white shadow-2xl dark:border-white/10 dark:bg-[#1a1a1a]">
                    <div class="sticky top-0 z-10 flex items-center justify-between gap-4 border-b border-gray-100 bg-white/90 px-6 py-4 backdrop-blur dark:border-white/10 dark:bg-[#1a1a1a]/90">
                        <div>
                            <h2 class="text-lg font-bold text-[#181611] dark:text-white sm:text-xl">Đánh giá sản phẩm</h2>
                            <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">
                                Sau khi đánh giá xong, đánh giá sẽ hiển thị ở phần bình luận của sản phẩm và được gắn tag
                                <span class="font-bold text-[#f4c025]">Đã mua</span>.
                            </p>
                        </div>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('client.orders.index', ['skip_review' => 1]) }}" class="inline-flex h-10 items-center justify-center rounded-xl border border-gray-200 bg-gray-100 px-4 text-sm font-bold transition-colors hover:border-[#f4c025] hover:text-[#f4c025] dark:border-white/10 dark:bg-white/5">
                                Bỏ qua
                            </a>
                            <button type="button" id="review-modal-close" class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-gray-200 bg-gray-100 transition-colors hover:border-[#f4c025] dark:border-white/10 dark:bg-white/5">
                                <span class="material-symbols-outlined text-[22px] text-gray-700 dark:text-gray-200">close</span>
                            </button>
                        </div>
                    </div>

                    <div class="px-6 py-6">
                        <div class="text-xs font-bold uppercase tracking-widest text-gray-400">
                            Mã đơn: {{ $reviewOrder->order_code }}
                        </div>

                        <div class="mt-5 space-y-5">
                            @foreach ($reviewOrder->items as $item)
                                @php $product = $item->product; @endphp
                                @if ($product)
                                    @php $productParam = $product->slug ?: $product->id; @endphp
                                    <div class="rounded-2xl border border-gray-100 bg-gray-50/60 p-5 dark:border-white/10 dark:bg-white/5">
                                        <div class="flex items-start gap-4">
                                            <div class="h-20 w-20 shrink-0 overflow-hidden rounded-2xl border border-gray-100 bg-white dark:border-white/10 dark:bg-black/20">
                                                <img src="{{ asset('storage/' . ($product->thumbnail ?? '')) }}" alt="{{ $product->name }}" class="h-full w-full object-cover">
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <div class="flex flex-wrap items-start justify-between gap-3">
                                                    <div class="min-w-0">
                                                        <p class="line-clamp-2 font-bold text-[#181611] dark:text-white">{{ $item->product_name ?? $product->name }}</p>
                                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Số lượng: {{ $item->quantity }}</p>
                                                    </div>
                                                    <a href="{{ route('client.product.detail', ['id' => $productParam]) }}#comments" class="text-sm font-bold text-[#f4c025] hover:underline">
                                                        Xem sản phẩm
                                                    </a>
                                                </div>

                                                <form action="{{ route('products.comments.store', $product) }}" method="POST" enctype="multipart/form-data" class="mt-4 grid gap-3">
                                                    @csrf
                                                    <input type="hidden" name="redirect_to" value="/don-mua">
                                                    <input type="hidden" name="order_id" value="{{ $reviewOrder->id }}">

                                                    <div class="grid gap-3 sm:grid-cols-2">
                                                        <div>
                                                            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400">Số sao</label>
                                                            <select name="rating" class="mt-1 w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm text-[#181611] dark:border-white/10 dark:bg-black/20 dark:text-white" required>
                                                                <option value="">Chọn</option>
                                                                @for ($i = 5; $i >= 1; $i--)
                                                                    <option value="{{ $i }}">{{ $i }} sao</option>
                                                                @endfor
                                                            </select>
                                                        </div>
                                                        <div>
                                                            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400">Ảnh (tùy chọn)</label>
                                                            <input type="file" name="image" accept=".jpg,.jpeg,.png,.webp" class="mt-1 w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-600 file:mr-4 file:rounded-lg file:border-0 file:bg-[#f4c025]/30 file:px-4 file:py-2 file:text-xs file:font-bold file:text-[#181611] hover:file:bg-[#f4c025]/40 dark:border-white/10 dark:bg-black/20 dark:text-gray-300">
                                                        </div>
                                                    </div>

                                                    <div>
                                                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400">Nội dung</label>
                                                        <textarea name="content" rows="3" required class="mt-1 w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm text-[#181611] dark:border-white/10 dark:bg-black/20 dark:text-white" placeholder="Chia sẻ cảm nhận của bạn..."></textarea>
                                                    </div>

                                                    <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-[#f4c025] px-6 py-3 text-sm font-bold text-black shadow-[0_14px_30px_-14px_rgba(244,192,37,0.45)] transition-all hover:brightness-105 active:scale-95">
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
                    function closeModal() { modal.remove(); }
                    if (closeBtn) closeBtn.addEventListener('click', closeModal);
                    modal.addEventListener('click', function (e) {
                        if (e.target === modal) closeModal();
                    });
                });
            </script>
        @endif

        {{-- TIÊU ĐỀ --}}
        <div class="mb-6 flex flex-col justify-between gap-4 border-b border-gray-100 pb-4 dark:border-white/10 lg:flex-row lg:items-end">
            <div>
                <h1 class="text-2xl font-bold uppercase tracking-tight text-[#181611] dark:text-white">Lịch sử đơn hàng</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Theo dõi thông tin và trạng thái xử lý các đơn hàng của bạn.</p>
            </div>

            @php $currentStatus = $statusParam ?? 'all'; @endphp
          
        </div>

        {{-- THÔNG BÁO ALERT --}}
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
                  <div class="flex gap-2 overflow-x-auto custom-scrollbar pb-2 lg:pb-0 mb-5">
                <a href="{{ route('client.orders.index', ['status' => 'all']) }}" class="{{ $currentStatus === 'all' ? 'bg-[#181611] text-[#f4c025] dark:bg-[#f4c025] dark:text-[#181611] shadow-md border-transparent' : 'bg-white dark:bg-white/5 border-gray-200 dark:border-white/10 text-gray-600 dark:text-gray-300 hover:border-[#181611] hover:text-[#181611] dark:hover:border-[#f4c025] dark:hover:text-[#f4c025]' }} border px-5 py-2.5 rounded-full text-sm font-bold whitespace-nowrap transition-all duration-300">Tất cả</a>
                <a href="{{ route('client.orders.index', ['status' => 'pending_payment']) }}" class="{{ $currentStatus === 'pending_payment' ? 'bg-red-50 text-red-600 border-red-200 shadow-sm' : 'bg-white dark:bg-white/5 border-gray-200 dark:border-white/10 text-gray-600 dark:text-gray-300 hover:border-red-400 hover:text-red-500' }} border px-5 py-2.5 rounded-full text-sm font-bold whitespace-nowrap transition-all duration-300">Chờ thanh toán</a>
                <a href="{{ route('client.orders.index', ['status' => 'processing']) }}" class="{{ $currentStatus === 'processing' ? 'bg-[#181611] text-[#f4c025] dark:bg-[#f4c025] dark:text-[#181611] shadow-md border-transparent' : 'bg-white dark:bg-white/5 border-gray-200 dark:border-white/10 text-gray-600 dark:text-gray-300 hover:border-[#181611] hover:text-[#181611] dark:hover:border-[#f4c025] dark:hover:text-[#f4c025]' }} border px-5 py-2.5 rounded-full text-sm font-bold whitespace-nowrap transition-all duration-300">Đang xử lý</a>
                <a href="{{ route('client.orders.index', ['status' => 'shipping']) }}" class="{{ $currentStatus === 'shipping' ? 'bg-indigo-50 text-indigo-600 border-indigo-200 shadow-sm' : 'bg-white dark:bg-white/5 border-gray-200 dark:border-white/10 text-gray-600 dark:text-gray-300 hover:border-indigo-400 hover:text-indigo-600' }} border px-5 py-2.5 rounded-full text-sm font-bold whitespace-nowrap transition-all duration-300">Đang vận chuyển</a>
                <a href="{{ route('client.orders.index', ['status' => 'completed']) }}" class="{{ $currentStatus === 'completed' ? 'bg-emerald-50 text-emerald-600 border-emerald-200 shadow-sm' : 'bg-white dark:bg-white/5 border-gray-200 dark:border-white/10 text-gray-600 dark:text-gray-300 hover:border-emerald-400 hover:text-emerald-500' }} border px-5 py-2.5 rounded-full text-sm font-bold whitespace-nowrap transition-all duration-300">Hoàn thành</a>
                <a href="{{ route('client.orders.index', ['status' => 'return']) }}" class="{{ $currentStatus === 'return' ? 'bg-amber-50 text-amber-700 border-amber-200 shadow-sm' : 'bg-white dark:bg-white/5 border-gray-200 dark:border-white/10 text-gray-600 dark:text-gray-300 hover:border-amber-400 hover:text-amber-600' }} border px-5 py-2.5 rounded-full text-sm font-bold whitespace-nowrap transition-all duration-300">Hoàn hàng trả tiền</a>
                <a href="{{ route('client.orders.index', ['status' => 'cancelled']) }}" class="{{ $currentStatus === 'cancelled' ? 'bg-gray-100 text-gray-800 border-gray-300 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-200 shadow-sm' : 'bg-white dark:bg-white/5 border-gray-200 dark:border-white/10 text-gray-600 dark:text-gray-300 hover:border-gray-400 hover:text-gray-800 dark:hover:text-white' }} border px-5 py-2.5 rounded-full text-sm font-bold whitespace-nowrap transition-all duration-300">Đã hủy</a>
            </div>
        {{-- DANH SÁCH ĐƠN HÀNG --}}
        @if (isset($orders) && $orders->count() > 0)
            <div class="space-y-6">
                @foreach ($orders as $order)
                    @php
                        $statusClass = $statusClasses[$order->status] ?? 'text-slate-700 bg-slate-100';
                        $returnClass = $returnClasses[$order->return_status] ?? 'text-slate-700 bg-slate-100';
                        $totalAmount = $order->total_amount ?? $order->total_price ?? 0;
                        $returnImageUrl = null;
                        if ($order->return_image) {
                            $returnImageUrl = \Illuminate\Support\Str::startsWith($order->return_image, ['http://', 'https://', 'uploads/'])
                                ? asset($order->return_image)
                                : asset('storage/' . $order->return_image);
                        }
                    @endphp

                    <div class="group overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm transition-colors hover:border-[#f4c025] dark:border-white/10 dark:bg-white/5">

                        {{-- Header Card --}}
                        <div class="flex flex-wrap items-center justify-between gap-4 border-b border-gray-100 bg-gray-50/50 px-6 py-4 dark:border-white/10 dark:bg-white/5">
                            <div class="flex items-center gap-4">
                                <span class="flex items-center gap-2 font-bold text-[#181611] dark:text-white">
                                    <span class="material-symbols-outlined text-[20px] text-[#f4c025]">receipt_long</span>
                                    <span class="uppercase tracking-wider">{{ $order->order_code }}</span>
                                </span>
                                <span class="border-l border-gray-300 dark:border-gray-700 font-semibold pl-4 text-[13px] text-gray-500">
                                    {{ optional($order->ordered_at ?? $order->created_at)->format('d/m/Y H:i') }}
                                </span>
                            </div>

                            <div class="flex flex-wrap items-center gap-2">
                                <span class="rounded-lg px-3 py-1.5 text-[11px] font-bold uppercase tracking-wider border {{ $statusClass }}">
                                    {{ $statusLabels[$order->status] ?? $order->status }}
                                </span>
                                @if($order->return_status != \App\Models\Order::RETURN_NONE)
                                    <span class="rounded-lg px-3 py-1.5 text-[11px] font-bold uppercase tracking-wider border {{ $returnClass }}">
                                        <span class="material-symbols-outlined text-[13px] inline-block align-middle mr-1">assignment_return</span>{{ $returnStatusLabels[$order->return_status] ?? $order->return_status }}
                                    </span>
                                @endif
                                <span class="rounded-lg px-2.5 py-1.5 text-[11px] font-bold uppercase tracking-wider border ml-1 border-gray-200 bg-gray-100 text-gray-600 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300">
                                    {{ $order->payment_method ?? 'COD' }}
                                </span>
                            </div>
                        </div>

                        {{-- Danh sách Sản Phẩm (Tóm tắt bản nâng cấp) --}}
                        <div class="px-6 py-5 space-y-4 cursor-pointer" onclick="window.location='{{ route('client.orders.show', $order->id) }}'">
                            @php 
                                $firstItem = $order->items->first(); 
                            @endphp

                            @if($firstItem)
                                @php
                                    $thumbnail = $firstItem->thumbnail ?? null;
                                    $imageUrl = $thumbnail
                                        ? (\Illuminate\Support\Str::startsWith($thumbnail, ['http://', 'https://']) ? $thumbnail : asset('storage/' . $thumbnail))
                                        : 'https://placehold.co/160x160?text=Bee+Phone';

                                    $baseName = $firstItem->product_name;
                                    $variantInfo = '';
                                    if (preg_match('/^(.*?)\s*\((.*?)\)$/', $firstItem->product_name, $matches)) {
                                        $baseName = trim($matches[1]);
                                        $variantInfo = trim($matches[2]);
                                    }
                                @endphp
                                <div class="flex items-center justify-between gap-4">
                                    <div class="flex items-center gap-4">
                                        <div class="h-20 w-20 shrink-0 rounded-xl border border-gray-100 bg-gray-50 p-2 dark:border-white/5 dark:bg-black/20">
                                            <img src="{{ $imageUrl }}" alt="{{ $baseName }}" class="h-full w-full object-contain mix-blend-multiply dark:mix-blend-normal">
                                        </div>
                                        <div class="flex-grow">
                                            <h3 class="line-clamp-1 text-[15px] font-bold text-[#181611] transition-colors group-hover:text-[#f4c025] dark:text-white">
                                                {{ $baseName }}
                                            </h3>
                                            @if($variantInfo)
                                                <p class="text-xs text-gray-400 mt-1 font-semibold border border-gray-100 dark:border-white/10 w-fit px-2 py-0.5 rounded uppercase tracking-wide">{{ $variantInfo }}</p>
                                            @endif
                                            <p class="mt-2 text-[13px] font-semibold text-gray-500">x{{ $firstItem->quantity }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        @if($firstItem->price > $firstItem->unit_price)
                                        <p class="text-xs text-gray-400 line-through mb-1">{{ number_format($firstItem->price) }} ₫</p>
                                        @endif
                                        <p class="text-[15px] font-bold text-[#181611] dark:text-white">{{ number_format($firstItem->unit_price) }} ₫</p>
                                    </div>
                                </div>
                            @endif

                            @if($order->items->count() > 1)
                                <div class="pt-4 text-center border-t border-gray-50 dark:border-white/5 mt-4">
                                    <span class="text-xs font-bold text-gray-400 uppercase tracking-widest bg-gray-50 dark:bg-white/5 px-4 py-1.5 rounded-full">
                                        ...và {{ $order->items->count() - 1 }} sản phẩm khác
                                    </span>
                                </div>
                            @endif
                        </div>

                        {{-- Đã loại bỏ khối thông tin Hoàn hàng cấp độ Đơn do chuyển xuống cấp độ Sản Phẩm --}}

                        {{-- Footer Card (Tổng tiền + Nút bấm) --}}
                        <div class="flex flex-wrap items-center justify-between gap-4 border-t border-gray-100 bg-gray-50/50 px-6 py-4 dark:border-white/10 dark:bg-white/5">
                            <div class="space-y-1">
                                <div class="flex items-center gap-2">
                                    <span class="text-[13px] font-bold text-gray-500 uppercase tracking-wide">Thành tiền /</span>
                                    <span class="text-lg font-bold text-red-500">{{ number_format($totalAmount, 0, ',', '.') }}<span class="text-sm">₫</span></span>
                                </div>
                                @if ($order->cancelled_at) <p class="text-xs text-gray-400 font-semibold flex items-center gap-1 mt-1.5"><span class="material-symbols-outlined text-[14px]">cancel</span> Đã hủy lúc {{ $order->cancelled_at->format('H:i d/m/Y') }}</p> @endif
                            </div>

                            <div class="flex max-w-full flex-wrap justify-end gap-3">
                                <a href="{{ route('client.orders.show', $order->id) }}" class="px-6 py-2.5 bg-white dark:bg-white/10 text-gray-700 dark:text-white font-bold rounded-xl hover:border-[#f4c025] hover:text-[#181611] dark:hover:text-[#f4c025] transition-all text-sm shadow-sm border border-gray-200 dark:border-white/10">
                                    Xem chi tiết
                                </a>

                                @if($order->status != 'cancelled' && $order->payment_status === 'pending' && in_array($order->payment_method, ['vnpay', 'vnp']))
                                    <a href="{{ route('client.checkout.retry', $order->id) }}" class="px-6 py-2.5 bg-[#f4c025] text-[#181611] font-bold rounded-xl hover:scale-105 transition-transform text-sm shadow-[0_4px_14px_0_rgba(244,192,37,0.39)]">
                                        Thanh toán ngay khoản thiếu
                                    </a>
                                @endif
                               

                                @if ($order->status === \App\Models\Order::STATUS_PENDING)
                                    <button type="button" onclick="openCancelModal('{{ $order->order_code }}', '{{ route('client.orders.cancel', $order->id) }}')" class="px-6 py-2.5 rounded-xl border border-red-200 bg-red-50 text-sm font-semibold text-red-600 transition hover:bg-red-500 hover:text-white dark:border-red-900/50 dark:bg-red-900/20 dark:hover:bg-red-600">
                                        Hủy đơn
                                    </button>
                                @endif

                                @if ($order->status === \App\Models\Order::STATUS_DELIVERED)
                                    <form action="{{ route('client.orders.confirm', $order->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="rounded-lg bg-green-500 px-6 py-2 text-sm font-semibold text-white shadow-md transition hover:bg-green-600" onclick="return confirm('Bạn xác nhận đã nhận được hàng?')">
                                            Đã nhận được hàng
                                        </button>
                                    </form>
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
            {{-- CHƯA CÓ ĐƠN HÀNG NÀO --}}
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
        {{-- MODAL HỦY ĐƠN HÀNG --}}
        <div id="cancel-modal" class="fixed inset-0 z-[9999] hidden items-center justify-center p-4 transition-all duration-300">
            <!-- Overlay -->
            <div class="absolute inset-0 bg-black/60 backdrop-blur-sm transition-opacity opacity-0 cancel-modal-overlay" onclick="closeCancelModal()"></div>
            
            <!-- Modal Content -->
            <div class="relative z-10 w-full max-w-md transform overflow-hidden rounded-3xl bg-white shadow-2xl transition-all scale-95 opacity-0 dark:bg-[#1a1a1a] border border-gray-100 dark:border-white/10 cancel-modal-content">
                <div class="px-6 py-6 sm:p-8">
                    <div class="mb-6 flex flex-col items-center text-center">
                        <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-red-50 dark:bg-red-500/10">
                            <span class="material-symbols-outlined text-[32px] text-red-500">warning</span>
                        </div>
                        <h3 class="text-xl font-bold text-[#181611] dark:text-white">Xác nhận hủy đơn hàng</h3>
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                            Bạn đang yêu cầu hủy đơn <strong id="cancel-order-code" class="text-[#181611] dark:text-white"></strong>. Hành động này không thể hoàn tác.
                        </p>
                    </div>

                    <form id="cancel-form" method="POST" action="">
                        @csrf
                        @method('PATCH')
                        <div class="mb-6">
                            <label class="mb-3 block text-sm font-bold text-[#181611] dark:text-white">Vui lòng chọn lý do hủy <span class="text-red-500">*</span></label>
                            <div class="space-y-2.5 max-h-[30vh] overflow-y-auto custom-scrollbar pr-1">
                                @php
                                    $reasons = [
                                        'Thay đổi ý định mua hàng',
                                        'Tìm thấy giá rẻ hơn ở nơi khác',
                                        'Đổi địa chỉ / Số điện thoại nhận hàng',
                                        'Đặt nhầm sản phẩm / số lượng',
                                        'Thời gian giao hàng quá lâu',
                                        'Lý do khác'
                                    ];
                                @endphp
                                @foreach($reasons as $index => $reason)
                                <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-gray-200 bg-white px-4 py-3 transition-colors hover:border-[#f4c025] hover:bg-[#f4c025]/5 has-[:checked]:border-[#f4c025] has-[:checked]:bg-[#f4c025]/10 dark:border-white/10 dark:bg-[#1a1a1a]">
                                    <input type="radio" name="cancellation_reason" value="{{ $reason }}" class="mt-0.5 h-4 w-4 text-[#f4c025] focus:ring-[#f4c025] border-gray-300 dark:bg-black/20 dark:border-white/20" required>
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300 leading-tight">{{ $reason }}</span>
                                </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="mt-8 flex gap-3">
                            <button type="button" onclick="closeCancelModal()" class="flex-1 rounded-xl border border-gray-200 bg-white py-3 text-sm font-bold text-gray-700 transition hover:bg-gray-50 dark:border-white/10 dark:bg-transparent dark:text-gray-300 dark:hover:bg-white/5">
                                Quay lại
                            </button>
                            <button type="submit" class="flex-1 rounded-xl bg-red-500 py-3 text-sm font-bold text-white shadow-[0_8px_20px_-8px_rgba(239,68,68,0.5)] transition hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-[#1a1a1a]">
                                Xác nhận hủy
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script>
            function openCancelModal(orderCode, actionUrl) {
                const modal = document.getElementById('cancel-modal');
                const overlay = modal.querySelector('.cancel-modal-overlay');
                const content = modal.querySelector('.cancel-modal-content');
                const form = document.getElementById('cancel-form');
                const orderCodeEl = document.getElementById('cancel-order-code');

                orderCodeEl.textContent = '#' + orderCode;
                form.action = actionUrl;

                // Reset radio buttons
                const radios = form.querySelectorAll('input[type="radio"]');
                radios.forEach(r => r.checked = false);

                modal.classList.remove('hidden');
                modal.classList.add('flex');
                
                // Allow display flex to apply before animating opacity/transform
                requestAnimationFrame(() => {
                    overlay.classList.remove('opacity-0');
                    overlay.classList.add('opacity-100');
                    content.classList.remove('scale-95', 'opacity-0');
                    content.classList.add('scale-100', 'opacity-100');
                });
            }

            function closeCancelModal() {
                const modal = document.getElementById('cancel-modal');
                const overlay = modal.querySelector('.cancel-modal-overlay');
                const content = modal.querySelector('.cancel-modal-content');

                overlay.classList.remove('opacity-100');
                overlay.classList.add('opacity-0');
                content.classList.remove('scale-100', 'opacity-100');
                content.classList.add('scale-95', 'opacity-0');

                setTimeout(() => {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                }, 300); // Wait for transition
            }
        </script>
    </section>

    <style>
        .custom-scrollbar::-webkit-scrollbar { height: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 10px; }
        .custom-scrollbar:hover::-webkit-scrollbar-thumb { background: #f4c025; }
    </style>
@endsection
