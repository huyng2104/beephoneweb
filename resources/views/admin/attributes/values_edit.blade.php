@extends('admin.layouts.app')

@section('content')
<div class="bg-background-light text-gray-800 min-h-screen antialiased font-sans">
    <header class="bg-white border-b border-gray-200 sticky top-0 z-30 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a class="text-gray-500 hover:text-gray-700 transition-colors" href="{{ route('admin.attributes.values.index', $attribute->id) }}">
                    <span class="material-symbols-outlined text-xl">arrow_back</span>
                </a>
                <h1 class="text-xl font-bold text-gray-900 leading-tight">Chỉnh sửa giá trị: <span class="text-primary">{{ $attribute_value->value }}</span></h1>
            </div>
        </div>
    </header>

    <main class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if($errors->any())
            <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm font-semibold shadow-sm">
                <ul class="list-disc pl-5 space-y-1">
                    @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white rounded-xl shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] border border-gray-100 p-6">
            <form action="{{ route('admin.attributes.values.update', [$attribute->id, $attribute_value->id]) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="mb-5">
                    <label class="block text-sm font-bold text-gray-700 mb-1.5">Tên giá trị <span class="text-red-500">*</span></label>
                    <input type="text" name="value" value="{{ old('value', $attribute_value->value) }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary px-3 py-2.5" required>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-bold text-gray-700 mb-1.5">Thứ tự hiển thị</label>
                    <input type="number" name="sort_order" value="{{ old('sort_order', $attribute_value->sort_order) }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary px-3 py-2.5">
                    <p class="text-xs text-gray-500 mt-1">Số càng nhỏ hiển thị càng lên trên (VD: 0, 1, 2...).</p>
                </div>
                
                <div class="flex gap-3 justify-end border-t border-gray-100 pt-5 mt-5">
                    <a href="{{ route('admin.attributes.values.index', $attribute->id) }}" class="px-5 py-2.5 rounded-lg border border-gray-300 font-bold text-sm bg-white hover:bg-gray-50">Hủy</a>
                    <button type="submit" class="px-5 py-2.5 rounded-lg bg-primary text-slate-900 font-bold text-sm shadow-sm hover:brightness-105">Lưu thay đổi</button>
                </div>
            </form>
        </div>
    </main>
</div>
@endsection