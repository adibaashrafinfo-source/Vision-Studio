<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CategoriesController extends Controller
{
    public function index(Request $request): View
    {
        $query = Category::query()->withCount('products');

        if ($search = $request->string('q')->trim()->value()) {
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('slug', 'like', "%{$search}%");
        }

        if (in_array($request->get('status'), ['active', 'inactive'], true)) {
            $query->where('status', $request->get('status'));
        }

        $categories = $query->orderBy('position')->orderBy('id')->paginate(15)->withQueryString();

        $stats = [
            'total' => Category::count(),
            'active' => Category::where('status', 'active')->count(),
            'inactive' => Category::where('status', '!=', 'active')->count(),
            'products' => \App\Models\Product::count(),
        ];

        $parents = Category::orderBy('name')->get(['id', 'name']);

        return view('admin.categories', compact('categories', 'stats', 'parents'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateData($request);
        $data = $this->handleUploads($request, $data);
        $data['slug'] = $this->uniqueSlug($data['slug'] ?? null, $data['name']);

        Category::create($data);

        return redirect()->route('admin.categories.index')->with('status', 'Category created.');
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $data = $this->validateData($request, $category);
        $data = $this->handleUploads($request, $data, $category);
        $data['slug'] = $this->uniqueSlug($data['slug'] ?? null, $data['name'], $category->id);

        $category->update($data);

        return redirect()->route('admin.categories.index')->with('status', 'Category updated.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        foreach (['image_path', 'banner_path'] as $col) {
            if ($category->$col) {
                Storage::disk('public')->delete($category->$col);
            }
        }

        $category->delete();

        return redirect()->route('admin.categories.index')->with('status', 'Category deleted.');
    }

    private function validateData(Request $request, ?Category $category = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'parent_id' => ['nullable', 'exists:categories,id'],
            'position' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', 'in:active,inactive'],
            'icon' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'max:4096'],
            'banner' => ['nullable', 'image', 'max:8192'],
        ]);
    }

    private function handleUploads(Request $request, array $data, ?Category $category = null): array
    {
        if ($request->hasFile('image')) {
            if ($category?->image_path) {
                Storage::disk('public')->delete($category->image_path);
            }
            $data['image_path'] = $request->file('image')->store('categories', 'public');
        }

        if ($request->hasFile('banner')) {
            if ($category?->banner_path) {
                Storage::disk('public')->delete($category->banner_path);
            }
            $data['banner_path'] = $request->file('banner')->store('categories/banners', 'public');
        }

        unset($data['image'], $data['banner']);
        $data['position'] ??= 0;

        return $data;
    }

    private function uniqueSlug(?string $slug, string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($slug ?: $name);
        $slug = $base ?: 'category';
        $i = 1;

        while (Category::where('slug', $slug)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = $base.'-'.(++$i);
        }

        return $slug;
    }
}
