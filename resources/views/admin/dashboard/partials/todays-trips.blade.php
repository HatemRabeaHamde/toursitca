<div class="adm-card" style="animation-delay:.3s">
    <div class="adm-card-header">
        <span class="adm-card-title">{{ __('ui.dashboard.todays_trips') }}</span>
        <span class="adm-card-meta">{{ now()->format('l, M j') }}</span>
    </div>

    @if($todaysSlots->isEmpty())
        <div class="adm-empty">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            {{ __('ui.dashboard.no_trips_today') }}
        </div>
    @else
        <div class="adm-card-body--flush">
            @foreach($todaysSlots as $slot)
                @php
                    $avail = $slot->availableSeats();
                    $pct   = $slot->max_seats > 0 ? round(($avail / $slot->max_seats) * 100) : 0;
                    $isFull = $avail === 0;
                    $isNearFull = $pct <= 25 && ! $isFull;
                @endphp
                <div class="adm-trip-row">
                    <div class="adm-trip-time">{{ substr($slot->time_slot, 0, 5) }}</div>
                    <div class="adm-trip-info">
                        <div class="adm-trip-title">
                            {{ Str::limit($slot->experience->getTranslation('title', app()->getLocale(), false), 34) }}
                        </div>
                        <div class="adm-trip-agency">{{ $slot->experience->agency?->name }}</div>
                    </div>
                    <div class="adm-trip-seats">
                        @if($isFull)
                            <span class="adm-badge adm-badge--cancelled" style="font-size:.65rem;">Full</span>
                        @else
                            <span class="adm-trip-seat-count {{ $isNearFull ? 'adm-trip-seat-count--warn' : '' }}">
                                {{ $avail }}/{{ $slot->max_seats }}
                            </span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
