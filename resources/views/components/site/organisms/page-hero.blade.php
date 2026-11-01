{{--
    Cinematic page hero (Discover 760px · Destination / Interest 680px).
    Optional slot: extra hero content under the lede (Experience detail: meta, rating, photos).
    Wired to the motion intro through the data-hero* hooks (see modules/motion/hero-intro.js).
--}}
@props([
    'image',
    'title',
    'accent' => null,
    'after' => null,
    'eyebrow' => null,
    'location' => null,
    'lede' => null,
    'breadcrumb' => [],
    'primary' => null,
    'secondary' => null,
    'size' => 'default',
])

<section {{ $attributes->class(['lv-page-hero relative isolate overflow-hidden', 'lv-page-hero--tall' => $size === 'tall']) }} data-hero aria-labelledby="lv-page-title">
    <div class="absolute inset-0 -z-10" aria-hidden="true" data-hero-media data-hero-intro>
        <img src="{{ $image }}" alt="" class="h-full w-full object-cover" fetchpriority="high">
        <div class="lv-page-hero__veil absolute inset-0"></div>
    </div>

    <div class="lv-container flex min-h-[inherit] flex-col pb-16 pt-28 lg:pt-[122px]" data-hero-content>
        @if($breadcrumb)
            <x-site.molecules.breadcrumb :items="$breadcrumb" class="self-end lv-tone-cream" data-hero-fade data-hero-intro />
        @endif

        <div class="mt-14 flex max-w-[780px] flex-col gap-6 lg:mt-[90px]">
            @if($location)
                <p class="flex items-center gap-2 lv-t-ui-s lv-tone-cream" data-hero-eyebrow data-hero-intro>
                    <x-site.atoms.icon name="pin" :size="16" class="lv-icon--on-dark" />{{ $location }}
                </p>
            @elseif($eyebrow)
                <x-site.atoms.eyebrow tone="gold" data-hero-eyebrow data-hero-intro>{{ $eyebrow }}</x-site.atoms.eyebrow>
            @endif

            <h1 id="lv-page-title" class="lv-t-display lv-tone-cream" data-hero-title data-hero-intro>
                {{ $title }}
                @if($accent)<em class="lv-italic">{{ $accent }}</em>@endif
                @if($after){{ $after }}@endif
            </h1>

            @if($lede)
                <p class="lv-t-lead lv-tone-cream max-w-[600px]" data-hero-fade data-hero-intro>{{ $lede }}</p>
            @endif

            @if($slot->isNotEmpty())
                <div class="flex flex-col items-start gap-[18px]" data-hero-fade data-hero-intro>{{ $slot }}</div>
            @endif

            @if($primary || $secondary)
                <div class="flex flex-wrap items-center gap-7 pt-4" data-hero-fade data-hero-intro>
                    @if($primary)<x-site.atoms.button :href="$primary['href']">{{ $primary['label'] }}</x-site.atoms.button>@endif
                    @if($secondary)<x-site.atoms.link-arrow :href="$secondary['href']" arrow="light" tone="cream">{{ $secondary['label'] }}</x-site.atoms.link-arrow>@endif
                </div>
            @endif
        </div>
    </div>
</section>
