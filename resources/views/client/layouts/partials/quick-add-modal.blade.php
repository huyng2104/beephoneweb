<style>
    .quick-attr-btn-modal.active {
        border-color: #f4c025 !important;
        background-color: rgba(244, 192, 37, 0.1) !important;
        box-shadow: 0 0 0 2px #f4c025 !important;
        color: #000;
    }
    .dark .quick-attr-btn-modal.active {
        color: #fff;
    }
    .quick-attr-btn-modal.disabled {
        opacity: 0.4 !important;
        filter: grayscale(100%) !important;
    }
</style>

{{-- MODAL CHỌN BIẾN THỂ THÊM NHANH GIỎ HÀNG --}}
<div id="quick-add-modal" class="fixed inset-0 z-[9999] hidden flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
    <div class="bg-white dark:bg-[#111111] w-full max-w-lg rounded-2xl shadow-2xl overflow-hidden border border-gray-100 dark:border-white/10">
        <div class="p-6 border-b border-gray-100 dark:border-white/10 flex items-center justify-between">
            <h3 class="text-xl font-bold text-[#181611] dark:text-white">Chọn phiên bản</h3>
            <button type="button" class="close-quick-modal text-gray-400 hover:text-red-500 transition-colors">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <div class="p-6 max-h-[70vh] overflow-y-auto custom-scrollbar">
            <div id="quick-modal-product-info" class="flex gap-4 mb-6 pb-6 border-b border-dashed border-gray-200 dark:border-white/10">
                <div class="w-20 h-20 bg-gray-50 dark:bg-white/5 rounded-lg p-2 flex items-center justify-center">
                    <img id="quick-modal-img" src="" class="max-w-full max-h-full object-contain">
                </div>
                <div class="flex-grow">
                    <h4 id="quick-modal-product-name" class="font-bold text-base text-[#181611] dark:text-white line-clamp-2"></h4>
                    <p id="quick-modal-price-display" class="text-red-500 font-bold text-xl mt-1"></p>
                    <p id="quick-modal-stock-display" class="text-xs text-gray-500 mt-1"></p>
                </div>
            </div>
            
            <div id="quick-modal-attributes" class="space-y-6">
                {{-- Sẽ được render bằng JS --}}
                <div class="flex items-center justify-center py-10">
                    <span class="text-gray-500 font-bold animate-pulse">Đang tải...</span>
                </div>
            </div>
        </div>
        <div class="p-6 border-t border-gray-100 dark:border-white/10 flex flex-col items-center gap-4">
            <div class="flex items-center justify-between w-full h-12 bg-gray-50 dark:bg-white/5 rounded-xl px-4 border border-gray-200 dark:border-white/10">
                <span class="font-bold text-sm text-[#181611] dark:text-white uppercase tracking-wider">Số lượng:</span>
                <div class="flex items-center gap-2">
                    <button type="button" id="quick-modal-qty-minus" class="w-8 h-8 flex items-center justify-center rounded hover:bg-gray-200 dark:hover:bg-white/10 transition-colors">
                        <span class="material-symbols-outlined text-sm">remove</span>
                    </button>
                    <input id="quick-modal-qty" type="text" value="1" min="1" class="w-10 text-center bg-transparent border-none focus:ring-0 font-bold text-[#181611] dark:text-white p-0 pointer-events-none" readonly/>
                    <button type="button" id="quick-modal-qty-plus" class="w-8 h-8 flex items-center justify-center rounded hover:bg-gray-200 dark:hover:bg-white/10 transition-colors">
                        <span class="material-symbols-outlined text-sm">add</span>
                    </button>
                </div>
            </div>
            <div class="flex gap-3 w-full">
                <button type="button" class="close-quick-modal w-1/3 bg-gray-100 dark:bg-white/5 text-gray-500 font-bold py-3 rounded-xl hover:bg-gray-200 transition-colors active:scale-95">Hủy</button>
                <button type="button" id="confirm-quick-add" class="w-2/3 bg-primary text-black font-bold py-3 rounded-xl shadow-lg shadow-primary/20 hover:opacity-90 active:scale-95 transition-all disabled:opacity-50 disabled:cursor-not-allowed text-center">Thêm vào giỏ</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const quickModal = document.getElementById('quick-add-modal');
    // Ensure modal operates safely if elements missing
    if(!quickModal) return;

    const modalAttrContainer = document.getElementById('quick-modal-attributes');
    const modalImg = document.getElementById('quick-modal-img');
    const modalName = document.getElementById('quick-modal-product-name');
    const modalPrice = document.getElementById('quick-modal-price-display');
    const modalStock = document.getElementById('quick-modal-stock-display');
    const confirmBtn = document.getElementById('confirm-quick-add');
    
    const qtyMinusBtn = document.getElementById('quick-modal-qty-minus');
    const qtyPlusBtn = document.getElementById('quick-modal-qty-plus');
    const qtyInput = document.getElementById('quick-modal-qty');
    
    let currentProdId = null;
    let variantsData = [];
    let selectedAttrs = {};
    let maxStock = 1;

    function formatMoney(num) {
        return num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.') + '₫';
    }

    function openModal() { 
        quickModal.classList.remove('hidden'); 
        document.body.style.overflow = 'hidden'; 
        qtyInput.value = 1;
    }
    function closeModal() { quickModal.classList.add('hidden'); document.body.style.overflow = ''; }

    document.querySelectorAll('.close-quick-modal').forEach(b => b.addEventListener('click', closeModal));

    // Handle variable product add click via document delegation (important since products might be loaded dynamically)
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-add-cart-quick-variable');
        if (btn) {
            e.preventDefault();
            currentProdId = btn.getAttribute('data-product-id');
            
            openModal();
            modalAttrContainer.innerHTML = '<div class="flex items-center justify-center py-10"><span class="text-gray-500 font-bold animate-pulse">Đang tải...</span></div>';
            confirmBtn.disabled = true;

            fetch(`{{ route('client.cart.get_variants') }}?product_id=${currentProdId}`)
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        variantsData = data.variants;
                        modalName.innerText = data.product_name;
                        // Mặc định chọn variant đầu tiên
                        let defaultVarId = null;
                        if (variantsData.length > 0) defaultVarId = variantsData[0].id;
                        renderModalAttributes(data.attributes, defaultVarId);
                    } else {
                        alert(data.message);
                        closeModal();
                    }
                })
                .catch(err => {
                    alert('Lỗi tải dữ liệu');
                    closeModal();
                });
        }
    });

    function renderModalAttributes(attributes, currentVariantId) {
        let html = '';
        selectedAttrs = {};
        
        // Tìm biến thể hiện tại để pre-select
        const currentVar = variantsData.find(v => v.id == currentVariantId) || variantsData[0];
        
        for (let attrName in attributes) {
            html += `<div class="quick-modal-attr-group" data-name="${attrName}">
                <p class="font-bold text-sm text-[#181611] dark:text-white uppercase tracking-wider mb-2">${attrName}:</p>
                <div class="grid grid-cols-2 gap-2">`;
            
            for (let valId in attributes[attrName]) {
                const valName = attributes[attrName][valId];
                const isActive = currentVar && currentVar.attributes.includes(parseInt(valId));
                if (isActive) selectedAttrs[attrName] = parseInt(valId);

                html += `<button type="button" class="quick-attr-btn-modal border dark:border-white/10 rounded-xl py-2 px-3 text-center text-sm font-bold transition-all hover:border-primary ${isActive ? 'active' : ''}" data-id="${valId}">
                    ${valName}
                </button>`;
            }
            html += `</div></div>`;
        }
        
        modalAttrContainer.innerHTML = html;
        
        // Gắn sự kiện click
        document.querySelectorAll('.quick-attr-btn-modal').forEach(btn => {
            btn.addEventListener('click', function() {
                const group = this.closest('.quick-modal-attr-group');
                const groupName = group.getAttribute('data-name');
                const valId = parseInt(this.getAttribute('data-id'));
                
                group.querySelectorAll('.quick-attr-btn-modal').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                
                selectedAttrs[groupName] = valId;
                autoSelectMatchingQuickModal(groupName);
                updateModalUI();
            });
        });

        function autoSelectMatchingQuickModal(clickedGroupName) {
            const selectedIdsArr = Object.values(selectedAttrs);
            const currentMatch = variantsData.find(v => selectedIdsArr.every(id => v.attributes.includes(id)));

            if (!currentMatch) {
                const clickedValId = selectedAttrs[clickedGroupName];
                const newMatch = variantsData.find(v => v.stock > 0 && v.attributes.includes(clickedValId)) 
                              || variantsData.find(v => v.attributes.includes(clickedValId));
                if (newMatch) {
                    const groups = document.querySelectorAll('.quick-modal-attr-group');
                    groups.forEach(g => {
                        const gName = g.getAttribute('data-name');
                        if (gName !== clickedGroupName) {
                            const btns = g.querySelectorAll('.quick-attr-btn-modal');
                            btns.forEach(b => {
                                const bValId = parseInt(b.getAttribute('data-id'));
                                if (newMatch.attributes.includes(bValId)) {
                                    g.querySelectorAll('.quick-attr-btn-modal').forEach(bb => bb.classList.remove('active'));
                                    b.classList.add('active');
                                    selectedAttrs[gName] = bValId;
                                }
                            });
                        }
                    });
                }
            }
        }

        updateModalUI();
    }

    function updateModalUI() {
        if (!variantsData || variantsData.length === 0) return;
        const totalGroups = document.querySelectorAll('.quick-modal-attr-group').length;
        const selectedCount = Object.keys(selectedAttrs).length;
        const selectedIdsArr = Object.values(selectedAttrs);

        // Update availability of other buttons
        document.querySelectorAll('.quick-attr-btn-modal').forEach(btn => {
            const group = btn.closest('.quick-modal-attr-group');
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
                modalStock.innerText = `Còn trống: ${match.stock} sản phẩm`;
                modalImg.src = match.image || '';
                maxStock = match.stock;
                
                if (maxStock === 0) {
                   qtyInput.value = 0;
                   confirmBtn.disabled = true;
                } else {
                   let currentQty = parseInt(qtyInput.value);
                   if (currentQty === 0 || isNaN(currentQty)) qtyInput.value = 1;
                   if (parseInt(qtyInput.value) > maxStock) qtyInput.value = maxStock;
                   confirmBtn.disabled = false;
                }

                confirmBtn.setAttribute('data-variant-id', match.id);
            } else {
                modalPrice.innerText = 'Biến thể không tồn tại';
                modalStock.innerText = '';
                confirmBtn.disabled = true;
                maxStock = 0;
            }
        }
    }

    qtyMinusBtn.addEventListener('click', function() {
        let qty = parseInt(qtyInput.value);
        if (qty > 1) {
            qtyInput.value = qty - 1;
        }
    });

    qtyPlusBtn.addEventListener('click', function() {
        let qty = parseInt(qtyInput.value);
        if (qty < maxStock) {
            qtyInput.value = qty + 1;
        } else {
            alert('Tối đa trong kho (' + maxStock + ')!');
        }
    });

    confirmBtn.addEventListener('click', function() {
        const variantId = this.getAttribute('data-variant-id');
        const originalText = this.innerHTML;
        this.innerHTML = '<span class="material-symbols-outlined animate-spin align-middle inline-block h-5 w-5 mr-1" style="font-size: 18px;">refresh</span> Đang xử lý...';
        this.disabled = true;

        fetch('{{ route("client.cart.add") }}', {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/json', 
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') 
            },
            body: JSON.stringify({ 
                product_id: currentProdId, 
                variant_id: variantId, 
                quantity: parseInt(qtyInput.value) || 1 
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                this.innerHTML = '<span class="material-symbols-outlined align-middle inline-block h-5 w-5 mr-1" style="font-size: 18px;">check</span> Đã thêm';
                this.classList.replace('bg-primary', 'bg-green-500');
                
                // Cập nhật số lượng giỏ hàng trên header
                const cartBadges = document.querySelectorAll('.bg-primary.text-black.rounded-full');
                cartBadges.forEach(badge => badge.innerText = data.cart_count);
                
                setTimeout(() => {
                    closeModal();
                    this.innerHTML = originalText;
                    this.classList.replace('bg-green-500', 'bg-primary');
                    this.disabled = false;
                }, 1000);
            } else {
                alert(data.message);
                this.innerHTML = originalText;
                this.disabled = false;
            }
        })
        .catch(err => {
            alert('Lỗi thêm giỏ hàng');
            this.innerHTML = originalText;
            this.disabled = false;
        });
    });
});
</script>
