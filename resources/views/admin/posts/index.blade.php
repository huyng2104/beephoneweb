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
                            <div class="flex flex-wrap items-center justify-between gap-4">
                                <form method="GET" action="{{ route('admin.posts.index') }}" class="flex gap-4 flex-wrap">
                                    <div class="flex flex-1 min-w-[300px] max-w-md">
                                        <div class="relative w-full">
                                            <span
                                                class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#8a8060]">search</span>
                                            <input name="keyword" value="{{ request('keyword') }}"
                                                class="w-full pl-10 pr-4 py-2 rounded-lg border border-[#e6e3db] dark:border-[#3d3a30] bg-[#f8f8f5] dark:bg-[#3d3a30] text-[#181611] dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/50"
                                                placeholder="Tìm kiếm theo tiêu đề hoặc tác giả..." type="text" />
                                        </div>
                                    </div>
                                    <div class="flex gap-2 items-center">
                                        <span class="text-sm font-bold text-[#8a8060] mr-2">Bộ lọc:</span>
                                        {{-- <div class="flex gap-2">
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
                                        </button> --}}
                                        <select name="category_id" class="h-9 rounded-lg border border-[#e6e3db] px-3">

                                            <option value="">Tất cả</option>

                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}"
                                                    {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                                    {{ $category->name }}
                                                </option>
                                            @endforeach

                                        </select>
                                        <button class="px-4 py-2 bg-primary text-white rounded-lg">
                                            Tìm kiếm
                                        </button>
                                    </div>
                                </form>
                            </div>
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
                                                        <input type="checkbox" class="sr-only toggle-checkbox toggle-status"
                                                            data-id="{{ $post->id }}"
                                                            {{ $post->status ? 'checked' : '' }} />
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

                                <p class="text-sm text-[#8a8060]">
                                    Hiển thị {{ $posts->firstItem() }} - {{ $posts->lastItem() }} của
                                    {{ $posts->total() }} bài viết
                                </p>

                                <div class="flex gap-1">

                                    {{-- Previous --}}
                                    @if ($posts->onFirstPage())
                                        <span
                                            class="w-10 h-10 flex items-center justify-center rounded-lg border border-[#e6e3db] text-gray-400">
                                            <span class="material-symbols-outlined">chevron_left</span>
                                        </span>
                                    @else
                                        <a href="{{ $posts->appends(request()->query())->previousPageUrl() }}"
                                            class="w-10 h-10 flex items-center justify-center rounded-lg border border-[#e6e3db] hover:bg-gray-50">
                                            <span class="material-symbols-outlined">chevron_left</span>
                                        </a>
                                    @endif


                                    {{-- Page Numbers --}}
                                    @for ($i = 1; $i <= $posts->lastPage(); $i++)
                                        @if ($i == $posts->currentPage())
                                            <span
                                                class="w-10 h-10 flex items-center justify-center rounded-lg bg-primary text-[#181611] font-bold">
                                                {{ $i }}
                                            </span>
                                        @else
                                            <a href="{{ $posts->appends(request()->query())->url($i) }}"
                                                class="w-10 h-10 flex items-center justify-center rounded-lg border border-[#e6e3db] hover:bg-gray-50">
                                                {{ $i }}
                                            </a>
                                        @endif
                                    @endfor


                                    {{-- Next --}}
                                    @if ($posts->hasMorePages())
                                        <a href="{{ $posts->appends(request()->query())->nextPageUrl() }}"
                                            class="w-10 h-10 flex items-center justify-center rounded-lg border border-[#e6e3db] hover:bg-gray-50">
                                            <span class="material-symbols-outlined">chevron_right</span>
                                        </a>
                                    @else
                                        <span
                                            class="w-10 h-10 flex items-center justify-center rounded-lg border border-[#e6e3db] text-gray-400">
                                            <span class="material-symbols-outlined">chevron_right</span>
                                        </span>
                                    @endif

                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Summary Footer Info -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div
                            class="bg-white dark:bg-[#2c2818] p-5 rounded-xl border border-[#e6e3db] dark:border-[#3d3a30] shadow-sm flex items-center gap-4">
                            <div
                                class="w-12 h-12 rounded-full bg-primary/20 flex items-center justify-center text-primary">
                                <span class="material-symbols-outlined">article</span>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-[#8a8060] uppercase tracking-wider">Tổng bài viết</p>
                                <p class="text-2xl font-black text-[#181611] dark:text-white">
                                    {{ number_format($totalPosts) }}</p>
                            </div>
                        </div>
                        <div
                            class="bg-white dark:bg-[#2c2818] p-5 rounded-xl border border-[#e6e3db] dark:border-[#3d3a30] shadow-sm flex items-center gap-4">
                            <div
                                class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center text-green-600">
                                <span class="material-symbols-outlined">trending_up</span>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-[#8a8060] uppercase tracking-wider">Lượt xem tháng này</p>
                                <p class="text-2xl font-black text-[#181611] dark:text-white">
                                    {{ number_format($viewsThisMonth) }}</p>
                            </div>
                        </div>
                        <div
                            class="bg-white dark:bg-[#2c2818] p-5 rounded-xl border border-[#e6e3db] dark:border-[#3d3a30] shadow-sm flex items-center gap-4">
                            <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center text-blue-600">
                                <span class="material-symbols-outlined">category</span>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-[#8a8060] uppercase tracking-wider">Danh mục hoạt động</p>
                                <p class="text-2xl font-black text-[#181611] dark:text-white">{{ $totalCategories }}</p>
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

        document.querySelectorAll('.toggle-status').forEach(function(checkbox) {
            checkbox.addEventListener('change', function() {
                let postId = this.dataset.id;

                fetch(`/admin/posts/toggle-status/${postId}`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (!data.success) {
                            alert('Có lỗi xảy ra!');
                        }
                    });
            });
        });
    </script>
@endsection
