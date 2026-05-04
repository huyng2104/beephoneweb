<aside id="admin-sidebar" class="w-64 border-r border-slate-200 dark:border-slate-800 bg-white dark:bg-background-dark flex flex-col fixed h-full z-50 transition-transform duration-300 -translate-x-full lg:translate-x-0">
    @php
        $logo = isset($site_settings['site_logo']) ? $site_settings['site_logo']->value : null;
    @endphp
        <a href="{{ url('/') }}" class="p-6 flex items-center gap-3 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
            <div class=" p-1.5 flex items-center justify-center shrink-0">
                @if(isset($site_settings['site_logo']) && $site_settings['site_logo']->value)
                    <img src="{{ asset($site_settings['site_logo']->value) }}" alt="{{ $site_settings['site_name']->value ?? 'Bee Phone' }}" class="h-10 w-auto object-contain transition-transform group-hover:scale-105">
                @else
                    <div class="size-10 bg-primary rounded-xl flex items-center justify-center text-black group-hover:scale-105 transition-transform shadow-lg shadow-primary/20">
                        <span class="material-symbols-outlined text-2xl">rocket_launch</span>
                    </div>
                @endif
            </div>
            <div class="overflow-hidden">
                <h1 class="text-lg font-bold tracking-tight text-slate-900 dark:text-white leading-none truncate">Bee Phone</h1>
                <p class="text-[10px] text-slate-500 dark:text-slate-400 mt-1 font-semibold uppercase tracking-wider">Hệ thống quản trị</p>
            </div>
        </a>

    <nav class="flex-1 px-4 py-4 space-y-1.5 overflow-y-auto custom-scrollbar">
        {{-- Bảng điều khiển --}}
        {{-- NHÓM THỐNG KÊ (DROPDOWN) --}}
        @php
            $isStatsGroupActive = request()->is('admin') || request()->routeIs('admin.dashboard.orders', 'admin.dashboard.users');
        @endphp
        <div>
            <button onclick="toggleSidebarMenu('menu-stats', 'icon-stats')"
                class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg transition-colors {{ $isStatsGroupActive ? 'bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-white font-bold' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 font-medium' }}">
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined">monitoring</span>
                    <span>Thống kê</span>
                </div>
                <span id="icon-stats"
                    class="material-symbols-outlined text-[18px] transition-transform duration-300 {{ $isStatsGroupActive ? 'rotate-180' : '' }}">
                    expand_more
                </span>
            </button>

            <div id="menu-stats"
                class="overflow-hidden transition-all duration-300 ease-in-out {{ $isStatsGroupActive ? 'max-h-96 opacity-100 mt-1' : 'max-h-0 opacity-0' }} flex flex-col space-y-1">
                <a href="{{ route('admin.dashboard') }}"
                    class="pl-11 pr-3 py-2 rounded-lg text-sm font-medium transition-colors flex items-center gap-2 {{ request()->is('admin') ? 'text-slate-900 dark:text-white bg-slate-100 dark:bg-slate-800 font-bold' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                    <div class="w-1.5 h-1.5 rounded-full {{ request()->is('admin') ? 'bg-primary' : 'bg-slate-300 dark:bg-slate-600' }}"></div>
                    Doanh thu
                </a>

                <a href="{{ route('admin.dashboard.orders') }}"
                    class="pl-11 pr-3 py-2 rounded-lg text-sm font-medium transition-colors flex items-center gap-2 {{ request()->routeIs('admin.dashboard.orders') ? 'text-slate-900 dark:text-white bg-slate-100 dark:bg-slate-800 font-bold' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                    <div class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('admin.dashboard.orders') ? 'bg-primary' : 'bg-slate-300 dark:bg-slate-600' }}"></div>
                    Đơn hàng
                </a>

                @can('customer.view')
                <a href="{{ route('admin.dashboard.users') }}"
                    class="pl-11 pr-3 py-2 rounded-lg text-sm font-medium transition-colors flex items-center gap-2 {{ request()->routeIs('admin.dashboard.users') ? 'text-slate-900 dark:text-white bg-slate-100 dark:bg-slate-800 font-bold' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                    <div class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('admin.dashboard.users') ? 'bg-primary' : 'bg-slate-300 dark:bg-slate-600' }}"></div>
                    Người dùng
                </a>
                @endcan
            </div>
        </div>

        {{-- NHÓM SẢN PHẨM (DROPDOWN) --}}
        @canany(['product.view', 'attribute.view', 'category.view', 'brand.view'])
        @php
            $isProductGroupActive = request()->routeIs(
                'admin.products.*',
                'admin.attributes.*',
                'admin.categories.*',
                'admin.brands.*',
            );
        @endphp

        <div>
            <button onclick="toggleSidebarMenu('menu-products', 'icon-products')"
                class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg transition-colors {{ $isProductGroupActive ? 'bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-white font-bold' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 font-medium' }}">
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
                @can('product.view')
                    <a href="{{ route('admin.products.index') }}"
                        class="pl-11 pr-3 py-2 rounded-lg text-sm font-medium transition-colors flex items-center gap-2 {{ request()->routeIs('admin.products.*') ? 'text-slate-900 dark:text-white bg-slate-100 dark:bg-slate-800 font-bold' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                        <div class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('admin.products.*') ? 'bg-primary' : 'bg-slate-300 dark:bg-slate-600' }}"></div>
                        Danh sách SP
                    </a>
                @endcan

                @can('attribute.view')
                    <a href="{{ route('admin.attributes.index') }}"
                        class="pl-11 pr-3 py-2 rounded-lg text-sm font-medium transition-colors flex items-center gap-2 {{ request()->routeIs('admin.attributes.*') ? 'text-slate-900 dark:text-white bg-slate-100 dark:bg-slate-800 font-bold' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                        <div class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('admin.attributes.*') ? 'bg-primary' : 'bg-slate-300 dark:bg-slate-600' }}"></div>
                        Thuộc tính
                    </a>
                @endcan

                @can('category.view')
                    <a href="{{ route('admin.categories.index') }}"
                        class="pl-11 pr-3 py-2 rounded-lg text-sm font-medium transition-colors flex items-center gap-2 {{ request()->routeIs('admin.categories.*') ? 'text-slate-900 dark:text-white bg-slate-100 dark:bg-slate-800 font-bold' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                        <div class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('admin.categories.*') ? 'bg-primary' : 'bg-slate-300 dark:bg-slate-600' }}"></div>
                        Danh mục
                    </a>
                @endcan

                @can('brand.view')
                    <a href="{{ route('admin.brands.index') }}"
                        class="pl-11 pr-3 py-2 rounded-lg text-sm font-medium transition-colors flex items-center gap-2 {{ request()->routeIs('admin.brands.*') ? 'text-slate-900 dark:text-white bg-slate-100 dark:bg-slate-800 font-bold' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                        <div class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('admin.brands.*') ? 'bg-primary' : 'bg-slate-300 dark:bg-slate-600' }}"></div>
                        Thương hiệu
                    </a>
                @endcan
            </div>
        </div>
        @endcanany

        {{-- Các Menu Khác --}}
        @can('order.view')
            @php
                $pendingOrders  = \App\Models\Order::where('status', 'pending')->count();
                $pendingReturns = \App\Models\ReturnRequest::where('status', 'pending')->count();
                $totalOrderBadge = $pendingOrders + $pendingReturns;
            @endphp
            <div class="mb-1">
                <div id="btn-toggle-orders"
                    class="px-3 py-2.5 flex items-center justify-between text-sm cursor-pointer transition-colors {{ request()->routeIs('admin.orders.*') || request()->routeIs('admin.returns.*') ? 'bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-white font-bold rounded-lg' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 font-medium rounded-lg' }}">
                    <span class="flex items-center gap-3">
                        <span class="material-symbols-outlined">shopping_cart</span>
                        Quản lý đơn hàng
                        @if($totalOrderBadge > 0)
                            <span class="inline-flex items-center justify-center min-w-[20px] h-5 px-1.5 rounded-full bg-red-500 text-white text-[10px] font-bold">
                                {{ $totalOrderBadge > 99 ? '99+' : $totalOrderBadge }}
                            </span>
                        @endif
                    </span>

                    <svg id="icon-arrow-orders" xmlns="http://www.w3.org/2000/svg"
                        class="w-4 h-4 transition-transform duration-200 {{ request()->routeIs('admin.orders.*') || request()->routeIs('admin.returns.*') ? 'rotate-180' : '' }}"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>

                <ul id="menu-orders-sub"
                    class="space-y-1 mt-1 list-none transition-all duration-300 {{ request()->routeIs('admin.orders.*') || request()->routeIs('admin.returns.*') ? '' : 'hidden' }}">

                    <li>
                        <a href="{{ route('admin.orders.index') }}"
                            class="pl-11 pr-3 py-2 rounded-lg text-sm font-medium transition-colors flex items-center gap-2 {{ request()->routeIs('admin.orders.*') && !request()->routeIs('admin.returns.*') ? 'text-slate-900 dark:text-white bg-slate-100 dark:bg-slate-800 font-bold' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                            <div
                                class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('admin.orders.*') && !request()->routeIs('admin.returns.*') ? 'bg-primary' : 'bg-slate-300 dark:bg-slate-600' }}">
                            </div>
                            Danh sách đơn hàng
                            @if($pendingOrders > 0)
                                <span class="ml-auto inline-flex items-center justify-center min-w-[20px] h-5 px-1.5 rounded-full bg-amber-500 text-white text-[10px] font-bold">
                                    {{ $pendingOrders > 99 ? '99+' : $pendingOrders }}
                                </span>
                            @endif
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('admin.returns.index') }}"
                            class="pl-11 pr-3 py-2 rounded-lg text-sm font-medium transition-colors flex items-center gap-2 {{ request()->routeIs('admin.returns.*') ? 'text-slate-900 dark:text-white bg-slate-100 dark:bg-slate-800 font-bold' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                            <div
                                class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('admin.returns.*') ? 'bg-primary' : 'bg-slate-300 dark:bg-slate-600' }}">
                            </div>
                            Yêu cầu hoàn hàng
                            @if($pendingReturns > 0)
                                <span class="ml-auto inline-flex items-center justify-center min-w-[20px] h-5 px-1.5 rounded-full bg-orange-500 text-white text-[10px] font-bold">
                                    {{ $pendingReturns > 99 ? '99+' : $pendingReturns }}
                                </span>
                            @endif
                        </a>
                    </li>
                </ul>
            </div>
        @endcan

        @can('customer.view')
            <div class="mb-1">
                <a class="{{ request()->routeIs('admin.users.*') ? 'bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-white font-bold' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 font-medium' }} flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors"
                    href="{{ route('admin.users.index') }}">
                    <span class="material-symbols-outlined">group</span>
                    <span>Người dùng</span>
                </a>
            </div>
        @endcan

        @can('roles.view')
            @php $isPermissionGroupActive = request()->routeIs('admin.role.*', 'admin.member*', 'admin.permissions.*'); @endphp
            <div class="mb-1">
                <button onclick="toggleSidebarMenu('menu-permission', 'icon-permission')"
                    class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg transition-colors {{ $isPermissionGroupActive ? 'bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-white font-bold' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 font-medium' }}">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined">admin_panel_settings</span>
                        <span>Phân quyền</span>
                    </div>
                    <span id="icon-permission"
                        class="material-symbols-outlined text-[18px] transition-transform duration-300 {{ $isPermissionGroupActive ? 'rotate-180' : '' }}">
                        expand_more
                    </span>
                </button>

                <div id="menu-permission"
                    class="overflow-hidden transition-all duration-300 ease-in-out {{ $isPermissionGroupActive ? 'max-h-96 opacity-100 mt-1' : 'max-h-0 opacity-0' }} flex flex-col space-y-1">

                    <a href="{{ route('admin.permissions.index') }}"
                        class="pl-11 pr-3 py-2 rounded-lg text-sm font-medium transition-colors flex items-center gap-2 {{ request()->routeIs('admin.permissions.*') ? 'text-slate-900 dark:text-white bg-slate-100 dark:bg-slate-800 font-bold' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                        <div class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('admin.permissions.*') ? 'bg-primary' : 'bg-slate-300 dark:bg-slate-600' }}"></div>
                        Danh sách quyền hạn
                    </a>

                    <a href="{{ route('admin.role.index') }}"
                        class="pl-11 pr-3 py-2 rounded-lg text-sm font-medium transition-colors flex items-center gap-2 {{ request()->routeIs('admin.role.*') ? 'text-slate-900 dark:text-white bg-slate-100 dark:bg-slate-800 font-bold' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                        <div class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('admin.role.*') ? 'bg-primary' : 'bg-slate-300 dark:bg-slate-600' }}"></div>
                        Quản lý nhóm
                    </a>

                    <a href="{{ route('admin.member') }}"
                        class="pl-11 pr-3 py-2 rounded-lg text-sm font-medium transition-colors flex items-center gap-2 {{ request()->routeIs('admin.member*') ? 'text-slate-900 dark:text-white bg-slate-100 dark:bg-slate-800 font-bold' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                        <div class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('admin.member*') ? 'bg-primary' : 'bg-slate-300 dark:bg-slate-600' }}"></div>
                        Quản lý thành viên
                    </a>
                </div>
            </div>
        @endcan

        @can('voucher.view')
            <a class="{{ request()->routeIs('admin.vouchers.*') ? 'bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-white font-bold' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 font-medium' }} flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors"
                href="{{ route('admin.vouchers.index') }}">
                <span class="material-symbols-outlined">confirmation_number</span>
                <span>Voucher</span>
            </a>
        @endcan

        {{-- Đánh giá sản phẩm --}}
        @can('comment.view')
            <a class="{{ request()->routeIs('admin.reviews.*') ? 'bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-white font-bold' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 font-medium' }} flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors relative"
                href="{{ route('admin.reviews.index') }}">
                <span class="material-symbols-outlined">rate_review</span>
                <span>Đánh giá</span>
                @php $pendingReviews = \App\Models\Review::where('status', 0)->count(); @endphp
                @if($pendingReviews > 0)
                <span class="ml-auto inline-flex items-center justify-center min-w-[20px] h-5 px-1.5 rounded-full bg-amber-500 text-white text-[10px] font-bold">
                    {{ $pendingReviews > 99 ? '99+' : $pendingReviews }}
                </span>
                @endif
            </a>
        @endcan

        {{-- Hỏi đáp sản phẩm --}}
        @can('comment.view')
            <a class="{{ request()->routeIs('admin.comments.*') ? 'bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-white font-bold' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 font-medium' }} flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors relative"
                href="{{ route('admin.comments.index') }}">
                <span class="material-symbols-outlined">forum</span>
                <span>Hỏi đáp</span>
                @php $pendingComments = \App\Models\ProductComment::where('status', 1)->whereNull('parent_id')->count(); @endphp
            </a>
        @endcan

        @can('posts.view')
            <a class="{{ request()->routeIs('admin.posts.*') ? 'bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-white font-bold' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 font-medium' }} flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors"
                href="{{ route('admin.posts.index') }}">
                <span class="material-symbols-outlined">post</span>
                <span>Bài viết</span>
            </a>
        @endcan

        @can('banner.view')
            <a class="{{ request()->routeIs('admin.banners.*') ? 'bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-white font-bold' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 font-medium' }} flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors"
                href="{{ route('admin.banners.index') }}">
                <span class="material-symbols-outlined">ad_units</span>
                <span>Banner</span>
            </a>
        @endcan

        @can('wallet.view')
            @php
                $pendingWithdrawals = \App\Models\WithdrawalRequest::where('status', 'pending')->count();
                $isWalletGroupActive = request()->routeIs('admin.wallet.*', 'admin.withdrawals.*');
            @endphp
            <div class="mb-1">
                <button onclick="toggleSidebarMenu('menu-wallet', 'icon-wallet')"
                    class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg transition-colors {{ $isWalletGroupActive ? 'bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-white font-bold' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 font-medium' }}">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined">account_balance_wallet</span>
                        <span>Quản lý ví</span>
                        @if($pendingWithdrawals > 0)
                            <span class="inline-flex items-center justify-center min-w-[20px] h-5 px-1.5 rounded-full bg-red-500 text-white text-[10px] font-bold">
                                {{ $pendingWithdrawals > 99 ? '99+' : $pendingWithdrawals }}
                            </span>
                        @endif
                    </div>
                    <span id="icon-wallet"
                        class="material-symbols-outlined text-[18px] transition-transform duration-300 {{ $isWalletGroupActive ? 'rotate-180' : '' }}">
                        expand_more
                    </span>
                </button>

                <div id="menu-wallet"
                    class="overflow-hidden transition-all duration-300 ease-in-out {{ $isWalletGroupActive ? 'max-h-96 opacity-100 mt-1' : 'max-h-0 opacity-0' }} flex flex-col space-y-1">

                    <a href="{{ route('admin.wallet.index') }}"
                        class="pl-11 pr-3 py-2 rounded-lg text-sm font-medium transition-colors flex items-center gap-2 {{ request()->routeIs('admin.wallet.*') ? 'text-slate-900 dark:text-white bg-slate-100 dark:bg-slate-800 font-bold' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                        <div class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('admin.wallet.*') ? 'bg-primary' : 'bg-slate-300 dark:bg-slate-600' }}"></div>
                        Danh sách ví
                    </a>

                    <a href="{{ route('admin.withdrawals.index') }}"
                        class="pl-11 pr-3 py-2 rounded-lg text-sm font-medium transition-colors flex items-center gap-2 {{ request()->routeIs('admin.withdrawals.*') ? 'text-slate-900 dark:text-white bg-slate-100 dark:bg-slate-800 font-bold' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                        <div class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('admin.withdrawals.*') ? 'bg-primary' : 'bg-slate-300 dark:bg-slate-600' }}"></div>
                        <span>Yêu cầu rút tiền</span>
                        @if($pendingWithdrawals > 0)
                            <span class="ml-auto inline-flex items-center justify-center min-w-[20px] h-5 px-1.5 rounded-full bg-red-500 text-white text-[10px] font-bold">
                                {{ $pendingWithdrawals > 99 ? '99+' : $pendingWithdrawals }}
                            </span>
                        @endif
                    </a>
                </div>
            </div>
        @endcan

        @can('point.view')
            <a class="{{ request()->routeIs('admin.points.*') ? 'bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-white font-bold' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 font-medium' }} flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors"
                href="{{ route('admin.points.index') }}">
                <span class="material-symbols-outlined">stars</span>
                <span>Điểm thưởng</span>
            </a>
        @endcan

        @can('settings.view')
            <a class="{{ request()->routeIs('admin.chatbot-faqs.*') ? 'bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-white font-bold' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 font-medium' }} flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors"
                href="{{ route('admin.chatbot-faqs.index') }}">
                <span class="material-symbols-outlined">smart_toy</span>
                <span>Cài đặt Chatbot</span>
            </a>
        @endcan

        @can('support.view')
            <a class="{{ request()->routeIs('admin.tickets.*') ? 'bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-white font-bold' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 font-medium' }} flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors"
                href="{{ route('admin.tickets.index') }}">
                <span class="material-symbols-outlined">support_agent</span>
                <span>Hỗ trợ KH (Tickets)</span>
            </a>
        @endcan

        @can('settings.view')
            <div class="pt-4 mt-4 border-t border-slate-100 dark:border-slate-800">
                <a class="{{ request()->routeIs('admin.settings.*') ? 'bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-white font-bold' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 font-medium' }} flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors"
                    href="{{ route('admin.settings.index') }}">
                    <span class="material-symbols-outlined">settings</span>
                    <span>Cài đặt hệ thống</span>
                </a>
            </div>
        @endcan
    </nav>

    @php
        $avatarUrl =
            auth()->check() && auth()->user()->avatar
                ? auth()->user()->avatar
                : 'https://lh3.googleusercontent.com/aida-public/AB6AXuCQ9FLwed6hUAodxd9ykvBX9jnJPa0SIZOAFTt7JD5S5S8LXWLFY62U-5aeNRvaZQetgkhn0Y2YgXmLc89xuKY4atiMN4hOXt6_aM2ursKgGi8pl6Gigoe6gbYZw7-1MfbjHkiROQCGnnfsRHNqbFp0QA_5PHl55Z81GnnMVM0tKXWUQDVpKrueckovvrx3oJwLl0Z1RvjLR5tvPWPMlZX24Up9_TbdPxlcAdiZW0lhBSt-Iyb0xrrtvxktfM33K4G9JbPO05fOiBwn';
    @endphp
    <div class="p-4 bg-slate-50 dark:bg-slate-900 m-4 rounded-xl border border-slate-100 dark:border-slate-800">
        <div class="flex items-center gap-3 w-full">
            @auth
                {{-- 1. Ảnh Avatar --}}
                <a href="{{ route('admin.users.show', Auth::user()->id) }}">
                    <div class="w-10 h-10 rounded-full bg-slate-300 overflow-hidden shrink-0"
                        data-alt="Avatar của {{ Auth::user()->name }}"
                        style="background-image: url('{{ Auth::user()->avatar ? asset('storage/' . Auth::user()->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) }}'); background-size: cover; background-position: center;">
                    </div>
                </a>

                {{-- 2. Thông tin User --}}
                @php
                    $sidebarRoleValue = Auth::user()->role;
                    $sidebarRoleName = is_object($sidebarRoleValue)
                        ? ($sidebarRoleValue->name ?? $sidebarRoleValue->name_role ?? null)
                        : $sidebarRoleValue;

                    $sidebarRoleLabel = match ($sidebarRoleName) {
                        'admin' => 'Quản trị viên',
                        'staff' => 'Nhân viên',
                        default => 'Người dùng',
                    };
                @endphp
                <div class="overflow-hidden flex-1">
                    <p class="text-sm font-semibold truncate text-slate-800 dark:text-white">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400 truncate">{{ $sidebarRoleLabel }}</p>
                </div>

                {{-- 3. Nút Đăng xuất --}}
                <a href="{{ route('logout') }}"
                    class="ml-auto flex items-center justify-center w-8 h-8 rounded-full text-slate-400 transition-colors hover:bg-red-50 hover:text-red-500"
                    title="Đăng xuất">
                    <span class="material-symbols-outlined" style="font-size: 20px;">logout</span>
                </a>
            @endauth
        </div>
    </div>
</aside>

{{-- Overlay backdrop (mobile only) --}}
<div id="sidebar-overlay" class="fixed inset-0 bg-black/50 z-40 hidden lg:hidden" onclick="closeSidebar()"></div>
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
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Toggle đơn hàng
        const btnOrders = document.getElementById("btn-toggle-orders");
        const menuOrders = document.getElementById("menu-orders-sub");
        const arrowOrders = document.getElementById("icon-arrow-orders");

        if (btnOrders && menuOrders) {
            btnOrders.addEventListener("click", function() {
                menuOrders.classList.toggle("hidden");
                if (arrowOrders) arrowOrders.classList.toggle("rotate-180");
            });
        }
    });
</script>

<script>
    function openSidebar() {
        document.getElementById('admin-sidebar').classList.remove('-translate-x-full');
        document.getElementById('sidebar-overlay').classList.remove('hidden');
    }
    function closeSidebar() {
        document.getElementById('admin-sidebar').classList.add('-translate-x-full');
        document.getElementById('sidebar-overlay').classList.add('hidden');
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
