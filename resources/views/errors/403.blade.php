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
                        "display": ["Inter"]
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
    <title>403 - Truy cập bị từ chối | Bee Phone</title>
</head>

<body
    class="bg-background-light dark:bg-background-dark font-display text-slate-900 dark:text-slate-100 min-h-screen flex flex-col">
    <!-- Top Navigation Bar -->
    <main class="flex-1 flex flex-col items-center justify-center px-6 py-12">
        <div class="max-w-[600px] w-full text-center space-y-8">
            <!-- Error Illustration -->
            <div class="relative flex justify-center">
                <div class="w-64 h-64 bg-primary/10 rounded-full flex items-center justify-center relative">
                    <span class="material-symbols-outlined text-[120px] text-primary">lock_person</span>
                    <!-- Decorative Bee/Tech Elements -->
                    <div
                        class="absolute -top-2 -right-2 w-12 h-12 bg-primary rounded-full flex items-center justify-center shadow-lg">
                        <span class="material-symbols-outlined text-slate-900 text-xl">shield</span>
                    </div>
                    <div
                        class="absolute bottom-4 -left-4 w-10 h-10 bg-slate-900 dark:bg-primary rounded-lg flex items-center justify-center transform -rotate-12 shadow-md">
                        <span
                            class="material-symbols-outlined text-white dark:text-slate-900 text-lg">no_accounts</span>
                    </div>
                </div>
            </div>
            <!-- Error Code & Message -->
            <div class="space-y-4">
                <h1 class="text-primary text-8xl font-black tracking-tighter">403</h1>
                <h2 class="text-slate-900 dark:text-slate-100 text-3xl md:text-4xl font-bold leading-tight">Truy cập bị
                    từ chối</h2>
                <p class="text-slate-600 dark:text-slate-400 text-lg max-w-md mx-auto">
                    Rất tiếc, bạn không có quyền truy cập vào trang này. Vui lòng kiểm tra lại tài khoản hoặc liên hệ
                    quản trị viên Bee Phone để được hỗ trợ.
                </p>
            </div>
            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4 pt-4">
                <a class="w-full sm:w-auto px-8 py-3 bg-primary hover:bg-yellow-500 text-slate-900 font-bold rounded-xl transition-all shadow-lg shadow-primary/20 flex items-center justify-center gap-2"
                    href="">
                    <span class="material-symbols-outlined">home</span>
                    Quay lại trang chủ
                </a>
                <a class="w-full sm:w-auto px-8 py-3 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-semibold rounded-xl transition-all flex items-center justify-center gap-2 border border-slate-200 dark:border-slate-700"
                    href="#">
                    <span class="material-symbols-outlined">support_agent</span>
                    Liên hệ hỗ trợ
                </a>
            </div>
            <!-- Quick Links -->

        </div>
    </main>
</body>

</html>
