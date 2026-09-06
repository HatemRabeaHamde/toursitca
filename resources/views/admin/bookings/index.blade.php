@extends('layouts.admin')

@section('page-title', __('ui.nav.bookings'))
@section('page-subtitle', 'All bookings across every agency')

@section('content')

    @if(session('success'))
        <div class="adm-flash adm-flash--success" x-data x-init="setTimeout(() => $el.remove(), 4000)">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="adm-flash adm-flash--error" x-data x-init="setTimeout(() => $el.remove(), 5000)">{{ session('error') }}</div>
    @endif

    {{-- Status mini-cards --}}
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:20px;">
        @foreach([
            ['label'=>'Pending',   'key'=>'pending',   'color'=>'#b23a2f','bg'=>'#fdeae8'],
            ['label'=>'Confirmed', 'key'=>'confirmed', 'color'=>'#1e7a78','bg'=>'#e3f3f2'],
            ['label'=>'Completed', 'key'=>'completed', 'color'=>'#4a6fa5','bg'=>'#eef2fa'],
            ['label'=>'Cancelled', 'key'=>'cancelled', 'color'=>'#8b5e48','bg'=>'#f4ede8'],
        ] as $s)
            <a href="{{ route('admin.bookings.index', ['status' => $s['key']]) }}"
               style="display:block;background:{{ $status === $s['key'] ? $s['color'] : 'var(--white)' }};border:1px solid {{ $status === $s['key'] ? $s['color'] : 'var(--adm-border)' }};border-radius:14px;padding:16px;text-decoration:none;transition:all .15s;">
                <div style="font-size:1.5rem;font-weight:800;color:{{ $status === $s['key'] ? '#fff' : $s['color'] }};">{{ $counts[$s['key']] }}</div>
                <div style="font-size:.75rem;font-weight:600;color:{{ $status === $s['key'] ? 'rgba(255,255,255,.8)' : 'var(--adm-text-muted)' }};margin-top:2px;">{{ $s['label'] }}</div>
            </a>
        @endforeach
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('admin.bookings.index') }}" style="display:flex;gap:10px;margin-bottom:20px;flex-wrap:wrap;align-items:flex-end;">
        <div style="flex:1;min-width:160px;">
            <label class="adm-label">Status</label>
            <select name="status" class="adm-select">
                <option value="">All statuses</option>
                @foreach(['pending','confirmed','completed','cancelled'] as $s)
                    <option value="{{ $s }}" @selected($status === $s)>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
        </div>
        <div style="flex:1;min-width:160px;">
            <label class="adm-label">Payment</label>
            <select name="payment_status" class="adm-select">
                <option value="">All payments</option>
                @foreach(['pending','not_required','paid','failed','cancelled'] as $ps)
                    <option value="{{ $ps }}" @selected($paymentStatus === $ps)>{{ str_replace('_',' ',ucfirst($ps)) }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="adm-btn adm-btn--primary">Filter</button>
        @if($status || $paymentStatus)
            <a href="{{ route('admin.bookings.index') }}" class="adm-btn adm-btn--ghost">Clear</a>
        @endif
    </form>

    <div class="adm-card">
        @if($bookings->isEmpty())
            <div class="adm-empty">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                <p>No bookings found.</p>
            </div>
        @else
            <div class="adm-table-wrap">
                <table class="adm-table">
                    <thead>
                        <tr>
                            <th>Ref</th>
                            <th>Guest</th>
                            <th>Experience · Agency</th>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bookings as $i => $booking)
                            @php
                                $sc = match($booking->status) {
                                    'confirmed' => 'adm-badge--confirmed',
                                    'completed' => 'adm-badge--completed',
                                    'cancelled' => 'adm-badge--cancelled',
                                    default     => 'adm-badge--pending',
                                };
                            @endphp
                            <tr>
                                <td>
                                    <a href="{{ route('admin.bookings.show', $booking) }}"
                                       style="font-weight:700;color:var(--adm-navy);font-size:.78rem;font-family:monospace;white-space:nowrap;">
                                        #{{ str_pad($booking->id, 5, '0', STR_PAD_LEFT) }}
                                    </a>
                                </td>
                                <td>
                                    <div style="display:flex;align-items:center;gap:9px;">
                                        <div class="adm-avatar adm-avatar--{{ $i % 6 }}" style="width:30px;height:30px;font-size:.7rem;flex-shrink:0;">
                                            {{ strtoupper(substr($booking->guest_name ?? '?', 0, 1)) }}
                                        </div>
                                        <div>
                                            <div style="font-weight:600;font-size:.875rem;color:var(--adm-text);">{{ $booking->guest_name }}</div>
                                            <div style="font-size:.7rem;color:var(--adm-text-muted);">{{ $booking->guest_email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div style="font-size:.875rem;font-weight:500;color:var(--adm-text);">
                                        {{ Str::limit($booking->experience?->getTranslation('title', app()->getLocale(), false), 28) }}
                                    </div>
                                    <div style="font-size:.75rem;color:var(--adm-text-muted);">{{ $booking->experience?->agency?->name }}</div>
                                </td>
                                <td style="font-size:.8rem;white-space:nowrap;">
                                    <div style="color:var(--adm-text);font-weight:500;">{{ $booking->availability?->date?->format('M j, Y') }}</div>
                                    <div style="color:var(--adm-text-muted);">{{ substr($booking->availability?->time_slot ?? '', 0, 5) }}</div>
                                </td>
                                <td style="font-size:.75rem;color:var(--adm-text-muted);white-space:nowrap;">
                                    {{ ucfirst($booking->booking_type) }} · {{ $booking->participants_count }}p
                                </td>
                                <td style="font-weight:700;color:var(--adm-navy);font-size:.875rem;white-space:nowrap;">
                                    {{ number_format((float)$booking->total_price, 0) }} <span style="font-weight:400;font-size:.7rem;">MAD</span>
                                </td>
                                <td><span class="adm-badge {{ $sc }}">{{ __('ui.status.'.$booking->status) }}</span></td>
                                <td>
                                    <div style="display:flex;gap:5px;justify-content:flex-end;">
                                        <a href="{{ route('admin.bookings.show', $booking) }}" class="adm-btn adm-btn--ghost adm-btn--sm">View</a>
                                        @if($booking->status === 'pending')
                                            <form method="POST" action="{{ route('admin.bookings.confirm', $booking) }}">
                                                @csrf
                                                <button class="adm-btn adm-btn--success adm-btn--sm">Confirm</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($bookings->hasPages())
                <div style="padding:16px 20px;border-top:1px solid var(--adm-border);">
                    {{ $bookings->links() }}
                </div>
            @endif
        @endif
    </div>

@endsection
