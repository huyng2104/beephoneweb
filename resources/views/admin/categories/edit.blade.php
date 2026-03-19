@extends('admin.layouts.app')

@section('content')
<div class="p-8 space-y-6">
    {{-- Header block: Tiêu đề bên trái, Nút bên phải --}}
    <div class="bg-white dark:bg-slate-900 p-6 rounded-xl border border-slate-100 dark:border-slate-800 shadow-sm flex items-center justify-between">
        <div>
            <h1 class="text-slate-900 dark:text-white text-3xl font-bold leading-tight">Sửa danh mục</h1>
            <p class="text-slate-500 text-sm mt-1">Cập nhật thông tin danh mục: <span class="text-primary font-bold">{{ $category->name }}</span></p>
        </div>
        
        {{-- Nút quay lại bên phải --}}
        <a href="{{ route('admin.categories.index') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg bg-slate-50 dark:bg-slate-800 text-sm font-bold text-slate-600 dark:text-slate-400 hover:text-primary transition-all border border-slate-200 dark:border-slate-700 shadow-sm">
            <span class="material-symbols-outlined text-sm">arrow_back</span>
            <span>Quay lại danh sách</span>
        </a>
    </div>

    {{-- Form cập nhật --}}
    <form action="{{ route('admin.categories.update', $category) }}" method="POST" class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-xl p-6 shadow-sm">
        @csrf
        @method('PUT')
        
        <div class="space-y-6">
            @include('admin.categories.partials.form')
        </div>

        
    </form>
</div>
@endsection