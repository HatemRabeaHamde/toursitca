{{-- Sticky filter bar: search pill (Where · When · Who) + interest chips · Filters · Sort. Pins under the header while the results scroll. --}}
<div class="lv-xstick" x-data="{ stuck: false, check() { const r = $el.getBoundingClientRect(); this.stuck = window.scrollY > 0 && r.top <= (parseFloat(getComputedStyle($el).top) || 0) + 1 } }"
     x-init="check()" @transitionend="check()" @scroll.window.passive="check()" @resize.window.passive="check()" :class="{ 'is-stuck': stuck }">
    <div class="lv-container lv-xsearch-wrap">
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

            {{-- When: native date picker (shows "Add dates" until focused). --}}
            <label class="lv-xsearch__field lv-xsearch__field--when">
                <x-site.atoms.icon name="calendar-terracotta" :size="20" />
                <span class="lv-xsearch__text">
                    <span class="lv-xsearch__label">{{ __('experiences.search.when') }}</span>
                    <input type="{{ $page->filters->date ? 'date' : 'text' }}" name="date" value="{{ $page->filters->date }}" min="{{ now()->toDateString() }}"
                           placeholder="{{ __('experiences.search.add_dates') }}" class="lv-xsearch__value" aria-label="{{ __('experiences.search.when') }}"
                           x-data @focus="$el.type = 'date'; $nextTick(() => { try { $el.showPicker() } catch (e) {} })"
                           @blur="if (! $el.value) $el.type = 'text'" @change="$el.form.requestSubmit()">
                </span>
            </label>
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
