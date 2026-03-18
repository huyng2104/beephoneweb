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
