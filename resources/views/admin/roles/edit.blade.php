@extends('admin.layouts.app')

@section('title', 'Chỉnh sửa vai trò')

@section('content')
<main class="flex-1 flex flex-col overflow-hidden px-8 bg-slate-50/50 dark:bg-slate-900/50">

    <div class="py-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-black text-slate-900 dark:text-slate-100 tracking-tight flex items-center gap-2">
                <span class="material-symbols-outlined text-primary text-3xl">edit_square</span>
                Chỉnh sửa vai trò: <span class="text-primary">{{ $role->name }}</span>
            </h2>
            <p class="text-slate-500 text-sm mt-1">Cập nhật thông tin và điều chỉnh quyền hạn truy cập của vai trò</p>
        </div>

        <a href="{{ route('admin.role.index') }}">
            <button class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold px-5 py-2.5 rounded-xl shadow-sm flex items-center gap-2 transition-all text-sm">
                <span class="material-symbols-outlined text-lg">arrow_back</span>
                Quay lại danh sách
            </button>
        </a>
    </div>

    <form action="{{ route('admin.role.update', $role->id) }}" method="POST" class="space-y-6 max-w-7xl pb-10">
        @csrf
        @method('PUT') <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm p-6">
            <h3 class="text-lg font-bold text-slate-900 dark:text-slate-100 mb-5 flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">analytics</span>
                Thông tin vai trò
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label for="name" class="block text-sm font-bold text-slate-700 dark:text-slate-300">
                        Tên vai trò <span class="text-red-500">*</span>
                    </label>

                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">badge</span>
                        <input type="text" name="name" id="name" placeholder="Ví dụ: Quản trị viên"
                            value="{{ old('name', $role->name) }}"
                            class="w-full pl-12 pr-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 text-sm font-medium focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all outline-none">
                    </div>
                    @error('name')
                        <span class="text-red-500 text-sm">{{$message}}</span>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label for="description" class="block text-sm font-bold text-slate-700 dark:text-slate-300">
                        Mô tả ngắn
                    </label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">description</span>
                        <input type="text" name="description" id="description" placeholder="Tóm tắt trách nhiệm của vai trò..."
                            value="{{ old('description', $role->description) }}"
                            class="w-full pl-12 pr-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 text-sm font-medium focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all outline-none">
                    </div>
                    @error('description')
                        <span class="text-red-500 text-sm">{{$message}}</span>
                    @enderror
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm p-6">

            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6 pb-5 border-b border-slate-100 dark:border-slate-700">
                <div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary">rule</span>
                        Thiết lập quyền truy cập cụ thể
                    </h3>
                    <p class="text-xs text-slate-500 mt-1">Đánh dấu tích để gán quyền cho vai trò này</p>
                </div>

                <button type="button" id="select-all-permissions" class="text-xs font-bold text-primary hover:text-primary/80 transition-colors flex items-center gap-1 self-start sm:self-auto">
                    <span class="material-symbols-outlined text-sm">done_all</span>
                    Chọn tất cả quyền
                </button>
            </div>

            @php
                $moduleNames = [
                    'product'   => 'Sản phẩm',
                    'attribute' => 'Thuộc tính',
                    'category'  => 'Danh mục',
                    'brand'     => 'Thương hiệu',
                    'order'     => 'Đơn hàng',
                    'customer'  => 'Khách hàng',
                    'voucher'   => 'Mã giảm giá',
                ];
            @endphp

            <div class="space-y-6">
                @foreach ($permissions as $moduleKey => $modulePermissions)
                    <div class="p-5 rounded-xl border border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900/50 space-y-4">

                        <div class="flex items-center justify-between pb-3 border-b border-slate-200/60 dark:border-slate-700/60">
                            <span class="text-sm font-bold text-slate-800 dark:text-slate-200 flex items-center gap-2">
                                <span class="material-symbols-outlined text-slate-400">folder_managed</span>
                                Quản lý {{ $moduleNames[$moduleKey] ?? ucfirst($moduleKey) }}
                            </span>

                            <button type="button" class="check-module text-xs font-bold text-slate-400 hover:text-primary transition-colors">
                                Chọn tất cả mục này
                            </button>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            @foreach ($modulePermissions as $permission)
                                <label class="flex items-center gap-3 p-3 bg-white dark:bg-slate-800 rounded-lg border border-slate-100 dark:border-slate-700 cursor-pointer hover:border-primary/50 dark:hover:border-primary/50 transition-all">
                                    <input type="checkbox" name="permissions[]" value="{{ $permission->id }}"
                                        class="permission-checkbox w-4 h-4 rounded text-primary focus:ring-primary/30 border-slate-300 dark:border-slate-600 dark:bg-slate-900"
                                        {{ in_array($permission->id, $rolePermissions) ? 'checked' : '' }}>
                                        <span class="text-sm font-medium text-slate-700 dark:text-slate-300">
                                        {{ $permission->name }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 pt-4">
            <a href="{{ route('admin.role.index') }}" class="px-6 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 font-bold text-sm hover:bg-slate-50 dark:hover:bg-slate-700 transition-all">
                Hủy bỏ
            </a>
            <button type="submit" class="bg-primary hover:bg-primary/90 text-slate-900 font-bold px-6 py-2.5 rounded-xl shadow-sm flex items-center gap-2 transition-all">
                <span class="material-symbols-outlined">save</span>
                Cập nhật vai trò
            </button>
        </div>
    </form>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const btnSelectAll = document.getElementById('select-all-permissions');
            const allCheckboxes = document.querySelectorAll('.permission-checkbox');

            // 1. Logic Chọn tất cả toàn bộ hệ thống
            btnSelectAll.addEventListener('click', function() {
                const allChecked = Array.from(allCheckboxes).every(cb => cb.checked);

                allCheckboxes.forEach(cb => {
                    cb.checked = !allChecked;
                });

                this.innerHTML = !allChecked ?
                    '<span class="material-symbols-outlined text-sm">deselect</span> Bỏ chọn tất cả' :
                    '<span class="material-symbols-outlined text-sm">done_all</span> Chọn tất cả quyền';
            });

            // 2. Logic Chọn tất cả trong Module lẻ
            const btnModules = document.querySelectorAll('.check-module');
            btnModules.forEach(btn => {
                btn.addEventListener('click', function() {
                    const parentModule = this.closest('.p-5');
                    const checkboxesInModule = parentModule.querySelectorAll('.permission-checkbox');
                    const moduleChecked = Array.from(checkboxesInModule).every(cb => cb.checked);

                    checkboxesInModule.forEach(cb => {
                        cb.checked = !moduleChecked;
                    });

                    this.innerText = !moduleChecked ? 'Bỏ chọn tất cả' : 'Chọn tất cả mục này';
                });
            });
        });
    </script>
</main>
@endsection
