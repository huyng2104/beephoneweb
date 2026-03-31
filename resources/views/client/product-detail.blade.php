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
        <section class="lg:col-span-7" data-purpose="product-gallery">
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
                
                <div class="mt-8 p-5 bg-primary/10 dark:bg-primary/5 rounded-xl border border-primary/20 shadow-sm">
                    <h3 class="font-bold text-[#181611] dark:text-white mb-3 flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary">stars</span> Đặc điểm nổi bật
                    </h3>
                    <div class="text-sm space-y-2 text-gray-700 dark:text-gray-300 leading-relaxed line-clamp-4">
                        {!! strip_tags($product->description) !!}
                    </div>
                </div>
            </div>
        </section>

        <section class="lg:col-span-5" data-purpose="product-info-actions">
            <form action="#" method="POST" id="add-to-cart-form" class="flex flex-col gap-6">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <input type="hidden" name="variant_id" id="selected-variant-id" value="">

                <div class="bg-white dark:bg-white/5 p-6 rounded-2xl border border-gray-100 dark:border-white/10 custom-shadow relative overflow-hidden">
                    <h1 class="text-2xl lg:text-3xl font-bold text-[#181611] dark:text-white mb-2 pr-12">{{ $product->name }}</h1>
                    
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
                        <span id="main-price" class="text-3xl font-black text-red-500 dark:text-red-400 transition-opacity duration-200">
                            Đang cập nhật...
                        </span>
                        <span id="old-price" class="text-lg text-gray-400 line-through transition-opacity duration-200"></span>
                    </div>
                </div>

                <div class="bg-white dark:bg-white/5 p-6 rounded-2xl border border-gray-100 dark:border-white/10 custom-shadow space-y-6">
                    @php
                        $groupedAttributes = [];
                        $variantsJS = [];

                        if($product->type == 'variable' && isset($product->variants)) {
                            foreach($product->variants as $variant) {
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
                                    'price' => $variant->price,
                                    'sale_price' => $variant->sale_price,
                                    'stock' => $variant->stock,
                                    'image' => $variant->thumbnail ? asset('storage/' . $variant->thumbnail) : null
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
                    @else
                        <div>
                            <p class="font-bold mb-3 text-[#181611] dark:text-white">Phiên bản:</p>
                            <button type="button" class="border-2 border-primary bg-primary/10 rounded-xl py-3 px-6 font-bold text-sm text-[#181611] dark:text-white">
                                Sản phẩm tiêu chuẩn
                            </button>
                        </div>
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
                    <p class="text-xs text-right text-gray-500 dark:text-gray-400 mt-3">Trong kho còn: <span id="stock-text" class="font-bold text-primary">0</span> máy</p>
                </div>

                <div class="flex flex-col gap-3 mt-2">
                    <button type="button" id="btn-buy-now" class="w-full bg-primary text-black font-bold py-4 rounded-xl shadow-lg transition-transform hover:scale-[1.02] flex flex-col items-center justify-center">
                        <span class="text-lg uppercase tracking-wider">Mua ngay</span>
                        <span class="text-xs font-medium opacity-80">(Giao hàng tận nơi)</span>
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
                <div class="border border-gray-100 dark:border-white/10 rounded-xl overflow-hidden text-sm">
                    @if(is_array($product->specifications) && count($product->specifications) > 0)
                        <div class="w-full">
                            @foreach($product->specifications as $key => $value)
                            <div class="spec-row p-3 flex justify-between border-b border-gray-100 dark:border-white/5 last:border-0">
                                <span class="text-sm text-gray-500 dark:text-gray-400 w-1/3 font-medium">{{ $key }}:</span>
                                <span class="text-sm font-bold text-[#181611] dark:text-white text-right w-2/3">{{ is_array($value) ? implode(', ', $value) : $value }}</span>
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
</main>

<section id="comments" class="max-w-[1440px] mx-auto px-4 md:px-10 lg:px-20 pb-12 -mt-4">
    <div class="overflow-hidden rounded-2xl border border-white/10 bg-[#141a1e] text-white shadow-[0_16px_45px_-30px_rgba(0,0,0,0.75)]">
        <div class="border-b border-white/10 px-6 py-6">
            <div class="grid gap-8 lg:grid-cols-[260px_minmax(0,1fr)_1px]">
                <div class="flex flex-col justify-center">
                    <div class="flex items-end gap-2">
                        <span class="text-6xl font-black leading-none">{{ number_format($averageRating, 1) }}</span>
                        <span class="pb-2 text-3xl font-bold text-slate-300">/5</span>
                    </div>
                    <div class="mt-4 flex items-center gap-0.5 text-primary">
                        @for($i = 1; $i <= 5; $i++)
                            <span class="material-symbols-outlined text-[22px]">star</span>
                        @endfor
                    </div>
                    <p class="mt-2 text-lg font-medium text-slate-200">{{ $totalRatings }} luot danh gia</p>

                    <button id="open-review-modal" type="button" class="mt-5 inline-flex h-12 w-44 items-center justify-center rounded-xl bg-primary px-6 text-base font-black text-black shadow-sm transition hover:brightness-105 focus:outline-none focus:ring-2 focus:ring-primary/60">
                        Viết đánh giá
                    </button>
                </div>

                <div class="flex flex-col justify-center gap-3 lg:pr-6">
                    @foreach($ratingBreakdown as $star => $count)
                        @php
                            $percent = $totalRatings > 0 ? round(($count / $totalRatings) * 100, 2) : 0;
                        @endphp
                        <div class="grid grid-cols-[30px_minmax(0,1fr)_84px] items-center gap-3">
                            <div class="flex items-center gap-1 text-base font-bold text-white">
                                <span>{{ $star }}</span>
                                <span class="material-symbols-outlined text-primary text-[18px]">star</span>
                            </div>
                            <div class="h-2.5 overflow-hidden rounded-full bg-slate-800">
                                <div class="h-full rounded-full bg-primary transition-all duration-300" style="width: {{ $percent }}%"></div>
                            </div>
                            <div class="text-right text-sm text-slate-300">{{ $count }} danh gia</div>
                        </div>
                    @endforeach
                </div>

                <div class="hidden lg:block w-px bg-white/10"></div>
            </div>
        </div>

        <div class="comments-list px-6 py-2">
            @forelse($comments as $comment)
                @include('components.comment', ['comment' => $comment, 'product' => $product])
            @empty
                <div class="rounded-2xl border border-dashed border-white/15 bg-black/10 px-6 py-10 text-center text-sm font-medium text-slate-300">
                    Chua co danh gia nao. Hay viet danh gia dau tien cho san pham nay.
                </div>
            @endforelse
        </div>
    </div>
</section>

<div id="review-modal" aria-hidden="true" class="fixed inset-0 z-[9998] flex items-start justify-center px-4 py-8 opacity-0 pointer-events-none transition duration-200 ease-out sm:py-12">
    <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" data-review-close></div>

    <div id="review-modal-panel" class="relative w-full max-w-5xl overflow-hidden rounded-2xl border border-white/10 bg-[#141a1e] shadow-[0_20px_60px_-40px_rgba(0,0,0,0.9)] opacity-0 translate-y-3 scale-95 transition duration-200 ease-out">
        <div class="flex items-center justify-between border-b border-white/10 px-6 py-4">
            <h3 class="text-lg font-black text-white">Danh gia & nhan xet</h3>
            <button type="button" class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-white/10 bg-white/5 text-slate-200 transition hover:border-white/20 hover:bg-white/10" data-review-close>
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <div class="grid gap-6 px-6 py-6 lg:grid-cols-[260px_minmax(0,1fr)]">
            <div class="flex flex-col gap-4 rounded-2xl border border-white/10 bg-black/10 p-5">
                <div class="flex items-center gap-4">
                    <div class="flex h-16 w-16 shrink-0 items-center justify-center overflow-hidden rounded-2xl border border-white/10 bg-black/20">
                        @if($product->thumbnail)
                            <img src="{{ asset('storage/' . $product->thumbnail) }}" alt="{{ $product->name }}" class="h-full w-full object-cover">
                        @else
                            <span class="material-symbols-outlined text-4xl text-slate-500">image</span>
                        @endif
                    </div>
                    <div class="min-w-0">
                        <div class="text-xs font-bold uppercase tracking-wide text-slate-400">San pham</div>
                        <div class="mt-1 line-clamp-2 text-base font-black text-white">{{ $product->name }}</div>
                    </div>
                </div>

                <div class="rounded-2xl border border-white/10 bg-[#0f141a] p-4">
                    <div class="text-sm font-bold text-slate-200">Danh gia chung</div>
                    <div class="mt-3 flex items-center gap-2 text-primary" data-rating-stars>
                        @for($i = 1; $i <= 5; $i++)
                            <button type="button" class="review-star inline-flex items-center justify-center rounded-lg p-1 transition hover:bg-white/5 focus:outline-none focus:ring-2 focus:ring-primary/40" data-value="{{ $i }}" aria-label="{{ $i }} sao">
                                <span class="material-symbols-outlined text-[26px]">star</span>
                            </button>
                        @endfor
                    </div>
                    <p class="mt-2 text-xs text-slate-400" data-rating-label>Chon so sao de danh gia</p>
                </div>
            </div>

            <div class="rounded-2xl border border-white/10 bg-black/10 p-5">
                <form action="{{ route('products.comments.store', $product) }}" method="POST" enctype="multipart/form-data" id="review-form" class="space-y-4">
                    @csrf
                    <input type="hidden" name="rating" id="review_rating" value="">

                    @guest
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label for="review_guest_name" class="block text-xs font-bold text-slate-300">Ten cua ban</label>
                            <input id="review_guest_name" type="text" name="guest_name" required class="mt-2 w-full rounded-xl border border-white/10 bg-[#0f141a] px-4 py-3 text-sm text-white placeholder:text-slate-500 focus:border-primary focus:ring-primary/30" placeholder="Nhap ten hien thi">
                        </div>
                        <div>
                            <label for="review_guest_email" class="block text-xs font-bold text-slate-300">Email</label>
                            <input id="review_guest_email" type="email" name="guest_email" required class="mt-2 w-full rounded-xl border border-white/10 bg-[#0f141a] px-4 py-3 text-sm text-white placeholder:text-slate-500 focus:border-primary focus:ring-primary/30" placeholder="email@example.com">
                        </div>
                    </div>
                    @endguest

                    <div>
                        <label for="review_content" class="block text-xs font-bold text-slate-300">Nhan xet</label>
                        <textarea id="review_content" name="content" rows="6" required class="mt-2 w-full rounded-xl border border-white/10 bg-[#0f141a] px-4 py-3 text-sm text-white placeholder:text-slate-500 focus:border-primary focus:ring-primary/30" placeholder="Xin moi chia se mot so cam nhan ve san pham (nhap toi thieu 15 ki tu)"></textarea>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label for="review_image" class="block text-xs font-bold text-slate-300">Anh (tuy chon)</label>
                            <input id="review_image" type="file" name="image" accept=".jpg,.jpeg,.png,.webp" class="mt-2 w-full rounded-xl border border-white/10 bg-[#0f141a] px-4 py-3 text-sm text-slate-200 file:mr-4 file:rounded-lg file:border-0 file:bg-white/10 file:px-4 file:py-2 file:text-xs file:font-bold file:text-slate-100 hover:file:bg-white/15">
                        </div>
                        <div class="flex items-end">
                            <div class="w-full rounded-xl border border-white/10 bg-[#0f141a] px-4 py-3 text-xs text-slate-400">
                                Meo: ban co the nhan <span class="font-bold text-slate-200">ESC</span> de dong form.
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="mt-2 inline-flex w-full items-center justify-center rounded-xl bg-primary px-6 py-4 text-sm font-black text-black shadow-sm transition hover:brightness-105 focus:outline-none focus:ring-2 focus:ring-primary/60">
                        Gui danh gia
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

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
        const productType = "{{ $product->type }}";
        const basePrice = {{ $product->price ?? 0 }};
        const baseSalePrice = {{ $product->sale_price ?? 0 }};
        const baseStock = {{ $product->stock ?? 0 }};
        const csrfToken = '{{ csrf_token() }}';
        
        const variantsList = @json($variantsJS ?? []);
        
        const priceEl = document.getElementById('main-price');
        const oldPriceEl = document.getElementById('old-price');
        const stockStatusEl = document.getElementById('header-stock-status');
        const stockTextEl = document.getElementById('stock-text');
        const inputVariantId = document.getElementById('selected-variant-id');
        const inputQty = document.getElementById('input-qty');
        
        const btnBuyNow = document.getElementById('btn-buy-now');
        const btnAddCart = document.getElementById('btn-add-cart');

        let currentMaxStock = baseStock;

        function formatCurrency(num) {
            return new Intl.NumberFormat('vi-VN').format(num) + 'đ';
        }

        function updateUI(price, salePrice, stock, image, variantId) {
            const finalPrice = (salePrice > 0 && salePrice < price) ? salePrice : price;
            priceEl.textContent = formatCurrency(finalPrice);
            
            if (salePrice > 0 && salePrice < price) {
                oldPriceEl.textContent = formatCurrency(price);
            } else {
                oldPriceEl.textContent = '';
            }

            currentMaxStock = stock;
            stockTextEl.textContent = stock;
            
            if(stock > 0) {
                stockStatusEl.textContent = 'Còn hàng';
                stockStatusEl.className = 'text-xs text-green-600 bg-green-100 dark:bg-green-500/20 dark:text-green-400 px-2.5 py-1 rounded-full font-bold uppercase tracking-wider';
                btnBuyNow.classList.remove('btn-disabled');
                btnAddCart.classList.remove('btn-disabled');
            } else {
                stockStatusEl.textContent = 'Hết hàng';
                stockStatusEl.className = 'text-xs text-red-600 bg-red-100 dark:bg-red-500/20 dark:text-red-400 px-2.5 py-1 rounded-full font-bold uppercase tracking-wider';
                btnBuyNow.classList.add('btn-disabled');
                btnAddCart.classList.add('btn-disabled');
            }

            if (image && image !== mainImage.src) {
                mainImage.style.opacity = '0.3';
                setTimeout(() => { mainImage.src = image; mainImage.style.opacity = '1'; }, 150);
            }

            inputVariantId.value = variantId || '';
            inputQty.value = 1;
        }

        // --- XỬ LÝ CHỌN THUỘC TÍNH ---
        if(productType === 'variable' && variantsList.length > 0) {
            let selectedAttributes = {};

            document.querySelectorAll('.attr-group').forEach(group => {
                const groupName = group.getAttribute('data-name');
                const firstBtn = group.querySelector('.attr-btn');
                if (firstBtn) {
                    selectButton(firstBtn, group);
                    selectedAttributes[groupName] = parseInt(firstBtn.getAttribute('data-id'));
                }
            });

            document.querySelectorAll('.attr-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const group = this.closest('.attr-group');
                    const groupName = group.getAttribute('data-name');
                    
                    selectButton(this, group);
                    selectedAttributes[groupName] = parseInt(this.getAttribute('data-id'));
                    
                    findMatchingVariant();
                });
            });

            function selectButton(btn, group) {
                group.querySelectorAll('.attr-btn').forEach(b => {
                    b.classList.remove('border-primary', 'bg-primary/10', 'border-2');
                    b.classList.add('border-gray-200', 'dark:border-white/10', 'border', 'bg-transparent');
                    b.querySelector('.check-icon').classList.add('hidden');
                    b.querySelector('.attr-text').classList.remove('text-[#181611]', 'dark:text-white');
                    b.querySelector('.attr-text').classList.add('text-gray-600', 'dark:text-gray-300');
                });
                
                btn.classList.remove('border-gray-200', 'dark:border-white/10', 'border', 'bg-transparent');
                btn.classList.add('border-primary', 'bg-primary/10', 'border-2');
                btn.querySelector('.check-icon').classList.remove('hidden');
                btn.querySelector('.attr-text').classList.remove('text-gray-600', 'dark:text-gray-300');
                btn.querySelector('.attr-text').classList.add('text-[#181611]', 'dark:text-white');
            }

            function findMatchingVariant() {
                let selectedIds = Object.values(selectedAttributes).sort((a,b) => a-b).join(',');
                let matchedVariant = variantsList.find(v => v.attributes.join(',') === selectedIds);

                if (matchedVariant) {
                    updateUI(matchedVariant.price, matchedVariant.sale_price, matchedVariant.stock, matchedVariant.image, matchedVariant.id);
                } else {
                    stockStatusEl.textContent = 'Ngừng kinh doanh';
                    stockStatusEl.className = 'text-xs text-gray-600 bg-gray-100 dark:bg-white/10 dark:text-gray-300 px-2.5 py-1 rounded-full font-bold uppercase tracking-wider';
                    priceEl.textContent = 'Liên hệ';
                    oldPriceEl.textContent = '';
                    stockTextEl.textContent = 0;
                    
                    btnBuyNow.classList.add('btn-disabled');
                    btnAddCart.classList.add('btn-disabled');
                }
            }

            findMatchingVariant();
        } else {
            updateUI(basePrice, baseSalePrice, baseStock, null, null);
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
        function handleAddToCart(isBuyNow = false) {
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

            if (!modal || !panel) return;

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
                    var map = { 1: 'Rat te', 2: 'Te', 3: 'Binh thuong', 4: 'Tot', 5: 'Tuyet voi' };
                    ratingLabel.textContent = value ? value + '/5 - ' + (map[value] || '') : 'Chon so sao de danh gia';
                }
            }

            function openModal() {
                modal.setAttribute('aria-hidden', 'false');
                modal.classList.remove('opacity-0', 'pointer-events-none');
                modal.classList.add('opacity-100', 'pointer-events-auto');

                panel.classList.remove('opacity-0', 'translate-y-3', 'scale-95');
                panel.classList.add('opacity-100', 'translate-y-0', 'scale-100');

                if (ratingInput && !ratingInput.value) setRating(5);

                var content = byId('review_content');
                if (content && content.focus) content.focus();
            }

            function closeModal() {
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
                    stars[i].classList.add('text-slate-600');
                }
            }

            // Event delegation (more robust than binding to a single element)
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
            });

            document.addEventListener('keydown', function (e) {
                if (e.key !== 'Escape') return;
                if (modal.getAttribute('aria-hidden') === 'true') return;
                closeModal();
            });
        });
    </script>
@endpush
@endsection
