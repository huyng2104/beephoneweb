@extends('admin.layouts.app')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<div class="bg-slate-50 text-slate-900 font-display min-h-screen">
    <div class="relative flex min-h-screen w-full flex-col">
        
        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" id="form-create-product">
            @csrf
            
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
                            <label class="block text-sm font-bold text-slate-700 mb-3">Tên sản phẩm <span class="text-red-500">*</span></label>
                            <input name="name" value="{{ old('name') }}" required 
                                   class="w-full text-xl font-bold border-slate-200 rounded-lg focus:border-primary focus:ring-primary py-3 px-4 bg-slate-50/50 placeholder-slate-400" 
                                   placeholder="Nhập tên sản phẩm..." type="text"/>
                        </div>

                        <div class="bg-white rounded-xl p-6 shadow-sm border border-slate-200">
                            <label class="block text-sm font-bold text-slate-700 mb-4">Mô tả sản phẩm</label>
                            <div class="border border-slate-200 rounded-lg overflow-hidden">
                                <textarea name="description" class="w-full border-none focus:ring-0 min-h-[250px] p-4 text-slate-800 leading-relaxed text-sm bg-white" placeholder="Bắt đầu viết mô tả chi tiết tại đây...">{{ old('description') }}</textarea>
                            </div>
                        </div>

                        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden" id="product-data-container">
                            <div class="bg-slate-50 p-4 border-b border-slate-200 flex items-center justify-between">
                                <div class="flex items-center gap-4">
                                    <span class="font-bold text-sm text-slate-700 uppercase tracking-wide">Dữ liệu sản phẩm —</span>
                                    <select name="type" id="product_type" class="text-sm border-slate-200 rounded-lg py-1.5 px-3 bg-white focus:ring-primary font-bold text-primary shadow-sm cursor-pointer transition-colors">
                                        <option value="simple" {{ old('type') == 'simple' ? 'selected' : '' }}>Sản phẩm đơn giản</option>
                                        <option value="variable" {{ old('type') == 'variable' ? 'selected' : '' }}>Sản phẩm biến thể</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="flex flex-col md:flex-row min-h-[500px]">
                                <div class="w-full md:w-56 border-r border-slate-200 flex-shrink-0 bg-slate-50/30">
                                    <nav class="flex flex-col h-full" id="product-data-tabs">
                                        <button type="button" data-target="tab-general" class="tab-btn active-tab flex items-center gap-3 px-5 py-4 text-sm font-semibold transition-colors border-b border-slate-100">
                                            <span class="material-symbols-outlined text-lg text-slate-500">settings</span> Chung
                                        </button>
                                        <button type="button" data-target="tab-inventory" class="tab-btn flex items-center gap-3 px-5 py-4 text-sm font-semibold text-slate-600 hover:bg-slate-100 transition-colors border-b border-slate-100">
                                            <span class="material-symbols-outlined text-lg text-slate-500">inventory_2</span> Kho hàng
                                        </button>
                                        <button type="button" data-target="tab-attributes" class="tab-btn flex items-center gap-3 px-5 py-4 text-sm font-semibold text-slate-600 hover:bg-slate-100 transition-colors border-b border-slate-100">
                                            <span class="material-symbols-outlined text-lg text-slate-500">list_alt</span> Thuộc tính
                                        </button>
                                        <button type="button" data-target="tab-variations" id="btn-tab-variations" class="tab-btn hidden flex items-center gap-3 px-5 py-4 text-sm font-semibold text-slate-600 hover:bg-slate-100 transition-colors border-b border-slate-100">
                                            <span class="material-symbols-outlined text-lg text-slate-500">layers</span> Các biến thể
                                        </button>
                                    </nav>
                                </div>
                                
                                <div class="flex-1 p-6 bg-white overflow-y-auto max-h-[700px]">
                                    
                                    <div id="tab-general" class="tab-content space-y-5">
                                        <div class="flex items-center gap-4">
                                            <label class="w-1/3 text-sm font-bold text-slate-700 text-right">Giá bán thường (₫)</label>
                                            <input type="number" name="price" value="{{ old('price') }}" class="flex-1 text-sm border-slate-200 rounded-lg focus:ring-primary focus:border-primary py-2.5 px-3 bg-slate-50" placeholder="VD: 25000000"/>
                                        </div>
                                        <div class="flex items-center gap-4">
                                            <label class="w-1/3 text-sm font-bold text-slate-700 text-right">Giá khuyến mãi (₫)</label>
                                            <input type="number" name="sale_price" value="{{ old('sale_price') }}" class="flex-1 text-sm border-slate-200 rounded-lg focus:ring-primary focus:border-primary py-2.5 px-3 bg-slate-50" placeholder="VD: 23900000 (Tùy chọn)"/>
                                        </div>
                                    </div>

                                    <div id="tab-inventory" class="tab-content hidden space-y-5">
                                        <div class="flex items-center gap-4">
                                            <label class="w-1/3 text-sm font-bold text-slate-700 text-right">Mã sản phẩm (SKU)</label>
                                            <input type="text" name="sku" value="{{ old('sku') }}" class="flex-1 text-sm border-slate-200 rounded-lg focus:ring-primary focus:border-primary py-2.5 px-3 bg-slate-50" placeholder="Mã duy nhất (VD: IPHONE-15-PRM)"/>
                                        </div>
                                        <div class="flex items-center gap-4">
                                            <label class="w-1/3 text-sm font-bold text-slate-700 text-right">Tồn kho</label>
                                            <input type="number" name="stock" value="{{ old('stock', 0) }}" class="w-32 text-sm border-slate-200 rounded-lg focus:ring-primary focus:border-primary py-2.5 px-3 bg-slate-50" placeholder="0"/>
                                        </div>
                                    </div>

                                    <div id="tab-attributes" class="tab-content hidden">
                                        <div class="flex items-center gap-3 bg-slate-50 p-3 rounded-lg border border-slate-200 mb-6">
                                            <select id="attribute-selector" class="flex-1 text-sm border-slate-200 rounded py-2 px-3 focus:ring-primary">
                                                <option value="">-- Thêm thuộc tính hiện có --</option>
                                                @foreach($attributes as $attr)
                                                    <option value="{{ $attr->id }}" data-name="{{ $attr->name }}">{{ $attr->name }}</option>
                                                @endforeach
                                            </select>
                                            <button type="button" id="btn-add-attribute" class="bg-slate-200 hover:bg-slate-300 text-slate-700 px-4 py-2 rounded text-sm font-bold transition-colors">Thêm</button>
                                        </div>

                                        <div class="space-y-4" id="attributes-wrapper"></div>
                                    </div>

                                   <div id="tab-variations" class="tab-content hidden">
                                        <div class="bg-blue-50 text-blue-700 p-3 rounded-lg text-sm mb-5 border border-blue-100">
                                            Trước khi bạn có thể thêm một biến thể, bạn cần thêm một số thuộc tính biến thể trên tab <strong>Thuộc tính</strong>, và nhớ tick chọn <strong>"Dùng cho nhiều biến thể"</strong> nhé.
                                        </div>
                                        
                                        <div class="flex items-center gap-3 bg-slate-50 p-3 rounded-lg border border-slate-200 mb-6">
                                            <select id="variation-action" class="flex-1 text-sm border-slate-200 rounded py-2 px-3 focus:ring-primary font-bold cursor-pointer">
                                                <option value="">-- Chọn hành động --</option>
                                                <option value="generate">Tạo biến thể từ tất cả thuộc tính</option>
                                            </select>
                                            <button type="button" id="btn-do-variation" class="bg-primary hover:brightness-105 text-slate-900 px-5 py-2 rounded text-sm font-bold transition-all shadow-sm">Đi</button>
                                        </div>

                                        <div id="variations-wrapper" class="space-y-4"></div>
                                    </div>

                                </div>
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
                                <div class="max-h-48 overflow-y-auto space-y-3 mb-5 pr-2 custom-scrollbar">
                                    @forelse($categories as $category)
                                        <label class="flex items-center gap-2.5 text-sm cursor-pointer group">
                                            <input name="category_ids[]" value="{{ $category->id }}" type="checkbox" 
                                                   class="rounded border-slate-300 text-primary focus:ring-primary"
                                                   {{ is_array(old('category_ids')) && in_array($category->id, old('category_ids')) ? 'checked' : '' }} />
                                            <span class="group-hover:text-primary transition-colors font-medium">{{ $category->name }}</span>
                                        </label>
                                    @empty
                                        <p class="text-xs text-slate-400 italic">Chưa có danh mục nào.</p>
                                    @endforelse
                                </div>
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
                                    <input type="file" name="thumbnail" accept="image/*" required class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                                    <span class="material-symbols-outlined text-4xl text-slate-300 group-hover:text-primary transition-colors">add_photo_alternate</span>
                                    <span class="text-xs font-bold text-slate-400 mt-2 uppercase">Chọn ảnh (Bắt buộc)</span>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                            <div class="p-4 border-b border-slate-100 bg-slate-50/50">
                                <h4 class="font-black text-sm uppercase tracking-wider text-slate-700">Album hình ảnh</h4>
                            </div>
                            <div class="p-5">
                                <div class="relative group aspect-[2/1] rounded-lg border-2 border-dashed border-slate-200 bg-slate-50 hover:border-primary transition-colors cursor-pointer flex flex-col items-center justify-center">
                                    <input type="file" name="images[]" multiple accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                                    <span class="material-symbols-outlined text-2xl text-slate-300 group-hover:text-primary transition-colors">collections</span>
                                    <span class="text-[10px] font-black uppercase mt-1 text-slate-400">Chọn nhiều ảnh</span>
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
    
    .active-tab {
        border-left: 3px solid #f4c025;
        background-color: rgba(244, 192, 37, 0.08);
        color: #000 !important;
    }
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const tabBtns = document.querySelectorAll('.tab-btn');
        const tabContents = document.querySelectorAll('.tab-content');

        function switchTab(targetId) {
            tabContents.forEach(content => content.classList.add('hidden'));
            tabBtns.forEach(btn => {
                btn.classList.remove('active-tab', 'text-black');
                btn.classList.add('text-slate-600');
            });

            document.getElementById(targetId).classList.remove('hidden');
            const activeBtn = document.querySelector(`[data-target="${targetId}"]`);
            if(activeBtn) {
                activeBtn.classList.add('active-tab', 'text-black');
                activeBtn.classList.remove('text-slate-600');
            }
        }

        tabBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                switchTab(this.getAttribute('data-target'));
            });
        });

        const productTypeSelect = document.getElementById('product_type');
        const tabGeneral = document.querySelector('[data-target="tab-general"]');
        const tabVariations = document.getElementById('btn-tab-variations');

        function toggleProductType() {
            let isVar = productTypeSelect.value === 'variable';
            if(isVar) {
                tabGeneral.classList.add('hidden');
                tabVariations.classList.remove('hidden');
                $('.var-checkbox-wrapper').css('display', 'flex'); 
                switchTab('tab-attributes');
            } else {
                tabGeneral.classList.remove('hidden');
                tabVariations.classList.add('hidden');
                $('.var-checkbox-wrapper').css('display', 'none');
                switchTab('tab-general');
            }
        }

        productTypeSelect.addEventListener('change', toggleProductType);
        toggleProductType(); 
    });

    $(document).ready(function() {
        const baseUrl = '/admin'; 

        $('#btn-add-attribute').click(function() {
            let select = $('#attribute-selector');
            let attrId = select.val();
            let attrName = select.find('option:selected').data('name');

            if (!attrId) {
                alert('Bro chưa chọn thuộc tính nào kìa!');
                return;
            }

            if ($(`#attr-block-${attrId}`).length > 0) {
                alert('Thuộc tính này đã được thêm rồi nhé!');
                return;
            }

            $.ajax({
                url: `${baseUrl}/attributes/${attrId}/get-values`, 
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        renderAttributeBlock(attrId, attrName, response.data);
                        select.val(''); 
                    } else {
                        alert('Lỗi lấy dữ liệu: ' + response.message);
                    }
                },
                error: function(err) {
                    alert('Lỗi kết nối tới Server. Bro bật F12 lên xem mục Console nhé!');
                    console.error(err);
                }
            });
        });

        function renderAttributeBlock(id, name, values) {
            let optionsHtml = '';
            values.forEach(val => {
                let valName = val.value || val.name || "Giá trị"; 
                optionsHtml += `<option value="${val.id}">${valName}</option>`;
            });

            let isVariable = $('#product_type').val() === 'variable';
            let displayVarCheckbox = isVariable ? 'flex' : 'none';

            let html = `
                <div class="border border-slate-200 rounded-lg overflow-hidden mb-4" id="attr-block-${id}">
                    <div class="bg-slate-50 p-3 flex justify-between items-center border-b border-slate-200 cursor-pointer">
                        <strong class="text-sm text-slate-700">${name}</strong>
                        <div class="flex items-center gap-3">
                            <button type="button" class="text-red-500 hover:underline text-xs font-bold btn-remove-attr" data-id="${id}">Xóa</button>
                            <span class="material-symbols-outlined text-slate-400 text-lg">expand_more</span>
                        </div>
                    </div>
                    <div class="p-4 flex gap-6 bg-white">
                        <div class="w-1/3 border-r border-slate-100 pr-4 space-y-3">
                            <strong class="text-sm text-slate-800">Tên: <br><span class="text-slate-500 font-normal">${name}</span></strong>
                            
                            <input type="hidden" name="attributes[${id}][id]" value="${id}">
                            
                            <label class="flex items-start gap-2 text-sm cursor-pointer">
                                <input type="checkbox" name="attributes[${id}][visible]" value="1" checked class="rounded border-slate-300 text-primary mt-0.5">
                                <span class="text-slate-600">Hiển thị trên trang SP</span>
                            </label>
                            <label class="items-start gap-2 text-sm cursor-pointer var-checkbox-wrapper" style="display: ${displayVarCheckbox};">
                                <input type="checkbox" name="attributes[${id}][variation]" value="1" class="rounded border-slate-300 text-primary mt-0.5">
                                <span class="text-slate-600 font-bold">Dùng cho nhiều biến thể</span>
                            </label>
                        </div>
                        <div class="w-2/3 space-y-2">
                            <label class="text-sm font-bold text-slate-700">Giá trị (Terms):</label>
                            <select multiple name="attributes[${id}][values][]" class="w-full text-sm border-slate-200 rounded-lg focus:ring-primary select2-dynamic">
                                ${optionsHtml}
                            </select>
                            <div class="flex gap-2 mt-2">
                                <button type="button" class="btn-select-all bg-slate-100 border border-slate-300 px-3 py-1 text-xs rounded hover:bg-slate-200 font-medium text-slate-700">Chọn tất cả</button>
                                <button type="button" class="btn-deselect-all bg-slate-100 border border-slate-300 px-3 py-1 text-xs rounded hover:bg-slate-200 font-medium text-slate-700">Không chọn</button>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            $('#attributes-wrapper').append(html);
            
            $(`#attr-block-${id} .select2-dynamic`).select2({
                placeholder: "Click để chọn giá trị...",
                width: '100%'
            });
        }

        $(document).on('click', '.btn-remove-attr', function() {
            let id = $(this).data('id');
            $(`#attr-block-${id}`).remove();
        });

        $(document).on('click', '.btn-select-all', function() {
            let select = $(this).closest('.w-2\\/3').find('select');
            select.find('option').prop('selected', true);
            select.trigger('change');
        });

        $(document).on('click', '.btn-deselect-all', function() {
            let select = $(this).closest('.w-2\\/3').find('select');
            select.find('option').prop('selected', false);
            select.trigger('change');
        });
    });

    // ==========================================
        // HIỂN THỊ PREVIEW ẢNH KHI CHỌN FILE
        // ==========================================

        // 1. Preview Ảnh đại diện (Thumbnail)
        const thumbnailInput = document.querySelector('input[name="thumbnail"]');
        if (thumbnailInput) {
            const thumbnailContainer = thumbnailInput.closest('.group');
            
            thumbnailInput.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        // Phủ ảnh lên background
                        thumbnailContainer.style.backgroundImage = `url('${e.target.result}')`;
                        thumbnailContainer.style.backgroundSize = 'cover';
                        thumbnailContainer.style.backgroundPosition = 'center';
                        
                        // Làm mờ cái icon và dòng chữ đi cho đỡ rối
                        thumbnailContainer.querySelector('.material-symbols-outlined').style.opacity = '0';
                        thumbnailContainer.querySelector('.text-xs').style.opacity = '0';
                    }
                    reader.readAsDataURL(this.files[0]);
                }
            });
        }

        // 2. Hiển thị số lượng Ảnh Album (Gallery)
        const galleryInput = document.querySelector('input[name="images[]"]');
        if (galleryInput) {
            const galleryText = galleryInput.parentElement.querySelector('span.text-\\[10px\\]');
            
            galleryInput.addEventListener('change', function() {
                if (this.files && this.files.length > 0) {
                    // Cập nhật chữ hiển thị số lượng ảnh đã chọn
                    galleryText.textContent = `Đã chọn ${this.files.length} ảnh`;
                    galleryText.classList.remove('text-slate-400');
                    galleryText.classList.add('text-primary', 'font-black', 'text-sm');
                    
                    // Đổi màu cái viền cho nó có cảm giác "đã chọn"
                    this.parentElement.classList.add('border-primary');
                } else {
                    galleryText.textContent = 'Chọn nhiều ảnh';
                }
            });
        }

        // ==========================================
        // LOGIC TẠO BIẾN THỂ (TRÙM CUỐI)
        // ==========================================
        $('#btn-do-variation').click(function() {
            let action = $('#variation-action').val();
            if (action === 'generate') {
                generateVariations();
            } else {
                alert('Bro chọn "Tạo biến thể từ tất cả thuộc tính" rồi hẵng bấm Đi nhé!');
            }
        });

        function generateVariations() {
            let attrGroups = [];

            // 1. Quét tìm tất cả các thuộc tính đang được tick "Dùng cho nhiều biến thể"
            $('.var-checkbox-wrapper input[type="checkbox"]:checked').each(function() {
                let block = $(this).closest('[id^="attr-block-"]');
                let attrId = block.find('input[type="hidden"]').val(); // ID của Màu sắc, Kích thước
                let attrName = block.find('strong.text-slate-700').first().text();
                
                // Lấy các giá trị (Đỏ, Xanh) đang chọn trong ô Select2
                let selectedOptions = block.find('select').select2('data');
                
                if (selectedOptions.length > 0) {
                    let values = selectedOptions.map(opt => {
                        return { attrId: attrId, attrName: attrName, valId: opt.id, valName: opt.text };
                    });
                    attrGroups.push(values);
                }
            });

            if (attrGroups.length === 0) {
                alert('Bro chưa chọn thuộc tính nào dùng cho biến thể, hoặc chưa chọn giá trị (Đỏ, Xanh) nào cả!');
                return;
            }

            // 2. Thuật toán nhân chéo (Cartesian Product) để đẻ ra tổ hợp Đỏ-256GB, Xanh-512GB...
            let combinations = cartesianProduct(attrGroups);
            
            // 3. Vẽ ra màn hình
            let wrapper = $('#variations-wrapper');
            wrapper.empty(); // Xóa sạch cái cũ làm lại từ đầu

            combinations.forEach((combo, index) => {
                let title = combo.map(c => c.valName).join(' - ');
                
                // Tạo các input ẩn để gửi ID thuộc tính lên Laravel
                let hiddenInputs = '';
                combo.forEach(c => {
                    hiddenInputs += `<input type="hidden" name="variations[${index}][attributes][${c.attrId}]" value="${c.valId}">`;
                });

                let html = `
                <div class="border border-slate-200 rounded-lg overflow-hidden bg-white variation-item shadow-sm">
                    <div class="bg-slate-50 p-3 flex justify-between items-center border-b border-slate-200 cursor-pointer" onclick="$(this).next().slideToggle()">
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined text-slate-400">drag_indicator</span>
                            <strong class="text-sm text-slate-800 text-primary">#${index + 1} — ${title}</strong>
                            ${hiddenInputs}
                        </div>
                        <div class="flex items-center gap-4">
                            <button type="button" class="text-red-500 hover:underline text-xs font-bold" onclick="$(this).closest('.variation-item').remove(); event.stopPropagation();">Xóa</button>
                            <span class="material-symbols-outlined text-slate-400 text-lg">expand_more</span>
                        </div>
                    </div>
                    <div class="p-5 flex gap-6 bg-white" style="display:none;">
                        <div class="flex-1 grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-600 mb-1">Mã SP (SKU)</label>
                                <input type="text" name="variations[${index}][sku]" placeholder="VD: IP15-DO-256" class="w-full text-sm border-slate-200 rounded focus:ring-primary py-1.5 px-2">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-600 mb-1">Giá thường (₫) <span class="text-red-500">*</span></label>
                                <input type="number" name="variations[${index}][price]" required class="w-full text-sm border-slate-200 rounded focus:ring-primary py-1.5 px-2">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-600 mb-1">Giá KM (₫)</label>
                                <input type="number" name="variations[${index}][sale_price]" class="w-full text-sm border-slate-200 rounded focus:ring-primary py-1.5 px-2">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-600 mb-1">Tồn kho</label>
                                <input type="number" name="variations[${index}][stock]" value="0" class="w-full text-sm border-slate-200 rounded focus:ring-primary py-1.5 px-2">
                            </div>
                        </div>
                    </div>
                </div>
                `;
                wrapper.append(html);
            });
            
            // Tự động mở cái khung đầu tiên cho đẹp
            wrapper.find('.p-5').first().show();
        }

        // Hàm toán học ghép tổ hợp
        function cartesianProduct(arr) {
            return arr.reduce(function(a,b){
                return a.map(function(x){
                    return b.map(function(y){
                        return x.concat([y]);
                    })
                }).reduce(function(a,b){ return a.concat(b) },[])
            }, [[]]);
        }
</script>
@endsection