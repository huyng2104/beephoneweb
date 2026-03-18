@extends('client.layouts.app')

@section('title', 'Bee Phone - Danh sách sản phẩm')

@section('content')
<style>
    .custom-scrollbar::-webkit-scrollbar { width: 4px; height: 4px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #f4c025; border-radius: 10px; }
    
    .filter-radio { display: none; }
    .filter-label { cursor: pointer; transition: all 0.2s; }
    .filter-radio:checked + .filter-label { border-color: #f4c025; background-color: rgba(244, 192, 37, 0.1); font-weight: bold; color: #181611; }
    
    .dark .filter-radio:checked + .filter-label { color: #f4c025; }

    #compare-bar { transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1); transform: translateY(150%); }
    #compare-bar.show { transform: translateY(0); }
    
    .modal-overlay { background: rgba(0,0,0,0.6); backdrop-filter: blur(4px); transition: opacity 0.3s; opacity: 0; pointer-events: none; }
    .modal-overlay.show { opacity: 1; pointer-events: auto; }
    .modal-content { transition: transform 0.3s; transform: scale(0.95); }
    .modal-overlay.show .modal-content { transform: scale(1); }
</style>

<main class="max-w-[1440px] mx-auto px-4 md:px-10 lg:px-20 py-8 relative min-h-screen">
    
    <nav class="flex items-center space-x-2 text-sm text-gray-500 dark:text-gray-400 mb-6">
        <a class="hover:text-primary transition-colors flex items-center" href="{{ route('home') }}">
            <span class="material-symbols-outlined mr-1 text-[18px]">home</span> Trang chủ
        </a>
        <span class="material-symbols-outlined text-[16px]">chevron_right</span>
        <span class="font-bold text-[#181611] dark:text-white">{{ $currentCategory->name ?? 'Tất cả Điện thoại' }}</span>
    </nav>

    <div class="mb-8">
        <h1 class="text-3xl md:text-4xl font-bold uppercase tracking-tight mb-2">{{ $currentCategory->name ?? 'Tất cả Điện thoại' }}</h1>
        <p class="text-gray-500 dark:text-gray-400 text-sm">Tìm thấy <span class="font-bold text-primary">{{ $products->total() }}</span> sản phẩm phù hợp</p>
    </div>

    <div class="flex flex-col lg:flex-row gap-8">
        
        <form id="filter-form" action="{{ route('client.products.index') }}" method="GET" class="w-full lg:w-64 flex-shrink-0 space-y-8 bg-white dark:bg-white/5 p-6 rounded-2xl border border-gray-100 dark:border-white/10 h-fit sticky top-24 shadow-sm">
            @if(request('category')) <input type="hidden" name="category" value="{{ request('category') }}"> @endif
            <input type="hidden" name="sort" id="sort-input" value="{{ $sort }}">

            <section>
                <h3 class="font-bold text-base uppercase mb-4 flex items-center gap-2 border-b border-gray-100 dark:border-white/10 pb-2">
                    <span class="material-symbols-outlined text-primary">payments</span> Khoảng giá
                </h3>
                <div class="grid grid-cols-2 gap-2 text-xs">
                    <div>
                        <input type="radio" name="price_range" id="price-1" value="under-5" class="filter-radio filter-trigger" {{ request('price_range') == 'under-5' ? 'checked' : '' }}>
                        <label for="price-1" class="filter-label block border border-gray-200 dark:border-gray-700 py-2.5 px-1 text-center rounded-lg hover:border-primary transition-colors">Dưới 5 triệu</label>
                    </div>
                    <div>
                        <input type="radio" name="price_range" id="price-2" value="5-10" class="filter-radio filter-trigger" {{ request('price_range') == '5-10' ? 'checked' : '' }}>
                        <label for="price-2" class="filter-label block border border-gray-200 dark:border-gray-700 py-2.5 px-1 text-center rounded-lg hover:border-primary transition-colors">5 - 10 triệu</label>
                    </div>
                    <div>
                        <input type="radio" name="price_range" id="price-3" value="10-15" class="filter-radio filter-trigger" {{ request('price_range') == '10-15' ? 'checked' : '' }}>
                        <label for="price-3" class="filter-label block border border-gray-200 dark:border-gray-700 py-2.5 px-1 text-center rounded-lg hover:border-primary transition-colors">10 - 15 triệu</label>
                    </div>
                    <div>
                        <input type="radio" name="price_range" id="price-4" value="over-15" class="filter-radio filter-trigger" {{ request('price_range') == 'over-15' ? 'checked' : '' }}>
                        <label for="price-4" class="filter-label block border border-gray-200 dark:border-gray-700 py-2.5 px-1 text-center rounded-lg hover:border-primary transition-colors">Trên 15 triệu</label>
                    </div>
                </div>
                @if(request('price_range'))
                    <div class="mt-3 text-right">
                        <button type="button" class="text-xs font-bold text-red-500 hover:text-red-600 hover:underline flex items-center justify-end gap-1 ml-auto w-fit" onclick="document.querySelectorAll('input[name=price_range]').forEach(r => r.checked=false); document.getElementById('filter-form').submit();">
                            <span class="material-symbols-outlined text-[14px]">close</span> Xóa lọc
                        </button>
                    </div>
                @endif
            </section>

            <section>
                <h3 class="font-bold text-base uppercase mb-4 flex items-center gap-2 border-b border-gray-100 dark:border-white/10 pb-2 mt-6">
                    <span class="material-symbols-outlined text-primary">sell</span> Thương hiệu
                </h3>
                <div class="space-y-3 max-h-[250px] overflow-y-auto custom-scrollbar pr-2">
                    @foreach($brands as $brand)
                    <label class="flex items-center space-x-3 cursor-pointer group">
                        <input type="checkbox" name="brands[]" value="{{ $brand->id }}" class="filter-trigger rounded border-gray-300 text-primary focus:ring-primary h-4 w-4 bg-transparent"
                            {{ is_array(request('brands')) && in_array($brand->id, request('brands')) ? 'checked' : '' }} />
                        <span class="text-sm group-hover:text-primary transition-colors {{ is_array(request('brands')) && in_array($brand->id, request('brands')) ? 'font-bold text-primary' : 'text-gray-600 dark:text-gray-300' }}">{{ $brand->name }}</span>
                    </label>
                    @endforeach
                </div>
            </section>
        </form>

        <div class="flex-1 pb-24">
            
            <div class="flex flex-wrap items-center justify-between mb-8 gap-4 bg-white dark:bg-white/5 p-4 rounded-xl border border-gray-100 dark:border-white/10 shadow-sm">
                <div class="flex items-center space-x-3 overflow-x-auto whitespace-nowrap custom-scrollbar pb-1 md:pb-0 w-full">
                    <span class="text-sm font-bold text-gray-500 dark:text-gray-400 mr-2 flex items-center gap-1"><span class="material-symbols-outlined text-[18px]">sort</span> Sắp xếp:</span>
                    <button type="button" class="sort-btn {{ $sort == 'newest' ? 'bg-primary text-black border-primary' : 'bg-transparent border-gray-200 dark:border-gray-700 hover:border-primary dark:hover:border-primary' }} border px-5 py-2 rounded-lg text-sm font-semibold transition-all" data-sort="newest">Mới nhất</button>
                    <button type="button" class="sort-btn {{ $sort == 'price-asc' ? 'bg-primary text-black border-primary' : 'bg-transparent border-gray-200 dark:border-gray-700 hover:border-primary dark:hover:border-primary' }} border px-5 py-2 rounded-lg text-sm font-semibold transition-all" data-sort="price-asc">Giá thấp - cao</button>
                    <button type="button" class="sort-btn {{ $sort == 'price-desc' ? 'bg-primary text-black border-primary' : 'bg-transparent border-gray-200 dark:border-gray-700 hover:border-primary dark:hover:border-primary' }} border px-5 py-2 rounded-lg text-sm font-semibold transition-all" data-sort="price-desc">Giá cao - thấp</button>
                </div>
            </div>

            @if($products->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
                    @foreach($products as $product)
                        @php
                            $prodImg = $product->thumbnail ?? '';
                            $prodUrl = Str::startsWith($prodImg, ['http://', 'https://']) ? $prodImg : ($prodImg ? asset('storage/' . $prodImg) : 'https://placehold.co/400x400/f8f9fa/1a1a1a?text=BeePhone');
                            
                            $finalPrice = $product->price;
                            $finalSalePrice = $product->sale_price;
                            $isVariable = false;

                            $specsList = is_array($product->specifications) ? $product->specifications : (json_decode($product->specifications, true) ?? []);

                            if($product->type == 'variable' && $product->variants && $product->variants->count() > 0) {
                                $isVariable = true;
                                $minVariant = $product->variants->sortBy(function($v) {
                                    return ($v->sale_price > 0 && $v->sale_price < $v->price) ? $v->sale_price : $v->price;
                                })->first();

                                $finalPrice = $minVariant->price;
                                $finalSalePrice = $minVariant->sale_price;

                                $colors = []; $rams = []; $roms = [];
                                foreach($product->variants as $var) {
                                    foreach($var->attributeValues as $val) {
                                        $attrName = mb_strtolower($val->attribute->name ?? '');
                                        if (str_contains($attrName, 'màu')) $colors[] = $val->value;
                                        elseif (str_contains($attrName, 'ram')) $rams[] = $val->value;
                                        elseif (str_contains($attrName, 'dung lượng') || str_contains($attrName, 'rom')) $roms[] = $val->value;
                                    }
                                }
                                
                                if(!empty($rams)) $specsList['Các bản RAM'] = implode(', ', array_unique($rams));
                                if(!empty($roms)) $specsList['Các bản ROM'] = implode(', ', array_unique($roms));
                                if(!empty($colors)) $specsList['Màu sắc hiện có'] = implode(', ', array_unique($colors));
                            }
                            
                            $hasSale = $finalSalePrice > 0 && $finalSalePrice < $finalPrice;
                            $discountPercent = $hasSale ? round((($finalPrice - $finalSalePrice) / $finalPrice) * 100) : 0;
                            $displayPrice = $hasSale ? $finalSalePrice : $finalPrice;
                            $priceStr = number_format($displayPrice, 0, ',', '.') . 'đ';
                        @endphp
                        
                        <article class="bg-white dark:bg-white/5 p-4 rounded-2xl border border-transparent hover:border-primary transition-all group flex flex-col shadow-sm hover:shadow-xl">
                            @if($hasSale)
                                <div class="absolute top-6 left-6 z-10">
                                    <span class="bg-red-500 text-white text-[10px] font-bold px-2.5 py-1 rounded uppercase tracking-wider shadow-sm">-{{ $discountPercent }}%</span>
                                </div>
                            @endif
                            
                            <a href="{{ route('client.product.detail', $product->slug ?? $product->id) }}" class="relative rounded-xl overflow-hidden aspect-square bg-gray-50 dark:bg-black/20 mb-4 block flex items-center justify-center p-4">
                                <img alt="{{ $product->name }}" class="w-full h-full object-contain group-hover:scale-110 transition-transform duration-500 mix-blend-multiply dark:mix-blend-normal" src="{{ $prodUrl }}"/>
                            </a>
                            
                            <div class="flex flex-col flex-grow">
                                <p class="text-[11px] text-gray-400 mb-1 uppercase tracking-widest font-bold">{{ $product->brand->name ?? 'HOT' }}</p>
                                <a href="{{ route('client.product.detail', $product->slug ?? $product->id) }}">
                                    <h3 class="font-bold text-lg mb-2 group-hover:text-primary transition-colors line-clamp-2 leading-tight" title="{{ $product->name }}">{{ $product->name }}</h3>
                                </a>
                                
                                <div class="mt-auto pt-2">
                                    <div class="flex flex-col mb-4">
                                        @if($isVariable) <span class="text-[10px] text-gray-400 font-bold leading-none mb-1 uppercase tracking-wider">Giá từ</span> @endif
                                        <div class="flex items-end gap-2">
                                            <span class="text-xl font-bold text-red-500 dark:text-red-400">{{ $priceStr }}</span>
                                            @if($hasSale)
                                                <span class="text-sm text-gray-400 line-through mb-0.5">{{ number_format($finalPrice, 0, ',', '.') }}đ</span>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-center justify-between border-t border-gray-100 dark:border-white/10 pt-4">
                                        <label class="flex items-center space-x-2 cursor-pointer group/check">
                                            <input type="checkbox" class="compare-cb w-4 h-4 border-2 border-gray-300 rounded text-primary focus:ring-primary bg-transparent" 
                                                data-id="{{ $product->id }}" 
                                                data-name="{{ $product->name }}" 
                                                data-img="{{ $prodUrl }}"
                                                data-price="{{ $priceStr }}"
                                                data-specs="{{ json_encode($specsList) }}"
                                            />
                                            <span class="text-xs font-bold text-gray-500 dark:text-gray-400 group-hover/check:text-primary transition-colors">So sánh</span>
                                        </label>

                                        @if($isVariable)
                                            <a href="{{ route('client.product.detail', $product->slug ?? $product->id) }}" 
                                               class="bg-[#f5f3f0] dark:bg-white/10 text-[#181611] dark:text-white w-10 h-10 rounded-lg flex items-center justify-center hover:bg-primary hover:text-black transition-colors shrink-0" title="Chọn phiên bản">
                                                <span class="material-symbols-outlined">tune</span>
                                            </a>
                                        @else
                                            <button class="btn-add-cart-quick bg-[#f5f3f0] dark:bg-white/10 text-[#181611] dark:text-white w-10 h-10 rounded-lg flex items-center justify-center hover:bg-primary hover:text-black transition-colors shrink-0" 
                                                    data-product-id="{{ $product->id }}" title="Thêm vào giỏ">
                                                <span class="material-symbols-outlined">add_shopping_cart</span>
                                            </button>
                                        @endif

                                    </div>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
                
                <div class="mt-12 flex justify-center pt-8">
                    {{ $products->links() }} 
                </div>
            @else
                <div class="flex flex-col items-center justify-center py-20 bg-white dark:bg-white/5 rounded-2xl border border-dashed border-gray-200 dark:border-white/10">
                    <span class="material-symbols-outlined text-6xl text-gray-300 dark:text-gray-600 mb-4">search_off</span>
                    <h3 class="text-xl font-bold mb-2">Không tìm thấy sản phẩm nào!</h3>
                    <p class="text-gray-500 dark:text-gray-400 text-sm mb-6">Thử thay đổi bộ lọc hoặc từ khóa tìm kiếm xem sao.</p>
                    <a href="{{ route('client.products.index') }}" class="bg-primary text-black px-6 py-2.5 rounded-lg font-bold hover:scale-105 transition-transform">Xóa tất cả bộ lọc</a>
                </div>
            @endif
        </div>
    </div>
</main>

<div id="compare-bar" class="fixed bottom-0 left-0 w-full z-40 px-4 pb-6 pt-4 pointer-events-none">
    <div class="max-w-4xl mx-auto pointer-events-auto">
        <div class="bg-white dark:bg-[#221e10] rounded-2xl shadow-[0_-10px_40px_rgba(0,0,0,0.1)] dark:shadow-[0_-10px_40px_rgba(0,0,0,0.5)] p-4 flex items-center justify-between border border-gray-200 dark:border-white/10">
            <div class="flex items-center space-x-4">
                <div class="flex items-center space-x-2 mr-2" id="compare-slots">
                </div>
                <div class="hidden sm:block">
                    <p class="font-bold text-sm text-[#181611] dark:text-white">So sánh cấu hình</p>
                    <p class="text-xs text-gray-500 font-medium">Đã chọn <span id="compare-count" class="text-primary font-bold">0</span>/4 sản phẩm</p>
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <button id="btn-clear-compare" class="text-xs font-bold text-red-500 hover:text-red-600 transition-colors px-2 underline">Xóa hết</button>
                <button id="btn-open-compare" class="bg-primary text-black font-bold text-xs uppercase px-6 py-3.5 rounded-xl hover:scale-105 transition-transform flex items-center gap-1">
                    <span class="material-symbols-outlined text-[18px]">compare_arrows</span> So sánh
                </button>
            </div>
        </div>
    </div>
</div>

<div id="compare-modal" class="modal-overlay fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="modal-content bg-white dark:bg-[#181611] rounded-2xl w-full max-w-6xl max-h-[90vh] flex flex-col overflow-hidden shadow-2xl border border-gray-200 dark:border-white/10">
        <div class="p-5 border-b border-gray-100 dark:border-white/10 flex justify-between items-center bg-gray-50 dark:bg-white/5">
            <h2 class="text-xl font-bold uppercase flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">tune</span>
                So sánh chi tiết
            </h2>
            <button id="btn-close-modal" class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-200 dark:bg-white/10 hover:bg-red-500 hover:text-white transition-colors font-bold">✕</button>
        </div>
        
        <div class="p-0 overflow-auto custom-scrollbar flex-1 bg-white dark:bg-[#181611]">
            <table class="w-full text-sm text-left border-collapse" id="compare-table">
            </table>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        // --- LOGIC THÊM GIỎ HÀNG NHANH ---
        const csrfToken = '{{ csrf_token() }}';
        
        document.querySelectorAll('.btn-add-cart-quick').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                
                const productId = this.getAttribute('data-product-id');
                const originalHtml = this.innerHTML;
                
                this.innerHTML = '<span class="material-symbols-outlined animate-spin text-[20px]">refresh</span>';
                this.classList.add('pointer-events-none', 'opacity-70');

                fetch('{{ route("client.cart.add") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        product_id: productId,
                        variant_id: '', // Bỏ trống vì đây là SP thường
                        quantity: 1
                    })
                })
                .then(response => response.json())
                .then(data => {
                    this.innerHTML = '<span class="material-symbols-outlined text-[20px]">check</span>';
                    this.classList.remove('pointer-events-none', 'opacity-70');
                    this.classList.replace('bg-[#f5f3f0]', 'bg-green-500'); 
                    this.classList.replace('dark:bg-white/10', 'dark:bg-green-600');
                    this.classList.remove('text-[#181611]', 'dark:text-white');
                    this.classList.add('text-white');
                    
                    setTimeout(() => {
                        this.innerHTML = originalHtml;
                        this.classList.replace('bg-green-500', 'bg-[#f5f3f0]');
                        this.classList.replace('dark:bg-green-600', 'dark:bg-white/10');
                        this.classList.add('text-[#181611]', 'dark:text-white');
                        this.classList.remove('text-white');
                    }, 2000);

                    if (data.success) {
                        const cartBadges = document.querySelectorAll('.bg-primary.text-black.rounded-full');
                        cartBadges.forEach(badge => badge.innerText = data.cart_count);
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => {
                    this.innerHTML = originalHtml;
                    this.classList.remove('pointer-events-none', 'opacity-70');
                    alert('Có lỗi xảy ra, vui lòng thử lại!');
                });
            });
        });

        // --- LOGIC LỌC ---
        const filterForm = document.getElementById('filter-form');
        document.querySelectorAll('.filter-trigger').forEach(trigger => {
            trigger.addEventListener('change', () => { document.body.style.opacity = '0.5'; filterForm.submit(); });
        });
        document.querySelectorAll('.sort-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault(); document.getElementById('sort-input').value = this.getAttribute('data-sort');
                document.body.style.opacity = '0.5'; filterForm.submit();
            });
        });

        // --- LOGIC SO SÁNH ---
        let compareList = [];
        const checkboxes = document.querySelectorAll('.compare-cb');
        const compareBar = document.getElementById('compare-bar');
        const compareSlots = document.getElementById('compare-slots');
        const compareCount = document.getElementById('compare-count');
        const modal = document.getElementById('compare-modal');
        const btnOpenModal = document.getElementById('btn-open-compare');
        const btnCloseModal = document.getElementById('btn-close-modal');
        const table = document.getElementById('compare-table');

        function updateCompareUI() {
            compareCount.innerText = compareList.length;
            if (compareList.length > 0) compareBar.classList.add('show');
            else compareBar.classList.remove('show');

            compareSlots.innerHTML = '';
            for (let i = 0; i < 4; i++) {
                if (i < compareList.length) {
                    const p = compareList[i];
                    compareSlots.innerHTML += `
                        <div class="w-12 h-12 rounded-xl bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 overflow-hidden relative p-1 group">
                            <img class="w-full h-full object-contain mix-blend-multiply dark:mix-blend-normal" src="${p.img}">
                            <button data-id="${p.id}" class="remove-cb absolute -top-1 -right-1 bg-red-500 text-white rounded-full w-4 h-4 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity text-[10px]">✕</button>
                        </div>`;
                } else {
                    compareSlots.innerHTML += `<div class="w-12 h-12 rounded-xl bg-gray-50 dark:bg-white/5 border border-dashed border-gray-200 dark:border-white/20 flex items-center justify-center text-gray-400 dark:text-gray-600 text-xs font-bold">${i+1}</div>`;
                }
            }

            document.querySelectorAll('.remove-cb').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    document.querySelector(`.compare-cb[data-id="${id}"]`).click();
                });
            });
        }

        checkboxes.forEach(cb => {
            cb.addEventListener('change', function(e) {
                const id = this.getAttribute('data-id');
                if (this.checked) {
                    if (compareList.length >= 4) {
                        e.preventDefault(); this.checked = false;
                        alert('Bạn chỉ có thể so sánh tối đa 4 sản phẩm cùng lúc!');
                        return;
                    }
                    
                    let parsedSpecs = {};
                    try { parsedSpecs = JSON.parse(this.getAttribute('data-specs') || '{}'); } 
                    catch(err) { console.log('Lỗi thông số:', err); }

                    compareList.push({
                        id: id,
                        name: this.getAttribute('data-name'),
                        img: this.getAttribute('data-img'),
                        price: this.getAttribute('data-price'),
                        specs: parsedSpecs
                    });
                } else {
                    compareList = compareList.filter(item => item.id !== id);
                }
                updateCompareUI();
            });
        });

        document.getElementById('btn-clear-compare').addEventListener('click', () => {
            compareList = []; checkboxes.forEach(cb => cb.checked = false); updateCompareUI();
        });

        btnOpenModal.addEventListener('click', () => {
            if(compareList.length < 2) {
                alert('Vui lòng chọn ít nhất 2 sản phẩm để so sánh!'); return;
            }
            
            let allSpecKeys = new Set();
            compareList.forEach(p => Object.keys(p.specs).forEach(k => allSpecKeys.add(k)));
            
            let sortedKeys = Array.from(allSpecKeys).sort((a, b) => {
                let priorityA = a.includes('RAM') ? 1 : (a.includes('ROM') ? 2 : (a.includes('Màu') ? 3 : 99));
                let priorityB = b.includes('RAM') ? 1 : (b.includes('ROM') ? 2 : (b.includes('Màu') ? 3 : 99));
                return priorityA - priorityB;
            });

            let theadHTML = `<thead><tr><th class="p-5 border border-gray-100 dark:border-white/10 w-1/5 bg-gray-50 dark:bg-white/5 text-gray-500 uppercase text-xs tracking-wider">Thông số</th>`;
            compareList.forEach(p => {
                theadHTML += `
                    <th class="p-5 border border-gray-100 dark:border-white/10 text-center w-[20%]">
                        <div class="bg-white dark:bg-white/5 p-2 rounded-xl inline-block mb-3">
                            <img src="${p.img}" class="w-24 h-24 object-contain mix-blend-multiply dark:mix-blend-normal">
                        </div>
                        <h4 class="font-bold text-[#181611] dark:text-white line-clamp-2 text-sm">${p.name}</h4>
                        <p class="text-red-500 font-bold mt-2">${p.price}</p>
                    </th>`;
            });
            theadHTML += `</tr></thead>`;

            let tbodyHTML = `<tbody>`;
            sortedKeys.forEach(key => {
                tbodyHTML += `<tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition-colors"><td class="p-4 border border-gray-100 dark:border-white/10 font-bold text-[#181611] dark:text-white bg-gray-50 dark:bg-white/5">${key}</td>`;
                compareList.forEach(p => {
                    let val = p.specs[key];
                    let textVal = Array.isArray(val) ? val.join(', ') : (val || '-');
                    tbodyHTML += `<td class="p-4 border border-gray-100 dark:border-white/10 text-center text-gray-600 dark:text-gray-300 font-medium">${textVal}</td>`;
                });
                tbodyHTML += `</tr>`;
            });
            tbodyHTML += `</tbody>`;

            table.innerHTML = theadHTML + tbodyHTML;
            modal.classList.add('show');
        });

        btnCloseModal.addEventListener('click', () => modal.classList.remove('show'));
        modal.addEventListener('click', (e) => { if(e.target === modal) modal.classList.remove('show'); });
    });
</script>
@endsection