@extends('layouts.admin')

@section('page-title', __('ui.nav.availability'))
@section('page-subtitle', 'Manage date slots across all experiences')

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

    <div class="adm-grid-3-2">

        {{-- Add slot form --}}
        <div>
            <div class="adm-card">
                <div class="adm-card-header"><span class="adm-card-title">Add Availability Slot</span></div>
                <div class="adm-card-body">
                    <form method="POST" action="{{ route('admin.availability.store') }}"
                          style="display:flex;flex-direction:column;gap:14px;">
                        @csrf

                        <div class="adm-form-row" style="margin:0;">
                            <label class="adm-label">Experience</label>
                            <select name="experience_id" class="adm-select" required>
                                <option value="">Select experience…</option>
                                @foreach($experiences as $exp)
                                    <option value="{{ $exp->id }}" @selected(old('experience_id')==$exp->id)>
                                        {{ $exp->getTranslation('title', app()->getLocale(), false) }} · {{ $exp->agency->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="adm-form-row" style="margin:0;">
                            <label class="adm-label">Option <span style="font-weight:400;color:var(--adm-text-muted);">(optional)</span></label>
                            <select name="experience_option_id" class="adm-select">
                                <option value="">General / all options</option>
                                @foreach($experiences as $exp)
                                    @foreach($exp->options as $opt)
                                        <option value="{{ $opt->id }}" @selected(old('experience_option_id')==$opt->id)>
                                            {{ $exp->getTranslation('title', app()->getLocale(), false) }} → {{ $opt->getTranslation('title', app()->getLocale(), false) }}
                                        </option>
                                    @endforeach
                                @endforeach
                            </select>
                        </div>

                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                            <div class="adm-form-row" style="margin:0;">
                                <label class="adm-label">Date</label>
                                <input type="date" name="date" class="adm-input" value="{{ old('date') }}" required min="{{ date('Y-m-d') }}">
                            </div>
                            <div class="adm-form-row" style="margin:0;">
                                <label class="adm-label">Time</label>
                                <input type="time" name="time_slot" class="adm-input" value="{{ old('time_slot','09:00') }}" required>
                            </div>
                        </div>

                        <div class="adm-form-row" style="margin:0;">
                            <label class="adm-label">Max Seats</label>
                            <input type="number" name="max_seats" class="adm-input" min="1" max="500" value="{{ old('max_seats',12) }}" required>
                        </div>

                        @if($errors->any())
                            <div class="adm-flash adm-flash--error" style="margin:0;">
                                @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
                            </div>
                        @endif

                        <button type="submit" class="adm-btn adm-btn--primary">Add Slot</button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Slots table --}}
        <div class="adm-card">
            <div class="adm-card-header">
                <span class="adm-card-title">All Slots</span>
                <span style="font-size:.8rem;color:var(--adm-text-muted);">{{ $availabilities->total() }} total</span>
            </div>
            @if($availabilities->isEmpty())
                <div class="adm-empty">
                    <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    <p>No slots yet.</p>
                </div>
            @else
                <div class="adm-table-wrap">
                    <table class="adm-table">
                        <thead>
                            <tr>
                                <th>Experience</th>
                                <th>Option</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Capacity</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($availabilities as $slot)
                                @php
                                    $pct   = $slot->max_seats > 0 ? round(($slot->booked_seats / $slot->max_seats) * 100) : 0;
                                    $fillColor = $pct >= 100 ? 'var(--deal)' : ($pct >= 75 ? 'var(--amber)' : 'var(--green)');
                                @endphp
                                <tr>
                                    <td>
                                        <div style="font-size:.8rem;font-weight:500;color:var(--adm-text);">
                                            {{ Str::limit($slot->experience->getTranslation('title', app()->getLocale(), false), 28) }}
                                        </div>
                                        <div style="font-size:.7rem;color:var(--adm-text-muted);">{{ $slot->experience->agency->name }}</div>
                                    </td>
                                    <td style="font-size:.75rem;color:var(--adm-text-muted);">
                                        {{ $slot->option?->getTranslation('title', app()->getLocale(), false) ?? 'General' }}
                                    </td>
                                    <td style="font-size:.8rem;font-weight:600;color:var(--adm-text);white-space:nowrap;">
                                        {{ $slot->date->format('M j, Y') }}
                                    </td>
                                    <td style="font-size:.8rem;color:var(--adm-text-muted);">{{ substr($slot->time_slot, 0, 5) }}</td>
                                    <td>
                                        <div style="display:flex;align-items:center;gap:8px;">
                                            <div style="width:48px;height:5px;background:var(--ink-15);border-radius:3px;flex-shrink:0;">
                                                <div style="width:{{ $pct }}%;height:100%;background:{{ $fillColor }};border-radius:3px;"></div>
                                            </div>
                                            <span style="font-size:.75rem;color:var(--adm-text);">{{ $slot->booked_seats }}/{{ $slot->max_seats }}</span>
                                        </div>
                                    </td>
                                    <td style="min-width:220px;">
                                        <details>
                                            <summary class="adm-btn adm-btn--ghost adm-btn--sm" style="display:inline-flex;cursor:pointer;">
                                                Manage
                                            </summary>
                                            <div style="margin-top:12px;padding:14px;border:1px solid var(--adm-border);border-radius:12px;background:var(--adm-bg-soft);min-width:520px;">
                                                @include('availability.partials.form', [
                                                    'action' => route('admin.availability.update', $slot),
                                                    'experiences' => $experiences,
                                                    'availability' => $slot,
                                                    'method' => 'PATCH',
                                                ])

                                                <form method="POST" action="{{ route('admin.availability.destroy', $slot) }}"
                                                      style="margin-top:12px;display:flex;justify-content:flex-end;"
                                                      onsubmit="return confirm('Delete this slot? This cannot be undone.')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="adm-btn adm-btn--danger adm-btn--sm">Delete</button>
                                                </form>
                                            </div>
                                        </details>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($availabilities->hasPages())
                    <div style="padding:16px 20px;border-top:1px solid var(--adm-border);">
                        {{ $availabilities->links() }}
                    </div>
                @endif
            @endif
        </div>

    </div>

@endsection
