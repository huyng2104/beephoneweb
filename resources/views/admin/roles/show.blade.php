@extends('admin.layouts.app')

@section('title', 'Chi tiết vai trò')

@section('content')
    <main class="flex-1 flex flex-col overflow-hidden px-8 bg-slate-50/50 dark:bg-slate-900/50">

        <div class="py-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-black text-slate-900 dark:text-slate-100 tracking-tight flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-3xl">visibility</span>
                    Chi tiết vai trò: <span class="text-primary">{{ $role->name }}</span>
                </h2>
                <p class="text-slate-500 text-sm mt-1">Xem chi tiết thông tin và danh sách quyền hạn đã cấp</p>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('admin.role.index') }}"
                    class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold px-5 py-2.5 rounded-xl shadow-sm flex items-center gap-2 transition-all text-sm">
                    <span class="material-symbols-outlined text-lg">arrow_back</span>
                    Quay lại
                </a>
                <a href="{{ route('admin.role.edit', $role->id) }}"
                    class="bg-primary hover:bg-primary/90 text-slate-900 font-bold px-5 py-2.5 rounded-xl shadow-sm flex items-center gap-2 transition-all text-sm">
                    <span class="material-symbols-outlined text-lg">edit_square</span>
                    Chỉnh sửa ngay
                </a>
            </div>
        </div>

        <div class="space-y-6 max-w-7xl pb-10">

            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm p-6">
                <h3 class="text-lg font-bold text-slate-900 dark:text-slate-100 mb-5 flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">info</span>
                    Thông tin chung
                </h3>

                <div
                    class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-slate-50/50 dark:bg-slate-900/50 p-5 rounded-xl border border-slate-100 dark:border-slate-700">
                    <div class="space-y-1">
                        <span class="block text-xs font-bold text-slate-400 uppercase tracking-wider">Tên vai trò</span>
                        <p class="text-base font-medium text-slate-800 dark:text-slate-200">
                            {{ $role->name }}
                        </p>
                    </div>

                    <div class="space-y-1">
                        <span class="block text-xs font-bold text-slate-400 uppercase tracking-wider">Mô tả</span>
                        <p class="text-base font-medium text-slate-800 dark:text-slate-200">
                            {{ $role->description ?: 'Không có mô tả cho vai trò này.' }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm p-6">

                <div class="mb-6 pb-5 border-b border-slate-100 dark:border-slate-700">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary">verified_user</span>
                        Bảng phân quyền chi tiết
                    </h3>
                    <p class="text-xs text-slate-500 mt-1">Những mục sáng màu là quyền hạn mà vai trò này được phép thực
                        hiện.</p>
                </div>

                @php
                    $moduleNames = [
                        'product' => 'Sản phẩm',
                        'attribute' => 'Thuộc tính',
                        'category' => 'Danh mục',
                        'brand' => 'Thương hiệu',
                        'order' => 'Đơn hàng',
                        'customer' => 'Khách hàng',
                        'voucher' => 'Mã giảm giá',
                    ];
                @endphp

                <div class="space-y-6">
                    @foreach ($permissions as $moduleKey => $modulePermissions)
                        @php
                            // 🔍 Lọc xem trong module này có quyền nào khớp với mảng $rolePermissions không
                            $validPermissions = $modulePermissions->filter(function ($permission) use (
                                $rolePermissions,
                            ) {
                                return in_array($permission->id, $rolePermissions);
                            });
                        @endphp

                        {{-- 🛑 Nếu có quyền khớp thì mới bốc ra vẽ --}}
                        @if ($validPermissions->isNotEmpty())
                            <div
                                class="p-5 rounded-xl border border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900/50 space-y-4">

                                <div class="pb-3 border-b border-slate-200/60 dark:border-slate-700/60">
                                    <span
                                        class="text-sm font-bold text-slate-800 dark:text-slate-200 flex items-center gap-2">
                                        <span class="material-symbols-outlined text-slate-400">folder_managed</span>
                                        Quản lý {{ $moduleNames[$moduleKey] ?? ucfirst($moduleKey) }}
                                    </span>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                    @foreach ($validPermissions as $permission)
                                        <div
                                            class="flex items-center gap-3 p-3 bg-white dark:bg-slate-800 rounded-lg border border-primary/50 shadow-sm transition-all">
                                            <span
                                                class="material-symbols-outlined text-green-500 text-xl">check_circle</span>
                                            <span class="text-sm font-bold text-slate-800 dark:text-slate-200">
                                                {{ $permission->name }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>

        </div>
    </main>
@endsection
