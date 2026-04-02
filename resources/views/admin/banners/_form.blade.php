<div class="space-y-4">
    <div>
        <label class="block text-sm font-medium text-slate-700">Tiêu đề</label>
        <input type="text" name="title" value="{{ old('title', $banner->title ?? '') }}" class="mt-1 block w-full rounded-lg border border-slate-200 px-3 py-2" />
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700">Liên kết (nếu có)</label>
        <input type="text" name="link" value="{{ old('link', $banner->link ?? '') }}" class="mt-1 block w-full rounded-lg border border-slate-200 px-3 py-2" />
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700">Loại</label>
        <select name="type" class="mt-1 block w-full rounded-lg border border-slate-200 px-3 py-2">
            <option value="slider" {{ (old('type', $banner->type ?? '') == 'slider') ? 'selected' : '' }}>Slider</option>
            <option value="featured" {{ (old('type', $banner->type ?? '') == 'featured') ? 'selected' : '' }}>Sản phẩm nổi bật</option>
            <option value="new" {{ (old('type', $banner->type ?? '') == 'new') ? 'selected' : '' }}>Mới</option>
            <option value="promo" {{ (old('type', $banner->type ?? '') == 'promo') ? 'selected' : '' }}>Khuyến mãi</option>
        </select>
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700">Hình ảnh</label>
        <input type="file" name="image" class="mt-1 block w-full" />
        @if (!empty($banner->image_url))
        <div class="mt-2 size-20 rounded overflow-hidden">
            <img src="{{ $banner->image_url }}" alt="{{ $banner->title }}" class="w-48 h-24 object-contain">
        </div>
        @endif
    </div>

    <div class="grid grid-cols-2 gap-4">
        <label class="flex items-center gap-3">
            <input type="checkbox" name="is_active" value="1" {{ (old('is_active', $banner->is_active ?? 1) ? 'checked' : '') }} />
            <span class="text-sm">Kích hoạt</span>
        </label>

        <div>
            <label class="block text-sm font-medium text-slate-700">Thứ tự</label>
            <input type="number" name="sort_order" value="{{ old('sort_order', $banner->sort_order ?? 0) }}" class="mt-1 block w-full rounded-lg border border-slate-200 px-3 py-2" />
        </div>
    </div>
</div>