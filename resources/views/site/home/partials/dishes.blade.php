@php
    $dishImages = [
        [
            'thumb' => 'https://images.unsplash.com/photo-1585937421612-70a008356fbe?w=200&q=75',
            'visual' => 'https://images.unsplash.com/photo-1585937421612-70a008356fbe?w=800&q=85',
        ],
        [
            'thumb' => 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=200&q=75',
            'visual' => 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=800&q=85',
        ],
        [
            'thumb' => 'https://images.unsplash.com/photo-1547592166-23ac45744acd?w=200&q=75',
            'visual' => 'https://images.unsplash.com/photo-1547592166-23ac45744acd?w=800&q=85',
        ],
        [
            'thumb' => 'https://images.unsplash.com/photo-1556679343-c7306c1976bc?w=200&q=75',
            'visual' => 'https://images.unsplash.com/photo-1556679343-c7306c1976bc?w=800&q=85',
        ],
    ];

    $dishVisualImages = collect($dishImages)->pluck('visual')->all();
@endphp

<section class="dishes-section" x-data="{ activeDish: 0, images: @js($dishVisualImages) }">
    <div class="dishes-grid">
        <div>
            <p class="dishes-eyebrow">{{ __('ui.landing.sections.dishes_label') }}</p>
            <h2 class="dishes-h">{{ __('ui.landing.sections.dishes_title') }}</h2>
            <p class="dishes-sub">{{ __('ui.landing.sections.dishes_body') }}</p>
            <div class="dish-list">
                @foreach (__('ui.landing.dishes') as $index => $dish)
                    <div
                        class="dish-item"
                        tabindex="0"
                        @mouseenter="activeDish = {{ $index }}"
                        @focusin="activeDish = {{ $index }}"
                        :class="{ 'is-active': activeDish === {{ $index }} }"
                    >
                        <div class="dish-img">
                            <img src="{{ $dishImages[$index]['thumb'] ?? $dishImages[0]['thumb'] }}" alt="{{ $dish['name'] }}" loading="lazy">
                        </div>
                        <div>
                            <p class="dish-name">{{ $dish['name'] }}</p>
                            <p class="dish-desc">{{ $dish['description'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="dishes-visual">
            <div class="dishes-photo">
                <img
                    src="{{ $dishVisualImages[0] }}"
                    :src="images[activeDish]"
                    alt="{{ __('ui.landing.sections.dishes_title') }}"
                    loading="lazy"
                >
            </div>
            <div class="dishes-badge">
                <span class="db-num">{{ count(__('ui.landing.dishes')) }}+</span>
                <span class="db-lbl">{{ __('ui.landing.sections.dishes_label') }}</span>
            </div>
        </div>
    </div>
</section>
