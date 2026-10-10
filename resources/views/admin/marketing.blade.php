@extends('layouts.admin')

@section('title', 'Marketing')

@php $stPill = ['active'=>'done','scheduled'=>'proc','draft'=>'pend','ended'=>'cancel']; @endphp

@section('content')
<div x-data="marketingPage()">
    <div class="ph-head">
        <div><h1>Marketing</h1><p>Plan, launch and track marketing campaigns across every channel.</p></div>
        <div class="ph-tools"><button class="btn btn-primary" @click="openAdd()"><svg class="icon sm" viewBox="0 0 24 24" style="stroke:#fff"><path d="M12 5v14"></path><path d="M5 12h14"></path></svg>New Campaign</button></div>
    </div>

    <div class="stat-row s4">
        <div class="stat"><div class="top"><span class="ic b"><svg class="icon" viewBox="0 0 24 24"><path d="M3 11v2a2 2 0 0 0 2 2h2l5 4V5l-5 4H5a2 2 0 0 0-2 2z"></path></svg></span><div><div class="v tnum">{{ $stats['total'] }}</div><div class="k">Total Campaigns</div></div></div></div>
        <div class="stat"><div class="top"><span class="ic g"><svg class="icon" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"></circle><path d="M8 12l3 3 5-6"></path></svg></span><div><div class="v tnum">{{ $stats['active'] }}</div><div class="k">Active Campaigns</div></div></div></div>
        <div class="stat"><div class="top"><span class="ic p"><svg class="icon" viewBox="0 0 24 24"><circle cx="9" cy="8" r="3.5"></circle><path d="M2 20c0-3.5 3-6 7-6s7 2.5 7 6"></path></svg></span><div><div class="v tnum">{{ number_format($stats['audience']) }}</div><div class="k">Total Reach</div></div></div></div>
        <div class="stat"><div class="top"><span class="ic a"><svg class="icon" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"></circle><path d="M12 7v5l3 2"></path></svg></span><div><div class="v tnum">{{ $stats['scheduled'] }}</div><div class="k">Scheduled</div></div></div></div>
    </div>

    <div class="pg-panel">
        <div class="card">
            <h3>Recent Campaigns</h3>
            <table class="tbl" style="margin-top:12px"><thead><tr><th>Campaign</th><th>Channel</th><th>Reach</th><th>Period</th><th>Status</th><th></th></tr></thead>
            <tbody>
                @forelse ($campaigns as $campaign)
                    <tr>
                        <td class="pname">{{ $campaign->name }}</td>
                        <td>{{ ucfirst($campaign->channel ?: '—') }}</td>
                        <td class="tnum">{{ number_format($campaign->audience_count) }}</td>
                        <td class="pcat">{{ $campaign->starts_at?->format('M d') ?? '—' }} – {{ $campaign->ends_at?->format('M d, Y') ?? '—' }}</td>
                        <td><span class="pillst {{ $stPill[$campaign->status] ?? 'pend' }}">{{ ucfirst($campaign->status) }}</span></td>
                        <td><div class="acts">
                            <button type="button" class="ic-btn" title="Edit" @click="openEdit({{ Illuminate\Support\Js::from([
                                'id'=>$campaign->id,'name'=>$campaign->name,'channel'=>$campaign->channel,'audience_count'=>$campaign->audience_count,
                                'status'=>$campaign->status,'starts_at'=>$campaign->starts_at?->format('Y-m-d'),'ends_at'=>$campaign->ends_at?->format('Y-m-d'),
                            ]) }})"><svg class="icon xs" viewBox="0 0 24 24"><path d="M12 20h9"></path><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4z"></path></svg></button>
                            <form method="POST" action="{{ route('admin.marketing.destroy', $campaign) }}" onsubmit="return confirm('Delete campaign?')" style="display:inline">@csrf @method('DELETE')<button class="ic-btn danger"><svg class="icon xs" viewBox="0 0 24 24"><path d="M4 7h16"></path><path d="M6 7l1 13h10l1-13"></path></svg></button></form>
                        </div></td>
                    </tr>
                @empty
                    <tr><td colspan="6" style="color:var(--muted);padding:22px 12px">No campaigns yet.</td></tr>
                @endforelse
            </tbody></table>
            <div class="tbl-foot"><div class="pginfo">Showing {{ $campaigns->firstItem() ?? 0 }} to {{ $campaigns->lastItem() ?? 0 }} of {{ $campaigns->total() }}</div><div class="pg">{{ $campaigns->links('vendor.pagination.admin') }}</div></div>
        </div>

        <div class="panel">
            <form method="POST" :action="formAction">
                @csrf
                <input type="hidden" name="_method" :value="mode==='edit' ? 'PUT' : 'POST'">
                <div class="panel-h"><h3 x-text="mode==='edit' ? 'Edit Campaign' : 'New Campaign'"></h3><button type="button" class="panel-x" @click="reset()">✕</button></div>
                <div class="panel-b">
                    <div class="field"><label>Campaign Name <span class="req">*</span></label><input name="name" x-model="form.name" required></div>
                    <div class="field"><label>Channel</label><select name="channel" x-model="form.channel"><option value="">—</option><option value="email">Email</option><option value="sms">SMS</option><option value="social">Social</option><option value="push">Push</option></select></div>
                    <div class="field"><label>Audience Reach</label><input type="number" name="audience_count" x-model="form.audience_count"></div>
                    <div class="f2"><div class="field"><label>Starts</label><input type="date" name="starts_at" x-model="form.starts_at"></div><div class="field"><label>Ends</label><input type="date" name="ends_at" x-model="form.ends_at"></div></div>
                    <div class="field"><label>Status</label><select name="status" x-model="form.status"><option value="draft">Draft</option><option value="scheduled">Scheduled</option><option value="active">Active</option><option value="ended">Ended</option></select></div>
                </div>
                <div class="panel-f"><button type="button" class="btn btn-ghost" @click="reset()">Cancel</button><button type="submit" class="btn btn-primary" x-text="mode==='edit' ? 'Update' : 'Create'"></button></div>
            </form>
        </div>
    </div>
</div>

<script>
function marketingPage() {
    const blank = { id:null, name:'', channel:'', audience_count:'', status:'draft', starts_at:'', ends_at:'' };
    return {
        mode:'add', baseUrl: @json(route('admin.marketing.index')), form: { ...blank },
        get formAction() { return this.mode === 'edit' ? this.baseUrl + '/' + this.form.id : this.baseUrl; },
        openAdd() { this.reset(); },
        openEdit(c) { this.mode='edit'; this.form = { ...blank, ...c, audience_count:c.audience_count??'', starts_at:c.starts_at??'', ends_at:c.ends_at??'' }; },
        reset() { this.mode='add'; this.form = { ...blank }; },
    };
}
</script>
@endsection
