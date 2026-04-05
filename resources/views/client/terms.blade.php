@extends('client.layouts.app')

@section('title', 'Điều khoản Sử dụng | Bee Phone')

@section('content')
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&amp;family=Inter:wght@400;500;600&amp;display=swap" rel="stylesheet"/>
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
      body {
        font-family: 'Inter', sans-serif;
        background-color: #f9f9f9;
        color: #1a1c1c;
      }
      h1, h2, h3 {
        font-family: 'Plus Jakarta Sans', sans-serif;
      }
    </style>

<!-- Main Content Layout -->
<main class="pt-24 pb-20 px-6 md:px-12 max-w-screen-2xl mx-auto min-h-screen">
<div class="flex flex-col md:flex-row gap-12">
<!-- SideNavBar (Legal Center) -->
<aside class="hidden md:flex flex-col w-64 gap-2 p-4 sticky top-24 h-fit bg-zinc-50 dark:bg-zinc-900 rounded-lg">
<div class="mb-6 px-3">
<h3 class="text-zinc-900 dark:text-white font-bold text-lg leading-tight">Legal Center</h3>
<p class="text-zinc-500 text-xs mt-1">Policy Navigation</p>
</div>
<nav class="flex flex-col gap-1">
<a class="flex items-center gap-3 text-zinc-500 dark:text-zinc-400 py-2 px-3 hover:bg-zinc-200 dark:hover:bg-zinc-800 transition-all duration-300 font-['Plus_Jakarta_Sans'] text-sm font-medium" href="{{ route('privacy-policy') }}">
<span class="material-symbols-outlined text-lg" data-icon="security">security</span>
<span>Privacy Policy</span>
</a>
<a class="flex items-center gap-3 text-zinc-500 dark:text-zinc-400 py-2 px-3 hover:bg-zinc-200 dark:hover:bg-zinc-800 transition-all duration-300 font-['Plus_Jakarta_Sans'] text-sm font-medium" href="{{ route('return-policy') }}">
<span class="material-symbols-outlined text-lg" data-icon="assignment_return">assignment_return</span>
<span>Return Policy</span>
</a>
<a class="flex items-center gap-3 text-yellow-700 dark:text-yellow-400 bg-yellow-50 dark:bg-yellow-950/30 rounded-md py-2 px-3 transition-all duration-300 font-['Plus_Jakarta_Sans'] text-sm font-medium" href="#">
<span class="material-symbols-outlined text-lg" data-icon="description">description</span>
<span>Terms of Service</span>
</a>
<a class="flex items-center gap-3 text-zinc-500 dark:text-zinc-400 py-2 px-3 hover:bg-zinc-200 dark:hover:bg-zinc-800 transition-all duration-300 font-['Plus_Jakarta_Sans'] text-sm font-medium" href="{{ route('cookie-policy') }}">
<span class="material-symbols-outlined text-lg" data-icon="cookie">cookie</span>
<span>Cookie Policy</span>
</a>
</nav>
</aside>
<!-- Terms Content Canvas -->
<div class="flex-1 max-w-4xl">
<!-- Hero Header -->
<header class="mb-12">
<div class="inline-block px-3 py-1 bg-primary-container text-on-primary-container text-[10px] uppercase tracking-widest font-bold rounded mb-4">
                        Cập nhật lần cuối: 24/05/2024
                    </div>
<h1 class="text-5xl font-extrabold text-on-surface tracking-tighter mb-6 leading-none">
                        Điều khoản Sử dụng
                    </h1>
<p class="text-xl text-secondary leading-relaxed max-w-2xl">
                        Vui lòng đọc kỹ các điều khoản này trước khi sử dụng dịch vụ tại Bee Phone. Bằng việc truy cập, bạn đồng ý với các quy tắc của chúng tôi.
                    </p>
</header>
<!-- Terms Sections -->
<div class="space-y-16">
<!-- Section 1 -->
<section class="bg-surface-container-low p-8 rounded-xl">
<div class="flex items-start gap-6">
<span class="text-4xl font-black text-primary/20 select-none">01</span>
<div>
<h2 class="text-2xl font-bold text-on-surface mb-4">Quy định chung</h2>
<div class="space-y-4 text-on-surface-variant leading-relaxed">
<p>Trang web này được vận hành bởi Bee Phone. Xuyên suốt trang web, các thuật ngữ "chúng tôi", "của chúng tôi" đề cập đến Bee Phone Digital Boutique.</p>
<p>Bằng việc truy cập trang web hoặc mua sắm, bạn tham gia vào "Dịch vụ" và đồng ý chịu ràng buộc bởi các điều khoản và điều kiện sau đây bao gồm cả các điều khoản và chính sách bổ sung được dẫn chiếu ở đây.</p>
</div>
</div>
</div>
</section>
<!-- Section 2: Bento Style Grid for IP and Responsibilities -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
<div class="bg-surface-container-lowest p-8 rounded-xl border border-outline-variant/15 flex flex-col justify-between">
<div>
<div class="w-12 h-12 bg-primary-container rounded-lg flex items-center justify-center mb-6">
<span class="material-symbols-outlined text-on-primary-container" data-icon="copyright">copyright</span>
</div>
<h2 class="text-xl font-bold text-on-surface mb-4">Sở hữu Trí tuệ</h2>
<p class="text-on-surface-variant text-sm leading-relaxed mb-6">
                                    Tất cả nội dung bao gồm văn bản, đồ họa, logo, biểu tượng hình ảnh, đoạn âm thanh và phần mềm đều là tài sản của Bee Phone và được bảo vệ bởi luật bản quyền quốc tế.
                                </p>
</div>
<ul class="space-y-2 text-xs font-semibold text-primary uppercase tracking-wider">
<li class="flex items-center gap-2"><span class="w-1 h-1 bg-primary rounded-full"></span> Bản quyền thiết kế</li>
<li class="flex items-center gap-2"><span class="w-1 h-1 bg-primary rounded-full"></span> Thương hiệu Bee Phone</li>
</ul>
</div>
<div class="bg-surface-container-lowest p-8 rounded-xl border border-outline-variant/15 flex flex-col justify-between">
<div>
<div class="w-12 h-12 bg-secondary-container rounded-lg flex items-center justify-center mb-6">
<span class="material-symbols-outlined text-secondary" data-icon="person_check">person_check</span>
</div>
<h2 class="text-xl font-bold text-on-surface mb-4">Trách nhiệm Người dùng</h2>
<p class="text-on-surface-variant text-sm leading-relaxed mb-6">
                                    Bạn không được sử dụng sản phẩm của chúng tôi cho bất kỳ mục đích bất hợp pháp hoặc trái phép nào. Bạn cam kết cung cấp thông tin mua hàng và tài khoản hiện tại, đầy đủ và chính xác.
                                </p>
</div>
<ul class="space-y-2 text-xs font-semibold text-secondary uppercase tracking-wider">
<li class="flex items-center gap-2"><span class="w-1 h-1 bg-secondary rounded-full"></span> Bảo mật tài khoản</li>
<li class="flex items-center gap-2"><span class="w-1 h-1 bg-secondary rounded-full"></span> Tuân thủ pháp luật</li>
</ul>
</div>
</div>
<!-- Section 3: Spec-Sheet style for Store Obligations -->
<section class="mt-12">
<h2 class="text-2xl font-bold text-on-surface mb-8">Trách nhiệm của Cửa hàng</h2>
<div class="rounded-xl overflow-hidden border border-outline-variant/10">
<div class="bg-surface-container-low p-6 flex justify-between items-center">
<span class="font-bold text-sm uppercase tracking-widest text-secondary">Cam kết Dịch vụ</span>
<span class="font-bold text-sm uppercase tracking-widest text-secondary">Trạng thái</span>
</div>
<div class="bg-surface p-6 flex justify-between items-center">
<span class="text-on-surface-variant">Đảm bảo chất lượng sản phẩm chính hãng 100%</span>
<span class="material-symbols-outlined text-green-600" data-icon="check_circle" style="font-variation-settings: 'FILL' 1;">check_circle</span>
</div>
<div class="bg-surface-container-low p-6 flex justify-between items-center">
<span class="text-on-surface-variant">Bảo mật thông tin giao dịch của khách hàng</span>
<span class="material-symbols-outlined text-green-600" data-icon="check_circle" style="font-variation-settings: 'FILL' 1;">check_circle</span>
</div>
<div class="bg-surface p-6 flex justify-between items-center">
<span class="text-on-surface-variant">Hỗ trợ kỹ thuật và bảo hành theo đúng quy định</span>
<span class="material-symbols-outlined text-green-600" data-icon="check_circle" style="font-variation-settings: 'FILL' 1;">check_circle</span>
</div>
<div class="bg-surface-container-low p-6 flex justify-between items-center">
<span class="text-on-surface-variant">Thông báo trước các thay đổi về giá hoặc chính sách</span>
<span class="material-symbols-outlined text-green-600" data-icon="check_circle" style="font-variation-settings: 'FILL' 1;">check_circle</span>
</div>
</div>
</section>
<!-- Image Section -->
<div class="relative h-64 w-full rounded-2xl overflow-hidden mt-12">
<img alt="Premium Retail Space" class="w-full h-full object-cover" data-alt="Modern luxury electronics store interior with warm wood accents, minimalist glass displays, and soft ambient lighting in a boutique setting" src="https://lh3.googleusercontent.com/aida-public/AB6AXuC4kdPrHl1ArQUyzx_4Gt_8d3orwy9QSTAKOW_wBshjcDy3CxCNe-ehGfjo9XlgQhecIH9cRdEc38K3YQpT3IJJtOEz-BMez0D9Ya8Dqn9Ikv6Sj99wE_zc-Z7NZHtNmvNRbe7Rwb8Xq7IN3cD0Y2X4WtFH5rES69MopV0m1p0GnraUHjCZykdkRQSpq4odAuXZdGSjiSfEH83I1PV9VRCtIbO75uIQ8jy0LjWVyHHHQIzdBPeCxZgoZ4gfbe82pPTw9uZOVgMoMB4"/>
<div class="absolute inset-0 bg-gradient-to-r from-on-surface/60 to-transparent flex items-center p-12">
<h3 class="text-white text-3xl font-bold max-w-xs leading-tight">Trải nghiệm mua sắm đẳng cấp tại Bee Phone.</h3>
</div>
</div>
<!-- Final Clause -->
<footer class="mt-20 pt-12 border-t border-outline-variant/20">
<p class="text-secondary text-sm italic">
                            Nếu bạn có bất kỳ câu hỏi nào về Điều khoản Sử dụng, vui lòng liên hệ với chúng tôi qua email: <span class="text-primary font-bold">legal@beephone.com</span>
</p>
</footer>
</div>
</div>
</div>
</main>
@endsection