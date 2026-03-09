@extends('admin.layouts.app')

@section('content')
<div class="p-8 space-y-6">
    {{-- Header block: Dùng justify-between để đẩy nút sang phải --}}
    <div class="bg-white dark:bg-slate-900 p-6 rounded-xl border border-slate-100 dark:border-slate-800 shadow-sm flex items-center justify-between">
        <div>
            <h1 class="text-slate-900 dark:text-white text-3xl font-bold leading-tight">Thêm danh mục mới</h1>
            <p class="text-slate-500 text-sm mt-1">Tạo danh mục cha hoặc danh mục con trong hệ thống sản phẩm.</p>
        </div>
        
        {{-- Nút quay lại nằm bên phải --}}
        <a href="{{ route('admin.categories.index') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg bg-slate-50 dark:bg-slate-800 text-sm font-bold text-slate-600 dark:text-slate-400 hover:text-primary dark:hover:text-primary transition-all border border-slate-200 dark:border-slate-700 shadow-sm">
            <span class="material-symbols-outlined text-sm">arrow_back</span>
            <span>Quay lại danh sách</span>
        </a>
    </div>

    {{-- Form nhập liệu --}}
    <form action="{{ route('admin.categories.store') }}" method="POST" class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-xl p-6 shadow-sm">
        @csrf
        
        <div class="space-y-6">
            @include('admin.categories.partials.form')
        </div>

        {{-- Thêm nút Lưu ở cuối form cho tiện --}}
        
    </form>
</div>
@endsection