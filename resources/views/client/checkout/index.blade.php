@extends('client.layouts.app')

@section('title', 'Bee Phone - Thanh toán')

@section('content')
@php
    // CHUYỂN LOGIC TÍNH TIỀN LÊN ĐẦU ĐỂ DÙNG CHO PHẦN CHẶN COD
    $discount = session()->has('voucher') ? session('voucher')['discount_amount'] : 0;
    $finalTotal = $totalPrice - $discount;
    if($finalTotal < 0) $finalTotal = 0;
    
    // MỐC CHẶN SHIP COD (Ví dụ: 30.000.000đ)
    $maxCodAmount = 30000000; 
    $isCodAllowed = $finalTotal <= $maxCodAmount;
@endphp

<main class="pt-10 pb-20 px-6 md:px-12 max-w-screen-2xl mx-auto min-h-screen">
    <div class="mb-8">
        <h1 class="text-3xl font-bold uppercase tracking-tight text-[#181611] dark:text-white">Thanh toán đơn hàng</h1>
    </div>

    @if(session('error'))
        <div class="bg-red-100 text-red-600 p-4 rounded-xl mb-6 font-bold flex items-center gap-2">
            <span class="material-symbols-outlined">error</span> {{ session('error') }}
        </div>
    @endif
    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-4 rounded-xl mb-6 font-bold flex items-center gap-2">
            <span class="material-symbols-outlined">check_circle</span> {{ session('success') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="bg-red-100 text-red-600 p-4 rounded-xl mb-6 font-bold">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    {{-- FORM ĐẶT HÀNG CHÍNH --}}
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
                        <input type="text" id="customer_name" name="customer_name" value="{{ old('customer_name', Auth::user()->name ?? '') }}" class="w-full bg-gray-50 dark:bg-black/20 border border-gray-200 dark:border-white/10 rounded-xl p-4 focus:ring-2 focus:ring-primary text-[#181611] dark:text-white" placeholder="Nhập họ và tên">
                        <p id="err_customer_name" class="text-red-500 text-sm mt-1 hidden">Vui lòng nhập họ và tên</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Số điện thoại <span class="text-red-500">*</span></label>
                        <input type="text" id="customer_phone" name="customer_phone" value="{{ old('customer_phone', Auth::user()->phone ?? '') }}" class="w-full bg-gray-50 dark:bg-black/20 border border-gray-200 dark:border-white/10 rounded-xl p-4 focus:ring-2 focus:ring-primary text-[#181611] dark:text-white" placeholder="Ví dụ: 0987654321">
                        <p id="err_customer_phone" class="text-red-500 text-sm mt-1 hidden">Vui lòng nhập số điện thoại</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Email (Không bắt buộc)</label>
                        <input type="email" name="customer_email" value="{{ old('customer_email', Auth::user()->email ?? '') }}" class="w-full bg-gray-50 dark:bg-black/20 border border-gray-200 dark:border-white/10 rounded-xl p-4 focus:ring-2 focus:ring-primary text-[#181611] dark:text-white" placeholder="Để nhận thông báo đơn hàng">
                    </div>

                    {{-- ĐỊA CHỈ GIAO HÀNG (4 ô + API) --}}
                    <div class="col-span-1 md:col-span-2">
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-3">Địa chỉ giao hàng <span class="text-red-500">*</span></label>

                        {{-- NÚT DÙNG ĐỊA CHỈ MẶC ĐỊNH (chỉ hiện khi user đã có địa chỉ) --}}
                        @if(Auth::check() && Auth::user()->address)
                        <div id="default-address-bar" class="flex items-center gap-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-xl px-4 py-3 mb-4">
                            <span class="material-symbols-outlined text-blue-500">home</span>
                            <div class="flex-grow min-w-0">
                                <p class="text-xs font-bold text-blue-600 dark:text-blue-400 uppercase tracking-wide mb-0.5">Địa chỉ mặc định</p>
                                <p id="default-address-text" class="text-sm text-gray-700 dark:text-gray-200 truncate font-medium">{{ Auth::user()->address }}</p>
                            </div>
                            <button type="button" id="use-default-btn" onclick="useDefaultAddress()" class="shrink-0 bg-blue-500 hover:bg-blue-600 text-white text-xs font-bold px-3 py-1.5 rounded-lg transition-colors">
                                Dùng ngay
                            </button>
                        </div>

                        <div id="address-or-divider" class="flex items-center gap-3 mb-4">
                            <div class="flex-1 h-px bg-gray-200 dark:bg-white/10"></div>
                            <span class="text-xs text-gray-400 font-medium">hoặc nhập địa chỉ khác</span>
                            <div class="flex-1 h-px bg-gray-200 dark:bg-white/10"></div>
                        </div>
                        @endif

                        {{-- INPUT ẨN ĐỂ SUBMIT --}}
                        <input type="hidden" name="shipping_address" id="shipping_address_hidden" value="{{ old('shipping_address') }}">

                        {{-- 4 Ô NHẬP ĐỊA CHỈ --}}
                        <div id="address-fields" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            

                            {{-- Ô 2: Tỉnh/Thành phố --}}
                            <div class="md:col-span-2 md:col-span-1">
                                <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1.5">Tỉnh / Thành phố <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <select id="addr_province" onchange="loadDistricts(this.value)" class="w-full bg-gray-50 dark:bg-black/20 border border-gray-200 dark:border-white/10 rounded-xl p-3.5 pr-10 focus:ring-2 focus:ring-primary text-[#181611] dark:text-white text-sm appearance-none cursor-pointer transition-all">
                                        <option value="">-- Chọn Tỉnh/Thành phố --</option>
                                    </select>
                                    <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-lg pointer-events-none">expand_more</span>
                                </div>
                            </div>

                            {{-- Ô 3: Quận/Huyện --}}
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1.5">Quận / Huyện <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <select id="addr_district" onchange="loadWards(this.value)" disabled class="w-full bg-gray-50 dark:bg-black/20 border border-gray-200 dark:border-white/10 rounded-xl p-3.5 pr-10 focus:ring-2 focus:ring-primary text-[#181611] dark:text-white text-sm appearance-none cursor-pointer transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                                        <option value="">-- Chọn Quận/Huyện --</option>
                                    </select>
                                    <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-lg pointer-events-none">expand_more</span>
                                </div>
                            </div>

                            {{-- Ô 4: Phường/Xã --}}
                            <div class="mb-4" >
                                <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1.5">Phường / Xã <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <select id="addr_ward" onchange="buildFullAddress()" disabled class="w-full bg-gray-50 dark:bg-black/20 border border-gray-200 dark:border-white/10 rounded-xl p-3.5 pr-10 focus:ring-2 focus:ring-primary text-[#181611] dark:text-white text-sm appearance-none cursor-pointer transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                                        <option value="">-- Chọn Phường/Xã --</option>
                                    </select>
                                    <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-lg pointer-events-none">expand_more</span>
                                </div>
                            </div> 
                        </div>
                        {{-- Ô 1: Số nhà, tên đường --}}
                            <div class="md:col-span-2">
                                <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1.5">Số nhà, tên đường <span class="text-red-500">*</span></label>
                                <input type="text" id="addr_street" placeholder="Ví dụ: 123 Nguyễn Trãi" class="w-full bg-gray-50 dark:bg-black/20 border border-gray-200 dark:border-white/10 rounded-xl p-3.5 focus:ring-2 focus:ring-primary text-[#181611] dark:text-white text-sm transition-all" oninput="buildFullAddress()">
                            </div>
                        {{-- PREVIEW ĐỊA CHỈ ĐÃ GHÉP --}}
                        <div id="address-preview" class="hidden mt-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 rounded-xl px-4 py-2.5">
                            <p class="text-xs font-bold text-green-600 dark:text-green-400 mb-0.5 flex items-center gap-1">
                                <span class="material-symbols-outlined text-[14px]">check_circle</span> Địa chỉ giao hàng
                            </p>
                            <p id="address-preview-text" class="text-sm text-gray-700 dark:text-gray-200 font-medium"></p>
                        </div>
                        <p id="err_shipping_address" class="text-red-500 text-sm mt-2 hidden">Vui lòng cung cấp đầy đủ thông tin địa chỉ giao hàng</p>
                    </div>

                    <div class="col-span-1 md:col-span-2">
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Ghi chú cho cửa hàng</label>
                        <textarea name="note" rows="3" maxlength="1000" class="w-full bg-gray-50 dark:bg-black/20 border border-gray-200 dark:border-white/10 rounded-xl p-4 focus:ring-2 focus:ring-primary text-[#181611] dark:text-white" placeholder="Ghi chú thêm về thời gian giao hàng, chỉ đường... (tối đa 1000 ký tự)"></textarea>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-white/5 p-8 rounded-2xl shadow-sm border border-gray-100 dark:border-white/10">
                <h2 class="text-xl font-bold mb-6 text-[#181611] dark:text-white flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">payments</span> Phương thức thanh toán
                </h2>
                
                <div class="space-y-4">
                    {{-- LOGIC KHÓA COD NẾU ĐƠN QUÁ LỚN --}}
                    @if($isCodAllowed)
                        <label class="border-2 border-gray-200 hover:border-primary bg-white dark:bg-black/20 rounded-xl p-4 flex items-center gap-4 cursor-pointer transition-colors has-[:checked]:border-primary has-[:checked]:bg-primary/5">
                            <input type="radio" name="payment_method" value="cod" checked class="w-5 h-5 text-primary focus:ring-primary">
                            <div>
                                <p class="font-bold text-[#181611] dark:text-white">Thanh toán khi nhận hàng (COD)</p>
                                <p class="text-sm text-gray-500">Thanh toán bằng tiền mặt cho shipper khi nhận hàng.</p>
                            </div>
                        </label>
                    @else
                        <label class="border-2 border-red-200 bg-red-50/50 dark:bg-red-900/10 rounded-xl p-4 flex items-center gap-4 cursor-not-allowed opacity-75">
                            <input type="radio" disabled class="w-5 h-5 text-gray-400 cursor-not-allowed">
                            <div>
                                <p class="font-bold text-gray-500 line-through">Thanh toán khi nhận hàng (COD)</p>
                                <p class="text-sm font-bold text-red-500 flex items-center gap-1 mt-1">
                                    <span class="material-symbols-outlined text-[16px]">block</span> 
                                    COD không hỗ trợ cho đơn hàng trên {{ number_format($maxCodAmount, 0, ',', '.') }}₫. Vui lòng chọn thanh toán trước.
                                </p>
                            </div>
                        </label>
                    @endif

                    <label class="border-2 border-gray-200 hover:border-primary bg-white dark:bg-black/20 rounded-xl p-4 flex items-center gap-4 cursor-pointer transition-colors has-[:checked]:border-primary has-[:checked]:bg-primary/5">
                        <input type="radio" name="payment_method" value="vnpay" {{ !$isCodAllowed ? 'checked' : '' }} class="w-5 h-5 text-primary focus:ring-primary">
                        <img src="data:image/png;base64,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" alt="VNPAY" class="w-10 h-10 object-contain bg-white rounded p-1">
                        <div>
                            <p class="font-bold text-[#181611] dark:text-white">Thanh toán qua VNPAY</p>
                            <p class="text-sm text-gray-500">Hỗ trợ thẻ ATM, Internet Banking, mã QR...</p>
                        </div>
                    </label>

                    @php
                        $wallet = Auth::check() ? \App\Models\Wallet::where('user_id', Auth::id())->first() : null;
                        $balance = $wallet ? $wallet->balance : 0;
                        
                        // Tự động mở khóa nếu đã qua thời gian khóa 15p
                        if ($wallet && $wallet->locked_until && $wallet->locked_until <= now()) {
                            $wallet->update(['status' => 'active', 'locked_until' => null, 'pin_attempts' => 0]);
                        }
                        
                        $isWalletLocked = $wallet && ($wallet->status !== 'active' || ($wallet->locked_until && $wallet->locked_until > now()));
                    @endphp

                    @if($isWalletLocked)
                        <label class="border-2 border-red-200 bg-red-50/50 dark:bg-red-900/10 rounded-xl p-4 flex items-center gap-4 cursor-not-allowed opacity-75">
                            <input type="radio" disabled class="w-5 h-5 text-gray-400 cursor-not-allowed">
                            <span class="material-symbols-outlined text-4xl text-gray-400">account_balance_wallet</span>
                            <div>
                                <p class="font-bold text-gray-500 line-through">Ví Bee Pay</p>
                                <p class="text-sm font-bold text-red-500 mt-1 flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[16px]">lock</span>
                                    Ví Bee Pay của bạn đang bị khóa!
                                </p>
                            </div>
                        </label>
                    @else
                        <label class="border-2 border-gray-200 hover:border-primary bg-white dark:bg-black/20 rounded-xl p-4 flex items-center gap-4 cursor-pointer transition-colors has-[:checked]:border-primary has-[:checked]:bg-primary/5 flex-wrap">
                            <div class="flex items-center gap-4 w-full">
                                <input type="radio" name="payment_method" value="wallet" id="payment_wallet" class="w-5 h-5 text-primary focus:ring-primary payment-radio">
                                <span class="material-symbols-outlined text-4xl text-primary">account_balance_wallet</span>
                                <div>
                                    <p class="font-bold text-[#181611] dark:text-white">Ví Bee Pay</p>
                                    <p class="text-sm text-gray-500">Thanh toán siêu tốc không cần qua ngân hàng.</p>
                                    @if(Auth::check())
                                        <p class="text-xs font-bold text-green-600 mt-1">Số dư: {{ number_format($balance, 0, ',', '.') }}₫</p>
                                    @else
                                        <p class="text-xs font-bold text-red-500 mt-1">Vui lòng đăng nhập để sử dụng</p>
                                    @endif
                                </div>
                            </div>
                            
                            {{-- Field nhập mã PIN ví (chỉ gồm 6 số), ẩn mặc định --}}
                            @if(Auth::check())
                            <div id="wallet_password_section" class="w-full mt-2 hidden border-t border-gray-100 dark:border-white/10 pt-3 pl-[3.25rem]">
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Mã PIN giao dịch <span class="text-red-500">*</span></label>
                                <input type="password" id="wallet_pin" name="wallet_pin" placeholder="Nhập mã PIN 6 số của bạn" class="w-50 bg-gray-50 dark:bg-black/20 border border-gray-200 dark:border-white/10 rounded-xl p-3 focus:ring-2 focus:ring-primary text-sm text-[#181611] dark:text-white text-center" maxlength="6" inputmode="numeric" pattern="[0-9]{6}" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 6);">
                                <p id="err_wallet_pin" class="text-red-500 text-sm mt-1 hidden">Vui lòng nhập mã PIN hợp lệ gồm 6 chữ số</p>
                            </div>
                            @endif
                        </label>
                    @endif
                </div>
            </div>
        </div>

        <div class="lg:w-96 flex-shrink-0">
            <div class="bg-white dark:bg-white/5 p-8 rounded-2xl shadow-sm sticky top-24 border border-gray-100 dark:border-white/10">
                <h2 class="text-xl font-bold mb-6 tracking-tight text-[#181611] dark:text-white">Đơn hàng của bạn</h2>
                
                <div class="space-y-4 mb-6 max-h-[300px] overflow-y-auto pr-2 custom-scrollbar">
                    @php $hasVoucherInOrder = false; @endphp
                    @foreach($cart->items as $item)
                        @php
                            $price = $item->product->sale_price > 0 ? $item->product->sale_price : $item->product->price;
                            $variantName = '';
                            if ($item->variant) {
                                $price = $item->variant->sale_price > 0 ? $item->variant->sale_price : $item->variant->price;
                                $variantName = $item->variant->attributeValues->pluck('value')->implode(' - ');
                            }
                            
                            // Xác định sản phẩm voucher (dựa trên tên sản phẩm)
                            $isVoucherProd = Str::contains(Str::lower($item->product->name), ['voucher', 'thẻ quà tặng']);
                            if($isVoucherProd) $hasVoucherInOrder = true;

                            // Lấy ảnh thumbnail
                            $img = ($item->variant && $item->variant->thumbnail) ? $item->variant->thumbnail : $item->product->thumbnail;
                            $imgUrl = Str::startsWith($img, ['http://', 'https://']) ? $img : asset('storage/' . $img);
                        @endphp
                        <div class="flex justify-between gap-4 border-b border-gray-100 dark:border-white/5 pb-4 last:border-0 last:pb-0">
                            <div class="flex gap-3">
                                <div class="w-12 h-12 bg-gray-50 dark:bg-black/20 rounded-lg overflow-hidden flex-shrink-0 p-1">
                                    <img src="{{ $imgUrl }}" class="w-full h-full object-contain mix-blend-multiply dark:mix-blend-normal" alt="{{ $item->product->name }}">
                                </div>
                                <div class="flex-grow min-w-0">
                                    <p class="font-bold text-sm text-[#181611] dark:text-white line-clamp-2">{{ $item->product->name }}</p>
                                    @if($variantName) <p class="text-[11px] text-gray-500 mt-1 uppercase">{{ $variantName }}</p> @endif
                                    <p class="text-xs text-gray-500 mt-1">SL: <span class="font-bold text-[#181611] dark:text-white">{{ $item->quantity }}</span></p>
                                </div>
                            </div>
                            <span class="font-bold text-red-500 shrink-0">{{ number_format($price * $item->quantity, 0, ',', '.') }}₫</span>
                        </div>
                    @endforeach
                </div>

                {{-- Ô CHỌN/NHẬP MÃ GIẢM GIÁ (UX CHUẨN SHOPEE) --}}
                <div class="mb-6 border-t border-gray-100 pt-6">
                    <div class="flex justify-between items-center mb-2">
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300">Mã ưu đãi / Kho Voucher</label>
                        <a href="{{ route('vouchers.index') }}" target="_blank" class="text-xs text-blue-600 font-bold hover:underline flex items-center gap-1">
                            <span class="material-symbols-outlined text-[14px]">loyalty</span> Săn thêm mã
                        </a>
                    </div>

                    @php
                        $savedVouchers = collect();
                        if(Auth::check()){
                            $savedVouchers = Auth::user()->userVouchers()
                                ->where('status', 1)
                                ->wherePivotNull('order_id') // Laravel's helper or wherePivot('order_id', null)
                                ->where(function($q) {
                                    $q->whereNull('end_date')->orWhere('end_date', '>=', \Carbon\Carbon::now());
                                })
                                ->get();
                        }
                    @endphp

                    <div class="flex gap-2">
                        @if(Auth::check())
                            @if($savedVouchers->count() > 0)
                                {{-- HIỂN THỊ DROPDOWN NẾU CÓ MÃ TRONG VÍ --}}
                                <select id="display_voucher_code" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-primary focus:border-primary text-gray-900 cursor-pointer shadow-sm">
                                    <option value="">-- Bấm để chọn mã giảm giá trong ví --</option>
                                    @foreach($savedVouchers as $v)
                                        @php
                                            $isEligible = $totalPrice >= $v->min_order_value;
                                            $discountText = $v->discount_type == 'percent' ? $v->discount_value.'%' : number_format($v->discount_value, 0, ',', '.').'₫';
                                            $minOrderText = $v->min_order_value > 0 ? ' (Đơn từ '.number_format($v->min_order_value, 0, ',', '.').'₫)' : '';
                                            $isSelected = session()->has('voucher') && session('voucher')['code'] == $v->code;
                                        @endphp
                                        <option value="{{ $v->code }}" {{ !$isEligible ? 'disabled' : '' }} {{ $isSelected ? 'selected' : '' }}>
                                            Mã {{ $v->code }}: Giảm {{ $discountText }}{{ $minOrderText }} {{ !$isEligible ? ' - ❌ CHƯA ĐỦ ĐIỀU KIỆN' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                                <button type="button" onclick="submitVoucher()" class="bg-[#181611] text-white px-5 py-2.5 rounded-xl text-sm font-bold hover:bg-gray-800 transition-colors whitespace-nowrap shadow-md">
                                    Áp dụng
                                </button>
                            @else
                                <div class="w-full bg-gray-100 border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-500 italic shadow-sm flex items-center">
                                    <span class="material-symbols-outlined text-sm mr-1">info</span> Ví Voucher của bạn đang trống
                                </div>
                            @endif
                        @else
                            <div class="w-full bg-gray-100 border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-500 italic shadow-sm flex items-center justify-between">
                                <span class="flex items-center"><span class="material-symbols-outlined text-sm mr-1">lock</span> Đăng nhập để dùng Voucher</span>
                                <a href="{{ route('login') }}" class="text-primary font-bold hover:underline">Đăng nhập</a>
                            </div>
                        @endif
                    </div>
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
                    {{-- Hiển thị dòng giảm giá + Nút xóa mã (X) nếu có --}}
                    @if($discount > 0)
                        <div class="flex justify-between items-center mb-4 bg-green-50 p-3 rounded-lg border border-green-100">
                            <div class="flex items-center gap-2">
                                <span class="text-green-700 font-bold text-sm flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[16px]">loyalty</span> Mã: {{ session('voucher')['code'] }}
                                </span>
                                <button type="button" onclick="removeVoucherAjax()" class="text-red-400 hover:text-red-600 bg-white rounded-full p-0.5 shadow-sm border border-red-100 flex items-center justify-center transition-colors" title="Bỏ dùng mã này">
                                    <span class="material-symbols-outlined text-[14px]">close</span>
                                </button>
                            </div>
                            <span class="font-black text-green-600">-{{ number_format($discount, 0, ',', '.') }}₫</span>
                        </div>
                    @endif

                    <div class="flex justify-between items-end">
                        <span class="text-lg font-bold text-[#181611] dark:text-white">Tổng cộng</span>
                        <div class="text-right">
                            <span class="text-3xl font-black text-red-500">{{ number_format($finalTotal, 0, ',', '.') }}₫</span>
                            <p class="text-[10px] text-gray-400 uppercase font-bold mt-1">Đã bao gồm VAT</p>
                        </div>
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

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Xử lý submit từ Dropdown hoặc Ô nhập tay mặc định
    function submitVoucher() {
        let code = document.getElementById('display_voucher_code').value.trim();
        if(!code) {
            @if(session()->has('voucher'))
                removeVoucherAjax();
            @else
                Swal.fire({ icon: 'warning', title: 'Ê khoan!', text: 'Bro chưa chọn mã giảm giá kìa!' });
            @endif
            return;
        }
        sendVoucherAjax(code);
    }

    // Xử lý submit từ Ô nhập tay phụ (Manual)
    function submitManualVoucher() {
        let code = document.getElementById('manual_voucher_code').value.trim();
        if(!code) {
            Swal.fire({ icon: 'warning', title: 'Ê khoan!', text: 'Bro chưa nhập mã giảm giá!' });
            return;
        }
        sendVoucherAjax(code);
    }

    // GỌI AJAX HỦY MÃ (Nhớ khai báo route ở CheckoutController nhé)
    function removeVoucherAjax() {
        fetch('{{ route('client.checkout.remove_voucher') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            Swal.fire({
                icon: 'info',
                title: 'Đã bỏ chọn!',
                text: 'Đã hủy áp dụng mã giảm giá.',
                timer: 1500,
                showConfirmButton: false
            }).then(() => {
                window.location.reload(); // F5 lại để tiền trở về như cũ
            });
        });
    }

    // Hàm gọi AJAX ngầm xuống Backend xử lý Voucher
    function sendVoucherAjax(code) {
        fetch('{{ route('client.cart.apply_voucher') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ 
                code: code,           
                voucher_code: code    
            })
        })
        .then(response => response.json())
        .then(data => {
            if(data.success || data.status === 'success' || data.status === 200) {
                // Áp mã thành công
                Swal.fire({
                    icon: 'success',
                    title: 'Ngon lành!',
                    text: data.message || 'Đã áp dụng mã giảm giá thành công!',
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    window.location.reload(); // Reload lại trang để update tiền
                });
            } else {
                // Áp mã thất bại
                Swal.fire({
                    icon: 'error',
                    title: 'Rất tiếc!',
                    text: data.message || 'Mã giảm giá không hợp lệ hoặc đã hết hạn!'
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Lỗi mạng!',
                text: 'Hệ thống đang bận, không thể áp dụng mã lúc này.'
            });
        });
    }

    // ==========================================
    // ĐỊA CHỈ - API PROVINCES
    // ==========================================
    const API_BASE = 'https://provinces.open-api.vn/api';
    let usingDefault = false;

    // Load danh sách tỉnh/thành khi trang load
    async function loadProvinces() {
        try {
            const res = await fetch(`${API_BASE}/p/`);
            const data = await res.json();
            const select = document.getElementById('addr_province');
            data.forEach(p => {
                const opt = document.createElement('option');
                opt.value = p.code;
                opt.textContent = p.name;
                select.appendChild(opt);
            });
        } catch(e) {
            console.error('Không load được danh sách tỉnh:', e);
        }
    }

    // Load quận/huyện theo mã tỉnh
    async function loadDistricts(provinceCode) {
        const districtSelect = document.getElementById('addr_district');
        const wardSelect = document.getElementById('addr_ward');

        // Reset dropdowns con
        districtSelect.innerHTML = '<option value="">-- Chọn Quận/Huyện --</option>';
        wardSelect.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';
        wardSelect.disabled = true;

        if (!provinceCode) {
            districtSelect.disabled = true;
            buildFullAddress();
            return;
        }

        districtSelect.disabled = true;
        districtSelect.innerHTML = '<option value="">Đang tải...</option>';

        try {
            const res = await fetch(`${API_BASE}/p/${provinceCode}?depth=2`);
            const data = await res.json();
            districtSelect.innerHTML = '<option value="">-- Chọn Quận/Huyện --</option>';
            data.districts.forEach(d => {
                const opt = document.createElement('option');
                opt.value = d.code;
                opt.textContent = d.name;
                districtSelect.appendChild(opt);
            });
            districtSelect.disabled = false;
        } catch(e) {
            districtSelect.innerHTML = '<option value="">-- Lỗi tải dữ liệu --</option>';
            console.error(e);
        }
        buildFullAddress();
    }

    // Load phường/xã theo mã quận/huyện
    async function loadWards(districtCode) {
        const wardSelect = document.getElementById('addr_ward');
        wardSelect.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';
        wardSelect.disabled = true;

        if (!districtCode) { buildFullAddress(); return; }

        wardSelect.innerHTML = '<option value="">Đang tải...</option>';

        try {
            const res = await fetch(`${API_BASE}/d/${districtCode}?depth=2`);
            const data = await res.json();
            wardSelect.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';
            data.wards.forEach(w => {
                const opt = document.createElement('option');
                opt.value = w.code;
                opt.textContent = w.name;
                wardSelect.appendChild(opt);
            });
            wardSelect.disabled = false;
        } catch(e) {
            wardSelect.innerHTML = '<option value="">-- Lỗi tải dữ liệu --</option>';
            console.error(e);
        }
        buildFullAddress();
    }

    // Ghép địa chỉ đầy đủ từ 4 ô
    function buildFullAddress() {
        if (usingDefault) return; // Đang dùng địa chỉ mặc định, không ghi đè

        const street   = document.getElementById('addr_street')?.value.trim() || '';
        const provSel  = document.getElementById('addr_province');
        const distSel  = document.getElementById('addr_district');
        const wardSel  = document.getElementById('addr_ward');

        const province = provSel?.options[provSel.selectedIndex]?.text || '';
        const district = distSel?.options[distSel.selectedIndex]?.text || '';
        const ward     = wardSel?.options[wardSel.selectedIndex]?.text || '';

        const parts = [street, ward, district, province].filter(p => p && !p.startsWith('--') && !p.includes('Đang tải'));
        const full = parts.join(', ');

        document.getElementById('shipping_address_hidden').value = full;

        const preview = document.getElementById('address-preview');
        const previewText = document.getElementById('address-preview-text');
        if (full) {
            previewText.textContent = full;
            preview.classList.remove('hidden');
        } else {
            preview.classList.add('hidden');
        }
    }

    // Nút "Dùng ngay" - dùng địa chỉ mặc định từ profile
    function useDefaultAddress() {
        const defaultAddr = document.getElementById('default-address-text')?.textContent.trim();
        if (!defaultAddr) return;

        usingDefault = true;
        document.getElementById('shipping_address_hidden').value = defaultAddr;

        // Ẩn form địa chỉ + divider, hiện preview
        const fields = document.getElementById('address-fields');
        const divider = document.getElementById('address-or-divider');
        if (fields) fields.classList.add('hidden');
        if (divider) divider.classList.add('hidden');

        // Hiện preview
        const preview = document.getElementById('address-preview');
        document.getElementById('address-preview-text').textContent = defaultAddr;
        preview.classList.remove('hidden');

        // Đổi nút thành "Thay đổi"
        const btn = document.getElementById('use-default-btn');
        if (btn) {
            btn.textContent = 'Thay đổi';
            btn.onclick = cancelDefaultAddress;
            btn.classList.replace('bg-blue-500', 'bg-gray-400');
            btn.classList.replace('hover:bg-blue-600', 'hover:bg-gray-500');
        }
    }

    // Huỷ dùng địa chỉ mặc định → hiện lại form chọn
    function cancelDefaultAddress() {
        usingDefault = false;
        document.getElementById('shipping_address_hidden').value = '';

        const fields = document.getElementById('address-fields');
        const divider = document.getElementById('address-or-divider');
        if (fields) fields.classList.remove('hidden');
        if (divider) divider.classList.remove('hidden');

        document.getElementById('address-preview').classList.add('hidden');

        const btn = document.getElementById('use-default-btn');
        if (btn) {
            btn.textContent = 'Dùng ngay';
            btn.onclick = useDefaultAddress;
            btn.classList.replace('bg-gray-400', 'bg-blue-500');
            btn.classList.replace('hover:bg-gray-500', 'hover:bg-blue-600');
        }

        buildFullAddress();
    }

    // Validate địa chỉ trước khi submit form
    document.querySelector('form[action="{{ route('client.checkout.process') }}"]')
        ?.addEventListener('submit', function(e) {
            let isValid = true;

            const nameInput = document.getElementById('customer_name');
            const errName = document.getElementById('err_customer_name');
            if (!nameInput.value.trim()) {
                errName.classList.remove('hidden');
                isValid = false;
            } else {
                errName.classList.add('hidden');
            }

            const phoneInput = document.getElementById('customer_phone');
            const errPhone = document.getElementById('err_customer_phone');
            if (!phoneInput.value.trim()) {
                errPhone.classList.remove('hidden');
                isValid = false;
            } else {
                errPhone.classList.add('hidden');
            }

            const addr = document.getElementById('shipping_address_hidden').value.trim();
            const errAddress = document.getElementById('err_shipping_address');
            if (!addr) {
                if (errAddress) errAddress.classList.remove('hidden');
                isValid = false;
            } else {
                if (errAddress) errAddress.classList.add('hidden');
            }

            // wallet_pin validation if wallet is selected
            const paymentWallet = document.getElementById('payment_wallet');
            if (paymentWallet && paymentWallet.checked) {
                const pinInput = document.getElementById('wallet_pin');
                const errPin = document.getElementById('err_wallet_pin');
                if (!pinInput || !pinInput.value.trim() || pinInput.value.trim().length !== 6) {
                    if (errPin) errPin.classList.remove('hidden');
                    isValid = false;
                } else {
                    if (errPin) errPin.classList.add('hidden');
                }
            }

            if (!isValid) {
                e.preventDefault();
                // Remove Swal so we just show inline errors naturally:
                // Swal.fire({ ... });
            }
        });

    // Khởi động - load tỉnh thành khi DOMContentLoaded
    document.addEventListener('DOMContentLoaded', function() {
        loadProvinces();

        // Toggle hiển thị input nhập mật khẩu ví
        const paymentRadios = document.querySelectorAll('input[name="payment_method"]');
        const walletPasswordSection = document.getElementById('wallet_password_section');

        function toggleWalletPassword() {
            if (!walletPasswordSection) return;
            const walletRadio = document.getElementById('payment_wallet');
            if (walletRadio && walletRadio.checked) {
                walletPasswordSection.classList.remove('hidden');
            } else {
                walletPasswordSection.classList.add('hidden');
            }
        }

        paymentRadios.forEach(radio => {
            radio.addEventListener('change', toggleWalletPassword);
        });

        // Chạy lần đầu nếu lỡ Wallet bị checked default
        toggleWalletPassword();
    });
</script>
@endsection