<!DOCTYPE html>
<html class="light" lang="vi">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />   
    <title>Gán thuộc tính lọc theo danh mục</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet" />
</head>

<body class="bg-slate-50 text-slate-900">
    <div class="max-w-7xl mx-auto p-6 space-y-4">
        @if (session('status'))
        <div class="p-3 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-lg text-sm">
            {{ session('status') }}
        </div>
        @endif

        <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">
            <div class="flex border-b border-slate-200 px-6 pt-2">
                <a href="{{ route('admin.categories.index') }}" class="flex items-center gap-2 border-b-2 border-transparent text-slate-500 py-4 px-2 font-bold text-sm leading-tight hover:text-primary transition-colors">
                    <span class="material-symbols-outlined text-[20px]">account_tree</span>
                    Cấu trúc Danh mục
                </a>
                <button type="button" class="flex items-center gap-2 border-b-2 border-primary text-primary py-4 px-6 font-bold text-sm leading-tight">
                    <span class="material-symbols-outlined text-[20px]">tune</span>
                    Gán thuộc tính lọc
                </button>
                <a href="{{ route('admin.brands.index') }}" class="flex items-center gap-2 border-b-2 border-transparent text-slate-500 py-4 px-6 font-bold text-sm leading-tight hover:text-primary transition-colors">
                    <span class="material-symbols-outlined text-[20px]">verified</span>
                    Quản lý Thương hiệu
                </a>
            </div>

            <div class="flex flex-col lg:flex-row min-h-[620px]">
                <div class="w-full lg:w-1/3 border-r border-slate-100 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wide">Cây Danh mục</h3>
                        <button class="text-primary text-xs font-semibold">Mở rộng tất cả</button>
                    </div>

                    <div class="space-y-2">
                        @forelse ($rows as $row)
                        @php
                        $item = $row['category'];
                        $depth = $row['depth'];
                        $isActive = $item->id === $category->id;
                        $hasChildren = isset($rows[$loop->index + 1]) && $rows[$loop->index + 1]['depth'] > $depth;
                        $indentClass = match (true) {
                        $depth === 0 => '',
                        $depth === 1 => 'ml-6',
                        default => 'ml-12 border-l border-slate-200 pl-3',
                        };
                        @endphp
                        <div class="{{ $indentClass }}">
                            <a href="{{ route('admin.categories.filters.edit', $item) }}" class="group flex items-center justify-between py-2 px-3 rounded-xl transition-colors {{ $isActive ? 'bg-primary/10' : 'hover:bg-slate-50' }}">
                                <div class="flex items-center gap-2 min-w-0">
                                    <span class="material-symbols-outlined text-slate-400 text-[18px]">{{ $hasChildren ? 'keyboard_arrow_down' : 'keyboard_arrow_right' }}</span>
                                    @if ($depth === 0)
                                    <span class="material-symbols-outlined text-primary text-[18px]">folder</span>
                                    @endif
                                    <span class="text-sm {{ $depth === 0 ? 'font-bold' : 'font-medium' }} {{ $isActive ? 'text-primary' : 'text-slate-700' }} truncate">{{ $item->name }}</span>
                                </div>
                                @if ($isActive)
                                <span class="material-symbols-outlined text-primary text-[18px]">check_circle</span>
                                @elseif ($depth === 0 && $item->children_count > 0)
                                <span class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-slate-100 text-slate-500">{{ $item->children_count }}</span>
                                @endif
                            </a>
                        </div>
                        @empty
                        <div class="text-sm text-slate-500">Chưa có danh mục nào.</div>
                        @endforelse
                    </div>
                </div>

                <div class="flex-1 p-6 bg-slate-50/30">
                    <div class="flex items-start justify-between gap-4 mb-4">
                        <div>
                            <h2 class="text-4xl font-bold text-slate-900 leading-tight">{{ $category->name }}</h2>
                            <p class="text-sm text-slate-500">Chọn các thuộc tính kỹ thuật để hiển thị làm bộ lọc cho danh mục này.</p>
                            <p class="text-xs text-slate-400 mt-1">Đang gán: {{ $assignedAttributes->count() }} thuộc tính</p>
                        </div>

                        <details id="new-attribute-panel" class="w-full max-w-md">
                            <summary class="list-none cursor-pointer inline-flex items-center gap-2 px-3 py-2 border border-slate-200 rounded-lg bg-white text-sm font-semibold hover:border-primary transition-colors">
                                <span class="material-symbols-outlined text-[18px]">add</span>
                                Thêm thuộc tính mới
                            </summary>
                            <form action="{{ route('admin.categories.filters.attributes.store', $category) }}" method="POST" class="mt-3 bg-white border border-slate-200 rounded-lg p-3 space-y-2">
                                @csrf
                                <input type="text" name="name" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" placeholder="Tên thuộc tính (VD: RAM)" required>
                                <input type="text" name="code" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" placeholder="Mã thuộc tính (tùy chọn)">
                                <select name="input_type" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm">
                                    <option value="text">Text</option>
                                    <option value="select">Select</option>
                                    <option value="number">Number</option>
                                </select>
                                <input type="text" name="suggested_values" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" placeholder="Giá trị gợi ý: 8GB,16GB">
                                <label class="inline-flex items-center gap-2 text-xs">
                                    <input type="hidden" name="is_filterable" value="0">
                                    <input type="checkbox" name="is_filterable" value="1" checked>
                                    Hiển thị lọc
                                </label>
                                <button type="submit" class="w-full px-3 py-2 bg-primary text-black rounded-lg text-sm font-semibold">Lưu thuộc tính</button>
                            </form>
                        </details>
                    </div>

                    <div class="overflow-x-auto rounded-xl border border-slate-100 bg-white">
                        <table class="w-full text-left border-collapse">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase">Tên thuộc tính</th>
                                    <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase">Giá trị gợi ý</th>
                                    <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase">Thứ tự</th>
                                    <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase">Bắt buộc</th>
                                    <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase">Hiển thị lọc</th>
                                    <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase text-right">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($assignedAttributes as $attribute)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-4 py-4 text-sm font-bold text-slate-900">{{ $attribute->name }}</td>
                                    <td class="px-4 py-4">
                                        <div class="flex flex-wrap gap-1.5">
                                            @forelse($attribute->suggestedValuesArray() as $value)
                                            <span class="px-2 py-0.5 bg-primary/10 text-primary text-[10px] font-bold rounded">{{ $value }}</span>
                                            @empty
                                            <span class="text-xs text-slate-400">—</span>
                                            @endforelse
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 text-sm text-slate-600">{{ $attribute->pivot->sort_order }}</td>
                                    <td class="px-4 py-4">
                                        <span class="px-2.5 py-1 rounded text-xs font-bold {{ $attribute->pivot->is_required ? 'bg-red-100 text-red-600' : 'bg-slate-100 text-slate-500' }}">
                                            {{ $attribute->pivot->is_required ? 'Có' : 'Không' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4">
                                        <form action="{{ route('admin.categories.filters.attributes.toggle', [$category, $attribute]) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="relative inline-flex items-center cursor-pointer">
                                                <span class="w-8 h-4 rounded-full {{ $attribute->is_filterable ? 'bg-primary' : 'bg-slate-300' }}"></span>
                                                <span class="absolute {{ $attribute->is_filterable ? 'left-4' : 'left-1' }} size-3 bg-white rounded-full transition-all"></span>
                                            </button>
                                        </form>
                                    </td>
                                    <td class="px-4 py-4 text-right">
                                        <form action="{{ route('admin.categories.filters.attributes.detach', [$category, $attribute]) }}" method="POST" class="inline" onsubmit="return confirm('Gỡ thuộc tính này khỏi danh mục?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-slate-400 hover:text-red-500 transition-colors" title="Xóa">
                                                <span class="material-symbols-outlined text-[20px]">delete</span>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center">
                                        <p class="text-sm text-slate-500">Danh mục này chưa có thuộc tính nào.</p>
                                        <button type="button" id="empty-add-attribute" class="mt-3 inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border border-slate-200 text-slate-700 text-xs font-semibold hover:border-primary hover:text-primary transition-colors">
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
                    <div class="mt-4 bg-white border border-slate-200 rounded-xl p-4">
                        <form id="quick-assign-form" action="{{ route('admin.categories.filters.update', $category) }}" method="POST" class="space-y-3">
                            @csrf
                            @method('PUT')

                            <label class="block text-sm font-semibold">Gán nhanh thuộc tính hiện có</label>
                            <div class="overflow-x-auto rounded-lg border border-slate-100">
                                <table class="w-full text-left border-collapse">
                                    <thead class="bg-slate-50">
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
                                    <tbody class="divide-y divide-slate-100">
                                        @foreach($attributes as $attribute)
                                        @php
                                        $isChecked = $assignedAttributes->contains('id', $attribute->id);
                                        $assignedItem = $assignedAttributes->firstWhere('id', $attribute->id);
                                        @endphp
                                        <tr>
                                            <td class="px-3 py-2">
                                                <input type="checkbox" name="selected[]" value="{{ $attribute->id }}" @checked($isChecked) class="attribute-select rounded border-slate-300 text-primary focus:ring-primary" data-row-id="{{ $attribute->id }}">
                                            </td>
                                            <td class="px-3 py-2 text-sm font-medium text-slate-700">{{ $attribute->name }}</td>
                                            <td class="px-3 py-2">
                                                <label class="inline-flex items-center gap-2 text-sm text-slate-600">
                                                    <input type="checkbox" name="required[]" value="{{ $attribute->id }}" @checked($assignedItem?->pivot->is_required) class="required-checkbox rounded border-slate-300 text-primary focus:ring-primary" data-row-id="{{ $attribute->id }}">
                                                    Bắt buộc
                                                </label>
                                            </td>
                                            <td class="px-3 py-2 w-28">
                                                <input type="number" min="0" name="sort_order[{{ $attribute->id }}]" value="{{ old('sort_order.' . $attribute->id, $assignedItem?->pivot->sort_order ?? 0) }}" class="sort-order-input w-full rounded-lg border border-slate-200 px-2 py-1.5 text-sm" data-row-id="{{ $attribute->id }}">
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

                    <div class="mt-10 border-t border-slate-200 pt-8">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-2xl font-bold text-slate-900 leading-tight">Thương hiệu liên kết</h3>
                            <a href="{{ route('admin.brands.index') }}" class="text-primary text-sm font-semibold flex items-center gap-1">Xem tất cả <span class="material-symbols-outlined text-[16px]">arrow_forward</span></a>
                        </div>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                            @foreach ($brands as $brand)
                            <a href="{{ route('admin.brands.edit', $brand) }}" class="bg-white p-4 rounded-xl border border-slate-100 flex flex-col items-center gap-2 hover:border-amber-400 transition-colors">
                                <div class="size-14 rounded-lg bg-slate-50 flex items-center justify-center overflow-hidden">
                                    @if ($brand->logo_url)
                                    <img src="{{ $brand->logo_url }}" alt="{{ $brand->name }}" class="w-full h-full object-contain">
                                    @else
                                    <span class="material-symbols-outlined text-slate-300 text-[28px]">verified</span>
                                    @endif
                                </div>
                                <span class="text-sm font-semibold text-slate-700">{{ $brand->name }}</span>
                            </a>
                            @endforeach
                            <a href="{{ route('admin.brands.create') }}" class="bg-white p-4 rounded-xl border border-slate-100 flex flex-col items-center gap-2 hover:border-amber-400 transition-colors">
                                <div class="size-14 rounded-lg bg-slate-50 flex items-center justify-center">
                                    <span class="material-symbols-outlined text-slate-300 text-[28px]">add</span>
                                </div>
                                <span class="text-sm font-semibold text-slate-400">Thêm mới</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end">
            <a href="{{ route('admin.products.create') }}" class="px-4 py-2 bg-slate-900 text-white rounded-lg text-sm font-semibold">Đi tới tạo sản phẩm</a>
        </div>
    </div>

    <script>
        const selectInputs = document.querySelectorAll('.attribute-select');
        const selectAllInput = document.getElementById('select-all-attributes');
        const emptyAddAttributeButton = document.getElementById('empty-add-attribute');
        const newAttributePanel = document.getElementById('new-attribute-panel');

        function syncAssignRowState(rowId) {
            const selectedInput = document.querySelector(`.attribute-select[data-row-id="${rowId}"]`);
            const requiredInput = document.querySelector(`.required-checkbox[data-row-id="${rowId}"]`);
            const sortInput = document.querySelector(`.sort-order-input[data-row-id="${rowId}"]`);

            if (!selectedInput || !requiredInput || !sortInput) {
                return;
            }

            const enabled = selectedInput.checked;
            requiredInput.disabled = !enabled;
            sortInput.disabled = !enabled;

            if (!enabled) {
                requiredInput.checked = false;
                sortInput.classList.add('bg-slate-100', 'text-slate-400');
            } else {
                sortInput.classList.remove('bg-slate-100', 'text-slate-400');
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
</body>

</html>
