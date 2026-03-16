<header class="sticky top-0 z-50 bg-white border-b border-bee-gray-border" data-purpose="navigation-header">
    <div class="container mx-auto px-4 h-20 flex items-center justify-between">
        
        <a href="{{ route('home') }}" class="flex-shrink-0 flex items-center gap-2 hover:opacity-80 transition-opacity" data-purpose="logo">
            <div class="w-10 h-10 bg-bee-yellow rounded-lg flex items-center justify-center font-bold text-bee-dark text-xl">B</div>
            <span class="text-xl font-bold tracking-tight text-bee-dark">BEE PHONE</span>
        </a>
        
        <nav class="hidden md:flex items-center space-x-8" data-purpose="main-menu">
            <a class="text-sm font-medium hover:text-bee-yellow smooth-transition" href="{{ route('client.products.index') }}">Điện thoại</a>
            
            <a class="text-sm font-medium hover:text-bee-yellow smooth-transition" href="{{ route('client.products.index', ['category' => 2]) }}">Laptop</a>
            
            <a class="text-sm font-medium hover:text-bee-yellow smooth-transition" href="{{ route('client.products.index', ['category' => 3]) }}">Phụ kiện</a>
            
            <a class="text-sm font-medium hover:text-bee-yellow smooth-transition" href="#">Tin tức</a>
            <a class="text-sm font-medium hover:text-bee-yellow smooth-transition" href="#">Liên hệ</a>
        </nav>
        
        <div class="flex items-center gap-6" data-purpose="header-actions">
            <form action="{{ route('client.products.index') }}" method="GET" class="hidden lg:flex items-center bg-bee-gray-light border border-bee-gray-border rounded-full px-4 py-2 w-64 focus-within:border-bee-yellow transition-colors">
                <button type="submit">
                    <svg class="w-4 h-4 text-gray-400 hover:text-bee-yellow" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path></svg>
                </button>
                <input name="search" class="bg-transparent border-none focus:ring-0 text-sm w-full placeholder-gray-400" placeholder="Tìm kiếm sản phẩm..." type="text" value="{{ request('search') }}"/>
            </form>
            
           <div class="flex items-center gap-5">
                
                @guest
                    <a href="{{ route('login') }}" aria-label="Tài khoản" class="relative flex items-center gap-2 hover:text-bee-yellow smooth-transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"></path></svg>
                        <span class="text-sm font-medium hidden md:block">Đăng nhập</span>
                    </a>
                @endguest

                @auth
                    <div class="relative group">
                        <button aria-label="Tài khoản" class="relative flex items-center gap-2 hover:text-bee-yellow smooth-transition py-2">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"></path></svg>
                            <span class="text-sm font-bold truncate max-w-[120px]">{{ Auth::user()->name }}</span>
                        </button>
                        
                        <div class="absolute right-0 mt-1 w-48 bg-white rounded-xl shadow-xl border border-gray-100 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 z-50">
                            <div class="p-2 space-y-1">
                                @if(Auth::user()->role === 'admin' || Auth::user()->role === 'staff')
                                    <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-bee-yellow hover:text-bee-dark rounded-lg font-medium transition-colors">
                                        Trang quản trị
                                    </a>
                                @endif
                                
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-bee-yellow hover:text-bee-dark rounded-lg font-medium transition-colors">
                                    Đơn hàng của tôi
                                </a>
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-bee-yellow hover:text-bee-dark rounded-lg font-medium transition-colors">
                                    Thông tin tài khoản
                                </a>
                                
                                <hr class="my-1 border-gray-100">
                                
                                <a href="{{ route('logout') }}" class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50 rounded-lg font-bold transition-colors">
                                    Đăng xuất
                                </a>
                            </div>
                        </div>
                    </div>
                @endauth
                <a href="#" aria-label="Giỏ hàng" class="relative hover:text-bee-yellow smooth-transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"></path></svg>
                    <span class="absolute -top-1 -right-1 bg-bee-yellow text-bee-dark text-[10px] font-bold w-4 h-4 rounded-full flex items-center justify-center">0</span>
                </a>
            </div>
                
                <a href="#" aria-label="Giỏ hàng" class="relative hover:text-bee-yellow smooth-transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"></path></svg>
                    <span class="absolute -top-1 -right-1 bg-bee-yellow text-bee-dark text-[10px] font-bold w-4 h-4 rounded-full flex items-center justify-center">0</span>
                </a>
            </div>
        </div>
    </div>
</header>