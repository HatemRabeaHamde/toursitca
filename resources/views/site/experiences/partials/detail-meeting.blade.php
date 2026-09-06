@if ($experience->meeting_point || $experience->pickup_enabled)
    <section id="meeting" class="xp-detail-section">
        <h2 class="xp-section-heading">{{ __('ui.fields.meeting_point') }}</h2>

        <div class="xp-meeting-box">
            <span class="xp-meeting-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M12 21s7-5.2 7-12a7 7 0 0 0-14 0c0 6.8 7 12 7 12z"/><circle cx="12" cy="9" r="2.5"/></svg>
            </span>
            <div>
                @if ($experience->meeting_point)
                    <p>{{ $experience->meeting_point }}</p>
                @elseif ($experience->pickup_enabled)
                    <p>{{ __('ui.experience_detail.key_specs.pickup_body') }}</p>
                @endif
                @if ($experience->pickup_enabled)
                    <strong>{{ __('ui.experience_detail.key_specs.pickup_value') }}</strong>
                @endif
            </div>
        </div>
    </section>
@endif
