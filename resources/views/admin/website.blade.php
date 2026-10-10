@extends('layouts.admin')

@section('title', 'Website')

@section('content')
<div x-data="{ hero: @js(($s['hero_image'] ?? null) ? asset('storage/'.$s['hero_image']) : null) }">
    <div class="ph-head">
        <div><h1>Website Management</h1><p>Control what shows on your live storefront — hero, announcement bar, footer and social links.</p></div>
    </div>

    <form method="POST" action="{{ route('admin.website.update') }}" enctype="multipart/form-data">
        @csrf @method('PUT')
        <div class="set-grid">
            <div class="card">
                <h3 class="sec-ttl">Hero Section</h3>
                <p class="sec-sub">The large banner at the top of your homepage.</p>
                <div class="field"><label>Headline</label><input name="hero_headline" value="{{ old('hero_headline', $s['hero_headline'] ?? 'Premium Eyewear For a Clearer Tomorrow') }}"></div>
                <div class="field"><label>Sub-headline</label><textarea name="hero_subheadline">{{ old('hero_subheadline', $s['hero_subheadline'] ?? '') }}</textarea></div>
                <div class="f2">
                    <div class="field"><label>Button Text</label><input name="hero_cta_text" value="{{ old('hero_cta_text', $s['hero_cta_text'] ?? 'Shop Now') }}"></div>
                    <div class="field"><label>Button Link</label><input name="hero_cta_link" value="{{ old('hero_cta_link', $s['hero_cta_link'] ?? '/products') }}"></div>
                </div>
                <div class="field"><label>Hero Image</label>
                    <div class="img-prev" style="aspect-ratio:16/7">
                        <template x-if="hero"><img :src="hero" style="width:100%;height:100%;object-fit:cover"></template>
                        <template x-if="!hero"><span style="color:var(--muted);font-size:11px">No hero image</span></template>
                    </div>
                    <label class="ubox" style="cursor:pointer;margin-top:10px"><svg class="icon" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"></rect><circle cx="9" cy="9" r="2"></circle><path d="M21 15l-5-5L5 21"></path></svg><b>Upload hero image</b><span>Recommended 1920×900 px</span><input type="file" name="hero_image" accept="image/*" class="hide" @change="hero=URL.createObjectURL($event.target.files[0])"></label>
                </div>
            </div>

            <div style="display:flex;flex-direction:column;gap:16px">
                <div class="card">
                    <h3 class="sec-ttl">Announcement Bar</h3>
                    <p class="sec-sub">Thin bar shown above the header.</p>
                    <div class="field"><label>Announcement Text</label><input name="announcement_text" value="{{ old('announcement_text', $s['announcement_text'] ?? 'Free Shipping on Orders Over ৳2,000') }}"></div>
                </div>
                <div class="card">
                    <h3 class="sec-ttl">Footer</h3>
                    <div class="field"><label>About Text</label><textarea name="footer_about">{{ old('footer_about', $s['footer_about'] ?? '') }}</textarea></div>
                    <div class="f2"><div class="field"><label>Phone</label><input name="footer_phone" value="{{ old('footer_phone', $s['footer_phone'] ?? '') }}"></div><div class="field"><label>Email</label><input name="footer_email" value="{{ old('footer_email', $s['footer_email'] ?? '') }}"></div></div>
                    <div class="field"><label>Address</label><input name="footer_address" value="{{ old('footer_address', $s['footer_address'] ?? '') }}"></div>
                </div>
                <div class="card">
                    <h3 class="sec-ttl">Social Links</h3>
                    <div class="soc-row"><span class="soc-ic fb">f</span><input name="social_facebook" value="{{ old('social_facebook', $s['social_facebook'] ?? '') }}" placeholder="Facebook URL"></div>
                    <div class="soc-row"><span class="soc-ic ig">ig</span><input name="social_instagram" value="{{ old('social_instagram', $s['social_instagram'] ?? '') }}" placeholder="Instagram URL"></div>
                    <div class="soc-row"><span class="soc-ic yt">yt</span><input name="social_youtube" value="{{ old('social_youtube', $s['social_youtube'] ?? '') }}" placeholder="YouTube URL"></div>
                    <div class="soc-row"><span class="soc-ic tk">tk</span><input name="social_tiktok" value="{{ old('social_tiktok', $s['social_tiktok'] ?? '') }}" placeholder="TikTok URL"></div>
                    <div class="soc-row"><span class="soc-ic li">in</span><input name="social_linkedin" value="{{ old('social_linkedin', $s['social_linkedin'] ?? '') }}" placeholder="LinkedIn URL"></div>
                </div>
            </div>
        </div>
        <div class="save-bar"><button type="submit" class="btn btn-primary">Save Changes</button></div>
    </form>
</div>
@endsection
