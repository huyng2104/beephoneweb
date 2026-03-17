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

class CategoryController extends Controller
{
    public function index(Request $request): View
    {
        $requestedMode = $request->string('mode')->toString();

        if ($requestedMode === 'brands') {
            return redirect()->route('admin.brands.index');
        }

        $categories = Category::query()
            ->with('parent')
            ->withCount('children')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $rows = $this->flattenCategories($categories);

        $viewMode = in_array($requestedMode, ['filters'], true)
            ? $requestedMode
            : 'structure';

        $firstDepthTwo = collect($rows)->firstWhere('depth', 2);
        $defaultCategoryId = $firstDepthTwo['category']->id ?? ($rows[0]['category']->id ?? null);

        $activeCategoryId = $request->integer('category_id') ?: $defaultCategoryId;
        $activeCategory = $activeCategoryId
            ? Category::query()->find($activeCategoryId)
            : null;

        $assignedAttributes = collect();
        $attributes = collect();

        if ($viewMode === 'filters' && $activeCategory) {
            $assignedAttributes = $activeCategory->filterAttributes()
                ->withPivot(['sort_order', 'is_required'])
                ->orderBy('category_filter_attribute.sort_order')
                ->orderBy('name')
                ->get();

            $attributes = FilterAttribute::query()
                ->orderBy('name')
                ->get()
                ->keyBy('id');
        }

        return view('admin.categories.index', [
            'rows' => $rows,
            'viewMode' => $viewMode,
            'activeCategoryId' => $activeCategoryId,
            'activeCategory' => $activeCategory,
            'assignedAttributes' => $assignedAttributes,
            'attributes' => $attributes,
            'brands' => Brand::query()
                ->orderBy('sort_order')
                ->orderBy('name')
                ->take(7)
                ->get(),
            'allBrands' => Brand::query()
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.categories.create', [
            'categoryOptions' => $this->categoryOptions(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateCategory($request);
        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['slug'] = $this->generateUniqueSlug($data['slug'] ?: $data['name']);

        Category::create($data);

        return redirect()
            ->route('admin.categories.index')
            ->with('status', 'Đã thêm danh mục thành công.');
    }

    public function edit(Category $category): View
    {
        return view('admin.categories.edit', [
            'category' => $category,
            'categoryOptions' => $this->categoryOptions($category->id),
        ]);
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $data = $this->validateCategory($request, $category->id);
        $data['sort_order'] = $data['sort_order'] ?? 0;

        if ($this->wouldCreateCycle($category->id, $data['parent_id'] ?? null)) {
            return back()->withErrors([
                'parent_id' => 'Danh mục cha không hợp lệ.',
            ])->withInput();
        }

        $data['slug'] = $this->generateUniqueSlug($data['slug'] ?: $data['name'], $category->id);

        $category->update($data);

        return redirect()
            ->route('admin.categories.index')
            ->with('status', 'Đã cập nhật danh mục.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        $category->delete();

        return redirect()
            ->route('admin.categories.index')
            ->with('status', 'Đã xóa danh mục.');
    }

    public function trash(): View
    {
        $categories = Category::onlyTrashed()
            ->with('parent')
            ->withCount('children')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $rows = $this->flattenCategories($categories);

        return view('admin.categories.trash', [
            'rows' => $rows,
        ]);
    }

    public function restore(int $id): RedirectResponse
    {
        $category = Category::onlyTrashed()->findOrFail($id);
        $category->restore();

        return redirect()
            ->route('admin.categories.index')
            ->with('status', 'Đã phục hồi danh mục.');
    }

    public function forceDelete(int $id): RedirectResponse
    {
        $category = Category::onlyTrashed()->findOrFail($id);
        $category->forceDelete();

        return redirect()
            ->route('admin.categories.index')
            ->with('status', 'Đã xóa vĩnh viễn danh mục.');
    }

    private function validateCategory(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'slug' => ['nullable', 'string', 'max:180'],
            'parent_id' => ['nullable', 'integer', 'exists:categories,id'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);
    }

    private function generateUniqueSlug(string $source, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($source);

        if ($baseSlug === '') {
            $baseSlug = 'danh-muc';
        }

        $slug = $baseSlug;
        $counter = 1;

        while (
            Category::query()
            ->when($ignoreId, fn($query) => $query->whereKeyNot($ignoreId))
            ->where('slug', $slug)
            ->exists()
        ) {
            $counter++;
            $slug = $baseSlug . '-' . $counter;
        }

        return $slug;
    }

    private function wouldCreateCycle(int $categoryId, ?int $parentId): bool
    {
        if ($parentId === null) {
            return false;
        }

        $visited = [];
        $currentParentId = $parentId;

        while ($currentParentId !== null) {
            if ($currentParentId === $categoryId) {
                return true;
            }

            if (in_array($currentParentId, $visited, true)) {
                return true;
            }

            $visited[] = $currentParentId;
            $currentParentId = Category::query()
                ->whereKey($currentParentId)
                ->value('parent_id');
        }

        return false;
    }

    private function categoryOptions(?int $excludeId = null): array
    {
        $categories = Category::query()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $grouped = $categories->groupBy('parent_id');

        return $this->buildOptions($grouped, null, '', $excludeId);
    }

    private function buildOptions(Collection $grouped, ?int $parentId, string $prefix, ?int $excludeId): array
    {
        $options = [];

        foreach ($grouped->get($parentId, collect()) as $category) {
            if ($excludeId && $category->id === $excludeId) {
                continue;
            }

            $options[$category->id] = $prefix . $category->name;
            $options += $this->buildOptions($grouped, $category->id, $prefix . '— ', $excludeId);
        }

        return $options;
    }

    private function flattenCategories(Collection $categories): array
    {
        $grouped = $categories->groupBy('parent_id');

        return $this->buildRows($grouped, null, 0);
    }

    private function buildRows(Collection $grouped, ?int $parentId, int $depth): array
    {
        $rows = [];

        foreach ($grouped->get($parentId, collect()) as $category) {
            $rows[] = [
                'category' => $category,
                'depth' => $depth,
            ];

            $rows = array_merge($rows, $this->buildRows($grouped, $category->id, $depth + 1));
        }

        return $rows;
    }
}
