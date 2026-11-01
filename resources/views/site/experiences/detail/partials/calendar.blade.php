{{-- Date picker dialog (same availability calendar as before). Teleported to the page root. --}}
@php($calendar = $page->booking['calendar'])

<template x-teleport="[data-lv-motion]">
    <div class="lv-dialog" x-show="dateOpen" x-cloak x-transition.opacity.duration.250ms
         @click.self="dateOpen = false" role="dialog" aria-modal="true" aria-labelledby="booking-calendar-title" data-lenis-prevent>
        <div class="lv-dialog__panel lv-calendar" x-trap.noscroll="dateOpen">
            <div class="flex items-center justify-between gap-4">
                <p id="booking-calendar-title" class="lv-t-h5 lv-serif lv-tone-ink">{{ __('experiences.detail.booking.calendar') }}</p>
                <button type="button" class="lv-dialog__close" @click="dateOpen = false" aria-label="{{ __('experiences.detail.booking.close') }}">×</button>
            </div>

            <p class="lv-t-label-m lv-tone-ink">{{ $calendar['month_label'] }}</p>

            <div class="lv-calendar__grid" role="grid">
                @foreach($calendar['weekdays'] as $weekday)
                    <span class="lv-calendar__weekday lv-t-label-xs lv-tone-muted" role="columnheader">{{ $weekday }}</span>
                @endforeach

                @foreach($calendar['weeks'] as $week)
                    @foreach($week as $day)
                        @php($open = $day['in_month'] && in_array($day['status'], ['available', 'few'], true))
                        @if($open)
                            <button type="button" @class(['lv-calendar__day', 'is-few' => $day['status'] === 'few'])
                                    :class="{ 'is-selected': store.selectedDate === @js($day['date']) }"
                                    :aria-pressed="(store.selectedDate === @js($day['date'])).toString()"
                                    aria-label="{{ \Carbon\CarbonImmutable::parse($day['date'])->locale(app()->getLocale())->translatedFormat('l j F Y') }}"
                                    @click="pickDate(@js($day['date']), @js(\Carbon\CarbonImmutable::parse($day['date'])->locale(app()->getLocale())->translatedFormat('j M')), @js($day['availability_ids']))">
                                {{ $day['number'] }}
                            </button>
                        @else
                            <span @class(['lv-calendar__day is-off', 'is-full' => $day['in_month'] && $day['status'] === 'full', 'is-outside' => ! $day['in_month']])>{{ $day['in_month'] ? $day['number'] : '' }}</span>
                        @endif
                    @endforeach
                @endforeach
            </div>

            @if($page->booking['hasDates'])
                <ul class="flex flex-wrap gap-x-5 gap-y-2 lv-t-caption lv-tone-muted">
                    <li class="lv-calendar__key lv-calendar__key--available">{{ __('experiences.detail.booking.available') }}</li>
                    <li class="lv-calendar__key lv-calendar__key--few">{{ __('experiences.detail.booking.few') }}</li>
                    <li class="lv-calendar__key lv-calendar__key--full">{{ __('experiences.detail.booking.full') }}</li>
                </ul>
            @else
                <div class="flex flex-col gap-1 rounded-xl bg-[color:var(--lv-sand)] p-4">
                    <p class="lv-t-label-m lv-tone-ink">{{ __('experiences.detail.booking.no_dates') }}</p>
                    <p class="lv-t-caption lv-tone-muted">{{ __('experiences.detail.booking.no_dates_body') }}</p>
                </div>
            @endif
        </div>
    </div>
</template>
