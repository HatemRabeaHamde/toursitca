<div class="adm-card" style="animation-delay:.3s">
    <div class="adm-card-header">
        <span class="adm-card-title">{{ __('ui.dashboard.almost_full_slots') }}</span>
        <a href="{{ route('admin.availability.index') }}" class="adm-card-link">
            {{ __('ui.nav.view_all') }}
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
        </a>
    </div>

    @if($almostFullSlots->isEmpty())
        <div class="adm-empty">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            {{ __('ui.dashboard.no_almost_full_slots') }}
        </div>
    @else
        <ul class="adm-slot-list adm-card-body--flush">
            @foreach($almostFullSlots as $slot)
                @php
                    $avail = $slot->availableSeats();
                    $pct   = $slot->max_seats > 0 ? round(($avail / $slot->max_seats) * 100) : 0;
                    $fillClass = $pct <= 20 ? 'adm-slot-bar-fill--full' : 'adm-slot-bar-fill--warn';
                @endphp
                <li class="adm-slot-row">
                    <div class="adm-slot-icon">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                            <line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
                        </svg>
                    </div>
                    <div class="adm-slot-body">
                        <div class="adm-slot-exp">{{ $slot->experience->getTranslation('title', app()->getLocale(), false) }}</div>
                        <div class="adm-slot-detail">
                            {{ $slot->experience->agency->name }} · {{ $slot->date->format('M j, Y') }} · {{ substr($slot->time_slot, 0, 5) }}
                        </div>
                    </div>
                    <div class="adm-slot-seats">
                        <div class="adm-slot-seats-count">{{ $avail }} / {{ $slot->max_seats }}</div>
                        <div class="adm-slot-bar">
                            <div class="adm-slot-bar-fill {{ $fillClass }}" style="width: {{ $pct }}%"></div>
                        </div>
                    </div>
                </li>
            @endforeach
        </ul>
    @endif
</div>
