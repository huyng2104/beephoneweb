@extends('admin.layouts.app')
@section('content')
<div class="p-8">
    <div class="bg-white dark:bg-slate-900 p-6 rounded-xl border border-slate-100 dark:border-slate-800 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h1 class="text-2xl font-bold">Thêm Banner mới</h1>
                <p class="text-sm text-slate-500">Tạo banner/slider cho trang chủ.</p>
            </div>
            <a href="{{ route('admin.banners.index') }}" class="text-sm text-primary">Quay lại danh sách</a>
        </div>

        <form action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @include('admin.banners._form')

            <div class="mt-6">
                <button class="px-5 py-2 rounded-lg bg-primary text-black font-bold">Lưu</button>
            </div>
        </form>
    </div>
</div>
@endsection