@php
    $prodImg = $product->thumbnail ?? '';
    $prodUrl = Str::startsWith($prodImg, ['http://', 'https://']) ? $prodImg : ($prodImg ? asset('storage/' . $prodImg) : 'https://placehold.co/400x400/f8f9fa/1a1a1a?text=BeePhone');
    
    // Chỉ lấy biến thể đang active
    $activeVariants = $product->variants ? $product->variants->where('status', 'active') : collect();

    // Logic tìm giá chuẩn cho biến thể
    $finalPrice = $product->price ?? 0;
    $finalSalePrice = $product->sale_price ?? 0;
    $isVariable = false;

    if($product->type == 'simple' && $activeVariants->isNotEmpty()) {
        $firstVar = $activeVariants->first();
        $finalPrice = $firstVar->price;
        $finalSalePrice = $firstVar->sale_price;
    } elseif($product->type == 'variable' && $activeVariants->isNotEmpty()) {
        $isVariable = true;
        // Lấy giá thấp nhất trong các biến thể active
        $minVariant = $activeVariants->sortBy(function($v) {
            return ($v->sale_price > 0 && $v->sale_price < $v->price) ? $v->sale_price : $v->price;
        })->first();

        if($minVariant) {
            $finalPrice = $minVariant->price;
            $finalSalePrice = $minVariant->sale_price;
        }
    }
    
    $hasSale = $finalSalePrice > 0 && $finalSalePrice < $finalPrice;
    $discountPercent = $hasSale ? round((($finalPrice - $finalSalePrice) / $finalPrice) * 100) : 0;
    $displayPrice = $hasSale ? $finalSalePrice : $finalPrice;
@endphp

<div class="bg-white dark:bg-white/5 p-4 rounded-xl border border-transparent hover:border-primary hover:shadow-xl transition-all duration-300 group flex flex-col h-full">
    <a href="{{ route('client.product.detail', $product->slug ?? $product->id) }}" class="relative rounded-lg overflow-hidden aspect-square bg-gray-100 mb-4 block">
        <div class="w-full h-full bg-cover bg-center group-hover:scale-110 transition-transform duration-700"
            style="background-image: url('{{ $prodUrl }}');">
        </div>
        
        {{-- Badges --}}
        <div class="absolute top-2 left-2 flex flex-col gap-1">
            @if(isset($badge))
                <span class="bg-primary text-black text-[10px] font-bold px-2 py-1 rounded uppercase shadow-sm w-fit">{{ $badge }}</span>
            @endif
            @if($product->is_featured && (!isset($badge) || strtoupper($badge) !== 'HOT'))
                <span class="bg-red-500 text-white text-[10px] font-bold px-2 py-1 rounded uppercase shadow-sm w-fit">HOT</span>
            @endif
        </div>

        @if($hasSale)
            <span class="absolute top-2 right-2 bg-red-600 text-white text-[10px] font-bold px-2 py-1 rounded-full shadow-sm">-{{ $discountPercent }}%</span>
        @endif
    </a>
    
    <a href="{{ route('client.product.detail', $product->slug ?? $product->id) }}" class="flex-1">
        <h3 class="font-bold text-lg mb-2 line-clamp-2 group-hover:text-primary transition-colors text-gray-800 dark:text-gray-100" title="{{ $product->name }}">{{ $product->name }}</h3>
        
        <div class="flex items-center gap-2 mb-4">
            <div class="flex text-yellow-400">
                @for($i=0; $i<5; $i++)
                    <span class="material-symbols-outlined text-sm">{{ $i < 4 ? 'star' : 'star_half' }}</span>
                @endfor
            </div>
            <span class="text-[10px] text-gray-400 font-bold">(128)</span>
        </div>
    </a>
    
    <div class="flex items-end justify-between mt-auto">
        <div class="flex flex-col">
            @if($isVariable) 
                <span class="text-[10px] text-gray-400 font-bold leading-none mb-1 uppercase tracking-wider">Từ</span> 
            @endif
            <div class="flex items-center gap-2">
                <span class="text-xl font-black text-red-500">{{ number_format($displayPrice, 0, ',', '.') }}₫</span>
                @if($hasSale)
                    <span class="text-xs text-gray-400 line-through">{{ number_format($finalPrice, 0, ',', '.') }}₫</span>
                @endif
            </div>
        </div>
        
        @if($isVariable)
            <a href="{{ route('client.product.detail', $product->slug ?? $product->id) }}" 
               class="bg-gray-100 dark:bg-white/10 text-gray-600 dark:text-white w-10 h-10 rounded-lg flex items-center justify-center hover:bg-primary hover:text-black transition-all shrink-0 shadow-sm" title="Chọn phiên bản">
                <span class="material-symbols-outlined">tune</span>
            </a>
        @else
            @php
                $simpleVarId = $activeVariants->first()->id ?? '';
            @endphp
            <button class="btn-add-cart-quick bg-black dark:bg-primary text-white dark:text-black w-10 h-10 rounded-lg flex items-center justify-center hover:scale-110 transition-all shrink-0 shadow-md" 
                    data-product-id="{{ $product->id }}" data-variant-id="{{ $simpleVarId }}" title="Thêm vào giỏ">
                <span class="material-symbols-outlined">add_shopping_cart</span>
            </button>
        @endif
    </div>
</div>
