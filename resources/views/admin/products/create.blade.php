<!DOCTYPE html>
<html class="light" lang="vi">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Tạo sản phẩm</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
</head>

<body class="bg-slate-50 text-slate-900">
    <div class="max-w-4xl mx-auto p-8 space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold">Tạo sản phẩm</h1>
                <p class="text-sm text-slate-500">Chọn danh mục để hệ thống tự hiện trường thuộc tính tương ứng (RAM, CPU, dung lượng...).</p>
            </div>
            <a href="{{ route('admin.categories.index') }}" class="text-sm text-slate-600 hover:underline">Quay lại danh mục</a>
        </div>

        @if (session('status'))
        <div class="p-3 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-lg text-sm">
            {{ session('status') }}
        </div>
        @endif

        @if ($errors->any())
        <div class="p-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
            <ul class="list-disc pl-5 space-y-1">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('admin.products.store') }}" method="POST" class="bg-white border border-slate-200 rounded-xl p-6 space-y-5">
            @csrf

            <div>
                <label class="block text-sm font-semibold mb-1">Tên sản phẩm</label>
                <input type="text" name="name" value="{{ old('name') }}" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" required>
            </div>

            <div>
                <label class="block text-sm font-semibold mb-1">Danh mục</label>
                <select id="category_id" name="category_id" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" required>
                    <option value="">-- Chọn danh mục --</option>
                    @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected(old('category_id')==$category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <h2 class="text-lg font-bold mb-2">Thuộc tính theo danh mục</h2>
                <div id="dynamic-attributes" class="space-y-4"></div>
                <p id="no-attribute" class="text-sm text-slate-500">Chọn danh mục để hiển thị thuộc tính.</p>
            </div>

            <div>
                <button type="submit" class="px-4 py-2 bg-primary text-black rounded-lg font-semibold text-sm">Lưu sản phẩm</button>
            </div>
        </form>
    </div>

    <script type="application/json" id="category-attributes-json">
        @json($categoryAttributesMap)
    </script>
    <script type="application/json" id="old-attributes-json">
        @json(old('attributes', []))
    </script>

    <script>
        const categoryAttributesMap = JSON.parse(document.getElementById('category-attributes-json')?.textContent || '{}');
        const oldValues = JSON.parse(document.getElementById('old-attributes-json')?.textContent || '{}');

        const categorySelect = document.getElementById('category_id');
        const dynamicAttributes = document.getElementById('dynamic-attributes');
        const noAttribute = document.getElementById('no-attribute');

        function createInput(attribute) {
            const wrapper = document.createElement('div');

            const label = document.createElement('label');
            label.className = 'block text-sm font-semibold mb-1';
            label.textContent = attribute.name + (attribute.is_required ? ' *' : '');

            let input;
            if (attribute.input_type === 'select' && attribute.suggested_values.length) {
                input = document.createElement('select');
                input.className = 'w-full rounded-lg border border-slate-200 px-3 py-2 text-sm';

                const emptyOption = document.createElement('option');
                emptyOption.value = '';
                emptyOption.textContent = '-- Chọn --';
                input.appendChild(emptyOption);

                attribute.suggested_values.forEach(value => {
                    const option = document.createElement('option');
                    option.value = value;
                    option.textContent = value;
                    if ((oldValues[attribute.id] ?? '') === value) {
                        option.selected = true;
                    }
                    input.appendChild(option);
                });
            } else {
                input = document.createElement('input');
                input.type = attribute.input_type === 'number' ? 'number' : 'text';
                input.className = 'w-full rounded-lg border border-slate-200 px-3 py-2 text-sm';
                input.value = oldValues[attribute.id] ?? '';
            }

            input.name = `attributes[${attribute.id}]`;
            if (attribute.is_required) {
                input.required = true;
            }

            wrapper.appendChild(label);
            wrapper.appendChild(input);

            if (attribute.suggested_values.length && attribute.input_type !== 'select') {
                const hint = document.createElement('p');
                hint.className = 'text-xs text-slate-500 mt-1';
                hint.textContent = 'Gợi ý: ' + attribute.suggested_values.join(', ');
                wrapper.appendChild(hint);
            }

            return wrapper;
        }

        function renderAttributes() {
            const categoryId = categorySelect.value;
            const attributes = categoryAttributesMap[categoryId] || [];

            dynamicAttributes.innerHTML = '';

            if (!attributes.length) {
                noAttribute.classList.remove('hidden');
                return;
            }

            noAttribute.classList.add('hidden');
            attributes.forEach(attribute => {
                dynamicAttributes.appendChild(createInput(attribute));
            });
        }

        categorySelect.addEventListener('change', renderAttributes);
        renderAttributes();
    </script>
</body>

</html>