@extends('admin.layouts.app')

@section('content')
    <main class="flex-1 flex flex-col overflow-hidden font-display">

        @include('popup_notify.index')

        <!-- Body Content -->
        <div class="flex-1 overflow-y-auto p-8 space-y-6 bg-slate-50/50">
            
            <!-- Header Area -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-black text-slate-900 tracking-tight">Quản lý sản phẩm</h2>
                    <p class="text-slate-500 text-sm mt-1">Xem và quản lý tất cả các sản phẩm Bee Phone trên hệ thống</p>
                </div>
                <div class="flex items-center gap-3">
                     @php $trashCount = \App\Models\Product::onlyTrashed()->count(); @endphp
                     <a href="{{ route('admin.products.trash') }}">
                        <button class="bg-white hover:bg-slate-50 text-slate-600 border border-slate-200 font-bold px-5 py-2.5 rounded-xl shadow-sm flex items-center gap-2 transition-all">
                            <span class="material-symbols-outlined text-[20px]">delete_sweep</span>
                            Thùng rác
                            @if($trashCount > 0)
                                <span class="ml-1 bg-red-500 text-white px-2 py-0.5 rounded-full text-[10px]">{{ $trashCount }}</span>
                            @endif
                        </button>
                    </a>
                    <a href="{{ route('admin.products.create') }}">
                        <button class="bg-primary hover:bg-primary/90 text-slate-900 font-bold px-5 py-2.5 rounded-xl shadow-sm shadow-primary/20 flex items-center gap-2 transition-all">
                            <span class="material-symbols-outlined">add_circle</span>
                            Thêm sản phẩm mới
                        </button>
                    </a>
                </div>
            </div>

            <!-- Stats Bar (Exact match to screenshot) -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm">
                    <p class="text-slate-400 text-[10px] font-black uppercase tracking-widest">TỔNG SẢN PHẨM</p>
                    <p class="text-3xl font-black mt-2 text-slate-900 leading-none">{{ number_format($totalProducts) }}</p>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm">
                    <p class="text-slate-400 text-[10px] font-black uppercase tracking-widest">ĐANG HIỂN THỊ</p>
                    <p class="text-3xl font-black mt-2 text-emerald-500 leading-none">{{ number_format($activeProducts) }}</p>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm">
                    <p class="text-slate-400 text-[10px] font-black uppercase tracking-widest">SẮP HẾT HÀNG</p>
                    <div class="flex items-end gap-2">
                        <p class="text-3xl font-black mt-2 text-amber-500 leading-none">{{ number_format($lowStockCount) }}</p>
                    </div>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm">
                    <p class="text-slate-400 text-[10px] font-black uppercase tracking-widest">BỊ HẾT HÀNG</p>
                    <p class="text-3xl font-black mt-2 text-rose-500 leading-none">{{ number_format($outOfStockProducts) }}</p>
                </div>
            </div>

            <!-- Filters & Table Block (Exact match to screenshot) -->
            <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
                
                <!-- Filter Row -->
                <form action="{{ route('admin.products.index') }}" method="GET" id="filter-form">
                    <div class="p-5 border-b border-slate-50 flex flex-wrap gap-3 items-center bg-slate-50/30">
                        <div class="flex-1 min-w-[200px] relative group">
                            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-primary transition-colors">search</span>
                            <input name="search" value="{{ request('search') }}"
                                class="w-full bg-white border-slate-200 rounded-xl pl-12 py-2.5 focus:ring-primary focus:border-primary text-sm transition-all placeholder:text-slate-400 shadow-sm"
                                placeholder="Tìm tên, SKU hoặc mã sản phẩm..." type="text" />
                        </div>

                        <select name="category" onchange="this.form.submit()"
                            class="bg-white border-slate-200 rounded-xl px-4 py-2.5 text-sm font-bold text-slate-600 focus:ring-primary focus:border-primary cursor-pointer w-40 shadow-sm">
                            <option value="">Tất cả danh mục</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>

                        <select name="type" onchange="this.form.submit()"
                            class="bg-white border-slate-200 rounded-xl px-4 py-2.5 text-sm font-bold text-slate-600 focus:ring-primary focus:border-primary cursor-pointer w-40 shadow-sm">
                            <option value="">Loại SP</option>
                            <option value="simple" {{ request('type') == 'simple' ? 'selected' : '' }}>Sản phẩm đơn</option>
                            <option value="variable" {{ request('type') == 'variable' ? 'selected' : '' }}>Sản phẩm biến thể</option>
                        </select>

                        <select name="status" onchange="this.form.submit()"
                            class="bg-white border-slate-200 rounded-xl px-4 py-2.5 text-sm font-bold text-slate-600 focus:ring-primary focus:border-primary cursor-pointer w-36 shadow-sm">
                            <option value="">Trạng thái</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Đang bán</option>
                            <option value="out_of_stock" {{ request('status') == 'out_of_stock' ? 'selected' : '' }}>Hết hàng</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Đang ẩn</option>
                        </select>

                        <select name="sort" onchange="this.form.submit()"
                            class="bg-white border-slate-200 rounded-xl px-4 py-2.5 text-sm font-bold text-slate-600 focus:ring-primary focus:border-primary cursor-pointer w-44 shadow-sm">
                            <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Mới nhất</option>
                            <option value="price-asc" {{ request('sort') == 'price-asc' ? 'selected' : '' }}>Giá: Thấp đến Cao</option>
                            <option value="price-desc" {{ request('sort') == 'price-desc' ? 'selected' : '' }}>Giá: Cao xuống Thấp</option>
                            <option value="stock-desc" {{ request('sort') == 'stock-desc' ? 'selected' : '' }}>Tồn kho: Nhiều nhất</option>
                            <option value="stock-asc" {{ request('sort') == 'stock-asc' ? 'selected' : '' }}>Tồn kho: Ít nhất</option>
                        </select>

                        <button type="submit"
                            class="bg-primary hover:bg-primary/90 text-slate-900 px-5 py-2.5 rounded-xl flex items-center justify-center transition-all font-bold text-sm gap-2 shadow-sm">
                            <span class="material-symbols-outlined text-[18px]">filter_list</span>
                            Lọc
                        </button>

                        @if (request()->filled('search') || request()->filled('status') || request()->filled('category') || request()->filled('sort') || request()->filled('type'))
                            <a href="{{ route('admin.products.index') }}"
                                class="bg-slate-100 p-2.5 rounded-xl border border-slate-200 flex items-center justify-center text-slate-500 hover:text-red-500 transition-colors shadow-sm"
                                title="Xóa lọc">
                                <span class="material-symbols-outlined text-[20px]">filter_list_off</span>
                            </a>
                        @endif
                    </div>
                </form>

                <!-- Table Content -->
                <div class="overflow-x-auto overflow-y-hidden">
                    <table class="w-full text-left">
                        <thead class="bg-slate-50/50 text-slate-400 text-[10px] font-black uppercase tracking-widest">
                            <tr>
                                <th class="px-6 py-5 w-12 text-center">STT</th>
                                <th class="px-6 py-5">SẢN PHẨM</th>
                                <th class="px-6 py-5">LOẠI</th>
                                <th class="px-6 py-5">DANH MỤC</th>
                                <th class="px-6 py-5 text-center">TRẠNG THÁI</th>
                                <th class="px-6 py-5 text-center">TỒN KHO</th>
                                <th class="px-6 py-5 text-right">GIÁ BÁN</th>
                                <th class="px-6 py-5 text-right">HÀNH ĐỘNG</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @forelse($products as $index => $product)
                                <tr class="hover:bg-slate-50/30 transition-colors">
                                    <td class="px-6 py-5">
                                        <div class="flex items-center justify-center gap-1.5 flex-col">
                                            <span class="text-sm font-medium text-slate-400">{{ ($products->currentPage() - 1) * $products->perPage() + $loop->iteration }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5">
                                        <div class="flex items-center gap-3">
                                            <div class="size-12 rounded-xl border border-slate-100 bg-white p-1 flex-shrink-0 shadow-sm relative overflow-hidden">
                                                @if($product->thumbnail)
                                                    <img src="{{ asset('storage/' . $product->thumbnail) }}" class="w-full h-full object-contain">
                                                @else
                                                    <span class="material-symbols-outlined text-slate-200 text-xl absolute inset-0 flex items-center justify-center">image</span>
                                                @endif
                                                @if($product->is_featured)
                                                    <div class="absolute top-0 left-0 w-3 h-3 bg-rose-500 rounded-br-md shadow-sm border-r border-b border-white/50 z-10"></div>
                                                @endif
                                            </div>
                                            <div class="flex flex-col">
                                                <a href="{{ route('admin.products.show', $product->id) }}" class="text-sm font-bold text-slate-900 hover:text-primary transition-colors line-clamp-1 truncate max-w-[200px]">{{ $product->name }}</a>
                                                <span class="text-[10px] text-slate-400 font-medium">SKU: {{ $product->sku ?? '---' }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5">
                                        @if($product->type == 'variable')
                                            <div class="inline-flex items-center px-2 py-1 bg-purple-50 text-purple-600 border border-purple-200 rounded font-bold text-[10px] uppercase whitespace-nowrap">
                                                Biến thể
                                            </div>
                                        @else
                                            <div class="inline-flex items-center px-2 py-1 bg-sky-50 text-sky-600 border border-sky-200 rounded font-bold text-[10px] uppercase whitespace-nowrap">
                                                Đơn
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-5">
                                        <div class="flex flex-wrap gap-1 max-w-[150px]">
                                            @forelse($product->categories->take(2) as $cat)
                                                <span class="px-2 py-0.5 bg-slate-100 text-slate-600 rounded-lg text-[9px] font-black uppercase tracking-wider">{{ $cat->name }}</span>
                                            @empty
                                                <span class="text-slate-300 italic text-[10px]">---</span>
                                            @endforelse
                                            @if($product->categories->count() > 2)
                                                <span class="text-[10px] font-black text-slate-300 ml-1">+{{ $product->categories->count() - 2 }}</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-5 text-center">
                                        @if($product->status == 'active')
                                            <div class="flex items-center justify-center gap-1.5 text-emerald-500">
                                                <span class="text-xs font-bold">Hoạt động</span>
                                            </div>
                                        @else
                                            <div class="flex items-center justify-center gap-1.5 text-slate-300">
                                                <span class="text-xs font-bold">Đang ẩn</span>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-5 text-center">
                                        @php
                                            $totalStock = $product->type == 'variable' ? $product->variants->sum('stock') : ($product->variants->first()->stock ?? 0);
                                        @endphp
                                        <span class="text-sm font-black {{ $totalStock <= 5 ? 'text-rose-500' : 'text-slate-700' }}">{{ number_format($totalStock) }}</span>
                                    </td>
                                    <td class="px-6 py-5 text-right">
                                        <div class="flex flex-col items-end">
                                            @if($product->type == 'variable')
                                                @php
                                                    $minPrice = $product->variants->count() > 0 ? $product->variants->min(function($v) { return $v->sale_price ?: $v->price; }) : 0;
                                                @endphp
                                                <span class="text-[10px] font-bold text-emerald-600 italic">Giá từ:</span>
                                                <span class="text-sm font-black text-slate-900 tracking-tight">{{ number_format($minPrice, 0, ',', '.') }}₫</span>
                                            @else
                                                @php $mainVar = $product->variants->first(); @endphp
                                                @if($mainVar)
                                                    <span class="text-sm font-black text-slate-900 tracking-tight">{{ number_format($mainVar->sale_price ?? $mainVar->price ?? 0, 0, ',', '.') }}₫</span>
                                                    @if($mainVar->sale_price)
                                                        <span class="text-[10px] font-bold text-slate-400 line-through tracking-tighter">{{ number_format($mainVar->price, 0, ',', '.') }}₫</span>
                                                    @endif
                                                @else
                                                    <span class="text-sm font-black text-slate-900 tracking-tight">0₫</span>
                                                @endif
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-5 text-right">
                                        <div class="flex justify-end gap-1.5">
                                            <a href="{{ route('admin.products.show', $product->id) }}" class="p-2 text-slate-300 hover:text-blue-500 transition-all" title="Xem">
                                                <span class="material-symbols-outlined text-[20px]">visibility</span>
                                            </a>
                                            <a href="{{ route('admin.products.edit', $product->id) }}" class="p-2 text-slate-300 hover:text-primary transition-all" title="Sửa">
                                                <span class="material-symbols-outlined text-[20px]">edit</span>
                                            </a>
                                            <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" onsubmit="return confirm('Xóa sản phẩm này?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="p-2 text-slate-300 hover:text-rose-600 transition-all" title="Xóa">
                                                    <span class="material-symbols-outlined text-[20px]">delete</span>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-24 text-center">
                                        <div class="flex flex-col items-center gap-4">
                                            <span class="material-symbols-outlined text-6xl text-slate-100">inventory_2</span>
                                            <p class="text-slate-400 font-bold italic text-sm">Hệ thống chưa ghi nhận sản phẩm nào phù hợp.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination (Square style from screenshot) -->
                <div class="m-5 flex gap-2">
                    {{-- Previous --}}
                    <a href="{{ $products->previousPageUrl() }}"
                        class="size-9 flex items-center justify-center rounded-lg border border-slate-200 text-slate-400 hover:bg-slate-50 transition-colors">
                        <span class="material-symbols-outlined text-[18px]">chevron_left</span>
                    </a>

                    {{-- Page numbers --}}
                    @if($products->lastPage() > 1)
                        @foreach ($products->links()->elements[0] ?? [] as $page => $url)
                            <a href="{{ $url }}"
                                class="size-9 flex items-center justify-center rounded-lg border 
                                {{ $products->currentPage() == $page ? 'border-primary bg-primary text-slate-900 shadow-sm' : 'border-slate-200 text-slate-600 hover:bg-slate-50' }}
                                font-bold text-sm">
                                {{ $page }}
                            </a>
                        @endforeach
                    @endif

                    {{-- Next --}}
                    <a href="{{ $products->nextPageUrl() }}"
                        class="size-9 flex items-center justify-center rounded-lg border border-slate-200 text-slate-400 hover:bg-slate-50 transition-colors">
                        <span class="material-symbols-outlined text-[18px]">chevron_right</span>
                    </a>
                </div>
            </div>
        </div>
    </main>
@endsection

<style>
    .font-display { font-family: 'Inter', sans-serif; }
    ::-webkit-scrollbar { width: 5px; height: 5px; }
    ::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
</style>