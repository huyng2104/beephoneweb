@extends('client.layouts.app')

@section('title', 'Bee Phone - Giỏ hàng của bạn')

@section('content')
<style>
    .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #f4c025; border-radius: 10px; }
    
    .attr-btn-modal.active {
        border-color: #f4c025;
        background-color: rgba(244, 192, 37, 0.1);
        box-shadow: 0 0 0 2px #f4c025;
    }
    .attr-btn-modal.disabled {
        opacity: 0.4;
        filter: grayscale(100%);
        pointer-events: none;
        cursor: not-allowed;
    }
</style>

<main class="pt-10 pb-20 px-6 md:px-12 max-w-screen-2xl mx-auto min-h-screen">
    <div class="mb-8">
        <h1 class="text-3xl font-bold tracking-tight text-[#181611] dark:text-white">Giỏ hàng của bạn</h1>
    </div>

    <div class="flex flex-col lg:flex-row gap-8">
        
        <div class="flex-grow">
            
            @if($cartItems->count() > 0)
                {{-- NÚT CHỌN TẤT CẢ --}}
                <div class="bg-white dark:bg-white/5 p-4 rounded-xl mb-4 flex items-center gap-4 shadow-sm border border-gray-100 dark:border-white/10">
                    <input type="checkbox" id="check-all" checked class="w-5 h-5 rounded border-gray-300 text-primary focus:ring-primary cursor-pointer"/>
                    <label for="check-all" class="font-bold text-gray-700 dark:text-gray-300 cursor-pointer select-none">Chọn tất cả ({{ $cartItems->count() }} sản phẩm)</label>
                </div>

                {{-- FORM GỬI CÁC ITEM ĐƯỢC CHỌN ĐI THANH TOÁN --}}
                <form id="checkout-form" action="{{ route('client.cart.checkout_select') }}" method="POST">
                    @csrf
                    <div class="space-y-4">
                        @foreach($cartItems as $item)
                            @php
                                $price = $item->product->sale_price > 0 ? $item->product->sale_price : $item->product->price;
                                $image = $item->product->thumbnail;
                                $variantName = '';
                                $stock = $item->product->stock;

                                if ($item->variant) {
                                    $price = $item->variant->sale_price > 0 ? $item->variant->sale_price : $item->variant->price;
                                    $image = $item->variant->thumbnail ?? $item->product->thumbnail;
                                    $variantName = $item->variant->attributeValues->pluck('value')->implode(' / ');
                                    $stock = $item->variant->stock;
                                }

                                $imageUrl = Str::startsWith($image, ['http://', 'https://']) ? $image : asset('storage/' . $image);
                            @endphp

                            <div class="cart-item bg-white dark:bg-white/5 p-6 rounded-xl flex flex-col md:flex-row items-center gap-6 shadow-sm border border-gray-100 dark:border-white/10 transition-transform hover:scale-[1.01]" 
                                 data-id="{{ $item->id }}" data-price="{{ $price }}" data-stock="{{ $stock }}">
                                
                                <div class="flex items-center gap-4 w-full md:w-auto">
                                    {{-- CHECKBOX CỦA TỪNG ITEM --}}
                                    <input type="checkbox" name="selected_items[]" value="{{ $item->id }}" checked class="item-checkbox w-5 h-5 rounded border-gray-300 text-primary focus:ring-primary cursor-pointer"/>
                                    
                                    <a href="{{ route('client.product.detail', $item->product->slug ?? $item->product->id) }}" class="w-24 h-24 bg-gray-50 dark:bg-black/20 rounded-lg overflow-hidden flex-shrink-0 p-2">
                                        <img class="w-full h-full object-contain mix-blend-multiply dark:mix-blend-normal" src="{{ $imageUrl }}" alt="{{ $item->product->name }}"/>
                                    </a>
                                </div>
                                
                                <div class="flex-grow grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <a href="{{ route('client.product.detail', $item->product->slug ?? $item->product->id) }}">
                                            <h3 class="text-lg font-bold leading-tight text-[#181611] dark:text-white hover:text-primary transition-colors line-clamp-2">{{ $item->product->name }}</h3>
                                        </a>
                                        @if($variantName)
                                            <div class="flex items-center gap-2 mt-1">
                                                <p class="text-gray-500 dark:text-gray-400 text-xs uppercase tracking-wider font-bold">{{ $variantName }}</p>
                                                @if($item->product->type == 'variable')
                                                    <button type="button" class="btn-change-variant text-primary text-[10px] font-black uppercase tracking-tighter hover:underline bg-primary/10 px-2 py-0.5 rounded transition-transform active:scale-95" 
                                                            data-product-id="{{ $item->product_id }}" 
                                                            data-item-id="{{ $item->id }}"
                                                            data-current-variant-id="{{ $item->product_variant_id }}">
                                                        Thay đổi
                                                    </button>
                                                @endif
                                            </div>
                                        @endif
                                        <span class="inline-flex items-center mt-2 px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-widest bg-emerald-100 text-emerald-700">Còn hàng ({{ $stock }})</span>
                                    </div>
                                    <div class="flex flex-col md:items-end justify-center">
                                        <span class="text-xl font-bold text-red-500">{{ number_format($price, 0, ',', '.') }}₫</span>
                                        @php $oldPrice = $item->variant ? $item->variant->price : $item->product->price; @endphp
                                        @if($oldPrice > $price)
                                            <span class="text-sm text-gray-400 line-through">{{ number_format($oldPrice, 0, ',', '.') }}₫</span>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="flex items-center gap-6 w-full md:w-auto justify-between md:justify-end">
                                    <div class="flex items-center border border-gray-200 dark:border-white/10 rounded-lg bg-gray-50 dark:bg-black/20">
                                        <button type="button" class="btn-qty-minus p-2 hover:text-primary transition-colors" data-id="{{ $item->id }}">
                                            <span class="material-symbols-outlined text-sm">remove</span>
                                        </button>
                                        <input class="qty-input w-12 text-center bg-transparent border-none focus:ring-0 font-bold text-[#181611] dark:text-white p-0" type="text" value="{{ $item->quantity }}" readonly/>
                                        <button type="button" class="btn-qty-plus p-2 hover:text-primary transition-colors" data-id="{{ $item->id }}">
                                            <span class="material-symbols-outlined text-sm">add</span>
                                        </button>
                                    </div>
                                    <button type="button" class="btn-remove text-gray-400 hover:text-red-500 transition-colors p-2" data-id="{{ $item->id }}" title="Xóa">
                                        <span class="material-symbols-outlined">delete</span>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </form>
            @else
                <div class="flex flex-col items-center justify-center py-20 bg-white dark:bg-white/5 rounded-2xl border border-dashed border-gray-200 dark:border-white/10">
                    <div class="w-24 h-24 bg-gray-50 dark:bg-white/5 rounded-full flex items-center justify-center mb-6">
                        <span class="material-symbols-outlined text-5xl text-gray-300 dark:text-gray-600">shopping_cart</span>
                    </div>
                    <h2 class="text-2xl font-bold text-[#181611] dark:text-white mb-2">Giỏ hàng của bạn đang trống</h2>
                    <p class="text-gray-500 dark:text-gray-400 mb-8">Hãy tìm cho mình những sản phẩm tuyệt vời nhé!</p>
                    <a href="{{ route('client.products.index') }}" class="bg-primary text-black font-bold px-8 py-3 rounded-xl hover:scale-105 transition-transform shadow-md">
                        Tiếp tục mua sắm
                    </a>
                </div>
            @endif
        </div>

        @if($cartItems->count() > 0)
        <div class="lg:w-96 flex-shrink-0">
            <div class="bg-white dark:bg-white/5 p-8 rounded-2xl shadow-sm sticky top-24 border border-gray-100 dark:border-white/10">
                <h2 class="text-xl font-bold mb-6 tracking-tight text-[#181611] dark:text-white">Tóm tắt đơn hàng</h2>
                
                <div class="space-y-4 mb-8">
                    <div class="flex justify-between text-gray-500 dark:text-gray-400">
                        <span>Tạm tính (<span id="summary-count">{{ $cartItems->sum('quantity') }}</span> sản phẩm)</span>
                    </div>
                    <div class="flex justify-between items-end border-t border-gray-100 dark:border-white/10 pt-4">
                        <span class="text-lg font-bold text-[#181611] dark:text-white">Tổng tiền</span>
                        <div class="text-right">
                            <p class="text-3xl font-bold text-red-500" id="summary-total">{{ number_format($totalPrice, 0, ',', '.') }}₫</p>
                            <p class="text-[10px] text-gray-500 dark:text-gray-400 uppercase font-bold tracking-widest mt-1">(Đã bao gồm VAT)</p>
                        </div>
                    </div>
                </div>

                <button type="button" onclick="submitCheckout()" class="w-full bg-primary text-black font-bold py-4 rounded-xl shadow-lg shadow-primary/20 hover:opacity-90 transition-all flex items-center justify-center gap-3 active:scale-[0.98]">
                    <span>Thanh toán ngay</span>
                    <span class="material-symbols-outlined">arrow_forward</span>
                </button>
            </div>
        </div>
        @endif
        
    </div>
</main>

{{-- MODAL CHỌN BIẾN THỂ --}}
<div id="variant-modal" class="fixed inset-0 z-[9999] hidden flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
    <div class="bg-white dark:bg-[#111111] w-full max-w-lg rounded-2xl shadow-2xl overflow-hidden border border-gray-100 dark:border-white/10">
        <div class="p-6 border-b border-gray-100 dark:border-white/10 flex items-center justify-between">
            <h3 class="text-xl font-bold text-[#181611] dark:text-white">Thay đổi phiên bản</h3>
            <button type="button" class="close-variant-modal text-gray-400 hover:text-red-500 transition-colors">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <div class="p-6 max-h-[70vh] overflow-y-auto custom-scrollbar">
            <div id="modal-product-info" class="flex gap-4 mb-6 pb-6 border-b border-dashed border-gray-200 dark:border-white/10">
                <div class="w-20 h-20 bg-gray-50 dark:bg-white/5 rounded-lg p-2 flex items-center justify-center">
                    <img id="modal-img" src="" class="max-w-full max-h-full object-contain">
                </div>
                <div class="flex-grow">
                    <h4 id="modal-product-name" class="font-bold text-base text-[#181611] dark:text-white line-clamp-2"></h4>
                    <p id="modal-price-display" class="text-red-500 font-black text-xl mt-1"></p>
                    <p id="modal-stock-display" class="text-xs text-gray-500 mt-1"></p>
                </div>
            </div>
            
            <div id="modal-attributes" class="space-y-6">
                {{-- Sẽ được render bằng JS --}}
                <div class="flex items-center justify-center py-10">
                    <span class="text-gray-500 font-bold animate-pulse">Đang tải...</span>
                </div>
            </div>
        </div>
        <div class="p-6 border-t border-gray-100 dark:border-white/10 flex gap-3">
            <button type="button" class="close-variant-modal flex-1 bg-gray-100 dark:bg-white/5 text-gray-500 font-bold py-3 rounded-xl hover:bg-gray-200 transition-colors transition-all active:scale-95">Hủy</button>
            <button type="button" id="confirm-variant-change" class="flex-[2] bg-primary text-black font-bold py-3 rounded-xl shadow-lg shadow-primary/20 hover:opacity-90 active:scale-95 transition-all disabled:opacity-50 disabled:cursor-not-allowed">Xác nhận</button>
        </div>
    </div>
</div>

<script> 
document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = '{{ csrf_token() }}';

    function formatMoney(num) {
        return num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.') + '₫';
    }

    // TÍNH LẠI TIỀN DỰA TRÊN NHỮNG MÓN ĐƯỢC TÍCH
    function updateCartTotals() {
        let totalQty = 0;
        let totalPrice = 0;
        let allChecked = true;

        document.querySelectorAll('.cart-item').forEach(item => {
            const checkbox = item.querySelector('.item-checkbox');
            if (checkbox && checkbox.checked) {
                const qtyInput = item.querySelector('.qty-input');
                const qty = qtyInput ? parseInt(qtyInput.value) : 0;
                const price = parseFloat(item.getAttribute('data-price')) || 0;
                totalQty += qty;
                totalPrice += (qty * price);
            } else {
                allChecked = false;
            }
        });

        const checkAllEl = document.getElementById('check-all');
        if(checkAllEl) checkAllEl.checked = allChecked;

        const totalEl = document.getElementById('summary-total');
        const countEl = document.getElementById('summary-count');
        if(totalEl) totalEl.innerText = formatMoney(totalPrice);
        if(countEl) countEl.innerText = totalQty;
    }

    // Sự kiện Checkbox Từng món
    document.querySelectorAll('.item-checkbox').forEach(cb => {
        cb.addEventListener('change', updateCartTotals);
    });

    // Sự kiện Checkbox "Chọn tất cả"
    const checkAllBox = document.getElementById('check-all');
    if(checkAllBox) {
        checkAllBox.addEventListener('change', function() {
            const isChecked = this.checked;
            document.querySelectorAll('.item-checkbox').forEach(cb => {
                cb.checked = isChecked;
            });
            updateCartTotals();
        });
    }

    function updateQuantityAjax(itemId, newQty) {
        fetch('{{ route("client.cart.update") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ item_id: itemId, quantity: newQty })
        }).then(res => res.json()).then(data => {
            if(!data.success) { alert(data.message); window.location.reload(); }
        });
    }

    // Tăng / Giảm SL
    document.querySelectorAll('.btn-qty-plus').forEach(btn => {
        btn.addEventListener('click', function() {
            const itemRow = this.closest('.cart-item');
            const maxStock = parseInt(itemRow.getAttribute('data-stock')); 
            const input = this.previousElementSibling;
            let qty = parseInt(input.value);

            if (qty < maxStock) { 
                qty += 1;
                input.value = qty;
                updateCartTotals();
                updateQuantityAjax(this.getAttribute('data-id'), qty);
            } else {
                alert('Tối đa trong kho (' + maxStock + ')!');
            }
        });
    });

    document.querySelectorAll('.btn-qty-minus').forEach(btn => {
        btn.addEventListener('click', function() {
            const input = this.nextElementSibling;
            let qty = parseInt(input.value);
            if (qty > 1) {
                qty -= 1;
                input.value = qty;
                updateCartTotals();
                updateQuantityAjax(this.getAttribute('data-id'), qty);
            }
        });
    });

    document.querySelectorAll('.btn-remove').forEach(btn => {
        btn.addEventListener('click', function() {
            if(confirm('Bạn có chắc muốn xóa sản phẩm này?')) {
                const itemRow = this.closest('.cart-item');
                fetch('{{ route("client.cart.remove") }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify({ item_id: this.getAttribute('data-id') })
                }).then(res => res.json()).then(data => {
                    if(data.success) { itemRow.remove(); updateCartTotals(); }
                });
            }
        });
    });

    // HÀM SUBMIT FORM THANH TOÁN
    window.submitCheckout = function() {
        const checkedItems = document.querySelectorAll('.item-checkbox:checked');
        if(checkedItems.length === 0) {
            alert('Vui lòng chọn ít nhất 1 sản phẩm để thanh toán!');
            return;
        }
        document.getElementById('checkout-form').submit();
    }

    // ==========================================
    // LOGIC THAY ĐỔI BIẾN THỂ (VARIANT CHANGE)
    // ==========================================
    const variantModal = document.getElementById('variant-modal');
    const modalAttributes = document.getElementById('modal-attributes');
    const modalImg = document.getElementById('modal-img');
    const modalName = document.getElementById('modal-product-name');
    const modalPrice = document.getElementById('modal-price-display');
    const modalStock = document.getElementById('modal-stock-display');
    const confirmBtn = document.getElementById('confirm-variant-change');
    
    let currentProductId = null;
    let currentItemId = null;
    let variantsData = [];
    let selectedAttrs = {};

    function openModal() { variantModal.classList.remove('hidden'); document.body.style.overflow = 'hidden'; }
    function closeModal() { variantModal.classList.add('hidden'); document.body.style.overflow = ''; }

    document.querySelectorAll('.close-variant-modal').forEach(b => b.addEventListener('click', closeModal));

    document.querySelectorAll('.btn-change-variant').forEach(btn => {
        btn.addEventListener('click', function() {
            currentProductId = this.getAttribute('data-product-id');
            currentItemId = this.getAttribute('data-item-id');
            const currentVariantId = this.getAttribute('data-current-variant-id');
            
            openModal();
            modalAttributes.innerHTML = '<div class="flex items-center justify-center py-10"><span class="text-gray-500 font-bold animate-pulse">Đang tải...</span></div>';
            confirmBtn.disabled = true;

            fetch(`{{ route('client.cart.get_variants') }}?product_id=${currentProductId}`)
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        variantsData = data.variants;
                        modalName.innerText = data.product_name;
                        
                        // Render Attributes
                        renderModalAttributes(data.attributes, currentVariantId);
                    } else {
                        alert(data.message);
                        closeModal();
                    }
                });
        });
    });

    function renderModalAttributes(attributes, currentVariantId) {
        let html = '';
        selectedAttrs = {};
        
        // Tìm biến thể hiện tại để pre-select
        const currentVar = variantsData.find(v => v.id == currentVariantId) || variantsData[0];
        
        for (let attrName in attributes) {
            html += `<div class="modal-attr-group" data-name="${attrName}">
                <p class="font-bold text-sm text-[#181611] dark:text-white uppercase tracking-wider mb-2">${attrName}:</p>
                <div class="grid grid-cols-2 gap-2">`;
            
            for (let valId in attributes[attrName]) {
                const valName = attributes[attrName][valId];
                const isActive = currentVar && currentVar.attributes.includes(parseInt(valId));
                if (isActive) selectedAttrs[attrName] = parseInt(valId);

                html += `<button type="button" class="attr-btn-modal border dark:border-white/10 rounded-xl py-2 px-3 text-center text-sm font-bold transition-all hover:border-primary ${isActive ? 'active' : ''}" data-id="${valId}">
                    ${valName}
                </button>`;
            }
            html += `</div></div>`;
        }
        
        modalAttributes.innerHTML = html;
        
        // Gắn sự kiện click
        document.querySelectorAll('.attr-btn-modal').forEach(btn => {
            btn.addEventListener('click', function() {
                const group = this.closest('.modal-attr-group');
                const groupName = group.getAttribute('data-name');
                const valId = parseInt(this.getAttribute('data-id'));
                
                group.querySelectorAll('.attr-btn-modal').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                
                selectedAttrs[groupName] = valId;
                updateModalUI();
            });
        });

        updateModalUI();
    }

    function updateModalUI() {
        const totalGroups = document.querySelectorAll('.modal-attr-group').length;
        const selectedCount = Object.keys(selectedAttrs).length;
        const selectedIdsArr = Object.values(selectedAttrs);

        // Update availability of other buttons
        document.querySelectorAll('.attr-btn-modal').forEach(btn => {
            const group = btn.closest('.modal-attr-group');
            const gName = group.getAttribute('data-name');
            const valId = parseInt(btn.getAttribute('data-id'));
            
            let testIds = [valId];
            for (let name in selectedAttrs) { if (name !== gName) testIds.push(selectedAttrs[name]); }

            const isPossible = variantsData.some(v => testIds.every(id => v.attributes.includes(id)));
            btn.classList.toggle('disabled', !isPossible);
        });

        if (selectedCount === totalGroups) {
            const match = variantsData.find(v => selectedIdsArr.every(id => v.attributes.includes(id)));
            if (match) {
                const finalPrice = match.sale_price > 0 ? match.sale_price : match.price;
                modalPrice.innerText = formatMoney(finalPrice);
                modalStock.innerText = `Còn lại: ${match.stock} sản phẩm`;
                modalImg.src = match.image || '';
                confirmBtn.disabled = match.stock === 0;
                confirmBtn.setAttribute('data-variant-id', match.id);
            }
        }
    }

    confirmBtn.addEventListener('click', function() {
        const variantId = this.getAttribute('data-variant-id');
        this.innerHTML = 'Đang xử lý...';
        this.disabled = true;

        fetch('{{ route("client.cart.change_variant") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ item_id: currentItemId, variant_id: variantId })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else {
                alert(data.message);
                this.innerHTML = 'Xác nhận';
                this.disabled = false;
            }
        });
    });
});
</script>
@endsection