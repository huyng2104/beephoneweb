@extends('admin.layouts.app')
@section('content')
    <form action="{{ route('admin.post-categories.update', $category->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <!DOCTYPE html>

        <html lang="vi">

        <head>
            <meta charset="utf-8" />
            <meta content="width=device-width, initial-scale=1.0" name="viewport" />
            <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
            <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@200..800&amp;display=swap" rel="stylesheet" />
            <link
                href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght@100..700,0..1&amp;display=swap"
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
            <title>Thêm Danh Mục Tin Tức - Bee Phone Admin</title>
        </head>

        <body class="bg-background-light dark:bg-background-dark font-display text-slate-900 dark:text-slate-100">
            <div class="">
                <!-- Sidebar Navigation -->
                <!-- Main Content -->
                <main class="flex-1 flex flex-col">
                    <!-- Top Header -->

                    <!-- Page Content -->
                    <div class="p-8 max-w-5xl mx-auto w-full">
                        <!-- Breadcrumbs -->
                        <!-- Header Title Section -->
                        <div class="mb-8">
                            <h1 class="text-3xl font-extrabold text-slate-900 dark:text-white mb-2">Chỉnh sửa Danh mục</h1>
                            <p class="text-slate-500 dark:text-slate-400">Cập nhật thông tin danh mục tin tức trên hệ thống
                                Bee Phone.</p>
                        </div>
                        <!-- Form Card -->
                        <div
                            class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 p-8">
                            <form class="space-y-8">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-5">
                                    <!-- Category Name -->
                                    <div class="space-y-2">
                                        <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Tên danh mục
                                            <span class="text-red-500">*</span></label>
                                        <input
                                            class="w-full px-4 py-3 rounded-lg border border-slate-200 dark:border-slate-700 dark:bg-slate-800 focus:ring-2 focus:ring-primary focus:border-primary transition-all outline-none"
                                            type="text" name="name" id="name" class="form-control"
                                            value="{{ $category->name }}">
                                    </div>
                                    <!-- Parent Category -->
                                    <div class="space-y-2">
                                        <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Đường dẫn
                                            (Slug)</label>
                                        <input
                                            class="w-full px-4 py-3 rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-slate-800/50 text-slate-500 cursor-not-allowed transition-all outline-none"
                                            type="text" name="slug" id="slug" class="form-control" disabled
                                            value="{{ $category->slug }}">
                                    </div>
                                </div>
                                <!-- Category Icon Selector -->
                                <!-- Display Toggle -->
                                <div
                                    class="flex items-center justify-between p-4 bg-slate-50 dark:bg-slate-800/50 rounded-lg">
                                    <div>
                                        <h4 class="text-sm font-bold text-slate-800 dark:text-slate-200">Hiển thị danh mục
                                        </h4>
                                        <p class="text-xs text-slate-500">Cho phép danh mục này xuất hiện trên trang chủ và
                                            menu điều hướng.</p>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input checked="" class="sr-only peer" type="checkbox" value="" />
                                        <div
                                            class="w-11 h-6 bg-slate-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary">
                                        </div>
                                    </label>
                                </div>
                                <!-- Form Actions -->
                                <div
                                    class="flex items-center justify-end gap-4 pt-6 border-t border-slate-100 dark:border-slate-800">
                                    <button
                                        class="px-6 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 font-bold text-sm hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors"
                                        type="button">
                                        Hủy bỏ
                                    </button>
                                    <button
                                        class="px-8 py-2.5 rounded-lg bg-primary text-slate-900 font-extrabold text-sm shadow-md shadow-primary/20 hover:bg-opacity-90 transition-all"
                                        type="submit">Lưu thay đổi</button>
                                </div>
                            </form>
                        </div>
                        <!-- Info Help -->
                    </div>
                    <!-- Footer -->
                    <footer class="mt-auto py-6 px-8 border-t border-slate-200 dark:border-slate-800 text-center">
                        <p class="text-xs text-slate-400">© 2024 Bee Phone Admin. Tất cả quyền được bảo lưu.</p>
                    </footer>
                </main>
            </div>
        </body>

        </html>
    </form>
@endsection
