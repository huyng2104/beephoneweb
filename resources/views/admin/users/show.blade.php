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


        {{-- lịch sử đặt hàng --}}


        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 mb-8">
            <div class="lg:col-span-12 space-y-8"> {{-- 🔥 Thêm space-y-8 để các card cách nhau đẹp --}}

                <div class="bg-white dark:bg-slate-900 rounded-xl overflow-hidden border border-primary/10 shadow-sm">
                    <div class="p-8">
                        <div>
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
                                    <div class="flex flex-wrap gap-2"> {{-- 🔥 Thêm flex-wrap gap-2 để tag hiển thị hàng ngang đẹp --}}
                                        @switch($user->role->name)
                                            @case('admin')
                                                <span
                                                    class="px-2 py-1 bg-white dark:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs rounded border border-slate-200 dark:border-slate-600">•
                                                    Toàn quyền truy cập hệ thống</span>
                                            @break

                                            @case('user')
                                                <span
                                                    class="px-2 py-1 bg-white dark:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs rounded border border-slate-200 dark:border-slate-600">•
                                                    Mua hàng</span>
                                            @break

                                            @default
                                                <div class="w-full mb-1">
                                                    <p class="text-sm text-slate-600 dark:text-slate-400">Quyền mặc định theo vai
                                                        trò:</p>
                                                </div>
                                                @foreach ($user->role->permissions as $permission)
                                                    <span
                                                        class="px-2 py-1 bg-white dark:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs rounded border border-slate-200 dark:border-slate-600">•
                                                        {{ $permission->name }}</span>
                                                @endforeach

                                                <div class="w-full mt-3 mb-1">
                                                    <p class="text-sm text-slate-600 dark:text-slate-400">Quyền cấp riêng:</p>
                                                </div>
                                                @forelse ($user->permissions as $pms)
                                                    <span
                                                        class="px-2 py-1 bg-white dark:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs rounded border border-slate-200 dark:border-slate-600">•
                                                        {{ $pms->name }}</span>
                                                @empty
                                                    <span class="text-xs text-slate-400 italic">Không có quyền riêng</span>
                                                @endforelse
                                            @endswitch
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-slate-900 rounded-xl overflow-hidden border border-primary/10 shadow-sm">
                        <div class="p-8">
                            <div class="flex items-center justify-between mb-6">
                                <h3 class="text-lg font-bold flex items-center gap-2 text-slate-900 dark:text-slate-100 mb-0">
                                    <span class="w-1.5 h-6 bg-primary rounded-full"></span> Lịch sử đặt hàng
                                </h3>
                                <a href="{{ route('admin.users.orders', $user->id) }}" class="text-primary hover:underline text-xs font-bold flex items-center gap-1">
                                    Xem tất cả
                                    <span class="material-symbols-outlined text-sm">chevron_right</span>
                                </a>
                            </div>

                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-800 text-sm">
                                    <thead class="bg-slate-50 dark:bg-slate-800/50">
                                        <tr>
                                            <th class="px-4 py-3 text-left font-semibold text-slate-600 dark:text-slate-300">Mã
                                                Đơn</th>
                                            <th class="px-4 py-3 text-left font-semibold text-slate-600 dark:text-slate-300">
                                                Ngày đặt</th>
                                            <th class="px-4 py-3 text-left font-semibold text-slate-600 dark:text-slate-300">
                                                Tổng tiền</th>
                                            <th class="px-4 py-3 text-left font-semibold text-slate-600 dark:text-slate-300">
                                                Trạng thái</th>
                                            <th class="px-4 py-3 text-center font-semibold text-slate-600 dark:text-slate-300">
                                                Thao tác</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">

                                        {{-- 🔥 Đã thêm collect()->sortByDesc('created_at') ở đây --}}
                                        @forelse (collect($user->orders ?? [])->sortByDesc('created_at') as $order)
                                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                                                <td class="px-4 py-4 font-medium text-slate-900 dark:text-slate-100">
                                                    #{{ $order->order_code ?? $order->id }}</td>
                                                <td class="px-4 py-4 text-slate-500 dark:text-slate-400">
                                                    {{ $order->created_at->format('d/m/Y') }}</td>
                                                <td class="px-4 py-4 font-semibold text-slate-900 dark:text-slate-100">
                                                    {{ number_format($order->total_price ?? 0) }}đ</td>
                                                <td class="px-4 py-4">
                                                    @php
                                                        $status_class = match ($order->status ?? 'pending') {
                                                            'completed',
                                                            'received'
                                                                => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400', // Gộp chung màu xanh lá
                                                            'shipping'
                                                                => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
                                                            'cancelled'
                                                                => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                                                            'pending'
                                                                => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400', // Cố định màu vàng cho Chờ xác nhận
                                                            default
                                                                => 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-400', // Màu xám cho trường hợp lạ
                                                        };

                                                        $status_text = match ($order->status ?? 'pending') {
                                                            'pending' => 'Chờ xác nhận',
                                                            'shipping' => 'Đang giao',
                                                            'received' => 'Đã nhận',
                                                            'completed' => 'Thành công',
                                                            'cancelled' => 'Đã hủy',
                                                            default
                                                                => 'Chờ xử lý', // Đóng vai trò bao quát tất cả các trường hợp còn lại
                                                        };
                                                    @endphp
                                                    <span
                                                        class="px-2.5 py-1 text-xs font-bold rounded-full {{ $status_class }}">
                                                        {{ $status_text }}
                                                    </span>
                                                </td>
                                                <td class="px-4 py-4 text-center">
                                                    <a href="{{ route('admin.orders.show', $order->id) }}"
                                                        class="text-primary hover:underline text-xs font-bold flex items-center justify-center gap-1">
                                                        Xem chi tiết
                                                        <span class="material-symbols-outlined text-sm">chevron_right</span>
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5"
                                                    class="px-4 py-8 text-center text-slate-400 dark:text-slate-500 italic">
                                                    Người dùng này chưa có đơn hàng nào.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div class="bg-white dark:bg-slate-900 rounded-xl p-6 mb-8 border border-primary/10 shadow-sm">
    <div class="flex-1">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-3">
                <p class="text-sm font-bold text-slate-900 dark:text-slate-100">Lịch sử hoạt động của người dùng</p>
                <span class="text-xs font-medium bg-blue-50 text-blue-600 px-2 py-1 rounded-md dark:bg-blue-900/30 dark:text-blue-400">
                    Tổng: {{ $activities->total() }} bản ghi
                </span>
            </div>
            
            <a href="{{ route('admin.users.activities', $user->id) }}" class="text-primary hover:underline text-xs font-bold flex items-center gap-1">
                Xem tất cả
                <span class="material-symbols-outlined text-sm">chevron_right</span>
            </a>
        </div>
        @if($activities->isEmpty())
            <div class="text-center py-8 text-slate-500">
                <span class="material-symbols-outlined text-4xl mb-2 opacity-50">history</span>
                <p>Người dùng này chưa có hoạt động nào.</p>
            </div>
        @else
            @php
                if (!isset($formatLogValue)) {
                    $formatLogValue = function($value) {
                        if (is_array($value) || is_object($value)) return '[Dữ liệu]';
                        if (is_bool($value)) return $value ? 'Bật (True)' : 'Tắt (False)';
                        if (is_null($value) || $value === '') return '[Trống]';
                        $str = strip_tags(trim((string)$value));
                        return mb_strlen($str) > 40 ? mb_substr($str, 0, 40) . '...' : $str;
                    };

                    $translateField = function($field) {
                        $map = [
                            'name' => 'Tên',
                            'title' => 'Tiêu đề',
                            'price' => 'Giá',
                            'regular_price' => 'Giá gốc',
                            'sale_price' => 'Giá KM',
                            'status' => 'Trạng thái',
                            'description' => 'Mô tả',
                            'content' => 'Nội dung',
                            'quantity' => 'Số lượng',
                            'email' => 'Email',
                            'phone' => 'SĐT',
                            'address' => 'Địa chỉ',
                            'role_id' => 'Vai trò',
                            'category_id' => 'Danh mục',
                            'brand_id' => 'Nhãn hiệu',
                            'is_active' => 'Trạng thái Kích hoạt',
                        ];
                        return $map[$field] ?? strtoupper($field);
                    };
                }
            @endphp
            <div class="space-y-6">
                @foreach ($activities as $activity)
                    @php
                        // 1. Dùng SWITCH để gán Class và Tên hiển thị
                        $badge_class = '';
                        $log_name_display = '';

                        switch ($activity->log_name) {
                            case 'voucher':
                                $badge_class = 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400';
                                $log_name_display = 'MÃ GIẢM GIÁ';
                                break;
                            case 'user':
                                $badge_class = 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400';
                                $log_name_display = 'NGƯỜI DÙNG';
                                break;
                            case 'product':
                                $badge_class = 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400';
                                $log_name_display = 'SẢN PHẨM';
                                break;
                            case 'order':
                                $badge_class = 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400';
                                $log_name_display = 'ĐƠN HÀNG';
                                break;
                            case 'auth':
                                $badge_class = 'bg-red-100 text-red-700 dark:bg-red-800 dark:text-red-300';
                                $log_name_display = 'BẢO MẬT';
                                break;
                            case 'post category':
                                $badge_class = 'bg-pink-100 text-pink-700 dark:bg-pink-800 dark:text-pink-300';
                                $log_name_display = 'DANH MỤC BV';
                                break;
                            case 'post':
                                $badge_class = 'bg-teal-100 text-teal-700 dark:bg-teal-900/30 dark:text-teal-400';
                                $log_name_display = 'BÀI VIẾT';
                                break;
                            case 'role':
                                $badge_class = 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400';
                                $log_name_display = 'VAI TRÒ';
                                break;
                            case 'brand':
                                $badge_class = 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400';
                                $log_name_display = 'THƯƠNG HIỆU';
                                break;
                            case 'category':
                                $badge_class = 'bg-cyan-100 text-cyan-700 dark:bg-cyan-900/30 dark:text-cyan-400';
                                $log_name_display = 'DANH MỤC SP';
                                break;
                            case 'wallet':
                                $badge_class = 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400';
                                $log_name_display = 'VÍ TIỀN';
                                break;
                            case 'withdrawal request':
                                $badge_class = 'bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-400';
                                $log_name_display = 'YÊU CẦU RÚT TIỀN';
                                break;
                            case 'point':
                                $badge_class = 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400';
                                $log_name_display = 'ĐIỂM THƯỞNG';
                                break;
                            case 'attribute':
                                $badge_class = 'bg-fuchsia-100 text-fuchsia-700 dark:bg-fuchsia-900/30 dark:text-fuchsia-400';
                                $log_name_display = 'THUỘC TÍNH';
                                break;
                            case 'attribute value':
                                $badge_class = 'bg-violet-100 text-violet-700 dark:bg-violet-900/30 dark:text-violet-400';
                                $log_name_display = 'GIÁ TRỊ THUỘC TÍNH';
                                break;
                            case 'banner':
                                $badge_class = 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400';
                                $log_name_display = 'ẢNH BÌA';
                                break;
                            default:
                                $badge_class = 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-400';
                                $log_name_display = strtoupper($activity->log_name ?? 'HỆ THỐNG');
                                break;
                        }

                        // 2. Dùng SWITCH xử lý hành động
                        $action_text = '';
                        switch ($activity->description) {
                            case 'created':
                                $action_text = 'đã thêm mới';
                                break;
                            case 'updated':
                                $action_text = 'đã cập nhật';
                                break;
                            case 'deleted':
                                $action_text = 'đã xóa';
                                break;
                            default:
                                $action_text = $activity->description;
                                break;
                        }

                        // 3. Lấy tên đối tượng (Subject) để Admin biết User thao tác lên cái gì
                        $new_attributes = $activity->properties['attributes'] ?? [];
                        $old_attributes = $activity->properties['old'] ?? [];

                        $subject_display = null;

                        if ($activity->log_name === 'wallet') {
                            $subject_display = $activity->subject?->user?->name ?? 'Ví người dùng';
                        } else {
                            $subject_display =
                                $activity->subject?->name ??
                                $activity->subject?->title ??
                                $activity->subject?->account_name ??
                                $activity->subject?->code ??
                                $activity->subject?->order_code ??
                                ($new_attributes['name'] ??
                                ($new_attributes['title'] ??
                                ($new_attributes['account_name'] ??
                                ($new_attributes['code'] ??
                                ($old_attributes['name'] ??
                                ($old_attributes['title'] ??
                                ($old_attributes['account_name'] ??
                                ($old_attributes['code'] ?? null))))))));
                        }

                        // 4. Lọc dữ liệu thay đổi / bị xóa
                        $changed_fields = [];
                        $deleted_fields = [];
                        $ignored_keys = ['id', 'created_at', 'updated_at', 'deleted_at', 'remember_token', 'password'];

                        if ($activity->description === 'updated') {
                            foreach ($new_attributes as $key => $newValue) {
                                if (!in_array($key, $ignored_keys)) {
                                    $oldValue = $old_attributes[$key] ?? null;
                                    if ($oldValue !== $newValue) {
                                        $changed_fields[$key] = ['old' => $oldValue, 'new' => $newValue];
                                    }
                                }
                            }
                        } elseif ($activity->description === 'deleted') {
                            foreach ($old_attributes as $key => $oldValue) {
                                if (!in_array($key, $ignored_keys)) {
                                    $deleted_fields[$key] = $oldValue;
                                }
                            }
                        }
                    @endphp

                    <div class="relative pl-6 border-l-2 border-slate-200 dark:border-slate-700 ml-3">
                        <span class="absolute -left-[9px] top-4 flex h-4 w-4 items-center justify-center rounded-full bg-white dark:bg-slate-900 border-2 border-slate-400">
                            <span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span>
                        </span>

                        <div class="bg-white dark:bg-slate-800 p-4 rounded-lg shadow-sm border border-slate-100 dark:border-slate-700">
                            <div class="flex flex-wrap items-center justify-between gap-2 pb-3 border-b border-slate-100 dark:border-slate-700">
                                <div class="flex items-center gap-2">
                                    <span class="text-sm text-slate-600 dark:text-slate-300 flex items-center flex-wrap gap-1.5">
                                        <strong class="text-slate-900 dark:text-white">{{ $user->name }}</strong>
                                        
                                        <span class="{{ $activity->description === 'deleted' ? 'text-red-600 dark:text-red-400 font-medium' : '' }}">
                                            {{ $action_text }}
                                        </span>
                                        
                                        <span class="px-2 py-0.5 text-[11px] font-bold rounded-md uppercase tracking-wide {{ $badge_class }}">
                                            {{ $log_name_display }}
                                        </span>

                                        @if($subject_display)
                                            <strong class="text-slate-900 dark:text-white">
                                                {{ $subject_display }}
                                            </strong>
                                        @elseif($activity->subject_id)
                                            <strong class="text-slate-900 dark:text-white">
                                                #{{ $activity->subject_id }}
                                            </strong>
                                        @endif
                                    </span>
                                </div>

                                <div class="text-right whitespace-nowrap">
                                    <div class="text-xs font-bold text-slate-600 dark:text-slate-400">
                                        {{ $activity->created_at->format('H:i') }}
                                    </div>
                                    <div class="text-[10px] text-slate-400">
                                        {{ $activity->created_at->format('d/m/Y') }}
                                    </div>
                                </div>
                            </div>

                            {{-- Hiển thị thông tin bảo mật (Nếu log auth) --}}
                            @if($activity->log_name === 'auth' && isset($activity->properties['ip_address']))
                                <div class="mt-2 text-xs text-slate-500 flex gap-3">
                                    <span>🌐 IP: {{ $activity->properties['ip_address'] }}</span>
                                    @if(isset($activity->properties['user_agent']))
                                        <span class="truncate max-w-[200px] md:max-w-md">💻 Thiết bị: {{ $activity->properties['user_agent'] }}</span>
                                    @endif
                                </div>
                            @endif

                            {{-- Bảng thay đổi dữ liệu (Khi CẬP NHẬT) --}}
                            @if ($activity->description === 'updated' && !empty($changed_fields))
                                <div class="mt-3 space-y-2">
                                    <div class="text-xs font-semibold text-slate-500 mb-1">Dữ liệu thay đổi:</div>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                        @foreach ($changed_fields as $field => $data)
                                            <div class="p-2 border border-slate-100 dark:border-slate-700 rounded-lg bg-slate-50/50 dark:bg-slate-900/50 flex flex-col justify-center text-xs">
                                                <div class="font-bold text-slate-700 dark:text-slate-300 mb-1 truncate">
                                                    📋 {{ $translateField($field) }}
                                                </div>
                                                <div class="flex items-center gap-2 flex-wrap">
                                                    <span class="line-through decoration-red-400 text-slate-400 bg-red-50 dark:bg-red-900/20 px-1.5 py-0.5 rounded truncate max-w-[150px]" title="{{ is_string($data['old']) ? strip_tags((string)$data['old']) : '' }}">
                                                        {{ $formatLogValue($data['old']) }}
                                                    </span>
                                                    <span class="text-slate-400">➔</span>
                                                    <span class="text-green-700 dark:text-green-400 font-medium bg-green-50 dark:bg-green-900/20 px-1.5 py-0.5 rounded truncate max-w-[150px]" title="{{ is_string($data['new']) ? strip_tags((string)$data['new']) : '' }}">
                                                        {{ $formatLogValue($data['new']) }}
                                                    </span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            {{-- Bảng dữ liệu bị mất (Khi XÓA) --}}
                            @if ($activity->description === 'deleted' && !empty($deleted_fields))
                                <div class="mt-3 space-y-2">
                                    <div class="text-xs font-semibold text-red-500 mb-1">Dữ liệu đã xóa:</div>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                        @foreach ($deleted_fields as $field => $value)
                                            <div class="p-2 border border-red-100 dark:border-red-900/30 rounded-lg bg-red-50 dark:bg-red-900/10 flex flex-col justify-center text-xs">
                                                <div class="font-bold text-slate-700 dark:text-slate-300 mb-1 truncate">
                                                    🗑️ {{ $translateField($field) }}
                                                </div>
                                                <div class="flex items-center">
                                                    <span class="line-through decoration-red-400 text-slate-500 px-1 py-0.5 rounded truncate max-w-full" title="{{ is_string($value) ? strip_tags((string)$value) : '' }}">
                                                        {{ $formatLogValue($value) }}
                                                    </span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Phân trang paginate(5) của bạn --}}
            @if ($activities->hasPages())
                <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-800">
                    {{ $activities->links() }}
                </div>
            @endif
        @endif

    </div>
</div>
        </main>
        @if (session('new_password'))
            <div id="password-modal"
                class="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-[9999] p-4">

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
