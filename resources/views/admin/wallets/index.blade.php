@extends('admin.layouts.app')

@section('title', 'Quản lý ví')

@section('content')
    <main class="flex-1 flex flex-col overflow-hidden">

        @include('popup_notify.index')

        <div class="flex-1 overflow-y-auto p-8 space-y-6">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-black text-slate-900 dark:text-slate-100 tracking-tight">Quản lý ví</h2>
                    <p class="text-slate-500 text-sm mt-1">Xem và quản lý số dư, trạng thái ví của người dùng trên hệ thống
                    </p>
                </div>
                <a href="#">
                    <button
                        class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700/50 text-slate-700 dark:text-slate-200 font-bold px-5 py-2.5 rounded-xl shadow-sm flex items-center gap-2 transition-all">
                        <span class="material-symbols-outlined">download</span>
                        Xuất báo cáo
                    </button>
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white dark:bg-slate-800 p-4 rounded-xl border border-slate-200 dark:border-slate-700">
                    <p class="text-slate-500 text-xs font-bold uppercase tracking-wider">Tổng số dư hệ thống</p>
                    <p class="text-2xl font-black mt-1 text-slate-900 dark:text-slate-100">
                        {{ number_format($totalBalance ?? 0) }}đ
                    </p>
                </div>

                <div class="bg-white dark:bg-slate-800 p-4 rounded-xl border border-slate-200 dark:border-slate-700">
                    <p class="text-slate-500 text-xs font-bold uppercase tracking-wider">Tổng nạp (Tháng này)</p>
                    <p class="text-2xl font-black mt-1 text-blue-500">
                        +{{ number_format($totalDeposit ?? 0) }}đ
                    </p>
                </div>

                <div class="bg-white dark:bg-slate-800 p-4 rounded-xl border border-slate-200 dark:border-slate-700">
                    <p class="text-slate-500 text-xs font-bold uppercase tracking-wider">Tổng rút (Tháng này)</p>
                    <p class="text-2xl font-black mt-1 text-red-500">
                        -{{ number_format($totalWithdraw ?? 0) }}đ
                    </p>
                </div>

                <div class="bg-white dark:bg-slate-800 p-4 rounded-xl border border-slate-200 dark:border-slate-700">
                    <p class="text-slate-500 text-xs font-bold uppercase tracking-wider">Ví đang bị khóa</p>
                    <p class="text-2xl font-black mt-1 text-orange-500">
                        {{ number_format($lockedWalletsCount ?? 0) }}
                    </p>
                </div>
            </div>

            <div
                class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                <form action="" method="GET" id="filter-form">
                    <div class="p-4 border-b border-slate-100 dark:border-slate-700 flex flex-wrap gap-4 items-center">
                        <div class="flex-1 min-w-[300px] relative">
                            <span
                                class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">search</span>
                            <input name="search" value="{{ request('search') }}"
                                class="w-full bg-slate-50 dark:bg-slate-900 border-slate-200 dark:border-slate-700 rounded-lg pl-10 focus:ring-primary focus:border-primary text-sm dark:text-slate-200"
                                placeholder="Tìm tên, email người dùng..." type="text" />
                        </div>

                        <div class="flex gap-2">
                            <select name="status" onchange="this.form.submit()"
                                class="bg-slate-50 dark:bg-slate-900 border-slate-200 dark:border-slate-700 rounded-lg text-sm font-medium focus:ring-primary focus:border-primary dark:text-slate-200">
                                <option value="">Trạng thái ví</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Đang hoạt động
                                </option>
                                <option value="locked" {{ request('status') == 'locked' ? 'selected' : '' }}>Đã khóa
                                </option>
                            </select>

                            <select name="sort_balance" onchange="this.form.submit()"
                                class="bg-slate-50 dark:bg-slate-900 border-slate-200 dark:border-slate-700 rounded-lg text-sm font-medium focus:ring-primary focus:border-primary dark:text-slate-200">
                                <option value="">Sắp xếp số dư</option>
                                <option value="desc" {{ request('sort_balance') == 'desc' ? 'selected' : '' }}>Nhiều nhất
                                </option>
                                <option value="asc" {{ request('sort_balance') == 'asc' ? 'selected' : '' }}>Ít nhất
                                </option>
                            </select>

                            @if (request()->filled('search') || request()->filled('status') || request()->filled('sort_balance'))
                                <a href="{{ route('admin.wallet.index') }}"
                                    class="bg-slate-100 dark:bg-slate-900 p-2 rounded-lg border border-slate-200 dark:border-slate-700 flex items-center justify-center text-slate-500 hover:text-red-500 transition-colors"
                                    title="Xóa tất cả bộ lọc">
                                    <span class="material-symbols-outlined">filter_list_off</span>
                                </a>
                            @endif

                            <button type="submit"
                                class="bg-primary hover:bg-primary/90 p-2 rounded-lg border border-transparent flex items-center justify-center text-slate-900 transition-colors font-medium text-sm gap-1">
                                <span class="material-symbols-outlined text-lg">filter_list</span>
                                Tìm kiếm
                            </button>
                        </div>
                    </div>
                </form>

                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead
                            class="bg-slate-50 dark:bg-slate-900/50 text-slate-500 text-xs font-bold uppercase tracking-wider">
                            <tr>
                                <th class="px-6 py-4">STT</th>
                                <th class="px-6 py-4">Người dùng</th>
                                <th class="px-6 py-4">Số dư khả dụng</th>
                                <th class="px-6 py-4">Trạng thái</th>
                                <th class="px-6 py-4">Cập nhật lúc</th>
                                <th class="px-6 py-4 text-right">Hành động</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700">

                            @forelse ($wallets as $index => $wallet)
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                                    <td class="px-6 py-4 text-sm font-medium text-slate-400">
                                        {{ $wallets->firstItem() + $index }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="size-10 rounded-full bg-slate-200 overflow-hidden flex-shrink-0">
                                                <img class="w-full h-full object-cover"
                                                    src="{{ $wallet->user->avatar ? Storage::url($wallet->user->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($wallet->user->name) . '&background=cbd5e1&color=1e293b' }}"
                                                    alt="Avatar" />
                                            </div>
                                            <div>
                                                <p class="text-sm font-bold text-slate-900 dark:text-slate-100">
                                                    {{ $wallet->user->name }}</p>
                                                <p class="text-xs text-slate-500">{{ $wallet->user->email }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <p class="text-sm font-black text-blue-600 dark:text-blue-500">
                                            {{ number_format($wallet->balance) }}đ
                                        </p>
                                    </td>

                                    <td class="px-6 py-4">
                                        @if ($wallet->status === 'active')
                                            <div class="flex items-center gap-1.5 text-green-600 dark:text-green-500">
                                                <span class="size-1.5 rounded-full bg-green-500"></span>
                                                <span class="text-xs font-bold">Hoạt động</span>
                                            </div>
                                        @else
                                            <div class="flex flex-col gap-1">
                                                <div class="flex items-center gap-1.5 text-red-600 dark:text-red-500">
                                                    <span class="size-1.5 rounded-full bg-red-500"></span>
                                                    <span class="text-xs font-bold">Đã khóa</span>
                                                </div>
                                                @if ($wallet->lock_reason)
                                                    <span class="text-[11px] text-slate-400 line-clamp-1"
                                                        title="{{ $wallet->lock_reason }}">
                                                        Lý do: {{ $wallet->lock_reason }}
                                                    </span>
                                                @endif
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm text-slate-500">
                                        {{ $wallet->updated_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex justify-end gap-2">
                                            {{-- Nút xem lịch sử giao dịch của ví này --}}
                                            <a href="{{ route('admin.wallet.transactions', $wallet->id) }}">
                                                <button class="p-2 text-slate-400 hover:text-blue-500 transition-colors"
                                                    title="Lịch sử giao dịch">
                                                    <span class="material-symbols-outlined text-lg">history</span>
                                                </button>
                                            </a>

                                            {{-- Nút Khóa / Mở khóa ví --}}
                                            @if ($wallet->status === 'active')
                                                <form action="{{ route('admin.wallet.lock', $wallet->id) }}"
                                                    method="POST">
                                                    @csrf
                                                    {{-- @method('PATCH') --}} <button
                                                        onclick="return confirm('Bạn có chắc chắn muốn KHÓA ví của {{ $wallet->user->name }}?')"
                                                        class="p-2 text-slate-400 hover:text-red-500 transition-colors"
                                                        title="Khóa ví">
                                                        <span class="material-symbols-outlined text-lg">lock</span>
                                                    </button>
                                                </form>
                                            @else
                                                <form action="{{ route('admin.wallet.unlock', $wallet->id) }}"
                                                    method="POST">
                                                    @csrf
                                                    {{-- @method('PATCH') --}} <button
                                                        onclick="return confirm('Mở khóa ví cho {{ $wallet->user->name }}?')"
                                                        class="p-2 text-slate-400 hover:text-green-500 transition-colors"
                                                        title="Mở khóa ví">
                                                        <span class="material-symbols-outlined text-lg">lock_open</span>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-slate-500">
                                        <div class="flex flex-col items-center justify-center">
                                            <span
                                                class="material-symbols-outlined text-4xl mb-2 text-slate-300">account_balance_wallet</span>
                                            <p>Không tìm thấy ví nào phù hợp.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse

                        </tbody>
                    </table>
                </div>
                <div class="p-5">{{ $wallets->links() }}</div>


            </div>
        </div>

    </main>
@endsection
