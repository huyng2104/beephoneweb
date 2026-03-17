@extends('admin.layouts.app')
@section('content')
<div class="p-8">
    <div class="bg-white dark:bg-slate-900 p-6 rounded-xl border border-slate-100 dark:border-slate-800 shadow-sm mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold">Thùng rác - Thương hiệu</h1>
                <p class="text-sm text-slate-500">Danh sách thương hiệu đã xóa (xóa mềm). Tại đây bạn có thể phục hồi hoặc xóa vĩnh viễn.</p>
            </div>
            <div>
                <a href="{{ route('admin.brands.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-slate-200 dark:border-slate-700 text-sm font-semibold hover:bg-slate-100">Quay lại</a>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-100 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-50 dark:bg-slate-800/50">
                    <tr>
                        <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wide">Thương hiệu</th>
                        <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wide">Slug</th>
                        <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wide">Đã xóa lúc</th>
                        <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wide text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($brands as $brand)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                        <td class="px-5 py-5 text-sm font-semibold text-slate-900 dark:text-white">{{ $brand->name }}</td>
                        <td class="px-5 py-5 text-sm text-slate-600 dark:text-slate-300">{{ $brand->slug }}</td>
                        <td class="px-5 py-5 text-sm text-slate-600 dark:text-slate-300">{{ $brand->deleted_at?->format('Y-m-d H:i') ?? '—' }}</td>
                        <td class="px-5 py-5 text-right whitespace-nowrap">
                            <form action="{{ route('admin.brands.restore', $brand->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="text-slate-400 hover:text-green-600 transition-colors ml-3" title="Phục hồi">Phục hồi</button>
                            </form>
                            <form action="{{ route('admin.brands.force_delete', $brand->id) }}" method="POST" class="inline" onsubmit="return confirm('Xóa vĩnh viễn thương hiệu này?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-slate-400 hover:text-red-600 transition-colors ml-3" title="Xóa vĩnh viễn">Xóa vĩnh viễn</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-5 py-8 text-center text-sm text-slate-500">Thùng rác trống.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection