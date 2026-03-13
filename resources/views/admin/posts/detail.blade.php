@extends('admin.layouts.app')
@section('content')
    <!DOCTYPE html>

    <html class="light" lang="vi">

    <head>
        <meta charset="utf-8" />
        <meta content="width=device-width, initial-scale=1.0" name="viewport" />
        <title>Chi tiết bài viết</title>
        <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
        <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@200;300;400;500;600;700;800&amp;display=swap"
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
                            "display": ["Manrope"]
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
            .material-symbols-outlined {
                font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            }
        </style>
    </head>

    <body class="bg-background-light dark:bg-background-dark font-display text-slate-900 dark:text-slate-100">
        <div class="relative flex h-auto min-h-screen w-full flex-col group/design-root overflow-x-hidden">
            <div class="layout-container flex h-full grow flex-col">
                <!-- Main Content Area -->
                <main class="flex-1 w-full px-10 py-8">
                    <div class="">
                        <!-- Breadcrumbs & Back Button -->
                        <div class="flex flex-col gap-4 mb-8">
                            <div class="flex flex-wrap items-center justify-between gap-4">
                                <a href="{{ route('admin.posts.index') }}">
                                    <button
                                        class="flex items-center gap-2 px-4 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm font-bold hover:bg-slate-50 dark:hover:bg-slate-700 transition-all shadow-sm">
                                        <span class="material-symbols-outlined">arrow_back</span>
                                        <span>Quay lại danh sách</span>
                                    </button>
                                </a>
                                <a href="{{ route('admin.posts.edit', $post->id) }}">
                                    <div class="flex gap-2">
                                        <button
                                            class="flex items-center gap-2 px-4 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm font-bold text-slate-600 dark:text-slate-300 hover:text-primary">
                                            <span class="material-symbols-outlined">edit</span>
                                            <span>Chỉnh sửa</span>
                                        </button>
                                    </div>
                                </a>
                            </div>
                        </div>
                        <!-- Article Header Section -->
                        <div
                            class="bg-white dark:bg-slate-900 rounded-xl p-6 lg:p-10 shadow-sm border border-slate-100 dark:border-slate-800 mb-8">
                            <div class="flex flex-col gap-6">
                                <div class="flex flex-wrap items-center gap-3">
                                    <span
                                        class="px-3 py-1 bg-primary text-slate-900 text-xs font-bold uppercase tracking-wider rounded-full">Sản
                                        phẩm mới</span>
                                    <span
                                        class="px-3 py-1 bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400 text-xs font-bold uppercase tracking-wider rounded-full flex items-center gap-1">
                                        <span class="size-2 bg-green-500 rounded-full"></span>
                                        Đang xuất bản
                                    </span>
                                </div>
                                <h1 class="text-3xl lg:text-5xl font-black text-slate-900 dark:text-white leading-tight">
                                    {{ $post->title }}</h1>
                                <div
                                    class="flex flex-wrap items-center gap-6 py-6 border-y border-slate-100 dark:border-slate-800">
                                    <div class="flex items-center gap-3">
                                        <div class="size-10 rounded-full bg-slate-200 overflow-hidden"
                                            data-alt="Ảnh tác giả"
                                            style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuAYX_6Lfybc828LBmjQ0aWJFBNPaJIiEcMn0heDRzFHWBRwT29QaeQu1VUn5PQp3PrhrhClYHkCbzEBg6Ixb2XkNorkiL9H7W_zP_yhUlaeMCcBseLFqidkjAeJcXuHlPbFz-Rpjpi7JlxPYkvqgBm3NLLj0kOaw73RwI1XmE1MDgIW0gYSWXMHgn82qaqm3WmJpTtN6ATAj8N3bamxSC-4zSp9GG1aQW0DWwYU8bJuIv5j4uvXzp3SFtjDXI-k2DdLKVqp1urbDks'); background-size: cover;">
                                        </div>
                                        <div>
                                            <p class="text-xs text-slate-500 font-medium">Tác giả</p>
                                            <p class="text-sm font-bold text-slate-900 dark:text-slate-100">
                                                {{ $post->user->name }}
                                            </p>
                                        </div>
                                    </div>
                                    <div>
                                        <p class="text-xs text-slate-500 font-medium">Ngày đăng</p>
                                        <p class="text-sm font-bold text-slate-900 dark:text-slate-100">
                                            {{ $post->created_at->format('d/m/Y') }}
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-slate-500 font-medium">Danh mục</p>
                                        <p class="text-sm font-bold text-slate-900 dark:text-slate-100">
                                            {{ $post->category->name }}
                                        </p>
                                    </div>

                                </div>
                                <!-- Featured Image -->
                                <div class="rounded-xl overflow-hidden aspect-video w-full relative group">
                                    <img alt="Bee Phone Pro Banner" class="w-full h-full object-cover"
                                        data-alt="Ảnh bìa bài viết điện thoại Bee Phone Pro"
                                        src="/uploads/posts/{{ $post->thumbnail }}" />
                                </div>
                            </div>
                            <!-- Article Body Content -->

                            <div class="mt-10 prose prose-slate dark:prose-invert max-w-none">
                                {!! $post->content !!}
                            </div>
                        </div>
                    </div>
                </main>
            </div>
        </div>
    </body>

    </html>
@endsection
