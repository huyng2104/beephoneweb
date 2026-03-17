<div class="grid grid-cols-1 md:grid-cols-2 gap-5">
    <div>
        <label class="text-sm font-semibold text-slate-700 dark:text-slate-200">Tên danh mục <span class="text-red-500">*</span></label>
        <input type="text" name="name" value="{{ old('name', $category->name ?? '') }}" required class="mt-1.5 w-full rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 focus:border-primary focus:ring-primary" placeholder="VD: Điện thoại" />
        @error('name')
        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="text-sm font-semibold text-slate-700 dark:text-slate-200">Slug</label>
        <input type="text" name="slug" value="{{ old('slug', $category->slug ?? '') }}" class="mt-1.5 w-full rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 focus:border-primary focus:ring-primary" placeholder="tu-dong-neu-bo-trong" />
        @error('slug')
        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="text-sm font-semibold text-slate-700 dark:text-slate-200">Danh mục cha</label>
        <select name="parent_id" class="mt-1.5 w-full rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 focus:border-primary focus:ring-primary">
            <option value="">— Không —</option>
            @foreach($categoryOptions as $id => $label)
            <option value="{{ $id }}" @selected(old('parent_id', $category->parent_id ?? request()->input('parent_id')) == $id)>{{ $label }}</option>
            @endforeach
        </select>
        @error('parent_id')
        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="text-sm font-semibold text-slate-700 dark:text-slate-200">Thứ tự</label>
        <input type="number" name="sort_order" min="0" value="{{ old('sort_order', $category->sort_order ?? 0) }}" class="mt-1.5 w-full rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 focus:border-primary focus:ring-primary" />
        @error('sort_order')
        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>
</div>

<label class="mt-5 inline-flex items-center gap-2 text-sm font-medium text-slate-700 dark:text-slate-200">
    <input type="hidden" name="is_active" value="0">
    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $category->is_active ?? true)) class="rounded border-slate-300 text-primary focus:ring-primary" />
    Kích hoạt danh mục
</label>

<div class="mt-6 flex items-center justify-end gap-3">
    <a href="{{ route('admin.categories.index') }}" class="px-4 py-2 rounded-lg border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 text-sm font-semibold hover:bg-slate-50 dark:hover:bg-slate-800">Hủy</a>
    <button type="submit" class="px-5 py-2 rounded-lg bg-primary text-black text-sm font-bold hover:brightness-105">Lưu danh mục</button>
</div>