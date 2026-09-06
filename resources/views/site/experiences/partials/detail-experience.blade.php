@php
    $description = trim((string) ($detail->description ?? ''));
    $isLong = mb_strlen($description) > 420;
    $shortDescription = $isLong ? mb_substr($description, 0, 420).'...' : $description;
@endphp

@if ($description !== '')
    <section id="overview" class="xp-detail-section">
        <h2 class="xp-section-heading">{{ __('ui.experience_detail.the_experience_title') }}</h2>

        @if ($isLong)
            <div x-data="{ expanded: false }">
                <p class="xp-detail-copy" x-show="! expanded">{{ $shortDescription }}</p>
                <p class="xp-detail-copy" x-show="expanded" x-cloak>{{ $description }}</p>
                <button
                    type="button"
                    class="xp-read-more-btn"
                    @click="expanded = ! expanded"
                    x-text="expanded ? '{{ __('ui.actions.show_less') }}' : '{{ __('ui.actions.read_more') }}'"
                ></button>
            </div>
        @else
            <p class="xp-detail-copy">{{ $description }}</p>
        @endif
    </section>
@endif
