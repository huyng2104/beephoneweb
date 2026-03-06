@extends('admin.layouts.app')
@section('content')
    <!DOCTYPE html>
    <html class="light" lang="vi">

    <head>
        <meta charset="utf-8" />
        <meta content="width=device-width, initial-scale=1.0" name="viewport" />
        <title>Quản lý danh mục Bee Phone Admin</title>
        <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
        <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@200..800&amp;display=swap" rel="stylesheet" />
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
        <style type="text/tailwindcss">
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

    <body class="bg-background-light dark:bg-background-dark min-h-screen">
        <div class="layout-container flex flex-col">
            <header
                class="flex items-center justify-between whitespace-nowrap border-b border-solid border-[#e6e3db] dark:border-[#3d3a30] bg-white dark:bg-[#2c2818] px-10 py-3 sticky top-0 z-50">
                <div class="flex items-center gap-8">
                    <div class="flex items-center gap-4 text-[#181611] dark:text-white">
                        <div class="size-8 bg-primary rounded-lg flex items-center justify-center">
                            <span class="material-symbols-outlined text-white">smartphone</span>
                        </div>
                        <h2 class="text-[#181611] dark:text-white text-lg font-bold leading-tight tracking-[-0.015em]">Bee
                            Phone Admin</h2>
                    </div>
                    <div class="flex items-center gap-6 ml-4">
                        <a class="text-[#181611] dark:text-white text-sm font-medium leading-normal hover:text-primary transition-colors"
                            href="">Bảng điều khiển</a>
                        <a class="text-[#181611] dark:text-white text-sm font-medium leading-normal hover:text-primary transition-colors"
                            href="#">Sản phẩm</a>
                        <a class="text-[#181611] dark:text-white text-sm font-medium leading-normal hover:text-primary transition-colors"
                            href="#">Đơn hàng</a>
                        <a class="text-primary text-sm font-bold leading-normal border-b-2 border-primary pb-1"
                            href="#">Bài viết</a>
                        <a class="text-[#181611] dark:text-white text-sm font-medium leading-normal hover:text-primary transition-colors"
                            href="#">Cài đặt</a>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <div class="flex gap-2">
                        <button
                            class="flex items-center justify-center rounded-lg h-10 w-10 bg-[#f5f3f0] dark:bg-[#3d3a30] text-[#181611] dark:text-white">
                            <span class="material-symbols-outlined">notifications</span>
                        </button>
                        <button
                            class="flex items-center justify-center rounded-lg h-10 w-10 bg-[#f5f3f0] dark:bg-[#3d3a30] text-[#181611] dark:text-white">
                            <span class="material-symbols-outlined">account_circle</span>
                        </button>
                    </div>
                    <div class="bg-center bg-no-repeat aspect-square bg-cover rounded-full size-10 border-2 border-primary"
                        style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuBYWX_6bt7h3oWIt9FfTjpViYiNu_Lrd0FxbDwbKTuw69hlwIldss5yjQsKOSeFohjmymqFkiFz0lVuI1erEOBMKZpm_Td0kFFPnk6iJhN7iWUahyGGB_0dTqsSPCUAs-JoDvF1bAB8jiXa3u21LwgIoh_5QV5XCXYhwT5UpkdMWO07diecwhyV4ntMwTCenmZd8Xd06s8Iqt_veQjsQIz3x_ohWoxkyyUIlk9U40wuirt_LQdppxmiTye_LlZgXlpl3m3jQbu8bZU");'>
                    </div>
                </div>
            </header>
            <main class="flex-1 px-10 py-8">
                <div class="max-w-[1280px] mx-auto space-y-6">
                    <div class="flex flex-wrap justify-between items-end gap-3">
                        <div class="flex min-w-72 flex-col gap-1">
                            <p class="text-[#181611] dark:text-white text-4xl font-black leading-tight tracking-[-0.033em]">
                                Quản lý Danh mục</p>
                            <p class="text-[#8a8060] dark:text-[#b5ae98] text-base font-normal leading-normal">Tổ chức các
                                chủ đề tin tức, hướng dẫn và đánh giá sản phẩm</p>
                        </div>
                        <button
                            class="flex min-w-[200px] items-center justify-center overflow-hidden rounded-lg h-12 px-6 bg-primary text-[#181611] text-sm font-bold leading-normal tracking-[0.015em] hover:bg-[#e0b020] transition-all shadow-sm">
                            <span class="material-symbols-outlined mr-2 font-bold">add_circle</span>
                            <span class="truncate">Thêm danh mục mới</span>
                        </button>
                    </div>
                    <div
                        class="bg-white dark:bg-[#2c2818] rounded-xl shadow-sm overflow-hidden border border-[#e6e3db] dark:border-[#3d3a30]">
                        <div class="flex border-b border-[#e6e3db] dark:border-[#3d3a30] px-6 gap-8">
                            <a class="flex flex-col items-center justify-center border-b-[3px] border-transparent text-[#8a8060] dark:text-[#b5ae98] pb-[13px] pt-4 hover:text-primary transition-colors"
                                href="{{ route('admin.posts.index') }}">
                                <p class="text-sm font-bold leading-normal tracking-[0.015em]">Danh sách bài viết</p>
                            </a>
                            <a class="flex flex-col items-center justify-center border-b-[3px] border-primary text-[#181611] dark:text-white pb-[13px] pt-4"
                                href="#">
                                <p class="text-sm font-bold leading-normal tracking-[0.015em]">Quản lý danh mục</p>
                            </a>
                            <a class="flex flex-col items-center justify-center border-b-[3px] border-transparent text-[#8a8060] dark:text-[#b5ae98] pb-[13px] pt-4 hover:text-primary transition-colors"
                                href="#">
                                <p class="text-sm font-bold leading-normal tracking-[0.015em]">Bình luận</p>
                            </a>
                        </div>
                        <div class="p-6 space-y-4">
                            <div class="flex flex-wrap items-center justify-between gap-4">
                                <div class="flex flex-1 min-w-[300px] max-w-md">
                                    <div class="relative w-full">
                                        <span
                                            class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#8a8060]">search</span>
                                        <input
                                            class="w-full pl-10 pr-4 py-2 rounded-lg border border-[#e6e3db] dark:border-[#3d3a30] bg-[#f8f8f5] dark:bg-[#3d3a30] text-[#181611] dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/50"
                                            placeholder="Tìm kiếm danh mục..." type="text" />
                                    </div>
                                </div>
                                <div class="flex gap-2">
                                    <button
                                        class="flex h-9 items-center justify-center gap-x-2 rounded-lg bg-[#f5f3f0] dark:bg-[#3d3a30] text-[#181611] dark:text-white px-4 border border-[#e6e3db] dark:border-[#3d3a30]">
                                        <span class="material-symbols-outlined text-sm">unfold_more</span>
                                        <p class="text-xs font-bold leading-normal uppercase">Thu gọn tất cả</p>
                                    </button>
                                </div>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="w-full text-left border-collapse">
                                    <thead>
                                        <tr
                                            class="text-[#8a8060] dark:text-[#b5ae98] border-b border-[#e6e3db] dark:border-[#3d3a30]">
                                            <th class="py-4 px-2 font-bold text-xs uppercase tracking-wider">Tên danh mục
                                            </th>
                                            <th class="py-4 px-2 font-bold text-xs uppercase tracking-wider">Đường dẫn
                                                (Slug)</th>
                                            <th class="py-4 px-2 font-bold text-xs uppercase tracking-wider text-center">Số
                                                bài viết</th>
                                            <th class="py-4 px-2 font-bold text-xs uppercase tracking-wider text-center">
                                                Hiển thị</th>
                                            <th class="py-4 px-2 font-bold text-xs uppercase tracking-wider text-right">Thao
                                                tác</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-[#e6e3db] dark:divide-[#3d3a30]">
                                        <tr class="hover:bg-primary/5 dark:hover:bg-primary/5 transition-colors group">
                                            <td class="py-4 px-2">
                                                <div class="flex items-center gap-3">
                                                    <span
                                                        class="material-symbols-outlined text-[#8a8060] cursor-pointer">expand_more</span>
                                                    <div class="flex items-center gap-2">
                                                        <span class="material-symbols-outlined text-primary">folder</span>
                                                        <p class="text-[#181611] dark:text-white font-bold text-sm">Tin công
                                                            nghệ</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="py-4 px-2 text-sm text-[#8a8060]">/tin-cong-nghe</td>
                                            <td
                                                class="py-4 px-2 text-center text-sm font-bold text-[#181611] dark:text-white">
                                                124</td>
                                            <td class="py-4 px-2 text-center">
                                                <label class="relative inline-flex items-center cursor-pointer">
                                                    <input checked="" class="sr-only toggle-checkbox" type="checkbox" />
                                                    <div
                                                        class="toggle-label w-10 h-5 bg-gray-300 rounded-full transition-colors relative">
                                                        <div
                                                            class="toggle-dot absolute left-1 top-1 bg-white w-3 h-3 rounded-full transition-transform">
                                                        </div>
                                                    </div>
                                                </label>
                                            </td>
                                            <td class="py-4 px-2 text-right">
                                                <div
                                                    class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                                    <button
                                                        class="p-1.5 hover:bg-primary/20 text-[#181611] dark:text-white rounded-md"
                                                        title="Chỉnh sửa">
                                                        <span class="material-symbols-outlined">edit</span>
                                                    </button>
                                                    <button class="p-1.5 hover:bg-red-50 text-red-600 rounded-md"
                                                        title="Xóa">
                                                        <span class="material-symbols-outlined">delete</span>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr class="hover:bg-primary/5 dark:hover:bg-primary/5 transition-colors group">
                                            <td class="py-4 px-2 pl-12">
                                                <div class="flex items-center gap-2">
                                                    <span class="text-[#e6e3db]">—</span>
                                                    <span
                                                        class="material-symbols-outlined text-[#8a8060] text-lg">subdirectory_arrow_right</span>
                                                    <p class="text-[#181611] dark:text-white font-medium text-sm">Tin tức
                                                        Apple</p>
                                                </div>
                                            </td>
                                            <td class="py-4 px-2 text-sm text-[#8a8060]">/tin-tuc-apple</td>
                                            <td
                                                class="py-4 px-2 text-center text-sm font-bold text-[#181611] dark:text-white">
                                                45</td>
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
                                                <div
                                                    class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                                    <button
                                                        class="p-1.5 hover:bg-primary/20 text-[#181611] dark:text-white rounded-md">
                                                        <span class="material-symbols-outlined">edit</span>
                                                    </button>
                                                    <button class="p-1.5 hover:bg-red-50 text-red-600 rounded-md">
                                                        <span class="material-symbols-outlined">delete</span>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr class="hover:bg-primary/5 dark:hover:bg-primary/5 transition-colors group">
                                            <td class="py-4 px-2">
                                                <div class="flex items-center gap-3">
                                                    <span
                                                        class="material-symbols-outlined text-[#8a8060] cursor-pointer">expand_more</span>
                                                    <div class="flex items-center gap-2">
                                                        <span class="material-symbols-outlined text-primary">folder</span>
                                                        <p class="text-[#181611] dark:text-white font-bold text-sm">Đánh
                                                            giá</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="py-4 px-2 text-sm text-[#8a8060]">/danh-gia</td>
                                            <td
                                                class="py-4 px-2 text-center text-sm font-bold text-[#181611] dark:text-white">
                                                82</td>
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
                                                <div
                                                    class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                                    <button
                                                        class="p-1.5 hover:bg-primary/20 text-[#181611] dark:text-white rounded-md">
                                                        <span class="material-symbols-outlined">edit</span>
                                                    </button>
                                                    <button class="p-1.5 hover:bg-red-50 text-red-600 rounded-md">
                                                        <span class="material-symbols-outlined">delete</span>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr class="hover:bg-primary/5 dark:hover:bg-primary/5 transition-colors group">
                                            <td class="py-4 px-2">
                                                <div class="flex items-center gap-3">
                                                    <span
                                                        class="material-symbols-outlined text-[#8a8060] cursor-pointer">expand_more</span>
                                                    <div class="flex items-center gap-2">
                                                        <span class="material-symbols-outlined text-primary">folder</span>
                                                        <p class="text-[#181611] dark:text-white font-bold text-sm">Mẹo hay
                                                        </p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="py-4 px-2 text-sm text-[#8a8060]">/meo-hay</td>
                                            <td
                                                class="py-4 px-2 text-center text-sm font-bold text-[#181611] dark:text-white">
                                                56</td>
                                            <td class="py-4 px-2 text-center">
                                                <label class="relative inline-flex items-center cursor-pointer">
                                                    <input class="sr-only toggle-checkbox" type="checkbox" />
                                                    <div
                                                        class="toggle-label w-10 h-5 bg-gray-300 rounded-full transition-colors relative">
                                                        <div
                                                            class="toggle-dot absolute left-1 top-1 bg-white w-3 h-3 rounded-full transition-transform">
                                                        </div>
                                                    </div>
                                                </label>
                                            </td>
                                            <td class="py-4 px-2 text-right">
                                                <div
                                                    class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                                    <button
                                                        class="p-1.5 hover:bg-primary/20 text-[#181611] dark:text-white rounded-md">
                                                        <span class="material-symbols-outlined">edit</span>
                                                    </button>
                                                    <button class="p-1.5 hover:bg-red-50 text-red-600 rounded-md">
                                                        <span class="material-symbols-outlined">delete</span>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div
                            class="bg-white dark:bg-[#2c2818] p-5 rounded-xl border border-[#e6e3db] dark:border-[#3d3a30] shadow-sm flex items-center gap-4">
                            <div
                                class="w-12 h-12 rounded-full bg-primary/20 flex items-center justify-center text-primary">
                                <span class="material-symbols-outlined">category</span>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-[#8a8060] uppercase tracking-wider">Tổng danh mục</p>
                                <p class="text-2xl font-black text-[#181611] dark:text-white">12</p>
                            </div>
                        </div>
                        <div
                            class="bg-white dark:bg-[#2c2818] p-5 rounded-xl border border-[#e6e3db] dark:border-[#3d3a30] shadow-sm flex items-center gap-4">
                            <div
                                class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center text-green-600">
                                <span class="material-symbols-outlined">article</span>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-[#8a8060] uppercase tracking-wider">Nhiều bài nhất</p>
                                <p class="text-lg font-black text-[#181611] dark:text-white">Tin công nghệ</p>
                            </div>
                        </div>
                        <div
                            class="bg-white dark:bg-[#2c2818] p-5 rounded-xl border border-[#e6e3db] dark:border-[#3d3a30] shadow-sm flex items-center gap-4">
                            <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center text-blue-600">
                                <span class="material-symbols-outlined">new_releases</span>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-[#8a8060] uppercase tracking-wider">Mới thêm tháng này</p>
                                <p class="text-2xl font-black text-[#181611] dark:text-white">3</p>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>

    </body>

    </html>
@endsection
