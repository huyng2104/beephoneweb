@extends('client.layouts.app')

@section('title', 'Bee Phone - Đặt hàng thành công')

@section('content')
<main class="max-w-[800px] mx-auto py-12 px-6">
    <div class="bg-white dark:bg-black/20 rounded-xl shadow-sm border border-[#e6e3db] dark:border-white/10 overflow-hidden animate-[fade-in-up_0.6s_ease-out]">
        
        <div class="bg-primary p-8 text-center relative overflow-hidden">
            <div class="absolute -top-10 -left-10 w-32 h-32 bg-white/20 rounded-full blur-2xl"></div>
            <div class="absolute -bottom-10 -right-10 w-32 h-32 bg-white/20 rounded-full blur-2xl"></div>

            <div class="bg-white w-16 h-16 rounded-full mx-auto flex items-center justify-center mb-4 relative z-10 shadow-lg animate-[bounce_1s_ease-in-out_infinite]">
                <span class="material-symbols-outlined text-green-500 text-3xl font-bold">check_circle</span>
            </div>
            <h1 class="text-2xl font-bold text-[#181611] relative z-10 uppercase tracking-tight">Đặt hàng thành công!</h1>
            <p class="text-[#181611]/80 mt-1 font-medium relative z-10">Cảm ơn bạn đã tin tưởng Bee Phone</p>
        </div>
        
        <div class="p-8">
            <div class="mb-8">
                <h3 class="text-lg font-bold text-[#181611] dark:text-white mb-2">Chào {{ $order->customer_name }},</h3>
                <p class="text-[#8a8060] dark:text-gray-400 leading-relaxed">
                    Đơn hàng <span class="font-bold text-[#181611] dark:text-primary">#{{ $order->order_code }}</span> của bạn đã được xác nhận và đang trong quá trình chuẩn bị để giao đi.
                </p>
            </div>

            <div class="bg-[#f8f8f5] dark:bg-white/5 rounded-lg p-6 mb-8 border border-[#e6e3db] dark:border-white/10">
                <h4 class="font-bold text-[#181611] dark:text-white mb-4 uppercase text-xs tracking-wider">Thông tin đơn hàng</h4>
                
                <div class="space-y-4">
                    {{-- VÒNG LẶP IN RA CÁC MÓN HÀNG ĐÃ MUA --}}
                    @foreach($order->items as $item)
                        @php
                            $imageUrl = Str::startsWith($item->thumbnail, ['http://', 'https://']) ? $item->thumbnail : asset('storage/' . $item->thumbnail);
                        @endphp
                        <div class="flex items-center gap-4">
                            <div class="w-16 h-16 bg-white dark:bg-black/40 border border-[#e6e3db] dark:border-white/10 rounded-lg p-1 shrink-0">
                                <img class="w-full h-full object-contain mix-blend-multiply dark:mix-blend-normal" src="{{ $imageUrl }}" alt="{{ $item->product_name }}"/>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-bold text-[#181611] dark:text-white line-clamp-2">{{ $item->product_name }}</p>
                                <p class="text-xs text-[#8a8060] dark:text-gray-400 mt-1">Số lượng: {{ $item->quantity }}</p>
                            </div>
                            <p class="text-sm font-bold text-red-500">{{ number_format($item->line_total, 0, ',', '.') }}đ</p>
                        </div>
                    @endforeach
                    
                    <hr class="border-[#e6e3db] dark:border-white/10"/>
                    
                    {{-- TÍNH TIỀN --}}
                    <div class="flex justify-between text-sm">
                        <span class="text-[#8a8060] dark:text-gray-400">Tạm tính:</span>
                        <span class="text-[#181611] dark:text-white font-medium">{{ number_format($order->total_price, 0, ',', '.') }}đ</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-[#8a8060] dark:text-gray-400">Phí vận chuyển:</span>
                        <span class="text-[#181611] dark:text-white font-medium">Miễn phí</span>
                    </div>
                    
                    @if($order->total_price > $order->total_amount)
                    <div class="flex justify-between text-sm">
                        <span class="text-green-600 font-medium flex items-center gap-1">
                            <span class="material-symbols-outlined text-[14px]">loyalty</span> Giảm giá (Voucher):
                        </span>
                        <span class="text-green-600 font-bold">-{{ number_format($order->total_price - $order->total_amount, 0, ',', '.') }}đ</span>
                    </div>
                    @endif

                    <div class="flex justify-between items-center text-lg font-bold border-t border-[#e6e3db] dark:border-white/10 pt-4 mt-2">
                        <span class="text-[#181611] dark:text-white">Tổng thanh toán:</span>
                        <span class="text-primary text-2xl">{{ number_format($order->total_amount, 0, ',', '.') }}đ</span>
                    </div>
                </div>
            </div>

            <div class="mb-8 bg-gray-50 dark:bg-white/5 p-4 rounded-lg border border-dashed border-gray-200 dark:border-white/10">
                <h4 class="font-bold text-[#181611] dark:text-white mb-2 text-sm flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-lg">location_on</span>
                    Địa chỉ nhận hàng:
                </h4>
                <p class="text-sm text-[#8a8060] dark:text-gray-300 ml-7 leading-relaxed">
                    {{ $order->recipient_address }}<br/>
                    SĐT: <span class="font-bold">{{ $order->recipient_phone }}</span>
                </p>
            </div>

            <div class="flex justify-center gap-4">
                <a href="{{ route('home') }}" class="bg-gray-100 dark:bg-white/10 text-[#181611] dark:text-white font-bold py-3 px-8 rounded-xl hover:bg-gray-200 transition-colors shadow-sm">
                    Về trang chủ
                </a>
                @if(Auth::check())
                <a href="{{ route('client.orders.index') }}" class="bg-[#181611] dark:bg-primary text-white dark:text-[#181611] font-bold py-3 px-8 rounded-xl hover:opacity-90 transition-colors shadow-lg">
    Theo dõi đơn hàng
</a>
                @endif
            </div>
        </div>

        <div class="bg-[#f8f8f5] dark:bg-black/40 p-6 text-center border-t border-[#e6e3db] dark:border-white/10">
            <p class="text-xs text-[#8a8060] dark:text-gray-400">Nếu bạn có bất kỳ thắc mắc nào, vui lòng liên hệ hotline <span class="font-bold text-primary">1900 6789</span></p>
            <div class="flex justify-center gap-4 mt-4">
                <div class="size-8 bg-[#e6e3db] dark:bg-white/10 rounded-full flex items-center justify-center hover:bg-primary transition-colors cursor-pointer"><span class="material-symbols-outlined text-sm text-[#181611] dark:text-white">public</span></div>
                <div class="size-8 bg-[#e6e3db] dark:bg-white/10 rounded-full flex items-center justify-center hover:bg-primary transition-colors cursor-pointer"><span class="material-symbols-outlined text-sm text-[#181611] dark:text-white">forum</span></div>
                <div class="size-8 bg-[#e6e3db] dark:bg-white/10 rounded-full flex items-center justify-center hover:bg-primary transition-colors cursor-pointer"><span class="material-symbols-outlined text-sm text-[#181611] dark:text-white">mail</span></div>
            </div>
        </div>
        </div>

        @if(!empty($order) && $order->items && $order->items->count())
            <div class="bg-white dark:bg-[#1a1a1a] p-8 rounded-[2rem] shadow-xl border border-gray-100 dark:border-white/5">
                <div class="flex items-center justify-between gap-4 flex-wrap">
                    <h2 class="text-2xl font-black text-[#181611] dark:text-white">Đánh giá sản phẩm</h2>
                    <div class="text-xs font-bold uppercase tracking-widest text-gray-400">
                        Mã đơn: {{ $order->order_code }}
                    </div>
                </div>
                <p class="mt-2 text-gray-600 dark:text-gray-300">
                    Sau khi đánh giá xong, đánh giá sẽ hiển thị ở phần comment của sản phẩm và được gắn tag <span class="font-black text-primary">Đã mua</span>.
                </p>

                <div class="mt-6 space-y-5">
                    @foreach($order->items as $item)
                        @php
                            $p = $item->product;
                        @endphp
                        @if($p)
                            @php
                                $productParam = $p->slug ?: $p->id;
                            @endphp
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
                                            <a href="{{ route('client.product.detail', ['slug' => $productParam]) }}#comments" class="text-sm font-black text-primary hover:underline">
                                                Xem sản phẩm
                                            </a>
                                        </div>

                                        <form action="{{ route('products.comments.store', $p) }}" method="POST" enctype="multipart/form-data" class="mt-4 grid gap-3">
                                            @csrf
                                            <input type="hidden" name="redirect_to" value="{{ route('client.product.detail', ['slug' => $productParam]) }}#comments">

                                            @guest
                                                <div class="grid gap-3 sm:grid-cols-2">
                                                    <div>
                                                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400">Tên</label>
                                                        <input name="guest_name" required value="{{ $order->customer_name ?? '' }}" class="mt-1 w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-black/20 px-4 py-3 text-sm text-[#181611] dark:text-white">
                                                    </div>
                                                    <div>
                                                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400">Email</label>
                                                        <input name="guest_email" type="email" required value="{{ $order->customer_email ?? '' }}" class="mt-1 w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-black/20 px-4 py-3 text-sm text-[#181611] dark:text-white" placeholder="email@example.com">
                                                    </div>
                                                </div>
                                            @endguest

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
                                                    <label class="block text-xs font-bold text-gray-500 dark:text-gray-400">Ảnh (tuỳ chọn)</label>
                                                    <input type="file" name="image" accept=".jpg,.jpeg,.png,.webp" class="mt-1 w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-black/20 px-4 py-3 text-sm text-gray-600 dark:text-gray-300 file:mr-4 file:rounded-lg file:border-0 file:bg-primary/20 file:px-4 file:py-2 file:text-xs file:font-black file:text-[#181611] hover:file:bg-primary/30">
                                                </div>
                                            </div>

                                            <div>
                                                <label class="block text-xs font-bold text-gray-500 dark:text-gray-400">Nội dung</label>
                                                <textarea name="content" rows="3" required class="mt-1 w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-black/20 px-4 py-3 text-sm text-[#181611] dark:text-white" placeholder="Chia sẻ cảm nhận của bạn..."></textarea>
                                            </div>

                                            <button type="submit" class="btn-shine inline-flex items-center justify-center rounded-xl bg-primary px-6 py-3 text-sm font-black text-black hover:brightness-105 active:scale-95 transition-all shadow-[0_14px_30px_-14px_rgba(244,192,37,0.45)]">
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
        @endif
    </div>
</main>

<style>
    @keyframes fade-in-up {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endsection
