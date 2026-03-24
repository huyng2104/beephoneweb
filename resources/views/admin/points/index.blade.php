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
        <div class="bg-white dark:bg-slate-900 p-5 rounded-xl border border-slate-200 dark:border-slate-800 flex flex-col gap-2">
            <div class="flex justify-between items-start">
                <p class="text-slate-500 text-sm font-medium">Tổng điểm phát hành</p>
                <span class="material-symbols-outlined text-primary">toll</span>
            </div>
            <p class="text-2xl font-extrabold text-slate-900 dark:text-white">1,250,000</p>
            <div class="flex items-center gap-1 text-green-600 text-xs font-bold mt-1">
                <span class="material-symbols-outlined text-[16px]">trending_up</span>
                <span>+12.5% so với tháng trước</span>
            </div>
        </div>
        <div class="bg-white dark:bg-slate-900 p-5 rounded-xl border border-slate-200 dark:border-slate-800 flex flex-col gap-2">
            <div class="flex justify-between items-start">
                <p class="text-slate-500 text-sm font-medium">Tổng điểm đã đổi</p>
                <span class="material-symbols-outlined text-orange-500">redeem</span>
            </div>
            <p class="text-2xl font-extrabold text-slate-900 dark:text-white">840,000</p>
            <div class="flex items-center gap-1 text-green-600 text-xs font-bold mt-1">
                <span class="material-symbols-outlined text-[16px]">trending_up</span>
                <span>+5.2% hiệu suất đổi quà</span>
            </div>
        </div>
        <div class="bg-white dark:bg-slate-900 p-5 rounded-xl border border-slate-200 dark:border-slate-800 flex flex-col gap-2">
            <div class="flex justify-between items-start">
                <p class="text-slate-500 text-sm font-medium">Khách hàng tích cực</p>
                <span class="material-symbols-outlined text-blue-500">person_celebrate</span>
            </div>
            <p class="text-2xl font-extrabold text-slate-900 dark:text-white">12,450</p>
            <div class="flex items-center gap-1 text-green-600 text-xs font-bold mt-1">
                <span class="material-symbols-outlined text-[16px]">trending_up</span>
                <span>+812 người dùng mới</span>
            </div>
        </div>
        <div class="bg-white dark:bg-slate-900 p-5 rounded-xl border border-slate-200 dark:border-slate-800 flex flex-col gap-2">
            <div class="flex justify-between items-start">
                <p class="text-slate-500 text-sm font-medium">Tỷ lệ quy đổi trung bình</p>
                <span class="material-symbols-outlined text-purple-500">currency_exchange</span>
            </div>
            <p class="text-2xl font-extrabold text-slate-900 dark:text-white">1.000đ/điểm</p>
            <div class="flex items-center gap-1 text-slate-500 text-xs font-bold mt-1">
                <span class="material-symbols-outlined text-[16px]">info</span>
                <span>Cố định theo chính sách</span>
            </div>
        </div>
    </section>

    <section>
        <h3 class="text-slate-900 dark:text-white text-xl font-extrabold mb-5 flex items-center gap-2">
            <span class="material-symbols-outlined text-primary">tune</span> Cấu hình tỷ lệ và quy đổi
        </h3>
        
        <form action="{{ route('admin.points.settings.update') }}" method="POST" class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            @csrf
            
            <div class="bg-white dark:bg-slate-900 p-6 rounded-xl border border-slate-200 dark:border-slate-800">
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

            <div class="bg-white dark:bg-slate-900 p-6 rounded-xl border border-slate-200 dark:border-slate-800 flex flex-col justify-between">
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
            <button class="bg-black dark:bg-white dark:text-black text-white px-4 py-2 rounded-lg text-sm font-bold flex items-center gap-2 hover:opacity-90 transition-all">
                <span class="material-symbols-outlined text-[20px]">add</span> Thêm phần quà mới
            </button>
        </div>
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 overflow-hidden">
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
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="size-12 rounded-lg bg-yellow-100 flex items-center justify-center border border-primary/20">
                                    <span class="material-symbols-outlined text-primary text-3xl">confirmation_number</span>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-slate-900 dark:text-white">Voucher Giảm 200k</p>
                                    <p class="text-xs text-slate-500">Mã giảm giá đơn hàng</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-1">
                                <span class="text-sm font-bold text-slate-900 dark:text-white">2,000</span>
                                <span class="material-symbols-outlined text-primary text-[18px]">toll</span>
                            </div>
                        </td>
                        <td class="px-6 py-4"><p class="text-sm text-slate-900 dark:text-slate-300">Vô hạn</p></td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-2">
                                <button class="p-2 text-slate-500 hover:text-primary transition-colors"><span class="material-symbols-outlined">edit</span></button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>

</div>
@endsection