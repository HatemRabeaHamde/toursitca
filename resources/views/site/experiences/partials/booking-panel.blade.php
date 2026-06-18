<div class="ed-top-booking" x-data="{
        guestsOpen: false,
        dateOpen: false,
        langOpen: false,
        selectedLang: '{{ __('ui.experience_detail.booking_panel.select_language') }}',
        pickDate(date, label) {
            const store = this.$store.experienceBooking;

            if (! store.rangeStart || store.rangeEnd) {
                store.rangeStart = date;
                store.rangeStartLabel = label;
                store.rangeEnd = null;
                store.rangeEndLabel = null;
                return;
            }
            if (date < store.rangeStart) {
                store.rangeEnd = store.rangeStart;
                store.rangeEndLabel = store.rangeStartLabel;
                store.rangeStart = date;
                store.rangeStartLabel = label;
            } else {
                store.rangeEnd = date;
                store.rangeEndLabel = label;
            }
            this.dateOpen = false;
        },
    }">
    <div class="ed-booking-card">
        <div class="ed-price-line">
            @if ($detail->bookingSummary['original_price'])
                <span>{{ $detail->bookingSummary['currency'] }} {{ $detail->bookingSummary['original_price'] }}</span>
            @endif
            <strong>{{ $detail->bookingSummary['currency'] }} {{ $detail->bookingSummary['price_from'] }}</strong>
            <em>/{{ __('ui.price_type.per_person') }}</em>
        </div>

        {{-- Participants dropdown --}}
        <div class="ed-bp-field" @click.outside="guestsOpen = false">
            <button type="button" class="ed-bp-trigger" @click="guestsOpen = ! guestsOpen; dateOpen = false; langOpen = false">
                <span aria-hidden="true">⚇</span>
                <span x-text="($store.experienceBooking.adults + $store.experienceBooking.children) + ' ' + '{{ __('ui.experience_detail.booking_panel.select_participants') }}'"></span>
                <span class="ed-bp-chevron" :class="{ 'is-open': guestsOpen }" aria-hidden="true">▾</span>
            </button>

            <div class="ed-bp-popover" x-show="guestsOpen" x-cloak x-transition>
                <div class="ed-bp-counter-row">
                    <div>
                        <p class="ed-bp-counter-title">{{ __('ui.experience_detail.booking_panel.adults') }}</p>
                        <p class="ed-bp-counter-sub">{{ __('ui.experience_detail.booking_panel.adults_age') }}</p>
                    </div>
                    <div class="ed-bp-counter">
                        <button type="button" :disabled="$store.experienceBooking.adults <= 1" @click="$store.experienceBooking.adults--" aria-label="-">−</button>
                        <span x-text="$store.experienceBooking.adults"></span>
                        <button type="button" :disabled="$store.experienceBooking.adults + $store.experienceBooking.children >= {{ $experience->max_group_size }}" @click="$store.experienceBooking.adults++" aria-label="+">+</button>
                    </div>
                </div>
                <div class="ed-bp-counter-row">
                    <div>
                        <p class="ed-bp-counter-title">{{ __('ui.experience_detail.booking_panel.children') }}</p>
                        <p class="ed-bp-counter-sub">{{ __('ui.experience_detail.booking_panel.children_age') }}</p>
                    </div>
                    <div class="ed-bp-counter">
                        <button type="button" :disabled="$store.experienceBooking.children <= 0" @click="$store.experienceBooking.children--" aria-label="-">−</button>
                        <span x-text="$store.experienceBooking.children"></span>
                        <button type="button" :disabled="$store.experienceBooking.adults + $store.experienceBooking.children >= {{ $experience->max_group_size }}" @click="$store.experienceBooking.children++" aria-label="+">+</button>
                    </div>
                </div>
                <button type="button" class="ed-bp-apply" @click="guestsOpen = false">{{ __('ui.experience_detail.booking_panel.apply') }}</button>
            </div>
        </div>

        {{-- Date range dropdown (opens calendar modal) --}}
        <div class="ed-bp-field">
            <button type="button" class="ed-bp-trigger" @click="dateOpen = true; guestsOpen = false; langOpen = false">
                <span aria-hidden="true">▦</span>
                <span x-text="$store.experienceBooking.rangeStartLabel ? ($store.experienceBooking.rangeStartLabel + ($store.experienceBooking.rangeEndLabel ? ' – ' + $store.experienceBooking.rangeEndLabel : '')) : '{{ __('ui.experience_detail.booking_panel.select_date') }}'"></span>
                <span class="ed-bp-chevron" aria-hidden="true">▾</span>
            </button>
        </div>

        {{-- Language dropdown --}}
        <div class="ed-bp-field" @click.outside="langOpen = false">
            <button type="button" class="ed-bp-trigger" @click="langOpen = ! langOpen; guestsOpen = false; dateOpen = false">
                <span aria-hidden="true">◯</span>
                <span x-text="selectedLang"></span>
                <span class="ed-bp-chevron" :class="{ 'is-open': langOpen }" aria-hidden="true">▾</span>
            </button>

            <div class="ed-bp-popover ed-bp-lang-popover" x-show="langOpen" x-cloak x-transition>
                @foreach (['en' => 'English', 'fr' => 'Français'] as $code => $label)
                    <label class="ed-bp-radio">
                        <input type="radio" name="bp-lang" value="{{ $label }}"
                               @if ($code === app()->getLocale()) checked @endif
                               @click="selectedLang = '{{ $label }}'; langOpen = false">
                        <span>{{ $label }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        <x-ui.button href="{{ $detail->bookingUrl }}" class="w-full justify-center !rounded-[12px] !bg-[#D2592D] hover:!bg-[#b94722]">
            {{ __('ui.nav.book_now') }}
        </x-ui.button>

        <div class="ed-bp-trust">
            <p>
                <span class="ed-bp-trust-icon" aria-hidden="true">✓</span>
                <span>
                    <strong>{{ __('ui.experience_detail.booking_panel.free_cancellation_title') }}</strong>
                    <em>{{ __('ui.experience_detail.booking_panel.free_cancellation_body') }}</em>
                </span>
            </p>
            <p>
                <span class="ed-bp-trust-icon" aria-hidden="true">✓</span>
                <span>
                    <strong>{{ __('ui.experience_detail.key_specs.pay_later_title') }}</strong>
                    <em>{{ __('ui.experience_detail.key_specs.pay_later_body') }}</em>
                </span>
            </p>
        </div>
    </div>

    {{-- Date selection modal --}}
    <div class="ed-bp-modal" x-show="dateOpen" x-cloak x-transition @click.self="dateOpen = false">
        <div class="ed-bp-modal-card">
            <button type="button" class="ed-bp-modal-close" @click="dateOpen = false" aria-label="{{ __('ui.actions.close') }}">×</button>

            <div class="ed-calendar">
                <div class="ed-calendar-head">
                    <button type="button" aria-label="{{ __('ui.experience_detail.previous_month') }}">‹</button>
                    <strong>{{ $detail->bookingSummary['calendar']['month_label'] }}</strong>
                    <button type="button" aria-label="{{ __('ui.experience_detail.next_month') }}">›</button>
                </div>

                <div class="ed-calendar-weekdays">
                    @foreach ($detail->bookingSummary['calendar']['weekdays'] as $weekday)
                        <span>{{ $weekday }}</span>
                    @endforeach
                </div>

                <div class="ed-calendar-days">
                    @foreach ($detail->bookingSummary['calendar']['weeks'] as $week)
                        @foreach ($week as $day)
                            @php($dayLabel = \Carbon\CarbonImmutable::parse($day['date'])->translatedFormat('j M'))
                            <span @class([
                                'ed-day',
                                'muted' => ! $day['in_month'],
                                'available' => $day['status'] === 'available',
                                'few' => $day['status'] === 'few',
                                'full' => $day['status'] === 'full',
                            ])
                            @if ($day['in_month'] && $day['status'])
                                :class="{ 'selected': '{{ $day['date'] }}' === $store.experienceBooking.rangeStart || '{{ $day['date'] }}' === $store.experienceBooking.rangeEnd, 'in-range': $store.experienceBooking.rangeStart && $store.experienceBooking.rangeEnd && '{{ $day['date'] }}' > $store.experienceBooking.rangeStart && '{{ $day['date'] }}' < $store.experienceBooking.rangeEnd }"
                                @click="pickDate('{{ $day['date'] }}', '{{ $dayLabel }}')"
                            @endif
                            >
                                {{ $day['number'] }}
                            </span>
                        @endforeach
                    @endforeach
                </div>

                <div class="ed-calendar-legend">
                    <span><i class="available"></i>{{ __('ui.experience_detail.calendar.available') }}</span>
                    <span><i class="few"></i>{{ __('ui.experience_detail.calendar.few') }}</span>
                    <span><i class="full"></i>{{ __('ui.experience_detail.calendar.full') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
