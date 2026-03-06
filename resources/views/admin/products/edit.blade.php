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
                            <label class="block text-sm font-bold text-slate-700 mb-3">Tên sản phẩm <span class="text-red-500">*</span></label>
                            <input name="name" value="{{ old('name', $product->name) }}" required 
                                   class="w-full text-xl font-bold border-slate-200 rounded-lg focus:border-primary focus:ring-primary py-3 px-4 bg-slate-50/50" type="text"/>
                        </div>

                        <div class="bg-white rounded-xl p-6 shadow-sm border border-slate-200">
                            <label class="block text-sm font-bold text-slate-700 mb-4">Mô tả sản phẩm</label>
                            <textarea name="description" class="w-full border border-slate-200 rounded-lg min-h-[200px] p-4 text-slate-800 text-sm bg-slate-50 focus:ring-primary">{{ old('description', $product->description) }}</textarea>
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
                                            <label class="w-1/3 text-sm font-bold text-slate-700 text-right">Mã sản phẩm (SKU)</label>
                                            <input type="text" name="sku" value="{{ old('sku', $product->sku) }}" class="flex-1 text-sm border-slate-200 rounded-lg py-2.5 px-3 bg-slate-50"/>
                                        </div>
                                        <div class="flex items-center gap-4">
                                            <label class="w-1/3 text-sm font-bold text-slate-700 text-right">Tồn kho</label>
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
                                            Lưu ý: Nếu bro thay đổi thuộc tính, hãy bấm nút "Tạo lại biến thể" để cập nhật danh sách mới. Các dữ liệu biến thể cũ sẽ bị thay thế.
                                        </div>
                                        <div class="flex items-center gap-3 bg-slate-50 p-3 rounded-lg border border-slate-200 mb-6">
                                            <select id="variation-action" class="flex-1 text-sm border-slate-200 rounded py-2 px-3 font-bold cursor-pointer">
                                                <option value="">-- Hành động biến thể --</option>
                                                <option value="generate">Xóa sạch & Tạo lại tất cả tổ hợp</option>
                                            </select>
                                            <button type="button" id="btn-do-variation" class="bg-primary text-slate-900 px-5 py-2 rounded text-sm font-bold shadow-sm">Đi</button>
                                        </div>
                                        <div id="variations-wrapper" class="space-y-3">
                                            @foreach($product->variants as $index => $variant)
                                                <div class="border border-slate-200 rounded-lg overflow-hidden bg-white variation-item shadow-sm">
                                                    <div class="bg-slate-50 p-3 flex justify-between items-center border-b border-slate-200 cursor-pointer" onclick="$(this).next().slideToggle()">
                                                        <div class="flex items-center gap-3">
                                                            <span class="material-symbols-outlined text-slate-400">drag_indicator</span>
                                                            <strong class="text-xs text-primary uppercase">
                                                                #{{ $index + 1 }} — 
                                                                @foreach($variant->attributeValues as $val)
                                                                    {{ $val->value }}{{ !$loop->last ? ' - ' : '' }}
                                                                @endforeach
                                                            </strong>
                                                            {{-- Lưu lại các ID thuộc tính để update --}}
                                                            @foreach($variant->attributeValues as $val)
                                                                <input type="hidden" name="variations[{{ $index }}][attributes][{{ $val->attribute_id }}]" value="{{ $val->id }}">
                                                            @endforeach
                                                        </div>
                                                        <div class="flex items-center gap-4">
                                                            <button type="button" class="text-red-500 text-[10px] font-bold uppercase" onclick="$(this).closest('.variation-item').remove()">Xóa</button>
                                                            <span class="material-symbols-outlined text-slate-400 text-lg">expand_more</span>
                                                        </div>
                                                    </div>
                                                    <div class="p-4 grid grid-cols-2 md:grid-cols-4 gap-4 bg-white" style="display:none;">
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
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="lg:col-span-4 space-y-6">
                        
                        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                            <div class="p-4 border-b border-slate-100 bg-slate-50/50">
                                <h4 class="font-black text-sm uppercase text-slate-700">Trạng thái & Thương hiệu</h4>
                            </div>
                            <div class="p-5 space-y-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-500 mb-2 uppercase">Trạng thái hiển thị</label>
                                    <select name="status" class="w-full text-sm border-slate-200 rounded py-2 bg-slate-50">
                                        <option value="active" {{ old('status', $product->status) == 'active' ? 'selected' : '' }}>Hiển thị công khai</option>
                                        <option value="inactive" {{ old('status', $product->status) == 'inactive' ? 'selected' : '' }}>Lưu bản nháp</option>
                                    </select>
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
                                                {{-- Kiểm tra nếu ID danh mục nằm trong danh sách danh mục của sản phẩm thì tích chọn --}}
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
                                <div>
                                    <label class="block text-xs font-bold text-slate-500 mb-2 uppercase">Thương hiệu</label>
                                    <select name="brand_id" class="w-full text-sm border-slate-200 rounded py-2 bg-slate-50">
                                        <option value="">-- Không có --</option>
                                        @foreach($brands as $brand)
                                            <option value="{{ $brand->id }}" {{ old('brand_id', $product->brand_id) == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
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

                    </div>
                </div>
            </main>
        </form>
    </div>
</div>

<style>
    .active-tab { border-left: 3px solid #f4c025; background-color: rgba(244, 192, 37, 0.08); color: #000 !important; }
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // --- 1. Tabs Logic ---
        const tabBtns = document.querySelectorAll('.tab-btn');
        const tabContents = document.querySelectorAll('.tab-content');

        function switchTab(targetId) {
            tabContents.forEach(content => content.classList.add('hidden'));
            tabBtns.forEach(btn => btn.classList.remove('active-tab', 'text-black'));
            
            document.getElementById(targetId).classList.remove('hidden');
            document.querySelector(`[data-target="${targetId}"]`).classList.add('active-tab', 'text-black');
        }

        tabBtns.forEach(btn => btn.addEventListener('click', () => switchTab(btn.getAttribute('data-target'))));

        // --- 2. Product Type Toggle ---
        const productType = document.getElementById('product_type');
        const tabVarBtn = document.getElementById('btn-tab-variations');

        productType.addEventListener('change', function() {
            if(this.value === 'variable') {
                tabVarBtn.classList.remove('hidden');
                $('.var-checkbox-wrapper').show();
            } else {
                tabVarBtn.classList.add('hidden');
                $('.var-checkbox-wrapper').hide();
                switchTab('tab-general');
            }
        });

        // --- 3. Preview Image ---
        $('#input-thumbnail').change(function() {
            if (this.files && this.files[0]) {
                let reader = new FileReader();
                reader.onload = (e) => $('#preview-thumbnail').attr('src', e.target.result);
                reader.readAsDataURL(this.files[0]);
            }
        });
    });

    $(document).ready(function() {
        const baseUrl = '/admin';

        // --- 4. Logic Thêm Thuộc tính ---
        $('#btn-add-attribute').click(function() {
            let select = $('#attribute-selector');
            let id = select.val();
            let name = select.find('option:selected').data('name');

            if (!id || $(`#attr-block-${id}`).length > 0) return;

            $.get(`${baseUrl}/attributes/${id}/get-values`, function(res) {
                if (res.success) renderAttr(id, name, res.data);
            });
        });

        function renderAttr(id, name, values) {
            let options = values.map(v => `<option value="${v.id}">${v.value || v.name}</option>`).join('');
            let isVar = $('#product_type').val() === 'variable' ? 'flex' : 'none';
            
            let html = `
                <div class="border border-slate-200 rounded-lg p-4 bg-white shadow-sm" id="attr-block-${id}">
                    <div class="flex justify-between mb-3">
                        <strong class="text-sm text-slate-700">${name}</strong>
                        <button type="button" class="text-red-500 text-[10px] font-bold uppercase" onclick="$(this).parent().parent().remove()">Xóa</button>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Giá trị</label>
                            <select multiple name="attributes[${id}][values][]" class="w-full select2-edit">${options}</select>
                            <input type="hidden" name="attributes[${id}][id]" value="${id}">
                        </div>
                        <div class="var-checkbox-wrapper items-center gap-2 pt-5" style="display: ${isVar}">
                            <input type="checkbox" name="attributes[${id}][variation]" value="1" class="rounded text-primary">
                            <span class="text-xs font-bold text-slate-600">Dùng cho biến thể</span>
                        </div>
                    </div>
                </div>`;
            $('#attributes-wrapper').append(html);
            $('.select2-edit').select2({ width: '100%', placeholder: 'Chọn giá trị...' });
        }

        // --- 5. Logic Tạo Biến thể (Tổ hợp) ---
        $('#btn-do-variation').click(function() {
            if ($('#variation-action').val() !== 'generate') return;
            
            let attrGroups = [];
            $('.var-checkbox-wrapper input:checked').each(function() {
                let block = $(this).closest('[id^="attr-block-"]');
                let vals = block.find('select').select2('data');
                if (vals.length) {
                    attrGroups.push(vals.map(v => ({ attrId: block.find('input[type="hidden"]').val(), valId: v.id, valName: v.text })));
                }
            });

            if (!attrGroups.length) return alert('Tick chọn "Dùng cho biến thể" ở tab Thuộc tính trước nhé!');

            let combos = cartesian(attrGroups);
            let wrapper = $('#variations-wrapper').empty();

            combos.forEach((combo, i) => {
                let title = combo.map(c => c.valName).join(' - ');
                let hiddens = combo.map(c => `<input type="hidden" name="variations[${i}][attributes][${c.attrId}]" value="${c.valId}">`).join('');
                
                wrapper.append(`
                    <div class="border border-slate-200 rounded-lg bg-white variation-item">
                        <div class="bg-slate-50 p-2 flex justify-between items-center cursor-pointer" onclick="$(this).next().slideToggle()">
                            <span class="text-xs font-bold text-primary">#${i+1} — ${title} ${hiddens}</span>
                            <button type="button" class="text-red-500 text-[10px]" onclick="$(this).closest('.variation-item').remove()">Xóa</button>
                        </div>
                        <div class="p-3 grid grid-cols-2 md:grid-cols-4 gap-3" style="display:none">
                            <input type="text" name="variations[${i}][sku]" placeholder="SKU" class="text-xs border-slate-200 rounded p-1">
                            <input type="number" name="variations[${i}][price]" placeholder="Giá" class="text-xs border-slate-200 rounded p-1">
                            <input type="number" name="variations[${i}][sale_price]" placeholder="Giá KM" class="text-xs border-slate-200 rounded p-1">
                            <input type="number" name="variations[${i}][stock]" placeholder="Kho" value="0" class="text-xs border-slate-200 rounded p-1">
                        </div>
                    </div>`);
            });
        });

        function cartesian(a) { return a.reduce((a, b) => a.flatMap(d => b.map(e => [d, e].flat()))); }
    });
</script>
@endsection