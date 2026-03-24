@extends('admin.layouts.app')

@section('title', 'Chi tiết người dùng')

@section('content')
    <main class="max-w-[1200px] mx-auto w-full p-4 md:p-8">
        <!-- Profile Overview Header -->
        <div class="bg-white dark:bg-slate-900 rounded-xl p-6 mb-8 border border-primary/10 shadow-sm">
            <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-6">
                <div class="flex flex-col sm:flex-row items-center sm:items-start gap-6">
                    <div class="relative group">
                        {{-- Khối logic lấy 2 chữ cái đầu của Tên (Hỗ trợ tiếng Việt có dấu) --}}
                        @php
                            $words = explode(' ', trim($user->name));
                            $initials = mb_substr($words[0], 0, 1, 'UTF-8'); // Chữ cái của họ/từ đầu tiên
                            if (count($words) > 1) {
                                $initials .= mb_substr(end($words), 0, 1, 'UTF-8'); // Chữ cái của tên/từ cuối cùng
                            }
                            $initials = mb_strtoupper($initials, 'UTF-8'); // Viết hoa toàn bộ (vd: vt -> VT)
                        @endphp

                        {{-- Đã thay cụm 'ring-4 ring-primary...' thành 'border-4 border-black' --}}
                        <div class="bg-center bg-no-repeat aspect-square bg-cover rounded-full w-32 h-32  shadow-lg overflow-hidden flex items-center justify-center relative"
                            data-alt="Ảnh chân dung người dùng {{ $user->name }}" {{-- Chỉ load ảnh nền khi user thực sự có avatar --}}
                            @if ($user->avatar) style="background-image: url('{{ Storage::url($user->avatar) }}');" @endif>

                            {{-- Nếu KHÔNG có avatar, hiển thị khối chữ cái --}}
                            @if (!$user->avatar)
                                <div
                                    class="w-full h-full flex items-center justify-center bg-slate-200 dark:bg-slate-700 text-slate-900 dark:text-white text-4xl  shadow-inner">
                                    {{ $initials }}
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="flex flex-col justify-center text-center sm:text-left">
                        <div class="flex items-center justify-center sm:justify-start gap-3">
                            <h1 class="text-slate-900 dark:text-slate-100 text-3xl font-bold">{{ $user->name }}</h1>
                            <span
                                class="px-2 py-0.5 bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400 text-xs font-bold rounded">{{ $user->user_status }}</span>
                        </div>
                        <p class="text-slate-500 dark:text-slate-400 text-base mt-1">ID: <span
                                class="font-mono">{{ $user->id }}</span> • Thành viên từ:
                            {{ $user->created_at->format('d/m/Y') }}</p>
                        <div class="flex flex-wrap justify-center sm:justify-start gap-4 mt-3">
                            <div class="flex items-center gap-1 text-slate-600 dark:text-slate-400 text-sm">
                                <span class="material-symbols-outlined text-primary text-lg">mail</span>
                                {{ $user->email }}
                            </div>
                            <div class="flex items-center gap-1 text-slate-600 dark:text-slate-400 text-sm">
                                <span class="material-symbols-outlined text-primary text-lg">call</span> {{ $user->phone }}
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="flex flex-col sm:flex-row gap-3 mt-10">
                <a href="{{ route('admin.users.edit', $user->id) }}">
                    <button
                        class="flex items-center justify-center gap-2 rounded-lg h-11 px-6 bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-sm font-bold hover:bg-slate-200 transition-colors">
                        <span class="material-symbols-outlined text-lg">edit</span>
                        <span>Chỉnh sửa</span>
                    </button>
                </a>
                @if (Auth::user()->id != $user->id)
                    @if ($user->status == 'banned')
                        <form action="{{ route('admin.user.unblock', $user->id) }}" method="POST">
                            @csrf
                            <button onclick="return(confirm('Bạn có muốn mở khóa không'))"
                                class="flex items-center justify-center gap-2 rounded-lg h-11 px-6 bg-slate-100 dark:bg-slate-800 text-red-600 text-sm font-bold hover:bg-red-50 transition-colors border border-transparent hover:border-red-200">
                                <span class="material-symbols-outlined text-lg">block</span>
                                <span>Mở khóa tài khoản</span>
                            </button>
                        </form>
                    @else
                        <form action="{{ route('admin.user.block', $user->id) }}"
                            onclick="return(confirm('Bạn có muốn khóa không'))" method="post">
                            @csrf
                            <button
                                class="flex items-center justify-center gap-2 rounded-lg h-11 px-6 bg-slate-100 dark:bg-slate-800 text-red-600 text-sm font-bold hover:bg-red-50 transition-colors border border-transparent hover:border-red-200">
                                <span class="material-symbols-outlined text-lg">block</span>
                                <span>Khóa tài khoản</span>
                            </button>
                        </form>
                    @endif
                @endif
                <form action="{{ route('admin.resetPw', $user->id) }}" method="POST">
                    @csrf
                    <button onclick="return(confirm('Bạn có muốn reset mật khẩu không'))"
                        class="flex items-center justify-center gap-2 rounded-lg h-11 px-6 bg-primary text-slate-900 text-sm font-bold hover:opacity-90 transition-opacity">
                        <span class="material-symbols-outlined text-lg">lock_reset</span>
                        <span>Reset mật khẩu</span>
                    </button>
                </form>
            </div>
        </div>
        <div class="bg-white dark:bg-slate-900 rounded-xl p-6 mb-8 border border-primary/10 shadow-sm">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-8">
                <div class="space-y-6">
                    <h3 class="text-lg font-bold flex items-center gap-2 text-slate-900 dark:text-slate-100">
                        <span class="w-1.5 h-6 bg-primary rounded-full"></span> Thông tin cá nhân
                    </h3>
                    <div class="grid grid-cols-1 gap-4">
                        <div class="flex flex-col gap-1">
                            <p class="text-slate-500 dark:text-slate-400 text-xs font-bold uppercase tracking-wider">
                                Họ và tên</p>
                            <p class="text-slate-900 dark:text-slate-100 text-base font-semibold">{{ $user->name }}</p>
                        </div>
                        <div class="flex flex-col gap-1">
                            <p class="text-slate-500 dark:text-slate-400 text-xs font-bold uppercase tracking-wider">
                                Giới tính</p>
                            <p class="text-slate-900 dark:text-slate-100 text-base">
                                {{ $user->gender == null ? 'Chưa có giới tính' : $user->gender }}</p>
                        </div>
                        <div class="flex flex-col gap-1">
                            <p class="text-slate-500 dark:text-slate-400 text-xs font-bold uppercase tracking-wider">
                                Ngày sinh</p>
                            <p class="text-slate-900 dark:text-slate-100 text-base">
                                {{ $user->birthday == null ? 'Chưa có ngày sinh' : $user->birthday }}</p>
                        </div>
                    </div>
                </div>
                <div class="space-y-6">
                    <h3 class="text-lg font-bold flex items-center gap-2 text-slate-900 dark:text-slate-100">
                        <span class="w-1.5 h-6 bg-primary rounded-full"></span> Liên hệ &amp; Địa chỉ
                    </h3>
                    <div class="grid grid-cols-1 gap-4">
                        <div class="flex flex-col gap-1">
                            <p class="text-slate-500 dark:text-slate-400 text-xs font-bold uppercase tracking-wider">
                                Email liên hệ</p>
                            <p class="text-slate-900 dark:text-slate-100 text-base font-semibold">{{ $user->email }}</p>
                        </div>
                        <div class="flex flex-col gap-1">
                            <p class="text-slate-500 dark:text-slate-400 text-xs font-bold uppercase tracking-wider">
                                Số điện thoại</p>
                            <p class="text-slate-900 dark:text-slate-100 text-base">
                                {{ $user->phone == null ? 'Chưa có số điện thoại' : $user->phone }}</p>
                        </div>
                        <div class="flex flex-col gap-1">
                            <p class="text-slate-500 dark:text-slate-400 text-xs font-bold uppercase tracking-wider">
                                Địa chỉ thường trú</p>
                            <p class="text-slate-900 dark:text-slate-100 text-base">
                                {{ $user->address == null ? 'Chưa có địa chỉ' : $user->address }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Content Tabs & Sections -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 mb-8">
            <!-- Tabs and Main Info (Left/Main Column) -->
            <div class="lg:col-span-12">
                <div class="bg-white dark:bg-slate-900 rounded-xl overflow-hidden border border-primary/10 shadow-sm">

                    <div class="p-8">

                        <!-- Info Section -->

                        <!-- Role Section (Summary) -->
                        <div class="">
                            <h3 class="text-lg font-bold flex items-center gap-2 text-slate-900 dark:text-slate-100 mb-6">
                                <span class="w-1.5 h-6 bg-primary rounded-full"></span> Vai trò &amp; Phân quyền
                            </h3>
                            <div class="flex flex-col md:flex-row gap-8">
                                <div
                                    class="flex-1 p-6 bg-background-light dark:bg-slate-800/50 rounded-xl border border-primary/5">
                                    <div class="flex items-center gap-3 mb-4">
                                        <span
                                            class="material-symbols-outlined text-primary p-2 bg-primary/10 rounded-lg">verified_user</span>
                                        <div>
                                            <p class="text-xs font-bold uppercase text-slate-500">Vai trò chính</p>
                                            <p class="text-lg font-bold text-slate-900 dark:text-slate-100">
                                                {{ $user->role->name }}</p>
                                        </div>
                                    </div>
                                    <div class="space-y-2">
                                        <p class="text-sm text-slate-600 dark:text-slate-400">Các quyền cụ thể:</p>
                                        @switch($user->role->name)
                                            @case('staff')
                                                <div class="flex flex-wrap gap-2">
                                                    @foreach ($user->permissions as $permission)
                                                        <span
                                                            class="px-2 py-1 bg-white dark:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs rounded border border-slate-200 dark:border-slate-600">•
                                                            {{ $permission->name }}</span>
                                                    @endforeach
                                                </div>
                                            @break

                                            @case('admin')
                                                <span
                                                    class="px-2 py-1 bg-white dark:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs rounded border border-slate-200 dark:border-slate-600">•
                                                    Toàn quyền truy cập hệ thống</span>
                                            @break

                                            @default
                                                <span
                                                    class="px-2 py-1 bg-white dark:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs rounded border border-slate-200 dark:border-slate-600">•
                                                    Mua hàng</span>
                                        @endswitch
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-slate-900 rounded-xl p-6 mb-8 border border-primary/10 shadow-sm">
            <div class="flex-1">
                <p class="text-sm font-bold text-slate-900 dark:text-slate-100 mb-4">Lịch sử
                    hoạt động gần đây</p>
                <div class="space-y-4">
                    @foreach ($user->activityLogs()->latest()->take(5)->get() as $log)
                        <div class="flex gap-4">
                            <div class="relative flex flex-col items-center">
                                <div class="size-3 bg-slate-300 rounded-full z-10"></div>
                                <div class="w-0.5 h-full bg-slate-200 dark:bg-slate-700 absolute top-2">
                                </div>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-slate-700 dark:text-slate-300">
                                    {{ $log->description }}
                                    @if ($log->model)
                                        - <a class="text-blue-500" href="#">{{ $log->model->name ?? '' }}</a>
                                    @endif
                                </p>

                                <p class="text-xs text-slate-500">
                                    {{ $log->created_at->format('H:i') }}, Ngày {{ $log->created_at->format('d/m/Y') }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                    {{-- @dd($user->activityLogs) --}}
                    <button class="text-primary text-xs font-bold hover:underline">Xem tất cả
                        lịch sử</button>
                </div>
            </div>
        </div>
    </main>
    @if (session('new_password'))
        <div id="password-modal" class="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-[9999] p-4">

            <div class="bg-white rounded-2xl shadow-2xl max-w-sm w-full p-8 text-center border border-gray-100">

                <div class="mx-auto flex items-center justify-center h-14 w-14 rounded-full bg-green-100 mb-4">
                    <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                        </path>
                    </svg>
                </div>

                <h3 class="text-xl font-bold text-gray-900 mb-1">Mật khẩu mới</h3>
                <p class="text-sm text-gray-500 mb-6">Đã cập nhật thành công cho người dùng.</p>

                <div class="bg-gray-50 border-2 border-dashed border-blue-200 p-4 rounded-xl mb-6">
                    <span class="text-2xl font-mono font-bold text-blue-600 tracking-wider">
                        {{ session('new_password') }}
                    </span>
                </div>

                <button onclick="document.getElementById('password-modal').remove()"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-xl transition duration-200">
                    Đã hiểu và lưu lại
                </button>
            </div>
        </div>
    @endif

    <style>
        /* Hiệu ứng hiện lên nhẹ nhàng */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: scale(0.95);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        #password-modal>div {
            animation: fadeIn 0.3s ease-out;
        }
    </style>
@endsection
