@extends('client.profiles.layouts.app')

@section('profile_content')
    <main class="flex-1 flex flex-col overflow-hidden">

        @include('popup_notify.index')

        <div class="flex-1 overflow-y-auto  space-y-6">

            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <a href="{{ url()->previous() }}"
                        class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700/50 text-slate-500 hover:text-slate-700 dark:hover:text-slate-200 p-2 rounded-xl shadow-sm transition-all">
                        <span class="material-symbols-outlined flex items-center justify-center">arrow_back</span>
                    </a>
                    <div>
                        <h2 class="text-2xl font-bold text-slate-900 dark:text-slate-100 tracking-tight">Lịch sử rút tiền
                        </h2>
                    </div>
                </div>
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
                    <p class="text-slate-500 text-xs font-bold uppercase tracking-wider">Tổng tiền đã nhận</p>
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
                                placeholder="Tìm theo mã giao dịch..." type="text" />
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

                            @if (request()->filled('search') || request()->filled('status') || request()->filled('sort'))
                                <a href="{{ route('admin.withdrawals.history', $user->id) }}"
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
                                <th class="px-6 py-4">Mã GD</th>
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
                                        <span
                                            class="text-sm font-bold text-slate-900 dark:text-slate-100 uppercase">#{{ $withdrawal->id }}</span>
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
                                            {{-- Nút Xem chi tiết truyền đúng ID vào JS --}}
                                            <button type="button"
                                                onclick="openModal('withdrawalModal-{{ $withdrawal->id }}')"
                                                class="p-2 text-slate-400 hover:text-blue-500 transition-colors bg-slate-50 hover:bg-blue-50 dark:bg-slate-800 dark:hover:bg-slate-700 rounded-lg"
                                                title="Xem chi tiết">
                                                <span class="material-symbols-outlined text-lg">visibility</span>
                                            </button>

                                            {{-- Nút Hủy --}}
                                            @if ($withdrawal->status === 'pending' || $withdrawal->status === 'Chờ duyệt')
                                                <form action="{{ route('wallet.withdrawal.cancelled', $withdrawal->id) }}"
                                                    method="POST">
                                                    @csrf
                                                    <button type="submit"
                                                        class="p-2 text-slate-400 hover:text-red-500 transition-colors bg-slate-50 hover:bg-red-50 dark:bg-slate-800 dark:hover:bg-red-900/30 rounded-lg"
                                                        title="Hủy bỏ">
                                                        <span class="material-symbols-outlined text-lg">close</span>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-slate-500">
                                        <div class="flex flex-col items-center justify-center">
                                            <span
                                                class="material-symbols-outlined text-5xl mb-3 text-slate-300">receipt_long</span>
                                            <p class="font-medium text-slate-400">Người dùng này chưa có yêu cầu rút tiền
                                                nào.</p>
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

    {{-- MODAL CHI TIẾT GIAO DỊCH (RENDER TRỰC TIẾP TỪ BLADE CHO TỪNG RECORD) --}}
    @foreach ($withdrawals as $withdrawal)
        <div id="withdrawalModal-{{ $withdrawal->id }}"
            class="fixed inset-0 z-[100] hidden overflow-y-auto w-full h-full">
            {{-- Nền mờ --}}
            <div class="fixed inset-0 bg-slate-900/50 dark:bg-black/80 backdrop-blur-sm transition-opacity"
                onclick="closeModal('withdrawalModal-{{ $withdrawal->id }}')"></div>

            <div class="relative min-h-screen flex items-center justify-center p-4 pointer-events-none">
                <div
                    class="relative bg-white dark:bg-slate-800 rounded-2xl max-w-lg w-full shadow-2xl overflow-hidden border border-slate-200 dark:border-slate-700 transition-all pointer-events-auto">

                    {{-- Header --}}
                    <div
                        class="p-5 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center bg-slate-50/50 dark:bg-slate-800/50">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-10 h-10 rounded-full bg-blue-50 dark:bg-blue-500/10 flex items-center justify-center text-blue-500">
                                <span class="material-symbols-outlined">payments</span>
                            </div>
                            <h3 class="text-lg font-bold text-slate-900 dark:text-slate-100">Chi tiết giao dịch <span
                                    class="text-blue-500">#{{ $withdrawal->id }}</span></h3>
                        </div>
                        <button onclick="closeModal('withdrawalModal-{{ $withdrawal->id }}')"
                            class="text-slate-400 hover:text-red-500 bg-white hover:bg-red-50 dark:bg-slate-900 dark:hover:bg-red-500/10 w-8 h-8 rounded-full flex items-center justify-center transition-all border border-slate-200 dark:border-slate-700">
                            <span class="material-symbols-outlined text-sm">close</span>
                        </button>
                    </div>

                    {{-- Body --}}
                    <div class="p-6 space-y-6 max-h-[70vh] overflow-y-auto custom-scrollbar">
                        {{-- Status & Amount --}}
                        <div class="text-center pb-6 border-b border-slate-100 dark:border-slate-700">
                            <div class="mb-4">
                                {{-- Render Trạng thái Badge --}}
                                @if (strtolower($withdrawal->status) === 'pending')
                                    <div
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold uppercase border tracking-wider bg-orange-50 dark:bg-orange-500/10 text-orange-600 dark:text-orange-500 border-orange-200 dark:border-orange-500/20">
                                        <span class="w-1.5 h-1.5 rounded-full bg-orange-500 animate-pulse"></span> Chờ
                                        duyệt
                                    </div>
                                @elseif (strtolower($withdrawal->status) === 'approved' || strtolower($withdrawal->status) === 'completed')
                                    <div
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold uppercase border tracking-wider bg-green-50 dark:bg-green-500/10 text-green-600 dark:text-green-500 border-green-200 dark:border-green-500/20">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Đã duyệt
                                    </div>
                                @elseif (strtolower($withdrawal->status) === 'rejected')
                                    <div
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold uppercase border tracking-wider bg-red-50 dark:bg-red-500/10 text-red-600 dark:text-red-500 border-red-200 dark:border-red-500/20">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Từ chối
                                    </div>
                                @else
                                    <div
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold uppercase border tracking-wider bg-slate-100 dark:bg-slate-500/10 text-slate-600 dark:text-slate-400 border-slate-200 dark:border-slate-500/20">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-500"></span> Đã hủy
                                    </div>
                                @endif
                            </div>

                            <p class="text-xs text-slate-500 font-bold uppercase tracking-wider mb-2">Số tiền yêu cầu rút
                            </p>
                            <p class="text-4xl font-black text-slate-900 dark:text-slate-100">
                                {{ number_format($withdrawal->amount) }}₫</p>
                            <p class="text-xs text-slate-500 font-medium mt-2">Tạo lúc:
                                {{ $withdrawal->created_at->format('d/m/Y H:i') }}</p>
                        </div>

                        {{-- Bank Info --}}
                        <div
                            class="grid grid-cols-2 gap-4 bg-slate-50 dark:bg-slate-900 p-5 rounded-xl border border-slate-100 dark:border-slate-700">
                            <div class="space-y-1">
                                <p class="text-xs text-slate-500 font-bold uppercase tracking-wider">Ngân hàng</p>
                                <p class="text-sm font-bold text-slate-900 dark:text-slate-100">
                                    {{ $withdrawal->bank_name }}</p>
                            </div>
                            <div class="space-y-1">
                                <p class="text-xs text-slate-500 font-bold uppercase tracking-wider">Số tài khoản</p>
                                <p class="text-sm font-bold text-blue-500">{{ $withdrawal->account_number }}</p>
                            </div>
                            <div class="col-span-2 pt-3 border-t border-slate-200 dark:border-slate-700 mt-1">
                                <p class="text-xs text-slate-500 font-bold uppercase tracking-wider mb-1">Chủ tài khoản</p>
                                <p class="text-sm font-bold text-slate-900 dark:text-slate-100 uppercase">
                                    {{ $withdrawal->account_name ?? 'Chưa cập nhật' }}</p>
                            </div>
                            {{-- <div class="col-span-2 pt-3 border-t border-slate-200 dark:border-slate-700 mt-1">
                                <p class="text-xs text-slate-500 font-bold uppercase tracking-wider mb-1">Mã giao dịch (Mã
                                    tham chiếu)</p>
                                <p class="text-sm font-medium text-slate-700 dark:text-slate-300">
                                    {{ $withdrawal->transaction_id ?? 'Chưa có' }}</p>
                            </div> --}}
                        </div>

                        {{-- Khu vực hiển thị Ảnh Chứng Từ --}}
                        @if ($withdrawal->proof_image)
                            <div>
                                <p class="text-xs text-slate-500 font-bold uppercase tracking-wider mb-2">Biên lai / Hình
                                    ảnh chứng từ</p>
                                <div
                                    class="rounded-xl overflow-hidden border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 flex items-center justify-center p-2">
                                    <img src="{{ asset('storage/' . $withdrawal->proof_image) }}" alt="Biên lai giao dịch"
                                        class="max-w-full h-auto max-h-64 object-contain rounded-lg cursor-pointer hover:opacity-90 transition-opacity"
                                        onclick="window.open(this.src, '_blank')" title="Nhấn để xem ảnh lớn">
                                </div>
                            </div>
                        @endif

                        {{-- Note --}}
                        <div
                            class="p-4 bg-orange-50 dark:bg-orange-500/10 rounded-xl border border-orange-100 dark:border-orange-500/20">
                            <p
                                class="text-xs text-orange-600 dark:text-orange-400 font-bold uppercase tracking-wider mb-1.5 flex items-center gap-1">
                                <span class="material-symbols-outlined text-[14px]">edit_note</span> Ghi chú xử lý
                            </p>
                            <p class="text-sm text-orange-800 dark:text-orange-200 leading-relaxed">
                                {{ $withdrawal->admin_note ?? 'Không có ghi chú từ hệ thống' }}</p>
                        </div>
                    </div>

                    {{-- Footer --}}
                    <div
                        class="p-4 bg-slate-50 dark:bg-slate-800/50 border-t border-slate-100 dark:border-slate-700 flex justify-end">
                        <button onclick="closeModal('withdrawalModal-{{ $withdrawal->id }}')"
                            class="px-5 py-2.5 bg-white dark:bg-slate-700 hover:bg-slate-100 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 rounded-lg font-medium text-sm transition-all border border-slate-200 dark:border-slate-600 shadow-sm">
                            Đóng cửa sổ
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    <script>
        let currentOpenModalId = null;

        function openModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
                currentOpenModalId = modalId;
            }
        }

        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.add('hidden');
                document.body.style.overflow = 'auto';
                currentOpenModalId = null;
            }
        }

        // Hỗ trợ đóng modal hiện tại khi nhấn phím ESC
        window.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && currentOpenModalId) {
                closeModal(currentOpenModalId);
            }
        });
    </script>
@endsection
