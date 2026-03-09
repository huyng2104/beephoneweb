@extends('admin.layouts.app')

@section('content')
<div class="bg-background-light text-gray-800 min-h-screen antialiased font-sans">
    <header class="bg-white border-b border-gray-200 sticky top-0 z-30 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a class="text-gray-500 hover:text-gray-700 flex items-center transition-colors" href="{{ route('admin.attributes.index') }}">
                    <span class="material-symbols-outlined text-xl">arrow_back</span>
                </a>
                <div>
                    <h1 class="text-xl font-bold text-gray-900 leading-tight">Cấu hình giá trị thuộc tính: <span class="text-primary">{{ $attribute->name }}</span></h1>
                    <div class="text-xs text-gray-500 flex items-center gap-1 mt-0.5">
                        <a class="hover:text-primary transition-colors font-medium" href="{{ route('admin.attributes.index') }}">Thuộc tính</a>
                        <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                        <span class="text-gray-700 font-bold">{{ $attribute->name }}</span>
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.attributes.values.trash', $attribute->id) }}" class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm font-bold hover:text-red-600 hover:bg-red-50 hover:border-red-200 transition-colors shadow-sm flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[18px]">delete</span>
                    Thùng rác
                    @php $trashCount = $attribute->values()->onlyTrashed()->count(); @endphp
                    @if($trashCount > 0)
                        <span class="bg-red-500 text-white px-2 py-0.5 rounded-full text-[10px] ml-1">{{ $trashCount }}</span>
                    @endif
                </a>

                <a href="{{ route('admin.attributes.index') }}" class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm font-bold hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-200 transition-colors shadow-sm">
                    Trở về danh mục gốc
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
        @if($errors->any())
            <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm font-semibold shadow-sm">
                <ul class="list-disc pl-5 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="flex flex-col lg:flex-row gap-8">
            
            <div class="w-full lg:w-[30%]">
                <div class="bg-white rounded-xl shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] border border-gray-100 p-6 sticky top-24">
                    <div class="mb-5 pb-4 border-b border-gray-100">
                        <h2 class="text-lg font-bold text-gray-900">Thêm giá trị mới</h2>
                        <p class="text-sm text-gray-500 mt-1">Bổ sung tùy chọn cho <strong class="text-slate-700">{{ $attribute->name }}</strong>.</p>
                    </div>
                    <form action="{{ route('admin.attributes.values.store', $attribute->id) }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label class="block text-sm font-bold text-gray-700 mb-1.5" for="val-name">Tên giá trị (Term) <span class="text-red-500">*</span></label>
                            <input class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm px-3 py-2.5 transition-colors" 
                                   id="val-name" name="value" placeholder="Ví dụ: Đỏ, 128GB..." required type="text" value="{{ old('value') }}"/>
                            <p class="text-[11px] text-gray-500 mt-1.5 font-medium">Giá trị này sẽ hiển thị cho khách hàng chọn khi mua hàng.</p>
                        </div>
                        
                        <div class="mb-6">
    <label class="block text-sm font-bold text-gray-700 mb-1.5">Thứ tự hiển thị</label>
    <input class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm px-3 py-2.5 transition-colors" 
           name="sort_order" placeholder="Để trống tự động tăng..." type="number" value="{{ old('sort_order') }}"/>
    <p class="text-[11px] text-gray-500 mt-1.5 font-medium">Để trống hệ thống sẽ tự động xếp xuống cuối cùng.</p>
</div>
                        
                        <button type="submit" class="w-full flex justify-center items-center px-4 py-2.5 border border-transparent text-sm font-bold rounded-lg shadow-sm text-slate-900 bg-primary hover:brightness-105 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-all">
                            <span class="material-symbols-outlined text-[18px] mr-1.5">add</span>
                            Thêm giá trị
                        </button>
                    </form>
                </div>
            </div>

            <div class="w-full lg:w-[70%]">
                <div class="bg-white rounded-xl shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] border border-gray-100 overflow-hidden flex flex-col h-full">
                    
                    <div class="px-6 py-4 border-b border-gray-100 flex flex-wrap gap-4 justify-between items-center bg-gray-50/50">
                        <form action="{{ route('admin.attributes.values.index', $attribute->id) }}" method="GET" class="flex items-center gap-3">
                            <div class="relative w-full sm:w-72">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="material-symbols-outlined text-gray-400 text-[20px]">search</span>
                                </div>
                                <input name="search" value="{{ request('search') }}" class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-primary focus:border-primary sm:text-sm font-medium transition-colors" placeholder="Tìm kiếm giá trị..." type="text"/>
                            </div>
                            
                            @if(request()->has('search') && request('search') != '')
                                <a href="{{ route('admin.attributes.values.index', $attribute->id) }}" class="text-sm text-red-500 hover:text-red-700 font-medium underline whitespace-nowrap">
                                    Xóa lọc
                                </a>
                            @endif
                        </form>
                    </div>
                    
                    <div class="overflow-x-auto grow">
                        <table class="min-w-full">
                            <thead class="bg-white border-b border-gray-200">
                                <tr>
                                    <th class="px-6 py-4 text-left w-10" scope="col">
                                        <input class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded cursor-pointer" type="checkbox"/>
                                    </th>
                                    <th class="px-6 py-4 text-left text-xs font-black text-gray-500 uppercase tracking-wider" scope="col">Tên giá trị</th>
                                    <th class="px-6 py-4 text-center text-xs font-black text-gray-500 uppercase tracking-wider" scope="col">Thứ tự</th>
                                    <th class="px-6 py-4 text-center text-xs font-black text-gray-500 uppercase tracking-wider" scope="col">ID</th>
                                    <th class="px-6 py-4 text-center text-xs font-black text-gray-500 uppercase tracking-wider" scope="col">Số SP đang dùng</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                @forelse ($values as $val)
                                <tr class="group hover:bg-yellow-50/30 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap w-10">
                                        <input class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded cursor-pointer" type="checkbox"/>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex flex-col">
                                            <span class="text-sm font-bold text-gray-900">{{ $val->value }}</span>
                                            
                                            <div class="row-actions flex items-center gap-2 mt-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                                <a href="{{ route('admin.attributes.values.edit', [$attribute->id, $val->id]) }}" class="text-[11px] font-bold text-blue-600 hover:text-blue-800 transition-colors">Chỉnh sửa</a>
                                                <span class="text-gray-300 text-[10px]">|</span>

                                                <form action="{{ route('admin.attributes.values.destroy', $val->id) }}" method="POST" class="inline" onsubmit="return confirm('Bro có chắc muốn xóa giá trị này? Các biến thể đang dùng nó có thể bị ảnh hưởng.');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-[11px] font-bold text-red-500 hover:text-red-700 transition-colors">Xóa vào thùng rác</button>
                                                </form>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium text-gray-600">
                                        {{ $val->sort_order }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-mono text-gray-400">
                                        #{{ $val->id }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-full text-xs font-bold bg-yellow-100 text-yellow-800 border border-yellow-200 min-w-[32px]">
                                            {{ $val->variants()->count() }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-gray-400 italic text-sm">
                                        @if(request('search'))
                                            Không tìm thấy giá trị nào khớp với "{{ request('search') }}".
                                        @else
                                            Chưa có giá trị nào. Hãy thêm giá trị đầu tiên ở form bên trái.
                                        @endif
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
@endsection