@extends('layouts.agency')

@section('page-title', __('ui.pages.bookings_title'))
@section('page-subtitle', __('ui.agency_dashboard.bookings_subtitle'))

@section('topbar-actions')
    <form method="GET" action="{{ route('agency.bookings.index') }}" class="ag-filter-row">
        <select name="status" class="ag-filter-select" onchange="this.form.submit()">
            <option value="">{{ __('ui.fields.all_statuses') }}</option>
            @foreach (['pending', 'confirmed', 'completed', 'cancelled'] as $s)
                <option value="{{ $s }}" @selected($status === $s)>{{ __('booking.status.' . $s) }}</option>
            @endforeach
        </select>
    </form>
@endsection

@section('content')

    <div class="ag-card">

        <div class="ag-card-header">
            <span class="ag-card-title">
                {{ $bookings->total() }} {{ __('ui.agency_dashboard.bookings_found') }}
                @if ($status)
                    · <span style="color:var(--clay);font-weight:500;">{{ __('booking.status.' . $status) }}</span>
                @endif
            </span>
        </div>

        @if ($bookings->isEmpty())
            <div class="ag-empty">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/>
                </svg>
                {{ __('ui.messages.no_bookings') }}
            </div>
        @else
            <ul class="ag-booking-list ag-card-body--flush">
                @foreach ($bookings as $i => $booking)
                    @php
                        $initials = collect(explode(' ', $booking->guest_name))
                            ->take(2)->map(fn ($w) => strtoupper($w[0] ?? ''))->implode('');
                        $color = $i % 6;
                        $status = $booking->status;
                        $expTitle = $booking->experience?->getTranslation('title', app()->getLocale(), false) ?? '—';
                        $date = $booking->availability?->date?->format('M j, Y') ?? '—';
                        $time = $booking->availability ? substr($booking->availability->time_slot, 0, 5) : '';
                    @endphp
                    <li>
                        <a href="{{ route('agency.bookings.show', $booking) }}" class="ag-booking-row ag-booking-row--detail">
                            <div class="ag-avatar ag-avatar--{{ $color }}" aria-hidden="true">{{ $initials }}</div>

                            <div class="ag-booking-info">
                                <div class="ag-booking-name">{{ $booking->guest_name }}</div>
                                <div class="ag-booking-exp">{{ $booking->guest_email }}</div>
                            </div>

                            <div class="ag-booking-exp-col">
                                <div style="font-size:13px;font-weight:500;color:var(--ink);">{{ $expTitle }}</div>
                                <div style="font-size:11.5px;color:var(--ink-70);margin-top:2px;">{{ $date }}{{ $time ? ' · ' . $time : '' }}</div>
                            </div>

                            <div style="text-align:right;flex-shrink:0;">
                                <div style="font-size:12px;color:var(--ink-40);margin-bottom:2px;">{{ __('booking.types.' . $booking->booking_type) }} × {{ $booking->participants_count }}</div>
                                <div class="ag-booking-amount">{{ number_format((float) $booking->total_price, 0) }} MAD</div>
                            </div>

                            <span class="ag-badge ag-badge--{{ $status }}">
                                {{ __('booking.status.' . $status) }}
                            </span>
                        </a>
                    </li>
                @endforeach
            </ul>

            @if ($bookings->hasPages())
                <div class="ag-pagination">
                    {{ $bookings->links() }}
                </div>
            @endif
        @endif

    </div>

@endsection
