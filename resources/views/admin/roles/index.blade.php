@extends('admin.layouts.app')

@section('title', 'Quản lý nhóm quản trị')

@section('content')
    <main class="flex-1 flex flex-col overflow-hidden">

        @include('popup_notify.index')
        <div class="flex-1 overflow-y-auto p-8 space-y-6">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-black text-slate-900 dark:text-slate-100 tracking-tight">Quản lý nhóm quản trị
                    </h2>
                    <p class="text-slate-500 text-sm mt-1">Xem và quản lý tất cả vai trò trên hệ thống</p>
                </div>
                <a href="{{ route('admin.role.create') }}">
                    <button
                        class="bg-primary hover:bg-primary/90 text-slate-900 font-bold px-5 py-2.5 rounded-xl shadow-sm shadow-primary/20 flex items-center gap-2 transition-all">
                        <span class="material-symbols-outlined">person_add</span>
                        Thêm vai trò mới
                    </button>
                </a>
            </div>

            <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead
                            class="bg-slate-50 dark:bg-slate-900/50 text-slate-500 text-xs font-bold uppercase tracking-wider">
                            <tr>
                                <th class="px-6 py-4">STT</th>
                                <th class="px-6 py-4">Tên vai trò</th>
                                <th class="px-6 py-4">Mô tả</th>
                                {{-- 1. Thêm 2 tiêu đề cột mới --}}
                                <th class="px-6 py-4">Thành viên</th>
                                <th class="px-6 py-4">Quyền hạn (Permissions)</th>
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
                                            <span class="text-sm font-bold">{{ $role->name }}</span>
                                        </div>
                                    </td>

                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-1.5 text-slate-600 dark:text-slate-500">
                                            <span class="text-sm">{{ $role->description }}</span>
                                        </div>
                                    </td>

                                    {{-- 2. Cột hiển thị Số lượng thành viên --}}
                                    <td class="px-6 py-4">
                                        <a href="{{ route('admin.member', ['role' => $role->id]) }}"
                                            class="inline-block transition-transform hover:scale-105"
                                            title="Xem danh sách thành viên nhóm này">

                                            <span
                                                class="inline-flex items-center justify-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 hover:bg-blue-200 dark:bg-blue-900 dark:text-blue-300 dark:hover:bg-blue-800 transition-colors cursor-pointer shadow-sm">
                                                {{ $role->users_count ?? $role->users()->count() }} thành viên
                                            </span>

                                        </a>
                                    </td>

                                    {{-- 3. Cột hiển thị Danh sách Quyền hạn (Giới hạn hiển thị để tránh bể giao diện) --}}
                                    <td class="px-6 py-4">
                                        <div class="flex flex-wrap gap-1.5 max-w-xs">
                                            @if ($role->permissions->count() > 0)
                                                {{-- Chỉ hiển thị tối đa 3 quyền đầu tiên --}}
                                                @foreach ($role->permissions->take(3) as $permission)
                                                    <span
                                                        class="px-2 py-1 text-[10px] font-semibold rounded-md bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-600">
                                                        {{ $permission->name }}
                                                    </span>
                                                @endforeach

                                                {{-- Nếu có nhiều hơn 3 quyền, hiển thị số lượng còn lại --}}
                                                @if ($role->permissions->count() > 3)
                                                    <span
                                                        class="px-2 py-1 text-[10px] font-semibold rounded-md bg-slate-200 text-slate-700 dark:bg-slate-600 dark:text-slate-200">
                                                        +{{ $role->permissions->count() - 3 }} quyền khác
                                                    </span>
                                                @endif
                                            @else
                                                <span class="text-xs text-slate-400 italic">Chưa cấp quyền</span>
                                            @endif
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
                                            <form action="{{ route('admin.role.destroy', $role->id) }}" method="post"
                                                class="inline-block">
                                                @csrf
                                                @method('delete')
                                                <button type="submit"
                                                    onclick="return confirm('Bạn có chắc chắn muốn xóa vai trò này?')"
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

                <div class="p-4 border-t border-slate-200 dark:border-slate-700">
                    {{ $roles->links('pagination::tailwind') }}
                </div>
            </div>
        </div>
    </main>
@endsection
