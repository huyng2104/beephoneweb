<!DOCTYPE html>
<html class="light" lang="vi">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Thêm thương hiệu - Bee Phone Admin</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&amp;display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet" />
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#ffc105",
                        "background-light": "#f8f8f5",
                        "background-dark": "#231e0f",
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
            vertical-align: middle;
        }

        .sidebar-item-active {
            background-color: #ffc105;
            color: #231e0f;
        }
    </style>
</head>

<body class="bg-background-light dark:bg-background-dark font-display text-slate-900 dark:text-slate-100">
    <div class="flex min-h-screen">
        <aside class="w-64 border-r border-slate-200 dark:border-slate-800 bg-white dark:bg-background-dark flex flex-col fixed h-full">
            <div class="p-6 flex items-center gap-3">
                <div class="bg-primary rounded-lg p-1.5 flex items-center justify-center">
                    <span class="material-symbols-outlined text-background-dark font-bold">smartphone</span>
                </div>
                <div>
                    <h1 class="text-xl font-bold tracking-tight text-slate-900 dark:text-white leading-none">Bee Phone</h1>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Hệ thống quản trị</p>
                </div>
            </div>
            <nav class="flex-1 px-4 py-4 space-y-1">
                <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors" href="{{ url('/admin') }}">
                    <span class="material-symbols-outlined">dashboard</span>
                    <span>Bảng điều khiển</span>
                </a>
                <a class="sidebar-item-active flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium transition-colors" href="{{ route('admin.categories.index') }}">
                    <span class="material-symbols-outlined">category</span>
                    <span>Danh mục</span>
                </a>
                <div class="pt-4 mt-4 border-t border-slate-100 dark:border-slate-800">
                    <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors" href="#">
                        <span class="material-symbols-outlined">settings</span>
                        <span>Cài đặt</span>
                    </a>
                </div>
            </nav>
        </aside>

        <main class="flex-1 ml-64">
            <header class="h-16 border-b border-slate-200 dark:border-slate-800 bg-white/80 dark:bg-background-dark/80 backdrop-blur-md sticky top-0 z-10 px-8 flex items-center justify-between">
                <div class="w-96">
                    <div class="relative group">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-primary transition-colors">search</span>
                        <input class="w-full pl-10 pr-4 py-2 bg-slate-100 dark:bg-slate-800 border-none rounded-lg text-sm focus:ring-2 focus:ring-primary/50 transition-all outline-none" placeholder="Tìm kiếm..." type="text" />
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <button class="w-10 h-10 flex items-center justify-center rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                        <span class="material-symbols-outlined text-slate-600 dark:text-slate-300">notifications</span>
                    </button>
                </div>
            </header>

            <div class="p-8 space-y-6">
                <div class="bg-white dark:bg-slate-900 p-6 rounded-xl border border-slate-100 dark:border-slate-800 shadow-sm">
                    <div class="flex flex-wrap justify-between items-end gap-4">
                        <div class="flex flex-col gap-1">
                            <p class="text-slate-900 dark:text-white text-4xl leading-tight font-bold">Thêm thương hiệu mới</p>
                            <p class="text-slate-500 text-sm">Tạo thương hiệu để liên kết với sản phẩm và danh mục.</p>
                        </div>
                        <a href="{{ route('admin.brands.index') }}" class="flex items-center justify-center rounded-lg h-11 px-5 border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 text-sm font-semibold hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                            Quay lại danh sách
                        </a>
                    </div>
                </div>

                <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-100 dark:border-slate-800 shadow-sm overflow-hidden">
                    <div class="flex border-b border-slate-100 dark:border-slate-800 px-6 pt-2">
                        <a href="{{ route('admin.categories.index') }}" class="flex items-center gap-2 border-b-2 border-transparent text-slate-500 py-4 px-2 font-bold text-sm leading-tight hover:text-primary transition-colors">
                            <span class="material-symbols-outlined text-[20px]">account_tree</span>
                            Cấu trúc Danh mục
                        </a>
                        <a href="{{ route('admin.brands.index') }}" class="flex items-center gap-2 border-b-2 border-primary text-primary py-4 px-6 font-bold text-sm leading-tight transition-colors">
                            <span class="material-symbols-outlined text-[20px]">verified</span>
                            Quản lý Thương hiệu
                        </a>
                    </div>

                    <form action="{{ route('admin.brands.store') }}" method="POST" class="p-6">
                        @csrf
                        @include('admin.categories.brands.partials.form')
                    </form>
                </div>
            </div>
    </div>
    </div>
</body>

</html>