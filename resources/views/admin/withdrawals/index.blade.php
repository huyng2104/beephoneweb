@extends('admin.layouts.app')

@section('title', 'Yêu cầu rút tiền')

@section('content')
    <main class="flex-1 flex flex-col overflow-hidden">

        @include('popup_notify.index')

        <div class="flex-1 overflow-y-auto p-8 space-y-6">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-black text-slate-900 dark:text-slate-100 tracking-tight">Yêu cầu rút tiền</h2>
                    <p class="text-slate-500 text-sm mt-1">Quản lý và xét duyệt các giao dịch rút tiền từ người dùng</p>
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
                    <p class="text-slate-500 text-xs font-bold uppercase tracking-wider">Tổng tiền chờ duyệt</p>
                    <p class="text-2xl font-black mt-1 text-orange-500">
                        {{ number_format($totalPendingAmount ?? 0) }}đ
                    </p>
                </div>

                <div class="bg-white dark:bg-slate-800 p-4 rounded-xl border border-slate-200 dark:border-slate-700">
                    <p class="text-slate-500 text-xs font-bold uppercase tracking-wider">Đơn chờ xử lý</p>
                    <p class="text-2xl font-black mt-1 text-slate-900 dark:text-slate-100">
                        {{ number_format($pendingCount ?? 0) }}
                    </p>
                </div>

                <div class="bg-white dark:bg-slate-800 p-4 rounded-xl border border-slate-200 dark:border-slate-700">
                    <p class="text-slate-500 text-xs font-bold uppercase tracking-wider">Đã duyệt (Tháng này)</p>
                    <p class="text-2xl font-black mt-1 text-green-500">
                        {{ number_format($totalCompletedAmount ?? 0) }}đ
                    </p>
                </div>

                <div class="bg-white dark:bg-slate-800 p-4 rounded-xl border border-slate-200 dark:border-slate-700">
                    <p class="text-slate-500 text-xs font-bold uppercase tracking-wider">Đơn bị từ chối</p>
                    <p class="text-2xl font-black mt-1 text-red-500">
                        {{ number_format($rejectedCount ?? 0) }}
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
                                placeholder="Tìm mã giao dịch, email người dùng..." type="text" />
                        </div>

                        <div class="flex gap-2">
                            {{-- Bộ lọc Trạng thái --}}
                            <select name="status" onchange="this.form.submit()"
                                class="bg-slate-50 dark:bg-slate-900 border-slate-200 dark:border-slate-700 rounded-lg text-sm font-medium focus:ring-primary focus:border-primary dark:text-slate-200">
                                <option value="" {{ request('status') == '' ? 'selected' : '' }}>Tất cả trạng thái
                                </option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ duyệt
                                </option>
                                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Đã duyệt
                                </option>
                                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Đã từ chối
                                </option>
                                <option value="canceled" {{ request('status') == 'canceled' ? 'selected' : '' }}>Đã hủy
                                </option>
                            </select>

                            {{-- Bộ lọc Sắp xếp --}}
                            <select name="sort" onchange="this.form.submit()"
                                class="bg-slate-50 dark:bg-slate-900 border-slate-200 dark:border-slate-700 rounded-lg text-sm font-medium focus:ring-primary focus:border-primary dark:text-slate-200">
                                <option value="" {{ request('sort') == '' ? 'selected' : '' }}>-- Sắp xếp --</option>
                                <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Mới nhất
                                </option>
                                <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Cũ nhất</option>
                                <option value="amount_desc" {{ request('sort') == 'amount_desc' ? 'selected' : '' }}>Số
                                    tiền: Cao -> Thấp</option>
                            </select>

                            {{-- Nút Xóa bộ lọc (Chỉ hiện khi có chọn lọc) --}}
                            @if (request()->filled('search') || request()->filled('status') || request()->filled('sort'))
                                <a href="{{ route('admin.withdrawals.index') }}"
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
                                <th class="px-6 py-4">Số tiền rút</th>
                                <th class="px-6 py-4">Ngân hàng nhận</th>
                                <th class="px-6 py-4">Trạng thái</th>
                                <th class="px-6 py-4">Thời gian tạo</th>
                                <th class="px-6 py-4 text-right">Hành động</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700">

                            @forelse ($withdrawals as $index => $withdrawal)
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                                    <td class="px-6 py-4 text-sm font-medium text-slate-400">
                                        {{ $index + 1 }}
                                    </td>

                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="size-10 rounded-full bg-slate-200 overflow-hidden flex-shrink-0">
                                                <img class="w-full h-full object-cover"
                                                    src="{{ $withdrawal->user->avatar ? Storage::url($withdrawal->user->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($withdrawal->user->name) . '&background=cbd5e1&color=1e293b' }}"
                                                    alt="Avatar" />
                                            </div>
                                            <div>
                                                <p class="text-sm font-bold text-slate-900 dark:text-slate-100">
                                                    {{ $withdrawal->user->name }}
                                                </p>
                                                <p class="text-xs text-slate-500">{{ $withdrawal->user->email }}</p>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-6 py-4">
                                        <p class="text-sm font-black text-slate-900 dark:text-slate-100">
                                            {{ number_format($withdrawal->amount) }}đ
                                        </p>
                                    </td>

                                    <td class="px-6 py-4">
                                        <p class="text-sm font-medium text-slate-700 dark:text-slate-300">
                                            {{ $withdrawal->bank_name }}</p>
                                        <p class="text-xs text-slate-500">{{ $withdrawal->account_number }}</p>
                                    </td>

                                    <td class="px-6 py-4">
                                        @if ($withdrawal->status === 'pending')
                                            <div
                                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-orange-50 dark:bg-orange-500/10 text-orange-600 dark:text-orange-500 border border-orange-200 dark:border-orange-500/20">
                                                <span class="size-1.5 rounded-full bg-orange-500 animate-pulse"></span>
                                                <span class="text-xs font-bold">Chờ duyệt</span>
                                            </div>
                                        @elseif ($withdrawal->status === 'approved')
                                            <div
                                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-green-50 dark:bg-green-500/10 text-green-600 dark:text-green-500 border border-green-200 dark:border-green-500/20">
                                                <span class="size-1.5 rounded-full bg-green-500"></span>
                                                <span class="text-xs font-bold">Đã duyệt</span>
                                            </div>
                                        @elseif ($withdrawal->status === 'rejected')
                                            <div
                                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-red-50 dark:bg-red-500/10 text-red-600 dark:text-red-500 border border-red-200 dark:border-red-500/20">
                                                <span class="size-1.5 rounded-full bg-red-500"></span>
                                                <span class="text-xs font-bold">Từ chối</span>
                                            </div>
                                        @elseif ($withdrawal->status === 'canceled')
                                            <div
                                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-slate-100 dark:bg-slate-500/10 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-500/20">
                                                <span class="size-1.5 rounded-full bg-slate-500"></span>
                                                <span class="text-xs font-bold">Đã hủy</span>
                                            </div>
                                        @endif
                                    </td>

                                    <td class="px-6 py-4 text-sm text-slate-500">
                                        {{ $withdrawal->created_at->format('d/m/Y H:i') }}
                                    </td>

                                    <td class="px-6 py-4 text-right">
                                        <div class="flex justify-end gap-2">
                                            <a href="{{ route('admin.withdrawals.show', $withdrawal->id) }}">
                                                <button
                                                    class="p-2 text-slate-400 hover:text-blue-500 transition-colors bg-slate-50 hover:bg-blue-50 dark:bg-slate-800 dark:hover:bg-slate-700 rounded-lg"
                                                    title="Xem chi tiết">
                                                    <span class="material-symbols-outlined text-lg">visibility</span>
                                                </button>
                                            </a>
                                            {{-- Các nút Duyệt/Từ chối nhanh giữ nguyên trạng thái comment của bạn --}}
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-slate-500">
                                        <div class="flex flex-col items-center justify-center">
                                            <span
                                                class="material-symbols-outlined text-5xl mb-3 text-slate-300">receipt_long</span>
                                            <p class="font-medium text-slate-400">Không tìm thấy yêu cầu rút tiền nào.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse

                        </tbody>
                    </table>
                </div>

                <div class="p-5 border-t border-slate-100 dark:border-slate-700">
                    {{ $withdrawals->links() }}
                </div>

            </div>
        </div>
    </main>
@endsection
