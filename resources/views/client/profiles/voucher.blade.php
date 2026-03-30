@extends('client.profiles.layouts.app')

@section('profile_content')
    @include('popup_notify.index')

    <main class="flex-1  rounded-xl p-4 md:p-2">
        <header class="mb-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-2">Kho Voucher của tôi</h1>
            <p class="text-gray-500 max-w-2xl font-medium">Quản lý và sử dụng các ưu đãi dành riêng cho bạn tại Bee Phone</p>
        </header>

        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mb-12 ">
            @forelse ($user->userVouchers as $userVoucher)
                @php
                    $voucher = $userVoucher;

                    // KIỂM TRA: Nếu có order_id thì ép trạng thái hiển thị thành "Đã sử dụng"
                    $displayStatus = $voucher->order_id ? 'Đã sử dụng' : $voucher->voucher_status;

                    $statusClasses = match ($displayStatus) {
                        'Hoạt động' => 'bg-green-100 text-green-700 border-green-200',
                        'Tạm dừng' => 'bg-amber-100 text-amber-700 border-amber-200',
                        'Hết lượt dùng' => 'bg-red-100 text-red-700 border-red-200',
                        'Đã Hết hạn' => 'bg-gray-100 text-gray-600 border-gray-200',
                        'Chưa được sử dụng' => 'bg-blue-100 text-blue-700 border-blue-200',
                        'Đã sử dụng'
                            => 'bg-purple-100 text-purple-700 border-purple-200', // Màu riêng cho voucher đã dùng
                        default => 'bg-gray-100 text-gray-600 border-gray-200',
                    };

                    // Thêm 'Đã sử dụng' vào nhóm không còn hiệu lực dùng (isInactive)
                    $isInactive = in_array($displayStatus, ['Đã Hết hạn', 'Hết lượt dùng', 'Tạm dừng', 'Đã sử dụng']);
                @endphp

                <div
                    class="group bg-white rounded-xl overflow-hidden flex border border-gray-200 transition-all duration-300 {{ $isInactive ? 'opacity-75 grayscale-[20%]' : 'hover:shadow-lg hover:border-amber-200' }} relative">
                    <div
                        class="w-32 {{ $isInactive ? 'bg-gray-50' : 'bg-amber-50' }} flex flex-col items-center justify-center p-4 border-r-2 border-dashed border-gray-200 relative">
                        <div
                            class="absolute -top-3 -right-3 w-6 h-6 bg-gray-50 rounded-full border-b border-l border-gray-200">
                        </div>
                        <div
                            class="absolute -bottom-3 -right-3 w-6 h-6 bg-gray-50 rounded-full border-t border-l border-gray-200">
                        </div>

                        <span
                            class="material-symbols-outlined {{ $isInactive ? 'text-gray-400' : 'text-amber-500' }} text-4xl mb-2">
                            {{ $isInactive ? 'block' : 'confirmation_number' }}
                        </span>
                        <span
                            class="text-[10px] font-bold {{ $isInactive ? 'text-gray-500' : 'text-amber-700' }} uppercase tracking-widest text-center">
                            {{ $voucher->code }}
                        </span>
                    </div>

                    <div class="flex-1 p-5">
                        <div class="flex justify-between items-start mb-2">
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase border {{ $statusClasses }}">
                                {{ $displayStatus }}
                            </span>
                            <span onclick="openModal('modal-voucher-{{ $voucher->id }}')"
                                class="material-symbols-outlined text-gray-400 text-lg cursor-pointer hover:text-amber-500 transition-colors">info</span>
                        </div>

                        <h3
                            class="text-lg font-bold {{ $isInactive ? 'text-gray-500' : 'text-gray-900' }} mb-1 leading-tight">
                            {{ $voucher->name }}
                        </h3>

                        <p class="text-sm font-bold text-amber-600 mb-3">
                            @if ($voucher->discount_type == 'percent')
                                Giảm {{ $voucher->discount_value }}%
                                @if ($voucher->max_discount)
                                    <span class="text-xs text-gray-500 font-normal">(Tối đa
                                        {{ number_format($voucher->max_discount, 0, ',', '.') }}đ)</span>
                                @endif
                            @else
                                Giảm {{ number_format($voucher->discount_value, 0, ',', '.') }}đ
                            @endif
                        </p>

                        <div class="flex items-center text-xs text-gray-500 font-medium mb-4">
                            <span class="material-symbols-outlined text-sm mr-1">schedule</span>
                            HSD: {{ $voucher->start_date ? $voucher->start_date->format('d/m/Y') : 'N/A' }}
                        </div>

                        <div class="flex space-x-3">
                            @if ($displayStatus == 'Đã sử dụng')
                                {{-- Nút Xem đơn hàng nếu đã sử dụng --}}
                                <a href="/don-hang/{{ $voucher->order_id }}"
                                    class="flex-1 py-2 bg-purple-500 text-white font-bold rounded-lg text-sm hover:bg-purple-600 transition-colors active:scale-95 shadow-sm text-center">
                                    Xem đơn #{{ $voucher->order_id }}
                                </a>
                            @elseif (!$isInactive)
                                {{-- Nút Dùng ngay nếu còn hiệu lực --}}
                                <a href="{{ route('client.products.index') }}"
                                    class="flex-1 py-2 bg-amber-500 text-white font-bold rounded-lg text-sm hover:bg-amber-600 transition-colors active:scale-95 shadow-sm text-center">
                                    Dùng ngay
                                </a>
                            @else
                                {{-- Truyền ID của voucher vào route --}}
                                <form action="{{ route('user.vouchers.delete', $voucher->id) }}" method="POST"
                                    class="flex-1" onsubmit="return confirm('Bỏ lưu ưu đãi này?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="w-full py-2 bg-red-500 text-white font-bold rounded-lg text-sm hover:bg-red-600 transition-colors active:scale-95 shadow-sm">
                                        Bỏ lưu
                                    </button>
                                </form>
                            @endif

                            <button onclick="openModal('modal-voucher-{{ $voucher->id }}')"
                                class="px-4 py-2 border border-gray-200 text-gray-700 font-semibold rounded-lg text-sm hover:bg-gray-50 transition-colors">
                                Chi tiết
                            </button>
                        </div>
                    </div>
                </div>

                <div id="modal-voucher-{{ $voucher->id }}" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title"
                    role="dialog" aria-modal="true">
                    <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity opacity-0 duration-300"
                        id="backdrop-{{ $voucher->id }}" onclick="closeModal('modal-voucher-{{ $voucher->id }}')"></div>

                    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                            <div id="dialog-{{ $voucher->id }}"
                                class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95 duration-300 sm:my-8 sm:w-full sm:max-w-lg">

                                <button onclick="closeModal('modal-voucher-{{ $voucher->id }}')"
                                    class="absolute top-4 right-4 text-gray-400 hover:text-red-500 transition-colors">
                                    <span class="material-symbols-outlined">close</span>
                                </button>

                                <div class="bg-amber-50 px-6 py-5 border-b border-amber-100 flex items-center">
                                    <div
                                        class="w-12 h-12 bg-white rounded-full flex items-center justify-center shadow-sm mr-4 border border-amber-100">
                                        <span
                                            class="material-symbols-outlined text-amber-500 text-2xl">local_activity</span>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-bold text-gray-900" id="modal-title">Chi tiết ưu đãi</h3>
                                        <p class="text-sm text-amber-600 font-bold tracking-widest">{{ $voucher->code }}
                                        </p>
                                    </div>
                                </div>

                                <div class="px-6 py-6 text-gray-600 space-y-5">
                                    <h4 class="text-xl font-bold text-gray-800 leading-snug">{{ $voucher->name }}</h4>

                                    <div class="bg-gray-50 rounded-xl p-4 border border-gray-100 space-y-3">

                                        <div class="flex justify-between border-b border-gray-200 pb-2">
                                            <span class="text-gray-500 text-sm">Mức giảm:</span>
                                            <span class="font-bold text-amber-600">
                                                @if ($voucher->discount_type == 'percent')
                                                    Giảm {{ $voucher->discount_value }}%
                                                    @if ($voucher->max_discount)
                                                        <span class="text-xs text-gray-500 font-normal">(Tối đa
                                                            {{ number_format($voucher->max_discount, 0, ',', '.') }}đ)</span>
                                                    @endif
                                                @else
                                                    Giảm {{ number_format($voucher->discount_value, 0, ',', '.') }}đ
                                                @endif
                                            </span>
                                        </div>

                                        <div class="flex justify-between border-b border-gray-200 pb-2">
                                            <span class="text-gray-500 text-sm">Đơn tối thiểu:</span>
                                            <span
                                                class="font-bold text-gray-800">{{ number_format($voucher->min_order_value, 0, ',', '.') }}đ</span>
                                        </div>

                                        <div class="flex justify-between border-b border-gray-200 pb-2">
                                            <span class="text-gray-500 text-sm">Giới hạn sử dụng:</span>
                                            <span class="font-medium text-gray-800">
                                                {{ $voucher->usage_limit_per_user ? $voucher->usage_limit_per_user . ' lần/người' : 'Không giới hạn' }}
                                            </span>
                                        </div>

                                        <div class="flex justify-between border-b border-gray-200 pb-2">
                                            <span class="text-gray-500 text-sm">Lượt dùng còn lại:</span>
                                            <span class="font-medium text-gray-800">
                                                @if ($voucher->usage_limit)
                                                    {{ $voucher->usage_limit - $voucher->used_count }} /
                                                    {{ $voucher->usage_limit }} lượt
                                                @else
                                                    Không giới hạn
                                                @endif
                                            </span>
                                        </div>

                                        <div class="flex justify-between border-b border-gray-200 pb-2">
                                            <span class="text-gray-500 text-sm">Hiệu lực từ:</span>
                                            <span
                                                class="font-medium text-gray-800">{{ $voucher->start_date ? \Carbon\Carbon::parse($voucher->start_date)->format('H:i - d/m/Y') : 'N/A' }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-500 text-sm">Hết hạn vào:</span>
                                            <span class="font-bold {{ $isInactive ? 'text-red-500' : 'text-amber-600' }}">
                                                {{ $voucher->end_date ? \Carbon\Carbon::parse($voucher->end_date)->format('H:i - d/m/Y') : 'Vô thời hạn' }}
                                            </span>
                                        </div>
                                        @if ($voucher->order_id)
                                            <div class="flex justify-between border-t border-gray-200 pt-2 mt-2">
                                                <span class="text-gray-500 text-sm">Đã dùng cho đơn:</span>
                                                <a href="/don-hang/{{ $voucher->order_id }}"
                                                    class="font-bold text-purple-600 hover:underline">
                                                    #{{ $voucher->order_id }}
                                                </a>
                                            </div>
                                        @endif
                                    </div>

                                    <div>
                                        <h5 class="font-bold text-gray-800 mb-2 flex items-center">
                                            <span
                                                class="material-symbols-outlined text-base mr-1 text-gray-400">description</span>
                                            Thể lệ chương trình:
                                        </h5>
                                        <div
                                            class="text-sm text-gray-600 leading-relaxed bg-white p-3 border border-gray-100 rounded-lg">
                                            {!! nl2br(
                                                e(
                                                    $voucher->description ??
                                                        'Áp dụng cho mọi đơn hàng thỏa mãn điều kiện giá trị tối thiểu. Số lượng mã có hạn, chương trình có thể kết thúc sớm hơn dự kiến khi hết lượt sử dụng.',
                                                ),
                                            ) !!}
                                        </div>
                                    </div>
                                    <div>
                                        <h5 class="font-bold text-gray-800 mb-2 flex items-center">
                                            <span
                                                class="material-symbols-outlined text-base mr-1 text-gray-400">check_circle</span>
                                            Sản phẩm áp dụng:
                                        </h5>

                                        <div class="text-sm text-gray-600 bg-white p-3 border border-gray-100 rounded-lg">
                                            @php
                                                $hasProducts = $voucher->products && $voucher->products->count() > 0;
                                                $hasCategories =
                                                    $voucher->categories && $voucher->categories->count() > 0;
                                                $hasBrands = $voucher->brands && $voucher->brands->count() > 0;
                                                $hasSpecificConditions = $hasProducts || $hasCategories || $hasBrands;
                                            @endphp

                                            @if ($hasSpecificConditions)
                                                <div class="space-y-3">
                                                    @if ($hasCategories)
                                                        <div>
                                                            <span
                                                                class="text-xs font-bold text-gray-500 uppercase tracking-wider block mb-1">Danh
                                                                mục:</span>
                                                            <div class="flex flex-wrap gap-1.5">
                                                                @foreach ($voucher->categories as $category)
                                                                    <span
                                                                        class="inline-block px-2.5 py-1 bg-blue-50 text-blue-700 border border-blue-100 rounded-md text-[11px] font-semibold">
                                                                        {{ $category->name }}
                                                                    </span>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    @endif

                                                    @if ($hasBrands)
                                                        <div>
                                                            <span
                                                                class="text-xs font-bold text-gray-500 uppercase tracking-wider block mb-1">Thương
                                                                hiệu:</span>
                                                            <div class="flex flex-wrap gap-1.5">
                                                                @foreach ($voucher->brands as $brand)
                                                                    <span
                                                                        class="inline-block px-2.5 py-1 bg-amber-50 text-amber-700 border border-amber-100 rounded-md text-[11px] font-semibold">
                                                                        {{ $brand->name }}
                                                                    </span>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    @endif

                                                    @if ($hasProducts)
                                                        <div>
                                                            <span
                                                                class="text-xs font-bold text-gray-500 uppercase tracking-wider block mb-1">Sản
                                                                phẩm cụ thể:</span>
                                                            <div class="flex flex-wrap gap-1.5">
                                                                @foreach ($voucher->products->take(5) as $product)
                                                                    <span
                                                                        class="inline-block px-2 py-1 bg-gray-50 text-gray-700 border border-gray-200 rounded-md text-[11px] truncate max-w-[200px]"
                                                                        title="{{ $product->name }}">
                                                                        {{ $product->name }}
                                                                    </span>
                                                                @endforeach

                                                                @if ($voucher->products->count() > 5)
                                                                    <span
                                                                        class="inline-block px-2 py-1 bg-gray-800 text-white rounded-md text-[11px] font-bold shadow-sm">
                                                                        +{{ $voucher->products->count() - 5 }} sản phẩm
                                                                        khác
                                                                    </span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            @else
                                                <div class="flex items-center text-green-600 font-bold text-sm">
                                                    <span
                                                        class="material-symbols-outlined text-lg mr-1.5">all_inclusive</span>
                                                    Áp dụng cho toàn bộ sản phẩm trên Bee Phone
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div
                                    class="bg-gray-50 px-6 py-4 flex justify-end space-x-3 rounded-b-2xl border-t border-gray-100">
                                    <button onclick="closeModal('modal-voucher-{{ $voucher->id }}')" type="button"
                                        class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 font-semibold rounded-xl text-sm hover:bg-gray-100 transition-colors">
                                        Đóng
                                    </button>
                                    @if (!$isInactive)
                                        <a href="{{ route('client.products.index') }}"
                                            class="px-5 py-2.5 bg-amber-500 text-white font-bold rounded-xl text-sm hover:bg-amber-600 transition-colors shadow-sm active:scale-95 text-center inline-block">
                                            Sử dụng ngay
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            @empty
                <div
                    class="col-span-full flex flex-col items-center justify-center py-16 bg-white rounded-2xl border-2 border-dashed border-gray-200">
                    <div class="w-24 h-24 bg-amber-50 rounded-full flex items-center justify-center mb-4">
                        <span class="material-symbols-outlined text-amber-300 text-5xl">sentiment_dissatisfied</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Kho Voucher đang trống</h3>
                    <p class="text-gray-500 text-sm mb-6 text-center max-w-sm">
                        Bạn chưa lưu mã giảm giá nào. Hãy săn ngay các ưu đãi hấp dẫn để mua sắm tiết kiệm hơn tại Bee Phone
                        nhé!
                    </p>
                    <a href="/vouchers"
                        class="px-8 py-3 bg-amber-500 text-white font-bold rounded-xl hover:bg-amber-600 transition-colors hover:shadow-lg active:scale-95">
                        Tới trang Khuyến mãi
                    </a>
                </div>
            @endforelse
        </div>

        <section
            class="bg-gradient-to-r from-gray-800 to-gray-900 p-8 rounded-2xl relative overflow-hidden group shadow-lg">
            <div
                class="absolute top-0 right-0 w-64 h-64 bg-white opacity-20 rounded-full -mr-20 -mt-20 blur-2xl group-hover:scale-110 transition-transform duration-700">
            </div>
            <div class="absolute bottom-0 left-0 w-32 h-32 bg-amber-400 opacity-50 rounded-full -ml-10 -mb-10 blur-xl">
            </div>

            <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-6">
                <div class="flex-1">
                    <h2 class="text-2xl font-bold text-white mb-2 shadow-sm">Bạn cần thêm ưu đãi?</h2>
                    <p class="text-amber-50 font-medium max-w-lg">Tham gia các trò chơi hằng ngày trên ứng dụng hoặc đổi
                        điểm Bee Points để lấy thêm nhiều voucher hấp dẫn khác.</p>
                </div>
                <div class="flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-4 shrink-0 w-full md:w-auto">
                    <button
                        class="px-8 py-3 bg-white text-amber-600 font-bold rounded-lg hover:bg-gray-50 transition-colors active:scale-95 shadow-md w-full sm:w-auto">
                        Săn Deal ngay
                    </button>
                    <button
                        class="px-8 py-3 bg-gray-900 text-white font-bold rounded-lg hover:bg-gray-800 transition-colors active:scale-95 shadow-md w-full sm:w-auto">
                        Đổi Bee Points
                    </button>
                </div>
            </div>
        </section>
    </main>
@endsection

@push('js')
    <script>
        function openModal(modalId) {
            const modal = document.getElementById(modalId);
            if (!modal) return; // Bảo vệ: Nếu không tìm thấy modal thì dừng lại

            const backdrop = document.getElementById(modalId.replace('modal-voucher', 'backdrop'));
            const dialog = document.getElementById(modalId.replace('modal-voucher', 'dialog'));

            modal.classList.remove('hidden');

            setTimeout(() => {
                backdrop.classList.remove('opacity-0');
                backdrop.classList.add('opacity-100');

                dialog.classList.remove('opacity-0', 'translate-y-4', 'sm:scale-95');
                dialog.classList.add('opacity-100', 'translate-y-0', 'sm:scale-100');
            }, 10);
        }

        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            if (!modal) return;

            const backdrop = document.getElementById(modalId.replace('modal-voucher', 'backdrop'));
            const dialog = document.getElementById(modalId.replace('modal-voucher', 'dialog'));

            backdrop.classList.remove('opacity-100');
            backdrop.classList.add('opacity-0');

            dialog.classList.remove('opacity-100', 'translate-y-0', 'sm:scale-100');
            dialog.classList.add('opacity-0', 'translate-y-4', 'sm:scale-95');

            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }
    </script>
@endpush
