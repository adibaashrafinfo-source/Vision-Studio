<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProductsController extends Controller
{
    public function index(Request $request): View
    {
        $query = Product::query()->with('category', 'primaryImage');

        if ($search = $request->string('q')->trim()->value()) {
            $query->where(fn ($q) => $q->where('name', 'like', "%{$search}%")
                ->orWhere('sku', 'like', "%{$search}%"));
        }
        if ($request->filled('category')) {
            $query->where('category_id', $request->get('category'));
        }
        if (in_array($request->get('status'), ['active', 'draft', 'inactive'], true)) {
            $query->where('status', $request->get('status'));
        }

        $products = $query->latest()->paginate(12)->withQueryString();

        $stats = [
            'total' => Product::count(),
            'active' => Product::where('status', 'active')->count(),
            'inactive' => Product::where('status', '!=', 'active')->count(),
            'featured' => Product::where('featured', true)->count(),
            'low' => Product::where('stock_qty', '<=', 5)->count(),
        ];

        $categories = Category::orderBy('name')->get(['id', 'name']);

        return view('admin.products.index', compact('products', 'stats', 'categories'));
    }

    public function create(): View
    {
        return view('admin.products.form', [
            'product' => new Product(['status' => 'active', 'stock_qty' => 0]),
            'categories' => Category::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function edit(Product $product): View
    {
        $product->load('images', 'variants');

        return view('admin.products.form', [
            'product' => $product,
            'categories' => Category::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateData($request);
        $data['slug'] = $this->uniqueSlug($data['slug'] ?? null, $data['name']);

        $product = Product::create($data);
        $this->syncImages($request, $product);
        $this->syncVideo($request, $product);
        $this->syncVariants($request, $product);

        return redirect()->route('admin.products.index')->with('status', 'Product created.');
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $data = $this->validateData($request, $product);
        $data['slug'] = $this->uniqueSlug($data['slug'] ?? null, $data['name'], $product->id);

        $product->update($data);
        $this->syncImages($request, $product);
        $this->syncVideo($request, $product);
        $this->syncVariants($request, $product);

        return redirect()->route('admin.products.index')->with('status', 'Product updated.');
    }

    public function destroyVideo(Product $product): RedirectResponse
    {
        if ($product->video_path) {
            Storage::disk('public')->delete($product->video_path);
            $product->update(['video_path' => null]);
        }

        return back()->with('status', 'Video removed.');
    }

    public function toggleFeatured(Product $product): RedirectResponse
    {
        $product->update(['featured' => ! $product->featured]);

        return back()->with('status', $product->featured ? 'Marked as featured.' : 'Removed from featured.');
    }

    public function destroyImage(Product $product, ProductImage $image): RedirectResponse
    {
        abort_unless($image->product_id === $product->id, 404);

        Storage::disk('public')->delete($image->image_path);
        $wasPrimary = $image->is_primary;
        $image->delete();

        if ($wasPrimary) {
            $product->images()->orderBy('position')->first()?->update(['is_primary' => true]);
        }

        return back()->with('status', 'Image removed.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        foreach ($product->images as $image) {
            Storage::disk('public')->delete($image->image_path);
        }
        $product->delete();

        return redirect()->route('admin.products.index')->with('status', 'Product deleted.');
    }

    private function validateData(Request $request, ?Product $product = null): array
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'sku' => ['nullable', 'string', 'max:255'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'brand' => ['nullable', 'string', 'max:120'],
            'price' => ['required', 'numeric', 'min:0'],
            'compare_price' => ['nullable', 'numeric', 'min:0'],
            'stock_qty' => ['required', 'integer', 'min:0'],
            'status' => ['required', 'in:active,draft,inactive'],
            'short_desc' => ['nullable', 'string'],
            'long_desc' => ['nullable', 'string'],
            'video' => ['nullable', 'file', 'mimetypes:video/mp4,video/quicktime,video/webm', 'max:51200'],
        ]);

        $validated['featured'] = $request->boolean('featured');
        unset($validated['video']);

        return $validated;
    }

    private function syncVariants(Request $request, Product $product): void
    {
        $variants = collect($request->input('variants', []))
            ->filter(fn ($v) => filled($v['name'] ?? null) && filled($v['value'] ?? null));

        $product->variants()->delete();
        foreach ($variants as $v) {
            $product->variants()->create([
                'name' => $v['name'],
                'value' => $v['value'],
                'price_delta' => $v['price_delta'] ?? 0,
                'stock_qty' => $v['stock_qty'] ?? 0,
            ]);
        }
    }

    private function syncVideo(Request $request, Product $product): void
    {
        if (! $request->hasFile('video')) {
            return;
        }

        if ($product->video_path) {
            Storage::disk('public')->delete($product->video_path);
        }

        $path = $request->file('video')->store('products/videos', 'public');
        $product->update(['video_path' => $path]);
    }

    private function syncImages(Request $request, Product $product): void
    {
        if (! $request->hasFile('images')) {
            // Ensure at least one primary remains if images exist.
            if ($product->images()->where('is_primary', true)->doesntExist() && $product->images()->exists()) {
                $product->images()->orderBy('position')->first()?->update(['is_primary' => true]);
            }

            return;
        }

        $startPos = (int) $product->images()->max('position');
        $hasPrimary = $product->images()->where('is_primary', true)->exists();

        foreach ($request->file('images') as $i => $file) {
            $path = $file->store('products', 'public');
            $product->images()->create([
                'image_path' => $path,
                'is_primary' => ! $hasPrimary && $i === 0,
                'position' => ++$startPos,
            ]);
        }
    }

    private function uniqueSlug(?string $slug, string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($slug ?: $name) ?: 'product';
        $slug = $base;
        $i = 1;
        while (Product::where('slug', $slug)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = $base.'-'.(++$i);
        }

        return $slug;
    }
}
