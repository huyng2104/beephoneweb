@extends('admin.layouts.app')

@section('content')
<div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 py-10 font-display">
    
   <div class="mb-8 flex justify-between items-end">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Quản lý Thuộc tính Sản phẩm</h1>
            <p class="text-gray-500 dark:text-gray-400">Thiết lập các thuộc tính cho sản phẩm bổ sung, chẳng hạn như kích thước hoặc màu sắc.</p>
        </div>
        <a href="{{ route('admin.attributes.trash') }}" class="flex items-center gap-2 bg-white border border-gray-300 px-4 py-2 rounded-lg text-sm font-bold text-gray-600 hover:text-red-600 hover:border-red-200 hover:bg-red-50 transition-all shadow-sm">
            <span class="material-symbols-outlined text-[18px]">delete</span> Thùng rác
            @php $trashCount = \App\Models\Attribute::onlyTrashed()->count(); @endphp
            @if($trashCount > 0)
                <span class="bg-red-500 text-white px-2 py-0.5 rounded-full text-[10px]">{{ $trashCount }}</span>
            @endif
        </a>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl text-sm font-semibold flex items-center gap-2">
            <span class="material-symbols-outlined text-green-600 text-lg">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm font-semibold">
            <ul class="list-disc pl-5 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="flex flex-col lg:flex-row gap-8">
        <div class="w-full lg:w-[30%]">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 sticky top-24">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Thêm thuộc tính mới</h2>
                
                <form action="{{ route('admin.attributes.store') }}" method="POST">
                    @csrf
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1" for="name">Tên thuộc tính <span class="text-red-500">*</span></label>
                        <input class="w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-primary focus:ring-primary sm:text-sm dark:bg-gray-700 dark:text-white placeholder-gray-400 py-2.5 px-3" 
                               id="name" name="name" value="{{ old('name') }}" placeholder="Ví dụ: Màu sắc, Dung lượng..." type="text" required />
                        <p class="text-[11px] text-gray-500 mt-1">Tên hiển thị trên trang sản phẩm.</p>
                    </div>

                    <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2.5 border border-transparent text-sm font-bold rounded-md shadow-sm text-gray-900 bg-primary hover:brightness-105 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-all">
                        <span class="material-symbols-outlined text-sm mr-2">add</span>
                        Thêm thuộc tính
                    </button>
                </form>
            </div>
        </div>

        <div class="w-full lg:w-[70%]">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900/50">
                            <tr>
                                <th class="px-6 py-4 text-left text-sm font-bold text-gray-900 dark:text-gray-100 w-1/4" scope="col">Tên</th>
                                <th class="px-6 py-4 text-left text-sm font-bold text-gray-900 dark:text-gray-100 w-1/2" scope="col">Các giá trị (Terms)</th>
                                <th class="px-6 py-4 text-right text-sm font-bold text-gray-900 dark:text-gray-100" scope="col">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                            
                            @forelse ($attributes as $attr)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-bold text-blue-600 dark:text-blue-400">{{ $attr->name }}</div>
                                </td>
                                
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap gap-2 mb-2">
                                        @forelse ($attr->values as $val)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-semibold bg-gray-100 dark:bg-gray-700 border border-gray-200 text-gray-800 dark:text-gray-200 shadow-sm">
                                                {{ $val->value }}
                                            </span>
                                        @empty
                                            <span class="text-sm text-gray-400 italic">Chưa có giá trị nào</span>
                                        @endforelse
                                    </div>
                                    <a class="text-xs text-blue-500 hover:text-blue-700 font-medium flex items-center gap-1 mt-1" 
                                       href="{{ route('admin.attributes.values.index', $attr->id) }}">
                                        Cấu hình chủng loại của thuộc tính
                                        <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                                    </a>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end gap-2">
                                        <form action="{{ route('admin.attributes.destroy', $attr->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Bro có chắc muốn xóa thuộc tính này không?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 text-red-400 hover:text-red-600 dark:text-red-400 dark:hover:text-red-300 hover:bg-red-50 rounded-md transition-colors" title="Xóa">
                                                <span class="material-symbols-outlined text-[18px]">delete</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400 bg-gray-50/50">
                                    <span class="material-symbols-outlined text-4xl mb-2 opacity-50">tune</span>
                                    <p>Chưa có thuộc tính nào được tạo.</p>
                                </td>
                            </tr>
                            @endforelse
                            
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection