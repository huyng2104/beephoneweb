@extends('admin.layouts.app')

@section('title', 'Thêm người dùng mới - Bee Phone Admin')

@section('content')


    <main class="flex-1 flex flex-col overflow-hidden">
        <div class="flex-1 overflow-y-auto p-8 space-y-6">
            <form action="{{ route('admin.users.update', $user->id) }}" method="post" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="mb-8">
                    <h1 class="text-3xl font-black text-slate-900 dark:text-slate-100 tracking-tight">Thêm người dùng
                        mới</h1>
                    <p class="text-slate-500 mt-1">Điền đầy đủ các thông tin bên dưới để tạo tài khoản truy cập hệ
                        thống.</p>
                </div>
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Left Side: Basic Info -->
                    <div class="lg:col-span-2 space-y-6">
                        <div
                            class="bg-white dark:bg-slate-900/50 p-6 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
                            <h3 class="text-lg font-bold text-slate-900 dark:text-slate-100 mb-6 flex items-center gap-2">
                                <span class="material-symbols-outlined text-primary">person</span>
                                Thông tin cơ bản
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Họ và
                                        tên</label>
                                    <input name="name" value="{{ old('name', $user->name) }}"
                                        class="w-full px-4 py-2 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all"
                                        placeholder="Nguyễn Văn A" type="text" />
                                    @error('name')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="space-y-2">
                                    <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Email</label>
                                    <input name="email" value="{{ old('email', $user->email) }}"
                                        class="w-full px-4 py-2 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all"
                                        placeholder="email@vi-du.com" type="text" />
                                    @error('email')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="space-y-2">
                                    <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Số điện
                                        thoại</label>
                                    <input name="phone" value="{{ old('phone', $user->phone) }}"
                                        class="w-full px-4 py-2 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all"
                                        placeholder="0123 456 789" type="text" />
                                    @error('phone')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="space-y-2">
                                    <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Vai trò</label>

                                    <select name="role" id="role"
                                        class="w-full px-4 py-2 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all">


                                        @foreach ($roles as $role)
                                            <option value="{{ $role->id }}"
                                                {{ old('role', $user->role_id ?? '') == $role->id ? 'selected' : '' }}>
                                                {{ $role->name }}
                                            </option>
                                        @endforeach


                                    </select>

                                    @error('role')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="space-y-2">
                                    <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Giới
                                        tính</label>
                                    <select name="gender"
                                        class="w-full px-4 py-2 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all">
                                        <option value="">-- Chọn giới tính --</option>
                                        <option value="male"
                                            {{ old('gender', $user->gender) == 'male' ? 'selected' : '' }}>Nam</option>
                                        <option value="female"
                                            {{ old('gender', $user->gender) == 'female' ? 'selected' : '' }}>Nữ</option>
                                        <option value="other"
                                            {{ old('gender', $user->gender) == 'other' ? 'selected' : '' }}>Khác</option>
                                    </select>
                                    @error('gender')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="space-y-2">
                                    <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Ngày
                                        sinh</label>
                                    <input name="dob"
                                        value="{{ old('dob', $user->birthday ? \Carbon\Carbon::parse($user->birthday)->format('Y-m-d') : '') }}"
                                        class="w-full px-4 py-2 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all"
                                        type="date" />
                                    @error('dob')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="space-y-2 md:col-span-2">
                                    <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Địa chỉ</label>
                                    <input name="address" value="{{ old('address', $user->address) }}"
                                        class="w-full px-4 py-2 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all"
                                        placeholder="Số nhà, tên đường, phường/xã, quận/huyện..." type="text" />
                                    @error('address')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                        </div>

                        <div
                            class="bg-white dark:bg-slate-900/50 p-6 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
                            <h3 class="text-lg font-bold text-slate-900 dark:text-slate-100 mb-6 flex items-center gap-2">
                                <span class="material-symbols-outlined text-primary">lock</span>
                                Bảo mật
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Mật
                                        khẩu</label>
                                    <div class="relative">
                                        <input name="password" value="{{ old('password') }}"
                                            class="w-full px-4 py-2 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all"
                                            placeholder="••••••••" type="password" />
                                        @error('password')
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>

                                </div>
                                <div class="space-y-2">
                                    <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Xác nhận mật
                                        khẩu</label>
                                    <input name="comfirm_password" value="{{ old('comfirm_password') }}"
                                        class="w-full px-4 py-2 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all"
                                        placeholder="••••••••" type="password" />
                                    @error('comfirm_password')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div
                            class="bg-white dark:bg-slate-900 rounded-xl p-6 shadow-sm border border-slate-200 dark:border-slate-800">
                            <h4 class="font-bold mb-4 flex items-center gap-2">
                                <span class="material-symbols-outlined text-primary">security</span>
                                Phân quyền truy cập (Quyền cấp riêng)
                            </h4>

                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">

                                @php
                                    // 🔍 1. Lấy mảng ID các quyền mà Vai trò (Role) của User ĐÃ CÓ
                                    $rolePermissionIds = [];
                                    if (isset($user) && $user->role) {
                                        // Ép kiểu toArray() để dùng hàm in_array cho siêu nhanh
                                        $rolePermissionIds = $user->role->permissions->pluck('id')->toArray();
                                    }
                                @endphp

                                @foreach ($permissions as $permission)
                                    {{-- 🛑 2. NẾU QUYỀN NÀY CHƯA CÓ TRONG ROLE THÌ MỚI HIỂN THỊ --}}
                                    @if (!in_array($permission->id, $rolePermissionIds))
                                        <label class="flex items-center gap-3 cursor-pointer group">
                                            @php
                                                // Kiểm tra xem User hiện tại có đang sở hữu quyền cấp RIÊNG này trong Database không
                                                $hasUserPermission =
                                                    isset($user) && $user->permissions->contains('id', $permission->id);

                                                // Ưu tiên lấy old() nếu form bị lỗi, ngược lại lấy quyền gốc của User
                                                // Lưu ý: Dùng is_array() an toàn hơn khi old('permissions') trả về null
                                                $oldPermissions = old('permissions');
                                                $isChecked = is_array($oldPermissions)
                                                    ? in_array($permission->id, $oldPermissions)
                                                    : $hasUserPermission;
                                            @endphp

                                            <input type="checkbox" name="permissions[]" value="{{ $permission->id }}"
                                                class="w-5 h-5 rounded border-slate-300 text-primary focus:ring-primary"
                                                {{ $isChecked ? 'checked' : '' }}>

                                            <span
                                                class="text-sm text-slate-700 dark:text-slate-300 group-hover:text-slate-900 transition-colors">
                                                {{ $permission->name }}
                                            </span>
                                        </label>
                                    @endif
                                @endforeach

                            </div>
                        </div>
                        <div class="mt-8 flex items-center gap-4 pb-12">
                            <button
                                class="px-8 py-2.5 rounded-lg bg-primary text-slate-900 font-bold hover:shadow-lg hover:shadow-primary/20 transition-all flex items-center gap-2">
                                <span class="material-symbols-outlined">person_add</span>
                                Cập nhập người dùng
                            </button>
                        </div>
                    </div>
                    <!-- Right Side: Profile & Status -->
                    <div class="space-y-6">
                        <div
                            class="bg-white dark:bg-slate-900/50 p-6 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
                            <div class="bg-white dark:bg-slate-900/50">
                                <h3 class="text-lg font-bold text-slate-900 dark:text-slate-100 mb-6">Ảnh đại diện</h3>
                                <div class="flex flex-col items-center">

                                    <label for="avatarInput"
                                        class="size-32 rounded-full border-4 border-dashed border-slate-200 dark:border-slate-700 flex flex-col items-center justify-center bg-slate-50 dark:bg-slate-800/50 hover:bg-primary/10 transition-colors cursor-pointer mb-4 group relative overflow-hidden">

                                        <input type="file" id="avatarInput" name="avatar"
                                            accept="image/png, image/jpeg, image/gif" class="hidden"
                                            onchange="previewAvatar(event)">

                                        <div id="uploadPlaceholder"
                                            class="flex flex-col items-center justify-center {{ isset($user) && $user->avatar ? 'hidden' : '' }}">
                                            <span
                                                class="material-symbols-outlined text-4xl text-slate-400 group-hover:text-primary">upload_file</span>
                                            <span class="text-xs text-slate-400 mt-2">Tải ảnh lên</span>
                                        </div>

                                        <img id="avatarPreview"
                                            src="{{ isset($user) && $user->avatar ? asset('storage/' . $user->avatar) : '' }}"
                                            alt="Ảnh xem trước"
                                            class="absolute inset-0 w-full h-full object-cover {{ isset($user) && $user->avatar ? '' : 'hidden' }}">
                                    </label>

                                    <p class="text-xs text-slate-500 text-center px-4">Định dạng JPG, PNG hoặc GIF</p>
                                </div>
                                @error('avatar')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div
                            class="bg-white dark:bg-slate-900/50 p-6 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
                            <h3 class="text-lg font-bold text-slate-900 dark:text-slate-100 mb-6">Trạng thái tài khoản
                            </h3>
                            <div class="flex items-center justify-between p-3 bg-slate-50 dark:bg-slate-800/50 rounded-lg">
                                <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Hoạt động</span>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input name="status" type="checkbox" value="1" class="sr-only peer"
                                        {{ old('status', $user->status == 'active') ? 'checked' : '' }}>
                                    <div
                                        class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-primary">
                                    </div>
                                </label>
                            </div>
                            <p class="text-xs text-slate-500 mt-3">Cho phép hoặc chặn người dùng truy cập vào hệ thống
                                ngay lập tức.</p>
                            @error('status')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
                <!-- Action Buttons -->

            </form>
        </div>
    </main>
    <script>
        function previewAvatar(event) {
            const file = event.target.files[0];
            if (file) {
                // 1. Tạo đường dẫn tạm thời cho bức ảnh vừa chọn
                const imageUrl = URL.createObjectURL(file);
                // 2. Lấy các thẻ HTML cần thao tác
                const previewImage = document.getElementById('avatarPreview');
                const placeholder = document.getElementById('uploadPlaceholder');
                // 3. Gán đường dẫn vào thẻ img và hiển thị nó lên
                previewImage.src = imageUrl;
                previewImage.classList.remove('hidden');
                // 4. Ẩn biểu tượng và chữ "Tải ảnh lên" đi
                placeholder.classList.add('hidden');
            }
        }
    </script>

@endsection
