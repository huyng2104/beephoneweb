@extends('admin.layouts.app')

@section('title', 'Lịch sử giao dịch ví')

@section('content')
    <main class="flex-1 flex flex-col overflow-hidden">

        @include('popup_notify.index')

        <div class="flex-1 overflow-y-auto p-8 space-y-6">

            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <a href="{{ route('admin.wallet.index') }}"
                        class="size-10 flex items-center justify-center rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-500 hover:text-primary transition-colors hover:shadow-sm">
                        <span class="material-symbols-outlined">arrow_back</span>
                    </a>
                    <div>
                        <h2 class="text-2xl font-black text-slate-900 dark:text-slate-100 tracking-tight">Chi tiết giao dịch
                        </h2>
                        <p class="text-slate-500 text-sm mt-1">Xem biến động số dư của người dùng</p>
                    </div>
                </div>

                <button
                    class="bg-primary hover:bg-primary/90 text-slate-900 font-bold px-5 py-2.5 rounded-xl shadow-sm flex items-center gap-2 transition-all text-sm">
                    <span class="material-symbols-outlined">download</span>
                    Xuất file Excel
                </button>
            </div>
            <div
                class="bg-[#1A1A1A] rounded-xl border border-slate-800 shadow-xl p-6 flex flex-wrap lg:flex-nowrap items-center justify-between gap-6">
                <div class="flex items-center gap-4">
                    <div
                        class="size-16 rounded-full bg-slate-800 overflow-hidden border-2 border-slate-700 shadow-sm flex-shrink-0">
                        <img class="w-full h-full object-cover"
                            src="{{ $wallet->user->avatar ? Storage::url($wallet->user->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($wallet->user->name) . '&background=cbd5e1&color=1e293b' }}"
                            alt="Avatar" />
                    </div>

                    <div>
                        <h3 class="text-lg font-bold text-white">{{ $wallet->user->name }}</h3>
                        <p class="text-sm text-slate-400">{{ $wallet->user->email }}</p>

                        <div class="mt-2 flex items-center gap-2">
                            @if ($wallet->status === 'active')
                                <span
                                    class="px-2 py-0.5 rounded text-[10px] font-bold bg-green-500/20 text-green-400 border border-green-500/30 uppercase tracking-wider">
                                    Đang hoạt động
                                </span>
                            @else
                                <span
                                    class="px-2 py-0.5 rounded text-[10px] font-bold bg-red-500/20 text-red-400 border border-red-500/30 uppercase tracking-wider">
                                    Đã khóa
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <div
                    class="text-right border-t lg:border-t-0 lg:border-l border-slate-800 pt-4 lg:pt-0 lg:pl-8 min-w-[250px]">
                    <p class="text-gray-400 text-sm font-medium mb-1">Số dư hiện tại trong hệ thống</p>
                    <h2 class="text-5xl font-bold tracking-tight text-white">
                        {{ number_format($wallet->balance, 0, ',', '.') }}<span
                            class="text-2xl ml-1 text-[#f4c025]">đ</span>
                    </h2>
                </div>
            </div>
            <div class="bg-[#1A1A1A] rounded-xl border border-slate-800 shadow-xl p-6">

                <div class="flex flex-wrap items-center justify-between gap-4 mb-6 border-b border-slate-800 pb-4">
                    <div>
                        <h3 class="text-xl font-bold text-white mb-1">Ngân hàng liên kết</h3>
                        <p class="text-slate-400 text-sm">Quản lý tài khoản nhận tiền hoàn hoặc rút tiền</p>
                    </div>

                    <div class="flex items-center gap-3">
                        <a href="{{route('admin.withdrawals.history',$wallet->user->id)}}"
                            class="px-4 py-2.5 bg-transparent border border-slate-600 text-slate-300 rounded-lg text-sm font-bold flex items-center gap-2 hover:bg-slate-800 hover:text-white transition-colors">
                            <span class="material-symbols-outlined text-base">history</span>
                            Lịch sử rút tiền
                        </a>
                    </div>
                </div>

                <div class="bg-[#1A1A1A] rounded-xl border border-slate-800 shadow-xl p-6">



                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                        @forelse ($banks as $bank)
                            <div
                                class="border border-slate-700/80 bg-slate-800/20 rounded-xl p-4 flex items-center justify-between group hover:border-slate-500 transition-colors">
                                <div class="flex items-center gap-4 w-full">

                                    <div
                                        class="w-20 h-14 bg-white rounded-lg p-2 flex items-center justify-center flex-shrink-0 shadow-sm">
                                        <img src="https://cdn.vietqr.io/img/{{ $bank->bank_code }}.png"
                                            alt="{{ $bank->bank_code }}" class="max-w-full max-h-full object-contain"
                                            onerror="this.src='https://ui-avatars.com/api/?name={{ $bank->bank_code }}&background=f1f5f9&color=64748b&font-size=0.4'">
                                    </div>

                                    <div class="flex-1">
                                        <div class="flex flex-wrap items-center gap-2 mb-1">
                                            <h4 class="text-white font-bold text-sm lg:text-base leading-tight">
                                                {{ $bank->bank_name }}
                                            </h4>

                                            @if ($bank->is_default)
                                                <span
                                                    class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-500/20 text-blue-400 border border-blue-500/30 uppercase tracking-wider whitespace-nowrap">
                                                    Mặc định
                                                </span>
                                            @endif
                                        </div>

                                        <p class="text-slate-400 text-sm font-medium">{{ $bank->account_number }}</p>
                                        <p class="text-slate-500 text-xs uppercase mt-0.5 tracking-wider">
                                            {{ $bank->account_name }}
                                        </p>
                                    </div>

                                </div>
                            </div>
                        @empty
                            <div
                                class="col-span-1 md:col-span-2 text-center py-8 border border-dashed border-slate-700 rounded-xl bg-slate-800/10">
                                <span
                                    class="material-symbols-outlined text-slate-500 text-4xl mb-2">account_balance_wallet</span>
                                <p class="text-slate-400 font-medium">Người dùng chưa liên kết tài khoản ngân hàng nào.</p>
                            </div>
                        @endforelse

                    </div>
                </div>
            </div>
            <form method="GET" action="{{ url()->current() }}"
                class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-5 shadow-sm">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 items-end">

                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Loại giao
                            dịch</label>
                        <select name="type" onchange="this.form.submit()"
                            class="w-full rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-300 focus:ring-primary focus:border-primary text-sm py-2.5">
                            <option value="">Tất cả loại</option>
                            <option value="deposit" {{ request('type') == 'deposit' ? 'selected' : '' }}>Nạp tiền</option>
                            <option value="withdraw" {{ request('type') == 'withdraw' ? 'selected' : '' }}>Rút tiền
                            </option>
                            <option value="payment" {{ request('type') == 'payment' ? 'selected' : '' }}>Thanh toán
                            </option>
                            <option value="refund" {{ request('type') == 'refund' ? 'selected' : '' }}>Hoàn tiền</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Trạng
                            thái</label>
                        <select name="status" onchange="this.form.submit()"
                            class="w-full rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-300 focus:ring-primary focus:border-primary text-sm py-2.5">
                            <option value="">Tất cả trạng thái</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Thành công
                            </option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Đang chờ
                            </option>
                            <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Thất bại</option>
                            {{-- <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Đã hủy
                            </option> --}}
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Từ ngày</label>
                        <input type="date" name="date_from" value="{{ request('date_from') }}"
                            onchange="this.form.submit()"
                            class="w-full rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-300 focus:ring-primary focus:border-primary text-sm py-2.5">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Đến ngày</label>
                        <input type="date" name="date_to" value="{{ request('date_to') }}" onchange="this.form.submit()"
                            class="w-full rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-300 focus:ring-primary focus:border-primary text-sm py-2.5">
                    </div>

                    <div class="flex items-center gap-2">
                        @if (request()->anyFilled(['type', 'status', 'date_from', 'date_to']))
                            <a href="{{ url()->current() }}"
                                class="w-full bg-orange-100 hover:bg-orange-200 dark:bg-orange-700 dark:hover:bg-orange-600 text-orange-600 dark:text-orange-300 font-bold py-2.5 px-4 rounded-lg transition-colors text-sm flex items-center justify-center gap-2"
                                title="Xóa bộ lọc">
                                <span class="material-symbols-outlined text-sm">close</span>
                                Xóa lọc
                            </a>
                        @else
                            <div class="w-full py-2.5"></div>
                        @endif
                    </div>
                </div>
            </form>
            <div
                class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                <div
                    class="p-4 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center bg-slate-50/50 dark:bg-slate-900/20">
                    <h3 class="font-bold text-slate-800 dark:text-slate-200">Lịch sử biến động</h3>
                    <span class="text-xs text-slate-500">Tổng số: {{ $transactions->total() }} giao dịch</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead
                            class="bg-slate-50 dark:bg-slate-900/50 text-slate-500 text-xs font-bold uppercase tracking-wider">
                            <tr>
                                <th class="px-6 py-4">Giao dịch / Thời gian</th>
                                <th class="px-6 py-4 text-center">Loại GD</th>
                                <th class="px-6 py-4">Nội dung chi tiết</th>
                                <th class="px-6 py-4 text-right">Số tiền</th>
                                <th class="px-6 py-4 text-right">Biến động số dư</th>
                                <th class="px-6 py-4 text-center">Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                            @forelse ($transactions as $tx)
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">

                                    <td class="px-6 py-4">
                                        <p class="font-bold text-slate-700 dark:text-slate-300">#{{ $tx->id }}</p>
                                        <p class="text-xs text-slate-500 mt-1">
                                            {{ $tx->created_at->format('d/m/Y H:i:s') }}
                                        </p>
                                    </td>

                                    <td class="px-6 py-4 text-center">
                                        {!! $tx->type_transaction !!}
                                    </td>

                                    <td class="px-6 py-4">
                                        <p class="text-slate-700 dark:text-slate-300 font-medium line-clamp-2"
                                            title="{{ $tx->description }}">
                                            {{ $tx->description ?? 'Không có nội dung' }}
                                        </p>

                                        {{-- Hiển thị thông tin tham chiếu nếu có --}}
                                        @if ($tx->reference_type && $tx->reference_id)
                                            <p class="text-[11px] text-slate-400 mt-1">
                                                Nguồn: {{ class_basename($tx->reference_type) }} #{{ $tx->reference_id }}
                                            </p>
                                        @endif
                                    </td>

                                    <td class="px-6 py-4 text-right whitespace-nowrap">
                                        @if ($tx->status === 'completed')
                                            @if ($tx->type === 'deposit' || $tx->type === 'refund')
                                                <p class="text-base font-black text-green-600 dark:text-green-500">
                                                    +{{ number_format($tx->amount) }}đ
                                                </p>
                                            @else
                                                <p class="text-base font-black text-red-600 dark:text-red-500">
                                                    -{{ number_format($tx->amount) }}đ
                                                </p>
                                            @endif
                                        @elseif($tx->status === 'failed' || $tx->status === 'cancelled')
                                            <p class="text-base font-bold text-slate-400 dark:text-slate-500 line-through">
                                                {{ number_format($tx->amount) }}đ
                                            </p>
                                        @else
                                            <p class="text-base font-bold text-slate-500 dark:text-slate-400">
                                                {{ number_format($tx->amount) }}đ
                                            </p>
                                        @endif
                                    </td>

                                    <td class="px-6 py-4 text-right text-xs whitespace-nowrap">
                                        @if ($tx->status === 'completed')
                                            <div class="flex flex-col gap-1 text-slate-500">
                                                {{-- <p>Trước: <span
                                                        class="font-medium">{{ number_format($tx->balance_before) }}đ</span>
                                                </p> --}}
                                                <p>SD: <span
                                                        class="font-bold text-slate-800 dark:text-slate-200">{{ number_format($tx->balance_after) }}đ</span>
                                                </p>
                                            </div>
                                        @else
                                            <span class="text-slate-400 italic">Chưa biến động</span>
                                        @endif
                                    </td>

                                    <td class="px-6 py-4 text-center">
                                        @if ($tx->status === 'completed')
                                            <span
                                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-green-50 text-green-600 dark:bg-green-500/10 dark:text-green-400 border border-green-100 dark:border-green-500/20 whitespace-nowrap">
                                                <span class="size-1.5 rounded-full bg-green-500"></span>
                                                {{ $tx->status_transaction }}
                                            </span>
                                        @elseif($tx->status === 'pending')
                                            <span
                                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-orange-50 text-orange-600 dark:bg-orange-500/10 dark:text-orange-400 border border-orange-100 dark:border-orange-500/20 whitespace-nowrap">
                                                <span class="size-1.5 rounded-full bg-orange-500 animate-pulse"></span>
                                                {{ $tx->status_transaction }}
                                            </span>
                                        @elseif($tx->status === 'failed')
                                            <span
                                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-red-50 text-red-600 dark:bg-red-500/10 dark:text-red-400 border border-red-100 dark:border-red-500/20 whitespace-nowrap">
                                                <span class="size-1.5 rounded-full bg-red-500"></span>
                                                {{ $tx->status_transaction }}
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-600 whitespace-nowrap">
                                                <span class="size-1.5 rounded-full bg-slate-500"></span>
                                                {{ $tx->status_transaction }}
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                                        <div class="flex flex-col items-center justify-center">
                                            <span
                                                class="material-symbols-outlined text-4xl mb-3 text-slate-300">receipt_long</span>
                                            <p class="text-base font-medium text-slate-600 dark:text-slate-400">Ví này chưa
                                                có giao dịch nào</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($transactions->hasPages())
                    <div class="p-4 border-t border-slate-100 dark:border-slate-700">
                        {{ $transactions->links() }}
                    </div>
                @endif
            </div>

        </div>
    </main>
@endsection
