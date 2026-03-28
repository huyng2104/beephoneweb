<!DOCTYPE html>

<html lang="vi">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Xác nhận OTP - Bee Phone</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&amp;display=swap" rel="stylesheet" />
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
                        "primary": "#f4c025",
                        "background-light": "#f8f8f5",
                        "background-dark": "#221e10",
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

        /* Chrome, Safari, Edge, Opera */
        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        /* Firefox */
        input[type=number] {
            -moz-appearance: textfield;
        }
    </style>
</head>

<body class="bg-background-light dark:bg-background-dark text-slate-900 dark:text-slate-100 min-h-screen flex flex-col">
    <!-- Top Navigation Bar -->
    <header
        class="w-full px-6 py-4 flex items-center justify-between bg-white dark:bg-background-dark border-b border-slate-200 dark:border-slate-800">
        <div class="flex items-center gap-2">
            <div class="bg-primary p-1.5 rounded-lg flex items-center justify-center text-slate-900">
                <span class="material-symbols-outlined text-2xl font-bold">rocket_launch</span>
            </div>
            <h2 class="text-xl font-bold tracking-tight">Bee Phone</h2>
        </div>
        <a class="flex items-center gap-2 px-3 py-2 text-sm font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors"
            href="#">
            <span class="material-symbols-outlined text-lg">arrow_back</span>
            Quay lại
        </a>
    </header>
    <!-- Main Content Container -->

    <main class="flex-grow flex items-center justify-center p-6">
        @if (session('success') || session('error'))

            <div id="custom-sweet-alert"
                class="fixed inset-0 z-[9999] flex items-center justify-center bg-slate-900/60 backdrop-blur-sm transition-opacity duration-300 opacity-0">

                <div id="alert-box"
                    class="bg-white dark:bg-slate-800 rounded-3xl p-8 max-w-sm w-full mx-4 shadow-2xl transform transition-all duration-300 scale-75 opacity-0 flex flex-col items-center text-center">

                    @if (session('success'))
                        <div
                            class="w-20 h-20 bg-green-100 dark:bg-green-500/20 text-green-500 rounded-full flex items-center justify-center mb-5 animate-[bounce_1s_ease-in-out]">
                            <span class="material-symbols-outlined text-5xl">check_circle</span>
                        </div>
                        <h3 class="text-2xl font-black text-slate-800 dark:text-white mb-2">Thành công!</h3>
                        <p class="text-slate-600 dark:text-slate-400 mb-6 font-medium">{{ session('success') }}</p>

                        <button onclick="closeAlert()"
                            class="w-full bg-green-500 hover:bg-green-600 text-white font-bold py-3.5 rounded-xl transition-all active:scale-95 shadow-lg shadow-green-500/30">
                            Tuyệt vời
                        </button>
                    @endif

                    @if (session('error'))
                        <div
                            class="w-20 h-20 bg-red-100 dark:bg-red-500/20 text-red-500 rounded-full flex items-center justify-center mb-5 animate-[pulse_1s_ease-in-out]">
                            <span class="material-symbols-outlined text-5xl">warning</span>
                        </div>
                        <h3 class="text-2xl font-black text-slate-800 dark:text-white mb-2">Ôi hỏng!</h3>
                        <p class="text-slate-600 dark:text-slate-400 mb-6 font-medium">
                            @if (session('error'))
                                {{ session('error') }}
                            @else
                                Thông tin nhập vào chưa chính xác. Vui lòng kiểm tra lại!
                            @endif
                        </p>

                        <button onclick="closeAlert()"
                            class="w-full bg-red-500 hover:bg-red-600 text-white font-bold py-3.5 rounded-xl transition-all active:scale-95 shadow-lg shadow-red-500/30">
                            Đóng lại
                        </button>
                    @endif

                </div>
            </div>

            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    const overlay = document.getElementById('custom-sweet-alert');
                    const alertBox = document.getElementById('alert-box');

                    if (overlay && alertBox) {
                        // 1. Hiệu ứng "Pop-up" mượt mà ngay khi trang vừa tải xong
                        requestAnimationFrame(() => {
                            overlay.classList.remove('opacity-0');
                            overlay.classList.add('opacity-100');

                            alertBox.classList.remove('scale-75', 'opacity-0');
                            alertBox.classList.add('scale-100', 'opacity-100');
                        });

                        // 2. Tự động đóng sau 5 giây (5000ms)
                        setTimeout(() => {
                            closeAlert();
                        }, 5000);
                    }
                });

                // Hàm đóng thông báo (dùng cho nút bấm và setTimeout)
                function closeAlert() {
                    const overlay = document.getElementById('custom-sweet-alert');
                    const alertBox = document.getElementById('alert-box');

                    if (overlay && alertBox) {
                        // Đảo ngược hiệu ứng: Thu nhỏ và mờ dần
                        overlay.classList.remove('opacity-100');
                        overlay.classList.add('opacity-0');

                        alertBox.classList.remove('scale-100', 'opacity-100');
                        alertBox.classList.add('scale-75', 'opacity-0');

                        // Đợi 300ms cho hiệu ứng CSS chạy xong rồi mới xóa hẳn khỏi mã HTML
                        setTimeout(() => {
                            overlay.remove();
                        }, 300);
                    }
                }
            </script>
        @endif
        <div
            class="w-full max-w-md bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-800 p-8 md:p-12">
            <!-- Icon & Header -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-primary/10 rounded-full mb-6">
                    <span class="material-symbols-outlined text-primary text-4xl">mark_email_read</span>
                </div>
                <h1 class="text-2xl md:text-3xl font-bold text-slate-900 dark:text-white mb-3">Xác nhận mã bảo mật</h1>
                <p class="text-slate-500 dark:text-slate-400 leading-relaxed">
                    Vui lòng nhập mã 6 số chúng tôi đã gửi đến email của bạn để tiếp tục.
                </p>
            </div>
            <!-- OTP Input Fields -->
            <form action="{{ route('check_otp') }}" class="space-y-8" method="POST">
                @csrf
                <div class="flex justify-between gap-2 md:gap-4" id="otp-container">
                    <input name="otp[]"
                        class="otp-input w-full h-14 md:h-16 text-center text-2xl font-bold border-2 border-slate-200 dark:border-slate-700 rounded-xl focus:border-primary focus:ring-0 bg-transparent transition-all outline-none"
                        maxlength="1" placeholder="·" type="text" inputmode="numeric" />
                    <input name="otp[]"
                        class="otp-input w-full h-14 md:h-16 text-center text-2xl font-bold border-2 border-slate-200 dark:border-slate-700 rounded-xl focus:border-primary focus:ring-0 bg-transparent transition-all outline-none"
                        maxlength="1" placeholder="·" type="text" inputmode="numeric" />
                    <input name="otp[]"
                        class="otp-input w-full h-14 md:h-16 text-center text-2xl font-bold border-2 border-slate-200 dark:border-slate-700 rounded-xl focus:border-primary focus:ring-0 bg-transparent transition-all outline-none"
                        maxlength="1" placeholder="·" type="text" inputmode="numeric" />
                    <input name="otp[]"
                        class="otp-input w-full h-14 md:h-16 text-center text-2xl font-bold border-2 border-slate-200 dark:border-slate-700 rounded-xl focus:border-primary focus:ring-0 bg-transparent transition-all outline-none"
                        maxlength="1" placeholder="·" type="text" inputmode="numeric" />
                    <input name="otp[]"
                        class="otp-input w-full h-14 md:h-16 text-center text-2xl font-bold border-2 border-slate-200 dark:border-slate-700 rounded-xl focus:border-primary focus:ring-0 bg-transparent transition-all outline-none"
                        maxlength="1" placeholder="·" type="text" inputmode="numeric" />
                    <input name="otp[]"
                        class="otp-input w-full h-14 md:h-16 text-center text-2xl font-bold border-2 border-slate-200 dark:border-slate-700 rounded-xl focus:border-primary focus:ring-0 bg-transparent transition-all outline-none"
                        maxlength="1" placeholder="·" type="text" inputmode="numeric" />
                </div>
                @error('otp.*')
                    <span class="text-red-500">{{$message}}</span>
                @enderror
                @error('otp')
                    <span class="text-red-500">{{$message}}</span>
                @enderror
                <!-- Timer & Resend -->
                <div class="text-center space-y-2">
                    <p class="text-sm text-slate-500 dark:text-slate-400">
                        Bạn không nhận được mã?
                        <span class="font-medium text-slate-900 dark:text-white">Gửi lại mã trong 00:59</span>
                    </p>
                    <button class="text-primary font-semibold hover:underline text-sm opacity-50 cursor-not-allowed"
                        type="button">
                        Gửi lại mã ngay
                    </button>
                </div>
                <!-- Action Button -->
                <button
                    class="w-full bg-primary hover:bg-primary/90 text-slate-900 font-bold py-4 rounded-xl shadow-lg shadow-primary/20 transition-all transform active:scale-[0.98] flex items-center justify-center gap-2"
                    type="submit">
                    Xác nhận
                    <span class="material-symbols-outlined font-bold">arrow_forward</span>
                </button>
            </form>
            <!-- Footer Help -->
            <div class="mt-8 text-center">
                <p class="text-xs text-slate-400 dark:text-slate-500 uppercase tracking-widest font-semibold">
                    An toàn • Bảo mật • Nhanh chóng
                </p>
            </div>
        </div>
    </main>
    <!-- Simple Footer -->
    <footer class="py-6 text-center text-slate-400 text-sm">
        © 2024 Bee Phone Inc. Bảo lưu mọi quyền.
    </footer>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const otpInputs = document.querySelectorAll('.otp-input');

            otpInputs.forEach((input, index) => {
                // 1. Xử lý khi DÁN (Paste) mã 6 số
                input.addEventListener('paste', (e) => {
                    e.preventDefault();
                    // Lấy text vừa paste, loại bỏ khoảng trắng/chữ, chỉ lấy tối đa 6 số
                    const pastedData = e.clipboardData.getData('text').replace(/\D/g, '').slice(0,
                        otpInputs.length);

                    // Rải từng số vào từng ô tương ứng
                    pastedData.split('').forEach((char, i) => {
                        otpInputs[i].value = char;
                        if (i === pastedData.length - 1) {
                            otpInputs[i].focus(); // Focus vào ô cuối cùng được điền
                        }
                    });
                });

                // 2. Xử lý khi NHẬP từng số (tự nhảy sang phải)
                input.addEventListener('input', (e) => {
                    // Chỉ cho phép nhập số
                    input.value = input.value.replace(/\D/g, '');

                    // Nếu có giá trị và chưa phải ô cuối cùng thì focus ô tiếp theo
                    if (input.value !== '' && index < otpInputs.length - 1) {
                        otpInputs[index + 1].focus();
                    }
                });

                // 3. Xử lý khi bấm nút XÓA (Backspace) (tự lùi về trái)
                input.addEventListener('keydown', (e) => {
                    if (e.key === 'Backspace' && input.value === '' && index > 0) {
                        otpInputs[index - 1].focus();
                    }
                });
            });
        });
    </script>
</body>

</html>
