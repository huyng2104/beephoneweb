@extends('admin.layouts.app')

@section('content')
<div class="p-6">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-slate-800 dark:text-white">Tất cả thông báo</h2>
            <p class="text-sm text-slate-500 mt-1">Lịch sử hoạt động và cập nhật hệ thống đơn hàng</p>
        </div>
    </div>

    <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
        <div class="divide-y divide-slate-100 dark:divide-slate-800">
            @forelse ($notifications as $notification)
                <div class="p-5 flex items-start gap-4 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors {{ is_null($notification->read_at) ? 'bg-primary/5 dark:bg-primary/10' : '' }}">
                    
                    <div class="mt-1 {{ is_null($notification->read_at) ? 'text-primary' : 'text-slate-400' }}">
                        <span class="material-symbols-outlined text-3xl">
                            {{ Str::contains(strtolower($notification->data['title']), 'hủy') ? 'cancel' : 'local_shipping' }}
                        </span>
                    </div>

                    <div class="flex-1">
                        <div class="flex items-center justify-between">
                            <h4 class="text-base font-bold {{ is_null($notification->read_at) ? 'text-slate-900 dark:text-white' : 'text-slate-700 dark:text-slate-300' }}">
                                {{ $notification->data['title'] ?? 'Thông báo hệ thống' }}
                            </h4>
                            <span class="text-xs text-slate-500 whitespace-nowrap ml-4">
                                {{ $notification->created_at->diffForHumans() }}
                            </span>
                        </div>
                        
                        <p class="text-sm text-slate-600 dark:text-slate-400 mt-1">
                            {{ $notification->data['message'] ?? '' }}
                        </p>
                        
                        @if(isset($notification->data['url']) && $notification->data['url'] !== '#')
                            <a href="{{ $notification->data['url'] }}" class="inline-block mt-3 text-sm font-semibold text-primary hover:text-yellow-600 transition-colors">
                                Xem chi tiết &rarr;
                            </a>
                        @endif
                    </div>
                </div>
            @empty
                <div class="p-16 text-center text-slate-500">
                    <span class="material-symbols-outlined text-6xl mb-4 text-slate-300">notifications_paused</span>
                    <p class="text-lg">Hiện tại không có thông báo nào.</p>
                </div>
            @endforelse
        </div>
        
        @if($notifications->hasPages())
            <div class="p-4 border-t border-slate-100 dark:border-slate-800">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
</div>
@endsection