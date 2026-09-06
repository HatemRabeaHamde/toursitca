@extends('layouts.agency')

@section('title', __('ui.nav.payouts'))

@section('page-title', __('ui.nav.payouts'))

@section('content')

    {{-- ── Summary cards ──────────────────────────────────────── --}}
    <div class="ag-grid-2" style="margin-bottom:20px;">

        <div class="ag-stat-card">
            <div class="ag-stat-label">{{ __('ui.agency_dashboard.pending_payout') }}</div>
            <div class="ag-stat-value">{{ number_format((float) $totals['pending'], 2) }} MAD</div>
        </div>

        <div class="ag-stat-card">
            <div class="ag-stat-label">{{ __('ui.agency_dashboard.total_paid') }}</div>
            <div class="ag-stat-value">{{ number_format((float) $totals['paid'], 2) }} MAD</div>
        </div>

    </div>

    {{-- ── Filter ──────────────────────────────────────────────── --}}
    <div class="mb-4 flex flex-wrap items-center gap-3">
        <form method="GET" action="{{ route('agency.payouts.index') }}" class="flex flex-wrap items-end gap-3">
            <label class="block">
                <span class="label">{{ __('ui.fields.status') }}</span>
                <select name="status" class="input min-w-40">
                    <option value="">{{ __('ui.fields.all_statuses') }}</option>
                    <option value="pending" @selected($status === 'pending')>Pending</option>
                    <option value="paid" @selected($status === 'paid')>Paid</option>
                </select>
            </label>
            <x-ui.button type="submit" variant="secondary">{{ __('ui.nav.filter') }}</x-ui.button>
        </form>
    </div>

    {{-- ── Table ───────────────────────────────────────────────── --}}
    <x-ui.card :title="__('ui.nav.payouts')">
        @if ($payouts->isEmpty())
            <x-ui.empty-state :title="__('ui.messages.no_payouts')" />
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="text-left text-xs font-semibold uppercase text-slate-500">
                        <tr>
                            <th class="py-3 pr-4">#</th>
                            <th class="py-3 pr-4">{{ __('ui.fields.experience') }}</th>
                            <th class="py-3 pr-4">{{ __('ui.fields.date') }}</th>
                            <th class="py-3 pr-4">{{ __('ui.fields.amount') }}</th>
                            <th class="py-3 pr-4">{{ __('ui.fields.status') }}</th>
                            <th class="py-3 pr-4">{{ __('ui.fields.transferred_at') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($payouts as $payout)
                            @php
                                $expTitle = $payout->booking?->experience?->getTranslation('title', app()->getLocale(), false) ?? '—';
                            @endphp
                            <tr>
                                <td class="py-3 pr-4 font-mono text-slate-500 text-xs">#{{ $payout->id }}</td>
                                <td class="py-3 pr-4 font-medium text-slate-950">{{ $expTitle }}</td>
                                <td class="py-3 pr-4 text-slate-600">
                                    {{ $payout->created_at->format('d M Y') }}
                                </td>
                                <td class="py-3 pr-4 font-semibold text-slate-950">
                                    {{ number_format((float) $payout->amount, 2) }} MAD
                                </td>
                                <td class="py-3 pr-4">
                                    <x-ui.badge :color="$payout->status === 'paid' ? 'green' : 'amber'">
                                        {{ ucfirst($payout->status) }}
                                    </x-ui.badge>
                                </td>
                                <td class="py-3 pr-4 text-slate-600">
                                    {{ $payout->transferred_at?->format('d M Y') ?? '—' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <x-ui.pagination :paginator="$payouts" />
        @endif
    </x-ui.card>

@endsection
