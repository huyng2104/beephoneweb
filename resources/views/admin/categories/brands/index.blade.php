<!DOCTYPE html>
<html class="light" lang="vi">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Quản lý thương hiệu - Bee Phone Admin</title>
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
                            <p class="text-slate-900 dark:text-white text-4xl leading-tight font-bold">Quản lý thương hiệu</p>
                            <p class="text-slate-500 text-sm">Tạo, cập nhật và kiểm soát trạng thái thương hiệu hiển thị.</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.categories.index') }}" class="flex items-center justify-center rounded-lg h-11 px-5 border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 text-sm font-semibold hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                                Danh mục
                            </a>
                            <a href="{{ route('admin.brands.create') }}" class="flex items-center justify-center rounded-lg h-11 px-5 bg-primary text-black text-sm font-bold hover:brightness-105 transition-all shadow-sm">
                                <span class="material-symbols-outlined mr-2 text-[20px]">add_circle</span>
                                Thêm thương hiệu
                            </a>
                        </div>
                    </div>
                </div>

                @if (session('status'))
                <div class="p-3 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-lg text-sm">
                    {{ session('status') }}
                </div>
                @endif

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

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead class="bg-slate-50 dark:bg-slate-800/50">
                                <tr>
                                    <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wide">Thương hiệu</th>
                                    <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wide">Slug</th>
                                    <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wide">Thứ tự</th>
                                    <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wide">Trạng thái</th>
                                    <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wide text-right">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                @forelse ($brands as $brand)
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                                    <td class="px-5 py-5">
                                        <div class="flex items-center gap-3">
                                            <div class="size-10 rounded-lg bg-slate-100 dark:bg-slate-800 flex items-center justify-center overflow-hidden">
                                                @if ($brand->logo_url)
                                                <img src="{{ $brand->logo_url }}" alt="{{ $brand->name }}" class="w-full h-full object-contain">
                                                @else
                                                <span class="material-symbols-outlined text-slate-400 text-[20px]">verified</span>
                                                @endif
                                            </div>
                                            <span class="text-sm font-semibold text-slate-900 dark:text-white">{{ $brand->name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-5 py-5 text-sm text-slate-600 dark:text-slate-300">{{ $brand->slug }}</td>
                                    <td class="px-5 py-5 text-sm text-slate-600 dark:text-slate-300">{{ $brand->sort_order }}</td>
                                    <td class="px-5 py-5">
                                        <span class="px-3 py-1 rounded text-xs font-bold {{ $brand->is_active ? 'bg-primary/10 text-primary' : 'bg-slate-200 text-slate-600' }}">
                                            {{ $brand->is_active ? 'Kích hoạt' : 'Ẩn' }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-5 text-right whitespace-nowrap">
                                        <a href="{{ route('admin.brands.edit', $brand) }}" class="text-slate-400 hover:text-primary transition-colors" title="Sửa">
                                            <span class="material-symbols-outlined text-[20px]">edit</span>
                                        </a>
                                        <form action="{{ route('admin.brands.destroy', $brand) }}" method="POST" class="inline" onsubmit="return confirm('Bạn chắc chắn muốn xóa thương hiệu này?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-slate-400 hover:text-red-500 transition-colors ml-3" title="Xóa">
                                                <span class="material-symbols-outlined text-[20px]">delete</span>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-5 py-8 text-center">
                                        <p class="text-sm text-slate-500">Chưa có thương hiệu nào.</p>
                                        <a href="{{ route('admin.brands.create') }}" class="mt-3 inline-flex items-center gap-2 px-3 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 text-xs font-semibold hover:border-primary hover:text-primary transition-colors">
                                            <span class="material-symbols-outlined text-[16px]">add</span>
                                            Thêm thương hiệu đầu tiên
                                        </a>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
    </div>
    </div>
</body>

</html>