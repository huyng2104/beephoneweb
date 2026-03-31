<header class="h-16 border-b border-slate-200 dark:border-slate-800 bg-white/80 dark:bg-background-dark/80 backdrop-blur-md sticky top-0 z-10 px-8 flex items-center justify-between">
    <div class="w-96">
        {{-- <div class="relative group">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-primary transition-colors">search</span>
            <input class="w-full pl-10 pr-4 py-2 bg-slate-100 dark:bg-slate-800 border-none rounded-lg text-sm focus:ring-2 focus:ring-primary/50 transition-all outline-none" placeholder="Tìm kiếm đơn hàng, khách hàng..." type="text" />
        </div> --}}
    </div>
    
    <div class="flex items-center gap-4">
        
        <div class="relative inline-block text-left" id="notification-wrapper">
            <button id="bell-icon-btn" class="w-10 h-10 flex items-center justify-center rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors relative focus:outline-none">
                <span class="material-symbols-outlined text-slate-600 dark:text-slate-300">notifications</span>
                <span id="bell-count" class="absolute top-2 right-2 w-4 h-4 flex items-center justify-center text-[9px] font-bold text-background-dark bg-primary rounded-full border border-white dark:border-background-dark hidden">
                    0
                </span>
            </button>

            <div id="bell-dropdown" class="absolute right-0 mt-2 w-80 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl shadow-2xl hidden z-50 overflow-hidden transform transition-all origin-top-right">
                <div class="p-3 border-b border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50 font-bold text-slate-800 dark:text-white flex items-center justify-between">
                    <span>Thông báo mới</span>
                </div>
                
                <div id="bell-list" class="max-h-80 overflow-y-auto custom-scrollbar">
                    <div class="p-8 text-center text-sm text-slate-400 dark:text-slate-500">Đang tải...</div>
                </div>
                
               <div class="p-2 text-center border-t border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50">
        <a href="{{ route('admin.notifications.index') }}" class="text-xs font-semibold text-primary hover:underline">Xem tất cả</a>
    </div>
            </div>
        </div>
        <button class="w-10 h-10 flex items-center justify-center rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
            <span class="material-symbols-outlined text-slate-600 dark:text-slate-300">help</span>
        </button>
    </div>
</header>