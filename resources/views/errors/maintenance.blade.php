<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bảo trì hệ thống | {{ $site_settings['site_name']->value ?? 'Bee Phone' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" />
    <style>
        body { font-family: 'Space Grotesk', sans-serif; }
        .gradient-bg {
            background: radial-gradient(circle at top right, #f4c025 0%, transparent 40%),
                        radial-gradient(circle at bottom left, #f4c025 0%, transparent 40%),
                        #221e10;
        }
        @keyframes rotate-gear {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        .gear-animation {
            animation: rotate-gear 8s linear infinite;
        }
    </style>
</head>
<body class="gradient-bg min-h-screen flex items-center justify-center p-6 text-white overflow-hidden">
    <!-- Decorative Elements -->
    <div class="fixed top-20 left-20 opacity-10 gear-animation">
        <span class="material-symbols-outlined text-[200px]">settings</span>
    </div>
    <div class="fixed bottom-20 right-20 opacity-10 gear-animation" style="animation-direction: reverse;">
        <span class="material-symbols-outlined text-[150px]">build</span>
    </div>

    <div class="max-w-2xl w-full text-center relative z-10">
        <!-- Logo -->
        <div class="mb-12 flex flex-col items-center gap-4 animate-bounce">
            @php
                $site_settings = \App\Models\Setting::all()->keyBy('key');
            @endphp
            @if(isset($site_settings['site_logo']) && $site_settings['site_logo']->value)
                <img src="{{ asset($site_settings['site_logo']->value) }}" alt="Logo" class="h-20 w-auto">
            @else
                <div class="size-20 bg-[#f4c025] rounded-[2rem] flex items-center justify-center text-black shadow-[0_0_50px_rgba(244,192,37,0.3)]">
                    <span class="material-symbols-outlined text-5xl">rocket_launch</span>
                </div>
            @endif
        </div>

        <!-- Content -->
        <h1 class="text-4xl md:text-6xl font-bold mb-6 tracking-tight">BeePhone đang <span class="text-[#f4c025]">nâng cấp</span> hệ thống</h1>
        
        <p class="text-lg md:text-xl text-gray-400 font-medium mb-10 leading-relaxed max-w-xl mx-auto">
            {{ $message ?? 'Chúng tôi đang tiến hành bảo trì định kỳ để mang lại trải nghiệm tốt nhất cho bạn. Vui lòng quay lại sau.' }}
        </p>

        @if($end_at)
            <div class="inline-flex flex-col items-center bg-white/5 backdrop-blur-xl border border-white/10 rounded-3xl p-8 mb-10 shadow-2xl">
                <span class="text-xs font-bold uppercase tracking-[0.3em] text-[#f4c025] mb-2">Dự kiến hoàn thành</span>
                <span class="text-3xl font-bold tracking-tight">
                    {{ \Carbon\Carbon::parse($end_at)->format('H:i - d/m/Y') }}
                </span>
            </div>
        @endif

        <div class="flex flex-col md:flex-row items-center justify-center gap-6">
            <a href="mailto:{{ $site_settings['email_contact']->value ?? 'contact@beephone.vn' }}" class="flex items-center gap-3 px-8 py-4 bg-white/5 hover:bg-white/10 border border-white/10 rounded-2xl transition-all font-bold">
                <span class="material-symbols-outlined text-primary">mail</span>
                Liên hệ hỗ trợ
            </a>
            <div class="flex items-center gap-3 px-8 py-4 bg-white/5 border border-white/10 rounded-2xl font-bold">
                <span class="material-symbols-outlined text-primary">call</span>
                {{ $site_settings['hotline']->value ?? '1900 xxxx' }}
            </div>
        </div>

        <p class="mt-20 text-sm text-gray-500 font-medium tracking-widest uppercase">
            &copy; {{ date('Y') }} {{ $site_settings['site_name']->value ?? 'Bee Phone' }}. All rights reserved.
        </p>
    </div>
</body>
</html>
