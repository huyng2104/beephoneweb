@extends('admin.layouts.app')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<div class="bg-slate-50 text-slate-900 font-display min-h-screen">
    <div class="relative flex min-h-screen w-full flex-col">
        
        <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <main class="flex-1 max-w-[1400px] mx-auto w-full p-4 sm:p-8">
                
                @if($errors->any())
                    <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm font-semibold shadow-sm">
                        <ul class="list-disc pl-5 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="mb-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2 text-sm text-slate-500 mb-2">
                            <a href="{{ route('admin.products.index') }}" class="flex items-center gap-1 font-bold text-primary hover:underline">
                                <span class="material-symbols-outlined text-sm">inventory_2</span> Sản phẩm
                            </a>
                            <span class="material-symbols-outlined text-xs">chevron_right</span>
                            <span class="text-slate-900 font-medium">Sửa sản phẩm</span>
                        </div>
                        <h1 class="text-3xl font-black tracking-tight text-slate-900">Sửa: {{ $product->name }}</h1>
                    </div>
                    <div class="flex items-center gap-3">
                        <a href="{{ route('admin.products.index') }}" class="px-5 py-2.5 rounded-lg border border-slate-300 font-bold text-sm bg-white hover:bg-slate-50 transition-all text-slate-700">Hủy bỏ</a>
                        <button type="submit" class="px-6 py-2.5 rounded-lg bg-primary text-slate-900 font-bold text-sm hover:brightness-105 shadow-md shadow-primary/20 transition-all">Cập nhật sản phẩm</button>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                    <div class="lg:col-span-8 space-y-6">

                        {{-- Tên sản phẩm & SKU --}}
                        <div class="bg-white rounded-xl p-6 shadow-sm border border-slate-200">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-3">Tên sản phẩm <span class="text-red-500">*</span></label>
                                    <input name="name" value="{{ old('name', $product->name) }}" required
                                           class="w-full text-xl font-bold rounded-lg focus:border-primary focus:ring-primary py-3 px-4 bg-slate-50/50 placeholder-slate-400 @error('name') border-red-500 @else border-slate-200 @enderror"
                                           placeholder="Nhập tên sản phẩm..." type="text"/>
                                    @error('name')
                                        <span class="text-sm text-red-500 mt-2 block font-medium">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-3">Mã SP (SKU) <span class="text-red-500">*</span></label>
                                    <input name="sku" value="{{ old('sku', $product->sku) }}" 
                                           class="w-full text-xl font-bold rounded-lg focus:border-primary focus:ring-primary py-3 px-4 bg-slate-50/50 placeholder-slate-400 @error('sku') border-red-500 @else border-slate-200 @enderror" 
                                           placeholder="Để trống sẽ tự tạo theo tên..." type="text"/>
                                    @error('sku')
                                        <span class="text-sm text-red-500 mt-2 block font-medium">{{ $message }}</span>
                                    @enderror
                                    <span class="text-xs text-slate-400 mt-2 block">Cập nhật mã SKU cho sản phẩm chính.</span>
                                </div>
                            </div>
                        </div>

                        {{-- Mô tả --}}
                        <div class="bg-white rounded-xl p-6 shadow-sm border border-slate-200">
                            <label class="block text-sm font-bold text-slate-700 mb-4">Mô tả sản phẩm</label>
                            <div class="border border-slate-200 rounded-lg overflow-hidden">
                                <textarea id="description-editor" name="description"
                                          class="w-full border-none focus:ring-0 min-h-[250px] p-4 text-slate-800 leading-relaxed text-sm bg-white"
                                          placeholder="Bắt đầu viết mô tả chi tiết tại đây...">{{ old('description', $product->description ?? '') }}</textarea>
                            </div>
                        </div>

                        {{-- Loại sản phẩm --}}
                        <div class="bg-white rounded-xl p-6 shadow-sm border border-slate-200">
                            <label class="block text-sm font-bold text-slate-700 mb-3">Loại sản phẩm <span class="text-red-500">*</span></label>
                            <select name="type" id="product_type" class="w-full md:w-1/2 text-sm border-slate-200 rounded-lg py-2.5 px-4 bg-white focus:ring-primary font-bold text-primary shadow-sm cursor-pointer transition-colors">
                                <option value="simple"  {{ old('type', $product->type) == 'simple'   ? 'selected' : '' }}>Sản phẩm đơn giản</option>
                                <option value="variable"{{ old('type', $product->type) == 'variable' ? 'selected' : '' }}>Sản phẩm biến thể</option>
                            </select>
                        </div>

                        {{-- GIÁ & TỒN KHO (Sản phẩm đơn) --}}
                        <div id="simple-product-card" class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden {{ old('type', $product->type) == 'variable' ? 'hidden' : '' }}">
                            @php
                                $simpleVariant = $product->variants->first();
                            @endphp
                            <div class="p-4 border-b border-slate-100 bg-slate-50/50">
                                <h4 class="font-black text-sm uppercase tracking-wider text-slate-700">Giá & Tồn kho</h4>
                            </div>
                            <div class="p-6 space-y-5">
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
                                    <div>
                                        <label class="block text-sm font-bold text-slate-700 mb-2">Mã SP (SKU)</label>
                                        <input type="text" name="sku" value="{{ old('sku', $simpleVariant->sku ?? '') }}"
                                               class="w-full text-sm rounded-lg focus:ring-primary focus:border-primary py-2.5 px-3 bg-slate-50 @error('sku') border-red-500 @else border-slate-200 @enderror"
                                               placeholder="VD: IP15-PRM"/>
                                        @error('sku')
                                            <span class="text-xs text-red-500 mt-1.5 block font-medium">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-slate-700 mb-2">Giá bán thường (₫)</label>
                                        <input type="number" name="price" value="{{ old('price', $simpleVariant ? (int)$simpleVariant->price : '') }}"
                                               class="w-full text-sm rounded-lg focus:ring-primary focus:border-primary py-2.5 px-3 bg-slate-50 @error('price') border-red-500 @else border-slate-200 @enderror"
                                               placeholder="VD: 25000000"/>
                                        @error('price')
                                            <span class="text-xs text-red-500 mt-1.5 block font-medium">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-slate-700 mb-2">Giá khuyến mãi (₫)</label>
                                        <input type="number" name="sale_price" value="{{ old('sale_price', ($simpleVariant && $simpleVariant->sale_price) ? (int)$simpleVariant->sale_price : '') }}"
                                               class="w-full text-sm rounded-lg focus:ring-primary focus:border-primary py-2.5 px-3 bg-slate-50 @error('sale_price') border-red-500 @else border-slate-200 @enderror"
                                               placeholder="Tùy chọn"/>
                                        @error('sale_price')
                                            <span class="text-xs text-red-500 mt-1.5 block font-medium">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-slate-700 mb-2">Tồn kho</label>
                                        <input type="number" name="stock" value="{{ old('stock', $simpleVariant->stock ?? 0) }}"
                                               class="w-full text-sm rounded-lg focus:ring-primary focus:border-primary py-2.5 px-3 bg-slate-50 @error('stock') border-red-500 @else border-slate-200 @enderror"
                                               placeholder="0"/>
                                        @error('stock')
                                            <span class="text-xs text-red-500 mt-1.5 block font-medium">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            {{-- Thông số cho sản phẩm đơn --}}
                            <div class="p-4 border-t border-slate-100 bg-slate-50/50 flex justify-between items-center">
                                <h4 class="font-black text-sm uppercase tracking-wider text-slate-700">
                                    <span class="material-symbols-outlined text-base align-middle mr-1">memory</span>
                                    Thông số kỹ thuật
                                </h4>
                                <button type="button" class="btn-add-spec text-xs font-bold bg-slate-200 text-slate-700 hover:bg-slate-300 px-3 py-1.5 rounded transition-colors shadow-sm"
                                        data-target="#simple-specs-wrapper" data-name="spec">
                                    + Thêm dòng mới
                                </button>
                            </div>
                            <div class="p-5">
                                <div id="simple-specs-wrapper" class="space-y-1">
                                    @php
                                        $existingSpecs = $simpleVariant ? $simpleVariant->specifications : collect();
                                    @endphp
                                    @foreach($existingSpecs as $spec)
                                        <div class="flex items-center gap-2 spec-row mt-2 group">
                                            <span class="material-symbols-outlined text-slate-300 cursor-grab text-base flex-shrink-0">drag_indicator</span>
                                            <input type="text" name="spec_keys[]" value="{{ $spec->spec_key }}"
                                                   placeholder="Tên thông số (VD: RAM, Pin, Màn hình...)"
                                                   class="w-2/5 text-xs border-slate-200 rounded-md focus:ring-primary focus:border-primary py-1.5 px-2.5 bg-white spec-key-input">
                                            <span class="text-slate-300 text-xs flex-shrink-0">:</span>
                                            <input type="text" name="spec_values[]" value="{{ $spec->spec_value }}"
                                                   placeholder="Giá trị (VD: 8GB, 6.1 inch, 5000mAh...)"
                                                   class="flex-1 text-xs border-slate-200 rounded-md focus:ring-primary focus:border-primary py-1.5 px-2.5 bg-white spec-val-input">
                                            <button type="button" class="btn-remove-spec flex-shrink-0 text-slate-300 hover:text-red-500 p-1 rounded transition-colors opacity-0 group-hover:opacity-100" title="Xóa dòng này">
                                                <span class="material-symbols-outlined text-base">close</span>
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        {{-- THUỘC TÍNH & BIẾN THỂ (Sản phẩm biến thể) --}}
                        <div id="variable-product-card" class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden {{ old('type', $product->type) == 'simple' ? 'hidden' : '' }}">
                            <div class="p-4 border-b border-slate-100 bg-slate-50/50">
                                <h4 class="font-black text-sm uppercase tracking-wider text-slate-700">Thuộc tính & Các Biến thể</h4>
                            </div>
                            <div class="p-6">

                                <h5 class="font-bold text-sm text-slate-800 mb-3 border-l-4 border-primary pl-2">1. Thuộc tính</h5>
                                <div class="flex items-center gap-3 bg-slate-50 p-3 rounded-lg border border-slate-200 mb-6">
                                    <select id="attribute-selector" class="flex-1 text-sm border-slate-200 rounded-lg py-2 px-3 focus:ring-primary">
                                        <option value="">-- Thêm thuộc tính --</option>
                                        @foreach($attributes as $attr)
                                            <option value="{{ $attr->id }}" data-name="{{ $attr->name }}">{{ $attr->name }}</option>
                                        @endforeach
                                    </select>
                                    <button type="button" id="btn-add-attribute" class="bg-slate-200 hover:bg-slate-300 text-slate-700 px-4 py-2 rounded text-sm font-bold transition-colors h-10">Thêm</button>
                                </div>
                                <div id="attributes-wrapper" class="space-y-4 mb-8 border-b border-slate-100 pb-8"></div>

                                <h5 class="font-bold text-sm text-slate-800 mb-3 border-l-4 border-primary pl-2">2. Cài đặt biến thể</h5>
                                <div class="flex flex-col md:flex-row gap-3 mb-6">
                                    <button type="button" id="btn-generate-variations" class="bg-primary hover:brightness-105 text-slate-900 px-5 py-2.5 rounded-lg text-sm font-bold transition-all shadow-sm">Tạo tất cả thuộc tính (bổ sung)</button>
                                    <button type="button" id="btn-add-manual-variation" class="bg-slate-200 hover:bg-slate-300 text-slate-700 px-5 py-2.5 rounded-lg text-sm font-bold transition-all border border-slate-300">Tạo một cái một</button>
                                </div>

                                <div id="bulk-update-variations" class="bg-emerald-50 border border-emerald-200 p-4 rounded-lg mb-6 shadow-sm"
                                     style="display: {{ $product->variants && $product->variants->count() > 0 ? 'block' : 'none' }};">
                                    <h5 class="font-bold text-sm text-emerald-800 mb-3 flex items-center gap-2">
                                        <span class="material-symbols-outlined text-lg">bolt</span> Cập nhật nhanh cho TẤT CẢ biến thể
                                    </h5>
                                    <div class="flex flex-col md:flex-row items-center gap-3">
                                        <input type="number" id="bulk-price" class="flex-1 w-full text-sm border-emerald-200 rounded py-2 px-3" placeholder="Giá thường chung (₫)...">
                                        <input type="number" id="bulk-sale-price" class="flex-1 w-full text-sm border-emerald-200 rounded py-2 px-3" placeholder="Giá KM chung (₫)...">
                                        <input type="number" id="bulk-stock" class="w-full md:w-32 text-sm border-emerald-200 rounded py-2 px-3" placeholder="Tồn kho...">
                                        <button type="button" id="btn-apply-bulk" class="bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2 rounded text-sm font-bold transition-all shadow-sm whitespace-nowrap">Áp dụng tất cả</button>
                                    </div>
                                </div>

                                <div id="variations-wrapper" class="space-y-4 mt-4">
                                    @if($product->variants)
                                        @foreach($product->variants as $index => $variant)
                                            <div class="border border-slate-200 rounded-lg overflow-hidden bg-white variation-item shadow-sm">
                                                <div class="bg-slate-50 p-3 flex justify-between items-center border-b border-slate-200 cursor-pointer" onclick="$(this).siblings('.var-wrapper').slideToggle()">
                                                    <div class="flex items-center gap-3">
                                                        <span class="material-symbols-outlined text-slate-400">drag_indicator</span>
                                                        <strong class="text-sm text-slate-800">
                                                            #{{ $index + 1 }} —
                                                            @foreach($variant->attributeValues as $val)
                                                                {{ $val->value }}{{ !$loop->last ? ' - ' : '' }}
                                                            @endforeach
                                                        </strong>
                                                        <input type="hidden" name="variations[{{ $index }}][id]" value="{{ $variant->id }}">
                                                    </div>
                                                    <button type="button" class="text-red-400 hover:text-red-600 text-xs font-bold"
                                                            onclick="if(confirm('Xóa biến thể này?')) $(this).closest('.variation-item').remove()">Xóa</button>
                                                </div>
                                                <div class="var-wrapper">
                                                    <div class="p-4 bg-slate-50/50 border-b border-slate-100 flex flex-wrap gap-3">
                                                        <div class="attribute-selects-container flex flex-wrap gap-3 w-full">
                                                            @foreach($variant->attributeValues as $val)
                                                                <input type="hidden" name="variations[{{ $index }}][attributes][{{ $val->attribute_id }}]" value="{{ $val->id }}" class="old-hidden-attr">
                                                            @endforeach
                                                        </div>
                                                    </div>

                                                    {{-- Body biến thể --}}
                                                    <div class="var-body p-5 space-y-4 bg-white">

                                                    {{-- Hàng 1: Ảnh --}}
                                                    <div>
                                                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Ảnh biến thể</label>
                                                        <div class="relative group w-24 h-24 rounded-xl border-2 border-dashed border-slate-200 bg-slate-50 hover:border-primary transition-colors cursor-pointer flex flex-col items-center justify-center overflow-hidden variation-thumb-container">
                                                            @if($variant->thumbnail)
                                                                <img src="{{ asset('storage/' . $variant->thumbnail) }}" class="absolute inset-0 w-full h-full object-cover z-0 rounded-xl">
                                                            @endif
                                                            <input type="file" name="variations[{{ $index }}][thumbnail]" accept="image/*"
                                                                   class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10 var-thumb-input">
                                                            <span class="material-symbols-outlined text-2xl text-slate-300 group-hover:text-primary transition-colors {{ $variant->thumbnail ? 'hidden' : '' }}">add_photo_alternate</span>
                                                        </div>
                                                    </div>

                                                    {{-- Hàng 2: SKU + Tồn kho --}}
                                                    <div class="grid grid-cols-2 gap-4">
                                                        <div>
                                                            <label class="block text-xs font-bold text-slate-600 mb-1.5">Mã SP (SKU)</label>
                                                            <input type="text" name="variations[{{ $index }}][sku]" value="{{ $variant->sku }}"
                                                                   class="w-full text-sm border-slate-200 rounded-lg focus:ring-primary focus:border-primary py-2 px-3 bg-slate-50">
                                                        </div>
                                                        <div>
                                                            <label class="block text-xs font-bold text-slate-600 mb-1.5">Tồn kho</label>
                                                            <input type="number" name="variations[{{ $index }}][stock]" value="{{ $variant->stock }}"
                                                                   class="w-full text-sm border-slate-200 rounded-lg focus:ring-primary focus:border-primary py-2 px-3 bg-slate-50">
                                                        </div>
                                                    </div>

                                                    {{-- Hàng 3: Giá thường + Giá KM --}}
                                                    <div class="grid grid-cols-2 gap-4">
                                                        <div>
                                                            <label class="block text-xs font-bold text-slate-600 mb-1.5">Giá thường (₫) <span class="text-red-500">*</span></label>
                                                            <input type="number" name="variations[{{ $index }}][price]" value="{{ (int)$variant->price }}"
                                                                   class="w-full text-sm border-slate-200 rounded-lg focus:ring-primary focus:border-primary py-2 px-3 bg-slate-50">
                                                        </div>
                                                        <div>
                                                            <label class="block text-xs font-bold text-slate-600 mb-1.5">Giá KM (₫)</label>
                                                            <input type="number" name="variations[{{ $index }}][sale_price]" value="{{ $variant->sale_price ? (int)$variant->sale_price : '' }}"
                                                                   class="w-full text-sm border-slate-200 rounded-lg focus:ring-primary focus:border-primary py-2 px-3 bg-slate-50"
                                                                   placeholder="Tùy chọn">
                                                        </div>
                                                    </div>

                                                    {{-- Hàng 4: Trạng thái hiển thị --}}
                                                    <div class="pt-2">
                                                        <label class="flex items-center gap-2 cursor-pointer w-max">
                                                            <input type="hidden" name="variations[{{ $index }}][status]" value="inactive">
                                                            <input type="checkbox" name="variations[{{ $index }}][status]" value="active" {{ ($variant->status ?? 'active') == 'active' ? 'checked' : '' }} class="rounded text-primary border-slate-300 focus:ring-primary">
                                                            <span class="text-xs font-bold text-slate-700">Hiển thị (Kích hoạt)</span>
                                                        </label>
                                                    </div>

                                                    {{-- Dưới cùng: Thông số --}}
                                                    <div class="border-t border-slate-100 pt-4">
                                                        <div class="flex justify-between items-center mb-2">
                                                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">
                                                                <span class="material-symbols-outlined text-sm align-middle">memory</span> Thông số kỹ thuật
                                                            </label>
                                                            <button type="button"
                                                                    class="btn-add-spec text-[11px] font-semibold bg-slate-100 text-slate-600 hover:bg-primary hover:text-slate-900 px-3 py-1.5 rounded-lg transition-colors border border-slate-200"
                                                                    data-target="#edit-var-specs-{{ $index }}"
                                                                    data-name="variations[{{ $index }}][spec]">
                                                                + Thêm dòng
                                                            </button>
                                                        </div>
                                                        <div id="edit-var-specs-{{ $index }}" class="space-y-2">
                                                            @foreach($variant->specifications as $spec)
                                                                <div class="flex items-center gap-2 spec-row mt-2 group">
                                                                    <span class="material-symbols-outlined text-slate-300 cursor-grab text-base flex-shrink-0">drag_indicator</span>
                                                                    <input type="text" name="variations[{{ $index }}][spec_keys][]" value="{{ $spec->spec_key }}"
                                                                           placeholder="Tên thông số"
                                                                           class="w-2/5 text-xs border-slate-200 rounded-md focus:ring-primary focus:border-primary py-1.5 px-2.5 bg-white spec-key-input">
                                                                    <span class="text-slate-300 text-xs flex-shrink-0">:</span>
                                                                    <input type="text" name="variations[{{ $index }}][spec_values][]" value="{{ $spec->spec_value }}"
                                                                           placeholder="Giá trị"
                                                                           class="flex-1 text-xs border-slate-200 rounded-md focus:ring-primary focus:border-primary py-1.5 px-2.5 bg-white spec-val-input">
                                                                    <button type="button" class="btn-remove-spec flex-shrink-0 text-slate-300 hover:text-red-500 p-1 rounded transition-colors opacity-0 group-hover:opacity-100">
                                                                        <span class="material-symbols-outlined text-base">close</span>
                                                                    </button>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>

                                                    </div> {{-- End var-body --}}
                                                </div> {{-- End var-wrapper --}}
                                            </div>
                                        @endforeach
                                    @endif
                                </div>

                            </div>
                        </div>

                    </div>

                    {{-- RIGHT SIDEBAR --}}
                    <div class="lg:col-span-4 space-y-6">

                        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                            <div class="p-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                                <h4 class="font-black text-sm uppercase tracking-wider text-slate-700">Đăng</h4>
                            </div>
                            <div class="p-5 space-y-4">
                                <div class="flex items-center justify-between text-sm">
                                    <span class="flex items-center gap-2 text-slate-500 font-medium"><span class="material-symbols-outlined text-lg text-slate-400">key</span> Trạng thái:</span>
                                    <span class="font-bold flex items-center gap-1">
                                        <select name="status" class="border-none bg-transparent p-0 py-0.5 text-sm font-bold focus:ring-0 cursor-pointer">
                                            <option value="active"   {{ old('status', $product->status) == 'active'   ? 'selected' : '' }}>Hiển thị</option>
                                            <option value="inactive" {{ old('status', $product->status) == 'inactive' ? 'selected' : '' }}>Bản nháp</option>
                                        </select>
                                    </span>
                                </div>
                                <div class="flex items-center justify-between text-sm">
                                    <span class="flex items-center gap-2 text-slate-500 font-medium"><span class="material-symbols-outlined text-lg text-amber-500">rocket_launch</span> Nổi bật:</span>
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="hidden" name="is_featured" value="0">
                                        <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $product->is_featured) ? 'checked' : '' }} class="rounded text-primary border-slate-300 focus:ring-primary">
                                        <span class="text-sm font-bold text-slate-700">Sản phẩm nổi bật</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                            <div class="p-4 border-b border-slate-100 bg-slate-50/50">
                                <h4 class="font-black text-sm uppercase tracking-wider text-slate-700">Danh mục <span class="text-red-500">*</span></h4>
                            </div>
                            <div class="p-5">
                                <select name="category_ids[]" multiple class="select2-categories w-full">
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ in_array($category->id, $product->categories->pluck('id')->toArray()) ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_ids')
                                    <p class="text-red-500 text-[10px] mt-2 font-bold uppercase">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                            <div class="p-4 border-b border-slate-100 bg-slate-50/50">
                                <h4 class="font-black text-sm uppercase tracking-wider text-slate-700">Thương hiệu</h4>
                            </div>
                            <div class="p-5">
                                <select name="brand_id" class="w-full text-sm border-slate-200 rounded-lg py-2.5 px-3 bg-slate-50 focus:ring-primary">
                                    <option value="">-- Không có --</option>
                                    @foreach($brands as $brand)
                                        <option value="{{ $brand->id }}" {{ old('brand_id', $product->brand_id) == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                            <div class="p-4 border-b border-slate-100 bg-slate-50/50">
                                <h4 class="font-black text-sm uppercase text-slate-700">Ảnh đại diện</h4>
                            </div>
                            <div class="p-5 text-center">
                                <div class="mb-4 rounded-xl overflow-hidden border border-slate-200 shadow-inner group relative">
                                    @if($product->thumbnail)
                                        <img src="{{ asset('storage/' . $product->thumbnail) }}" id="preview-thumbnail" class="w-full aspect-square object-cover">
                                    @else
                                        <img src="" id="preview-thumbnail" class="w-full aspect-square object-cover hidden">
                                        <div class="w-full aspect-square flex items-center justify-center bg-slate-50 text-slate-300" id="thumb-placeholder">
                                            <span class="material-symbols-outlined text-5xl">image</span>
                                        </div>
                                    @endif
                                </div>
                                <input type="file" name="thumbnail" id="input-thumbnail" accept="image/*"
                                       class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-primary file:text-slate-900 cursor-pointer">
                            </div>
                        </div>

                        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                            <div class="p-4 border-b border-slate-100 bg-slate-50/50">
                                <h4 class="font-black text-sm uppercase text-slate-700">Album hình ảnh</h4>
                            </div>
                            <div class="p-5 overflow-hidden">
                                {{-- Preview cho ảnh đã có --}}
                                <div class="grid grid-cols-3 sm:grid-cols-4 gap-3 mb-4" id="gallery-manager-container">
                                    @foreach($product->images as $img)
                                        <div class="relative aspect-square rounded-lg border border-slate-200 overflow-hidden group shadow-sm bg-white p-1 existing-img-item" data-id="{{ $img->id }}">
                                            <img src="{{ asset('storage/' . $img->path) }}" class="w-full h-full object-cover rounded-md">
                                            <button type="button" class="absolute top-1 right-1 size-5 bg-red-600 text-white rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity shadow-md hover:bg-red-700 btn-remove-existing-img">
                                                <span class="material-symbols-outlined text-[14px]">close</span>
                                            </button>
                                        </div>
                                    @endforeach
                                    {{-- Previews for NEW images will go here --}}
                                </div>

                                {{-- Container for new previews --}}
                                <div id="gallery-new-previews" class="grid grid-cols-3 sm:grid-cols-4 gap-3 mb-4"></div>

                                <div class="relative group aspect-[2/1] rounded-lg border-2 border-dashed border-slate-200 bg-slate-50 hover:border-primary transition-colors cursor-pointer flex flex-col items-center justify-center overflow-hidden">
                                    <input type="file" name="images[]" id="gallery-input" multiple accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                                    <div class="flex flex-col items-center justify-center pointer-events-none" id="gallery-placeholder">
                                        <span class="material-symbols-outlined text-2xl text-slate-300 group-hover:text-primary transition-colors">collections</span>
                                        <span class="text-[10px] font-black uppercase mt-1 text-slate-400">Chọn thêm ảnh nhiều ảnh</span>
                                    </div>
                                </div>

                                {{-- Hidden inputs to track deleted existing images --}}
                                <div id="deleted-images-wrapper"></div>
                            </div>
                        </div>

                    </div>
                </div>
            </main>
        </form>
    </div>
</div>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #f8f9fa; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    .tox-notifications-container { display: none !important; }
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.2/tinymce.min.js" referrerpolicy="origin"></script>

<script>
    const baseUrl = '/admin';

    document.addEventListener("DOMContentLoaded", function() {
        // --- TINYMCE ---
        tinymce.init({
            selector: '#description-editor',
            height: 500,
            plugins: ['advlist','autolink','lists','link','image','charmap','preview','anchor','searchreplace','visualblocks','code','fullscreen','insertdatetime','media','table','wordcount'],
            toolbar: 'undo redo | blocks | bold italic | alignleft aligncenter alignright | bullist numlist | link image | removeformat | code',
            content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; font-size: 15px; color: #334155; }',
            paste_data_images: true, branding: false, promotion: false
        });

        // --- SELECT2 CATEGORIES ---
        $('.select2-categories').select2({
            placeholder: "Chọn một hoặc nhiều danh mục...",
            width: '100%',
            allowClear: true
        });

        // --- PREVIEW THUMBNAIL ---
        document.getElementById('input-thumbnail')?.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                let reader = new FileReader();
                reader.onload = (e) => {
                    let img = document.getElementById('preview-thumbnail');
                    if (img) {
                        img.src = e.target.result;
                        img.classList.remove('hidden');
                    }
                    let ph = document.getElementById('thumb-placeholder');
                    if (ph) ph.style.display = 'none';
                };
                reader.readAsDataURL(this.files[0]);
            }
        });

        // --- GALLERY MANAGER (NEW + EXISTING) ---
        let galleryDataTransfer = new DataTransfer();
        const galleryInput = document.getElementById('gallery-input');
        const galleryNewPreviews = document.getElementById('gallery-new-previews');
        const galleryPlaceholder = document.getElementById('gallery-placeholder');

        if (galleryInput) {
            galleryInput.addEventListener('change', function() {
                for (let i = 0; i < this.files.length; i++) {
                    galleryDataTransfer.items.add(this.files[i]);
                }
                this.files = galleryDataTransfer.files;
                renderNewGalleryPreviews();
            });
        }

        function renderNewGalleryPreviews() {
            galleryNewPreviews.innerHTML = '';
            const files = galleryDataTransfer.files;
            
            if (files.length > 0) {
                galleryPlaceholder.querySelector('span.text-\\[10px\\]').textContent = `Thêm mới (${files.length} đã chọn)`;
            } else {
                galleryPlaceholder.querySelector('span.text-\\[10px\\]').textContent = 'Chọn thêm ảnh nhiều ảnh';
            }

            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                const reader = new FileReader();
                reader.onload = function(e) {
                    const previewHtml = `
                        <div class="relative aspect-square rounded-lg border border-primary/30 overflow-hidden group shadow-sm bg-indigo-50/10 p-1">
                            <img src="${e.target.result}" class="w-full h-full object-cover rounded-md">
                            <span class="absolute top-1 left-1 px-1.5 py-0.5 bg-primary text-slate-900 text-[8px] font-black uppercase rounded shadow-sm">Mới</span>
                            <button type="button" class="absolute top-1 right-1 size-5 bg-red-600 text-white rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity shadow-md hover:bg-red-700 btn-remove-new-img" data-index="${i}">
                                <span class="material-symbols-outlined text-[14px]">close</span>
                            </button>
                        </div>
                    `;
                    galleryNewPreviews.insertAdjacentHTML('beforeend', previewHtml);
                };
                reader.readAsDataURL(file);
            }
        }

        $(document).on('click', '.btn-remove-new-img', function(e) {
            e.preventDefault();
            const indexToRemove = parseInt($(this).data('index'));
            const newDataTransfer = new DataTransfer();
            const files = galleryDataTransfer.files;
            for (let i = 0; i < files.length; i++) {
                if (i !== indexToRemove) newDataTransfer.items.add(files[i]);
            }
            galleryDataTransfer = newDataTransfer;
            galleryInput.files = galleryDataTransfer.files;
            renderNewGalleryPreviews();
        });

        // Xử lý xóa ảnh đã có
        $(document).on('click', '.btn-remove-existing-img', function() {
            const item = $(this).closest('.existing-img-item');
            const imgId = item.data('id');
            if (confirm('Bạn có chắc muốn xóa ảnh này khỏi hệ thống?')) {
                $('#deleted-images-wrapper').append(`<input type="hidden" name="deleted_image_ids[]" value="${imgId}">`);
                item.fadeOut(150, function() { $(this).remove(); });
            }
        });

        // ==========================================
        // JQUERY LOGIC (TYPES, SPECS, ATTRS, VARS)
        // ==========================================
        function toggleProductType() {
            let type = $('#product_type').val();
            if (type === 'variable') {
                $('#simple-product-card').addClass('hidden');
                $('#variable-product-card').removeClass('hidden');
            } else {
                $('#simple-product-card').removeClass('hidden');
                $('#variable-product-card').addClass('hidden');
            }
        }
        $('#product_type').on('change', toggleProductType);
        toggleProductType(); // Init

        function addSpecRow(targetDiv, keyName, valName, keyVal = '', valVal = '') {
            let newRow = `
                <div class="flex items-center gap-2 spec-row mt-2 group">
                    <span class="material-symbols-outlined text-slate-300 cursor-grab text-base flex-shrink-0">drag_indicator</span>
                    <input type="text" name="${keyName}" value="${keyVal}"
                           placeholder="Tên thông số" class="w-2/5 text-xs border-slate-200 rounded-md focus:ring-primary py-1.5 px-2.5">
                    <span class="text-slate-300 text-xs flex-shrink-0">:</span>
                    <input type="text" name="${valName}" value="${valVal}"
                           placeholder="Giá trị" class="flex-1 text-xs border-slate-200 rounded-md focus:ring-primary py-1.5 px-2.5">
                    <button type="button" class="btn-remove-spec flex-shrink-0 text-slate-300 hover:text-red-500 p-1 rounded transition-colors opacity-0 group-hover:opacity-100">
                        <span class="material-symbols-outlined text-base">close</span>
                    </button>
                </div>`;
            $(targetDiv).append(newRow);
        }

        $(document).on('click', '.btn-add-spec', function() {
            let targetDiv = $(this).data('target');
            let namePrefix = $(this).data('name');
            let keyName = namePrefix === 'spec' ? 'spec_keys[]' : namePrefix.replace('[spec]', '[spec_keys][]');
            let valName = namePrefix === 'spec' ? 'spec_values[]' : namePrefix.replace('[spec]', '[spec_values][]');
            addSpecRow(targetDiv, keyName, valName);
        });

        $(document).on('click', '.btn-remove-spec', function() {
            $(this).closest('.spec-row').remove();
        });

        // Attributes logic
        function renderAttr(id, name, values, selectedIds = []) {
            if ($(`#attr-block-${id}`).length > 0) return;
            let options = values.map(v => `<option value="${v.id}">${v.value || v.name}</option>`).join('');
            let html = `
                <div class="border border-slate-200 rounded-lg p-4 bg-white shadow-sm" id="attr-block-${id}">
                    <div class="flex justify-between mb-3 border-b border-slate-50 pb-2">
                        <strong class="text-sm font-black text-slate-700 uppercase tracking-tight">${name}</strong>
                        <button type="button" class="text-red-400 hover:text-red-600 text-xs font-bold btn-remove-attr" data-id="${id}">Xóa</button>
                    </div>
                    <input type="hidden" name="attributes[${id}][id]" value="${id}">
                    <select multiple name="attributes[${id}][values][]" class="w-full select2-edit">${options}</select>
                    <div class="flex gap-2 mt-3">
                        <button type="button" class="btn-select-all bg-slate-50 border border-slate-200 px-3 py-1 text-[10px] rounded hover:bg-slate-100 font-bold text-slate-600 uppercase">Chọn tất cả</button>
                        <button type="button" class="btn-deselect-all bg-slate-50 border border-slate-200 px-3 py-1 text-[10px] rounded hover:bg-slate-100 font-bold text-slate-600 uppercase">Bỏ chọn</button>
                    </div>
                </div>`;
            $('#attributes-wrapper').append(html);
            let s2 = $(`#attr-block-${id} .select2-edit`).select2({ width: '100%', placeholder: 'Chọn giá trị...' });
            if (selectedIds.length) setTimeout(() => s2.val(selectedIds.map(String)).trigger('change'), 100);

            // ĐỒNG BỘ: Cập nhật thẻ select cho thuộc tính mới vào TẤT CẢ các biến thể hiện tại
            $('.attribute-selects-container').each(function() {
                let container = $(this);
                let item = container.closest('.variation-item');
                let nameMatch = item.find('input[name^="variations["]').first().attr('name') || container.find('input.old-hidden-attr').first().attr('name') || container.find('select.select2-manual-var').first().attr('name');
                if(!nameMatch) return;
                let m = nameMatch.match(/variations\[([\w\d_]+)\]/);
                if(!m) return;
                let index = m[1];

                if(container.find(`[data-attr-id="${id}"]`).length === 0) {
                    let oldHidden = container.find(`input[name="variations[${index}][attributes][${id}]"].old-hidden-attr`);
                    let preSelectedVal = oldHidden.length ? oldHidden.val() : '';
                    let optsForManual = values.map(val => `<option value="${val.id}" ${preSelectedVal == val.id ? 'selected' : ''}>${val.value || val.name}</option>`).join('');

                    let selectHtml = `
                        <div class="flex flex-col gap-1 w-full md:w-48" data-attr-id="${id}">
                            <label class="text-[11px] uppercase tracking-wider font-bold text-slate-500">${name}</label>
                            <select name="variations[${index}][attributes][${id}]" class="select2-manual-var w-full text-sm border-slate-200 rounded py-1 px-2 focus:ring-primary bg-slate-50">
                                <option value="">-- Chọn --</option>
                                ${optsForManual}
                            </select>
                        </div>
                    `;
                    container.append(selectHtml);
                    container.find('select').last().select2({ width: '100%' });
                    if(oldHidden.length) oldHidden.remove();
                }
            });
        }

        $(document).on('click', '.btn-remove-attr', function() { 
            let attrId = $(this).data('id');
            $(`#attr-block-${attrId}`).remove(); 
            $(`.attribute-selects-container [data-attr-id="${attrId}"]`).remove();
        });

        $(document).on('change', '.select2-manual-var', function() {
            let item = $(this).closest('.variation-item');
            let selectedTexts = [];
            item.find('.select2-manual-var option:selected').each(function() {
                if ($(this).val()) selectedTexts.push($(this).text());
            });
            let strong = item.find('strong.text-slate-800');
            if (strong.text().includes('—')) {
                let indexText = strong.text().split('—')[0].trim();
                strong.text(`${indexText} — ${selectedTexts.length ? selectedTexts.join(' - ') : 'Chưa chọn đủ'}`);
            }
        });

        $('#btn-add-attribute').click(function() {
            let id = $('#attribute-selector').val();
            let name = $('#attribute-selector option:selected').data('name');
            if (!id) return;
            $.get(`${baseUrl}/attributes/${id}/get-values`, function(res) {
                if (res.success) renderAttr(id, name, res.data);
            });
        });

        $(document).on('click', '.btn-select-all', function() {
            $(this).closest('div').siblings('select').find('option').prop('selected', true).trigger('change');
        });
        $(document).on('click', '.btn-deselect-all', function() {
            $(this).closest('div').siblings('select').find('option').prop('selected', false).trigger('change');
        });

        // --- VARIATION BUILDER & BULK APPLY ---
        function buildEditVarBody(idx) {
            let activeChecked = 'checked';
            return `
            <div class="var-body p-5 space-y-4 bg-white" style="display:none;">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Ảnh biến thể</label>
                    <div class="relative group w-24 h-24 rounded-xl border-2 border-dashed border-slate-200 bg-slate-50 hover:border-primary transition-colors cursor-pointer flex flex-col items-center justify-center overflow-hidden variation-thumb-container">
                        <input type="file" name="variations[${idx}][thumbnail]" accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10 var-thumb-input">
                        <span class="material-symbols-outlined text-3xl text-slate-300 group-hover:text-primary transition-colors">add_photo_alternate</span>
                        <span class="text-[10px] text-slate-400 mt-1">Chọn ảnh</span>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">Mã SP (SKU)</label>
                        <input type="text" name="variations[${idx}][sku]" placeholder="VD: SKU-NEW" class="w-full text-sm border-slate-200 rounded-lg focus:ring-primary py-2 px-3 bg-slate-50">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">Tồn kho</label>
                        <input type="number" name="variations[${idx}][stock]" value="0" class="w-full text-sm border-slate-200 rounded-lg focus:ring-primary py-2 px-3 bg-slate-50">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">Giá thường (₫) <span class="text-red-500">*</span></label>
                        <input type="number" name="variations[${idx}][price]" placeholder="VD: 25000000" class="w-full text-sm border-slate-200 rounded-lg focus:ring-primary py-2 px-3 bg-slate-50">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">Giá KM (₫)</label>
                        <input type="number" name="variations[${idx}][sale_price]" placeholder="Tùy chọn" class="w-full text-sm border-slate-200 rounded-lg focus:ring-primary py-2 px-3 bg-slate-50">
                    </div>
                </div>
                <div class="pt-2">
                    <label class="flex items-center gap-2 cursor-pointer w-max">
                        <input type="hidden" name="variations[${idx}][status]" value="inactive">
                        <input type="checkbox" name="variations[${idx}][status]" value="active" ${activeChecked} class="rounded text-primary border-slate-300 focus:ring-primary">
                        <span class="text-xs font-bold text-slate-700">Hiển thị (Kích hoạt)</span>
                    </label>
                </div>
                <div class="border-t border-slate-100 pt-4">
                    <div class="flex justify-between items-center mb-2">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">
                            <span class="material-symbols-outlined text-sm align-middle">memory</span> Thông số kỹ thuật
                        </label>
                        <button type="button" class="btn-add-spec text-[11px] font-semibold bg-slate-100 text-slate-600 hover:bg-primary hover:text-slate-900 px-3 py-1.5 rounded-lg border border-slate-200"
                                data-target="#new-var-specs-${idx}" data-name="variations[${idx}][spec]">
                            + Thêm
                        </button>
                    </div>
                    <div id="new-var-specs-${idx}" class="space-y-2"></div>
                </div>
            </div>`;
        }

        $('#btn-generate-variations').click(function() {
            let attrGroups = [];
            let missingValue = false;

            $('#attributes-wrapper [id^="attr-block-"]').each(function() {
                let block = $(this);
                let attrId = block.find('input[type="hidden"]').val();
                let attrName = block.find('strong').first().text().trim();
                let vals = block.find('select').select2('data');
                if (vals.length) {
                    attrGroups.push(vals.map(v => ({ attrId, valId: v.id, valName: v.text, attrName })));
                } else {
                    missingValue = true;
                }
            });

            if (missingValue) return alert('Vui lòng chọn ít nhất 1 giá trị cho TẤT CẢ các thuộc tính đã thêm để đảm bảo biến thể được tạo đồng bộ!');
            if (!attrGroups.length) return alert('Hãy chọn thuộc tính và giá trị trước!');

            let combos = attrGroups.reduce((a, b) => a.flatMap(d => b.map(e => [d, e].flat())));
            let wrapper = $('#variations-wrapper');

            $('#bulk-update-variations').fadeIn();

            let existingSigs = [];
            $('.variation-item').each(function() {
                let parts = [];
                $(this).find('input[type="hidden"][name*="[attributes]"]').each(function() {
                    let m = $(this).attr('name').match(/\[attributes\]\[(\d+)\]/);
                    if (m) parts.push(m[1] + '-' + $(this).val());
                });
                $(this).find('select[name*="[attributes]"]').each(function() {
                    let match = $(this).attr('name').match(/\[attributes\]\[(\d+)\]/);
                    if (match && $(this).val()) parts.push(match[1] + '-' + $(this).val());
                });
                if(parts.length) existingSigs.push(parts.sort().join('|'));
            });

            let allAttrs = [];
            $('#attributes-wrapper [id^="attr-block-"]').each(function() {
                let block = $(this);
                let attrId = block.find('input[name*="[id]"]').val(); 
                let attrName = block.find('strong.text-slate-800').first().text();
                let options = [];
                block.find('select.select2-edit option').each(function() {
                    options.push({ id: $(this).attr('value'), text: $(this).text() });
                });
                if (options.length > 0) allAttrs.push({ id: attrId, name: attrName, options: options });
            });

            let addedCount = 0;
            combos.forEach(combo => {
                combo = [combo].flat();
                let sig = combo.map(c => c.attrId + '-' + c.valId).sort().join('|');
                if (existingSigs.includes(sig)) return;

                let title = combo.map(c => c.valName).join(' - ');
                let idx = 'new_' + Date.now() + '_' + Math.floor(Math.random() * 9999);
                
                let selectsHtml = allAttrs.map(attr => {
                    let opts = attr.options.map(o => {
                        let matched = combo.find(c => c.attrId == attr.id && c.valId == o.id);
                        return `<option value="${o.id}" ${matched ? 'selected' : ''}>${o.text}</option>`;
                    }).join('');
                    return `
                        <div class="flex flex-col gap-1 w-full md:w-48" data-attr-id="${attr.id}">
                            <label class="text-[11px] uppercase tracking-wider font-bold text-slate-500">${attr.name}</label>
                            <select name="variations[${idx}][attributes][${attr.id}]" class="select2-manual-var w-full text-sm border-slate-200 rounded py-1 px-2 focus:ring-primary bg-slate-50">
                                <option value="">-- Chọn --</option>
                                ${opts}
                            </select>
                        </div>
                    `;
                }).join('');

                let html = `
                    <div class="border border-green-300 rounded-lg overflow-hidden bg-white variation-item shadow-sm">
                        <div class="bg-green-50 p-3 flex justify-between items-center border-b border-green-200 cursor-pointer" onclick="$(this).siblings('.var-wrapper').slideToggle()">
                            <div class="flex items-center gap-3">
                                <span class="material-symbols-outlined text-green-400">drag_indicator</span>
                                <strong class="text-sm text-green-700">#MỚI — ${title}</strong>
                            </div>
                            <button type="button" class="text-red-400 hover:text-red-600 text-xs font-bold"
                                    onclick="if(confirm('Xóa biến thể này?')) $(this).closest('.variation-item').remove()">Xóa</button>
                        </div>
                        <div class="var-wrapper">
                            <div class="p-4 bg-slate-50/50 border-b border-slate-100 flex flex-wrap gap-3">
                                <div class="attribute-selects-container flex flex-wrap gap-3 w-full">
                                    ${selectsHtml}
                                </div>
                            </div>
                            ${buildEditVarBody(idx).replace('style="display:none;"', '')}
                        </div>
                    </div>`;
                wrapper.append(html);
                wrapper.find('.variation-item:last-child .select2-manual-var').select2({ width: '100%' });
                addedCount++;
            });

            if (addedCount === 0) {
                alert('Tất cả tổ hợp này đã tồn tại rồi!');
            } else if (addedCount > 0) {
                alert(`Đã thêm ${addedCount} biến thể mới.`);
            }
        });

        $('#btn-add-manual-variation').click(function() {
            let attrs = [];
            let missingValue = false;
            $('#attributes-wrapper [id^="attr-block-"]').each(function() {
                let block = $(this);
                let attrId = block.find('input[type="hidden"]').val();
                let attrName = block.find('strong').first().text().trim();
                let selectedOptions = block.find('select').select2('data');
                
                if (selectedOptions.length) {
                    attrs.push({
                        id: attrId, 
                        name: attrName, 
                        options: selectedOptions.map(o => ({ id: o.id, text: o.text }))
                    });
                } else {
                    missingValue = true;
                }
            });

            if (missingValue) return alert("Vui lòng chọn ít nhất 1 giá trị cho TẤT CẢ các thuộc tính đã thêm để biến thể thủ công có đủ cặp dữ liệu!");
            if(attrs.length === 0) return alert("Bạn hãy thêm và chọn giá trị cho các thuộc tính dùng cho biến thể trước nhé!");

            let idx = 'manual_' + Date.now() + '_' + Math.floor(Math.random() * 9999);
            $('#bulk-update-variations').fadeIn();
            
            let selectsHtml = attrs.map(attr => {
                let opts = attr.options.map(o => `<option value="${o.id}">${o.text}</option>`).join('');
                return `
                    <div class="flex flex-col gap-1 w-full md:w-48" data-attr-id="${attr.id}">
                        <label class="text-[11px] uppercase tracking-wider font-bold text-slate-500">${attr.name}</label>
                        <select name="variations[${idx}][attributes][${attr.id}]" class="select2-manual-var w-full text-sm border-slate-200 rounded py-1 px-2 focus:ring-primary bg-slate-50">
                            <option value="">-- Chọn --</option>
                            ${opts}
                        </select>
                    </div>
                `;
            }).join('');

            let html = `
                <div class="border border-green-300 rounded-lg overflow-hidden bg-white variation-item shadow-sm">
                    <div class="bg-green-50 p-3 flex justify-between items-center border-b border-green-200 cursor-pointer" onclick="$(this).siblings('.var-wrapper').slideToggle()">
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined text-green-400">drag_indicator</span>
                            <strong class="text-sm text-green-700">#MỚI — Chưa chọn đủ</strong>
                        </div>
                        <button type="button" class="text-red-400 hover:text-red-600 text-xs font-bold" onclick="$(this).closest('.variation-item').remove(); event.stopPropagation();">Xóa</button>
                    </div>
                    <div class="var-wrapper">
                        <div class="p-4 bg-slate-50/50 border-b border-slate-100 flex flex-wrap gap-3">
                            <div class="attribute-selects-container flex flex-wrap gap-3 w-full">
                                ${selectsHtml}
                            </div>
                        </div>
                        ${buildEditVarBody(idx).replace('style="display:none;"', '')}
                    </div>
                </div>`;
            $('#variations-wrapper').append(html);
            $('#variations-wrapper .variation-item:last-child .select2-manual-var').select2({ width: '100%' });
        });

        $('#btn-apply-bulk').click(function() {
            let n = $('#variations-wrapper .variation-item').length;
            if (!n) return alert('Chưa có biến thể nào!');
            let p = $('#bulk-price').val(), sp = $('#bulk-sale-price').val(), st = $('#bulk-stock').val();
            if (!p && !sp && !st) return alert('Nhập ít nhất 1 giá trị!');
            if (confirm(`Áp dụng cho toàn bộ ${n} biến thể?`)) {
                $('#variations-wrapper .variation-item').each(function() {
                    if (p)  $(this).find('input[name$="[price]"]').val(p);
                    if (sp) $(this).find('input[name$="[sale_price]"]').val(sp);
                    if (st) $(this).find('input[name$="[stock]"]').val(st);
                });
                $('#bulk-price, #bulk-sale-price, #bulk-stock').val('');
                alert('Đã cập nhật hàng loạt! 🎉');
            }
        });

        // Initialize Existing Attributes
        @php
            $groupedAttrs = [];
            if (isset($product) && $product->variants) {
                foreach($product->variants as $variant) {
                    if ($variant->attributeValues) {
                        foreach($variant->attributeValues as $attrVal) {
                            $attrId = $attrVal->attribute_id;
                            if (!isset($groupedAttrs[$attrId])) {
                                $groupedAttrs[$attrId] = ['id' => $attrId, 'name' => $attrVal->attribute->name ?? 'Thuộc tính', 'values' => []];
                            }
                            $existingIds = array_column($groupedAttrs[$attrId]['values'], 'id');
                            if (!in_array($attrVal->id, $existingIds)) {
                                $groupedAttrs[$attrId]['values'][] = $attrVal;
                            }
                        }
                    }
                }
            }
        @endphp

        @if(count($groupedAttrs) > 0)
            @foreach($groupedAttrs as $attrId => $data)
                $.get(`${baseUrl}/attributes/{{ $attrId }}/get-values`, function(res) {
                    if (res.success) {
                        renderAttr('{{ $attrId }}', '{!! addslashes($data['name']) !!}', res.data, {!! json_encode(array_column($data['values'], 'id')) !!});
                    }
                });
            @endforeach
        @endif

        // Variation thumbnail preview
        $(document).on('change', '.var-thumb-input', function() {
            if (this.files && this.files[0]) {
                let reader = new FileReader();
                let container = $(this).closest('.variation-thumb-container');
                reader.onload = (e) => {
                    let img = container.find('img');
                    if (img.length) {
                        img.attr('src', e.target.result).removeClass('hidden');
                    } else {
                        container.prepend(`<img src="${e.target.result}" class="w-full h-full object-cover rounded-xl">`);
                    }
                    container.find('.material-symbols-outlined').addClass('hidden');
                    container.find('span.text-\\[10px\\]').addClass('hidden');
                };
                reader.readAsDataURL(this.files[0]);
            }
        });

        // ==========================================
        // VALIDATION TẠI CLIENT VÀ HIỂN THỊ LỖI (EDIT)
        // ==========================================
        $('form').on('submit', function(e) {
            let isValid = true;
            $('.client-error').remove(); 
            $('.server-error').remove(); 

            function showError(input, msg) {
                $(input).after(`<span class="text-xs text-red-500 mt-1.5 block font-medium client-error">${msg}</span>`);
                $(input).addClass('border-red-500').removeClass('border-slate-200');
                isValid = false;
            }

            function clearError(input) {
                $(input).removeClass('border-red-500').addClass('border-slate-200');
            }

            // Tên SP
            let name = $('input[name="name"]');
            clearError(name);
            if (!name.val().trim()) showError(name, 'Vui lòng nhập tên sản phẩm!');

            // Loại SP
            if ($('#product_type').val() === 'simple') {
                let p = $('input[name="price"]');
                clearError(p);
                if (!p.val()) showError(p, 'Vui lòng nhập giá!');
            } else {
                let variations = $('#variations-wrapper .variation-item');
                if (variations.length === 0) {
                    $('#variations-wrapper').append(`<div class="p-4 bg-red-50 text-red-600 font-bold rounded-lg border border-red-200 client-error mb-4">Vui lòng tạo ít nhất 1 biến thể!</div>`);
                    isValid = false;
                }

                let signatures = [];
                variations.each(function() {
                    let item = $(this);
                    
                    let p = item.find('input[name$="[price]"]');
                    clearError(p);
                    if (!p.val()) showError(p, 'Vui lòng nhập giá!');

                    // Kiểm tra chọn thiếu thuộc tính và trùng lặp
                    let sigParts = [];
                    let hasMissing = false;

                    item.find('.attribute-selects-container input.old-hidden-attr').each(function() {
                        let m = $(this).attr('name').match(/\[attributes\]\[(\d+)\]/);
                        if (m) {
                            if (!$(this).val()) hasMissing = true;
                            else sigParts.push(m[1] + '-' + $(this).val());
                        }
                    });
                    
                    item.find('.attribute-selects-container select[name*="[attributes]"]').each(function() {
                        let m = $(this).attr('name').match(/\[attributes\]\[(\d+)\]/);
                        if (m) {
                            if (!$(this).val()) {
                                hasMissing = true;
                                $(this).addClass('border-red-500').removeClass('border-slate-200');
                            } else {
                                $(this).removeClass('border-red-500').addClass('border-slate-200');
                                sigParts.push(m[1] + '-' + $(this).val());
                            }
                        }
                    });

                    if (hasMissing) {
                        let head = item.children().first();
                        item.find('.uncompleted-error').remove();
                        head.after(`<div class="p-3 bg-red-50 text-red-600 text-sm font-bold border-b border-red-200 client-error uncompleted-error"><span class="material-symbols-outlined text-[16px] align-middle">error</span> Biến thể này chưa chọn đủ thuộc tính! Vui lòng chọn giá trị hoặc xóa nó đi.</div>`);
                        item.addClass('border-red-500').removeClass('border-slate-200 border-green-300');
                        isValid = false;
                    } else if (sigParts.length > 0) {
                        let sig = sigParts.sort().join('|');
                        if (signatures.includes(sig)) {
                            let head = item.children().first();
                            head.after(`<div class="p-3 bg-red-50 text-red-600 text-sm font-bold border-b border-red-200 client-error"><span class="material-symbols-outlined text-[16px] align-middle">error</span> Biến thể này bị trùng lặp thuộc tính với biến thể khác! Vui lòng thay đổi hoặc xóa đi.</div>`);
                            item.addClass('border-red-500').removeClass('border-slate-200 border-green-300');
                            isValid = false;
                        } else {
                            signatures.push(sig);
                            item.removeClass('border-red-500').addClass('border-slate-200 border-green-300');
                        }
                    }
                });
            }

            if (!isValid) {
                e.preventDefault();
                $('html, body').animate({
                    scrollTop: $('.client-error').first().offset().top - 150
                }, 500);
            }
        });

        // Hiển thị lỗi từ backend trực tiếp dưới input
        let backendErrors = @json($errors->toArray());
        if (Object.keys(backendErrors).length > 0) {
            for (let key in backendErrors) {
                let nameAttr = key;
                if (key.includes('.')) {
                    let parts = key.split('.');
                    nameAttr = parts[0];
                    for (let i = 1; i < parts.length; i++) {
                        nameAttr += `[${parts[i]}]`;
                    }
                }
                
                let input = $(`[name="${nameAttr}"]`);
                if (input.length > 0) {
                    input.after(`<span class="text-xs text-red-500 mt-1.5 block font-medium server-error">${backendErrors[key][0]}</span>`);
                    input.addClass('border-red-500').removeClass('border-slate-200');
                }
            }
        }
    });
</script>
@endsection