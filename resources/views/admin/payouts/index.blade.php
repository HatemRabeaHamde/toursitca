@extends('layouts.admin')

@section('page-title', __('ui.nav.payouts'))
@section('page-subtitle', 'Track and mark agency payouts')

@section('content')

    @if(session('success'))
        <div class="adm-flash adm-flash--success" x-data x-init="setTimeout(() => $el.remove(), 4000)">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- Totals --}}
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px;">
        <div class="adm-card" style="border-left:4px solid var(--gold);">
            <div class="adm-card-body" style="display:flex;align-items:center;gap:16px;">
                <div style="width:44px;height:44px;border-radius:12px;background:var(--gold-lt);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--gold)" stroke-width="1.8"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                </div>
                <div>
                    <div style="font-size:1.4rem;font-weight:800;color:var(--adm-navy);">{{ number_format((float)$totals['pending'], 0) }} <span style="font-size:.75rem;font-weight:500;">MAD</span></div>
                    <div style="font-size:.75rem;color:var(--adm-text-muted);font-weight:600;">Pending payouts</div>
                </div>
            </div>
        </div>
        <div class="adm-card" style="border-left:4px solid var(--green);">
            <div class="adm-card-body" style="display:flex;align-items:center;gap:16px;">
                <div style="width:44px;height:44px;border-radius:12px;background:var(--green-lt);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--green)" stroke-width="1.8"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                </div>
                <div>
                    <div style="font-size:1.4rem;font-weight:800;color:var(--adm-navy);">{{ number_format((float)$totals['paid'], 0) }} <span style="font-size:.75rem;font-weight:500;">MAD</span></div>
                    <div style="font-size:.75rem;color:var(--adm-text-muted);font-weight:600;">Total paid out</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter tabs --}}
    <div class="adm-tabs">
        <a href="{{ route('admin.payouts.index') }}" class="adm-tab {{ !$status ? 'adm-tab--active' : '' }}">All</a>
        <a href="{{ route('admin.payouts.index', ['status'=>'pending']) }}" class="adm-tab {{ $status==='pending' ? 'adm-tab--active' : '' }}">Pending</a>
        <a href="{{ route('admin.payouts.index', ['status'=>'paid']) }}" class="adm-tab {{ $status==='paid' ? 'adm-tab--active' : '' }}">Paid</a>
    </div>

    <div class="adm-card">
        @if($payouts->isEmpty())
            <div class="adm-empty">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                <p>No payouts found.</p>
            </div>
        @else
            <div class="adm-table-wrap">
                <table class="adm-table">
                    <thead>
                        <tr>
                            <th>Agency</th>
                            <th>Experience</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Transferred</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($payouts as $payout)
                            <tr>
                                <td>
                                    <div style="font-weight:600;font-size:.875rem;color:var(--adm-text);">{{ $payout->agency?->name }}</div>
                                </td>
                                <td style="font-size:.8rem;color:var(--adm-text-muted);">
                                    {{ Str::limit($payout->booking?->experience?->getTranslation('title', app()->getLocale(), false), 32) }}
                                    @if($payout->booking)
                                        <div style="font-size:.7rem;color:var(--ink-40);">Booking #{{ str_pad($payout->booking_id, 5, '0', STR_PAD_LEFT) }}</div>
                                    @endif
                                </td>
                                <td style="font-weight:700;color:var(--adm-navy);font-size:.9rem;white-space:nowrap;">
                                    {{ number_format((float)$payout->amount, 2) }} MAD
                                </td>
                                <td>
                                    <span class="adm-badge {{ $payout->status === 'paid' ? 'adm-badge--confirmed' : 'adm-badge--pending' }}">
                                        {{ ucfirst($payout->status) }}
                                    </span>
                                </td>
                                <td style="font-size:.8rem;color:var(--adm-text-muted);">{{ $payout->created_at->format('M j, Y') }}</td>
                                <td style="font-size:.8rem;color:var(--adm-text-muted);">
                                    {{ $payout->transferred_at ? $payout->transferred_at->format('M j, Y') : '—' }}
                                </td>
                                <td>
                                    @if($payout->status === 'pending')
                                        <div x-data="{ open: false }" style="position:relative;">
                                            <button @click="open = !open" class="adm-btn adm-btn--primary adm-btn--sm">Mark Paid</button>
                                            <div x-show="open" @click.outside="open = false"
                                                 style="position:absolute;right:0;top:36px;z-index:50;background:var(--white);border:1px solid var(--adm-border);border-radius:12px;padding:16px;min-width:220px;box-shadow:var(--shadow-md);">
                                                <form method="POST" action="{{ route('admin.payouts.mark-paid', $payout) }}">
                                                    @csrf
                                                    <div class="adm-form-row" style="margin-bottom:12px;">
                                                        <label class="adm-label" style="font-size:.75rem;">Transfer notes (optional)</label>
                                                        <input type="text" name="notes" class="adm-input" placeholder="Bank ref, date…">
                                                    </div>
                                                    <button type="submit" class="adm-btn adm-btn--success" style="width:100%;font-size:.8rem;">
                                                        ✓ Confirm transfer
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    @else
                                        <span style="font-size:.75rem;color:var(--adm-text-muted);">
                                            {{ $payout->notes ?: '—' }}
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($payouts->hasPages())
                <div style="padding:16px 20px;border-top:1px solid var(--adm-border);">
                    {{ $payouts->links() }}
                </div>
            @endif
        @endif
    </div>

@endsection
