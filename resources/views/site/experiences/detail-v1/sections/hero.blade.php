{{-- Hero: breadcrumb, location, two-tone title, description, meta, rating + trust, video, photo strip. --}}
@php([$lead, $accent] = $page->titleParts())
@php($photos = array_slice($page->gallery, 0, 6))
@php($hiddenPhotos = count($page->gallery) - count($photos))

<section class="lv-page-hero lv-page-hero--detail lv-v1-hero relative isolate overflow-hidden" data-hero aria-labelledby="lv-page-title">
    <div class="absolute inset-0 -z-10" aria-hidden="true" data-hero-media data-hero-intro>
        <img src="{{ $page->hero['image'] }}" alt="" class="h-full w-full object-cover" fetchpriority="high">
        <div class="lv-page-hero__veil absolute inset-0"></div>
    </div>

    <div class="lv-container flex flex-col pb-10 pt-28 lg:pt-[122px]" data-hero-content>
        <x-site.molecules.breadcrumb :items="$page->breadcrumb" class="self-end lv-tone-cream" data-hero-fade data-hero-intro />

        <div class="mt-7 flex max-w-[780px] flex-col items-start gap-4">
            <p class="flex items-center gap-2 lv-t-ui-m lv-tone-cream" data-hero-eyebrow data-hero-intro>
                <x-site.atoms.icon name="pin-cream" :size="18" />{{ $page->hero['location'] }}
            </p>
            <h1 id="lv-page-title" class="lv-t-display lv-tone-cream" data-hero-title data-hero-intro>
                {{ $lead }}
                @if($accent)<em class="lv-italic lv-v1-hero__accent">{{ $accent }}</em>@endif
            </h1>
            @if($page->description)
                <p class="lv-t-lead lv-tone-cream max-w-[600px]" data-hero-fade data-hero-intro>{{ $page->description }}</p>
            @endif
        </div>

        <div class="mt-5 flex flex-wrap items-end justify-between gap-6" data-hero-fade data-hero-intro>
            <div class="flex flex-col items-start gap-3.5">
                @if($page->hero['meta'])
                    <ul class="lv-xhero__row">
                        @foreach($page->hero['meta'] as $meta)
                            <li class="lv-xhero__item lv-t-body-m"><x-site.atoms.icon :name="$meta['icon']" :size="18" />{{ $meta['label'] }}</li>
                        @endforeach
                    </ul>
                @endif
                @if($page->hero['rating'] || $page->hero['verified'])
                    <div class="lv-xhero__row lv-xhero__row--trust">
                        @if($page->hero['rating'])
                            <a href="#reviews" class="lv-xhero__item lv-t-body-m">
                                <x-site.atoms.icon name="star-gold" :size="18" />
                                <strong class="lv-t-label-l">{{ $page->hero['rating'] }}</strong>
                                <span>{{ trans_choice('experiences.detail.reviews_count', $page->hero['reviews'], ['count' => $page->hero['reviews']]) }}</span>
                            </a>
                        @endif
                        @if($page->hero['rating'] && $page->hero['verified'])<span class="lv-xhero__divider" aria-hidden="true"></span>@endif
                        @if($page->hero['verified'])
                            <span class="lv-xhero__item lv-t-body-m"><x-site.atoms.icon name="shield-cream" :size="18" />{{ __('experiences.detail.verified') }}</span>
                        @endif
                    </div>
                @endif
            </div>

            @if($page->hero['video'])
                <a href="{{ $page->hero['video'] }}" target="_blank" rel="noopener" class="lv-v1-play lv-t-label-l">
                    <span class="lv-v1-play__dot"><x-site.atoms.icon name="play-cream" :size="20" /></span>
                    {{ __('experiences.detail.v1.hero.play') }}
                </a>
            @endif
        </div>

        @if($photos)
            <ul class="lv-v1-strip" data-hero-fade data-hero-intro>
                @foreach($photos as $photo)
                    <li>
                        <button type="button" class="lv-v1-strip__tile" @click="show({{ $loop->index }})" aria-haspopup="dialog"
                                aria-label="{{ trans_choice('experiences.detail.photos', count($page->gallery), ['count' => count($page->gallery)]) }}">
                            <img src="{{ $photo }}" alt="" width="186" height="128" loading="lazy">
                            @if($loop->last && $hiddenPhotos > 0)
                                <span class="lv-v1-strip__more">
                                    <span class="lv-t-h4 lv-serif">+{{ $hiddenPhotos }}</span>
                                    <span class="lv-t-ui-xs">{{ __('experiences.detail.v1.hero.photos') }}</span>
                                </span>
                            @endif
                        </button>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</section>
