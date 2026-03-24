@extends('admin.layouts.app')

@section('content')
<div class="bg-background-light text-gray-800 min-h-screen antialiased font-sans">
    <header class="bg-white border-b border-gray-200 sticky top-0 z-30 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a class="text-gray-500 hover:text-gray-700 flex items-center transition-colors" href="{{ route('admin.attributes.values.index', $attribute->id) }}">
                    <span class="material-symbols-outlined text-xl">arrow_back</span>
                </a>
                <div>
                    <h1 class="text-xl font-bold text-gray-900 leading-tight flex items-center gap-2">
                        <span class="material-symbols-outlined text-red-500">delete</span>
                        Thùng rác: <span class="text-primary">{{ $attribute->name }}</span>
                    </h1>
                </div>
            </div>
            <div>
                <a href="{{ route('admin.attributes.values.index', $attribute->id) }}" class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm font-bold hover:bg-gray-50 transition-colors shadow-sm">
                    Trở về cấu hình giá trị
                </a>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl text-sm font-semibold flex items-center gap-2 shadow-sm">
                <span class="material-symbols-outlined text-green-600 text-lg">check_circle</span>
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-red-50/50 border-b border-red-100">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-black text-gray-700 uppercase">Tên giá trị đã xóa</th>
                            <th class="px-6 py-4 text-left text-xs font-black text-gray-700 uppercase">Ngày xóa</th>
                            <th class="px-6 py-4 text-right text-xs font-black text-gray-700 uppercase">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($trashedValues as $val)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-bold text-gray-500 line-through">{{ $val->value }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-mono">
                                {{ $val->deleted_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <form action="{{ route('admin.attributes.values.restore', $val->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-[11px] font-bold text-blue-600 hover:text-blue-800 bg-blue-50 px-2.5 py-1.5 rounded border border-blue-200">Khôi phục</button>
                                    </form>
                                    <form action="{{ route('admin.attributes.values.force_delete', $val->id) }}" method="POST" class="inline" onsubmit="return confirm('Xóa vĩnh viễn không thể khôi phục. Tiếp tục?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-[11px] font-bold text-red-600 hover:text-red-800 bg-red-50 px-2.5 py-1.5 rounded border border-red-200">Xóa vĩnh viễn</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-6 py-12 text-center text-gray-400 italic text-sm bg-gray-50/50">
                                <span class="material-symbols-outlined text-4xl mb-2 opacity-50 block">recycling</span>
                                Không có giá trị nào trong thùng rác.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>
@endsection