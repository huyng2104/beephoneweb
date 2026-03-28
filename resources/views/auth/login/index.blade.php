<!DOCTYPE html>

<html class="light" lang="vi">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Đăng nhập - Bee Phone</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&amp;display=swap"
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
                        "display": ["Inter", "sans-serif"]
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
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body class="bg-background-light dark:bg-background-dark min-h-screen flex flex-col font-display">
    @if (session('success') || session('error') )

        <div id="custom-sweet-alert"
            class="fixed inset-0 z-[9999] flex items-center justify-center bg-slate-900/60 backdrop-blur-sm transition-opacity duration-300 opacity-0">

            <div id="alert-box"
                class="bg-white dark:bg-slate-800 rounded-3xl p-8 max-w-sm w-full mx-4 shadow-2xl transform transition-all duration-300 scale-75 opacity-0 flex flex-col items-center text-center">

                @if (session('success'))
                    <div
                        class="w-20 h-20 bg-green-100 dark:bg-green-500/20 text-green-500 rounded-full flex items-center justify-center mb-5 animate-[bounce_1s_ease-in-out]">
                        <span class="material-symbols-outlined text-5xl">check_circle</span>
                    </div>
                    <h3 class="text-2xl font-black text-slate-800 dark:text-white mb-2">Thành công!</h3>
                    <p class="text-slate-600 dark:text-slate-400 mb-6 font-medium">{{ session('success') }}</p>

                    <button onclick="closeAlert()"
                        class="w-full bg-green-500 hover:bg-green-600 text-white font-bold py-3.5 rounded-xl transition-all active:scale-95 shadow-lg shadow-green-500/30">
                        Tuyệt vời
                    </button>
                @endif

                @if (session('error') )
                    <div
                        class="w-20 h-20 bg-red-100 dark:bg-red-500/20 text-red-500 rounded-full flex items-center justify-center mb-5 animate-[pulse_1s_ease-in-out]">
                        <span class="material-symbols-outlined text-5xl">warning</span>
                    </div>
                    <h3 class="text-2xl font-black text-slate-800 dark:text-white mb-2">Ôi hỏng!</h3>
                    <p class="text-slate-600 dark:text-slate-400 mb-6 font-medium">
                        @if (session('error'))
                            {{ session('error') }}
                        @else
                            Thông tin nhập vào chưa chính xác. Vui lòng kiểm tra lại!
                        @endif
                    </p>

                    <button onclick="closeAlert()"
                        class="w-full bg-red-500 hover:bg-red-600 text-white font-bold py-3.5 rounded-xl transition-all active:scale-95 shadow-lg shadow-red-500/30">
                        Đóng lại
                    </button>
                @endif

            </div>
        </div>

        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const overlay = document.getElementById('custom-sweet-alert');
                const alertBox = document.getElementById('alert-box');

                if (overlay && alertBox) {
                    // 1. Hiệu ứng "Pop-up" mượt mà ngay khi trang vừa tải xong
                    requestAnimationFrame(() => {
                        overlay.classList.remove('opacity-0');
                        overlay.classList.add('opacity-100');

                        alertBox.classList.remove('scale-75', 'opacity-0');
                        alertBox.classList.add('scale-100', 'opacity-100');
                    });

                    // 2. Tự động đóng sau 5 giây (5000ms)
                    setTimeout(() => {
                        closeAlert();
                    }, 5000);
                }
            });

            // Hàm đóng thông báo (dùng cho nút bấm và setTimeout)
            function closeAlert() {
                const overlay = document.getElementById('custom-sweet-alert');
                const alertBox = document.getElementById('alert-box');

                if (overlay && alertBox) {
                    // Đảo ngược hiệu ứng: Thu nhỏ và mờ dần
                    overlay.classList.remove('opacity-100');
                    overlay.classList.add('opacity-0');

                    alertBox.classList.remove('scale-100', 'opacity-100');
                    alertBox.classList.add('scale-75', 'opacity-0');

                    // Đợi 300ms cho hiệu ứng CSS chạy xong rồi mới xóa hẳn khỏi mã HTML
                    setTimeout(() => {
                        overlay.remove();
                    }, 300);
                }
            }
        </script>
    @endif
    <main class="min-h-screen flex">
        <!-- Left Side: Marketing/Visual Panel (Hidden on small screens) -->
        <section
            class="hidden lg:flex w-1/2 bg-slate-900 items-center justify-center p-8 relative overflow-hidden group">
            <div class="absolute inset-0 bg-gradient-to-br from-primary/80 via-slate-900 to-slate-900 z-0"></div>
            <div
                class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI0MCIgaGVpZ2h0PSI0MCI+CjxwYXRoIGQ9Ik0wIDBoNDB2NDBIMHoiIGZpbGw9Im5vbmUiLz4KPHBhdGggZD0iTTAgMTBoNDBNMTAgMHY0ME0wIDIwaDQwTTIwIDB2NDBNMCAzMGg0ME0zMCAwdjQwIiBzdHJva2U9InJnYmEoMjU1LDI1NSwyNTUsMC4wNSkiIHN0cm9rZS13aWR0aD0iMSIvPgo8L3N2Zz4=')] opacity-30 z-0">
            </div>

            <div
                class="absolute top-0 right-0 -mr-20 -mt-20 w-96 h-96 rounded-full bg-primary/40 blur-[100px] animate-pulse">
            </div>
            <div class="absolute bottom-10 left-10 w-80 h-80 rounded-full bg-blue-500/20 blur-[80px]"></div>

            <div
                class="relative z-10 w-full max-w-lg bg-white/10 backdrop-blur-xl border border-white/20 p-10 rounded-3xl shadow-2xl transition-transform duration-500 hover:scale-[1.02]">

                <div class="flex items-center gap-5 mb-8">
                    <div
                        class="w-16 h-16 bg-gradient-to-tr from-primary to-yellow-400 rounded-2xl flex items-center justify-center shadow-lg shadow-primary/30 rotate-3 transition-transform group-hover:rotate-6">
                        <span class="material-symbols-outlined text-4xl text-slate-900">smartphone</span>
                    </div>
                    <div>
                        <h1 class="text-4xl font-black text-white tracking-tight">Bee Phone</h1>
                        <p class="text-primary font-semibold text-sm tracking-widest uppercase mt-1">Premium Tech</p>
                    </div>
                </div>

                <div class="space-y-6">
                    <h2 class="text-4xl font-extrabold text-white leading-tight">
                        Khám phá công nghệ <br />
                        <span
                            class="inline-block mt-2 px-4 py-1.5 bg-gradient-to-r from-primary to-yellow-400 text-slate-900 rounded-lg shadow-md transform -rotate-2">
                            đỉnh cao
                        </span>
                        <br /> trong tầm tay.
                    </h2>
                    <p class="text-lg text-slate-300 font-medium leading-relaxed">
                        Tham gia cộng đồng Bee Phone ngay hôm nay. Nhận đặc quyền VIP, ưu đãi độc quyền và trải nghiệm
                        mua sắm thiết bị thông minh tuyệt vời nhất.
                    </p>
                </div>

                <div class="mt-10 flex items-center gap-4 border-t border-white/10 pt-6">
                    <div class="flex -space-x-3">
                        <img class="w-10 h-10 rounded-full border-2 border-slate-800"
                            src="https://i.pravatar.cc/100?img=11" alt="User">
                        <img class="w-10 h-10 rounded-full border-2 border-slate-800"
                            src="https://i.pravatar.cc/100?img=12" alt="User">
                        <img class="w-10 h-10 rounded-full border-2 border-slate-800"
                            src="https://i.pravatar.cc/100?img=33" alt="User">
                        <div
                            class="w-10 h-10 rounded-full border-2 border-slate-800 bg-primary flex items-center justify-center text-xs font-bold text-slate-900">
                            +5k
                        </div>
                    </div>
                    <p class="text-sm text-slate-400 font-medium">Khách hàng <br>đã tin dùng</p>
                </div>
            </div>
        </section>
        <!-- Right Side: Login Form Panel -->
        <section class="w-full lg:w-1/2 bg-white dark:bg-slate-950 flex flex-col px-6 md:px-20 py-12">
            <!-- Top Header for Mobile/Global Navigation -->
            <div class="flex justify-between items-center mb-12 lg:mb-24">
                <div class="flex lg:hidden items-center gap-2">
                    <div class="size-10 bg-primary rounded-lg flex items-center justify-center text-slate-900">
                        <span class="material-symbols-outlined">rocket_launch</span>
                    </div>
                    <h2 class="text-xl font-bold tracking-tight text-slate-900 dark:text-white">Bee Phone</h2>
                </div>
                <div class="hidden lg:block"></div>
                <a class="text-sm font-semibold text-slate-500 hover:text-primary transition-colors flex items-center gap-1"
                    href="#">
                    Về chúng tôi
                    <span class="material-symbols-outlined text-sm">arrow_forward</span>
                </a>
            </div>
            <div class="flex-1 flex flex-col justify-center max-w-md mx-auto w-full">
                <div class="mb-10">
                    <h1 class="text-4xl font-black text-slate-900 dark:text-white mb-3">Chào mừng trở lại!</h1>
                    <p class="text-slate-500 dark:text-slate-400 font-medium">Đăng nhập vào tài khoản Bee Phone của bạn
                        để tiếp tục.</p>
                </div>
                <form class="space-y-6" method="post" action="{{ route('login.post') }}">
                    @csrf
                    <div class="space-y-2">
                        <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Email</label>
                        <div class="relative">
                            <span
                                class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">mail</span>
                            <input name="email" value="{{old('email')}}"
                                class="w-full pl-12 pr-4 py-4 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all text-slate-900 dark:text-white"
                                placeholder="example@email.com" type="text" />

                        </div>
                        @error('email')
                            <span class="text-red-500">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="space-y-2">
                        <div class="flex justify-between items-center">
                            <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Mật khẩu</label>
                            <a class="text-xs font-bold text-primary hover:underline" href="{{route('reset-password')}}">Quên mật khẩu?</a>
                        </div>
                        <div class="relative">
                            <span
                                class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">lock</span>
                            <input name="password"
                                class="w-full pl-12 pr-12 py-4 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all text-slate-900 dark:text-white"
                                placeholder="••••••••" type="password" />

                            <button onclick="toggle(this)"
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600"
                                type="button">
                                <span class="material-symbols-outlined">visibility</span>
                            </button>
                        </div>
                        @error('password')
                            <span class="text-red-500">{{ $message }}</span>
                        @enderror
                    </div>
                    <button
                        class="w-full bg-primary hover:bg-primary/90 text-slate-900 font-black py-4 rounded-xl shadow-xl shadow-primary/20 transition-all active:scale-[0.98] text-lg"
                        type="submit">
                        Đăng nhập
                    </button>
                </form>
                {{-- <div class="relative my-10">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-slate-200 dark:border-slate-800"></div>
                    </div>
                    <div class="relative flex justify-center text-xs uppercase">
                        <span class="bg-white dark:bg-slate-950 px-4 text-slate-400 font-bold tracking-widest">Hoặc đăng
                            nhập bằng</span>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4 mb-10">
                    <button
                        class="flex items-center justify-center gap-3 py-4 border border-slate-200 dark:border-slate-800 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-900 transition-all font-bold text-slate-700 dark:text-slate-300 text-sm">
                        <img alt="Google" class="size-5"
                            src="https://lh3.googleusercontent.com/aida-public/AB6AXuDAPoUghIUCWpqqVYBjiDlloExa9dHqc4GvlZV_SAA9BwYOJOmefMqvaZN6AomVTq8le9sHEhtHUJlbDMxBEHaKDJzNX0gWDL1vbFzg6cvMfH1zaT3IT4UUiEhRc5RTOikS-yBsKNTZenUVfSklsneSGTufyup6HUECkHDWO3_zXj4_cA3lpL7GTrp3ghq0byuhFtgbGPWw5c7LOJtV9f-WeLvEDxHo4jcN1lxqN0LKsYb_9dRBN-5mxa_M0ddXpfVsNH1r_kjx1w" />
                        Google
                    </button>
                    <button
                        class="flex items-center justify-center gap-3 py-4 border border-slate-200 dark:border-slate-800 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-900 transition-all font-bold text-slate-700 dark:text-slate-300 text-sm">
                        <img alt="Facebook" class="size-5"
                            src="https://lh3.googleusercontent.com/aida-public/AB6AXuBqxrULU4SDRraqxac-OPsq0w0MTUtvQENGIoiW1ihtvBLi4z86UD8PkXMpLx1PEwmKbMIViSkkRZrx6j2oDym8WAVGyeDYWwJ5qaYsZpeKaef5cVSLoo6U-xIz0ljm6ZKsZr9tTvsBTREst1RzH7p11rXMEST3xlW_s35hyCtwqw9dP_xXrqVq1UGkA9r6HdV1aBkp0DfsFZ2ZpafPDnXNLWqJ7EODX08i9qvrVrbCRVyl1ZIUKdJ5nEgMBKMxEe393qAooNGFdg" />
                        Facebook
                    </button>
                </div> --}}
                <p class="text-center text-slate-600 dark:text-slate-400 font-medium mt-10">
                    Chưa có tài khoản? <a class="text-primary font-black hover:underline"
                        href="{{ route('register') }}">Đăng ký
                        ngay</a>
                </p>
            </div>
            <!-- Bottom Copyright -->
            <div class="mt-auto pt-8 text-center">
                <p class="text-[10px] text-slate-400 uppercase tracking-[0.2em]">© 2024 Bee Phone Store. All rights
                    reserved.</p>
            </div>
        </section>
    </main>
    <script>
        function toggle(btn) {
            let input = btn.previousElementSibling;

            input.type = input.type === "password" ? "text" : "password";
        }
    </script>
</body>

</html>
