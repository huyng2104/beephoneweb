@extends('admin.layouts.app')

@section('content')
<div class="p-4 md:p-8 bg-[#f8f9fa] min-h-screen">
    @if(session('success'))
        <div class="fixed top-8 right-8 z-[9999] bg-white border-l-4 border-green-500 shadow-2xl rounded-xl p-4 flex items-center gap-4 animate-bounce-short">
            <div class="bg-green-100 text-green-600 p-2 rounded-full">
                <span class="material-symbols-outlined">check_circle</span>
            </div>
            <div class="text-slate-700 font-bold uppercase text-xs tracking-widest">{{ session('success') }}</div>
            <button onclick="this.parentElement.remove()" class="text-slate-300 hover:text-slate-500 transition-colors">
                <span class="material-symbols-outlined text-sm">close</span>
            </button>
        </div>
    @endif

    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-extrabold text-slate-800 tracking-tight flex items-center gap-3">
                <span class="p-2 bg-primary/20 rounded-2xl text-primary material-symbols-outlined text-3xl">settings</span>
                Cài đặt hệ thống
            </h1>
            <p class="text-slate-400 mt-2 text-sm font-medium">Cấu hình tham số vận hành sàn thương mại điện tử BeePhone.</p>
        </div>
        <button type="submit" form="settings-form" class="bg-primary hover:bg-primary/90 text-background-dark px-8 py-3 rounded-2xl font-extrabold shadow-xl shadow-primary/20 transition-all transform hover:-translate-y-1 active:scale-95 flex items-center justify-center gap-2">
            <span class="material-symbols-outlined text-[20px]">save</span>
            Lưu tất cả thay đổi
        </button>
    </div>

    <!-- Tab Navigation -->
    <div class="flex items-end space-x-1 border-b border-slate-200 mb-0 px-2 overflow-x-auto custom-scrollbar whitespace-nowrap bg-white/50 pt-4 rounded-t-3xl border-x border-t">
        <button type="button" onclick="switchTab(event, 'general')" class="tab-btn active px-6 py-4 -mb-[1px] text-[13px] uppercase tracking-widest font-extrabold text-primary border-b-2 border-primary focus:outline-none transition-all duration-300 flex items-center gap-2">
            <span class="material-symbols-outlined text-[20px]">settings_suggest</span>
            Tổng quát
        </button>
        <button type="button" onclick="switchTab(event, 'header')" class="tab-btn px-6 py-4 -mb-[1px] text-[13px] uppercase tracking-widest font-extrabold text-slate-400 hover:text-slate-600 focus:outline-none transition-all duration-300 flex items-center gap-2">
            <span class="material-symbols-outlined text-[20px]">view_headline</span>
            Header
        </button>
        <button type="button" onclick="switchTab(event, 'footer')" class="tab-btn px-6 py-4 -mb-[1px] text-[13px] uppercase tracking-widest font-extrabold text-slate-400 hover:text-slate-600 focus:outline-none transition-all duration-300 flex items-center gap-2">
            <span class="material-symbols-outlined text-[20px]">view_stream</span>
            Footer
        </button>
        <button type="button" onclick="switchTab(event, 'payment')" class="tab-btn px-6 py-4 -mb-[1px] text-[13px] uppercase tracking-widest font-extrabold text-slate-400 hover:text-slate-600 focus:outline-none transition-all duration-300 flex items-center gap-2">
            <span class="material-symbols-outlined text-[20px]">payments</span>
            Thanh toán
        </button>
        <button type="button" onclick="switchTab(event, 'shipping')" class="tab-btn px-6 py-4 -mb-[1px] text-[13px] uppercase tracking-widest font-extrabold text-slate-400 hover:text-slate-600 focus:outline-none transition-all duration-300 flex items-center gap-2">
            <span class="material-symbols-outlined text-[20px]">local_shipping</span>
            Vận chuyển
        </button>
    </div>

    <!-- Tab Content -->
    <div id="settings-content" class="bg-white border border-slate-200 border-t-0 p-6 md:p-10 rounded-b-3xl shadow-2xl shadow-slate-200/40 min-h-[500px]">
        <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data" id="settings-form">
            @csrf
            
            <!-- General Section -->
            <div id="general" class="tab-pane space-y-8 animate-fade-in">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                    <div class="space-y-4">
                        <label class="block text-xs font-black text-slate-400 uppercase tracking-widest">Tên website</label>
                        <input type="text" name="settings[site_name]" value="{{ $settings['site_name']->value ?? '' }}" class="w-full bg-slate-50 border-slate-200 rounded-2xl focus:ring-4 focus:ring-primary/10 focus:border-primary px-5 py-4 text-slate-700 font-semibold transition-all">
                    </div>
                    <div class="space-y-4">
                        <label class="block text-xs font-black text-slate-400 uppercase tracking-widest">Hotline hỗ trợ</label>
                        <input type="text" name="settings[hotline]" value="{{ $settings['hotline']->value ?? '' }}" class="w-full bg-slate-50 border-slate-200 rounded-2xl focus:ring-4 focus:ring-primary/10 focus:border-primary px-5 py-4 text-slate-700 font-semibold transition-all">
                    </div>
                    <div class="space-y-4">
                        <label class="block text-xs font-black text-slate-400 uppercase tracking-widest">Email liên lạc</label>
                        <input type="email" name="settings[email_contact]" value="{{ $settings['email_contact']->value ?? '' }}" class="w-full bg-slate-50 border-slate-200 rounded-2xl focus:ring-4 focus:ring-primary/10 focus:border-primary px-5 py-4 text-slate-700 font-semibold transition-all">
                    </div>
                    <div class="space-y-4">
                        <label class="block text-xs font-black text-slate-400 uppercase tracking-widest">Địa chỉ showroom</label>
                        <input type="text" name="settings[address]" value="{{ $settings['address']->value ?? '' }}" class="w-full bg-slate-50 border-slate-200 rounded-2xl focus:ring-4 focus:ring-primary/10 focus:border-primary px-5 py-4 text-slate-700 font-semibold transition-all">
                    </div>
                    <div class="space-y-4">
                        <label class="block text-xs font-black text-slate-400 uppercase tracking-widest">Logo Website</label>
                        <div class="flex flex-col gap-4 items-start">
                            <img id="preview_logo" src="{{ isset($settings['site_logo']) && $settings['site_logo']->value ? asset($settings['site_logo']->value) : '' }}" class="h-16 w-auto object-contain border rounded-xl p-2 bg-slate-50 {{ isset($settings['site_logo']) && $settings['site_logo']->value ? '' : 'hidden' }}">
                            <input type="file" name="settings[site_logo]" id="input_logo" accept="image/*" onchange="previewSettingImage(this, 'preview_logo')" class="w-full bg-slate-50 border-slate-200 rounded-2xl focus:ring-4 focus:ring-primary/10 focus:border-primary px-5 py-3 text-slate-700 font-semibold transition-all file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20">
                        </div>
                    </div>
                    <div class="space-y-4">
                        <label class="block text-xs font-black text-slate-400 uppercase tracking-widest">Favicon</label>
                        <div class="flex flex-col gap-4 items-start">
                            <img id="preview_favicon" src="{{ isset($settings['site_favicon']) && $settings['site_favicon']->value ? asset($settings['site_favicon']->value) : '' }}" class="h-12 w-12 object-contain border rounded-xl p-2 bg-slate-50 {{ isset($settings['site_favicon']) && $settings['site_favicon']->value ? '' : 'hidden' }}">
                            <input type="file" name="settings[site_favicon]" id="input_favicon" accept="image/*" onchange="previewSettingImage(this, 'preview_favicon')" class="w-full bg-slate-50 border-slate-200 rounded-2xl focus:ring-4 focus:ring-primary/10 focus:border-primary px-5 py-3 text-slate-700 font-semibold transition-all file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20">
                        </div>
                    </div>
                </div>
                
                <div class="mt-8 p-6 bg-amber-50 rounded-3xl border border-amber-200/60 w-full">
                    <div class="flex items-start gap-4 mb-6">
                        <div class="mt-1">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="hidden" name="settings_json[maintenance_mode][is_enabled]" value="0">
                                <input type="checkbox" name="settings_json[maintenance_mode][is_enabled]" value="1" {{ (isset($settings['maintenance_mode']) && ($settings['maintenance_mode']->value['is_enabled'] ?? false)) ? 'checked' : '' }} class="sr-only peer">
                                <div class="w-14 h-7 bg-slate-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[4px] after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-amber-500 shadow-inner"></div>
                            </label>
                        </div>
                        <div>
                            <h4 class="font-extrabold text-amber-800 text-lg">Chế độ bảo trì (Maintenance Mode)</h4>
                            <p class="text-sm text-amber-700/80 font-medium mt-1">Bật chế độ này sẽ tạm thời đóng cửa trang web với người dùng. Chỉ có Admin mới có thể truy cập.</p>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-white p-5 rounded-2xl border border-amber-100">
                        <div class="space-y-3">
                            <label class="block text-[11px] font-black text-amber-600 uppercase tracking-widest pl-1">Thời gian dự kiến kết thúc</label>
                            <input type="datetime-local" name="settings_json[maintenance_mode][end_at]" value="{{ $settings['maintenance_mode']->value['end_at'] ?? '' }}" class="w-full bg-slate-50 border-slate-200 rounded-xl px-4 py-3 text-sm font-semibold shadow-sm focus:ring-amber-500/20 focus:border-amber-500 transition-all text-slate-700">
                        </div>
                        <div class="space-y-3">
                            <label class="block text-[11px] font-black text-amber-600 uppercase tracking-widest pl-1">Thông báo bảo trì</label>
                            <input type="text" name="settings_json[maintenance_mode][message]" value="{{ $settings['maintenance_mode']->value['message'] ?? 'BeePhone đang nâng cấp hệ thống định kỳ.' }}" class="w-full bg-slate-50 border-slate-200 rounded-xl px-4 py-3 text-sm font-semibold shadow-sm focus:ring-amber-500/20 focus:border-amber-500 transition-all text-slate-700" placeholder="VD: Đang nâng cấp hệ thống...">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Header Section -->
            <div id="header" class="tab-pane hidden animate-fade-in">
                <div class="space-y-6 mb-8 border-b border-slate-100 pb-6">
                    <div class="max-w-2xl space-y-4">
                        <label class="block text-xs font-black text-slate-400 uppercase tracking-widest">Thanh thông báo (Top Bar)</label>
                        <input type="text" name="settings[header_top_bar]" value="{{ $settings['header_top_bar']->value ?? '' }}" class="w-full bg-slate-50 border-slate-200 rounded-2xl focus:ring-4 focus:ring-primary/10 focus:border-primary px-5 py-4 text-slate-700 font-semibold transition-all">
                        <p class="text-[11px] text-slate-400 font-medium italic">Nội dung sẽ cuộn hoặc hiển thị tĩnh ở đầu trang web.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                    <!-- Left Column: Add items -->
                    <div class="lg:col-span-4 space-y-4">
                        <h3 class="font-extrabold text-slate-700 text-sm uppercase tracking-widest mb-4">Thêm các mục menu</h3>
                        
                        <div class="border border-slate-200 rounded-xl bg-white overflow-hidden shadow-sm">
                            <!-- Custom Link -->
                            <div class="border-b border-slate-100 last:border-0 wp-accordion">
                                <button type="button" class="w-full px-4 py-3 bg-slate-50 flex justify-between items-center text-sm font-bold text-slate-700 hover:text-primary transition-colors focus:outline-none" onclick="toggleWpAccordion(this)">
                                    Liên kết tự tạo
                                    <span class="material-symbols-outlined text-[20px] transition-transform">expand_more</span>
                                </button>
                                <div class="p-4 space-y-3 bg-white hidden">
                                    <div>
                                        <label class="block text-[11px] font-black text-slate-400 uppercase tracking-widest pl-1 mb-1">URL</label>
                                        <input type="text" id="wp_custom_url" value="http://" class="w-full bg-slate-50 border-slate-200 rounded-lg px-3 py-2 text-sm focus:ring-primary/20 focus:border-primary">
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-black text-slate-400 uppercase tracking-widest pl-1 mb-1">Tên đường dẫn</label>
                                        <input type="text" id="wp_custom_title" placeholder="Ví dụ: Giới thiệu" class="w-full bg-slate-50 border-slate-200 rounded-lg px-3 py-2 text-sm focus:ring-primary/20 focus:border-primary">
                                    </div>
                                    <div class="text-right pt-2">
                                        <button type="button" onclick="addCustomLinkToMenu()" class="border border-primary text-primary hover:bg-primary hover:text-white px-3 py-1.5 rounded-lg text-xs font-bold transition-colors">
                                            Thêm vào menu
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Product Categories -->
                            <div class="border-b border-slate-100 last:border-0 wp-accordion">
                                <button type="button" class="w-full px-4 py-3 bg-slate-50 flex justify-between items-center text-sm font-bold text-slate-700 hover:text-primary transition-colors focus:outline-none" onclick="toggleWpAccordion(this)">
                                    Danh mục sản phẩm
                                    <span class="material-symbols-outlined text-[20px] transition-transform">expand_more</span>
                                </button>
                                <div class="bg-white hidden flex flex-col h-full">
                                    <div class="p-3 max-h-48 overflow-y-auto space-y-1 border-b border-slate-100">
                                        @foreach($categories as $cat)
                                        <label class="flex items-center gap-2 cursor-pointer p-1.5 hover:bg-slate-50 rounded">
                                            <input type="checkbox" class="wp-cat-checkbox text-primary focus:ring-primary rounded border-slate-300" value="{{ $cat->slug }}" data-name="{{ $cat->name }}" data-type="Danh mục SP">
                                            <span class="text-sm font-bold text-slate-700">{{ $cat->name }}</span>
                                        </label>
                                            @if($cat->children && $cat->children->count())
                                                <div class="pl-6 border-l-2 border-slate-100 ml-2 space-y-1 mb-2">
                                                @foreach($cat->children as $child)
                                                    <label class="flex items-center gap-2 cursor-pointer p-1 hover:bg-slate-50 rounded">
                                                        <input type="checkbox" class="wp-cat-checkbox text-primary focus:ring-primary rounded border-slate-300" value="{{ $child->slug }}" data-name="{{ $child->name }}" data-type="Danh mục SP">
                                                        <span class="text-sm font-medium text-slate-600">{{ $child->name }}</span>
                                                    </label>
                                                @endforeach
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                    <div class="p-3 flex justify-between items-center bg-slate-50/50">
                                        <label class="text-xs text-slate-500 cursor-pointer flex items-center gap-1 hover:text-primary">
                                            <input type="checkbox" onchange="toggleAllCheckboxes(this, 'wp-cat-checkbox')" class="rounded border-slate-300 text-primary focus:ring-primary"> Chọn tất cả
                                        </label>
                                        <button type="button" onclick="addCheckedItemsToMenu('wp-cat-checkbox', '/danh-muc/')" class="border border-primary text-primary hover:bg-primary hover:text-white px-3 py-1.5 rounded-lg text-xs font-bold transition-colors">
                                            Thêm vào menu
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Post Categories -->
                            <div class="border-b border-slate-100 last:border-0 wp-accordion">
                                <button type="button" class="w-full px-4 py-3 bg-slate-50 flex justify-between items-center text-sm font-bold text-slate-700 hover:text-primary transition-colors focus:outline-none" onclick="toggleWpAccordion(this)">
                                    Danh mục bài viết
                                    <span class="material-symbols-outlined text-[20px] transition-transform">expand_more</span>
                                </button>
                                <div class="bg-white hidden flex flex-col h-full">
                                    <div class="p-3 max-h-48 overflow-y-auto space-y-2 border-b border-slate-100">
                                        @foreach($postCategories as $pcat)
                                        <label class="flex items-center gap-2 cursor-pointer p-1 hover:bg-slate-50 rounded">
                                            <input type="checkbox" class="wp-pcat-checkbox text-primary focus:ring-primary rounded border-slate-300" value="{{ $pcat->slug }}" data-name="{{ $pcat->name }}" data-type="Danh mục Tin tức">
                                            <span class="text-sm font-medium text-slate-600">{{ $pcat->name }}</span>
                                        </label>
                                        @endforeach
                                    </div>
                                    <div class="p-3 flex justify-between items-center bg-slate-50/50">
                                        <label class="text-xs text-slate-500 cursor-pointer flex items-center gap-1 hover:text-primary">
                                            <input type="checkbox" onchange="toggleAllCheckboxes(this, 'wp-pcat-checkbox')" class="rounded border-slate-300 text-primary focus:ring-primary"> Chọn tất cả
                                        </label>
                                        <button type="button" onclick="addCheckedItemsToMenu('wp-pcat-checkbox', '/tin-tuc/danh-muc/')" class="border border-primary text-primary hover:bg-primary hover:text-white px-3 py-1.5 rounded-lg text-xs font-bold transition-colors">
                                            Thêm vào menu
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Brands -->
                            <div class="border-b border-slate-100 last:border-0 wp-accordion">
                                <button type="button" class="w-full px-4 py-3 bg-slate-50 flex justify-between items-center text-sm font-bold text-slate-700 hover:text-primary transition-colors focus:outline-none" onclick="toggleWpAccordion(this)">
                                    Thương hiệu (Brands)
                                    <span class="material-symbols-outlined text-[20px] transition-transform">expand_more</span>
                                </button>
                                <div class="bg-white hidden flex flex-col h-full">
                                    <div class="p-3 max-h-48 overflow-y-auto space-y-2 border-b border-slate-100">
                                        @if(isset($brands) && $brands->count())
                                            @foreach($brands as $b)
                                            <label class="flex items-center gap-2 cursor-pointer p-1 hover:bg-slate-50 rounded">
                                                <input type="checkbox" class="wp-brand-checkbox text-primary focus:ring-primary rounded border-slate-300" value="{{ $b->slug }}" data-name="{{ $b->name }}" data-type="Thương hiệu">
                                                <span class="text-sm font-medium text-slate-600">{{ $b->name }}</span>
                                            </label>
                                            @endforeach
                                        @else
                                            <p class="text-xs text-slate-400 pl-2">Chưa có thương hiệu nào.</p>
                                        @endif
                                    </div>
                                    <div class="p-3 flex justify-between items-center bg-slate-50/50">
                                        <label class="text-xs text-slate-500 cursor-pointer flex items-center gap-1 hover:text-primary">
                                            <input type="checkbox" onchange="toggleAllCheckboxes(this, 'wp-brand-checkbox')" class="rounded border-slate-300 text-primary focus:ring-primary"> Chọn tất cả
                                        </label>
                                        <button type="button" onclick="addCheckedItemsToMenu('wp-brand-checkbox', '/thuong-hieu/')" class="border border-primary text-primary hover:bg-primary hover:text-white px-3 py-1.5 rounded-lg text-xs font-bold transition-colors">
                                            Thêm vào menu
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Menu Structure -->
                    <div class="lg:col-span-8 space-y-4">
                        <h3 class="font-extrabold text-slate-700 text-sm uppercase tracking-widest mb-4">Cấu trúc menu</h3>
                        <div class="p-5 bg-slate-50 border border-slate-200 rounded-2xl min-h-[400px]">
                            <p class="text-[12px] text-slate-500 mb-4 px-2">Nhấp chuột vào mũi tên bên phải để thiết lập tuỳ chỉnh cho mỗi mục.</p>
                            
                            <div id="menu-editor-container" class="space-y-1">
                                <!-- Menu Items go here -->
                            </div>
                            
                            <div id="menu-inputs-container"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Section -->
            <div id="footer" class="tab-pane hidden space-y-8 animate-fade-in">
                <div class="max-w-2xl space-y-6">
                    <div class="space-y-4">
                        <label class="block text-xs font-black text-slate-400 uppercase tracking-widest">Dòng bản quyền (Copyright)</label>
                        <input type="text" name="settings[footer_copyright]" value="{{ $settings['footer_copyright']->value ?? '' }}" class="w-full bg-slate-50 border-slate-200 rounded-2xl focus:ring-4 focus:ring-primary/10 focus:border-primary px-5 py-4 text-slate-700 font-semibold transition-all">
                    </div>
                </div>
            </div>

            <!-- Payment Section -->
            <div id="payment" class="tab-pane hidden space-y-8 animate-fade-in">
                <div class="grid grid-cols-1 gap-6 max-w-4xl">
                    <div class="group p-6 bg-white rounded-3xl border-2 border-slate-100 hover:border-primary/30 transition-all flex items-center justify-between shadow-sm">
                        <div class="flex items-center gap-6">
                            <div class="w-16 h-16 bg-slate-50 rounded-2xl flex items-center justify-center text-slate-400 group-hover:text-primary transition-colors ring-1 ring-slate-100">
                                <span class="material-symbols-outlined text-4xl">payments</span>
                            </div>
                            <div>
                                <h4 class="font-extrabold text-slate-800 text-lg">Thanh toán khi nhận hàng (COD)</h4>
                                <p class="text-sm text-slate-400 font-medium mt-1">Phổ biến và an toàn nhất cho khách hàng mới.</p>
                            </div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="hidden" name="settings_json[payment_cod][enabled]" value="0">
                            <input type="checkbox" name="settings_json[payment_cod][enabled]" value="1" {{ (isset($settings['payment_cod']->value['enabled']) && $settings['payment_cod']->value['enabled']) ? 'checked' : '' }} class="sr-only peer">
                            <div class="w-14 h-7 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[4px] after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-primary shadow-inner"></div>
                        </label>
                    </div>
                    
                    <div class="group p-6 bg-white rounded-3xl border-2 border-slate-100 hover:border-primary/30 transition-all flex items-center justify-between shadow-sm">
                        <div class="flex items-center gap-6">
                            <div class="w-16 h-16 bg-slate-50 rounded-2xl flex items-center justify-center overflow-hidden ring-1 ring-slate-100 p-2">
                                <img src="https://sandbox.vnpayment.vn/paymentv2/Images/brands/logo-vnpay.png" alt="VNPAY" class="w-full object-contain filter grayscale group-hover:grayscale-0 transition-all">
                            </div>
                            <div>
                                <h4 class="font-extrabold text-slate-800 text-lg">Cổng thanh toán VNPAY</h4>
                                <p class="text-sm text-slate-400 font-medium mt-1">Hỗ trợ QR-Code, Thẻ ATM, VISA/MasterCard.</p>
                            </div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="hidden" name="settings_json[payment_vnpay][enabled]" value="0">
                            <input type="checkbox" name="settings_json[payment_vnpay][enabled]" value="1" {{ (isset($settings['payment_vnpay']->value['enabled']) && $settings['payment_vnpay']->value['enabled']) ? 'checked' : '' }} class="sr-only peer">
                            <div class="w-14 h-7 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[4px] after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-primary shadow-inner"></div>
                        </label>
                    </div>

                    <div class="p-8 bg-slate-50 rounded-[40px] border border-slate-200/60 space-y-8 mt-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-1.5 bg-primary rounded-full"></div>
                            <h4 class="font-black text-slate-800 text-sm uppercase tracking-tighter">Cấu hình tham số VNPAY</h4>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-4">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest pl-1">TMN Code</label>
                                <input type="text" name="settings_json[payment_vnpay][tmn_code]" value="{{ $settings['payment_vnpay']->value['tmn_code'] ?? 'EXAMPLE' }}" class="w-full bg-white border-slate-200 rounded-2xl px-5 py-4 text-sm font-bold shadow-sm focus:ring-primary focus:border-primary transition-all">
                            </div>
                            <div class="space-y-4">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest pl-1">Hash Secret</label>
                                <input type="password" name="settings_json[payment_vnpay][hash_secret]" value="{{ $settings['payment_vnpay']->value['hash_secret'] ?? 'SECRET' }}" class="w-full bg-white border-slate-200 rounded-2xl px-5 py-4 text-sm font-bold shadow-sm focus:ring-primary focus:border-primary transition-all">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Shipping Section -->
            <div id="shipping" class="tab-pane hidden space-y-8 animate-fade-in">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-10 max-w-5xl">
                    <div class="p-8 bg-white border-2 border-slate-100 rounded-[40px] space-y-6 shadow-sm hover:shadow-md transition-all">
                        <div class="flex items-center justify-between">
                            <div class="w-16 h-16 bg-blue-50 text-blue-500 rounded-3xl flex items-center justify-center">
                                <span class="material-symbols-outlined text-4xl">local_shipping</span>
                            </div>
                            <span class="bg-blue-100 text-blue-600 px-4 py-1.5 rounded-full text-xs font-black uppercase tracking-wider">Mặc định</span>
                        </div>
                        <div class="space-y-2">
                            <h4 class="font-black text-slate-800 text-xl tracking-tight">Phí vận chuyển</h4>
                            <p class="text-sm text-slate-400 font-medium">Chi phí khách hàng phải trả cho mỗi đơn hàng giao tận nơi.</p>
                        </div>
                        <div class="flex items-center gap-4 bg-slate-50 p-2 rounded-3xl border border-slate-100">
                            <input type="number" name="settings[shipping_fee]" value="{{ $settings['shipping_fee']->value ?? 30000 }}" class="w-full bg-transparent border-none focus:ring-0 px-6 py-4 font-black text-2xl text-slate-800">
                            <span class="pr-8 text-slate-400 font-bold uppercase text-sm">VNĐ</span>
                        </div>
                    </div>

                    <div class="p-8 bg-white border-2 border-slate-100 rounded-[40px] space-y-6 shadow-sm hover:shadow-md transition-all">
                        <div class="flex items-center justify-between">
                            <div class="w-16 h-16 bg-green-50 text-green-500 rounded-3xl flex items-center justify-center">
                                <span class="material-symbols-outlined text-4xl">redeem</span>
                            </div>
                            <span class="bg-green-100 text-green-600 px-4 py-1.5 rounded-full text-xs font-black uppercase tracking-wider">Khuyến mãi</span>
                        </div>
                        <div class="space-y-2">
                            <h4 class="font-black text-slate-800 text-xl tracking-tight">Miễn phí từ mức</h4>
                            <p class="text-sm text-slate-400 font-medium">Đơn hàng đạt giá trị này sẽ không tính phí vận chuyển.</p>
                        </div>
                        <div class="flex items-center gap-4 bg-slate-50 p-2 rounded-3xl border border-slate-100">
                            <input type="number" name="settings[free_shipping_threshold]" value="{{ $settings['free_shipping_threshold']->value ?? 500000 }}" class="w-full bg-transparent border-none focus:ring-0 px-6 py-4 font-black text-2xl text-slate-800">
                            <span class="pr-8 text-slate-400 font-bold uppercase text-sm">VNĐ</span>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@push('js')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.6/Sortable.min.js"></script>
<script>
    function switchTab(evt, tabId) {
        document.querySelectorAll('.tab-pane').forEach(pane => pane.classList.add('hidden'));
        document.getElementById(tabId).classList.remove('hidden');
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('active', 'text-primary', 'border-primary', 'border-b-2');
            btn.classList.add('text-slate-400');
        });
        const btn = evt.currentTarget;
        btn.classList.add('active', 'text-primary', 'border-primary', 'border-b-2');
        btn.classList.remove('text-slate-400');
    }

    function previewSettingImage(input, previewId) {
        const preview = document.getElementById(previewId);
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.classList.remove('hidden');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    // =============================================
    // ===  Header Menu Logic (with Drag & Drop) ===
    // =============================================
    let menuData = {!! isset($settings['header_menu']) && $settings['header_menu']->value ? json_encode($settings['header_menu']->value) : '[]' !!};

    // --- Helper: find item by id (recursive) ---
    function findMenuItemById(id, items, parentArr) {
        items = items || menuData;
        parentArr = parentArr || menuData;
        for (let i = 0; i < items.length; i++) {
            if (items[i].id == id) return { item: items[i], parentArr: items, index: i };
            if (items[i].children && items[i].children.length > 0) {
                const found = findMenuItemById(id, items[i].children, items[i].children);
                if (found) return found;
            }
        }
        return null;
    }

    // --- Render ---
    function renderMenuEditor() {
        const container = document.getElementById('menu-editor-container');
        container.innerHTML = '';
        if (menuData.length === 0) {
            container.innerHTML = '<div class="text-center text-slate-400 text-sm py-8"><span class="material-symbols-outlined text-4xl block mb-2">menu_open</span>Chưa có menu nào.<br>Sử dụng cột bên trái để thêm mục.</div>';
            return;
        }

        menuData.forEach((item) => {
            container.appendChild(createMenuItemElement(item, 0));
        });

        // Init SortableJS on all containers (parent + children)
        initAllSortables();
    }

    function createMenuItemElement(item, depth) {
        const div = document.createElement('div');
        div.className = 'menu-sortable-item bg-white border border-slate-200 rounded-lg overflow-hidden shadow-sm transition-all duration-200';
        div.setAttribute('data-id', item.id);
        
        if (depth > 0) {
            div.classList.add('mt-1', 'border-l-4', 'border-l-primary/40');
            div.style.marginLeft = (depth * 28) + 'px';
        } else {
            div.classList.add('mb-2');
        }

        // Header bar
        const headerDiv = document.createElement('div');
        headerDiv.className = 'px-3 py-2.5 bg-slate-50 border-b border-slate-100 flex justify-between items-center cursor-pointer hover:bg-slate-100 transition-colors select-none';
        headerDiv.innerHTML = `
            <div class="flex items-center gap-2">
                <span class="drag-handle material-symbols-outlined text-slate-300 hover:text-primary cursor-grab active:cursor-grabbing text-[20px]" title="Kéo để di chuyển">drag_indicator</span>
                <span class="font-bold text-slate-700 text-sm">${item.title || '(Chưa có tên)'}</span>
                ${depth > 0 ? '<span class="text-[10px] bg-slate-200 px-1.5 py-0.5 rounded text-slate-500 font-medium">mục phụ</span>' : ''}
            </div>
            <div class="flex items-center gap-2">
                <span class="text-[11px] text-slate-400 font-medium">${item.type || 'Tùy chỉnh'}</span>
                <span class="material-symbols-outlined text-slate-400 transition-transform duration-300 transform toggle-icon pointer-events-none text-[18px]">arrow_drop_down</span>
            </div>
        `;

        // Body (collapsed by default)
        const bodyDiv = document.createElement('div');
        bodyDiv.className = 'p-4 bg-white hidden space-y-3 border-t border-slate-100';

        bodyDiv.innerHTML = `
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-[11px] font-black text-slate-400 uppercase tracking-widest pl-1 mb-1">Đường dẫn (URL)</label>
                    <input type="text" value="${item.url || ''}" data-field="url" class="menu-field w-full bg-white border-slate-200 rounded-lg px-3 py-2 text-sm focus:ring-primary/20 focus:border-primary transition-all text-slate-700">
                </div>
                <div>
                    <label class="block text-[11px] font-black text-slate-400 uppercase tracking-widest pl-1 mb-1">Tên hiển thị</label>
                    <input type="text" value="${item.title || ''}" data-field="title" class="menu-field w-full bg-white border-slate-200 rounded-lg px-3 py-2 text-sm focus:ring-primary/20 focus:border-primary transition-all text-slate-700">
                </div>
            </div>
            <div class="flex items-center justify-end pt-1 border-t border-slate-100">
                <button type="button" class="remove-btn text-red-500 text-xs hover:underline flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">delete</span> Xóa</button>
            </div>
        `;

        // Event: field change
        bodyDiv.querySelectorAll('.menu-field').forEach(input => {
            input.addEventListener('change', () => {
                const found = findMenuItemById(item.id);
                if (found) {
                    found.item[input.dataset.field] = input.value;
                    if (input.dataset.field === 'title') {
                        headerDiv.querySelector('.font-bold').textContent = input.value || '(Chưa có tên)';
                    }
                }
            });
        });

        // Event: remove
        bodyDiv.querySelector('.remove-btn').addEventListener('click', () => {
            if (confirm('Bạn có chắc muốn xóa mục "' + (item.title || '') + '"?')) {
                const found = findMenuItemById(item.id);
                if (found) {
                    found.parentArr.splice(found.index, 1);
                    renderMenuEditor();
                }
            }
        });

        // Event: toggle body
        headerDiv.addEventListener('click', (e) => {
            if (e.target.closest('.drag-handle') || e.target.closest('button')) return;
            bodyDiv.classList.toggle('hidden');
            headerDiv.querySelector('.toggle-icon').classList.toggle('rotate-180');
        });

        div.appendChild(headerDiv);
        div.appendChild(bodyDiv);

        // Children container - every item gets one so any item can accept children via drag
        const childrenContainer = document.createElement('div');
        childrenContainer.className = 'children-sortable px-1 min-h-[4px]';
        childrenContainer.setAttribute('data-parent-id', item.id);
        if (item.children && item.children.length > 0) {
            childrenContainer.classList.add('pt-1', 'pb-2', 'bg-slate-50/60');
            item.children.forEach(child => {
                childrenContainer.appendChild(createMenuItemElement(child, depth + 1));
            });
        }
        div.appendChild(childrenContainer);

        return div;
    }

    // --- SortableJS initialization ---
    function initAllSortables() {
        const mainContainer = document.getElementById('menu-editor-container');
        const sortableConfig = {
            group: 'menu-items',
            animation: 200,
            handle: '.drag-handle',
            ghostClass: 'sortable-ghost',
            chosenClass: 'sortable-chosen',
            dragClass: 'sortable-drag',
            fallbackOnBody: true,
            swapThreshold: 0.65,
            onEnd: function() { syncMenuDataFromDom(); renderMenuEditor(); }
        };

        // Parent level
        new Sortable(mainContainer, sortableConfig);

        // All children containers (any depth)
        mainContainer.querySelectorAll('.children-sortable').forEach(el => {
            new Sortable(el, sortableConfig);
        });
    }

    // --- Sync menuData from DOM order after drag (recursive) ---
    function syncMenuDataFromDom() {
        // Build flat lookup from current menuData
        const lookup = {};
        function flatCollect(items) {
            items.forEach(item => {
                lookup[item.id] = { id: item.id, title: item.title, url: item.url, type: item.type };
                if (item.children) flatCollect(item.children);
            });
        }
        flatCollect(menuData);

        // Recursively read the DOM tree
        function readChildren(containerEl) {
            const result = [];
            containerEl.querySelectorAll(':scope > .menu-sortable-item').forEach(el => {
                const id = el.getAttribute('data-id');
                const original = lookup[id];
                if (!original) return;
                const newItem = { ...original, children: [] };
                const childContainer = el.querySelector(':scope > .children-sortable');
                if (childContainer) {
                    newItem.children = readChildren(childContainer);
                }
                result.push(newItem);
            });
            return result;
        }

        const container = document.getElementById('menu-editor-container');
        menuData = readChildren(container);
    }

    function generateMenuId() {
        return Math.floor(Math.random() * 1000000000);
    }

    // --- Left panel functions ---
    function toggleWpAccordion(btn) {
        const content = btn.nextElementSibling;
        const icon = btn.querySelector('span');
        content.classList.toggle('hidden');
        icon.classList.toggle('rotate-180', !content.classList.contains('hidden'));
    }

    function toggleAllCheckboxes(masterCheckbox, className) {
        document.querySelectorAll('.' + className).forEach(cb => cb.checked = masterCheckbox.checked);
    }

    function addCustomLinkToMenu() {
        const urlInput = document.getElementById('wp_custom_url');
        const titleInput = document.getElementById('wp_custom_title');
        if (!titleInput.value.trim()) { alert('Vui lòng nhập tên đường dẫn.'); return; }
        menuData.push({ id: generateMenuId(), title: titleInput.value.trim(), url: urlInput.value.trim() || '#', type: 'Tùy chỉnh', children: [] });
        urlInput.value = 'http://';
        titleInput.value = '';
        renderMenuEditor();
    }

    function addCheckedItemsToMenu(checkboxClass, urlPrefix) {
        const checkboxes = document.querySelectorAll('.' + checkboxClass + ':checked');
        if (checkboxes.length === 0) return;
        checkboxes.forEach(cb => {
            menuData.push({ id: generateMenuId(), title: cb.getAttribute('data-name'), url: urlPrefix + cb.value, type: cb.getAttribute('data-type'), children: [] });
            cb.checked = false;
        });
        const container = checkboxes[0].closest('.wp-accordion');
        if (container) { const master = container.querySelector('input[onchange*="toggleAllCheckboxes"]'); if (master) master.checked = false; }
        renderMenuEditor();
    }

    // --- Form submit: build hidden inputs ---
    document.getElementById('settings-form').addEventListener('submit', function() {
        // Sync latest order from DOM before submitting
        syncMenuDataFromDom();

        const container = document.getElementById('menu-inputs-container');
        container.innerHTML = '';
        function buildInputs(data, parentName) {
            data.forEach((item, i) => {
                let p = `${parentName}[${i}]`;
                container.innerHTML += `<input type="hidden" name="${p}[id]" value="${item.id}">`;
                container.innerHTML += `<input type="hidden" name="${p}[title]" value="${item.title}">`;
                container.innerHTML += `<input type="hidden" name="${p}[url]" value="${item.url}">`;
                container.innerHTML += `<input type="hidden" name="${p}[type]" value="${item.type || 'Tùy chỉnh'}">`;
                if (item.children && item.children.length > 0) buildInputs(item.children, `${p}[children]`);
            });
        }
        buildInputs(menuData, 'settings_json[header_menu]');
    });

    document.addEventListener('DOMContentLoaded', () => renderMenuEditor());
</script>
@endpush

<style>
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in {
        animation: fadeIn 0.4s ease-out forwards;
    }
    @keyframes bounceShort {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-5px); }
    }
    .animate-bounce-short {
        animation: bounceShort 2s infinite;
    }
    .custom-scrollbar::-webkit-scrollbar {
        height: 3px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: #f1f5f9;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }
    .tab-btn {
        border-bottom: 2px solid transparent;
    }
    /* SortableJS Drag & Drop Styles */
    .sortable-ghost {
        opacity: 0.4;
        background: #e0f2fe !important;
        border: 2px dashed #38bdf8 !important;
        border-radius: 8px;
    }
    .sortable-chosen {
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        transform: scale(1.02);
        z-index: 999;
    }
    .sortable-drag {
        opacity: 0.9;
    }
    .drag-handle:hover {
        color: var(--primary, #f59e0b);
    }
</style>
@endsection
