<!DOCTYPE html>
<html class="light" lang="vi">
<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Bee Phone')</title>

    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />

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
                        "display": ["Space Grotesk", "sans-serif"]
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
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        body {
            font-family: "Space Grotesk", sans-serif;
        }
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        .ai-sparkle {
            background: linear-gradient(90deg, #f4c025, #fff, #f4c025);
            background-size: 200% auto;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: shine 3s linear infinite;
        }
        @keyframes shine {
            to {
                background-position: 200% center;
            }
        }
        @keyframes fade-in-up {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>

    @stack('styles')

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-background-light dark:bg-background-dark text-[#181611] dark:text-white transition-colors duration-200">

    {{-- Header --}}
    @include('client.layouts.header')

    {{-- Content --}}
    @yield('content')

    {{-- Footer --}}
    @include('client.layouts.footer')

    {{-- Chatbot --}}
    @include('client.layouts.chatbot')

    @auth
    <script type="module">
        document.addEventListener('DOMContentLoaded', function () {
            const currentUserId = {{ auth()->id() }};
            const bellBtn = document.getElementById('client-bell-btn');
            const bellDropdown = document.getElementById('client-bell-dropdown');
            const bellWrapper = document.getElementById('client-notification-wrapper');
            const bellCount = document.getElementById('client-bell-count');
            const bellList = document.getElementById('client-bell-list');

            if(!bellBtn) return;

            // Đóng mở bảng thông báo
            bellBtn.addEventListener('click', () => bellDropdown.classList.toggle('hidden'));
            document.addEventListener('click', (e) => {
                if (!bellWrapper.contains(e.target)) bellDropdown.classList.add('hidden');
            });

            // Giao diện 1 thẻ thông báo
            function renderNotification(id, title, message, url, isRead = false) {
                let bgClass = isRead ? 'bg-white dark:bg-[#221e10] opacity-70' : 'bg-primary/10 dark:bg-primary/20 font-bold';
                let iconColor = isRead ? 'text-gray-400' : 'text-primary';
                return `
                    <a href="${url}" data-id="${id}" class="noti-item block p-4 border-b border-gray-100 dark:border-white/10 hover:bg-gray-50 dark:hover:bg-white/5 transition-all ${bgClass}">
                        <div class="flex gap-3">
                            <div class="mt-0.5 ${iconColor}"><span class="material-symbols-outlined text-xl">local_shipping</span></div>
                            <div>
                                <p class="text-sm text-[#181611] dark:text-white">${title}</p>
                                <p class="text-xs text-gray-600 dark:text-gray-400 mt-1 font-normal">${message}</p>
                            </div>
                        </div>
                    </a>
                `;
            }

            // Gọi API lấy thông báo
            fetch('/api/notifications/unread')
                .then(res => res.json())
                .then(data => {
                    if (data.notifications && data.notifications.length > 0) {
                        if(data.count > 0) {
                            bellCount.innerText = data.count > 99 ? '99+' : data.count;
                            bellCount.classList.remove('hidden');
                        } else {
                            bellCount.classList.add('hidden');
                        }
                        let html = '';
                        data.notifications.forEach(n => {
                            let isRead = n.read_at !== null;
                            html += renderNotification(n.id, n.data.title, n.data.message, n.data.url, isRead);
                        });
                        bellList.innerHTML = html;
                    } else {
                        bellList.innerHTML = '<div class="p-8 text-center text-sm text-gray-400">Không có thông báo mới</div>';
                    }
                });

            // Click đánh dấu đã đọc
            document.addEventListener('click', function (e) {
                let item = e.target.closest('.noti-item');
                if (item && bellWrapper.contains(item)) {
                    let notiId = item.getAttribute('data-id');
                    let csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    if (notiId && notiId !== 'temp') {
                        fetch(`/api/notifications/${notiId}/read`, {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json' }
                        }).then(() => {
                            item.classList.remove('bg-primary/10', 'dark:bg-primary/20', 'font-bold');
                            item.classList.add('bg-white', 'dark:bg-[#221e10]', 'opacity-70');

                            let countText = bellCount.innerText;
                            let count = parseInt(countText.replace('+', ''));
                            if (count > 1) {
                                bellCount.innerText = count - 1;
                            } else {
                                bellCount.classList.add('hidden');
                            }
                        });
                    }
                }
            });

            // Bắt sự kiện Real-time và bắn Toast
            if (window.Echo) {
                window.Echo.channel('order-tracker')
                    .listen('.status-updated', (e) => {
                        if (e.targetUserId == currentUserId) {
                            // Cập nhật số đỏ trên chuông
                            let countText = bellCount.innerText;
                            let count = parseInt(countText.replace('+', '')) || 0;
                            bellCount.innerText = count + 1 > 99 ? '99+' : count + 1;
                            bellCount.classList.remove('hidden');

                            // Rớt thông báo vào danh sách dropdown
                            if(bellList.innerHTML.includes('Không có thông báo mới')) bellList.innerHTML = '';
                            bellList.insertAdjacentHTML('afterbegin', renderNotification('temp', e.title, e.message, e.url, false));
                            if (bellList.children.length > 5) bellList.lastElementChild.remove();

                            // ==========================================
                            // HIỆU ỨNG TOAST TRƯỢT RA TRONG 8 GIÂY
                            // ==========================================
                            let toast = document.createElement('div');
                            toast.className = 'fixed bottom-10 right-4 md:right-8 bg-white dark:bg-[#221e10] border-l-4 border-primary shadow-[0_20px_50px_rgba(8,_112,_184,_0.1)] dark:shadow-[0_20px_50px_rgba(0,_0,_0,_0.5)] rounded-xl p-5 z-[9999] flex gap-4 transform transition-all translate-x-[150%] duration-700 min-w-[320px] max-w-sm cursor-default';

                            toast.innerHTML = `
                                <div class="text-primary mt-1 animate-bounce"><span class="material-symbols-outlined text-3xl">notifications_active</span></div>
                                <div class="flex-1 pr-4">
                                    <h4 class="font-bold text-sm text-[#181611] dark:text-white leading-tight">${e.title}</h4>
                                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-1.5 leading-relaxed">${e.message}</p>
                                </div>
                                <button class="absolute top-3 right-3 text-gray-400 hover:text-red-500 transition-colors close-toast-btn">
                                    <span class="material-symbols-outlined text-[18px]">close</span>
                                </button>
                            `;
                            document.body.appendChild(toast);

                            // Trượt ra
                            setTimeout(() => toast.classList.remove('translate-x-[150%]'), 100);

                            // Hàm trượt vào và xóa
                            const removeToast = () => {
                                toast.classList.add('translate-x-[150%]');
                                setTimeout(() => toast.remove(), 700);
                            };

                            // Gắn nút tắt và tự động tắt sau 8s
                            toast.querySelector('.close-toast-btn').addEventListener('click', removeToast);
                            setTimeout(removeToast, 8000);
                        }
                    });
            }
        });
    </script>
    @endauth

    @stack('js')
</body>
</html>
