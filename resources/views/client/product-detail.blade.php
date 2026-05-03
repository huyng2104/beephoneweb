@extends('client.layouts.app')
@section('title', 'Bee Phone - ' . $product->name)

@push('styles')
    <link rel="stylesheet" href="/css/comments.css">
@endpush

@section('content')
<style data-purpose="custom-styles">
    /* Đã xóa màu body cứng để ăn theo app.blade.php */
    .custom-shadow { box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05); }
    .dark .custom-shadow { box-shadow: 0 4px 20px rgba(0, 0, 0, 0.4); }
    
    .spec-row:nth-child(even) { background-color: #f8fafc; }
    .dark .spec-row:nth-child(even) { background-color: rgba(255, 255, 255, 0.02); }
    
    .thumb-scroll::-webkit-scrollbar { height: 4px; }
    .thumb-scroll::-webkit-scrollbar-thumb { background: #f4c025; border-radius: 10px; }
    
    .zoom-container { position: relative; overflow: hidden; cursor: crosshair; }
    .zoom-image { transition: transform 0.1s ease-out; width: 100%; height: 100%; object-fit: contain; }
    .zoom-container:hover .zoom-image { transform: scale(2); }

    .toast-notification {
        position: fixed; top: 20px; right: -300px; background: #10B981; color: white;
        padding: 15px 25px; border-radius: 10px; font-weight: bold; box-shadow: 0 5px 15px rgba(16, 185, 129, 0.3);
        transition: right 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55); z-index: 9999; display: flex; align-items: center; gap: 10px;
    }
    .toast-notification.show { right: 20px; }

    .btn-disabled { opacity: 0.5; cursor: not-allowed; filter: grayscale(100%); pointer-events: none; }
</style>

<div class="toast-notification z-[100]">
    <span class="material-symbols-outlined">check_circle</span>
    <span id="toast-message">Đã thêm vào giỏ hàng!</span>
</div>

<main class="max-w-[1440px] mx-auto px-4 md:px-10 lg:px-20 py-8 lg:py-12 relative min-h-screen">
    <nav class="flex text-sm text-gray-500 dark:text-gray-400 mb-6" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ url('/') }}" class="hover:text-primary transition-colors flex items-center">
                    <span class="material-symbols-outlined text-[18px] mr-1">home</span> Trang chủ
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <span class="material-symbols-outlined text-[16px] mx-1">chevron_right</span>
                    <a href="{{ route('client.products.index') }}" class="hover:text-primary transition-colors">{{ $product->categories->first()?->name ?? 'Sản phẩm' }}</a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <span class="material-symbols-outlined text-[16px] mx-1">chevron_right</span>
                    <span class="text-[#181611] dark:text-white font-bold truncate w-48 sm:w-auto">{{ $product->name }}</span>
                </div>
            </li>
        </ol>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <section class="lg:col-span-8" data-purpose="product-gallery">
            <div class="bg-white dark:bg-white/5 rounded-2xl p-6 border border-gray-100 dark:border-white/10 custom-shadow mb-4 sticky top-24">
                @php
                    $mainImg = $product->thumbnail ?? '';
                    $mainUrl = 'https://placehold.co/600x600/f8f9fa/1a1a1a?text=BeePhone';
                    if ($mainImg) {
                        $mainUrl = Str::startsWith($mainImg, ['http://', 'https://']) ? $mainImg : asset('storage/' . $mainImg);
                    }
                @endphp
                
                <div class="aspect-square flex items-center justify-center mb-6 rounded-xl bg-gray-50 dark:bg-black/20 p-4 border border-gray-100 dark:border-white/5 zoom-container" id="image-zoom-wrapper">
                    <img alt="{{ $product->name }}" class="zoom-image mix-blend-multiply dark:mix-blend-normal" id="main-product-image" src="{{ $mainUrl }}"/>
                </div>
                
                <div class="flex gap-4 overflow-x-auto pb-2 thumb-scroll">
                    <button class="thumb-btn flex-shrink-0 w-20 h-20 border-2 border-primary rounded-xl p-2 bg-white dark:bg-white/5 transition-colors">
                        <img alt="Thumb Main" class="w-full h-full object-contain mix-blend-multiply dark:mix-blend-normal" src="{{ $mainUrl }}"/>
                    </button>
                    
                    @if(isset($product->images) && $product->images->count() > 0)
                        @foreach($product->images as $gallery)
                            @php
                                $galImg = $gallery->image_path ?? $gallery->image ?? $gallery->path ?? ''; 
                                $galUrl = $galImg ? (Str::startsWith($galImg, ['http://', 'https://']) ? $galImg : asset('storage/' . $galImg)) : 'https://placehold.co/200x200';
                            @endphp
                            <button class="thumb-btn flex-shrink-0 w-20 h-20 border border-gray-200 dark:border-white/10 rounded-xl p-2 bg-white dark:bg-white/5 hover:border-primary dark:hover:border-primary transition-colors">
                                <img alt="Gallery" class="w-full h-full object-contain mix-blend-multiply dark:mix-blend-normal" src="{{ $galUrl }}"/>
                            </button>
                        @endforeach
                    @endif
                </div>
                

            </div>
        </section>

        <section class="lg:col-span-4" data-purpose="product-info-actions">
            <form action="#" method="POST" id="add-to-cart-form" class="flex flex-col gap-6">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <input type="hidden" name="variant_id" id="selected-variant-id" value="">

                <div class="bg-white dark:bg-white/5 p-6 rounded-2xl border border-gray-100 dark:border-white/10 custom-shadow relative overflow-hidden">
                    <div class="flex items-center justify-between mb-4">
                        <div class="text-xs font-bold text-gray-400">
                            SKU: <span id="display-sku" class="text-gray-600 dark:text-gray-300">{{ $product->sku ?? 'N/A' }}</span>
                        </div>
                    </div>

                    <h1 class="text-2xl lg:text-3xl font-bold text-[#181611] dark:text-white mb-1 pr-12">{{ $product->name }}</h1>
                    
                    <div class="flex flex-col gap-1 text-sm mb-5">
                        @if($product->brand)
                        <div class="flex items-center">
                            <span class="text-gray-500 mr-1.5">Thương hiệu:</span>
                            <span class="font-bold text-[#181611] dark:text-gray-200">{{ $product->brand->name }}</span>
                        </div>
                        @endif
                        
                        @if($product->categories->isNotEmpty())
                        <div class="flex items-center">
                            <span class="text-gray-500 mr-1.5">Danh mục:</span>
                            <span class="font-bold text-[#181611] dark:text-gray-200">
                                {{ $product->categories->pluck('name')->join(' | ') }}
                            </span>
                        </div>
                        @endif
                    </div>
                    
                    <div class="flex items-center gap-2 mb-4">
                        <div class="flex text-primary">
                            @for($i=0; $i<5; $i++) <span class="material-symbols-outlined text-[16px] ai-sparkle">star</span> @endfor
                        </div>
                        <span class="text-sm text-gray-300 dark:text-gray-600">|</span>
                        <span id="header-stock-status" class="text-xs text-green-600 bg-green-100 dark:bg-green-500/20 dark:text-green-400 px-2.5 py-1 rounded-full font-bold transition-all uppercase tracking-wider">
                            Đang kiểm tra...
                        </span>
                    </div>

                    <div class="flex items-baseline gap-4 mt-6 p-4 bg-gray-50 dark:bg-black/20 rounded-xl border border-gray-100 dark:border-white/5">
                        <span id="main-price" class="text-3xl font-bold text-red-500 dark:text-red-400 transition-opacity duration-200">
                            Đang cập nhật...
                        </span>
                        <span id="old-price" class="text-lg text-gray-400 line-through transition-opacity duration-200"></span>
                    </div>
                </div>

                <div class="bg-white dark:bg-white/5 p-6 rounded-2xl border border-gray-100 dark:border-white/10 custom-shadow space-y-6">
                    @php
                        $groupedAttributes = [];
                        $variantsJS = [];
                        
                        // Lấy tất cả biến thể ĐANG HOẠT ĐỘNG
                        $activeVariants = $product->variants->where('status', 'active');

                        if($product->type == 'variable' && $activeVariants->isNotEmpty()) {
                            foreach($activeVariants as $variant) {
                                $attrIds = [];
                                foreach($variant->attributeValues as $val) {
                                    $attrName = $val->attribute->name;
                                    $groupedAttributes[$attrName][$val->id] = $val->value;
                                    $attrIds[] = $val->id;
                                }
                                sort($attrIds);

                                $variantsJS[] = [
                                    'id' => $variant->id,
                                    'attributes' => $attrIds,
                                    'sku' => $variant->sku,
                                    'price' => $variant->price,
                                    'sale_price' => $variant->sale_price,
                                    'stock' => $variant->stock,
                                    'image' => $variant->thumbnail ? asset('storage/' . $variant->thumbnail) : null,
                                    'specs' => $variant->specifications->mapWithKeys(fn($s) => [$s->spec_key => $s->spec_value])
                                ];
                            }
                        }
                    @endphp

                    @if(!empty($groupedAttributes))
                        @foreach($groupedAttributes as $attrName => $values)
                            <div class="attr-group" data-name="{{ $attrName }}">
                                <p class="font-bold mb-3 text-[#181611] dark:text-white text-sm uppercase tracking-wider">{{ $attrName }}:</p>
                                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                                    @foreach($values as $valId => $valName)
                                        <button type="button" 
                                            class="attr-btn relative border-2 border-gray-100 dark:border-white/5 bg-white dark:bg-black/20 rounded-xl py-2.5 px-3 flex items-center justify-center transition-all duration-200 hover:border-[#f4c025]/50 overflow-hidden"
                                            data-id="{{ $valId }}">
                                            <div class="check-icon absolute bottom-0 right-0 bg-[#f4c025] w-5 h-5 rounded-tl-xl flex items-center justify-center hidden">
                                                <span class="material-symbols-outlined text-[13px] fonte-bold text-[#181611] mr-[1px] mb-[1px]">check</span>
                                            </div>
                                            <span class="block font-semibold text-sm text-gray-600 dark:text-gray-300 attr-text transition-colors relative z-10">{{ $valName }}</span>
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>

                <div class="bg-white dark:bg-white/5 p-6 rounded-2xl border border-gray-100 dark:border-white/10 custom-shadow">
                    <div class="flex items-center justify-between">
                        <p class="font-bold text-[#181611] dark:text-white">Số lượng:</p>
                        <div class="flex items-center border border-gray-200 dark:border-white/10 rounded-xl overflow-hidden focus-within:border-primary transition-colors bg-gray-50 dark:bg-black/20">
                            <button type="button" id="btn-minus" class="px-4 py-2 hover:bg-primary hover:text-black font-bold text-lg transition-colors">-</button>
                            <input type="number" id="input-qty" name="quantity" value="1" min="1" class="w-12 text-center border-0 focus:ring-0 p-2 font-bold bg-transparent text-[#181611] dark:text-white" readonly>
                            <button type="button" id="btn-plus" class="px-4 py-2 hover:bg-primary hover:text-black font-bold text-lg transition-colors">+</button>
                        </div>
                    </div>
                    <div class="flex items-center justify-between mt-3">
                        <span class="text-xs text-gray-400 dark:text-gray-500 flex items-center gap-1">
                            <span class="material-symbols-outlined text-[14px]">inventory_2</span>
                            Kho hàng
                        </span>
                        <span id="stock-display" class="text-xs font-bold flex items-center gap-1 transition-all">
                            <span id="stock-dot" class="inline-block w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                            <span id="stock-text" class="text-gray-400">Đang tải...</span>
                        </span>
                    </div>
                </div>

                <div class="flex flex-col gap-3 mt-2">
                    <button type="button" id="btn-buy-now" class="w-full bg-primary text-black font-bold py-4 rounded-xl shadow-lg transition-transform hover:scale-[1.02] flex flex-col items-center justify-center">
                        <span class="text-lg uppercase tracking-wider">Mua ngay</span>
                    </button>
                    <button type="button" id="btn-add-cart" class="w-full bg-[#181611] dark:bg-white dark:text-black text-white font-bold py-4 rounded-xl flex items-center justify-center gap-2 hover:bg-primary hover:text-black dark:hover:bg-primary transition-all shadow-md mt-2 group">
                        <span class="material-symbols-outlined group-hover:scale-110 transition-transform">add_shopping_cart</span>
                        THÊM VÀO GIỎ HÀNG
                    </button>
                </div>
            </form>
        </section>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 mt-12">
        <section class="lg:col-span-8 space-y-8">
            <div class="bg-white dark:bg-white/5 p-8 rounded-2xl border border-gray-100 dark:border-white/10 custom-shadow relative">
                <h2 class="text-2xl font-bold mb-6 pb-2 border-b-2 border-primary inline-flex items-center gap-2 uppercase text-[#181611] dark:text-white">
                    <span class="material-symbols-outlined text-primary">article</span> Đánh giá chi tiết
                </h2>
                <div class="prose prose-slate dark:prose-invert max-w-none text-gray-700 dark:text-gray-300 leading-relaxed overflow-hidden" id="product-content" style="max-height: 800px;">
                    {!! $product->description !!}
                </div>
                <div class="mt-8 text-center relative">
                    <div class="absolute bottom-full left-0 w-full h-32 bg-gradient-to-t from-white dark:from-[#221e10] to-transparent" id="content-gradient"></div>
                    <button id="read-more-btn" class="text-[#181611] dark:text-white font-bold px-8 py-3 rounded-full border-2 border-gray-200 dark:border-white/20 hover:border-primary hover:bg-primary hover:text-black transition-all relative z-10 bg-white dark:bg-[#221e10]">Xem thêm</button>
                </div>
            </div>
        </section>

        <section class="lg:col-span-4">
            <div class="bg-white dark:bg-white/5 p-6 rounded-2xl border border-gray-100 dark:border-white/10 custom-shadow sticky top-24">
                <h2 class="text-xl font-bold mb-6 pb-2 border-b-2 border-primary inline-flex items-center gap-2 uppercase text-[#181611] dark:text-white">
                    <span class="material-symbols-outlined text-primary">memory</span> Thông số kỹ thuật
                </h2>
                <div class="border border-gray-100 dark:border-white/10 rounded-xl overflow-hidden text-sm" id="specifications-table">
                    @php
                        $displaySpecs = collect();
                        if ($activeVariants->isNotEmpty()) {
                            $displaySpecs = $activeVariants->first()->specifications;
                        }
                    @endphp

                    @if($displaySpecs->isNotEmpty())
                        <div class="w-full">
                            @foreach($displaySpecs as $spec)
                            <div class="spec-row p-3 flex justify-between border-b border-gray-100 dark:border-white/5 last:border-0">
                                <span class="text-sm text-gray-500 dark:text-gray-400 w-1/3 font-medium">{{ $spec->spec_key }}:</span>
                                <span class="text-sm font-bold text-[#181611] dark:text-white text-right w-2/3">{{ $spec->spec_value }}</span>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="p-8 text-center flex flex-col items-center gap-2">
                            <span class="material-symbols-outlined text-4xl text-gray-300 dark:text-gray-600">inventory_2</span>
                            <span class="text-gray-400 dark:text-gray-500 italic">Đang cập nhật thông số...</span>
                        </div>
                    @endif
                </div>
            </div>
        </section>
    </div>

    {{-- ===== SẢN PHẨM TƯƠNG TỰ ===== --}}
    @if($relatedProducts->isNotEmpty() || $sameBrandProducts->isNotEmpty() || $fallbackProducts->isNotEmpty())
    <div class="mt-20" id="similar-products-section">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-8">
            <div>
                @if($fallbackProducts->isNotEmpty())
                    <p class="text-xs font-bold text-primary uppercase tracking-widest mb-1">Dành cho bạn</p>
                    <h2 class="text-2xl lg:text-3xl fonte-bold text-[#181611] dark:text-white">Có thể bạn thích</h2>
                @else
                    <p class="text-xs font-bold text-primary uppercase tracking-widest mb-1">Gợi ý cho bạn</p>
                    <h2 class="text-2xl lg:text-3xl fonte-bold text-[#181611] dark:text-white">Sản phẩm tương tự</h2>
                @endif
            </div>
            <a href="{{ route('client.products.index') }}"
               class="inline-flex items-center gap-1.5 text-sm font-bold text-gray-500 dark:text-gray-400 hover:text-primary dark:hover:text-primary transition-colors shrink-0">
                Xem tất cả
                <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
            </a>
        </div>

        {{-- Tabs (chỉ hiện nếu có cả 2 nguồn) --}}
        @if($relatedProducts->isNotEmpty() && $sameBrandProducts->isNotEmpty())
        <div class="flex gap-2 mb-6" id="similar-tabs">
            <button class="similar-tab-btn active px-5 py-2 rounded-full text-sm font-bold border-2 border-primary bg-primary text-black transition-all" data-tab="category">
                <span class="material-symbols-outlined text-[15px] align-middle mr-1" style="font-variation-settings:'FILL' 1">category</span>
                Cùng danh mục
            </button>
            <button class="similar-tab-btn px-5 py-2 rounded-full text-sm font-bold border-2 border-gray-200 dark:border-white/10 text-gray-500 dark:text-gray-400 hover:border-primary hover:text-primary transition-all" data-tab="brand">
                <span class="material-symbols-outlined text-[15px] align-middle mr-1" style="font-variation-settings:'FILL' 1">storefront</span>
                Cùng thương hiệu
            </button>
        </div>
        @endif

        {{-- Slider Cùng danh mục --}}
        @if($relatedProducts->isNotEmpty())
        <div class="similar-tab-panel" id="panel-category">
            <div class="relative">
                {{-- Prev/Next buttons --}}
                <button id="cat-prev" class="absolute -left-4 top-1/2 -translate-y-1/2 z-20 w-10 h-10 bg-white dark:bg-[#2a2412] rounded-full shadow-lg border border-gray-100 dark:border-white/10 flex items-center justify-center hover:bg-primary hover:border-primary hover:text-black transition-all opacity-0 pointer-events-none" aria-label="Trước">
                    <span class="material-symbols-outlined text-[20px]">chevron_left</span>
                </button>
                <button id="cat-next" class="absolute -right-4 top-1/2 -translate-y-1/2 z-20 w-10 h-10 bg-white dark:bg-[#2a2412] rounded-full shadow-lg border border-gray-100 dark:border-white/10 flex items-center justify-center hover:bg-primary hover:border-primary hover:text-black transition-all" aria-label="Tiếp">
                    <span class="material-symbols-outlined text-[20px]">chevron_right</span>
                </button>

                <div class="overflow-hidden rounded-2xl" id="cat-slider-wrapper">
                    <div class="flex gap-4 transition-transform duration-500 ease-in-out" id="cat-slider-track" style="width: max-content;">
                        @foreach($relatedProducts as $relProduct)
                        <div class="similar-card w-[220px] sm:w-[240px]">
                            @include('client.home.partials.product-card', ['product' => $relProduct])
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Dots --}}
                <div class="flex justify-center gap-2 mt-5" id="cat-dots"></div>
            </div>
        </div>
        @endif

        {{-- Slider Cùng thương hiệu --}}
        @if($sameBrandProducts->isNotEmpty())
        <div class="similar-tab-panel {{ $relatedProducts->isNotEmpty() ? 'hidden' : '' }}" id="panel-brand">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                @foreach($sameBrandProducts as $bProduct)
                    <div class="animate-fadeInUp" style="animation-delay: {{ $loop->index * 80 }}ms">
                        @include('client.home.partials.product-card', ['product' => $bProduct])
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Fallback: không có sản phẩm tương tự → hiển thị sản phẩm nổi bật --}}
        @if($fallbackProducts->isNotEmpty())
        <div id="panel-fallback">
            <div class="relative">
                {{-- Prev/Next buttons --}}
                <button id="fallback-prev" class="absolute -left-4 top-1/2 -translate-y-1/2 z-20 w-10 h-10 bg-white dark:bg-[#2a2412] rounded-full shadow-lg border border-gray-100 dark:border-white/10 flex items-center justify-center hover:bg-primary hover:border-primary hover:text-black transition-all opacity-0 pointer-events-none" aria-label="Trước">
                    <span class="material-symbols-outlined text-[20px]">chevron_left</span>
                </button>
                <button id="fallback-next" class="absolute -right-4 top-1/2 -translate-y-1/2 z-20 w-10 h-10 bg-white dark:bg-[#2a2412] rounded-full shadow-lg border border-gray-100 dark:border-white/10 flex items-center justify-center hover:bg-primary hover:border-primary hover:text-black transition-all" aria-label="Tiếp">
                    <span class="material-symbols-outlined text-[20px]">chevron_right</span>
                </button>

                <div class="overflow-hidden rounded-2xl" id="fallback-slider-wrapper">
                    <div class="flex gap-4 transition-transform duration-500 ease-in-out" id="fallback-slider-track" style="width: max-content;">
                        @foreach($fallbackProducts as $fbProduct)
                        <div class="similar-card w-[220px] sm:w-[240px]">
                            @include('client.home.partials.product-card', ['product' => $fbProduct])
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="flex justify-center gap-2 mt-5" id="fallback-dots"></div>
            </div>
        </div>
        @endif

    </div>
    @endif

    <style>
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .animate-fadeInUp { animation: fadeInUp 0.5s ease both; }

        .similar-tab-btn.active { border-color: #f4c025; background: #f4c025; color: #181611; }
        .similar-tab-btn:not(.active) { background: transparent; }

        #cat-slider-wrapper::-webkit-scrollbar { display: none; }

        .dot-btn { width: 8px; height: 8px; border-radius: 50%; background: #e5e7eb; transition: all .3s; }
        .dark .dot-btn { background: rgba(255,255,255,.15); }
        .dot-btn.active { background: #f4c025; width: 24px; border-radius: 8px; }
    </style>

    <script>
    (function() {
        // ===== TABS =====
        const tabBtns = document.querySelectorAll('.similar-tab-btn');
        const panels  = document.querySelectorAll('.similar-tab-panel');
        tabBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                tabBtns.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                const target = btn.dataset.tab;
                panels.forEach(p => {
                    if (p.id === 'panel-' + target) p.classList.remove('hidden');
                    else p.classList.add('hidden');
                });
            });
        });

        // ===== SLIDER CHO DANH MỤC =====
        const track   = document.getElementById('cat-slider-track');
        const wrapper = document.getElementById('cat-slider-wrapper');
        const prevBtn = document.getElementById('cat-prev');
        const nextBtn = document.getElementById('cat-next');
        const dotsContainer = document.getElementById('cat-dots');

        if (!track) return;

        const cards        = track.querySelectorAll('.similar-card');
        const totalCards   = cards.length;
        if (totalCards === 0) return;

        // Tính số card hiển thị dựa theo viewport
        function getVisible() {
            const w = window.innerWidth;
            if (w >= 1280) return 4;
            if (w >= 1024) return 3;
            if (w >= 640)  return 2;
            return 1;
        }

        let current  = 0;
        let visible  = getVisible();
        let maxIndex = Math.max(0, totalCards - visible);

        function cardWidth() {
            return cards[0] ? cards[0].offsetWidth + 16 : 256; // gap-4 = 16px
        }

        function buildDots() {
            dotsContainer.innerHTML = '';
            const pageCount = maxIndex + 1;
            if (pageCount <= 1) return;
            for (let i = 0; i < pageCount; i++) {
                const d = document.createElement('button');
                d.className = 'dot-btn' + (i === 0 ? ' active' : '');
                d.addEventListener('click', () => goTo(i));
                dotsContainer.appendChild(d);
            }
        }

        function updateDots() {
            dotsContainer.querySelectorAll('.dot-btn').forEach((d, i) => {
                d.classList.toggle('active', i === current);
            });
        }

        function updateButtons() {
            prevBtn.classList.toggle('opacity-0',          current === 0);
            prevBtn.classList.toggle('pointer-events-none', current === 0);
            nextBtn.classList.toggle('opacity-0',          current >= maxIndex);
            nextBtn.classList.toggle('pointer-events-none', current >= maxIndex);
        }

        function goTo(index) {
            current = Math.max(0, Math.min(index, maxIndex));
            track.style.transform = `translateX(-${current * cardWidth()}px)`;
            updateButtons();
            updateDots();
        }

        prevBtn.addEventListener('click', () => goTo(current - 1));
        nextBtn.addEventListener('click', () => goTo(current + 1));

        // Swipe support
        let startX = 0;
        wrapper.addEventListener('touchstart', e => { startX = e.touches[0].clientX; }, { passive: true });
        wrapper.addEventListener('touchend', e => {
            const dx = startX - e.changedTouches[0].clientX;
            if (Math.abs(dx) > 50) goTo(current + (dx > 0 ? 1 : -1));
        });

        function recalc() {
            visible  = getVisible();
            maxIndex = Math.max(0, totalCards - visible);
            current  = Math.min(current, maxIndex);
            buildDots();
            goTo(current);
        }

        window.addEventListener('resize', recalc);
        recalc();

        // Entrance animation cho các card
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.querySelectorAll('.similar-card').forEach((c, i) => {
                        c.style.opacity = '0';
                        c.style.transform = 'translateY(20px)';
                        c.style.transition = `opacity .4s ease ${i*80}ms, transform .4s ease ${i*80}ms`;
                        setTimeout(() => {
                            c.style.opacity = '1';
                            c.style.transform = 'translateY(0)';
                        }, 50);
                    });
                    observer.disconnect();
                }
            });
        }, { threshold: 0.1 });

        const section = document.getElementById('similar-products-section');
        if (section) observer.observe(section);

        // ===== SLIDER FALLBACK (cùng logic, ID khác) =====
        (function initFallbackSlider() {
            const fTrack   = document.getElementById('fallback-slider-track');
            const fWrapper = document.getElementById('fallback-slider-wrapper');
            const fPrev    = document.getElementById('fallback-prev');
            const fNext    = document.getElementById('fallback-next');
            const fDots    = document.getElementById('fallback-dots');
            if (!fTrack) return;

            const fCards = fTrack.querySelectorAll('.similar-card');
            if (fCards.length === 0) return;

            function fGetVisible() {
                const w = window.innerWidth;
                if (w >= 1280) return 4;
                if (w >= 1024) return 3;
                if (w >= 640)  return 2;
                return 1;
            }

            let fCur = 0, fVis = fGetVisible(), fMax = Math.max(0, fCards.length - fVis);
            const fCardW = () => fCards[0] ? fCards[0].offsetWidth + 16 : 256;

            function fBuildDots() {
                fDots.innerHTML = '';
                if (fMax < 1) return;
                for (let i = 0; i <= fMax; i++) {
                    const d = document.createElement('button');
                    d.className = 'dot-btn' + (i === 0 ? ' active' : '');
                    d.addEventListener('click', () => fGoTo(i));
                    fDots.appendChild(d);
                }
            }
            function fGoTo(i) {
                fCur = Math.max(0, Math.min(i, fMax));
                fTrack.style.transform = `translateX(-${fCur * fCardW()}px)`;
                fPrev.classList.toggle('opacity-0', fCur === 0);
                fPrev.classList.toggle('pointer-events-none', fCur === 0);
                fNext.classList.toggle('opacity-0', fCur >= fMax);
                fNext.classList.toggle('pointer-events-none', fCur >= fMax);
                fDots.querySelectorAll('.dot-btn').forEach((d, idx) => d.classList.toggle('active', idx === fCur));
            }

            fPrev.addEventListener('click', () => fGoTo(fCur - 1));
            fNext.addEventListener('click', () => fGoTo(fCur + 1));

            let fStartX = 0;
            fWrapper.addEventListener('touchstart', e => { fStartX = e.touches[0].clientX; }, { passive: true });
            fWrapper.addEventListener('touchend', e => {
                const dx = fStartX - e.changedTouches[0].clientX;
                if (Math.abs(dx) > 50) fGoTo(fCur + (dx > 0 ? 1 : -1));
            });

            function fRecalc() {
                fVis = fGetVisible();
                fMax = Math.max(0, fCards.length - fVis);
                fCur = Math.min(fCur, fMax);
                fBuildDots();
                fGoTo(fCur);
            }
            window.addEventListener('resize', fRecalc);
            fRecalc();
        })();
    })();
    </script>


    {{-- ===== ĐÁNH GIÁ KHÁCH HÀNG ===== --}}
    <div class="mt-16" id="reviews-section">
        @php
            $avgRating     = $reviews->avg('rating') ?? 0;
            $totalReviews  = $reviews->count();
            $ratingLabels  = [5 => 'Tuyệt vời', 4 => 'Tốt', 3 => 'Bình thường', 2 => 'Tệ', 1 => 'Rất tệ'];
        @endphp

        {{-- ===== TOAST NOTIFICATIONS ===== --}}
        @if(session('success'))
        <div id="review-toast-success"
             class="flex items-center gap-3 mb-5 px-5 py-4 bg-green-50 border border-green-200 text-green-800 rounded-2xl shadow-sm
                    opacity-0 translate-y-2 transition-all duration-500">
            <span class="material-symbols-outlined text-green-500 text-[22px] flex-shrink-0">check_circle</span>
            <p class="text-sm font-semibold">{{ session('success') }}</p>
            <button onclick="this.parentElement.remove()" class="ml-auto text-green-400 hover:text-green-600 transition-colors">
                <span class="material-symbols-outlined text-[18px]">close</span>
            </button>
        </div>
        @endif

        @if(session('error'))
        <div id="review-toast-error"
             class="flex items-center gap-3 mb-5 px-5 py-4 bg-red-50 border border-red-200 text-red-700 rounded-2xl shadow-sm
                    opacity-0 translate-y-2 transition-all duration-500">
            <span class="material-symbols-outlined text-red-500 text-[22px] flex-shrink-0">error</span>
            <p class="text-sm font-semibold">{{ session('error') }}</p>
            <button onclick="this.parentElement.remove()" class="ml-auto text-red-400 hover:text-red-600 transition-colors">
                <span class="material-symbols-outlined text-[18px]">close</span>
            </button>
        </div>
        @endif

        @if($errors->has('review'))
        <div id="review-toast-validation"
             class="flex items-center gap-3 mb-5 px-5 py-4 bg-amber-50 border border-amber-200 text-amber-800 rounded-2xl shadow-sm
                    opacity-0 translate-y-2 transition-all duration-500">
            <span class="material-symbols-outlined text-amber-500 text-[22px] flex-shrink-0">warning</span>
            <p class="text-sm font-semibold">{{ $errors->first('review') }}</p>
            <button onclick="this.parentElement.remove()" class="ml-auto text-amber-400 hover:text-amber-600 transition-colors">
                <span class="material-symbols-outlined text-[18px]">close</span>
            </button>
        </div>
        @endif

        {{-- ===== HEADER: Tiêu đề + Rating Box + Bars ===== --}}
        <h2 class="text-xl font-bold text-[#181611] dark:text-white mb-5">
            Đánh giá {{ $product->name }}
        </h2>

        <div class="bg-white dark:bg-white/5 rounded-2xl border border-gray-100 dark:border-white/10 p-6 mb-6 custom-shadow">
            <div class="flex flex-col sm:flex-row gap-8 items-start">

                {{-- Điểm trung bình + Sao + Nút --}}
                <div class="flex flex-col items-center gap-3 min-w-[140px]">
                    <div class="text-center">
                        <p class="text-6xl font-bold text-[#181611] dark:text-white leading-none">
                            {{ $totalReviews > 0 ? number_format($avgRating, 1) : '—' }}
                            @if($totalReviews > 0)<span class="text-2xl font-bold text-gray-400">/5</span>@endif
                        </p>
                        <div class="flex justify-center gap-0.5 mt-2">
                            @for($i = 1; $i <= 5; $i++)
                                <span class="material-symbols-outlined text-[22px] {{ $i <= round($avgRating) ? 'text-primary' : 'text-gray-300 dark:text-gray-600' }}"
                                      style="font-variation-settings:'FILL' 1">star</span>
                            @endfor
                        </div>
                        <p class="text-sm text-gray-400 mt-1">{{ $totalReviews }} lượt đánh giá</p>
                    </div>

                    {{-- Nút hành động --}}
                    <div class="text-center mt-2">
                        <p class="text-xs text-gray-400 leading-relaxed italic">
                            Tính năng đánh giá chỉ dành cho<br>khách hàng đã mua sản phẩm này.
                        </p>
                    </div>
                </div>

                {{-- Thanh bars 5→1 sao --}}
                <div class="flex-1 flex flex-col gap-2 justify-center">
                    @foreach($ratingBreakdown as $star => $count)
                    @php $pct = $totalReviews > 0 ? round(($count / $totalReviews) * 100) : 0; @endphp
                    <div class="flex items-center gap-3 text-sm">
                        <span class="flex items-center gap-0.5 w-10 justify-end flex-shrink-0">
                            <span class="font-bold text-gray-700 dark:text-gray-300">{{ $star }}</span>
                            <span class="material-symbols-outlined text-[14px] text-primary" style="font-variation-settings:'FILL' 1">star</span>
                        </span>
                        <div class="flex-1 h-3 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
                            <div class="h-full bg-[#f4c025] rounded-full transition-all duration-700"
                                 style="width: {{ $pct }}%"></div>
                        </div>
                        <span class="text-xs text-gray-400 w-16 flex-shrink-0">{{ $count }} đánh giá</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- ===== FILTER PILLS ===== --}}
        <div class="flex flex-wrap gap-2 mb-6" id="review-filters">
            <button class="review-filter-btn active px-4 py-1.5 rounded-full border text-sm font-semibold
                           border-[#f4c025] text-[#181611] bg-[#f4c025] transition-all shadow-[0_4px_10px_-2px_rgba(244,192,37,0.4)]"
                    data-filter="all">Tất cả</button>
            <button class="review-filter-btn px-4 py-1.5 rounded-full border text-sm font-semibold
                           border-gray-200 text-gray-600 bg-white hover:border-[#f4c025] hover:text-[#f4c025] hover:bg-[#f4c025]/5 transition-all dark:bg-white/5 dark:border-white/10 dark:text-gray-300 dark:hover:border-[#f4c025] dark:hover:text-[#f4c025]"
                    data-filter="has-image">Có hình ảnh</button>
            {{-- <button class="review-filter-btn px-4 py-1.5 rounded-full border text-sm font-semibold
                           border-gray-200 text-gray-600 bg-white hover:border-[#f4c025] hover:text-[#f4c025] hover:bg-[#f4c025]/5 transition-all dark:bg-white/5 dark:border-white/10 dark:text-gray-300"
                    data-filter="purchased">Đã mua hàng</button> --}}
            @foreach([5,4,3,2,1] as $star)
            <button class="review-filter-btn px-4 py-1.5 rounded-full border text-sm font-semibold
                           border-gray-200 text-gray-600 bg-white hover:border-[#f4c025] hover:text-[#f4c025] hover:bg-[#f4c025]/5 transition-all dark:bg-white/5 dark:border-white/10 dark:text-gray-300 dark:hover:border-[#f4c025] dark:hover:text-[#f4c025]"
                    data-filter="star-{{ $star }}">
                {{ $star }} sao
            </button>
            @endforeach
        </div>

        {{-- ===== DANH SÁCH ĐÁNH GIÁ ===== --}}
        @if($reviews->isNotEmpty())
            <div class="flex flex-col" id="review-list">
                @foreach($reviews as $review)
                <div class="review-card py-6 border-b border-gray-100 dark:border-white/10 last:border-0"
                     id="review-{{ $review->id }}"
                     data-rating="{{ $review->rating }}"
                     data-has-image="{{ $review->images->isNotEmpty() ? '1' : '0' }}"
                     data-purchased="{{ $review->is_purchased ? '1' : '0' }}">

                    <div class="flex items-start gap-4">
                        {{-- Avatar --}}
                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-white text-sm font-bold flex-shrink-0"
                             style="background-color: #2d6a4f;">
                            {{ mb_strtoupper(mb_substr($review->user?->name ?? 'A', 0, 1)) }}
                        </div>

                        <div class="flex-1 min-w-0">
                            {{-- Tên + Badge --}}
                            <div class="flex flex-wrap items-center gap-2 mb-1">
                                <span class="font-bold text-sm text-[#181611] dark:text-white">
                                    {{ $review->user?->name ?? 'Khách ẩn danh' }}
                                    @if(auth()->id() === $review->user_id)
                                        <span class="text-xs font-normal text-gray-400 ml-1">(Bạn)</span>
                                    @endif
                                </span>
                                {{-- @if($review->is_purchased)
                                    <span class="inline-flex items-center gap-0.5 px-2 py-0.5 bg-green-50 border border-green-200 text-green-700 text-[10px] font-bold rounded-full">
                                        <span class="material-symbols-outlined text-[11px]">verified</span> Đã mua hàng
                                    </span>
                                @endif --}}

                            </div>

                            {{-- Sao + Label + Thời gian --}}
                            <div class="flex items-center gap-2 mb-3">
                                <span class="flex gap-0.5">
                                    @for($i = 1; $i <= 5; $i++)
                                        <span class="material-symbols-outlined text-[15px] {{ $i <= $review->rating ? 'text-primary' : 'text-gray-200 dark:text-gray-600' }}"
                                              style="font-variation-settings:'FILL' 1">star</span>
                                    @endfor
                                </span>
                                <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                                    {{ $ratingLabels[$review->rating] ?? '' }}
                                </span>
                            </div>

                            {{-- Nội dung --}}
                            <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed mb-3">{{ $review->comment }}</p>

                            {{-- Ảnh đính kèm --}}
                            @if($review->images->isNotEmpty())
                                <div class="flex gap-2 flex-wrap mb-3">
                                    @foreach($review->images as $img)
                                        <a href="{{ asset('storage/' . $img->image_path) }}" target="_blank">
                                            <img src="{{ asset('storage/' . $img->image_path) }}"
                                                 class="w-16 h-16 object-cover rounded-xl border border-gray-100 dark:border-white/10 hover:opacity-80 transition-opacity">
                                        </a>
                                    @endforeach
                                </div>
                            @endif

                            {{-- Thời gian & Nút Like --}}
                            <div class="flex items-center gap-6 mt-1 mb-2">
                                <p class="text-xs text-gray-400 flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[13px]">schedule</span>
                                    Đánh giá đã đăng vào {{ $review->created_at->diffForHumans() }}
                                </p>
                                @php
                                    $isLiked = session()->has('liked_review_' . $review->id);
                                @endphp
                                <button type="button" class="btn-helpful text-xs font-semibold {{ $isLiked ? 'text-primary' : 'text-gray-400' }} hover:text-primary transition-colors flex items-center gap-1" data-url="{{ route('reviews.helpful', $review->id) }}">
                                    <span class="material-symbols-outlined text-[14px]">thumb_up</span>
                                    Hữu ích (<span class="helpful-count">{{ $review->helpful_count }}</span>)
                                </button>
                            </div>

                            {{-- Phản hồi Admin --}}
                            @if($review->hasReply())
                                <div class="mt-3 pl-4 border-l-2 border-primary/50 bg-primary/5 dark:bg-primary/10 rounded-r-xl py-2.5 pr-3">
                                    <p class="text-xs font-bold text-primary flex items-center gap-1 mb-1">
                                        <span class="material-symbols-outlined text-[13px]">support_agent</span>
                                        Bee Phone đã phản hồi
                                        @if($review->replied_at)
                                            <span class="font-normal text-gray-400">• {{ $review->replied_at->diffForHumans() }}</span>
                                        @endif
                                    </p>
                                    <p class="text-sm text-gray-700 dark:text-gray-300">{{ $review->reply_comment }}</p>
                                </div>
                            @endif
                        </div>

                        {{-- Nút xóa (chỉ user đó thấy, chưa duyệt và trong 15 phút) --}}
                        @if(auth()->id() === $review->user_id && !$review->isApproved() && $review->created_at->diffInMinutes(now()) <= 15)
                            <form action="{{ route('reviews.destroy', $review->id) }}" method="POST"
                                  onsubmit="return confirm('Xóa đánh giá này? Không thể hoàn tác.')">
                                @csrf @method('DELETE')
                                <button type="submit" title="Xóa đánh giá"
                                        class="p-1.5 text-gray-300 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors">
                                    <span class="material-symbols-outlined text-[18px]">delete</span>
                                </button>
                            </form>
                        @endif                    </div>
                </div>
                @endforeach

                {{-- Không có kết quả sau filter --}}
                <div id="no-filter-result" class="hidden text-center py-10 text-gray-400">
                    <span class="material-symbols-outlined text-4xl text-gray-200">filter_list_off</span>
                    <p class="mt-2 text-sm">Không có đánh giá phù hợp với bộ lọc.</p>
                </div>
            </div>
        @else
            <div class="bg-white dark:bg-white/5 rounded-2xl p-12 border border-gray-100 dark:border-white/10 text-center custom-shadow">
                <span class="material-symbols-outlined text-5xl text-gray-300 dark:text-gray-600">rate_review</span>
                <p class="mt-3 text-gray-400 dark:text-gray-500">Chưa có đánh giá nào. Hãy là người đầu tiên đánh giá!</p>
            </div>
        @endif
    </div>

    {{-- ===== HỎI VÀ ĐÁP (Q&A) ===== --}}
    <div class="mt-16" id="qa-section">
        <div class="bg-white dark:bg-white/5 rounded-2xl border border-gray-100 dark:border-white/10 p-6 custom-shadow">
            <h2 class="text-xl font-bold text-[#181611] dark:text-white mb-4">Hỏi và đáp</h2>
            
            @auth
            <form action="{{ route('products.comments.store', $product->id) }}" method="POST" class="mb-8 block">
                @csrf
                <div class="flex flex-col gap-3">
                    <textarea name="content" rows="3" required placeholder="Xin mời để lại câu hỏi, BeePhone sẽ phản hồi trong 1 giờ..."
                              class="w-full border border-gray-200 dark:border-white/10 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-[#f4c025] outline-none resize-none dark:bg-gray-800 dark:text-white transition-all"></textarea>

                    <div class="flex justify-end mt-1">
                        <button type="submit" class="px-6 py-2.5 bg-[#f4c025] text-[#181611] font-bold rounded-xl hover:brightness-105 transition-all text-sm shadow-[0_4px_14px_0_rgba(244,192,37,0.39)]">
                            <span class="material-symbols-outlined text-[16px] inline-block align-middle mr-1" style="font-variation-settings: 'FILL' 1;">send</span> Gửi câu hỏi
                        </button>
                    </div>
                </div>
            </form>
            @endauth
            @guest
            <div class="mb-8 block relative cursor-pointer" onclick="showLoginModal()">
                <div class="absolute inset-0 z-10" title="Đăng nhập để bình luận"></div>
                <div class="flex flex-col gap-3">
                    <textarea rows="3" placeholder="Xin mời để lại câu hỏi, BeePhone sẽ phản hồi trong 1 giờ..." readonly
                              class="w-full border border-gray-200 dark:border-white/10 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-[#f4c025] outline-none resize-none dark:bg-gray-800 dark:text-gray-400 bg-gray-50 transition-all"></textarea>
                    
                    <div class="flex justify-end mt-1">
                        <button type="button" class="px-6 py-2.5 bg-gray-300 dark:bg-gray-700 text-gray-500 font-bold rounded-xl pointer-events-none text-sm transition-all shadow-none">
                            <span class="material-symbols-outlined text-[16px] inline-block align-middle mr-1" style="font-variation-settings: 'FILL' 1;">send</span> Gửi câu hỏi
                        </button>
                    </div>
                </div>
            </div>
            @endguest

            <div class="flex flex-col">
                @forelse($comments as $comment)
                    <div class="py-6 border-b border-gray-100 dark:border-white/10 last:border-0 transition-all">
                        <div class="flex gap-4">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold bg-[#181611] text-[#f4c025] shrink-0 shadow-inner">
                                {{ mb_strtoupper(mb_substr($comment->user_id ? ($comment->user->name ?? 'K') : ($comment->guest_name ?: 'K'), 0, 1)) }}
                            </div>
                            <div class="flex-1 min-w-0 text-sm">
                                <div class="flex flex-wrap items-center gap-2 mb-1">
                                    <span class="font-bold text-[#181611] dark:text-white break-words">{{ $comment->user_id ? ($comment->user->name ?? 'Khách') : ($comment->guest_name ?: 'Khách') }}</span>
                                    @if($comment->user && $comment->user->role && $comment->user->role->name === 'admin')
                                        <span class="bg-[#f4c025] text-[#181611] text-[10px] font-bold px-1.5 py-0.5 rounded-sm uppercase tracking-wide">QTV</span>
                                    @endif
                                    <span class="text-xs text-gray-400 whitespace-nowrap">&bull; {{ $comment->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="text-gray-700 dark:text-gray-300 leading-relaxed mb-2 break-words">{{ $comment->content }}</p>
                                <div class="flex items-center gap-4 mt-2">
                                    <button type="button" class="text-xs text-gray-500 font-semibold hover:text-[#181611] dark:hover:text-[#f4c025] flex items-center gap-1 whitespace-nowrap transition-colors w-fit" onclick="@auth toggleReplyForm({{ $comment->id }}) @else showLoginModal() @endauth">
                                        <span class="material-symbols-outlined text-[14px]">reply</span> Phản hồi
                                    </button>
                                    @if(auth()->check() && (auth()->id() === $comment->user_id || (auth()->user()->role && auth()->user()->role->name === 'admin')))
                                    <form action="{{ route('products.comments.destroy', $comment->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa không? Hành động này không thể hoàn tác.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" title="Xóa bình luận" class="text-xs text-red-400 font-semibold hover:text-red-600 flex items-center gap-1 whitespace-nowrap transition-colors w-fit">
                                            <span class="material-symbols-outlined text-[14px]">delete</span> Xóa
                                        </button>
                                    </form>
                                    @endif
                                </div>

                                @auth
                                {{-- Form Reply Cho User --}}
                                <form action="{{ route('products.comments.store', $product->id) }}" method="POST" id="reply-form-{{ $comment->id }}" class="hidden mt-4 bg-[#f9f8f5] dark:bg-white/5 p-4 rounded-xl border border-[#e6e3db] dark:border-white/10 w-full">
                                    @csrf
                                    <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                                    <div class="flex flex-col gap-3">
                                        <textarea name="content" rows="2" required placeholder="Nhập câu trả lời..."
                                                  class="w-full border border-[#e6e3db] dark:border-white/10 rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-[#f4c025] outline-none resize-none dark:bg-gray-800 dark:text-white text-xs"></textarea>
                                        <div class="flex justify-end gap-2 mt-1">
                                            <button type="button" class="px-3 py-1.5 text-xs text-gray-500 font-medium hover:bg-white dark:hover:bg-white/5 rounded-lg transition" onclick="toggleReplyForm({{ $comment->id }})">Huỷ</button>
                                            <button type="submit" class="px-4 py-1.5 text-xs bg-[#f4c025] text-[#181611] font-semibold rounded-lg hover:brightness-105 transition shadow-sm">Gửi</button>
                                        </div>
                                    </div>
                                </form>
                                @endauth

                                {{-- Các câu trả lời --}}
                                @if($comment->replies->count() > 0)
                                    <div class="mt-4 flex flex-col gap-4 bg-[#f9f8f5] dark:bg-white/5 rounded-xl border border-[#e6e3db] dark:border-white/10 p-4">
                                        @foreach($comment->replies as $reply)
                                        <div class="flex gap-4">
                                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold shrink-0 {{ ($reply->user && $reply->user->role && $reply->user->role->name === 'admin') ? 'bg-[#181611] text-[#f4c025] shadow-inner' : 'bg-gray-400' }}">
                                                {{ mb_strtoupper(mb_substr($reply->user_id ? ($reply->user->name ?? 'K') : ($reply->guest_name ?: 'K'), 0, 1)) }}
                                            </div>
                                            <div class="flex-1 min-w-0 text-sm">
                                                <div class="flex flex-wrap items-center gap-2 mb-1">
                                                    <span class="font-bold text-[#181611] dark:text-white break-words">{{ $reply->user_id ? ($reply->user->name ?? 'Khách') : ($reply->guest_name ?: 'Khách') }}</span>
                                                    @if($reply->user && $reply->user->role && $reply->user->role->name === 'admin')
                                                        <span class="bg-[#f4c025] text-[#181611] text-[10px] font-bold px-1.5 py-0.5 rounded-sm uppercase tracking-wide">QTV</span>
                                                    @endif
                                                    <span class="text-xs text-gray-400 whitespace-nowrap">&bull; {{ $reply->created_at->diffForHumans() }}</span>
                                                </div>
                                                <p class="text-gray-700 dark:text-gray-300 leading-relaxed break-words">{{ $reply->content }}</p>
                                                <div class="flex items-center gap-4 mt-1.5">
                                                    <button type="button" class="text-xs text-gray-500 font-semibold hover:text-[#181611] dark:hover:text-[#f4c025] flex items-center gap-1 whitespace-nowrap transition-colors w-fit" onclick="@auth focusReplyForm({{ $comment->id }}, '{{ $reply->user_id ? ($reply->user->name ?? 'Khách') : ($reply->guest_name ?: 'Khách') }}') @else showLoginModal() @endauth">
                                                        <span class="material-symbols-outlined text-[14px]">reply</span> Phản hồi
                                                    </button>
                                                    @if(auth()->check() && (auth()->id() === $reply->user_id || (auth()->user()->role && auth()->user()->role->name === 'admin')))
                                                    <form action="{{ route('products.comments.destroy', $reply->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa phản hồi này không? Hành động này không thể hoàn tác.');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" title="Xóa phản hồi" class="text-xs text-red-400 font-semibold hover:text-red-600 flex items-center gap-1 whitespace-nowrap transition-colors w-fit">
                                                            <span class="material-symbols-outlined text-[14px]">delete</span> Xóa
                                                        </button>
                                                    </form>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                @endif
                                
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-6 text-gray-400">
                        <span class="material-symbols-outlined text-4xl mb-2 text-gray-300 dark:text-gray-600">forum</span>
                        <p class="text-sm">Chưa có câu hỏi nào. Hãy là người đầu tiên đặt câu hỏi!</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
    
    {{-- Login Modal --}}
    <div id="login-modal-overlay" class="fixed inset-0 bg-black/60 z-[100] hidden flex items-center justify-center opacity-0 transition-opacity duration-300 backdrop-blur-sm">
        <div class="bg-white dark:bg-[#221e10] p-8 rounded-3xl shadow-2xl max-w-sm w-full mx-4 transform scale-95 transition-transform duration-300 relative border border-gray-100 dark:border-white/10">
            <button onclick="hideLoginModal()" class="absolute top-4 right-4 text-gray-400 hover:text-red-500 transition-colors bg-gray-50 dark:bg-white/5 rounded-full p-1.5 focus:outline-none">
                <span class="material-symbols-outlined text-xl block">close</span>
            </button>
            <div class="text-center">
                <div class="w-20 h-20 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-5 text-primary">
                    <span class="material-symbols-outlined text-4xl">lock</span>
                </div>
                <h3 class="text-2xl font-bold text-[#181611] dark:text-white mb-2">Yêu cầu đăng nhập</h3>
                <p class="text-gray-500 dark:text-gray-400 text-sm mb-8 leading-relaxed">Hãy đăng nhập để sử dụng chức năng này và tham gia thảo luận cùng cộng đồng Bee Phone.</p>
                <div class="flex flex-col gap-3">
                    <a href="{{ route('login') }}" class="w-full bg-[#f4c025] text-[#181611] font-bold py-3.5 rounded-xl hover:brightness-105 transition-all shadow-[0_4px_14px_0_rgba(244,192,37,0.39)] inline-block">Đăng nhập ngay</a>
                    <button onclick="hideLoginModal()" class="w-full bg-gray-100 dark:bg-white/5 text-gray-600 dark:text-gray-300 font-bold py-3.5 rounded-xl hover:bg-gray-200 dark:hover:bg-white/10 transition-colors">Để sau</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showLoginModal() {
            const modal = document.getElementById('login-modal-overlay');
            const modalBox = modal.querySelector('div');
            modal.classList.remove('hidden');
            // Trigger reflow
            void modal.offsetWidth;
            modal.classList.remove('opacity-0');
            modalBox.classList.remove('scale-95');
        }

        function hideLoginModal() {
            const modal = document.getElementById('login-modal-overlay');
            const modalBox = modal.querySelector('div');
            modal.classList.add('opacity-0');
            modalBox.classList.add('scale-95');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }

        function toggleReplyForm(id) {
            const form = document.getElementById('reply-form-' + id);
            if (form.classList.contains('hidden')) {
                form.classList.remove('hidden');
                form.querySelector('textarea').focus();
            } else {
                form.classList.add('hidden');
            }
        }

        function focusReplyForm(commentId, authorName) {
            const form = document.getElementById('reply-form-' + commentId);
            if (form.classList.contains('hidden')) {
                form.classList.remove('hidden');
            }
            const textarea = form.querySelector('textarea');
            const prefix = '@' + authorName + ' ';
            if (!textarea.value.includes(prefix)) {
                textarea.value = prefix + textarea.value;
            }
            textarea.focus();
            // Đưa con trỏ xuống cuối
            textarea.setSelectionRange(textarea.value.length, textarea.value.length);
        }
    </script>
</main>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        // --- LOGIC ĐỔI ẢNH GALLERY ---
        const mainImage = document.getElementById('main-product-image');
        const thumbBtns = document.querySelectorAll('.thumb-btn'); 

        thumbBtns.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                thumbBtns.forEach(b => { 
                    b.classList.remove('border-primary', 'border-2'); 
                    b.classList.add('border-gray-200', 'dark:border-white/10', 'border'); 
                });
                this.classList.remove('border-gray-200', 'dark:border-white/10', 'border'); 
                this.classList.add('border-primary', 'border-2');
                
                mainImage.style.opacity = '0.3';
                setTimeout(() => { mainImage.src = this.querySelector('img').src; mainImage.style.opacity = '1'; }, 150);
            });
        });

        // --- DỮ LIỆU TỪ SERVER SANG JS ---
        @php
            $basePrice = 0;
            $baseSalePrice = 0;
            $baseStock = 0;
            $baseVariantId = '';
            
            if ($activeVariants->isNotEmpty()) {
                $firstVar = $activeVariants->first();
                $basePrice = $firstVar->price;
                $baseSalePrice = $firstVar->sale_price;
                $baseStock = $firstVar->stock;
                $baseVariantId = $firstVar->id;
            }
        @endphp
        const productType = "{{ $product->type }}";
        const basePrice = {{ $basePrice }};
        const baseSalePrice = {{ $baseSalePrice ?? 0 }};
        const baseStock = {{ $baseStock }};
        const csrfToken = '{{ csrf_token() }}';
        
        const variantsList = @json($variantsJS ?? []);
        
        const priceEl = document.getElementById('main-price');
        const oldPriceEl = document.getElementById('old-price');
        const skuEl = document.getElementById('display-sku');
        const stockStatusEl = document.getElementById('header-stock-status');
        const stockTextEl = document.getElementById('stock-text');
        const specsWrapper = document.getElementById('specifications-table');
        const inputVariantId = document.getElementById('selected-variant-id');
        const inputQty = document.getElementById('input-qty');
        
        const btnBuyNow = document.getElementById('btn-buy-now');
        const btnAddCart = document.getElementById('btn-add-cart');

        let currentMaxStock = baseStock;

        function formatCurrency(num) {
            return new Intl.NumberFormat('vi-VN').format(num) + 'đ';
        }

        function updateUI(price, salePrice, stock, image, variantId, sku, specs) {
            const finalPrice = (salePrice > 0 && salePrice < price) ? salePrice : price;
            priceEl.textContent = formatCurrency(finalPrice);
            
            if (salePrice > 0 && salePrice < price) {
                oldPriceEl.textContent = formatCurrency(price);
            } else {
                oldPriceEl.textContent = '';
            }

            if (skuEl) skuEl.textContent = sku || '{{ $product->sku }}';

            currentMaxStock = stock;
            
            const stockDotEl = document.getElementById('stock-dot');

            if(stock > 10) {
                // Còn nhiều hàng
                stockStatusEl.textContent = 'Còn hàng';
                stockStatusEl.className = 'text-xs text-green-600 bg-green-100 dark:bg-green-500/20 dark:text-green-400 px-2.5 py-1 rounded-full font-bold uppercase tracking-wider';
                stockTextEl.textContent = stock + ' máy';
                stockTextEl.className = 'text-green-600 dark:text-green-400';
                if (stockDotEl) { stockDotEl.className = 'inline-block w-1.5 h-1.5 rounded-full bg-green-500'; }
                btnBuyNow.classList.remove('btn-disabled');
                btnAddCart.classList.remove('btn-disabled');
            } else if (stock > 0) {
                // Sắp hết hàng
                stockStatusEl.textContent = 'Còn hàng';
                stockStatusEl.className = 'text-xs text-green-600 bg-green-100 dark:bg-green-500/20 dark:text-green-400 px-2.5 py-1 rounded-full font-bold uppercase tracking-wider';
                stockTextEl.textContent = 'Còn ' + stock + ' máy (sắp hết)';
                stockTextEl.className = 'text-amber-600 dark:text-amber-400';
                if (stockDotEl) { stockDotEl.className = 'inline-block w-1.5 h-1.5 rounded-full bg-amber-500'; }
                btnBuyNow.classList.remove('btn-disabled');
                btnAddCart.classList.remove('btn-disabled');
            } else {
                // Hết hàng
                stockStatusEl.textContent = 'Hết hàng';
                stockStatusEl.className = 'text-xs text-red-600 bg-red-100 dark:bg-red-500/20 dark:text-red-400 px-2.5 py-1 rounded-full font-bold uppercase tracking-wider';
                stockTextEl.textContent = 'Hết hàng';
                stockTextEl.className = 'text-red-600 dark:text-red-400';
                if (stockDotEl) { stockDotEl.className = 'inline-block w-1.5 h-1.5 rounded-full bg-red-500'; }
                btnBuyNow.classList.add('btn-disabled');
                btnAddCart.classList.add('btn-disabled');
            }

            if (image && image !== mainImage.src) {
                mainImage.style.opacity = '0.3';
                setTimeout(() => { mainImage.src = image; mainImage.style.opacity = '1'; }, 150);
            }

            // Cập nhật bảng thông số
            if (specsWrapper && specs) {
                let specsHtml = '<div class="w-full">';
                for (let key in specs) {
                    specsHtml += `
                        <div class="spec-row p-3 flex justify-between border-b border-gray-100 dark:border-white/5 last:border-0 font-medium">
                            <span class="text-sm text-gray-500 dark:text-gray-400 w-1/3">${key}:</span>
                            <span class="text-sm font-bold text-[#181611] dark:text-white text-right w-2/3">${specs[key]}</span>
                        </div>`;
                }
                specsHtml += '</div>';
                specsWrapper.innerHTML = specsHtml;
            }

            inputVariantId.value = variantId || '';
            inputQty.value = 1;
        }

        // --- XỬ LÝ CHỌN THUỘC TÍNH ---
        if(productType === 'variable' && variantsList.length > 0) {
            let selectedAttributes = {};

            // Khởi tạo mặc định: Chọn biến thể đầu tiên có sẵn
            const firstAvailableVariant = variantsList.find(v => v.stock > 0) || variantsList[0];
            if (firstAvailableVariant) {
                const groups = document.querySelectorAll('.attr-group');
                groups.forEach(group => {
                    const groupName = group.getAttribute('data-name');
                    const btns = group.querySelectorAll('.attr-btn');
                    btns.forEach(btn => {
                        const valId = parseInt(btn.getAttribute('data-id'));
                        if (firstAvailableVariant.attributes.includes(valId)) {
                            selectButton(btn, group);
                            selectedAttributes[groupName] = valId;
                        }
                    });
                });
            }

            document.querySelectorAll('.attr-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const group = this.closest('.attr-group');
                    const groupName = group.getAttribute('data-name');
                    const valId = parseInt(this.getAttribute('data-id'));

                    // Chỉ cho đổi, không cho hủy: Nếu đã chọn rồi thì không cho bỏ chọn
                    if (selectedAttributes[groupName] === valId) {
                        return;
                    } 
                    
                    // Chọn mới
                    selectButton(this, group);
                    selectedAttributes[groupName] = valId;
                    
                    autoSelectMatching(groupName);
                    
                    updateAvailability();
                    findMatchingVariant();
                });
            });

            function autoSelectMatching(clickedGroupName) {
                const selectedIdsArr = Object.values(selectedAttributes);
                const currentMatch = variantsList.find(variant => {
                    return selectedIdsArr.every(attrId => variant.attributes.includes(attrId));
                });

                if (!currentMatch) {
                    const clickedValId = selectedAttributes[clickedGroupName];
                    const newMatch = variantsList.find(v => v.stock > 0 && v.attributes.includes(clickedValId)) 
                                  || variantsList.find(v => v.attributes.includes(clickedValId));
                    if (newMatch) {
                        const groups = document.querySelectorAll('.attr-group');
                        groups.forEach(g => {
                            const gName = g.getAttribute('data-name');
                            if (gName !== clickedGroupName) {
                                const btns = g.querySelectorAll('.attr-btn');
                                btns.forEach(b => {
                                    const bValId = parseInt(b.getAttribute('data-id'));
                                    if (newMatch.attributes.includes(bValId)) {
                                        selectButton(b, g);
                                        selectedAttributes[gName] = bValId;
                                    }
                                });
                            }
                        });
                    }
                }
            }

            function deselectButtonInGroup(group) {
                group.querySelectorAll('.attr-btn').forEach(b => {
                    b.classList.remove('border-[#f4c025]', 'bg-[#f4c025]/10');
                    b.classList.add('border-gray-100', 'dark:border-white/5', 'bg-white', 'dark:bg-black/20');
                    b.querySelector('.check-icon').classList.add('hidden');
                    b.querySelector('.attr-text').classList.remove('text-[#181611]', 'dark:text-[#f4c025]', 'fonte-bold');
                    b.querySelector('.attr-text').classList.add('text-gray-600', 'dark:text-gray-300', 'font-semibold');
                });
            }

            function selectButton(btn, group) {
                deselectButtonInGroup(group);
                
                btn.classList.remove('border-gray-100', 'dark:border-white/5', 'bg-white', 'dark:bg-black/20');
                btn.classList.add('border-[#f4c025]', 'bg-[#f4c025]/10');
                btn.querySelector('.check-icon').classList.remove('hidden');
                btn.querySelector('.attr-text').classList.remove('text-gray-600', 'dark:text-gray-300', 'font-semibold');
                btn.querySelector('.attr-text').classList.add('text-[#181611]', 'dark:text-[#f4c025]', 'fonte-bold');
            }

            function updateAvailability() {
                const groups = document.querySelectorAll('.attr-group');
                
                groups.forEach(group => {
                    const currentGroupName = group.getAttribute('data-name');
                    const buttons = group.querySelectorAll('.attr-btn');
                    
                    buttons.forEach(btn => {
                        const valId = parseInt(btn.getAttribute('data-id'));
                        
                        let testIds = [valId];
                        for (let gName in selectedAttributes) {
                            if (gName !== currentGroupName) {
                                testIds.push(selectedAttributes[gName]);
                            }
                        }

                        // Một nút khả dụng nếu tồn tại ít nhất 1 biến thể active mà
                        // TẤT CẢ thuộc tính testIds của chúng ta đều nằm trong biến thể đó
                        const isPossible = variantsList.some(variant => {
                            return testIds.every(attrId => variant.attributes.includes(attrId));
                        });

                        if (isPossible) {
                            // Mở khóa nút
                            btn.classList.remove('opacity-40', 'grayscale', 'pointer-events-none', 'cursor-not-allowed');
                            // Xóa gạch ngang nếu có
                            const strikeEl = btn.querySelector('.strike-line');
                            if (strikeEl) strikeEl.remove();
                        } else {
                            // Khóa nút: mờ, nhưng VẪN CHO PHÉP CLICK để đổi tổ hợp
                            btn.classList.add('opacity-40', 'grayscale');
                            btn.classList.remove('pointer-events-none', 'cursor-not-allowed');
                            // Thêm gạch ngang nếu chưa có
                            if (!btn.querySelector('.strike-line')) {
                                const strike = document.createElement('span');
                                strike.className = 'strike-line absolute inset-0 flex items-center justify-center pointer-events-none';
                                strike.innerHTML = '<span style="display:block;width:80%;height:1.5px;background:currentColor;transform:rotate(-20deg);opacity:0.6;"></span>';
                                btn.appendChild(strike);
                            }
                        }
                    });
                });
            }

            function findMatchingVariant() {
                const selectedIdsArr = Object.values(selectedAttributes);
                const totalGroups = document.querySelectorAll('.attr-group').length;
                
                // Nếu chưa chọn gì
                if (selectedIdsArr.length === 0) {
                    updateUI(basePrice, baseSalePrice, baseStock, null, '{{ $baseVariantId }}', '{{ $product->sku }}', null);
                    return;
                }

                // Tìm biến thể khớp CHÍNH XÁC với tất cả lựa chọn
                let exactMatch = variantsList.find(variant => {
                    return selectedIdsArr.every(attrId => variant.attributes.includes(attrId)) && 
                           variant.attributes.length === selectedIdsArr.length; // Số lượng thuộc tính phải bằng nhau
                });

                // Tìm biến thể khớp một phần (tập con) để hiển thị giá tạm thời nếu chưa chọn đủ
                let partialMatches = variantsList.filter(variant => {
                    return selectedIdsArr.every(attrId => variant.attributes.includes(attrId));
                });

                if (selectedIdsArr.length < totalGroups) {
                    // Cần chọn thêm để xác định chính xác biến thể
                    stockStatusEl.textContent = 'Chọn thêm cấu hình';
                    stockStatusEl.className = 'text-xs text-amber-600 bg-amber-100 dark:bg-amber-500/20 dark:text-amber-400 px-2.5 py-1 rounded-full font-bold uppercase tracking-wider';
                    stockTextEl.textContent = 'Chọn cấu hình';
                    stockTextEl.className = 'text-amber-600 dark:text-amber-400';
                    const _dot1 = document.getElementById('stock-dot');
                    if (_dot1) _dot1.className = 'inline-block w-1.5 h-1.5 rounded-full bg-amber-500';
                    btnBuyNow.classList.add('btn-disabled');
                    btnAddCart.classList.add('btn-disabled');

                    if (partialMatches.length > 0) {
                        let minPrice = Math.min(...partialMatches.map(m => m.sale_price > 0 && m.sale_price < m.price ? m.sale_price : m.price));
                        priceEl.textContent = formatCurrency(minPrice);
                        oldPriceEl.textContent = '';
                    } else {
                        priceEl.textContent = formatCurrency(basePrice);
                        oldPriceEl.textContent = baseSalePrice > 0 ? formatCurrency(baseSalePrice) : '';
                    }
                } else {
                    // Đã chọn xong TẤT CẢ các nhóm thuộc tính
                    if (exactMatch || partialMatches.length > 0) {
                        let matchedVariant = exactMatch || partialMatches[0];
                        updateUI(matchedVariant.price, matchedVariant.sale_price, matchedVariant.stock, matchedVariant.image, matchedVariant.id, matchedVariant.sku, matchedVariant.specs);
                    } else {
                        // Tổ hợp không có trong db -> "hiển thị biến thể không có"
                        stockStatusEl.textContent = 'Biến thể không tồn tại';
                        stockStatusEl.className = 'text-xs text-red-600 bg-red-100 dark:bg-red-500/20 dark:text-red-400 px-2.5 py-1 rounded-full font-bold uppercase tracking-wider';
                        priceEl.textContent = 'Liên hệ';
                        oldPriceEl.textContent = '';
                        stockTextEl.textContent = 'Không có';
                        stockTextEl.className = 'text-red-600 dark:text-red-400';
                        const _dot2 = document.getElementById('stock-dot');
                        if (_dot2) _dot2.className = 'inline-block w-1.5 h-1.5 rounded-full bg-red-500';
                        
                        btnBuyNow.classList.add('btn-disabled');
                        btnAddCart.classList.add('btn-disabled');
                    }
                }
            }

            updateAvailability();
            findMatchingVariant();
        } else {
            @php
                $firstVar = $activeVariants->first();
                $sSpecs = $firstVar ? $firstVar->specifications->mapWithKeys(fn($s) => [$s->spec_key => $s->spec_value]) : null;
            @endphp
            if(typeof updateUI === 'function') {
                updateUI(basePrice, baseSalePrice, baseStock, null, '{{ $baseVariantId }}', '{{ $firstVar->sku ?? $product->sku }}', @json($sSpecs));
            }
        }

        // --- TĂNG GIẢM SỐ LƯỢNG ---
        const btnMinus = document.getElementById('btn-minus');
        const btnPlus = document.getElementById('btn-plus');

        if (btnMinus && btnPlus && inputQty) {
            btnMinus.addEventListener('click', () => {
                let currentVal = parseInt(inputQty.value);
                if (currentVal > 1) inputQty.value = currentVal - 1;
            });
            btnPlus.addEventListener('click', () => {
                let currentVal = parseInt(inputQty.value);
                if (currentVal < currentMaxStock) inputQty.value = currentVal + 1;
                else alert('Bạn đã chọn tối đa số lượng trong kho rồi!');
            });
        }

        // --- AJAX XỬ LÝ MUA HÀNG ---
        @guest
        const IS_GUEST = true;
        @else
        const IS_GUEST = false;
        @endguest

        function handleAddToCart(isBuyNow = false) {
            // Nếu chưa đăng nhập → hiện modal yêu cầu đăng nhập
            if (IS_GUEST) {
                if (typeof window.showLoginRequiredModal === 'function') {
                    window.showLoginRequiredModal();
                }
                return;
            }

            const productId = document.querySelector('input[name="product_id"]').value;
            const variantId = document.querySelector('input[name="variant_id"]').value;
            const quantity = document.getElementById('input-qty').value;

            // Đổi giao diện nút đang tải
            const btn = isBuyNow ? btnBuyNow : btnAddCart;
            const originalHtml = btn.innerHTML;
            btn.innerHTML = `<span class="material-symbols-outlined animate-spin">refresh</span> ĐANG XỬ LÝ...`;
            btn.classList.add('pointer-events-none', 'opacity-70');

            fetch('{{ route("client.cart.add") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    product_id: productId,
                    variant_id: variantId,
                    quantity: quantity
                })
            })
            .then(res => res.json())
            .then(data => {
                btn.innerHTML = originalHtml;
                btn.classList.remove('pointer-events-none', 'opacity-70');

                if (data.success) {
                    if (isBuyNow) {
                        // Nếu Mua ngay -> Bơm sang giỏ hàng luôn
                        window.location.href = "{{ route('client.cart.index') }}";
                    } else {
                        // Hiện toast báo thành công
                        const toast = document.querySelector('.toast-notification');
                        if(toast) {
                            toast.classList.add('show');
                            setTimeout(() => toast.classList.remove('show'), 3000);
                        }
                        
                        // Nhảy số giỏ hàng trên Header
                        const cartBadges = document.querySelectorAll('.bg-primary.text-black.rounded-full');
                        cartBadges.forEach(badge => badge.innerText = data.cart_count);
                    }
                } else {
                    alert(data.message);
                }
            })
            .catch(err => {
                btn.innerHTML = originalHtml;
                btn.classList.remove('pointer-events-none', 'opacity-70');
                alert('Có lỗi xảy ra, vui lòng thử lại!');
            });
        }

        // Gắn sự kiện cho 2 nút
        if(btnAddCart) btnAddCart.addEventListener('click', (e) => { e.preventDefault(); handleAddToCart(false); });
        if(btnBuyNow) btnBuyNow.addEventListener('click', (e) => { e.preventDefault(); handleAddToCart(true); });

        // --- XEM THÊM NỘI DUNG ---
        const contentDiv = document.getElementById('product-content');
        const readMoreBtn = document.getElementById('read-more-btn');
        const gradient = document.getElementById('content-gradient');

        if (contentDiv && readMoreBtn && gradient) {
            if (contentDiv.scrollHeight <= 800) {
                readMoreBtn.style.display = 'none';
                gradient.style.display = 'none';
            }
            readMoreBtn.addEventListener('click', function(e) {
                e.preventDefault();
                if (contentDiv.style.maxHeight) {
                    contentDiv.style.maxHeight = null;
                    readMoreBtn.textContent = 'Thu gọn bài viết';
                    gradient.style.display = 'none';
                } else {
                    contentDiv.style.maxHeight = '800px';
                    readMoreBtn.textContent = 'Xem thêm';
                    gradient.style.display = 'block';
                    window.scrollTo({ top: contentDiv.offsetTop - 100, behavior: 'smooth' });
                }
            });
        }
    });
</script>

@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            function byId(id) {
                return document.getElementById(id);
            }



            // ===== TOAST ANIMATE =====
            var toastIds = ['review-toast-success', 'review-toast-error', 'review-toast-validation'];
            var hasToast = false;

            toastIds.forEach(function(id) {
                var toast = byId(id);
                if (!toast) return;
                hasToast = true;

                // Animate in
                requestAnimationFrame(function() {
                    setTimeout(function() {
                        toast.classList.remove('opacity-0', 'translate-y-2');
                        toast.classList.add('opacity-100', 'translate-y-0');
                    }, 80);
                });

                // Tự ẩn sau 5s
                setTimeout(function() {
                    toast.classList.remove('opacity-100', 'translate-y-0');
                    toast.classList.add('opacity-0', 'translate-y-2');
                    setTimeout(function() { toast && toast.remove(); }, 500);
                }, 5000);
            });

            // Scroll tới review section nếu có toast thông báo
            if (hasToast) {
                var section = byId('reviews-section');
                if (section) {
                    setTimeout(function() {
                        section.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }, 200);
                }
            }



            // Event delegation
            document.addEventListener('click', function (e) {
                var target = e.target;
                if (!(target instanceof Element)) return;


                var helpfulBtn = target.closest('.btn-helpful');
                if (helpfulBtn) {
                    e.preventDefault();
                    if (helpfulBtn.classList.contains('text-primary')) return;

                    var url = helpfulBtn.getAttribute('data-url');
                    fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({})
                    })
                    .then(res => Object.assign(res, { isOk: res.ok }))
                    .then(res => res.json().then(data => ({ status: res.status, ok: res.isOk, data })))
                    .then(res => {
                        var data = res.data;
                        if (res.ok && data.ok) {
                            helpfulBtn.querySelector('.helpful-count').textContent = data.count;
                            helpfulBtn.classList.remove('text-gray-400');
                            helpfulBtn.classList.add('text-primary');
                        } else {
                            alert(data.message || 'Lỗi hệ thống: không thể like bình luận.');
                        }
                    })
                    .catch(e => {
                        console.error('Lỗi Like:', e);
                        alert('Không thể kết nối đến máy chủ hoặc lỗi cấu trúc dữ liệu.');
                    });
                }
            });

            // ===== FILTER PILLS =====
            var filterBtns = document.querySelectorAll('.review-filter-btn');
            var reviewCards = document.querySelectorAll('.review-card');
            var noResult = document.getElementById('no-filter-result');

            filterBtns.forEach(function(btn) {
                btn.addEventListener('click', function() {
                    var filter = this.getAttribute('data-filter');

                    // Toggle active style
                    filterBtns.forEach(function(b) {
                        b.classList.remove('border-[#f4c025]', 'text-[#181611]', 'bg-[#f4c025]', 'shadow-[0_4px_10px_-2px_rgba(244,192,37,0.4)]', 'active');
                        b.classList.add('border-gray-200', 'text-gray-600', 'bg-white', 'dark:bg-white/5', 'dark:border-white/10', 'dark:text-gray-300');
                    });
                    this.classList.add('border-[#f4c025]', 'text-[#181611]', 'bg-[#f4c025]', 'shadow-[0_4px_10px_-2px_rgba(244,192,37,0.4)]', 'active');
                    this.classList.remove('border-gray-200', 'text-gray-600', 'bg-white', 'dark:bg-white/5', 'dark:border-white/10', 'dark:text-gray-300', 'hover:border-[#f4c025]', 'hover:text-[#f4c025]', 'hover:bg-[#f4c025]/5');

                    var visible = 0;
                    reviewCards.forEach(function(card) {
                        var show = false;
                        if (filter === 'all') {
                            show = true;
                        } else if (filter === 'has-image') {
                            show = card.getAttribute('data-has-image') === '1';
                        } else if (filter === 'purchased') {
                            show = card.getAttribute('data-purchased') === '1';
                        } else if (filter.startsWith('star-')) {
                            var star = filter.replace('star-', '');
                            show = card.getAttribute('data-rating') === star;
                        }

                        if (show) {
                            card.style.display = '';
                            visible++;
                        } else {
                            card.style.display = 'none';
                        }
                    });

                    if (noResult) noResult.classList.toggle('hidden', visible > 0);
                });
            });
        });
    </script>
@endpush
@endsection
