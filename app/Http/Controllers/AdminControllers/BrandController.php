<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BrandController extends Controller
{
    public function index(): View
    {
        $brands = Brand::query()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('admin.categories.brands.index', [
            'brands' => $brands,
        ]);
    }

    public function create(): View
    {
        return view('admin.categories.brands.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateBrand($request);
        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['slug'] = $this->generateUniqueSlug($data['slug'] ?: $data['name']);

        Brand::create($data);

        return redirect()
            ->route('admin.brands.index')
            ->with('status', 'Đã thêm thương hiệu thành công.');
    }

    public function edit(Brand $brand): View
    {
        return view('admin.categories.brands.edit', [
            'brand' => $brand,
        ]);
    }

    public function update(Request $request, Brand $brand): RedirectResponse
    {
        $data = $this->validateBrand($request, $brand->id);
        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['slug'] = $this->generateUniqueSlug($data['slug'] ?: $data['name'], $brand->id);

        $brand->update($data);

        return redirect()
            ->route('admin.brands.index')
            ->with('status', 'Đã cập nhật thương hiệu.');
    }

    public function destroy(Brand $brand): RedirectResponse
    {
        $brand->delete();

        return redirect()
            ->route('admin.brands.index')
            ->with('status', 'Đã xóa thương hiệu.');
    }

    private function validateBrand(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'slug' => ['nullable', 'string', 'max:180'],
            'logo_url' => ['nullable', 'url', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);
    }

    private function generateUniqueSlug(string $source, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($source);

        if ($baseSlug === '') {
            $baseSlug = 'thuong-hieu';
        }

        $slug = $baseSlug;
        $counter = 1;

        while (
            Brand::query()
            ->when($ignoreId, fn($query) => $query->whereKeyNot($ignoreId))
            ->where('slug', $slug)
            ->exists()
        ) {
            $counter++;
            $slug = $baseSlug . '-' . $counter;
        }

        return $slug;
    }
}
