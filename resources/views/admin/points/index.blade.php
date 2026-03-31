@extends('admin.layouts.app')

@section('content')
<div class="p-8 flex flex-col gap-8">

    @if (session('success'))
    <div class="p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl font-bold flex items-center gap-2">
        <span class="material-symbols-outlined">check_circle</span>
        {{ session('success') }}
    </div>
    @endif

    <section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        
        <div class="bg-white dark:bg-slate-900 p-5 rounded-xl border border-slate-200 dark:border-slate-800 flex flex-col gap-2 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex justify-between items-start">
                <p class="text-slate-500 text-sm font-medium">Tổng điểm phát hành</p>
                <span class="material-symbols-outlined text-primary">toll</span>
            </div>
            <p class="text-2xl font-extrabold text-slate-900 dark:text-white">{{ number_format($totalPointsIssued ?? 0) }}</p>
            <div class="flex items-center gap-1 text-slate-500 text-xs font-bold mt-1">
                <span class="material-symbols-outlined text-[16px]">inventory_2</span>
                <span>Điểm khách đang giữ</span>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-5 rounded-xl border border-slate-200 dark:border-slate-800 flex flex-col gap-2 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex justify-between items-start">
                <p class="text-slate-500 text-sm font-medium">Tổng điểm đã quy đổi</p>
                <span class="material-symbols-outlined text-orange-500">redeem</span>
            </div>
            <p class="text-2xl font-extrabold text-slate-900 dark:text-white">{{ number_format($totalPointsRedeemed ?? 0) }}</p>
            <div class="flex items-center gap-1 text-green-600 text-xs font-bold mt-1">
                <span class="material-symbols-outlined text-[16px]">check_circle</span>
                <span>Đã khấu trừ vào đơn hàng</span>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-5 rounded-xl border border-slate-200 dark:border-slate-800 flex flex-col gap-2 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex justify-between items-start">
                <p class="text-slate-500 text-sm font-medium">Khách hàng tích cực</p>
                <span class="material-symbols-outlined text-blue-500">person_celebrate</span>
            </div>
            <p class="text-2xl font-extrabold text-slate-900 dark:text-white">{{ number_format($activeCustomers ?? 0) }}</p>
            <div class="flex items-center gap-1 text-blue-600 text-xs font-bold mt-1">
                <span class="material-symbols-outlined text-[16px]">group</span>
                <span>Thành viên có điểm</span>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-5 rounded-xl border border-slate-200 dark:border-slate-800 flex flex-col gap-2 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex justify-between items-start">
                <p class="text-slate-500 text-sm font-medium">Giá trị quy đổi</p>
                <span class="material-symbols-outlined text-purple-500">currency_exchange</span>
            </div>
            <p class="text-2xl font-extrabold text-slate-900 dark:text-white">
                {{ number_format($setting->redeem_rate ?? 1000) }}đ <span class="text-sm text-slate-500">/ 1 điểm</span>
            </p>
            <div class="flex items-center gap-1 text-slate-500 text-xs font-bold mt-1">
                <span class="material-symbols-outlined text-[16px]">info</span>
                <span>Đang áp dụng trên hệ thống</span>
            </div>
        </div>
    </section>

    <section>
        <h3 class="text-slate-900 dark:text-white text-xl font-extrabold mb-5 flex items-center gap-2">
            <span class="material-symbols-outlined text-primary">tune</span> Cấu hình tỷ lệ và quy đổi
        </h3>

        <form action="{{ route('admin.points.settings.update') }}" method="POST" class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            @csrf
            
            <div class="bg-white dark:bg-slate-900 p-6 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <h4 class="text-lg font-bold text-slate-900 dark:text-white">Tỷ lệ tích điểm</h4>
                        <p class="text-sm text-slate-500">Cài đặt số tiền chi tiêu để nhận được 1 điểm thưởng.</p>
                    </div>
                    <div class="bg-primary/10 p-2 rounded-lg">
                        <span class="material-symbols-outlined text-primary">add_shopping_cart</span>
                    </div>
                </div>
                <div class="flex flex-col gap-4">
                    <div class="flex items-center gap-4">
                        <div class="flex-1">
                            <label class="block text-xs font-bold text-slate-500 mb-1 uppercase tracking-wider">Giá trị chi tiêu (VNĐ)</label>
                            <input name="earn_rate" type="number" value="{{ old('earn_rate', $setting->earn_rate) }}" class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-lg font-bold text-slate-900 dark:text-white focus:ring-primary"/>
                            @error('earn_rate') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div class="mt-5 text-xl font-bold text-slate-500">=</div>
                        <div class="w-24">
                            <label class="block text-xs font-bold text-slate-500 mb-1 uppercase tracking-wider">Điểm</label>
                            <input class="w-full bg-slate-100 dark:bg-slate-700 border-none rounded-lg font-bold text-slate-900 dark:text-white" readonly type="number" value="1"/>
                        </div>
                    </div>
                    <div class="p-3 bg-primary/5 rounded-lg border border-primary/20">
                        <p class="text-sm text-slate-900 dark:text-slate-300">
                            <span class="font-bold">Mẹo:</span> Cứ mua <span class="text-primary font-bold">{{ number_format($setting->earn_rate) }}đ</span> khách sẽ được cộng 1 điểm.
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-900 p-6 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col justify-between">
                <div>
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <h4 class="text-lg font-bold text-slate-900 dark:text-white">Giá trị quy đổi</h4>
                            <p class="text-sm text-slate-500">Cài đặt giá trị của 1 điểm khi quy đổi ra tiền/mã.</p>
                        </div>
                        <div class="bg-green-100 p-2 rounded-lg">
                            <span class="material-symbols-outlined text-green-600">payments</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-24">
                            <label class="block text-xs font-bold text-slate-500 mb-1 uppercase tracking-wider">1 Điểm</label>
                            <input class="w-full bg-slate-100 dark:bg-slate-700 border-none rounded-lg font-bold text-slate-900 dark:text-white" readonly type="number" value="1"/>
                        </div>
                        <div class="mt-5 text-xl font-bold text-slate-500">=</div>
                        <div class="flex-1">
                            <label class="block text-xs font-bold text-slate-500 mb-1 uppercase tracking-wider">Giá trị (VNĐ)</label>
                            <input name="redeem_rate" type="number" value="{{ old('redeem_rate', $setting->redeem_rate) }}" class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-lg font-bold text-slate-900 dark:text-white focus:ring-primary"/>
                            @error('redeem_rate') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="p-3 bg-green-50 rounded-lg border border-green-100 mb-4">
                        <p class="text-sm text-green-800">
                            <span class="font-bold">Lưu ý:</span> Thay đổi tỷ lệ sẽ ảnh hưởng đến giá trị quy đổi của toàn bộ điểm hiện tại của khách hàng.
                        </p>
                    </div>
                </div>

                <button type="submit" class="w-full bg-primary hover:brightness-105 text-black font-bold py-3 rounded-lg transition-all flex items-center justify-center gap-2 mt-auto">
                    <span class="material-symbols-outlined text-[20px]">save</span> CẬP NHẬT CẤU HÌNH ĐIỂM
                </button>
            </div>
        </form>
    </section>

  <section>
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-slate-900 dark:text-white text-xl font-extrabold flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">featured_seasonal_and_gifts</span> Quản lý Đổi quà & Mã giảm giá
            </h3>
            {{-- 🚀 NÚT THÊM MỚI CHUYỂN HƯỚNG SANG TRANG CREATE VOUCHER --}}
            <a href="{{ route('admin.vouchers.create') }}" class="bg-black dark:bg-white dark:text-black text-white px-4 py-2 rounded-lg text-sm font-bold flex items-center gap-2 hover:opacity-90 transition-all shadow-sm">
                <span class="material-symbols-outlined text-[20px]">add</span> Thêm phần quà mới
            </a>
        </div>
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-sm">
            <table class="w-full text-left">
                <thead class="bg-slate-50 dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700">
                    <tr>
                        <th class="px-6 py-4 text-xs font-extrabold text-slate-500 uppercase tracking-wider">Quà tặng / Mã giảm giá</th>
                        <th class="px-6 py-4 text-xs font-extrabold text-slate-500 uppercase tracking-wider">Điểm cần đổi</th>
                        <th class="px-6 py-4 text-xs font-extrabold text-slate-500 uppercase tracking-wider">Kho hàng</th>
                        <th class="px-6 py-4 text-xs font-extrabold text-slate-500 uppercase tracking-wider text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                    {{-- 🚀 LẶP DATA TỪ DATABASE RA ĐÂY --}}
                    @forelse($vouchers as $voucher)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="size-12 rounded-lg bg-yellow-100 flex items-center justify-center border border-primary/20">
                                    <span class="material-symbols-outlined text-primary text-3xl">confirmation_number</span>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-slate-900 dark:text-white">{{ $voucher->name }}</p>
                                    <p class="text-xs text-slate-500">Mã code: <span class="font-mono text-primary font-bold">{{ $voucher->code }}</span></p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-1">
                                <span class="text-sm font-bold text-slate-900 dark:text-white">{{ number_format($voucher->points_required) }}</span>
                                <span class="material-symbols-outlined text-primary text-[18px]">toll</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            {{-- Tính toán số lượng tồn kho --}}
                            @if(is_null($voucher->usage_limit))
                                <p class="text-sm text-slate-900 dark:text-slate-300 font-bold">Vô hạn</p>
                            @else
                                @php
                                    $remaining = max(0, $voucher->usage_limit - $voucher->used_count);
                                @endphp
                                <p class="text-sm font-bold {{ $remaining == 0 ? 'text-red-500' : 'text-slate-900 dark:text-slate-300' }}">
                                    Còn {{ $remaining }} / {{ $voucher->usage_limit }}
                                </p>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-2">
                                {{-- 🚀 NÚT EDIT CHUYỂN HƯỚNG SANG TRANG EDIT VOUCHER --}}
                                <a href="{{ route('admin.vouchers.edit', $voucher->id) }}" class="p-2 text-slate-500 hover:text-primary transition-colors" title="Sửa mã này">
                                    <span class="material-symbols-outlined">edit</span>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-slate-500">
                            Chưa có phần quà (Voucher) nào được thiết lập đổi bằng điểm.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            
            {{-- 🚀 THANH PHÂN TRANG (PAGINATION) --}}
            @if($vouchers->hasPages())
                <div class="p-4 border-t border-slate-200 dark:border-slate-700">
                    {{ $vouchers->links() }}
                </div>
            @endif
        </div>
    </section>

    <section>
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-slate-900 dark:text-white text-xl font-extrabold flex items-center gap-2">
                <span class="material-symbols-outlined text-yellow-500 text-3xl animate-pulse">emoji_events</span> 
                Bảng vàng Khách hàng Tích điểm
            </h3>
            <a href="#" class="text-sm font-bold text-primary hover:underline flex items-center gap-1">
                Xem tất cả <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
            </a>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-sm">
            <table class="w-full text-left">
                <thead class="bg-slate-50 dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700">
                    <tr>
                        <th class="px-6 py-4 text-xs font-extrabold text-slate-500 uppercase tracking-wider w-20 text-center">Thứ hạng</th>
                        <th class="px-6 py-4 text-xs font-extrabold text-slate-500 uppercase tracking-wider">Khách hàng</th>
                        <th class="px-6 py-4 text-xs font-extrabold text-slate-500 uppercase tracking-wider">Phân hạng</th>
                        <th class="px-6 py-4 text-xs font-extrabold text-slate-500 uppercase tracking-wider text-right">Tổng điểm</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                    @forelse($topUsers ?? [] as $index => $user)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors group">
                        
                        {{-- Cột 1: Huy chương / Số thứ tự --}}
                        <td class="px-6 py-4 text-center">
                            @if($index == 0)
                                <div class="size-10 bg-yellow-100 rounded-full flex items-center justify-center mx-auto border-2 border-yellow-400 shadow-[0_0_15px_rgba(250,204,21,0.5)]">
                                    <span class="material-symbols-outlined text-yellow-600 text-2xl">workspace_premium</span>
                                </div>
                            @elseif($index == 1)
                                <div class="size-10 bg-slate-100 rounded-full flex items-center justify-center mx-auto border-2 border-slate-300">
                                    <span class="material-symbols-outlined text-slate-500 text-2xl">workspace_premium</span>
                                </div>
                            @elseif($index == 2)
                                <div class="size-10 bg-orange-50 rounded-full flex items-center justify-center mx-auto border-2 border-orange-300">
                                    <span class="material-symbols-outlined text-orange-600 text-2xl">workspace_premium</span>
                                </div>
                            @else
                                <span class="text-lg font-black text-slate-400">#{{ $index + 1 }}</span>
                            @endif
                        </td>

                        {{-- Cột 2: Thông tin user --}}
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="size-10 rounded-full bg-primary/20 flex items-center justify-center text-primary font-extrabold text-lg">
                                    {{ substr($user->name ?? 'K', 0, 1) }}
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-slate-900 dark:text-white group-hover:text-primary transition-colors">{{ $user->name ?? 'Khách hàng' }}</p>
                                    <p class="text-xs text-slate-500">{{ $user->email ?? $user->phone ?? 'Chưa cập nhật' }}</p>
                                </div>
                            </div>
                        </td>

                        {{-- Cột 3: Phân hạng (VIP, Vàng, Bạc...) --}}
                        <td class="px-6 py-4">
                            @if(($user->total_points ?? 0) > 10000)
                                <span class="px-3 py-1 bg-yellow-100 text-yellow-700 text-[10px] font-extrabold uppercase rounded-full border border-yellow-200">💎 VIP Kim Cương</span>
                            @elseif(($user->total_points ?? 0) > 5000)
                                <span class="px-3 py-1 bg-orange-100 text-orange-700 text-[10px] font-extrabold uppercase rounded-full border border-orange-200">👑 Vàng</span>
                            @else
                                <span class="px-3 py-1 bg-slate-100 text-slate-600 text-[10px] font-extrabold uppercase rounded-full border border-slate-200">🥈 Bạc</span>
                            @endif
                        </td>

                        {{-- Cột 4: Tổng điểm --}}
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-1">
                                <span class="text-xl font-black text-primary">{{ number_format($user->total_points ?? 0) }}</span>
                                <span class="material-symbols-outlined text-primary text-[18px]">toll</span>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center">
                            <span class="material-symbols-outlined text-5xl text-slate-300 mb-3">sentiment_dissatisfied</span>
                            <p class="text-slate-500 font-medium">Hệ thống chưa ghi nhận khách hàng nào có điểm.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

</div>
@endsection
