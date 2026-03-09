@extends('admin.layouts.app')

@section('content')
<div class="bg-background-light text-gray-800 min-h-screen antialiased font-sans">
    <header class="bg-white border-b border-gray-200 sticky top-0 z-30 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold text-gray-900 leading-tight flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">inventory_2</span>
                    Danh sách Sản phẩm
                </h1>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.products.trash') }}" class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm font-bold hover:text-red-600 hover:bg-red-50 hover:border-red-200 transition-colors shadow-sm flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[18px]">delete</span>
                    Thùng rác
                    @php $trashCount = \App\Models\Product::onlyTrashed()->count(); @endphp
                    @if($trashCount > 0)
                        <span class="bg-red-500 text-white px-2 py-0.5 rounded-full text-[10px] ml-1">{{ $trashCount }}</span>
                    @endif
                </a>

                <a href="{{ route('admin.products.create') }}" class="bg-primary text-slate-900 px-4 py-2 rounded-lg text-sm font-bold hover:brightness-105 transition-colors shadow-sm flex items-center gap-1.5 border border-transparent">
                    <span class="material-symbols-outlined text-[18px]">add</span>
                    Thêm sản phẩm mới
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

        <div class="bg-white rounded-xl shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex flex-wrap gap-4 justify-between items-center bg-gray-50/50">
                <div class="relative w-full sm:w-80">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="material-symbols-outlined text-gray-400 text-[20px]">search</span>
                    </div>
                    <input class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary sm:text-sm font-medium transition-colors" placeholder="Tìm kiếm sản phẩm..." type="text"/>
                </div>
                <div class="flex gap-2">
                    <select class="block w-full py-2 pl-3 pr-10 text-sm border-gray-300 rounded-lg focus:ring-primary focus:border-primary">
                        <option value="">Tất cả danh mục</option>
                    </select>
                    <button class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-bold text-gray-700 bg-white hover:bg-gray-50">Lọc</button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-white">
                        <tr>
                            <th class="px-6 py-4 text-left w-10"><input class="h-4 w-4 text-primary border-gray-300 rounded" type="checkbox"/></th>
                            <th class="px-6 py-4 text-left text-xs font-black text-gray-500 uppercase">Sản phẩm</th>
                            <th class="px-6 py-4 text-left text-xs font-black text-gray-500 uppercase">Danh mục</th>
                            <th class="px-6 py-4 text-left text-xs font-black text-gray-500 uppercase">Thương hiệu</th>
                            <th class="px-6 py-4 text-center text-xs font-black text-gray-500 uppercase">Trạng thái</th>
                            <th class="px-6 py-4 text-right text-xs font-black text-gray-500 uppercase">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse ($products as $product)
                        <tr class="group hover:bg-yellow-50/30 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap"><input class="h-4 w-4 text-primary border-gray-300 rounded" type="checkbox"/></td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-4">
                                    <div class="h-12 w-12 flex-shrink-0 rounded-lg border border-gray-200 overflow-hidden bg-gray-50 flex items-center justify-center">
                                        @if($product->thumbnail)
                                            <img src="{{ Storage::url($product->thumbnail) }}" alt="{{ $product->name }}" class="h-full w-full object-cover">
                                        @else
                                            <span class="material-symbols-outlined text-gray-400">image</span>
                                        @endif
                                    </div>
                                    <div class="flex flex-col">
                                        <a href="{{ route('admin.products.edit', $product->id) }}" class="text-sm font-bold text-blue-600 hover:text-blue-800 transition-colors line-clamp-1" title="{{ $product->name }}">
                                            {{ $product->name }}
                                        </a>
                                        <div class="flex items-center gap-2 mt-1">
                                            @if($product->type == 'variable')
                                                <span class="text-[10px] font-bold bg-purple-100 text-purple-700 px-2 py-0.5 rounded border border-purple-200">Biến thể</span>
                                            @else
                                                <span class="text-[10px] font-bold bg-green-100 text-green-700 px-2 py-0.5 rounded border border-green-200">Đơn giản</span>
                                            @endif
                                            @if($product->is_featured)
                                                <span class="material-symbols-outlined text-[14px] text-yellow-500" title="Nổi bật">star</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-1">
                                    @forelse($product->categories as $category)
                                        <span class="text-[11px] font-medium bg-gray-100 text-gray-700 px-2 py-0.5 rounded border border-gray-200">
                                            {{ $category->name }}
                                        </span>
                                    @empty
                                        <span class="text-xs text-gray-400 italic">Chưa phân loại</span>
                                    @endforelse
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 font-medium">
                                {{ $product->brand ? $product->brand->name : '---' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($product->status == 'active')
                                    <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-full text-xs font-bold bg-green-100 text-green-800">Hiển thị</span>
                                @elseif($product->status == 'inactive')
                                    <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-800">Ẩn</span>
                                @else
                                    <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-full text-xs font-bold bg-red-100 text-red-800">Lưu trữ</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                               <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
    <a href="{{ route('admin.products.show', $product->id) }}" class="text-[11px] font-bold text-gray-500 hover:text-primary transition-colors">Xem</a>
    <span class="text-gray-300 text-[10px]">|</span>
    
    <a href="{{ route('admin.products.edit', $product->id) }}" class="text-[11px] font-bold text-blue-600 hover:text-blue-800 transition-colors">Sửa</a>
    <span class="text-gray-300 text-[10px]">|</span>
    
    <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" class="inline" onsubmit="return confirm('Chuyển sản phẩm này vào thùng rác?');">
        @csrf
        @method('DELETE')
        <button type="submit" class="text-[11px] font-bold text-red-500 hover:text-red-700 transition-colors">Xóa</button>
    </form>
</div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center text-gray-400 bg-gray-50/50">
                                <span class="material-symbols-outlined text-5xl mb-3 text-gray-300 block">inventory_2</span>
                                <p class="text-sm font-medium text-gray-500">Chưa có sản phẩm nào.</p>
                                <a href="{{ route('admin.products.create') }}" class="mt-3 inline-block text-primary hover:text-yellow-600 font-bold text-sm underline">Thêm sản phẩm đầu tiên</a>
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