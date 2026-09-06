@php
    $agency = $detail->agencySummary;
    $initials = mb_strtoupper(mb_substr($agency['name'], 0, 2));
@endphp

<section id="provider" class="xp-detail-section">
    <h2 class="xp-section-heading">{{ __('ui.experience_detail.agency_title') }}</h2>

    <div class="xp-agency-card">
        <div class="xp-agency-avatar" aria-hidden="true">{{ $initials }}</div>
        <div class="xp-agency-info">
            <h3>{{ $agency['name'] }}</h3>
            @if (! empty($agency['city']))
                <p>{{ $agency['city'] }}</p>
            @endif
            @if ($agency['is_verified'])
                <span class="xp-verified-badge">✓ {{ __('ui.experience_detail.verified_provider') }}</span>
            @endif
        </div>
    </div>
</section>
