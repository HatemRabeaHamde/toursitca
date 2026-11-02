{{-- Sticky filter bar: search pill (Where · When · Who) + interest chips · Filters · Sort. Pins under the header while the results scroll. --}}
<div class="lv-xstick" x-data="{ stuck: false, check() { const r = $el.getBoundingClientRect(); this.stuck = window.scrollY > 0 && r.top <= (parseFloat(getComputedStyle($el).top) || 0) + 1 } }"
     x-init="check()" @transitionend="check()" @scroll.window.passive="check()" @resize.window.passive="check()" :class="{ 'is-stuck': stuck }">
    <div class="lv-container lv-xsearch-wrap">
        <form id="xp-filters" method="GET" action="{{ $page->indexUrl }}#results" role="search" aria-label="{{ __('experiences.search.label') }}"
              class="lv-xsearch" @submit.prevent="submit($el)">
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
    <div class="lv-container lv-xtools">
<div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div data-fragment="chips">@include('site.experiences.marketplace.partials.chips')</div>

            <div class="flex items-center gap-4" x-data="{ open: false }" @live-filters:submitted.window="open = false" @keydown.escape.window="open = false">
                <button type="button" class="lv-xfilter-btn lv-t-label-s" @click="open = true" aria-haspopup="dialog" :aria-expanded="open.toString()">
                    <x-site.atoms.icon name="filters" :size="16" />
                    <span>{{ __('experiences.filters.button') }}</span>
                    <span data-fragment="badge" class="contents">@include('site.experiences.marketplace.partials.badge')</span>
                </button>
                <x-site.molecules.select-menu variant="plain" name="sort" form="xp-filters" :options="$page->sorts()" :selected="$page->filters->sort" :label="__('experiences.sort.label')" />

                @include('site.experiences.marketplace.partials.drawer')
            </div>
        </div>

    </div>
</div>
