@php
    $durationLabel = trans_choice(
        'ui.units.hours',
        (int) ceil((float) $experience->duration_hours),
        ['count' => rtrim(rtrim(number_format((float) $experience->duration_hours, 1), '0'), '.')]
    );
    $agencyInitials = mb_strtoupper(mb_substr($detail->agencySummary['name'], 0, 1));
@endphp

<header class="xp-hero">

    {{-- Breadcrumb --}}
    <nav class="xp-breadcrumb" aria-label="Breadcrumb">
        <a href="{{ route('site.home', ['locale' => app()->getLocale()]) }}">{{ __('ui.nav.home') }}</a>
        <span class="xp-breadcrumb-sep" aria-hidden="true">›</span>
        <a href="{{ route('site.experiences.index', ['locale' => app()->getLocale()]) }}">{{ __('ui.experience_detail.back_to_experiences') }}</a>
        <span class="xp-breadcrumb-sep" aria-hidden="true">›</span>
        <span>{{ $experience->location_city }}</span>
    </nav>

    <div class="xp-hero-top">

        <div class="xp-hero-left">

            {{-- Location + category pill --}}
            <p class="xp-location-pill" aria-hidden="true">
                <span>⌖</span>
                <span>{{ $experience->location_city }}</span>
                @if ($experience->category)
                    <span>·</span>
                    <span>{{ $experience->category }}</span>
                @endif
            </p>

            {{-- Title --}}
            <h1 class="xp-title">{{ $detail->title }}</h1>

            {{-- Meta row: rating, duration, group size, badges --}}
            <div class="xp-meta-row">
                @if ($detail->reviewSummary['has_reviews'])
                    <span class="xp-rating-stars" aria-label="{{ $detail->reviewSummary['rating'] }} {{ __('ui.experience_detail.out_of_5') }}">
                        ★ {{ $detail->reviewSummary['rating'] }}
                    </span>
                    <span class="xp-rating-count">
                        ({{ trans_choice('ui.messages.review_count', $detail->reviewSummary['count'], ['count' => number_format($detail->reviewSummary['count'])]) }})
                    </span>
                    <span class="xp-meta-row-sep" aria-hidden="true">·</span>
                @endif

                <span class="xp-meta-item">
                    <span class="xp-meta-icon" aria-hidden="true">◷</span>
                    {{ $durationLabel }}
                </span>

                <span class="xp-meta-row-sep" aria-hidden="true">·</span>

                <span class="xp-meta-item">
                    <span class="xp-meta-icon" aria-hidden="true">⚇</span>
                    {{ __('ui.fields.max_group_size') }}: {{ $experience->max_group_size }}
                </span>

                @foreach ($detail->badges as $badge)
                    <span class="xp-meta-row-sep" aria-hidden="true">·</span>
                    <span class="xp-badge">{{ $badge }}</span>
                @endforeach
            </div>

            {{-- Agency line --}}
            <p class="xp-agency-line">
                {{ __('ui.experience_detail.agency_title') }}:
                <strong>{{ $detail->agencySummary['name'] }}</strong>
                @if ($detail->agencySummary['is_verified'])
                    · ✓ {{ __('ui.experience_detail.verified_provider') }}
                @endif
            </p>

        </div>

        {{-- Share / Save actions --}}
        <div class="xp-hero-actions">
            <button type="button" class="xp-action-btn" aria-label="{{ __('ui.experience_detail.share') }}">
                <span aria-hidden="true">⌯</span>
                <span>{{ __('ui.experience_detail.share') }}</span>
            </button>
            <button
                type="button"
                class="xp-action-btn"
                aria-label="{{ __('ui.experience_detail.save') }}"
                x-data="wishlistBtn({
                    isSaved: {{ in_array($experience->id, $wishlisted ?? []) ? 'true' : 'false' }},
                    toggleUrl: '{{ route('site.wishlist.toggle', ['locale' => app()->getLocale(), 'experience' => $experience->id]) }}'
                })"
                @click="toggle()"
                :aria-pressed="isSaved"
            >
                <span aria-hidden="true" x-text="isSaved ? '♥' : '♡'" :class="isSaved ? 'xp-action-heart-saved' : ''"></span>
                <span x-text="isSaved ? '{{ __('ui.experience_detail.saved') }}' : '{{ __('ui.experience_detail.save') }}'"></span>
            </button>
        </div>

    </div>

</header>
