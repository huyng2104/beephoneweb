@extends('client.layouts.app')

@section('title', 'Chính sách Bảo mật | Bee Phone')

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
            vertical-align: middle;
        }
        body { font-family: 'Inter', sans-serif; }
        h1, h2, h3, .brand-font { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>

<!-- Main Content Canvas -->
<main class="pt-24 pb-12 min-h-screen max-w-screen-2xl mx-auto px-6 md:px-12">
<!-- Header Section -->
<header class="mb-12">
<span class="text-xs font-bold uppercase tracking-widest text-primary mb-2 block">Trung tâm Pháp lý</span>
<h1 class="text-4xl md:text-5xl font-extrabold tracking-tight text-on-surface mb-4">Quyền riêng tư &amp; Bảo mật</h1>
<p class="text-secondary max-w-2xl leading-relaxed">Tại Bee Phone, chúng tôi cam kết bảo vệ dữ liệu cá nhân của bạn với tiêu chuẩn bảo mật cao nhất của một cửa hàng công nghệ cao cấp.</p>
</header>
<div class="flex flex-col md:flex-row gap-12">
<!-- SideNavBar -->
<aside class="hidden md:flex flex-col w-64 gap-2 p-4 sticky top-24 h-fit bg-zinc-50 dark:bg-zinc-900 rounded-lg">
<div class="mb-6 px-3">
<h2 class="text-primary font-bold tracking-tight text-lg">Legal Center</h2>
<p class="text-xs text-zinc-500">Policy Navigation</p>
</div>
<nav class="flex flex-col gap-1">
<a class="flex items-center gap-3 text-yellow-700 dark:text-yellow-400 bg-yellow-50 dark:bg-yellow-950/30 rounded-md py-2 px-3 font-['Plus_Jakarta_Sans'] text-sm font-medium transition-all duration-300" href="#">
<span class="material-symbols-outlined text-lg" data-icon="security">security</span>
                        Privacy Policy
                    </a>
<a class="flex items-center gap-3 text-zinc-500 dark:text-zinc-400 py-2 px-3 font-['Plus_Jakarta_Sans'] text-sm font-medium hover:bg-zinc-200 dark:hover:bg-zinc-800 transition-all duration-300" href="{{ route('return-policy') }}">
<span class="material-symbols-outlined text-lg" data-icon="assignment_return">assignment_return</span>
                        Return Policy
                    </a>
<a class="flex items-center gap-3 text-zinc-500 dark:text-zinc-400 py-2 px-3 font-['Plus_Jakarta_Sans'] text-sm font-medium hover:bg-zinc-200 dark:hover:bg-zinc-800 transition-all duration-300" href="{{ route('terms') }}">
<span class="material-symbols-outlined text-lg" data-icon="description">description</span>
                        Terms of Service
                    </a>
<a class="flex items-center gap-3 text-zinc-500 dark:text-zinc-400 py-2 px-3 font-['Plus_Jakarta_Sans'] text-sm font-medium hover:bg-zinc-200 dark:hover:bg-zinc-800 transition-all duration-300" href="{{ route('cookie-policy') }}">
<span class="material-symbols-outlined text-lg" data-icon="cookie">cookie</span>
                        Cookie Policy
                    </a>
</nav>
</aside>
<!-- Policy Content -->
<article class="flex-1 space-y-16">
<!-- Section: Data Collection -->
<section class="bg-surface-container-lowest p-8 md:p-12 rounded-xl shadow-sm border border-outline-variant/10" id="data-collection">
<div class="flex items-center gap-4 mb-8">
<div class="w-12 h-12 rounded-lg bg-primary-container flex items-center justify-center">
<span class="material-symbols-outlined text-on-primary-container" data-icon="database">database</span>
</div>
<h2 class="text-2xl font-bold tracking-tight">1. Thu thập dữ liệu</h2>
</div>
<p class="text-on-surface-variant leading-relaxed mb-6">
                        Để cung cấp trải nghiệm mua sắm tốt nhất tại Bee Phone, chúng tôi thu thập một số thông tin cơ bản khi bạn tương tác với nền tảng của chúng tôi.
                    </p>
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
<div class="p-6 bg-surface-container-low rounded-lg">
<h3 class="font-bold mb-2 flex items-center gap-2">
<span class="material-symbols-outlined text-primary text-sm" data-icon="person">person</span>
                                Thông tin cá nhân
                            </h3>
<p class="text-sm text-secondary">Họ tên, địa chỉ email, số điện thoại và địa chỉ giao hàng khi bạn tạo tài khoản hoặc đặt hàng.</p>
</div>
<div class="p-6 bg-surface-container-low rounded-lg">
<h3 class="font-bold mb-2 flex items-center gap-2">
<span class="material-symbols-outlined text-primary text-sm" data-icon="devices">devices</span>
                                Thông tin thiết bị
                            </h3>
<p class="text-sm text-secondary">Địa chỉ IP, loại trình duyệt và lịch sử xem sản phẩm để cá nhân hóa gợi ý iPhone và Mac.</p>
</div>
</div>
</section>
<!-- Section: Usage Purpose -->
<section class="relative overflow-hidden bg-zinc-900 text-white p-8 md:p-12 rounded-xl" id="usage-purpose">
<div class="relative z-10">
<div class="flex items-center gap-4 mb-8">
<div class="w-12 h-12 rounded-lg bg-yellow-500 flex items-center justify-center">
<span class="material-symbols-outlined text-zinc-900" data-icon="ads_click">ads_click</span>
</div>
<h2 class="text-2xl font-bold tracking-tight text-white">2. Mục đích sử dụng</h2>
</div>
<div class="space-y-6 max-w-3xl">
<div class="flex gap-4">
<span class="text-yellow-500 font-bold">01</span>
<p class="text-zinc-300"><strong class="text-white">Xử lý đơn hàng:</strong> Đảm bảo các sản phẩm Bee Phone của bạn được vận chuyển chính xác và an toàn đến tay bạn.</p>
</div>
<div class="flex gap-4">
<span class="text-yellow-500 font-bold">02</span>
<p class="text-zinc-300"><strong class="text-white">Hỗ trợ khách hàng:</strong> Giải đáp thắc mắc về bảo hành, kỹ thuật và dịch vụ hậu mãi nhanh chóng nhất.</p>
</div>
<div class="flex gap-4">
<span class="text-yellow-500 font-bold">03</span>
<p class="text-zinc-300"><strong class="text-white">Nâng cấp trải nghiệm:</strong> Phân tích xu hướng mua sắm để tối ưu hóa giao diện và danh mục sản phẩm của cửa hàng.</p>
</div>
</div>
</div>
<!-- Decorative Background Image -->
<div class="absolute inset-0 opacity-20 pointer-events-none">
<img alt="Cybersecurity and data protection illustration" class="w-full h-full object-cover" data-alt="Dark abstract futuristic high-tech circuit background with glowing yellow lines and data patterns" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCSXQVeztXM6k1Orj4-B-o_7NIpB-2DcTDPHfNcL8gliZ-3TjMm9E6OvQpojwzUos7UjGC6M0Txq1klT2hoZfJqX5TLYqcqnfhgqFxp20QEBRbSHixaUAciJE42RGDBXAU1PRTswSyAlj4Et7ZZcALDbN4ckZk1ugAk2_mHukDlCLbgdOorHss5cdhZtOuy2l_IBc-bIz6CcHGMZ4CZK1ZnlKk5DDl7kx9MNOW3p7k0FFJVE9FvtUMHYn8yeQ9-sbV1PgPpOs8oxcw"/>
</div>
</section>
<!-- Section: Security Commitment -->
<section class="bg-surface-container-low p-8 md:p-12 rounded-xl border-l-4 border-primary" id="security">
<div class="flex items-center gap-4 mb-8">
<div class="w-12 h-12 rounded-lg bg-white shadow-sm flex items-center justify-center">
<span class="material-symbols-outlined text-primary" data-icon="verified_user">verified_user</span>
</div>
<h2 class="text-2xl font-bold tracking-tight">3. Cam kết bảo mật</h2>
</div>
<p class="text-on-surface-variant leading-relaxed mb-8">
                        Chúng tôi hiểu rằng sự tin tưởng là tài sản quý giá nhất. Bee Phone áp dụng các giao thức bảo mật khắt khe nhất để bảo vệ thông tin của bạn.
                    </p>
<div class="grid grid-cols-1 gap-4">
<div class="flex items-start gap-4 p-4 bg-white/50 rounded-lg">
<span class="material-symbols-outlined text-green-600 mt-1" data-icon="lock" data-weight="fill" style="font-variation-settings: 'FILL' 1;">lock</span>
<div>
<h4 class="font-semibold">Mã hóa đầu cuối (AES-256)</h4>
<p class="text-sm text-secondary">Mọi giao dịch thanh toán đều được mã hóa và xử lý qua các cổng thanh toán quốc tế uy tín.</p>
</div>
</div>
<div class="flex items-start gap-4 p-4 bg-white/50 rounded-lg">
<span class="material-symbols-outlined text-green-600 mt-1" data-icon="visibility_off" data-weight="fill" style="font-variation-settings: 'FILL' 1;">visibility_off</span>
<div>
<h4 class="font-semibold">Không chia sẻ bên thứ ba</h4>
<p class="text-sm text-secondary">Chúng tôi cam kết không bao giờ bán hoặc trao đổi dữ liệu cá nhân của bạn cho bất kỳ đơn vị quảng cáo nào.</p>
</div>
</div>
<div class="flex items-start gap-4 p-4 bg-white/50 rounded-lg">
<span class="material-symbols-outlined text-green-600 mt-1" data-icon="history" data-weight="fill" style="font-variation-settings: 'FILL' 1;">history</span>
<div>
<h4 class="font-semibold">Quyền kiểm soát của người dùng</h4>
<p class="text-sm text-secondary">Bạn có quyền yêu cầu trích xuất hoặc xóa bỏ vĩnh viễn dữ liệu cá nhân khỏi hệ thống của chúng tôi bất cứ lúc nào.</p>
</div>
</div>
</div>
</section>
<!-- Specialized Component: Spec-Sheet for Data Retention -->
<section class="mt-12">
<h3 class="text-xl font-bold mb-6 tracking-tight">Thời gian lưu trữ dữ liệu</h3>
<div class="rounded-lg overflow-hidden border border-outline-variant/20">
<div class="grid grid-cols-2 p-4 bg-zinc-900 text-white font-semibold">
<div>Loại dữ liệu</div>
<div>Thời gian lưu trữ</div>
</div>
<div class="grid grid-cols-2 p-4 bg-surface">
<div class="font-medium text-sm">Hồ sơ tài khoản</div>
<div class="text-sm text-secondary">Đến khi người dùng yêu cầu xóa</div>
</div>
<div class="grid grid-cols-2 p-4 bg-surface-container-low">
<div class="font-medium text-sm">Lịch sử giao dịch</div>
<div class="text-sm text-secondary">10 năm (theo luật kế toán)</div>
</div>
<div class="grid grid-cols-2 p-4 bg-surface">
<div class="font-medium text-sm">Cookies trình duyệt</div>
<div class="text-sm text-secondary">30 ngày</div>
</div>
<div class="grid grid-cols-2 p-4 bg-surface-container-low">
<div class="font-medium text-sm">Dữ liệu hỗ trợ khách hàng</div>
<div class="text-sm text-secondary">2 năm sau yêu cầu cuối cùng</div>
</div>
</div>
</section>
<!-- Help CTA Card -->
<section class="bg-primary-container p-8 rounded-xl flex flex-col md:flex-row items-center justify-between gap-6">
<div>
<h3 class="text-xl font-extrabold text-on-primary-container mb-2">Bạn còn thắc mắc về quyền riêng tư?</h3>
<p class="text-on-primary-fixed-variant">Đội ngũ pháp lý của chúng tôi luôn sẵn sàng hỗ trợ bạn 24/7.</p>
</div>
<button class="px-8 py-3 bg-zinc-900 text-white font-bold rounded-md hover:scale-105 transition-transform active:scale-95 shadow-lg">Liên hệ ngay</button>
</section>
</article>
</div>
</main>
@endsection