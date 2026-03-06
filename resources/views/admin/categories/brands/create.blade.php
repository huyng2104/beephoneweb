@extends('admin.layouts.app')
@section('content')

        

            <div class="p-8 space-y-6">
                <div class="bg-white dark:bg-slate-900 p-6 rounded-xl border border-slate-100 dark:border-slate-800 shadow-sm">
                    <div class="flex flex-wrap justify-between items-end gap-4">
                        <div class="flex flex-col gap-1">
                            <p class="text-slate-900 dark:text-white text-4xl leading-tight font-bold">Thêm thương hiệu mới</p>
                            <p class="text-slate-500 text-sm">Tạo thương hiệu để liên kết với sản phẩm và danh mục.</p>
                        </div>
                        <a href="{{ route('admin.brands.index') }}" class="flex items-center justify-center rounded-lg h-11 px-5 border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 text-sm font-semibold hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                            Quay lại danh sách
                        </a>
                    </div>
                </div>

                <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-100 dark:border-slate-800 shadow-sm overflow-hidden">
                    <div class="flex border-b border-slate-100 dark:border-slate-800 px-6 pt-2">
                        <a href="{{ route('admin.categories.index') }}" class="flex items-center gap-2 border-b-2 border-transparent text-slate-500 py-4 px-2 font-bold text-sm leading-tight hover:text-primary transition-colors">
                            <span class="material-symbols-outlined text-[20px]">account_tree</span>
                            Cấu trúc Danh mục
                        </a>
                        <a href="{{ route('admin.brands.index') }}" class="flex items-center gap-2 border-b-2 border-primary text-primary py-4 px-6 font-bold text-sm leading-tight transition-colors">
                            <span class="material-symbols-outlined text-[20px]">verified</span>
                            Quản lý Thương hiệu
                        </a>
                    </div>

                    <form action="{{ route('admin.brands.store') }}" method="POST" class="p-6">
                        @csrf
                        @include('admin.categories.brands.partials.form')
                    </form>
                </div>
            </div>
    </div>
    </div>
</body>

</html>
@endsection