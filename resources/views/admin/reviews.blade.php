@extends('layouts.admin')

@section('title', 'Reviews')

@php $avColors = ['', 'c2', 'c3', 'c4', 'c5']; $stPill = ['approved'=>'done','pending'=>'pend','rejected'=>'cancel']; @endphp

@section('content')
    <div class="ph-head">
        <div><h1>Reviews Management</h1><p>Moderate, respond to and manage customer reviews across all products.</p></div>
    </div>

    <div class="stat-row s5">
        <div class="stat"><div class="top"><span class="ic b"><svg class="icon" viewBox="0 0 24 24"><path d="M12 3l2.6 5.5 6 .8-4.4 4.1 1.1 6-5.3-2.9-5.3 2.9 1.1-6L3.4 9.3l6-.8z"></path></svg></span><div><div class="v tnum">{{ $stats['total'] }}</div><div class="k">Total Reviews</div></div></div></div>
        <div class="stat"><div class="top"><span class="ic a"><svg class="icon" viewBox="0 0 24 24"><path d="M12 3l2.6 5.5 6 .8-4.4 4.1 1.1 6-5.3-2.9-5.3 2.9 1.1-6L3.4 9.3l6-.8z"></path></svg></span><div><div class="v tnum">{{ $stats['avg'] ?: '0.0' }}</div><div class="k">Average Rating</div></div></div></div>
        <div class="stat"><div class="top"><span class="ic g"><svg class="icon" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"></circle><path d="M8 12l3 3 5-6"></path></svg></span><div><div class="v tnum">{{ $stats['approved'] }}</div><div class="k">Approved Reviews</div></div></div></div>
        <div class="stat"><div class="top"><span class="ic p"><svg class="icon" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"></circle><path d="M12 7v5l3 2"></path></svg></span><div><div class="v tnum">{{ $stats['pending'] }}</div><div class="k">Pending Reviews</div></div></div></div>
        <div class="stat"><div class="top"><span class="ic r"><svg class="icon" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"></circle><path d="M15 9l-6 6"></path><path d="M9 9l6 6"></path></svg></span><div><div class="v tnum">{{ $stats['rejected'] }}</div><div class="k">Rejected</div></div></div></div>
    </div>

    <div class="card">
        <form class="tbar" method="GET" action="{{ route('admin.reviews.index') }}">
            <div style="flex:1"></div>
            <select class="sel" name="rating" onchange="this.form.submit()"><option value="">All Ratings</option>@for($r=5;$r>=1;$r--)<option value="{{ $r }}" @selected(request('rating')==$r)>{{ $r }} Stars</option>@endfor</select>
            <select class="sel" name="status" onchange="this.form.submit()">
                <option value="">All Status</option>
                @foreach (['pending','approved','rejected'] as $st)<option value="{{ $st }}" @selected(request('status')===$st)>{{ ucfirst($st) }}</option>@endforeach
            </select>
        </form>
        <table class="tbl"><thead><tr><th>Customer</th><th>Product</th><th>Rating</th><th>Review</th><th>Status</th><th>Date</th><th>Actions</th></tr></thead>
        <tbody>
            @forelse ($reviews as $review)
                @php $name = $review->customer?->name ?? $review->author_name ?? 'Anonymous'; $init = collect(explode(' ', $name))->take(2)->map(fn($w)=>mb_strtoupper(mb_substr($w,0,1)))->implode(''); @endphp
                <tr>
                    <td><div class="cust"><div class="cav {{ $avColors[$review->id % 5] }}">{{ $init }}</div><div class="pname">{{ $name }}</div></div></td>
                    <td class="pcat">{{ $review->product?->name ?? '—' }}</td>
                    <td><span class="starsrow">{{ str_repeat('★', $review->rating).str_repeat('☆', 5 - $review->rating) }}</span></td>
                    <td style="max-width:280px"><div class="pname" style="font-size:12.5px">{{ $review->title }}</div><div class="pcat">{{ \Illuminate\Support\Str::limit($review->comment, 80) }}</div></td>
                    <td><span class="pillst {{ $stPill[$review->status] ?? 'pend' }}">{{ ucfirst($review->status) }}</span></td>
                    <td class="pcat">{{ $review->created_at->format('M d, Y') }}</td>
                    <td><div class="acts">
                        @if ($review->status !== 'approved')
                            <form method="POST" action="{{ route('admin.reviews.update', $review) }}" style="display:inline">@csrf @method('PUT')<input type="hidden" name="status" value="approved"><button class="ic-btn" title="Approve"><svg class="icon xs" viewBox="0 0 24 24" style="stroke:var(--green)"><path d="M20 6L9 17l-5-5"></path></svg></button></form>
                        @endif
                        @if ($review->status !== 'rejected')
                            <form method="POST" action="{{ route('admin.reviews.update', $review) }}" style="display:inline">@csrf @method('PUT')<input type="hidden" name="status" value="rejected"><button class="ic-btn danger" title="Reject"><svg class="icon xs" viewBox="0 0 24 24"><path d="M6 6l12 12"></path><path d="M18 6L6 18"></path></svg></button></form>
                        @endif
                        <form method="POST" action="{{ route('admin.reviews.destroy', $review) }}" onsubmit="return confirm('Delete this review?')" style="display:inline">@csrf @method('DELETE')<button class="ic-btn danger" title="Delete"><svg class="icon xs" viewBox="0 0 24 24"><path d="M4 7h16"></path><path d="M6 7l1 13h10l1-13"></path></svg></button></form>
                    </div></td>
                </tr>
            @empty
                <tr><td colspan="7" style="color:var(--muted);padding:22px 12px">No reviews found.</td></tr>
            @endforelse
        </tbody></table>
        <div class="tbl-foot">
            <div class="pginfo">Showing {{ $reviews->firstItem() ?? 0 }} to {{ $reviews->lastItem() ?? 0 }} of {{ $reviews->total() }} reviews</div>
            <div class="pg">{{ $reviews->links('vendor.pagination.admin') }}</div>
        </div>
    </div>
@endsection
