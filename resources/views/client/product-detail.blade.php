@extends('client.layouts.app')

@section('title', 'Bee Phone - ' . $product->name)

@section('content')
<style data-purpose="custom-styles">
    body { background-color: #F9FAFB; color: #111111; }
    .custom-shadow { box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05); }
    .spec-row:nth-child(even) { background-color: #F9FAFB; }
    .thumb-scroll::-webkit-scrollbar { height: 4px; }
    .thumb-scroll::-webkit-scrollbar-thumb { background: #E5E7EB; border-radius: 10px; }
    
    /* CSS cho hiệu ứng Zoom ảnh */
    .zoom-container { position: relative; overflow: hidden; cursor: crosshair; }
    .zoom-image { transition: transform 0.1s ease-out; width: 100%; height: 100%; object-fit: contain; }
    .zoom-container:hover .zoom-image { transform: scale(2); /* Độ phóng to x2 */ }

    /* CSS cho Toast Thông báo */
    .toast-notification {
        position: fixed; top: 20px; right: -300px; background: #10B981; color: white;
        padding: 15px 25px; border-radius: 10px; font-weight: bold; box-shadow: 0 5px 15px rgba(16, 185, 129, 0.3);
        transition: right 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55); z-index: 9999; display: flex; align-items: center; gap: 10px;
    }
    .toast-notification.show { right: 20px; }
</style>

<main class="max-w-7xl mx-auto px-4 py-8 lg:py-12 relative">
    <nav class="flex text-sm text-gray-500 mb-6" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ url('/') }}" class="hover:text-bee-yellow transition-colors">Trang chủ</a>
            </li>
            <li><div class="flex items-center"><span class="mx-2">/</span>
                <a href="#" class="hover:text-bee-yellow transition-colors">{{ $product->categories->first()?->name ?? 'Sản phẩm' }}</a>
            </div></li>
            <li aria-current="page"><div class="flex items-center"><span class="mx-2">/</span>
                <span class="text-gray-900 font-semibold truncate w-48 sm:w-auto">{{ $product->name }}</span>
            </div></li>
        </ol>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <section class="lg:col-span-7" data-purpose="product-gallery">
            <div class="bg-white rounded-2xl p-6 custom-shadow mb-4 sticky top-4">
                @php
                    $mainImg = $product->thumbnail ?? '';
                    $mainUrl = 'https://placehold.co/600x600/f8f9fa/1a1a1a?text=BeePhone';
                    if ($mainImg) {
                        $mainUrl = Str::startsWith($mainImg, ['http://', 'https://']) ? $mainImg : asset('storage/' . $mainImg);
                    }
                @endphp
                
                <div class="aspect-square flex items-center justify-center mb-6 rounded-xl bg-white p-4 border border-gray-100 zoom-container" id="image-zoom-wrapper">
                    <img alt="{{ $product->name }}" class="zoom-image mix-blend-multiply" id="main-product-image" src="{{ $mainUrl }}"/>
                </div>
                
                <div class="flex gap-4 overflow-x-auto pb-2 thumb-scroll">
                    <button class="thumb-btn flex-shrink-0 w-20 h-20 border-2 border-bee-yellow rounded-lg p-1 bg-white transition-colors">
                        <img alt="Thumb Main" class="w-full h-full object-contain mix-blend-multiply" src="{{ $mainUrl }}"/>
                    </button>
                    
                    @if(isset($product->images) && $product->images->count() > 0)
                        @foreach($product->images as $gallery)
                            @php
                                $galImg = $gallery->image_path ?? $gallery->image ?? $gallery->path ?? ''; 
                                $galUrl = 'https://placehold.co/200x200/f8f9fa/1a1a1a?text=Img';
                                if ($galImg) {
                                    $galUrl = Str::startsWith($galImg, ['http://', 'https://']) ? $galImg : asset('storage/' . $galImg);
                                }
                            @endphp
                            <button class="thumb-btn flex-shrink-0 w-20 h-20 border border-gray-200 rounded-lg p-1 bg-white hover:border-bee-yellow transition-colors">
                                <img alt="Gallery Image" class="w-full h-full object-contain mix-blend-multiply" src="{{ $galUrl }}"/>
                            </button>
                        @endforeach
                    @endif
                </div>
                
                <div class="mt-8 p-5 bg-gradient-to-r from-yellow-50 to-white rounded-xl border border-yellow-100 shadow-sm">
                    <h3 class="font-bold text-bee-dark mb-3 flex items-center gap-2">
                        <svg class="w-5 h-5 text-bee-yellow" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                        Đặc điểm nổi bật
                    </h3>
                    <div class="text-sm space-y-2 text-gray-700 leading-relaxed line-clamp-4">
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

                <div class="bg-white p-6 rounded-2xl custom-shadow relative overflow-hidden">
                    <h1 class="text-2xl lg:text-3xl font-bold text-bee-dark mb-2 pr-12">{{ $product->name }}</h1>
                    
                    <button type="button" class="absolute top-6 right-6 text-gray-400 hover:text-bee-yellow transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path></svg>
                    </button>

                    <div class="flex items-center gap-2 mb-4">
                        <div class="flex text-bee-yellow">
                            @for($i=0; $i<5; $i++)
                                <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"></path></svg>
                            @endfor
                        </div>
                        <span class="text-sm text-blue-600 font-medium hover:underline cursor-pointer">0 đánh giá</span>
                        <span class="text-sm text-gray-300">|</span>
                        <span class="text-sm {{ $product->stock > 0 ? 'text-green-600 bg-green-50' : 'text-red-600 bg-red-50' }} px-2 py-0.5 rounded font-medium">
                            {{ $product->stock > 0 ? 'Còn hàng' : 'Hết hàng' }}
                        </span>
                    </div>

                    @php
                        $hasSale = $product->sale_price > 0 && $product->sale_price < $product->price;
                    @endphp
                    <div class="flex items-baseline gap-4 mt-6 p-4 bg-gray-50 rounded-xl border border-gray-100">
                        <span id="main-price" class="text-3xl font-black text-bee-red transition-opacity duration-200">
                            {{ number_format($hasSale ? $product->sale_price : $product->price, 0, ',', '.') }}đ
                        </span>
                        <span id="old-price" class="text-lg text-gray-400 line-through transition-opacity duration-200">
                            {{ $hasSale ? number_format($product->price, 0, ',', '.') . 'đ' : '' }}
                        </span>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl custom-shadow space-y-6">
                    @if($product->type == 'variable' && isset($product->variants) && $product->variants->count() > 0)
                        <div>
                            <p class="font-bold mb-3 text-bee-dark flex items-center gap-2">
                                <svg class="w-4 h-4 text-bee-yellow" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path></svg>
                                Chọn Phiên bản / Màu sắc:
                            </p>
                            <div class="grid grid-cols-2 gap-3">
                                @foreach($product->variants as $index => $variant)
                                @php
                                    $vHasSale = $variant->sale_price > 0 && $variant->sale_price < $variant->price;
                                    $vPrice = $vHasSale ? $variant->sale_price : $variant->price;
                                @endphp
                                <button type="button" 
                                    class="variant-btn relative overflow-hidden border rounded-xl py-3 px-3 text-left transition-all duration-300 {{ $index == 0 ? 'border-2 border-bee-yellow bg-yellow-50 active-variant shadow-sm' : 'border-gray-200 hover:border-bee-yellow' }}"
                                    data-price="{{ number_format($vPrice, 0, ',', '.') }}đ"
                                    data-old-price="{{ $vHasSale ? number_format($variant->price, 0, ',', '.') . 'đ' : '' }}"
                                    data-id="{{ $variant->id }}"
                                    data-stock="{{ $variant->stock }}">
                                    <div class="check-icon absolute top-0 right-0 bg-bee-yellow text-bee-dark rounded-bl-lg p-1 {{ $index == 0 ? 'block' : 'hidden' }}">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                    </div>
                                    <span class="block font-bold text-sm text-bee-dark truncate pr-4" title="{{ $variant->sku }}">{{ $variant->sku }}</span>
                                    <span class="block text-xs text-red-500 font-semibold mt-1">{{ number_format($vPrice, 0, ',', '.') }}đ</span>
                                </button>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div>
                            <p class="font-bold mb-3 text-bee-dark">Phiên bản:</p>
                            <div class="grid grid-cols-3 gap-3">
                                <button type="button" class="border-2 border-bee-yellow rounded-xl py-3 px-2 text-center bg-yellow-50 relative">
                                    <div class="absolute top-0 right-0 bg-bee-yellow text-bee-dark rounded-bl-lg p-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                    </div>
                                    <span class="block font-bold text-sm">Mặc định</span>
                                    <span class="block text-xs text-gray-500">{{ number_format($hasSale ? $product->sale_price : $product->price, 0, ',', '.') }}đ</span>
                                </button>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="bg-white p-6 rounded-2xl custom-shadow">
                    <div class="flex items-center justify-between">
                        <p class="font-bold text-bee-dark">Số lượng:</p>
                        <div class="flex items-center border-2 border-gray-200 rounded-xl overflow-hidden focus-within:border-bee-yellow transition-colors">
                            <button type="button" id="btn-minus" class="px-4 py-2 bg-gray-50 text-gray-600 hover:bg-bee-yellow hover:text-bee-dark font-bold text-lg transition-colors">-</button>
                            <input type="number" id="input-qty" name="quantity" value="1" min="1" class="w-14 text-center border-0 focus:ring-0 p-2 font-bold text-bee-dark bg-white" readonly>
                            <button type="button" id="btn-plus" class="px-4 py-2 bg-gray-50 text-gray-600 hover:bg-bee-yellow hover:text-bee-dark font-bold text-lg transition-colors">+</button>
                        </div>
                    </div>
                    <p class="text-xs text-right text-gray-400 mt-2">Sẵn sàng giao: <span id="stock-text" class="font-bold text-green-600">{{ $product->stock }}</span> máy</p>
                </div>

                <div class="flex flex-col gap-3 mt-2">
                    <button type="button" id="btn-buy-now" class="w-full bg-gradient-to-r from-bee-yellow to-[#f1c40f] hover:from-[#f1c40f] hover:to-bee-yellow text-bee-dark font-black py-4 rounded-xl shadow-lg transition-all transform hover:-translate-y-1 flex flex-col items-center border border-yellow-400">
                        <span class="text-lg uppercase tracking-wider">Mua ngay</span>
                        <span class="text-xs font-medium opacity-80">(Giao hàng tận nơi hoặc nhận tại cửa hàng)</span>
                    </button>
                    <div class="grid grid-cols-2 gap-3">
                        <button type="button" class="border border-gray-300 text-bee-dark font-bold py-3 rounded-xl hover:border-bee-yellow hover:text-bee-yellow transition flex flex-col items-center bg-white shadow-sm">
                            <span>TRẢ GÓP 0%</span>
                            <span class="text-[10px] font-normal text-gray-500">Duyệt hồ sơ 5 phút</span>
                        </button>
                        <button type="button" class="border border-gray-300 text-bee-dark font-bold py-3 rounded-xl hover:border-bee-yellow hover:text-bee-yellow transition flex flex-col items-center bg-white shadow-sm">
                            <span>THU CŨ LÊN ĐỜI</span>
                            <span class="text-[10px] font-normal text-gray-500">Trợ giá lên đến 2tr</span>
                        </button>
                    </div>
                    <button type="button" id="btn-add-cart" class="w-full bg-bee-dark text-white font-bold py-4 rounded-xl flex items-center justify-center gap-2 hover:bg-gray-800 transition shadow-lg mt-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        THÊM VÀO GIỎ HÀNG
                    </button>
                </div>
            </form>
        </section>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 mt-12">
        <section class="lg:col-span-8 space-y-8">
            <div class="bg-white p-8 rounded-2xl custom-shadow relative">
                <h2 class="text-2xl font-bold mb-6 pb-2 border-b-2 border-bee-yellow inline-block uppercase tracking-wide">Đánh giá chi tiết</h2>
                <div class="prose max-w-none text-gray-700 leading-relaxed overflow-hidden" id="product-content" style="max-height: 800px;">
                    {!! $product->description !!}
                </div>
                <div class="mt-8 text-center relative">
                    <div class="absolute bottom-full left-0 w-full h-32 bg-gradient-to-t from-white to-transparent" id="content-gradient"></div>
                    <button id="read-more-btn" class="text-bee-dark font-bold hover:bg-bee-yellow px-8 py-3 rounded-full border-2 border-bee-dark transition-all shadow-sm">Xem thêm toàn bộ bài viết</button>
                </div>
            </div>
        </section>

        <section class="lg:col-span-4">
            <div class="bg-white p-6 rounded-2xl custom-shadow sticky top-4">
                <h2 class="text-xl font-bold mb-6 uppercase tracking-wide border-b-2 border-bee-yellow pb-2 inline-block">Thông số kỹ thuật</h2>
                <div class="border border-gray-100 rounded-xl overflow-hidden text-sm">
                    @if(is_array($product->specifications) && count($product->specifications) > 0)
                        <div class="w-full">
                            @foreach($product->specifications as $key => $value)
                            <div class="spec-row p-3 flex justify-between border-b border-gray-100 last:border-0 hover:bg-gray-50 transition-colors">
                                <span class="text-sm text-gray-500 w-1/3 font-medium">{{ $key }}:</span>
                                <span class="text-sm font-bold text-bee-dark text-right w-2/3">{{ is_array($value) ? implode(', ', $value) : $value }}</span>
                            </div>
                            @endforeach
                        </div>
                    @elseif(is_string($product->specifications) && !empty($product->specifications))
                        <div class="prose prose-sm w-full p-4">
                            {!! $product->specifications !!}
                        </div>
                    @else
                        <div class="p-8 text-center flex flex-col items-center gap-2">
                            <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span class="text-gray-400 italic">Đang cập nhật thông số...</span>
                        </div>
                    @endif
                </div>
            </div>
        </section>
    </div>
</main>

<div id="toast" class="toast-notification">
    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
    <span>Đã thêm vào giỏ hàng thành công!</span>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        // 1. ZOOM ẢNH VÀ ĐỔI ẢNH GALLERY
        const mainImage = document.getElementById('main-product-image');
        const zoomWrapper = document.getElementById('image-zoom-wrapper');
        const thumbBtns = document.querySelectorAll('.thumb-btn'); 

        // Xử lý click đổi ảnh
        thumbBtns.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                thumbBtns.forEach(b => {
                    b.classList.remove('border-bee-yellow', 'border-2');
                    b.classList.add('border-gray-200', 'border');
                });
                this.classList.remove('border-gray-200', 'border');
                this.classList.add('border-bee-yellow', 'border-2');
                
                const thumbSrc = this.querySelector('img').src;
                mainImage.style.opacity = '0.3';
                setTimeout(() => {
                    mainImage.src = thumbSrc;
                    mainImage.style.opacity = '1';
                }, 150);
            });
        });

        // Xử lý Rê chuột Zoom ảnh
        if(zoomWrapper && mainImage) {
            zoomWrapper.addEventListener('mousemove', function(e) {
                const { left, top, width, height } = this.getBoundingClientRect();
                const x = (e.clientX - left) / width * 100;
                const y = (e.clientY - top) / height * 100;
                mainImage.style.transformOrigin = `${x}% ${y}%`;
            });
            zoomWrapper.addEventListener('mouseleave', function() {
                mainImage.style.transformOrigin = `center center`;
            });
        }

        // 2. CHỌN BIẾN THỂ
        const variantBtns = document.querySelectorAll('.variant-btn');
        const mainPriceEl = document.getElementById('main-price');
        const oldPriceEl = document.getElementById('old-price');
        const stockTextEl = document.getElementById('stock-text');
        const inputVariantId = document.getElementById('selected-variant-id');
        const inputQty = document.getElementById('input-qty');
        
        let currentMaxStock = {{ $product->stock ?? 1 }}; 

        if(variantBtns.length > 0) {
            inputVariantId.value = variantBtns[0].getAttribute('data-id');
            currentMaxStock = parseInt(variantBtns[0].getAttribute('data-stock') || 0);
            if(stockTextEl) stockTextEl.innerText = currentMaxStock;
        }

        variantBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                variantBtns.forEach(b => {
                    b.classList.remove('border-2', 'border-bee-yellow', 'bg-yellow-50', 'active-variant', 'shadow-sm');
                    b.classList.add('border', 'border-gray-200');
                    b.querySelector('.check-icon')?.classList.add('hidden');
                });
                
                this.classList.remove('border', 'border-gray-200');
                this.classList.add('border-2', 'border-bee-yellow', 'bg-yellow-50', 'active-variant', 'shadow-sm');
                this.querySelector('.check-icon')?.classList.remove('hidden');

                const newPrice = this.getAttribute('data-price');
                const oldPrice = this.getAttribute('data-old-price');
                const newStock = parseInt(this.getAttribute('data-stock') || 0);
                inputVariantId.value = this.getAttribute('data-id');

                mainPriceEl.style.opacity = '0';
                if(oldPriceEl) oldPriceEl.style.opacity = '0';

                setTimeout(() => {
                    mainPriceEl.textContent = newPrice;
                    mainPriceEl.style.opacity = '1';
                    if(oldPriceEl) {
                        oldPriceEl.textContent = oldPrice;
                        oldPriceEl.style.opacity = '1';
                    }
                }, 150);

                currentMaxStock = newStock;
                if(stockTextEl) stockTextEl.innerText = currentMaxStock;
                if(inputQty) inputQty.value = 1;
            });
        });

        // 3. TĂNG GIẢM SỐ LƯỢNG
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
                else alert('Bạn đã chọn tối đa số lượng có sẵn trong kho!');
            });
        }

        // 4. XEM THÊM NỘI DUNG
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
                    readMoreBtn.textContent = 'Xem thêm toàn bộ bài viết';
                    gradient.style.display = 'block';
                    window.scrollTo({ top: contentDiv.offsetTop - 100, behavior: 'smooth' });
                }
            });
        }

        // 5. TOAST NOTIFICATION (Hiệu ứng thêm giỏ hàng)
        const btnAddCart = document.getElementById('btn-add-cart');
        const toast = document.getElementById('toast');
        let toastTimeout;

        if(btnAddCart && toast) {
            btnAddCart.addEventListener('click', function(e) {
                e.preventDefault(); // Ngăn form submit để test UI
                
                // Thêm hiệu ứng click cho nút
                this.innerHTML = `<svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> ĐANG THÊM...`;
                
                setTimeout(() => {
                    // Trả lại nút cũ
                    this.innerHTML = `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg> THÊM VÀO GIỎ HÀNG`;
                    
                    // Bật Toast
                    clearTimeout(toastTimeout);
                    toast.classList.add('show');
                    
                    toastTimeout = setTimeout(() => {
                        toast.classList.remove('show');
                    }, 3000);
                }, 500); // Giả lập loading 0.5s
            });
        }
    });
</script>
@endsection