@extends('layouts.admin')

@section('title', $post->exists ? 'Edit Post' : 'New Post')

@section('content')
<div x-data="{ preview: @js($post->featured_image ? asset('storage/'.$post->featured_image) : null) }">
    <div class="ph-head">
        <div><h1>{{ $post->exists ? 'Edit Post' : 'New Post' }}</h1><p>Write and publish an article for your journal.</p></div>
        <div class="ph-tools"><a class="btn btn-ghost" href="{{ route('admin.blog.index') }}">← Back to Blog</a></div>
    </div>

    <form method="POST" action="{{ $post->exists ? route('admin.blog.update', $post) : route('admin.blog.store') }}" enctype="multipart/form-data">
        @csrf
        @if ($post->exists) @method('PUT') @endif
        <div class="grid2c">
            <div class="card">
                <div class="field"><label>Title <span class="req">*</span></label><input name="title" value="{{ old('title', $post->title) }}" required></div>
                <div class="field"><label>Slug</label><input name="slug" value="{{ old('slug', $post->slug) }}" placeholder="auto-generated"></div>
                <div class="field"><label>Excerpt</label><textarea name="excerpt">{{ old('excerpt', $post->excerpt) }}</textarea></div>
                <div class="field"><label>Content</label><div class="rte"><div class="rte-bar"><b>B</b><i>I</i><u>U</u><span class="sep"></span><span class="ri">≣</span></div><textarea name="content" style="min-height:260px">{{ old('content', $post->content) }}</textarea></div></div>
            </div>

            <div style="display:flex;flex-direction:column;gap:16px">
                <div class="card">
                    <h3>Publish</h3>
                    <div class="field" style="margin-top:12px"><label>Status</label><select name="status"><option value="draft" @selected(old('status', $post->status)==='draft')>Draft</option><option value="published" @selected(old('status', $post->status)==='published')>Published</option></select></div>
                    <div class="field"><label>Publish Date</label><input type="date" name="published_at" value="{{ old('published_at', optional($post->published_at)->format('Y-m-d')) }}"></div>
                    <div class="field"><label>Category</label><input name="category" value="{{ old('category', $post->category) }}" placeholder="e.g. Eye Care"></div>
                </div>
                <div class="card">
                    <h3>Featured Image</h3>
                    <div class="img-prev" style="margin-top:12px;aspect-ratio:16/9">
                        <template x-if="preview"><img :src="preview" style="width:100%;height:100%;object-fit:cover"></template>
                        <template x-if="!preview"><span style="color:var(--muted);font-size:11px">No image</span></template>
                    </div>
                    <label class="ubox" style="cursor:pointer;margin-top:10px"><svg class="icon" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"></rect><circle cx="9" cy="9" r="2"></circle><path d="M21 15l-5-5L5 21"></path></svg><b>Click to upload</b><input type="file" name="image" accept="image/*" class="hide" @change="preview=URL.createObjectURL($event.target.files[0])"></label>
                </div>
            </div>
        </div>
        <div class="save-bar" style="gap:10px"><a class="btn btn-ghost" href="{{ route('admin.blog.index') }}">Cancel</a><button type="submit" class="btn btn-primary">{{ $post->exists ? 'Update Post' : 'Save Post' }}</button></div>
    </form>
</div>
@endsection
