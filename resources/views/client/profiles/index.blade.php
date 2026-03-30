@extends('client.profiles.layouts.app')

@section('profile_content')
    <section class="flex-1" data-purpose="user-main-section">
        <!-- Welcome Header -->
        @include('popup_notify.index')
        @if ($errors->any())
            <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-lg shadow-sm transition-all duration-300">
                <div class="flex items-center mb-2">
                    <svg class="w-5 h-5 text-red-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                            clip-rule="evenodd" />
                    </svg>
                    <h3 class="text-red-800 font-bold text-sm uppercase tracking-wider">Vui lòng kiểm tra lại các thông tin
                        sau:</h3>
                </div>
                <ul class="list-disc list-inside text-sm text-red-600 space-y-1 ml-1">
                    {{-- Vòng lặp lấy tất cả các lỗi ra --}}
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="mb-8" data-purpose="user-welcome">
            <h1 class="text-2xl font-bold">{{ $user->name }}</h1>
            <p class="text-gray-500">Ngày tham gia: {{ $user->created_at->format('d/m/Y') }} • Chào mừng bạn trở lại Bee
                Phone</p>
        </div>

        <!-- Quick Stats Widgets -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8" data-purpose="quick-stats">
            <div
                class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm flex items-center gap-4 transition-all duration-300 hover:shadow-md hover:border-[#f4c025] group cursor-default">
                <div class="bg-blue-50 p-3 rounded-full text-blue-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewbox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" stroke-linecap="round" stroke-linejoin="round"
                            stroke-width="2"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Tổng đơn hàng</p>
                    <p class="text-xl font-bold">{{ $user->orders->count() }}</p>
                </div>
            </div>
            <div
                class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm flex items-center gap-4 transition-all duration-300 hover:shadow-md hover:border-[#f4c025] group cursor-default">
                <div class="bg-yellow-50 p-3 rounded-full text-[#f4c025]">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewbox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Bee Point</p>
                    <p class="text-xl font-bold">{{ number_format($user->reward_points, 0, ',', '.') }}</p>
                </div>
            </div>
            <div
                class="bg-[#1a1a1a] p-5 rounded-xl shadow-sm flex items-center gap-4 text-white transition-all duration-300 hover:shadow-md hover:ring-1 hover:ring-[#f4c025] group cursor-default">
                <div class="bg-[#f4c025] p-3 rounded-full text-[#1a1a1a]">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewbox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-400">Số dư ví</p>
                    <p class="text-xl font-bold">{{ number_format($user->wallet?->balance ?? 0, 0, ',', '.') }}đ</p>
                </div>
            </div>
        </div>
        <!-- Personal Info Card -->
        {{-- Bọc toàn bộ vào thẻ form để có thể submit dữ liệu --}}
        <form id="profileForm" action="{{ route('profile.update', $user->id) }}" method="POST"
            enctype="multipart/form-data">
            @csrf
            {{-- Nếu bạn dùng route PUT thì mở comment dòng dưới --}}
            @method('PUT')

            <div class="bg-white rounded-xl border border-gray-100 shadow-sm mb-6 overflow-hidden transition-all duration-300 hover:shadow-lg hover:border-yellow-200"
                data-purpose="personal-info-card">
                <div class="p-6 border-b border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4">

                    {{-- KHỐI AVATAR (Giữ nguyên như cũ) --}}
                    <div class="flex items-center gap-6">
                        <div class="relative group">
                            <input type="file" id="avatarInput" name="avatar" class="hidden edit-field-input"
                                accept="image/png, image/jpeg, image/jpg, image/webp" onchange="previewAvatar(event)">
                            <div
                                class="w-24 h-24 rounded-full border-4 border-gray-50 overflow-hidden flex items-center justify-center shadow-sm">
                                @if ($user->avatar)
                                    <img id="avatarPreview" alt="{{ $user->name }}" class="w-full h-full object-cover"
                                        src="{{ asset('storage/' . $user->avatar) }}" />
                                    <div id="initialsPreview"
                                        class="hidden w-full h-full bg-gray-200 text-gray-900 flex items-center justify-center text-3xl font-bold uppercase tracking-wider">
                                    </div>
                                @else
                                    @php
                                        $words = explode(' ', trim($user->name));
                                        $initials =
                                            count($words) > 1
                                                ? mb_substr($words[0], 0, 1) . mb_substr(end($words), 0, 1)
                                                : mb_substr($user->name, 0, 2);
                                    @endphp
                                    <div id="initialsPreview"
                                        class="w-full h-full bg-gray-200 text-gray-900 flex items-center justify-center text-3xl font-bold uppercase tracking-wider">
                                        {{ mb_strtoupper($initials) }}
                                    </div>
                                    <img id="avatarPreview" alt="{{ $user->name }}"
                                        class="hidden w-full h-full object-cover" src="" />
                                @endif
                            </div>
                            {{-- Nút đổi ảnh: Chỉ hiện khi ở chế độ chỉnh sửa (class toggle-edit ẩn đi mặc định, sẽ được JS bật lên) --}}
                            <button type="button" id="btnChangeAvatar"
                                onclick="document.getElementById('avatarInput').click()"
                                class="hidden absolute bottom-0 right-0 bg-white border border-gray-200 p-1.5 rounded-full shadow-md hover:bg-gray-50 transition"
                                title="Thay đổi ảnh">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"
                                        stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                                    <path d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" stroke-linecap="round"
                                        stroke-linejoin="round" stroke-width="2"></path>
                                </svg>
                            </button>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold">Thông tin cá nhân</h2>
                            <p class="text-sm text-gray-500">Quản lý thông tin hồ sơ của bạn</p>
                        </div>
                    </div>

                    {{-- CÁC NÚT ĐIỀU KHIỂN --}}
                    <div class="flex gap-2">
                        {{-- Trạng thái mặc định (Xem) --}}
                        <button type="button" id="btnEditMode" onclick="toggleEditMode(true)"
                            class="px-4 py-2 bg-[#f4c025] text-[#1a1a1a] font-semibold rounded-lg hover:opacity-90 transition text-sm">
                            Cập nhật thông tin
                        </button>
                        <button type="button" id="btnChangePassword"
                            class="px-4 py-2 bg-gray-100 text-[#1a1a1a] font-semibold rounded-lg hover:bg-gray-200 transition text-sm">
                            Đổi mật khẩu
                        </button>

                        {{-- Trạng thái khi chỉnh sửa (Ẩn mặc định) --}}
                        <button type="button" id="btnCancelEdit" onclick="toggleEditMode(false)"
                            class="hidden px-4 py-2 bg-gray-200 text-[#1a1a1a] font-semibold rounded-lg hover:bg-gray-300 transition text-sm">
                            Hủy
                        </button>
                        <button type="submit" id="btnSaveProfile"
                            class="hidden px-4 py-2 bg-yellow-500 font-semibold rounded-lg hover:bg-yellow-600 transition text-sm">
                            Lưu thông tin
                        </button>
                    </div>
                </div>

                {{-- KHỐI THÔNG TIN CÁ NHÂN --}}
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-y-6 gap-x-12">
                    {{-- Họ và Tên --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Họ và
                            Tên</label>
                        <p class="text-base font-medium display-field">{{ $user->name }}</p>
                        <input type="text" name="name" value="{{ $user->name }}"
                            class="hidden edit-field w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-yellow-400 focus:ring-1 focus:ring-yellow-400">
                    </div>

                    {{-- Email --}}
                    <div>
                        <label
                            class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Email</label>
                        <p class="text-base font-medium display-field">{{ $user->email }}</p>
                        <input type="email" name="email" value="{{ $user->email }}"
                            class="hidden edit-field w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-yellow-400 focus:ring-1 focus:ring-yellow-400">
                    </div>

                    {{-- Số điện thoại --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Số điện
                            thoại</label>
                        <p class="text-base font-medium display-field">{{ $user->phone ?? 'Chưa có SĐT' }}</p>
                        <input type="text" name="phone" value="{{ $user->phone }}"
                            placeholder="Nhập số điện thoại"
                            class="hidden edit-field w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-yellow-400 focus:ring-1 focus:ring-yellow-400">
                    </div>

                    {{-- Ngày sinh --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Ngày
                            sinh</label>
                        <p class="text-base font-medium display-field">
                            {{ $user->birthday == null ? 'Chưa có ngày sinh' : $user->birthday->format('d/m/Y') }}</p>
                        {{-- Input type date bắt buộc format value là Y-m-d --}}
                        <input type="date" name="birthday"
                            value="{{ $user->birthday ? $user->birthday->format('Y-m-d') : '' }}"
                            class="hidden edit-field w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-yellow-400 focus:ring-1 focus:ring-yellow-400">
                    </div>

                    {{-- Giới tính --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Giới
                            tính</label>
                        <p class="text-base font-medium display-field">{{ $user->gender ?? 'Chưa có giới tính' }}</p>
                        <select name="gender"
                            class="hidden edit-field w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-yellow-400 focus:ring-1 focus:ring-yellow-400">
                            <option value="" disabled {{ !$user->gender ? 'selected' : '' }}>Chọn giới tính</option>
                            <option value="male" {{ $user->gender == 'male' ? 'selected' : '' }}>Nam</option>
                            <option value="female" {{ $user->gender == 'female' ? 'selected' : '' }}>Nữ</option>
                            <option value="other" {{ $user->gender == 'other' ? 'selected' : '' }}>Khác</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- KHỐI ĐỊA CHỈ --}}
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm mb-8 overflow-hidden transition-all duration-300 hover:shadow-md hover:border-yellow-100"
                data-purpose="address-card">
                <div class="p-6 border-b border-gray-100">
                    <h3 class="font-bold text-lg">Địa chỉ giao hàng</h3>
                </div>
                <div class="p-6">
                    <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Địa chỉ
                        chính</label>
                    <p class="text-base font-medium display-field">{{ $user->address ?? 'Chưa có địa chỉ giao hàng' }}</p>
                    <input type="text" name="address" value="{{ $user->address }}"
                        placeholder="Nhập địa chỉ của bạn"
                        class="hidden edit-field w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-yellow-400 focus:ring-1 focus:ring-yellow-400">
                </div>
            </div>
        </form>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm mb-6 overflow-hidden transition-all duration-300 hover:shadow-md hover:border-yellow-100"
            data-purpose="role-permissions-card">
            <div class="p-6 border-b border-gray-100">
                <h3 class="font-bold text-lg">Vai trò &amp; Quyền hạn</h3>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-y-6 gap-x-12">
                <div>
                    <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Vai
                        trò</label>
                    <p class="text-base font-medium">{{ $user->role->name_role }}</p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Quyền
                        hạn</label>
                    @if ($user->role->name == 'admin')
                        <p class="text-base font-medium">Toàn quyền truy cập hệ thống</p>
                    @elseif ($user->role->name == 'staff')
                        @foreach ($user->permissions as $permission)
                            <p class="text-base font-medium">{{ $permission->name }}</p>
                        @endforeach
                    @else
                        <p class="text-base font-medium"> Mua hàng</p>
                    @endif
                </div>
            </div>
        </div>
        <!-- Recent Activity Summary -->
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm transition-all duration-300 hover:shadow-sm"
            data-purpose="recent-activity">
            <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                <h3 class="font-bold">Hoạt động gần đây</h3>
                <a class="text-sm text-[#f4c025] font-semibold hover:underline" href="#">Xem tất cả</a>
            </div>

            <div class="divide-y divide-gray-100">
                <!-- Order Item 1 -->
                {{-- @foreach ($user->activityLogs()->latest()->take(3)->get() as $log)
                    <div
                        class="p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4 transition-colors duration-200 hover:bg-gray-50">
                        <div class="flex gap-4">
                            <div>
                                <h4 class="font-bold text-sm">{{ $log->description }}
                                    @if ($log->model)
                                        - <a class="text-blue-500" href="#">{{ $log->model->name ?? '' }}</a>
                                    @endif
                                </h4>
                                <p class="text-xs text-gray-500 mt-1">Ngày {{ $log->created_at->format('d/m/Y') }}</p>
                            </div>
                        </div>

                    </div>
                @endforeach --}}
                <!-- Order Item 2 -->
                {{-- <div
                            class="p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4 transition-colors duration-200 hover:bg-gray-50">
                            <div class="flex gap-4">
                                <div class="w-16 h-16 bg-gray-50 rounded-lg flex-shrink-0 border border-gray-100 p-2">
                                    <img alt="AirPods Pro" class="w-full h-full object-contain"
                                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuDWFWvIy1yFD9N030EJix0um1cvujySUjEtemTx52GPpVhvekdMYCkJS55x6NhIlRoQDU108I5bZ3HHEoBWnLqbqooNxMibdjtzivO1CtKGp6x1aWIbvzPX2lHrEz1LEFlyx4HgA0txXTQsgyEpHpom7EzeF7DwLWYbCuSbkLrBXSDUdVwgh39k8MNIdTtS-3W9nZIz1smpUeeBW4BfBHqCzCTtwGsmzh5o_NlEoIsvOh5JFbI8k__BZs2pk5yo00sK8FoRRA6Fck4" />
                                </div>
                                <div>
                                    <h4 class="font-bold text-sm">AirPods Pro (Gen 2) MagSafe USB-C</h4>
                                    <p class="text-xs text-gray-500 mt-1">Đơn hàng: #BP-88055 • Ngày: 05/10/2023</p>
                                    <p class="text-sm font-bold mt-1 text-[#f4c025]">5.890.000₫</p>
                                </div>
                            </div>
                            <div>
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Giao hàng thành công
                                </span>
                            </div>
                        </div>
                        <!-- Order Item 3 -->
                        <div
                            class="p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4 transition-colors duration-200 hover:bg-gray-50">
                            <div class="flex gap-4">
                                <div class="w-16 h-16 bg-gray-50 rounded-lg flex-shrink-0 border border-gray-100 p-2">
                                    <img alt="Samsung S23" class="w-full h-full object-contain"
                                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuAnlgqAfgNUiMOqb6ktzkHscAdpjbgP_zOwVFbI7j1hkbhLNsfneFg46h2c_f8aEvzGLhmioZ1HDswXQbEVUVdoAskuPtMQS7a50SIX56bLFbe_a3wREhs9eevNJ_vko_YnW112mNhmZbl07xxzfNO5sv5imUM2JkUxQdtIGTmI_U82-OToXH3b2hfiGG3h9uW3Na-rBvBhMcEjJA8_xiS_-eQQ2dPFZ9uTX0bKkmLhQxult-PTZ27yhq-HOqRFgMM2wEjJuDIArzY" />
                                </div>
                                <div>
                                    <h4 class="font-bold text-sm">Ốp lưng Silicone iPhone 15 Pro Max</h4>
                                    <p class="text-xs text-gray-500 mt-1">Đơn hàng: #BP-87990 • Ngày: 28/09/2023</p>
                                    <p class="text-sm font-bold mt-1 text-[#f4c025]">1.290.000₫</p>
                                </div>
                            </div>
                            <div>
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    Đang giao hàng
                                </span>
                            </div>
                        </div> --}}
            </div>
        </div>
    </section>
    <div id="passwordModal"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden transition-opacity duration-300">

        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md p-6 relative transform transition-all">

            <button type="button"
                class="close-modal absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                    </path>
                </svg>
            </button>

            <h3 class="text-xl font-bold mb-1 text-[#1a1a1a]">Đổi mật khẩu</h3>
            <p class="text-sm text-gray-500 mb-6">Vui lòng nhập mật khẩu hiện tại và mật khẩu mới.</p>

            {{-- Nhớ thay 'profile.password.update' bằng tên Route thực tế của bạn --}}
            <form action="{{ route('profile.password.update', $user->id) }}" method="POST">
                @csrf
               <div class="space-y-4">
                    {{-- Mật khẩu cũ --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Mật khẩu
                            hiện tại</label>
                        <input type="password" name="current_password" required
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:border-[#f4c025] focus:ring-1 focus:ring-[#f4c025]">
                        @error('current_password')
                            <p class="mt-1 text-xs text-red-500 font-medium">* {{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Mật khẩu mới --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Mật khẩu
                            mới</label>
                        <input type="password" name="password" required
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:border-[#f4c025] focus:ring-1 focus:ring-[#f4c025]">
                        @error('password')
                            <p class="mt-1 text-xs text-red-500 font-medium">* {{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Xác nhận mật khẩu mới --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Xác nhận mật
                            khẩu mới</label>
                        <input type="password" name="password_confirmation" required
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:border-[#f4c025] focus:ring-1 focus:ring-[#f4c025]">
                    </div>
                </div>

                <div class="mt-8 flex justify-end gap-3">
                    <button type="button"
                        class="close-modal px-4 py-2 bg-gray-100 text-gray-700 font-semibold rounded-lg hover:bg-gray-200 transition text-sm">
                        Hủy bỏ
                    </button>
                    <button type="submit"
                        class="px-4 py-2 bg-[#f4c025] text-[#1a1a1a] font-semibold rounded-lg hover:opacity-90 transition text-sm">
                        Lưu mật khẩu
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
@push('js')
    <script>
        // 1. Hàm hiển thị ảnh xem trước khi người dùng chọn file
        function previewAvatar(event) {
            const input = event.target;
            const reader = new FileReader();

            reader.onload = function() {
                const dataURL = reader.result; // Mã base64 của bức ảnh tạm
                const imagePreview = document.getElementById('avatarPreview');
                const initialsPreview = document.getElementById('initialsPreview');

                // Cập nhật đường dẫn ảnh tạm thời vào thẻ img
                imagePreview.src = dataURL;

                // Hiện thẻ ảnh lên, và giấu cái nền chữ viết tắt đi (nếu đang hiển thị chữ)
                imagePreview.classList.remove('hidden');
                if (initialsPreview) {
                    initialsPreview.classList.add('hidden');
                }
            };

            // Nếu người dùng có chọn file, thì tiến hành đọc file
            if (input.files && input.files[0]) {
                reader.readAsDataURL(input.files[0]);
            }
        }

        // 2. Hàm xử lý hoán đổi giao diện Xem <-> Chỉnh sửa
        function toggleEditMode(isEditing) {
            // Ẩn/hiện các text và input
            const displayFields = document.querySelectorAll('.display-field');
            const editFields = document.querySelectorAll('.edit-field');

            displayFields.forEach(field => isEditing ? field.classList.add('hidden') : field.classList.remove('hidden'));
            editFields.forEach(field => isEditing ? field.classList.remove('hidden') : field.classList.add('hidden'));

            // Ẩn/hiện nút cập nhật ảnh Avatar
            const btnChangeAvatar = document.getElementById('btnChangeAvatar');
            if (isEditing) {
                btnChangeAvatar.classList.remove('hidden');
            } else {
                btnChangeAvatar.classList.add('hidden');
            }

            // Đổi các nút bấm Điều khiển
            const btnEditMode = document.getElementById('btnEditMode');
            const btnChangePassword = document.getElementById('btnChangePassword');
            const btnSaveProfile = document.getElementById('btnSaveProfile');
            const btnCancelEdit = document.getElementById('btnCancelEdit');

            if (isEditing) {
                btnEditMode.classList.add('hidden');
                btnChangePassword.classList.add('hidden');
                btnSaveProfile.classList.remove('hidden');
                btnCancelEdit.classList.remove('hidden');
            } else {
                btnEditMode.classList.remove('hidden');
                btnChangePassword.classList.remove('hidden');
                btnSaveProfile.classList.add('hidden');
                btnCancelEdit.classList.add('hidden');

                // Bấm hủy -> Reset form lại trạng thái ban đầu
                document.getElementById('profileForm').reset();

                // MẸO UX: Khi bấm hủy, nếu lỡ chọn ảnh mới thì phải reset lại ảnh cũ
                const imagePreview = document.getElementById('avatarPreview');
                const oldAvatarUrl = "{{ $user->avatar ? asset('storage/' . $user->avatar) : '' }}";

                if (oldAvatarUrl) {
                    imagePreview.src = oldAvatarUrl;
                } else {
                    // Nếu lúc đầu chưa có ảnh, thì ẩn ảnh đi và hiện lại chữ cái tên
                    imagePreview.classList.add('hidden');
                    const initialsPreview = document.getElementById('initialsPreview');
                    if (initialsPreview) initialsPreview.classList.remove('hidden');
                }
            }
        }
    </script>

    <script>
        // Script xử lý Modal Đổi Mật Khẩu
        document.addEventListener('DOMContentLoaded', function() {
            const btnChangePassword = document.getElementById('btnChangePassword');
            const passwordModal = document.getElementById('passwordModal');
            const closeModals = document.querySelectorAll('.close-modal');

            // Mở popup khi bấm nút Đổi mật khẩu
            if (btnChangePassword) {
                btnChangePassword.addEventListener('click', function() {
                    passwordModal.classList.remove('hidden');
                });
            }

            // Đóng popup khi bấm nút X hoặc nút Hủy
            closeModals.forEach(btn => {
                btn.addEventListener('click', function() {
                    passwordModal.classList.add('hidden');
                });
            });

            // Trải nghiệm xịn: Đóng popup khi người dùng bấm ra vùng nền đen bên ngoài
            passwordModal.addEventListener('click', function(e) {
                if (e.target === passwordModal) {
                    passwordModal.classList.add('hidden');
                }
            });
        });
    </script>
@endpush
