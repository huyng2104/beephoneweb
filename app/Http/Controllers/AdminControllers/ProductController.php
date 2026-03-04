<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function create(): View
    {
        $categories = Category::query()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $categoryAttributesMap = Category::query()
            ->with([
                'filterAttributes' => fn($query) => $query
                    ->orderBy('category_filter_attribute.sort_order')
                    ->orderBy('name'),
            ])
            ->get()
            ->mapWithKeys(function (Category $category) {
                return [
                    $category->id => $category->filterAttributes->map(function ($attribute) {
                        return [
                            'id' => $attribute->id,
                            'name' => $attribute->name,
                            'code' => $attribute->code,
                            'input_type' => $attribute->input_type,
                            'is_required' => (bool) $attribute->pivot->is_required,
                            'suggested_values' => $attribute->suggestedValuesArray(),
                        ];
                    })->values(),
                ];
            });

        return view('admin.products.create', [
            'categories' => $categories,
            'categoryAttributesMap' => $categoryAttributesMap,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:180'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'attributes' => ['nullable', 'array'],
            'attributes.*' => ['nullable', 'string', 'max:255'],
        ]);

        $category = Category::query()
            ->with([
                'filterAttributes' => fn($query) => $query
                    ->orderBy('category_filter_attribute.sort_order')
                    ->orderBy('name'),
            ])
            ->findOrFail((int) $data['category_id']);

        $allowedAttributes = $category->filterAttributes->keyBy('id');
        $requestAttributes = collect($data['attributes'] ?? []);

        $validatedAttributes = $this->validateDynamicAttributes($requestAttributes, $allowedAttributes);

        $product = Product::create([
            'name' => $data['name'],
            'slug' => $this->generateUniqueSlug($data['name']),
            'category_id' => $category->id,
        ]);

        $product->attributeValues()->createMany(
            $validatedAttributes
                ->map(fn($value, $attributeId) => [
                    'filter_attribute_id' => (int) $attributeId,
                    'value' => $value,
                ])
                ->values()
                ->all()
        );

        return back()->with('status', 'Đã tạo sản phẩm và lưu thuộc tính theo danh mục.');
    }

    private function validateDynamicAttributes(Collection $requestAttributes, Collection $allowedAttributes): Collection
    {
        $normalized = $requestAttributes
            ->mapWithKeys(fn($value, $attributeId) => [(int) $attributeId => trim((string) $value)])
            ->filter(fn($value, $attributeId) => $value !== '' && $allowedAttributes->has($attributeId));

        foreach ($allowedAttributes as $attributeId => $attribute) {
            if ($attribute->pivot->is_required && !$normalized->has($attributeId)) {
                throw ValidationException::withMessages([
                    'attributes.' . $attributeId => 'Thiếu thuộc tính bắt buộc: ' . $attribute->name,
                ]);
            }
        }

        return $normalized;
    }

    private function generateUniqueSlug(string $name): string
    {
        $baseSlug = Str::slug($name);

        if ($baseSlug === '') {
            $baseSlug = 'san-pham';
        }

        $slug = $baseSlug;
        $counter = 1;

        while (Product::query()->where('slug', $slug)->exists()) {
            $counter++;
            $slug = $baseSlug . '-' . $counter;
        }

        return $slug;
    }
}
