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

    <div class="lv-container lv-xsearch-wrap">
        <form id="xp-filters" method="GET" action="{{ $page->indexUrl }}#results" role="search" aria-label="{{ __('experiences.search.label') }}"
              class="lv-xsearch" @submit.prevent="submit($el)" data-hero-fade data-hero-intro>
            <label class="lv-xsearch__field">
                <span class="lv-t-label-xs lv-tone-ink">{{ __('experiences.search.where') }}</span>
                <select name="city" class="lv-t-body-s" @change="$el.form.requestSubmit()">
                    <option value="">{{ __('experiences.search.any_destination') }}</option>
                    @foreach($page->cities as $city)
                        <option value="{{ $city['value'] }}" @selected($page->filters->city === $city['value'])>{{ $city['label'] }}</option>
                    @endforeach
                </select>
            </label>
            <span class="lv-xsearch__divider" aria-hidden="true"></span>
            <label class="lv-xsearch__field">
                <span class="lv-t-label-xs lv-tone-ink">{{ __('experiences.search.when') }}</span>
                {{-- Shows "Add dates" until focused, then the native date picker. --}}
                <input type="{{ $page->filters->date ? 'date' : 'text' }}" name="date" value="{{ $page->filters->date }}" min="{{ now()->toDateString() }}"
                       placeholder="{{ __('experiences.search.add_dates') }}" class="lv-t-body-s" aria-label="{{ __('experiences.search.when') }}"
                       x-data @focus="$el.type = 'date'; $nextTick(() => { try { $el.showPicker() } catch (e) {} })"
                       @blur="if (! $el.value) $el.type = 'text'" @change="$el.form.requestSubmit()">
            </label>
            <span class="lv-xsearch__divider" aria-hidden="true"></span>
            <label class="lv-xsearch__field">
                <span class="lv-t-label-xs lv-tone-ink">{{ __('experiences.search.who') }}</span>
                <select name="participants" class="lv-t-body-s" @change="$el.form.requestSubmit()">
                    <option value="">{{ __('experiences.search.any_travellers') }}</option>
                    @foreach($page->travellers() as $option)
                        <option value="{{ $option['value'] }}" @selected((string) $page->filters->participants === $option['value'])>{{ $option['label'] }}</option>
                    @endforeach
                </select>
            </label>
            <button type="submit" class="lv-xsearch__submit lv-t-label-m">
                <x-site.atoms.icon name="search-white" :size="16" />
                <span>{{ __('experiences.search.submit') }}</span>
            </button>
        </form>
    </div>
</section>
