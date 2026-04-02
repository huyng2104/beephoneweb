@extends('admin.layouts.app')

@section('content')
    <!DOCTYPE html>

    <html class="light" lang="vi">

    <head>
        <meta charset="utf-8" />
        <meta content="width=device-width, initial-scale=1.0" name="viewport" />
        <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
        <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&amp;display=swap"
            rel="stylesheet" />
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
                            "tertiary-container": "#47d7ff",
                            "primary-fixed-dim": "#f3bf24",
                            "on-background": "#181611",
                            "on-secondary-fixed-variant": "#5e5a4d",
                            "surface-dim": "#e6e3db",
                            "surface-container-highest": "#e6e3db",
                            "secondary-fixed": "#f5f3f0",
                            "secondary": "#8a8060",
                            "secondary-fixed-dim": "#e6e3db",
                            "outline-variant": "#e6e3db",
                            "tertiary-fixed": "#b5ebff",
                            "surface-container-low": "#fcfcfc",
                            "primary-container": "#fef4d4",
                            "inverse-primary": "#f3bf24",
                            "surface-bright": "#ffffff",
                            "on-tertiary-container": "#005b6f",
                            "secondary-container": "#f5f3f0",
                            "surface-container-lowest": "#ffffff",
                            "tertiary-fixed-dim": "#46d6fe",
                            "background": "#f8f8f5",
                            "outline": "#8a8060",
                            "on-surface-variant": "#5e5a4d",
                            "on-primary": "#ffffff",
                            "on-tertiary-fixed-variant": "#004e5f",
                            "on-tertiary": "#ffffff",
                            "primary": "#f4c025",
                            "surface-variant": "#f5f3f0",
                            "inverse-on-surface": "#faefdf",
                            "surface-tint": "#f4c025",
                            "on-primary-fixed-variant": "#594400",
                            "error": "#e71408",
                            "inverse-surface": "#353025",
                            "tertiary": "#00677e",
                            "on-tertiary-fixed": "#001f28",
                            "on-error-container": "#93000a",
                            "on-error": "#ffffff",
                            "primary-fixed": "#fef4d4",
                            "on-surface": "#181611",
                            "surface-container": "#f5f3f0",
                            "surface": "#ffffff",
                            "on-secondary": "#ffffff",
                            "on-primary-container": "#684f00",
                            "on-primary-fixed": "#251a00",
                            "error-container": "#ffdad6",
                            "surface-container-high": "#ebe9e6",
                            "on-secondary-container": "#181611",
                            "on-secondary-fixed": "#181611"
                        },
                        fontFamily: {
                            "headline": ["Manrope"],
                            "body": ["Manrope"],
                            "label": ["Manrope"]
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
                font-family: 'Manrope', sans-serif;
            }

            .material-symbols-outlined {
                font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            }
        </style>
    </head>

    <body class="bg-background text-on-surface min-h-screen">
        <!-- Side Navigation Bar -->
        <!-- Main Content Canvas -->
        <main class="min-h-screen flex flex-col">
            <!-- Top App Bar -->
            <!-- Page Header Section -->
            <section class="p-8 pb-4">
                <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
                    <div>
                        <h2 class="text-3xl font-extrabold text-on-surface tracking-tight">Thùng rác bài viết</h2>
                        <p class="text-on-surface-variant mt-1 font-medium">Quản lý các bài viết đã xóa. Bạn có thể khôi
                            phục hoặc xóa vĩnh viễn.</p>
                    </div>
                </div>
            </section>
            <!-- Content Area - Table Card -->
            <section class="p-8 flex-1">
                <div class="bg-white rounded-2xl shadow-sm overflow-hidden border border-outline-variant/30">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-surface-container-low border-b border-outline-variant">
                                    <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-secondary">
                                        Hình ảnh</th>
                                    <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-secondary">
                                        Tiêu đề bài viết</th>
                                    <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-secondary">
                                        Danh mục</th>
                                    <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-secondary">
                                        Ngày xóa</th>
                                    <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-secondary">
                                        Tác giả</th>
                                    <th
                                        class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-secondary text-right">
                                        Thao tác</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-outline-variant/50">

                                @if ($posts->count() > 0)
                                    @foreach ($posts as $post)
                                        <tr class="hover:bg-surface-container/30 transition-colors">

                                            <td class="px-6 py-4">
                                                <img class="w-16 h-10 rounded-lg object-cover bg-surface-container"
                                                    src="/uploads/posts/{{ $post->thumbnail }}"
                                                    alt="thumbnail">
                                            </td>

                                            <td class="px-6 py-4">
                                                <p class="text-sm font-bold text-on-surface line-clamp-1">
                                                    {{ $post->title }}
                                                </p>

                                                <p class="text-xs text-secondary">
                                                    ID: #POST-{{ $post->id }}
                                                </p>
                                            </td>

                                            <td class="px-6 py-4">

                                                @if ($post->category)
                                                    <span
                                                        class="px-2.5 py-1 rounded-full bg-primary-container text-on-primary-container text-[10px] font-black uppercase">
                                                        {{ $post->category->name }}
                                                    </span>
                                                @endif

                                            </td>

                                            <td class="px-6 py-4 text-sm font-medium text-on-surface-variant">

                                                {{ $post->deleted_at->format('d/m/Y') }}

                                                <span class="block text-[10px] text-secondary">

                                                    {{ $post->deleted_at->format('H:i') }}

                                                </span>

                                            </td>

                                            <td class="px-6 py-4">

                                                <div class="flex items-center gap-2">

                                                    <div
                                                        class="w-6 h-6 rounded-full bg-secondary-container flex items-center justify-center text-[10px] font-bold">

                                                        {{ strtoupper(substr($post->author->name ?? 'A', 0, 2)) }}

                                                    </div>

                                                    <span class="text-sm font-medium">

                                                        {{ $post->author->name ?? 'Admin' }}

                                                    </span>

                                                </div>

                                            </td>

                                            <td class="px-6 py-4 text-right">

                                                <div class="flex justify-end gap-2">

                                                    <!-- Restore -->

                                                    <form action="{{ route('admin.posts.restore', $post->id) }}"
                                                        method="POST">

                                                        @csrf

                                                        <button
                                                            class="p-2 hover:bg-primary/10 text-primary rounded-lg transition-colors"
                                                            title="Khôi phục">

                                                            <span
                                                                class="material-symbols-outlined">restore_from_trash</span>

                                                        </button>

                                                    </form>

                                                    <!-- Force delete -->

                                                    <form action="{{ route('admin.posts.forceDelete', $post->id) }}"
                                                        method="POST">

                                                        @csrf
                                                        @method('DELETE')

                                                        <button
                                                            class="p-2 hover:bg-error/10 text-error rounded-lg transition-colors"
                                                            onclick="return confirm('Xóa vĩnh viễn bài viết này?')"
                                                            title="Xóa vĩnh viễn">

                                                            <span class="material-symbols-outlined">delete_forever</span>

                                                        </button>

                                                    </form>

                                                </div>

                                            </td>

                                        </tr>
                                    @endforeach
                                @else
                                    <tr>

                                        <td colspan="6" class="text-center py-10 text-gray-400">

                                            Thùng rác trống

                                        </td>

                                    </tr>
                                @endif

                            </tbody>
                        </table>
                    </div>
                    <!-- Pagination Footer -->
                    <div class="p-6 border-t border-outline-variant flex items-center justify-between">
                        <p class="text-xs font-bold text-secondary">Hiển thị 3 trên 24 bài viết đã xóa</p>
                        <div class="flex items-center gap-1">
                            <button
                                class="w-10 h-10 flex items-center justify-center rounded-xl border border-outline-variant text-secondary hover:bg-surface-container transition-colors disabled:opacity-50"
                                disabled="">
                                <span class="material-symbols-outlined">chevron_left</span>
                            </button>
                            <button
                                class="w-10 h-10 flex items-center justify-center rounded-xl bg-primary text-white font-bold text-sm shadow-sm">1</button>
                            <button
                                class="w-10 h-10 flex items-center justify-center rounded-xl hover:bg-surface-container text-on-surface font-bold text-sm transition-colors">2</button>
                            <button
                                class="w-10 h-10 flex items-center justify-center rounded-xl hover:bg-surface-container text-on-surface font-bold text-sm transition-colors">3</button>
                            <span class="px-2 text-secondary">...</span>
                            <button
                                class="w-10 h-10 flex items-center justify-center rounded-xl hover:bg-surface-container text-on-surface font-bold text-sm transition-colors">8</button>
                            <button
                                class="w-10 h-10 flex items-center justify-center rounded-xl border border-outline-variant text-on-surface hover:bg-surface-container transition-colors">
                                <span class="material-symbols-outlined">chevron_right</span>
                            </button>
                        </div>
                    </div>
                </div>
            </section>
            <!-- Contextual Empty State (Hidden by default, shown for illustration) -->
            <!--
                <div class="flex flex-col items-center justify-center py-20 opacity-40">
                    <span class="material-symbols-outlined text-6xl mb-4">auto_delete</span>
                    <p class="text-xl font-extrabold">Thùng rác trống</p>
                    <p class="text-sm font-medium">Không có bài viết nào bị xóa gần đây.</p>
                </div>
                -->
        </main>
        <!-- FAB for quick actions -->
        <div class="fixed bottom-8 right-8 flex flex-col gap-3">
            <button
                class="w-14 h-14 bg-primary text-white rounded-full shadow-lg flex items-center justify-center hover:scale-105 active:scale-95 transition-all group"
                title="Tạo bài viết mới">
                <span class="material-symbols-outlined text-3xl">add</span>
            </button>
        </div>
    </body>

    </html>
@endsection
