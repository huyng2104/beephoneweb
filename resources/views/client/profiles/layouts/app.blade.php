@extends('client.layouts.app')

@section('title', 'Thông tin cá nhân')

@section('content')

    <main class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col lg:flex-row gap-8">
            <aside class="w-full lg:w-64 flex-shrink-0" data-purpose="sidebar-navigation">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-100">
                        <div class="flex items-center gap-3">

                            {{-- Phần Avatar --}}
                            <div class="w-12 h-12 rounded-full overflow-hidden flex-shrink-0 flex items-center justify-center shadow-sm">
                                @if (Auth::user()->avatar)
                                    <img alt="{{ Auth::user()->name }}" class="w-full h-full object-cover"
                                        src="{{ asset('storage/' . Auth::user()->avatar) }}" />
                                @else
                                    <div class="w-full h-full bg-[#f4c025] flex items-center justify-center text-black font-bold text-xl uppercase">
                                        {{ Str::substr(Auth::user()->name, 0, 1) }}
                                    </div>
                                @endif
                            </div>
                            
                            {{-- Phần Thông tin User (ĐÃ FIX SẠCH LỖI $user) --}}
                            <div>
                                <p class="font-bold text-[#181611] dark:text-white text-sm">{{ Auth::user()->name }}</p>
                                <p class="text-xs text-amber-500 uppercase font-semibold">{{ Auth::user()->role->name_role ?? 'Khách hàng' }}</p>
                            </div>

                        </div>
                    </div>

                    <nav class="py-2">
                        <a class="flex items-center gap-3 px-6 py-3 text-sm transition {{ request()->routeIs('profile.index') ? 'border-l-4 border-amber-400 bg-amber-50 text-gray-900 font-semibold' : 'text-gray-600 hover:bg-gray-50' }}"
                            href="{{ route('profile.index') }}">
                            <span class="material-symbols-outlined text-[20px]">person</span>
                            Hồ sơ cá nhân
                        </a>

                        <a class="flex items-center gap-3 px-6 py-3 text-sm transition {{ request()->routeIs('client.orders.*') ? 'border-l-4 border-amber-400 bg-amber-50 text-gray-900 font-semibold' : 'text-gray-600 hover:bg-gray-50' }}"
                            href="{{ route('client.orders.index') }}">
                            <span class="material-symbols-outlined text-[20px]">local_mall</span>
                            Đơn hàng của tôi
                        </a>
                        
                        <a class="flex items-center gap-3 px-6 py-3 text-sm transition {{ request()->routeIs('user.vouchers*') ? 'border-l-4 border-amber-400 bg-amber-50 text-gray-900 font-semibold' : 'text-gray-600 hover:bg-gray-50' }}"
                            href="{{ route('user.vouchers') }}">
                            <span class="material-symbols-outlined text-[20px]">confirmation_number</span>
                            Voucher đã lưu
                        </a>

                        <a class="flex items-center gap-3 px-6 py-3 text-sm transition {{ request()->routeIs('profile.wallet') ? 'border-l-4 border-amber-400 bg-amber-50 text-gray-900 font-semibold' : 'text-gray-600 hover:bg-gray-50' }}"
                            href="{{ route('profile.wallet') }}">
                            <span class="material-symbols-outlined text-[20px]">account_balance_wallet</span>
                            Ví Bee Pay
                        </a>
                        
                        <a class="flex items-center gap-3 px-6 py-3 text-sm transition {{ request()->routeIs('client.points.*') ? 'border-l-4 border-amber-400 bg-amber-50 text-gray-900 font-semibold' : 'text-gray-600 hover:bg-gray-50' }}"
                            href="{{ route('client.points.index') }}">
                            <span class="material-symbols-outlined text-[20px] {{ request()->routeIs('client.points.*') ? 'text-amber-500' : '' }}">workspace_premium</span>
                            Điểm Bee Point
                        </a>

                        <a class="flex items-center gap-3 px-6 py-3 text-sm transition {{ request()->routeIs('client.notifications.*') ? 'border-l-4 border-amber-400 bg-amber-50 text-gray-900 font-semibold' : 'text-gray-600 hover:bg-gray-50' }}"
                            href="{{ route('client.notifications.index') }}">
                            <span class="material-symbols-outlined text-[20px]">notifications</span>
                            Thông báo
                        </a>

                        <div class="mt-4 pt-4 border-t border-gray-100">
                            <a class="flex items-center gap-3 px-6 py-3 text-sm text-red-500 hover:bg-red-50 transition font-medium"
                                href="{{ route('logout') }}">
                                <span class="material-symbols-outlined text-[20px]">logout</span>
                                Đăng xuất
                            </a>
                        </div>
                    </nav>
                </div>
            </aside>
            
            @yield('profile_content')
            
        </div>
    </main>
@endsection