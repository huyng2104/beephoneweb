@extends('client.layouts.app')

@section('title', 'Bee Phone - Chuyên gia Công nghệ')

@section('content')
    <style>
        .marquee-container { overflow: hidden; white-space: nowrap; }
        .marquee-content { display: inline-flex; animation: marquee 25s linear infinite; }
        .marquee-content:hover { animation-play-state: paused; cursor: pointer; }
        @keyframes marquee { 0% { transform: translateX(0); } 100% { transform: translateX(-50%); } }
    </style>

    <section class="relative bg-bee-gray-light py-12 lg:py-20 overflow-hidden">
        <div class="swiper heroSwiper container mx-auto px-4">
            <div class="swiper-wrapper">
                <div class="swiper-slide grid lg:grid-cols-2 gap-12 items-center">
                    <div class="order-2 lg:order-1 space-y-6" data-aos="fade-right">
                        <span class="inline-block px-3 py-1 bg-white border border-bee-yellow text-xs font-semibold rounded-full text-bee-dark uppercase tracking-widest">Siêu phẩm ra mắt</span>
                        <h1 class="text-5xl lg:text-7xl font-bold text-bee-dark leading-tight">iPhone 15 Pro<br/><span class="text-gray-400">Titan tự nhiên.</span></h1>
                        <p class="text-gray-500 text-lg max-w-md">Thiết kế titan cực kỳ bền bỉ, nhẹ và sang trọng. Hệ thống camera tiên tiến nâng tầm mọi khung hình.</p>
                        <div class="flex items-center gap-4 pt-4">
                            <button class="bg-bee-yellow text-bee-dark px-8 py-4 rounded-full font-semibold hover:opacity-90 smooth-transition shadow-lg shadow-yellow-200">Mua ngay</button>
                        </div>
                    </div>
                    <div class="order-1 lg:order-2 flex justify-center" data-aos="fade-left">
                        <img alt="iPhone 15 Pro" class="w-full max-w-xl object-contain drop-shadow-2xl" src="https://cdn.tgdd.vn/News/1548159/thuc-te-hinh-anh-iphone-15-pro-max-anh-iphone-15-1(1)-800x450-1.jpg"/>
                    </div>
                </div>
                <div class="swiper-slide grid lg:grid-cols-2 gap-12 items-center">
                    <div class="order-2 lg:order-1 space-y-6">
                        <span class="inline-block px-3 py-1 bg-black text-white text-xs font-semibold rounded-full uppercase tracking-widest">Đỉnh cao AI</span>
                        <h1 class="text-5xl lg:text-7xl font-bold text-bee-dark leading-tight">Galaxy S24<br/><span class="text-bee-yellow">Ultra</span></h1>
                        <p class="text-gray-500 text-lg max-w-md">Khai phóng sức mạnh AI trên chiếc điện thoại Galaxy quyền năng nhất từ trước đến nay.</p>
                        <div class="flex items-center gap-4 pt-4">
                            <button class="bg-bee-dark text-white px-8 py-4 rounded-full font-semibold hover:bg-gray-800 smooth-transition shadow-lg">Khám phá</button>
                        </div>
                    </div>
                    <div class="order-1 lg:order-2 flex justify-center">
                        <img alt="Samsung S24" class="w-full max-w-xl object-contain drop-shadow-2xl" src="https://images.samsung.com/vn/smartphones/galaxy-s24-ultra/images/galaxy-s24-ultra-highlights-color-titanium-orange-back-mo.jpg?imbypass=true"/>
                    </div>
                </div>
            </div>
            <div class="swiper-pagination"></div>
        </div>
    </section>

    <div class="bg-white border-b border-bee-gray-border py-5 marquee-container">
        <div class="marquee-content items-center gap-16 text-sm font-bold text-gray-500 uppercase tracking-wider">
            <span class="inline-flex items-center gap-2 hover:text-bee-yellow transition-colors">🚀 Giao hàng siêu tốc 2H</span>
            <span class="inline-flex items-center gap-2 hover:text-bee-yellow transition-colors">🛡️ Bảo hành chính hãng 12 tháng</span>
            <span class="inline-flex items-center gap-2 hover:text-bee-yellow transition-colors">🔄 Lỗi 1 đổi 1 trong 30 ngày</span>
            <span class="inline-flex items-center gap-2 hover:text-bee-yellow transition-colors">💳 Hỗ trợ trả góp 0%</span>
            <span class="inline-flex items-center gap-2 hover:text-bee-yellow transition-colors">💯 Cam kết máy chuẩn Zin</span>
            <span class="inline-flex items-center gap-2 hover:text-bee-yellow transition-colors">🎧 Hỗ trợ kỹ thuật trọn đời</span>
            
            <span class="inline-flex items-center gap-2 hover:text-bee-yellow transition-colors">🚀 Giao hàng siêu tốc 2H</span>
            <span class="inline-flex items-center gap-2 hover:text-bee-yellow transition-colors">🛡️ Bảo hành chính hãng 12 tháng</span>
            <span class="inline-flex items-center gap-2 hover:text-bee-yellow transition-colors">🔄 Lỗi 1 đổi 1 trong 30 ngày</span>
            <span class="inline-flex items-center gap-2 hover:text-bee-yellow transition-colors">💳 Hỗ trợ trả góp 0%</span>
            <span class="inline-flex items-center gap-2 hover:text-bee-yellow transition-colors">💯 Cam kết máy chuẩn Zin</span>
            <span class="inline-flex items-center gap-2 hover:text-bee-yellow transition-colors">🎧 Hỗ trợ kỹ thuật trọn đời</span>
        </div>
    </div>

    <section class="py-16 bg-bee-yellow">
        <div class="container mx-auto px-4" data-aos="fade-up">
            <div class="flex flex-col md:flex-row items-center justify-between mb-10 gap-6">
                <div class="flex items-center gap-4">
                    <h2 class="text-4xl font-black text-bee-dark italic tracking-wider drop-shadow-sm">⚡ FLASH SALE</h2>
                    <div class="flex gap-2 text-bee-yellow font-bold text-lg tracking-widest">
                        <span class="bg-bee-dark px-3 py-2 rounded-lg shadow-inner" id="hours">02</span>
                        <span class="text-bee-dark py-2 font-black">:</span>
                        <span class="bg-bee-dark px-3 py-2 rounded-lg shadow-inner" id="minutes">45</span>
                        <span class="text-bee-dark py-2 font-black">:</span>
                        <span class="bg-bee-dark px-3 py-2 rounded-lg shadow-inner" id="seconds">30</span>
                    </div>
                </div>
                <a href="#" class="text-bee-dark hover:text-gray-700 font-bold underline decoration-2 underline-offset-4 transition-colors">Xem tất cả ></a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($newProducts->take(4) as $product)
                @php
                    $prodImg = $product->thumbnail ?? '';
                    $prodUrl = Str::startsWith($prodImg, ['http://', 'https://']) ? $prodImg : ($prodImg ? asset('storage/' . $prodImg) : 'https://placehold.co/400x400/f8f9fa/1a1a1a?text=BeePhone');
                    $hasSale = $product->sale_price > 0 && $product->sale_price < $product->price;
                    $discountPercent = $hasSale ? round((($product->price - $product->sale_price) / $product->price) * 100) : 15;
                @endphp
                <div class="bg-white p-5 rounded-2xl card-shadow group relative overflow-hidden border-2 border-transparent hover:border-bee-dark transition-all duration-300">
                    <div class="absolute top-3 left-3 bg-bee-dark text-bee-yellow text-xs font-black px-2 py-1 rounded z-10 shadow-md">-{{ $discountPercent }}%</div>
                    
                    <div class="absolute top-3 right-3 flex flex-col gap-2 translate-x-12 opacity-0 group-hover:translate-x-0 group-hover:opacity-100 transition-all duration-300 z-10">
                        <button class="w-10 h-10 bg-white rounded-full shadow border hover:bg-bee-yellow flex items-center justify-center">♥</button>
                        <a href="{{ route('client.product.detail', $product->slug ?? $product->id) }}" class="w-10 h-10 bg-white rounded-full shadow border hover:bg-bee-yellow flex items-center justify-center">👁</a>
                    </div>

                    <a href="{{ route('client.product.detail', $product->slug ?? $product->id) }}" class="relative mb-4 h-56 flex items-center justify-center p-2 block cursor-pointer">
                        <img alt="{{ $product->name }}" class="w-full h-full object-contain group-hover:scale-110 transition-transform duration-500" src="{{ $prodUrl }}"/>
                    </a>
                    <div>
                        <a href="{{ route('client.product.detail', $product->slug ?? $product->id) }}">
                            <h3 class="font-bold text-gray-800 mb-2 line-clamp-2 hover:text-bee-yellow transition-colors">{{ $product->name }}</h3>
                        </a>
                        <div class="flex items-center gap-2">
                            <span class="text-bee-dark font-black text-lg">{{ number_format($hasSale ? $product->sale_price : $product->price * 0.85, 0, ',', '.') }}đ</span>
                            <span class="text-gray-400 text-sm line-through font-medium">{{ number_format($product->price, 0, ',', '.') }}đ</span>
                        </div>
                    </div>
                    <div class="mt-4 w-full bg-gray-100 rounded-full h-2.5 shadow-inner overflow-hidden">
                        <div class="bg-gradient-to-r from-bee-dark to-gray-700 h-full rounded-full" style="width: 75%"></div>
                    </div>
                    <p class="text-[11px] font-semibold text-gray-500 mt-2 text-center uppercase tracking-wider">Đã bán 15/20</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="py-20">
        <div class="container mx-auto px-4" data-aos="fade-up">
            <div class="flex items-end justify-between mb-12">
                <div>
                    <h2 class="text-3xl font-bold text-bee-dark">Danh mục nổi bật</h2>
                    <p class="text-gray-400 mt-2">Tìm kiếm thiết bị phù hợp với phong cách của bạn</p>
                </div>
            </div>
            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                @foreach($categories as $category)
                @php
                    $catImg = $category->thumbnail ?? '';
                    $catUrl = Str::startsWith($catImg, ['http://', 'https://']) ? $catImg : ($catImg ? asset('storage/' . $catImg) : 'https://placehold.co/200x200/f8f9fa/1a1a1a?text=Category');
                @endphp
                <div class="group bg-bee-gray-light rounded-3xl p-8 flex flex-col items-center justify-center text-center cursor-pointer hover:bg-white border border-transparent hover:border-bee-yellow card-shadow transition-all duration-300">
                    <div class="w-24 h-24 mb-4 flex items-center justify-center bg-white rounded-full p-4 shadow-sm group-hover:scale-110 transition-transform duration-300">
                        <img alt="{{ $category->name }}" class="w-full h-full object-contain" src="{{ $catUrl }}"/>
                    </div>
                    <h3 class="font-bold text-bee-dark group-hover:text-bee-yellow">{{ $category->name }}</h3>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="py-20">
        <div class="container mx-auto px-4" data-aos="fade-up">
            <div class="flex items-end justify-between mb-12 border-b border-bee-gray-border pb-4">
                <div>
                    <h2 class="text-3xl font-bold text-bee-dark relative inline-block">
                        Sản phẩm Bán chạy
                        <span class="absolute -bottom-4 left-0 w-1/2 h-1 bg-bee-yellow rounded-full"></span>
                    </h2>
                </div>
                
                <div class="hidden md:flex gap-6 text-sm font-semibold text-gray-400" id="brand-filter-container">
                    <a href="#" data-filter="all" class="brand-filter-btn text-bee-yellow font-bold transition-colors">Tất cả</a>
                    @if(isset($brands))
                        @foreach($brands->take(4) as $brand)
                            <a href="#" data-filter="{{ $brand->id }}" class="brand-filter-btn hover:text-bee-yellow transition-colors">{{ $brand->name }}</a>
                        @endforeach
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8" id="product-list">
                @foreach($newProducts as $product)
                @php
                    $prodImg = $product->thumbnail ?? '';
                    $prodUrl = 'https://placehold.co/400x400/f8f9fa/1a1a1a?text=BeePhone';
                    if ($prodImg) {
                        if (Str::startsWith($prodImg, ['http://', 'https://'])) {
                            $prodUrl = $prodImg;
                        } else {
                            $prodUrl = Str::startsWith($prodImg, 'storage/') ? asset($prodImg) : asset('storage/' . $prodImg);
                        }
                    }
                    $hasSale = $product->sale_price > 0 && $product->sale_price < $product->price;
                @endphp
                
                <div class="product-item bg-white p-6 rounded-2xl border border-bee-gray-border hover:border-bee-yellow hover:shadow-xl transition-all duration-300 group flex flex-col" data-brand="{{ $product->brand_id ?? 'none' }}">
                    <a href="{{ route('client.product.detail', $product->slug ?? $product->id) }}" class="relative mb-6 overflow-hidden rounded-xl flex items-center justify-center h-56 bg-white block">
                        <img alt="{{ $product->name }}" class="w-full h-full object-contain mix-blend-multiply group-hover:scale-105 transition-transform duration-500" src="{{ $prodUrl }}"/>
                    </a>
                    <div class="flex-grow text-center">
                        <p class="text-xs text-gray-400 mb-1 uppercase tracking-wider font-semibold">
                            {{ $product->brand->name ?? $product->category->name ?? 'HOT' }}
                        </p>
                        <a href="{{ route('client.product.detail', $product->slug ?? $product->id) }}">
                            <h3 class="font-bold text-lg text-bee-dark mb-2 line-clamp-2 hover:text-bee-yellow transition-colors cursor-pointer">{{ $product->name }}</h3>
                        </a>
                        <div class="flex items-center justify-center gap-3">
                            <span class="text-red-500 font-bold text-lg">{{ number_format($hasSale ? $product->sale_price : $product->price, 0, ',', '.') }}đ</span>
                            @if($hasSale)
                                <span class="text-gray-300 text-sm line-through">{{ number_format($product->price, 0, ',', '.') }}đ</span>
                            @endif
                        </div>
                    </div>
                    <button class="mt-6 w-full py-2.5 rounded-xl border-2 border-bee-yellow text-bee-dark font-bold hover:bg-bee-yellow transition-colors text-sm">
                        Thêm vào giỏ hàng
                    </button>
                </div>
                @endforeach
            </div>
            
            <div id="no-product-msg" class="hidden text-center py-10 text-gray-500">
                <p>Chưa có sản phẩm nào thuộc thương hiệu này!</p>
            </div>
            
            <div class="mt-12 text-center" id="load-more-wrap">
                <button id="load-more-btn" class="inline-flex items-center gap-2 px-8 py-3 rounded-full border border-gray-300 text-gray-600 font-semibold hover:border-bee-yellow hover:bg-bee-yellow hover:text-bee-dark transition-all duration-300 shadow-sm hover:shadow-md group">
                    Xem thêm sản phẩm
                    <svg class="w-4 h-4 group-hover:translate-y-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
            </div>
        </div>
    </section>

    <section class="py-20 bg-bee-gray-light">
        <div class="container mx-auto px-4" data-aos="fade-up">
            <div class="flex items-center justify-between mb-12">
                <h2 class="text-3xl font-bold text-bee-dark">Tin tức công nghệ</h2>
                <button class="flex items-center gap-2 text-sm font-semibold hover:text-bee-yellow transition-colors group">
                    Khám phá Blog →
                </button>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @foreach($news as $post)
                @php
                    $newsImg = $post->thumbnail ?? '';
                    $newsUrl = 'https://placehold.co/600x400/f8f9fa/1a1a1a?text=News';
                    if ($newsImg) {
                        $newsUrl = Str::startsWith($newsImg, ['http://', 'https://']) ? $newsImg : asset('uploads/posts/' . $newsImg);
                    }
                @endphp
                <article class="bg-white rounded-2xl overflow-hidden card-shadow group cursor-pointer hover:-translate-y-2 transition-transform duration-300">
                    <div class="overflow-hidden h-56 relative">
                        <div class="absolute inset-0 bg-black bg-opacity-20 group-hover:bg-opacity-0 transition-all z-10"></div>
                        <img alt="{{ $post->title }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" src="{{ $newsUrl }}"/>
                    </div>
                    <div class="p-6">
                        <time class="text-xs text-bee-yellow font-bold uppercase">{{ $post->created_at->format('d/m/Y') }}</time>
                        <h3 class="text-xl font-bold text-bee-dark mt-2 group-hover:text-bee-yellow transition-colors line-clamp-2" title="{{ $post->title }}">{{ $post->title }}</h3>
                        <p class="text-gray-500 mt-3 line-clamp-2 text-sm">{{ Str::limit(strip_tags($post->content), 120) }}</p>
                    </div>
                </article>
                @endforeach
            </div>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            AOS.init({ duration: 800, once: true, offset: 100 });

            new Swiper('.heroSwiper', {
                loop: true, autoplay: { delay: 4000, disableOnInteraction: false },
                effect: 'fade', fadeEffect: { crossFade: true },
                pagination: { el: '.swiper-pagination', clickable: true },
            });

            let time = 2 * 3600 + 45 * 60 + 30; 
            setInterval(() => {
                if(time > 0) time--;
                document.getElementById('hours').innerText = String(Math.floor(time / 3600)).padStart(2, '0');
                document.getElementById('minutes').innerText = String(Math.floor((time % 3600) / 60)).padStart(2, '0');
                document.getElementById('seconds').innerText = String(time % 60).padStart(2, '0');
            }, 1000);

            const filterBtns = document.querySelectorAll('.brand-filter-btn');
            const productItems = Array.from(document.querySelectorAll('.product-item')); 
            const noProductMsg = document.getElementById('no-product-msg');
            const loadMoreBtn = document.getElementById('load-more-btn');
            const loadMoreWrap = document.getElementById('load-more-wrap');
            
            let currentItems = 8; 

            function initProducts() {
                productItems.forEach((item, index) => {
                    item.classList.remove('hidden-by-loadmore', 'hidden-by-filter');
                    if (index < currentItems) {
                        item.style.display = 'flex';
                        item.style.opacity = '1';
                    } else {
                        item.style.display = 'none';
                        item.classList.add('hidden-by-loadmore'); 
                    }
                });
                updateLoadMoreButton();
                noProductMsg.classList.add('hidden');
            }

            function updateLoadMoreButton() {
                if(!loadMoreWrap) return;
                const hiddenItems = productItems.filter(item => item.classList.contains('hidden-by-loadmore') && !item.classList.contains('hidden-by-filter'));
                if (hiddenItems.length > 0) {
                    loadMoreWrap.style.display = 'block';
                } else {
                    loadMoreWrap.style.display = 'none';
                }
            }

            initProducts();

            if (loadMoreBtn) {
                loadMoreBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const hiddenItems = productItems.filter(item => item.classList.contains('hidden-by-loadmore') && !item.classList.contains('hidden-by-filter'));
                    
                    let shownCount = 0;
                    for (let i = 0; i < hiddenItems.length; i++) {
                        if (shownCount < 4) { 
                            hiddenItems[i].style.display = 'flex';
                            hiddenItems[i].classList.remove('hidden-by-loadmore');
                            hiddenItems[i].style.opacity = '0';
                            setTimeout(() => hiddenItems[i].style.opacity = '1', 50);
                            shownCount++;
                        }
                    }
                    updateLoadMoreButton();
                });
            }

            filterBtns.forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();

                    filterBtns.forEach(b => {
                        b.classList.remove('text-bee-yellow', 'font-bold');
                        b.classList.add('text-gray-400');
                    });
                    this.classList.remove('text-gray-400');
                    this.classList.add('text-bee-yellow', 'font-bold');

                    const filterValue = this.getAttribute('data-filter');
                    let visibleCount = 0;

                    if (filterValue === 'all') {
                        initProducts();
                        return;
                    }

                    productItems.forEach(item => {
                        item.classList.remove('hidden-by-loadmore');
                        const productBrand = item.getAttribute('data-brand');
                        
                        if (productBrand === filterValue) {
                            item.style.display = 'flex';
                            item.classList.remove('hidden-by-filter');
                            item.style.opacity = '0';
                            setTimeout(() => item.style.opacity = '1', 50);
                            visibleCount++;
                        } else {
                            item.style.display = 'none';
                            item.classList.add('hidden-by-filter');
                        }
                    });

                    if(loadMoreWrap) loadMoreWrap.style.display = 'none';

                    if (visibleCount === 0) {
                        noProductMsg.classList.remove('hidden');
                    } else {
                        noProductMsg.classList.add('hidden');
                    }
                });
            });
        });
    </script>
@endsection