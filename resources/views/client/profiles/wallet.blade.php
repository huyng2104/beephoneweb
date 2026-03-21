@extends('client.profiles.layouts.app')

@section('profile_content')
    @include('popup_notify.index')
    <main class="flex-1 space-y-6" data-purpose="wallet-main-content">
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
                            {{ number_format($user->wallet->balance, 0, ',', '.') }}<span
                                class="text-2xl ml-1 text-[#f4c025]">đ</span></h2>
                    </div>
                    <div class="flex flex-wrap gap-4">
                        <button type="button" onclick="openDepositModal()"
                            class="bg-[#f4c025] text-black px-8 py-3 rounded-xl font-bold hover:bg-yellow-400 transition-all flex items-center gap-2 shadow-lg shadow-yellow-900/20">
                            <svg class="h-5 w-5" fill="currentColor" viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
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
                    </div>

                </div>
            </div>
        </section>
        <section class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden"
            data-purpose="transaction-list">
            <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                <h3 class="font-bold text-lg text-gray-900">Lịch sử giao dịch nội bộ</h3>
                <button class="text-sm text-[#f4c025] font-semibold hover:underline">Xem tất cả</button>
            </div>
            <div class="overflow-x-auto w-full rounded-lg border border-gray-100 dark:border-gray-800">
                <table class="w-full text-left min-w-[800px]">
                    <thead class="bg-gray-50 dark:bg-gray-800 text-gray-500 text-xs uppercase">
                        <tr>
                            <th class="px-4 py-3 md:px-6 md:py-4 font-bold whitespace-nowrap">Ngày</th>
                            <th class="px-4 py-3 md:px-6 md:py-4 font-bold whitespace-nowrap">Loại giao dịch</th>
                            <th class="px-4 py-3 md:px-6 md:py-4 font-bold min-w-[250px]">Mô tả</th>
                            <th class="px-4 py-3 md:px-6 md:py-4 font-bold whitespace-nowrap">Số tiền</th>
                            <th class="px-4 py-3 md:px-6 md:py-4 font-bold whitespace-nowrap">Trạng thái</th>
                            <th class="px-4 py-3 md:px-6 md:py-4 font-bold text-right whitespace-nowrap">Chi tiết</th>
                            <th class="px-4 py-3 md:px-6 md:py-4 font-bold text-right whitespace-nowrap">Hành Động</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @php
                            // KHAI BÁO BIẾN PHÂN TRANG Ở ĐÂY ĐỂ TRÁNH LỖI LINKS()
                            $paginatedTransactions = $user->wallet->transactions()->latest()->paginate(4);
                        @endphp
                        
                        @if ($paginatedTransactions->count() != 0)
                            @foreach ($paginatedTransactions as $transaction)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">

                                    <td class="px-4 py-3 md:px-6 md:py-4 text-sm whitespace-nowrap">
                                        <div class="font-medium text-gray-700 dark:text-gray-300">
                                            {{ $transaction->created_at->format('d/m/Y') }}
                                        </div>
                                        <div class="text-xs text-gray-500 mt-0.5">
                                            {{ $transaction->created_at->format('H:i:s') }}
                                        </div>
                                    </td>

                                    <td class="px-4 py-3 md:px-6 md:py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-2">
                                            {!! $transaction->type_transaction !!}
                                        </div>
                                    </td>

                                    <td class="px-4 py-3 md:px-6 md:py-4 whitespace-normal min-w-[250px]">
                                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                                            {{ $transaction->description }}

                                            @if ($transaction->reference)
                                                @if ($transaction->reference_type == 'App\Models\User')
                                                    <a href="{{ $transaction->reference->id }}"
                                                        class="text-blue-500 hover:underline">
                                                        #{{ $transaction->reference->name }}
                                                    </a>
                                                @elseif ($transaction->reference_type == 'App\Models\WithdrawalRequest')
                                                    <a href="{{ $transaction->reference->id }}"
                                                        class="text-blue-500 hover:underline">
                                                        #{{ $transaction->reference->id }}
                                                    </a>
                                                @endif
                                            @endif
                                        </p>
                                    </td>

                                    @if ($transaction->type == 'deposit' || $transaction->type == 'refund')
                                        @if ($transaction->status == 'completed')
                                            <td
                                                class="px-4 py-3 md:px-6 md:py-4 font-bold text-green-600 text-sm whitespace-nowrap">
                                                + {{ number_format($transaction->amount, 0, ',', '.') }}đ
                                            </td>
                                        @else
                                            <td class="px-4 py-3 md:px-6 md:py-4 font-bold text-sm whitespace-nowrap">
                                                {{ number_format($transaction->amount, 0, ',', '.') }}đ
                                            </td>
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

                                    <td class="px-4 py-3 md:px-6 md:py-4 text-right whitespace-nowrap">
                                        <button type="button"
                                            class="text-gray-400 hover:text-blue-500 transition-colors inline-flex justify-end w-full"
                                            title="Xem chi tiết"
                                            data-before="{{ number_format($transaction->balance_before ?? 0, 0, ',', '.') }}đ"
                                            data-after="{{ number_format($transaction->balance_after ?? 0, 0, ',', '.') }}đ"
                                            data-desc="{{ $transaction->description ?? 'Không có mô tả' }}"
                                            onclick="openTransactionModal(this)">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                                fill="currentColor">
                                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                                <path fill-rule="evenodd"
                                                    d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </td>

                                    <td class="px-4 py-3 md:px-6 md:py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex justify-end gap-2">
                                            @if ($transaction->type === 'withdraw' && $transaction->status === 'pending')
                                                <form action="{{ route('wallet.withdrawal.cancelled', $transaction->id) }}"
                                                    method="POST"
                                                    onsubmit="return confirm('Bạn có chắc chắn muốn hủy lệnh rút tiền này? Số tiền sẽ được hoàn lại vào ví của bạn ngay lập tức.');">
                                                    @csrf
                                                    <button type="submit"
                                                        class="bg-red-50 text-red-500 border border-red-200 hover:bg-red-500 hover:text-white px-3 py-1.5 rounded-lg transition-colors whitespace-nowrap">
                                                        Hủy lệnh
                                                    </button>
                                                </form>
                                            @endif

                                            @if ($transaction->type === 'deposit' && $transaction->status === 'pendding')
                                                <a
                                                    class="bg-blue-50 text-blue-500 border border-blue-200 hover:bg-blue-500 hover:text-white px-3 py-1.5 rounded-lg transition-colors text-center cursor-pointer whitespace-nowrap">
                                                    Thanh toán lại
                                                </a>
                                            @endif
                                        </div>
                                    </td>

                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="7" class="px-6 py-8 text-center text-gray-500 italic">
                                    Chưa có lịch sử giao dịch
                                </td>
                            </tr>
                        @endif

                    </tbody>

                </table>
                <div class="p-4 border-t border-gray-100">
                    {{-- SỬ DỤNG BIẾN MỚI ĐỂ GỌI LINKS() CHUẨN XÁC --}}
                    {{ $paginatedTransactions->links() }}
                </div>
            </div>
        </section>
        </main>

    {{-- Popup cho chi tiết giao dịch --}}
    <div id="transactionModal"
        class="fixed inset-0 z-50 hidden bg-black/50 backdrop-blur-sm flex items-center justify-center transition-opacity">

        <div
            class="bg-white dark:bg-gray-800 w-full max-w-md rounded-2xl shadow-xl overflow-hidden transform transition-all">
            <div
                class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center bg-gray-50 dark:bg-gray-900">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Chi tiết giao dịch</h3>
                <button onclick="closeTransactionModal()" class="text-gray-400 hover:text-red-500">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <div class="px-6 py-6 space-y-4">
                <div
                    class="flex justify-between items-center pb-3 border-b border-dashed border-gray-200 dark:border-gray-700">
                    <span class="text-sm text-gray-500">Số dư trước GD:</span>
                    <span id="modal-before" class="font-medium text-gray-900 dark:text-gray-300"></span>
                </div>

                <div
                    class="flex justify-between items-center pb-3 border-b border-dashed border-gray-200 dark:border-gray-700">
                    <span class="text-sm text-gray-500">Số dư sau GD:</span>
                    <span id="modal-after" class="font-bold text-gray-900 dark:text-white"></span>
                </div>

                <div>
                    <span class="text-sm text-gray-500 block mb-2">Mô tả giao dịch:</span>
                    <p id="modal-desc"
                        class="text-sm p-3 bg-gray-50 dark:bg-gray-900/50 rounded-lg text-gray-700 dark:text-gray-300 italic">
                    </p>
                </div>
            </div>

            <div class="px-6 py-4 bg-gray-50 dark:bg-gray-900/50 text-right">
                <button onclick="closeTransactionModal()"
                    class="px-4 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-white text-sm font-medium rounded-lg transition-colors">
                    Đóng lại
                </button>
            </div>
        </div>
    </div>

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