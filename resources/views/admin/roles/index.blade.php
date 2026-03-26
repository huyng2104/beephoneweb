@extends('admin.layouts.app')

@section('title', 'Quản lý nhóm quản trị')

@section('content')
    <main class="flex-1 flex flex-col overflow-hidden">

        @include('popup_notify.index')
        <!-- Body Content -->
        <div class="flex-1 overflow-y-auto p-8 space-y-6">
            <!-- Breadcrumbs & Actions -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-black text-slate-900 dark:text-slate-100 tracking-tight">Quản lý nhóm quản trị
                    </h2>
                    <p class="text-slate-500 text-sm mt-1">Xem và quản lý tất cả vai trò trên hệ thống
                    </p>
                </div>
                <a href="{{ route('admin.role.create') }}">
                    <button
                        class="bg-primary hover:bg-primary/90 text-slate-900 font-bold px-5 py-2.5 rounded-xl shadow-sm shadow-primary/20 flex items-center gap-2 transition-all">
                        <span class="material-symbols-outlined">person_add</span>
                        Thêm vai trò mới
                    </button>
                </a>
            </div>
            <!-- Stats Bar (Optional UI touch) -->
            {{-- <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-white dark:bg-slate-800 p-4 rounded-xl border border-slate-200 dark:border-slate-700">
                    <p class="text-slate-500 text-xs font-bold uppercase tracking-wider">Tổng người dùng</p>
                    <p class="text-2xl font-black mt-1">{{ $users->total() }}</p>
                </div>
                <div class="bg-white dark:bg-slate-800 p-4 rounded-xl border border-slate-200 dark:border-slate-700">
                    <p class="text-slate-500 text-xs font-bold uppercase tracking-wider">Khách hàng</p>
                    <p class="text-2xl font-black mt-1 text-primary">1,120</p>
                </div>
                <div class="bg-white dark:bg-slate-800 p-4 rounded-xl border border-slate-200 dark:border-slate-700">
                    <p class="text-slate-500 text-xs font-bold uppercase tracking-wider">Nhân viên</p>
                    <p class="text-2xl font-black mt-1 text-blue-500">{{ $totalStaff }}</p>
                </div>
                <div class="bg-white dark:bg-slate-800 p-4 rounded-xl border border-slate-200 dark:border-slate-700">
                    <p class="text-slate-500 text-xs font-bold uppercase tracking-wider">Bị khóa</p>
                    <p class="text-2xl font-black mt-1 text-red-500">{{ $totalBanned }}</p>
                </div>
            </div> --}}
            <!-- Filters & Table -->
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden">

                <!-- Table -->

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead
                            class="bg-slate-50 dark:bg-slate-900/50 text-slate-500 text-xs font-bold uppercase tracking-wider">
                            <tr>
                                <th class="px-6 py-4">STT</th>
                                <th class="px-6 py-4">Tên vai trò</th>
                                <th class="px-6 py-4">Mô tả</th>
                                <th class="px-6 py-4 text-right">Hành động</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                            @foreach ($roles as $index => $role)
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">

                                    <td class="px-6 py-4 text-sm font-medium text-slate-400">
                                        {{ $index + 1 }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-1.5 text-slate-600 dark:text-slate-500">
                                            <span class="text-xs font-bold">{{ $role->name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-1.5 text-slate-600 dark:text-slate-500">
                                            <span class="text-xs font-bold">{{ $role->description }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex justify-end gap-2">
                                            <a href="{{ route('admin.role.show', $role->id) }}">
                                                <button class="p-2 text-slate-400 hover:text-blue-500 transition-colors"
                                                    title="Xem chi tiết">
                                                    <span class="material-symbols-outlined text-lg">visibility</span>
                                                </button>
                                            </a>
                                            <a href="{{ route('admin.role.edit', $role->id) }}">
                                                <button class="p-2 text-slate-400 hover:text-primary transition-colors"
                                                    title="Chỉnh sửa">
                                                    <span class="material-symbols-outlined text-lg">edit</span>
                                                </button>
                                            </a>
                                            <form action="{{ route('admin.role.destroy', $role->id) }}" method="post">
                                                @csrf
                                                @method('delete')
                                                <button onclick="return(confirm('Xóa vai trò'))"
                                                    class="p-2 text-slate-400 hover:text-red-600 transition-colors"
                                                    title="Xóa vai trò">
                                                    <span class="material-symbols-outlined text-lg">delete</span>
                                                </button>
                                            </form>
                                        </div>
                                    </td>

                                </tr>
                            @endforeach

                        </tbody>
                    </table>

                </div>
                <div class="p-4">
                    {{ $roles->links('pagination::tailwind') }}
                </div>

            </div>
        </div>
    </main>
@endsection
