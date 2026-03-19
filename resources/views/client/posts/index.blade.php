@extends('client.layouts.app')
@section('content')
    <!DOCTYPE html>

    <html class="light" lang="vi">

    <head>
        <meta charset="utf-8" />
        <meta content="width=device-width, initial-scale=1.0" name="viewport" />
        <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
        <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&amp;display=swap"
            rel="stylesheet" />
        <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght@100..700,0..1&amp;display=swap"
            rel="stylesheet" />
        <link
            href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
            rel="stylesheet" />
        <script id="tailwind-config">
            tailwind.config = {
                darkMode: "class",
                theme: {
                    extend: {
                        colors: {
                            "primary": "#f4c025",
                            "background-light": "#f8f8f5",
                            "background-dark": "#221e10",
                        },
                        fontFamily: {
                            "display": ["Space Grotesk", "sans-serif"]
                        },
                        borderRadius: {
                            "DEFAULT": "0.25rem",
                            "lg": "0.5rem",
                            "xl": "0.75rem",
                            "full": "9999px"
                        },
                    },
                },
            }
        </script>
        <style>
            body {
                font-family: 'Space Grotesk', sans-serif;
            }

            .material-symbols-outlined {
                font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            }
        </style>
    </head>

    <body class="bg-background-light dark:bg-background-dark text-[#181611] dark:text-white transition-colors duration-200">
        <!-- Navigation Bar -->

        <main class="max-w-[1280px] mx-auto px-4 lg:px-10 py-8">
            <!-- Page Heading -->
            <div class="mb-8">
                <h1 class="text-4xl md:text-5xl font-black tracking-tighter mb-4 dark:text-white">Tin tức &amp; Cẩm nang Bee
                    Phone</h1>
                <p class="text-[#8a8060] text-lg max-w-2xl">Cập nhật xu hướng công nghệ, đánh giá chuyên sâu và mẹo vặt sử
                    dụng điện thoại mới nhất từ đội ngũ chuyên gia.</p>
            </div>
            <!-- Chips / Category Filter -->
            <div class="flex gap-3 overflow-x-auto pb-4 mb-8 no-scrollbar">
                <button class="bg-primary text-[#181611] px-6 py-2 rounded-full text-sm font-bold whitespace-nowrap">Tất
                    cả</button>
                <button
                    class="bg-white dark:bg-[#2d291e] border border-[#f5f3f0] dark:border-[#3d3829] px-6 py-2 rounded-full text-sm font-medium whitespace-nowrap hover:border-primary transition-colors">Đánh
                    giá</button>
                <button
                    class="bg-white dark:bg-[#2d291e] border border-[#f5f3f0] dark:border-[#3d3829] px-6 py-2 rounded-full text-sm font-medium whitespace-nowrap hover:border-primary transition-colors">Mẹo
                    hay</button>
                <button
                    class="bg-white dark:bg-[#2d291e] border border-[#f5f3f0] dark:border-[#3d3829] px-6 py-2 rounded-full text-sm font-medium whitespace-nowrap hover:border-primary transition-colors">Cẩm
                    nang mua sắm</button>
                <button
                    class="bg-white dark:bg-[#2d291e] border border-[#f5f3f0] dark:border-[#3d3829] px-6 py-2 rounded-full text-sm font-medium whitespace-nowrap hover:border-primary transition-colors">Tin
                    công nghệ</button>
            </div>
            <!-- Featured Post -->
            <section class="mb-12 @container">
                <div
                    class="bg-white dark:bg-[#1a170d] rounded-xl overflow-hidden shadow-sm flex flex-col @3xl:flex-row group cursor-pointer border border-[#f5f3f0] dark:border-[#332e1c]">
                    <div class="w-full @3xl:w-3/5 h-[300px] @3xl:h-auto overflow-hidden">
                        <div class="w-full h-full bg-center bg-no-repeat bg-cover transition-transform duration-500 group-hover:scale-105"
                            data-alt="Professional review of iPhone 15 Pro Max on a sleek desk"
                            style="background-image: url('{{ asset('uploads/posts/' . $featuredPost->thumbnail) }}')">
                        </div>
                    </div>
                    <div class="p-8 @3xl:w-2/5 flex flex-col justify-center">
                        <span class="text-primary font-bold text-xs tracking-widest uppercase mb-3 block">Bài viết nổi
                            bật</span>
                        <h2
                            class="text-2xl @3xl:text-3xl font-bold leading-tight mb-4 group-hover:text-primary transition-colors">
                            {{ $featuredPost->title }}</h2>
                        <p class="text-[#8a8060] mb-6 line-clamp-3">
                            {{ Str::limit(strip_tags($featuredPost->content), 150) }}</p>
                        <div
                            class="flex items-center justify-between mt-auto pt-6 border-t border-[#f5f3f0] dark:border-[#332e1c]">
                            <div class="flex items-center gap-4 text-xs text-[#8a8060]">
                                <span class="flex items-center gap-1"><span
                                        class="material-symbols-outlined text-[16px]">calendar_today</span>
                                    {{ $featuredPost->created_at->format('d/m/Y') }}</span>
                                <span class="flex items-center gap-1"><span
                                        class="material-symbols-outlined text-[16px]">chat_bubble</span> 15 bình luận</span>
                                <span class="flex items-center gap-1"><span
                                        class="material-symbols-outlined text-[16px]">visibility</span>{{ $featuredPost->views }}
                                </span>
                            </div>
                            <button
                                class="bg-primary hover:bg-opacity-90 text-[#181611] font-bold py-2 px-6 rounded-lg text-sm transition-all">Đọc
                                bài viết</button>
                        </div>
                    </div>
                </div>
            </section>
            {{-- @if ($featuredPost)
                <div class="bg-white ... group">
                    <a href="{{ route('client.posts.show', $featuredPost->slug) }}" class="flex flex-col @3xl:flex-row">

                        <div class="w-full @3xl:w-3/5 h-[300px] overflow-hidden">
                            <div class="w-full h-full bg-center bg-cover"
                                style="background-image: url('{{ asset('uploads/posts/' . $featuredPost->thumbnail) }}')">
                            </div>
                        </div>

                        <div class="p-8 @3xl:w-2/5 flex flex-col">
                            <span class="text-primary text-xs font-bold uppercase mb-3">Bài viết nổi bật</span>

                            <h2 class="text-2xl font-bold mb-4">
                                {{ $featuredPost->title }}
                            </h2>

                            <p class="text-[#8a8060] mb-6">
                                {{ Str::limit(strip_tags($featuredPost->content), 150) }}
                            </p>

                            <div class="flex justify-between text-xs text-[#8a8060] mt-auto">
                                <span>{{ $featuredPost->created_at->format('d/m/Y') }}</span>
                                <span>{{ $featuredPost->views }} lượt xem</span>
                            </div>
                        </div>

                    </a>
                </div>
            @endif --}}
            <!-- Main Content Area with Sidebar -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                <!-- Article Grid (8 columns) -->
                <div class="lg:col-span-8">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-2xl font-bold tracking-tight">Bài viết mới nhất</h3>
                        <div class="h-px flex-grow mx-4 bg-[#f5f3f0] dark:bg-[#332e1c]"></div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Card 1 -->
                        <div
                            class="bg-white dark:bg-[#1a170d] rounded-xl overflow-hidden border border-[#f5f3f0] dark:border-[#332e1c] group">
                            <div class="h-48 overflow-hidden relative">
                                <div class="w-full h-full bg-center bg-cover transition-transform group-hover:scale-105"
                                    data-alt="Modern smartphone showing various mobile apps"
                                    style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuDmSuQdMGUu-v3DlW_Lg0S9Fewjd0eQxDx9hx5QJWdfl-65kAy-4AzoWltU7SE_L7P9UbMDzDAk8vtH3Ui5ZQY4bGlmDRmqFwGreyszGH-iV-YnM42Tt2QAzy1qTLqcyk6YN7-VQmFTSfa_3zzp1h4Uh4rVTsQ01FXHw8Zb0u9g1gW0xdQ9FYk3HBDtVSYQAYt1yLz-HAoCfRLuEv3ll3ajSXSVBiulA-KbcIUxSVjSOUJKdswt7rhjh0iLNWR4zxBjSdEFDZL0dy4");'>
                                </div>
                                <span
                                    class="absolute top-3 left-3 bg-primary text-[#181611] text-[10px] font-black uppercase px-2 py-1 rounded">Mẹo
                                    hay</span>
                            </div>
                            <div class="p-5">
                                <h4 class="text-lg font-bold mb-3 group-hover:text-primary transition-colors">10 mẹo tối ưu
                                    hóa pin cực hay cho Android và iOS</h4>
                                <p class="text-[#8a8060] text-sm line-clamp-2 mb-4">Làm sao để pin điện thoại dùng được lâu
                                    hơn? Khám phá ngay những thiết lập ẩn giúp kéo dài tuổi thọ pin...</p>
                                <div
                                    class="flex items-center justify-between pt-4 border-t border-[#f5f3f0] dark:border-[#332e1c]">
                                    <span class="text-xs text-[#8a8060]">8 bình luận</span>
                                    <a class="text-primary text-sm font-bold flex items-center gap-1 hover:gap-2 transition-all"
                                        href="#">Đọc tiếp <span
                                            class="material-symbols-outlined text-[18px]">arrow_forward</span></a>
                                </div>
                            </div>
                        </div>
                        <!-- Card 2 -->
                        <div
                            class="bg-white dark:bg-[#1a170d] rounded-xl overflow-hidden border border-[#f5f3f0] dark:border-[#332e1c] group">
                            <div class="h-48 overflow-hidden relative">
                                <div class="w-full h-full bg-center bg-cover transition-transform group-hover:scale-105"
                                    data-alt="Person holding a new smartphone in a tech store"
                                    style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuDnP0tHgAkQcCnpww9JB4tpNHQrPKnD_ZEZOtTcJQU9tXsy-O3tUPblVCnt_Vt4T9zI6Mmnbpa8UTFcoPpjJpiKiaAPbKcdBfZVHIGOTz6jyumJ_QmlIgt2Gzu11CuSctrj2T0inD2J0_EZbqxmmt6TCgEb9eygiPGlzjWf89MwQwSQ6wnS5OcCEnhLep1BTSH04HELb-U-36yQLzwD5lsvfbZiJj1nwvPimjGHbQBnr9i4V3RpnFO3rVyqGyWBYRJvxjYbkDiGGE0");'>
                                </div>
                                <span
                                    class="absolute top-3 left-3 bg-primary text-[#181611] text-[10px] font-black uppercase px-2 py-1 rounded">Cẩm
                                    nang</span>
                            </div>
                            <div class="p-5">
                                <h4 class="text-lg font-bold mb-3 group-hover:text-primary transition-colors">Nên mua Galaxy
                                    S24 Ultra hay đợi iPhone 16?</h4>
                                <p class="text-[#8a8060] text-sm line-clamp-2 mb-4">So sánh chi tiết thông số và trải nghiệm
                                    người dùng giữa hai siêu phẩm hàng đầu thế giới hiện nay.</p>
                                <div
                                    class="flex items-center justify-between pt-4 border-t border-[#f5f3f0] dark:border-[#332e1c]">
                                    <span class="text-xs text-[#8a8060]">24 bình luận</span>
                                    <a class="text-primary text-sm font-bold flex items-center gap-1 hover:gap-2 transition-all"
                                        href="#">Đọc tiếp <span
                                            class="material-symbols-outlined text-[18px]">arrow_forward</span></a>
                                </div>
                            </div>
                        </div>
                        <!-- Card 3 -->
                        <div
                            class="bg-white dark:bg-[#1a170d] rounded-xl overflow-hidden border border-[#f5f3f0] dark:border-[#332e1c] group">
                            <div class="h-48 overflow-hidden relative">
                                <div class="w-full h-full bg-center bg-cover transition-transform group-hover:scale-105"
                                    data-alt="Close up of a professional mobile camera lens"
                                    style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuANc8HbUZv_B8cO62tF106GnOOkiFqZwGTu43mwt8yywexg3WY4LgwoRl05k6cYom--iOA9eUpoGelkvv3X1Q3LTLty-uMuWdqZxkPQ6evVZ6uEn26940yUcd7tl_pMdaziYAXZeJEBI_DDl0_yYeGb_0S0FzwaKmlG4Q6wGpqbmKCxeDa5TVWrvdDB_yDi54cZpyYZppklFdY-_zWsJyIAKfFK670AHOOrUX8KGnja1ky7Sh1Yq_O2SLQzblIlXofATQ_mREXhY3o");'>
                                </div>
                                <span
                                    class="absolute top-3 left-3 bg-primary text-[#181611] text-[10px] font-black uppercase px-2 py-1 rounded">Tin
                                    công nghệ</span>
                            </div>
                            <div class="p-5">
                                <h4 class="text-lg font-bold mb-3 group-hover:text-primary transition-colors">Sony ra mắt
                                    cảm biến máy ảnh mới cho di động</h4>
                                <p class="text-[#8a8060] text-sm line-clamp-2 mb-4">Cuộc cách mạng nhiếp ảnh di động sắp
                                    bắt đầu với cảm biến 1-inch thế hệ mới từ Sony.</p>
                                <div
                                    class="flex items-center justify-between pt-4 border-t border-[#f5f3f0] dark:border-[#332e1c]">
                                    <span class="text-xs text-[#8a8060]">12 bình luận</span>
                                    <a class="text-primary text-sm font-bold flex items-center gap-1 hover:gap-2 transition-all"
                                        href="#">Đọc tiếp <span
                                            class="material-symbols-outlined text-[18px]">arrow_forward</span></a>
                                </div>
                            </div>
                        </div>
                        <!-- Card 4 -->
                        <div
                            class="bg-white dark:bg-[#1a170d] rounded-xl overflow-hidden border border-[#f5f3f0] dark:border-[#332e1c] group">
                            <div class="h-48 overflow-hidden relative">
                                <div class="w-full h-full bg-center bg-cover transition-transform group-hover:scale-105"
                                    data-alt="Selection of wireless earphones and accessories"
                                    style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuDklUHFXAH-6ivpTlM6bJ5JNEbXJeqelfKPCt8w0poyJVzpT01N-KolzgjnvD-d8afYUXrmQJ-hR5WwDYN43Xa7E5YhFMt7-urQ8Xk-ANRmVROJYCt7NHiRtQVG3lW5kMLjn73qk19YQkU5SGBD6UxH9-TPCfd1iIuK2HsLs_TgC-3YrZl-xCLyBBMZcS7eUd9AZYTlftSxfT6HXqMGrFEifp3lgZswM98kbmL-qP8Thb1UStHLcHzBHlKQbsnIaHql9WMGy6qjVl4");'>
                                </div>
                                <span
                                    class="absolute top-3 left-3 bg-primary text-[#181611] text-[10px] font-black uppercase px-2 py-1 rounded">Đánh
                                    giá</span>
                            </div>
                            <div class="p-5">
                                <h4 class="text-lg font-bold mb-3 group-hover:text-primary transition-colors">Top 5 tai
                                    nghe Bluetooth giá rẻ đáng mua nhất</h4>
                                <p class="text-[#8a8060] text-sm line-clamp-2 mb-4">Không cần chi quá nhiều tiền vẫn có thể
                                    sở hữu tai nghe chống ồn chủ động cực xịn.</p>
                                <div
                                    class="flex items-center justify-between pt-4 border-t border-[#f5f3f0] dark:border-[#332e1c]">
                                    <span class="text-xs text-[#8a8060]">45 bình luận</span>
                                    <a class="text-primary text-sm font-bold flex items-center gap-1 hover:gap-2 transition-all"
                                        href="#">Đọc tiếp <span
                                            class="material-symbols-outlined text-[18px]">arrow_forward</span></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Pagination -->
                    <div class="mt-12 flex justify-center gap-2">
                        <button
                            class="size-10 flex items-center justify-center rounded-lg bg-primary text-[#181611] font-bold">1</button>
                        <button
                            class="size-10 flex items-center justify-center rounded-lg bg-white dark:bg-[#2d291e] hover:bg-primary/20 transition-colors">2</button>
                        <button
                            class="size-10 flex items-center justify-center rounded-lg bg-white dark:bg-[#2d291e] hover:bg-primary/20 transition-colors">3</button>
                        <span class="size-10 flex items-center justify-center">...</span>
                        <button
                            class="size-10 flex items-center justify-center rounded-lg bg-white dark:bg-[#2d291e] hover:bg-primary/20 transition-colors">
                            <span class="material-symbols-outlined">chevron_right</span>
                        </button>
                    </div>
                </div>
                <!-- Sidebar (4 columns) -->
                <aside class="lg:col-span-4 space-y-8">
                    <!-- Most Viewed Section -->
                    <div class="bg-white dark:bg-[#1a170d] p-6 rounded-xl border border-[#f5f3f0] dark:border-[#332e1c]">
                        <h4 class="text-xl font-bold mb-6 border-l-4 border-primary pl-3">Xem nhiều nhất</h4>
                        <div class="space-y-6">
                            <!-- Top item 1 -->
                            <div class="flex gap-4 group cursor-pointer">
                                <div class="size-20 shrink-0 rounded-lg bg-center bg-cover"
                                    data-alt="Close-up of a vibrant mobile screen"
                                    style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuAuKxCByBOIiqH0_sWB1SCJDNu99T827Cc6K77aw_aPELYB7m-uu6rSYWsFWdepL9mVsa1OxIpppwWyeB6xjR6t3IGcX5Kn8UISfKN2Ev2z7mhBQd8UGxeDm70g1ixFtu2vmWcHK4nZBjqJ9PiQU5sfQsGOouHsZvWFg99czDPUaMAWJhz-kpuPPY3Ww1yHjwPbypnynnYoeedX75bWVZU1DR7DunTigZYJPQia2MEQ5t00Lil7xvgHkQojbTWRpnHHucgNx6Qaqgs");'>
                                </div>
                                <div class="flex flex-col justify-center">
                                    <h5 class="text-sm font-bold group-hover:text-primary transition-colors line-clamp-2">
                                        Cách bảo mật thông tin cá nhân trên smartphone cực kỳ quan trọng</h5>
                                    <span class="text-[10px] text-[#8a8060] mt-1 uppercase tracking-wider">Mẹo hay • 1.2k
                                        lượt xem</span>
                                </div>
                            </div>
                            <!-- Top item 2 -->
                            <div class="flex gap-4 group cursor-pointer">
                                <div class="size-20 shrink-0 rounded-lg bg-center bg-cover"
                                    data-alt="New iPhone model in various colors"
                                    style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuCqsWvZFd0-ecMO3MFejIPH8k2ILu7-RKDdDa98y7SQ6zL_qIBG31JYGB9zV2zPDifP6gkE6HlETlUjojigiQIGonkf05PdFCg6uZosO0kWkixMi6i6Qz03uVRuC0-zv7hrfafgxobi9bnXZI6S5vwRfYJghQl9LdP_V6WFxSbyYIxwNfVuxQVTZLrzYQcIhoEi45-K14RUE4V4SsLC9XyjKgFL_KjPo55PuLCoETja4zRH1HMgMJugUgduL8L2eF4sBa3aIbFx13s");'>
                                </div>
                                <div class="flex flex-col justify-center">
                                    <h5 class="text-sm font-bold group-hover:text-primary transition-colors line-clamp-2">
                                        Tổng hợp bảng giá iPhone cũ mới nhất tháng 5/2024</h5>
                                    <span class="text-[10px] text-[#8a8060] mt-1 uppercase tracking-wider">Cẩm nang • 900
                                        lượt xem</span>
                                </div>
                            </div>
                            <!-- Top item 3 -->
                            <div class="flex gap-4 group cursor-pointer">
                                <div class="size-20 shrink-0 rounded-lg bg-center bg-cover"
                                    data-alt="Digital watch and smart devices"
                                    style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuB1dAjOvRscyNZIyZxWWd118zWQ5fZ478L-Kt_Q1w5W_asBqSYOxs8ffy6ET-qMroK7ch2gHmG9DVej9ipzYKgSx3CxaW363Vza82058qrtiTKAh2t87Qi4wyqu1BUWkrWh66Mnj2pgkxGyQXfYpkEheHp34q3-pvRdmQV6qyaK7TbcDPscrcPXJDhC7x699fu718TExKK4k1RQaNXp3TnvLnGqHqIvufYxctLYDXcR0L9jW-h5t0iGPJQgDGuZ7xJwFf051LIatRM");'>
                                </div>
                                <div class="flex flex-col justify-center">
                                    <h5 class="text-sm font-bold group-hover:text-primary transition-colors line-clamp-2">
                                        Smartwatch nào pin trâu nhất hiện nay? Đánh giá thực tế</h5>
                                    <span class="text-[10px] text-[#8a8060] mt-1 uppercase tracking-wider">Đánh giá • 850
                                        lượt xem</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Newsletter Signup -->
                    <div class="bg-primary p-8 rounded-xl relative overflow-hidden">
                        <div class="relative z-10">
                            <h4 class="text-2xl font-black text-[#181611] mb-2">Đăng ký nhận tin</h4>
                            <p class="text-[#181611]/80 text-sm mb-6 font-medium">Nhận những tin tức công nghệ mới nhất và
                                ưu đãi đặc quyền từ Bee Phone.</p>
                            <form class="space-y-3">
                                <input
                                    class="w-full px-4 py-3 rounded-lg border-none focus:ring-2 focus:ring-[#181611] text-sm bg-white/90"
                                    placeholder="Địa chỉ email của bạn" type="email" />
                                <button
                                    class="w-full bg-[#181611] text-white py-3 rounded-lg font-bold text-sm hover:bg-[#181611]/90 transition-all">ĐĂNG
                                    KÝ NGAY</button>
                            </form>
                        </div>
                        <!-- Decorative Icon -->
                        <span
                            class="material-symbols-outlined absolute -bottom-6 -right-6 text-black/10 text-[120px] pointer-events-none">mail</span>
                    </div>
                    <!-- Ad Banner or Promotion -->
                    <div class="bg-[#2d291e] p-6 rounded-xl text-white text-center">
                        <p class="text-primary text-xs font-bold tracking-[0.2em] mb-2">QUẢNG CÁO</p>
                        <h5 class="text-lg font-bold mb-4">Lên đời iPhone 15 Pro Max <br /> trợ giá tới 2.000.000đ</h5>
                        <button
                            class="border border-white/30 hover:bg-white hover:text-[#2d291e] transition-all px-6 py-2 rounded-lg text-sm font-bold">Xem
                            ngay</button>
                    </div>
                </aside>
            </div>
        </main>
        <!-- Footer Simple -->
        <footer class="bg-white dark:bg-[#1a170d] border-t border-[#f5f3f0] dark:border-[#332e1c] py-12 mt-12">
            <div class="max-w-[1280px] mx-auto px-4 lg:px-10 flex flex-col md:flex-row justify-between items-center gap-8">
                <div class="flex flex-col gap-2 items-center md:items-start">
                    <div class="flex items-center gap-3">
                        <div class="text-primary size-6">
                            <svg fill="none" viewbox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
                                <path clip-rule="evenodd"
                                    d="M39.475 21.6262C40.358 21.4363 40.6863 21.5589 40.7581 21.5934C40.7876 21.655 40.8547 21.857 40.8082 22.3336C40.7408 23.0255 40.4502 24.0046 39.8572 25.2301C38.6799 27.6631 36.5085 30.6631 33.5858 33.5858C30.6631 36.5085 27.6632 38.6799 25.2301 39.8572C24.0046 40.4502 23.0255 40.7407 22.3336 40.8082C21.8571 40.8547 21.6551 40.7875 21.5934 40.7581C21.5589 40.6863 21.4363 40.358 21.6262 39.475C21.8562 38.4054 22.4689 36.9657 23.5038 35.2817C24.7575 33.2417 26.5497 30.9744 28.7621 28.762C30.9744 26.5497 33.2417 24.7574 35.2817 23.5037C36.9657 22.4689 38.4054 21.8562 39.475 21.6262ZM4.41189 29.2403L18.7597 43.5881C19.8813 44.7097 21.4027 44.9179 22.7217 44.7893C24.0585 44.659 25.5148 44.1631 26.9723 43.4579C29.9052 42.0387 33.2618 39.5667 36.4142 36.4142C39.5667 33.2618 42.0387 29.9052 43.4579 26.9723C44.1631 25.5148 44.659 24.0585 44.7893 22.7217C44.9179 21.4027 44.7097 19.8813 43.5881 18.7597L29.2403 4.41187C27.8527 3.02428 25.8765 3.02573 24.2861 3.36776C22.6081 3.72863 20.7334 4.58419 18.8396 5.74801C16.4978 7.18716 13.9881 9.18353 11.5858 11.5858C9.18354 13.988 7.18717 16.4978 5.74802 18.8396C4.58421 20.7334 3.72865 22.6081 3.36778 24.2861C3.02574 25.8765 3.02429 27.8527 4.41189 29.2403Z"
                                    fill="currentColor" fill-rule="evenodd"></path>
                            </svg>
                        </div>
                        <span class="text-lg font-black tracking-tight">Bee Phone</span>
                    </div>
                    <p class="text-[#8a8060] text-sm">© 2024 Bee Phone. Tất cả quyền lợi được bảo lưu.</p>
                </div>
                <div class="flex gap-6">
                    <a class="text-[#8a8060] hover:text-primary transition-colors" href="#"><span
                            class="material-symbols-outlined">social_leaderboard</span></a>
                    <a class="text-[#8a8060] hover:text-primary transition-colors" href="#"><span
                            class="material-symbols-outlined">youtube_activity</span></a>
                    <a class="text-[#8a8060] hover:text-primary transition-colors" href="#"><span
                            class="material-symbols-outlined">alternate_email</span></a>
                </div>
            </div>
        </footer>
    </body>

    </html>
@endsection
