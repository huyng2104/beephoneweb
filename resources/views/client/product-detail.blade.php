@extends('client.layouts.app')

@section('title', 'Bee Phone - ' . $product->name)

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

    .btn-disabled { opacity: 0.5; cursor: not-allowed; filter: grayscale(100%); }
</style>

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
                btnBuyNow.disabled = false; btnAddCart.disabled = false;
            } else {
                stockStatusEl.textContent = 'Hết hàng';
                stockStatusEl.className = 'text-xs text-red-600 bg-red-100 dark:bg-red-500/20 dark:text-red-400 px-2.5 py-1 rounded-full font-bold uppercase tracking-wider';
                btnBuyNow.classList.add('btn-disabled');
                btnAddCart.classList.add('btn-disabled');
                btnBuyNow.disabled = true; btnAddCart.disabled = true;
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
                    btnBuyNow.disabled = true; btnAddCart.disabled = true;
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
@endsection