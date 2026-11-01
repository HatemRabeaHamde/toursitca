{{-- "Your route" trip-builder preview. Static UI from the design — no trip-planning backend yet. --}}
@php($route = site_content('home.featured.route'))

<div data-reveal-group>
<div data-reveal="fade" class="lv-route mt-12 flex flex-col gap-6 rounded-2xl px-6 py-5 xl:h-24 xl:flex-row xl:items-center xl:justify-between xl:px-7 xl:py-0">
    <div class="flex flex-col gap-5 md:flex-row md:items-center md:gap-[22px]">
        <div class="shrink-0" data-reveal="fade" data-reveal-at="<0.25">
            <p class="lv-route-label">{{ $route['label'] }}</p>
            <p class="mt-1 text-[15px] text-[color:var(--lv-on-dark-78)]">{{ $route['meta'] }}</p>
        </div>
        <span class="hidden h-11 w-px bg-[rgba(250,246,235,.18)] md:block" aria-hidden="true"></span>
        <ol class="flex flex-wrap items-center gap-2 xl:flex-nowrap">
            @foreach($route['stops'] as $stop)
                <li class="flex items-center gap-2" data-reveal="pop" data-reveal-at="<0.14">
                    <span class="lv-route-stop">
                        <span class="lv-route-num">{{ $loop->iteration }}</span>
                        {{ $stop }}
                    </span>
                    @unless($loop->last)<span class="h-px w-5 bg-[rgba(250,246,235,.4)]" aria-hidden="true"></span>@endunless
                </li>
            @endforeach
            <li data-reveal="pop" data-reveal-at="<0.14"><span class="lv-route-add">{{ $route['add'] }}</span></li>
        </ol>
    </div>

    <div class="flex items-center justify-between gap-[22px] xl:justify-end" data-reveal="fade" data-reveal-at="<0.2">
        <div class="text-end">
            <p class="text-[13px] text-[color:var(--lv-on-dark-78)]">{{ $route['estimated'] }}</p>
            <p class="whitespace-nowrap text-xl font-semibold text-[color:var(--lv-on-dark)]">{{ $route['price'] }}</p>
        </div>
        <a href="{{ route('site.experiences.index', ['locale' => app()->getLocale()]) }}" class="lv-btn lv-btn--gold h-[52px] px-[26px] text-base">
            {{ $route['cta'] }}
            <img src="{{ asset(config('landing_v2.icons.arrow_dark')) }}" alt="" width="20" height="20" aria-hidden="true">
        </a>
    </div>
</div>
</div>
