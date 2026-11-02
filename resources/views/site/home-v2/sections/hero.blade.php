@php
    $locale = app()->getLocale();
    $icons = config('landing_v2.icons');
    $cities = $page->heroCities();
    $experiencesUrl = route('site.experiences.index', ['locale' => $locale]);
    $slideMs = (int) config('landing_v2.hero_slide_ms');
@endphp

<section
    class="lv-hero relative isolate overflow-hidden"
    data-hero
    aria-labelledby="lv-hero-title"
    x-data="lvHero({ total: {{ $content->preview && request()->boolean('content_edit') ? 1 : max($cities->count(), 1) }}, intervalMs: {{ $slideMs }} })"
    style="--lv-slide-ms: {{ $slideMs }}ms"
>
    {{-- Background: design hero first, then each featured city's own image. --}}
    <div class="absolute inset-0 -z-10" aria-hidden="true" data-hero-media data-hero-intro>
        <img src="{{ site_content('home.hero.image')['url'] }}" alt="{{ site_content('home.hero.image')['alt'] }}" class="lv-hero-slide absolute inset-0 h-full w-full object-cover" :class="active === 0 ? 'opacity-100' : 'opacity-0'" fetchpriority="high">
        @foreach($cities->skip(1) as $index => $city)
            <img src="{{ $city['image_url'] }}" alt="" loading="lazy" class="lv-hero-slide absolute inset-0 h-full w-full object-cover opacity-0" :class="active === {{ $index }} ? 'opacity-100' : 'opacity-0'">
        @endforeach
        <div class="lv-hero-veil-x absolute inset-0"></div>
        <div class="lv-hero-veil-b absolute inset-x-0 bottom-0 h-1/3"></div>
        <div class="lv-hero-veil-t absolute inset-x-0 top-0 h-28"></div>
    </div>

    <div class="lv-container flex min-h-[inherit] flex-col justify-between pb-8 pt-32 lg:pt-44" data-hero-content>
        <div class="max-w-3xl">
            <p class="lv-hero-eyebrow flex items-center gap-3.5">
                <span class="lv-hero-rule" aria-hidden="true" data-hero-rule data-hero-intro></span>
                <span data-hero-eyebrow data-hero-intro>{{ site_content('home.hero.eyebrow') }}</span>
            </p>
            <h1 id="lv-hero-title" class="lv-serif lv-h1 mt-7" data-hero-title data-hero-intro>
                <span class="block">{{ site_content('home.hero.title_line_1') }}</span>
                <span class="block">{{ site_content('home.hero.title_line_2') }}</span>
                <span class="lv-accent block">{{ site_content('home.hero.title_accent') }}</span>
            </h1>
            <div class="lv-hero-desc mt-8 max-w-xl" data-hero-fade data-hero-intro><x-site.content key="home.hero.description" /></div>
            <div class="mt-10 flex flex-wrap items-center gap-8 lg:mt-11" data-hero-fade data-hero-intro>
                <a href="{{ $experiencesUrl }}" class="lv-btn lv-btn--clay lv-btn--lg">
                    {{ site_content('home.hero.cta') }}
                    <img src="{{ asset($icons['arrow_white']) }}" alt="" width="20" height="20" aria-hidden="true">
                </a>
                <a href="{{ site_content('home.hero.film_url') }}" class="group flex items-center gap-3.5">
                    <span class="lv-play flex h-[62px] w-[62px] items-center justify-center rounded-full">
                        <img src="{{ asset($icons['play']) }}" alt="" width="22" height="22" aria-hidden="true">
                    </span>
                    <span class="text-[17px] font-semibold text-[color:var(--lv-on-dark)]">{{ site_content('home.hero.film') }}</span>
                    <span class="text-[17px] text-[color:var(--lv-on-dark-78)]">{{ site_content('home.hero.film_duration') }}</span>
                </a>
            </div>
        </div>

        @if($cities->isNotEmpty())
            <nav class="mt-16 grid grid-cols-2 gap-5 sm:grid-cols-3 lg:mt-20 lg:grid-cols-5 lg:gap-7" aria-label="{{ site_content('home.hero.cities_label') }}">
                @foreach($cities as $index => $city)
                    <a href="{{ $city['url'] }}" class="lv-city flex flex-col gap-3" data-hero-city data-hero-intro :class="{ 'is-active': active === {{ $index }} }" @mouseenter="active = {{ $index }}">
                        <span class="lv-progress" :class="{ 'is-active': active === {{ $index }}, 'is-done': active > {{ $index }} }"><span></span></span>
                        <span class="flex items-baseline gap-2.5">
                            <span class="lv-city-num">{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</span>
                            <span class="lv-city-name">{{ $city['name'] }}</span>
                        </span>
                    </a>
                @endforeach
            </nav>
        @endif
    </div>
</section>
