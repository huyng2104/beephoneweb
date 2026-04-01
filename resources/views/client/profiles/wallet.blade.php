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
                                <span class="text-red-500 font-bold">Bị khóa bỏi: {{ $user->wallet->lock_reason }}</span>
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
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Tìm kiếm</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg class="w-4 h-4 text-slate-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 20 20">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                                </svg>
                            </div>

                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Mã GD, nội dung... (Ấn Enter)"
                                onkeydown="if(event.key === 'Enter') this.form.submit();"
                                class="w-full pl-9 pr-3 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-300 focus:ring-primary focus:border-primary text-sm transition-colors outline-none">
                        </div>
                    </div>
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
                        @if (request()->anyFilled(['type', 'search', 'date_from', 'date_to']))
                            <a href="{{ url()->current() }}"
                                class="w-full bg-orange-100 hover:bg-orange-200 dark:bg-orange-700 dark:hover:bg-orange-600 text-orange-600 dark:text-orange-300 font-bold py-2.5 px-4 rounded-lg transition-colors text-sm flex items-center justify-center gap-2"
                                title="Xóa bộ lọc">
                                <span class="material-symbols-outlined text-sm">close</span>
                                Xóa lọc
                            </a>
                        @else
                            <button type="submit"
                                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 px-4 rounded-lg transition-colors text-sm flex items-center justify-center gap-2"
                                title="Lọc dữ liệu">
                                <span class="material-symbols-outlined text-sm">search</span>
                                Lọc
                            </button>
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
                                <th class="px-6 py-4 text-center">Loại giao dịch</th>
                                <th class="px-6 py-4">Nội dung chi tiết</th>
                                <th class="px-6 py-4 text-right">Số tiền</th>
                                <th class="px-6 py-4 text-right">Biến động số dư</th>
                                {{-- <th class="px-6 py-4 text-center">Trạng thái</th> --}}
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
                                        @if ($tx->status === 'completed' || $tx->status === 'cancelled')
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

                                    {{-- <td class="px-6 py-4 text-center">
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
                                    </td> --}}
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
        class="fixed inset-0 z-[100] flex items-center justify-center bg-black/80 backdrop-blur-sm hidden opacity-0 transition-opacity duration-300">

        <div
            class="bg-[#18181b] border border-white/10 w-full max-w-md rounded-2xl shadow-2xl transform scale-95 transition-transform duration-300 p-6 relative mx-4 max-h-[90vh] overflow-y-auto custom-scrollbar">

            <button onclick="closeWithdrawModal()"
                class="absolute top-4 right-4 text-gray-500 hover:text-red-500 transition-colors">
                <span class="material-symbols-outlined">close</span>
            </button>

            <h3 class="text-xl font-bold text-white mb-6 flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">account_balance</span>
                Tạo lệnh rút tiền
            </h3>

            <form action="{{ route('wallet.withdrawal') }}" method="POST" enctype="multipart/form-data"
                class="space-y-5">
                @csrf

                <div class="mb-5">
                    <label class="block text-sm font-medium text-gray-300 mb-1">Mã PIN giao dịch (6 số) *</label>
                    <div class="relative">
                        <input type="password" name="wallet_pin_confirm" required maxlength="6" inputmode="numeric"
                            pattern="[0-9]{6}"
                            class="w-full h-11 pl-4 pr-12 rounded-xl border border-white/10 bg-white/5 text-white focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all placeholder-gray-500 tracking-widest font-mono text-lg @error('wallet_pin_confirm') border-red-500 focus:ring-red-500 @enderror"
                            placeholder="••••••">

                        <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
                                    clip-rule="evenodd" />
                            </svg>
                        </span>
                    </div>
                    <div class="flex justify-between mt-1">
                        <p class="text-xs text-gray-500">Nhập mã PIN ví của bạn để xác nhận rút tiền</p>
                        @error('wallet_pin_confirm', 'withdrawal')
                            <p class="text-xs text-red-500 italic">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1">Số tiền muốn rút (VNĐ) *</label>
                    <div class="relative">
                        <input type="number" name="amount" required min="50000" value="{{ old('amount') }}"
                            class="w-full h-11 pl-4 pr-12 rounded-xl border border-white/10 bg-white/5 text-white focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all placeholder-gray-500 @error('amount') border-red-500 focus:ring-red-500 @enderror"
                            placeholder="VD: 500000">
                        <span
                            class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm font-medium">VNĐ</span>
                    </div>
                    <div class="flex justify-between mt-1">
                        <p class="text-xs text-gray-500">Tối thiểu: 50.000 VNĐ</p>
                        @error('amount', 'withdrawal')
                            <p class="text-xs text-red-500 italic">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <div class="flex justify-between items-end mb-2">
                        <label class="block text-sm font-medium text-gray-300">Chọn tài khoản nhận tiền *</label>
                        @error('bank_account_id', 'withdrawal')
                            <p class="text-xs text-red-500 italic">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-2 max-h-40 overflow-y-auto pr-1 custom-scrollbar">

                        @forelse($user->bankAccounts ?? [] as $bank)
                            <label
                                class="relative flex items-center p-3 border rounded-xl cursor-pointer transition-all border-white/10 hover:bg-white/5 has-[:checked]:border-primary has-[:checked]:bg-primary/10 group">
                                <input type="radio" name="bank_account_id" value="{{ $bank->id }}"
                                    class="peer sr-only" required onchange="toggleManualEntry(false)"
                                    {{ old('bank_account_id') == $bank->id ? 'checked' : '' }}>

                                <div class="flex-1">
                                    <p class="font-bold text-white uppercase text-sm">{{ $bank->bank_name }}</p>
                                    <p class="text-sm text-gray-400">{{ $bank->account_number }} - <span
                                            class="uppercase">{{ $bank->account_name }}</span></p>
                                </div>

                                <span
                                    class="material-symbols-outlined text-primary scale-0 peer-checked:scale-100 transition-transform duration-200">check_circle</span>
                            </label>
                        @empty
                            <div class="p-3 border border-dashed border-white/10 rounded-xl text-center">
                                <p class="text-sm text-gray-500">Bạn chưa có tài khoản nào được lưu.</p>
                            </div>
                        @endforelse

                        <label
                            class="relative flex items-center p-3 border rounded-xl cursor-pointer transition-all border-white/10 hover:bg-white/5 has-[:checked]:border-primary has-[:checked]:bg-primary/10 group">
                            <input type="radio" name="bank_account_id" value="manual" class="peer sr-only" required
                                onchange="toggleManualEntry(true)" id="radio-manual"
                                {{ old('bank_account_id') == 'manual' ? 'checked' : '' }}>

                            <div class="flex-1 flex items-center gap-2">
                                <span
                                    class="material-symbols-outlined text-gray-400 peer-checked:text-primary">add_card</span>
                                <p class="font-bold text-white text-sm">Nhập tài khoản mới</p>
                            </div>
                            <span
                                class="material-symbols-outlined text-primary scale-0 peer-checked:scale-100 transition-transform duration-200">check_circle</span>
                        </label>
                    </div>

                    <div id="manual-entry-fields"
                        class="hidden space-y-3 mt-3 p-4 border border-white/10 rounded-xl bg-white/5 relative">

                        <div>
                            <label class="block text-xs font-medium text-gray-400 mb-1">Ngân hàng thụ hưởng *</label>
                            <div class="relative">
                                <select name="manual_bank_name" id="manual_bank_name"
                                    data-old="{{ old('manual_bank_name') }}"
                                    class="w-full h-10 px-3 rounded-lg border border-white/10 bg-[#18181b] text-white focus:ring-1 focus:ring-primary focus:border-primary outline-none transition-all text-sm appearance-none @error('manual_bank_name') border-red-500 @enderror">
                                    <option value="">-- Đang tải danh sách ngân hàng... --</option>
                                </select>
                                <span
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none material-symbols-outlined text-base">expand_more</span>
                            </div>
                            @error('manual_bank_name', 'withdrawal')
                                <p class="text-xs text-red-500 mt-1 italic">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-400 mb-1">Số tài khoản *</label>
                            <input type="text" name="manual_account_number" id="manual_account_number"
                                value="{{ old('manual_account_number') }}"
                                class="w-full h-10 px-3 rounded-lg border border-white/10 bg-[#18181b] text-white focus:ring-1 focus:ring-primary focus:border-primary outline-none transition-all placeholder-gray-600 text-sm @error('manual_account_number') border-red-500 @enderror"
                                placeholder="Nhập số tài khoản...">
                            @error('manual_account_number', 'withdrawal')
                                <p class="text-xs text-red-500 mt-1 italic">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-400 mb-1">Tên chủ tài khoản *</label>
                            <input type="text" name="manual_account_name" id="manual_account_name"
                                value="{{ old('manual_account_name') }}"
                                class="w-full h-10 px-3 rounded-lg border border-white/10 bg-[#18181b] text-white focus:ring-1 focus:ring-primary focus:border-primary outline-none transition-all placeholder-gray-600 text-sm uppercase @error('manual_account_name') border-red-500 @enderror"
                                placeholder="NGUYEN VAN A">
                            @error('manual_account_name', 'withdrawal')
                                <p class="text-xs text-red-500 mt-1 italic">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="flex gap-3 pt-4 border-t border-white/10">
                    <button type="button" onclick="closeWithdrawModal()"
                        class="flex-1 py-2.5 rounded-xl border border-white/10 text-gray-300 font-medium hover:bg-white/10 transition-colors">
                        Hủy bỏ
                    </button>
                    <button type="submit"
                        class="flex-1 py-2.5 rounded-xl bg-primary text-black font-bold hover:bg-primary/90 transition-colors shadow-lg shadow-primary/30">
                        Xác nhận rút
                    </button>
                </div>
            </form>
        </div>
    </div>
    @if ($errors->hasBag('withdrawal'))
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                // Đợi HTML load xong thì tự động gọi hàm mở modal
                openWithdrawModal();
            });
        </script>
    @endif
    {{-- Popup kích hoạt ví  --}}
    <div id="activateWalletModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title"
        role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">

            <div class="fixed inset-0 transition-opacity bg-black/80 backdrop-blur-sm" aria-hidden="true"
                onclick="closeActivateModal()"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div
                class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-[#1e1e1e] rounded-2xl shadow-2xl sm:my-8 sm:align-middle sm:max-w-xl w-full border border-gray-800">

                <div class="px-6 py-4 border-b border-gray-800 flex justify-between items-center bg-[#1a1a1a]">
                    <h3 class="text-xl font-bold text-white" id="modal-title">
                        Kích hoạt Ví Điện Tử
                    </h3>
                    <button type="button" onclick="closeActivateModal()"
                        class="text-gray-400 hover:text-white transition-colors">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <form action="{{ route('wallet.active', $user->id) }}" method="POST">
                    @csrf
                    <div class="px-6 py-6 space-y-6">

                        <div>
                            <h4
                                class="text-[#f4c025] font-semibold mb-3 flex items-center gap-2 text-sm uppercase tracking-wider">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                                    </path>
                                </svg>
                                1. Thiết lập mã PIN (6 số)
                            </h4>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-400 mb-1.5">Mã PIN Ví</label>
                                    <input type="password" name="wallet_pin" maxlength="6" pattern="\d{6}" required
                                        placeholder="••••••"
                                        class="w-full bg-[#2a2a2a] border border-gray-700 text-white rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-[#f4c025]/50 outline-none text-center tracking-[0.5em] font-bold @error('wallet_pin') border-red-500 focus:border-red-500 focus:ring-red-500/20 @else focus:border-[#f4c025] @enderror">

                                    @error('wallet_pin')
                                        <p class="text-red-500 text-xs mt-1.5 flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-400 mb-1.5">Nhập lại mã PIN</label>
                                    <input type="password" name="wallet_pin_confirmation" maxlength="6" pattern="\d{6}"
                                        required placeholder="••••••"
                                        class="w-full bg-[#2a2a2a] border border-gray-700 text-white rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-[#f4c025]/50 outline-none text-center tracking-[0.5em] font-bold @error('wallet_pin_confirmation') border-red-500 focus:border-red-500 focus:ring-red-500/20 @else focus:border-[#f4c025] @enderror">

                                    @error('wallet_pin_confirmation')
                                        <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 mt-2">* Mã PIN dùng để xác thực khi bạn rút tiền hoặc thanh
                                toán.</p>
                        </div>

                        <hr class="border-gray-800">

                        <div>
                            <h4
                                class="text-[#f4c025] font-semibold mb-3 flex items-center gap-2 text-sm uppercase tracking-wider">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 10h18M7 10V4m10 6V4M2 21h20M5 10v11m14-11v11m-8-11v11M4 21h16"></path>
                                </svg>
                                2. Tài khoản nhận tiền
                            </h4>
                            <div class="space-y-4">

                                <div>
                                    <label class="block text-sm font-medium text-gray-400 mb-1.5">Ngân hàng thụ
                                        hưởng</label>
                                    <select name="bank_code" id="bank_code_select"
                                        class="w-full bg-[#2a2a2a] border border-gray-700 text-white rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-[#f4c025]/50 outline-none appearance-none @error('bank_code') border-red-500 focus:border-red-500 @else focus:border-[#f4c025] @enderror">
                                        <option value="" disabled selected>-- Đang tải danh sách ngân hàng... --
                                        </option>
                                    </select>
                                    <input type="hidden" name="bank_name" id="bank_name_input"
                                        value="{{ old('bank_name') }}">

                                    @error('bank_code')
                                        <p class="text-red-500 text-xs mt-1.5 flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-400 mb-1.5">Số tài khoản</label>
                                        <input type="text" name="account_number" value="{{ old('account_number') }}"
                                            placeholder="Nhập số tài khoản"
                                            class="w-full bg-[#2a2a2a] border border-gray-700 text-white rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-[#f4c025]/50 outline-none font-mono @error('account_number') border-red-500 focus:border-red-500 @else focus:border-[#f4c025] @enderror">

                                        @error('account_number')
                                            <p class="text-red-500 text-xs mt-1.5 flex items-center gap-1">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                {{ $message }}
                                            </p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-400 mb-1.5">Tên chủ tài
                                            khoản</label>
                                        <input type="text" name="account_name" value="{{ old('account_name') }}"
                                            placeholder="NGUYEN VAN A"
                                            class="w-full bg-[#2a2a2a] border border-gray-700 text-white rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-[#f4c025]/50 outline-none uppercase @error('account_name') border-red-500 focus:border-red-500 @else focus:border-[#f4c025] @enderror">

                                        @error('account_name')
                                            <p class="text-red-500 text-xs mt-1.5 flex items-center gap-1">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                {{ $message }}
                                            </p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="px-6 py-4 bg-[#1a1a1a] border-t border-gray-800 flex justify-end gap-3 rounded-b-2xl">
                        <button type="button" onclick="closeActivateModal()"
                            class="px-5 py-2.5 text-sm font-bold text-gray-300 hover:text-white bg-transparent hover:bg-white/5 rounded-xl transition-all">
                            Để sau
                        </button>
                        <button type="submit"
                            class="px-6 py-2.5 text-sm font-bold text-black bg-[#f4c025] hover:bg-yellow-400 rounded-xl shadow-lg shadow-yellow-900/20 transition-all">
                            Hoàn tất kích hoạt
                        </button>
                    </div>
                </form>
                @error('wallet_pin')
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            // Chỉ cần gọi hàm mở modal, lỗi đã được vẽ sẵn bằng HTML bên trong!
                            openActivateModal();
                        });
                    </script>
                @enderror

            </div>
        </div>
    </div>

    {{-- Popup thêm ngân hàng --}}

    <div id="addBankModal"
        class="fixed inset-0 z-50 flex items-center justify-center hidden opacity-0 transition-opacity duration-300">
        <div class="absolute inset-0 bg-black/80 backdrop-blur-sm" onclick="closeAddBankModal()"></div>

        <div id="addBankModalContent"
            class="relative w-full max-w-md bg-[#1a1a1a] rounded-2xl shadow-2xl shadow-black/50 border border-gray-800 flex flex-col transform scale-95 transition-transform duration-300">

            <div class="px-6 py-4 border-b border-gray-800 flex justify-between items-center">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <svg class="h-5 w-5 text-[#f4c025]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 10h18M7 10V4m10 6V4M2 21h20M5 10v11m14-11v11m-8-11v11M4 21h16"></path>
                    </svg>
                    Thêm ngân hàng mới
                </h3>
                <button type="button" onclick="closeAddBankModal()"
                    class="text-gray-400 hover:text-[#f4c025] transition-colors p-1 rounded-lg hover:bg-white/5">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>

            <form action="{{ route('wallet.bank-account', $user->id) }}" method="POST">
                @csrf
                <input type="hidden" name="form_type" value="add_bank">

                <div class="p-6 space-y-5">

                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-1.5">Ngân hàng thụ hưởng <span
                                class="text-red-500">*</span></label>
                        <select name="bank_code" id="modal_bank_select" required data-old="{{ old('bank_code') }}"
                            class="w-full bg-[#2a2a2a] border border-gray-700 text-white rounded-xl px-4 py-2.5 outline-none appearance-none cursor-pointer transition-all focus:ring-2 @error('bank_code') border-red-500 focus:border-red-500 focus:ring-red-500/20 @else focus:ring-[#f4c025]/50 focus:border-[#f4c025] @enderror">
                            <option value="" disabled selected>-- Đang tải danh sách... --</option>
                        </select>

                        @error('bank_code', 'addBank')
                            <p class="text-red-500 text-xs mt-1.5 flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror

                        <input type="hidden" name="bank_name" id="modal_bank_name" value="{{ old('bank_name') }}">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-1.5">Số tài khoản <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="account_number" id="modal_acc_number" required
                            value="{{ old('account_number') }}" placeholder="Nhập số tài khoản"
                            class="w-full bg-[#2a2a2a] border border-gray-700 text-white rounded-xl px-4 py-2.5 outline-none font-mono transition-all focus:ring-2 @error('account_number') border-red-500 focus:border-red-500 focus:ring-red-500/20 @else focus:ring-[#f4c025]/50 focus:border-[#f4c025] @enderror">

                        @error('account_number', 'addBank')
                            <p class="text-red-500 text-xs mt-1.5 flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-1.5 flex justify-between">
                            <span>Tên chủ tài khoản <span class="text-red-500">*</span></span>
                            <span id="modal_lookup_status" class="text-xs text-[#f4c025] hidden">Đang tra cứu...</span>
                        </label>
                        <input type="text" name="account_name" id="modal_acc_name" required
                            value="{{ old('account_name') }}" placeholder="Ví dụ: NGUYEN VAN A"
                            class="w-full bg-[#2a2a2a] border border-gray-700 text-white rounded-xl px-4 py-2.5 outline-none uppercase font-bold transition-all focus:ring-2 @error('account_name') border-red-500 focus:border-red-500 focus:ring-red-500/20 @else focus:ring-[#f4c025]/50 focus:border-[#f4c025] @enderror">

                        @error('account_name', 'addBank')
                            <p class="text-red-500 text-xs mt-1.5 flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>

                <div class="px-6 py-4 bg-[#1a1a1a] border-t border-gray-800 flex justify-end gap-3 rounded-b-2xl">
                    <button type="button" onclick="closeAddBankModal()"
                        class="px-5 py-2.5 text-sm font-bold text-gray-300 hover:text-white bg-transparent hover:bg-white/5 rounded-xl transition-all">Hủy
                        bỏ</button>
                    <button type="submit"
                        class="px-6 py-2.5 text-sm font-bold text-black bg-[#f4c025] hover:bg-yellow-400 rounded-xl shadow-lg shadow-yellow-900/20 transition-all">Lưu
                        tài khoản</button>
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


    {{-- popup rút tiền --}}
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

    {{-- mở form kích hoạt ví --}}
    <script>
        function openActivateModal() {
            document.getElementById('activateWalletModal').classList.remove('hidden');
            // Vô hiệu hóa cuộn trang khi mở modal
            document.body.style.overflow = 'hidden';
        }

        function closeActivateModal() {
            document.getElementById('activateWalletModal').classList.add('hidden');
            // Bật lại cuộn trang
            document.body.style.overflow = 'auto';
        }

        // Tự động gán bank_name dựa vào option được chọn trong <select>
        document.querySelector('select[name="bank_code"]').addEventListener('change', function(e) {
            let selectedOption = e.target.options[e.target.selectedIndex];
            document.getElementById('bank_name_input').value = selectedOption.text;
        });
    </script>

    {{-- api hiển thị ngân hàng --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 1. Gọi API lấy danh sách ngân hàng từ VietQR
            fetch('https://api.vietqr.io/v2/banks')
                .then(response => response.json())
                .then(data => {
                    if (data.code === '00') {
                        const bankSelect = document.getElementById('bank_code_select');

                        // Reset lại option mặc định
                        bankSelect.innerHTML =
                            '<option value="" disabled selected>-- Chọn ngân hàng --</option>';

                        // Lặp qua mảng dữ liệu và tạo các thẻ <option>
                        data.data.forEach(bank => {
                            const option = document.createElement('option');
                            option.value = bank.code; // Lưu mã ngắn: VCB, TCB, MB...
                            option.text =
                                `${bank.shortName} - ${bank.name}`; // Hiển thị: VCB - Ngân hàng TMCP Ngoại Thương

                            // Lưu tên đầy đủ vào một data-attribute ẩn để lát gán vào input hidden
                            option.setAttribute('data-fullname', bank.name);

                            bankSelect.appendChild(option);
                        });
                    }
                })
                .catch(error => {
                    console.error('Lỗi lấy danh sách ngân hàng:', error);
                    document.getElementById('bank_code_select').innerHTML =
                        '<option value="" disabled selected>-- Lỗi tải dữ liệu, vui lòng thử lại --</option>';
                });

            // 2. Bắt sự kiện khi người dùng chọn Ngân hàng -> Gán tên đầy đủ vào input ẩn
            document.getElementById('bank_code_select').addEventListener('change', function(e) {
                const selectedOption = e.target.options[e.target.selectedIndex];
                const fullName = selectedOption.getAttribute('data-fullname');

                // Đổ tên đầy đủ vào thẻ input hidden để form submit lên Server
                document.getElementById('bank_name_input').value = fullName;
            });
        });
    </script>

    <script>
        const addBankModal = document.getElementById('addBankModal');
        const addBankModalContent = document.getElementById('addBankModalContent');
        let isBankListLoaded = false; // Biến kiểm tra để không gọi API nhiều lần

        // Hàm mở Modal
        function openAddBankModal() {
            addBankModal.classList.remove('hidden');
            // Delay 1 chút để hiệu ứng CSS chạy mượt
            setTimeout(() => {
                addBankModal.classList.remove('opacity-0');
                addBankModalContent.classList.remove('scale-95');
            }, 10);

            // Load danh sách ngân hàng nếu chưa load
            if (!isBankListLoaded) {
                loadModalBanks();
            }
        }

        // Hàm đóng Modal
        function closeAddBankModal() {
            addBankModal.classList.add('opacity-0');
            addBankModalContent.classList.add('scale-95');
            setTimeout(() => {
                addBankModal.classList.add('hidden');
            }, 300); // Đợi CSS transition chạy xong mới ẩn hẳn
        }

        // Hàm tải danh sách ngân hàng (Chỉ dùng riêng cho modal này)
        function loadModalBanks() {
            const select = document.getElementById('modal_bank_select');
            const inputName = document.getElementById('modal_bank_name');

            fetch('https://api.vietqr.io/v2/banks')
                .then(res => res.json())
                .then(data => {
                    if (data.code === '00') {
                        select.innerHTML = '<option value="" disabled selected>-- Chọn ngân hàng --</option>';
                        data.data.forEach(bank => {
                            let option = document.createElement('option');
                            option.value = bank.code; // Hoặc bank.bin nếu bạn dùng tra cứu Napas thật
                            option.text = `${bank.shortName} - ${bank.name}`;
                            option.setAttribute('data-fullname', bank.name);
                            select.appendChild(option);
                        });
                        isBankListLoaded = true;
                    }
                });

            // Tự động điền Tên đầy đủ của ngân hàng vào input hidden
            select.addEventListener('change', function(e) {
                inputName.value = e.target.options[e.target.selectedIndex].getAttribute('data-fullname');
            });
        }
        // Mở lại modal nếu Submit bị lỗi (Laravel trả về error)
        @if ($errors->addBank->any())

            document.addEventListener('DOMContentLoaded', function() {
                openAddBankModal();
            });
        @endif
    </script>


    <script>
        // Xử lý hiện/ẩn Form nhập thủ công
        function toggleManualEntry(isManual) {
            const manualFields = document.getElementById('manual-entry-fields');
            const bankName = document.getElementById('manual_bank_name');
            const accNumber = document.getElementById('manual_account_number');
            const accName = document.getElementById('manual_account_name');

            if (isManual) {
                // Hiển thị form và bắt buộc nhập (required)
                manualFields.classList.remove('hidden');
                bankName.setAttribute('required', 'true');
                accNumber.setAttribute('required', 'true');
                accName.setAttribute('required', 'true');
            } else {
                // Ẩn form và gỡ bỏ bắt buộc nhập để không bị kẹt khi submit
                manualFields.classList.add('hidden');
                bankName.removeAttribute('required');
                accNumber.removeAttribute('required');
                accName.removeAttribute('required');

                // Xóa trắng dữ liệu lỡ nhập thừa
                bankName.value = '';
                accNumber.value = '';
                accName.value = '';
            }
        }
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            fetchBankList();
        });

        function fetchBankList() {
            const selectElement = document.getElementById('manual_bank_name');
            // Lấy giá trị ngân hàng người dùng đã chọn trước đó (nếu bị lỗi form)
            const oldBankValue = selectElement.getAttribute('data-old');

            // Gọi API của VietQR
            fetch('https://api.vietqr.io/v2/banks')
                .then(response => response.json())
                .then(data => {
                    if (data.code === '00' && data.data) {
                        // Xóa chữ "Đang tải..." và thay bằng chữ mặc định
                        selectElement.innerHTML = '<option value="">-- Chọn ngân hàng --</option>';

                        // Duyệt qua danh sách ngân hàng API trả về
                        data.data.forEach(bank => {
                            const option = document.createElement('option');
                            // Bạn có thể lưu bank.shortName (VD: VCB, MB) hoặc bank.bin (mã định danh)
                            option.value = bank.shortName;
                            option.textContent =
                                `${bank.shortName} - ${bank.name}`; // Hiện: "VCB - Ngân hàng Ngoại thương Việt Nam"

                            // Nếu giống giá trị cũ đã nhập thì tự động select lại
                            if (oldBankValue === bank.shortName) {
                                option.selected = true;
                            }

                            selectElement.appendChild(option);
                        });
                    }
                })
                .catch(error => {
                    console.error('Lỗi lấy danh sách ngân hàng:', error);
                    selectElement.innerHTML = '<option value="">Lỗi tải danh sách, vui lòng thử lại sau</option>';
                });
        }
    </script>
@endpush
