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
    @include('popup_notify.index')
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
