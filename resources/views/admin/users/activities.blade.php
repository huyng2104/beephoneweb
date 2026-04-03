@extends('admin.layouts.app')

@section('title', 'Lịch sử hoạt động')

@section('content')
    <main class="max-w-[1200px] mx-auto w-full p-4 md:p-8">
        
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-slate-900 dark:text-slate-100">
                Lịch sử hoạt động của: {{ $user->name }}
            </h1>
            <a href="{{ route('admin.users.show', $user->id) }}" class="flex items-center gap-2 text-primary hover:underline font-medium">
                <span class="material-symbols-outlined text-sm">arrow_back</span>
                Danh sách chi tiết
            </a>
        </div>

        {{-- BỘ LỌC --}}
        <form method="GET" action="{{ route('admin.users.activities', $user->id) }}" class="mb-6 bg-white dark:bg-slate-900 rounded-xl p-4 border border-primary/10 shadow-sm flex flex-wrap items-end gap-3">
            <div class="flex-1 min-w-[160px]">
                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">Loại đối tượng</label>
                <select name="log_name" onchange="this.form.submit()" class="w-full text-sm border-slate-200 dark:border-slate-700 rounded-md dark:bg-slate-800 dark:text-white pb-1.5 pt-1.5 px-3 focus:ring-primary focus:border-primary transition-colors">
                    <option value="">Tất cả</option>
                    <option value="auth" {{ request('log_name') === 'auth' ? 'selected' : '' }}>Bảo mật / Auth</option>
                    <option value="user" {{ request('log_name') === 'user' ? 'selected' : '' }}>Người dùng</option>
                    <option value="role" {{ request('log_name') === 'role' ? 'selected' : '' }}>Vai trò</option>
                    <option value="product" {{ request('log_name') === 'product' ? 'selected' : '' }}>Sản phẩm</option>
                    <option value="order" {{ request('log_name') === 'order' ? 'selected' : '' }}>Đơn hàng</option>
                    <option value="voucher" {{ request('log_name') === 'voucher' ? 'selected' : '' }}>Mã giảm giá</option>
                    <option value="post" {{ request('log_name') === 'post' ? 'selected' : '' }}>Bài viết</option>
                    <option value="post category" {{ request('log_name') === 'post category' ? 'selected' : '' }}>Danh mục bài viết</option>
                    <option value="brand" {{ request('log_name') === 'brand' ? 'selected' : '' }}>Thương hiệu</option>
                    <option value="category" {{ request('log_name') === 'category' ? 'selected' : '' }}>Danh mục SP</option>
                    <option value="wallet" {{ request('log_name') === 'wallet' ? 'selected' : '' }}>Ví tiền</option>
                    <option value="withdrawal request" {{ request('log_name') === 'withdrawal request' ? 'selected' : '' }}>Yêu cầu rút tiền</option>
                    <option value="point" {{ request('log_name') === 'point' ? 'selected' : '' }}>Điểm thưởng</option>
                    <option value="attribute" {{ request('log_name') === 'attribute' ? 'selected' : '' }}>Thuộc tính</option>
                    <option value="attribute value" {{ request('log_name') === 'attribute value' ? 'selected' : '' }}>Giá trị TT</option>
                    <option value="banner" {{ request('log_name') === 'banner' ? 'selected' : '' }}>Ảnh bìa</option>
                </select>
            </div>
            
            <div class="flex-1 min-w-[140px]">
                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">Hành động</label>
                <select name="event_type" onchange="this.form.submit()" class="w-full text-sm border-slate-200 dark:border-slate-700 rounded-md dark:bg-slate-800 dark:text-white pb-1.5 pt-1.5 px-3 focus:ring-primary focus:border-primary transition-colors">
                    <option value="">Tất cả</option>
                    <option value="created" {{ request('event_type') === 'created' ? 'selected' : '' }}>Thêm mới</option>
                    <option value="updated" {{ request('event_type') === 'updated' ? 'selected' : '' }}>Cập nhật / Sửa</option>
                    <option value="deleted" {{ request('event_type') === 'deleted' ? 'selected' : '' }}>Xóa</option>
                    <option value="login" {{ request('event_type') === 'login' ? 'selected' : '' }}>Đăng nhập</option>
                    <option value="logout" {{ request('event_type') === 'logout' ? 'selected' : '' }}>Đăng xuất</option>
                </select>
            </div>
            
            <div class="flex-1 min-w-[130px]">
                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">Từ ngày</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" onchange="this.form.submit()" class="w-full text-sm border-slate-200 dark:border-slate-700 rounded-md dark:bg-slate-800 dark:text-white pb-1.5 pt-1.5 px-3 focus:ring-primary focus:border-primary transition-colors">
            </div>
            
            <div class="flex-1 min-w-[130px]">
                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">Đến ngày</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" onchange="this.form.submit()" class="w-full text-sm border-slate-200 dark:border-slate-700 rounded-md dark:bg-slate-800 dark:text-white pb-1.5 pt-1.5 px-3 focus:ring-primary focus:border-primary transition-colors">
            </div>

            <div class="flex-1 min-w-[110px]">
                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">Sắp xếp</label>
                <select name="sort" onchange="this.form.submit()" class="w-full text-sm border-slate-200 dark:border-slate-700 rounded-md dark:bg-slate-800 dark:text-white pb-1.5 pt-1.5 px-3 focus:ring-primary focus:border-primary transition-colors">
                    <option value="desc" {{ request('sort', 'desc') === 'desc' ? 'selected' : '' }}>Mới nhất</option>
                    <option value="asc" {{ request('sort') === 'asc' ? 'selected' : '' }}>Cũ nhất</option>
                </select>
            </div>
            
            @if(request()->anyFilled(['log_name', 'event_type', 'date_from', 'date_to']) || request('sort') === 'asc')
            <div class="min-w-[100px] mb-[2px]">
                <a href="{{ route('admin.users.activities', $user->id) }}" class="flex items-center justify-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-red-600 bg-red-50 hover:bg-red-100 rounded-md transition-colors w-full border border-red-200 dark:bg-red-900/20 dark:text-red-400 dark:border-red-900/40">
                    <span class="material-symbols-outlined text-[16px]">close</span> Xóa lọc
                </a>
            </div>
            @endif
        </form>

        <div class="bg-white dark:bg-slate-900 rounded-xl p-6 mb-8 border border-primary/10 shadow-sm">
            <div class="flex-1">
                <div class="flex items-center justify-between mb-4">
                    <p class="text-sm font-bold text-slate-900 dark:text-slate-100">Toàn bộ lịch sử hoạt động</p>
                    <span class="text-xs font-medium bg-blue-50 text-blue-600 px-2 py-1 rounded-md dark:bg-blue-900/30 dark:text-blue-400">
                        Tổng: {{ $activities->total() }} bản ghi
                    </span>
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

                    @if ($activities->hasPages())
                        <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-800">
                            {{ $activities->links() }}
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </main>
@endsection
