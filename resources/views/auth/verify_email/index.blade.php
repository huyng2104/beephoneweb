<!DOCTYPE html>

<html class="light" lang="vi">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
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

        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
    </style>
</head>

<body class="bg-background-light dark:bg-background-dark text-slate-900 dark:text-slate-100 font-display">
    @if (session('success') || session('error') || $errors->any())

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

                @if (session('error') || $errors->any())
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
    <div class="flex min-h-screen w-full flex-col justify-center items-center">
        <!-- Left Section: Brand Visual -->
        <!-- Right Section: Content -->
        <div
            class="flex w-full flex-col items-center justify-center bg-white px-6 py-12 dark:bg-background-dark lg:px-20 lg:max-w-2xl">
            <div class="w-full max-w-md">
                <!-- Mobile Logo -->
                <div class="mb-10 flex flex-col items-center text-center lg:text-left">
                    <div class="mb-6 flex h-16 w-16 items-center justify-center rounded-2xl bg-primary/10 text-primary">
                        <span class="material-symbols-outlined !text-4xl">mark_email_unread</span>
                    </div>
                    <h2 class="text-3xl font-extrabold tracking-tight text-slate-900 dark:text-slate-100 lg:text-4xl">
                        Kiểm tra email của bạn</h2>
                    <p class="mt-4 text-lg leading-relaxed text-slate-600 dark:text-slate-400">
                        Chúng tôi đã gửi một liên kết xác nhận đến địa chỉ email:
                        <span class="font-bold text-slate-900 dark:text-primary">
                            @auth
                                {{ Auth::user()->email }}
                            @endauth
                        </span>.
                    </p>
                    <p class="mt-2 text-slate-600 dark:text-slate-400">
                        Vui lòng kiểm tra hộp thư đến (hoặc thư rác) và nhấp vào liên kết để kích hoạt tài khoản của
                        bạn.
                    </p>
                </div>
                <div class="space-y-4">
                    <a href="https://mail.google.com/" target="_blank"
                        class="flex w-full items-center justify-center gap-2 rounded-xl bg-primary px-6 py-4 text-lg font-bold text-slate-900 shadow-lg shadow-primary/20 transition-transform active:scale-95">
                        <span class="material-symbols-outlined">mail</span>
                        Mở ứng dụng Email
                    </a>
                    <div class="flex flex-col items-center gap-6 pt-6">

                        <form method="POST" action="{{ route('verification.send') }}">
                            @csrf
                            <div class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-400">
                                Bạn không nhận được email?
                                <button class="font-bold text-primary hover:underline">Gửi lại email</button>
                            </div>
                        </form>

                        <a class="flex items-center gap-2 text-sm font-semibold text-slate-900 dark:text-slate-100 transition-colors hover:text-primary"
                            href="{{ route('logout') }}">
                            <span class="material-symbols-outlined !text-base">arrow_back</span>
                            Quay lại đăng nhập
                        </a>
                    </div>
                </div>
                <!-- Footer / Support Info -->
                <div class="mt-20 border-t border-slate-100 pt-8 dark:border-slate-800 lg:mt-32">
                    <div class="flex flex-col items-center justify-between gap-4 text-sm text-slate-400 lg:flex-row">
                        <span>© 2024 Bee Phone Việt Nam</span>
                        <div class="flex gap-4">
                            <a class="hover:text-primary" href="#">Hỗ trợ</a>
                            <a class="hover:text-primary" href="#">Bảo mật</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
