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
                <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
                    @forelse($vouchers->where('points_required', '!=', 0)->where('voucher_status', 'Hoạt động') as $index => $voucher)
                    @php
                        // Tính điểm để đổi Voucher
                        $redeemRate = $setting->redeem_rate ?: 1000;
                        $pointCost = $voucher->discount_type === 'fixed' ? ceil($voucher->discount_value / $redeemRate) : ceil(($voucher->max_discount ?: 50000) / $redeemRate);
                        
                        $isSaved = in_array($voucher->id, $userVoucherIds ?? []);
                    @endphp
                    <div class="group bg-white rounded-xl overflow-hidden flex border border-gray-200 transition-all duration-300 hover:shadow-lg hover:border-amber-200 relative">
                        <div class="w-32 bg-amber-50 flex flex-col items-center justify-center p-4 border-r-2 border-dashed border-gray-200 relative">
                            <div class="absolute -top-3 -right-3 w-6 h-6 bg-gray-50 rounded-full border-b border-l border-gray-200"></div>
                            <div class="absolute -bottom-3 -right-3 w-6 h-6 bg-gray-50 rounded-full border-t border-l border-gray-200"></div>

                            <span class="material-symbols-outlined text-amber-500 text-4xl mb-2">loyalty</span>
                            <span class="text-[10px] font-bold text-amber-700 uppercase tracking-widest text-center">{{ $voucher->code }}</span>
                        </div>

                        <div class="flex-1 p-5 flex flex-col justify-between">
                            <div>
                                <div class="flex justify-between items-start mb-2">
                                    <h3 class="text-lg font-bold text-gray-900 leading-tight line-clamp-2" title="{{ $voucher->name }}">
                                        {{ $voucher->name }}
                                    </h3>
                                    <span onclick="openModal('modal-voucher-{{ $voucher->id }}')" class="material-symbols-outlined text-gray-400 text-lg cursor-pointer hover:text-amber-500 transition-colors shrink-0 ml-2">info</span>
                                </div>

                                <p class="text-sm font-bold text-amber-600 mb-3">
                                    @if ($voucher->discount_type == 'percent')
                                        Giảm {{ $voucher->discount_value }}%
                                        @if ($voucher->max_discount)
                                            <span class="text-xs text-gray-500 font-normal">(Tối đa {{ number_format($voucher->max_discount, 0, ',', '.') }}đ)</span>
                                        @endif
                                    @else
                                        Giảm {{ number_format($voucher->discount_value, 0, ',', '.') }}đ
                                    @endif
                                </p>

                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center text-xs text-gray-500 font-medium">
                                        <span class="material-symbols-outlined text-sm mr-1">schedule</span>
                                        HSD: {{ $voucher->end_date ? \Carbon\Carbon::parse($voucher->end_date)->format('d/m/Y') : 'Vô thời hạn' }}
                                    </div>
                                    <span class="font-bold text-[#eab308] text-lg">{{ number_format($pointCost) }} BP</span>
                                </div>
                            </div>

                            <div class="flex space-x-3 mt-auto">
                                @if($isSaved)
                                    <button disabled class="flex-1 py-2 bg-gray-200 text-gray-500 font-bold rounded-lg text-sm cursor-not-allowed">
                                        Đã có
                                    </button>
                                @elseif($user->total_points >= $pointCost)
                                    <form action="{{ route('client.points.redeem') }}" method="POST" class="flex-1">
                                        @csrf
                                        <input type="hidden" name="voucher_id" value="{{ $voucher->id }}">
                                        <button type="submit" onclick="return confirm('Bạn có chắc muốn dùng {{ number_format($pointCost) }} điểm để đổi mã này không?')" class="w-full py-2 bg-amber-500 text-white font-bold rounded-lg text-sm hover:bg-amber-600 transition-colors active:scale-95 shadow-sm">
                                            Đổi ngay
                                        </button>
                                    </form>
                                @else
                                    <button disabled class="flex-1 py-2 bg-gray-100 text-gray-400 font-bold rounded-lg text-sm cursor-not-allowed">
                                        Chưa đủ điểm
                                    </button>
                                @endif
                                <button onclick="openModal('modal-voucher-{{ $voucher->id }}')" class="px-4 py-2 border border-gray-200 text-gray-700 font-semibold rounded-lg text-sm hover:bg-gray-50 transition-colors">
                                    Chi tiết
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Modal Chi Tiết Voucher --}}
                    <div id="modal-voucher-{{ $voucher->id }}" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                        <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity opacity-0 duration-300" id="backdrop-{{ $voucher->id }}" onclick="closeModal('modal-voucher-{{ $voucher->id }}')"></div>
                        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                                <div id="dialog-{{ $voucher->id }}" class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95 duration-300 sm:my-8 sm:w-full sm:max-w-lg">
                                    <button onclick="closeModal('modal-voucher-{{ $voucher->id }}')" class="absolute top-4 right-4 text-gray-400 hover:text-red-500 transition-colors">
                                        <span class="material-symbols-outlined">close</span>
                                    </button>

                                    <div class="bg-amber-50 px-6 py-5 border-b border-amber-100 flex items-center">
                                        <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center shadow-sm mr-4 border border-amber-100">
                                            <span class="material-symbols-outlined text-amber-500 text-2xl">local_activity</span>
                                        </div>
                                        <div>
                                            <h3 class="text-lg font-bold text-gray-900" id="modal-title">Chi tiết ưu đãi</h3>
                                            <p class="text-sm text-amber-600 font-bold tracking-widest">{{ $voucher->code }}</p>
                                        </div>
                                    </div>

                                    <div class="px-6 py-6 text-gray-600 space-y-5">
                                        <h4 class="text-xl font-bold text-gray-800 leading-snug">{{ $voucher->name }}</h4>

                                        <div class="bg-gray-50 rounded-xl p-4 border border-gray-100 space-y-3">
                                            <div class="flex justify-between border-b border-gray-200 pb-2">
                                                <span class="text-gray-500 text-sm">Mức giảm:</span>
                                                <span class="font-bold text-amber-600">
                                                    @if ($voucher->discount_type == 'percent')
                                                        Giảm {{ $voucher->discount_value }}%
                                                        @if ($voucher->max_discount)
                                                            <span class="text-xs text-gray-500 font-normal">(Tối đa {{ number_format($voucher->max_discount, 0, ',', '.') }}đ)</span>
                                                        @endif
                                                    @else
                                                        Giảm {{ number_format($voucher->discount_value, 0, ',', '.') }}đ
                                                    @endif
                                                </span>
                                            </div>

                                            <div class="flex justify-between border-b border-gray-200 pb-2">
                                                <span class="text-gray-500 text-sm">Đơn tối thiểu:</span>
                                                <span class="font-bold text-gray-800">{{ number_format($voucher->min_order_value, 0, ',', '.') }}đ</span>
                                            </div>

                                            <div class="flex justify-between border-b border-gray-200 pb-2">
                                                <span class="text-gray-500 text-sm">Giới hạn sử dụng:</span>
                                                <span class="font-medium text-gray-800">
                                                    {{ $voucher->usage_limit_per_user ? $voucher->usage_limit_per_user . ' lần/người' : 'Không giới hạn' }}
                                                </span>
                                            </div>

                                            <div class="flex justify-between border-b border-gray-200 pb-2">
                                                <span class="text-gray-500 text-sm">Hiệu lực từ:</span>
                                                <span class="font-medium text-gray-800">{{ $voucher->start_date ? \Carbon\Carbon::parse($voucher->start_date)->format('H:i - d/m/Y') : 'N/A' }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-gray-500 text-sm">Hết hạn vào:</span>
                                                <span class="font-bold text-amber-600">
                                                    {{ $voucher->end_date ? \Carbon\Carbon::parse($voucher->end_date)->format('H:i - d/m/Y') : 'Vô thời hạn' }}
                                                </span>
                                            </div>
                                        </div>

                                        <div>
                                            <h5 class="font-bold text-gray-800 mb-2 flex items-center">
                                                <span class="material-symbols-outlined text-base mr-1 text-gray-400">description</span> Thể lệ chương trình:
                                            </h5>
                                            <div class="text-sm text-gray-600 leading-relaxed bg-white p-3 border border-gray-100 rounded-lg">
                                                {!! nl2br(e($voucher->description ?? 'Áp dụng cho mọi đơn hàng thỏa mãn điều kiện giá trị tối thiểu. Số lượng mã có hạn, chương trình có thể kết thúc sớm hơn dự kiến khi hết lượt sử dụng.')) !!}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="bg-gray-50 px-6 py-4 flex justify-end space-x-3 rounded-b-2xl border-t border-gray-100">
                                        <button onclick="closeModal('modal-voucher-{{ $voucher->id }}')" type="button" class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 font-semibold rounded-xl text-sm hover:bg-gray-100 transition-colors">
                                            Đóng
                                        </button>
                                        @if($isSaved)
                                            <button disabled class="px-5 py-2.5 bg-gray-200 text-gray-500 font-bold rounded-xl text-sm cursor-not-allowed">Đã có</button>
                                        @elseif($user->total_points >= $pointCost)
                                            <form action="{{ route('client.points.redeem') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="voucher_id" value="{{ $voucher->id }}">
                                                <button type="submit" onclick="return confirm('Bạn có chắc muốn dùng {{ number_format($pointCost) }} điểm để đổi mã này không?')" class="px-5 py-2.5 bg-amber-500 text-white font-bold rounded-xl text-sm hover:bg-amber-600 transition-colors shadow-sm active:scale-95 text-center inline-block">
                                                    Đổi ngay
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="col-span-full flex flex-col items-center justify-center py-16 bg-white rounded-2xl border-2 border-dashed border-gray-200">
                        <div class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                            <span class="material-symbols-outlined text-gray-300 text-5xl">inventory_2</span>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800 mb-2">Chưa có mã giảm giá nào</h3>
                        <p class="text-gray-500 text-sm mb-6 text-center max-w-sm">
                            Hiện tại chưa có mã giảm giá nào để đổi. Bạn quay lại sau nhé!
                        </p>
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

@push('js')
<script>
    function openModal(modalId) {
        const modal = document.getElementById(modalId);
        if (!modal) return;

        const backdrop = document.getElementById(modalId.replace('modal-voucher', 'backdrop'));
        const dialog = document.getElementById(modalId.replace('modal-voucher', 'dialog'));

        modal.classList.remove('hidden');

        setTimeout(() => {
            backdrop.classList.remove('opacity-0');
            backdrop.classList.add('opacity-100');

            dialog.classList.remove('opacity-0', 'translate-y-4', 'sm:scale-95');
            dialog.classList.add('opacity-100', 'translate-y-0', 'sm:scale-100');
        }, 10);
    }

    function closeModal(modalId) {
        const modal = document.getElementById(modalId);
        if (!modal) return;

        const backdrop = document.getElementById(modalId.replace('modal-voucher', 'backdrop'));
        const dialog = document.getElementById(modalId.replace('modal-voucher', 'dialog'));

        backdrop.classList.remove('opacity-100');
        backdrop.classList.add('opacity-0');

        dialog.classList.remove('opacity-100', 'translate-y-0', 'sm:scale-100');
        dialog.classList.add('opacity-0', 'translate-y-4', 'sm:scale-95');

        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }
</script>
@endpush
