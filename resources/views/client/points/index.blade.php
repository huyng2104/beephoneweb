    @extends('client.profiles.layouts.app')

    @section('title', 'Bee Point - Tích điểm đổi quà')

    @section('profile_content')
    <style>
        /* Giữ lại config custom của bro cho trang này đẹp */
        .bg-surface-container-low { background-color: #f5f3f0; }
        .bg-surface-container-high { background-color: #f1e7d7; }
        .text-on-surface { color: #181611; }
        .bg-on-surface { background-color: #181611; }
        .text-on-primary { color: #000000; }

        .ai-sparkle {
            background: linear-gradient(90deg, #f4c025 0%, #ffffff 50%, #f4c025 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-size: 200% auto;
        }
    </style>

    <main class="pt-10 pb-20 max-w-7xl mx-auto px-6 min-h-screen">

        @if(session('success'))
            <div class="bg-green-100 text-green-700 p-4 rounded-xl mb-6 font-bold flex items-center gap-2">
                <span class="material-symbols-outlined">check_circle</span> {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 text-red-600 p-4 rounded-xl mb-6 font-bold flex items-center gap-2">
                <span class="material-symbols-outlined">error</span> {{ session('error') }}
            </div>
        @endif

        <header class="mb-12 mt-6">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
                <div>
                    <span class="text-primary font-bold tracking-widest text-xs uppercase mb-2 block">QUẢN LÝ ĐIỂM THƯỞNG</span>
                    <h1 class="text-4xl md:text-5xl font-bold tracking-tight text-on-surface">Xin chào, <span class="ai-sparkle">{{ $user->name }}!</span></h1>
                </div>
                <div class="flex gap-4">
                    <a href="{{ route('client.products.index') }}" class="flex items-center gap-2 bg-surface-container-low text-on-surface px-4 py-2 rounded-lg font-bold hover:bg-surface-container-high transition-colors">
                        <span class="material-symbols-outlined text-xl">shopping_cart</span>
                        Mua sắm ngay
                    </a>
                </div>
            </div>
        </header>

        <div class="grid grid-cols-1 md:grid-cols-12 gap-6">

            <section class="md:col-span-8 bg-on-surface text-white rounded-xl p-8 relative overflow-hidden shadow-2xl flex flex-col justify-between min-h-[320px]">
                <div class="absolute top-0 right-0 w-64 h-64 bg-primary/20 rounded-full blur-[100px] -mr-32 -mt-32"></div>
                <div class="relative z-10">
                    <div class="flex justify-between items-start mb-8">
                        <div>
                            <p class="text-white/60 font-medium mb-1">Số dư hiện tại</p>
                            <div class="flex items-baseline gap-2">
                                <span class="text-5xl font-bold text-primary">{{ number_format($user->total_points) }}</span>
                                <span class="text-xl font-bold">Bee Point</span>
                            </div>
                        </div>
                        <div class="bg-primary/10 border border-primary/20 px-4 py-2 rounded-full backdrop-blur-md">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-primary" style="font-variation-settings: 'FILL' 1;">workspace_premium</span>
                                <span class="font-bold text-primary">Thành viên</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="relative z-10 flex gap-4 mt-8">
                    <a href="{{ route('client.products.index') }}" class="flex-1 bg-primary text-on-primary text-center py-3 rounded-lg font-bold hover:scale-[1.02] transition-transform">Tích thêm điểm</a>
                    <a href="#cua-hang-doi-qua" class="flex-1 border border-white/20 text-center py-3 rounded-lg font-bold hover:bg-white/5 transition-colors">Đổi quà ngay</a>
                </div>
            </section>

            <section class="md:col-span-4 bg-white rounded-xl p-8 shadow-sm border border-zinc-100 flex flex-col">
                <h3 class="text-xl text-on-surface font-bold mb-6">Cách kiếm điểm</h3>
                <div class="space-y-6">
                    <div class="flex items-start gap-4 group">
                        <div class="w-12 h-12 rounded-lg bg-surface-container-low flex items-center justify-center shrink-0 group-hover:bg-primary transition-colors">
                            <span class="material-symbols-outlined text-on-surface group-hover:text-on-primary">shopping_bag</span>
                        </div>
                        <div>
                            <p class="font-bold text-on-surface">Mua sắm</p>
                            <p class="text-sm text-zinc-500">Nhận 1 BP cho mỗi {{ number_format($setting->earn_rate) }}đ chi tiêu tại cửa hàng.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4 group">
                        <div class="w-12 h-12 rounded-lg bg-surface-container-low flex items-center justify-center shrink-0 group-hover:bg-primary transition-colors">
                            <span class="material-symbols-outlined text-on-surface group-hover:text-on-primary">star</span>
                        </div>
                        <div>
                            <p class="font-bold text-on-surface">Đánh giá</p>
                            <p class="text-sm text-zinc-500">Nhận 1 BP cho mỗi đánh giá sản phẩm sau khi mua hàng.</p>
                        </div>
                    </div>
                </div>
            </section>

            <section id="cua-hang-doi-qua" class="md:col-span-12 mt-8">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @forelse($vouchers as $index => $voucher)
                    @php
                        // Tính điểm để đổi Voucher
                        $redeemRate = $setting->redeem_rate ?: 1000;
                        $pointCost = $voucher->discount_type === 'fixed' ? ceil($voucher->discount_value / $redeemRate) : ceil(($voucher->max_discount ?: 50000) / $redeemRate);
                        $bgColors = ['from-[#fef3c7] to-[#fde68a]', 'from-[#fef08a] to-[#facc15]', 'from-[#eab308] to-[#ca8a04]', 'from-[#fde047] to-[#eab308]'];
                        $bgClass = $bgColors[$index % count($bgColors)];
                    @endphp
                    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-sm flex flex-col hover:-translate-y-1 transition-transform duration-300">
                        <div class="h-28 bg-gradient-to-br {{ $bgClass }}"></div>
                        <div class="p-5 flex flex-col flex-grow bg-white">
                            <h4 class="font-bold text-[#181611] text-sm mb-1 leading-tight line-clamp-1">{{ $voucher->name }}</h4>
                            <p class="text-xs text-gray-500 mb-6 line-clamp-1">
                                @if($voucher->discount_type == 'fixed')
                                    Áp dụng cho đơn từ {{ number_format($voucher->min_order_value) }}đ
                                @else
                                    Tối đa {{ number_format($voucher->max_discount) }}đ cho mọi đơn
                                @endif
                            </p>

                            <div class="mt-auto flex items-center justify-between">
                                <span class="font-bold text-[#eab308] text-lg">{{ number_format($pointCost) }}đ</span>

                                @if($user->total_points >= $pointCost)
                                <form action="{{ route('client.points.redeem') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="voucher_id" value="{{ $voucher->id }}">
                                    <button type="submit" onclick="return confirm('Bạn có chắc muốn dùng {{ number_format($pointCost) }} điểm để đổi mã này không?')" class="bg-[#facc15] hover:bg-[#eab308] text-[#181611] px-5 py-1.5 rounded-full text-xs font-bold transition-colors">
                                        Đổi ngay
                                    </button>
                                </form>
                                @else
                                <button disabled class="bg-gray-100 text-gray-400 px-5 py-1.5 rounded-full text-xs font-bold cursor-not-allowed">
                                    Chưa đủ điểm
                                </button>
                                @endif
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="col-span-full p-8 text-center text-zinc-500 bg-white rounded-2xl border border-gray-100">
                        Hiện tại chưa có mã giảm giá nào để đổi. Bạn quay lại sau nhé!
                    </div>
                    @endforelse
                </div>
            </section>

            <section class="md:col-span-12 mt-12">
                <div class="flex justify-between items-center mb-6 px-2">
                    <h2 class="text-2xl font-bold text-[#181611]">Lịch sử điểm thưởng</h2>
                    <a href="#" class="text-[#eab308] hover:text-[#ca8a04] text-sm font-bold flex items-center transition-colors">
                        Xem tất cả <span class="material-symbols-outlined text-[16px] ml-1">arrow_forward</span>
                    </a>
                </div>

                <div class="bg-white rounded-2xl shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] border border-gray-100 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="bg-[#fafafa] border-b border-gray-100">
                                    <th class="px-8 py-4 font-semibold text-sm text-gray-500 w-[15%]">Ngày</th>
                                    <th class="px-8 py-4 font-semibold text-sm text-gray-500">Nội dung giao dịch</th>
                                    <th class="px-8 py-4 font-semibold text-sm text-gray-500 text-right w-[20%]">Biến động</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($histories as $history)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-8 py-5">
                                        <p class="text-sm font-semibold text-[#181611]">{{ $history->created_at->format('d/m/Y') }}</p>
                                    </td>
                                    <td class="px-8 py-5">
                                        <p class="text-sm font-semibold text-[#181611]">
                                            {{ $history->description }}
                                        </p>
                                    </td>
                                    <td class="px-8 py-5 text-right font-bold text-sm {{ $history->points > 0 ? 'text-[#10b981]' : 'text-[#ef4444]' }}">
                                        {{ $history->points > 0 ? '+' : '' }}{{ number_format($history->points) }}đ
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="px-8 py-10 text-center text-zinc-500">Bạn chưa có giao dịch điểm nào!</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </div>
    </main>
    @endsection
