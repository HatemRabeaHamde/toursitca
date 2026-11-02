{{--
    Booking card (Figma "Booking card"). Same booking logic as before — the
    experienceBooking store (date → quote → checkout); experienceBookingCard only formats it.
--}}
@php($booking = $page->booking)

<div class="lv-bcard"
     x-data="experienceBookingCard(@js(['store' => $booking['store'], 'card' => $booking['card'], 'options' => $booking['options']]))"
     @keydown.escape.window="dateOpen = false; closeGuests()">

    <x-site.molecules.booking-summary :price="$booking['priceFrom']" :rating="$booking['rating']" :reviews="$page->hero['reviews']" />

    <x-site.molecules.booking-field :label="__('experiences.detail.booking.dates')">
        <button type="button" class="lv-bfield__control" @click="openDates()" aria-haspopup="dialog" :aria-expanded="dateOpen.toString()">
            <x-site.atoms.icon name="calendar-terracotta" :size="18" />
            <span class="lv-bfield__value" :class="{ 'lv-tone-muted': !store.selectedDate }"
                  x-text="dateLabel() || @js(__('experiences.detail.booking.choose_date'))">{{ __('experiences.detail.booking.choose_date') }}</span>
            <x-site.atoms.icon name="chevron-down-muted" :size="16" />
        </button>
    </x-site.molecules.booking-field>

    <x-site.molecules.booking-time />

    <div class="grid grid-cols-2 gap-3">
        @include('site.experiences.detail.partials.travellers')

        <x-site.molecules.booking-field :label="__('experiences.detail.booking.group')" :for="$booking['supportsPrivate'] ? 'booking-group' : null">
            @if($booking['supportsPrivate'])
                <span class="lv-bfield__control">
                    <x-site.atoms.icon name="user-terracotta" :size="18" />
                    <select id="booking-group" class="lv-bfield__select" @change="setBookingType($event.target.value)">
                        <option value="group">{{ __('experiences.detail.booking.group_shared', ['count' => $booking['maxGroup']]) }}</option>
                        <option value="private">{{ __('experiences.detail.booking.group_private') }}</option>
                    </select>
                    <x-site.atoms.icon name="chevron-down-muted" :size="16" />
                </span>
            @else
                <span class="lv-bfield__control is-static">
                    <x-site.atoms.icon name="user-terracotta" :size="18" />
                    <span class="lv-bfield__value">{{ __('experiences.detail.booking.group_shared', ['count' => $booking['maxGroup']]) }}</span>
                </span>
            @endif
        </x-site.molecules.booking-field>
    </div>

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

    @if($booking['pickup'])
        <x-site.molecules.booking-field :label="__('experiences.detail.booking.pickup')">
            <span class="lv-bfield__control is-static">
                <x-site.atoms.icon name="pin-terracotta-18" :size="18" />
                <span class="lv-bfield__value">{{ $booking['pickup'] }}</span>
            </span>
            <p class="lv-t-caption lv-tone-muted">
                {{ $booking['hostName'] ? __('experiences.detail.booking.pickup_note', ['name' => $booking['hostName']]) : __('experiences.detail.booking.pickup_note_generic') }}
            </p>
        </x-site.molecules.booking-field>
    @endif

    <hr class="lv-bcard__rule">

    <div class="flex items-center justify-between gap-4">
        <div class="flex min-w-0 flex-col gap-0.5">
            <p class="lv-t-label-m lv-tone-ink"
               x-text="store.quote ? @js(__('experiences.detail.booking.confirmed_total')) : @js(__('experiences.detail.booking.total'))">{{ __('experiences.detail.booking.total') }}</p>
            <p class="lv-t-caption lv-tone-muted" x-text="breakdown()"></p>
        </div>
        <p class="lv-bcard__total" x-text="money(total())">{{ $booking['priceFrom'] }}</p>
    </div>

    <p class="lv-t-caption lv-tone-muted" x-show="store.isQuoting" x-cloak role="status">{{ __('experiences.detail.booking.checking') }}</p>
    <p class="lv-bcard__error lv-t-caption" x-show="store.quoteError" x-cloak x-text="store.quoteError" role="alert"></p>

    <x-site.atoms.button class="w-full" @click="request()" ::disabled="store.isStartingCheckout" ::aria-busy="store.isStartingCheckout.toString()">
        <span x-text="store.isStartingCheckout ? @js(__('experiences.detail.booking.requesting')) : @js(__('experiences.detail.booking.request'))">{{ __('experiences.detail.booking.request') }}</span>
    </x-site.atoms.button>

    <p class="flex items-center justify-center gap-2 text-center lv-t-caption lv-tone-muted">
        <x-site.atoms.icon name="card-muted" :size="15" />{{ __('experiences.detail.booking.reassurance') }}
    </p>

    @include('site.experiences.detail.partials.calendar')
</div>
