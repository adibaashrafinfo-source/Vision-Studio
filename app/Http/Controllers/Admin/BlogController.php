<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function index(Request $request): View
    {
        $query = BlogPost::query()->with('author');

        if ($search = $request->string('q')->trim()->value()) {
            $query->where('title', 'like', "%{$search}%");
        }
        if (in_array($request->get('status'), ['draft', 'published'], true)) {
            $query->where('status', $request->get('status'));
        }

        $posts = $query->latest()->paginate(10)->withQueryString();

        $stats = [
            'total' => BlogPost::count(),
            'published' => BlogPost::where('status', 'published')->count(),
            'draft' => BlogPost::where('status', 'draft')->count(),
            'categories' => BlogPost::distinct('category')->whereNotNull('category')->count('category'),
        ];

        return view('admin.blog.index', compact('posts', 'stats'));
    }

    public function create(): View
    {
        return view('admin.blog.form', ['post' => new BlogPost(['status' => 'draft'])]);
    }

    public function edit(BlogPost $post): View
    {
        return view('admin.blog.form', compact('post'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateData($request);
        $data = $this->handleImage($request, $data);
        $data['slug'] = $this->uniqueSlug($data['slug'] ?? null, $data['title']);
        $data['author_id'] = auth('admin')->id();
        $data['published_at'] = $data['status'] === 'published' ? ($data['published_at'] ?? now()) : ($data['published_at'] ?? null);

        BlogPost::create($data);

        return redirect()->route('admin.blog.index')->with('status', 'Post created.');
    }

    public function update(Request $request, BlogPost $post): RedirectResponse
    {
        $data = $this->validateData($request, $post);
        $data = $this->handleImage($request, $data, $post);
        $data['slug'] = $this->uniqueSlug($data['slug'] ?? null, $data['title'], $post->id);
        if ($data['status'] === 'published' && ! $post->published_at && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        $post->update($data);

        return redirect()->route('admin.blog.index')->with('status', 'Post updated.');
    }

    public function destroy(BlogPost $post): RedirectResponse
    {
        if ($post->featured_image) {
            Storage::disk('public')->delete($post->featured_image);
        }
        $post->delete();

        return redirect()->route('admin.blog.index')->with('status', 'Post deleted.');
    }

    private function validateData(Request $request, ?BlogPost $post = null): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:120'],
            'excerpt' => ['nullable', 'string'],
            'content' => ['nullable', 'string'],
            'status' => ['required', 'in:draft,published'],
            'published_at' => ['nullable', 'date'],
            'image' => ['nullable', 'image', 'max:8192'],
        ]);
    }

    private function handleImage(Request $request, array $data, ?BlogPost $post = null): array
    {
        if ($request->hasFile('image')) {
            if ($post?->featured_image) {
                Storage::disk('public')->delete($post->featured_image);
            }
            $data['featured_image'] = $request->file('image')->store('blog', 'public');
        }
        unset($data['image']);

        return $data;
    }

    private function uniqueSlug(?string $slug, string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($slug ?: $title) ?: 'post';
        $slug = $base;
        $i = 1;
        while (BlogPost::where('slug', $slug)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = $base.'-'.(++$i);
        }

        return $slug;
    }
}
