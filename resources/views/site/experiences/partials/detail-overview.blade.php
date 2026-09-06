@php
    $desc        = $detail->description ?? '';
    $isLong      = mb_strlen($desc) > 300;
    $shortDesc   = $isLong ? mb_substr($desc, 0, 300) . '…' : $desc;

    $trustIcons = [
        '01' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/><polyline points="9 16 11 18 15 14"/></svg>',
        '02' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>',
        '03' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="3" width="15" height="13" rx="2"/><path d="M16 8h4l3 6v2h-7z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>',
        '04' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>',
        'pay_later' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>',
    ];
@endphp

{{-- ── Description ──────────────────────────────────────────── --}}
<section id="overview" class="xp-card">
    <h2 class="xp-card-title">{{ __('ui.experience_detail.the_experience_title') }}</h2>

    @if ($isLong)
        <div x-data="{ expanded: false }">
            <p class="xp-desc-short" x-show="!expanded">{{ $shortDesc }}</p>
            <p class="xp-desc-full" x-show="expanded" x-cloak>{{ $desc }}</p>
            <button
                type="button"
                class="xp-read-more-btn"
                @click="expanded = !expanded"
                x-text="expanded ? '{{ __('ui.actions.show_less') }}' : '{{ __('ui.actions.read_more') }}'"
            ></button>
        </div>
    @else
        <p class="xp-card-body">{{ $desc }}</p>
    @endif
</section>

{{-- ── Key highlights / trust items — GetYourGuide style ──── --}}
@if (count($detail->trustItems) > 0)
    <section id="highlights" class="xp-card">
        <h2 class="xp-card-title">{{ __('ui.experience_detail.what_you_will_do_title') }}</h2>
        <div class="xp-trust-grid">
            @foreach ($detail->trustItems as $item)
                <div class="xp-trust-item">
                    <div class="xp-trust-icon" aria-hidden="true">
                        {!! $trustIcons[$item['icon'] ?? '01'] ?? $trustIcons['01'] !!}
                    </div>
                    <div class="xp-trust-text">
                        <h3>{{ $item['title'] }}</h3>
                        <p>{{ $item['body'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
@endif

{{-- ── Inclusions & exclusions ──────────────────────────────── --}}
@if ($experience->inclusions || $experience->exclusions)
    <section id="included" class="xp-card">
        <h2 class="xp-card-title">{{ __('ui.experience_detail.included_title') }}</h2>
        <div class="xp-inclusions-wrap">
            @include('site.experiences.partials.inclusions', ['experience' => $experience])
        </div>
    </section>
@endif
