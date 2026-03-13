@extends('admin.layouts.app')
@section('content')
    <form action="{{ route('admin.posts.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <!DOCTYPE html>

        <html class="light" lang="vi">

        <head>
            <meta charset="utf-8" />
            <meta content="width=device-width, initial-scale=1.0" name="viewport" />
            <title>Soạn thảo bài viết</title>
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

        <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>

        <body class="bg-background-light dark:bg-background-dark min-h-screen">
            <div class="flex flex-col min-h-screen">
                <!-- TopNavBar Component -->
                <main class="flex-1 w-full p-4 md:p-8">
                    <!-- PageHeading Component -->
                    <div class="flex flex-wrap justify-between items-center gap-3 mb-8">
                        <div class="flex flex-col gap-1">
                            <p
                                class="text-[#181611] dark:text-zinc-100 text-3xl font-black leading-tight tracking-[-0.033em]">
                                Soạn thảo bài viết</p>
                            <p class="text-[#8a8060] dark:text-zinc-400 text-sm font-normal">Tạo nội dung blog công nghệ
                                chất
                                lượng cho Bee Phone</p>
                        </div>
                        <button
                            class="flex min-w-[100px] cursor-pointer items-center justify-center rounded-lg h-10 px-4 bg-primary text-[#181611] text-sm font-bold shadow-sm hover:opacity-90 transition-opacity">
                            <span>Xuất bản</span>
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
                                        placeholder="Nhập tiêu đề hấp dẫn tại đây..." type="text" name="title"
                                        id="title" class="form-control" required>
                                </label>
                            </div>
                            <!-- Composer Component (WYSIWYG) -->
                            <div
                                class="bg-white dark:bg-zinc-900 rounded-xl border border-[#e6e3db] dark:border-zinc-800 shadow-sm overflow-hidden flex flex-col min-h-[600px]">

                                {{-- <textarea
                                    class="form-input flex-1 w-full min-w-0 resize-none overflow-y-auto text-[#181611] dark:text-zinc-200 focus:outline-0 focus:ring-0 border-0 bg-transparent p-6 text-lg leading-relaxed placeholder:text-[#cfcaba]"
                                    placeholder="Bắt đầu viết nội dung bài viết của bạn tại đây..." name="content" rows="8" class="form-control"></textarea> --}}
                                <textarea id="editor" name="content"
                                    class="form-input flex-1 w-full min-w-0 resize-none overflow-y-auto text-[#181611] dark:text-zinc-200 focus:outline-0 focus:ring-0 border-0 bg-transparent p-6 text-lg leading-relaxed placeholder:text-[#cfcaba]"
                                    placeholder="Bắt đầu viết nội dung bài viết của bạn tại đây..."></textarea>

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
                                        </label>
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
                                        <option value="{{ $category->id }}">
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

                                            <img id="previewImage"
                                                class="absolute inset-0 w-full h-full object-cover opacity-80 hidden">

                                            <div class="relative z-10 flex flex-col items-center gap-2 p-4 text-center">

                                                <span class="material-symbols-outlined text-primary text-3xl">image</span>

                                                <p class="text-xs font-medium text-[#181611] dark:text-zinc-300">
                                                    Nhấp để thay đổi ảnh
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <p class="mt-2 text-[10px] text-[#8a8060] text-center italic">Định dạng JPG, PNG, WEBP. Tối
                                    đa
                                    2MB. Tỷ lệ 16:9.</p>
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
        </script>
        {{-- <script>
            ClassicEditor
                .create(document.querySelector('#editor'), {
                    ckfinder: {
                        uploadUrl: "{{ route('admin.posts.upload') }}?_token={{ csrf_token() }}"
                    },
                    toolbar: [
                        'heading',
                        '|',
                        'bold', 'italic', 'underline',
                        '|',
                        'bulletedList', 'numberedList',
                        '|',
                        'link',
                        'imageUpload',
                        '|',
                        'undo', 'redo'
                    ]
                })
                .catch(error => {
                    console.error(error);
                });
        </script> --}}
        <script>
            ClassicEditor
                .create(document.querySelector('#editor'), {

                    ckfinder: {
                        uploadUrl: "{{ route('admin.posts.upload') }}?_token={{ csrf_token() }}"
                    },

                    toolbar: [
                        'heading',
                        '|',
                        'bold', 'italic', 'underline',
                        '|',
                        'bulletedList', 'numberedList',
                        '|',
                        'imageUpload',
                        'link',
                        '|',
                        'undo', 'redo'
                    ],

                    image: {
                        toolbar: [
                            'imageStyle:inline',
                            'imageStyle:block',
                            'imageStyle:side',
                            '|',
                            'toggleImageCaption',
                            'imageTextAlternative'
                        ]
                    }
                })
                .catch(error => {
                    console.error(error);
                });
        </script>
    </form>
@endsection
