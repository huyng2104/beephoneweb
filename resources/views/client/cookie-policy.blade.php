@extends('client.layouts.app')

@section('title', 'Chính sách Cookie | Bee Phone')

@section('content')
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&amp;family=Inter:wght@300;400;500;600&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            display: inline-block;
            line-height: 1;
            text-transform: none;
            letter-spacing: normal;
            word-wrap: normal;
            white-space: nowrap;
            direction: ltr;
        }
        body {
            font-family: 'Inter', sans-serif;
            color: #1a1c1c;
            background-color: #f9f9f9;
        }
        h1, h2, h3, .font-headline {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .glass-nav {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(20px);
        }
    </style>
<script id="tailwind-config">
      tailwind.config = {
        darkMode: "class",
        theme: {
          extend: {
            colors: {
              "on-primary": "#ffffff",
              "on-tertiary-container": "#005e6c",
              "secondary-container": "#e2e2e2",
              "primary": "#785900",
              "surface-tint": "#785900",
              "surface-container-highest": "#e2e2e2",
              "outline-variant": "#d4c5ab",
              "surface-container-high": "#e8e8e8",
              "on-surface-variant": "#4f4632",
              "on-secondary-fixed-variant": "#474747",
              "tertiary-fixed": "#a5eeff",
              "on-secondary": "#ffffff",
              "surface": "#f9f9f9",
              "on-primary-fixed": "#261a00",
              "inverse-on-surface": "#f0f1f1",
              "secondary-fixed": "#e2e2e2",
              "secondary": "#5e5e5e",
              "on-tertiary-fixed": "#001f25",
              "on-error-container": "#93000a",
              "background": "#f9f9f9",
              "surface-container-low": "#f3f3f4",
              "outline": "#827660",
              "on-surface": "#1a1c1c",
              "surface-bright": "#f9f9f9",
              "surface-dim": "#dadada",
              "on-secondary-container": "#646464",
              "surface-container-lowest": "#ffffff",
              "tertiary-fixed-dim": "#00daf8",
              "on-primary-container": "#6d5100",
              "on-error": "#ffffff",
              "secondary-fixed-dim": "#c6c6c6",
              "error-container": "#ffdad6",
              "on-tertiary-fixed-variant": "#004e5a",
              "on-secondary-fixed": "#1b1b1b",
              "inverse-primary": "#fabd00",
              "on-tertiary": "#ffffff",
              "tertiary": "#006877",
              "primary-fixed-dim": "#fabd00",
              "inverse-surface": "#2f3131",
              "tertiary-container": "#00defd",
              "primary-container": "#ffc107",
              "error": "#ba1a1a",
              "primary-fixed": "#ffdf9e",
              "surface-variant": "#e2e2e2",
              "on-background": "#1a1c1c",
              "surface-container": "#eeeeee",
              "on-primary-fixed-variant": "#5b4300"
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

<main class="pt-24 pb-20 max-w-screen-2xl mx-auto px-6 md:px-12">
<div class="flex flex-col md:flex-row gap-12">
<!-- SideNavBar -->
<aside class="hidden md:flex flex-col w-64 gap-2 p-4 sticky top-24 h-fit bg-zinc-50 dark:bg-zinc-900 rounded-lg font-['Plus_Jakarta_Sans'] text-sm font-medium">
<div class="mb-6 px-3">
<h2 class="text-lg font-bold text-zinc-900 dark:text-white">Legal Center</h2>
<p class="text-xs text-zinc-500 uppercase tracking-wider mt-1">Policy Navigation</p>
</div>
<a class="flex items-center gap-3 text-zinc-500 dark:text-zinc-400 py-2 px-3 hover:bg-zinc-200 dark:hover:bg-zinc-800 transition-all duration-300" href="{{ route('privacy-policy') }}">
<span class="material-symbols-outlined text-lg" data-icon="security">security</span>
<span>Privacy Policy</span>
</a>
<a class="flex items-center gap-3 text-zinc-500 dark:text-zinc-400 py-2 px-3 hover:bg-zinc-200 dark:hover:bg-zinc-800 transition-all duration-300" href="{{ route('return-policy') }}">
<span class="material-symbols-outlined text-lg" data-icon="assignment_return">assignment_return</span>
<span>Return Policy</span>
</a>
<a class="flex items-center gap-3 text-zinc-500 dark:text-zinc-400 py-2 px-3 hover:bg-zinc-200 dark:hover:bg-zinc-800 transition-all duration-300" href="{{ route('terms') }}">
<span class="material-symbols-outlined text-lg" data-icon="description">description</span>
<span>Terms of Service</span>
</a>
<a class="flex items-center gap-3 text-yellow-700 dark:text-yellow-400 bg-yellow-50 dark:bg-yellow-950/30 rounded-md py-2 px-3 transition-all duration-300" href="#">
<span class="material-symbols-outlined text-lg" data-icon="cookie">cookie</span>
<span>Cookie Policy</span>
</a>
</aside>
<!-- Content Canvas -->
<div class="flex-1 space-y-12">
<!-- Header Section -->
<section class="space-y-4">
<span class="text-xs font-bold uppercase tracking-[0.2em] text-primary">Pháp lý &amp; Bảo mật</span>
<h1 class="text-5xl font-extrabold tracking-tight text-on-surface">Chính sách Cookie</h1>
<p class="text-lg text-secondary max-w-2xl leading-relaxed">
                        Tại Bee Phone, chúng tôi tin tưởng vào việc minh bạch về cách chúng tôi thu thập và sử dụng dữ liệu liên quan đến bạn. Trang này cung cấp thông tin chi tiết về cách thức và lý do chúng tôi sử dụng cookie.
                    </p>
</section>
<div class="w-full h-px bg-surface-container-high"></div>
<!-- What are cookies -->
<section class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
<div class="lg:col-span-4">
<h2 class="text-2xl font-bold tracking-tight">Cookie là gì?</h2>
</div>
<div class="lg:col-span-8 bg-surface-container-lowest p-8 rounded-xl space-y-4">
<p class="leading-relaxed text-secondary">
                            Cookie là các tệp văn bản nhỏ được gửi đến trình duyệt của bạn bởi một trang web bạn truy cập. Chúng giúp trang web ghi nhớ thông tin về lần truy cập của bạn, giúp việc truy cập lại trang web dễ dàng hơn và làm cho trang web hữu ích hơn cho bạn.
                        </p>
<div class="relative overflow-hidden rounded-lg aspect-video mt-6">
<img alt="Modern digital interface with abstract data nodes" class="object-cover w-full h-full" data-alt="abstract close-up of a high-tech circuit board with glowing golden light paths and soft bokeh background representing digital data" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBXdO1fNxnsNVFqb7RFqT3GvXAAYawsvp64tCJG4UOhL_p7V-5nKYkAa0SxF_dNaXQlMETlid_Wa12npMY9FFgOSy_lE2P9SzFq0AZvQlXIUV3ubrJQak_Q8hXpnSWvwdRa4hLacNDyZ1kPFpEab5kMV-_mlVoX5g7TV8k0MBPUyf225_RKhy5PFTs2ASaEkH8xIStTJ99g9Y5aZQV5o54d5XtHINekgW0WBO_is0nziOO_zVSQqiXNqEqI7qcR_8d4TBNIP-WIPbw"/>
</div>
</div>
</section>
<!-- How we use cookies -->
<section class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
<div class="lg:col-span-4">
<h2 class="text-2xl font-bold tracking-tight">Cách chúng tôi sử dụng</h2>
</div>
<div class="lg:col-span-8 space-y-6">
<p class="leading-relaxed text-secondary">
                            Chúng tôi sử dụng cookie cho nhiều mục đích khác nhau để nâng cao trải nghiệm mua sắm thiết bị công nghệ cao của bạn:
                        </p>
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
<div class="p-6 bg-surface-container-low rounded-lg">
<span class="material-symbols-outlined text-primary mb-3" data-icon="auto_awesome">auto_awesome</span>
<h3 class="font-bold mb-2">Cá nhân hóa</h3>
<p class="text-sm text-secondary">Ghi nhớ sở thích ngôn ngữ và các sản phẩm iPhone, Mac bạn đã xem gần đây.</p>
</div>
<div class="p-6 bg-surface-container-low rounded-lg">
<span class="material-symbols-outlined text-primary mb-3" data-icon="shopping_bag">shopping_bag</span>
<h3 class="font-bold mb-2">Giỏ hàng</h3>
<p class="text-sm text-secondary">Giữ các sản phẩm trong giỏ hàng của bạn khi bạn tiếp tục duyệt web.</p>
</div>
</div>
</div>
</section>
<!-- Types of cookies (Bento Style) -->
<section class="space-y-8">
<h2 class="text-2xl font-bold tracking-tight">Các loại cookie được sử dụng</h2>
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
<!-- Essential -->
<div class="md:col-span-2 bg-surface-container-lowest p-8 rounded-xl border border-outline-variant/10 shadow-sm">
<div class="flex items-center gap-3 mb-4">
<div class="w-10 h-10 rounded bg-primary/10 flex items-center justify-center">
<span class="material-symbols-outlined text-primary" data-icon="verified_user">verified_user</span>
</div>
<h3 class="text-xl font-bold">Cookie Thiết yếu</h3>
</div>
<p class="text-secondary leading-relaxed mb-6">Những cookie này cực kỳ quan trọng để bạn có thể di chuyển quanh trang web và sử dụng các tính năng của nó, chẳng hạn như truy cập vào các khu vực an toàn của trang web. Nếu không có những cookie này, các dịch vụ như giỏ hàng hoặc hóa đơn điện tử không thể được cung cấp.</p>
<div class="flex flex-wrap gap-2">
<span class="px-3 py-1 bg-surface-container-high rounded-full text-xs font-medium">Bảo mật</span>
<span class="px-3 py-1 bg-surface-container-high rounded-full text-xs font-medium">Xác thực</span>
<span class="px-3 py-1 bg-surface-container-high rounded-full text-xs font-medium">Phiên làm việc</span>
</div>
</div>
<!-- Analytics -->
<div class="bg-primary-container p-8 rounded-xl flex flex-col justify-between">
<div>
<span class="material-symbols-outlined text-on-primary-container text-4xl mb-4" data-icon="analytics">analytics</span>
<h3 class="text-xl font-bold text-on-primary-container mb-2">Phân tích</h3>
<p class="text-on-primary-container/80 text-sm leading-relaxed">Giúp chúng tôi hiểu cách khách hàng tương tác với Bee Phone bằng cách thu thập và báo cáo thông tin ẩn danh.</p>
</div>
<button class="mt-6 w-full py-3 bg-on-primary-container text-white rounded-md font-bold text-sm hover:opacity-90 transition-opacity">Tìm hiểu thêm</button>
</div>
<!-- Marketing -->
<div class="md:col-span-3 bg-surface-container-high p-8 rounded-xl flex flex-col md:flex-row gap-8 items-center">
<div class="flex-1">
<h3 class="text-xl font-bold mb-3">Cookie Tiếp thị</h3>
<p class="text-secondary text-sm">Chúng tôi sử dụng các cookie này để hiển thị các quảng cáo có liên quan hơn đến bạn và sở thích của bạn. Chúng cũng được sử dụng để giới hạn số lần bạn thấy một quảng cáo cũng như giúp đo lường hiệu quả của chiến dịch quảng cáo.</p>
</div>
<div class="flex gap-4">
<div class="w-16 h-16 rounded-full bg-white flex items-center justify-center shadow-inner">
<span class="material-symbols-outlined text-primary" data-icon="campaign">campaign</span>
</div>
<div class="w-16 h-16 rounded-full bg-white flex items-center justify-center shadow-inner">
<span class="material-symbols-outlined text-primary" data-icon="target">target</span>
</div>
</div>
</div>
</div>
</section>
<!-- Management -->
<section class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
<div class="lg:col-span-4">
<h2 class="text-2xl font-bold tracking-tight">Quản lý Cookie</h2>
</div>
<div class="lg:col-span-8 space-y-6">
<div class="bg-surface-container-lowest border border-outline-variant/20 rounded-xl overflow-hidden">
<table class="w-full text-left text-sm">
<thead class="bg-surface-container-low font-headline">
<tr>
<th class="p-4 font-bold uppercase tracking-wider">Trình duyệt</th>
<th class="p-4 font-bold uppercase tracking-wider">Cách điều chỉnh</th>
</tr>
</thead>
<tbody class="divide-y divide-surface-container-high">
<tr>
<td class="p-4 font-semibold">Safari</td>
<td class="p-4 text-secondary">Cài đặt &gt; Quyền riêng tư &gt; Chặn tất cả cookie</td>
</tr>
<tr>
<td class="p-4 font-semibold">Chrome</td>
<td class="p-4 text-secondary">Cài đặt &gt; Quyền riêng tư và bảo mật &gt; Cookie</td>
</tr>
<tr>
<td class="p-4 font-semibold">Firefox</td>
<td class="p-4 text-secondary">Tùy chọn &gt; Quyền riêng tư &amp; Bảo mật &gt; Cookie</td>
</tr>
</tbody>
</table>
</div>
<p class="text-sm text-secondary italic">Lưu ý: Việc vô hiệu hóa cookie có thể ảnh hưởng đến chức năng của trang web Bee Phone.</p>
</div>
</section>
</div>
</div>
</main>
@endsection