@extends('layouts.admin')

@section('title', 'Blog')

@section('content')
    <div class="ph-head">
        <div><h1>Blog Management</h1><p>Write, publish and manage articles for the {{ config('app.name') }} journal.</p></div>
        <div class="ph-tools"><a class="btn btn-primary" href="{{ route('admin.blog.create') }}"><svg class="icon sm" viewBox="0 0 24 24" style="stroke:#fff"><path d="M12 5v14"></path><path d="M5 12h14"></path></svg>New Post</a></div>
    </div>

    <div class="stat-row s4">
        <div class="stat"><div class="top"><span class="ic b"><svg class="icon" viewBox="0 0 24 24"><path d="M5 4h14v16H5z"></path><path d="M8 9h8"></path><path d="M8 13h8"></path></svg></span><div><div class="v tnum">{{ $stats['total'] }}</div><div class="k">Total Posts</div></div></div></div>
        <div class="stat"><div class="top"><span class="ic g"><svg class="icon" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"></circle><path d="M8 12l3 3 5-6"></path></svg></span><div><div class="v tnum">{{ $stats['published'] }}</div><div class="k">Published</div></div></div></div>
        <div class="stat"><div class="top"><span class="ic a"><svg class="icon" viewBox="0 0 24 24"><path d="M12 20h9"></path><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4z"></path></svg></span><div><div class="v tnum">{{ $stats['draft'] }}</div><div class="k">Drafts</div></div></div></div>
        <div class="stat"><div class="top"><span class="ic p"><svg class="icon" viewBox="0 0 24 24"><rect x="3" y="3" width="8" height="8" rx="2"></rect><rect x="13" y="3" width="8" height="8" rx="2"></rect><rect x="3" y="13" width="8" height="8" rx="2"></rect></svg></span><div><div class="v tnum">{{ $stats['categories'] }}</div><div class="k">Categories</div></div></div></div>
    </div>

    <div class="card">
        <form class="tbar" method="GET" action="{{ route('admin.blog.index') }}">
            <div class="srch"><svg class="icon" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"></circle><path d="M21 21l-4-4"></path></svg><input name="q" value="{{ request('q') }}" placeholder="Search posts…"></div>
            <select class="sel" name="status" onchange="this.form.submit()">
                <option value="">All Status</option>
                <option value="published" @selected(request('status')==='published')>Published</option>
                <option value="draft" @selected(request('status')==='draft')>Draft</option>
            </select>
        </form>
        <table class="tbl"><thead><tr><th>Image</th><th>Title</th><th>Category</th><th>Author</th><th>Status</th><th>Date</th><th></th></tr></thead>
        <tbody>
            @forelse ($posts as $post)
                <tr>
                    <td><div class="pthumb">
                        @if ($post->featured_image)<img class="pimg" src="{{ asset('storage/'.$post->featured_image) }}" alt="">
                        @else<svg class="icon" viewBox="0 0 24 24" style="stroke:var(--muted)"><rect x="3" y="3" width="18" height="18" rx="2"></rect><circle cx="9" cy="9" r="2"></circle><path d="M21 15l-5-5L5 21"></path></svg>@endif
                    </div></td>
                    <td class="pname" style="max-width:280px">{{ $post->title }}</td>
                    <td class="pcat">{{ $post->category ?: '—' }}</td>
                    <td class="pcat">{{ $post->author?->name ?? '—' }}</td>
                    <td><span class="pillst {{ $post->status === 'published' ? 'done' : 'pend' }}">{{ ucfirst($post->status) }}</span></td>
                    <td class="pcat">{{ ($post->published_at ?? $post->created_at)->format('M d, Y') }}</td>
                    <td><div class="acts">
                        <a class="ic-btn" href="{{ route('admin.blog.edit', $post) }}" title="Edit"><svg class="icon xs" viewBox="0 0 24 24"><path d="M12 20h9"></path><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4z"></path></svg></a>
                        <form method="POST" action="{{ route('admin.blog.destroy', $post) }}" onsubmit="return confirm('Delete this post?')" style="display:inline">@csrf @method('DELETE')<button class="ic-btn danger"><svg class="icon xs" viewBox="0 0 24 24"><path d="M4 7h16"></path><path d="M6 7l1 13h10l1-13"></path></svg></button></form>
                    </div></td>
                </tr>
            @empty
                <tr><td colspan="7" style="color:var(--muted);padding:22px 12px">No posts yet. Click “New Post”.</td></tr>
            @endforelse
        </tbody></table>
        <div class="tbl-foot">
            <div class="pginfo">Showing {{ $posts->firstItem() ?? 0 }} to {{ $posts->lastItem() ?? 0 }} of {{ $posts->total() }} posts</div>
            <div class="pg">{{ $posts->links('vendor.pagination.admin') }}</div>
        </div>
    </div>
@endsection
