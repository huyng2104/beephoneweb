@extends('client.layouts.app') 

@section('content')
<div class="max-w-4xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-slate-900">Thông báo của bạn</h2>
        <p class="text-slate-500 mt-1">Cập nhật tình trạng đơn hàng và khuyến mãi</p>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="divide-y divide-slate-100">
            @forelse ($notifications as $notification)
                <div class="p-5 flex items-start gap-4 hover:bg-slate-50 transition-colors {{ is_null($notification->read_at) ? 'bg-yellow-50/30' : '' }}">
                    
                    <div class="mt-1 {{ is_null($notification->read_at) ? 'text-primary' : 'text-slate-400' }}">
                        <span class="material-symbols-outlined text-3xl">
                            {{ Str::contains(strtolower($notification->data['title']), 'hủy') ? 'sentiment_dissatisfied' : 'redeem' }}
                        </span>
                    </div>

                    <div class="flex-1">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1">
                            <h4 class="text-base font-bold {{ is_null($notification->read_at) ? 'text-slate-900' : 'text-slate-700' }}">
                                {{ $notification->data['title'] ?? 'Bee Phone Store' }}
                            </h4>
                            <span class="text-xs font-medium text-slate-500 bg-slate-100 px-2 py-1 rounded-md w-fit">
                                {{ $notification->created_at->diffForHumans() }}
                            </span>
                        </div>
                        
                        <p class="text-sm text-slate-600 mt-2 leading-relaxed">
                            {{ $notification->data['message'] ?? '' }}
                        </p>
                        
                        @if(isset($notification->data['url']) && $notification->data['url'] !== '#')
                            <a href="{{ $notification->data['url'] }}" class="inline-flex items-center gap-1 mt-3 text-sm font-semibold text-primary hover:text-yellow-600 transition-colors">
                                Xem chi tiết đơn hàng
                                <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                            </a>
                        @endif
                    </div>
                </div>
            @empty
                <div class="p-16 text-center text-slate-500 flex flex-col items-center">
                    <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mb-4">
                        <span class="material-symbols-outlined text-4xl text-slate-300">notifications_off</span>
                    </div>
                    <p class="text-lg font-medium text-slate-700">Chưa có thông báo nào</p>
                    <p class="text-sm mt-1">Khi có cập nhật về đơn hàng, thông báo sẽ hiển thị ở đây.</p>
                    <a href="{{ route('client.products.index') }}" class="mt-6 px-6 py-2 bg-primary text-slate-900 font-bold rounded-lg hover:bg-yellow-500 transition-colors">
                        Tiếp tục mua sắm
                    </a>
                </div>
            @endforelse
        </div>
        
        @if($notifications->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
</div>
@endsection