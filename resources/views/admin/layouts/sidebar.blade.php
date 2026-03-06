<aside
    class="w-64 border-r border-slate-200 dark:border-slate-800 bg-white dark:bg-background-dark flex flex-col fixed h-full">
    <div class="p-6 flex items-center gap-3">
        <div class="bg-primary rounded-lg p-1.5 flex items-center justify-center">
            <span class="material-symbols-outlined text-background-dark font-bold">smartphone</span>
        </div>
        <div>
            <h1 class="text-xl font-bold tracking-tight text-slate-900 dark:text-white leading-none">Bee Phone</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Hệ thống quản trị</p>
        </div>
    </div>
    <nav class="flex-1 px-4 py-4 space-y-1">
        <a class="sidebar-item-active flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium transition-colors"
            href="#">
            <span class="material-symbols-outlined">dashboard</span>
            <span>Bảng điều khiển</span>
        </a>
        <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
            href="#">
            <span class="material-symbols-outlined">inventory_2</span>
            <span>Sản phẩm</span>
        </a>
        <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
            href="#">
            <span class="material-symbols-outlined">shopping_cart</span>
            <span>Đơn hàng</span>
        </a>
        <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
            href="{{route('admin.users.index')}}">
            <span class="material-symbols-outlined">group</span>
            <span>Khách hàng</span>
        </a>
        <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
            href="#">
            <span class="material-symbols-outlined">category</span>
            <span>Danh mục</span>
        </a>
        <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
            href="{{route('admin.vouchers.index')}}">
            <span class="material-symbols-outlined">local_offer</span>
            <span>Vouchers</span>
        </a>
        <div class="pt-4 mt-4 border-t border-slate-100 dark:border-slate-800">
            <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
                href="#">
                <span class="material-symbols-outlined">settings</span>
                <span>Cài đặt</span>
            </a>
        </div>
    </nav>
    <div class="p-4 bg-slate-50 dark:bg-slate-900 m-4 rounded-xl border border-slate-100 dark:border-slate-800">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-slate-300 overflow-hidden"
                data-alt="Avatar của Alex Johnson quản trị viên"
                style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuCQ9FLwed6hUAodxd9ykvBX9jnJPa0SIZOAFTt7JD5S5S8LXWLFY62U-5aeNRvaZQetgkhn0Y2YgXmLc89xuKY4atiMN4hOXt6_aM2ursKgGi8pl6Gigoe6gbYZw7-1MfbjHkiROQCGnnfsRHNqbFp0QA_5PHl55Z81GnnMVM0tKXWUQDVpKrueckovvrx3oJwLl0Z1RvjLR5tvPWPMlZX24Up9_TbdPxlcAdiZW0lhBSt-Iyb0xrrtvxktfM33K4G9JbPO05fOiBwn')">
            </div>
            <div class="overflow-hidden">
                <p class="text-sm font-semibold truncate">Alex Johnson</p>
                <p class="text-xs text-slate-500 truncate">Quản trị viên</p>
            </div>
        </div>
    </div>
</aside>
