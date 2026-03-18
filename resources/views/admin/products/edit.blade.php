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
                        
                        <div class="bg-white rounded-xl p-6 shadow-sm border border-slate-200">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-bold text-slate-700 mb-3">Tên sản phẩm <span class="text-red-500">*</span></label>
                                    <input name="name" value="{{ old('name', $product->name) }}" required class="w-full text-xl font-bold border-slate-200 rounded-lg focus:border-primary focus:ring-primary py-3 px-4 bg-slate-50/50" type="text"/>
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-3">Mã SP (SKU gốc)</label>
                                    <input name="sku" value="{{ old('sku', $product->sku) }}" class="w-full text-xl font-bold border-slate-200 rounded-lg focus:border-primary focus:ring-primary py-3 px-4 bg-slate-50/50" type="text" placeholder="VD: IP-15-PRM"/>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white rounded-xl p-6 shadow-sm border border-slate-200">
                            <label class="block text-sm font-bold text-slate-700 mb-4">Mô tả sản phẩm</label>
                            <div class="border border-slate-200 rounded-lg overflow-hidden">
                                <textarea id="description-editor" name="description" class="w-full border-none focus:ring-0 min-h-[250px] p-4 text-slate-800 leading-relaxed text-sm bg-white" placeholder="Bắt đầu viết mô tả chi tiết tại đây...">{{ old('description', $product->description ?? '') }}</textarea>
                            </div>
                        </div>

                        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden mt-6 mb-6">
                            <div class="p-4 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center">
                                <h4 class="font-black text-sm uppercase tracking-wider text-slate-700">
                                    <span class="material-symbols-outlined text-base align-middle mr-1">memory</span> Thông số kỹ thuật
                                </h4>
                                <button type="button" id="btn-add-spec" class="text-xs font-bold bg-slate-200 text-slate-700 hover:bg-slate-300 px-3 py-1.5 rounded transition-colors shadow-sm">
                                    + Thêm dòng mới
                                </button>
                            </div>
                            <div class="p-5">
                                <div class="bg-blue-50 text-blue-700 p-3 rounded-lg text-xs mb-5 border border-blue-100 font-medium">
                                    Mỗi thông số chỉ được chọn 1 lần. Nhập liệu giá trị vào ô trống.
                                </div>
                                
                                <div id="specs-wrapper" class="space-y-3">
                                  @php
                                        $existingSpecs = old('specifications', isset($product) && $product->specifications ? $product->specifications : []);
                                        $fixedKeys = ['Màn hình', 'Hệ điều hành', 'Camera trước', 'Camera sau', 'Chip xử lý (CPU)', 'Dung lượng pin', 'Sạc nhanh', 'Cổng kết nối', 'Kích thước & Trọng lượng'];
                                    @endphp

                                    @if(!empty($existingSpecs))
                                        @foreach($existingSpecs as $key => $value)
                                            <div class="flex items-center gap-3 spec-row">
                                                <span class="material-symbols-outlined text-slate-300 cursor-move">drag_indicator</span>
                                                <select name="spec_keys[]" class="w-1/3 text-sm border-slate-200 rounded focus:ring-primary py-2 px-3 bg-slate-50 spec-key-select" required>
                                                    <option value="" disabled>-- Chọn thông số --</option>
                                                    @foreach($fixedKeys as $fk)
                                                        <option value="{{ $fk }}" {{ $key == $fk ? 'selected' : '' }}>{{ $fk }}</option>
                                                    @endforeach
                                                </select>
                                                
                                                <input type="text" name="spec_values[]" value="{{ $value }}" placeholder="Nhập giá trị (VD: 8GB)..." class="flex-1 text-sm border-slate-200 rounded focus:ring-primary py-2 px-3 bg-slate-50" required>
                                                
                                                <button type="button" class="btn-remove-spec text-red-500 hover:text-red-700 p-2 transition-colors" title="Xóa dòng này">
                                                    <span class="material-symbols-outlined text-lg">delete</span>
                                                </button>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden" id="product-data-container">
                            <div class="bg-slate-50 p-4 border-b border-slate-200 flex items-center justify-between">
                                <div class="flex items-center gap-4">
                                    <span class="font-bold text-sm text-slate-700 uppercase tracking-wide">Dữ liệu sản phẩm —</span>
                                    <select name="type" id="product_type" class="text-sm border-slate-200 rounded-lg py-1 px-3 bg-white focus:ring-primary font-bold text-primary shadow-sm cursor-pointer">
                                        <option value="simple" {{ old('type', $product->type) == 'simple' ? 'selected' : '' }}>Sản phẩm đơn giản</option>
                                        <option value="variable" {{ old('type', $product->type) == 'variable' ? 'selected' : '' }}>Sản phẩm biến thể</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="flex flex-col md:flex-row min-h-[450px]">
                                <div class="w-full md:w-52 border-r border-slate-200 bg-slate-50/30">
                                    <nav class="flex flex-col" id="product-data-tabs">
                                        <button type="button" data-target="tab-general" class="tab-btn active-tab flex items-center gap-3 px-5 py-4 text-sm font-semibold border-b border-slate-100">
                                            <span class="material-symbols-outlined text-lg">settings</span> Chung
                                        </button>
                                        <button type="button" data-target="tab-inventory" class="tab-btn flex items-center gap-3 px-5 py-4 text-sm font-semibold text-slate-600 hover:bg-slate-100 border-b border-slate-100">
                                            <span class="material-symbols-outlined text-lg">inventory_2</span> Kho hàng
                                        </button>
                                        <button type="button" data-target="tab-attributes" class="tab-btn flex items-center gap-3 px-5 py-4 text-sm font-semibold text-slate-600 hover:bg-slate-100 border-b border-slate-100">
                                            <span class="material-symbols-outlined text-lg">list_alt</span> Thuộc tính
                                        </button>
                                        <button type="button" data-target="tab-variations" id="btn-tab-variations" class="tab-btn flex items-center gap-3 px-5 py-4 text-sm font-semibold text-slate-600 hover:bg-slate-100 border-b border-slate-100 {{ $product->type == 'simple' ? 'hidden' : '' }}">
                                            <span class="material-symbols-outlined text-lg">layers</span> Các biến thể
                                        </button>
                                    </nav>
                                </div>
                                
                                <div class="flex-1 p-6 bg-white overflow-y-auto max-h-[600px]">
                                    
                                    <div id="tab-general" class="tab-content space-y-5">
                                        <div class="flex items-center gap-4">
                                            <label class="w-1/3 text-sm font-bold text-slate-700 text-right">Giá bán thường (₫)</label>
                                            <input type="number" name="price" value="{{ old('price', (int)$product->price) }}" class="flex-1 text-sm border-slate-200 rounded-lg py-2.5 px-3 bg-slate-50"/>
                                        </div>
                                        <div class="flex items-center gap-4">
                                            <label class="w-1/3 text-sm font-bold text-slate-700 text-right">Giá khuyến mãi (₫)</label>
                                            <input type="number" name="sale_price" value="{{ old('sale_price', $product->sale_price ? (int)$product->sale_price : '') }}" class="flex-1 text-sm border-slate-200 rounded-lg py-2.5 px-3 bg-slate-50"/>
                                        </div>
                                    </div>

                                    <div id="tab-inventory" class="tab-content hidden space-y-5">
                                        <div class="flex items-center gap-4">
                                            <label class="w-1/3 text-sm font-bold text-slate-700 text-right">Tồn kho chung</label>
                                            <input type="number" name="stock" value="{{ old('stock', $product->stock) }}" class="w-32 text-sm border-slate-200 rounded-lg py-2.5 px-3 bg-slate-50"/>
                                        </div>
                                    </div>

                                    <div id="tab-attributes" class="tab-content hidden">
                                        <div class="flex items-center gap-3 bg-slate-50 p-3 rounded-lg border border-slate-200 mb-6">
                                            <select id="attribute-selector" class="flex-1 text-sm border-slate-200 rounded py-2 px-3 focus:ring-primary">
                                                <option value="">-- Thêm thuộc tính mới --</option>
                                                @foreach($attributes as $attr)
                                                    <option value="{{ $attr->id }}" data-name="{{ $attr->name }}">{{ $attr->name }}</option>
                                                @endforeach
                                            </select>
                                            <button type="button" id="btn-add-attribute" class="bg-slate-200 hover:bg-slate-300 text-slate-700 px-4 py-2 rounded text-sm font-bold transition-colors">Thêm</button>
                                        </div>
                                        <div id="attributes-wrapper" class="space-y-4">
                                            </div>
                                    </div>

                                    <div id="tab-variations" class="tab-content hidden">
                                        <div class="bg-blue-50 text-blue-700 p-3 rounded-lg text-xs mb-5 border border-blue-100 font-medium">
                                            Lưu ý: Nếu bro thay đổi thuộc tính, hãy bấm nút "Đi" để cập nhật danh sách mới. Các dữ liệu biến thể chưa lưu sẽ bị ảnh hưởng.
                                        </div>
                                        <div class="flex items-center gap-3 bg-slate-50 p-3 rounded-lg border border-slate-200 mb-6">
                                            <select id="variation-action" class="flex-1 text-sm border-slate-200 rounded py-2 px-3 font-bold cursor-pointer">
                                                <option value="add_missing" selected>Thêm biến thể mới (Giữ nguyên cái cũ)</option>
                                                <option value="generate">Xóa sạch & Tạo lại tất cả (Nguy hiểm)</option>
                                            </select>
                                            <button type="button" id="btn-do-variation" class="bg-primary text-slate-900 px-5 py-2 rounded text-sm font-bold shadow-sm">Đi</button>
                                        </div>

                                        <div id="bulk-update-variations" class="bg-emerald-50 border border-emerald-200 p-4 rounded-lg mb-6 shadow-sm" style="display: {{ $product->variants && $product->variants->count() > 0 ? 'block' : 'none' }};">
                                            <h5 class="font-bold text-sm text-emerald-800 mb-3 flex items-center gap-2">
                                                <span class="material-symbols-outlined text-lg">bolt</span> Cập nhật nhanh cho TẤT CẢ biến thể
                                            </h5>
                                            <div class="flex flex-col md:flex-row items-center gap-3">
                                                <input type="number" id="bulk-price" class="flex-1 w-full text-sm border-emerald-200 rounded py-2 px-3 focus:ring-emerald-500 focus:border-emerald-500" placeholder="Giá thường chung (₫)...">
                                                <input type="number" id="bulk-sale-price" class="flex-1 w-full text-sm border-emerald-200 rounded py-2 px-3 focus:ring-emerald-500 focus:border-emerald-500" placeholder="Giá KM chung (₫)...">
                                                <input type="number" id="bulk-stock" class="w-full md:w-32 text-sm border-emerald-200 rounded py-2 px-3 focus:ring-emerald-500 focus:border-emerald-500" placeholder="Tồn kho...">
                                                <button type="button" id="btn-apply-bulk" class="bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2 rounded text-sm font-bold transition-all shadow-sm whitespace-nowrap">
                                                    Áp dụng tất cả
                                                </button>
                                            </div>
                                        </div>
                                        
                                        <div id="variations-wrapper" class="space-y-3">
                                            @if($product->variants)
                                                @foreach($product->variants as $index => $variant)
                                                    <div class="border border-slate-200 rounded-lg overflow-hidden bg-white variation-item shadow-sm mt-3">
                                                        <div class="bg-slate-50 p-3 flex justify-between items-center border-b border-slate-200 cursor-pointer" onclick="$(this).next().slideToggle()">
                                                            <div class="flex items-center gap-3">
                                                                <span class="material-symbols-outlined text-slate-400">drag_indicator</span>
                                                                <strong class="text-xs text-primary uppercase">
                                                                    #{{ $index + 1 }} — 
                                                                    @php
                                                                        $sortedVals = $variant->attributeValues->sortBy(function($val) {
                                                                            $name = mb_strtolower($val->attribute->name ?? '');
                                                                            if (str_contains($name, 'màu')) return 1;
                                                                            if (str_contains($name, 'ram')) return 2;
                                                                            if (str_contains($name, 'dung lượng') || str_contains($name, 'rom')) return 3;
                                                                            return 99;
                                                                        });
                                                                    @endphp
                                                                    
                                                                    @foreach($sortedVals as $val)
                                                                        {{ $val->value }}{{ !$loop->last ? ' - ' : '' }}
                                                                    @endforeach
                                                                </strong>
                                                                @foreach($variant->attributeValues as $val)
                                                                    <input type="hidden" name="variations[{{ $index }}][attributes][{{ $val->attribute_id }}]" value="{{ $val->id }}">
                                                                @endforeach
                                                                <input type="hidden" name="variations[{{ $index }}][id]" value="{{ $variant->id }}">
                                                            </div>
                                                            <div class="flex items-center gap-4">
                                                                <button type="button" class="text-red-500 text-[10px] font-bold uppercase" onclick="$(this).closest('.variation-item').remove()">Xóa</button>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="p-4 grid grid-cols-2 md:grid-cols-5 gap-4 bg-white" style="display:none;">
                                                            <div>
                                                                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">SKU</label>
                                                                <input type="text" name="variations[{{ $index }}][sku]" value="{{ $variant->sku }}" class="w-full text-sm border-slate-200 rounded py-1.5 px-2">
                                                            </div>
                                                            <div>
                                                                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Giá bán (₫)</label>
                                                                <input type="number" name="variations[{{ $index }}][price]" value="{{ (int)$variant->price }}" class="w-full text-sm border-slate-200 rounded py-1.5 px-2">
                                                            </div>
                                                            <div>
                                                                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Giá KM</label>
                                                                <input type="number" name="variations[{{ $index }}][sale_price]" value="{{ $variant->sale_price ? (int)$variant->sale_price : '' }}" class="w-full text-sm border-slate-200 rounded py-1.5 px-2">
                                                            </div>
                                                            <div>
                                                                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Kho</label>
                                                                <input type="number" name="variations[{{ $index }}][stock]" value="{{ $variant->stock }}" class="w-full text-sm border-slate-200 rounded py-1.5 px-2">
                                                            </div>
                                                            <div>
                                                                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Ảnh biến thể</label>
                                                                @if($variant->thumbnail)
                                                                    <img src="{{ asset('storage/' . $variant->thumbnail) }}" class="h-10 w-10 object-cover rounded mb-2 border border-slate-200 shadow-sm">
                                                                @endif
                                                                <input type="file" name="variations[{{ $index }}][thumbnail]" accept="image/*" class="w-full text-[11px] border-slate-200 rounded py-1 bg-slate-50 cursor-pointer file:border-0 file:bg-slate-200 file:text-slate-700 file:text-[10px] file:font-bold file:px-2 file:py-1 file:rounded hover:file:bg-slate-300">
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @endif
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="lg:col-span-4 space-y-6">
                        
                        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                            <div class="p-4 border-b border-slate-100 bg-slate-50/50">
                                <h4 class="font-black text-sm uppercase text-slate-700">Trạng thái</h4>
                            </div>
                            <div class="p-5 space-y-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-500 mb-2 uppercase">Trạng thái hiển thị</label>
                                    <select name="status" class="w-full text-sm border-slate-200 rounded py-2 bg-slate-50">
                                        <option value="active" {{ old('status', $product->status) == 'active' ? 'selected' : '' }}>Hiển thị công khai</option>
                                        <option value="inactive" {{ old('status', $product->status) == 'inactive' ? 'selected' : '' }}>Lưu bản nháp</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                            <div class="p-4 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                                <h4 class="font-black text-sm uppercase text-slate-700">Danh mục <span class="text-red-500">*</span></h4>
                            </div>
                            <div class="p-5">
                                <div class="max-h-48 overflow-y-auto space-y-3 mb-2 custom-scrollbar">
                                    @foreach($categories as $category)
                                        <label class="flex items-center gap-2.5 text-sm cursor-pointer group">
                                            <input name="category_ids[]" value="{{ $category->id }}" type="checkbox" 
                                                class="rounded border-slate-300 text-primary focus:ring-primary"
                                                {{ in_array($category->id, $product->categories->pluck('id')->toArray()) ? 'checked' : '' }}>
                                            <span class="group-hover:text-primary transition-colors font-medium text-slate-600">{{ $category->name }}</span>
                                        </label>
                                    @endforeach
                                </div>
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
                                <select name="brand_id" class="w-full text-sm border-slate-200 rounded py-2 bg-slate-50">
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
                                @if($product->thumbnail)
                                    <div class="mb-4 rounded-xl overflow-hidden border border-slate-200 shadow-inner group relative">
                                        <img src="{{ asset('storage/' . $product->thumbnail) }}" id="preview-thumbnail" class="w-full aspect-square object-cover">
                                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white text-xs font-bold">Thay đổi ảnh</div>
                                    </div>
                                @endif
                                <input type="file" name="thumbnail" id="input-thumbnail" accept="image/*" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-primary file:text-slate-900 cursor-pointer">
                            </div>
                        </div>

                        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                            <div class="p-4 border-b border-slate-100 bg-slate-50/50">
                                <h4 class="font-black text-sm uppercase text-slate-700">Album hình ảnh</h4>
                            </div>
                            <div class="p-5">
                                @if($product->images && $product->images->count() > 0)
                                    <div class="grid grid-cols-3 gap-2 mb-4">
                                        @foreach($product->images as $img)
                                            <div class="rounded-lg overflow-hidden border border-slate-200 relative group aspect-square">
                                                <img src="{{ asset('storage/' . $img->path) }}" class="w-full h-full object-cover">
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                                <div class="relative group aspect-[2/1] rounded-lg border-2 border-dashed border-slate-200 bg-slate-50 hover:border-primary transition-colors cursor-pointer flex flex-col items-center justify-center">
                                    <input type="file" name="images[]" multiple accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                                    <span class="material-symbols-outlined text-2xl text-slate-300 group-hover:text-primary transition-colors">collections</span>
                                    <span class="text-[10px] font-black uppercase mt-1 text-slate-400">Thêm nhiều ảnh</span>
                                </div>
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
    .active-tab { border-left: 3px solid #f4c025; background-color: rgba(244, 192, 37, 0.08); color: #000 !important; }
    .tox-notifications-container { display: none !important; }
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.2/tinymce.min.js" referrerpolicy="origin"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const tabBtns = document.querySelectorAll('.tab-btn');
        const tabContents = document.querySelectorAll('.tab-content');

        function switchTab(targetId) {
            tabContents.forEach(content => content.classList.add('hidden'));
            tabBtns.forEach(btn => btn.classList.remove('active-tab', 'text-black'));
            document.getElementById(targetId).classList.remove('hidden');
            document.querySelector(`[data-target="${targetId}"]`).classList.add('active-tab', 'text-black');
        }
        tabBtns.forEach(btn => btn.addEventListener('click', () => switchTab(btn.getAttribute('data-target'))));

        const productType = document.getElementById('product_type');
        const tabVarBtn = document.getElementById('btn-tab-variations');
        const tabGeneral = document.querySelector('[data-target="tab-general"]');
        const tabInventory = document.querySelector('[data-target="tab-inventory"]');

        function toggleProductTabs() {
            if(productType.value === 'variable') {
                tabVarBtn.classList.remove('hidden');
                $('.var-checkbox-wrapper').css('display', 'flex');
                tabGeneral.classList.add('hidden');
                tabInventory.classList.add('hidden');
                switchTab('tab-attributes'); 
            } else {
                tabVarBtn.classList.add('hidden');
                $('.var-checkbox-wrapper').hide();
                tabGeneral.classList.remove('hidden');
                tabInventory.classList.remove('hidden');
                switchTab('tab-general');
            }
        }
        productType.addEventListener('change', toggleProductTabs);
        toggleProductTabs();

        $('#input-thumbnail').change(function() {
            if (this.files && this.files[0]) {
                let reader = new FileReader();
                reader.onload = (e) => $('#preview-thumbnail').attr('src', e.target.result);
                reader.readAsDataURL(this.files[0]);
            }
        });
        
        // FIX: TẮT HIỆN THÔNG BÁO UPGRADE CỦA TINYMCE
        tinymce.init({
            selector: '#description-editor',
            height: 500,
            plugins: [ 'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview', 'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen', 'insertdatetime', 'media', 'table', 'wordcount' ],
            toolbar: 'undo redo | blocks | bold italic textcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media | table | removeformat | code',
            content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; font-size: 15px; color: #334155; }',
            paste_data_images: true, 
            branding: false, 
            promotion: false // Dòng này tắt nút Upgrade
        });
    });

    $(document).ready(function() {
        const baseUrl = '/admin';

        const fixedSpecKeys = ['Màn hình', 'Hệ điều hành', 'Camera trước', 'Camera sau', 'Chip xử lý (CPU)', 'Dung lượng pin', 'Sạc nhanh'];

        function getSpecOptionsHtml(selectedValue = '') {
            let html = '';
            fixedSpecKeys.forEach(key => {
                html += `<option value="${key}" ${key === selectedValue ? 'selected' : ''}>${key}</option>`;
            });
            return html;
        }

        function updateAvailableSpecs() {
            let selectedSpecs = [];
            $('.spec-key-select').each(function() {
                if ($(this).val()) selectedSpecs.push($(this).val());
            });

            $('.spec-key-select').each(function() {
                let currentVal = $(this).val();
                $(this).find('option').each(function() {
                    if ($(this).val() !== currentVal && selectedSpecs.includes($(this).val())) {
                        $(this).attr('disabled', true).hide();
                    } else {
                        $(this).removeAttr('disabled').show();
                    }
                });
            });
        }

        $('#btn-add-spec').click(function() {
            let currentRows = $('.spec-row').length;
            if (currentRows >= fixedSpecKeys.length) return alert('Đã thêm đủ tất cả các thông số!');

            let newRow = `
                <div class="flex items-center gap-3 spec-row mt-3">
                    <span class="material-symbols-outlined text-slate-300 cursor-move">drag_indicator</span>
                    <select name="spec_keys[]" class="w-1/3 text-sm border-slate-200 rounded focus:ring-primary py-2 px-3 bg-slate-50 cursor-pointer spec-key-select" required>
                        <option value="" disabled selected>-- Chọn thông số --</option>
                        ${getSpecOptionsHtml()}
                    </select>
                    <input type="text" name="spec_values[]" placeholder="Nhập giá trị..." class="flex-1 text-sm border-slate-200 rounded focus:ring-primary py-2 px-3 bg-slate-50" required>
                    <button type="button" class="btn-remove-spec text-red-500 hover:text-red-700 p-2" title="Xóa dòng này">
                        <span class="material-symbols-outlined text-lg">delete</span>
                    </button>
                </div>
            `;
            $('#specs-wrapper').append(newRow);
            updateAvailableSpecs(); 
        });

        $(document).on('click', '.btn-remove-spec', function() {
            $(this).closest('.spec-row').remove();
            updateAvailableSpecs(); 
        });

        $(document).on('change', '.spec-key-select', function() { updateAvailableSpecs(); });
        updateAvailableSpecs();

        // ==========================================
        // LOGIC ATTRIBUTES & VARIATIONS (EDIT)
        // ==========================================
        function renderAttr(id, name, values) {
            let options = values.map(v => `<option value="${v.id}">${v.value || v.name}</option>`).join('');
            let isVar = $('#product_type').val() === 'variable' ? 'flex' : 'none';
            
            let html = `
                <div class="border border-slate-200 rounded-lg p-4 bg-white shadow-sm mb-4" id="attr-block-${id}">
                    <div class="flex justify-between mb-3">
                        <strong class="text-sm text-slate-700">${name}</strong>
                        <button type="button" class="text-red-500 text-[10px] font-bold uppercase" onclick="$(this).parent().parent().remove()">Xóa</button>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Giá trị</label>
                            <select multiple name="attributes[${id}][values][]" class="w-full select2-edit">${options}</select>
                            <input type="hidden" name="attributes[${id}][id]" value="${id}">
                            
                            <div class="flex gap-2 mt-2">
                                <button type="button" class="btn-select-all bg-slate-100 border border-slate-300 px-3 py-1 text-xs rounded hover:bg-slate-200 font-medium text-slate-700">Chọn tất cả</button>
                                <button type="button" class="btn-deselect-all bg-slate-100 border border-slate-300 px-3 py-1 text-xs rounded hover:bg-slate-200 font-medium text-slate-700">Không chọn</button>
                            </div>
                        </div>
                        <div class="var-checkbox-wrapper items-center gap-2 pt-5" style="display: ${isVar}">
                            <input type="checkbox" name="attributes[${id}][variation]" value="1" class="rounded text-primary" checked>
                            <span class="text-xs font-bold text-slate-600">Dùng cho biến thể</span>
                        </div>
                    </div>
                </div>`;
            $('#attributes-wrapper').append(html);
            $(`#attr-block-${id} .select2-edit`).select2({ width: '100%', placeholder: 'Chọn giá trị...' });
        }

        $('#btn-add-attribute').click(function() {
            let select = $('#attribute-selector');
            let id = select.val();
            let name = select.find('option:selected').data('name');
            if (!id || $(`#attr-block-${id}`).length > 0) return;

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

        $('#btn-do-variation').click(function() {
            let action = $('#variation-action').val();
            let attrGroups = [];
            $('.var-checkbox-wrapper input:checked').each(function() {
                let block = $(this).closest('[id^="attr-block-"]');
                let attrId = block.find('input[type="hidden"]').val();
                let attrName = block.find('strong.text-sm').text().trim().toLowerCase(); 
                let vals = block.find('select').select2('data');      
                
                if (vals.length) {
                    attrGroups.push(vals.map(v => ({ attrId: attrId, valId: v.id, valName: v.text, attrName: attrName })));
                }
            });

            if (!attrGroups.length) return alert('Bro phải tick "Dùng cho biến thể" ở tab Thuộc tính và chọn ít nhất 1 giá trị đã nhé!');

            function getPriority(name) {
                if (name.includes('màu')) return 1;
                if (name.includes('ram')) return 2;
                if (name.includes('dung lượng') || name.includes('rom')) return 3;
                return 99;
            }

            attrGroups.sort((a, b) => getPriority(a[0].attrName) - getPriority(b[0].attrName));

            let combos = cartesian(attrGroups);
            let wrapper = $('#variations-wrapper');

            if (action === 'generate') {
                if(!confirm('CẢNH BÁO: Hành động này sẽ xóa toàn bộ biến thể đang có bên dưới. Bro chắc chứ?')) return;
                wrapper.empty();
            }

            $('#bulk-update-variations').fadeIn();

            let existingSignatures = [];
            $('.variation-item').each(function() {
                let sigParts = [];
                $(this).find('input[type="hidden"][name*="[attributes]"]').each(function() {
                    let match = $(this).attr('name').match(/\[attributes\]\[(\d+)\]/);
                    if (match) sigParts.push(match[1] + '-' + $(this).val()); 
                });
                sigParts.sort();
                existingSignatures.push(sigParts.join('|'));
            });

            let addedCount = 0;

            combos.forEach((combo) => {
                let comboSigParts = combo.map(c => c.attrId + '-' + c.valId);
                comboSigParts.sort();
                let signature = comboSigParts.join('|');

                if (action === 'add_missing' && existingSignatures.includes(signature)) return; 

                let title = combo.map(c => c.valName).join(' - ');
                let uniqueIdx = 'new_' + Date.now() + Math.floor(Math.random() * 1000); 
                let hiddens = combo.map(c => `<input type="hidden" name="variations[${uniqueIdx}][attributes][${c.attrId}]" value="${c.valId}">`).join('');
                
                let html = `
                    <div class="border border-green-300 rounded-lg overflow-hidden bg-green-50/20 variation-item shadow-sm mt-3">
                        <div class="bg-slate-50 p-3 flex justify-between items-center border-b border-slate-200 cursor-pointer" onclick="$(this).next().slideToggle()">
                            <div class="flex items-center gap-3">
                                <span class="material-symbols-outlined text-slate-400">drag_indicator</span>
                                <strong class="text-xs text-green-600 uppercase">#MỚI — ${title}</strong>
                                ${hiddens}
                            </div>
                            <div class="flex items-center gap-4">
                                <button type="button" class="text-red-500 text-[10px] font-bold uppercase" onclick="$(this).closest('.variation-item').remove()">Xóa</button>
                            </div>
                        </div>
                        <div class="p-4 grid grid-cols-2 md:grid-cols-5 gap-4 bg-white" style="display:none;">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">SKU</label>
                                <input type="text" name="variations[${uniqueIdx}][sku]" class="w-full text-sm border-slate-200 rounded py-1.5 px-2">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Giá bán (₫)</label>
                                <input type="number" name="variations[${uniqueIdx}][price]" class="w-full text-sm border-slate-200 rounded py-1.5 px-2">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Giá KM</label>
                                <input type="number" name="variations[${uniqueIdx}][sale_price]" class="w-full text-sm border-slate-200 rounded py-1.5 px-2">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Kho</label>
                                <input type="number" name="variations[${uniqueIdx}][stock]" value="0" class="w-full text-sm border-slate-200 rounded py-1.5 px-2">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Ảnh biến thể</label>
                                <input type="file" name="variations[${uniqueIdx}][thumbnail]" accept="image/*" class="w-full text-[11px] border-slate-200 rounded py-1 bg-slate-50 cursor-pointer file:border-0 file:bg-slate-200 file:text-slate-700 file:text-[10px] file:font-bold file:px-2 file:py-1 file:rounded hover:file:bg-slate-300">
                            </div>
                        </div>
                    </div>`;
                
                wrapper.append(html);
                addedCount++;
            });

            if (action === 'add_missing' && addedCount === 0) {
                alert('Tất cả các tổ hợp này đều đã tồn tại, không có biến thể nào mới được tạo thêm!');
            } else if (addedCount > 0) {
                alert(`Đã thêm thành công ${addedCount} biến thể mới. Bấm mở rộng để nhập giá, kho và ảnh nhé!`);
            } 
        });

        function cartesian(a) { return a.reduce((a, b) => a.flatMap(d => b.map(e => [d, e].flat()))); }

        $('#btn-apply-bulk').click(function() {
            let variationsCount = $('#variations-wrapper .variation-item').length;
            if (variationsCount === 0) return alert('Bro chưa có biến thể nào để cập nhật cả!');

            let bulkPrice = $('#bulk-price').val();
            let bulkSalePrice = $('#bulk-sale-price').val();
            let bulkStock = $('#bulk-stock').val();

            if (!bulkPrice && !bulkSalePrice && !bulkStock) return alert('Bro hãy nhập ít nhất 1 giá trị để áp dụng nhé!');

            if (confirm(`Bro có chắc chắn muốn áp dụng các mức giá/kho này cho toàn bộ ${variationsCount} biến thể không?`)) {
                $('#variations-wrapper .variation-item').each(function() {
                    if (bulkPrice !== '') $(this).find('input[name$="[price]"]').val(bulkPrice);
                    if (bulkSalePrice !== '') $(this).find('input[name$="[sale_price]"]').val(bulkSalePrice);
                    if (bulkStock !== '') $(this).find('input[name$="[stock]"]').val(bulkStock);
                });
                
                $('#bulk-price, #bulk-sale-price, #bulk-stock').val('');
                alert('Cập nhật hàng loạt thành công! 🎉');
            }
        });
        
        // --- LOAD THUỘC TÍNH CŨ KHI EDIT (FIX LỖI NULL) ---
        @php
            $groupedAttrs = [];
            if (isset($product) && $product->variants) {
                foreach($product->variants as $variant) {
                    if ($variant->attributeValues) {
                        foreach($variant->attributeValues as $attrVal) {
                            $attrId = $attrVal->attribute_id;
                            if(!isset($groupedAttrs[$attrId])) {
                                $groupedAttrs[$attrId] = [
                                    'id' => $attrId,
                                    'name' => $attrVal->attribute->name ?? 'Thuộc tính',
                                    'values' => []
                                ];
                            }
                            $existingIds = array_column($groupedAttrs[$attrId]['values'], 'id');
                            if(!in_array($attrVal->id, $existingIds)) {
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
                        renderAttr('{{ $attrId }}', '{!! $data['name'] !!}', res.data);
                        setTimeout(() => {
                            let select = $(`#attr-block-{{ $attrId }} .select2-edit`);
                            select.val({!! json_encode(array_column($data['values'], 'id')) !!}).trigger('change');
                        }, 500);
                    }
                });
            @endforeach
        @endif
    });
</script>
@endsection