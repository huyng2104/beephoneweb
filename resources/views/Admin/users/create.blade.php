@extends('admin.layout')

@section('title', 'Thêm người dùng mới - Bee Phone Admin')

@section('content')
<main class="flex-1 flex flex-col px-4 py-10 max-w-[900px] mx-auto w-full">
    <nav class="flex items-center gap-2 text-sm text-[#8a8060] mb-6">
        <a class="hover:text-primary transition-colors" href="/admin/users">Người dùng</a>
        <span class="material-symbols-outlined text-sm">chevron_right</span>
        <span class="text-[#181611] font-bold">Thêm người dùng mới</span>
    </nav>

    <div class="bg-white rounded-2xl shadow-sm border border-[#e6e3db] overflow-hidden">
        <div class="p-8 border-b border-[#f5f3f0]">
            <h1 class="text-3xl font-black text-[#181611]">Thêm người dùng mới</h1>
            <p class="text-[#8a8060] mt-1">Điền các thông tin bên dưới để khởi tạo tài khoản hệ thống cho nhân viên hoặc khách hàng.</p>
        </div>

        <form class="p-8 space-y-10" method="POST" action="/admin/users">
            @csrf

            <div class="flex flex-col items-center gap-4">
                <div class="relative group">
                    <div class="size-32 rounded-full border-4 border-white shadow-md bg-[#f5f3f0] flex items-center justify-center overflow-hidden">
                        <span class="material-symbols-outlined text-5xl text-[#8a8060]">person</span>
                    </div>
                    <button class="absolute bottom-0 right-0 size-10 bg-primary rounded-full flex items-center justify-center shadow-lg border-2 border-white hover:scale-110 transition-transform" type="button">
                        <span class="material-symbols-outlined text-white text-lg">photo_camera</span>
                    </button>
                </div>
                <div class="text-center">
                    <p class="text-sm font-bold text-[#181611]">Ảnh đại diện</p>
                    <p class="text-xs text-[#8a8060]">Hỗ trợ JPG, PNG. Tối đa 2MB.</p>
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
                        <input class="w-full h-12 border-[#e6e3db] rounded-xl focus:ring-primary focus:border-primary transition-all px-4 @error('name') border-red-300 @enderror" name="name" value="{{ old('name') }}" placeholder="Ví dụ: Nguyễn Văn A" type="text" required />
                        @error('name')
                        <p class="text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-bold text-[#8a8060] uppercase">Số điện thoại</label>
                        <input class="w-full h-12 border-[#e6e3db] rounded-xl focus:ring-primary focus:border-primary transition-all px-4 @error('phone') border-red-300 @enderror" name="phone" value="{{ old('phone') }}" placeholder="09xx xxx xxx" type="tel" />
                        @error('phone')
                        <p class="text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div class="space-y-2">
                    <label class="text-sm font-bold text-[#8a8060] uppercase">Email</label>
                    <input class="w-full h-12 border-[#e6e3db] rounded-xl focus:ring-primary focus:border-primary transition-all px-4 @error('email') border-red-300 @enderror" name="email" value="{{ old('email') }}" placeholder="email@beephone.vn" type="email" required />
                    @error('email')
                    <p class="text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2 relative">
                        <label class="text-sm font-bold text-[#8a8060] uppercase">Mật khẩu</label>
                        <div class="relative">
                            <input class="w-full h-12 border-[#e6e3db] rounded-xl focus:ring-primary focus:border-primary transition-all px-4 @error('password') border-red-300 @enderror" name="password" id="password" type="password" required />
                            <button class="password-toggle absolute right-4 top-1/2 -translate-y-1/2 text-[#8a8060] hover:text-[#181611]" type="button" data-target="#password" aria-label="Hiện mật khẩu">
                                <span class="material-symbols-outlined" data-icon>visibility</span>
                            </button>
                        </div>
                        @error('password')
                        <p class="text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="space-y-2 relative">
                        <label class="text-sm font-bold text-[#8a8060] uppercase">Xác nhận mật khẩu</label>
                        <div class="relative">
                            <input class="w-full h-12 border-[#e6e3db] rounded-xl focus:ring-primary focus:border-primary transition-all px-4" name="password_confirmation" id="password_confirmation" type="password" required />
                            <button class="password-toggle absolute right-4 top-1/2 -translate-y-1/2 text-[#8a8060] hover:text-[#181611]" type="button" data-target="#password_confirmation" aria-label="Hiện mật khẩu">
                                <span class="material-symbols-outlined" data-icon>visibility</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <h3 class="text-lg font-bold flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">admin_panel_settings</span>
                    Phân quyền &amp; Vai trò
                </h3>
                <div class="space-y-2">
                    <label class="text-sm font-bold text-[#8a8060] uppercase">Chọn vai trò</label>
                    <select class="w-full h-12 border-[#e6e3db] rounded-xl focus:ring-primary focus:border-primary transition-all px-4 bg-white" name="role">
                        @foreach($roles as $r)
                        <option value="{{ $r }}" {{ old('role') === $r ? 'selected' : '' }}>{{ ucfirst($r) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="bg-background-light p-5 rounded-xl border border-[#e6e3db]">
                    <p class="text-xs font-bold text-[#8a8060] mb-3 uppercase tracking-wider">Quyền hạn tương ứng</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div class="flex items-center gap-2 text-sm text-[#181611]">
                            <span class="material-symbols-outlined text-green-600 text-lg">check_circle</span>
                            Toàn quyền quản lý hệ thống
                        </div>
                        <div class="flex items-center gap-2 text-sm text-[#181611]">
                            <span class="material-symbols-outlined text-green-600 text-lg">check_circle</span>
                            Quản lý nhân sự và phân quyền
                        </div>
                        <div class="flex items-center gap-2 text-sm text-[#181611]">
                            <span class="material-symbols-outlined text-green-600 text-lg">check_circle</span>
                            Xem và xuất báo cáo tài chính
                        </div>
                        <div class="flex items-center gap-2 text-sm text-[#181611]">
                            <span class="material-symbols-outlined text-green-600 text-lg">check_circle</span>
                            Quản lý kho hàng &amp; danh mục
                        </div>
                    </div>
                </div>
            </div>

            <div class="pt-6 border-t border-[#f5f3f0] flex flex-col sm:flex-row gap-4">
                <a class="flex-1 h-12 rounded-xl border border-[#d1cfc7] text-[#181611] font-bold text-sm hover:bg-[#f5f3f0] transition-colors flex items-center justify-center" href="/admin/users">
                    Hủy
                </a>
                <button class="flex-[2] h-12 rounded-xl bg-primary text-[#181611] font-bold text-sm shadow-md shadow-primary/20 hover:opacity-90 transition-all flex items-center justify-center gap-2" type="submit">
                    <span class="material-symbols-outlined">person_add</span>
                    Tạo người dùng
                </button>
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
@push('scripts')
<script>
    document.querySelectorAll('.password-toggle').forEach(function(button) {
        button.addEventListener('click', function() {
            var selector = button.getAttribute('data-target');
            var input = selector ? document.querySelector(selector) : null;
            if (!input) {
                return;
            }

            var show = input.getAttribute('type') === 'password';
            input.setAttribute('type', show ? 'text' : 'password');

            var icon = button.querySelector('[data-icon]');
            if (icon) {
                icon.textContent = show ? 'visibility_off' : 'visibility';
            }
            button.setAttribute('aria-label', show ? 'Ẩn mật khẩu' : 'Hiện mật khẩu');
        });
    });
</script>
@endpush
@endsection