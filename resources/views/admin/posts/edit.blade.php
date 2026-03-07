@extends('admin.layouts.app')
@section('content')
    <form action="{{ route('admin.posts.update', $post->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <!DOCTYPE html>

        <html class="light" lang="vi">

        <head>
            <meta charset="utf-8" />
            <meta content="width=device-width, initial-scale=1.0" name="viewport" />
            <title>Sửa bài viết Bee Phone</title>
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
            <style>
                body {
                    font-family: 'Manrope', sans-serif;
                }

                .material-symbols-outlined {
                    font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
                }
            </style>
        </head>

        <body class="bg-background-light dark:bg-background-dark min-h-screen">
            <div class="flex flex-col min-h-screen">
                <!-- TopNavBar Component -->
                <main class="flex-1 max-w-[1280px] mx-auto w-full p-4 md:p-8">
                    <!-- PageHeading Component -->
                    <div class="flex flex-wrap justify-between items-center gap-3 mb-8">
                        <div class="flex flex-col gap-1">
                            <p
                                class="text-[#181611] dark:text-zinc-100 text-3xl font-black leading-tight tracking-[-0.033em]">
                                Sửa bài viết</p>
                            <p class="text-[#8a8060] dark:text-zinc-400 text-sm font-normal">Tạo nội dung blog công nghệ
                                chất
                                lượng cho Bee Phone</p>
                        </div>
                        <button
                            class="flex min-w-[100px] cursor-pointer items-center justify-center rounded-lg h-10 px-4 bg-primary text-[#181611] text-sm font-bold shadow-sm hover:opacity-90 transition-opacity">
                            <span>Cập nhật</span>
                        </button>

                    </div>
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        <!-- Left Column: Content Editor -->
                        <div class="lg:col-span-2 space-y-6">
                            <!-- Title Input Area -->
                            <div
                                class="bg-white dark:bg-zinc-900 rounded-xl p-6 border border-[#e6e3db] dark:border-zinc-800 shadow-sm">
                                <label class="flex flex-col w-full">
                                    <p
                                        class="text-[#181611] dark:text-zinc-200 text-sm font-bold pb-2 uppercase tracking-wider">
                                        Tiêu đề bài viết</p>
                                    <input
                                        class="form-input flex w-full min-w-0 flex-1 resize-none rounded-lg text-[#181611] dark:text-zinc-100 focus:outline-0 focus:ring-2 focus:ring-primary/50 border border-[#e6e3db] dark:border-zinc-700 bg-white dark:bg-zinc-800 h-14 placeholder:text-[#8a8060] px-4 text-xl font-bold"
                                        type="text" name="title" id="title" class="form-control"
                                        value="{{ $post->title }}">
                                </label>
                            </div>
                            <!-- Composer Component (WYSIWYG) -->
                            <div
                                class="bg-white dark:bg-zinc-900 rounded-xl border border-[#e6e3db] dark:border-zinc-800 shadow-sm overflow-hidden flex flex-col min-h-[600px]">
                                <div
                                    class="flex items-center gap-1 p-3 border-b border-[#e6e3db] dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-800/50 flex-wrap">
                                    <button
                                        class="p-2 hover:bg-zinc-200 dark:hover:bg-zinc-700 rounded transition-colors"><span
                                            class="material-symbols-outlined">format_bold</span></button>
                                    <button
                                        class="p-2 hover:bg-zinc-200 dark:hover:bg-zinc-700 rounded transition-colors"><span
                                            class="material-symbols-outlined">format_italic</span></button>
                                    <button
                                        class="p-2 hover:bg-zinc-200 dark:hover:bg-zinc-700 rounded transition-colors"><span
                                            class="material-symbols-outlined">format_underlined</span></button>
                                    <div class="w-[1px] h-6 bg-zinc-300 dark:bg-zinc-700 mx-1"></div>
                                    <button
                                        class="p-2 hover:bg-zinc-200 dark:hover:bg-zinc-700 rounded transition-colors"><span
                                            class="material-symbols-outlined">format_h1</span></button>
                                    <button
                                        class="p-2 hover:bg-zinc-200 dark:hover:bg-zinc-700 rounded transition-colors"><span
                                            class="material-symbols-outlined">format_h2</span></button>
                                    <button
                                        class="p-2 hover:bg-zinc-200 dark:hover:bg-zinc-700 rounded transition-colors"><span
                                            class="material-symbols-outlined">format_list_bulleted</span></button>
                                    <button
                                        class="p-2 hover:bg-zinc-200 dark:hover:bg-zinc-700 rounded transition-colors"><span
                                            class="material-symbols-outlined">format_list_numbered</span></button>
                                    <div class="w-[1px] h-6 bg-zinc-300 dark:bg-zinc-700 mx-1"></div>
                                    <button
                                        class="p-2 hover:bg-zinc-200 dark:hover:bg-zinc-700 rounded transition-colors"><span
                                            class="material-symbols-outlined">image</span></button>
                                    <button
                                        class="p-2 hover:bg-zinc-200 dark:hover:bg-zinc-700 rounded transition-colors"><span
                                            class="material-symbols-outlined">video_library</span></button>
                                    <button
                                        class="p-2 hover:bg-zinc-200 dark:hover:bg-zinc-700 rounded transition-colors"><span
                                            class="material-symbols-outlined">link</span></button>
                                    <button
                                        class="p-2 hover:bg-zinc-200 dark:hover:bg-zinc-700 rounded transition-colors ml-auto text-zinc-400"><span
                                            class="material-symbols-outlined">settings_ethernet</span></button>
                                </div>
                                <textarea
                                    class="form-input flex-1 w-full min-w-0 resize-none overflow-y-auto text-[#181611] dark:text-zinc-200 focus:outline-0 focus:ring-0 border-0 bg-transparent p-6 text-lg leading-relaxed placeholder:text-[#cfcaba]"
                                    name="content" rows="8" class="form-control">{{ $post->content }}</textarea>
                                <div
                                    class="px-6 py-3 border-t border-[#e6e3db] dark:border-zinc-800 flex justify-between items-center text-xs text-[#8a8060]">
                                    <span>Đã lưu nháp lúc 14:30</span>
                                    <span>345 từ</span>
                                </div>
                            </div>
                            <!-- SEO Section -->
                        </div>
                        <!-- Right Column: Settings Sidebar -->
                        <div class="space-y-6">
                            <!-- Publishing Settings Card -->
                            <div
                                class="bg-white dark:bg-zinc-900 rounded-xl p-6 border border-[#e6e3db] dark:border-zinc-800 shadow-sm">
                                <h3
                                    class="text-[#181611] dark:text-zinc-100 text-base font-bold mb-4 flex items-center gap-2">
                                    <span class="material-symbols-outlined text-sm">send</span> Thiết lập hiển thị
                                </h3>
                                <div class="space-y-4">
                                    {{-- <div class="flex items-center justify-between">
                                    <span class="text-sm font-medium text-[#181611] dark:text-zinc-300">Công khai bài
                                        viết</span>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input checked="" class="sr-only peer" type="checkbox" value="" />
                                        <div
                                            class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-zinc-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary">
                                        </div>
                                    </label>
                                </div> --}}
                                    <div class="flex items-center justify-between">

                                        <span class="text-sm font-medium text-[#181611] dark:text-zinc-300">
                                            Công khai bài viết
                                        </span>

                                        <label class="relative inline-flex items-center cursor-pointer">

                                            <!-- hidden để gửi 0 nếu không check -->
                                            <input type="hidden" name="status" value="0">

                                            <input type="checkbox" name="status" value="1" checked
                                                class="sr-only peer">

                                            <div
                                                class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-zinc-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary">
                                            </div>
                                            {{-- <select name="status" class="form-control">

                                                <option value="1" {{ $post->status == 1 ? 'selected' : '' }}>
                                                    Hiển thị
                                                </option>

                                                <option value="0" {{ $post->status == 0 ? 'selected' : '' }}>
                                                    Ẩn
                                                </option>

                                            </select> --}}

                                        </label>

                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-medium text-[#181611] dark:text-zinc-300">Bài viết nổi
                                            bật</span>
                                        <input
                                            class="w-5 h-5 text-primary bg-zinc-100 border-[#e6e3db] rounded focus:ring-primary focus:ring-2"
                                            type="checkbox" />
                                    </div>
                                    <div class="pt-4 border-t border-[#f5f3f0] dark:border-zinc-800 flex flex-col gap-2">
                                        <p class="text-xs text-[#8a8060]"><span class="font-bold">Lần cuối lưu:</span> 1
                                            phút
                                            trước</p>
                                        <p class="text-xs text-[#8a8060]"><span class="font-bold">Tác giả:</span> Admin Bee
                                            Phone</p>
                                    </div>
                                </div>
                            </div>
                            <!-- Categories Card -->
                            <div
                                class="bg-white dark:bg-zinc-900 rounded-xl p-6 border border-[#e6e3db] dark:border-zinc-800 shadow-sm">
                                <h3 class="text-[#181611] dark:text-zinc-100 text-base font-bold mb-4">Danh mục</h3>
                                <select
                                    class="form-select w-full rounded-lg border-[#e6e3db] dark:border-zinc-700 dark:bg-zinc-800 text-sm h-11 focus:border-primary focus:ring-primary"
                                    name="post_categories_id" class="form-control">

                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ $post->post_categories_id == $category->id ? 'selected' : '' }}>

                                            {{ $category->name }}

                                        </option>
                                    @endforeach

                                </select>
                                <button class="mt-3 text-primary text-xs font-bold hover:underline flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[14px]">add_circle</span> Thêm danh mục mới
                                </button>
                            </div>
                            <!-- Featured Image Card -->
                            <div
                                class="bg-white dark:bg-zinc-900 rounded-xl p-6 border border-[#e6e3db] dark:border-zinc-800 shadow-sm">
                                <h3 class="text-[#181611] dark:text-zinc-100 text-base font-bold mb-4">Ảnh đại diện</h3>
                                <div class="mb-3">
                                    <input type="file" name="thumbnail" id="thumbnail" hidden>
                                    <div class="relative group cursor-pointer"
                                        onclick="document.getElementById('thumbnail').click()">
                                        <div
                                            class="w-full aspect-video rounded-lg bg-zinc-100 dark:bg-zinc-800 border-2 border-dashed border-[#e6e3db] dark:border-zinc-700 flex flex-col items-center justify-center overflow-hidden transition-all hover:border-primary/50">

                                            {{-- <img id="previewImage"
                                                class="absolute inset-0 w-full h-full object-cover opacity-80 hidden">

                                            <div class="relative z-10 flex flex-col items-center gap-2 p-4 text-center">

                                                <span class="material-symbols-outlined text-primary text-3xl">image</span>

                                                <p class="text-xs font-medium text-[#181611] dark:text-zinc-300">
                                                    Nhấp để thay đổi ảnh
                                                </p>
                                            </div> --}}
                                            <img src="/uploads/posts/{{ $post->thumbnail }}" id="previewImage"
                                                style="width:100%;margin-top:10px">
                                        </div>
                                    </div>
                                </div>
                                <p class="mt-2 text-[10px] text-[#8a8060] text-center italic">Định dạng JPG, PNG, WEBP. Tối
                                    đa
                                    2MB. Tỷ lệ 16:9.</p>
                            </div>
                            <!-- Danger Zone -->
                            <div class="flex justify-center p-4">
                                <button
                                    class="text-red-500 text-xs font-medium hover:text-red-600 flex items-center gap-1 transition-colors">
                                    <span class="material-symbols-outlined text-[16px]">delete</span> Xóa bài viết này
                                </button>
                            </div>
                        </div>
                    </div>
                </main>
                <footer
                    class="bg-white dark:bg-zinc-900 border-t border-[#e6e3db] dark:border-zinc-800 py-6 text-center text-xs text-[#8a8060]">
                    <p>© 2024 Bee Phone Admin. All rights reserved.</p>
                </footer>
            </div>
        </body>

        </html>
        <script>
            document.getElementById("thumbnail").addEventListener("change", function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const preview = document.getElementById("previewImage");
                        preview.src = e.target.result;
                        preview.classList.remove("hidden");
                    }
                    reader.readAsDataURL(file);
                }
            });

            document.getElementById('thumbnail').onchange = evt => {

                const [file] = evt.target.files

                if (file) {

                    const preview = document.getElementById('previewImage')

                    preview.src = URL.createObjectURL(file)

                }

            }
        </script>
    </form>
@endsection
