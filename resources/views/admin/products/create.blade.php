@extends('admin.layouts.app')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<div class="bg-slate-50 text-slate-900 font-display min-h-screen">
    <div class="relative flex min-h-screen w-full flex-col">
        
        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" id="form-create-product">
            @csrf
            
            <main class="flex-1 max-w-[1400px] mx-auto w-full p-4 sm:p-8">
                
                <div class="mb-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2 text-sm text-slate-500 mb-2">
                            <a href="{{ route('admin.products.index') }}" class="flex items-center gap-1 font-bold text-primary hover:underline">
                                <span class="material-symbols-outlined text-sm">inventory_2</span> Sản phẩm
                            </a>
                            <span class="material-symbols-outlined text-xs">chevron_right</span>
                            <span class="text-slate-900 font-medium">Thêm sản phẩm mới</span>
                        </div>
                        <h1 class="text-3xl font-black tracking-tight text-slate-900">Thêm sản phẩm mới</h1>
                    </div>
                    <div class="flex items-center gap-3">
                        <button type="button" class="px-5 py-2.5 rounded-lg border border-slate-300 font-bold text-sm bg-white hover:bg-slate-50 transition-all">Lưu nháp</button>
                        <button type="submit" class="px-6 py-2.5 rounded-lg bg-primary text-slate-900 font-bold text-sm hover:brightness-105 shadow-md shadow-primary/20 transition-all">Đăng sản phẩm</button>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                    <div class="lg:col-span-8 space-y-6">
                        
                        <div class="bg-white rounded-xl p-6 shadow-sm border border-slate-200">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-3">Tên sản phẩm <span class="text-red-500">*</span></label>
                                    <input name="name" value="{{ old('name') }}" 
                                           class="w-full text-xl font-bold rounded-lg focus:border-primary focus:ring-primary py-3 px-4 bg-slate-50/50 placeholder-slate-400 @error('name') border-red-500 @else border-slate-200 @enderror" 
                                           placeholder="Nhập tên sản phẩm..." type="text"/>
                                    @error('name')
                                        <span class="text-sm text-red-500 mt-2 block font-medium">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-3">Mã SP (SKU) <span class="text-red-500">*</span></label>
                                    <input name="sku" value="{{ old('sku') }}" 
                                           class="w-full text-xl font-bold rounded-lg focus:border-primary focus:ring-primary py-3 px-4 bg-slate-50/50 placeholder-slate-400 @error('sku') border-red-500 @else border-slate-200 @enderror" 
                                           placeholder="Để trống sẽ tự tạo theo tên..." type="text"/>
                                    @error('sku')
                                        <span class="text-sm text-red-500 mt-2 block font-medium">{{ $message }}</span>
                                    @enderror
                                    <span class="text-xs text-slate-400 mt-2 block">Nếu không nhập, hệ thống sẽ tự tạo từ tên sản phẩm.</span>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white rounded-xl p-6 shadow-sm border border-slate-200">
                            <label class="block text-sm font-bold text-slate-700 mb-4">Mô tả sản phẩm</label>
                            <div class="border border-slate-200 rounded-lg overflow-hidden">
                                <textarea id="description-editor" name="description" class="w-full border-none focus:ring-0 min-h-[250px] p-4 text-slate-800 leading-relaxed text-sm bg-white" placeholder="Bắt đầu viết mô tả chi tiết tại đây...">{{ old('description') }}</textarea>
                            </div>
                        </div>

                        <!-- LOẠI SẢN PHẨM -->
                        <div class="bg-white rounded-xl p-6 shadow-sm border border-slate-200">
                            <label class="block text-sm font-bold text-slate-700 mb-3">Loại sản phẩm <span class="text-red-500">*</span></label>
                            <select name="type" id="product_type" class="w-full md:w-1/2 text-sm border-slate-200 rounded-lg py-2.5 px-4 bg-white focus:ring-primary font-bold text-primary shadow-sm cursor-pointer transition-colors">
                                <option value="simple" {{ old('type') == 'simple' ? 'selected' : '' }}>Sản phẩm đơn giản</option>
                                <option value="variable" {{ old('type') == 'variable' ? 'selected' : '' }}>Sản phẩm biến thể</option>
                            </select>
                        </div>

                        <!-- GIÁ VÀ TỒN KHO -->
                        <div id="simple-product-card" class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                            <div class="p-4 border-b border-slate-100 bg-slate-50/50">
                                <h4 class="font-black text-sm uppercase tracking-wider text-slate-700">Giá & Tồn kho</h4>
                            </div>
                            <div class="p-6 space-y-5">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                                    <div>
                                        <label class="block text-sm font-bold text-slate-700 mb-2">Giá bán thường (₫)</label>
                                        <input type="number" name="price" value="{{ old('price') }}" class="w-full text-sm rounded-lg focus:ring-primary focus:border-primary py-2.5 px-3 bg-slate-50 @error('price') border-red-500 @else border-slate-200 @enderror" placeholder="VD: 25000000"/>
                                        @error('price')
                                            <span class="text-xs text-red-500 mt-1.5 block font-medium">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-slate-700 mb-2">Giá khuyến mãi (₫)</label>
                                        <input type="number" name="sale_price" value="{{ old('sale_price') }}" class="w-full text-sm rounded-lg focus:ring-primary focus:border-primary py-2.5 px-3 bg-slate-50 @error('sale_price') border-red-500 @else border-slate-200 @enderror" placeholder="Tùy chọn"/>
                                        @error('sale_price')
                                            <span class="text-xs text-red-500 mt-1.5 block font-medium">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-slate-700 mb-2">Tồn kho</label>
                                        <input type="number" name="stock" value="{{ old('stock', 0) }}" class="w-full text-sm rounded-lg focus:ring-primary focus:border-primary py-2.5 px-3 bg-slate-50 @error('stock') border-red-500 @else border-slate-200 @enderror" placeholder="0"/>
                                        @error('stock')
                                            <span class="text-xs text-red-500 mt-1.5 block font-medium">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="p-4 border-t border-slate-100 bg-slate-50/50 flex justify-between items-center mt-2">
                                <h4 class="font-black text-sm uppercase tracking-wider text-slate-700">
                                    <span class="material-symbols-outlined text-base align-middle mr-1">memory</span> 
                                    Thông số kỹ thuật
                                </h4>
                                <button type="button" class="btn-add-spec text-xs font-bold bg-slate-200 text-slate-700 hover:bg-slate-300 px-3 py-1.5 rounded transition-colors shadow-sm" data-target="#simple-specs-wrapper" data-name="spec">
                                    + Thêm dòng mới
                                </button>
                            </div>
                            <div class="p-5">
                                <div id="simple-specs-wrapper" class="space-y-1"></div>
                            </div>
                        </div>

                        <!-- THUỘC TÍNH & BIẾN THỂ -->
                        <div id="variable-product-card" class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden hidden">
                            <div class="p-4 border-b border-slate-100 bg-slate-50/50">
                                <h4 class="font-black text-sm uppercase tracking-wider text-slate-700">Thuộc tính & Các Biến thể</h4>
                            </div>
                            <div class="p-6">
                                
                                <h5 class="font-bold text-sm text-slate-800 mb-3 border-l-4 border-primary pl-2">1. Chọn thuộc tính</h5>
                                <div class="flex items-center gap-3 bg-slate-50 p-3 rounded-lg border border-slate-200 mb-6">
                                    <div class="flex-1">
                                        <select id="attribute-selector" class="w-full text-sm border-slate-200 rounded-lg py-2 px-3 focus:ring-primary">
                                            <option value="">-- Thêm thuộc tính hiện có --</option>
                                            @foreach($attributes as $attr)
                                                <option value="{{ $attr->id }}" data-name="{{ $attr->name }}">{{ $attr->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <button type="button" id="btn-add-attribute" class="bg-slate-200 hover:bg-slate-300 text-slate-700 px-4 py-2 flex items-center justify-center rounded text-sm font-bold transition-colors h-10">Thêm</button>
                                </div>
                                <div class="space-y-4 mb-8 border-b border-slate-100 pb-8" id="attributes-wrapper"></div>
                                
                                <h5 class="font-bold text-sm text-slate-800 mb-3 border-l-4 border-primary pl-2">2. Cài đặt biến thể</h5>
                                <div class="flex flex-col md:flex-row gap-3 mb-6">
                                    <button type="button" id="btn-generate-variations" class="bg-primary hover:brightness-105 text-slate-900 px-5 py-2.5 rounded-lg text-sm font-bold transition-all shadow-sm">Tạo tất cả thuộc tính</button>
                                    <button type="button" id="btn-add-manual-variation" class="bg-slate-200 hover:bg-slate-300 text-slate-700 px-5 py-2.5 rounded-lg text-sm font-bold transition-all border border-slate-300">Tạo một cái một</button>
                                </div>

                                <div id="bulk-update-variations" class="bg-emerald-50 border border-emerald-200 p-4 rounded-lg mb-6 shadow-sm" style="display: none;">
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

                                <div id="variations-wrapper" class="space-y-4 mt-4"></div>

                            </div>
                        </div>

                    </div>

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
                                            <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Hiển thị</option>
                                            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Bản nháp</option>
                                        </select>
                                    </span>
                                </div>
                                <div class="flex items-center justify-between text-sm">
                                    <span class="flex items-center gap-2 text-slate-500 font-medium"><span class="material-symbols-outlined text-lg text-amber-500">rocket_launch</span> Nổi bật:</span>
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="hidden" name="is_featured" value="0">
                                        <input type="checkbox" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }} class="rounded text-primary border-slate-300 focus:ring-primary">
                                        <span class="text-sm font-bold text-slate-700">Sản phẩm nổi bật</span>
                                    </label>
                                </div>
                            </div>
                            <div class="bg-slate-50 p-4 border-t border-slate-100 flex items-center justify-between">
                                <a href="{{ route('admin.products.index') }}" class="text-slate-500 hover:text-red-500 text-sm font-bold hover:underline transition-colors">Hủy bỏ</a>
                                <button type="submit" class="bg-primary text-slate-900 px-6 py-2.5 rounded-lg font-black text-sm shadow-md shadow-primary/20 hover:brightness-105 transition-all">Lưu sản phẩm</button>
                            </div>
                        </div>

                        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                            <div class="p-4 border-b border-slate-100 bg-slate-50/50">
                                <h4 class="font-black text-sm uppercase tracking-wider text-slate-700">Danh mục sản phẩm <span class="text-red-500">*</span></h4>
                            </div>
                            <div class="p-5">
                                <select name="category_ids[]" multiple class="select2-categories w-full">
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ is_array(old('category_ids')) && in_array($category->id, old('category_ids')) ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_ids')
                                    <span class="text-sm text-red-500 mt-2 block font-medium">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                            <div class="p-4 border-b border-slate-100 bg-slate-50/50">
                                <h4 class="font-black text-sm uppercase tracking-wider text-slate-700">Thương hiệu</h4>
                            </div>
                            <div class="p-5">
                                <select name="brand_id" class="w-full text-sm border-slate-200 rounded-lg py-2.5 focus:ring-primary focus:border-primary font-medium bg-slate-50/50 cursor-pointer">
                                    <option value="">-- Chọn thương hiệu --</option>
                                    @foreach($brands ?? [] as $brand)
                                        <option value="{{ $brand->id }}" {{ old('brand_id') == $brand->id ? 'selected' : '' }}>
                                            {{ $brand->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                            <div class="p-4 border-b border-slate-100 bg-slate-50/50">
                                <h4 class="font-black text-sm uppercase tracking-wider text-slate-700">Ảnh đại diện <span class="text-red-500">*</span></h4>
                            </div>
                            <div class="p-5">
                                <div class="relative group aspect-square rounded-xl overflow-hidden border-2 border-dashed border-slate-200 bg-slate-50 hover:border-primary transition-colors cursor-pointer flex flex-col items-center justify-center">
                                    <input type="file" name="thumbnail" accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                                    <span class="material-symbols-outlined text-4xl text-slate-300 group-hover:text-primary transition-colors">add_photo_alternate</span>
                                    <span class="text-xs font-bold text-slate-400 mt-2 uppercase">Chọn ảnh (Bắt buộc)</span>
                                </div>
                                @error('thumbnail')
                                    <span class="text-sm text-red-500 mt-2 block font-medium">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                            <div class="p-4 border-b border-slate-100 bg-slate-50/50">
                                <h4 class="font-black text-sm uppercase tracking-wider text-slate-700">Album hình ảnh</h4>
                            </div>
                            <div class="p-5 overflow-hidden">
                                <div id="gallery-preview-container" class="grid grid-cols-3 sm:grid-cols-4 gap-3 mb-4">
                                    <!-- Previews will appear here -->
                                </div>
                                <div class="relative group aspect-[2/1] rounded-lg border-2 border-dashed border-slate-200 bg-slate-50 hover:border-primary transition-colors cursor-pointer flex flex-col items-center justify-center overflow-hidden">
                                    <input type="file" name="images[]" id="gallery-input" multiple accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                                    <div class="flex flex-col items-center justify-center pointer-events-none" id="gallery-placeholder">
                                        <span class="material-symbols-outlined text-2xl text-slate-300 group-hover:text-primary transition-colors">collections</span>
                                        <span class="text-[10px] font-black uppercase mt-1 text-slate-400">Chọn nhiều ảnh</span>
                                    </div>
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
        // --- PRODUCT TYPE TOGGLE ---
        const productTypeSelect = document.getElementById('product_type');
        const simpleCard = document.getElementById('simple-product-card');
        const variableCard = document.getElementById('variable-product-card');

        // Init select2 cho bộ chọn thuộc tính
        $('#attribute-selector').select2({
            placeholder: "-- Thêm thuộc tính hiện có --",
            width: '100%'
        });

        // Init select2 cho danh mục
        $('.select2-categories').select2({
            placeholder: "Chọn một hoặc nhiều danh mục...",
            width: '100%',
            allowClear: true
        });

        function toggleProductType() {
            if(productTypeSelect.value === 'variable') {
                simpleCard.classList.add('hidden');
                variableCard.classList.remove('hidden');
            } else {
                simpleCard.classList.remove('hidden');
                variableCard.classList.add('hidden');
            }
        }
        productTypeSelect.addEventListener('change', toggleProductType);
        toggleProductType();

        // --- PREVIEW IMAGES ---
        const thumbnailInput = document.querySelector('input[name="thumbnail"]');
        if (thumbnailInput) {
            const thumbnailContainer = thumbnailInput.closest('.group');
            thumbnailInput.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        thumbnailContainer.style.backgroundImage = `url('${e.target.result}')`;
                        thumbnailContainer.style.backgroundSize = 'cover';
                        thumbnailContainer.style.backgroundPosition = 'center';
                        thumbnailContainer.querySelector('.material-symbols-outlined').style.opacity = '0';
                        thumbnailContainer.querySelector('.text-xs').style.opacity = '0';
                    }
                    reader.readAsDataURL(this.files[0]);
                }
            });
        }

        // --- GALLERY PREVIEWS ---
        let galleryDataTransfer = new DataTransfer();
        const galleryInput = document.getElementById('gallery-input');
        const galleryPreviewContainer = document.getElementById('gallery-preview-container');
        const galleryPlaceholder = document.getElementById('gallery-placeholder');

        if (galleryInput) {
            galleryInput.addEventListener('change', function() {
                // Add newly selected files to our DataTransfer object
                for (let i = 0; i < this.files.length; i++) {
                    galleryDataTransfer.items.add(this.files[i]);
                }
                this.files = galleryDataTransfer.files; // Sync the input files
                renderGalleryPreviews();
            });
        }

        function renderGalleryPreviews() {
            galleryPreviewContainer.innerHTML = '';
            const files = galleryDataTransfer.files;
            
            if (files.length > 0) {
                // Keep placeholder minimal
                galleryPlaceholder.querySelector('span.text-\\[10px\\]').textContent = `Thêm ảnh (${files.length} đã chọn)`;
            } else {
                galleryPlaceholder.querySelector('span.text-\\[10px\\]').textContent = 'Chọn nhiều ảnh';
            }

            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                const reader = new FileReader();
                reader.onload = function(e) {
                    const previewHtml = `
                        <div class="relative aspect-square rounded-lg border border-slate-200 overflow-hidden group shadow-sm bg-white p-1">
                            <img src="${e.target.result}" class="w-full h-full object-cover rounded-md">
                            <button type="button" class="absolute top-1 right-1 size-5 bg-red-600 text-white rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity shadow-md hover:bg-red-700 btn-remove-gallery-img" data-index="${i}">
                                <span class="material-symbols-outlined text-[14px]">close</span>
                            </button>
                        </div>
                    `;
                    galleryPreviewContainer.insertAdjacentHTML('beforeend', previewHtml);
                };
                reader.readAsDataURL(file);
            }
        }

        $(document).on('click', '.btn-remove-gallery-img', function(e) {
            e.preventDefault();
            const indexToRemove = parseInt($(this).data('index'));
            const newDataTransfer = new DataTransfer();
            const files = galleryDataTransfer.files;
            
            for (let i = 0; i < files.length; i++) {
                if (i !== indexToRemove) {
                    newDataTransfer.items.add(files[i]);
                }
            }
            
            galleryDataTransfer = newDataTransfer;
            galleryInput.files = galleryDataTransfer.files; // Sync the input files
            renderGalleryPreviews();
        });

        $(document).on('change', '.var-thumb-input', function() {
            let container = $(this).closest('.variation-thumb-container');
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    container.css('background-image', `url('${e.target.result}')`);
                    container.css('background-size', 'cover');
                    container.css('background-position', 'center');
                    container.find('span').hide();
                }
                reader.readAsDataURL(this.files[0]);
            }
        });

        // --- TINYMCE ---
        tinymce.init({
            selector: '#description-editor',
            height: 500,
            plugins: [ 'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview', 'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen', 'insertdatetime', 'media', 'table', 'help', 'wordcount' ],
            toolbar: 'undo redo | blocks | bold italic textcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media | table | removeformat | code',
            content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; font-size: 15px; color: #334155; }',
            paste_data_images: true, branding: false, promotion: false
        });
    });

    $(document).ready(function() {
        const baseUrl = '/admin'; 

        // ==========================================
        // LOGIC THÔNG SỐ KỸ THUẬT (SPECS) - FREE-FORM
        // ==========================================

        /**
         * Tạo một dòng thông số mới (key là text input, không giới hạn số lượng)
         * @param {string} targetDiv - CSS selector của wrapper chứa dòng spec  
         * @param {string} keyName   - name attribute cho input key (vd: spec_keys[])
         * @param {string} valName   - name attribute cho input value
         * @param {string} keyVal    - giá trị mặc định cho key (khi phục hồi)
         * @param {string} valVal    - giá trị mặc định cho value (khi phục hồi)
         */
        function addSpecRow(targetDiv, keyName, valName, keyVal = '', valVal = '') {
            let newRow = `
                <div class="flex items-center gap-2 spec-row mt-2 group">
                    <span class="material-symbols-outlined text-slate-300 cursor-grab text-base flex-shrink-0" title="Kéo để sắp xếp">drag_indicator</span>
                    <input type="text"
                           name="${keyName}"
                           value="${keyVal}"
                           placeholder="Tên thông số (VD: RAM, Pin, Màn hình...)"
                           class="w-2/5 text-xs border-slate-200 rounded-md focus:ring-primary focus:border-primary py-1.5 px-2.5 bg-white spec-key-input"
                    >
                    <span class="text-slate-300 text-xs flex-shrink-0">:</span>
                    <input type="text"
                           name="${valName}"
                           value="${valVal}"
                           placeholder="Giá trị (VD: 8GB, 6.1 inch, 5000mAh...)"
                           class="flex-1 text-xs border-slate-200 rounded-md focus:ring-primary focus:border-primary py-1.5 px-2.5 bg-white spec-val-input"
                    >
                    <button type="button" class="btn-remove-spec flex-shrink-0 text-slate-300 hover:text-red-500 p-1 rounded transition-colors opacity-0 group-hover:opacity-100" title="Xóa dòng này">
                        <span class="material-symbols-outlined text-base">close</span>
                    </button>
                </div>
            `;
            $(targetDiv).append(newRow);
        }

        // Nút thêm dòng thông số — dùng delegation vì các nút được tạo động
        $(document).on('click', '.btn-add-spec', function() {
            let targetDiv = $(this).data('target');
            let namePrefix = $(this).data('name'); // Vd: "spec" hoặc "variations[0][spec]"

            // Xử lý tạo tên name="" cho thẻ input
            let keyName = namePrefix === 'spec' ? 'spec_keys[]' : namePrefix.replace('[spec]', '[spec_keys][]');
            let valName = namePrefix === 'spec' ? 'spec_values[]' : namePrefix.replace('[spec]', '[spec_values][]');

            addSpecRow(targetDiv, keyName, valName);
            // Focus vào ô key vừa tạo để nhập liền
            $(targetDiv).find('.spec-row:last-child .spec-key-input').focus();
        });

        // Nút xóa dòng
        $(document).on('click', '.btn-remove-spec', function() {
            $(this).closest('.spec-row').fadeOut(150, function() { $(this).remove(); });
        });

        // ==========================================
        // PHỤC HỒI THÔNG SỐ CHO SẢN PHẨM ĐƠN KHI LỖI VALIDATE
        // ==========================================
        let oldSimpleSpecKeys   = @json(old('spec_keys', []));
        let oldSimpleSpecValues = @json(old('spec_values', []));

        if (oldSimpleSpecKeys.length > 0) {
            oldSimpleSpecKeys.forEach((sk, i) => {
                let sv = oldSimpleSpecValues[i] ?? '';
                addSpecRow('#simple-specs-wrapper', 'spec_keys[]', 'spec_values[]', sk, sv);
            });
        }

        // ==========================================
        // LOGIC ATTRIBUTES & VARIATIONS
        // ==========================================
        
        // Phục hồi attributes nếu có lỗi validate
        let oldAttrs = @json(old('attributes', []));
        if (oldAttrs && typeof oldAttrs === 'object') {
            for (let id in oldAttrs) {
                let attrId = oldAttrs[id].id;
                let selectedVals = oldAttrs[id].values || [];
                $.ajax({
                    url: `${baseUrl}/attributes/${attrId}/get-values`, type: 'GET',
                    success: function(response) {
                        if (response.success) {
                            let attrName = $('#attribute-selector option[value="'+attrId+'"]').data('name') || `Thuộc tính #${attrId}`;
                            renderAttributeBlock(attrId, attrName, response.data);
                            $(`#attr-block-${attrId} select`).val(selectedVals).trigger('change');
                        }
                    }
                });
            }
        }

        $('#btn-add-attribute').click(function() {
            let select = $('#attribute-selector');
            let attrId = select.val();
            let attrName = select.find('option:selected').data('name');
            if (!attrId) return alert('Bro chưa chọn thuộc tính nào kìa!');
            if ($(`#attr-block-${attrId}`).length > 0) return alert('Thuộc tính này đã được thêm rồi nhé!');

            $.ajax({
                url: `${baseUrl}/attributes/${attrId}/get-values`, type: 'GET',
                success: function(response) {
                    if (response.success) {
                        renderAttributeBlock(attrId, attrName, response.data);
                        select.val(''); 
                    }
                }
            });
        });

        function renderAttributeBlock(id, name, values) {
            let optionsHtml = values.map(val => `<option value="${val.id}">${val.value || val.name}</option>`).join('');

            let html = `
                <div class="border border-slate-200 rounded-lg overflow-hidden mb-4" id="attr-block-${id}">
                    <div class="bg-slate-50 p-3 flex justify-between items-center border-b border-slate-200">
                        <strong class="text-sm text-slate-800">${name}</strong>
                        <button type="button" class="text-red-500 hover:text-red-700 text-xs font-bold btn-remove-attr" data-id="${id}">Xóa thuộc tính</button>
                    </div>
                    <div class="p-4 bg-white flex flex-col md:flex-row md:items-center gap-4">
                        <div class="w-full md:w-1/4">
                            <span class="text-sm text-slate-500 font-bold mb-1 block">Chọn giá trị:</span>
                            <input type="hidden" name="attributes[${id}][id]" value="${id}">
                            <input type="hidden" name="attributes[${id}][visible]" value="1">
                            <input type="hidden" name="attributes[${id}][variation]" value="1">
                        </div>
                        <div class="w-full md:w-3/4">
                            <select multiple name="attributes[${id}][values][]" class="w-full text-sm border-slate-200 rounded-lg focus:ring-primary select2-dynamic">${optionsHtml}</select>
                            <div class="flex gap-2 mt-3">
                                <button type="button" class="btn-select-all bg-slate-100 border border-slate-300 px-3 py-1 text-[11px] rounded hover:bg-slate-200 font-bold text-slate-700">Chọn tất cả</button>
                                <button type="button" class="btn-deselect-all bg-slate-100 border border-slate-300 px-3 py-1 text-[11px] rounded hover:bg-slate-200 font-bold text-slate-700">Không chọn</button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            $('#attributes-wrapper').append(html);
            $(`#attr-block-${id} .select2-dynamic`).select2({ placeholder: "Click để chọn giá trị...", width: '100%' });

            // ĐỒNG BỘ: Cập nhật thẻ select cho thuộc tính mới vào TẤT CẢ các biến thể hiện tại
            $('.attribute-selects-container').each(function() {
                let container = $(this);
                let item = container.closest('.variation-item');
                let nameMatch = item.find('input[name^="variations["]').first().attr('name') || container.find('input.old-hidden-attr').first().attr('name') || container.find('select.select2-manual-var').first().attr('name');
                if(!nameMatch) return;
                let m = nameMatch.match(/variations\[(\d+)\]/);
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

        // Tự động cập nhật tiêu đề biến thể khi thay đổi dropdown Select2
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
        $(document).on('click', '.btn-select-all', function() { $(this).closest('.w-full').find('select').find('option').prop('selected', true).trigger('change'); });
        $(document).on('click', '.btn-deselect-all', function() { $(this).closest('.w-full').find('select').find('option').prop('selected', false).trigger('change'); });

        // ==========================================
        // HÀM DỰNG NỘI DUNG THẺ BIẾN THỂ (shared)
        // prefix: 'var' | 'manual-var' | 'old-var'
        // data: object có sku/price/sale_price/stock (tuỳ chọn, dùng khi phục hồi)
        // ==========================================
        function buildVariationBody(index, prefix, data = {}) {
            let sku        = data.sku        ?? '';
            let price      = data.price      ?? '';
            let sale_price = data.sale_price ?? '';
            let stock      = data.stock      ?? '';
            let is_active  = data.hasOwnProperty('is_active') ? data.is_active : 1;
            let activeChecked = (is_active == 1) ? 'checked' : '';
            let specId     = `${prefix}-specs-${index}`;

            return `
            <div class="var-body p-5 space-y-4 bg-white" style="display:none;">

                {{-- Hàng 1: Ảnh --}}
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Ảnh biến thể</label>
                    <div class="relative group w-24 h-24 rounded-xl border-2 border-dashed border-slate-200 bg-slate-50 hover:border-primary transition-colors cursor-pointer flex flex-col items-center justify-center overflow-hidden variation-thumb-container">
                        <input type="file" name="variations[${index}][thumbnail]" accept="image/*"
                               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10 var-thumb-input">
                        <span class="material-symbols-outlined text-3xl text-slate-300 group-hover:text-primary transition-colors">add_photo_alternate</span>
                        <span class="text-[10px] text-slate-400 mt-1">Chọn ảnh</span>
                    </div>
                </div>
                {{-- Hàng 2: SKU + Tồn kho --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">Mã SP (SKU)</label>
                        <input type="text" name="variations[${index}][sku]" value="${sku}"
                               placeholder="Để trống tự tạo..."
                               class="w-full text-sm border-slate-200 rounded-lg focus:ring-primary focus:border-primary py-2 px-3 bg-slate-50">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">Tồn kho</label>
                        <input type="number" name="variations[${index}][stock]" value="${stock}"
                               placeholder="0"
                               class="w-full text-sm border-slate-200 rounded-lg focus:ring-primary focus:border-primary py-2 px-3 bg-slate-50">
                    </div>
                </div>

                {{-- Hàng 3: Giá thường + Giá KM --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">Giá thường (₫) <span class="text-red-500">*</span></label>
                        <input type="number" name="variations[${index}][price]" value="${price}"
                               placeholder="VD: 25000000"
                               class="w-full text-sm border-slate-200 rounded-lg focus:ring-primary focus:border-primary py-2 px-3 bg-slate-50">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">Giá KM (₫)</label>
                        <input type="number" name="variations[${index}][sale_price]" value="${sale_price}"
                               placeholder="Tùy chọn"
                               class="w-full text-sm border-slate-200 rounded-lg focus:ring-primary focus:border-primary py-2 px-3 bg-slate-50">
                    </div>
                </div>

                {{-- Hàng 4: Trạng thái hiển thị --}}
                <div class="pt-2">
                    <label class="flex items-center gap-2 cursor-pointer w-max">
                        <input type="hidden" name="variations[${index}][status]" value="inactive">
                        <input type="checkbox" name="variations[${index}][status]" value="active" ${activeChecked} class="rounded text-primary border-slate-300 focus:ring-primary">
                        <span class="text-xs font-bold text-slate-700">Hiển thị (Kích hoạt)</span>
                    </label>
                </div>

                {{-- Dưới cùng: Thông số kỹ thuật --}}
                <div class="border-t border-slate-100 pt-4">
                    <div class="flex justify-between items-center mb-2">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">
                            <span class="material-symbols-outlined text-sm align-middle">memory</span>
                            Thông số kỹ thuật
                        </label>
                        <button type="button"
                                class="btn-add-spec text-[11px] font-semibold bg-slate-100 text-slate-600 hover:bg-primary hover:text-slate-900 px-3 py-1.5 rounded-lg transition-colors border border-slate-200"
                                data-target="#${specId}"
                                data-name="variations[${index}][spec]">
                            + Thêm dòng
                        </button>
                    </div>
                    <div id="${specId}" class="space-y-2"></div>
                </div>

            </div>`;
        }

        $('#btn-generate-variations').click(function() {

            let attrGroups = [];
            let missingValue = false;
            $('#attributes-wrapper [id^="attr-block-"]').each(function() {
                let block = $(this);
                let attrId = block.find('input[name*="[id]"]').val(); 
                let attrName = block.find('strong.text-slate-800').first().text();
                let selectedOptions = block.find('select').select2('data');
                
                if (selectedOptions.length > 0) {
                    attrGroups.push(selectedOptions.map(opt => ({ attrId: attrId, attrName: attrName, valId: opt.id, valName: opt.text })));
                } else {
                    missingValue = true;
                }
            });

            if (missingValue) return alert('Vui lòng chọn ít nhất 1 giá trị cho TẤT CẢ các thuộc tính đã thêm để đảm bảo biến thể được tạo đồng bộ (tránh 3 thuộc tính mà 1 cặp chỉ có 2)!');
            if (attrGroups.length === 0) return alert('Ngươi chưa chọn thuộc tính nào dùng cho biến thể, hoặc chưa chọn giá trị nào cả!');

            let combinations = attrGroups.reduce((a,b) => a.flatMap(x => b.map(y => x.concat([y]))), [[]]);
            
            let wrapper = $('#variations-wrapper');
            $('#bulk-update-variations').fadeIn();

            let expectedSignatures = [];
            let expectedCombinationsData = [];
            
            combinations.forEach(combo => {
                let sigParts = combo.map(c => `${c.attrId}-${c.valId}`);
                let sig = sigParts.sort().join('|');
                expectedSignatures.push(sig);
                expectedCombinationsData.push({ sig: sig, combo: combo });
            });

            let existingSignatures = [];
            let maxIndex = -1;
            wrapper.find('.variation-item').each(function() {
                let item = $(this);
                let sigParts = [];
                item.find('input[type="hidden"][name*="[attributes]"]').each(function() {
                    let m = $(this).attr('name').match(/\[attributes\]\[(\d+)\]/);
                    if (m) sigParts.push(m[1] + '-' + $(this).val());
                });
                item.find('select[name*="[attributes]"]').each(function() {
                    let m = $(this).attr('name').match(/\[attributes\]\[(\d+)\]/);
                    if (m && $(this).val()) sigParts.push(m[1] + '-' + $(this).val());
                });
                
                let sig = sigParts.sort().join('|');
                
                if (!expectedSignatures.includes(sig)) {
                    item.remove();
                } else {
                    existingSignatures.push(sig);
                    let nameMatch = item.find('input[name^="variations["]').first().attr('name');
                    if (nameMatch) {
                        let m = nameMatch.match(/variations\[(\d+)\]/);
                        if (m && parseInt(m[1]) > maxIndex) maxIndex = parseInt(m[1]);
                    }
                }
            });

            let nextIndex = maxIndex + 1;

            // Xây dựng sẵn danh sách tùy chọn dựa trên DOM hiện hành
            let allAttrs = [];
            $('#attributes-wrapper [id^="attr-block-"]').each(function() {
                let block = $(this);
                let attrId = block.find('input[name*="[id]"]').val(); 
                let attrName = block.find('strong.text-slate-800').first().text();
                let options = [];
                block.find('select.select2-dynamic option').each(function() {
                    options.push({ id: $(this).attr('value'), text: $(this).text() });
                });
                if (options.length > 0) allAttrs.push({ id: attrId, name: attrName, options: options });
            });

            expectedCombinationsData.forEach(itemData => {
                if (!existingSignatures.includes(itemData.sig)) {
                    let combo = itemData.combo;
                    let title = combo.map(c => c.valName).join(' - ');
                    
                    let selectsHtml = allAttrs.map(attr => {
                        let opts = attr.options.map(o => {
                            let matched = combo.find(c => c.attrId == attr.id && c.valId == o.id);
                            return `<option value="${o.id}" ${matched ? 'selected' : ''}>${o.text}</option>`;
                        }).join('');
                        return `
                            <div class="flex flex-col gap-1 w-full md:w-48" data-attr-id="${attr.id}">
                                <label class="text-[11px] uppercase tracking-wider font-bold text-slate-500">${attr.name}</label>
                                <select name="variations[${nextIndex}][attributes][${attr.id}]" class="select2-manual-var w-full text-sm border-slate-200 rounded py-1 px-2 focus:ring-primary bg-slate-50">
                                    <option value="">-- Chọn --</option>
                                    ${opts}
                                </select>
                            </div>
                        `;
                    }).join('');

                    let html = `
                    <div class="border border-slate-200 rounded-lg overflow-hidden bg-white variation-item shadow-sm">
                        <div class="bg-slate-50 p-3 flex justify-between items-center border-b border-slate-200 cursor-pointer" onclick="$(this).siblings('.var-wrapper').slideToggle()">
                            <div class="flex items-center gap-3">
                                <span class="material-symbols-outlined text-slate-400">drag_indicator</span>
                                <strong class="text-sm text-slate-800">#${nextIndex + 1} — ${title}</strong>
                            </div>
                            <div class="flex items-center gap-3">
                                <button type="button" class="text-red-400 hover:text-red-600 text-xs font-bold" onclick="$(this).closest('.variation-item').remove(); event.stopPropagation();">Xóa</button>
                            </div>
                        </div>
                        <div class="var-wrapper">
                            <div class="p-4 bg-slate-50/50 border-b border-slate-100">
                                <div class="attribute-selects-container flex flex-wrap gap-3">
                                    ${selectsHtml}
                                </div>
                            </div>
                            ${buildVariationBody(nextIndex, 'var').replace('style="display:none;"', '')}
                        </div>
                    </div>`;
                    wrapper.append(html);
                    wrapper.find('.variation-item:last-child .select2-manual-var').select2({ width: '100%' });
                    nextIndex++;
                }
            });

            wrapper.find('.variation-item').each(function(i) {
                let strong = $(this).find('strong.text-slate-800');
                if(strong.text().includes('—')) {
                    let txt = strong.text().split('—')[1];
                    strong.text(`#${i + 1} — ${txt.trim()}`);
                }
            });

            wrapper.find('.var-body').first().show();
        });

        $('#btn-add-manual-variation').click(function() {
            let attrs = [];
            let missingValue = false;
            $('#attributes-wrapper [id^="attr-block-"]').each(function() {
                let block = $(this);
                let attrId = block.find('input[name*="[id]"]').val(); 
                let attrName = block.find('strong.text-slate-800').first().text();
                let selectedOptions = block.find('select').select2('data');
                
                if (selectedOptions.length > 0) {
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

            let index = $('#variations-wrapper .variation-item').length;
            $('#bulk-update-variations').fadeIn();
            
            let selectsHtml = attrs.map(attr => {
                let opts = attr.options.map(o => `<option value="${o.id}">${o.text}</option>`).join('');
                return `
                    <div class="flex flex-col gap-1 w-full md:w-48" data-attr-id="${attr.id}">
                        <label class="text-[11px] uppercase tracking-wider font-bold text-slate-500">${attr.name}</label>
                        <select name="variations[${index}][attributes][${attr.id}]" class="select2-manual-var w-full text-sm border-slate-200 rounded py-1 px-2 focus:ring-primary bg-slate-50">
                            <option value="">-- Chọn --</option>
                            ${opts}
                        </select>
                    </div>
                `;
            }).join('');

            let html = `
                <div class="border border-slate-200 rounded-lg overflow-hidden bg-white variation-item shadow-sm">
                    <div class="bg-slate-50 p-3 flex justify-between items-center border-b border-slate-200 cursor-pointer" onclick="$(this).siblings('.var-wrapper').slideToggle()">
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined text-slate-400">drag_indicator</span>
                            <strong class="text-sm text-slate-800">#${index + 1} — Chưa chọn đủ</strong>
                        </div>
                        <button type="button" class="text-red-400 hover:text-red-600 text-xs font-bold" onclick="$(this).closest('.variation-item').remove(); event.stopPropagation();">Xóa</button>
                    </div>
                    <div class="var-wrapper">
                        <div class="p-4 bg-slate-50/50 border-b border-slate-100">
                            <div class="attribute-selects-container flex flex-wrap gap-3">
                                ${selectsHtml}
                            </div>
                        </div>
                        ${buildVariationBody(index, 'manual-var').replace('style="display:none;"', '')}
                    </div>
                </div>`;
            $('#variations-wrapper').append(html);
            // Kích hoạt select2
            $('#variations-wrapper .variation-item:last-child .select2-manual-var').select2({ width: '100%' });
        });

        // Phục hồi variations nếu có lỗi validate
        let oldVars = @json(old('variations', []));
        if (oldVars && typeof oldVars === 'object' && Object.keys(oldVars).length > 0) {
            let wrapper = $('#variations-wrapper');
            $('#bulk-update-variations').fadeIn();
            Object.values(oldVars).forEach((vari, index) => {
                let hiddenInputs = '';
                if (vari.attributes) {
                    for(let attrId in vari.attributes) {
                        let valId = vari.attributes[attrId];
                        // Dùng old-hidden-attr để renderAttributeBlock nhận diện đồng bộ ngược
                        hiddenInputs += `<input type="hidden" name="variations[${index}][attributes][${attrId}]" value="${valId}" class="old-hidden-attr">`;
                    }
                }
                
                let html = `
                <div class="border border-slate-200 rounded-lg overflow-hidden bg-white variation-item shadow-sm">
                    <div class="bg-slate-50 p-3 flex justify-between items-center border-b border-slate-200 cursor-pointer" onclick="$(this).siblings('.var-wrapper').slideToggle()">
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined text-slate-400">drag_indicator</span>
                            <strong class="text-sm text-slate-800">#${index + 1} — Biến thể đã lưu</strong>
                        </div>
                        <button type="button" class="text-red-400 hover:text-red-600 text-xs font-bold" onclick="$(this).closest('.variation-item').remove(); event.stopPropagation();">Xóa</button>
                    </div>
                    <div class="var-wrapper">
                        <div class="p-4 bg-slate-50/50 border-b border-slate-100">
                            <div class="attribute-selects-container flex flex-wrap gap-3">
                                ${hiddenInputs}
                            </div>
                        </div>
                        ${buildVariationBody(index, 'old-var', vari).replace('style="display:none;"', '')}
                    </div>
                </div>`;
                wrapper.append(html);

                // Phục hồi specs cho biến thể cũ
                if (vari.spec_keys && vari.spec_values) {
                    let specTarget = '#old-var-specs-' + index;
                    let keyName = `variations[${index}][spec_keys][]`;
                    let valName = `variations[${index}][spec_values][]`;
                    vari.spec_keys.forEach((sk, si) => {
                        addSpecRow(specTarget, keyName, valName, sk, vari.spec_values[si] ?? '');
                    });
                }
            });
            // Mở thẻ đầu tiên tự động
            wrapper.find('.var-body').first().show();
        }

        $('#btn-apply-bulk').click(function() {
            let variationsCount = $('#variations-wrapper .variation-item').length;
            if (variationsCount === 0) return alert('Bro chưa có biến thể nào để cập nhật cả!');

            let bulkPrice = $('#bulk-price').val(); let bulkSalePrice = $('#bulk-sale-price').val(); let bulkStock = $('#bulk-stock').val();
            if (!bulkPrice && !bulkSalePrice && !bulkStock) return alert('Nhập ít nhất 1 giá trị để áp dụng!');

            if (confirm(`Áp dụng cho toàn bộ ${variationsCount} biến thể?`)) {
                $('#variations-wrapper .variation-item').each(function() {
                    if (bulkPrice !== '') $(this).find('input[name$="[price]"]').val(bulkPrice);
                    if (bulkSalePrice !== '') $(this).find('input[name$="[sale_price]"]').val(bulkSalePrice);
                    if (bulkStock !== '') $(this).find('input[name$="[stock]"]').val(bulkStock);
                });
                $('#bulk-price, #bulk-sale-price, #bulk-stock').val('');
                alert('Đã cập nhật hàng loạt thành công! 🎉');
            }
        });
        // ==========================================
        // VALIDATION TẠI CLIENT VÀ HIỂN THỊ LỖI
        // ==========================================
        $('#form-create-product').on('submit', function(e) {
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

            // Ảnh đại diện
            let thumb = $('input[name="thumbnail"]');
            clearError(thumb);
            if (thumb[0] && thumb[0].files.length === 0 && !thumb.closest('.group').attr('style')?.includes('background-image')) {
                showError(thumb.closest('.group'), 'Phải có ảnh đại diện mới cho đăng nhé!');
            }

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
                    
                    // Giá
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