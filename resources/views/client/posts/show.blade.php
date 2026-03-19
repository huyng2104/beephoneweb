@extends('client.layouts.app')
@section('content')
    <!DOCTYPE html>

    <html class="light" lang="vi">

    <head>
        <meta charset="utf-8" />
        <meta content="width=device-width, initial-scale=1.0" name="viewport" />
        <title>Bee Phone - Chi tiết bài viết và Bình luận</title>
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
                font-family: "Space Grotesk", sans-serif;
            }

            .material-symbols-outlined {
                font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            }
        </style>
    </head>

    <body class="bg-background-light dark:bg-background-dark text-[#181611] dark:text-white transition-colors duration-200">
        <!-- TopNavBar -->

        <main class="mx-auto px-4 lg:px-40 py-8">
            <!-- Breadcrumbs -->

            <div class="flex flex-col lg:flex-row gap-12">
                <!-- Left Column: Content & Comments -->
                <div class="flex-1">
                    <!-- Article Header -->
                    <div class="mb-8">
                        <h1
                            class="text-[#181611] dark:text-white text-4xl lg:text-5xl font-black leading-tight tracking-tight mb-4">
                            {{ $post->title }}
                        </h1>
                        <div class="flex items-center gap-4 text-[#8a8060] text-sm font-medium">
                            <div class="flex items-center gap-1">
                                <span class="material-symbols-outlined text-sm">person</span>
                                <span>{{ $post->user->name ?? 'Admin' }}</span>
                            </div>
                            <span class="w-1 h-1 bg-[#8a8060] rounded-full"></span>
                            <div class="flex items-center gap-1">
                                <span class="material-symbols-outlined text-sm">calendar_today</span>
                                <span>{{ \Carbon\Carbon::parse($post->created_at)->format('d/m/Y') }}</span>
                            </div>
                            {{-- <span class="w-1 h-1 bg-[#8a8060] rounded-full"></span>
                            <div class="flex items-center gap-1">
                                <span class="material-symbols-outlined text-sm">chat_bubble</span>
                                <span>12 Bình luận</span>
                            </div> --}}
                        </div>
                    </div>
                    <!-- Featured Image -->
                    <div class="rounded-xl overflow-hidden mb-8 shadow-lg">
                        <div class="w-full aspect-video bg-center bg-cover"
                            data-alt="Professional camera phone setup on a tripod"
                            style="background-image: url('{{ asset('uploads/posts/' . $post->thumbnail) }}')">
                        </div>
                    </div>
                    <!-- Article Body -->
                    <article
                        class="prose prose-lg dark:prose-invert max-w-none text-[#181611] dark:text-gray-300 leading-relaxed mb-12">
                        <p class="text-lg font-medium mb-6">
                            {!! $post->content !!}
                        </p>
                    </article>
                    <!-- Comments Section -->
                    <section class="border-t border-[#f5f3f0] dark:border-gray-800 pt-10" id="comments">
                        <h3 class="text-2xl font-bold mb-8 flex items-center gap-2">
                            Bình luận <span class="text-primary">(12)</span>
                        </h3>
                        <!-- Comment Form -->
                        <div
                            class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm mb-10 border border-gray-100 dark:border-gray-700">
                            <p class="text-sm font-bold mb-4 uppercase tracking-wider text-[#8a8060]">Gửi bình luận của bạn
                            </p>
                            <form class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="col-span-1">
                                    <input
                                        class="w-full rounded-lg border-gray-200 dark:border-gray-700 dark:bg-gray-900 focus:border-primary focus:ring-primary py-3"
                                        placeholder="Họ và tên *" required="" type="text" />
                                </div>
                                <div class="col-span-1">
                                    <input
                                        class="w-full rounded-lg border-gray-200 dark:border-gray-700 dark:bg-gray-900 focus:border-primary focus:ring-primary py-3"
                                        placeholder="Email (không bắt buộc)" type="email" />
                                </div>
                                <div class="col-span-2">
                                    <textarea
                                        class="w-full rounded-lg border-gray-200 dark:border-gray-700 dark:bg-gray-900 focus:border-primary focus:ring-primary py-3"
                                        placeholder="Viết bình luận của bạn tại đây..." required="" rows="4"></textarea>
                                </div>
                                <div class="col-span-2 flex justify-end">
                                    <button
                                        class="bg-primary hover:bg-yellow-500 text-[#181611] font-bold px-8 py-3 rounded-lg flex items-center gap-2 transition-all"
                                        type="submit">
                                        <span class="material-symbols-outlined">send</span>
                                        Gửi bình luận
                                    </button>
                                </div>
                            </form>
                        </div>
                        <!-- Comment List -->
                        <div class="space-y-8">
                            <!-- Comment 1 -->
                            <div class="flex gap-4">
                                <div class="size-12 rounded-full flex-shrink-0 bg-center bg-cover"
                                    data-alt="User profile photo"
                                    style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuDZgevFgBuPxcwfs0Q0O_R1jDoIt0OcOjbS4-XhAY7Fmwew-1wmvsA9abPNpSyAXnvWUtHA4_Ex07NrgCLWhWAEZj16DLigNKsLnOAk9lxGS2diLaPEVdVpxO6xdnXGpDJr2Iuo5saRCztaeX4XnIcaEERw65yHVzsoCigz0fgg8cz3Vftk_9giAnr9lsI150LuVa2F8C38qyyxqPga7ugEZ0XF3AGdF06WLlnvqDMNFI-1nKX5gamOfFJvCghMFnr--8lOT1tR5uA');">
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center justify-between mb-2">
                                        <h4 class="font-bold">Trần Hoàng Nam</h4>
                                        <span class="text-xs text-[#8a8060]">2 giờ trước</span>
                                    </div>
                                    <p class="text-[#181611] dark:text-gray-300 text-sm leading-relaxed mb-3">
                                        Bài viết rất chi tiết! Mình đang phân vân giữa S24 Ultra và iPhone 15 Pro Max, sau
                                        khi xem so sánh này chắc mình sẽ chọn Samsung vì cần zoom xa để đi xem concert.
                                    </p>
                                    <button class="text-primary text-xs font-bold flex items-center gap-1 hover:underline">
                                        <span class="material-symbols-outlined text-sm">reply</span> Trả lời
                                    </button>
                                </div>
                            </div>
                            <!-- Comment 2 (Reply) -->
                            <div class="flex gap-4 ml-16 border-l-2 border-[#f5f3f0] dark:border-gray-700 pl-6">
                                <div class="size-10 rounded-full flex-shrink-0 bg-center bg-cover"
                                    data-alt="Staff profile photo"
                                    style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuCYZXqgROt_yNUqMFYVoG4eXU7ydaAex8vscUfEJ6uyMWY5FXv725wj8TuFyT1X3TOIOoM509Pw5VgWP6h3Yqgo5zeyc1CnI48u2PQu8BcqLaUIGmvcxk-MnxNI2LCxFFnzZ5CaujTi3kt5bgytiRLZ7RilI1Yef32OmUwDIO44XhtBgK6B9QCKLDBDfpzPzx0JDOz1TY8pIfU-ipB0KokA2jqLH643SzgycicYqN3lEfqO7M2RYyB_2iMjQDpRNa9CPw0oCY_vnrI');">
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="flex items-center gap-2">
                                            <h4 class="font-bold">Admin Bee Phone</h4>
                                            <span
                                                class="bg-primary text-[10px] px-2 py-0.5 rounded-full font-bold">STAFF</span>
                                        </div>
                                        <span class="text-xs text-[#8a8060]">1 giờ trước</span>
                                    </div>
                                    <p class="text-[#181611] dark:text-gray-300 text-sm leading-relaxed mb-3">
                                        Chào bạn Nam, nếu bạn cần zoom xa thì S24 Ultra là lựa chọn tuyệt vời nhất hiện nay
                                        đó ạ. Bee Phone đang có chương trình thu cũ đổi mới cho dòng này, bạn ghé shop tham
                                        khảo nhé!
                                    </p>
                                    <button class="text-primary text-xs font-bold flex items-center gap-1 hover:underline">
                                        <span class="material-symbols-outlined text-sm">reply</span> Trả lời
                                    </button>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
                <!-- Sidebar: Ads & Products -->
                <aside class="w-full lg:w-[340px] space-y-8">
                    <!-- Promo Card -->
                    <div class="bg-background-dark text-white rounded-xl overflow-hidden relative p-6 group">
                        <div class="absolute top-0 right-0 p-4">
                            <span
                                class="bg-red-500 text-white text-[10px] font-black px-3 py-1 rounded-full animate-pulse">HOT
                                DEAL</span>
                        </div>
                        <p class="text-primary text-sm font-bold mb-2 uppercase">Sản phẩm nổi bật</p>
                        <h4 class="text-xl font-bold mb-4">iPhone 15 Pro Max <br /> Titanium</h4>
                        <div class="aspect-square bg-center bg-contain bg-no-repeat mb-4 group-hover:scale-105 transition-transform"
                            data-alt="iPhone 15 Pro Max Titanium product image"
                            style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuC-wkHtXYqKrT6yXUzSyF35iKHbdIhWbvAfoqcGXqJQw1YYPecVut2Q6t3NPSvVmGrSn_YtioD_fT5gBQ2K7AZBaF6oaHlmVO-T0xQ4vuSUfj4PHRpZ0Nv-Iff7n2vbXhgco3cSG2DQQO3Maq2q6WNs5KD_z-QpohqR3rbhxiuSTl5o7JZ7ZOG8G_p5VFscYt6kxMdLa_DH_KTBNloyEDMg4fkVzrGYAhbHyTrK7KTwXiLniHsjJIq5BKXzM6GFL6P25x0_t9Qj_zg');">
                        </div>
                        <div class="flex items-end gap-2 mb-6">
                            <span class="text-2xl font-bold text-primary">29.990.000đ</span>
                            <span class="text-sm text-gray-500 line-through">32.990.000đ</span>
                        </div>
                        <button
                            class="w-full bg-primary text-background-dark font-black py-3 rounded-lg hover:bg-white transition-colors">
                            MUA NGAY
                        </button>
                    </div>
                    <!-- Newsletter -->

                    <!-- Trending Topics -->
                    <div>
                        <h4 class="font-bold mb-4 uppercase text-xs tracking-widest text-[#8a8060]">Xu hướng</h4>
                        <div class="space-y-4">
                            <a class="flex items-center gap-3 group" href="#">
                                <span
                                    class="text-2xl font-black text-gray-200 group-hover:text-primary transition-colors">01</span>
                                <p class="text-sm font-medium leading-snug group-hover:text-primary">Đánh giá chi tiết iPad
                                    Pro M4 mới nhất</p>
                            </a>
                            <a class="flex items-center gap-3 group" href="#">
                                <span
                                    class="text-2xl font-black text-gray-200 group-hover:text-primary transition-colors">02</span>
                                <p class="text-sm font-medium leading-snug group-hover:text-primary">Cách tối ưu pin cho
                                    iOS 17.5</p>
                            </a>
                            <a class="flex items-center gap-3 group" href="#">
                                <span
                                    class="text-2xl font-black text-gray-200 group-hover:text-primary transition-colors">03</span>
                                <p class="text-sm font-medium leading-snug group-hover:text-primary">Mẹo chụp ảnh đêm bằng
                                    smartphone</p>
                            </a>
                        </div>
                    </div>
                </aside>
            </div>
            <!-- Related Articles Section -->
            <section class="mt-20 pt-10 border-t border-[#f5f3f0] dark:border-gray-800">
                <h3 class="text-2xl font-bold mb-8">Bài viết liên quan</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Related 1 -->
                    @foreach ($relatedPosts as $item)
                        <div class="group cursor-pointer">
                            <div class="aspect-[16/10] rounded-xl bg-center bg-cover mb-4 overflow-hidden"
                                data-alt="Close up of a phone battery charging"
                                style="background-image: url('{{ asset('uploads/posts/' . $item->thumbnail) }}')">
                                <div class="w-full h-full bg-black/20 group-hover:bg-black/0 transition-all"></div>
                            </div>
                            <span class="text-xs font-bold text-primary uppercase mb-2 block">{{ $item->category->name ?? 'Tin tức' }}</span>
                            <h4 class="font-bold text-lg leading-snug group-hover:text-primary transition-colors mb-2">{{ $item->title }}</h4>
                            <p class="text-sm text-[#8a8060] line-clamp-2">{{ Str::limit(strip_tags($item->content), 100) }}</p>
                        </div>
                    @endforeach
                    
                </div>
            </section>
        </main>
        <!-- Footer -->
        <footer
            class="bg-white dark:bg-background-dark border-t border-[#f5f3f0] dark:border-gray-800 mt-20 py-12 px-4 lg:px-40">
            <div class="max-w-[1200px] mx-auto flex flex-col md:flex-row justify-between items-center gap-8">
                <div class="flex items-center gap-2 text-[#181611] dark:text-white">
                    <div class="size-6 bg-primary rounded flex items-center justify-center text-white">
                        <span class="material-symbols-outlined text-sm">smartphone</span>
                    </div>
                    <h2 class="text-lg font-bold">Bee Phone</h2>
                </div>
                <p class="text-[#8a8060] text-sm">© 2024 Bee Phone Ecommerce. Tất cả quyền được bảo lưu.</p>
                <div class="flex gap-4">
                    <a class="text-[#8a8060] hover:text-primary transition-colors" href="#"><span
                            class="material-symbols-outlined">public</span></a>
                    <a class="text-[#8a8060] hover:text-primary transition-colors" href="#"><span
                            class="material-symbols-outlined">alternate_email</span></a>
                    <a class="text-[#8a8060] hover:text-primary transition-colors" href="#"><span
                            class="material-symbols-outlined">share</span></a>
                </div>
            </div>
        </footer>
    </body>

    </html>
@endsection
