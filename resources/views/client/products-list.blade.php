@extends('client.layouts.app')

@section('title', 'Bee Phone - Danh sách sản phẩm')

@section('content')
<style>
    .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; vertical-align: middle; font-size: 20px; }
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #E5E7EB; border-radius: 10px; }
    
    .filter-radio { display: none; }
    .filter-label { cursor: pointer; transition: all 0.2s; }
    .filter-radio:checked + .filter-label { border-color: #FFD700; background-color: rgba(255, 215, 0, 0.1); font-weight: bold; color: black; }
    
    /* Animation cho thanh So sánh */
    #compare-bar { transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1); transform: translateY(150%); }
    #compare-bar.show { transform: translateY(0); }
    
    /* CSS cho Modal */
    .modal-overlay { background: rgba(0,0,0,0.6); backdrop-filter: blur(4px); transition: opacity 0.3s; opacity: 0; pointer-events: none; }
    .modal-overlay.show { opacity: 1; pointer-events: auto; }
    .modal-content { transition: transform 0.3s; transform: scale(0.95); }
    .modal-overlay.show .modal-content { transform: scale(1); }
</style>

<main class="max-w-7xl mx-auto px-4 py-6 relative">
    <nav class="flex items-center space-x-2 text-sm text-muted mb-4">
        <a class="hover:text-primary flex items-center" href="{{ route('home') }}">
            <span class="material-symbols-outlined mr-1"></span> Trang chủ
        </a>
        <span class="text-secondary font-medium">{{ $currentCategory->name ?? 'Tất cả Điện thoại' }}</span>
    </nav>

    <div class="mb-8">
        <h1 class="text-3xl font-headline font-bold uppercase tracking-tight">{{ $currentCategory->name ?? 'Tất cả Điện thoại' }}</h1>
        <p class="text-muted mt-1 text-sm">Tìm thấy <span class="font-bold text-bee-dark">{{ $products->total() }}</span> sản phẩm phù hợp</p>
    </div>

    <div class="flex flex-col lg:flex-row gap-8">
        <form id="filter-form" action="{{ route('client.products.index') }}" method="GET" class="w-full lg:w-64 flex-shrink-0 space-y-8">
            @if(request('category')) <input type="hidden" name="category" value="{{ request('category') }}"> @endif
            <input type="hidden" name="sort" id="sort-input" value="{{ $sort }}">

            <section>
                <h3 class="font-bold text-sm uppercase mb-4">Khoảng giá</h3>
                <div class="grid grid-cols-2 gap-2 text-xs">
                    <div>
                        <input type="radio" name="price_range" id="price-1" value="under-5" class="filter-radio filter-trigger" {{ request('price_range') == 'under-5' ? 'checked' : '' }}>
                        <label for="price-1" class="filter-label block border border-border py-2 px-1 text-center rounded hover:border-primary">Dưới 5 triệu</label>
                    </div>
                    <div>
                        <input type="radio" name="price_range" id="price-2" value="5-10" class="filter-radio filter-trigger" {{ request('price_range') == '5-10' ? 'checked' : '' }}>
                        <label for="price-2" class="filter-label block border border-border py-2 px-1 text-center rounded hover:border-primary">5 - 10 triệu</label>
                    </div>
                    <div>
                        <input type="radio" name="price_range" id="price-3" value="10-15" class="filter-radio filter-trigger" {{ request('price_range') == '10-15' ? 'checked' : '' }}>
                        <label for="price-3" class="filter-label block border border-border py-2 px-1 text-center rounded hover:border-primary">10 - 15 triệu</label>
                    </div>
                    <div>
                        <input type="radio" name="price_range" id="price-4" value="over-15" class="filter-radio filter-trigger" {{ request('price_range') == 'over-15' ? 'checked' : '' }}>
                        <label for="price-4" class="filter-label block border border-border py-2 px-1 text-center rounded hover:border-primary">Trên 15 triệu</label>
                    </div>
                </div>
                @if(request('price_range'))
                    <div class="mt-2 text-right">
                        <button type="button" class="text-xs text-red-500 hover:underline" onclick="document.querySelectorAll('input[name=price_range]').forEach(r => r.checked=false); document.getElementById('filter-form').submit();">Xóa khoảng giá</button>
                    </div>
                @endif
            </section>

            <section>
                <h3 class="font-bold text-sm uppercase mb-4">Thương hiệu</h3>
                <div class="space-y-2 max-h-[250px] overflow-y-auto custom-scrollbar pr-2">
                    @foreach($brands as $brand)
                    <label class="flex items-center space-x-3 cursor-pointer group">
                        <input type="checkbox" name="brands[]" value="{{ $brand->id }}" class="filter-trigger rounded border-gray-300 text-primary focus:ring-primary h-4 w-4"
                            {{ is_array(request('brands')) && in_array($brand->id, request('brands')) ? 'checked' : '' }} />
                        <span class="text-sm group-hover:text-primary transition-colors {{ is_array(request('brands')) && in_array($brand->id, request('brands')) ? 'font-bold text-bee-dark' : '' }}">{{ $brand->name }}</span>
                    </label>
                    @endforeach
                </div>
            </section>
        </form>

        <div class="flex-1 pb-24">
            <div class="flex flex-wrap items-center justify-between mb-6 gap-4 bg-neutral-50 p-3 rounded-lg border border-border">
                <div class="flex items-center space-x-2 overflow-x-auto whitespace-nowrap custom-scrollbar pb-1 md:pb-0">
                    <span class="text-sm font-medium text-muted mr-2">Sắp xếp:</span>
                    <button type="button" class="sort-btn {{ $sort == 'newest' ? 'bg-primary border-primary shadow-sm' : 'bg-white border-border hover:border-primary' }} border px-4 py-1.5 rounded-full text-sm transition-colors" data-sort="newest">Mới nhất</button>
                    <button type="button" class="sort-btn {{ $sort == 'price-asc' ? 'bg-primary border-primary shadow-sm' : 'bg-white border-border hover:border-primary' }} border px-4 py-1.5 rounded-full text-sm transition-colors" data-sort="price-asc">Giá thấp - cao</button>
                    <button type="button" class="sort-btn {{ $sort == 'price-desc' ? 'bg-primary border-primary shadow-sm' : 'bg-white border-border hover:border-primary' }} border px-4 py-1.5 rounded-full text-sm transition-colors" data-sort="price-desc">Giá cao - thấp</button>
                </div>
            </div>

            @if($products->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
                    @foreach($products as $product)
                        @php
                            $prodImg = $product->thumbnail ?? '';
                            $prodUrl = Str::startsWith($prodImg, ['http://', 'https://']) ? $prodImg : ($prodImg ? asset('storage/' . $prodImg) : 'https://placehold.co/400x400/f8f9fa/1a1a1a?text=BeePhone');
                            $hasSale = $product->sale_price > 0 && $product->sale_price < $product->price;
                            $discountPercent = $hasSale ? round((($product->price - $product->sale_price) / $product->price) * 100) : 0;
                            $priceStr = number_format($hasSale ? $product->sale_price : $product->price, 0, ',', '.') . 'đ';
                        @endphp
                        
                        <article class="group relative bg-white border border-border hover:border-bee-yellow hover:shadow-xl transition-all duration-300 rounded-xl overflow-hidden flex flex-col">
                            @if($hasSale)
                                <div class="absolute top-3 left-3 z-10">
                                    <span class="bg-red-600 text-white text-[10px] font-bold px-2 py-1 rounded uppercase tracking-wider">Giảm {{ $discountPercent }}%</span>
                                </div>
                            @endif
                            
                            <a href="{{ route('client.product.detail', $product->slug ?? $product->id) }}" class="aspect-square p-6 relative overflow-hidden bg-neutral-50 block">
                                <img alt="{{ $product->name }}" class="w-full h-full object-contain group-hover:scale-110 transition-transform duration-500 mix-blend-multiply" src="{{ $prodUrl }}"/>
                            </a>
                            
                            <div class="p-4 flex flex-col flex-grow border-t border-gray-50">
                                <p class="text-xs text-gray-400 mb-1 uppercase tracking-wider font-semibold">{{ $product->brand->name ?? 'HOT' }}</p>
                                <a href="{{ route('client.product.detail', $product->slug ?? $product->id) }}">
                                    <h3 class="font-bold text-base mb-2 group-hover:text-bee-yellow transition-colors line-clamp-2">{{ $product->name }}</h3>
                                </a>
                                
                                <div class="mt-auto">
                                    <div class="flex items-baseline space-x-2">
                                        <span class="text-lg font-bold text-red-600">{{ $priceStr }}</span>
                                        @if($hasSale)
                                            <span class="text-sm text-muted line-through">{{ number_format($product->price, 0, ',', '.') }}đ</span>
                                        @endif
                                    </div>
                                    
                                    <div class="mt-4 flex items-center justify-between border-t border-border pt-4">
                                        <label class="flex items-center space-x-2 cursor-pointer group/check">
                                            <input type="checkbox" class="compare-cb w-4 h-4 border-2 border-border rounded text-bee-yellow focus:ring-bee-yellow" 
                                                data-id="{{ $product->id }}" 
                                                data-name="{{ $product->name }}" 
                                                data-img="{{ $prodUrl }}"
                                                data-price="{{ $priceStr }}"
                                                data-specs="{{ json_encode($product->specifications ?? []) }}"
                                            />
                                            <span class="text-xs font-medium text-muted group-hover/check:text-bee-dark">So sánh</span>
                                        </label>
                                        <button class="bg-black text-white p-2 rounded-lg hover:bg-bee-yellow hover:text-black transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
                
                <div class="mt-12 flex justify-center border-t border-border pt-8">
                    {{ $products->links() }} 
                </div>
            @else
                <div class="text-center py-20 bg-white rounded-xl border border-dashed border-gray-300">
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Không tìm thấy sản phẩm nào!</h3>
                    <a href="{{ route('client.products.index') }}" class="bg-black text-white px-6 py-2 rounded-lg hover:bg-primary hover:text-black transition-colors">Bỏ lọc tất cả</a>
                </div>
            @endif
        </div>
    </div>
</main>

<div id="compare-bar" class="fixed bottom-0 left-0 w-full z-40 px-4 pb-6 pt-4 pointer-events-none">
    <div class="max-w-4xl mx-auto pointer-events-auto">
        <div class="bg-bee-dark text-white rounded-2xl shadow-2xl p-4 flex items-center justify-between border border-gray-700">
            <div class="flex items-center space-x-4">
                <div class="flex items-center space-x-2 mr-2" id="compare-slots">
                </div>
                <div class="hidden sm:block">
                    <p class="font-bold text-sm text-bee-yellow">So sánh cấu hình</p>
                    <p class="text-[11px] text-gray-400">Đã chọn <span id="compare-count">0</span>/4 sản phẩm</p>
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <button id="btn-clear-compare" class="text-xs font-medium text-gray-400 hover:text-white transition-colors px-2 underline">Xóa hết</button>
                <button id="btn-open-compare" class="bg-bee-yellow text-bee-dark font-bold text-xs uppercase px-6 py-3 rounded-xl hover:bg-yellow-400 transition-all">So sánh ngay</button>
            </div>
        </div>
    </div>
</div>

<div id="compare-modal" class="modal-overlay fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="modal-content bg-white rounded-2xl w-full max-w-6xl max-h-[90vh] flex flex-col overflow-hidden shadow-2xl">
        <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
            <h2 class="text-xl font-bold text-bee-dark uppercase flex items-center gap-2">
                <svg class="w-6 h-6 text-bee-yellow" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                So sánh chi tiết cấu hình
            </h2>
            <button id="btn-close-modal" class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-200 hover:bg-red-500 hover:text-white transition-colors text-gray-600 font-bold">✕</button>
        </div>
        
        <div class="p-4 overflow-auto custom-scrollbar flex-1 bg-white">
            <table class="w-full text-sm text-left border-collapse" id="compare-table">
            </table>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- LOGIC LỌC ---
        const filterForm = document.getElementById('filter-form');
        document.querySelectorAll('.filter-trigger').forEach(trigger => {
            trigger.addEventListener('change', () => { document.body.style.opacity = '0.7'; filterForm.submit(); });
        });
        document.querySelectorAll('.sort-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault(); document.getElementById('sort-input').value = this.getAttribute('data-sort');
                document.body.style.opacity = '0.7'; filterForm.submit();
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
                        <div class="w-12 h-12 rounded-lg bg-white border border-gray-600 overflow-hidden relative p-1 group">
                            <img class="w-full h-full object-contain" src="${p.img}">
                            <button data-id="${p.id}" class="remove-cb absolute -top-1 -right-1 bg-red-500 text-white rounded-full w-4 h-4 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity text-[10px]">✕</button>
                        </div>`;
                } else {
                    compareSlots.innerHTML += `<div class="w-12 h-12 rounded-lg bg-gray-800 border border-dashed border-gray-600 flex items-center justify-center text-gray-500 text-xs">${i+1}</div>`;
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
                    
                    // THÊM TRY CATCH BẢO VỆ JS TRƯỚC LỖI PARSE JSON
                    let parsedSpecs = {};
                    try {
                        parsedSpecs = JSON.parse(this.getAttribute('data-specs') || '{}');
                    } catch(err) {
                        console.log('Sản phẩm này chưa có thông số cụ thể: ', err);
                    }

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
            const specKeysArray = Array.from(allSpecKeys);

            let theadHTML = `<thead><tr><th class="p-4 border border-gray-200 w-1/5 bg-gray-50 text-gray-500 uppercase text-xs">Thông số</th>`;
            compareList.forEach(p => {
                theadHTML += `
                    <th class="p-4 border border-gray-200 text-center w-[20%]">
                        <img src="${p.img}" class="w-32 h-32 object-contain mx-auto mb-3">
                        <h4 class="font-bold text-bee-dark line-clamp-2 text-sm">${p.name}</h4>
                        <p class="text-red-500 font-bold mt-2">${p.price}</p>
                    </th>`;
            });
            theadHTML += `</tr></thead>`;

            let tbodyHTML = `<tbody>`;
            specKeysArray.forEach(key => {
                tbodyHTML += `<tr class="hover:bg-gray-50 transition-colors"><td class="p-3 border border-gray-200 font-bold text-gray-700 bg-gray-50">${key}</td>`;
                compareList.forEach(p => {
                    let val = p.specs[key];
                    let textVal = Array.isArray(val) ? val.join(', ') : (val || '-');
                    tbodyHTML += `<td class="p-3 border border-gray-200 text-center text-gray-600">${textVal}</td>`;
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