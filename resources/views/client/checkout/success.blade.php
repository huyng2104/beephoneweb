@extends('client.layouts.app')

@section('title', 'Bee Phone - Đặt hàng thành công')

@section('content')
<main class="max-w-[800px] mx-auto py-12 px-6 min-h-[80vh]">
    <div class="bg-white dark:bg-black/20 rounded-xl shadow-sm border border-[#e6e3db] dark:border-white/10 overflow-hidden animate-[fade-in-up_0.6s_ease-out]">
        
        <div class="bg-primary p-8 text-center relative overflow-hidden">
            <div class="absolute -top-10 -left-10 w-32 h-32 bg-white/20 rounded-full blur-2xl"></div>
            <div class="absolute -bottom-10 -right-10 w-32 h-32 bg-white/20 rounded-full blur-2xl"></div>

            <div class="bg-white w-16 h-16 rounded-full mx-auto flex items-center justify-center mb-4 relative z-10 shadow-[0_0_30px_rgba(255,255,255,0.5)] animate-bounce-in">
                <span class="material-symbols-outlined text-green-500 text-4xl font-bold">check_circle</span>
            </div>
            <h1 class="text-2xl font-black text-[#181611] relative z-10 uppercase tracking-tight ai-sparkle-text">Đặt hàng thành công!</h1>
            <p class="text-[#181611]/80 mt-1 font-bold relative z-10">Cảm ơn bạn đã tin tưởng Bee Phone</p>
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
                        <span class="text-green-600 font-bold">Miễn phí</span>
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
                <a href="{{ route('client.orders.show', $order->id) }}" class="bg-[#181611] dark:bg-primary text-white dark:text-[#181611] font-bold py-3 px-8 rounded-xl hover:opacity-90 transition-colors shadow-lg">
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
</main>

<style>
    @keyframes fade-in-up {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    /* Chữ phát sáng */
    .ai-sparkle-text {
        background: linear-gradient(90deg, #181611 0%, #6b6b6b 50%, #181611 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-size: 200% auto;
        animation: shine 3s linear infinite;
    }
    .dark .ai-sparkle-text {
        background: linear-gradient(90deg, #ffffff 0%, #f4c025 50%, #ffffff 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-size: 200% auto;
    }
    @keyframes shine {
        to { background-position: 200% center; }
    }

    /* Hiệu ứng nảy mạnh */
    .animate-bounce-in {
        animation: bounceIn 0.8s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
    }
    @keyframes bounceIn {
        0% { transform: scale(0); opacity: 0; }
        50% { transform: scale(1.3); opacity: 1; }
        100% { transform: scale(1); opacity: 1; }
    }
</style>
@endsection

{{-- ĐẨY SCRIPT PHÁO GIẤY VÀ ÂM THANH XUỐNG CUỐI LAYOUT --}}
@push('js')
<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // 1. CHƠI ÂM THANH "TING" THÀNH CÔNG
        const audio = new Audio('https://cdn.pixabay.com/download/audio/2021/08/04/audio_0625c1539c.mp3?filename=success-1-6297.mp3');
        audio.volume = 0.5; // Giảm âm lượng 50%
        audio.play().catch(function(error) {
            console.log("Trình duyệt có thể chặn Autoplay âm thanh lần đầu!");
        });

        // 2. BẮN PHÁO GIẤY TỪ 2 BÊN MÀN HÌNH
        var duration = 3.5 * 1000; 
        var animationEnd = Date.now() + duration;
        // Tone màu Vàng Bee Phone + Trắng + Đen
        var defaults = { startVelocity: 30, spread: 360, ticks: 60, zIndex: 9999, colors: ['#f4c025', '#ffffff', '#181611'] };

        function randomInRange(min, max) {
            return Math.random() * (max - min) + min;
        }

        var interval = setInterval(function() {
            var timeLeft = animationEnd - Date.now();

            if (timeLeft <= 0) {
                return clearInterval(interval);
            }

            var particleCount = 50 * (timeLeft / duration);
            // Bắn góc trái
            confetti(Object.assign({}, defaults, { 
                particleCount,
                origin: { x: randomInRange(0.1, 0.3), y: Math.random() - 0.2 }
            }));
            // Bắn góc phải
            confetti(Object.assign({}, defaults, { 
                particleCount,
                origin: { x: randomInRange(0.7, 0.9), y: Math.random() - 0.2 }
            }));
        }, 250);
    });
</script>
@endpush
