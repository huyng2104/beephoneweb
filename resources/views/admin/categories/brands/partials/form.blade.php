<div class="grid grid-cols-1 md:grid-cols-2 gap-5">
    <div>
        <label class="text-sm font-semibold text-slate-700 dark:text-slate-200">Tên thương hiệu <span class="text-red-500">*</span></label>
        <input type="text" name="name" value="{{ old('name', $brand->name ?? '') }}" required class="mt-1.5 w-full rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 focus:border-primary focus:ring-primary" placeholder="VD: Apple" />
        @error('name')
        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="text-sm font-semibold text-slate-700 dark:text-slate-200">Slug</label>
        <input type="text" name="slug" value="{{ old('slug', $brand->slug ?? '') }}" class="mt-1.5 w-full rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 focus:border-primary focus:ring-primary" placeholder="tu-dong-neu-bo-trong" />
        @error('slug')
        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="text-sm font-semibold text-slate-700 dark:text-slate-200">Logo thương hiệu</label>
        <input id="brand-logo-input" type="file" name="logo_file" accept="image/*" class="mt-1.5 w-full rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 focus:border-primary focus:ring-primary" />
        <p class="mt-1 text-xs text-slate-500">Chọn ảnh từ máy tính (JPG, PNG, WEBP... tối đa 2MB).</p>
        <div id="brand-logo-preview-wrap" class="mt-2 h-14 w-14 rounded-lg border border-slate-200 dark:border-slate-700 overflow-hidden bg-white dark:bg-slate-800 {{ empty($brand?->logo_url) ? 'hidden' : '' }}">
            <img id="brand-logo-preview" src="{{ $brand->logo_url ?? '' }}" alt="{{ $brand->name ?? 'Logo preview' }}" class="w-full h-full object-contain">
        </div>
        @error('logo_file')
        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="text-sm font-semibold text-slate-700 dark:text-slate-200">Thứ tự</label>
        <input type="number" name="sort_order" min="0" value="{{ old('sort_order', $brand->sort_order ?? 0) }}" class="mt-1.5 w-full rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 focus:border-primary focus:ring-primary" />
        @error('sort_order')
        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>
</div>

<label class="mt-5 inline-flex items-center gap-2 text-sm font-medium text-slate-700 dark:text-slate-200">
    <input type="hidden" name="is_active" value="0">
    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $brand->is_active ?? true)) class="rounded border-slate-300 text-primary focus:ring-primary" />
    Kích hoạt thương hiệu
</label>

<div class="mt-6 flex items-center justify-end gap-3">
    <a href="{{ route('admin.brands.index') }}" class="px-4 py-2 rounded-lg border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 text-sm font-semibold hover:bg-slate-50 dark:hover:bg-slate-800">Hủy</a>
    <button type="submit" class="px-5 py-2 rounded-lg bg-primary text-black text-sm font-bold hover:brightness-105">Lưu thương hiệu</button>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const input = document.getElementById('brand-logo-input');
        const previewWrap = document.getElementById('brand-logo-preview-wrap');
        const previewImage = document.getElementById('brand-logo-preview');

        if (!input || !previewWrap || !previewImage) {
            return;
        }

        input.addEventListener('change', function(event) {
            const file = event.target.files && event.target.files[0];

            if (!file) {
                return;
            }

            const objectUrl = URL.createObjectURL(file);
            previewImage.src = objectUrl;
            previewWrap.classList.remove('hidden');
            previewImage.onload = function() {
                URL.revokeObjectURL(objectUrl);
            };
        });
    });
</script>