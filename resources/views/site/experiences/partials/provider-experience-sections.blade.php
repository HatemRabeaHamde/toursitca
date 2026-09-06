@php
    $agency = $detail->agencySummary;
    $initials = mb_strtoupper(mb_substr($agency['name'], 0, 2));
@endphp

<section id="provider" class="xp-provider-block">
    <div class="xp-recs-inner">
        <div class="xp-provider-panel">
            <div class="xp-provider-card-large">
                <div class="xp-agency-avatar xp-agency-avatar--large" aria-hidden="true">{{ $initials }}</div>
                <div class="xp-agency-info">
                    <p class="xp-section-kicker">{{ __('ui.experience_detail.provider_section.kicker') }}</p>
                    <h2>{{ $agency['name'] }}</h2>
                    @if (! empty($agency['city']))
                        <p>{{ $agency['city'] }}</p>
                    @endif
                    @if ($agency['is_verified'])
                        <span class="xp-verified-badge">✓ {{ __('ui.experience_detail.verified_provider') }}</span>
                    @endif
                </div>
            </div>

            @if ($detail->providerExperiences->isNotEmpty())
                <div class="xp-provider-rail">
                    <div class="xp-recs-header">
                        <div>
                            <p class="xp-recs-kicker">{{ __('ui.experience_detail.provider_section.same_provider_kicker') }}</p>
                            <h2 class="xp-recs-title">{{ __('ui.experience_detail.provider_section.same_provider_title') }}</h2>
                        </div>
                    </div>

                    <div class="xp-recs-grid">
                        @foreach ($detail->providerExperiences as $card)
                            @include('site.experiences.partials.card-grid', ['card' => $card])
                        @endforeach
                    </div>
                </div>
            @endif

            @if ($detail->categoryExperiences->isNotEmpty())
                <div class="xp-provider-rail">
                    <div class="xp-recs-header">
                        <div>
                            <p class="xp-recs-kicker">{{ __('ui.experience_detail.provider_section.other_provider_kicker') }}</p>
                            <h2 class="xp-recs-title">{{ __('ui.experience_detail.provider_section.other_provider_title') }}</h2>
                        </div>
                    </div>

                    <div class="xp-recs-grid">
                        @foreach ($detail->categoryExperiences as $card)
                            @include('site.experiences.partials.card-grid', ['card' => $card])
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</section>
