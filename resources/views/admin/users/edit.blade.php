@extends('admin.layout')

@section('title', 'Chỉnh sửa người dùng - Bee Phone Admin')

@section('content')
<main class="flex-1 flex flex-col px-4 py-10 max-w-[900px] mx-auto w-full">
    <nav class="flex items-center gap-2 text-sm text-[#8a8060] mb-6">
        <a class="hover:text-primary transition-colors" href="/admin/users">Người dùng</a>
        <span class="material-symbols-outlined text-sm">chevron_right</span>
        <span class="text-[#181611] font-bold">Chỉnh sửa người dùng</span>
    </nav>

    <div class="bg-white rounded-2xl shadow-sm border border-[#e6e3db] overflow-hidden">
        <div class="p-8 border-b border-[#f5f3f0]">
            <h1 class="text-3xl font-black text-[#181611]">Chỉnh sửa người dùng: {{ $user->name }}</h1>
            <p class="text-[#8a8060] mt-1">Cập nhật thông tin tài khoản và điều chỉnh phân quyền truy cập hệ thống.</p>
        </div>

        <form class="p-8 space-y-10" method="POST" action="/admin/users/{{ $user->id }}">
            @csrf
            @method('PUT')

            <div class="flex flex-col items-center gap-4">
                <div class="relative group">
                    <div class="size-32 rounded-full border-4 border-white shadow-md bg-[#f5f3f0] flex items-center justify-center overflow-hidden">
                        @if($user->avatar_url ?? false)
                        <img alt="User Avatar" class="w-full h-full object-cover" src="{{ $user->avatar_url }}" />
                        @else
                        <span class="material-symbols-outlined text-5xl text-[#8a8060]">person</span>
                        @endif
                    </div>
                    <button class="absolute bottom-0 right-0 size-10 bg-primary rounded-full flex items-center justify-center shadow-lg border-2 border-white hover:scale-110 transition-transform" type="button">
                        <span class="material-symbols-outlined text-white text-lg">photo_camera</span>
                    </button>
                </div>
                <div class="text-center">
                    <p class="text-sm font-bold text-[#181611]">Ảnh đại diện hiện tại</p>
                    <p class="text-xs text-[#8a8060]">Nhấn vào camera để thay đổi.</p>
                </div>
            </div>

            <div class="space-y-6">
                <h3 class="text-lg font-bold flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">badge</span>
                    Thông tin tài khoản
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-sm font-bold text-[#8a8060] uppercase">Họ và tên</label>
                        <input class="w-full h-12 border-[#e6e3db] rounded-xl focus:ring-primary focus:border-primary transition-all px-4 @error('name') border-red-300 @enderror" name="name" value="{{ old('name', $user->name) }}" type="text" required />
                        @error('name')
                        <p class="text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-bold text-[#8a8060] uppercase">Số điện thoại</label>
                        <input class="w-full h-12 border-[#e6e3db] rounded-xl focus:ring-primary focus:border-primary transition-all px-4 @error('phone') border-red-300 @enderror" name="phone" value="{{ old('phone', $user->phone) }}" type="tel" />
                        @error('phone')
                        <p class="text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div class="space-y-2">
                    <label class="text-sm font-bold text-[#8a8060] uppercase">Email</label>
                    <input class="w-full h-12 border-[#e6e3db] rounded-xl focus:ring-primary focus:border-primary transition-all px-4 bg-gray-50 @error('email') border-red-300 @enderror" name="email" value="{{ old('email', $user->email) }}" type="email" required />
                    @error('email')
                    <p class="text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6"></div>
            </div>

            <div class="space-y-6">
                <h3 class="text-lg font-bold flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">admin_panel_settings</span>
                    Phân quyền &amp; Vai trò
                </h3>
                <div class="space-y-2">
                    <label class="text-sm font-bold text-[#8a8060] uppercase">Vai trò người dùng</label>
                    <select class="w-full h-12 border-[#e6e3db] rounded-xl focus:ring-primary focus:border-primary transition-all px-4 bg-white" name="role">
                        @foreach($roles as $r)
                        <option value="{{ $r }}" {{ (old('role', $user->role ?? 'user') === $r) ? 'selected' : '' }}>{{ ucfirst($r) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="bg-background-light p-5 rounded-xl border border-[#e6e3db]">
                    <p class="text-xs font-bold text-[#8a8060] mb-3 uppercase tracking-wider">Quyền hạn đang áp dụng</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div class="flex items-center gap-2 text-sm text-[#181611]">
                            <span class="material-symbols-outlined text-green-600 text-lg">check_circle</span>
                            Tiếp nhận và xử lý khiếu nại
                        </div>
                        <div class="flex items-center gap-2 text-sm text-[#181611]">
                            <span class="material-symbols-outlined text-green-600 text-lg">check_circle</span>
                            Quản lý thông tin khách hàng
                        </div>
                        <div class="flex items-center gap-2 text-sm text-[#181611]">
                            <span class="material-symbols-outlined text-green-600 text-lg">check_circle</span>
                            Xem lịch sử đơn hàng
                        </div>
                        <div class="flex items-center gap-2 text-sm text-[#181611]">
                            <span class="material-symbols-outlined text-green-600 text-lg">check_circle</span>
                            Tạo phiếu hỗ trợ (Ticket)
                        </div>
                    </div>
                </div>
            </div>

            <div class="pt-6 border-t border-[#f5f3f0] flex flex-col sm:flex-row items-center gap-4">
                <div class="flex-shrink-0">
                    <button class="flex items-center gap-2 text-red-500 hover:text-red-700 font-bold text-sm px-4 py-2 bg-red-50 rounded-xl transition-colors border border-red-100" type="button">
                        <span class="material-symbols-outlined text-lg">person_off</span>
                        Khóa tài khoản
                    </button>
                </div>
                <div class="flex-1 w-full flex flex-col sm:flex-row gap-4 justify-end">
                    <a class="min-w-[120px] h-12 rounded-xl border border-[#d1cfc7] text-[#181611] font-bold text-sm hover:bg-[#f5f3f0] transition-colors flex items-center justify-center" href="/admin/users">
                        Hủy
                    </a>
                    <button class="flex-[1.5] sm:max-w-[300px] h-12 rounded-xl bg-primary text-[#181611] font-bold text-sm shadow-md shadow-primary/20 hover:opacity-90 transition-all flex items-center justify-center gap-2" type="submit">
                        <span class="material-symbols-outlined">save</span>
                        Cập nhật thông tin
                    </button>
                </div>
            </div>
        </form>
    </div>
</main>

<footer class="mt-10 py-10 px-10 border-t border-[#e6e3db] flex justify-between items-center text-[#8a8060] text-sm">
    <p>© 2024 Bee Phone Admin System. All rights reserved.</p>
    <div class="flex gap-6">
        <a class="hover:text-primary transition-colors" href="#">Hướng dẫn sử dụng</a>
        <a class="hover:text-primary transition-colors" href="#">Bảo mật hệ thống</a>
        <a class="hover:text-primary transition-colors" href="#">Hỗ trợ kỹ thuật</a>
    </div>
</footer>

@endsection