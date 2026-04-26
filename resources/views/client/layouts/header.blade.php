@php
    $topBarSetting = \App\Models\Setting::where('key', 'header_top_bar')->first();
    $topBarMessages = [];
    if ($topBarSetting && $topBarSetting->value) {
        $val = $topBarSetting->value;
        if (is_array($val)) {
            $topBarMessages = array_filter($val, fn($v) => trim($v) !== '');
        } elseif (is_string($val) && trim($val) !== '') {
            $topBarMessages = [$val];
        }
    }
@endphp

@if(count($topBarMessages) > 0)
<div id="top-announcement-bar" class="bg-gradient-to-r from-[#181611] via-[#2a2518] to-[#181611] text-white text-center text-xs font-semibold py-2 px-10 relative overflow-hidden">
    <div class="max-w-[1440px] mx-auto flex items-center justify-center gap-3">
        <span class="material-symbols-outlined text-primary text-[16px] animate-pulse shrink-0">campaign</span>
        <div id="top-bar-text" class="transition-all duration-500">
            {{ $topBarMessages[0] }}
        </div>
        <button onclick="document.getElementById('top-announcement-bar').remove()" class="absolute right-3 top-1/2 -translate-y-1/2 text-white/40 hover:text-white transition-colors">
            <span class="material-symbols-outlined text-[16px]">close</span>
        </button>
    </div>
</div>

@if(count($topBarMessages) > 1)
<script>
document.addEventListener('DOMContentLoaded', function() {
    const messages = @json(array_values($topBarMessages));
    const el = document.getElementById('top-bar-text');
    if (!el || messages.length <= 1) return;
    let idx = 0;
    setInterval(() => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(-8px)';
        setTimeout(() => {
            idx = (idx + 1) % messages.length;
            el.textContent = messages[idx];
            el.style.transform = 'translateY(8px)';
            requestAnimationFrame(() => {
                el.style.opacity = '1';
                el.style.transform = 'translateY(0)';
            });
        }, 300);
    }, 4000);
});
</script>
@endif
@endif

<header
    class="sticky top-0 z-50 bg-white/80 dark:bg-background-dark/80 backdrop-blur-md border-b border-solid border-[#f5f3f0] dark:border-white/10 px-4 md:px-10 lg:px-20 py-3">
    <div class="max-w-[1440px] mx-auto flex items-center justify-between gap-4">

        <a href="{{ route('home') }}" class="flex items-center gap-3 group">
            @if(isset($site_settings['site_logo']) && $site_settings['site_logo']->value)
                <img src="{{ asset($site_settings['site_logo']->value) }}" alt="{{ $site_settings['site_name']->value ?? 'Bee Phone' }}" class="h-10 w-auto object-contain transition-transform group-hover:scale-105">
            @else
                <div class="size-10 bg-primary rounded-xl flex items-center justify-center text-black group-hover:scale-105 transition-transform shadow-lg shadow-primary/20">
                    <span class="material-symbols-outlined text-2xl">rocket_launch</span>
                </div>
            @endif
            <div class="flex flex-col">
                <h1 class="text-xl font-bold leading-none tracking-tighter text-slate-800 dark:text-white group-hover:text-primary transition-colors">
                    {{ $site_settings['site_name']->value ?? 'Bee Phone' }}
                </h1>
                <span class="text-[10px] font-bold text-primary uppercase tracking-[0.2em] leading-none mt-1">Hệ thống bán lẻ</span>
            </div>
        </a>

        @php
            $headerMenuSetting = \App\Models\Setting::where('key', 'header_menu')->first();
            $headerMenuItems = $headerMenuSetting ? $headerMenuSetting->value : [];
            if (is_string($headerMenuItems)) {
                $headerMenuItems = json_decode($headerMenuItems, true);
            }

            // Lấy hiển thị số lượng giỏ hàng hiện tại
            $currentCartCount = 0;
            if (Auth::check()) {
                $cart = \App\Models\Cart::where('user_id', Auth::id())->first();
            } else {
                if (!\Illuminate\Support\Facades\Session::isStarted()) {
                    \Illuminate\Support\Facades\Session::start();
                }
                $cart = \App\Models\Cart::where('session_id', \Illuminate\Support\Facades\Session::getId())->first();
            }
            if (!empty($cart)) {
                $currentCartCount = \App\Models\CartItem::where('cart_id', $cart->id)->sum('quantity');
            }
        @endphp

        <nav class="hidden lg:flex items-center gap-8">
            @if(!empty($headerMenuItems) && is_array($headerMenuItems))
                @foreach($headerMenuItems as $item)
                    @include('client.layouts.menu-item', ['item' => $item, 'level' => 0])
                @endforeach
            @else
                <a class="text-sm font-medium hover:text-primary transition-colors"
                    href="{{ route('client.products.index') }}">Tất cả SP</a>
                <a class="text-sm font-medium hover:text-primary transition-colors"
                    href="{{ route('client.products.index', ['category' => 'dien-thoai']) }}">Điện thoại</a>
                <a class="text-sm font-medium hover:text-primary transition-colors"
                    href="{{ route('client.products.index', ['category' => 'am-thanh']) }}">Âm thanh</a>
                <a class="text-sm font-medium hover:text-primary transition-colors" href="{{ route('vouchers') }}">Khuyến
                    mãi</a>
                <a class="text-sm font-medium hover:text-primary transition-colors"
                    href="{{ route('client.posts.index') }}">Tin tức & Bài viết</a>
                <a class="text-sm font-medium hover:text-primary transition-colors"
                    href="{{ route('client.tickets.index') }}">Hỗ trợ</a>
            @endif
        </nav>

        <div class="flex flex-1 justify-end items-center gap-4 max-w-xl">
            <form action="{{ route('client.products.index') }}" method="GET"
                class="relative w-full max-w-md hidden sm:block" id="global-search-form">
                <button type="submit"
                    class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-primary flex items-center">
                    <span class="material-symbols-outlined">search</span>
                </button>
                <input name="search" value="{{ request('search') }}"
                    class="w-full h-10 pl-10 pr-4 rounded-lg border-none bg-[#f5f3f0] dark:bg-white/5 focus:ring-2 focus:ring-primary text-sm outline-none"
                    id="global-search-input" autocomplete="off"
                    placeholder="Tìm kiếm sản phẩm..." type="text" />

                <div id="global-search-dropdown"
                    class="hidden absolute top-12 right-0 left-auto w-[640px] max-w-[calc(100vw-1rem)] bg-white dark:bg-[#221e10] border border-[#e6e3db] dark:border-white/10 rounded-xl shadow-xl overflow-hidden z-[70]">
                    <div class="grid grid-cols-1 md:grid-cols-2">
                        <div class="p-3 md:p-4 border-r border-[#e6e3db] dark:border-white/10">
                            <h4 class="text-base font-bold mb-2 text-[#181611] dark:text-white">Xu hướng tìm kiếm</h4>
                            <div id="search-trending-list" class="space-y-1"></div>
                        </div>
                        <div class="p-3 md:p-4">
                            <h4 class="text-base font-bold mb-2 text-[#181611] dark:text-white">Sản phẩm bán chạy</h4>
                            <div id="search-bestseller-list" class="space-y-1.5"></div>
                        </div>
                    </div>
                </div>
            </form>

            <div class="flex items-center gap-2">

                @auth
                    <div class="relative group mr-1" id="client-notification-wrapper">
                        <button id="client-bell-btn"
                            class="flex items-center justify-center rounded-lg h-10 w-10 bg-[#f5f3f0] dark:bg-white/5 hover:bg-primary transition-colors focus:outline-none relative">
                            <span
                                class="material-symbols-outlined text-[#181611] dark:text-white group-hover:text-black">notifications</span>
                            <span id="client-bell-count"
                                class="absolute -top-1 -right-1 bg-red-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full leading-none hidden">0</span>
                        </button>

                        <div id="client-bell-dropdown"
                            class="absolute right-0 top-full mt-2 w-80 bg-white dark:bg-[#221e10] border border-gray-100 dark:border-white/10 rounded-xl shadow-lg hidden overflow-hidden transform transition-all origin-top-right z-50">
                            <div
                                class="p-3 border-b border-gray-100 dark:border-white/10 bg-gray-50 dark:bg-white/5 font-bold text-[#181611] dark:text-white flex items-center justify-between">
                                <span>Thông báo của bạn</span>
                            </div>
                            <div id="client-bell-list" class="max-h-80 overflow-y-auto no-scrollbar">
                                <div class="p-8 text-center text-sm text-gray-400">Đang tải...</div>
                            </div>
                            <div
                                class="p-2 text-center border-t border-gray-100 dark:border-white/10 bg-gray-50 dark:bg-white/5">
                                <a href="{{ route('client.notifications.index') }}"
                                    class="text-xs font-semibold text-primary hover:underline">Xem tất cả</a>
                            </div>
                        </div>
                    </div>
                    <div class="relative group">
                        <button
                            class="flex items-center justify-center rounded-lg h-10 w-10 bg-[#f5f3f0] dark:bg-white/5 hover:bg-primary transition-colors overflow-hidden">
                            @if (Auth::user()->avatar)
                                <div class="w-10 h-10 rounded-full bg-slate-300 overflow-hidden shrink-0"
                                    data-alt="Avatar của {{ Auth::user()->name }}"
                                    style="background-image: url('{{ Auth::user()->avatar ? asset('storage/' . Auth::user()->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) }}'); background-size: cover; background-position: center;">
                                </div>
                            @else
                                <span
                                    class="font-bold text-lg text-[#181611] dark:text-white group-hover:text-black uppercase">
                                    {{ Str::substr(Auth::user()->name, 0, 1) }}
                                </span>
                            @endif
                        </button>

                        <div
                            class="absolute right-0 top-full pt-2 w-56 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 transform origin-top-right z-50">
                            <div
                                class="bg-white dark:bg-[#221e10] border border-gray-100 dark:border-white/10 rounded-xl shadow-lg overflow-hidden">
                                <div class="p-4 border-b border-gray-100 dark:border-white/10 bg-gray-50 dark:bg-white/5">
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Xin chào,</p>
                                    <p class="text-sm font-bold text-[#181611] dark:text-white truncate">
                                        {{ Auth::user()->name }}</p>
                                </div>

                                @php
                                    $authRoleValue = Auth::user()->role ?? null;
                                    $authRoleName = is_object($authRoleValue)
                                        ? $authRoleValue->name
                                        : (string) ($authRoleValue ?? '');
                                @endphp
                                <div class="p-2 space-y-1">
                                    @if ($authRoleName !== 'user')
                                        <a href="{{ route('admin.dashboard') }}"
                                            class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-[#f5f3f0] dark:hover:bg-white/5 hover:text-primary rounded-lg transition-colors">
                                            <span class="material-symbols-outlined text-[20px]">admin_panel_settings</span>
                                            Quản trị viên
                                        </a>
                                        <hr class="border-gray-100 dark:border-white/10 my-1">
                                    @endif

                                    <a href="{{ route('profile.index') }}"
                                        class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-[#f5f3f0] dark:hover:bg-white/5 hover:text-primary rounded-lg transition-colors">
                                        <span class="material-symbols-outlined text-[20px]">manage_accounts</span> Tài khoản
                                        của tôi
                                    </a>
                                    <a href="#"
                                        class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-[#f5f3f0] dark:hover:bg-white/5 hover:text-primary rounded-lg transition-colors">
                                        <span class="material-symbols-outlined text-[20px]">local_mall</span> Đơn mua
                                    </a>

                                    <hr class="border-gray-100 dark:border-white/10 my-1">

                                    <a href="{{ route('logout') }}"
                                        class="flex items-center gap-3 px-3 py-2.5 text-sm font-bold text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 rounded-lg transition-colors">
                                        <span class="material-symbols-outlined text-[20px]">logout</span> Đăng xuất
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}"
                        class="flex items-center justify-center rounded-lg h-10 w-10 bg-[#f5f3f0] dark:bg-white/5 hover:bg-primary transition-colors group"
                        title="Đăng nhập">
                        <span
                            class="material-symbols-outlined text-[#181611] dark:text-white group-hover:text-black">login</span>
                    </a>
                @endauth

                <a href="{{ route('client.cart.index') }}"
                    class="flex items-center justify-center rounded-lg h-10 w-10 bg-[#f5f3f0] dark:bg-white/5 hover:bg-primary transition-colors group relative">
                    <span
                        class="material-symbols-outlined text-[#181611] dark:text-white group-hover:text-black">shopping_cart</span>
                    <span
                        class="absolute -top-1 -right-1 bg-primary text-[10px] font-bold px-1.5 py-0.5 rounded-full text-black leading-none">{{ $currentCartCount }}</span>
                </a>

            </div>
        </div>
    </div>
</header>

<script>
    (() => {
        const input = document.getElementById('global-search-input');
        const dropdown = document.getElementById('global-search-dropdown');
        const trendingList = document.getElementById('search-trending-list');
        const bestSellerList = document.getElementById('search-bestseller-list');
        const form = document.getElementById('global-search-form');
        if (!input || !dropdown || !trendingList || !bestSellerList || !form) return;

        let debounceTimer = null;

        const escapeHtml = (str) => (str || '')
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');

        const formatPrice = (value) => {
            const num = Number(value || 0);
            return num.toLocaleString('vi-VN') + 'đ';
        };

        const renderTrending = (trending, suggestions) => {
            const rows = (suggestions?.length ? suggestions.map(i => i.name) : trending || []).slice(0, 6);
            if (!rows.length) {
                trendingList.innerHTML = '<p class="text-sm text-[#8a8060]">Chưa có dữ liệu xu hướng.</p>';
                return;
            }
            trendingList.innerHTML = rows.map(name => `
                <a href="${form.action}?search=${encodeURIComponent(name)}" class="flex items-center gap-2 p-1.5 rounded-md hover:bg-[#f5f3f0] dark:hover:bg-white/5 transition-colors">
                    <span class="material-symbols-outlined text-[#8a8060] text-[18px]">search</span>
                    <span class="text-[#181611] dark:text-white text-sm leading-5 line-clamp-1">${escapeHtml(name)}</span>
                </a>
            `).join('');
        };

        const renderBestSellers = (items) => {
            if (!items?.length) {
                bestSellerList.innerHTML = '<p class="text-sm text-[#8a8060]">Chưa có dữ liệu bán chạy.</p>';
                return;
            }
            bestSellerList.innerHTML = items.map(item => `
                <a href="${item.url}" class="flex items-center gap-2 p-1.5 rounded-md hover:bg-[#f5f3f0] dark:hover:bg-white/5 transition-colors">
                    <div class="w-10 h-10 rounded-md bg-[#f5f3f0] dark:bg-white/5 bg-center bg-cover shrink-0" ${item.thumbnail ? `style="background-image:url('${item.thumbnail}')"` : ''}></div>
                    <div class="min-w-0">
                        <p class="text-[#181611] dark:text-white text-sm leading-5 line-clamp-2">${escapeHtml(item.name)}</p>
                        <p class="text-red-500 font-bold text-base leading-tight">${formatPrice(item.price)}</p>
                    </div>
                </a>
            `).join('');
        };

        const loadSuggestions = async () => {
            try {
                const keyword = input.value.trim();
                const url = `{{ route('client.search.suggestions') }}?q=${encodeURIComponent(keyword)}`;
                const res = await fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                if (!res.ok) return;
                const data = await res.json();
                renderTrending(data.trending, data.suggestions);
                renderBestSellers(data.best_sellers);
                dropdown.classList.remove('hidden');
            } catch (e) {
                // Keep header usable if API is unavailable.
            }
        };

        input.addEventListener('focus', loadSuggestions);
        input.addEventListener('input', () => {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(loadSuggestions, 220);
        });

        document.addEventListener('click', (event) => {
            if (!form.contains(event.target)) {
                dropdown.classList.add('hidden');
            }
        });
    })();
</script>
