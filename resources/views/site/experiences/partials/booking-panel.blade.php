<div class="ed-top-booking"
     :class="{ 'is-modal-open': dateOpen }"
     x-data="{
        guestsOpen: false,
        dateOpen: false,
        langOpen: false,
        selectedLang: 'English',
        init() {
            this.$store.experienceBooking.configure({
                checkoutUrl: @js($detail->bookingSummary['checkout_url']),
                csrfToken: @js(csrf_token()),
                defaultLanguage: 'en',
                experienceId: {{ $experience->id }},
                maxParticipants: {{ $experience->max_group_size }},
                privateAvailable: @js($detail->bookingSummary['supports_private']),
                quoteUrl: @js($detail->bookingSummary['quote_url']),
                pricePerPerson: {{ (float) $experience->price_per_person }},
                privatePrice: {{ $experience->private_price !== null ? (float) $experience->private_price : 'null' }},
                currency: @js($detail->bookingSummary['currency']),
                strings: {
                    checkoutFailed: @js(__('ui.experience_detail.booking_panel.checkout_failed')),
                    quoteFailed: @js(__('ui.experience_detail.booking_panel.quote_failed')),
                    selectDateAndParticipants: @js(__('ui.experience_detail.booking_panel.select_date_and_participants')),
                    adult: @js(__('ui.experience_detail.options.participant_types.adult')),
                    child: @js(__('ui.experience_detail.options.participant_types.child')),
                    group: @js(__('ui.experience_detail.options.participant_types.group')),
                    perPerson: @js(__('ui.price_type.per_person')),
                    perGroup: @js(__('ui.price_type.per_group')),
                    flatRate: @js(__('ui.price_type.flat')),
                    privateFlat: @js(__('ui.price_type.private_flat')),
                },
            });
        },
        async pickDate(date, label, availabilityIds) {
            this.$store.experienceBooking.selectDate(date, label, availabilityIds);
            this.dateOpen = false;
            const quote = await this.$store.experienceBooking.applyQuote();
            if (quote) {
                this.$nextTick(() => {
                    document.getElementById('experience-options')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
                });
            }
        },
    }">
    <div class="ed-booking-card">
        <div class="ed-price-line">
            @if ($detail->bookingSummary['original_price'])
                <span>{{ $detail->bookingSummary['currency'] }} {{ $detail->bookingSummary['original_price'] }}</span>
            @endif
            <strong x-text="$store.experienceBooking.livePriceLabel()">{{ $detail->bookingSummary['currency'] }} {{ $detail->bookingSummary['price_from'] }}</strong>
            <em x-text="'/ ' + $store.experienceBooking.livePriceSuffix()">
                /{{ __('ui.price_type.per_person') }}
            </em>
        </div>

        @if ($detail->bookingSummary['supports_private'])
            <div class="ed-bp-type-toggle" aria-label="{{ __('ui.fields.booking_type') }}">
                <button type="button"
                        :class="{ 'is-active': $store.experienceBooking.bookingType === 'group' }"
                        @click="$store.experienceBooking.setBookingType('group')">
                    {{ __('booking.types.group') }}
                </button>
                <button type="button"
                        :class="{ 'is-active': $store.experienceBooking.bookingType === 'private' }"
                        @click="$store.experienceBooking.setBookingType('private')">
                    {{ __('booking.types.private') }}
                </button>
            </div>
        @endif

        {{-- Participants dropdown --}}
        <div class="ed-bp-field ed-bp-field--guests" @click.outside="guestsOpen = false">
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
                        <button type="button" :disabled="$store.experienceBooking.adults <= 1" @click="$store.experienceBooking.adults--; $store.experienceBooking.clearQuote()" aria-label="-">−</button>
                        <span x-text="$store.experienceBooking.adults"></span>
                        <button type="button" :disabled="$store.experienceBooking.adults + $store.experienceBooking.children >= {{ $experience->max_group_size }}" @click="$store.experienceBooking.adults++; $store.experienceBooking.clearQuote()" aria-label="+">+</button>
                    </div>
                </div>
                <div class="ed-bp-counter-row">
                    <div>
                        <p class="ed-bp-counter-title">{{ __('ui.experience_detail.booking_panel.children') }}</p>
                        <p class="ed-bp-counter-sub">{{ __('ui.experience_detail.booking_panel.children_age') }}</p>
                    </div>
                    <div class="ed-bp-counter">
                        <button type="button" :disabled="$store.experienceBooking.children <= 0" @click="$store.experienceBooking.children--; $store.experienceBooking.syncChildAges(); $store.experienceBooking.clearQuote()" aria-label="-">−</button>
                        <span x-text="$store.experienceBooking.children"></span>
                        <button type="button" :disabled="$store.experienceBooking.adults + $store.experienceBooking.children >= {{ $experience->max_group_size }}" @click="$store.experienceBooking.children++; $store.experienceBooking.syncChildAges(); $store.experienceBooking.clearQuote()" aria-label="+">+</button>
                    </div>
                </div>

                {{-- Child age selectors --}}
                <template x-if="$store.experienceBooking.children > 0">
                    <div class="ed-bp-child-ages">
                        <p class="ed-bp-child-ages-label">{{ __('ui.experience_detail.booking_panel.child_ages_label') }}</p>
                        <template x-for="(age, i) in $store.experienceBooking.childAges" :key="i">
                            <div class="ed-bp-child-age-row">
                                <label class="ed-bp-child-age-name" x-text="'{{ __('ui.experience_detail.booking_panel.child') }} ' + (i + 1)"></label>
                                <select class="ed-bp-child-age-select"
                                        :value="age ?? ''"
                                        @change="$store.experienceBooking.childAges[i] = parseInt($event.target.value); $store.experienceBooking.clearQuote()">
                                    <option value="">{{ __('ui.experience_detail.booking_panel.age_placeholder') }}</option>
                                    @for ($age = 0; $age <= 17; $age++)
                                        <option value="{{ $age }}">{{ $age === 0 ? __('ui.experience_detail.booking_panel.age_under_1') : $age . ' ' . __('ui.experience_detail.booking_panel.years') }}</option>
                                    @endfor
                                </select>
                            </div>
                        </template>
                    </div>
                </template>

                <button type="button" class="ed-bp-apply" @click="guestsOpen = false; if ($store.experienceBooking.hasSelection($store.experienceBooking.activeOption)) $store.experienceBooking.applyQuote()">{{ __('ui.experience_detail.booking_panel.apply') }}</button>
            </div>
        </div>

        {{-- Start date dropdown (opens calendar modal) --}}
        <div class="ed-bp-field ed-bp-field--date">
            <button type="button" class="ed-bp-trigger" @click="dateOpen = true; guestsOpen = false; langOpen = false">
                <span aria-hidden="true">▦</span>
                <span x-text="$store.experienceBooking.selectedDateLabel || '{{ __('ui.experience_detail.booking_panel.select_date') }}'"></span>
                <span class="ed-bp-chevron" aria-hidden="true">▾</span>
            </button>
        </div>

        {{-- Language dropdown --}}
        <div class="ed-bp-field ed-bp-field--lang" @click.outside="langOpen = false">
            <button type="button" class="ed-bp-trigger" @click="langOpen = ! langOpen; guestsOpen = false; dateOpen = false">
                <span aria-hidden="true">◯</span>
                <span x-text="selectedLang"></span>
                <span class="ed-bp-chevron" :class="{ 'is-open': langOpen }" aria-hidden="true">▾</span>
            </button>

            <div class="ed-bp-popover ed-bp-lang-popover" x-show="langOpen" x-cloak x-transition>
                @foreach (['en' => 'English', 'fr' => 'Français'] as $code => $label)
                    <label class="ed-bp-radio">
                        <input type="radio" name="bp-lang" value="{{ $label }}"
                               @if ($code === 'en') checked @endif
                               @click="selectedLang = '{{ $label }}'; $store.experienceBooking.selectedLanguage = '{{ $code }}'; $store.experienceBooking.clearQuote(); langOpen = false; if ($store.experienceBooking.hasSelection($store.experienceBooking.activeOption)) $store.experienceBooking.applyQuote()">
                        <span>{{ $label }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        <p class="ed-bp-checking" x-show="$store.experienceBooking.isQuoting" x-cloak>{{ __('ui.experience_detail.booking_panel.checking') }}</p>

        <p class="ed-bp-error" x-show="$store.experienceBooking.quoteError" x-cloak x-text="$store.experienceBooking.quoteError"></p>

        {{-- Live price summary — always visible once participants > 0 --}}
        <div class="ed-bp-live-total" x-show="$store.experienceBooking.adults + $store.experienceBooking.children > 0">
            <div class="ed-bp-live-breakdown">
                <span class="ed-bp-live-opt" x-show="$store.experienceBooking.activeOptionTitle()" x-cloak x-text="$store.experienceBooking.activeOptionTitle()"></span>
                <span x-text="$store.experienceBooking.liveBreakdown()"></span>
            </div>
            <strong class="ed-bp-live-amount" x-text="$store.experienceBooking.liveTotalFormatted()"></strong>
        </div>

        {{-- Server-confirmed quote (after Check availability) --}}
        <div class="ed-bp-quote" x-show="$store.experienceBooking.quote" x-cloak x-transition>
            <span>{{ __('ui.experience_detail.booking_panel.selected_total') }}</span>
            <strong x-text="$store.experienceBooking.quote ? ($store.experienceBooking.quote.currency + ' ' + $store.experienceBooking.quote.total) : ''"></strong>
        </div>

        <div class="ed-bp-trust">
            <div class="ed-bp-trust-item">
                <span class="ed-bp-trust-icon" aria-hidden="true">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                </span>
                <span class="ed-bp-trust-text">
                    <strong>{{ __('ui.experience_detail.booking_panel.free_cancellation_title') }}</strong>
                    <em>{{ __('ui.experience_detail.booking_panel.free_cancellation_body') }}</em>
                </span>
            </div>
            <div class="ed-bp-trust-item">
                <span class="ed-bp-trust-icon" aria-hidden="true">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                </span>
                <span class="ed-bp-trust-text">
                    <strong>{{ __('ui.experience_detail.key_specs.pay_later_title') }}</strong>
                    <em>{{ __('ui.experience_detail.key_specs.pay_later_body') }}</em>
                </span>
            </div>
            <a href="#overview" class="ed-bp-trust-readmore">{{ __('ui.actions.read_more') }}</a>
        </div>
    </div>

    {{-- Date selection modal — teleported to <body> so it escapes the sticky
         sidebar's `overflow: auto`, which would otherwise become the containing
         block for this fixed-position modal and misplace it mid-scroll. --}}
    <template x-teleport="body">
    <div class="ed-bp-modal" x-show="dateOpen" x-cloak x-transition @click.self="dateOpen = false">
        <div class="ed-bp-modal-card">
            <button type="button" class="ed-bp-modal-close" @click="dateOpen = false" aria-label="{{ __('ui.actions.close') }}">×</button>

            <div class="ed-calendar">
                <div class="ed-calendar-head">
                    <button type="button" disabled aria-label="{{ __('ui.experience_detail.previous_month') }}">‹</button>
                    <strong>{{ $detail->bookingSummary['calendar']['month_label'] }}</strong>
                    <button type="button" disabled aria-label="{{ __('ui.experience_detail.next_month') }}">›</button>
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
                            @if ($day['in_month'] && in_array($day['status'], ['available', 'few'], true))
                                :class="{ 'selected': @js($day['date']) === $store.experienceBooking.selectedDate }"
                                @click="pickDate(@js($day['date']), @js($dayLabel), @js($day['availability_ids']))"
                            @endif
                            >
                                {{ $day['number'] }}
                            </span>
                        @endforeach
                    @endforeach
                </div>

                @if ($detail->bookingSummary['has_future_availability'])
                    <div class="ed-calendar-legend">
                        <span><i class="available"></i>{{ __('ui.experience_detail.calendar.available') }}</span>
                        <span><i class="few"></i>{{ __('ui.experience_detail.calendar.few') }}</span>
                        <span><i class="full"></i>{{ __('ui.experience_detail.calendar.full') }}</span>
                    </div>
                @else
                    <div class="ed-calendar-empty">
                        <strong>{{ __('ui.experience_detail.booking_panel.no_dates_title') }}</strong>
                        <p>{{ __('ui.experience_detail.booking_panel.no_dates_body') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
    </template>
</div>
