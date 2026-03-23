@extends('client.layouts.app')

@section('title', 'Bee Phone - ' . $product->name)

@section('content')
<style data-purpose="custom-styles">
    /* Use theme colors from app layout */
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
    <span id="toast-message">Added to cart!</span>
</div>

<main class="max-w-[1440px] mx-auto px-4 md:px-10 lg:px-20 py-8 lg:py-12 relative min-h-screen">
    <nav class="flex text-sm text-gray-500 dark:text-gray-400 mb-6" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ url('/') }}" class="hover:text-primary transition-colors flex items-center">
                    <span class="material-symbols-outlined text-[18px] mr-1">home</span> Home
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <span class="material-symbols-outlined text-[16px] mx-1">chevron_right</span>
                    <a href="{{ route('client.products.index') }}" class="hover:text-primary transition-colors">{{ $product->categories->first()?->name ?? 'Products' }}</a>
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
                        <span class="material-symbols-outlined text-primary">stars</span> Key highlights
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
                            Checking...
                        </span>
                    </div>

                    <div class="flex items-baseline gap-4 mt-6 p-4 bg-gray-50 dark:bg-black/20 rounded-xl border border-gray-100 dark:border-white/5">
                        <span id="main-price" class="text-3xl font-black text-red-500 dark:text-red-400 transition-opacity duration-200">
                            Updating...
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
                            <p class="font-bold mb-3 text-[#181611] dark:text-white">Version:</p>
                            <button type="button" class="border-2 border-primary bg-primary/10 rounded-xl py-3 px-6 font-bold text-sm text-[#181611] dark:text-white">
                                Standard product
                            </button>
                        </div>
                    @endif
                </div>

                <div class="bg-white dark:bg-white/5 p-6 rounded-2xl border border-gray-100 dark:border-white/10 custom-shadow">
                    <div class="flex items-center justify-between">
                        <p class="font-bold text-[#181611] dark:text-white">Quantity:</p>
                        <div class="flex items-center border border-gray-200 dark:border-white/10 rounded-xl overflow-hidden focus-within:border-primary transition-colors bg-gray-50 dark:bg-black/20">
                            <button type="button" id="btn-minus" class="px-4 py-2 hover:bg-primary hover:text-black font-bold text-lg transition-colors">-</button>
                            <input type="number" id="input-qty" name="quantity" value="1" min="1" class="w-12 text-center border-0 focus:ring-0 p-2 font-bold bg-transparent text-[#181611] dark:text-white" readonly>
                            <button type="button" id="btn-plus" class="px-4 py-2 hover:bg-primary hover:text-black font-bold text-lg transition-colors">+</button>
                        </div>
                    </div>
                    <p class="text-xs text-right text-gray-500 dark:text-gray-400 mt-3">In stock: <span id="stock-text" class="font-bold text-primary">0</span> units</p>
                </div>

                <div class="flex flex-col gap-3 mt-2">
                    <button type="button" id="btn-buy-now" class="w-full bg-primary text-black font-bold py-4 rounded-xl shadow-lg transition-transform hover:scale-[1.02] flex flex-col items-center justify-center">
                        <span class="text-lg uppercase tracking-wider">Buy now</span>
                        <span class="text-xs font-medium opacity-80">(Home delivery)</span>
                    </button>
                    <button type="button" id="btn-add-cart" class="w-full bg-[#181611] dark:bg-white dark:text-black text-white font-bold py-4 rounded-xl flex items-center justify-center gap-2 hover:bg-primary hover:text-black dark:hover:bg-primary transition-all shadow-md mt-2 group">
                        <span class="material-symbols-outlined group-hover:scale-110 transition-transform">add_shopping_cart</span>
                        ADD TO CART
                    </button>
                </div>
            </form>
        </section>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 mt-12">
        <section class="lg:col-span-8 space-y-8">
            <div class="bg-white dark:bg-white/5 p-8 rounded-2xl border border-gray-100 dark:border-white/10 custom-shadow relative">
                <h2 class="text-2xl font-bold mb-6 pb-2 border-b-2 border-primary inline-flex items-center gap-2 uppercase text-[#181611] dark:text-white">
                    <span class="material-symbols-outlined text-primary">article</span> Product details
                </h2>
                <div class="prose prose-slate dark:prose-invert max-w-none text-gray-700 dark:text-gray-300 leading-relaxed overflow-hidden" id="product-content" style="max-height: 800px;">
                    {!! $product->description !!}
                </div>
                <div class="mt-8 text-center relative">
                    <div class="absolute bottom-full left-0 w-full h-32 bg-gradient-to-t from-white dark:from-[#221e10] to-transparent" id="content-gradient"></div>
                    <button id="read-more-btn" class="text-[#181611] dark:text-white font-bold px-8 py-3 rounded-full border-2 border-gray-200 dark:border-white/20 hover:border-primary hover:bg-primary hover:text-black transition-all relative z-10 bg-white dark:bg-[#221e10]">Read more</button>
                </div>
            </div>

            <div id="comments" class="bg-white dark:bg-white/5 p-8 rounded-2xl border border-gray-100 dark:border-white/10 custom-shadow">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6 pb-2 border-b-2 border-primary">
                    <h2 class="text-2xl font-bold inline-flex items-center gap-2 uppercase text-[#181611] dark:text-white">
                        <span class="material-symbols-outlined text-primary">chat</span> Comments
                    </h2>
                </div>

                <div class="mb-6 rounded-2xl bg-[#181611] text-white p-6 border border-white/10 shadow-lg">
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
                        <div class="md:col-span-4">
                            <div class="text-5xl font-black leading-none">
                                {{ number_format((float) ($averageRating ?? 0), 1) }}<span class="text-xl font-bold text-white/70">/5</span>
                            </div>
                            <div class="mt-3 flex items-center gap-1 text-yellow-400">
                                @php $roundedAvg = (int) round((float) ($averageRating ?? 0)); @endphp
                                @for($i = 1; $i <= 5; $i++)
                                    <span class="material-symbols-outlined text-[20px]">{{ $i <= $roundedAvg ? 'star' : 'star_outline' }}</span>
                                @endfor
                            </div>
                            <div class="mt-2 text-sm text-white/70 font-semibold">{{ $totalRatings ?? 0 }} reviews</div>

                            <a href="#comment-form"
                                class="mt-5 inline-flex w-fit items-center justify-center rounded-xl bg-red-600 px-5 py-3 text-sm font-black uppercase tracking-wider hover:bg-red-500 transition-colors">
                                Write a review
                            </a>
                        </div>

                        <div class="md:col-span-8 space-y-2">
                            @for($star = 5; $star >= 1; $star--)
                                @php
                                    $count = (int) (($ratingBreakdown[$star] ?? 0));
                                    $percent = ($totalRatings ?? 0) > 0 ? ($count / (int) $totalRatings) * 100 : 0;
                                @endphp
                                <div class="flex items-center gap-3">
                                    <div class="w-10 text-sm font-black text-yellow-400">{{ $star }}★</div>
                                    <div class="flex-1 h-2 rounded-full bg-white/10 overflow-hidden">
                                        <div class="h-full rounded-full bg-red-600" style="width: {{ $percent }}%"></div>
                                    </div>
                                    <div class="w-24 text-right text-sm font-semibold text-white/70">{{ $count }} reviews</div>
                                </div>
                            @endfor
                        </div>
                    </div>
                </div>

                @if(session('success'))
                    <div class="mb-5 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl text-sm font-semibold flex items-center gap-2">
                        <span class="material-symbols-outlined text-green-600 text-lg">check_circle</span>
                        {{ session('success') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-5 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm font-semibold">
                        <ul class="list-disc pl-5 space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form id="comment-form" action="{{ route('client.comments.store', ['slug' => $product->slug ?? $product->id]) }}" method="POST" class="space-y-4">
                    @csrf

                    @guest
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-200 mb-1">Name</label>
                                <input name="guest_name" value="{{ old('guest_name') }}" class="w-full rounded-xl border-gray-200 dark:border-white/10 dark:bg-white/5 dark:text-white focus:ring-primary focus:border-primary" />
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-200 mb-1">Email</label>
                                <input name="guest_email" value="{{ old('guest_email') }}" class="w-full rounded-xl border-gray-200 dark:border-white/10 dark:bg-white/5 dark:text-white focus:ring-primary focus:border-primary" />
                            </div>
                        </div>
                    @endguest

                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-200 mb-1">Content</label>
                        <textarea name="content" rows="4" class="w-full rounded-xl border-gray-200 dark:border-white/10 dark:bg-white/5 dark:text-white focus:ring-primary focus:border-primary" placeholder="Write a comment...">{{ old('content') }}</textarea>
                    </div>

                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <label class="text-sm font-bold text-gray-700 dark:text-gray-200">Rating</label>
                            <select name="rating" class="rounded-xl border-gray-200 dark:border-white/10 dark:bg-white/5 dark:text-white focus:ring-primary focus:border-primary">
                                <option value="">--</option>
                                @for($i = 5; $i >= 1; $i--)
                                    <option value="{{ $i }}" @selected(old('rating') == $i)>{{ $i }} ★</option>
                                @endfor
                            </select>
                        </div>

                        <button type="submit" class="bg-primary text-black font-bold px-6 py-3 rounded-xl hover:scale-[1.02] transition-transform">
                            Post comment
                        </button>
                    </div>
                </form>

                <div class="mt-8 space-y-6">
                    @forelse($comments ?? collect() as $comment)
                        <div class="p-5 rounded-2xl border border-gray-100 dark:border-white/10 bg-gray-50/50 dark:bg-white/5">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <p class="font-black text-[#181611] dark:text-white">
                                        {{ $comment->user?->name ?? $comment->guest_name ?? 'Guest' }}
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        {{ $comment->created_at?->format('d/m/Y H:i') }}
                                    </p>
                                </div>
                                <div class="text-sm text-primary font-black">
                                    @if($comment->rating)
                                        {{ $comment->rating }}/5
                                    @endif
                                </div>
                            </div>

                            <div class="mt-3 text-gray-700 dark:text-gray-200 whitespace-pre-line">{{ $comment->content }}</div>

                            <details class="mt-4">
                                <summary class="cursor-pointer text-sm font-bold text-primary">Reply</summary>
                                <form action="{{ route('client.comments.store', ['slug' => $product->slug ?? $product->id]) }}" method="POST" class="mt-3 space-y-3">
                                    @csrf
                                    <input type="hidden" name="parent_id" value="{{ $comment->id }}">

                                    @guest
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                            <input name="guest_name" value="{{ old('guest_name') }}" placeholder="Name"
                                                class="w-full rounded-xl border-gray-200 dark:border-white/10 dark:bg-white/5 dark:text-white focus:ring-primary focus:border-primary" />
                                            <input name="guest_email" value="{{ old('guest_email') }}" placeholder="Email"
                                                class="w-full rounded-xl border-gray-200 dark:border-white/10 dark:bg-white/5 dark:text-white focus:ring-primary focus:border-primary" />
                                        </div>
                                    @endguest

                                    <textarea name="content" rows="3" class="w-full rounded-xl border-gray-200 dark:border-white/10 dark:bg-white/5 dark:text-white focus:ring-primary focus:border-primary" placeholder="Write a reply..."></textarea>
                                    <button type="submit" class="bg-[#181611] dark:bg-white dark:text-black text-white font-bold px-5 py-2.5 rounded-xl hover:bg-primary hover:text-black transition-colors">
                                        Post reply
                                    </button>
                                </form>
                            </details>

                            @if($comment->children && $comment->children->count() > 0)
                                <div class="mt-5 space-y-3 pl-4 border-l-2 border-primary/30">
                                    @foreach($comment->children as $reply)
                                        <div class="p-4 rounded-2xl bg-white dark:bg-black/20 border border-gray-100 dark:border-white/10">
                                            <div class="flex items-start justify-between gap-4">
                                                <div>
                                                    <p class="font-black text-[#181611] dark:text-white text-sm">
                                                        {{ $reply->user?->name ?? $reply->guest_name ?? 'Guest' }}
                                                    </p>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                        {{ $reply->created_at?->format('d/m/Y H:i') }}
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="mt-2 text-gray-700 dark:text-gray-200 text-sm whitespace-pre-line">{{ $reply->content }}</div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="p-10 text-center text-gray-400 dark:text-gray-500 italic">No comments yet.</div>
                    @endforelse
                </div>
            </div>
        </section>

        <section class="lg:col-span-4">
            <div class="bg-white dark:bg-white/5 p-6 rounded-2xl border border-gray-100 dark:border-white/10 custom-shadow sticky top-24">
                <h2 class="text-xl font-bold mb-6 pb-2 border-b-2 border-primary inline-flex items-center gap-2 uppercase text-[#181611] dark:text-white">
                    <span class="material-symbols-outlined text-primary">memory</span> Specifications
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
                            <span class="text-gray-400 dark:text-gray-500 italic">Updating specifications...</span>
                        </div>
                    @endif
                </div>
            </div>
        </section>
    </div>
</main>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        // --- GALLERY IMAGE SWITCHING ---
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

        // --- DATA FROM SERVER TO JS ---
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
            return new Intl.NumberFormat('en-US').format(num) + '₫';
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
                stockStatusEl.textContent = 'In stock';
                stockStatusEl.className = 'text-xs text-green-600 bg-green-100 dark:bg-green-500/20 dark:text-green-400 px-2.5 py-1 rounded-full font-bold uppercase tracking-wider';
                btnBuyNow.classList.remove('btn-disabled');
                btnAddCart.classList.remove('btn-disabled');
            } else {
                stockStatusEl.textContent = 'Out of stock';
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

        // --- ATTRIBUTE SELECTION ---
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
                    stockStatusEl.textContent = 'Discontinued';
                    stockStatusEl.className = 'text-xs text-gray-600 bg-gray-100 dark:bg-white/10 dark:text-gray-300 px-2.5 py-1 rounded-full font-bold uppercase tracking-wider';
                    priceEl.textContent = 'Contact';
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

        // --- QUANTITY INCREMENT/DECREMENT ---
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
                else alert('You have reached the maximum available stock.');
            });
        }

        // --- AJAX ADD TO CART / BUY NOW ---
        function handleAddToCart(isBuyNow = false) {
            const productId = document.querySelector('input[name="product_id"]').value;
            const variantId = document.querySelector('input[name="variant_id"]').value;
            const quantity = document.getElementById('input-qty').value;

            // Update loading button UI
            const btn = isBuyNow ? btnBuyNow : btnAddCart;
            const originalHtml = btn.innerHTML;
            btn.innerHTML = `<span class="material-symbols-outlined animate-spin">refresh</span> PROCESSING...`;
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
                        // If Buy Now -> redirect to cart
                        window.location.href = "{{ route('client.cart.index') }}";
                    } else {
                        // Show success toast
                        const toast = document.querySelector('.toast-notification');
                        if(toast) {
                            toast.classList.add('show');
                            setTimeout(() => toast.classList.remove('show'), 3000);
                        }
                        
                        // Update cart count on header
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
                alert('An error occurred. Please try again.');
            });
        }

        // Bind events for the two buttons
        if(btnAddCart) btnAddCart.addEventListener('click', (e) => { e.preventDefault(); handleAddToCart(false); });
        if(btnBuyNow) btnBuyNow.addEventListener('click', (e) => { e.preventDefault(); handleAddToCart(true); });

        // --- READ MORE / LESS ---
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
                    readMoreBtn.textContent = 'Show less';
                    gradient.style.display = 'none';
                } else {
                    contentDiv.style.maxHeight = '800px';
                    readMoreBtn.textContent = 'Read more';
                    gradient.style.display = 'block';
                    window.scrollTo({ top: contentDiv.offsetTop - 100, behavior: 'smooth' });
                }
            });
        }
    });
</script>
@endsection

