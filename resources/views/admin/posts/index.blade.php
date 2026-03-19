@extends('admin.layouts.app')
@section('content')
    <!DOCTYPE html>

    <html class="light" lang="vi">

    <head>
        <meta charset="utf-8" />
        <meta content="width=device-width, initial-scale=1.0" name="viewport" />
        <title>Quản lý bài viết</title>
        <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
        <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@200..800&amp;display=swap" rel="stylesheet" />
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
                            "display": ["Manrope", "sans-serif"]
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

            .toggle-checkbox:checked+.toggle-label {
                background-color: #f4c025;
            }

            .toggle-checkbox:checked+.toggle-label .toggle-dot {
                transform: translateX(100%);
            }
        </style>
    </head>
    @if (session('success'))
        <div id="toast-success"
            style="
position: fixed;
top: 20px;
right: 20px;
background: #28a745;
color: white;
padding: 15px 25px;
border-radius: 8px;
box-shadow: 0 5px 15px rgba(0,0,0,0.2);
z-index: 9999;
font-weight: 500;
">

            ✔ {{ session('success') }}

        </div>
    @endif

    <body class="bg-background-light dark:bg-background-dark min-h-screen">
        <div class="flex flex-col">
            <main class="flex-1 w-full px-10 py-8">
                <div class="space-y-6">
                    <div class="flex flex-wrap justify-between items-end gap-3">
                        <div class="flex min-w-72 flex-col gap-1">
                            <p class="text-[#181611] dark:text-white text-4xl font-black leading-tight tracking-[-0.033em]">
                                Quản lý Bài viết</p>
                            <p class="text-[#8a8060] dark:text-[#b5ae98] text-base font-normal leading-normal">Quản lý nội
                                dung Blog, Tin tức và Đánh giá công nghệ Bee Phone</p>
                        </div>
                        <a href="{{ route('admin.posts.create') }}">
                            <button
                                class="flex min-w-[180px] items-center justify-center overflow-hidden rounded-lg h-12 px-6 bg-primary text-[#181611] text-sm font-bold leading-normal tracking-[0.015em] hover:bg-[#e0b020] transition-all shadow-sm">
                                <span class="material-symbols-outlined mr-2">add</span>
                                <span class="truncate">Thêm bài viết mới</span>
                            </button>
                        </a>
                    </div>
                    <!-- Tabs & Quick Stats -->
                    <div
                        class="bg-white dark:bg-[#2c2818] rounded-xl shadow-sm overflow-hidden border border-[#e6e3db] dark:border-[#3d3a30]">
                        <div class="flex border-b border-[#e6e3db] dark:border-[#3d3a30] px-6 gap-8">
                            <a class="flex flex-col items-center justify-center border-b-[3px] border-primary text-[#181611] dark:text-white pb-[13px] pt-4"
                                href="#">
                                <p class="text-sm font-bold leading-normal tracking-[0.015em]">Danh sách bài viết</p>
                            </a>
                            <a class="flex flex-col items-center justify-center border-b-[3px] border-transparent text-[#8a8060] dark:text-[#b5ae98] pb-[13px] pt-4 hover:text-primary transition-colors"
                                href="{{ route('admin.post-categories.index') }}">
                                <p class="text-sm font-bold leading-normal tracking-[0.015em]">Quản lý danh mục</p>
                            </a>
                        </div>
                        <!-- Toolbar & Filters -->
                        <div class="p-6 space-y-4">
                            {{-- <div class="flex flex-wrap items-center justify-between gap-4">
                                <div class="flex flex-1 min-w-[300px] max-w-md">
                                    <div class="relative w-full">
                                        <span
                                            class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#8a8060]">search</span>
                                        <input
                                            class="w-full pl-10 pr-4 py-2 rounded-lg border border-[#e6e3db] dark:border-[#3d3a30] bg-[#f8f8f5] dark:bg-[#3d3a30] text-[#181611] dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/50"
                                            placeholder="Tìm kiếm theo tiêu đề hoặc tác giả..." type="text" />
                                    </div>
                                </div>
                                <div class="flex gap-2 items-center">
                                    <span class="text-sm font-bold text-[#8a8060] mr-2">Bộ lọc:</span>
                                    <div class="flex gap-2">
                                        <button
                                            class="flex h-9 items-center justify-center gap-x-2 rounded-lg bg-primary/10 text-primary px-4 border border-primary/20">
                                            <p class="text-xs font-bold leading-normal uppercase">Tất cả</p>
                                        </button>
                                        <button
                                            class="flex h-9 items-center justify-center gap-x-2 rounded-lg bg-[#f5f3f0] dark:bg-[#3d3a30] text-[#181611] dark:text-white px-4">
                                            <p class="text-xs font-bold leading-normal uppercase">Tin công nghệ</p>
                                            <span class="material-symbols-outlined text-sm">expand_more</span>
                                        </button>
                                        <button
                                            class="flex h-9 items-center justify-center gap-x-2 rounded-lg bg-[#f5f3f0] dark:bg-[#3d3a30] text-[#181611] dark:text-white px-4">
                                            <p class="text-xs font-bold leading-normal uppercase">Cẩm nang</p>
                                            <span class="material-symbols-outlined text-sm">expand_more</span>
                                        </button>
                                        <button
                                            class="flex h-9 items-center justify-center gap-x-2 rounded-lg bg-[#f5f3f0] dark:bg-[#3d3a30] text-[#181611] dark:text-white px-4">
                                            <p class="text-xs font-bold leading-normal uppercase">Đánh giá</p>
                                            <span class="material-symbols-outlined text-sm">expand_more</span>
                                        </button>
                                    </div>
                                    <button
                                        class="p-2 text-[#181611] dark:text-white hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg">
                                        <span class="material-symbols-outlined">filter_list</span>
                                    </button>
                                </div>
                            </div> --}}
                            <!-- Data Table -->
                            <div class="overflow-x-auto">
                                <table class="w-full text-left border-collapse">
                                    <thead>
                                        <tr
                                            class="text-[#8a8060] dark:text-[#b5ae98] border-b border-[#e6e3db] dark:border-[#3d3a30]">
                                            <th class="py-4 px-2 font-bold text-xs uppercase tracking-wider">Bài viết</th>
                                            <th class="py-4 px-2 font-bold text-xs uppercase tracking-wider">Danh mục</th>
                                            <th class="py-4 px-2 font-bold text-xs uppercase tracking-wider">Tác giả</th>
                                            <th class="py-4 px-2 font-bold text-xs uppercase tracking-wider">Ngày đăng</th>
                                            <th class="py-4 px-2 font-bold text-xs uppercase tracking-wider text-center">
                                                Lượt xem</th>
                                            <th class="py-4 px-2 font-bold text-xs uppercase tracking-wider text-center">
                                                Hiển thị</th>
                                            <th class="py-4 px-2 font-bold text-xs uppercase tracking-wider text-right">Thao
                                                tác</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-[#e6e3db] dark:divide-[#3d3a30]">
                                        <!-- Row 1 -->
                                        @foreach ($posts as $post)
                                            <tr class="hover:bg-primary/5 dark:hover:bg-primary/5 transition-colors group">
                                                <td class="py-4 px-2">
                                                    <div class="flex items-center gap-3">
                                                        <div
                                                            class="w-16 h-12 rounded-lg overflow-hidden shrink-0 border border-[#e6e3db]">
                                                            <img class="w-full h-full object-cover"
                                                                data-alt="Hình ảnh bài viết đánh giá iPhone 15"
                                                                src="/uploads/posts/{{ $post->thumbnail }}" />
                                                        </div>
                                                        <div class="max-w-xs">
                                                            <p
                                                                class="text-[#181611] dark:text-white font-bold text-sm line-clamp-2">
                                                                {{ $post->title }}</p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="py-4 px-2">
                                                    <span
                                                        class="px-2 py-1 rounded bg-[#f5f3f0] dark:bg-[#3d3a30] text-[#181611] dark:text-white text-[11px] font-bold uppercase tracking-wider">
                                                        {{ $post->category->name }}</span>
                                                </td>
                                                <td class="py-4 px-2 text-sm text-[#181611] dark:text-white">
                                                    {{ $post->user->name }}</td>
                                                <td class="py-4 px-2 text-sm text-[#8a8060] dark:text-[#b5ae98]">
                                                    {{ $post->created_at->format('d/m/Y') }}
                                                </td>
                                                <td class="py-4 px-2 text-center">
                                                    <div
                                                        class="flex items-center justify-center gap-1 text-sm text-[#8a8060]">
                                                        <span
                                                            class="material-symbols-outlined text-[16px]">visibility</span>
                                                        {{ $post->views }}
                                                    </div>
                                                </td>
                                                <td class="py-4 px-2 text-center">
                                                    <label class="relative inline-flex items-center cursor-pointer">
                                                        <input checked="" class="sr-only toggle-checkbox"
                                                            type="checkbox" />
                                                        <div
                                                            class="toggle-label w-10 h-5 bg-gray-300 rounded-full transition-colors relative">
                                                            <div
                                                                class="toggle-dot absolute left-1 top-1 bg-white w-3 h-3 rounded-full transition-transform">
                                                            </div>
                                                        </div>
                                                    </label>
                                                </td>
                                                <td class="py-4 px-2 text-right">
                                                    <div class="flex justify-end gap-2 transition-opacity">
                                                        <a href="{{ route('admin.posts.show', $post->id) }}">
                                                            <button class="p-1.5 hover:bg-blue-50 text-blue-600 rounded-md"
                                                                title="Xem thử">
                                                                <span class="material-symbols-outlined">visibility</span>
                                                            </button>
                                                        </a>
                                                        <a href="{{ route('admin.posts.edit', $post->id) }}"
                                                            class="btn btn-warning btn-sm">
                                                            <button
                                                                class="p-1.5 hover:bg-primary/20 text-primary-dark rounded-md"
                                                                title="Chỉnh sửa">
                                                                <span class="material-symbols-outlined">edit</span>
                                                            </button>

                                                        </a>
                                                        <form action="{{ route('admin.posts.destroy', $post->id) }}"
                                                            method="POST" style="display:inline">

                                                            @csrf
                                                            @method('DELETE')

                                                            <button
                                                                onclick="return confirm('Bạn có chắc chắn muốn xóa bài viết này không?')"
                                                                class="p-1.5 hover:bg-red-50 text-red-600 rounded-md"
                                                                title="Xóa">
                                                                <span class="material-symbols-outlined">delete</span>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <!-- Pagination -->
                            <div
                                class="flex items-center justify-between py-4 border-t border-[#e6e3db] dark:border-[#3d3a30]">
                                <p class="text-sm text-[#8a8060]">Hiển thị 1 - 3 của 48 bài viết</p>
                                <div class="flex gap-1">
                                    <button
                                        class="w-10 h-10 flex items-center justify-center rounded-lg border border-[#e6e3db] text-[#181611] hover:bg-gray-50">
                                        <span class="material-symbols-outlined">chevron_left</span>
                                    </button>
                                    <button
                                        class="w-10 h-10 flex items-center justify-center rounded-lg bg-primary text-[#181611] font-bold">1</button>
                                    <button
                                        class="w-10 h-10 flex items-center justify-center rounded-lg border border-[#e6e3db] text-[#181611] hover:bg-gray-50">2</button>
                                    <button
                                        class="w-10 h-10 flex items-center justify-center rounded-lg border border-[#e6e3db] text-[#181611] hover:bg-gray-50">3</button>
                                    <span class="flex items-center px-2 text-[#8a8060]">...</span>
                                    <button
                                        class="w-10 h-10 flex items-center justify-center rounded-lg border border-[#e6e3db] text-[#181611] hover:bg-gray-50">16</button>
                                    <button
                                        class="w-10 h-10 flex items-center justify-center rounded-lg border border-[#e6e3db] text-[#181611] hover:bg-gray-50">
                                        <span class="material-symbols-outlined">chevron_right</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </body>

    </html>
    <script>
        setTimeout(function() {
            let toast = document.getElementById('toast-success');
            if (toast) {
                toast.style.opacity = "0";
                toast.style.transition = "0.5s";

                setTimeout(() => {
                    toast.remove();
                }, 500);
            }
        }, 3000);
    </script>
@endsection
