@extends('layouts.admin')

@section('page-title', 'Booking #' . str_pad($booking->id, 5, '0', STR_PAD_LEFT))
@section('page-subtitle', $booking->experience?->getTranslation('title', app()->getLocale(), false) . ' · ' . ucfirst($booking->status))

@section('topbar-actions')
    <a href="{{ route('admin.bookings.index') }}" class="adm-btn adm-btn--ghost">← Back</a>
@endsection

@section('content')

    @if(session('success'))
        <div class="adm-flash adm-flash--success" x-data x-init="setTimeout(() => $el.remove(), 4000)">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="adm-flash adm-flash--error" x-data x-init="setTimeout(() => $el.remove(), 5000)">
            {{ session('error') }}
        </div>
    @endif

    <div class="adm-grid-2-1">

        {{-- Left: booking details --}}
        <div style="display:flex;flex-direction:column;gap:20px;">

            {{-- Guest details --}}
            <div class="adm-card">
                <div class="adm-card-header">
                    <span class="adm-card-title">Guest Details</span>
                    @php
                        $sc = match($booking->status) {
                            'confirmed' => 'adm-badge--confirmed',
                            'completed' => 'adm-badge--completed',
                            'cancelled' => 'adm-badge--cancelled',
                            default     => 'adm-badge--pending',
                        };
                    @endphp
                    <span class="adm-badge {{ $sc }}">{{ __('ui.status.'.$booking->status) }}</span>
                </div>
                <div class="adm-card-body" style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
                    <div>
                        <div style="font-size:.7rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--adm-text-muted);margin-bottom:3px;">Name</div>
                        <div style="font-size:.9rem;font-weight:600;color:var(--adm-text);">{{ $booking->guest_name }}</div>
                    </div>
                    <div>
                        <div style="font-size:.7rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--adm-text-muted);margin-bottom:3px;">Email</div>
                        <div style="font-size:.875rem;color:var(--adm-text);">{{ $booking->guest_email }}</div>
                    </div>
                    <div>
                        <div style="font-size:.7rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--adm-text-muted);margin-bottom:3px;">Phone</div>
                        <div style="font-size:.875rem;color:var(--adm-text);">{{ $booking->guest_phone ?: '—' }}</div>
                    </div>
                    <div>
                        <div style="font-size:.7rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--adm-text-muted);margin-bottom:3px;">Country</div>
                        <div style="font-size:.875rem;color:var(--adm-text);">{{ $booking->contact_country ?: '—' }}</div>
                    </div>
                    @if($booking->special_notes)
                        <div style="grid-column:span 2;">
                            <div style="font-size:.7rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--adm-text-muted);margin-bottom:3px;">Special Notes</div>
                            <div style="font-size:.875rem;color:var(--adm-text);font-style:italic;">{{ $booking->special_notes }}</div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Trip details --}}
            <div class="adm-card">
                <div class="adm-card-header"><span class="adm-card-title">Trip Details</span></div>
                <div class="adm-card-body" style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
                    <div style="grid-column:span 2;">
                        <div style="font-size:.7rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--adm-text-muted);margin-bottom:3px;">Experience</div>
                        <div style="font-size:.9rem;font-weight:600;color:var(--adm-text);">
                            {{ $booking->experience?->getTranslation('title', app()->getLocale(), false) }}
                        </div>
                        <div style="font-size:.8rem;color:var(--adm-text-muted);">{{ $booking->experience?->agency?->name }}</div>
                    </div>
                    <div>
                        <div style="font-size:.7rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--adm-text-muted);margin-bottom:3px;">Date & Time</div>
                        <div style="font-size:.875rem;font-weight:600;color:var(--adm-text);">{{ $booking->availability?->date?->format('M j, Y') }}</div>
                        <div style="font-size:.8rem;color:var(--adm-text-muted);">{{ substr($booking->availability?->time_slot ?? '', 0, 5) }}</div>
                    </div>
                    <div>
                        <div style="font-size:.7rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--adm-text-muted);margin-bottom:3px;">Booking Type</div>
                        <div style="font-size:.875rem;font-weight:600;color:var(--adm-text);">{{ ucfirst($booking->booking_type) }}</div>
                        <div style="font-size:.8rem;color:var(--adm-text-muted);">{{ $booking->participants_count }} participant(s)</div>
                    </div>
                    <div>
                        <div style="font-size:.7rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--adm-text-muted);margin-bottom:3px;">Pickup</div>
                        <div style="font-size:.875rem;color:var(--adm-text);">{{ $booking->pickup_status ? ucfirst(str_replace('_',' ',$booking->pickup_status)) : '—' }}</div>
                        @if($booking->pickup_address)
                            <div style="font-size:.75rem;color:var(--adm-text-muted);">{{ $booking->pickup_address }}</div>
                        @endif
                    </div>
                    <div>
                        <div style="font-size:.7rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--adm-text-muted);margin-bottom:3px;">Language</div>
                        <div style="font-size:.875rem;color:var(--adm-text);">{{ strtoupper($booking->tour_language ?? '—') }}</div>
                    </div>
                </div>
            </div>

        </div>

        {{-- Right: financials + actions --}}
        <div style="display:flex;flex-direction:column;gap:20px;">

            {{-- Financials --}}
            <div class="adm-card">
                <div class="adm-card-header"><span class="adm-card-title">Financials</span></div>
                <div class="adm-card-body" style="display:flex;flex-direction:column;gap:14px;">
                    @foreach([
                        ['label'=>'Total Price',     'value'=>number_format((float)$booking->total_price, 2).' MAD',      'bold'=>true],
                        ['label'=>'Commission ('.$booking->commission_rate.'%)', 'value'=>number_format((float)$booking->commission_amount, 2).' MAD', 'bold'=>false],
                        ['label'=>'Agency Amount',   'value'=>number_format((float)$booking->agency_amount, 2).' MAD',    'bold'=>false],
                        ['label'=>'Payment Status',  'value'=>str_replace('_',' ',ucfirst($booking->payment_status??'—')), 'bold'=>false],
                    ] as $row)
                        <div style="display:flex;justify-content:space-between;align-items:center;padding-bottom:10px;border-bottom:1px solid var(--adm-border);">
                            <div style="font-size:.8rem;color:var(--adm-text-muted);">{{ $row['label'] }}</div>
                            <div style="font-size:{{ $row['bold'] ? '1rem' : '.875rem' }};font-weight:{{ $row['bold'] ? '700' : '500' }};color:{{ $row['bold'] ? 'var(--adm-navy)' : 'var(--adm-text)' }};">
                                {{ $row['value'] }}
                            </div>
                        </div>
                    @endforeach

                    {{-- Update payment status --}}
                    <form method="POST" action="{{ route('admin.bookings.payment-status.update', $booking) }}" style="display:flex;gap:8px;margin-top:4px;">
                        @csrf @method('PATCH')
                        <select name="payment_status" class="adm-select" style="flex:1;">
                            @foreach(['pending','not_required','paid','failed','cancelled'] as $ps)
                                <option value="{{ $ps }}" @selected($booking->payment_status === $ps)>
                                    {{ str_replace('_',' ',ucfirst($ps)) }}
                                </option>
                            @endforeach
                        </select>
                        <button class="adm-btn adm-btn--ghost adm-btn--sm">Update</button>
                    </form>
                </div>
            </div>

            {{-- Payout info --}}
            @if($booking->payout)
                <div class="adm-card">
                    <div class="adm-card-header"><span class="adm-card-title">Payout</span></div>
                    <div class="adm-card-body" style="display:flex;flex-direction:column;gap:10px;">
                        <div style="display:flex;justify-content:space-between;">
                            <span style="font-size:.8rem;color:var(--adm-text-muted);">Amount</span>
                            <span style="font-weight:700;color:var(--adm-navy);">{{ number_format((float)$booking->payout->amount, 2) }} MAD</span>
                        </div>
                        <div style="display:flex;justify-content:space-between;">
                            <span style="font-size:.8rem;color:var(--adm-text-muted);">Status</span>
                            <span class="adm-badge {{ $booking->payout->status === 'paid' ? 'adm-badge--confirmed' : 'adm-badge--pending' }}">
                                {{ ucfirst($booking->payout->status) }}
                            </span>
                        </div>
                        @if($booking->payout->transferred_at)
                            <div style="display:flex;justify-content:space-between;">
                                <span style="font-size:.8rem;color:var(--adm-text-muted);">Transferred</span>
                                <span style="font-size:.8rem;color:var(--adm-text);">{{ $booking->payout->transferred_at->format('M j, Y') }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            {{-- Actions --}}
            <div class="adm-card">
                <div class="adm-card-header"><span class="adm-card-title">Actions</span></div>
                <div class="adm-card-body" style="display:flex;flex-direction:column;gap:8px;">
                    @if($booking->status === 'pending')
                        <form method="POST" action="{{ route('admin.bookings.confirm', $booking) }}">
                            @csrf
                            <button class="adm-btn adm-btn--success" style="width:100%;">✓ Confirm Booking</button>
                        </form>
                    @endif
                    @if(in_array($booking->status, ['pending','confirmed']))
                        <form method="POST" action="{{ route('admin.bookings.cancel', $booking) }}">
                            @csrf
                            <button class="adm-btn adm-btn--danger" style="width:100%;"
                                    onclick="return confirm('Cancel this booking? Seats will be released.')">
                                ✗ Cancel Booking
                            </button>
                        </form>
                    @endif
                    @if(in_array($booking->status, ['completed','cancelled']))
                        <p style="font-size:.8rem;color:var(--adm-text-muted);text-align:center;">No actions available.</p>
                    @endif
                </div>
            </div>

        </div>

    </div>

@endsection
