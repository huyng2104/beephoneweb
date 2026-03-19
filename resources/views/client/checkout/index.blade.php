@extends('client.layouts.app')

@section('title', 'Bee Phone - Thanh toán')

@section('content')
<main class="pt-10 pb-20 px-6 md:px-12 max-w-screen-2xl mx-auto min-h-screen">
    <div class="mb-8">
        <h1 class="text-3xl font-bold uppercase tracking-tight text-[#181611] dark:text-white">Thanh toán đơn hàng</h1>
    </div>

    @if(session('error'))
        <div class="bg-red-100 text-red-600 p-4 rounded-xl mb-6 font-bold flex items-center gap-2">
            <span class="material-symbols-outlined">error</span> {{ session('error') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="bg-red-100 text-red-600 p-4 rounded-xl mb-6 font-bold">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('client.checkout.process') }}" method="POST" class="flex flex-col lg:flex-row gap-8">
        @csrf
        
        <div class="flex-grow space-y-6">
            <div class="bg-white dark:bg-white/5 p-8 rounded-2xl shadow-sm border border-gray-100 dark:border-white/10">
                <h2 class="text-xl font-bold mb-6 text-[#181611] dark:text-white flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">location_on</span> Thông tin giao hàng
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="col-span-1 md:col-span-2">
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Họ và tên người nhận <span class="text-red-500">*</span></label>
                        <input type="text" name="customer_name" value="{{ old('customer_name', Auth::user()->name ?? '') }}" class="w-full bg-gray-50 dark:bg-black/20 border border-gray-200 dark:border-white/10 rounded-xl p-4 focus:ring-2 focus:ring-primary text-[#181611] dark:text-white" required placeholder="Nhập họ và tên">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Số điện thoại <span class="text-red-500">*</span></label>
                        <input type="text" name="customer_phone" value="{{ old('customer_phone', Auth::user()->phone ?? '') }}" class="w-full bg-gray-50 dark:bg-black/20 border border-gray-200 dark:border-white/10 rounded-xl p-4 focus:ring-2 focus:ring-primary text-[#181611] dark:text-white" required placeholder="Ví dụ: 0987654321">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Email (Không bắt buộc)</label>
                        <input type="email" name="customer_email" value="{{ old('customer_email', Auth::user()->email ?? '') }}" class="w-full bg-gray-50 dark:bg-black/20 border border-gray-200 dark:border-white/10 rounded-xl p-4 focus:ring-2 focus:ring-primary text-[#181611] dark:text-white" placeholder="Để nhận thông báo đơn hàng">
                    </div>

                    <div class="col-span-1 md:col-span-2">
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Địa chỉ giao hàng chi tiết <span class="text-red-500">*</span></label>
                        <input type="text" name="shipping_address" value="{{ old('shipping_address', Auth::user()->address ?? '') }}" class="w-full bg-gray-50 dark:bg-black/20 border border-gray-200 dark:border-white/10 rounded-xl p-4 focus:ring-2 focus:ring-primary text-[#181611] dark:text-white" required placeholder="Số nhà, tên đường, phường/xã, quận/huyện, tỉnh/thành phố">
                    </div>

                    <div class="col-span-1 md:col-span-2">
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Ghi chú cho cửa hàng</label>
                        <textarea name="note" rows="3" class="w-full bg-gray-50 dark:bg-black/20 border border-gray-200 dark:border-white/10 rounded-xl p-4 focus:ring-2 focus:ring-primary text-[#181611] dark:text-white" placeholder="Ghi chú thêm về thời gian giao hàng, chỉ đường..."></textarea>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-white/5 p-8 rounded-2xl shadow-sm border border-gray-100 dark:border-white/10">
                <h2 class="text-xl font-bold mb-6 text-[#181611] dark:text-white flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">payments</span> Phương thức thanh toán
                </h2>
                <div class="border-2 border-primary bg-primary/5 rounded-xl p-4 flex items-center gap-4 cursor-pointer">
                    <input type="radio" checked class="w-5 h-5 text-primary focus:ring-primary">
                    <div>
                        <p class="font-bold text-[#181611] dark:text-white">Thanh toán khi nhận hàng (COD)</p>
                        <p class="text-sm text-gray-500">Khách hàng thanh toán bằng tiền mặt cho shipper khi hàng được giao đến.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="lg:w-96 flex-shrink-0">
            <div class="bg-white dark:bg-white/5 p-8 rounded-2xl shadow-sm sticky top-24 border border-gray-100 dark:border-white/10">
                <h2 class="text-xl font-bold mb-6 tracking-tight text-[#181611] dark:text-white">Đơn hàng của bạn</h2>
                
                <div class="space-y-4 mb-6 max-h-[300px] overflow-y-auto pr-2 custom-scrollbar">
                    @foreach($cart->items as $item)
                        @php
                            $price = $item->product->sale_price > 0 ? $item->product->sale_price : $item->product->price;
                            $variantName = '';
                            if ($item->variant) {
                                $price = $item->variant->sale_price > 0 ? $item->variant->sale_price : $item->variant->price;
                                $variantName = $item->variant->attributeValues->pluck('value')->implode(' - ');
                            }
                        @endphp
                        <div class="flex justify-between gap-4 border-b border-gray-100 dark:border-white/5 pb-4 last:border-0 last:pb-0">
                            <div>
                                <p class="font-bold text-sm text-[#181611] dark:text-white line-clamp-2">{{ $item->product->name }}</p>
                                @if($variantName) <p class="text-[11px] text-gray-500 mt-1 uppercase">{{ $variantName }}</p> @endif
                                <p class="text-xs text-gray-500 mt-1">SL: <span class="font-bold text-[#181611] dark:text-white">{{ $item->quantity }}</span></p>
                            </div>
                            <span class="font-bold text-red-500 shrink-0">{{ number_format($price * $item->quantity, 0, ',', '.') }}₫</span>
                        </div>
                    @endforeach
                </div>

                <div class="space-y-4 mb-8 border-t border-gray-100 dark:border-white/10 pt-6">
                    <div class="flex justify-between text-gray-500 dark:text-gray-400">
                        <span>Tạm tính</span>
                        <span class="font-medium text-[#181611] dark:text-white">{{ number_format($totalPrice, 0, ',', '.') }}₫</span>
                    </div>
                    <div class="flex justify-between text-gray-500 dark:text-gray-400">
                        <span>Phí vận chuyển</span>
                        <span class="font-medium text-[#181611] dark:text-white">Miễn phí</span>
                    </div>
                </div>

                <div class="border-t border-gray-100 dark:border-white/10 pt-6 mb-8">
                    <div class="flex justify-between items-end">
                        <span class="text-lg font-bold text-[#181611] dark:text-white">Tổng cộng</span>
                        <span class="text-3xl font-black text-red-500">{{ number_format($totalPrice, 0, ',', '.') }}₫</span>
                    </div>
                </div>

                <button type="submit" class="w-full bg-primary text-black font-bold py-4 rounded-xl shadow-lg hover:scale-[1.02] transition-transform flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined">check_circle</span> HOÀN TẤT ĐẶT HÀNG
                </button>
            </div>
        </div>
    </form>
</main>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #f4c025; border-radius: 10px; }
</style>
@endsection