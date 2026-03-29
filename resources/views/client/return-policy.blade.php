@extends('client.layouts.app')

@section('title', 'Chính Sách Đổi Trả | Bee Phone')

@section('content')
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&amp;family=Inter:wght@300;400;500;600&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<script id="tailwind-config">
      tailwind.config = {
        darkMode: "class",
        theme: {
          extend: {
            colors: {
              "on-tertiary-fixed-variant": "#004e5a",
              "tertiary-fixed-dim": "#00daf8",
              "secondary-fixed": "#e2e2e2",
              "on-secondary-fixed-variant": "#474747",
              "on-error-container": "#93000a",
              "secondary-fixed-dim": "#c6c6c6",
              "outline-variant": "#d4c5ab",
              "on-primary": "#ffffff",
              "surface-container": "#eeeeee",
              "on-tertiary": "#ffffff",
              "tertiary-fixed": "#a5eeff",
              "surface-container-lowest": "#ffffff",
              "surface-variant": "#e2e2e2",
              "on-tertiary-container": "#005e6c",
              "on-secondary-fixed": "#1b1b1b",
              "surface-bright": "#f9f9f9",
              "inverse-surface": "#2f3131",
              "surface": "#f9f9f9",
              "on-primary-container": "#6d5100",
              "on-error": "#ffffff",
              "primary-container": "#ffc107",
              "on-tertiary-fixed": "#001f25",
              "secondary-container": "#e2e2e2",
              "secondary": "#5e5e5e",
              "inverse-primary": "#fabd00",
              "surface-container-highest": "#e2e2e2",
              "primary-fixed": "#ffdf9e",
              "tertiary": "#006877",
              "on-primary-fixed-variant": "#5b4300",
              "outline": "#827660",
              "on-surface-variant": "#4f4632",
              "surface-container-high": "#e8e8e8",
              "surface-container-low": "#f3f3f4",
              "inverse-on-surface": "#f0f1f1",
              "surface-dim": "#dadada",
              "on-background": "#1a1c1c",
              "primary": "#785900",
              "on-primary-fixed": "#261a00",
              "primary-fixed-dim": "#fabd00",
              "on-surface": "#1a1c1c",
              "on-secondary": "#ffffff",
              "surface-tint": "#785900",
              "error": "#ba1a1a",
              "background": "#f9f9f9",
              "tertiary-container": "#00defd",
              "error-container": "#ffdad6",
              "on-secondary-container": "#646464"
            },
            fontFamily: {
              "headline": ["Plus Jakarta Sans"],
              "body": ["Inter"],
              "label": ["Inter"]
            },
            borderRadius: {"DEFAULT": "0.125rem", "lg": "0.25rem", "xl": "0.5rem", "full": "0.75rem"},
          },
        },
      }
    </script>
<style>
      .material-symbols-outlined {
        font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
      }
    </style>

<main class="pt-24 pb-20 px-6 md:px-12 max-w-screen-2xl mx-auto flex flex-col md:flex-row gap-12">
<!-- SideNavBar -->
<aside class="hidden md:flex flex-col w-64 gap-2 p-4 sticky top-20 rounded-lg bg-zinc-50 dark:bg-zinc-900 h-fit">
<div class="mb-4 px-3">
<h3 class="text-zinc-900 dark:text-white font-bold text-lg font-headline">Legal Center</h3>
<p class="text-zinc-500 dark:text-zinc-400 text-xs uppercase tracking-widest">Policy Navigation</p>
</div>
<a class="flex items-center gap-3 text-zinc-500 dark:text-zinc-400 py-2 px-3 hover:bg-zinc-200 dark:hover:bg-zinc-800 transition-all duration-300 font-['Plus_Jakarta_Sans'] text-sm font-medium" href="{{ route('privacy-policy') }}">
<span class="material-symbols-outlined text-yellow-600 dark:text-yellow-500" data-icon="security">security</span>
                Privacy Policy
            </a>
<a class="flex items-center gap-3 text-yellow-700 dark:text-yellow-400 bg-yellow-50 dark:bg-yellow-950/30 rounded-md py-2 px-3 hover:bg-zinc-200 dark:hover:bg-zinc-800 transition-all duration-300 font-['Plus_Jakarta_Sans'] text-sm font-medium" href="#">
<span class="material-symbols-outlined text-yellow-600 dark:text-yellow-500" data-icon="assignment_return">assignment_return</span>
                Return Policy
            </a>
<a class="flex items-center gap-3 text-zinc-500 dark:text-zinc-400 py-2 px-3 hover:bg-zinc-200 dark:hover:bg-zinc-800 transition-all duration-300 font-['Plus_Jakarta_Sans'] text-sm font-medium" href="{{ route('terms') }}">
<span class="material-symbols-outlined text-yellow-600 dark:text-yellow-500" data-icon="description">description</span>
                Terms of Service
            </a>
<a class="flex items-center gap-3 text-zinc-500 dark:text-zinc-400 py-2 px-3 hover:bg-zinc-200 dark:hover:bg-zinc-800 transition-all duration-300 font-['Plus_Jakarta_Sans'] text-sm font-medium" href="{{ route('cookie-policy') }}">
<span class="material-symbols-outlined text-yellow-600 dark:text-yellow-500" data-icon="cookie">cookie</span>
                Cookie Policy
            </a>
</aside>
<!-- Content Area -->
<section class="flex-1 space-y-12">
<!-- Header Section -->
<div class="relative overflow-hidden rounded-xl bg-surface-container-low p-8 md:p-12 flex flex-col md:flex-row items-center gap-8">
<div class="z-10 flex-1">
<span class="inline-block px-3 py-1 bg-primary-container text-on-primary-container text-[10px] font-bold uppercase tracking-widest mb-4 rounded-sm">Cam Kết Hài Lòng</span>
<h1 class="text-4xl md:text-5xl font-extrabold font-headline text-on-surface tracking-tight leading-tight mb-4">Chính Sách Đổi Trả Linh Hoạt</h1>
<p class="text-on-surface-variant max-w-xl text-lg font-light leading-relaxed">Tại Bee Phone, chúng tôi coi trọng sự tin tưởng của bạn. Nếu sản phẩm không đạt kỳ vọng, quy trình đổi trả của chúng tôi luôn minh bạch và nhanh chóng.</p>
</div>
<div class="w-full md:w-1/3 aspect-square relative z-10">
<img alt="Smartphone service" class="object-cover w-full h-full rounded-xl shadow-lg" data-alt="Modern smartphone resting on a minimalist concrete surface with soft ambient yellow lighting and a luxury retail aesthetic" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAqDQqR6hs2W-HW179qtEKm-dxyDsHDjC7aX6l48IK9ZNNah0L3q2Z-qcwxusO6nmaNdPQ4hJCrQGNXrG94LiaT3vW5TvcXLvASekz1zCvG4CNuBt76knplslOY7WGv5JJJ-Mk3_I6BW1kwKBZ-PBi_HKJisrKXBXEQWlaeNj0pi45xL6qtMZMPcPfU__0MRFHzvB5iGrQcLeqyaUW0GD-7u89BM7-l-wXLkuZrZAq_rLDBuj0_j6-Vdbe7-m_UvsF7traIw4548QY"/>
</div>
<!-- Asymmetric background accent -->
<div class="absolute -right-20 -bottom-20 w-80 h-80 bg-primary-container/10 rounded-full blur-3xl"></div>
</div>
<!-- Policy Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
<!-- Condition Card -->
<div class="bg-surface-container-lowest p-8 rounded-xl border border-outline-variant/15 flex flex-col gap-6 hover:scale-[1.02] transition-transform duration-300">
<div class="w-12 h-12 bg-primary-container/20 rounded-lg flex items-center justify-center text-primary">
<span class="material-symbols-outlined" data-icon="calendar_today">calendar_today</span>
</div>
<div>
<h2 class="text-xl font-bold font-headline mb-3">Thời Hạn 30 Ngày</h2>
<p class="text-on-surface-variant leading-relaxed">Quý khách có quyền đổi sản phẩm mới hoặc trả lại lấy tiền trong vòng 30 ngày kể từ ngày nhận hàng nếu sản phẩm gặp lỗi kỹ thuật.</p>
</div>
</div>
<!-- Warranty Card -->
<div class="bg-surface-container-lowest p-8 rounded-xl border border-outline-variant/15 flex flex-col gap-6 hover:scale-[1.02] transition-transform duration-300">
<div class="w-12 h-12 bg-primary-container/20 rounded-lg flex items-center justify-center text-primary">
<span class="material-symbols-outlined" data-icon="verified">verified</span>
</div>
<div>
<h2 class="text-xl font-bold font-headline mb-3">Lỗi Nhà Sản Xuất</h2>
<p class="text-on-surface-variant leading-relaxed">Hỗ trợ 1 đổi 1 ngay lập tức cho các lỗi phần cứng như màn hình, pin, bo mạch chủ được xác nhận bởi đội ngũ kỹ thuật chuyên nghiệp.</p>
</div>
</div>
</div>
<!-- Step by Step Process (Bento Style) -->
<div class="space-y-6">
<h2 class="text-2xl font-bold font-headline px-2">Quy Trình Thực Hiện</h2>
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
<!-- Step 1 -->
<div class="md:col-span-1 bg-surface-container-high p-6 rounded-xl space-y-4">
<span class="text-primary font-black text-4xl opacity-20">01</span>
<h3 class="font-bold text-lg">Liên Hệ Hotline</h3>
<p class="text-sm text-on-surface-variant">Gọi ngay 1800-BEE-PHONE để thông báo tình trạng máy và nhận hướng dẫn sơ bộ.</p>
</div>
<!-- Step 2 -->
<div class="md:col-span-1 bg-surface-container-low p-6 rounded-xl space-y-4 border border-primary/10">
<span class="text-primary font-black text-4xl opacity-20">02</span>
<h3 class="font-bold text-lg">Đến Cửa Hàng</h3>
<p class="text-sm text-on-surface-variant">Mang theo máy, hộp và đầy đủ phụ kiện đến chi nhánh Bee Phone gần nhất để giám định.</p>
</div>
<!-- Step 3 -->
<div class="md:col-span-1 bg-primary-container p-6 rounded-xl space-y-4">
<span class="text-on-primary-container font-black text-4xl opacity-20">03</span>
<h3 class="font-bold text-lg text-on-primary-container">Hoàn Tiền Vào Ví</h3>
<p class="text-sm text-on-primary-container/80">Giá trị hoàn lại sẽ được cộng trực tiếp vào Ví Bee Pay trong vòng 24h sau khi xác nhận.</p>
</div>
</div>
</div>
<!-- Spec-Sheet Table (No-Line Style) -->
<div class="bg-surface-container-lowest rounded-xl overflow-hidden">
<div class="p-6 bg-surface-container-high">
<h2 class="font-bold font-headline">Yêu Cầu Về Tình Trạng Sản Phẩm</h2>
</div>
<div class="divide-y divide-transparent">
<div class="flex items-center justify-between p-4 bg-surface px-8">
<span class="text-sm font-medium">Ngoại hình máy</span>
<span class="text-sm text-on-surface-variant">Không trầy xước, móp méo, còn nguyên seal nếu chưa sử dụng</span>
</div>
<div class="flex items-center justify-between p-4 bg-surface-container-low px-8">
<span class="text-sm font-medium">Hộp và Phụ kiện</span>
<span class="text-sm text-on-surface-variant">Đầy đủ hộp, sách hướng dẫn, cáp sạc và quà tặng kèm</span>
</div>
<div class="flex items-center justify-between p-4 bg-surface px-8">
<span class="text-sm font-medium">Chứng từ</span>
<span class="text-sm text-on-surface-variant">Hóa đơn mua hàng hoặc email xác nhận đơn hàng</span>
</div>
<div class="flex items-center justify-between p-4 bg-surface-container-low px-8">
<span class="text-sm font-medium">Tài khoản</span>
<span class="text-sm text-on-surface-variant">Đã thoát các tài khoản iCloud, Google và mật khẩu màn hình</span>
</div>
</div>
</div>
</section>
</main>
@endsection