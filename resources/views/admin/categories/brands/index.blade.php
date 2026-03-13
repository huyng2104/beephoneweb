@extends('admin.layouts.app')
@section('content')
<div class="p-8 space-y-6">
    <div class="bg-white dark:bg-slate-900 p-6 rounded-xl border border-slate-100 dark:border-slate-800 shadow-sm">
        <div class="flex flex-wrap justify-between items-end gap-4">
            <div class="flex flex-col gap-1">
                <p class="text-slate-900 dark:text-white text-4xl leading-tight font-bold">Quản lý thương hiệu</p>
                <p class="text-slate-500 text-sm">Tạo, cập nhật và kiểm soát trạng thái thương hiệu hiển thị.</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.categories.index') }}" class="flex items-center justify-center rounded-lg h-11 px-5 border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 text-sm font-semibold hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                    Danh mục
                </a>
                <a href="{{ route('admin.brands.create') }}" class="flex items-center justify-center rounded-lg h-11 px-5 bg-primary text-black text-sm font-bold hover:brightness-105 transition-all shadow-sm">
                    <span class="material-symbols-outlined mr-2 text-[20px]">add_circle</span>
                    Thêm thương hiệu
                </a>
            </div>
        </div>
    </div>

    @if (session('status'))
    <div class="p-3 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-lg text-sm">
        {{ session('status') }}
    </div>
    @endif

    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-100 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="flex border-b border-slate-100 dark:border-slate-800 px-6 pt-2">
            <a href="{{ route('admin.categories.index') }}" class="flex items-center gap-2 border-b-2 border-transparent text-slate-500 py-4 px-2 font-bold text-sm leading-tight hover:text-primary transition-colors">
                <span class="material-symbols-outlined text-[20px]">account_tree</span>
                Cấu trúc Danh mục
            </a>
            <a href="{{ route('admin.brands.index') }}" class="flex items-center gap-2 border-b-2 border-primary text-primary py-4 px-6 font-bold text-sm leading-tight transition-colors">
                <span class="material-symbols-outlined text-[20px]">verified</span>
                Quản lý Thương hiệu
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-50 dark:bg-slate-800/50">
                    <tr>
                        <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wide">Thương hiệu</th>
                        <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wide">Slug</th>
                        <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wide">Thứ tự</th>
                        <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wide">Trạng thái</th>
                        <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wide text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse ($brands as $brand)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                        <td class="px-5 py-5">
                            <div class="flex items-center gap-3">
                                <div class="size-10 rounded-lg bg-slate-100 dark:bg-slate-800 flex items-center justify-center overflow-hidden">
                                    @if ($brand->logo_url)
                                    <img src="{{ $brand->logo_url }}" alt="{{ $brand->name }}" class="w-full h-full object-contain" onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden');">
                                    <span class="material-symbols-outlined text-slate-400 text-[20px] hidden">verified</span>
                                    @else
                                    <span class="material-symbols-outlined text-slate-400 text-[20px]">verified</span>
                                    @endif
                                </div>
                                <span class="text-sm font-semibold text-slate-900 dark:text-white">{{ $brand->name }}</span>
                            </div>
                        </td>
                        <td class="px-5 py-5 text-sm text-slate-600 dark:text-slate-300">{{ $brand->slug }}</td>
                        <td class="px-5 py-5 text-sm text-slate-600 dark:text-slate-300">{{ $brand->sort_order }}</td>
                        <td class="px-5 py-5">
                            <span class="px-3 py-1 rounded text-xs font-bold {{ $brand->is_active ? 'bg-primary/10 text-primary' : 'bg-slate-200 text-slate-600' }}">
                                {{ $brand->is_active ? 'Kích hoạt' : 'Ẩn' }}
                            </span>
                        </td>
                        <td class="px-5 py-5 text-right whitespace-nowrap">
                            <a href="{{ route('admin.brands.edit', $brand) }}" class="text-slate-400 hover:text-primary transition-colors" title="Sửa">
                                <span class="material-symbols-outlined text-[20px]">edit</span>
                            </a>
                            <form action="{{ route('admin.brands.destroy', $brand) }}" method="POST" class="inline" onsubmit="return confirm('Bạn chắc chắn muốn xóa thương hiệu này?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-slate-400 hover:text-red-500 transition-colors ml-3" title="Xóa">
                                    <span class="material-symbols-outlined text-[20px]">delete</span>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-5 py-8 text-center">
                            <p class="text-sm text-slate-500">Chưa có thương hiệu nào.</p>
                            <a href="{{ route('admin.brands.create') }}" class="mt-3 inline-flex items-center gap-2 px-3 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 text-xs font-semibold hover:border-primary hover:text-primary transition-colors">
                                <span class="material-symbols-outlined text-[16px]">add</span>
                                Thêm thương hiệu đầu tiên
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
</div>
</div>
</body>

</html>
@endsection