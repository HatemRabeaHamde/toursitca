{{--
    "Choose your option": the packages of this experience, shown once a date is picked.
    Availability and price come from the picked date and the travellers (see experienceBookingCard).
--}}
@php($options = $page->booking['options'])

<section id="options" class="lv-xsection lv-v1-desktop-only" aria-labelledby="options-title">
    <div class="flex flex-wrap items-end justify-between gap-3">
        <h2 id="options-title" class="lv-t-h2 lv-serif lv-tone-ink">{{ __('booking_flow.options.title') }}</h2>
        <p class="lv-t-body-s lv-tone-muted" x-show="store.selectedDate" x-cloak>
            <span x-text="@js(__('booking_flow.options.for_date')).replace(':date', store.selectedDateLabel || '')"></span>
            <button type="button" class="lv-tone-terracotta lv-t-label-s ms-2" @click="openDates()">{{ __('booking_flow.options.change_date') }}</button>
        </p>
    </div>

    {{-- Before a date: nothing to compare yet. --}}
    <div class="lv-opt-empty" x-show="!store.selectedDate">
        <x-site.atoms.icon name="calendar-terracotta" :size="22" />
        <div class="min-w-0 flex-1">
            <p class="lv-t-label-m lv-tone-ink">{{ __('booking_flow.options.pick_date_title') }}</p>
            <p class="lv-t-caption lv-tone-muted">{{ __('booking_flow.options.pick_date_body') }}</p>
        </div>
        <x-site.atoms.button type="button" variant="outline" :arrow="false" @click="openDates()">{{ __('booking_flow.cta.choose_date') }}</x-site.atoms.button>
    </div>

    <ul class="lv-opt-list" x-show="store.selectedDate" x-cloak role="radiogroup" aria-labelledby="options-title">
        <template x-for="option in options" :key="option.id">
            <li>
                <div class="lv-opt" :class="{ 'is-selected': store.activeOption === option.id, 'is-off': !optionAvailable(option) }">
                    <button type="button" class="lv-opt__main" role="radio" :aria-checked="(store.activeOption === option.id).toString()"
                            :disabled="!optionAvailable(option)" @click="selectOption(option)">
                        <span class="lv-opt__radio" aria-hidden="true"></span>
                        <span class="flex min-w-0 flex-1 flex-col gap-2 text-start">
                            <span class="lv-t-h5 lv-serif lv-tone-ink" x-text="option.title"></span>
                            <ul class="lv-opt__chips lv-t-caption lv-tone-muted">
                                <li x-show="option.duration"><x-site.atoms.icon name="clock" :size="14" /><span x-text="option.duration"></span></li>
                                <li x-show="option.languages.length"><x-site.atoms.icon name="globe" :size="14" class="lv-icon--ink" /><span x-text="option.languages.map((l) => l.label).join(', ')"></span></li>
                                <li x-show="option.pickup"><x-site.atoms.icon name="pin" :size="14" />{{ __('booking_flow.options.pickup') }}</li>
                                <li x-show="option.private"><x-site.atoms.icon name="user-terracotta" :size="14" />{{ __('booking_flow.options.private') }}</li>
                                <li x-show="option.cancellationHours"><x-site.atoms.icon name="calendar-terracotta" :size="14" /><span x-text="@js(__('booking_flow.options.cancellation')).replace(':hours', option.cancellationHours)"></span></li>
                            </ul>
                            <span class="lv-t-caption lv-tone-terracotta-deep" x-show="!optionAvailable(option)" x-cloak>{{ __('booking_flow.options.unavailable') }}</span>
                        </span>
                        <span class="lv-opt__price" x-show="optionAvailable(option)">
                            <span class="lv-opt__was lv-t-caption" x-show="optionDiscount(option)" x-text="money(optionTotal(option, true))"></span>
                            <strong class="lv-serif" x-text="money(optionTotal(option))"></strong>
                            <span class="lv-t-micro lv-tone-muted" x-text="optionBreakdown(option)"></span>
                        </span>
                    </button>
                </div>
            </li>
        </template>
    </ul>

    <p class="lv-t-caption lv-tone-muted" x-show="store.selectedDate && options.length && availableOptions().length === 0" x-cloak>{{ __('booking_flow.options.none_open') }}</p>
</section>
