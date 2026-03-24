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

<main class="min-h-[80vh] flex items-center justify-center p-6 bg-gray-50/50 dark:bg-black">
    <div class="bg-white dark:bg-[#1a1a1a] p-10 rounded-[2.5rem] shadow-2xl text-center max-w-lg border border-gray-100 dark:border-white/5 relative overflow-hidden">
        
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
</main>
@endsection