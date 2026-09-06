@extends('layouts.agency')

@section('title', __('ui.pages.bookings_title') . ' #' . $booking->id)

@section('page-title', '#' . $booking->id . ' — ' . $booking->guest_name)

@section('content')

    <div class="mb-4">
        <a href="{{ route('agency.bookings.index') }}" class="ag-back-link">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
            {{ __('ui.nav.bookings') }}
        </a>
    </div>

    @php
        $locale      = app()->getLocale();
        $expTitle    = $booking->experience?->getTranslation('title', $locale, false) ?? '—';
        $date        = $booking->availability?->date?->format('d M Y') ?? '—';
        $time        = $booking->availability ? substr($booking->availability->time_slot, 0, 5) : '—';
        $statusColor = match($booking->status) {
            'confirmed'  => 'green',
            'cancelled'  => 'red',
            'completed'  => 'blue',
            default      => 'amber',
        };
    @endphp

    <div class="ag-detail-grid">

        {{-- ── Left column ──────────────────────────────── --}}
        <div style="display:flex;flex-direction:column;gap:16px;">

            <div class="ag-card">
                <div class="ag-card-header">
                    <span class="ag-card-title">{{ __('ui.fields.booking_details') }}</span>
                    <x-ui.badge :color="$statusColor">{{ __('booking.status.' . $booking->status) }}</x-ui.badge>
                </div>
                <div class="ag-detail-rows">
                    <div class="ag-detail-row">
                        <span class="ag-detail-label">{{ __('ui.fields.experience') }}</span>
                        <span class="ag-detail-value">{{ $expTitle }}</span>
                    </div>
                    <div class="ag-detail-row">
                        <span class="ag-detail-label">{{ __('ui.fields.date') }}</span>
                        <span class="ag-detail-value">{{ $date }} · {{ $time }}</span>
                    </div>
                    <div class="ag-detail-row">
                        <span class="ag-detail-label">{{ __('ui.fields.booking_type') }}</span>
                        <span class="ag-detail-value">{{ __('booking.types.' . $booking->booking_type) }}</span>
                    </div>
                    <div class="ag-detail-row">
                        <span class="ag-detail-label">{{ __('ui.fields.participants_count') }}</span>
                        <span class="ag-detail-value">{{ $booking->participants_count }}</span>
                    </div>
                    <div class="ag-detail-row">
                        <span class="ag-detail-label">{{ __('ui.fields.total_price') }}</span>
                        <span class="ag-detail-value font-semibold">{{ number_format((float) $booking->total_price, 2) }} MAD</span>
                    </div>
                    @if ($booking->notes)
                        <div class="ag-detail-row ag-detail-row--block">
                            <span class="ag-detail-label">{{ __('ui.fields.special_notes') }}</span>
                            <p class="ag-detail-value">{{ $booking->notes }}</p>
                        </div>
                    @endif
                </div>
            </div>

            @if ($booking->payout)
                <div class="ag-card">
                    <div class="ag-card-header">
                        <span class="ag-card-title">{{ __('ui.nav.payouts') }}</span>
                        <x-ui.badge :color="$booking->payout->status === 'paid' ? 'green' : 'amber'">
                            {{ ucfirst($booking->payout->status) }}
                        </x-ui.badge>
                    </div>
                    <div class="ag-detail-rows">
                        <div class="ag-detail-row">
                            <span class="ag-detail-label">{{ __('ui.fields.amount') }}</span>
                            <span class="ag-detail-value font-semibold">{{ number_format((float) $booking->payout->amount, 2) }} MAD</span>
                        </div>
                        @if ($booking->payout->transferred_at)
                            <div class="ag-detail-row">
                                <span class="ag-detail-label">{{ __('ui.fields.transferred_at') }}</span>
                                <span class="ag-detail-value">{{ $booking->payout->transferred_at->format('d M Y') }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

        </div>

        {{-- ── Right column: guest info ─────────────────── --}}
        <div>
            <div class="ag-card">
                <div class="ag-card-header">
                    <span class="ag-card-title">{{ __('ui.fields.guest_name') }}</span>
                </div>
                <div class="ag-detail-rows">
                    <div class="ag-detail-row">
                        <span class="ag-detail-label">{{ __('ui.fields.guest_name') }}</span>
                        <span class="ag-detail-value">{{ $booking->guest_name }}</span>
                    </div>
                    <div class="ag-detail-row">
                        <span class="ag-detail-label">{{ __('ui.fields.email') }}</span>
                        <span class="ag-detail-value">
                            <a href="mailto:{{ $booking->guest_email }}" class="ag-link">{{ $booking->guest_email }}</a>
                        </span>
                    </div>
                    @if ($booking->guest_phone)
                        <div class="ag-detail-row">
                            <span class="ag-detail-label">{{ __('ui.fields.contact_phone') }}</span>
                            <span class="ag-detail-value">{{ $booking->guest_phone }}</span>
                        </div>
                    @endif
                    <div class="ag-detail-row">
                        <span class="ag-detail-label">{{ __('ui.fields.reference') }}</span>
                        <span class="ag-detail-value text-slate-500 font-mono text-sm">#{{ $booking->id }}</span>
                    </div>
                    <div class="ag-detail-row">
                        <span class="ag-detail-label">{{ __('ui.fields.created_at') }}</span>
                        <span class="ag-detail-value">{{ $booking->created_at->format('d M Y, H:i') }}</span>
                    </div>
                </div>
            </div>
        </div>

    </div>

@endsection
