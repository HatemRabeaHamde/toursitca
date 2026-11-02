{{-- Verified local host portrait on the landing (data: HomeV2ViewModel::hosts()). --}}
@props(['host'])

<article class="lv-card lv-card--shadow relative h-full overflow-hidden">
    <div class="relative">
        <img src="{{ $host['image_url'] }}" alt="{{ $host['name'] }}" loading="lazy" width="220" height="236" class="lv-host-img">
        <span class="lv-host-badge absolute -bottom-6 start-4" aria-hidden="true">
            <img src="{{ landing_artwork('trust.verified', config('landing_v2.icons.trust.verified')) }}" alt="" width="22" height="22">
        </span>
    </div>
    <div class="px-5 pb-6 pt-[34px]">
        <h3 class="lv-serif lv-host-name"><a href="{{ $host['href'] }}" class="after:absolute after:inset-0">{{ $host['name'] }}</a></h3>
        <p class="lv-body mt-0.5 text-sm">{{ $host['role'] }}</p>
        @if($host['quote'])<blockquote class="lv-host-quote mt-3.5">“{{ $host['quote'] }}”</blockquote>@endif
    </div>
</article>
