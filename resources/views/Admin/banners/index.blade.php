@extends('admin.layouts.app')
@section('content')
<div class="p-8 space-y-6">
    <div class="bg-white dark:bg-slate-900 p-6 rounded-xl border border-slate-100 dark:border-slate-800 shadow-sm">
        <div class="flex flex-wrap justify-between items-end gap-4">
            <div class="flex flex-col gap-1">
                <p class="text-slate-900 dark:text-white text-4xl leading-tight font-bold">Quản lý Banner & Slider Trang chủ</p>
                <p class="text-slate-500 text-sm">Quản lý banner, slider và các khu vực nội dung trang chủ: sản phẩm nổi bật, mới, khuyến mãi.</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.banners.trash') }}" class="flex items-center justify-center rounded-lg h-11 px-5 border border-slate-200 text-slate-700 text-sm font-bold hover:bg-slate-50 transition-all">
                    <span class="material-symbols-outlined mr-2 text-[20px]">delete</span>
                    Thùng rác
                </a>
                <a href="{{ route('admin.banners.create') }}" class="flex items-center justify-center rounded-lg h-11 px-5 bg-primary text-black text-sm font-bold hover:brightness-105 transition-all shadow-sm">
                    <span class="material-symbols-outlined mr-2 text-[20px]">add_circle</span>
                    Thêm Banner
                </a>
            </div>
        </div>
    </div>

    @if (session('success') || session('status'))
    <div class="px-5 py-3 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-lg text-sm font-medium">
        {{ session('success') ?? session('status') }}
    </div>
    @endif

    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-100 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="p-6 bg-slate-50/30 dark:bg-slate-900/30">
            <div class="overflow-x-auto rounded-xl border border-slate-100 dark:border-slate-800 bg-white dark:bg-slate-900">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-slate-50 dark:bg-slate-800/50">
                        <tr>
                            <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wide">Hình</th>
                            <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wide">Tiêu đề</th>
                            <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wide">Loại</th>
                            <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wide">Trạng thái</th>
                            <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wide text-right">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse ($banners ?? [] as $banner)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                            <td class="px-5 py-5 w-28">
                                <div class="size-14 rounded-lg bg-slate-100 dark:bg-slate-800 flex items-center justify-center overflow-hidden">
                                    @if ($banner->image_url ?? false)
                                    <img src="{{ $banner->image_url }}" alt="{{ $banner->title }}" class="w-full h-full object-contain">
                                    @else
                                    <span class="material-symbols-outlined text-slate-400 text-[20px]">image</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-5 py-5 text-sm text-slate-600 dark:text-slate-300">{{ $banner->title }}</td>
                            <td class="px-5 py-5 text-sm text-slate-600 dark:text-slate-300">{{ $banner->type }}</td>
                            <td class="px-5 py-5">
                                <span class="px-3 py-1 rounded text-xs font-bold {{ $banner->is_active ? 'bg-primary/10 text-primary' : 'bg-slate-200 text-slate-600' }}">
                                    {{ $banner->is_active ? 'Hiển thị' : 'Ẩn' }}
                                </span>
                            </td>
                            <td class="px-5 py-5 text-right whitespace-nowrap">
                                <a href="{{ route('admin.banners.edit', $banner) }}" class="text-slate-400 hover:text-primary transition-colors" title="Sửa">
                                    <span class="material-symbols-outlined text-[20px]">edit</span>
                                </a>
                                <form action="{{ route('admin.banners.destroy', $banner) }}" method="POST" class="inline" onsubmit="return confirm('Bạn chắc chắn muốn xóa banner này?')">
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
                                <p class="text-sm text-slate-500">Chưa có banner nào.</p>
                                <a href="{{ route('admin.banners.create') }}" class="mt-3 inline-flex items-center gap-2 px-3 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 text-xs font-semibold hover:border-primary hover:text-primary transition-colors">
                                    <span class="material-symbols-outlined text-[16px]">add</span>
                                    Thêm banner đầu tiên
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
@endsection