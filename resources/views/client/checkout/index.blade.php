@extends('client.layouts.app')

@section('title', 'Bee Phone - Thanh toán')

@section('content')
@php
    // LẤY CẤU HÌNH PHÍ SHIP TỪ ADMIN
    // Ban đầu chưa có địa chỉ nên để phí ship = -1 (để ẩn)
    $shipping_fee = -1; 
    $free_shipping_threshold = (int) (\App\Models\Setting::where('key', 'free_shipping_threshold')->first()?->value ?? 500000);

    // XÁC ĐỊNH PHÍ SHIP THỰC TẾ
    $applied_shipping_fee = 0;

    // CHUYỂN LOGIC TÍNH TIỀN LÊN ĐẦU ĐỂ DÙNG CHO PHẦN CHẶN COD
    $discount = session()->has('voucher') ? session('voucher')['discount_amount'] : 0;
    $finalTotal = $totalPrice - $discount; // Chưa cộng phí ship
    if($finalTotal < 0) $finalTotal = 0;
    
    // MỐC CHẶN SHIP COD (Ví dụ: 30.000.000đ)
    $maxCodAmount = 30000000; 
    $isCodAllowed = $finalTotal <= $maxCodAmount;

    // LẤY CẤU HÌNH TỪ ADMIN
    $settings_cod = \App\Models\Setting::where('key', 'payment_cod')->first()?->value ?? ['enabled' => true];
    $settings_vnpay = \App\Models\Setting::where('key', 'payment_vnpay')->first()?->value ?? ['enabled' => true];
    $settings_wallet = \App\Models\Setting::where('key', 'payment_wallet')->first()?->value ?? ['enabled' => true];

    $has_enabled_methods = ($settings_cod['enabled'] ?? false) || ($settings_vnpay['enabled'] ?? false) || ($settings_wallet['enabled'] ?? false);
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
    <form id="checkout-form" action="{{ route('client.checkout.process') }}" method="POST" class="flex flex-col lg:flex-row gap-8">
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
                        <input type="hidden" name="ghn_district_id" id="to_district_id" value="">
                        <input type="hidden" name="ghn_ward_code" id="to_ward_code" value="">
                        <input type="hidden" name="shipping_fee" id="shipping_fee_input" value="">

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
                        <p id="err_shipping_address" class="text-red-500 text-sm mt-2 hidden">Vui lòng cung cấp đầy đủ thông tin địa chỉ giao hàng (Số nhà, Phường/Xã, Quận/Huyện, Tỉnh/Thành)</p>
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
                    @if(!$has_enabled_methods)
                        <div class="bg-amber-100 text-amber-700 p-4 rounded-xl border border-amber-200">
                            Hệ thống đang bảo trì phương thức thanh toán. Vui lòng quay lại sau!
                        </div>
                    @else
                        {{-- THANH TOÁN KHI NHẬN HÀNG (COD) --}}
                        @if($settings_cod['enabled'] ?? false)
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
                        @endif

                        {{-- THANH TOÁN VNPAY --}}
                        @if($settings_vnpay['enabled'] ?? false)
                        <label class="border-2 border-gray-200 hover:border-primary bg-white dark:bg-black/20 rounded-xl p-4 flex items-center gap-4 cursor-pointer transition-colors has-[:checked]:border-primary has-[:checked]:bg-primary/5">
                            @php
                                $vnpayChecked = ($settings_vnpay['enabled'] && !($settings_cod['enabled'] && $isCodAllowed)) ? 'checked' : '';
                            @endphp
                            <input type="radio" name="payment_method" value="vnpay" {{ $vnpayChecked }} class="w-5 h-5 text-primary focus:ring-primary">
                            <img src="https://cdn.haitrieu.com/wp-content/uploads/2022/10/Icon-VNPAY-QR.png" alt="VNPAY" class="w-10 h-10 object-contain bg-white rounded p-1">
                            <div>
                                <p class="font-bold text-[#181611] dark:text-white">Thanh toán qua VNPAY</p>
                                <p class="text-sm text-gray-500">Hỗ trợ thẻ ATM, Internet Banking, mã QR...</p>
                            </div>
                        </label>
                        @endif

                        {{-- THANH TOÁN BẰNG VÍ --}}
                        @if($settings_wallet['enabled'] ?? false)
                            @php
                                $wallet = Auth::check() ? \App\Models\Wallet::where('user_id', Auth::id())->first() : null;
                                $balance = $wallet ? $wallet->balance : 0;
                                if ($wallet && $wallet->locked_until && $wallet->locked_until <= now()) {
                                    $wallet->update(['status' => 'active', 'locked_until' => null, 'pin_attempts' => 0]);
                                }
                                $isWalletLocked = $wallet && ($wallet->status !== 'active' || ($wallet->locked_until && $wallet->locked_until > now()));
                                $walletChecked = (!$settings_cod['enabled'] || !$isCodAllowed) && (!$settings_vnpay['enabled']) ? 'checked' : '';
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
                                        <input type="radio" name="payment_method" value="wallet" id="payment_wallet" {{ $walletChecked }} class="w-5 h-5 text-primary focus:ring-primary payment-radio">
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
                                    @if(Auth::check())
                                    <div id="wallet_password_section" class="w-full mt-2 hidden border-t border-gray-100 dark:border-white/10 pt-3 pl-[3.25rem]">
                                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Mã PIN giao dịch <span class="text-red-500">*</span></label>
                                        <input type="password" id="wallet_pin" name="wallet_pin" placeholder="Nhập mã PIN 6 số của bạn" class="w-50 bg-gray-50 dark:bg-black/20 border border-gray-200 dark:border-white/10 rounded-xl p-3 focus:ring-2 focus:ring-primary text-sm text-[#181611] dark:text-white text-center" maxlength="6" inputmode="numeric" pattern="[0-9]{6}" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 6);">
                                        <p id="err_wallet_pin" class="text-red-500 text-sm mt-1 hidden">Vui lòng nhập mã PIN hợp lệ gồm 6 chữ số</p>
                                    </div>
                                    @endif
                                </label>
                            @endif
                        @endif
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
                    <div class="flex justify-between items-center text-gray-500 dark:text-gray-400">
                        <div class="flex flex-col">
                            <span>Phí vận chuyển</span>
                            @if($shipping_fee == -1)
                                <p id="shipping-fee-notice" class="text-[10px] text-gray-400 font-bold uppercase">(Chọn địa chỉ để tính phí)</p>
                            @elseif($applied_shipping_fee == 0)
                                <p id="shipping-fee-notice" class="text-[10px] text-green-600 font-bold uppercase">(Đơn trên {{ number_format($free_shipping_threshold, 0, ',', '.') }}₫ nên Freeship)</p>
                            @else
                                <p id="shipping-fee-notice" class="text-[10px] text-amber-600 font-bold uppercase">(Freeship cho đơn trên {{ number_format($free_shipping_threshold, 0, ',', '.') }}₫)</p>
                            @endif
                        </div>
                        <span id="shipping-fee-display" class="font-bold {{ $shipping_fee == -1 ? 'text-gray-400' : ($applied_shipping_fee == 0 ? 'text-green-600' : 'text-[#181611] dark:text-white') }}">
                            @if($shipping_fee == -1)
                                ---
                            @elseif($applied_shipping_fee == 0)
                                Miễn phí
                            @else
                                {{ number_format($shipping_fee, 0, ',', '.') }}₫
                            @endif
                        </span>
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
                            <span class="font-bold text-green-600">-{{ number_format($discount, 0, ',', '.') }}₫</span>
                        </div>
                    @endif

                    <div class="flex justify-between items-end">
                        <span class="text-lg font-bold text-[#181611] dark:text-white">Tổng cộng</span>
                        <div class="text-right">
                            <span id="final-total-display" class="text-3xl font-bold text-red-500">{{ number_format($finalTotal, 0, ',', '.') }}₫</span>
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
    // ĐỊA CHỈ - API PROVINCES (GHN)
    // ==========================================
    const GHN_TOKEN = '{{ env('GHN_TOKEN') }}';
    const GHN_SHOP_ID = '{{ env('GHN_SHOP_ID') }}';
    // Đảm bảo URL kết thúc bằng / nếu có config
    let baseGhnUrl = '{{ env('GHN_API_URL', 'https://dev-online-gateway.ghn.vn/shiip/public-api/v2/') }}';
    if (!baseGhnUrl.endsWith('/')) baseGhnUrl += '/';
    
    // GHN Public API cấu trúc có thể là master-data ở bản cũ, nhưng ở v2 thường là /master-data/
    // Ta dùng theo document mặc định:
    const GHN_MASTER_DATA_URL = 'https://dev-online-gateway.ghn.vn/shiip/public-api/master-data/';
    const GHN_FEE_URL = baseGhnUrl + 'shipping-order/fee';

    let usingDefault = false;

    // Load danh sách tỉnh/thành khi trang load
    async function loadProvinces() {
        try {
            const res = await fetch(`${GHN_MASTER_DATA_URL}province`, {
                headers: { 'token': GHN_TOKEN }
            });
            const data = await res.json();
            const select = document.getElementById('addr_province');
            if (data.data) {
                data.data.forEach(p => {
                    const opt = document.createElement('option');
                    opt.value = p.ProvinceID;
                    opt.textContent = p.ProvinceName;
                    select.appendChild(opt);
                });
            }
        } catch(e) {
            console.error('Không load được danh sách tỉnh:', e);
        }
    }

    // Load quận/huyện theo mã tỉnh
    async function loadDistricts(provinceId) {
        const districtSelect = document.getElementById('addr_district');
        const wardSelect = document.getElementById('addr_ward');

        districtSelect.innerHTML = '<option value="">-- Chọn Quận/Huyện --</option>';
        wardSelect.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';
        wardSelect.disabled = true;

        if (!provinceId) {
            districtSelect.disabled = true;
            buildFullAddress();
            return;
        }

        districtSelect.disabled = true;
        districtSelect.innerHTML = '<option value="">Đang tải...</option>';

        try {
            const res = await fetch(`${GHN_MASTER_DATA_URL}district?province_id=${provinceId}`, {
                headers: { 'token': GHN_TOKEN }
            });
            const data = await res.json();
            districtSelect.innerHTML = '<option value="">-- Chọn Quận/Huyện --</option>';
            if (data.data) {
                data.data.forEach(d => {
                    const opt = document.createElement('option');
                    opt.value = d.DistrictID;
                    opt.textContent = d.DistrictName;
                    districtSelect.appendChild(opt);
                });
            }
            districtSelect.disabled = false;
        } catch(e) {
            districtSelect.innerHTML = '<option value="">-- Lỗi tải dữ liệu --</option>';
            console.error(e);
        }
        buildFullAddress();
    }

    // Load phường/xã theo mã quận/huyện
    async function loadWards(districtId) {
        const wardSelect = document.getElementById('addr_ward');
        wardSelect.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';
        wardSelect.disabled = true;

        if (!districtId) { buildFullAddress(); return; }

        wardSelect.innerHTML = '<option value="">Đang tải...</option>';

        try {
            const res = await fetch(`${GHN_MASTER_DATA_URL}ward?district_id=${districtId}`, {
                headers: { 'token': GHN_TOKEN }
            });
            const data = await res.json();
            wardSelect.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';
            if (data.data) {
                data.data.forEach(w => {
                    const opt = document.createElement('option');
                    opt.value = w.WardCode;
                    opt.textContent = w.WardName;
                    wardSelect.appendChild(opt);
                });
            }
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

        // Cập nhật ID cho GHN
        const districtId = distSel?.value;
        const wardCode = wardSel?.value;
        document.getElementById('to_district_id').value = districtId || '';
        document.getElementById('to_ward_code').value = wardCode || '';

        // Tính phí ship nếu đủ thông tin
        if (districtId && wardCode) {
            calculateShippingFee(districtId, wardCode);
        }

        const preview = document.getElementById('address-preview');
        const previewText = document.getElementById('address-preview-text');
        if (full) {
            previewText.textContent = full;
            preview.classList.remove('hidden');
        } else {
            preview.classList.add('hidden');
        }
    }

    // Cập nhật giao diện tiền phí
    function updateUIPrices(newShippingFee) {
        const freeShippingThreshold = {{ $free_shipping_threshold }};
        const baseTotalPrice = {{ $totalPrice }};
        const discountAmount = {{ $discount }};
        
        let appliedFee = 0;
        const feeDisplay = document.getElementById('shipping-fee-display');
        const feeNotice = document.getElementById('shipping-fee-notice');

        if (newShippingFee === -1) {
            // Trạng thái chưa có địa chỉ -> Ẩn phí ship
            if (feeDisplay) {
                feeDisplay.className = 'font-bold text-gray-400';
                feeDisplay.textContent = '---';
            }
            if (feeNotice) {
                feeNotice.className = 'text-[10px] text-gray-400 font-bold uppercase';
                feeNotice.textContent = '(Chọn địa chỉ để tính phí)';
            }
        } else {
            // Trạng thái đã có phí ship
            appliedFee = (baseTotalPrice >= freeShippingThreshold) ? 0 : newShippingFee;
            
            if (feeDisplay) {
                if (appliedFee === 0) {
                    feeDisplay.className = 'font-bold text-green-600';
                    feeDisplay.textContent = 'Miễn phí';
                } else {
                    feeDisplay.className = 'font-bold text-[#181611] dark:text-white';
                    feeDisplay.textContent = new Intl.NumberFormat('vi-VN').format(newShippingFee) + '₫';
                }
            }

            if (feeNotice) {
                if (appliedFee === 0) {
                    feeNotice.className = 'text-[10px] text-green-600 font-bold uppercase';
                    feeNotice.textContent = `(Đơn trên ${new Intl.NumberFormat('vi-VN').format(freeShippingThreshold)}₫ nên Freeship)`;
                } else {
                    feeNotice.className = 'text-[10px] text-amber-600 font-bold uppercase';
                    feeNotice.textContent = `(Freeship cho đơn trên ${new Intl.NumberFormat('vi-VN').format(freeShippingThreshold)}₫)`;
                }
            }
        }
        
        // Cập nhật input hidden để gửi lên backend
        const inputFee = document.getElementById('shipping_fee_input');
        if (inputFee) inputFee.value = (newShippingFee === -1) ? '' : appliedFee;

        // Tính tổng tiền cuối cùng
        let finalTotal = baseTotalPrice - discountAmount + appliedFee;
        if (finalTotal < 0) finalTotal = 0;

        const finalTotalDisplay = document.getElementById('final-total-display');
        if (finalTotalDisplay) {
            finalTotalDisplay.textContent = new Intl.NumberFormat('vi-VN').format(finalTotal) + '₫';
        }
    }

    // Tính phí ship qua GHN
    async function calculateShippingFee(districtId, wardCode) {
        try {
            const res = await fetch(GHN_FEE_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'token': GHN_TOKEN
                },
                body: JSON.stringify({
                    shop_id: parseInt(GHN_SHOP_ID),
                    to_district_id: parseInt(districtId),
                    to_ward_code: wardCode,
                    weight: {{ env('GHN_DEFAULT_WEIGHT', 500) }},
                    length: {{ env('GHN_DEFAULT_LENGTH', 10) }},
                    width: {{ env('GHN_DEFAULT_WIDTH', 10) }},
                    height: {{ env('GHN_DEFAULT_HEIGHT', 10) }},
                    service_type_id: {{ env('GHN_SERVICE_TYPE_ID', 2) }}
                })
            });
            const data = await res.json();
            if (data.code === 200) {
                let fee = data.data.total;
                updateUIPrices(fee);
            } else {
                console.error('GHN API Error:', data.message);
                updateUIPrices(-1);
                alert('Lỗi GHN API: ' + data.message);
            }
        } catch(e) {
            console.error('Lỗi tính phí ship:', e);
            updateUIPrices(-1);
        }
    }

    function updateUIPrices(newShippingFee) {
        const freeShippingThreshold = {{ $free_shipping_threshold }};
        const baseTotalPrice = {{ $totalPrice }};
        const discountAmount = {{ $discount }};
        
        // Xác định có freeship không
        let appliedFee = (baseTotalPrice >= freeShippingThreshold) ? 0 : newShippingFee;
        
        // Cập nhật DOM hiển thị phí ship
        const feeDisplay = document.getElementById('shipping-fee-display');
        if (feeDisplay) {
            if (appliedFee === 0) {
                feeDisplay.className = 'font-bold text-green-600';
                feeDisplay.textContent = 'Miễn phí';
            } else {
                feeDisplay.className = 'font-bold text-[#181611] dark:text-white';
                feeDisplay.textContent = new Intl.NumberFormat('vi-VN').format(newShippingFee) + '₫';
            }
        }

        const feeNotice = document.getElementById('shipping-fee-notice');
        if (feeNotice) {
            if (appliedFee === 0) {
                feeNotice.className = 'text-[10px] text-green-600 font-bold uppercase';
                feeNotice.textContent = `(Đơn trên ${new Intl.NumberFormat('vi-VN').format(freeShippingThreshold)}₫ nên Freeship)`;
            } else {
                feeNotice.className = 'text-[10px] text-amber-600 font-bold uppercase';
                feeNotice.textContent = `(Freeship cho đơn trên ${new Intl.NumberFormat('vi-VN').format(freeShippingThreshold)}₫)`;
            }
        }
        
        // Cập nhật input hidden để gửi lên backend
        const inputFee = document.getElementById('shipping_fee_input');
        if (inputFee) inputFee.value = appliedFee;

        // Tính tổng tiền cuối cùng
        let finalTotal = baseTotalPrice - discountAmount + appliedFee;
        if (finalTotal < 0) finalTotal = 0;

        const finalTotalDisplay = document.getElementById('final-total-display');
        if (finalTotalDisplay) {
            finalTotalDisplay.textContent = new Intl.NumberFormat('vi-VN').format(finalTotal) + '₫';
        }
    }

    // ==========================================
    // SUBMIT FORM THẲNG LÊN BACKEND (không qua GHN)
    // GHN chỉ được tạo khi Admin duyệt đơn ở trang quản trị
    // ==========================================

    // Hàm chuẩn hóa chuỗi để so sánh
    function normalizeStr(str) {
        if (!str) return '';
        return str.toLowerCase()
            .replace(/^(tỉnh|thành phố|tp|quận|q|huyện|h|thị xã|tx|phường|p|xã|thị trấn|tt)[\s\.]+/i, '')
            .replace(/-/g, ' ')
            .replace(/\s+/g, ' ')
            .trim();
    }

    // Hàm kiểm tra khớp chuỗi chính xác (hỗ trợ NameExtension của GHN)
    function checkMatch(userStr, apiItem, nameKey) {
        const u = normalizeStr(userStr);
        const g = normalizeStr(apiItem[nameKey]);
        if (u === g) return true;
        
        if (apiItem.NameExtension && Array.isArray(apiItem.NameExtension)) {
            for (let ext of apiItem.NameExtension) {
                if (u === normalizeStr(ext)) return true;
            }
        }
        return false;
    }

    // Nút "Dùng ngay" - dùng địa chỉ mặc định từ profile
    async function useDefaultAddress() {
        const defaultAddr = document.getElementById('default-address-text')?.textContent.trim();
        if (!defaultAddr) return;

        usingDefault = true;
        document.getElementById('shipping_address_hidden').value = defaultAddr;

        // Đặt ID về rỗng do không xác định được mã vùng của GHN từ text tự do
        document.getElementById('to_district_id').value = '';
        document.getElementById('to_ward_code').value = '';

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

        // Đang tải...
        updateUIPrices(-1);
        const feeDisplay = document.getElementById('shipping-fee-display');
        if (feeDisplay) feeDisplay.textContent = 'Đang tính...';

        // Tự động bóc tách để tính phí GHN
        const parts = defaultAddr.split(',').map(p => p.trim());
        if (parts.length >= 3) {
            const provinceName = parts[parts.length - 1];
            const districtName = parts[parts.length - 2];
            const wardName = parts[parts.length - 3];

            try {
                let res = await fetch(`${GHN_MASTER_DATA_URL}province`, { headers: { 'token': GHN_TOKEN } });
                let data = await res.json();
                let provinces = data.data || [];
                // Ưu tiên khớp chính xác tên chính (để tránh vụ Hà Nội 02 có alias Hà Nội trong Sandbox GHN)
                let province = provinces.find(p => normalizeStr(p.ProvinceName) === normalizeStr(provinceName)) 
                            || provinces.find(p => checkMatch(provinceName, p, 'ProvinceName'));
                
                if (province) {
                    res = await fetch(`${GHN_MASTER_DATA_URL}district?province_id=${province.ProvinceID}`, { headers: { 'token': GHN_TOKEN } });
                    data = await res.json();
                    let districts = data.data || [];
                    let district = districts.find(d => normalizeStr(d.DistrictName) === normalizeStr(districtName))
                                || districts.find(d => checkMatch(districtName, d, 'DistrictName'));

                    if (district) {
                        res = await fetch(`${GHN_MASTER_DATA_URL}ward?district_id=${district.DistrictID}`, { headers: { 'token': GHN_TOKEN } });
                        data = await res.json();
                        let wards = data.data || [];
                        let ward = wards.find(w => normalizeStr(w.WardName) === normalizeStr(wardName))
                                || wards.find(w => checkMatch(wardName, w, 'WardName'));

                        if (ward) {
                            document.getElementById('to_district_id').value = district.DistrictID;
                            document.getElementById('to_ward_code').value = ward.WardCode;
                            await calculateShippingFee(district.DistrictID, ward.WardCode);
                            return; // Thành công
                        } else {
                            console.error('Không tìm thấy Ward:', wardName);
                        }
                    } else {
                        console.error('Không tìm thấy District:', districtName);
                    }
                } else {
                    console.error('Không tìm thấy Province:', provinceName);
                }
            } catch(e) {
                console.error('Lỗi khi bóc tách địa chỉ mặc định:', e);
            }
        }

        // Nếu bóc tách thất bại hoặc API lỗi, không gán 30k nữa mà bắt chọn lại
        updateUIPrices(-1);
        alert('Không thể bóc tách tự động mã vùng GHN. Vui lòng nhập địa chỉ giao hàng bằng tay!');
        cancelDefaultAddress();
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

        const distSel  = document.getElementById('addr_district');
        const wardSel  = document.getElementById('addr_ward');
        if (distSel?.value && wardSel?.value) {
            buildFullAddress();
        } else {
            updateUIPrices(-1); // Đưa về trạng thái chưa tính phí
        }
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
            const distId = document.getElementById('to_district_id').value;
            const wardCode = document.getElementById('to_ward_code').value;
            const errAddress = document.getElementById('err_shipping_address');
            
            if (!addr || !distId || !wardCode) {
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