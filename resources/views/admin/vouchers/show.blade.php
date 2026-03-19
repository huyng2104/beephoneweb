@extends('admin.layouts.app')
@section('content')
    <main class="flex-1 p-2 overflow-y-auto">
        <div class="max-w-[1200px] mx-auto px-6 py-6">
            {{-- <nav class="flex items-center gap-2 mb-6 text-sm">
                            <a class="text-slate-500 hover:text-primary" href="#">Trang chủ</a>
                            <span class="material-symbols-outlined text-slate-400 text-[16px]">chevron_right</span>
                            <a class="text-slate-500 hover:text-primary" href="#">Quản lý khuyến mãi</a>
                            <span class="material-symbols-outlined text-slate-400 text-[16px]">chevron_right</span>
                            <span class="text-slate-900 dark:text-white font-semibold">Chi tiết mã giảm giá</span>
                        </nav> --}}
            <div class="flex flex-wrap justify-between items-end gap-4 mb-8">
                <div class="flex flex-col gap-2">
                    <div class="flex items-center gap-3">
                        <h1 class="text-slate-900 dark:text-white text-3xl font-black tracking-tight">{{ $voucher->name }}
                        </h1>
                        @php
                            $color = match ($voucher->voucher_status) {
                                'Tạm dừng' => 'yellow',
                                'Hết lượn dùng' => 'gray',
                                'Đã Hết hạn' => 'red',
                                default => 'green',
                            };
                        @endphp
                        <span
                            class="px-2.5 py-1 rounded-full bg-{{ $color }}-600 text-white text-xs font-bold uppercase tracking-wider">
                            {{ $voucher->voucher_status }}
                        </span>
                    </div>
                    <p class="text-slate-500 text-base">
                        Cập nhật lần cuối: {{ $voucher->updated_at->diffForHumans() }}
                    </p>
                </div>
                <div class="flex gap-3">

                    <a href="{{route('admin.vouchers.edit',$voucher->id)}}">
                        <button
                            class="flex min-w-[140px] cursor-pointer items-center justify-center rounded-lg h-11 px-5 bg-primary text-slate-900 text-sm font-bold shadow-lg shadow-primary/20 hover:scale-[1.02] transition-all">
                            <span>Chỉnh sửa</span>
                        </button>
                    </a>
                </div>
            </div>
            <div class="flex-1 overflow-y-auto  bg-background-light">
                <div class="max-w-7xl mx-auto space-y-6">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-6">
                            <h3 class="text-base font-bold text-slate-900 mb-4 flex items-center gap-2">
                                <span class="material-symbols-outlined text-primary">info</span>
                                Thông tin cơ bản
                            </h3>
                            <dl class="space-y-4">
                                <div>
                                    <dt class="text-xs font-medium text-slate-500 uppercase tracking-wider">Tên chương
                                        trình</dt>
                                    <dd class="text-sm font-bold text-slate-900 mt-1">{{ $voucher->name }}
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-xs font-medium text-slate-500 uppercase tracking-wider">Mã code
                                    </dt>
                                    <dd class="text-sm font-mono font-bold text-primary mt-1">{{ $voucher->code }}</dd>
                                </div>
                                <div>
                                    <dt class="text-xs font-medium text-slate-500 uppercase tracking-wider">Mô tả</dt>
                                    <dd class="text-sm text-slate-600 mt-1">{{ $voucher->description }}</dd>
                                </div>
                                <div class="grid grid-cols-2 gap-4 pt-2">
                                    <div>
                                        <dt class="text-xs font-medium text-slate-500 uppercase tracking-wider">Loại
                                            giảm giá</dt>
                                        <dd class="text-sm font-semibold text-slate-900 mt-1">
                                            {{ $voucher->discount_type == 'fixed' ? 'Cố định' : 'Phần trăm' }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-xs font-medium text-slate-500 uppercase tracking-wider">Giá trị giảm
                                        </dt>
                                        <dd class="text-sm font-bold text-emerald-600 mt-1">
                                            {{ $voucher->discount_value }}{{ $voucher->discount_type == 'fixed' ? 'đ' : '%' }}
                                        </dd>
                                    </div>
                                </div>
                                <div>
                                    <dt class="text-xs font-medium text-slate-500 uppercase tracking-wider">Đơn hàng
                                        tối thiểu</dt>
                                    <dd class="text-sm font-semibold text-slate-900 mt-1">{{ $voucher->min_order_value }}
                                    </dd>
                                </div>
                            </dl>
                        </div>
                        <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-6">
                            <h3 class="text-base font-bold text-slate-900 mb-4 flex items-center gap-2">
                                <span class="material-symbols-outlined text-primary">gavel</span>
                                Điều kiện &amp; Giới hạn
                            </h3>
                            <dl class="space-y-4">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <dt class="text-xs font-medium text-slate-500 uppercase tracking-wider">Bắt đầu</dt>
                                        <dd class="text-sm font-semibold text-slate-900 mt-1">
                                            {{ \Carbon\Carbon::parse($voucher->start_date)->format('d/m/Y') }}
                                        </dd>
                                    </div>

                                    <div>
                                        <dt class="text-xs font-medium text-slate-500 uppercase tracking-wider">Kết thúc
                                        </dt>
                                        <dd class="text-sm font-semibold text-slate-900 mt-1">
                                            {{ \Carbon\Carbon::parse($voucher->end_date)->format('d/m/Y') }}
                                        </dd>
                                    </div>
                                </div>
                                <div>
                                    <dt class="text-xs font-medium text-slate-500 uppercase tracking-wider">Tổng lượt
                                        phát hành</dt>
                                    <dd class="text-sm font-bold text-slate-900 mt-1">
                                        {{ $voucher->usage_limit == null ? 'Không giới hạn' : $voucher->usage_limit }}</dd>
                                </div>
                                <div>
                                    <dt class="text-xs font-medium text-slate-500 uppercase tracking-wider">Giới hạn
                                        mỗi khách hàng</dt>
                                    <dd class="text-sm font-semibold text-slate-900 mt-1">
                                        {{ $voucher->usage_limit_per_user }}</dd>
                                </div>
                                <div>
                                    @if (!$voucher->categories->isEmpty())
                                        <dt class="text-xs font-medium text-slate-500 uppercase tracking-wider">Danh mục áp
                                            dụng</dt>
                                        <div class="flex flex-wrap gap-2 mt-2">
                                            @foreach ($voucher->categories as $category)
                                                <span
                                                    class="px-2 py-1 bg-slate-100 text-slate-600 rounded text-[11px] font-bold">
                                                    {{ $category->name }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    @if (!$voucher->brands->isEmpty())
                                        <dt class="text-xs font-medium text-slate-500 uppercase tracking-wider">Thương hiệu
                                            áp dụng</dt>
                                        <div class="flex flex-wrap gap-2 mt-2">
                                            @foreach ($voucher->brands as $brand)
                                                <span
                                                    class="px-2 py-1 bg-slate-100 text-slate-600 rounded text-[11px] font-bold">
                                                    {{ $brand->name }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    @if (!$voucher->products->isEmpty())
                                        <dt class="text-xs font-medium text-slate-500 uppercase tracking-wider">Sản phẩm áp
                                            dụng</dt>
                                        <div class="flex flex-wrap gap-2 mt-2">
                                            @foreach ($voucher->products as $product)
                                                <span
                                                    class="px-2 py-1 bg-slate-100 text-slate-600 rounded text-[11px] font-bold">
                                                    {{ $product->name }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </dl>
                        </div>
                        <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-6">
                            <h3 class="text-base font-bold text-slate-900 mb-4 flex items-center gap-2">
                                <span class="material-symbols-outlined text-primary">analytics</span>
                                Thống kê hiệu quả
                            </h3>
                            <div class="space-y-6">
                                <div class="flex justify-between items-end mb-2">
                                    <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">
                                        Số lượt đã dùng
                                    </p>
                                    <p class="text-lg font-black text-slate-900">
                                        {{ $voucher->used_count }}/{{ $voucher->usage_limit }}
                                    </p>
                                </div>

                                <div class="h-2 w-full bg-slate-100 rounded-full overflow-hidden">
                                    <div class="h-full bg-primary" style="width: {{ $voucher->usage_percent }}%;"></div>
                                </div>
                                {{-- <div class="grid grid-cols-1 gap-4">
                                    <div class="p-4 bg-emerald-50 rounded-lg border border-emerald-100">
                                        <p class="text-xs font-medium text-emerald-600 uppercase tracking-wider">Doanh
                                            thu mang lại</p>
                                        <p class="text-xl font-black text-emerald-700 mt-1">452.500.000đ</p>
                                    </div>
                                    <div class="p-4 bg-blue-50 rounded-lg border border-blue-100">
                                        <p class="text-xs font-medium text-blue-600 uppercase tracking-wider">Tổng tiền
                                            đã giảm</p>
                                        <p class="text-xl font-black text-blue-700 mt-1">45.250.000đ</p>
                                    </div>
                                </div> --}}
                            </div>
                        </div>
                    </div>
                    {{-- <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                            <h3 class="text-base font-bold text-slate-900">Lịch sử sử dụng</h3>
                            <div class="flex items-center gap-2">
                                <div class="relative">
                                    <span
                                        class="material-symbols-outlined absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm">search</span>
                                    <input
                                        class="pl-8 pr-3 py-1.5 text-xs bg-slate-50 border-slate-200 rounded-lg focus:ring-primary focus:border-primary"
                                        placeholder="Tìm đơn hàng..." type="text" />
                                </div>
                            </div>
                        </div>
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50 border-b border-slate-100">
                                    <th
                                        class="px-6 py-3 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                        Mã đơn</th>
                                    <th
                                        class="px-6 py-3 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                        Khách hàng</th>
                                    <th
                                        class="px-6 py-3 text-[11px] font-bold text-slate-500 uppercase tracking-wider text-right">
                                        Giá trị đơn</th>
                                    <th
                                        class="px-6 py-3 text-[11px] font-bold text-slate-500 uppercase tracking-wider text-right text-emerald-600">
                                        Số tiền giảm</th>
                                    <th
                                        class="px-6 py-3 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                        Ngày dùng</th>
                                    <th
                                        class="px-6 py-3 text-[11px] font-bold text-slate-500 uppercase tracking-wider text-right">
                                        Thao tác</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-6 py-4">
                                        <span class="text-sm font-bold text-slate-900">#ORD-9921</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2">
                                            <div
                                                class="size-6 rounded-full bg-slate-100 flex items-center justify-center text-[10px] font-bold text-slate-500">
                                                HN</div>
                                            <span class="text-sm text-slate-700">Hoàng Nam</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm font-medium text-slate-700 text-right">15.990.000đ
                                    </td>
                                    <td class="px-6 py-4 text-sm font-bold text-emerald-600 text-right">-1.599.000đ
                                    </td>
                                    <td class="px-6 py-4 text-sm text-slate-500">15/10/2023 14:20</td>
                                    <td class="px-6 py-4 text-right">
                                        <button
                                            class="p-1.5 hover:bg-slate-200 rounded text-slate-400 hover:text-primary transition-colors">
                                            <span class="material-symbols-outlined text-[18px]">visibility</span>
                                        </button>
                                    </td>
                                </tr>
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-6 py-4">
                                        <span class="text-sm font-bold text-slate-900">#ORD-9854</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2">
                                            <div
                                                class="size-6 rounded-full bg-slate-100 flex items-center justify-center text-[10px] font-bold text-slate-500">
                                                MA</div>
                                            <span class="text-sm text-slate-700">Minh Anh</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm font-medium text-slate-700 text-right">8.500.000đ</td>
                                    <td class="px-6 py-4 text-sm font-bold text-emerald-600 text-right">-850.000đ</td>
                                    <td class="px-6 py-4 text-sm text-slate-500">14/10/2023 09:15</td>
                                    <td class="px-6 py-4 text-right">
                                        <button
                                            class="p-1.5 hover:bg-slate-200 rounded text-slate-400 hover:text-primary transition-colors">
                                            <span class="material-symbols-outlined text-[18px]">visibility</span>
                                        </button>
                                    </td>
                                </tr>
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-6 py-4">
                                        <span class="text-sm font-bold text-slate-900">#ORD-9732</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2">
                                            <div
                                                class="size-6 rounded-full bg-slate-100 flex items-center justify-center text-[10px] font-bold text-slate-500">
                                                QT</div>
                                            <span class="text-sm text-slate-700">Quốc Trung</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm font-medium text-slate-700 text-right">24.200.000đ
                                    </td>
                                    <td class="px-6 py-4 text-sm font-bold text-emerald-600 text-right">-2.420.000đ
                                    </td>
                                    <td class="px-6 py-4 text-sm text-slate-500">12/10/2023 21:45</td>
                                    <td class="px-6 py-4 text-right">
                                        <button
                                            class="p-1.5 hover:bg-slate-200 rounded text-slate-400 hover:text-primary transition-colors">
                                            <span class="material-symbols-outlined text-[18px]">visibility</span>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="px-6 py-3 bg-slate-50 border-t border-slate-100 flex items-center justify-between">
                            <p class="text-[11px] font-medium text-slate-500">Đang hiển thị 3 trên tổng số 75 lượt dùng
                            </p>
                            <div class="flex items-center gap-1">
                                <button
                                    class="p-1.5 rounded-md hover:bg-slate-200 text-slate-400 transition-colors"><span
                                        class="material-symbols-outlined text-[16px]">chevron_left</span></button>
                                <button
                                    class="size-6 flex items-center justify-center bg-primary text-background-dark text-[10px] font-bold rounded shadow-sm">1</button>
                                <button
                                    class="size-6 flex items-center justify-center hover:bg-slate-200 text-[10px] font-bold rounded">2</button>
                                <button
                                    class="size-6 flex items-center justify-center hover:bg-slate-200 text-[10px] font-bold rounded">3</button>
                                <button
                                    class="p-1.5 rounded-md hover:bg-slate-200 text-slate-400 transition-colors"><span
                                        class="material-symbols-outlined text-[16px]">chevron_right</span></button>
                            </div>
                        </div>
                    </div> --}}
                </div>
            </div>
        </div>
    </main>
@endsection
