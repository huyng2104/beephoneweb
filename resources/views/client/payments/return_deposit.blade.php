<!DOCTYPE html>

<html lang="vi">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&amp;display=swap" rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
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
            font-variation-settings: 'FILL' 1, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
    </style>
</head>

<body class="bg-background-light dark:bg-background-dark font-display text-slate-900 dark:text-slate-100 min-h-screen">
    <div class="relative flex h-auto min-h-screen w-full flex-col group/design-root overflow-x-hidden">
        <div class="layout-container flex h-full grow flex-col">
            <!-- Top Navigation Bar -->
            <header
                class="flex items-center justify-between whitespace-nowrap border-b border-solid border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 px-4 md:px-10 py-3 sticky top-0 z-50">
                <div class="flex items-center gap-4">
                    <div class="size-6 text-primary">
                        <svg fill="none" viewbox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M24 4C25.7818 14.2173 33.7827 22.2182 44 24C33.7827 25.7818 25.7818 33.7827 24 44C22.2182 33.7827 14.2173 25.7818 4 24C14.2173 22.2182 22.2182 14.2173 24 4Z"
                                fill="currentColor"></path>
                        </svg>
                    </div>
                    <h2 class="text-slate-900 dark:text-slate-100 text-lg font-bold leading-tight tracking-[-0.015em]">
                        Bee Pay</h2>
                </div>
                <button
                    class="flex items-center justify-center rounded-lg h-10 w-10 bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-slate-100 transition-colors hover:bg-slate-200 dark:hover:bg-slate-700">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </header>
            <main class="flex-1 flex flex-col items-center justify-start py-10 px-4">
                <div class="max-w-[480px] w-full flex flex-col items-center">
                    <!-- Success Icon & Header -->
                    <div class="mb-8 flex flex-col items-center animate-in fade-in zoom-in duration-500">
                        @if ($label == 'error')
                            <div
                                class="w-24 h-24 bg-red-50 dark:bg-red-900/20 rounded-full flex items-center justify-center mb-6">
                                <span class="material-symbols-outlined text-red-500 text-6xl">cancel</span>
                            </div>
                            <h1 class="text-slate-900 dark:text-slate-100 text-2xl font-bold leading-tight text-center">
                                Giao dịch thất bại!</h1>
                            <p
                                class="text-slate-600 dark:text-slate-400 text-sm font-normal leading-relaxed mt-2 text-center max-w-[320px]">
                                Bạn đã hủy giao dịch.
                            </p>
                        @else
                            <div
                                class="w-20 h-20 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center mb-6">
                                <span
                                    class="material-symbols-outlined text-green-500 dark:text-green-400 !text-5xl">check_circle</span>
                            </div>
                            <h1
                                class="text-slate-900 dark:text-slate-100 text-[28px] font-bold leading-tight text-center mb-2">
                                {{ $message }}</h1>
                            <p
                                class="text-slate-600 dark:text-slate-400 text-base font-normal leading-normal text-center">
                                Số dư ví Bee Pay của bạn đã được cập nhật.</p>
                        @endif


                    </div>
                    <!-- Transaction Details Card -->
                    <div
                        class="w-full bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden mb-8">
                        <div
                            class="p-6 border-b border-dashed border-slate-200 dark:border-slate-800 flex flex-col items-center">
                            <span
                                class="text-slate-500 dark:text-slate-400 text-sm font-medium mb-1 uppercase tracking-wider">Số
                                tiền nạp</span>
                            <h2 class="text-3xl font-bold text-{{$label == 'error' ? 'red' :'green'}}-600 dark:text-{{$label == 'error' ? 'red' :'green'}}-400 leading-tight"> {{ $label == 'error' ? '-' : '+'}}
                                {{ $amount }}
                            </h2>
                        </div>
                        <div class="p-6 space-y-4">
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-slate-500 dark:text-slate-400">Phương thức thanh toán</span>
                                <span class="text-slate-900 dark:text-slate-100 font-semibold text-right">VNPay / Thẻ
                                    ngân hàng</span>
                            </div>
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-slate-500 dark:text-slate-400">Mã giao dịch</span>
                                <div class="flex items-center gap-2">
                                    <span
                                        class="text-slate-900 dark:text-slate-100 font-mono font-medium">{{ $id_transaction }}</span>
                                    <button class="text-primary hover:opacity-80">
                                        <span class="material-symbols-outlined !text-base">content_copy</span>
                                    </button>
                                </div>
                            </div>
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-slate-500 dark:text-slate-400">Thời gian</span>
                                <span class="text-slate-900 dark:text-slate-100 font-medium">14:30 - 24/05/2024</span>
                            </div>
                        </div>

                    </div>
                    <!-- Action Buttons -->
                    <div class="w-full flex flex-col gap-3">
                        <a href="{{ route('profile.wallet') }}">
                            <button
                                class="w-full h-12 flex items-center justify-center gap-2 bg-primary hover:brightness-105 active:scale-[0.98] transition-all text-slate-900 font-bold rounded-xl">
                                <span class="material-symbols-outlined !text-xl">account_balance_wallet</span>
                                Về trang ví
                            </button>
                        </a>
                        <button
                            class="w-full h-12 flex items-center justify-center gap-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 active:scale-[0.98] transition-all text-slate-900 dark:text-slate-100 font-bold rounded-xl">
                            <span class="material-symbols-outlined !text-xl">shopping_cart</span>
                            Tiếp tục mua sắm
                        </button>
                    </div>
                    <!-- Support Link -->
                    <p class="mt-8 text-center text-sm text-slate-500 dark:text-slate-400">
                        Gặp vấn đề? <a class="text-primary font-semibold hover:underline" href="#">Liên hệ hỗ
                            trợ</a>
                    </p>
                </div>
            </main>
            <!-- Footer Graphic (Abstract Honeycomb Pattern) -->
            <div class="mt-auto opacity-10 pointer-events-none select-none overflow-hidden h-32 relative">
                <div class="absolute bottom-0 left-1/2 -translate-x-1/2 flex gap-4">
                    <span class="material-symbols-outlined !text-8xl text-primary">hexagon</span>
                    <span class="material-symbols-outlined !text-8xl text-primary -translate-y-8">hexagon</span>
                    <span class="material-symbols-outlined !text-8xl text-primary">hexagon</span>
                    <span class="material-symbols-outlined !text-8xl text-primary -translate-y-8">hexagon</span>
                    <span class="material-symbols-outlined !text-8xl text-primary">hexagon</span>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
