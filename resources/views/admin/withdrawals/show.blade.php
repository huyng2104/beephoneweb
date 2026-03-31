@extends('admin.layouts.app')
@section('title', 'Chi tiết rút tiền #' . $withdrawal->id)

@section('content')
    <main class="flex-1 overflow-y-auto p-8">
        <div class="">
            <a href="{{ route('admin.withdrawals.index') }}"
                class="flex items-center gap-2 text-slate-500 hover:text-primary mb-4 transition-colors w-max">
                <span class="material-symbols-outlined text-sm">arrow_back</span>
                <span class="font-medium">Quay lại danh sách</span>
            </a>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <div class="lg:col-span-2 space-y-6">
                    <div
                        class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">

                        <div class="p-6 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center">
                            <h3 class="font-black text-lg text-slate-900 dark:text-white uppercase tracking-tight">Thông tin
                                rút tiền</h3>
                            @if ($withdrawal->status == 'pending')
                                <span
                                    class="px-3 py-1 rounded-full bg-orange-100 text-orange-600 text-xs font-bold uppercase">Đang
                                    chờ xử lý</span>
                            @elseif($withdrawal->status == 'approved')
                                <span
                                    class="px-3 py-1 rounded-full bg-green-100 text-green-600 text-xs font-bold uppercase">Đã
                                    duyệt</span>
                            @elseif($withdrawal->status == 'rejected')
                                <span class="px-3 py-1 rounded-full bg-red-100 text-red-600 text-xs font-bold uppercase">Đã
                                    từ chối</span>
                            @elseif($withdrawal->status == 'canceled')
                                <span
                                    class="px-3 py-1 rounded-full bg-slate-100 text-slate-600 text-xs font-bold uppercase">Đã
                                    hủy</span>
                            @endif
                        </div>

                        <div class="p-6 grid grid-cols-2 gap-8">
                            <div>
                                <p class="text-xs font-bold text-slate-400 uppercase mb-1">Số tiền rút</p>
                                <p class="text-3xl font-black text-primary">{{ number_format($withdrawal->amount) }}đ</p>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-slate-400 uppercase mb-1">Mã giao dịch</p>
                                <p class="text-sm font-mono font-bold text-slate-700 dark:text-slate-300">
                                    #{{ $withdrawal->transaction_id ?? $withdrawal->id }}
                                </p>
                            </div>

                            <div
                                class="col-span-2 p-5 bg-slate-50 dark:bg-slate-900/50 rounded-2xl border border-dashed border-slate-200 dark:border-slate-700">
                                <p class="text-xs font-bold text-slate-500 uppercase mb-4 flex items-center gap-2">
                                    <span class="material-symbols-outlined text-sm">account_balance</span> Thông tin thụ
                                    hưởng
                                </p>

                                <div class="flex flex-col md:flex-row gap-6 relative">
                                    {{-- Cột thông tin Text --}}
                                    <div class="flex-1 space-y-4">
                                        <div
                                            class="flex justify-between items-center pb-2 border-b border-slate-200/50 dark:border-slate-800">
                                            <span class="text-sm text-slate-500">Ngân hàng:</span>
                                            <span
                                                class="text-sm font-bold text-slate-900 dark:text-white">{{ $withdrawal->bank_name }}</span>
                                        </div>
                                        <div
                                            class="flex justify-between items-center pb-2 border-b border-slate-200/50 dark:border-slate-800">
                                            <span class="text-sm text-slate-500">Số tài khoản:</span>
                                            <div class="flex items-center gap-2">
                                                <span id="account_number"
                                                    class="text-sm font-black text-blue-600 tracking-wider">{{ $withdrawal->account_number }}</span>
                                                <button onclick="copyToClipboard('{{ $withdrawal->account_number }}')"
                                                    type="button"
                                                    class="size-7 flex items-center justify-center rounded-lg bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-sm hover:text-primary transition-all"
                                                    title="Copy">
                                                    <span class="material-symbols-outlined text-xs">content_copy</span>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="flex justify-between items-center">
                                            <span class="text-sm text-slate-500">Chủ tài khoản:</span>
                                            <span
                                                class="text-sm font-bold text-slate-900 dark:text-white uppercase">{{ $withdrawal->account_name }}</span>
                                        </div>
                                    </div>

                                    {{-- Cột hiển thị mã QR --}}
                                    <div
                                        class="md:w-[150px] flex flex-col items-center justify-center pt-4 md:pt-0 md:pl-6 md:border-l border-slate-200 dark:border-slate-700">
                                        <div class="bg-white p-1.5 rounded-xl border border-slate-200 shadow-sm cursor-pointer hover:scale-105 transition-transform"
                                            onclick="openQrModal()">
                                            {{-- Gọi API VietQR --}}
                                            <img src="https://img.vietqr.io/image/{{ $withdrawal->bank_name }}-{{ $withdrawal->account_number }}-compact2.png?amount={{ $withdrawal->amount }}&addInfo=Thanh toan rut tien {{ $withdrawal->id }}&accountName={{ urlencode($withdrawal->account_name) }}"
                                                alt="QR Chuyển khoản" class="w-28 h-28 object-contain rounded-lg"
                                                onerror="this.style.display='none'; this.nextElementSibling.style.display='block';" />

                                            {{-- Text hiển thị dự phòng nếu API lỗi hoặc sai mã ngân hàng --}}
                                            <div style="display: none;"
                                                class="w-28 h-28 flex items-center justify-center text-center bg-slate-50 rounded-lg">
                                                <span class="text-[10px] text-slate-400">Không thể<br>tạo mã QR</span>
                                            </div>
                                        </div>
                                        <p
                                            class="text-[10px] font-medium text-slate-500 mt-2 text-center uppercase tracking-wide">
                                            Bấm vào để phóng to
                                        </p>
                                    </div>
                                </div>

                                {{-- Popup Modal Phóng To QR --}}
                                <div id="qrModal"
                                    class="fixed inset-0 z-[100] hidden bg-slate-900/80 backdrop-blur-sm flex items-center justify-center p-4 transition-all opacity-0"
                                    onclick="closeQrModal()">
                                    <div class="relative bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-2xl max-w-sm w-full transform scale-95 transition-transform duration-300"
                                        id="qrModalContent" onclick="event.stopPropagation()">
                                        {{-- Nút Đóng --}}
                                        <button onclick="closeQrModal()"
                                            class="absolute -top-12 right-0 text-white hover:text-slate-300 transition-colors flex items-center gap-1">
                                            <span class="material-symbols-outlined text-3xl">close</span>
                                        </button>

                                        <h3 class="text-center font-bold text-slate-800 dark:text-white mb-4 text-lg">Quét
                                            mã để chuyển khoản</h3>

                                        <div class="bg-white p-2 rounded-xl border border-slate-200 flex justify-center">
                                            <img src="https://img.vietqr.io/image/{{ $withdrawal->bank_name }}-{{ $withdrawal->account_number }}-compact2.png?amount={{ $withdrawal->amount }}&addInfo=Thanh toan rut tien {{ $withdrawal->id }}&accountName={{ urlencode($withdrawal->account_name) }}"
                                                alt="QR Chuyển khoản lớn"
                                                class="w-full h-auto max-w-[300px] object-contain rounded-lg" />
                                        </div>

                                        <div class="mt-4 text-center space-y-1">
                                            <p class="text-2xl font-black text-primary">
                                                {{ number_format($withdrawal->amount) }}đ</p>
                                            <p class="text-sm text-slate-500 dark:text-slate-400">
                                                {{ $withdrawal->bank_name }} - <span
                                                    class="font-bold">{{ $withdrawal->account_number }}</span></p>
                                            <p class="text-sm font-bold text-slate-700 dark:text-slate-300 uppercase">
                                                {{ $withdrawal->account_name }}</p>
                                        </div>
                                    </div>
                                </div>

                                {{-- Javascript điều khiển Modal --}}
                                <script>
                                    function openQrModal() {
                                        const modal = document.getElementById('qrModal');
                                        const content = document.getElementById('qrModalContent');

                                        modal.classList.remove('hidden');
                                        // Thêm timeout nhỏ để hiệu ứng fade-in mượt mà hơn
                                        setTimeout(() => {
                                            modal.classList.remove('opacity-0');
                                            modal.classList.add('opacity-100');
                                            content.classList.remove('scale-95');
                                            content.classList.add('scale-100');
                                        }, 10);
                                    }

                                    function closeQrModal() {
                                        const modal = document.getElementById('qrModal');
                                        const content = document.getElementById('qrModalContent');

                                        modal.classList.remove('opacity-100');
                                        modal.classList.add('opacity-0');
                                        content.classList.remove('scale-100');
                                        content.classList.add('scale-95');

                                        // Đợi hiệu ứng xong mới ẩn hẳn
                                        setTimeout(() => {
                                            modal.classList.add('hidden');
                                        }, 300);
                                    }
                                </script>
                            </div>
                        </div>

                        @if ($withdrawal->status == 'pending')
                            <div
                                class="p-6 bg-slate-50 dark:bg-slate-900/50 border-t border-slate-100 dark:border-slate-700">
                                <form method="POST" enctype="multipart/form-data" class="space-y-4">
                                    @csrf
                                    <div>
                                        <label class="block text-xs font-bold text-slate-500 uppercase mb-2 text-center">Xác
                                            nhận chuyển khoản</label>

                                        {{-- Khu vực tải ảnh lên --}}
                                        <div
                                            class="relative flex flex-col items-center justify-center border-2 border-dashed border-slate-300 dark:border-slate-600 rounded-2xl p-4 hover:border-primary transition-colors cursor-pointer bg-white dark:bg-slate-800 min-h-[120px] overflow-hidden">

                                            {{-- Input file: Cố định phủ toàn bộ khung ẩn --}}
                                            <input type="file" name="proof_image"
                                                accept="image/png, image/jpeg, image/jpg"
                                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                                                onchange="previewProofImage(event)">

                                            {{-- Giao diện chữ và icon lúc chưa có ảnh --}}
                                            <div id="upload-placeholder"
                                                class="flex flex-col items-center pointer-events-none transition-all">
                                                <span
                                                    class="material-symbols-outlined text-3xl text-slate-400 mb-2">add_a_photo</span>
                                                <p class="text-xs text-slate-500">Tải lên ảnh bill chuyển khoản (JPG, PNG)
                                                </p>
                                            </div>

                                            {{-- Thẻ img dùng để hiển thị ảnh tạm (Mặc định ẩn) --}}
                                            <img id="image-preview" src="" alt="Ảnh bill tạm"
                                                class="hidden absolute inset-0 w-full h-full object-contain p-2 z-0" />
                                        </div>
                                        {{-- Nút xóa ảnh tạm (Mặc định ẩn) --}}
                                        <div class="text-center mt-2 hidden" id="remove-image-btn">
                                            <button type="button" onclick="clearPreview()"
                                                class="text-xs text-red-500 hover:text-red-600 font-medium flex items-center justify-center gap-1 mx-auto">
                                                <span class="material-symbols-outlined text-[14px]">delete</span> Bỏ chọn
                                                ảnh
                                            </button>
                                        </div>
                                    </div>

                                    <textarea name="admin_note" rows="2"
                                        class="w-full rounded-xl border-slate-200 focus:border-primary focus:ring focus:ring-primary/20 dark:bg-slate-800 dark:border-slate-700 text-sm outline-none transition-all p-3"
                                        placeholder="Ghi chú cho người dùng (ví dụ: Đã chuyển khoản hoặc Lý do từ chối)"></textarea>

                                    <div class="grid grid-cols-2 gap-4">
                                        <button type="submit"
                                            formaction="{{ route('admin.withdrawals.approve', $withdrawal->id) }}"
                                            class="bg-primary hover:bg-primary/90 text-slate-900 font-bold py-3 rounded-xl shadow-lg shadow-primary/20 transition-all flex items-center justify-center gap-2">
                                            <span class="material-symbols-outlined">check_circle</span> Duyệt đơn
                                        </button>

                                        <button type="submit"
                                            formaction="{{ route('admin.withdrawals.reject', $withdrawal->id) }}"
                                            onclick="return confirm('Bạn có chắc muốn từ chối và HOÀN TIỀN lại cho khách?')"
                                            class="bg-white dark:bg-slate-800 border border-red-200 text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 font-bold py-3 rounded-xl transition-all flex items-center justify-center gap-2 shadow-sm">
                                            <span class="material-symbols-outlined">cancel</span> Từ chối
                                        </button>
                                    </div>
                                </form>
                            </div>

                            {{-- Javascript xử lý hiển thị ảnh --}}
                            <script>
                                function previewProofImage(event) {
                                    const file = event.target.files[0];
                                    const previewImg = document.getElementById('image-preview');
                                    const placeholder = document.getElementById('upload-placeholder');
                                    const removeBtn = document.getElementById('remove-image-btn');

                                    if (file) {
                                        // Tạo URL tạm thời cho ảnh vừa chọn
                                        previewImg.src = URL.createObjectURL(file);

                                        // Hiển thị ảnh, ẩn chữ
                                        previewImg.classList.remove('hidden');
                                        placeholder.classList.add('hidden');
                                        removeBtn.classList.remove('hidden');
                                    }
                                }

                                function clearPreview() {
                                    const fileInput = document.querySelector('input[name="proof_image"]');
                                    const previewImg = document.getElementById('image-preview');
                                    const placeholder = document.getElementById('upload-placeholder');
                                    const removeBtn = document.getElementById('remove-image-btn');

                                    // Xóa value của input file
                                    fileInput.value = '';

                                    // Xóa src và ẩn ảnh, hiện lại chữ
                                    previewImg.src = '';
                                    previewImg.classList.add('hidden');
                                    placeholder.classList.remove('hidden');
                                    removeBtn.classList.add('hidden');
                                }
                            </script>
                        @endif
                    </div>

                    @if ($withdrawal->status != 'pending')
                        <div
                            class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-200 dark:border-slate-700 shadow-sm p-6 space-y-4">
                            <h3
                                class="font-black text-sm text-slate-900 dark:text-white uppercase tracking-tight border-b border-slate-100 dark:border-slate-700 pb-3">
                                Kết quả xử lý</h3>
                            @if ($withdrawal->admin_note)
                                <div>
                                    <p class="text-xs font-bold text-slate-400 uppercase mb-1">Ghi chú của Admin</p>
                                    <p
                                        class="text-sm text-slate-700 dark:text-slate-300 bg-slate-50 dark:bg-slate-900 p-3 rounded-xl">
                                        {{ $withdrawal->admin_note }}</p>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>

                <div class="space-y-6">
                    <div
                        class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-200 dark:border-slate-700 shadow-sm p-6 text-center">
                        <div
                            class="size-24 rounded-full bg-slate-100 mx-auto mb-4 overflow-hidden border-4 border-white shadow-md">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($withdrawal->user->name) }}&background=random"
                                class="w-full h-full object-cover">
                        </div>
                        <h4 class="font-black text-slate-900 dark:text-white">{{ $withdrawal->user->name }}</h4>
                        <p class="text-xs text-slate-500 mb-4">{{ $withdrawal->user->email }}</p>

                        <div class="bg-blue-50 dark:bg-blue-500/10 rounded-2xl p-4">
                            <p class="text-[10px] font-bold text-blue-500 uppercase tracking-widest">Số dư ví hiện tại</p>
                            <p class="text-xl font-black text-blue-700 dark:text-blue-400">
                                {{ number_format($withdrawal->user->wallet->balance ?? 0) }}đ
                            </p>
                        </div>

                        <a href="{{ route('admin.wallet.index', ['search' => $withdrawal->user->email]) }}"
                            class="mt-4 inline-flex items-center gap-2 text-xs font-bold text-slate-400 hover:text-primary transition-colors">
                            Xem chi tiết ví <span class="material-symbols-outlined text-sm">open_in_new</span>
                        </a>
                    </div>

                    @if ($withdrawal->proof_image)
                        <div
                            class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-200 dark:border-slate-700 shadow-sm p-4 text-center">
                            <p class="text-xs font-bold text-slate-400 uppercase mb-3">Bằng chứng chuyển khoản</p>
                            <img src="{{ Storage::url($withdrawal->proof_image) }}"
                                class="rounded-xl w-full cursor-zoom-in border border-slate-100 dark:border-slate-700"
                                onclick="window.open(this.src)">
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </main>

    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text);
            alert('Đã copy số tài khoản: ' + text);
        }
    </script>
@endsection
