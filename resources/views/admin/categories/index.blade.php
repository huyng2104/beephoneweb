@extends('admin.layouts.app')
@section('content')
<div class="p-8 space-y-6">
    <div class="bg-white dark:bg-slate-900 p-6 rounded-xl border border-slate-100 dark:border-slate-800 shadow-sm">
        <div class="flex flex-wrap justify-between items-end gap-4">
            <div class="flex flex-col gap-1">
                <p class="text-slate-900 dark:text-white text-4xl leading-tight font-bold">Quản lý Danh mục và Thương hiệu</p>
                <p class="text-slate-500 text-sm">Tổ chức cấu trúc sản phẩm và thiết lập các bộ lọc tìm kiếm cho toàn bộ hệ thống.</p>
            </div>
            <div class="flex items-center gap-2">
                @if ($viewMode === 'brands')
                <a href="{{ route('admin.brands.create') }}" class="flex items-center justify-center rounded-lg h-11 px-5 bg-primary text-black text-sm font-bold hover:brightness-105 transition-all shadow-sm">
                    <span class="material-symbols-outlined mr-2 text-[20px]">add_circle</span>
                    Thêm thương hiệu mới
                </a>
                @else
                <a href="{{ route('admin.categories.create') }}" class="flex items-center justify-center rounded-lg h-11 px-5 bg-primary text-black text-sm font-bold hover:brightness-105 transition-all shadow-sm">
                    <span class="material-symbols-outlined mr-2 text-[20px]">add_circle</span>
                    Thêm Danh mục mới
                </a>
                <a href="{{ route('admin.categories.trash') }}" class="ml-2 inline-flex items-center justify-center rounded-lg h-11 px-4 border border-slate-200 dark:border-slate-700 text-sm font-semibold hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                    <span class="material-symbols-outlined mr-2 text-[18px]">delete_outline</span>
                    Thùng rác
                </a>
                @endif
            </div>
        </div>
    </div>

    @if (session('status'))
    <div class="p-3 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-lg text-sm">
        {{ session('status') }}
    </div>
    @endif

    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-100 dark:border-slate-800 shadow-sm overflow-hidden">
        @php
        $firstCategoryForFilterTab = $activeCategoryId ?? ($rows[0]['category']->id ?? null);
        @endphp
        <div class="flex border-b border-slate-100 dark:border-slate-800 px-6 pt-2">
            <a href="{{ route('admin.categories.index') }}" class="flex items-center gap-2 border-b-2 py-4 px-2 font-bold text-sm leading-tight transition-colors {{ $viewMode === 'structure' ? 'border-primary text-primary' : 'border-transparent text-slate-500 hover:text-primary' }}">
                <span class="material-symbols-outlined text-[20px]">account_tree</span>
                Cấu trúc Danh mục
            </a>
      
            
            <a href="{{ route('admin.brands.index') }}" class="flex items-center gap-2 border-b-2 py-4 px-6 font-bold text-sm leading-tight transition-colors">
                <span class="material-symbols-outlined text-[20px]">verified</span>
                Quản lý Thương hiệu
            </a>
        </div>

        @if ($viewMode === 'brands')
        <div class="p-6 bg-slate-50/30 dark:bg-slate-900/30 min-h-[760px]">
            <div class="overflow-x-auto rounded-xl border border-slate-100 dark:border-slate-800 bg-white dark:bg-slate-900">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-slate-50 dark:bg-slate-800/50">
                        <tr>
                            <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wide">Thương hiệu</th>
                            <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wide">Slug</th>
                            <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wide">Thứ tự</th>
                            <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wide">Trạng thái</th>
                            <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wide text-right">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse ($allBrands as $brand)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                            <td class="px-5 py-5">
                                <div class="flex items-center gap-3">
                                    <div class="size-10 rounded-lg bg-slate-100 dark:bg-slate-800 flex items-center justify-center overflow-hidden">
                                        @if ($brand->logo_url)
                                        <img src="{{ $brand->logo_url }}" alt="{{ $brand->name }}" class="w-full h-full object-contain" onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden');">
                                        <span class="material-symbols-outlined text-slate-400 text-[20px] hidden">verified</span>
                                        @else
                                        <span class="material-symbols-outlined text-slate-400 text-[20px]">verified</span>
                                        @endif
                                    </div>
                                    <span class="text-sm font-semibold text-slate-900 dark:text-white">{{ $brand->name }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-5 text-sm text-slate-600 dark:text-slate-300">{{ $brand->slug }}</td>
                            <td class="px-5 py-5 text-sm text-slate-600 dark:text-slate-300">{{ $brand->sort_order }}</td>
                            <td class="px-5 py-5">
                                <span class="px-3 py-1 rounded text-xs font-bold {{ $brand->is_active ? 'bg-primary/10 text-primary' : 'bg-slate-200 text-slate-600' }}">
                                    {{ $brand->is_active ? 'Kích hoạt' : 'Ẩn' }}
                                </span>
                            </td>
                            <td class="px-5 py-5 text-right whitespace-nowrap">
                                <a href="{{ route('admin.brands.edit', $brand) }}" class="text-slate-400 hover:text-primary transition-colors" title="Sửa">
                                    <span class="material-symbols-outlined text-[20px]">edit</span>
                                </a>
                                <form action="{{ route('admin.brands.destroy', $brand) }}" method="POST" class="inline" onsubmit="return confirm('Bạn chắc chắn muốn xóa thương hiệu này?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-slate-400 hover:text-red-500 transition-colors ml-3" title="Xóa">
                                        <span class="material-symbols-outlined text-[20px]">delete</span>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-5 py-8 text-center">
                                <p class="text-sm text-slate-500">Chưa có thương hiệu nào.</p>
                                <a href="{{ route('admin.brands.create') }}" class="mt-3 inline-flex items-center gap-2 px-3 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 text-xs font-semibold hover:border-primary hover:text-primary transition-colors">
                                    <span class="material-symbols-outlined text-[16px]">add</span>
                                    Thêm thương hiệu đầu tiên
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @else
        <div class="flex flex-col lg:flex-row min-h-[760px]">
            <div class="w-full lg:w-1/3 border-r border-slate-100 dark:border-slate-800 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white uppercase tracking-wide">Cây Danh mục</h3>
                    <button class="text-primary text-sm font-semibold">Mở rộng tất cả</button>
                </div>

                <div class="space-y-2">
                    @forelse ($rows as $row)
                    @php
                    $category = $row['category'];
                    $depth = $row['depth'];
                    $hasChildren = isset($rows[$loop->index + 1]) && $rows[$loop->index + 1]['depth'] > $depth;
                    $isActive = $activeCategoryId === $category->id;

                    $indentClass = match (true) {
                    $depth === 0 => '',
                    $depth === 1 => 'ml-8',
                    $depth === 2 => 'ml-16 border-l border-slate-300 dark:border-slate-700 pl-4',
                    default => 'ml-20 border-l border-slate-300 dark:border-slate-700 pl-4',
                    };
                    @endphp
                    <div class="{{ $indentClass }}">
                        @if ($viewMode === 'filters')
                        <a href="{{ route('admin.categories.index', ['mode' => 'filters', 'category_id' => $category->id]) }}" class="group flex items-center justify-between py-2.5 px-3 rounded-2xl transition-colors {{ $isActive ? 'bg-primary/10 text-primary' : 'hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                            <div class="flex items-center gap-2 min-w-0">
                                <span class="material-symbols-outlined text-slate-400 text-[18px]">{{ $hasChildren ? 'keyboard_arrow_down' : 'keyboard_arrow_right' }}</span>

                                @if ($depth === 0)
                                <span class="material-symbols-outlined text-primary text-[19px]">folder</span>
                                @endif

                                <span class="{{ $depth === 0 ? 'text-xl font-bold' : ($depth === 1 ? 'text-lg font-medium' : 'text-base font-semibold') }} {{ $isActive ? 'text-primary' : 'text-slate-700 dark:text-slate-300' }} truncate">
                                    {{ $category->name }}
                                </span>
                            </div>

                            @if ($isActive)
                            <span class="material-symbols-outlined text-primary text-[20px]">check_circle</span>
                            @elseif ($depth === 0 && $category->children_count > 0)
                            <span class="text-[14px] font-bold px-2 py-0.5 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-500">{{ $category->children_count }}</span>
                            @endif
                        </a>
                        @else
                        <div class="group flex items-center justify-between py-2.5 px-3 rounded-2xl transition-colors {{ $isActive ? 'bg-primary/10 text-primary' : 'hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                            <div class="flex items-center gap-2 min-w-0">
                                <span class="material-symbols-outlined text-slate-400 text-[18px]">{{ $hasChildren ? 'keyboard_arrow_down' : 'keyboard_arrow_right' }}</span>

                                @if ($depth === 0)
                                <span class="material-symbols-outlined text-primary text-[19px]">folder</span>
                                @endif

                                <span class="{{ $depth === 0 ? 'text-xl font-bold' : ($depth === 1 ? 'text-lg font-medium' : 'text-base font-semibold') }} {{ $isActive ? 'text-primary' : 'text-slate-700 dark:text-slate-300' }} truncate">
                                    {{ $category->name }}
                                </span>
                            </div>

                            @if ($isActive)
                            <span class="material-symbols-outlined text-primary text-[20px]">check_circle</span>
                            @elseif ($depth === 0 && $category->children_count > 0)
                            <span class="text-[14px] font-bold px-2 py-0.5 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-500">{{ $category->children_count }}</span>
                            @endif
                        </div>
                        @endif
                    </div>
                    @empty
                    <div class="text-sm text-slate-500">Chưa có danh mục nào.</div>
                    @endforelse
                </div>
            </div>

            <div class="flex-1 p-6 bg-slate-50/30 dark:bg-slate-900/30">
                @if ($viewMode === 'filters' && $activeCategory)
                <div class="flex items-start justify-between gap-4 mb-4">
                    <div>
                        <h2 class="text-4xl font-bold text-slate-900 dark:text-white leading-tight">{{ $activeCategory->name }}</h2>
                        <p class="text-sm text-slate-600 dark:text-slate-300">Chọn các thuộc tính kỹ thuật để hiển thị làm bộ lọc cho danh mục này.</p>
                        <p class="text-xs text-slate-400 mt-1">Đang gán: {{ $assignedAttributes->count() }} thuộc tính</p>
                    </div>

                    <details id="new-attribute-panel" class="w-full max-w-md">
                        <summary class="list-none cursor-pointer inline-flex items-center gap-2 px-3 py-2 border border-slate-200 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-900 text-sm font-semibold hover:border-primary transition-colors">
                            <span class="material-symbols-outlined text-[18px]">add</span>
                            Thêm thuộc tính mới
                        </summary>
                        <form action="{{ route('admin.categories.filters.attributes.store', $activeCategory) }}" method="POST" class="mt-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg p-3 space-y-2">
                            @csrf
                            <input type="text" name="name" class="w-full rounded-lg border border-slate-200 dark:border-slate-700 px-3 py-2 text-sm" placeholder="Tên thuộc tính (VD: RAM)" required>
                            <input type="text" name="code" class="w-full rounded-lg border border-slate-200 dark:border-slate-700 px-3 py-2 text-sm" placeholder="Mã thuộc tính (tùy chọn)">
                            <select name="input_type" class="w-full rounded-lg border border-slate-200 dark:border-slate-700 px-3 py-2 text-sm">
                                <option value="text">Text</option>
                                <option value="select">Select</option>
                                <option value="number">Number</option>
                            </select>
                            <input type="text" name="suggested_values" class="w-full rounded-lg border border-slate-200 dark:border-slate-700 px-3 py-2 text-sm" placeholder="Giá trị gợi ý: 8GB,16GB">
                            <label class="inline-flex items-center gap-2 text-xs">
                                <input type="hidden" name="is_filterable" value="0">
                                <input type="checkbox" name="is_filterable" value="1" checked>
                                Hiển thị lọc
                            </label>
                            <button type="submit" class="w-full px-3 py-2 bg-primary text-black rounded-lg text-sm font-semibold">Lưu thuộc tính</button>
                        </form>
                    </details>
                </div>

                <div class="overflow-x-auto rounded-xl border border-slate-100 dark:border-slate-800 bg-white dark:bg-slate-900">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-slate-50 dark:bg-slate-800/50">
                            <tr>
                                <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase">Tên thuộc tính</th>
                                <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase">Giá trị gợi ý</th>
                                <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase">Thứ tự</th>
                                <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase">Bắt buộc</th>
                                <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase">Hiển thị lọc</th>
                                <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase text-right">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @forelse($assignedAttributes as $attribute)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                                <td class="px-4 py-4 text-sm font-bold text-slate-900 dark:text-white">{{ $attribute->name }}</td>
                                <td class="px-4 py-4">
                                    <div class="flex flex-wrap gap-1.5">
                                        @forelse($attribute->suggestedValuesArray() as $value)
                                        <span class="px-2 py-0.5 bg-primary/10 text-primary text-[10px] font-bold rounded">{{ $value }}</span>
                                        @empty
                                        <span class="text-xs text-slate-400">—</span>
                                        @endforelse
                                    </div>
                                </td>
                                <td class="px-4 py-4 text-sm text-slate-600 dark:text-slate-300">{{ $attribute->pivot->sort_order ?? 0 }}</td>
                                <td class="px-4 py-4">
                                    <span class="px-2.5 py-1 rounded text-xs font-bold {{ $attribute->pivot->is_required ? 'bg-red-100 text-red-600' : 'bg-slate-100 text-slate-500' }}">
                                        {{ $attribute->pivot->is_required ? 'Có' : 'Không' }}
                                    </span>
                                </td>
                                <td class="px-4 py-4">
                                    <form action="{{ route('admin.categories.filters.attributes.toggle', [$activeCategory, $attribute]) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="relative inline-flex items-center cursor-pointer">
                                            <span class="w-8 h-4 rounded-full {{ $attribute->is_filterable ? 'bg-primary' : 'bg-slate-300' }}"></span>
                                            <span class="absolute {{ $attribute->is_filterable ? 'left-4' : 'left-1' }} size-3 bg-white rounded-full transition-all"></span>
                                        </button>
                                    </form>
                                </td>
                                <td class="px-4 py-4 text-right">
                                    <form action="{{ route('admin.categories.filters.attributes.detach', [$activeCategory, $attribute]) }}" method="POST" class="inline" onsubmit="return confirm('Gỡ thuộc tính này khỏi danh mục?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-slate-400 hover:text-red-500 transition-colors ml-2" title="Xóa">
                                            <span class="material-symbols-outlined text-[20px]">delete</span>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center">
                                    <p class="text-sm text-slate-500">Danh mục này chưa có thuộc tính nào.</p>
                                    <button type="button" id="empty-add-attribute" class="mt-3 inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 text-xs font-semibold hover:border-primary hover:text-primary transition-colors">
                                        <span class="material-symbols-outlined text-[16px]">add</span>
                                        Thêm thuộc tính đầu tiên
                                    </button>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($attributes->isNotEmpty())
                <div class="mt-4 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl p-4">
                    <form action="{{ route('admin.categories.filters.update', $activeCategory) }}" method="POST" class="space-y-3">
                        @csrf
                        @method('PUT')

                        <label class="block text-sm font-semibold">Gán nhanh thuộc tính hiện có</label>
                        <div class="overflow-x-auto rounded-lg border border-slate-100 dark:border-slate-800">
                            <table class="w-full text-left border-collapse">
                                <thead class="bg-slate-50 dark:bg-slate-800/50">
                                    <tr>
                                        <th class="px-3 py-2 text-xs font-bold text-slate-500 uppercase">
                                            <label class="inline-flex items-center gap-2">
                                                <input type="checkbox" id="select-all-attributes" class="rounded border-slate-300 text-primary focus:ring-primary">
                                                Chọn
                                            </label>
                                        </th>
                                        <th class="px-3 py-2 text-xs font-bold text-slate-500 uppercase">Thuộc tính</th>
                                        <th class="px-3 py-2 text-xs font-bold text-slate-500 uppercase">Bắt buộc</th>
                                        <th class="px-3 py-2 text-xs font-bold text-slate-500 uppercase">Thứ tự</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                    @foreach($attributes as $attribute)
                                    @php
                                    $isChecked = $assignedAttributes->contains('id', $attribute->id);
                                    $assignedItem = $assignedAttributes->firstWhere('id', $attribute->id);
                                    $rowId = 'attr-' . $attribute->id;
                                    @endphp
                                    <tr>
                                        <td class="px-3 py-2">
                                            <input type="checkbox" name="selected[]" value="{{ $attribute->id }}" data-row-id="{{ $rowId }}" @checked($isChecked) class="attribute-select rounded border-slate-300 text-primary focus:ring-primary">
                                        </td>
                                        <td class="px-3 py-2 text-sm font-medium text-slate-700 dark:text-slate-300">{{ $attribute->name }}</td>
                                        <td class="px-3 py-2">
                                            <label class="inline-flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300">
                                                <input type="checkbox" name="required[]" value="{{ $attribute->id }}" data-row-id="{{ $rowId }}" @checked($assignedItem?->pivot->is_required) class="required-checkbox rounded border-slate-300 text-primary focus:ring-primary">
                                                Bắt buộc
                                            </label>
                                        </td>
                                        <td class="px-3 py-2 w-28">
                                            <input type="number" min="0" name="sort_order[{{ $attribute->id }}]" value="{{ old('sort_order.' . $attribute->id, $assignedItem?->pivot->sort_order ?? 0) }}" data-row-id="{{ $rowId }}" class="sort-order-input w-full rounded-lg border border-slate-200 dark:border-slate-700 px-2 py-1.5 text-sm">
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <button type="submit" class="px-4 py-2 bg-slate-900 text-white rounded-lg text-sm font-semibold">Lưu gán nhanh</button>
                    </form>
                </div>
                @endif
                @else
                <div class="flex flex-wrap items-center justify-between mb-6 gap-4">
                    <div>
                        <h3 class="text-3xl font-bold text-primary leading-tight">Danh sách danh mục</h3>
                        <p class="text-sm text-slate-600 dark:text-slate-300 leading-tight">Quản lý nhanh trạng thái, thứ tự hiển thị và hành động CRUD.</p>
                    </div>
                </div>

                <div class="overflow-x-auto rounded-xl border border-slate-100 dark:border-slate-800 bg-white dark:bg-slate-900">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-slate-50 dark:bg-slate-800/50">
                            <tr>
                                <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wide">Danh mục</th>
                                <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wide">Danh mục cha</th>
                                <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wide">Thứ tự</th>
                                <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wide">Trạng thái</th>
                                <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wide text-right">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @forelse ($rows as $row)
                            @php
                            $category = $row['category'];
                            $depth = $row['depth'];
                            @endphp
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                                <td class="px-5 py-5 text-sm font-semibold text-slate-900 dark:text-white">
                                    <span class="text-slate-400">{{ str_repeat('— ', $depth) }}</span>{{ $category->name }}
                                </td>
                                <td class="px-5 py-5 text-sm text-slate-600 dark:text-slate-300">{{ $category->parent?->name ?? '—' }}</td>
                                <td class="px-5 py-5 text-sm text-slate-600 dark:text-slate-300">{{ $category->sort_order }}</td>
                                <td class="px-5 py-5">
                                    <span class="px-3 py-1 rounded text-xs font-bold {{ $category->is_active ? 'bg-primary/10 text-primary' : 'bg-slate-200 text-slate-600' }}">
                                        {{ $category->is_active ? 'Hiển thị' : 'Ẩn' }}
                                    </span>
                                </td>
                                <td class="px-5 py-5 text-right whitespace-nowrap">
                                    <a href="{{ route('admin.categories.create', ['parent_id' => $category->id]) }}" class="text-slate-400 hover:text-primary transition-colors" title="Tạo danh mục con">
                                        <span class="material-symbols-outlined text-[20px]">add</span>
                                    </a>
                                    <a href="{{ route('admin.categories.edit', $category) }}" class="text-slate-400 hover:text-primary transition-colors ml-3" title="Sửa">
                                        <span class="material-symbols-outlined text-[20px]">edit</span>
                                    </a>
                                    <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="inline" onsubmit="return confirm('Bạn chắc chắn muốn xóa danh mục này?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-slate-400 hover:text-red-500 transition-colors ml-3" title="Xóa">
                                            <span class="material-symbols-outlined text-[20px]">delete</span>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-5 py-6 text-sm text-slate-500">Chưa có danh mục nào.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @endif

            </div>
        </div>
    </div>
    @endif
</div>
</div>
</main>
</div>
</body>

<script>
    const selectInputs = document.querySelectorAll('.attribute-select');
    const selectAllInput = document.getElementById('select-all-attributes');
    const emptyAddAttributeButton = document.getElementById('empty-add-attribute');
    const newAttributePanel = document.getElementById('new-attribute-panel');

    function syncAssignRowState(rowId) {
        const selectedInput = document.querySelector(`.attribute-select[data-row-id="${rowId}"]`);
        const requiredInput = document.querySelector(`.required-checkbox[data-row-id="${rowId}"]`);
        const sortOrderInput = document.querySelector(`.sort-order-input[data-row-id="${rowId}"]`);

        if (!selectedInput) {
            return;
        }

        const isSelected = selectedInput.checked;

        if (requiredInput) {
            requiredInput.disabled = !isSelected;
            requiredInput.classList.toggle('opacity-40', !isSelected);
        }

        if (sortOrderInput) {
            sortOrderInput.disabled = !isSelected;
            sortOrderInput.classList.toggle('bg-slate-100', !isSelected);
            sortOrderInput.classList.toggle('dark:bg-slate-800', !isSelected);
        }
    }

    function syncSelectAllState() {
        if (!selectAllInput || !selectInputs.length) {
            return;
        }

        const total = selectInputs.length;
        const checked = Array.from(selectInputs).filter((input) => input.checked).length;

        selectAllInput.checked = checked === total;
        selectAllInput.indeterminate = checked > 0 && checked < total;
    }

    selectInputs.forEach((input) => {
        const rowId = input.dataset.rowId;
        syncAssignRowState(rowId);

        input.addEventListener('change', () => {
            syncAssignRowState(rowId);
            syncSelectAllState();
        });
    });

    if (selectAllInput) {
        selectAllInput.addEventListener('change', () => {
            selectInputs.forEach((input) => {
                input.checked = selectAllInput.checked;
                syncAssignRowState(input.dataset.rowId);
            });
            syncSelectAllState();
        });
        syncSelectAllState();
    }

    if (emptyAddAttributeButton && newAttributePanel) {
        emptyAddAttributeButton.addEventListener('click', () => {
            newAttributePanel.open = true;
            newAttributePanel.scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });
        });
    }
</script>

@endsection