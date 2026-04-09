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
                                            class="attr-btn relative border border-gray-200 dark:border-white/10 bg-transparent rounded-xl py-3 px-2 text-center transition-all hover:border-primary dark:hover:border-primary"
                                            data-id="{{ $valId }}">
                                            
                                            <div class="check-icon absolute top-0 right-0 bg-primary text-black rounded-bl-lg rounded-tr-lg p-1 hidden">
                                                <span class="material-symbols-outlined text-[12px] font-bold">check</span>
                                            </div>
                                            
                                            <span class="block font-bold text-sm text-gray-600 dark:text-gray-300 attr-text transition-colors">{{ $valName }}</span>
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

    {{-- SẢN PHẨM LIÊN QUAN --}}
    @if($relatedProducts->isNotEmpty())
    <div class="mt-20">
        <h2 class="text-2xl font-bold mb-8 pb-3 border-b-4 border-primary inline-block uppercase text-[#181611] dark:text-white">
            Sản phẩm liên quan
        </h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            @foreach($relatedProducts as $relProduct)
                @include('client.home.partials.product-card', ['product' => $relProduct])
            @endforeach
        </div>
    </div>
    @endif

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
                        <p class="text-6xl font-black text-[#181611] dark:text-white leading-none">
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
                    @auth
                        @if($userReview)
                            <div class="text-center">
                                <p class="text-xs text-green-600 font-semibold flex items-center gap-1 justify-center mb-2">
                                    <span class="material-symbols-outlined text-[14px]">check_circle</span>
                                    Bạn đã đánh giá
                                </p>
                                @if(!$userReview->isApproved() && $userReview->created_at->diffInMinutes(now()) <= 15)

                                    <form action="{{ route('reviews.destroy', $userReview->id) }}" method="POST"
                                        onsubmit="return confirm('Xóa đánh giá này? Không thể hoàn tác.')">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex items-center gap-1 px-3 py-1.5 border border-red-200 bg-red-50 hover:bg-red-100 text-red-600 text-xs font-semibold rounded-lg transition-colors">
                                            <span class="material-symbols-outlined text-[13px]">delete</span>
                                            Xóa đánh giá
                                        </button>
                                    </form>
                                @endif
                            </div>
                        @elseif($canReview)
                            <button id="open-review-modal"
                                    class="w-full px-5 py-2.5 bg-red-500 hover:bg-red-600 text-white font-bold text-sm rounded-xl transition-colors shadow-sm">
                                Viết đánh giá
                            </button>
                        @else
                            <p class="text-xs text-gray-400 text-center leading-relaxed">
                                Chỉ khách hàng đã mua<br>mới được đánh giá
                            </p>
                        @endif
                    @else
                        <a href="{{ route('login') }}"
                           class="w-full text-center px-5 py-2.5 bg-red-500 hover:bg-red-600 text-white font-bold text-sm rounded-xl transition-colors shadow-sm">
                            Đăng nhập để đánh giá
                        </a>
                    @endauth
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
                            <div class="h-full bg-red-500 rounded-full transition-all duration-700"
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
                           border-red-500 text-red-500 bg-red-50 transition-all"
                    data-filter="all">Tất cả</button>
            <button class="review-filter-btn px-4 py-1.5 rounded-full border text-sm font-semibold
                           border-gray-200 text-gray-600 bg-white hover:border-red-400 hover:text-red-500 transition-all dark:bg-white/5 dark:border-white/10 dark:text-gray-300"
                    data-filter="has-image">Có hình ảnh</button>
            {{-- <button class="review-filter-btn px-4 py-1.5 rounded-full border text-sm font-semibold
                           border-gray-200 text-gray-600 bg-white hover:border-red-400 hover:text-red-500 transition-all dark:bg-white/5 dark:border-white/10 dark:text-gray-300"
                    data-filter="purchased">Đã mua hàng</button> --}}
            @foreach([5,4,3,2,1] as $star)
            <button class="review-filter-btn px-4 py-1.5 rounded-full border text-sm font-semibold
                           border-gray-200 text-gray-600 bg-white hover:border-red-400 hover:text-red-500 transition-all dark:bg-white/5 dark:border-white/10 dark:text-gray-300"
                    data-filter="star-{{ $star }}">
                {{ $star }} sao
            </button>
            @endforeach
        </div>

        {{-- ===== DANH SÁCH ĐÁNH GIÁ ===== --}}
        @if($reviews->isNotEmpty())
            <div class="flex flex-col gap-4" id="review-list">
                @foreach($reviews as $review)
                <div class="review-card bg-white dark:bg-white/5 rounded-2xl p-5 border border-gray-100 dark:border-white/10 custom-shadow"
                     id="review-{{ $review->id }}"
                     data-rating="{{ $review->rating }}"
                     data-has-image="{{ $review->images->isNotEmpty() ? '1' : '0' }}"
                     data-purchased="{{ $review->is_purchased ? '1' : '0' }}">

                    <div class="flex items-start gap-3">
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
                                @if($review->isPending() && auth()->id() === $review->user_id)
                                    <span class="inline-flex items-center gap-0.5 px-2 py-0.5 bg-orange-50 border border-orange-200 text-orange-700 text-[10px] font-bold rounded-full">
                                        <span class="material-symbols-outlined text-[11px]">pending</span> Đang chờ duyệt
                                    </span>
                                @endif
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

    {{-- ===== MODAL VIẾT ĐÁNH GIÁ ===== --}}
    @if($canReview)
    <div id="review-modal" aria-hidden="true"
         class="fixed inset-0 z-[9999] flex items-center justify-center px-4 opacity-0 pointer-events-none transition-all duration-300">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" data-review-close></div>
        <div id="review-modal-panel"
             class="relative w-full max-w-lg bg-white dark:bg-gray-900 rounded-2xl shadow-2xl border border-gray-100 dark:border-white/10 overflow-hidden
                    opacity-0 translate-y-3 scale-95 transition-all duration-300">

            {{-- Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-white/10">
                <h3 class="font-bold text-[#181611] dark:text-white flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-[20px]">rate_review</span>
                    Viết đánh giá
                </h3>
                <button data-review-close class="p-2 hover:bg-gray-100 dark:hover:bg-white/10 rounded-lg transition-colors text-gray-500">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>

            {{-- Form --}}
            <form action="{{ route('products.reviews.store', $product) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="px-6 py-5 flex flex-col gap-5">

                    {{-- Chọn sao --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Số sao *</label>
                        <input type="hidden" name="rating" id="review_rating" value="">
                        <div class="flex gap-1" data-rating-stars>
                            @foreach([1,2,3,4,5] as $s)
                            <button type="button" class="review-star text-slate-300 transition-colors"
                                    data-value="{{ $s }}" title="{{ $s }} sao">
                                <span class="material-symbols-outlined text-[32px]" style="font-variation-settings:'FILL' 1">star</span>
                            </button>
                            @endforeach
                        </div>
                        <p class="text-xs text-gray-400 mt-1" data-rating-label>Chọn số sao để đánh giá</p>
                    </div>

                    {{-- Nội dung --}}
                    <div>
                        <label for="review_content" class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">
                            Nội dung nhận xét *
                        </label>
                        <textarea id="review_content" name="comment" rows="4" required minlength="10"
                                  class="w-full border border-gray-200 dark:border-white/10 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-primary outline-none resize-none dark:bg-gray-800 dark:text-white transition-shadow"
                                  placeholder="Chia sẻ cảm nhận của bạn về sản phẩm..."></textarea>
                    </div>

                    {{-- Ảnh đính kèm --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">
                            Hình ảnh (tùy chọn, tối đa 5 ảnh)
                        </label>
                        <input type="file" name="images[]" multiple accept="image/jpeg,image/png,image/webp"
                               class="w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-bold file:bg-primary/20 file:text-[#181611] hover:file:bg-primary/30 transition">
                    </div>
                </div>

                <div class="px-6 py-4 border-t border-gray-100 dark:border-white/10 flex justify-end gap-3 bg-gray-50/50 dark:bg-white/5">
                    <button type="button" data-review-close
                            class="px-5 py-2.5 border border-gray-200 dark:border-white/10 rounded-xl text-sm font-bold text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-white/10 transition-colors">
                        Hủy
                    </button>
                    <button type="submit"
                            class="px-5 py-2.5 bg-primary text-[#181611] rounded-xl text-sm font-bold hover:brightness-105 transition-all shadow-sm">
                        Gửi đánh giá
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

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
                    b.classList.remove('border-primary', 'bg-primary/10', 'border-2', 'ring-2', 'ring-primary');
                    b.classList.add('border-gray-200', 'dark:border-white/10', 'border', 'bg-transparent');
                    b.querySelector('.check-icon').classList.add('hidden');
                    b.querySelector('.attr-text').classList.remove('text-[#181611]', 'dark:text-white', 'font-bold');
                    b.querySelector('.attr-text').classList.add('text-gray-600', 'dark:text-gray-300');
                });
            }

            function selectButton(btn, group) {
                deselectButtonInGroup(group);
                
                btn.classList.remove('border-gray-200', 'dark:border-white/10', 'border', 'bg-transparent');
                btn.classList.add('border-primary', 'bg-primary/10', 'border-2', 'ring-2', 'ring-primary');
                btn.querySelector('.check-icon').classList.remove('hidden');
                btn.querySelector('.attr-text').classList.remove('text-gray-600', 'dark:text-gray-300');
                btn.querySelector('.attr-text').classList.add('text-[#181611]', 'dark:text-white', 'font-bold');
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

            var modal = byId('review-modal');
            var panel = byId('review-modal-panel');
            var ratingInput = byId('review_rating');
            var ratingLabel = modal ? modal.querySelector('[data-rating-label]') : null;
            var starsWrap = modal ? modal.querySelector('[data-rating-stars]') : null;

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

            function setRating(value) {
                if (ratingInput) ratingInput.value = String(value || '');

                if (starsWrap) {
                    var stars = starsWrap.querySelectorAll('.review-star');
                    for (var i = 0; i < stars.length; i++) {
                        var btn = stars[i];
                        var v = Number(btn.getAttribute('data-value') || 0);
                        if (v <= value) {
                            btn.classList.add('text-primary');
                            btn.classList.remove('text-slate-600');
                        } else {
                            btn.classList.add('text-slate-600');
                            btn.classList.remove('text-primary');
                        }
                    }
                }

                if (ratingLabel) {
                    var map = { 1: 'Rất tệ', 2: 'Tệ', 3: 'Bình thường', 4: 'Tốt', 5: 'Tuyệt vời' };
                    ratingLabel.textContent = value ? value + '/5 — ' + (map[value] || '') : 'Chọn số sao để đánh giá';
                }
            }

            function openModal() {
                if (!modal || !panel) return;
                modal.setAttribute('aria-hidden', 'false');
                modal.classList.remove('opacity-0', 'pointer-events-none');
                modal.classList.add('opacity-100', 'pointer-events-auto');

                panel.classList.remove('opacity-0', 'translate-y-3', 'scale-95');
                panel.classList.add('opacity-100', 'translate-y-0', 'scale-100');

                var content = byId('review_content');
                if (content && content.focus) content.focus();
            }

            function closeModal() {
                if (!modal || !panel) return;
                modal.setAttribute('aria-hidden', 'true');
                modal.classList.remove('opacity-100', 'pointer-events-auto');
                modal.classList.add('opacity-0', 'pointer-events-none');

                panel.classList.remove('opacity-100', 'translate-y-0', 'scale-100');
                panel.classList.add('opacity-0', 'translate-y-3', 'scale-95');
            }

            // init stars as unselected
            if (starsWrap) {
                var stars = starsWrap.querySelectorAll('.review-star');
                for (var i = 0; i < stars.length; i++) {
                    stars[i].classList.add('text-slate-300');
                }
            }

            // Event delegation
            document.addEventListener('click', function (e) {
                var target = e.target;
                if (!(target instanceof Element)) return;

                if (target.closest('#open-review-modal')) {
                    e.preventDefault();
                    openModal();
                    return;
                }

                if (target.closest('[data-review-close]')) {
                    e.preventDefault();
                    closeModal();
                    return;
                }

                var starBtn = target.closest('#review-modal .review-star');
                if (starBtn) {
                    e.preventDefault();
                    var v = Number(starBtn.getAttribute('data-value') || 0);
                    if (v >= 1 && v <= 5) setRating(v);
                }

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

            document.addEventListener('keydown', function (e) {
                if (e.key !== 'Escape') return;
                if (modal && modal.getAttribute('aria-hidden') === 'true') return;
                if (modal) closeModal();
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
                        b.classList.remove('border-red-500', 'text-red-500', 'bg-red-50', 'active');
                        b.classList.add('border-gray-200', 'text-gray-600', 'bg-white', 'dark:bg-white/5', 'dark:border-white/10', 'dark:text-gray-300');
                    });
                    this.classList.add('border-red-500', 'text-red-500', 'bg-red-50', 'active');
                    this.classList.remove('border-gray-200', 'text-gray-600', 'bg-white');

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
