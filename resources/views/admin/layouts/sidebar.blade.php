<aside
    class="w-64 border-r border-slate-200 dark:border-slate-800 bg-white dark:bg-background-dark flex flex-col fixed h-full z-50">
    <div class="p-6 flex items-center gap-3">
        <div class="bg-primary rounded-lg p-1.5 flex items-center justify-center">
            <span class="material-symbols-outlined text-background-dark font-bold">smartphone</span>
        </div>
        <div>
            <h1 class="text-xl font-bold tracking-tight text-slate-900 dark:text-white leading-none">Bee Phone</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Hệ thống quản trị</p>
        </div>
    </div>

    <nav class="flex-1 px-4 py-4 space-y-1.5 overflow-y-auto custom-scrollbar">
        {{-- Bảng điều khiển --}}
        <a class="{{ request()->is('admin') ? 'bg-primary/10 text-primary font-bold' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 font-medium' }} flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors"
            href="{{ url('admin') }}">
            <span class="material-symbols-outlined">dashboard</span>
            <span>Bảng điều khiển</span>
        </a>

        {{-- NHÓM SẢN PHẨM (DROPDOWN) --}}
        @php

            // Kiểm tra xem user có đang ở bất kỳ route nào thuộc Sản phẩm không
            $isProductGroupActive = request()->routeIs(
                'admin.products.*',
                'admin.attributes.*',
                'admin.categories.*',
                'admin.brands.*',
            );

            // Kiểm tra xem user có đang ở bất kỳ route nào thuộc Sản phẩm không
            $isProductGroupActive = request()->routeIs(
                'admin.products.*',
                'admin.attributes.*',
                'admin.categories.*',
                'admin.brands.*',
            );

        @endphp

        <div>
            <button onclick="toggleSidebarMenu('menu-products', 'icon-products')"
                class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg transition-colors {{ $isProductGroupActive ? 'bg-primary/10 text-primary font-bold' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 font-medium' }}">
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined">inventory_2</span>
                    <span>Sản phẩm</span>
                </div>
                <span id="icon-products"
                    class="material-symbols-outlined text-[18px] transition-transform duration-300 {{ $isProductGroupActive ? 'rotate-180' : '' }}">
                    expand_more
                </span>
            </button>

            <div id="menu-products"
                class="overflow-hidden transition-all duration-300 ease-in-out {{ $isProductGroupActive ? 'max-h-96 opacity-100 mt-1' : 'max-h-0 opacity-0' }} flex flex-col space-y-1">

                <a href="{{ route('admin.products.index') }}"
                    class="pl-11 pr-3 py-2 rounded-lg text-sm font-medium transition-colors flex items-center gap-2 {{ request()->routeIs('admin.products.*') ? 'text-primary bg-primary/5 dark:bg-primary/10' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800' }}">

                    <div
                        class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('admin.products.*') ? 'bg-primary' : 'bg-slate-300 dark:bg-slate-600' }}">
                    </div>

                    <div
                        class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('admin.products.*') ? 'bg-primary' : 'bg-slate-300 dark:bg-slate-600' }}">
                    </div>

                    Danh sách SP
                </a>

                <a href="{{ route('admin.attributes.index') }}"
                    class="pl-11 pr-3 py-2 rounded-lg text-sm font-medium transition-colors flex items-center gap-2 {{ request()->routeIs('admin.attributes.*') ? 'text-primary bg-primary/5 dark:bg-primary/10' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800' }}">

                    <div
                        class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('admin.attributes.*') ? 'bg-primary' : 'bg-slate-300 dark:bg-slate-600' }}">
                    </div>

                    <div
                        class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('admin.attributes.*') ? 'bg-primary' : 'bg-slate-300 dark:bg-slate-600' }}">
                    </div>

                    Thuộc tính
                </a>

                <a href="{{ route('admin.categories.index') }}"
                    class="pl-11 pr-3 py-2 rounded-lg text-sm font-medium transition-colors flex items-center gap-2 {{ request()->routeIs('admin.categories.*') ? 'text-primary bg-primary/5 dark:bg-primary/10' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800' }}">

                    <div
                        class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('admin.categories.*') ? 'bg-primary' : 'bg-slate-300 dark:bg-slate-600' }}">
                    </div>
                    <div
                        class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('admin.categories.*') ? 'bg-primary' : 'bg-slate-300 dark:bg-slate-600' }}">
                    </div>
                    Danh mục
                </a>

                <a href="{{ route('admin.brands.index') }}"
                    class="pl-11 pr-3 py-2 rounded-lg text-sm font-medium transition-colors flex items-center gap-2 {{ request()->routeIs('admin.brands.*') ? 'text-primary bg-primary/5 dark:bg-primary/10' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                    <div
                        class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('admin.brands.*') ? 'bg-primary' : 'bg-slate-300 dark:bg-slate-600' }}">
                    </div>
                    <div
                        class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('admin.brands.*') ? 'bg-primary' : 'bg-slate-300 dark:bg-slate-600' }}">
                    </div>
                    Thương hiệu
                </a>
            </div>
        </div>

        {{-- Các Menu Khác --}}
        <a class="{{ request()->routeIs('admin.orders.*') ? 'bg-primary/10 text-primary font-bold' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 font-medium' }} flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors"
            href="{{ route('admin.orders.index') }}">
            <span class="material-symbols-outlined">shopping_cart</span>
            <span>Đơn hàng</span>
        </a>

        <a class="{{ request()->routeIs('admin.users.*') ? 'bg-primary/10 text-primary font-bold' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 font-medium' }} flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors"
            href="{{ route('admin.users.index') }}">
            <span class="material-symbols-outlined">group</span>
            <span>Người dùng</span>
        </a>

        <a class="{{ request()->routeIs('admin.vouchers.*') ? 'bg-primary/10 text-primary font-bold' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 font-medium' }} flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors"
            href="{{ route('admin.vouchers.index') }}">
            <span class="material-symbols-outlined">confirmation_number</span>
            <span>Voucher</span>
        </a>

        <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
            href="{{ route('admin.posts.index') }}">
            <span class="material-symbols-outlined">local_offer</span>
            <span>Bài viết</span>
        </a>

        <a class="{{ request()->routeIs('admin.comments.*') ? 'bg-primary/10 text-primary font-bold' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 font-medium' }} flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors"
            href="{{ route('admin.comments.index') }}">
            <span class="material-symbols-outlined">chat</span>
            <span>Comments</span>
        </a>

  <a class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-300 {{ request()->routeIs('admin.wallets.*') ? 'bg-bee text-white shadow-lg shadow-bee/30' : 'text-slate-600 dark:text-slate-400 hover:bg-bee/10 hover:text-bee' }}"
   href="{{ route('admin.wallets.index') }}">
    <span class="material-symbols-outlined transition-transform group-hover:scale-110">
        account_balance_wallet
    </span>
    <span class="font-medium">Quản lý ví</span>
</a>

        <div class="pt-4 mt-4 border-t border-slate-100 dark:border-slate-800">
            <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 font-medium transition-colors"
                href="#">
                <span class="material-symbols-outlined">settings</span>
                <span>Cài đặt</span>
            </a>
        </div>
    </nav>
</aside>

{{-- SCRIPTS XỬ LÝ DROPDOWN --}}
<script>
    function toggleSidebarMenu(menuId, iconId) {
        const menu = document.getElementById(menuId);
        const icon = document.getElementById(iconId);

        // Đóng/Mở menu bằng cách thay đổi class max-h
        if (menu.classList.contains('max-h-0')) {
            // Mở menu
            menu.classList.remove('max-h-0', 'opacity-0');
            menu.classList.add('max-h-96', 'opacity-100', 'mt-1');
            // Xoay icon
            icon.classList.add('rotate-180');
        } else {
            // Đóng menu
            menu.classList.add('max-h-0', 'opacity-0');
            menu.classList.remove('max-h-96', 'opacity-100', 'mt-1');
            // Trả icon về cũ
            icon.classList.remove('rotate-180');
        }
    }
</script>

<style>
    /* Style thu gọn thanh cuộn nếu menu dài quá */
    .custom-scrollbar::-webkit-scrollbar {
        width: 4px;
    }

    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
</style>
