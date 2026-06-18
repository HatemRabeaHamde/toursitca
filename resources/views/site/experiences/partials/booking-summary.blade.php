<aside class="ed-booking-panel">
    <div class="ed-booking-card">
        <div class="ed-price-line">
            @if ($detail->bookingSummary['original_price'])
                <span>{{ $detail->bookingSummary['currency'] }} {{ $detail->bookingSummary['original_price'] }}</span>
            @endif
            <strong>{{ $detail->bookingSummary['currency'] }} {{ $detail->bookingSummary['price_from'] }}</strong>
            <em>/{{ __('ui.price_type.per_person') }}</em>
        </div>

        <div class="ed-booking-section">
            <h2>{{ __('ui.experience_detail.availability_title') }}</h2>
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
                            <span @class([
                                'ed-day',
                                'muted' => ! $day['in_month'],
                                'selected' => $day['selected'],
                                'available' => $day['status'] === 'available',
                                'few' => $day['status'] === 'few',
                                'full' => $day['status'] === 'full',
                            ])>
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

        <div class="ed-booking-section">
            <h2>{{ __('ui.experience_detail.experience_type_title') }}</h2>
            <div class="ed-choice-row">
                <span>{{ __('booking.types.group') }}</span>
                @if ($detail->bookingSummary['supports_private'])
                    <span>{{ __('booking.types.private') }}</span>
                @endif
            </div>
        </div>

        <div class="ed-booking-section">
            <h2>{{ __('ui.experience_detail.guests_title') }}</h2>
            <p>{{ __('ui.fields.max_group_size') }} {{ $detail->experience->max_group_size }}</p>
        </div>

        <div class="ed-booking-section">
            <h2>{{ __('ui.experience_detail.next_dates_title') }}</h2>
            @if ($detail->bookingSummary['next_availabilities'])
                <div class="ed-date-list">
                    @foreach ($detail->bookingSummary['next_availabilities'] as $availability)
                        <p>{{ $availability['date'] }} · {{ $availability['time'] }} · {{ __('ui.fields.available_seats') }}: {{ $availability['available_seats'] }}</p>
                    @endforeach
                </div>
            @else
                <p>{{ __('ui.messages.no_booking_slots') }}</p>
            @endif
        </div>

        <x-ui.button href="{{ $detail->bookingUrl }}" class="w-full justify-center !rounded-[12px] !bg-[#D2592D] hover:!bg-[#b94722]">
            {{ __('ui.nav.book_now') }}
        </x-ui.button>
    </div>
</aside>
