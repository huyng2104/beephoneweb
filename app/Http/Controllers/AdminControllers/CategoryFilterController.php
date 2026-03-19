<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\FilterAttribute;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CategoryFilterController extends Controller
{
    public function edit(Category $category): View
    {
        $categories = Category::query()
            ->withCount('children')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $assignedAttributes = $category->filterAttributes()
            ->withPivot(['sort_order', 'is_required'])
            ->orderBy('category_filter_attribute.sort_order')
            ->orderBy('name')
            ->get();

        $attributes = FilterAttribute::query()
            ->orderBy('name')
            ->get()
            ->keyBy('id');

        return view('admin.categories.filters', [
            'category' => $category,
            'rows' => $this->flattenCategories($categories),
            'assignedAttributes' => $assignedAttributes,
            'attributes' => $attributes,
            'brands' => Brand::query()
                ->orderBy('sort_order')
                ->orderBy('name')
                ->take(7)
                ->get(),
        ]);
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $data = $request->validate([
            'selected' => ['nullable', 'array'],
            'selected.*' => ['integer', 'exists:filter_attributes,id'],
            'required' => ['nullable', 'array'],
            'required.*' => ['integer', 'exists:filter_attributes,id'],
            'sort_order' => ['nullable', 'array'],
            'sort_order.*' => ['nullable', 'integer', 'min:0'],
        ]);

        $selected = collect($data['selected'] ?? [])->map(fn($id) => (int) $id)->unique()->values();
        $required = collect($data['required'] ?? [])->map(fn($id) => (int) $id);
        $sortOrder = $data['sort_order'] ?? [];

        $syncData = [];

        foreach ($selected as $attributeId) {
            $syncData[$attributeId] = [
                'is_required' => $required->contains($attributeId),
                'sort_order' => (int) ($sortOrder[$attributeId] ?? 0),
            ];
        }

        $category->filterAttributes()->sync($syncData);

        return back()->with('status', 'Đã cập nhật thuộc tính lọc cho danh mục.');
    }

    public function toggleFilterable(Request $request, Category $category, FilterAttribute $attribute): RedirectResponse
    {
        if (!$category->filterAttributes()->whereKey($attribute->id)->exists()) {
            return back()->with('status', 'Thuộc tính không thuộc danh mục này.');
        }

        $attribute->update([
            'is_filterable' => !$attribute->is_filterable,
        ]);

        return back()->with('status', 'Đã cập nhật trạng thái hiển thị lọc.');
    }

    public function detachAttribute(Category $category, FilterAttribute $attribute): RedirectResponse
    {
        $category->filterAttributes()->detach($attribute->id);

        return back()->with('status', 'Đã gỡ thuộc tính khỏi danh mục.');
    }

    public function storeAttribute(Request $request, Category $category): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'code' => ['nullable', 'string', 'max:120'],
            'input_type' => ['required', 'in:text,select,number'],
            'suggested_values' => ['nullable', 'string'],
            'is_filterable' => ['nullable', 'boolean'],
        ]);

        $baseCode = $data['code'] ?: Str::slug($data['name'], '_');
        $code = $baseCode;
        $counter = 1;

        while (FilterAttribute::query()->where('code', $code)->exists()) {
            $counter++;
            $code = $baseCode . '_' . $counter;
        }

        $attribute = FilterAttribute::create([
            'name' => $data['name'],
            'code' => $code,
            'input_type' => $data['input_type'],
            'suggested_values' => $data['suggested_values'] ?? null,
            'is_filterable' => (bool) ($data['is_filterable'] ?? true),
        ]);

        $category->filterAttributes()->syncWithoutDetaching([
            $attribute->id => [
                'sort_order' => 0,
                'is_required' => false,
            ],
        ]);

        return back()->with('status', 'Đã tạo thuộc tính và gán vào danh mục.');
    }

    private function flattenCategories(Collection $categories): array
    {
        $grouped = $categories->groupBy('parent_id');

        return $this->buildRows($grouped, null, 0);
    }

    private function buildRows(Collection $grouped, ?int $parentId, int $depth): array
    {
        $rows = [];

        foreach ($grouped->get($parentId, collect()) as $item) {
            $rows[] = [
                'category' => $item,
                'depth' => $depth,
            ];

            $rows = array_merge($rows, $this->buildRows($grouped, $item->id, $depth + 1));
        }

        return $rows;
    }
}
