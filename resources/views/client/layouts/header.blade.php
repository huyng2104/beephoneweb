<header
    class="sticky top-0 z-50 bg-white/80 dark:bg-background-dark/80 backdrop-blur-md border-b border-solid border-[#f5f3f0] dark:border-white/10 px-4 md:px-10 lg:px-20 py-3">
    <div class="max-w-[1440px] mx-auto flex items-center justify-between gap-4">

        <a href="{{ route('home') }}" class="flex items-center gap-2 group">
            <div
                class="size-8 bg-primary rounded-lg flex items-center justify-center text-black group-hover:scale-105 transition-transform">
                <span class="material-symbols-outlined">rocket_launch</span>
            </div>
            <h2
                class="text-xl font-bold leading-tight tracking-tight hidden md:block group-hover:text-primary transition-colors">
                Bee Phone</h2>
        </a>

        <nav class="hidden lg:flex items-center gap-8">
            <a class="text-sm font-medium hover:text-primary transition-colors"
                href="{{ route('client.products.index') }}">Tất cả SP</a>
            <a class="text-sm font-medium hover:text-primary transition-colors"
                href="{{ route('client.products.index', ['category' => 'dien-thoai']) }}">Điện thoại</a>
            <a class="text-sm font-medium hover:text-primary transition-colors"
                href="{{ route('client.products.index', ['category' => 'am-thanh']) }}">Âm thanh</a>
            <a class="text-sm font-medium hover:text-primary transition-colors"
                href="{{ route('vouchers') }}">Khuyến mãi</a>
            <a class="text-sm font-medium hover:text-primary transition-colors"
                href="{{ route('client.posts.index') }}">Tin tức & Bài viết</a>
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

                                <div class="p-2 space-y-1">
                                    @if (Auth::user()->role->name === 'admin' || Auth::user()->role->name === 'staff')
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
                        class="absolute -top-1 -right-1 bg-primary text-[10px] font-bold px-1.5 py-0.5 rounded-full text-black leading-none">0</span>
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
                // Silent fail to avoid breaking header interactions.
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
