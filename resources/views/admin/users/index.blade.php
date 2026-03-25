@extends('admin.layout')

@section('title', 'Quản lý người dùng')

@section('content')
<div class="flex flex-wrap justify-between items-end gap-4">
    <div class="flex flex-col gap-2">
        <h1 class="text-[#181611] text-3xl font-black leading-tight tracking-tight">Quản lý người dùng</h1>
        <p class="text-[#8a8060] text-base font-normal">Hệ thống quản trị tài khoản, phân quyền và giám sát hoạt động</p>
    </div>
    <div class="flex gap-3">
        <a href="/admin/users/activities" class="flex items-center gap-2 cursor-pointer justify-center rounded-lg h-11 px-6 bg-white border border-[#e6e3db] text-[#181611] text-sm font-bold hover:bg-gray-50">
            <span class="material-symbols-outlined text-xl">history</span>
            Lịch sử hệ thống
        </a>
        <a href="/admin/users/create" class="flex items-center gap-2 cursor-pointer justify-center rounded-lg h-11 px-6 bg-primary text-[#181611] text-sm font-bold shadow-sm hover:opacity-90">
            <span class="material-symbols-outlined text-xl">person_add</span>
            Thêm người dùng mới
        </a>
    </div>
</div>

<div class="border-b border-[#e6e3db]">
    <div class="flex gap-8">
        <a class="flex items-center gap-2 border-b-2 border-[#181611] text-[#181611] pb-4 px-2" href="#">
            <span class="material-symbols-outlined text-lg">group</span>
            <p class="text-sm font-bold">Danh sách người dùng</p>
        </a>
        <a class="flex items-center gap-2 border-b-2 border-transparent text-[#8a8060] pb-4 px-2 hover:text-[#181611] transition-all" href="#roles">
            <span class="material-symbols-outlined text-lg">admin_panel_settings</span>
            <p class="text-sm font-bold">Vai trò &amp; Quyền hạn</p>
        </a>
    </div>
</div>

<div class="flex flex-col gap-6 bg-white rounded-xl shadow-sm border border-[#e6e3db] p-6">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div class="flex-1 min-w-[300px] max-w-md">
            <label class="relative block">
                <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-[#8a8060]">
                    <span class="material-symbols-outlined">search</span>
                </span>
                <input class="w-full bg-[#f5f3f0] border-none rounded-lg h-12 pl-12 pr-4 text-sm focus:ring-2 focus:ring-primary focus:bg-white transition-all" placeholder="Tìm kiếm theo tên, email hoặc số điện thoại..." type="text" />
            </label>
        </div>
        <div class="flex gap-3">
            <select class="bg-[#f5f3f0] border-none rounded-lg h-12 px-4 text-sm text-[#181611] focus:ring-primary">
                <option>Tất cả vai trò</option>
                <option>Admin</option>
                <option>Nhân viên</option>
                <option>Khách hàng</option>
            </select>
            <select class="bg-[#f5f3f0] border-none rounded-lg h-12 px-4 text-sm text-[#181611] focus:ring-primary">
                <option>Trạng thái</option>
                <option>Đang hoạt động</option>
                <option>Đã khóa</option>
            </select>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-[#e6e3db]">
                    <th class="py-4 px-4 text-xs font-bold uppercase tracking-wider text-[#8a8060]">ID</th>
                    <th class="py-4 px-4 text-xs font-bold uppercase tracking-wider text-[#8a8060]">Người dùng</th>
                    <th class="py-4 px-4 text-xs font-bold uppercase tracking-wider text-[#8a8060]">Vai trò</th>
                    <th class="py-4 px-4 text-xs font-bold uppercase tracking-wider text-[#8a8060]">Trạng thái</th>
                    <th class="py-4 px-4 text-xs font-bold uppercase tracking-wider text-[#8a8060]">Ngày tạo</th>
                    <th class="py-4 px-4 text-xs font-bold uppercase tracking-wider text-[#8a8060] text-right">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#f5f3f0]">
                @forelse($users as $user)
                <tr class="hover:bg-gray-50 transition-colors {{ $user->isLocked() ? 'bg-red-50/30' : '' }}">
                    <td class="py-4 px-4 text-sm font-mono text-[#8a8060]">#US-{{ $user->id }}</td>
                    <td class="py-4 px-4">
                        <div class="flex items-center gap-3">
                            <div class="size-10 rounded-full bg-cover" style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuCNTqCJ0Bwe189mNgPjBt2DX5s84ISoCySYX11MlkLwTa1cHq0xyFRKhuQElGtSzScMsPal17Ulc9q5zT9dVjzPy0MnFTST5qa3qZSQwqw-gQwZu2tUBDA7rilmvJWpERjoFCKo6_27i9ctp1aRD6-sycJQ16KeEt7tHRjdoQcX3f2T68nGr2N7MegDXrv48c6myxP4w9P5UYeiXT3U5bikJBjbs93O0vi9HmQX2psEM_uqGdrjVbzxG6xB3fZIANF3FvQj5BfXaxk');"></div>
                            <div class="flex flex-col">
                                <span class="text-sm font-bold text-[#181611]">{{ $user->name }}</span>
                                <span class="text-xs text-[#8a8060]">{{ $user->email }}</span>
                            </div>
                        </div>
                    </td>
                    <td class="py-4 px-4">
                        @php
                        $roleName = $user->role->name ?? 'user';
                        $roleLabel = ucfirst($roleName);
                        $roleClass = $roleName === 'admin' ? 'bg-primary/20 text-[#7c5e00]' : ($roleName === 'staff' ? 'bg-[#f5f3f0] text-[#181611]' : 'bg-[#f5f3f0] text-[#181611]');
                        @endphp
                        <span class="px-2.5 py-1 rounded-full text-[11px] font-bold {{ $roleClass }} uppercase tracking-wide">
                            {{ $roleLabel === 'User' ? 'Khách hàng' : $roleLabel }}
                        </span>
                    </td>
                    <td class="py-4 px-4">
                        @if($user->isLocked())
                        <div class="flex items-center gap-2">
                            <div class="size-2 rounded-full bg-red-500"></div>
                            <span class="text-sm font-medium text-red-600">Đã khóa</span>
                        </div>
                        @else
                        <div class="flex items-center gap-2">
                            <div class="size-2 rounded-full bg-green-500"></div>
                            <span class="text-sm font-medium text-green-700">Đang hoạt động</span>
                        </div>
                        @endif
                    </td>
                    <td class="py-4 px-4 text-sm text-[#8a8060]">{{ optional($user->created_at)->format('d/m/Y') ?? 'N/A' }}</td>
                    <td class="py-4 px-4 text-right">
                        <div class="flex justify-end gap-2">
                            <a href="/admin/users/{{ $user->id }}/activities" class="p-2 text-[#8a8060] hover:text-primary transition-colors" title="Xem Log">
                                <span class="material-symbols-outlined">assignment_ind</span>
                            </a>
                            <a href="/admin/users/{{ $user->id }}/edit" class="p-2 text-[#8a8060] hover:text-primary transition-colors" title="Chỉnh sửa">
                                <span class="material-symbols-outlined">edit</span>
                            </a>
                            <form method="POST" action="/admin/users/{{ $user->id }}/lock">
                                @csrf
                                <button class="p-2 text-[#8a8060] hover:text-red-600 transition-colors" title="Khóa/Mở khóa" type="submit">
                                    <span class="material-symbols-outlined">lock_open</span>
                                </button>
                            </form>
                            <form method="POST" action="/admin/users/{{ $user->id }}/reset-password">
                                @csrf
                                <button class="p-2 text-[#8a8060] hover:text-primary transition-colors" title="Reset mật khẩu" type="submit">
                                    <span class="material-symbols-outlined">key</span>
                                </button>
                            </form>
                            <form method="POST" action="/admin/users/{{ $user->id }}" onsubmit="return confirm('Chắc chắn xóa?')">
                                @csrf
                                @method('DELETE')
                                <button class="p-2 text-[#8a8060] hover:text-red-600 transition-colors" title="Xóa" type="submit">
                                    <span class="material-symbols-outlined">delete</span>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-10 text-center text-[#8a8060]">Chưa có dữ liệu</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="flex items-center justify-between border-t border-[#f5f3f0] pt-6">
        <p class="text-sm text-[#8a8060]">Hiển thị {{ $users->count() }} người dùng</p>
        <div class="text-sm">{{ $users->links() }}</div>
    </div>
</div>

<div id="roles" class="flex flex-col gap-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-[#181611]">Phân quyền Vai trò</h2>
            <p class="text-[#8a8060] text-sm">Quản lý ma trận quyền hạn cho từng loại tài khoản trong hệ thống</p>
        </div>
        <button class="flex items-center gap-2 rounded-lg h-10 px-4 bg-white border border-[#e6e3db] text-[#181611] text-sm font-bold hover:bg-gray-50" type="button">
            <span class="material-symbols-outlined">add</span>
            Tạo vai trò mới
        </button>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-xl border-2 border-primary shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <span class="px-3 py-1 bg-primary text-[#181611] text-[11px] font-black rounded uppercase">Administrator</span>
                <span class="material-symbols-outlined text-primary">verified_user</span>
            </div>
            <ul class="space-y-3 mb-6">
                <li class="flex items-center gap-3 text-sm font-medium">
                    <span class="material-symbols-outlined text-green-600 text-lg">check_circle</span>
                    Toàn quyền hệ thống
                </li>
                <li class="flex items-center gap-3 text-sm font-medium">
                    <span class="material-symbols-outlined text-green-600 text-lg">check_circle</span>
                    Quản lý đơn hàng &amp; Tài chính
                </li>
                <li class="flex items-center gap-3 text-sm font-medium">
                    <span class="material-symbols-outlined text-green-600 text-lg">check_circle</span>
                    Quản lý kho sản phẩm
                </li>
            </ul>
            <button class="w-full h-10 border border-[#e6e3db] rounded-lg text-sm font-bold hover:bg-gray-50" type="button">Cấu hình chi tiết</button>
        </div>
        <div class="bg-white p-6 rounded-xl border border-[#e6e3db] shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <span class="px-3 py-1 bg-[#f5f3f0] text-[#181611] text-[11px] font-black rounded uppercase">Nhân viên</span>
                <span class="material-symbols-outlined text-[#8a8060]">badge</span>
            </div>
            <ul class="space-y-3 mb-6">
                <li class="flex items-center gap-3 text-sm font-medium">
                    <span class="material-symbols-outlined text-green-600 text-lg">check_circle</span>
                    Xem &amp; Xử lý đơn hàng
                </li>
                <li class="flex items-center gap-3 text-sm font-medium">
                    <span class="material-symbols-outlined text-[#8a8060] text-lg">cancel</span>
                    <span class="text-[#8a8060]">Sửa thông tin sản phẩm</span>
                </li>
                <li class="flex items-center gap-3 text-sm font-medium">
                    <span class="material-symbols-outlined text-green-600 text-lg">check_circle</span>
                    Quản lý kho (Xem)
                </li>
            </ul>
            <button class="w-full h-10 border border-[#e6e3db] rounded-lg text-sm font-bold hover:bg-gray-50" type="button">Cấu hình chi tiết</button>
        </div>
        <div class="bg-white p-6 rounded-xl border border-[#e6e3db] shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <span class="px-3 py-1 bg-[#f5f3f0] text-[#181611] text-[11px] font-black rounded uppercase">Khách hàng</span>
                <span class="material-symbols-outlined text-[#8a8060]">person</span>
            </div>
            <ul class="space-y-3 mb-6">
                <li class="flex items-center gap-3 text-sm font-medium">
                    <span class="material-symbols-outlined text-green-600 text-lg">check_circle</span>
                    Mua hàng &amp; Thanh toán
                </li>
                <li class="flex items-center gap-3 text-sm font-medium">
                    <span class="material-symbols-outlined text-green-600 text-lg">check_circle</span>
                    Theo dõi lịch sử đơn hàng
                </li>
                <li class="flex items-center gap-3 text-sm font-medium">
                    <span class="material-symbols-outlined text-[#8a8060] text-lg">cancel</span>
                    <span class="text-[#8a8060]">Truy cập Admin Panel</span>
                </li>
            </ul>
            <button class="w-full h-10 border border-[#e6e3db] rounded-lg text-sm font-bold hover:bg-gray-50" type="button">Cấu hình chi tiết</button>
        </div>
    </div>
</div>
@endsection