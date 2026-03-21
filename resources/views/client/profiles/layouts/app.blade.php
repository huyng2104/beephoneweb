@extends('client.layouts.app')

@section('title', 'Thông tin cá nhân')

@section('content')

    <main class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col lg:flex-row gap-8">
            <aside class="w-full lg:w-64 flex-shrink-0" data-purpose="sidebar-navigation">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-100">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-full overflow-hidden bg-gray-100">
                                <img alt="Avatar Sidebar"
                                    src="https://lh3.googleusercontent.com/aida-public/AB6AXuBlnd0uNBPq_YQXOSRidaH7U9gYhN8R2yfsAz2HMWPBYUfRaOKgYe8gFkpw9B0bkzlTtHyx7YIL4IU0on8pRSSzvTikoy2pnNRSp-ySM8oBnsKpJ4CVN-Nxk9pBPuAo3ENAWutWhILXbSjIOucMUPT_FqeULocWBUr7xsJ8b7qZuVPTLAZM5flME8aUo2XuUuRBmU6Y3LAfb5a1xcltgQMa2GRzk2lBsC8bf05AMlh6JSv9FjPIs8N3UxbRM9S9sCJAvvgg1KwJjpk" />
                            </div>
                            <div>
                                <p class="font-bold text-sm">{{ $user->name }}</p>
                                <p class="text-xs text-yellow-500 uppercase font-semibold">{{ $user->role->name_role }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <nav class="py-2">
                        <a class="flex items-center gap-3 px-6 py-3 text-sm transition {{ request()->routeIs('profile.index') ? 'border-l-4 border-amber-400 bg-amber-50 text-gray-900 font-semibold' : 'text-gray-600 hover:bg-gray-50' }}"
                            href="{{ route('profile.index') }}">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewbox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                                    stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                            </svg>
                            Hồ sơ cá nhân
                        </a>

                        <a class="flex items-center gap-3 px-6 py-3 text-sm transition {{ request()->routeIs('client.orders.*') ? 'border-l-4 border-amber-400 bg-amber-50 text-gray-900 font-semibold' : 'text-gray-600 hover:bg-gray-50' }}"
                            href="{{ route('client.orders.index') }}">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewbox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                            </svg>
                            Đơn hàng của tôi
                        </a>

                        <a class="flex items-center gap-3 px-6 py-3 text-sm transition text-gray-600 hover:bg-gray-50"
                            href="#">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewbox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"
                                    stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                            </svg>
                            Sản phẩm yêu thích
                        </a>

                        <a class="flex items-center gap-3 px-6 py-3 text-sm transition {{ request()->routeIs('profile.wallet') ? 'border-l-4 border-amber-400 bg-amber-50 text-gray-900 font-semibold' : 'text-gray-600 hover:bg-gray-50' }}"
                            href="{{ route('profile.wallet') }}">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewbox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"
                                    stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                            </svg>
                            Ví Bee Pay
                        </a>
                        <a class="flex items-center gap-3 px-6 py-3 text-sm transition {{ request()->routeIs('client.points.*') ? 'border-l-4 border-amber-400 bg-amber-50 text-gray-900 font-semibold' : 'text-gray-600 hover:bg-gray-50' }}"
                            href="{{ route('client.points.index') }}">
                            <span class="material-symbols-outlined text-[20px] {{ request()->routeIs('client.points.*') ? 'text-amber-500' : '' }}">
                                workspace_premium
                            </span>
                            Điểm Bee Point
                        </a>

                        <a class="flex items-center gap-3 px-6 py-3 text-sm transition text-gray-600 hover:bg-gray-50"
                            href="#">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewbox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"
                                    stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                            </svg>
                            Thông báo
                        </a>

                        <a class="flex items-center gap-3 px-6 py-3 text-sm transition text-gray-600 hover:bg-gray-50"
                            href="#">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewbox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 00-2 2zm10-10V7a4 4 0 00-8 0v4h8z"
                                    stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                            </svg>
                            Đổi mật khẩu
                        </a>

                        <div class="mt-4 pt-4 border-t border-gray-100">
                            <a class="flex items-center gap-3 px-6 py-3 text-sm text-red-500 hover:bg-red-50 transition font-medium"
                                href="#">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewbox="0 0 24 24"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"
                                        stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                                </svg>
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