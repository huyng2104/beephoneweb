@extends('client.layouts.app')

@section('title', 'Bee Phone - Đặt hàng thành công')

@section('content')
<style>
    /* Hiệu ứng xoay tròn và bung tỏa cho Icon */
    @keyframes success-check {
        0% { transform: scale(0) rotate(-180deg); opacity: 0; }
        60% { transform: scale(1.2) rotate(20deg); opacity: 1; }
        100% { transform: scale(1) rotate(0deg); }
    }

    /* Hiệu ứng trồi lên cho phần chữ */
    @keyframes fade-up {
        from { transform: translateY(20px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }

    .animate-success-icon {
        animation: success-check 0.8s cubic-bezier(0.4, 0, 0.2, 1) forwards;
    }

    .animate-text-content {
        opacity: 0;
        animation: fade-up 0.6s ease-out 0.5s forwards;
    }

    /* Hiệu ứng lấp lánh chạy qua nút */
    .btn-shine {
        position: relative;
        overflow: hidden;
    }
    .btn-shine::after {
        content: "";
        position: absolute;
        top: -50%;
        left: -60%;
        width: 20%;
        height: 200%;
        background: rgba(255, 255, 255, 0.4);
        transform: rotate(30deg);
        transition: 0s;
    }
    .btn-shine:hover::after {
        left: 120%;
        transition: 0.6s;
    }
</style>

<main class="min-h-[80vh] flex items-start justify-center p-6 py-10 bg-gray-50/50 dark:bg-black">
    <div class="w-full max-w-4xl space-y-6">
        <div class="bg-white dark:bg-[#1a1a1a] p-10 rounded-[2.5rem] shadow-2xl text-center border border-gray-100 dark:border-white/5 relative overflow-hidden">
        
        <div class="absolute -top-24 -left-24 w-48 h-48 bg-primary/10 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-24 -right-24 w-48 h-48 bg-green-500/10 rounded-full blur-3xl"></div>

        <div class="relative z-10">
            <div class="animate-success-icon w-28 h-28 bg-green-100 dark:bg-green-500/20 text-green-500 rounded-full flex items-center justify-center mx-auto mb-8 shadow-inner border-4 border-white dark:border-gray-800">
                <span class="material-symbols-outlined text-7xl">verified</span>
            </div>

            <div class="animate-text-content">
                <h1 class="text-4xl font-black mb-4 text-[#181611] dark:text-white tracking-tight">Tuyệt vời!</h1>
                
                <p class="text-gray-600 dark:text-gray-300 text-lg mb-8 leading-relaxed">
                    {{ session('success') ?? 'Bạn đã đặt hàng thành công tại Bee Phone.' }}
                </p>

                <div class="bg-gray-50 dark:bg-white/5 p-5 rounded-2xl mb-10 border border-gray-100 dark:border-white/5">
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Chúng tôi sẽ sớm liên hệ với bạn qua số điện thoại để xác nhận và tiến hành giao hàng sớm nhất có thể.
                    </p>
                </div>

                <a href="{{ route('home') }}" 
                   class="btn-shine inline-flex items-center gap-3 bg-primary text-black font-black px-10 py-5 rounded-2xl hover:scale-105 active:scale-95 transition-all shadow-[0_20px_40px_-10px_rgba(244,192,37,0.4)]">
                    <span class="material-symbols-outlined">home</span> 
                    VỀ TRANG CHỦ
                </a>
                
                <p class="mt-8 text-xs text-gray-400 dark:text-gray-500 uppercase tracking-widest font-bold">
                    Cảm ơn bạn đã tin tưởng Bee Phone!
                </p>
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
@endsection
