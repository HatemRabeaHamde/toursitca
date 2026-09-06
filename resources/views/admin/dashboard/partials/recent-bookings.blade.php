<div class="adm-card" style="animation-delay:.2s">
    <div class="adm-card-header">
        <span class="adm-card-title">{{ __('ui.dashboard.recent_bookings') }}</span>
        <a href="{{ route('admin.bookings.index') }}" class="adm-card-link">
            {{ __('ui.nav.view_all') }}
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
        </a>
    </div>

    @if($recentBookings->isEmpty())
        <div class="adm-empty">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            {{ __('ui.messages.no_bookings') }}
        </div>
    @else
        <ul class="adm-booking-list adm-card-body--flush">
            @foreach($recentBookings as $i => $booking)
                @php
                    $initials = collect(explode(' ', $booking->guest_name ?? '?'))
                        ->map(fn($w) => strtoupper(substr($w, 0, 1)))
                        ->take(2)->implode('');
                    $statusClass = match($booking->status) {
                        'confirmed'  => 'adm-badge--confirmed',
                        'pending'    => 'adm-badge--pending',
                        'completed'  => 'adm-badge--completed',
                        'cancelled'  => 'adm-badge--cancelled',
                        default      => 'adm-badge--pending',
                    };
                @endphp
                <li>
                    <div class="adm-booking-row">
                        <div class="adm-avatar adm-avatar--{{ $i % 6 }}">{{ $initials }}</div>
                        <div class="adm-booking-info">
                            <div class="adm-booking-name">{{ $booking->guest_name ?? '—' }}</div>
                            <div class="adm-booking-exp">{{ $booking->experience?->getTranslation('title', app()->getLocale(), false) }}</div>
                            <div class="adm-booking-agency">{{ $booking->experience?->agency?->name }}</div>
                        </div>
                        <div class="adm-booking-meta">
                            <div class="adm-booking-amount">{{ number_format((float)$booking->total_price, 0) }} MAD</div>
                            <div class="adm-booking-date">{{ $booking->availability?->date?->format('M j') }}</div>
                            <div style="margin-top:5px;">
                                <span class="adm-badge {{ $statusClass }}">{{ __('ui.status.' . $booking->status) }}</span>
                            </div>
                        </div>
                    </div>
                </li>
            @endforeach
        </ul>
    @endif
</div>
