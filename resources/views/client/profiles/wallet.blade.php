@extends('client.profiles.layouts.app')

@section('profile_content')
    @include('popup_notify.index')
    <main class="flex-1 space-y-6" data-purpose="wallet-main-content">
        @if ($user->wallet)
            <section>
                <h1 class="text-2xl font-bold text-gray-900">Ví Bee Pay của tôi</h1>
                <p class="text-gray-500 text-sm mt-1">Quản lý số dư và thực hiện thanh toán nội bộ nhanh chóng</p>
            </section>
            <section>
                <div class="bg-[#1a1a1a] text-white p-8 rounded-2xl relative overflow-hidden shadow-xl"
                    data-purpose="balance-display">
                    <div class="absolute -top-12 -right-12 w-64 h-64 bg-[#f4c025] opacity-10 rounded-full"></div>
                    <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-8">
                        <div>
                            <p class="text-gray-400 text-sm font-medium mb-1">Số dư hiện tại trong hệ thống</p>
                            <h2 class="text-5xl font-bold tracking-tight">
                                {{ number_format($user->wallet->balance, 0, ',', '.') ?? '' }}<span
                                    class="text-2xl ml-1 text-[#f4c025]">đ</span></h2>
                        </div>
                        <div class="flex flex-wrap gap-4">
                            @if ($user->wallet->status == 'active')
                                <button type="button" onclick="openDepositModal()"
                                class="bg-[#f4c025] text-black px-8 py-3 rounded-xl font-bold hover:bg-yellow-400 transition-all flex items-center gap-2 shadow-lg shadow-yellow-900/20">
                                <svg class="h-5 w-5" fill="currentColor" viewbox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path clip-rule="evenodd"
                                        d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                                        fill-rule="evenodd"></path>
                                </svg>
                                Nạp tiền
                            </button>
                            <button onclick="openWithdrawModal()"
                                class="bg-transparent border border-gray-600 text-white px-8 py-3 rounded-xl font-bold hover:bg-white/10 transition-colors">
                                Rút tiền
                            </button>
                            @else
                            <span class=" text-red-500">Bị khóa</span>
                            @endif

                        </div>

                    </div>
                </div>
            </section>
            <section class="mt-6">
                <div class="bg-[#1e1e1e] border border-gray-800 p-6 rounded-2xl shadow-xl">

                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                        <div class="flex-1">
                            <h3 class="text-white text-lg font-bold">Ngân hàng liên kết</h3>
                            <p class="text-gray-400 text-sm">Quản lý tài khoản nhận tiền hoàn hoặc rút tiền</p>
                        </div>

                        <div class="flex flex-wrap items-center gap-3 w-full md:w-auto">
                            <a href="{{ route('wallet.withdrawals', $user->id) }}"
                                class="flex-1 md:flex-none justify-center px-4 py-2.5 bg-transparent border border-slate-700 text-slate-300 rounded-xl text-sm font-bold flex items-center gap-2 hover:bg-slate-800 hover:text-white transition-all shadow-sm">
                                <span class="material-symbols-outlined text-[18px]">history</span>
                                Lịch sử rút tiền
                            </a>

                            <button type="button" onclick="openAddBankModal()"
                                class="flex-1 md:flex-none justify-center flex items-center gap-2 bg-[#f4c025] border border-[#f4c025] text-black px-5 py-2.5 rounded-xl font-bold hover:bg-[#d9ab21] transition-all text-sm shadow-lg shadow-yellow-900/10">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M12 4v16m8-8H4"></path>
                                </svg>
                                Thêm ngân hàng
                            </button>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                        @forelse($user->bankAccounts ?? [] as $bank)
                            <div
                                class="relative bg-[#1a1a1a] border border-gray-800 p-5 rounded-xl flex items-center justify-between hover:border-gray-700 transition-colors">
                                <div class="flex items-center gap-4">
                                    <div
                                        class="group w-24 h-16 bg-white border border-slate-200 rounded-xl flex items-center justify-center hover:border-blue-500 hover:shadow-[0_4px_12px_rgba(0,0,0,0.1)] hover:-translate-y-1 transition-all duration-300 cursor-pointer overflow-hidden">
                                        <img src="https://cdn.vietqr.io/img/{{ $bank->bank_code }}.png"
                                            class="h-8 w-16 object-contain group-hover:scale-110 transition-transform duration-300"
                                            alt="{{ $bank->bank_code }} logo">
                                    </div>

                                    <div>
                                        <div class="flex items-center gap-2">
                                            <span class="text-white font-bold">{{ $bank->bank_name ?? 'Bank' }}</span>
                                            @if ($bank->is_default)
                                                <span
                                                    class="bg-yellow-500/10 text-[#f4c025] text-xs px-2 py-0.5 rounded-md border border-yellow-500/30">Mặc
                                                    định</span>
                                            @endif
                                        </div>
                                        <p class="text-gray-400 text-sm tracking-wider mt-1">{{ $bank->account_number }}</p>
                                        <p class="text-gray-500 text-xs mt-1 uppercase">{{ $bank->account_name }}</p>
                                    </div>
                                </div>

                                <form action="{{ route('wallet.remove.bank-account', $bank->id) }}" method="POST"
                                    onsubmit="return confirm('Bạn có chắc chắn muốn gỡ bỏ tài khoản ngân hàng này không?');"
                                    class="inline-block">
                                    @csrf
                                    <button type="submit"
                                        class="p-2 text-gray-500 hover:text-red-500 hover:bg-red-500/10 rounded-xl transition-all"
                                        title="Xóa tài khoản này">

                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                            </path>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        @empty
                            <div
                                class="col-span-1 md:col-span-2 flex flex-col items-center justify-center py-8 bg-[#1a1a1a] rounded-xl border border-dashed border-gray-800">
                                <p class="text-gray-500 text-sm">Bạn chưa liên kết tài khoản ngân hàng nào.</p>
                            </div>
                        @endforelse

                    </div>
                </div>
            </section>
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
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Đến
                            ngày</label>
                        <input type="date" name="date_to" value="{{ request('date_to') }}"
                            onchange="this.form.submit()"
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
                                        @if ($tx->status === 'completed' || $tx->status === 'cancelled' )
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
                                    @else
                                        <td
                                            class="px-4 py-3 md:px-6 md:py-4 font-bold text-red-500 text-sm whitespace-nowrap">
                                            - {{ number_format($transaction->amount, 0, ',', '.') }}đ
                                        </td>
                                    @endif

                                    @php
                                        $status_color = match ($transaction->status_transaction) {
                                            'Đang chờ' => 'blue',
                                            'Thành công' => 'green',
                                            'Thất bại' => 'red',
                                            default => 'yellow',
                                        };
                                    @endphp
                                    <td class="px-4 py-3 md:px-6 md:py-4 whitespace-nowrap">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $status_color }}-100 text-{{ $status_color }}-800">
                                            {{ $transaction->status_transaction }}
                                        </span>
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
        @else
            <button type="button" onclick="openActivateModal()"
                class="bg-[#f4c025] text-black px-8 py-3 rounded-xl font-bold hover:bg-yellow-400 transition-all flex items-center gap-2 shadow-lg shadow-yellow-900/20">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z">
                    </path>
                </svg>
                Kích hoạt ví ngay
            </button>
        @endif
    </main>


    {{-- Popup cho nạp tiền --}}
    <div id="depositModal"
        class="fixed inset-0 z-50 hidden bg-black/50 backdrop-blur-sm flex items-center justify-center transition-opacity">

        <div
            class="bg-white dark:bg-gray-800 w-full max-w-md rounded-2xl shadow-xl overflow-hidden transform transition-all">

            <div
                class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center bg-gray-50 dark:bg-gray-900">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Nạp tiền vào ví</h3>
                <button type="button" onclick="closeDepositModal()"
                    class="text-gray-400 hover:text-red-500 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>

            {{-- Popup cho nạp tiền  --}}
            <form action="{{ route('wallet.deposit') }}" method="POST">
                @csrf
                <div class="px-6 py-6 space-y-5">

                    <div>
                        <label for="amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Số
                            tiền muốn nạp (VNĐ)</label>
                        <div class="relative">
                            <input type="number" name="amount" id="amount" min="10000" step="1000" required
                                class="w-full pl-4 pr-12 py-3 rounded-xl border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-[#f4c025] focus:border-[#f4c025] outline-none transition-colors text-lg font-semibold"
                                placeholder="VD: 50000">
                            <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                                <span class="text-gray-500 font-medium">VNĐ</span>
                            </div>
                        </div>
                        <p class="text-xs text-red-500 mt-2 italic">* Số tiền nạp tối thiểu là 10.000đ</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Phương thức thanh
                            toán</label>
                        <div
                            class="flex items-center gap-3 p-3 border-2 border-[#f4c025] bg-yellow-50 dark:bg-gray-700/50 rounded-xl cursor-default">
                            <div class="bg-white p-1 rounded border border-gray-200">
                                <span class="font-extrabold text-blue-600">VN</span><span
                                    class="font-extrabold text-red-600">PAY</span>
                            </div>
                            <span class="font-medium text-gray-800 dark:text-white">Thanh toán qua VNPay</span>
                            <svg class="w-5 h-5 ml-auto text-[#f4c025]" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd"></path>
                            </svg>
                        </div>
                    </div>

                </div>

                <div
                    class="px-6 py-4 bg-gray-50 dark:bg-gray-900/50 flex justify-end gap-3 border-t border-gray-200 dark:border-gray-700">
                    <button type="button" onclick="closeDepositModal()"
                        class="px-5 py-2.5 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-white font-medium rounded-xl transition-colors">
                        Hủy bỏ
                    </button>
                    <button type="submit"
                        class="px-6 py-2.5 bg-[#f4c025] hover:bg-yellow-400 text-black font-bold rounded-xl transition-colors shadow-lg shadow-yellow-900/20 flex items-center gap-2">
                        Xác nhận nạp
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                        </svg>
                    </button>
                </div>
            </form>
        </div>
    </div>
    {{-- Popup Rút tiền --}}
    <div id="withdrawModal"
        class="fixed inset-0 z-[100] flex items-center justify-center bg-black/60 backdrop-blur-sm hidden opacity-0 transition-opacity duration-300">

        <div
            class="bg-white dark:bg-[#221e10] border border-gray-200 dark:border-white/10 w-full max-w-md rounded-2xl shadow-2xl transform scale-95 transition-transform duration-300 p-6 relative mx-4">

            <button onclick="closeWithdrawModal()"
                class="absolute top-4 right-4 text-gray-400 hover:text-red-500 transition-colors">
                <span class="material-symbols-outlined">close</span>
            </button>

            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">account_balance</span>
                Tạo lệnh rút tiền
            </h3>

            <form action="{{ route('wallet.withdrawal') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Số tiền muốn rút (VNĐ)
                        *</label>
                    <div class="relative">
                        <input type="number" name="amount" required min="50000"
                            class="w-full h-11 pl-4 pr-12 rounded-lg border border-gray-300 dark:border-white/10 bg-gray-50 dark:bg-white/5 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all"
                            placeholder="VD: 500000">
                        <span
                            class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm font-medium">VNĐ</span>
                    </div>
                    <p class="text-xs text-gray-500 mt-1 mt-1">Tối thiểu: 50.000 VNĐ</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Ngân hàng thụ hưởng
                        *</label>
                    <input type="text" name="bank_name" required
                        class="w-full h-11 px-4 rounded-lg border border-gray-300 dark:border-white/10 bg-gray-50 dark:bg-white/5 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all"
                        placeholder="VD: Vietcombank, MB Bank...">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Số tài khoản *</label>
                    <input type="text" name="account_number" required
                        class="w-full h-11 px-4 rounded-lg border border-gray-300 dark:border-white/10 bg-gray-50 dark:bg-white/5 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all"
                        placeholder="Nhập số tài khoản...">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tên chủ tài khoản
                        *</label>
                    <input type="text" name="account_name" required
                        class="w-full h-11 px-4 rounded-lg border border-gray-300 dark:border-white/10 bg-gray-50 dark:bg-white/5 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all uppercase"
                        placeholder="NGUYEN VAN A">
                </div>

                <div class="flex gap-3 pt-4 border-t border-gray-100 dark:border-white/10">
                    <button type="button" onclick="closeWithdrawModal()"
                        class="flex-1 py-2.5 rounded-lg border border-gray-300 dark:border-white/10 text-gray-700 dark:text-gray-300 font-medium hover:bg-gray-50 dark:hover:bg-white/5 transition-colors">
                        Hủy bỏ
                    </button>
                    <button type="submit"
                        class="flex-1 py-2.5 rounded-lg bg-primary text-black font-bold hover:bg-primary/90 transition-colors shadow-lg shadow-primary/30">
                        Xác nhận rút
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
@push('js')
    <script>
        function openTransactionModal(buttonElement) {
            // 1. Lấy dữ liệu từ cái nút vừa bấm
            const before = buttonElement.getAttribute('data-before');
            const after = buttonElement.getAttribute('data-after');
            const desc = buttonElement.getAttribute('data-desc');

            // 2. Gắn dữ liệu vào Modal
            document.getElementById('modal-before').innerText = before;
            document.getElementById('modal-after').innerText = after;
            document.getElementById('modal-desc').innerText = desc;

            // 3. Hiển thị Modal
            document.getElementById('transactionModal').classList.remove('hidden');
        }

        function closeTransactionModal() {
            // Ẩn Modal đi
            document.getElementById('transactionModal').classList.add('hidden');
        }
    </script>
    <script>
        function openDepositModal() {
            // Hiện popup
            document.getElementById('depositModal').classList.remove('hidden');
            // Tự động focus vào ô nhập tiền cho tiện
            setTimeout(() => document.getElementById('amount').focus(), 100);
        }

        function closeDepositModal() {
            // Ẩn popup
            document.getElementById('depositModal').classList.add('hidden');
            // Xóa giá trị đã nhập đi
            document.getElementById('amount').value = '';
        }
    </script>
    <script>
        const modal = document.getElementById('withdrawModal');
        const modalContent = modal.querySelector('div'); // Lấy cái thẻ div chứa nội dung bên trong

        function openWithdrawModal() {
            // Xóa class hidden để modal hiện ra
            modal.classList.remove('hidden');

            // Thêm một chút delay nhỏ để hiệu ứng fade-in và scale hoạt động mượt
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                modalContent.classList.remove('scale-95');
                modalContent.classList.add('scale-100');
            }, 10);
        }

        function closeWithdrawModal() {
            // Bắt đầu hiệu ứng fade-out và scale nhỏ lại
            modal.classList.add('opacity-0');
            modalContent.classList.remove('scale-100');
            modalContent.classList.add('scale-95');

            // Đợi hiệu ứng chạy xong (300ms) thì mới ẩn hẳn modal đi
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }

        // (Tùy chọn) Bấm ra ngoài khoảng đen để đóng modal
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeWithdrawModal();
            }
        });
    </script>
@endpush
