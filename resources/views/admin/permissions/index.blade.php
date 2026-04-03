@extends('admin.layouts.app')

@section('title', 'Danh sách quyền hạn')

@section('content')
    <main class="flex-1 flex flex-col overflow-hidden">
        <div class="flex-1 overflow-y-auto p-8 space-y-6">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-black text-slate-900 dark:text-slate-100 tracking-tight">Danh sách quyền hạn (Permissions)
                    </h2>
                    <p class="text-slate-500 text-sm mt-1">Xem tất cả các quyền hạn có trong hệ thống</p>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden p-6 text-sm">
                @if ($permissions->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach ($permissions as $module => $modulePermissions)
                        <div class="border border-slate-200 dark:border-slate-700 rounded-lg p-4 bg-white dark:bg-slate-900 shadow-sm">
                            <h3 class="font-bold text-slate-900 dark:text-slate-100 mb-3 uppercase tracking-wider text-sm border-b border-slate-100 dark:border-slate-800 pb-2">{{ $module }}</h3>
                            <ul class="space-y-2.5 mt-3">
                                @foreach ($modulePermissions as $permission)
                                    <li class="flex items-start gap-2">
                                        <span class="material-symbols-outlined text-[18px] text-primary mt-0.5">verified_user</span>
                                        <div class="flex flex-col">
                                            <span class="text-slate-800 dark:text-slate-200 font-semibold">{{ $permission->name }}</span>
                                            <span class="text-[11px] text-slate-400 font-mono bg-slate-100 dark:bg-slate-800 px-1.5 py-0.5 rounded inline-block mt-0.5">{{ $permission->slug }}</span>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-center text-slate-500 py-8">Chưa có quyền hạn nào trong hệ thống.</p>
                @endif
            </div>
        </div>
    </main>
@endsection
