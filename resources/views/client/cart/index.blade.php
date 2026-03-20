@extends('client.layouts.app')

@section('title', 'Bee Phone - Giỏ hàng của bạn')

@section('content')
<style>
    .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #f4c025; border-radius: 10px; }
</style>

<main class="pt-10 pb-20 px-6 md:px-12 max-w-screen-2xl mx-auto min-h-screen">
    <div class="flex flex-col lg:flex-row gap-8">
        
        <div class="flex-grow">
            <h1 class="text-3xl font-bold mb-8 tracking-tight text-[#181611] dark:text-white">Giỏ hàng của bạn</h1>
            
            @if($cartItems->count() > 0)
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
                                <input checked class="w-5 h-5 rounded border-gray-300 text-primary focus:ring-primary" type="checkbox"/>
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
                                        <p class="text-gray-500 dark:text-gray-400 text-sm mt-1 uppercase tracking-wider font-bold">{{ $variantName }}</p>
                                    @endif
                                    <span class="inline-flex items-center mt-2 px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-widest bg-emerald-100 text-emerald-700">Còn hàng ({{ $stock }})</span>
                                </div>
                                <div class="flex flex-col md:items-end justify-center">
                                    <span class="text-xl font-bold text-red-500">{{ number_format($price, 0, ',', '.') }}₫</span>
                                    @php
                                        $oldPrice = $item->variant ? $item->variant->price : $item->product->price;
                                    @endphp
                                    @if($oldPrice > $price)
                                        <span class="text-sm text-gray-400 line-through">{{ number_format($oldPrice, 0, ',', '.') }}₫</span>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="flex items-center gap-6 w-full md:w-auto justify-between md:justify-end">
                                <div class="flex items-center border border-gray-200 dark:border-white/10 rounded-lg bg-gray-50 dark:bg-black/20">
                                    <button class="btn-qty-minus p-2 hover:text-primary transition-colors" data-id="{{ $item->id }}">
                                        <span class="material-symbols-outlined text-sm">remove</span>
                                    </button>
                                    <input class="qty-input w-12 text-center bg-transparent border-none focus:ring-0 font-bold text-[#181611] dark:text-white p-0" type="text" value="{{ $item->quantity }}" readonly/>
                                    <button class="btn-qty-plus p-2 hover:text-primary transition-colors" data-id="{{ $item->id }}">
                                        <span class="material-symbols-outlined text-sm">add</span>
                                    </button>
                                </div>
                                <button class="btn-remove text-gray-400 hover:text-red-500 transition-colors p-2" data-id="{{ $item->id }}" title="Xóa">
                                    <span class="material-symbols-outlined">delete</span>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-8 p-8 bg-primary/5 dark:bg-primary/10 rounded-2xl border-dashed border-2 border-primary/20 text-center">
                    <span class="material-symbols-outlined text-4xl text-primary mb-4">auto_awesome</span>
                    <h4 class="text-lg font-bold text-[#181611] dark:text-white">Ưu đãi cực lớn đang chờ bạn!</h4>
                    <p class="text-gray-500 dark:text-gray-400 text-sm max-w-md mx-auto mt-2">Áp dụng mã giảm giá ngay để nhận được mức giá tốt nhất từ Bee Phone.</p>
                </div>

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
                        <span class="font-medium text-[#181611] dark:text-white" id="summary-subtotal">{{ number_format($totalPrice, 0, ',', '.') }}₫</span>
                    </div>
                    <div class="flex justify-between text-gray-500 dark:text-gray-400">
                        <span class="flex items-center gap-1">Phí vận chuyển <span class="material-symbols-outlined text-xs">info</span></span>
                        <span class="font-medium text-[#181611] dark:text-white">Miễn phí</span>
                    </div>
                    <div class="pt-4 border-t border-dashed border-gray-200 dark:border-white/10 flex items-center justify-between">
                        <span class="text-sm font-bold uppercase tracking-widest text-[#181611] dark:text-white">Đơn vị vận chuyển</span>
                        <span class="text-sm font-bold text-primary">Giao Hàng Nhanh</span>
                    </div>
                </div>

                <div class="mb-8">
                    <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-2">Mã giảm giá</label>
                    <div class="flex gap-2">
                        <input id="voucher-code" class="flex-grow bg-gray-50 dark:bg-black/20 border border-gray-200 dark:border-white/10 rounded-lg focus:ring-2 focus:ring-primary px-4 py-2 text-sm text-[#181611] dark:text-white outline-none" placeholder="Nhập mã..." type="text"/>
                        <button id="btn-apply-voucher" class="bg-[#181611] dark:bg-white text-white dark:text-black px-4 py-2 rounded-lg font-bold text-sm transition-transform active:scale-95">Áp dụng</button>
                    </div>
                    <p id="voucher-msg" class="text-xs font-bold mt-2"></p> 
                </div>

                <div class="border-t border-gray-100 dark:border-white/10 pt-6 mb-8">
                    <div class="flex justify-between items-end mb-4 hidden" id="discount-row">
                        <span class="text-sm font-bold text-gray-500">Giảm giá voucher</span>
                        <span class="text-lg font-bold text-green-500">-<span id="summary-discount">0</span>₫</span>
                    </div>

                    <div class="flex justify-between items-end">
                        <span class="text-lg font-bold text-[#181611] dark:text-white">Tổng tiền</span>
                        <div class="text-right">
                            <p class="text-3xl font-bold text-red-500" id="summary-total">{{ number_format($totalPrice, 0, ',', '.') }}₫</p>
                            <p class="text-[10px] text-gray-500 dark:text-gray-400 uppercase font-bold tracking-widest mt-1">(Đã bao gồm thuế VAT)</p>
                        </div>
                    </div>
                </div>

                <a href="{{ route('client.checkout.index') }}" class="w-full bg-primary text-black font-bold py-4 rounded-xl shadow-lg shadow-primary/20 hover:opacity-90 transition-all flex items-center justify-center gap-3 active:scale-[0.98]">
                    <span>Thanh toán ngay</span>
                    <span class="material-symbols-outlined">arrow_forward</span>
                </a>
                <p class="text-center text-[10px] text-gray-400 dark:text-gray-500 mt-6 uppercase tracking-widest font-bold">An toàn • Bảo mật • Tiết kiệm</p>
            </div>
        </div>
        @endif
        
    </div>
</main>

<script> 
document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = '{{ csrf_token() }}';

    function formatMoney(num) {
        return num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.') + '₫';
    }

    // Cập nhật lại tổng tiền giao diện
    function updateCartTotals() {
        let totalQty = 0;
        let totalPrice = 0;

        document.querySelectorAll('.cart-item').forEach(item => {
            const qty = parseInt(item.querySelector('.qty-input').value);
            const price = parseFloat(item.getAttribute('data-price'));
            totalQty += qty;
            totalPrice += (qty * price);
        });

        const subtotalEl = document.getElementById('summary-subtotal');
        const totalEl = document.getElementById('summary-total');
        const countEl = document.getElementById('summary-count');

        if(subtotalEl) subtotalEl.innerText = formatMoney(totalPrice);
        if(totalEl) totalEl.innerText = formatMoney(totalPrice);
        if(countEl) countEl.innerText = totalQty;

        const headerBadges = document.querySelectorAll('.bg-primary.text-black.rounded-full');
        headerBadges.forEach(b => b.innerText = totalQty);

        if (totalQty === 0) {
            window.location.reload();
        }

        // Quan trọng: Nếu thay đổi số lượng, reset voucher để tránh lỗi tính toán
        const discountRow = document.getElementById('discount-row');
        if (discountRow && !discountRow.classList.contains('hidden')) {
            discountRow.classList.add('hidden');
            document.getElementById('voucher-code').value = '';
            document.getElementById('voucher-msg').innerText = 'Giỏ hàng đã thay đổi, vui lòng áp dụng lại mã (nếu có).';
            document.getElementById('voucher-msg').className = 'text-xs font-bold mt-2 text-amber-500';
        }
    }

    function updateQuantityAjax(itemId, newQty) {
        fetch('{{ route("client.cart.update") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ item_id: itemId, quantity: newQty })
        }).then(res => res.json()).then(data => {
            if(!data.success) {
                alert(data.message);
                window.location.reload();
            }
        });
    }

    // Nút Tăng số lượng
    document.querySelectorAll('.btn-qty-plus').forEach(btn => {
        btn.addEventListener('click', function() {
            const itemRow = this.closest('.cart-item');
            const maxStock = parseInt(itemRow.getAttribute('data-stock')); 
            const itemId = this.getAttribute('data-id');
            const input = this.previousElementSibling;
            let qty = parseInt(input.value);

            if (qty < maxStock) { 
                qty += 1;
                input.value = qty;
                updateCartTotals();
                updateQuantityAjax(itemId, qty);
            } else {
                alert('Bạn đã chọn tối đa số lượng trong kho (' + maxStock + ' sản phẩm)!');
            }
        });
    });

    // Nút Giảm số lượng
    document.querySelectorAll('.btn-qty-minus').forEach(btn => {
        btn.addEventListener('click', function() {
            const itemId = this.getAttribute('data-id');
            const input = this.nextElementSibling;
            let qty = parseInt(input.value);
            if (qty > 1) {
                qty -= 1;
                input.value = qty;
                updateCartTotals();
                updateQuantityAjax(itemId, qty);
            }
        });
    });

    // Nút Xóa sản phẩm
    document.querySelectorAll('.btn-remove').forEach(btn => {
        btn.addEventListener('click', function() {
            if(confirm('Bạn có chắc muốn xóa sản phẩm này khỏi giỏ hàng?')) {
                const itemId = this.getAttribute('data-id');
                const itemRow = this.closest('.cart-item');
                
                fetch('{{ route("client.cart.remove") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ item_id: itemId })
                }).then(res => res.json()).then(data => {
                    if(data.success) {
                        itemRow.remove(); 
                        updateCartTotals(); 
                    }
                });
            }
        });
    });

    // XỬ LÝ NÚT ÁP DỤNG MÃ GIẢM GIÁ
    const btnVoucher = document.getElementById('btn-apply-voucher');
    if (btnVoucher) {
        btnVoucher.addEventListener('click', function(e) {
            e.preventDefault();
            const code = document.getElementById('voucher-code').value;
            const msgEl = document.getElementById('voucher-msg');
            
            if(!code) {
                msgEl.innerText = 'Vui lòng nhập mã!';
                msgEl.className = 'text-xs font-bold mt-2 text-red-500';
                return;
            }

            msgEl.innerText = 'Đang kiểm tra...';
            msgEl.className = 'text-xs font-bold mt-2 text-blue-500';

            fetch('{{ route("client.cart.apply_voucher") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ code: code })
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    msgEl.innerText = data.message;
                    msgEl.className = 'text-xs font-bold mt-2 text-green-500';
                    
                    document.getElementById('discount-row').classList.remove('hidden');
                    document.getElementById('summary-discount').innerText = data.discount_formatted;
                    document.getElementById('summary-total').innerText = data.new_total + '₫';
                } else {
                    msgEl.innerText = data.message;
                    msgEl.className = 'text-xs font-bold mt-2 text-red-500';
                    document.getElementById('discount-row').classList.add('hidden');
                    
                    // Nếu lỗi thì hiện lại giá gốc dựa trên tạm tính
                    document.getElementById('summary-total').innerText = document.getElementById('summary-subtotal').innerText;
                }
            })
            .catch(err => {
                msgEl.innerText = 'Lỗi kết nối máy chủ! Vui lòng thử lại.';
                msgEl.className = 'text-xs font-bold mt-2 text-red-500';
            });
        });
    }
});
</script>
@endsection