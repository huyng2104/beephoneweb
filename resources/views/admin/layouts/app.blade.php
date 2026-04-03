<!DOCTYPE html>
<html class="light" lang="vi">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />

    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#ffc105",
                        "background-light": "#f8f8f5",
                        "background-dark": "#231e0f",
                    },
                    fontFamily: {
                        "display": ["Inter", "sans-serif"]
                    },
                },
            },
        }
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .sidebar-item-active { background-color: #ffc105; color: #231e0f; }
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>

    @stack('styles')
    @stack('css')

    {{-- @vite(['resources/css/app.css', 'resources/js/app.js']) --}}
    @vite(['resources/js/app.js'])
</head>

<body class="bg-background-light dark:bg-background-dark font-display text-slate-900 dark:text-slate-100">
    <div class="flex min-h-screen">
        @include('admin.layouts.sidebar')
        <main class="flex-1 ml-64">
            @include('admin.layouts.header')
            @yield('content')
        </main>
    </div>

    <script type="module">
        document.addEventListener('DOMContentLoaded', function () {
            const currentUserId = {{ auth()->check() ? auth()->id() : 'null' }};
            if (!currentUserId) return;

            const bellBtn = document.getElementById('bell-icon-btn');
            const bellDropdown = document.getElementById('bell-dropdown');
            const bellWrapper = document.getElementById('notification-wrapper');
            const bellCount = document.getElementById('bell-count');
            const bellList = document.getElementById('bell-list');

            if(!bellBtn) return;

            bellBtn.addEventListener('click', () => bellDropdown.classList.toggle('hidden'));
            document.addEventListener('click', (e) => {
                if (!bellWrapper.contains(e.target)) bellDropdown.classList.add('hidden');
            });

            // Hàm vẽ HTML cho 1 thông báo
            function renderNotification(id, title, message, url, isRead = false) {
                // Đã đọc thì nền trắng mờ, Chưa đọc thì nền vàng
                let bgClass = isRead ? 'bg-white dark:bg-slate-900 opacity-60' : 'bg-primary/10 font-bold';
                let iconColor = isRead ? 'text-slate-400' : 'text-primary';

                return `
                    <a href="${url}" data-id="${id}" class="noti-item block p-4 border-b border-slate-100 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800 transition-all ${bgClass}">
                        <div class="flex gap-3">
                            <div class="mt-0.5 ${iconColor}"><span class="material-symbols-outlined text-xl">notifications_active</span></div>
                            <div>
                                <p class="text-sm text-slate-800 dark:text-white">${title}</p>
                                <p class="text-xs text-slate-600 dark:text-slate-400 mt-1 font-normal">${message}</p>
                            </div>
                        </div>
                    </a>
                `;
            }

            // Fetch tải dữ liệu
            fetch('/api/notifications/unread')
                .then(res => res.json())
                .then(data => {
                    if (data.notifications.length > 0) {
                        if(data.count > 0) {
                            bellCount.innerText = data.count;
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
                        bellList.innerHTML = '<div class="p-8 text-center text-sm text-slate-400">Không có thông báo mới</div>';
                    }
                })
                .catch(err => console.error(err));

            // Đánh dấu đã đọc khi click
            document.addEventListener('click', function (e) {
                let item = e.target.closest('.noti-item');
                if (item) {
                    let notiId = item.getAttribute('data-id');
                    let csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    if (notiId && notiId !== 'temp') {
                        fetch(`/api/notifications/${notiId}/read`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                                'Content-Type': 'application/json'
                            }
                        }).then(() => {
                            item.classList.remove('bg-primary/10', 'font-bold');
                            item.classList.add('bg-white', 'opacity-60');

                            let count = parseInt(bellCount.innerText);
                            if (count > 1) {
                                bellCount.innerText = count - 1;
                            } else {
                                bellCount.classList.add('hidden');
                            }
                        });
                    }
                }
            });

            // Bắt sự kiện Real-time
            if (window.Echo) {
                window.Echo.channel('order-tracker')
                    .listen('.status-updated', (e) => {
                        if (e.targetUserId == currentUserId) {
                            let count = parseInt(bellCount.innerText) || 0;
                            bellCount.innerText = count + 1;
                            bellCount.classList.remove('hidden');

                            if(bellList.innerHTML.includes('Không có thông báo mới')) bellList.innerHTML = '';

                            // Nhét cái mới lên đầu, đảm bảo chỉ giữ 5 cái hiển thị
                            bellList.insertAdjacentHTML('afterbegin', renderNotification('temp', e.title, e.message, e.url, false));
                            if (bellList.children.length > 5) {
                                bellList.lastElementChild.remove();
                            }

                            // Toast Notification
                            let toast = document.createElement('div');
                            toast.className = 'fixed top-20 right-8 bg-white border-l-4 border-primary shadow-2xl rounded-xl p-4 z-[9999] flex gap-3 transform transition-all translate-x-[150%] duration-500 min-w-[300px]';
                            toast.innerHTML = `
                                <div class="text-primary mt-1"><span class="material-symbols-outlined text-2xl">local_shipping</span></div>
                                <div>
                                    <h4 class="font-bold text-sm text-slate-900">${e.title}</h4>
                                    <p class="text-xs text-slate-600 mt-1">${e.message}</p>
                                </div>
                            `;
                            document.body.appendChild(toast);
                            setTimeout(() => toast.classList.remove('translate-x-[150%]'), 100);
                            setTimeout(() => {
                                toast.classList.add('translate-x-[150%]');
                                setTimeout(() => toast.remove(), 500);
                            }, 5000);
                        }
                    });
            }
        });
    </script>

    @stack('js')
</body>
</html>
