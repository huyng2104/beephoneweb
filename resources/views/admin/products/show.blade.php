@extends('admin.layouts.app')

@section('content')
<div class="bg-slate-50 text-slate-900 font-display min-h-screen p-4 sm:p-8">
    <div class="max-w-[1200px] mx-auto w-full">
        
        <div class="flex flex-col flex-1 w-full bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="flex flex-wrap justify-between items-center gap-4 p-6 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.products.index') }}" class="text-slate-400 hover:text-primary transition-colors">
                        <span class="material-symbols-outlined text-2xl">arrow_back</span>
                    </a>
                    <h1 class="text-3xl font-black leading-tight tracking-tight text-slate-800">Chi tiết: {{ $product->name }}</h1>
                </div>
                <a href="{{ route('admin.products.edit', $product->id) }}" class="flex items-center justify-center gap-2 rounded-lg h-10 px-6 bg-primary hover:brightness-105 text-slate-900 text-sm font-bold transition-all shadow-sm">
                    <span class="material-symbols-outlined text-sm">edit</span>
                    <span>Chỉnh sửa sản phẩm</span>
                </a>
            </div>

            <div class="p-6 space-y-10">
                <div class="flex flex-col md:flex-row gap-6 bg-slate-50 p-6 rounded-xl border border-slate-100">
                    <div class="shrink-0">
                        @if($product->thumbnail)
                            @if(str_starts_with($product->thumbnail, 'http'))
                                <img alt="{{ $product->name }}" class="w-40 h-40 object-cover rounded-lg shadow-sm border border-slate-200 bg-white" src="{{ $product->thumbnail }}"/>
                            @else
                                <img alt="{{ $product->name }}" class="w-40 h-40 object-cover rounded-lg shadow-sm border border-slate-200 bg-white" src="{{ asset('storage/' . $product->thumbnail) }}"/>
                            @endif
                        @else
                            <div class="w-40 h-40 rounded-lg shadow-sm border border-slate-200 bg-white flex items-center justify-center text-slate-300">
                                <span class="material-symbols-outlined text-5xl">image</span>
                            </div>
                        @endif
                    </div>
                    
                    <div class="flex flex-col justify-center flex-1">
                        <h2 class="text-2xl font-bold leading-tight mb-2">{{ $product->name }} - SKU: {{ $product->sku ?? 'Chưa cập nhật' }}</h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm mt-4">
                            <div class="flex flex-col gap-1">
                                <span class="text-slate-500">Thương hiệu</span>
                                <span class="font-semibold text-base">{{ $product->brand ? $product->brand->name : 'Không có' }}</span>
                            </div>
                            <div class="flex flex-col gap-1">
                                <span class="text-slate-500">Danh mục</span>
                                <span class="font-semibold text-base">
                                    @forelse($product->categories as $category)
                                        <span class="inline-block bg-slate-200 text-slate-700 px-2 py-0.5 rounded text-xs mr-1">{{ $category->name }}</span>
                                    @empty
                                        Chưa phân loại
                                    @endforelse
                                </span>
                            </div>
                            <div class="flex flex-col gap-1">
                                <span class="text-slate-500">Trạng thái</span>
                                @if($product->status == 'active')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-green-100 text-green-800 font-medium text-xs w-fit">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-600"></span>
                                        Đang kinh doanh
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-slate-100 text-slate-800 font-medium text-xs w-fit">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-600"></span>
                                        Bản nháp
                                    </span>
                                @endif
                            </div>
                            <div class="flex flex-col gap-1">
                                <span class="text-slate-500">Ngày tạo</span>
                                <span class="font-semibold text-base">{{ $product->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <h3 class="text-xl font-bold leading-tight mb-4 flex items-center gap-2 text-slate-800">
                        <span class="material-symbols-outlined text-primary">insights</span> Thống kê nhanh
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="flex flex-col gap-2 rounded-xl p-5 border border-slate-200 bg-white shadow-sm">
                            <div class="flex items-center gap-2 text-slate-500 mb-1">
                                <span class="material-symbols-outlined text-lg">visibility</span>
                                <p class="text-sm font-medium">Tổng lượt xem</p>
                            </div>
                            <p class="text-3xl font-bold text-slate-900">12,500</p>
                        </div>
                        <div class="flex flex-col gap-2 rounded-xl p-5 border border-slate-200 bg-white shadow-sm">
                            <div class="flex items-center gap-2 text-slate-500 mb-1">
                                <span class="material-symbols-outlined text-lg">shopping_cart</span>
                                <p class="text-sm font-medium">Tổng lượt mua</p>
                            </div>
                            <p class="text-3xl font-bold text-slate-900">1,200</p>
                        </div>
                        <div class="flex flex-col gap-2 rounded-xl p-5 border border-slate-200 bg-white shadow-sm">
                            <div class="flex items-center gap-2 text-slate-500 mb-1">
                                <span class="material-symbols-outlined text-lg">star</span>
                                <p class="text-sm font-medium">Đánh giá trung bình</p>
                            </div>
                            <div class="flex items-end gap-2">
                                <p class="text-3xl font-bold text-slate-900">4.8</p>
                                <p class="text-lg text-slate-400 mb-0.5">/5</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <h3 class="text-xl font-bold leading-tight mb-4 flex items-center gap-2 text-slate-800">
                        <span class="material-symbols-outlined text-primary">memory</span> Thông số kỹ thuật
                    </h3>
                    <div class="overflow-x-auto rounded-xl border border-slate-200 shadow-sm bg-white">
                        <table class="w-full text-left text-sm text-slate-600">
                            <tbody class="divide-y divide-slate-200">
                                @if($product->specifications && is_array($product->specifications) && count($product->specifications) > 0)
                                    @foreach($product->specifications as $key => $value)
                                        <tr class="hover:bg-slate-50 transition-colors">
                                            <th class="py-3 px-4 font-semibold w-1/3 bg-slate-50/50 border-r border-slate-100">{{ $key }}</th>
                                            <td class="py-3 px-4 font-medium text-slate-800">{{ $value }}</td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td class="py-6 px-4 text-center text-slate-400 italic">Sản phẩm này chưa được cập nhật thông số kỹ thuật.</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>

                <div>
                    <h3 class="text-xl font-bold leading-tight mb-4 flex items-center gap-2 text-slate-800">
                        <span class="material-symbols-outlined text-primary">category</span> Phiên bản & Giá bán
                    </h3>
                    
                    @if($product->type == 'simple')
                        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
                            <div>
                                <p class="text-sm text-slate-500 font-bold mb-1">Giá bán hiện tại:</p>
                                <div class="flex items-center gap-3">
                                    @if($product->sale_price && $product->sale_price > 0)
                                        <span class="text-2xl font-black text-red-600">{{ number_format($product->sale_price, 0, ',', '.') }} ₫</span>
                                        <span class="text-sm text-slate-400 line-through">{{ number_format($product->price, 0, ',', '.') }} ₫</span>
                                    @else
                                        <span class="text-2xl font-black text-slate-900">{{ number_format($product->price, 0, ',', '.') }} ₫</span>
                                    @endif
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-slate-500 font-bold mb-1">Kho hàng:</p>
                                <span class="text-lg font-bold text-slate-800">{{ $product->stock }} sản phẩm</span>
                            </div>
                        </div>
                    @else
                        <div class="overflow-x-auto rounded-xl border border-slate-200 shadow-sm">
                            <table class="w-full text-left text-sm text-slate-600 whitespace-nowrap">
                                <thead class="bg-slate-50 text-slate-700 font-semibold border-b border-slate-200">
                                    <tr>
                                        <th class="py-3 px-4">Phiên bản</th>
                                        <th class="py-3 px-4 text-right">Giá bán (VNĐ)</th>
                                        <th class="py-3 px-4 text-right">Tồn kho</th>
                                        <th class="py-3 px-4 text-center">Trạng thái</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-200 bg-white">
                                    @forelse($product->variants as $variant)
                                        <tr class="hover:bg-slate-50 transition-colors">
                                            <td class="py-3 px-4 font-medium flex items-center gap-2">
                                                <span class="material-symbols-outlined text-slate-400 text-sm">sell</span>
                                                @foreach($variant->attributeValues as $value)
                                                    {{ $value->value }}{{ !$loop->last ? ' - ' : '' }}
                                                @endforeach
                                            </td>
                                            <td class="py-3 px-4 text-right">
                                                @if($variant->sale_price && $variant->sale_price > 0)
                                                    <div class="font-semibold text-red-600">{{ number_format($variant->sale_price, 0, ',', '.') }} ₫</div>
                                                    <div class="text-[10px] text-slate-400 line-through">{{ number_format($variant->price, 0, ',', '.') }} ₫</div>
                                                @else
                                                    <div class="font-semibold text-slate-900">{{ number_format($variant->price, 0, ',', '.') }} ₫</div>
                                                @endif
                                            </td>
                                            <td class="py-3 px-4 text-right font-medium {{ $variant->stock <= 5 ? 'text-amber-600' : 'text-slate-500' }}">
                                                {{ $variant->stock }}
                                            </td>
                                            <td class="py-3 px-4 text-center">
                                                @if($variant->stock > 0)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                                        Còn hàng
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                                        Hết hàng
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="py-8 text-center text-slate-400 italic">
                                                Sản phẩm này chưa có biến thể nào được tạo.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

                <div>
                    <h3 class="text-xl font-bold leading-tight mb-4 flex items-center gap-2 text-slate-800">
                        <span class="material-symbols-outlined text-primary">photo_library</span> Thư viện hình ảnh
                    </h3>
                    @if($product->images && $product->images->count() > 0)
                        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4">
                            <div class="aspect-square rounded-xl border border-primary overflow-hidden bg-slate-50 relative group shadow-sm">
                                <img alt="Thumbnail" class="w-full h-full object-cover" src="{{ asset('storage/' . $product->thumbnail) }}"/>
                                <div class="absolute inset-0 bg-black/10 flex items-start justify-end p-2">
                                    <span class="text-white text-[10px] font-bold uppercase bg-primary px-2 py-1 rounded shadow-sm text-slate-900">Ảnh chính</span>
                                </div>
                            </div>
                            
                            @foreach($product->images as $img)
                                <div class="aspect-square rounded-xl border border-slate-200 overflow-hidden bg-slate-50 shadow-sm">
                                    <img alt="Gallery" class="w-full h-full object-cover" src="{{ asset('storage/' . $img->path) }}"/>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="p-6 bg-slate-50 border border-slate-200 border-dashed rounded-xl text-center text-slate-400 font-medium text-sm">
                            Sản phẩm này chưa có album ảnh phụ nào.
                        </div>
                    @endif
                </div>

                <div>
                    <h3 class="text-xl font-bold leading-tight mb-4 flex items-center gap-2 text-slate-800">
                        <span class="material-symbols-outlined text-primary">description</span> Mô tả sản phẩm
                    </h3>
                    <div class="prose prose-slate max-w-none bg-slate-50 p-6 rounded-xl border border-slate-200 text-sm">
                        @if($product->description)
                            {!! $product->description !!}
                        @else
                            <p class="text-slate-400 italic">Chưa có thông tin mô tả chi tiết cho sản phẩm này.</p>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection