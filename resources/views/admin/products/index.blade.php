@extends('admin.layouts.app')

@section('content')
<div class="w-full p-4 sm:p-8 space-y-6 font-display">

    <div class="bg-white dark:bg-slate-900 p-6 rounded-xl border border-slate-100 dark:border-slate-800 shadow-sm">
        <div class="flex flex-wrap justify-between items-end gap-4">
            <div class="flex flex-col gap-1">
                <div class="flex items-center gap-2 mb-1">
                    <span class="text-slate-400 text-xs uppercase font-bold tracking-wider">Quản lý Sản phẩm</span>
                    <span class="material-symbols-outlined text-sm text-slate-400">chevron_right</span>
                    <span class="text-primary font-bold text-xs uppercase tracking-wider">Tất cả sản phẩm</span>
                </div>
                <p class="text-slate-900 dark:text-white text-3xl leading-tight font-bold">Danh sách sản phẩm</p>
                <p class="text-slate-500 text-sm">Quản lý kho hàng, cập nhật giá và thông tin các dòng điện thoại Bee Phone của bạn.</p>
            </div>
            
            <div class="flex items-center gap-3">
                @php $trashCount = \App\Models\Product::onlyTrashed()->count(); @endphp
                <a href="{{ route('admin.products.trash') }}" class="flex items-center justify-center rounded-lg h-11 px-4 bg-white border border-slate-200 text-slate-700 text-sm font-bold hover:text-red-600 hover:border-red-200 hover:bg-red-50 transition-all shadow-sm gap-2">
                    <span class="material-symbols-outlined text-[20px]">delete</span>
                    Thùng rác
                    @if($trashCount > 0)
                        <span class="bg-red-500 text-white px-2 py-0.5 rounded-full text-[10px]">{{ $trashCount }}</span>
                    @endif
                </a>

                <a href="{{ route('admin.products.create') }}" class="flex items-center justify-center rounded-lg h-11 px-5 bg-primary text-black text-sm font-bold hover:brightness-105 transition-all shadow-sm gap-2">
                    <span class="material-symbols-outlined text-[20px]">add_circle</span>
                    Thêm sản phẩm mới
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl text-sm font-bold flex items-center gap-2 shadow-sm">
            <span class="material-symbols-outlined text-green-600 text-lg">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-100 flex items-center gap-4">
            <div class="size-12 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                <span class="material-symbols-outlined">inventory</span>
            </div>
            <div>
                <p class="text-xs text-slate-500 font-bold uppercase tracking-wider mb-1">Tổng kho</p>
                <p class="text-2xl font-black text-slate-900 leading-none">{{ $products->count() }}</p>
            </div>
        </div>
        <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-100 flex items-center gap-4">
            <div class="size-12 rounded-lg bg-green-50 text-green-600 flex items-center justify-center">
                <span class="material-symbols-outlined">trending_up</span>
            </div>
            <div>
                <p class="text-xs text-slate-500 font-bold uppercase tracking-wider mb-1">Đang bán</p>
                <p class="text-2xl font-black text-slate-900 leading-none">{{ $products->where('status', 'active')->count() }}</p>
            </div>
        </div>
        <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-100 flex items-center gap-4">
            <div class="size-12 rounded-lg bg-orange-50 text-orange-600 flex items-center justify-center">
                <span class="material-symbols-outlined">warning</span>
            </div>
            <div>
                <p class="text-xs text-slate-500 font-bold uppercase tracking-wider mb-1">Sắp hết hàng</p>
                <p class="text-2xl font-black text-slate-900 leading-none">{{ $products->filter(fn($p) => $p->stock <= 5 && $p->stock > 0)->count() }}</p>
            </div>
        </div>
        <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-100 flex items-center gap-4">
            <div class="size-12 rounded-lg bg-red-50 text-red-600 flex items-center justify-center">
                <span class="material-symbols-outlined">auto_delete</span>
            </div>
            <div>
                <p class="text-xs text-slate-500 font-bold uppercase tracking-wider mb-1">Hết hàng</p>
                <p class="text-2xl font-black text-slate-900 leading-none">{{ $products->where('stock', 0)->count() }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
        
        <div class="p-6 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <div class="relative w-80">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[20px]">search</span>
                <input class="w-full pl-10 pr-4 py-2.5 bg-white border border-slate-200 rounded-lg text-sm font-medium focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all text-slate-700 placeholder-slate-400 shadow-sm" placeholder="Tìm kiếm theo tên hoặc mã SKU..." type="text"/>
            </div>
            <div class="flex gap-3">
                <button class="px-4 py-2.5 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors flex items-center gap-2 text-slate-600 text-sm font-bold shadow-sm">
                    <span class="material-symbols-outlined text-[18px]">filter_list</span> Bộ lọc
                </button>
                <button class="px-4 py-2.5 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors flex items-center gap-2 text-slate-600 text-sm font-bold shadow-sm">
                    <span class="material-symbols-outlined text-[18px]">download</span> Xuất Excel
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 border-b border-slate-200 text-[11px] uppercase font-black text-slate-500 tracking-wider">
                    <tr>
                        <th class="py-4 px-6 w-12 text-center">
                            <input type="checkbox" class="rounded border-slate-300 text-primary focus:ring-primary">
                        </th>
                        <th class="py-4 px-6">Sản phẩm</th>
                        <th class="py-4 px-6">Danh mục</th>
                        <th class="py-4 px-6">Thương hiệu</th>
                        <th class="py-4 px-6 text-center">Trạng thái</th>
                        <th class="py-4 px-6 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($products as $product)
                    <tr class="hover:bg-slate-50 transition-colors group">
                        <td class="py-4 px-6 text-center whitespace-nowrap">
                            <input type="checkbox" class="rounded border-slate-300 text-primary focus:ring-primary">
                        </td>
                        <td class="py-4 px-6">
                            <div class="flex items-center gap-4">
                                <div class="size-12 shrink-0 rounded-lg border border-slate-200 overflow-hidden bg-white flex items-center justify-center p-1 shadow-sm">
                                    @if($product->thumbnail)
                                        <img src="{{ Storage::url($product->thumbnail) }}" alt="{{ $product->name }}" class="h-full w-full object-contain">
                                    @else
                                        <span class="material-symbols-outlined text-slate-300 text-2xl">image</span>
                                    @endif
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm font-bold text-slate-900 line-clamp-1 mb-1" title="{{ $product->name }}">
                                        {{ $product->name }}
                                    </span>
                                    <div class="flex items-center gap-2">
                                        @if($product->type == 'variable')
                                            <span class="px-2 py-0.5 bg-blue-50 text-blue-600 border border-blue-100 rounded text-[10px] font-black uppercase tracking-wider">Biến thể</span>
                                        @else
                                            <span class="px-2 py-0.5 bg-slate-100 text-slate-600 border border-slate-200 rounded text-[10px] font-black uppercase tracking-wider">Đơn giản</span>
                                        @endif
                                        <span class="text-[11px] font-semibold text-slate-400">SKU: {{ $product->sku ?? '---' }}</span>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-6">
                            <div class="flex flex-wrap gap-1.5">
                                @forelse($product->categories as $category)
                                    <span class="text-[11px] font-bold bg-slate-100 text-slate-600 px-2 py-1 rounded border border-slate-200">
                                        {{ $category->name }}
                                    </span>
                                @empty
                                    <span class="text-xs text-slate-400 italic">---</span>
                                @endforelse
                            </div>
                        </td>
                        <td class="py-4 px-6 whitespace-nowrap text-sm text-slate-700 font-bold">
                            {{ $product->brand ? $product->brand->name : '---' }}
                        </td>
                        <td class="py-4 px-6 whitespace-nowrap text-center">
                            @if($product->status == 'active')
                                <span class="inline-flex items-center px-2.5 py-1 rounded text-[11px] font-black uppercase tracking-wider bg-green-100 text-green-700 border border-green-200">Hiển thị</span>
                            @elseif($product->status == 'inactive')
                                <span class="inline-flex items-center px-2.5 py-1 rounded text-[11px] font-black uppercase tracking-wider bg-slate-100 text-slate-600 border border-slate-200">Ẩn</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded text-[11px] font-black uppercase tracking-wider bg-red-100 text-red-700 border border-red-200">Lưu trữ</span>
                            @endif
                        </td>
                        <td class="py-4 px-6 whitespace-nowrap text-right">
                            <div class="flex items-center justify-end gap-3 opacity-0 group-hover:opacity-100 transition-opacity">
                                <a href="{{ route('admin.products.show', $product->id) }}" class="text-slate-400 hover:text-primary transition-colors" title="Xem">
                                    <span class="material-symbols-outlined text-[20px]">visibility</span>
                                </a>
                                
                                <a href="{{ route('admin.products.edit', $product->id) }}" class="text-slate-400 hover:text-blue-600 transition-colors" title="Sửa">
                                    <span class="material-symbols-outlined text-[20px]">edit</span>
                                </a>
                                
                                <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" class="inline" onsubmit="return confirm('Bạn có chắc muốn chuyển sản phẩm này vào thùng rác?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-slate-400 hover:text-red-500 transition-colors" title="Xóa">
                                        <span class="material-symbols-outlined text-[20px]">delete</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="h-64 text-center text-slate-400 italic bg-white">
                            <div class="flex flex-col items-center justify-center h-full">
                                <span class="material-symbols-outlined text-5xl mb-3 text-slate-200">inventory_2</span>
                                <p class="text-slate-500 font-medium">Chưa có sản phẩm nào.</p>
                                <a href="{{ route('admin.products.create') }}" class="mt-3 text-primary text-sm font-bold hover:underline">Thêm sản phẩm đầu tiên</a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if(method_exists($products, 'links'))
            <div class="p-4 border-t border-slate-100 bg-white">
                {{ $products->links() }}
            </div>
        @endif
    </div>
</div>
@endsection