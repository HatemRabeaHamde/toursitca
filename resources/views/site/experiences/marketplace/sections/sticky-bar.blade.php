{{-- Sticky filter bar: search pill (Where · When · Who) + interest chips · Filters · Sort. Pins under the header while the results scroll.
     On phones the pill becomes a one-line summary that opens the fields as a bottom sheet. --}}
@php($narrowQuery = '(max-width: 767px)')
<div class="lv-xstick" x-data="{ stuck: false, searchOpen: false, mobile: window.matchMedia(@js($narrowQuery)).matches, summary: '',
         check() { const r = $el.getBoundingClientRect(); this.stuck = window.scrollY > 0 && r.top <= (parseFloat(getComputedStyle($el).top) || 0) + 1 },
         refreshSummary() { this.summary = [...$el.querySelectorAll('.lv-xsearch__value:not(.is-placeholder)')].map((el) => el.textContent.trim()).filter(Boolean).join(' · ') } }"
     x-init="check(); $nextTick(() => refreshSummary()); window.matchMedia(@js($narrowQuery)).addEventListener('change', (event) => { mobile = event.matches; if (! event.matches) searchOpen = false })"
     @transitionend="check()" @scroll.window.passive="check()" @resize.window.passive="check()"
     @live-filters:submitted.window="searchOpen = false; $nextTick(() => refreshSummary())" @live-filters:synced.window="$nextTick(() => refreshSummary())"
     @keydown.escape.window="searchOpen = false"
     :class="{ 'is-stuck': stuck, 'is-search-open': searchOpen }">
    <div class="lv-xsheet-backdrop" x-show="searchOpen" x-cloak x-transition.opacity.duration.250ms @click="searchOpen = false" aria-hidden="true"></div>
    <div class="lv-container lv-xsearch-wrap">
        <div class="lv-xsheet-head">
            <p class="lv-t-h5 lv-serif lv-tone-ink">{{ __('experiences.search.label') }}</p>
            <button type="button" class="lv-dialog__close" @click="searchOpen = false" aria-label="{{ __('experiences.filters.close') }}">×</button>
        </div>
        <form id="xp-filters" method="GET" action="{{ $page->indexUrl }}#results" role="search" aria-label="{{ __('experiences.search.label') }}"
              class="lv-xsearch" @submit.prevent="submit($el)">
            {{-- Where: custom listbox instead of the browser's native menu; the value rides in a hidden input. --}}
            @php($cityLabel = collect($page->cities)->firstWhere('value', $page->filters->city)['label'] ?? '')
            <div class="lv-xsearch__field lv-xsearch__field--where" x-data="{ open: false, value: @js((string) $page->filters->city), label: @js($cityLabel),
                 pick(value, label) { this.value = value; this.label = label; this.open = false; this.$nextTick(() => this.$refs.input.form.requestSubmit()) },
                 sync(value) { this.value = value; this.label = [...this.$refs.list.querySelectorAll('[data-value]')].find((el) => el.dataset.value === value)?.dataset.label ?? '' } }"
                 @click.outside="open = false" @keydown.escape="open = false" @live-filters:synced.window="sync($event.detail.params.get('city') || '')">
                <button type="button" class="lv-xsearch__trigger" @click="open = !open" aria-haspopup="listbox" :aria-expanded="open.toString()">
                    <x-site.atoms.icon name="pin-terracotta" :size="20" />
                    <span class="lv-xsearch__text">
                        <span class="lv-xsearch__label">{{ __('experiences.search.where') }}</span>
                        <span class="lv-xsearch__value" :class="{ 'is-placeholder': !value }" x-text="label || @js(__('experiences.search.any_destination'))">{{ $cityLabel ?: __('experiences.search.any_destination') }}</span>
                    </span>
                    <x-site.atoms.icon name="chevron-down-muted" :size="16" class="lv-xsearch__chevron" ::class="{ 'is-open': open }" />
                </button>
                <input type="hidden" name="city" :value="value" x-ref="input">
                <ul class="lv-xmenu" x-ref="list" role="listbox" x-show="open" x-cloak x-transition.opacity.duration.150ms data-lenis-prevent>
                    <li><button type="button" role="option" class="lv-xmenu__item" :class="{ 'is-selected': !value }" :aria-selected="(!value).toString()" @click="pick('', '')">{{ __('experiences.search.any_destination') }}</button></li>
                    @foreach($page->cities as $city)
                        <li><button type="button" role="option" class="lv-xmenu__item" :class="{ 'is-selected': value === @js($city['value']) }" :aria-selected="(value === @js($city['value'])).toString()" data-value="{{ $city['value'] }}" data-label="{{ $city['label'] }}" @click="pick(@js($city['value']), @js($city['label']))">{{ $city['label'] }}</button></li>
                    @endforeach
                </ul>
            </div>
            <span class="lv-xsearch__divider" aria-hidden="true"></span>

            {{-- When: month calendar popover; the day rides in a hidden input. --}}
            <div class="lv-xsearch__field lv-xsearch__field--when" x-data="searchDate(@js(['value' => (string) $page->filters->date, 'locale' => app()->getLocale(), 'min' => now()->toDateString()]))"
                 @click.outside="open = false" @keydown.escape="open = false" @live-filters:synced.window="sync($event.detail.params.get('date') || '')">
                <button type="button" class="lv-xsearch__trigger" @click="open = !open" aria-haspopup="dialog" :aria-expanded="open.toString()">
                    <x-site.atoms.icon name="calendar-terracotta" :size="20" />
                    <span class="lv-xsearch__text">
                        <span class="lv-xsearch__label">{{ __('experiences.search.when') }}</span>
                        <span class="lv-xsearch__value" :class="{ 'is-placeholder': !value }" x-text="label || @js(__('experiences.search.add_dates'))">{{ __('experiences.search.add_dates') }}</span>
                    </span>
                    <x-site.atoms.icon name="chevron-down-muted" :size="16" class="lv-xsearch__chevron" ::class="{ 'is-open': open }" />
                </button>
                <input type="hidden" name="date" :value="value" x-ref="input">

                <div class="lv-xcal" x-show="open" x-cloak x-transition.opacity.duration.150ms role="dialog" aria-label="{{ __('experiences.search.when') }}">
                    <div class="lv-xcal__head">
                        <button type="button" class="lv-xcal__nav" @click="shift(-1)" :disabled="!canGoBack()" aria-label="{{ __('ui.experience_detail.previous_month') }}">←</button>
                        <p class="lv-xcal__title" x-text="title" aria-live="polite"></p>
                        <button type="button" class="lv-xcal__nav" @click="shift(1)" aria-label="{{ __('ui.experience_detail.next_month') }}">→</button>
                    </div>
                    <div class="lv-xcal__grid" role="grid">
                        <template x-for="weekday in weekdays"><span class="lv-xcal__weekday" role="columnheader" x-text="weekday"></span></template>
                        <template x-for="(cell, index) in cells" :key="index">
                            <span class="contents">
                                <span class="lv-xcal__blank" x-show="!cell"></span>
                                <button type="button" class="lv-xcal__day" x-show="cell" :disabled="cell && cell.past"
                                        :class="{ 'is-selected': cell && value === cell.key, 'is-today': cell && cell.today }"
                                        :aria-pressed="cell ? (value === cell.key).toString() : 'false'" @click="pick(cell.key)" x-text="cell ? cell.day : ''"></button>
                            </span>
                        </template>
                    </div>
                    <div class="lv-xcal__foot">
                        <button type="button" class="lv-xcal__link" x-show="value" x-cloak @click="pick('')">{{ __('experiences.search.clear_date') }}</button>
                        <button type="button" class="lv-xcal__link lv-xcal__link--end" @click="pick(@js(now()->toDateString()))">{{ __('experiences.search.today') }}</button>
                    </div>
                </div>
            </div>
            <span class="lv-xsearch__divider" aria-hidden="true"></span>

            {{-- Who --}}
            @php($whoLabel = collect($page->travellers())->firstWhere('value', (string) $page->filters->participants)['label'] ?? '')
            <div class="lv-xsearch__field lv-xsearch__field--who" x-data="{ open: false, value: @js((string) $page->filters->participants), label: @js($whoLabel),
                 pick(value, label) { this.value = value; this.label = label; this.open = false; this.$nextTick(() => this.$refs.input.form.requestSubmit()) },
                 sync(value) { this.value = value; this.label = [...this.$refs.list.querySelectorAll('[data-value]')].find((el) => el.dataset.value === value)?.dataset.label ?? '' } }"
                 @click.outside="open = false" @keydown.escape="open = false" @live-filters:synced.window="sync($event.detail.params.get('participants') || '')">
                <button type="button" class="lv-xsearch__trigger" @click="open = !open" aria-haspopup="listbox" :aria-expanded="open.toString()">
                    <x-site.atoms.icon name="user-terracotta" :size="20" />
                    <span class="lv-xsearch__text">
                        <span class="lv-xsearch__label">{{ __('experiences.search.who') }}</span>
                        <span class="lv-xsearch__value" :class="{ 'is-placeholder': !value }" x-text="label || @js(__('experiences.search.any_travellers'))">{{ $whoLabel ?: __('experiences.search.any_travellers') }}</span>
                    </span>
                    <x-site.atoms.icon name="chevron-down-muted" :size="16" class="lv-xsearch__chevron" ::class="{ 'is-open': open }" />
                </button>
                <input type="hidden" name="participants" :value="value" x-ref="input">
                <ul class="lv-xmenu" x-ref="list" role="listbox" x-show="open" x-cloak x-transition.opacity.duration.150ms data-lenis-prevent>
                    <li><button type="button" role="option" class="lv-xmenu__item" :class="{ 'is-selected': !value }" :aria-selected="(!value).toString()" @click="pick('', '')">{{ __('experiences.search.any_travellers') }}</button></li>
                    @foreach($page->travellers() as $option)
                        <li><button type="button" role="option" class="lv-xmenu__item" :class="{ 'is-selected': value === @js($option['value']) }" :aria-selected="(value === @js($option['value'])).toString()" data-value="{{ $option['value'] }}" data-label="{{ $option['label'] }}" @click="pick(@js($option['value']), @js($option['label']))">{{ $option['label'] }}</button></li>
                    @endforeach
                </ul>
            </div>
            <template x-if="mobile">
                <div class="lv-xsheet-sort">
                    <x-site.molecules.select-menu variant="plain" name="sort" form="xp-filters" :options="$page->sorts()" :selected="$page->filters->sort" :label="__('experiences.sort.label')" />
                </div>
            </template>
            <button type="submit" class="lv-xsearch__submit lv-t-label-m">
                <x-site.atoms.icon name="search-white" :size="16" />
                <span>{{ __('experiences.search.submit') }}</span>
            </button>
        </form>
    </div>
    <div class="lv-container lv-xtools">
<div class="lv-xtools__row flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <button type="button" class="lv-xpill" @click="searchOpen = true" aria-haspopup="dialog" :aria-expanded="searchOpen.toString()">
                <x-site.atoms.icon name="search-white" :size="16" class="lv-xpill__icon" />
                <span class="lv-xpill__text">
                    <span class="lv-xpill__title">{{ __('experiences.search.label') }}</span>
                    <span class="lv-xpill__sub" x-text="summary || @js(__('experiences.search.any_destination'))">{{ __('experiences.search.any_destination') }}</span>
                </span>
            </button>
            <div class="lv-xtools__chips" data-fragment="chips">@include('site.experiences.marketplace.partials.chips')</div>

            <div class="lv-xtools__actions flex items-center gap-4" x-data="{ open: false }" @live-filters:submitted.window="open = false" @keydown.escape.window="open = false">
                <button type="button" class="lv-xfilter-btn lv-t-label-s" @click="open = true" aria-haspopup="dialog" :aria-expanded="open.toString()">
                    <x-site.atoms.icon name="filters" :size="16" />
                    <span class="lv-xfilter-btn__label">{{ __('experiences.filters.button') }}</span>
                    <span data-fragment="badge" class="contents">@include('site.experiences.marketplace.partials.badge')</span>
                </button>
                <template x-if="!mobile">
                    <x-site.molecules.select-menu variant="plain" name="sort" form="xp-filters" :options="$page->sorts()" :selected="$page->filters->sort" :label="__('experiences.sort.label')" />
                </template>

                @include('site.experiences.marketplace.partials.drawer')
            </div>
        </div>

    </div>
</div>
