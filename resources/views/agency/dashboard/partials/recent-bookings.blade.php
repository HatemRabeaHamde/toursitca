@php
    $statusMap = [
        'confirmed'  => 'confirmed',
        'pending'    => 'pending',
        'completed'  => 'completed',
        'cancelled'  => 'cancelled',
    ];

    $avatarColors = [0, 1, 2, 3, 4, 5];
@endphp

<div class="ag-card" style="animation: agFadeUp .4s .15s both;">

    <div class="ag-card-header">
        <span class="ag-card-title">{{ __('ui.agency_dashboard.recent_bookings') }}</span>
        <a href="{{ route('agency.bookings.index') }}" class="ag-section-link">
            {{ __('ui.agency_dashboard.view_all') }}
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
        </a>
    </div>

    @if ($recentBookings->isEmpty())
        <div class="ag-empty">
            <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            {{ __('ui.messages.no_bookings') }}
        </div>
    @else
        <ul class="ag-booking-list ag-card-body--flush">
            @foreach ($recentBookings as $i => $booking)
                @php
                    $initials = collect(explode(' ', $booking->guest_name))
                        ->take(2)->map(fn ($w) => strtoupper($w[0] ?? ''))->implode('');
                    $color = $avatarColors[$i % count($avatarColors)];
                    $status = $statusMap[$booking->status] ?? 'pending';
                    $expTitle = $booking->experience->getTranslation('title', app()->getLocale(), false);
                    $date = $booking->availability?->date?->format('M j') ?? '—';
                    $time = $booking->availability ? substr($booking->availability->time_slot, 0, 5) : '';
                @endphp
                <li>
                    <a href="{{ route('agency.bookings.show', $booking) }}" class="ag-booking-row">
                        <div class="ag-avatar ag-avatar--{{ $color }}" aria-hidden="true">{{ $initials }}</div>

                        <div class="ag-booking-info">
                            <div class="ag-booking-name">{{ $booking->guest_name }}</div>
                            <div class="ag-booking-exp">{{ $expTitle }}</div>
                        </div>

                        <div class="ag-booking-meta">
                            <div class="ag-booking-amount">{{ number_format((float) $booking->total_price, 0) }} MAD</div>
                            <div class="ag-booking-date">{{ $date }}{{ $time ? ' · ' . $time : '' }}</div>
                        </div>

                        <span class="ag-badge ag-badge--{{ $status }}">
                            {{ __('booking.status.' . $booking->status) }}
                        </span>
                    </a>
                </li>
            @endforeach
        </ul>
    @endif

</div>
