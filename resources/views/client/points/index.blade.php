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
                            <p class="text-sm text-zinc-500">Đang phát triển. Tặng điểm khi bạn viết đánh giá.</p>
                        </div>
                    </div>
                </div>
            </section>

            <section id="cua-hang-doi-qua" class="md:col-span-12 mt-8">
                <div class="flex justify-between items-center mb-8">
                    <h2 class="text-3xl text-on-surface font-bold tracking-tight">Cửa hàng đổi quà</h2>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @forelse($vouchers as $voucher)
                    @php
                        // Tính điểm để đổi Voucher
                        $redeemRate = $setting->redeem_rate ?: 1000;
                        $pointCost = $voucher->discount_type === 'fixed' ? ceil($voucher->discount_value / $redeemRate) : ceil(($voucher->max_discount ?: 50000) / $redeemRate);
                    @endphp
                    <div class="bg-white rounded-xl overflow-hidden border border-zinc-100 hover:shadow-xl transition-shadow group flex flex-col">
                        <div class="h-32 bg-zinc-900 flex items-center justify-center relative">
                            <div class="absolute inset-0 opacity-20" style="background-image: radial-gradient(#f4c025 1px, transparent 1px); background-size: 20px 20px;"></div>
                            <span class="text-primary text-3xl font-bold relative z-10 tracking-tighter">{{ $voucher->code }}</span>
                        </div>
                        <div class="p-6 flex flex-col flex-grow">
                            <span class="text-[10px] font-bold uppercase tracking-widest text-primary mb-2 block">VOUCHER</span>
                            <h4 class="font-bold text-on-surface text-lg mb-1 leading-tight">{{ $voucher->name }}</h4>
                            <p class="text-sm text-zinc-500 mb-4">
                                @if($voucher->discount_type == 'fixed')
                                    Giảm trực tiếp {{ number_format($voucher->discount_value) }}đ
                                @else
                                    Giảm {{ $voucher->discount_value }}% (Tối đa {{ number_format($voucher->max_discount) }}đ)
                                @endif
                                <br>Đơn từ {{ number_format($voucher->min_order_value) }}đ
                            </p>

                            <div class="mt-auto pt-4 border-t border-zinc-100 flex items-center justify-between">
                                <span class="font-bold text-on-surface">{{ number_format($pointCost) }} BP</span>

                                <form action="{{ route('client.points.redeem') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="voucher_id" value="{{ $voucher->id }}">
                                    <button type="submit" onclick="return confirm('Bạn có chắc muốn dùng {{ number_format($pointCost) }} điểm để đổi mã này không?')" class="bg-surface-container-low text-on-surface hover:bg-primary hover:text-on-primary px-4 py-2 rounded-lg text-sm font-bold transition-colors">
                                        Đổi quà
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="col-span-full p-8 text-center text-zinc-500 bg-white rounded-xl border border-zinc-100">
                        Hiện tại chưa có mã giảm giá nào để đổi. Bạn quay lại sau nhé!
                    </div>
                    @endforelse
                </div>
            </section>

            <section class="md:col-span-12 mt-12 bg-white rounded-xl shadow-sm border border-zinc-100 overflow-hidden">
                <div class="px-8 py-6 border-b border-zinc-100 flex justify-between items-center">
                    <h2 class="text-xl text-on-surface font-bold">Giao dịch gần đây</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-surface-container-low">
                                <th class="px-8 py-4 font-bold text-sm text-zinc-500 uppercase tracking-wider">Thời gian</th>
                                <th class="px-8 py-4 font-bold text-sm text-zinc-500 uppercase tracking-wider">Nội dung</th>
                                <th class="px-8 py-4 font-bold text-sm text-zinc-500 uppercase tracking-wider text-right">Thay đổi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100 text-on-surface">
                            @forelse($histories as $history)
                            <tr class="hover:bg-zinc-50">
                                <td class="px-8 py-5">
                                    <p class="font-medium">{{ $history->created_at->format('d/m/Y') }}</p>
                                    <p class="text-xs text-zinc-400">{{ $history->created_at->format('H:i') }}</p>
                                </td>
                                <td class="px-8 py-5">
                                    <p class="font-bold text-on-surface">
                                        @if($history->type == 'earn') Tích điểm @elseif($history->type == 'redeem') Đổi quà @else Quản trị viên cộng @endif
                                    </p>
                                    <p class="text-xs text-zinc-500">{{ $history->description }}</p>
                                </td>
                                <td class="px-8 py-5 text-right font-bold {{ $history->points > 0 ? 'text-green-600' : 'text-red-500' }}">
                                    {{ $history->points > 0 ? '+' : '' }}{{ number_format($history->points) }} BP
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
            </section>
        </div>
    </main>
    @endsection
