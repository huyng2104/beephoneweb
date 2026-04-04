@extends('admin.layouts.app')

@section('content')
<div class="bg-slate-50 text-slate-900 font-display min-h-screen">
    <div class="max-w-[1400px] mx-auto w-full p-4 sm:p-8 space-y-8">
        
        <!-- 1. Header: Breadcrumbs & Actions (Matching Edit Style) -->
        <div class="mb-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 text-sm text-slate-500 mb-2">
                    <a href="{{ route('admin.products.index') }}" class="flex items-center gap-1 font-bold text-primary hover:underline">
                        <span class="material-symbols-outlined text-sm">inventory_2</span> Sản phẩm
                    </a>
                    <span class="material-symbols-outlined text-xs">chevron_right</span>
                    <span class="text-slate-900 font-medium">Chi tiết sản phẩm</span>
                </div>
                <h1 class="text-3xl font-black tracking-tight text-slate-900">Chi tiết: {{ $product->name }}</h1>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('client.product.detail', $product->id) }}" target="_blank" class="px-5 py-2.5 rounded-lg border border-slate-300 font-bold text-sm bg-white hover:bg-slate-50 transition-all flex items-center gap-2 shadow-sm text-slate-700">
                    <span class="material-symbols-outlined text-[20px]">visibility</span>
                    Xem Website
                </a>
                <a href="{{ route('admin.products.edit', $product->id) }}" class="px-6 py-2.5 rounded-lg bg-primary text-slate-900 font-bold text-sm hover:brightness-105 shadow-md shadow-primary/20 transition-all flex items-center gap-2">
                    <span class="material-symbols-outlined text-[20px]">edit</span>
                    Sửa sản phẩm
                </a>
                <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa sản phẩm này?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="p-2.5 rounded-lg border border-red-200 text-red-600 hover:bg-red-50 hover:text-red-700 transition-all">
                        <span class="material-symbols-outlined text-[20px]">delete</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- 2. Grid Layout Matching Edit (8:4) -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            <!-- LEFT COLUMN (8/12) -->
            <div class="lg:col-span-8 space-y-6">
                
                {{-- Tên sản phẩm & SKU --}}
                <div class="bg-white rounded-xl p-6 shadow-sm border border-slate-200">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-3">Tên sản phẩm</label>
                            <div class="text-xl font-bold p-3 bg-slate-50/50 rounded-lg border border-slate-100 text-slate-800">
                                {{ $product->name }}
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-3">Mã SP (SKU)</label>
                            <div class="text-xl font-bold p-3 bg-slate-50/50 rounded-lg border border-slate-100 text-slate-800">
                                {{ $product->sku ?? 'Chưa cấu hình' }}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Mô tả sản phẩm (Positioned exactly like Edit) --}}
                <div class="bg-white rounded-xl p-6 shadow-sm border border-slate-200">
                    <label class="block text-sm font-bold text-slate-700 mb-4">Mô tả sản phẩm</label>
                    <div class="relative">
                        @if($product->description)
                            <div id="admin-desc-content" class="prose prose-slate max-w-none text-sm text-slate-700 overflow-hidden leading-relaxed" style="max-height: 250px;">
                                {!! $product->description !!}
                            </div>
                            <div id="admin-desc-gradient" class="absolute bottom-0 left-0 w-full h-20 bg-gradient-to-t from-white via-white/80 to-transparent pointer-events-none transition-opacity duration-300"></div>
                            
                            <div class="mt-4 flex justify-center">
                                <button type="button" id="admin-desc-toggle" class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-6 py-1.5 rounded-full font-bold text-[11px] transition-all border border-slate-200 flex items-center gap-1 uppercase tracking-wider">
                                    <span id="toggle-text">Xem chi tiết</span>
                                    <span class="material-symbols-outlined text-[18px]">expand_more</span>
                                </button>
                            </div>
                        @else
                            <p class="text-sm text-slate-400 italic py-4">Chưa có bài viết mô tả cho sản phẩm này.</p>
                        @endif
                    </div>
                </div>

                {{-- Loại sản phẩm --}}
                <div class="bg-white rounded-xl p-6 shadow-sm border border-slate-200">
                    <label class="block text-sm font-bold text-slate-700 mb-3">Loại sản phẩm</label>
                    <div class="inline-flex items-center gap-2 px-4 py-2 bg-primary/10 text-primary border border-primary/20 rounded-lg font-bold text-sm">
                        <span class="material-symbols-outlined text-sm">{{ $product->type == 'variable' ? 'account_tree' : 'radio_button_checked' }}</span>
                        {{ $product->type == 'variable' ? 'Sản phẩm biến thể' : 'Sản phẩm đơn giản' }}
                    </div>
                </div>

                {{-- THUỘC TÍNH & BIẾN THỂ / CẤU HÌNH (Matching Edit structure) --}}
                @if($product->type == 'variable')
                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                        <div class="p-4 border-b border-slate-100 bg-slate-50/50">
                            <h4 class="font-black text-sm uppercase tracking-wider text-slate-700 flex items-center gap-2">
                                 <span class="material-symbols-outlined text-primary">inventory_2</span>
                                 Cài đặt phiên bản
                            </h4>
                        </div>
                        <div class="p-6">
                            <div id="variations-wrapper" class="space-y-4">
                                @forelse($product->variants as $index => $variant)
                                    <div class="border {{ $variant->status == 'inactive' ? 'border-red-200 opacity-50' : 'border-slate-200' }} rounded-lg overflow-hidden bg-white shadow-sm hover:border-slate-300 transition-all">
                                        <!-- Header (Collapsible) -->
                                        <div class="bg-slate-50 p-3 flex justify-between items-center border-b border-slate-200 cursor-pointer group" onclick="$(this).next().slideToggle()">
                                            <div class="flex items-center gap-3">
                                                <span class="material-symbols-outlined text-slate-400">drag_indicator</span>
                                                <strong class="text-sm text-slate-800">
                                                    #{{ $index + 1 }} —
                                                    @foreach($variant->attributeValues as $val)
                                                        {{ $val->value }}{{ !$loop->last ? ' - ' : '' }}
                                                    @endforeach
                                                </strong>
                                                @if($variant->status == 'inactive')
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-red-100 text-red-600 text-[10px] font-black uppercase rounded-full border border-red-200">
                                                        <span class="material-symbols-outlined text-[12px]">visibility_off</span> Ẩn
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="flex items-center gap-4">
                                                <div class="text-right">
                                                    <span class="text-xs font-black text-red-600 block">{{ number_format($variant->sale_price ?: $variant->price, 0, ',', '.') }}₫</span>
                                                    <span class="text-[10px] font-bold text-slate-400 capitalize">Tồn: {{ $variant->stock }}</span>
                                                </div>
                                                <span class="material-symbols-outlined text-slate-300 group-hover:text-primary transition-all">expand_more</span>
                                            </div>
                                        </div>

                                        <!-- Body (Matches Edit UI fields but read-only) -->
                                        <div class="p-5 space-y-4" style="display: none;">
                                            {{-- Row 1: Image --}}
                                            <div>
                                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Ảnh biến thể</label>
                                                <div class="w-24 h-24 rounded-xl border border-slate-200 bg-slate-50 flex items-center justify-center overflow-hidden">
                                                    @if($variant->thumbnail)
                                                        <img src="{{ asset('storage/' . $variant->thumbnail) }}" class="w-full h-full object-cover">
                                                    @else
                                                        <span class="material-symbols-outlined text-2xl text-slate-200">image</span>
                                                    @endif
                                                </div>
                                            </div>

                                            {{-- Row 2: SKU + Stock --}}
                                            <div class="grid grid-cols-2 gap-4">
                                                <div>
                                                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-wider mb-1.5 px-1">Mã SP (SKU)</label>
                                                    <div class="p-2.5 bg-slate-50 rounded-lg text-sm font-bold text-slate-800 border border-slate-100 italic-none">
                                                        {{ $variant->sku }}
                                                    </div>
                                                </div>
                                                <div>
                                                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-wider mb-1.5 px-1">Tồn kho</label>
                                                    <div class="p-2.5 bg-slate-50 rounded-lg text-sm font-bold text-slate-800 border border-slate-100">
                                                        {{ $variant->stock }}
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Row 3: Price + Sale --}}
                                            <div class="grid grid-cols-2 gap-4">
                                                <div>
                                                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-wider mb-1.5 px-1">Giá thường (₫)</label>
                                                    <div class="p-2.5 bg-slate-50 rounded-lg text-sm font-bold text-slate-500 {{ $variant->sale_price ? 'line-through' : '' }} border border-slate-100">
                                                        {{ number_format($variant->price, 0, ',', '.') }}₫
                                                    </div>
                                                </div>
                                                <div>
                                                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-wider mb-1.5 px-1">Giá KM (₫)</label>
                                                    <div class="p-2.5 bg-slate-50 rounded-lg text-sm font-bold text-red-600 border border-slate-100">
                                                        {{ $variant->sale_price ? number_format((int)$variant->sale_price, 0, ',', '.') . '₫' : 'Không có' }}
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Row 4: Specs --}}
                                            <div class="border-t border-slate-100 pt-4">
                                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-wider mb-3 px-1">
                                                    <span class="material-symbols-outlined text-sm align-middle mr-1">memory</span> Thông số kỹ thuật
                                                </label>
                                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                                    @forelse($variant->specifications as $spec)
                                                        <div class="flex items-center gap-2 p-2 bg-slate-50/50 rounded-md border border-slate-100">
                                                            <span class="text-[11px] font-bold text-slate-500 w-1/3 border-r border-slate-200 pr-2">{{ $spec->spec_key }}</span>
                                                            <span class="text-[11px] font-black text-slate-800 flex-1 pl-1">{{ $spec->spec_value }}</span>
                                                        </div>
                                                    @empty
                                                        <div class="col-span-2 text-[11px] text-slate-300 italic py-2">Chưa cấu hình thông số kĩ thuật.</div>
                                                    @endforelse
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="py-12 text-center text-slate-300 font-bold italic">Sản phẩm này hiện chưa có phiên bản nào.</div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                @else
                    @php $mainVariant = $product->variants->first(); @endphp
                    {{-- Card 1: Cấu hình chung --}}
                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                        <div class="p-4 border-b border-slate-100 bg-slate-50/50">
                            <h4 class="font-black text-sm uppercase tracking-wider text-slate-700 flex items-center gap-2">
                                <span class="material-symbols-outlined text-primary">settings_applications</span>
                                Cấu hình chung
                            </h4>
                        </div>
                        <div class="p-6 space-y-6">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-wider mb-2 px-1">Mã SP (SKU)</label>
                                    <div class="p-3 bg-slate-50 rounded-xl text-sm font-bold text-slate-800 border border-slate-100 flex items-center gap-2">
                                        <span class="material-symbols-outlined text-slate-400 text-lg">barcode</span>
                                        {{ $mainVariant->sku }}
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-wider mb-2 px-1">Tồn kho</label>
                                    <div class="p-3 bg-slate-50 rounded-xl text-sm font-bold text-slate-800 border border-slate-100 flex items-center gap-2">
                                        <span class="material-symbols-outlined text-slate-400 text-lg">inventory_2</span>
                                        {{ $mainVariant->stock }} sản phẩm
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-wider mb-2 px-1">Giá bán thường</label>
                                    <div class="p-3 bg-slate-50 rounded-xl text-sm font-bold text-slate-500 {{ $mainVariant->sale_price ? 'line-through' : '' }} border border-slate-100 flex items-center gap-2">
                                        <span class="material-symbols-outlined text-slate-400 text-lg">payments</span>
                                        {{ number_format($mainVariant->price, 0, ',', '.') }}₫
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-wider mb-2 px-1">Giá khuyến mãi</label>
                                    <div class="p-3 bg-rose-50 rounded-xl text-sm font-bold text-red-600 border border-rose-100 flex items-center gap-2">
                                        <span class="material-symbols-outlined text-rose-500 text-lg">local_offer</span>
                                        {{ $mainVariant->sale_price ? number_format((int)$mainVariant->sale_price, 0, ',', '.') . '₫' : 'Chưa thiết lập' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Card 2: Thông số kỹ thuật --}}
                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                        <div class="p-4 border-b border-slate-100 bg-slate-50/50">
                            <h4 class="font-black text-sm uppercase tracking-wider text-slate-700 flex items-center gap-2">
                                <span class="material-symbols-outlined text-primary">analytics</span>
                                Thông số kỹ thuật
                            </h4>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 gap-3">
                                @forelse($mainVariant->specifications as $spec)
                                    <div class="flex items-center gap-4 p-3 bg-slate-50/30 rounded-xl border border-slate-100 hover:bg-slate-50 transition-colors">
                                        <div class="w-1/3 text-xs font-bold text-slate-500 uppercase tracking-tight">{{ $spec->spec_key }}</div>
                                        <div class="flex-1 text-sm font-black text-slate-800">{{ $spec->spec_value }}</div>
                                    </div>
                                @empty
                                    <div class="py-8 text-center text-slate-300 italic font-bold">Chưa có thông số kỹ thuật nào được nhập.</div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                @endif

            </div>

            <!-- RIGHT SIDEBAR (4/12) -->
            <div class="lg:col-span-4 space-y-6">

                {{-- Card: Publishing Status --}}
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="p-4 border-b border-slate-100 bg-slate-50/50">
                        <h4 class="font-black text-sm uppercase tracking-wider text-slate-700">Đăng</h4>
                    </div>
                    <div class="p-5 space-y-4">
                        <div class="flex items-center justify-between text-sm">
                            <span class="flex items-center gap-2 text-slate-500 font-medium"><span class="material-symbols-outlined text-lg text-slate-400">key</span> Trạng thái:</span>
                            <span class="font-bold flex items-center gap-1 {{ $product->status == 'active' ? 'text-green-600' : 'text-slate-400' }}">
                                {{ $product->status == 'active' ? 'Hiển thị' : 'Đang ẩn' }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="flex items-center gap-2 text-slate-500 font-medium"><span class="material-symbols-outlined text-lg text-amber-500">rocket_launch</span> Nổi bật:</span>
                            <span class="font-bold text-slate-700">
                                {{ $product->is_featured ? 'Có' : 'Không' }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Card: Categories --}}
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="p-4 border-b border-slate-100 bg-slate-50/50">
                        <h4 class="font-black text-sm uppercase tracking-wider text-slate-700">Danh mục</h4>
                    </div>
                    <div class="p-5">
                        <div class="flex flex-wrap gap-2">
                             @forelse($product->categories as $category)
                                <span class="px-3 py-1.5 bg-slate-100 text-slate-600 rounded-lg text-[11px] font-bold border border-slate-200">
                                    {{ $category->name }}
                                </span>
                             @empty
                                <span class="text-slate-300 italic text-sm">Chưa chọn danh mục</span>
                             @endforelse
                        </div>
                    </div>
                </div>

                {{-- Card: Brand --}}
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="p-4 border-b border-slate-100 bg-slate-50/50">
                        <h4 class="font-black text-sm uppercase tracking-wider text-slate-700">Thương hiệu</h4>
                    </div>
                    <div class="p-5">
                        <div class="p-2.5 bg-slate-50 rounded-lg text-sm font-bold text-slate-700 border border-slate-100">
                            {{ $product->brand->name ?? 'Không có' }}
                        </div>
                    </div>
                </div>

                {{-- Card: Thumbnail --}}
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="p-4 border-b border-slate-100 bg-slate-50/50">
                        <h4 class="font-black text-sm uppercase text-slate-700">Ảnh đại diện</h4>
                    </div>
                    <div class="p-5">
                        <div class="rounded-xl overflow-hidden border border-slate-200 shadow-inner group relative aspect-square bg-slate-50 flex items-center justify-center p-2">
                            @if($product->thumbnail)
                                <img src="{{ asset('storage/' . $product->thumbnail) }}" class="w-full h-full object-contain mix-blend-multiply group-hover:scale-110 transition-transform duration-700">
                            @else
                                <span class="material-symbols-outlined text-5xl text-slate-200">image</span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Album hình ảnh --}}
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="p-4 border-b border-slate-100 bg-slate-50/50">
                        <h4 class="font-black text-sm uppercase text-slate-700">Album hình ảnh</h4>
                    </div>
                    <div class="p-5">
                        @if($product->images->count() > 0)
                            <div class="grid grid-cols-3 gap-2">
                                @foreach($product->images as $img)
                                    <div class="rounded-lg overflow-hidden border border-slate-100 relative group aspect-square bg-slate-50 p-1">
                                        <img src="{{ asset('storage/' . $img->path) }}" class="w-full h-full object-cover rounded-md">
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-xs text-slate-300 italic text-center py-4">Chưa có album ảnh phụ.</p>
                        @endif
                    </div>
                </div>

                <div class="pt-4">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 px-1">Slug sản phẩm</p>
                    <code class="block p-3 bg-slate-100 rounded-lg text-[11px] font-mono text-slate-500 break-all border border-slate-200">{{ $product->slug }}</code>
                </div>

            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const descContent = document.getElementById('admin-desc-content');
        const descToggleBtn = document.getElementById('admin-desc-toggle');
        const descGradient = document.getElementById('admin-desc-gradient');
        const toggleText = document.getElementById('toggle-text');

        if (descContent && descToggleBtn) {
            if (descContent.scrollHeight <= 250) {
                descToggleBtn.style.display = 'none';
                if(descGradient) descGradient.style.display = 'none';
            }

            descToggleBtn.addEventListener('click', function() {
                const isExpanded = descContent.style.maxHeight !== '250px';
                if (!isExpanded) {
                    descContent.style.maxHeight = descContent.scrollHeight + 'px';
                    toggleText.innerHTML = 'Thu gọn';
                    if(descGradient) descGradient.style.opacity = '0';
                    this.querySelector('.material-symbols-outlined').style.transform = 'rotate(180deg)';
                } else {
                    descContent.style.maxHeight = '250px';
                    toggleText.innerHTML = 'Xem chi tiết';
                    if(descGradient) descGradient.style.opacity = '1';
                    this.querySelector('.material-symbols-outlined').style.transform = 'rotate(0deg)';
                }
            });
        }
    });

    $(document).ready(function() {
        // Toggle variant collapsible
        window.toggleVariant = function(header) {
            $(header).next().slideToggle(200);
            const icon = $(header).find('.material-symbols-outlined:last-child');
            icon.toggleClass('rotate-180');
        };
    });
</script>

<style>
    .rotate-180 { transform: rotate(180deg); }
    .material-symbols-outlined { transition: transform 0.2s ease; }
</style>
@endsection