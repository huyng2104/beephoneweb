@extends('admin.layouts.app')

@section('title', 'Quản lý người dùng')

@section('content')
    <main class="flex-1 flex flex-col overflow-hidden">
        <div id="toast-container" class="fixed top-5 right-5 z-[9999] flex flex-col gap-3">

            @if (session('success') || session('error') || $errors->any())

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

                        @if (session('error') || $errors->any())
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

        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const toasts = document.querySelectorAll('.toast-message');

                toasts.forEach(function(toast) {
                    setTimeout(() => {
                        // Thêm hiệu ứng trượt sang phải trước khi xóa
                        toast.classList.add('translate-x-full', 'opacity-0');

                        // Đợi hiệu ứng chạy xong (300ms) rồi mới xóa khỏi DOM
                        setTimeout(() => {
                            toast.remove();
                        }, 300);
                    }, 3000); // 3000ms = 3 giây (Bạn có thể tăng lên nếu muốn)
                });
            });
        </script>

        <!-- Body Content -->
        <div class="flex-1 overflow-y-auto p-8 space-y-6">
            <!-- Breadcrumbs & Actions -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-black text-slate-900 dark:text-slate-100 tracking-tight">Quản lý người
                        dùng</h2>
                    <p class="text-slate-500 text-sm mt-1">Xem và quản lý tất cả tài khoản người dùng trên hệ thống
                    </p>
                </div>
                <a href="{{ route('admin.users.create') }}">
                    <button
                        class="bg-primary hover:bg-primary/90 text-slate-900 font-bold px-5 py-2.5 rounded-xl shadow-sm shadow-primary/20 flex items-center gap-2 transition-all">
                        <span class="material-symbols-outlined">person_add</span>
                        Thêm người dùng mới
                    </button>
                </a>
            </div>
            <!-- Stats Bar (Optional UI touch) -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-white dark:bg-slate-800 p-4 rounded-xl border border-slate-200 dark:border-slate-700">
                    <p class="text-slate-500 text-xs font-bold uppercase tracking-wider">Tổng người dùng</p>
                    <p class="text-2xl font-black mt-1">{{ $users->total() }}</p>
                </div>
                <div class="bg-white dark:bg-slate-800 p-4 rounded-xl border border-slate-200 dark:border-slate-700">
                    <p class="text-slate-500 text-xs font-bold uppercase tracking-wider">Khách hàng</p>
                    <p class="text-2xl font-black mt-1 text-primary">1,120</p>
                </div>
                <div class="bg-white dark:bg-slate-800 p-4 rounded-xl border border-slate-200 dark:border-slate-700">
                    <p class="text-slate-500 text-xs font-bold uppercase tracking-wider">Nhân viên</p>
                    <p class="text-2xl font-black mt-1 text-blue-500">{{ $totalStaff }}</p>
                </div>
                <div class="bg-white dark:bg-slate-800 p-4 rounded-xl border border-slate-200 dark:border-slate-700">
                    <p class="text-slate-500 text-xs font-bold uppercase tracking-wider">Bị khóa</p>
                    <p class="text-2xl font-black mt-1 text-red-500">{{ $totalBanned }}</p>
                </div>
            </div>
            <!-- Filters & Table -->
            <div
                class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                <!-- Filter Row -->
                <div class="p-4 border-b border-slate-100 dark:border-slate-700 flex flex-wrap gap-4 items-center">
                    <div class="flex-1 min-w-[300px] relative">
                        <span
                            class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">search</span>
                        <input
                            class="w-full bg-slate-50 dark:bg-slate-900 border-slate-200 dark:border-slate-700 rounded-lg pl-10 focus:ring-primary focus:border-primary text-sm"
                            placeholder="Tìm tên, email hoặc ID..." type="text" />
                    </div>
                    <div class="flex gap-2">
                        <select
                            class="bg-slate-50 dark:bg-slate-900 border-slate-200 dark:border-slate-700 rounded-lg text-sm font-medium focus:ring-primary focus:border-primary">
                            <option value="">Tất cả vai trò</option>
                            <option value="admin">Quản trị viên</option>
                            <option value="staff">Nhân viên</option>
                            <option value="customer">Khách hàng</option>
                        </select>
                        <select
                            class="bg-slate-50 dark:bg-slate-900 border-slate-200 dark:border-slate-700 rounded-lg text-sm font-medium focus:ring-primary focus:border-primary">
                            <option value="">Trạng thái</option>
                            <option value="active">Hoạt động</option>
                            <option value="locked">Bị khóa</option>
                        </select>
                        <button
                            class="bg-slate-100 dark:bg-slate-900 p-2 rounded-lg border border-slate-200 dark:border-slate-700 flex items-center justify-center text-slate-500 hover:text-primary transition-colors">
                            <span class="material-symbols-outlined">filter_list</span>
                        </button>
                    </div>
                </div>
                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead
                            class="bg-slate-50 dark:bg-slate-900/50 text-slate-500 text-xs font-bold uppercase tracking-wider">
                            <tr>
                                <th class="px-6 py-4">STT</th>
                                <th class="px-6 py-4">Người dùng</th>
                                <th class="px-6 py-4">Vai trò</th>
                                <th class="px-6 py-4">Trạng thái</th>
                                <th class="px-6 py-4">Ngày tham gia</th>
                                <th class="px-6 py-4 text-right">Hành động</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                            <!-- Row 1 -->
                            @foreach ($users as $index => $user)
                                @if (!Auth::check() || Auth::id() != $user->id)
                                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                                        <td class="px-6 py-4 text-sm font-medium text-slate-400">{{ $index + 1 }}</td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                <div
                                                    class="size-10 rounded-full bg-slate-200 overflow-hidden flex-shrink-0">
                                                    <img class="w-full h-full object-cover"
                                                        data-alt="Ảnh đại diện người dùng {{ $user->name }}"
                                                        src="{{ $user->avatar ? Storage::url($user->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=cbd5e1&color=1e293b' }}"
                                                        alt="Avatar" />
                                                </div>
                                                <div>
                                                    <p class="text-sm font-bold text-slate-900 dark:text-slate-100">
                                                        {{ $user->name }}</p>
                                                    <p class="text-xs text-slate-500">{{ $user->email }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        @php
                                            $role_color = match ($user->role->name) {
                                                'staff' => 'blue',
                                                'admin' => 'purple',
                                                default => 'slate',
                                            };
                                        @endphp
                                        <td class="px-6 py-4">
                                            <span
                                                class="px-2.5 py-1 rounded-full text-xs font-bold bg-{{ $role_color }}-100 text-{{ $role_color }}-700 dark:bg-{{ $role_color }}-900/30 dark:text-{{ $role_color }}-400">{{ $user->role->name }}</span>
                                        </td>
                                        @php
                                            $status_color = match ($user->user_status) {
                                                'Chưa kích hoạt' => 'yellow',
                                                'Bị khóa' => 'red',
                                                default => 'green',
                                            };
                                        @endphp
                                        <td class="px-6 py-4">
                                            <div
                                                class="flex items-center gap-1.5 text-{{ $status_color }}-600 dark:text-{{ $status_color }}-500">
                                                <span class="size-1.5 rounded-full bg-{{ $status_color }}-500"></span>
                                                <span class="text-xs font-bold">{{ $user->user_status }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-slate-500">
                                            {{ $user->created_at->format('d/m/Y') }}
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <div class="flex justify-end gap-2">
                                                <a href="{{ route('admin.users.show', $user->id) }}">
                                                    <button class="p-2 text-slate-400 hover:text-blue-500 transition-colors"
                                                        title="Xem chi tiết">
                                                        <span class="material-symbols-outlined text-lg">visibility</span>
                                                    </button>
                                                </a>

                                                <a href="{{ route('admin.users.edit', $user->id) }}">
                                                    <button class="p-2 text-slate-400 hover:text-primary transition-colors"
                                                        title="Chỉnh sửa">
                                                        <span class="material-symbols-outlined text-lg">edit</span>
                                                    </button>
                                                </a>

                                                @if ($user->status == 'banned')
                                                    <form action="{{ route('admin.user.unblock', $user->id) }}"
                                                        method="POST">
                                                        @csrf
                                                        <button onclick="return(confirm('Khôi phục người dùng'))"
                                                            class="p-2 text-slate-400 hover:text-green-500 transition-colors"
                                                            title="Khôi phục tài khoản">
                                                            <span class="material-symbols-outlined text-lg">restore</span>
                                                        </button>
                                                    </form>
                                                @else
                                                    <form action="{{ route('admin.user.block', $user->id) }}"
                                                        method="POST">
                                                        @csrf
                                                        <button onclick="return(confirm('Chặn người dùng'))"
                                                            class="p-2 text-slate-400 hover:text-red-500 transition-colors"
                                                            title="Khóa tài khoản">
                                                            <span class="material-symbols-outlined text-lg">block</span>
                                                        </button>
                                                    </form>
                                                @endif

                                                <form action="{{ route('admin.users.destroy', $user->id) }}"
                                                    method="post">
                                                    @csrf
                                                    @method('delete')
                                                    <button onclick="return(confirm('Xóa người dùng'))"
                                                        class="p-2 text-slate-400 hover:text-red-600 transition-colors"
                                                        title="Xóa tài khoản">
                                                        <span class="material-symbols-outlined text-lg">delete</span>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach

                        </tbody>
                    </table>
                </div>
                <!-- Pagination -->
                <div class="m-5  flex gap-2">

                    {{-- Previous --}}
                    <a href="{{ $users->previousPageUrl() }}"
                        class="size-9 flex items-center justify-center rounded-lg border border-slate-200 text-slate-400 hover:bg-slate-50">

                        <span class="material-symbols-outlined">chevron_left</span>
                    </a>

                    {{-- Page numbers --}}
                    @foreach ($users->links()->elements[0] ?? [] as $page => $url)
                        <a href="{{ $url }}"
                            class="size-9 flex items-center justify-center rounded-lg border
                            {{ $users->currentPage() == $page ? 'border-primary bg-primary text-white' : 'border-slate-200 text-slate-600 hover:bg-slate-50' }}
                            font-bold text-sm">

                            {{ $page }}
                        </a>
                    @endforeach

                    {{-- Next --}}
                    <a href="{{ $users->nextPageUrl() }}"
                        class="size-9 flex items-center justify-center rounded-lg border border-slate-200 text-slate-400 hover:bg-slate-50">

                        <span class="material-symbols-outlined">chevron_right</span>
                    </a>

                </div>
            </div>
        </div>
    </main>
@endsection
