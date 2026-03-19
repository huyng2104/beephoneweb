@extends('admin.layouts.app')

@section('content')
<div class="bg-slate-50 text-slate-900 font-display min-h-screen p-4 sm:p-8">
    <div class="max-w-[1400px] mx-auto w-full">
        
        <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 text-sm text-slate-500 mb-2">
                    <a href="{{ route('admin.products.index') }}" class="flex items-center gap-1 font-bold text-primary hover:underline">
                        <span class="material-symbols-outlined text-sm">inventory_2</span> Sản phẩm
                    </a>
                    <span class="material-symbols-outlined text-xs">chevron_right</span>
                    <span class="text-slate-900 font-medium">Thùng rác</span>
                </div>
                <h1 class="text-3xl font-black tracking-tight text-slate-900 flex items-center gap-3">
                    <span class="material-symbols-outlined text-3xl text-red-500">delete</span> Thùng rác sản phẩm
                </h1>
            </div>
            <div>
                <a href="{{ route('admin.products.index') }}" class="px-5 py-2.5 rounded-lg border border-slate-300 font-bold text-sm bg-white hover:bg-slate-50 transition-all">Quay lại danh sách</a>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl text-sm font-bold shadow-sm flex items-center gap-2">
                <span class="material-symbols-outlined">check_circle</span> {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-sm">
                            <th class="py-4 px-6 font-black text-slate-600 w-16 text-center">ID</th>
                            <th class="py-4 px-6 font-black text-slate-600 w-24">Ảnh</th>
                            <th class="py-4 px-6 font-black text-slate-600">Tên sản phẩm</th>
                            <th class="py-4 px-6 font-black text-slate-600">Ngày xóa</th>
                            <th class="py-4 px-6 font-black text-slate-600 w-48 text-right">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($trashedProducts as $item)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="py-3 px-6 text-center font-bold text-slate-500">{{ $item->id }}</td>
                                <td class="py-3 px-6">
                                    <div class="w-12 h-12 rounded-lg border border-slate-200 bg-slate-100 overflow-hidden">
                                        @if($item->thumbnail)
                                            <img src="{{ asset('storage/' . $item->thumbnail) }}" class="w-full h-full object-cover" alt="img">
                                        @else
                                            <span class="material-symbols-outlined text-slate-300 w-full h-full flex items-center justify-center">image</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-3 px-6">
                                    <p class="font-bold text-slate-800">{{ $item->name }}</p>
                                    <p class="text-xs text-slate-500 font-medium mt-0.5">SKU: {{ $item->sku ?? 'N/A' }}</p>
                                </td>
                                <td class="py-3 px-6 text-sm text-red-500 font-medium">
                                    {{ $item->deleted_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="py-3 px-6">
                                    <div class="flex items-center justify-end gap-2">
                                        <form action="{{ route('admin.products.restore', $item->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="px-3 py-1.5 bg-green-50 text-green-600 hover:bg-green-100 rounded text-xs font-bold transition-colors flex items-center gap-1" title="Khôi phục">
                                                <span class="material-symbols-outlined text-sm">restore</span> Phục hồi
                                            </button>
                                        </form>
                                        
                                        <form action="{{ route('admin.products.force_delete', $item->id) }}" method="POST" onsubmit="return confirm('Bro có chắc chắn muốn XÓA VĨNH VIỄN sản phẩm này không? Không thể khôi phục lại đâu nhé!');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-3 py-1.5 bg-red-50 text-red-600 hover:bg-red-100 rounded text-xs font-bold transition-colors flex items-center gap-1" title="Xóa vĩnh viễn">
                                                <span class="material-symbols-outlined text-sm">delete_forever</span> Xóa bỏ
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-12 text-center text-slate-400 font-medium">
                                    <span class="material-symbols-outlined text-4xl mb-2 block opacity-50">recycling</span>
                                    Thùng rác đang trống trơn bro ạ!
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