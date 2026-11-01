{{-- Booking card v1. Same logic as before: experienceBooking store (date → quote → checkout) through experienceBookingCard. --}}
@php($booking = $page->booking)

<div class="lv-bcard lv-v1-bcard"
     x-data="experienceBookingCard(@js(['store' => $booking['store'], 'card' => $booking['card'], 'options' => $booking['options']]))"
     @keydown.escape.window="dateOpen = false; closeGuests()">

    <div class="flex items-start justify-between gap-4">
        <div class="flex flex-col">
            <p class="flex items-baseline gap-1.5">
                <span class="lv-t-body-m lv-tone-muted">{{ __('experiences.detail.booking.from') }}</span>
                <span class="lv-bcard__amount" x-text="money(unitPrice())">{{ $booking['priceFrom'] }}</span>
            </p>
            <span class="lv-t-caption lv-tone-muted" x-show="!isFlat()">{{ __('experiences.detail.booking.per_person') }}</span>
        </div>
        @if($booking['rating'])
            <p class="flex items-center gap-[5px]">
                <x-site.atoms.icon name="star" :size="16" />
                <span class="lv-t-label-m lv-tone-ink">{{ $booking['rating'] }}</span>
                <span class="lv-t-caption lv-tone-muted">({{ $page->hero['reviews'] }})</span>
            </p>
        @endif
    </div>

    <x-site.molecules.booking-field :label="__('experiences.detail.v1.booking.dates')">
        <button type="button" class="lv-bfield__control" @click="openDates()" aria-haspopup="dialog" :aria-expanded="dateOpen.toString()">
            <x-site.atoms.icon name="calendar-terracotta" :size="18" />
            <span class="lv-bfield__value" :class="{ 'lv-tone-muted': !store.selectedDate }"
                  x-text="dateLabel() || @js(__('experiences.detail.booking.choose_date'))">{{ __('experiences.detail.booking.choose_date') }}</span>
            <x-site.atoms.icon name="chevron-down-muted" :size="16" />
        </button>
    </x-site.molecules.booking-field>

    @if($booking['hasChildPricing'])
        @include('site.experiences.detail.partials.travellers')
    @else
        <div class="lv-bfield">
            <span class="lv-t-label-m lv-tone-ink" id="booking-travellers-label">{{ __('experiences.detail.booking.travellers') }}</span>
            <div class="lv-v1-stepper" role="group" aria-labelledby="booking-travellers-label">
                <button type="button" @click="stepTravellers(-1)" :disabled="store.adults <= 1" aria-label="{{ __('experiences.detail.booking.decrease', ['label' => __('experiences.detail.booking.travellers')]) }}"><x-site.atoms.icon name="minus-ink" :size="16" /></button>
                <output class="lv-t-label-l lv-tone-ink" x-text="store.adults" aria-live="polite">1</output>
                <button type="button" @click="stepTravellers(1)" :disabled="!canAdd()" aria-label="{{ __('experiences.detail.booking.increase', ['label' => __('experiences.detail.booking.travellers')]) }}"><x-site.atoms.icon name="plus-ink" :size="16" /></button>
            </div>
        </div>
    @endif

    @if($booking['supportsPrivate'])
        <fieldset class="lv-bfield">
            <legend class="lv-t-label-m lv-tone-ink mb-2">{{ __('experiences.detail.v1.booking.group_type') }}</legend>
            <div class="grid grid-cols-2 gap-2.5">
                @foreach(['group' => ['small_group', 'small_group_hint'], 'private' => ['private', 'private_hint']] as $type => [$label, $hint])
                    <label class="lv-v1-choice" :class="{ 'is-selected': store.bookingType === @js($type) }">
                        <input type="radio" name="booking-type" value="{{ $type }}" class="sr-only" @checked($type === 'group') @change="setBookingType(@js($type))">
                        <img src="{{ asset('images/site/icons/check-circle-success.svg') }}" alt="" width="20" height="20" x-show="store.bookingType === @js($type)" @if($type !== 'group') x-cloak @endif>
                        <img src="{{ asset('images/site/icons/radio-empty.svg') }}" alt="" width="20" height="20" x-show="store.bookingType !== @js($type)" @if($type === 'group') x-cloak @endif>
                        <span class="flex min-w-0 flex-col">
                            <span class="lv-t-label-m lv-tone-ink">{{ __('experiences.detail.v1.booking.'.$label) }}</span>
                            <span class="lv-t-micro lv-tone-muted">{{ __('experiences.detail.v1.booking.'.$hint, ['count' => $booking['maxGroup']]) }}</span>
                        </span>
                    </label>
                @endforeach
            </div>
        </fieldset>
    @endif

    @if(count($booking['options']) > 1)
        <x-site.molecules.booking-field :label="__('experiences.detail.booking.option')" for="booking-option">
            <span class="lv-bfield__control">
                <x-site.atoms.icon name="route-terracotta" :size="18" />
                <select id="booking-option" class="lv-bfield__select" @change="setOption($event.target.value)">
                    @foreach($booking['options'] as $option)
                        <option value="{{ $option['id'] }}">{{ $option['title'] }}</option>
                    @endforeach
                </select>
                <x-site.atoms.icon name="chevron-down-muted" :size="16" />
            </span>
        </x-site.molecules.booking-field>
    @endif

    @if(count($booking['languages']) > 1)
        <x-site.molecules.booking-field :label="__('experiences.detail.booking.language')" for="booking-language">
            <span class="lv-bfield__control">
                <x-site.atoms.icon name="globe" :size="18" />
                <select id="booking-language" class="lv-bfield__select" @change="setLanguage($event.target.value)">
                    @foreach($booking['languages'] as $language)
                        <option value="{{ $language['code'] }}">{{ $language['label'] }}</option>
                    @endforeach
                </select>
                <x-site.atoms.icon name="chevron-down-muted" :size="16" />
            </span>
        </x-site.molecules.booking-field>
    @endif

    <div class="lv-v1-total">
        <div class="flex min-w-0 flex-col gap-0.5">
            <p class="lv-t-label-m lv-tone-ink" x-text="store.quote ? @js(__('experiences.detail.booking.confirmed_total')) : @js(__('experiences.detail.booking.total'))">{{ __('experiences.detail.booking.total') }}</p>
            <p class="lv-t-micro lv-tone-muted" x-text="breakdown()"></p>
            <p class="lv-t-micro lv-tone-muted">{{ __('experiences.detail.v1.booking.final_note') }}</p>
        </div>
        <p class="lv-bcard__total" x-text="money(total())">{{ $booking['priceFrom'] }}</p>
    </div>

    <p class="lv-t-caption lv-tone-muted" x-show="store.isQuoting" x-cloak role="status">{{ __('experiences.detail.booking.checking') }}</p>
    <p class="lv-bcard__error lv-t-caption" x-show="store.quoteError" x-cloak x-text="store.quoteError" role="alert"></p>

    <x-site.atoms.button class="w-full" @click="request()" ::disabled="store.isStartingCheckout" ::aria-busy="store.isStartingCheckout.toString()">
        <span x-text="store.isStartingCheckout ? @js(__('experiences.detail.booking.requesting')) : @js(__('experiences.detail.booking.request'))">{{ __('experiences.detail.booking.request') }}</span>
    </x-site.atoms.button>

    <ul class="flex flex-col gap-2.5 lv-t-caption lv-tone-ink">
        <li class="flex items-center gap-2.5"><x-site.atoms.icon name="card-ink" :size="17" />{{ __('experiences.detail.v1.booking.no_card') }}</li>
        @if($booking['cancellationHours'])
            <li class="flex items-center gap-2.5"><x-site.atoms.icon name="calendar-terracotta" :size="17" />{{ __('experiences.detail.v1.booking.free_cancellation', ['hours' => $booking['cancellationHours']]) }}</li>
        @endif
    </ul>

    @include('site.experiences.detail.partials.calendar')
</div>
