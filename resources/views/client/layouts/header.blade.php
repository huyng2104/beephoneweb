<header
    class="sticky top-0 z-50 bg-white/80 dark:bg-background-dark/80 backdrop-blur-md border-b border-solid border-[#f5f3f0] dark:border-white/10 px-4 md:px-10 lg:px-20 py-3">
    <div class="max-w-[1440px] mx-auto flex items-center justify-between gap-4">
        
        <a href="{{ route('home') }}" class="flex items-center gap-2 group">
            <div class="size-8 bg-primary rounded-lg flex items-center justify-center text-black group-hover:scale-105 transition-transform">
                <span class="material-symbols-outlined">rocket_launch</span>
            </div>
            <h2 class="text-xl font-bold leading-tight tracking-tight hidden md:block group-hover:text-primary transition-colors">Bee Phone</h2>
        </a>

        <nav class="hidden lg:flex items-center gap-8">
            <a class="text-sm font-medium hover:text-primary transition-colors" href="{{ route('client.products.index') }}">Tất cả SP</a>
            <a class="text-sm font-medium hover:text-primary transition-colors" href="{{ route('client.products.index', ['category' => 'dien-thoai']) }}">Điện thoại</a>
            <a class="text-sm font-medium hover:text-primary transition-colors" href="{{ route('client.products.index', ['category' => 'am-thanh']) }}">Âm thanh</a>
            <a class="text-sm font-medium hover:text-primary transition-colors" href="{{ route('client.products.index') }}">Khuyến mãi</a>
        </nav>

        <div class="flex flex-1 justify-end items-center gap-4 max-w-xl">
            
            <form action="{{ route('client.products.index') }}" method="GET" class="relative w-full max-w-md hidden sm:block">
                <button type="submit" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-primary flex items-center">
                    <span class="material-symbols-outlined">search</span>
                </button>
                <input name="search" value="{{ request('search') }}"
                    class="w-full h-10 pl-10 pr-4 rounded-lg border-none bg-[#f5f3f0] dark:bg-white/5 focus:ring-2 focus:ring-primary text-sm outline-none"
                    placeholder="Tìm kiếm sản phẩm..." type="text" />
            </form>

            <div class="flex items-center gap-2">
                
                @if(Auth::check())
                    <div class="relative group">
                        <button class="flex items-center justify-center rounded-lg h-10 w-10 bg-[#f5f3f0] dark:bg-white/5 hover:bg-primary transition-colors">
                            <span class="material-symbols-outlined text-[#181611] dark:text-white group-hover:text-black">person</span>
                        </button>
                        
                        <div class="absolute right-0 top-full pt-2 w-56 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 transform origin-top-right z-50">
                            <div class="bg-white dark:bg-[#221e10] border border-gray-100 dark:border-white/10 rounded-xl shadow-lg overflow-hidden">
                                <div class="p-4 border-b border-gray-100 dark:border-white/10 bg-gray-50 dark:bg-white/5">
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Xin chào,</p>
                                    <p class="text-sm font-bold text-[#181611] dark:text-white truncate">{{ Auth::user()->name }}</p>
                                </div>
                                
                                <div class="p-2 space-y-1">
                                    @if(Auth::user()->role === 'admin' || Auth::user()->role === 'staff')
                                        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-[#f5f3f0] dark:hover:bg-white/5 hover:text-primary rounded-lg transition-colors">
                                            <span class="material-symbols-outlined text-[20px]">admin_panel_settings</span> Quản trị viên
                                        </a>
                                        <hr class="border-gray-100 dark:border-white/10 my-1">
                                    @endif
                                    
                                    <a href="#" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-[#f5f3f0] dark:hover:bg-white/5 hover:text-primary rounded-lg transition-colors">
                                        <span class="material-symbols-outlined text-[20px]">manage_accounts</span> Tài khoản của tôi
                                    </a>
                                    <a href="#" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-[#f5f3f0] dark:hover:bg-white/5 hover:text-primary rounded-lg transition-colors">
                                        <span class="material-symbols-outlined text-[20px]">local_mall</span> Đơn mua
                                    </a>
                                    
                                    <hr class="border-gray-100 dark:border-white/10 my-1">
                                    
                                    <a href="{{ route('logout') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-bold text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 rounded-lg transition-colors">
                                        <span class="material-symbols-outlined text-[20px]">logout</span> Đăng xuất
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}"
                        class="flex items-center justify-center rounded-lg h-10 w-10 bg-[#f5f3f0] dark:bg-white/5 hover:bg-primary transition-colors group" title="Đăng nhập">
                        <span class="material-symbols-outlined text-[#181611] dark:text-white group-hover:text-black">login</span>
                    </a>
                @endif

                <a href="#"
                    class="flex items-center justify-center rounded-lg h-10 w-10 bg-[#f5f3f0] dark:bg-white/5 hover:bg-primary transition-colors group relative">
                    <span class="material-symbols-outlined text-[#181611] dark:text-white group-hover:text-black">shopping_cart</span>
                    <span class="absolute -top-1 -right-1 bg-primary text-[10px] font-bold px-1.5 py-0.5 rounded-full text-black leading-none">3</span>
                </a>
                
            </div>
        </div>
    </div>
</header>