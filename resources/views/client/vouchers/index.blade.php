@extends('client.layouts.app')

@section('title', 'Danh sách mã giảm giá')

@section('content')
    @include('popup_notify.index')
    <main class="flex-1 rounded-xl p-4 md:p-2 max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <header class="mb-8 mt-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-2">Khuyến mãi & Ưu đãi</h1>
            <p class="text-gray-500 max-w-2xl font-medium">Săn ngay các mã giảm giá hấp dẫn để mua sắm tiết kiệm hơn tại Bee
                Phone</p>
        </header>

        {{-- BỘ LỌC --}}
        <div class="bg-white p-5 rounded-xl border border-gray-200 mb-8 shadow-sm">
            <form action="" method="GET" id="filterForm" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1.5 tracking-wider">Tìm kiếm mã</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-2.5 text-gray-400 text-sm">search</span>
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Nhập tên hoặc mã..."
                            class="w-full pl-9 pr-4 py-2 text-sm border border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1.5 tracking-wider">Loại giảm
                        giá</label>
                    <select name="type"
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500">
                        <option value="">Tất cả loại</option>
                        <option value="percent" {{ request('type') == 'percent' ? 'selected' : '' }}>Giảm theo %</option>
                        <option value="fixed" {{ request('type') == 'fixed' ? 'selected' : '' }}>Giảm số tiền cố định
                        </option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1.5 tracking-wider">Đơn tối
                        thiểu</label>
                    <select name="min_spend"
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500">
                        <option value="">Mọi giá trị</option>
                        <option value="0-200k" {{ request('min_spend') == '0-200k' ? 'selected' : '' }}>Dưới 200.000đ
                        </option>
                        <option value="200k-1m" {{ request('min_spend') == '200k-1m' ? 'selected' : '' }}>Từ 200k - 1 triệu
                        </option>
                        <option value="above-1m" {{ request('min_spend') == 'above-1m' ? 'selected' : '' }}>Trên 1 triệu
                        </option>
                    </select>
                </div>
                <div class="flex space-x-2">
                    <button type="submit"
                        class="flex-1 py-2 bg-amber-500 text-white font-bold rounded-lg text-sm hover:bg-amber-600 transition-colors shadow-sm active:scale-95 flex items-center justify-center">
                        <span class="material-symbols-outlined text-sm mr-1">filter_alt</span> Lọc
                    </button>
                    <a href="{{ url()->current() }}"
                        class="px-4 py-2 border border-gray-300 text-gray-700 font-semibold rounded-lg text-sm hover:bg-gray-100 transition-colors flex items-center justify-center">
                        Bỏ lọc
                    </a>
                </div>
            </form>
        </div>

        {{-- DANH SÁCH VOUCHERS --}}
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mb-12">
            @forelse ($vouchers as $voucher)
                @php
                    // Kiểm tra xem user hiện tại đã lưu voucher này chưa
                    $isSaved = in_array($voucher->id, $savedVoucherIds ?? []);

                    // Logic cũ của bạn có nguy cơ lỗi, mình cấu trúc lại phần trạng thái:
                    $isOutOfStock = $voucher->usage_limit && $voucher->used_count >= $voucher->usage_limit;
                    $isExpired = $voucher->end_date && \Carbon\Carbon::now()->greaterThan($voucher->end_date);
                    $isInactive = $isOutOfStock || $isExpired;

                    $displayStatus = $isExpired ? 'Đã hết hạn' : ($isOutOfStock ? 'Hết lượt dùng' : 'Đang diễn ra');
                    $statusClasses = match ($displayStatus) {
                        'Đang diễn ra' => 'bg-green-100 text-green-700 border-green-200',
                        'Hết lượt dùng' => 'bg-red-100 text-red-700 border-red-200',
                        'Đã hết hạn' => 'bg-gray-100 text-gray-600 border-gray-200',
                        default => 'bg-gray-100 text-gray-600 border-gray-200',
                    };
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
                            {{ $isInactive ? 'block' : 'loyalty' }}
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

                        <h3 class="text-lg font-bold {{ $isInactive ? 'text-gray-500' : 'text-gray-900' }} mb-1 leading-tight line-clamp-2"
                            title="{{ $voucher->name }}">
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
                            HSD:
                            {{ $voucher->end_date ? \Carbon\Carbon::parse($voucher->end_date)->format('d/m/Y') : 'Vô thời hạn' }}
                        </div>

                        <div class="flex space-x-3">
                            @if ($isInactive)
                                <button disabled
                                    class="flex-1 py-2 bg-gray-200 text-gray-500 font-bold rounded-lg text-sm cursor-not-allowed">
                                    Đã kết thúc
                                </button>
                            @elseif ($isSaved)
                                <a href="{{ route('client.products.index') }}"
                                    class="flex-1 py-2 bg-purple-100 text-purple-700 border border-purple-200 font-bold rounded-lg text-sm hover:bg-purple-200 transition-colors text-center inline-block">
                                    Dùng ngay
                                </a>
                            @else
                                <form action="{{ route('vouchers.save', $voucher->id) }}" method="POST" class="flex-1">
                                    @csrf
                                    <button
                                        class="w-full py-2 bg-amber-500 text-white font-bold rounded-lg text-sm hover:bg-amber-600 transition-colors active:scale-95 shadow-sm">
                                        Lưu voucher
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

                {{-- Modal Chi Tiết Voucher --}}
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
                    <div class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                        <span class="material-symbols-outlined text-gray-300 text-5xl">inventory_2</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Chưa có chương trình khuyến mãi</h3>
                    <p class="text-gray-500 text-sm mb-6 text-center max-w-sm">
                        Hiện tại Bee Phone chưa có chương trình ưu đãi nào đang diễn ra. Bạn vui lòng quay lại sau nhé!
                    </p>
                </div>
            @endforelse
        </div>

        {{ $vouchers->links('pagination::tailwind') }}
    </main>
@endsection

@push('js')
    <script>
        function openModal(modalId) {
            const modal = document.getElementById(modalId);
            if (!modal) return;

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
