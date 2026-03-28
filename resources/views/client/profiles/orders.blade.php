@extends('client.profiles.layouts.app')

@section('title', 'Bee Phone - Lịch sử đơn h&agrave;ng')

@section('profile_content')
    <section class="flex-1" data-purpose="user-main-section">
        
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-gray-100 dark:border-white/10 pb-4">
            <div>
                <h1 class="text-2xl font-bold uppercase tracking-tight text-[#181611] dark:text-white">Lịch sử đơn h&agrave;ng</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Quản l&yacute; v&agrave; theo d&otilde;i trạng th&aacute;i c&aacute;c đơn h&agrave;ng của bạn</p>
            </div>
            
            <div class="flex gap-2 overflow-x-auto custom-scrollbar pb-2 sm:pb-0">
                <a href="#" class="bg-[#f4c025] text-[#1a1a1a] px-5 py-2 rounded-lg text-sm font-bold whitespace-nowrap shadow-sm">Tất cả</a>
                <a href="#" class="bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 px-5 py-2 rounded-lg text-sm text-gray-600 dark:text-gray-300 whitespace-nowrap hover:border-[#f4c025] hover:text-[#f4c025] transition-colors">Chờ x&aacute;c nhận</a>
                <a href="#" class="bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 px-5 py-2 rounded-lg text-sm text-gray-600 dark:text-gray-300 whitespace-nowrap hover:border-[#f4c025] hover:text-[#f4c025] transition-colors">Đang giao</a>
                <a href="#" class="bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 px-5 py-2 rounded-lg text-sm text-gray-600 dark:text-gray-300 whitespace-nowrap hover:border-[#f4c025] hover:text-[#f4c025] transition-colors">Ho&agrave;n th&agrave;nh</a>
            </div>
        </div>

        @if(isset($orders) && $orders->count() > 0)
            <div class="space-y-6">
                @foreach($orders as $order)
                    <div class="bg-white dark:bg-white/5 rounded-2xl shadow-sm border border-gray-100 dark:border-white/10 overflow-hidden hover:border-[#f4c025] transition-colors group">
                        
                        <div class="px-6 py-4 border-b border-gray-100 dark:border-white/10 flex flex-wrap items-center justify-between gap-4 bg-gray-50/50 dark:bg-white/5">
                            <div class="flex items-center gap-4">
                                <span class="font-bold text-[#181611] dark:text-white flex items-center gap-2">
                                    <span class="material-symbols-outlined text-[20px] text-[#f4c025]">receipt_long</span> 
                                    {{ $order->order_code }}
                                </span>
                                <span class="text-sm text-gray-500 border-l border-gray-300 pl-4">{{ \Carbon\Carbon::parse($order->created_at)->format('d/m/Y H:i') }}</span>
                            </div>
                            
                            <div>
                                @if($order->status == 'pending')
                                    <span class="text-yellow-700 bg-yellow-100 px-3 py-1 rounded-md text-xs font-bold uppercase tracking-wider">Chờ x&aacute;c nhận</span>
                                @elseif($order->status == 'packing')
                                    <span class="text-blue-700 bg-blue-100 px-3 py-1 rounded-md text-xs font-bold uppercase tracking-wider">Đang đ&oacute;ng g&oacute;i</span>
                                @elseif($order->status == 'shipping')
                                    <span class="text-indigo-700 bg-indigo-100 px-3 py-1 rounded-md text-xs font-bold uppercase tracking-wider">Đang giao h&agrave;ng</span>
                                @elseif($order->status == 'completed')
                                    <span class="text-green-700 bg-green-100 px-3 py-1 rounded-md text-xs font-bold uppercase tracking-wider">Đ&atilde; ho&agrave;n th&agrave;nh</span>
                                @elseif($order->status == 'cancelled')
                                    <span class="text-red-700 bg-red-100 px-3 py-1 rounded-md text-xs font-bold uppercase tracking-wider">Đ&atilde; hủy</span>
                                @endif
                            </div>
                        </div>

                        <div class="px-6 py-4 space-y-4">
                            @foreach($order->items as $item)
                                @php
                                    $imageUrl = Str::startsWith($item->thumbnail, ['http://', 'https://']) ? $item->thumbnail : asset('storage/' . $item->thumbnail);
                                @endphp
                                <div class="flex items-center gap-4">
                                    <div class="w-20 h-20 bg-gray-50 dark:bg-black/20 rounded-xl p-2 border border-gray-100 dark:border-white/5 flex-shrink-0">
                                        <img src="{{ $imageUrl }}" alt="{{ $item->product_name }}" class="w-full h-full object-contain mix-blend-multiply dark:mix-blend-normal">
                                    </div>
                                    <div class="flex-grow">
                                        <h3 class="font-bold text-[#181611] dark:text-white line-clamp-1 group-hover:text-[#f4c025] transition-colors">{{ $item->product_name }}</h3>
                                        <p class="text-sm text-gray-500 mt-1">Sản phẩm x{{ $item->quantity }}</p>
                                    </div>
                                    <div class="text-right">
                                        <span class="font-bold text-[#181611] dark:text-white">{{ number_format($item->unit_price, 0, ',', '.') }}₫</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="px-6 py-4 border-t border-gray-100 dark:border-white/10 flex flex-wrap items-center justify-between gap-4">
                            <div>
                                <span class="text-sm text-gray-500">Th&agrave;nh tiền:</span>
                                <span class="text-xl font-black text-red-500 ml-2">{{ number_format($order->total_price, 0, ',', '.') }}₫</span>
                            </div>
                           <div class="flex gap-3">
    <a href="#" class="px-6 py-2 bg-gray-100 text-[#1a1a1a] font-semibold rounded-lg hover:bg-gray-200 transition text-sm">
        Xem chi tiết
    </a>

   @if($order->status == \App\Models\Order::STATUS_PENDING)
        <form action="{{ route('client.orders.cancel', $order->id) }}" method="POST" class="inline">
            @csrf
            @method('PATCH')
            <button type="submit" class="px-6 py-2 bg-red-50 text-red-600 border border-red-200 font-semibold rounded-lg hover:bg-red-500 hover:text-white transition text-sm" onclick="return confirm('Bạn có chắc chắn muốn hủy đơn hàng này?')">
                Hủy đơn
            </button>
        </form>
    @endif
    @if($order->status == \App\Models\Order::STATUS_DELIVERED)
        <form action="{{ route('client.orders.confirm', $order->id) }}" method="POST" class="inline">
            @csrf
            @method('PATCH')
            <button type="submit" class="px-6 py-2 bg-green-500 text-white font-semibold rounded-lg hover:bg-green-600 transition text-sm shadow-md" onclick="return confirm('Bạn xác nhận đã nhận được hàng và sản phẩm không có vấn đề gì?')">
                Đã nhận được hàng
            </button>
        </form>
    @endif

    @if($order->status == \App\Models\Order::STATUS_RECEIVED)
        <button class="px-6 py-2 bg-[#f4c025] text-[#1a1a1a] font-semibold rounded-lg hover:opacity-90 transition text-sm shadow-sm">
            Mua lại
        </button>
    @endif
</div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <div class="mt-8 flex justify-center">
                {{ $orders->links() }}
            </div>
        @else
            <div class="flex flex-col items-center justify-center py-20 bg-white dark:bg-white/5 rounded-2xl border border-dashed border-gray-200 dark:border-white/10">
                <div class="w-20 h-20 bg-gray-50 dark:bg-white/5 rounded-full flex items-center justify-center mb-4">
                    <span class="material-symbols-outlined text-4xl text-gray-300 dark:text-gray-600">receipt_long</span>
                </div>
                <h2 class="text-xl font-bold text-[#181611] dark:text-white mb-2">Bạn chưa c&oacute; đơn h&agrave;ng n&agrave;o</h2>
                <p class="text-gray-500 dark:text-gray-400 mb-6 text-sm">H&atilde;y lượn một v&ograve;ng v&agrave; sắm v&agrave;i m&oacute;n đồ c&ocirc;ng nghệ nh&eacute;!</p>
                <a href="{{ route('client.products.index') }}" class="bg-[#f4c025] text-black font-bold px-8 py-2.5 rounded-lg hover:scale-105 transition-transform shadow-sm">
                    Tiếp tục mua sắm
                </a>
            </div>
        @endif

    </section>

    <style>
        .custom-scrollbar::-webkit-scrollbar { height: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 10px; }
        .custom-scrollbar:hover::-webkit-scrollbar-thumb { background: #f4c025; }
    </style>
@endsection