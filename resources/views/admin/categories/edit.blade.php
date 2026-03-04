<!DOCTYPE html>
<html class="light" lang="vi">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Sửa danh mục - Bee Phone Admin</title>
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
            </nav>
        </aside>

        <main class="flex-1 ml-64">
            <header class="h-16 border-b border-slate-200 dark:border-slate-800 bg-white/80 dark:bg-background-dark/80 backdrop-blur-md sticky top-0 z-10 px-8 flex items-center justify-between">
                <h2 class="text-lg font-bold">Sửa danh mục</h2>
                <a href="{{ route('admin.categories.index') }}" class="text-sm font-bold text-slate-600 hover:text-primary">Quay lại danh sách</a>
            </header>

            <div class="p-8 space-y-6">
                <div class="bg-white dark:bg-slate-900 p-6 rounded-xl border border-slate-100 dark:border-slate-800 shadow-sm">
                    <h1 class="text-slate-900 dark:text-white text-3xl font-bold leading-tight">Sửa danh mục</h1>
                    <p class="text-slate-500 text-sm mt-1">Cập nhật thông tin danh mục: {{ $category->name }}.</p>
                </div>

                <form action="{{ route('admin.categories.update', $category) }}" method="POST" class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-xl p-6 shadow-sm">
                    @csrf
                    @method('PUT')
                    @include('admin.categories.partials.form')
                </form>
            </div>
        </main>
    </div>
</body>

</html>