@extends('admin.layouts.app')
@section('content')
<div class="p-8 space-y-6">
    <div class="bg-white dark:bg-slate-900 p-6 rounded-xl border border-slate-100 dark:border-slate-800 shadow-sm">
        <div class="flex flex-wrap justify-between items-end gap-4">
            <div class="flex flex-col gap-1">
                <p class="text-slate-900 dark:text-white text-4xl leading-tight font-bold">Thùng rác Banner</p>
                <p class="text-slate-500 text-sm">Danh sách các banner đã xóa. Bạn có thể khôi phục hoặc xóa vĩnh viễn.</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.banners.index') }}" class="flex items-center justify-center rounded-lg h-11 px-5 border border-slate-200 text-slate-700 text-sm font-bold hover:bg-slate-50 transition-all">
                    Quay lại danh sách
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
                            <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wide">Ngày xóa</th>
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
                            <td class="px-5 py-5 text-sm text-slate-600 dark:text-slate-300">{{ $banner->deleted_at->format('d/m/Y H:i') }}</td>
                            <td class="px-5 py-5 text-right whitespace-nowrap">
                                <form action="{{ route('admin.banners.restore', $banner->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-emerald-600 hover:text-emerald-700 transition-colors" title="Khôi phục">
                                        <span class="material-symbols-outlined text-[20px]">restore</span>
                                    </button>
                                </form>
                                <form action="{{ route('admin.banners.force_delete', $banner->id) }}" method="POST" class="inline" onsubmit="return confirm('Bạn chắc chắn muốn xóa vĩnh viễn banner này? Thao tác này không thể hoàn tác!')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-slate-400 hover:text-red-500 transition-colors ml-3" title="Xóa vĩnh viễn">
                                        <span class="material-symbols-outlined text-[20px]">delete_forever</span>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-5 py-8 text-center text-sm text-slate-500">
                                Thùng rác trống.
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
