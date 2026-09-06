<div class="ed-options-section"
     id="experience-options"
     x-show="$store.experienceBooking.availabilityChecked && $store.experienceBooking.hasSelection()"
     x-cloak
     x-transition.opacity.duration.220ms
     x-init="$store.experienceBooking.activeOption ??= {{ $detail->experienceOptions[0]['id'] ?? 'null' }}">
    <h2 class="ed-options-heading">{{ trans_choice('ui.experience_detail.options.heading', count($detail->experienceOptions), ['count' => count($detail->experienceOptions)]) }}</h2>

    <div class="ed-options-list">
        @foreach ($detail->experienceOptions as $option)
            @php
                $adultPrice = collect($option['prices'])->firstWhere('type', 'adult');
                $childPrice = collect($option['prices'])->firstWhere('type', 'child');
                $groupPrice = collect($option['prices'])->firstWhere('type', 'group');
            @endphp
            <div class="ed-option-card"
                 :class="{ 'is-open': $store.experienceBooking.activeOption === {{ $option['id'] }} }"
                 x-data="{
                     priceType: '{{ $option['price_type'] }}',
                     adultPrice: {{ $adultPrice['price'] ?? 0 }},
                     adultOriginalPrice: {{ $adultPrice['original_price'] ?? 0 }},
                     childPrice: {{ $childPrice['price'] ?? 0 }},
                     childOriginalPrice: {{ $childPrice['original_price'] ?? 0 }},
                     groupPrice: {{ $groupPrice['price'] ?? 0 }},
                     groupOriginalPrice: {{ $groupPrice['original_price'] ?? 0 }},
                     privatePrice: @js($detail->bookingSummary['private_price']),
                     optionId: {{ $option['id'] }},
                     quote() {
                         return $store.experienceBooking.quoteForOption(this.optionId);
                     },
                     total() {
                         const quote = this.quote();

                         if (quote) {
                             return Number(quote.total);
                         }

                         if ($store.experienceBooking.bookingType === 'private' && this.privatePrice) {
                             return Number(this.privatePrice);
                         }

                         if (this.priceType === 'per_group') {
                             return this.groupPrice;
                         }

                         return ($store.experienceBooking.adults * this.adultPrice) + ($store.experienceBooking.children * this.childPrice);
                     },
                     originalTotal() {
                         const quote = this.quote();

                         if (quote && quote.original_subtotal) {
                             return Number(quote.original_subtotal);
                         }

                         if (this.priceType === 'per_group') {
                             return this.groupOriginalPrice;
                         }

                         return ($store.experienceBooking.adults * this.adultOriginalPrice) + ($store.experienceBooking.children * this.childOriginalPrice);
                     },
                     discountPercent() {
                         const original = this.originalTotal();

                         if (! original) {
                             return null;
                         }

                         return Math.round((1 - (this.total() / original)) * 100);
                     },
                 }"
                 x-init="$store.experienceBooking.registerOptionPricing({{ $option['id'] }}, {
                     title: @js($option['title']),
                     priceType: '{{ $option['price_type'] }}',
                     adultPrice: {{ $adultPrice['price'] ?? 0 }},
                     childPrice: {{ $childPrice['price'] ?? 0 }},
                     groupPrice: {{ $groupPrice['price'] ?? 0 }},
                 })">
                <button type="button" class="ed-option-header" @click="$store.experienceBooking.setActiveOption({{ $option['id'] }}); if ($store.experienceBooking.activeOption === {{ $option['id'] }} && $store.experienceBooking.hasSelection({{ $option['id'] }}) && ! $store.experienceBooking.quoteForOption({{ $option['id'] }})) $store.experienceBooking.applyQuote({{ $option['id'] }})">
                    <span class="ed-option-title">{{ $option['title'] }}</span>
                    <span class="ed-option-header-price">
                        <template x-if="discountPercent()">
                            <em x-text="'{{ $option['currency'] }} ' + originalTotal().toFixed(2)"></em>
                        </template>
                        <strong x-text="'{{ $option['currency'] }} ' + total().toFixed(2)"></strong>
                    </span>
                    <span class="ed-option-chevron" :class="{ 'is-open': $store.experienceBooking.activeOption === {{ $option['id'] }} }" aria-hidden="true">▾</span>
                </button>

                <div class="ed-option-body" :class="{ 'is-open': $store.experienceBooking.activeOption === {{ $option['id'] }} }">
                    <div class="ed-option-meta">
                        @if ($option['duration_minutes'])
                            <p>
                                <span class="ed-key-spec-icon" aria-hidden="true">◷</span>
                                <span>{{ trans_choice('ui.units.hours', (int) ceil($option['duration_minutes'] / 60), ['count' => rtrim(rtrim(number_format($option['duration_minutes'] / 60, 1), '0'), '.')]) }}</span>
                            </p>
                        @endif

                        @if (! empty($option['languages']))
                            <p>
                                <span class="ed-key-spec-icon" aria-hidden="true">◯</span>
                                <span>{{ __('ui.experience_detail.options.guide_language', ['language' => implode(', ', array_map('strtoupper', $option['languages']))]) }}</span>
                            </p>
                        @endif

                        @if ($option['pickup_enabled'])
                            <p>
                                <span class="ed-key-spec-icon" aria-hidden="true">⛽</span>
                                <span>{{ __('ui.experience_detail.options.pickup_note') }}</span>
                            </p>
                        @endif

                        @if ($option['private_available'])
                            <p>
                                <span class="ed-key-spec-icon" aria-hidden="true">★</span>
                                <span>{{ __('ui.experience_detail.key_specs.private_title') }}</span>
                            </p>
                        @endif

                        @if ($option['cancellation_hours'])
                            <p>
                                <span class="ed-key-spec-icon" aria-hidden="true">↺</span>
                                <span>{{ __('ui.experience_detail.options.cancellation_note', ['hours' => $option['cancellation_hours']]) }}</span>
                            </p>
                        @endif

                        @if ($option['pay_later_enabled'])
                            <p>
                                <span class="ed-key-spec-icon" aria-hidden="true">✓</span>
                                <span>{{ __('ui.experience_detail.options.pay_later_note') }}</span>
                            </p>
                        @endif
                    </div>

                    <div class="ed-option-price-block">
                        <div class="ed-option-price-line">
                            <template x-if="discountPercent()">
                                <span class="ed-option-original-price" x-text="'{{ $option['currency'] }} ' + originalTotal().toFixed(2)"></span>
                            </template>
                            <template x-if="discountPercent()">
                                <span class="ed-option-discount-badge" x-text="'-' + discountPercent() + '%'"></span>
                            </template>
                            <strong x-text="'{{ $option['currency'] }} ' + total().toFixed(2)"></strong>
                        </div>

                        @if ($option['price_type'] === 'per_group')
                            <p class="ed-option-price-breakdown">
                                {{ __('ui.experience_detail.options.price_breakdown', [
                                    'count' => 1,
                                    'label' => __('ui.experience_detail.options.participant_types.group'),
                                    'price' => $option['currency'].' '.($groupPrice['price'] ?? '0.00'),
                                ]) }}
                            </p>
                        @else
                            <p class="ed-option-price-breakdown" x-show="$store.experienceBooking.bookingType === 'private'">
                                {{ __('booking.types.private') }} · {{ __('ui.experience_detail.key_specs.private_body') }}
                            </p>
                            <p class="ed-option-price-breakdown" x-show="$store.experienceBooking.bookingType !== 'private' && $store.experienceBooking.adults > 0" x-text="$store.experienceBooking.adults + ' x {{ __('ui.experience_detail.options.participant_types.adult') }} · {{ $option['currency'] }} ' + adultPrice.toFixed(2)"></p>
                            <p class="ed-option-price-breakdown" x-show="$store.experienceBooking.bookingType !== 'private' && $store.experienceBooking.children > 0" x-text="$store.experienceBooking.children + ' x {{ __('ui.experience_detail.options.participant_types.child') }} · {{ $option['currency'] }} ' + childPrice.toFixed(2)"></p>
                        @endif

                        <p class="ed-option-taxes">{{ __('ui.experience_detail.options.all_taxes_included') }}</p>

                        <button type="button"
                                class="ed-option-cta"
                                :disabled="$store.experienceBooking.isStartingCheckout || ! $store.experienceBooking.hasSelection({{ $option['id'] }})"
                                @click="$store.experienceBooking.startCheckout({{ $option['id'] }})">
                            <span x-show="! $store.experienceBooking.isStartingCheckout">{{ __('ui.nav.book_now') }}</span>
                            <span x-show="$store.experienceBooking.isStartingCheckout" x-cloak>{{ __('ui.experience_detail.booking_panel.starting_checkout') }}</span>
                        </button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
