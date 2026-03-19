@extends('admin.layouts.app')

@section('content')
<div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 py-10 font-display">
    
    <div class="mb-8 flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2 text-sm text-gray-500 mb-2">
                <a href="{{ route('admin.attributes.index') }}" class="hover:text-primary font-bold flex items-center gap-1">
                    <span class="material-symbols-outlined text-sm">tune</span> Thuộc tính
                </a>
                <span class="material-symbols-outlined text-xs">chevron_right</span>
                <span class="text-gray-900 font-medium">Thùng rác</span>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2 flex items-center gap-3">
                <span class="material-symbols-outlined text-red-500 text-3xl">delete</span>
                Thùng rác Thuộc tính
            </h1>
        </div>
        <a href="{{ route('admin.attributes.index') }}" class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm font-bold hover:bg-gray-50 transition-colors shadow-sm">
            Trở về danh sách
        </a>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl text-sm font-semibold flex items-center gap-2">
            <span class="material-symbols-outlined text-green-600 text-lg">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-red-50/50 dark:bg-gray-900/50 border-b border-red-100">
                    <tr>
                        <th class="px-6 py-4 text-left text-sm font-bold text-gray-900 dark:text-gray-100 w-1/3" scope="col">Tên thuộc tính</th>
                        <th class="px-6 py-4 text-left text-sm font-bold text-gray-900 dark:text-gray-100 w-1/3" scope="col">Ngày xóa</th>
                        <th class="px-6 py-4 text-right text-sm font-bold text-gray-900 dark:text-gray-100 w-1/3" scope="col">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                    
                    @forelse ($trashedAttributes as $attr)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-bold text-gray-600 dark:text-gray-400 line-through">{{ $attr->name }}</div>
                        </td>
                        
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm text-gray-500 font-mono">{{ $attr->deleted_at->format('d/m/Y H:i') }}</span>
                        </td>
                        
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end gap-3">
                                <form action="{{ route('admin.attributes.restore', $attr->id) }}" method="POST" class="inline-block">
                                    @csrf
                                    <button type="submit" class="text-xs font-bold text-blue-600 hover:text-blue-800 bg-blue-50 px-3 py-1.5 rounded-lg border border-blue-200 transition-colors flex items-center gap-1" title="Khôi phục">
                                        <span class="material-symbols-outlined text-[16px]">restore</span> Khôi phục
                                    </button>
                                </form>

                                <form action="{{ route('admin.attributes.force_delete', $attr->id) }}" method="POST" class="inline-block" onsubmit="return confirm('CẢNH BÁO: Xóa vĩnh viễn sẽ xóa sạch cả các giá trị (Terms) bên trong nó. Bro có chắc chắn không?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs font-bold text-red-600 hover:text-red-800 bg-red-50 px-3 py-1.5 rounded-lg border border-red-200 transition-colors flex items-center gap-1" title="Xóa vĩnh viễn">
                                        <span class="material-symbols-outlined text-[16px]">delete_forever</span> Xóa vĩnh viễn
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-6 py-16 text-center text-gray-500 dark:text-gray-400 bg-gray-50/50">
                            <span class="material-symbols-outlined text-5xl mb-3 text-gray-300">recycling</span>
                            <p class="font-medium">Thùng rác trống. Mọi thứ đang sạch sẽ!</p>
                        </td>
                    </tr>
                    @endforelse
                    
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection