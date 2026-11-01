<section class="lv-page-hero lv-hp-hero relative isolate overflow-hidden" data-hero aria-labelledby="lv-page-title">
    <div class="absolute inset-0 -z-10" aria-hidden="true" data-hero-media data-hero-intro>
        <img src="{{ $page->cover }}" alt="" class="h-full w-full object-cover" fetchpriority="high">
        <div class="lv-page-hero__veil absolute inset-0"></div>
    </div>

    <div class="lv-container flex flex-col pb-14 pt-28 lg:pt-[122px]" data-hero-content>
        <x-site.molecules.breadcrumb :items="$page->breadcrumb" class="self-end lv-tone-cream" data-hero-fade data-hero-intro />

        <div class="lv-hp-hero__row">
            @if($page->photo)
                <img src="{{ $page->photo }}" alt="{{ $page->name }}" width="320" height="376" class="lv-hp-hero__photo" data-hero-fade data-hero-intro>
            @endif

            <div class="flex min-w-0 flex-col items-start gap-4">
                <x-site.atoms.eyebrow tone="gold" data-hero-eyebrow data-hero-intro>{{ collect([__('hosts.profile.eyebrow'), $page->role])->filter()->implode(' · ') }}</x-site.atoms.eyebrow>
                <h1 id="lv-page-title" class="lv-t-display lv-tone-cream" data-hero-title data-hero-intro>{{ $page->name }}</h1>

                <ul class="lv-hp-hero__meta" data-hero-fade data-hero-intro>
                    @if($page->place)
                        <li class="lv-hp-hero__item lv-t-body-m"><x-site.atoms.icon name="pin-cream" :size="18" />{{ $page->place }}</li>
                    @endif
                    @if($page->reviews)
                        <li>
                            <a href="#reviews" class="lv-hp-hero__item lv-t-body-m">
                                <x-site.atoms.icon name="star-gold" :size="18" />
                                <strong class="lv-t-label-l">{{ $page->reviews['average'] }}</strong>
                                <span>{{ trans_choice('experiences.detail.reviews_count', $page->reviews['count'], ['count' => $page->reviews['count']]) }}</span>
                            </a>
                        </li>
                    @endif
                    @if($page->languages)
                        <li class="lv-hp-hero__item lv-t-body-m"><x-site.atoms.icon name="globe" :size="18" class="lv-icon--on-dark" />{{ $page->languages }}</li>
                    @endif
                </ul>

                @if($page->quote)
                    <p class="lv-hp-hero__quote lv-tone-cream" data-hero-fade data-hero-intro>“{{ $page->quote }}”</p>
                @endif

                @if($page->experiences)
                    <x-site.atoms.button href="#experiences" class="mt-2" data-hero-fade data-hero-intro>{{ __('hosts.profile.see_experiences', ['name' => $page->name]) }}</x-site.atoms.button>
                @endif
            </div>
        </div>
    </div>
</section>
