@extends('layouts.admin')

@section('page-title', 'Edit: ' . $agency->name)
@section('page-subtitle', 'Update commission rate and contact details')

@section('topbar-actions')
    <a href="{{ route('admin.agencies.show', $agency) }}" class="adm-btn adm-btn--ghost">
        ← Back to Agency
    </a>
@endsection

@section('content')

    <div class="adm-grid-2-1">

        <div class="adm-card">
            <div class="adm-card-header">
                <span class="adm-card-title">Agency Settings</span>
            </div>
            <div class="adm-card-body">

                @if($errors->any())
                    <div class="adm-flash adm-flash--error" style="margin-bottom:20px;">
                        <ul style="margin:0;padding-left:16px;">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.agencies.update', $agency) }}">
                    @csrf
                    @method('PATCH')

                    <div class="adm-form-row">
                        <label class="adm-label" for="commission_rate">
                            Commission Rate (%)
                            <span style="font-weight:400;color:var(--adm-text-muted);font-size:.75rem;"> — percentage kept by the platform</span>
                        </label>
                        <input
                            type="number"
                            id="commission_rate"
                            name="commission_rate"
                            class="adm-input"
                            value="{{ old('commission_rate', $agency->commission_rate) }}"
                            min="0" max="100" step="0.5"
                            required
                        >
                    </div>

                    <div class="adm-form-row">
                        <label class="adm-label" for="city">City</label>
                        <input
                            type="text"
                            id="city"
                            name="city"
                            class="adm-input"
                            value="{{ old('city', $agency->city) }}"
                            required
                        >
                    </div>

                    <div class="adm-form-row">
                        <label class="adm-label" for="phone">Phone <span style="color:var(--adm-text-muted);font-weight:400;">(optional)</span></label>
                        <input
                            type="text"
                            id="phone"
                            name="phone"
                            class="adm-input"
                            value="{{ old('phone', $agency->phone) }}"
                            placeholder="+212 6 00 00 00 00"
                        >
                    </div>

                    <div style="margin-top:24px;display:flex;gap:12px;">
                        <button type="submit" class="adm-btn adm-btn--primary">
                            Save Changes
                        </button>
                        <a href="{{ route('admin.agencies.show', $agency) }}" class="adm-btn adm-btn--ghost">
                            Cancel
                        </a>
                    </div>

                </form>
            </div>
        </div>

        {{-- Info panel --}}
        <div class="adm-card">
            <div class="adm-card-header">
                <span class="adm-card-title">Agency Info</span>
            </div>
            <div class="adm-card-body" style="display:flex;flex-direction:column;gap:16px;">
                <div>
                    <div style="font-size:.7rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--adm-text-muted);margin-bottom:4px;">Name</div>
                    <div style="font-size:.875rem;color:var(--adm-text);font-weight:600;">{{ $agency->name }}</div>
                </div>
                <div>
                    <div style="font-size:.7rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--adm-text-muted);margin-bottom:4px;">Owner</div>
                    <div style="font-size:.875rem;color:var(--adm-text);">{{ $agency->user->name }}</div>
                    <div style="font-size:.75rem;color:var(--adm-text-muted);">{{ $agency->user->email }}</div>
                </div>
                <div>
                    <div style="font-size:.7rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--adm-text-muted);margin-bottom:4px;">Status</div>
                    @php
                        $sc = match($agency->status) {
                            'active'    => 'adm-badge--active',
                            'pending'   => 'adm-badge--pending',
                            'suspended' => 'adm-badge--cancelled',
                            default     => 'adm-badge--pending',
                        };
                    @endphp
                    <span class="adm-badge {{ $sc }}">{{ __('ui.status.'.$agency->status) }}</span>
                </div>
                <div>
                    <div style="font-size:.7rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--adm-text-muted);margin-bottom:4px;">Joined</div>
                    <div style="font-size:.875rem;color:var(--adm-text);">{{ $agency->created_at->format('M j, Y') }}</div>
                </div>
            </div>
        </div>

    </div>

@endsection
