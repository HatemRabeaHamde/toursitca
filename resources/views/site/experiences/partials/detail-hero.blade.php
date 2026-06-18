<a class="ed-back-link" href="{{ route('site.experiences.index', ['locale' => app()->getLocale()]) }}">
    <span aria-hidden="true">←</span>
    <span>{{ __('ui.experience_detail.back_to_experiences') }}</span>
</a>

<div class="ed-title-block">
    <p class="ed-location-line">
        <span aria-hidden="true">⌖</span>
        <span>{{ $experience->location_city }}</span>
        <span>·</span>
        <span>{{ $experience->category }}</span>
    </p>
    <h1 class="ed-reference-title">{{ $detail->title }}</h1>
    <div class="ed-reference-meta">
        @foreach ($detail->badges as $badge)
            <span class="ed-pill-dark">{{ $badge }}</span>
        @endforeach
        @if ($detail->reviewSummary['has_reviews'])
            <span class="ed-rating-line">★ {{ $detail->reviewSummary['rating'] }}</span>
            <span>{{ trans_choice('ui.messages.review_count', $detail->reviewSummary['count'], ['count' => number_format($detail->reviewSummary['count'])]) }}</span>
        @else
            <span>{{ __('ui.experience_detail.no_reviews_yet') }}</span>
        @endif
        <span>{{ trans_choice('ui.units.hours', (int) ceil((float) $experience->duration_hours), ['count' => rtrim(rtrim(number_format((float) $experience->duration_hours, 1), '0'), '.')]) }}</span>
        <span>{{ __('ui.fields.max_group_size') }} {{ $experience->max_group_size }}</span>
    </div>
    <p class="ed-provider-inline">
        <span>{{ __('ui.experience_detail.agency_title') }}</span>
        {{ $detail->agencySummary['name'] }}
        @if ($detail->agencySummary['is_verified'])
            · {{ __('ui.experience_detail.verified_provider') }}
        @endif
    </p>
</div>

<div class="ed-panel-actions">
    <button class="ed-icon-action" type="button">
        <span aria-hidden="true">⌯</span>
        <span>{{ __('ui.experience_detail.share') }}</span>
    </button>
    <button class="ed-icon-action" type="button">
        <span aria-hidden="true">♡</span>
        <span>{{ __('ui.experience_detail.save') }}</span>
    </button>
</div>

<div class="ed-top-grid">
    <div class="ed-top-gallery">
        @include('site.experiences.partials.gallery', ['gallery' => $detail->gallery, 'title' => $detail->title])

        @if (count($detail->experienceOptions) > 0)
            @include('site.experiences.partials.options-list', ['detail' => $detail])
        @endif

        <div class="ed-key-specs">
            <div class="ed-key-spec">
                <span class="ed-key-spec-icon" aria-hidden="true">↺</span>
                <div>
                    <h3>{{ __('ui.experience_detail.key_specs.pay_later_title') }}</h3>
                    <p>{{ __('ui.experience_detail.key_specs.pay_later_body') }}</p>
                </div>
            </div>
            <div class="ed-key-spec">
                <span class="ed-key-spec-icon" aria-hidden="true">✓</span>
                <div>
                    <h3>{{ __('ui.experience_detail.key_specs.manual_confirmation_title') }}</h3>
                    <p>{{ __('ui.experience_detail.key_specs.manual_confirmation_body') }}</p>
                </div>
            </div>
            <div class="ed-key-spec">
                <span class="ed-key-spec-icon" aria-hidden="true">◷</span>
                <div>
                    <h3>{{ __('ui.experience_detail.key_specs.duration_title') }}</h3>
                    <p>{{ trans_choice('ui.units.hours', (int) ceil((float) $experience->duration_hours), ['count' => rtrim(rtrim(number_format((float) $experience->duration_hours, 1), '0'), '.')]) }}</p>
                </div>
            </div>
            <div class="ed-key-spec">
                <span class="ed-key-spec-icon" aria-hidden="true">⚇</span>
                <div>
                    <h3>{{ __('ui.experience_detail.key_specs.group_size_title') }}</h3>
                    <p>{{ __('ui.experience_detail.key_specs.group_size_body', ['count' => $experience->max_group_size]) }}</p>
                </div>
            </div>
            @if ($experience->supportsPrivateBooking())
                <div class="ed-key-spec">
                    <span class="ed-key-spec-icon" aria-hidden="true">★</span>
                    <div>
                        <h3>{{ __('ui.experience_detail.key_specs.private_title') }}</h3>
                        <p>{{ __('ui.experience_detail.key_specs.private_body') }}</p>
                    </div>
                </div>
            @endif
            @if ($experience->pickup_enabled)
                <div class="ed-key-spec">
                    <span class="ed-key-spec-icon" aria-hidden="true">⛽</span>
                    <div>
                        <h3>{{ __('ui.experience_detail.key_specs.pickup_title') }}</h3>
                        <p>{{ __('ui.experience_detail.key_specs.pickup_body') }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    @include('site.experiences.partials.booking-panel', ['detail' => $detail, 'experience' => $experience])
</div>
