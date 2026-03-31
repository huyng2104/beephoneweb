@extends('client.profiles.layouts.app')

@section('title', 'Bee Phone - Lịch sử đơn h&agrave;ng')

@section('profile_content')
    <section class="flex-1" data-purpose="user-main-section">

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
                <h1 class="text-2xl font-bold uppercase tracking-tight text-[#181611] dark:text-white">Lịch sử đơn h&agrave;ng</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Quản l&yacute; v&agrave; theo d&otilde;i trạng th&aacute;i c&aacute;c đơn h&agrave;ng của bạn</p>
            </div>
            
            <div class="flex gap-2 overflow-x-auto custom-scrollbar pb-2 sm:pb-0">
                <a href="#" class="bg-[#f4c025] text-[#1a1a1a] px-5 py-2 rounded-lg text-sm font-bold whitespace-nowrap shadow-sm">Tất cả</a>
                <a href="#" class="bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 px-5 py-2 rounded-lg text-sm text-gray-600 dark:text-gray-300 whitespace-nowrap hover:border-[#f4c025] hover:text-[#f4c025] transition-colors">Chờ x&aacute;c nhận</a>
                <a href="#" class="bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 px-5 py-2 rounded-lg text-sm text-gray-600 dark:text-gray-300 whitespace-nowrap hover:border-[#f4c025] hover:text-[#f4c025] transition-colors">Đang giao</a>
                <a href="#" class="bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 px-5 py-2 rounded-lg text-sm text-gray-600 dark:text-gray-300 whitespace-nowrap hover:border-[#f4c025] hover:text-[#f4c025] transition-colors">Ho&agrave;n th&agrave;nh</a>
            </div>
        </div>

        @if(isset($orders) && $orders->count() > 0)
            <div class="space-y-6">
                @foreach($orders as $order)
                    <div class="bg-white dark:bg-white/5 rounded-2xl shadow-sm border border-gray-100 dark:border-white/10 overflow-hidden hover:border-[#f4c025] transition-colors group">
                        
                        <div class="px-6 py-4 border-b border-gray-100 dark:border-white/10 flex flex-wrap items-center justify-between gap-4 bg-gray-50/50 dark:bg-white/5">
                            <div class="flex items-center gap-4">
                                <span class="font-bold text-[#181611] dark:text-white flex items-center gap-2">
                                    <span class="material-symbols-outlined text-[20px] text-[#f4c025]">receipt_long</span> 
                                    {{ $order->order_code }}
                                </span>
                                <span class="text-sm text-gray-500 border-l border-gray-300 pl-4">{{ \Carbon\Carbon::parse($order->created_at)->format('d/m/Y H:i') }}</span>
                            </div>
                            
                            <div>
                                @if($order->status == 'pending')
                                    <span class="text-yellow-700 bg-yellow-100 px-3 py-1 rounded-md text-xs font-bold uppercase tracking-wider">Chờ x&aacute;c nhận</span>
                                @elseif($order->status == 'packing')
                                    <span class="text-blue-700 bg-blue-100 px-3 py-1 rounded-md text-xs font-bold uppercase tracking-wider">Đang đ&oacute;ng g&oacute;i</span>
                                @elseif($order->status == 'shipping')
                                    <span class="text-indigo-700 bg-indigo-100 px-3 py-1 rounded-md text-xs font-bold uppercase tracking-wider">Đang giao h&agrave;ng</span>
                                @elseif($order->status == 'completed')
                                    <span class="text-green-700 bg-green-100 px-3 py-1 rounded-md text-xs font-bold uppercase tracking-wider">Đ&atilde; ho&agrave;n th&agrave;nh</span>
                                @elseif($order->status == 'cancelled')
                                    <span class="text-red-700 bg-red-100 px-3 py-1 rounded-md text-xs font-bold uppercase tracking-wider">Đ&atilde; hủy</span>
                                @endif
                            </div>
                        </div>

                      <div class="px-6 py-4 space-y-4">
                            {{-- Lấy tối đa 2 sản phẩm để hiển thị thôi --}}
                            @php $visibleItems = $order->items->take(2); @endphp
                            
                            @foreach($visibleItems as $item)
                                @php
                                    $imageUrl = Str::startsWith($item->thumbnail, ['http://', 'https://']) ? $item->thumbnail : asset('storage/' . $item->thumbnail);
                                @endphp
                                <div class="flex items-center gap-4">
                                    <div class="w-20 h-20 bg-gray-50 dark:bg-black/20 rounded-xl p-2 border border-gray-100 dark:border-white/5 flex-shrink-0">
                                        <img src="{{ $imageUrl }}" alt="{{ $item->product_name }}" class="w-full h-full object-contain mix-blend-multiply dark:mix-blend-normal">
                                    </div>
                                    <div class="flex-grow">
                                        <h3 class="font-bold text-[#181611] dark:text-white line-clamp-1 group-hover:text-[#f4c025] transition-colors">{{ $item->product_name }}</h3>
                                        <p class="text-sm text-gray-500 mt-1">Số lượng: x{{ $item->quantity }}</p>
                                    </div>
                                    <div class="text-right">
                                        <span class="font-bold text-[#181611] dark:text-white">{{ number_format($item->unit_price, 0, ',', '.') }}₫</span>
                                    </div>
                                </div>
                            @endforeach

                            {{-- Nút xem thêm nếu đơn hàng có > 2 sản phẩm --}}
                            @if($order->items->count() > 2)
                                <div class="pt-2 text-center border-t border-gray-100 dark:border-white/5 mt-2">
                                    <a href="{{ route('client.orders.show', $order->id) }}" class="text-sm font-medium text-gray-500 hover:text-[#f4c025] transition-colors flex items-center justify-center gap-1">
                                        Xem thêm {{ $order->items->count() - 2 }} sản phẩm khác
                                        <span class="material-symbols-outlined text-[16px]">expand_more</span>
                                    </a>
                                </div>
                            @endif
                        </div>

                        <div class="px-6 py-4 border-t border-gray-100 dark:border-white/10 flex flex-wrap items-center justify-between gap-4">
                            <div>
                                <span class="text-sm text-gray-500">Th&agrave;nh tiền:</span>
                                <span class="text-xl font-black text-red-500 ml-2">{{ number_format($order->total_price, 0, ',', '.') }}₫</span>
                            </div>
                           <div class="flex gap-3">
   <a href="{{ route('client.orders.show', $order->id) }}" class="px-6 py-2 bg-gray-100 dark:bg-white/10 text-[#181611] dark:text-white font-bold rounded-lg hover:bg-[#f4c025] hover:text-black transition-colors text-sm shadow-sm border border-gray-200 dark:border-white/10">
    Xem chi tiết
</a>

   @if($order->status == \App\Models\Order::STATUS_PENDING)
        <form action="{{ route('client.orders.cancel', $order->id) }}" method="POST" class="inline">
            @csrf
            @method('PATCH')
            <button type="submit" class="px-6 py-2 bg-red-50 text-red-600 border border-red-200 font-semibold rounded-lg hover:bg-red-500 hover:text-white transition text-sm" onclick="return confirm('Bạn có chắc chắn muốn hủy đơn hàng này?')">
                Hủy đơn
            </button>
        </form>
    @endif
    @if($order->status == \App\Models\Order::STATUS_DELIVERED)
        <form action="{{ route('client.orders.confirm', $order->id) }}" method="POST" class="inline">
            @csrf
            @method('PATCH')
            <button type="submit" class="px-6 py-2 bg-green-500 text-white font-semibold rounded-lg hover:bg-green-600 transition text-sm shadow-md" onclick="return confirm('Bạn xác nhận đã nhận được hàng và sản phẩm không có vấn đề gì?')">
                Đã nhận được hàng
            </button>
        </form>
    @endif

    @if($order->status == \App\Models\Order::STATUS_RECEIVED)
        <button class="px-6 py-2 bg-[#f4c025] text-[#1a1a1a] font-semibold rounded-lg hover:opacity-90 transition text-sm shadow-sm">
            Mua lại
        </button>
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
            <div class="flex flex-col items-center justify-center py-20 bg-white dark:bg-white/5 rounded-2xl border border-dashed border-gray-200 dark:border-white/10">
                <div class="w-20 h-20 bg-gray-50 dark:bg-white/5 rounded-full flex items-center justify-center mb-4">
                    <span class="material-symbols-outlined text-4xl text-gray-300 dark:text-gray-600">receipt_long</span>
                </div>
                <h2 class="text-xl font-bold text-[#181611] dark:text-white mb-2">Bạn chưa c&oacute; đơn h&agrave;ng n&agrave;o</h2>
                <p class="text-gray-500 dark:text-gray-400 mb-6 text-sm">H&atilde;y lượn một v&ograve;ng v&agrave; sắm v&agrave;i m&oacute;n đồ c&ocirc;ng nghệ nh&eacute;!</p>
                <a href="{{ route('client.products.index') }}" class="bg-[#f4c025] text-black font-bold px-8 py-2.5 rounded-lg hover:scale-105 transition-transform shadow-sm">
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
