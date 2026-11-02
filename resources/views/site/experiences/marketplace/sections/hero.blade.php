{{-- Hero + floating search pill (Where · When · Who). The pill is the main filter form (#xp-filters). --}}
<section class="lv-xhero relative isolate" data-hero aria-labelledby="lv-page-title">
    <div class="absolute inset-0 -z-10 overflow-hidden" aria-hidden="true" data-hero-media data-hero-intro>
        <img src="{{ asset(config('experiences.marketplace.hero_image')) }}" alt="" class="h-full w-full object-cover" fetchpriority="high">
        <div class="lv-xhero__veil absolute inset-0"></div>
    </div>

    <div class="lv-container flex min-h-[inherit] flex-col justify-center pb-10 pt-32 md:pb-24 lg:pt-[120px]" data-hero-content>
        <div class="flex flex-col gap-4">
            <x-site.atoms.eyebrow tone="gold" data-hero-eyebrow data-hero-intro>{{ __('experiences.hero.eyebrow') }}</x-site.atoms.eyebrow>
            <h1 id="lv-page-title" class="lv-t-display lv-tone-cream" data-hero-title data-hero-intro>
                {{ __('experiences.hero.title') }}
                <em class="lv-italic lv-tone-gold">{{ __('experiences.hero.accent') }}</em>
            </h1>
            <p class="lv-t-lead lv-tone-cream" data-hero-fade data-hero-intro>{{ __('experiences.hero.lede') }}</p>
        </div>
    </div>

</section>
