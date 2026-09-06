@php
    $processImages = [
        'https://images.unsplash.com/photo-1524492412937-b28074a5d7da?w=800&q=85',
        'https://images.unsplash.com/photo-1539650116574-75c0c6d73f6e?w=800&q=85',
        'https://images.unsplash.com/photo-1548013146-72479768bada?w=800&q=85',
    ];
@endphp

<section class="how-section" x-data="{ activeStep: 0, images: @js($processImages) }">
    <div class="how-grid">
        <div>
            <p class="sec-label">{{ __('ui.landing.sections.process_label') }}</p>
            <h2 class="sec-h2">{{ __('ui.landing.sections.process_title_line_1') }}<br>{{ __('ui.landing.sections.process_title_line_2') }}</h2>
            <div class="how-steps">
                @foreach (__('ui.landing.process_steps') as $index => $step)
                    <div
                        class="step"
                        tabindex="0"
                        @mouseenter="activeStep = {{ $index }}"
                        @focusin="activeStep = {{ $index }}"
                        :class="{ 'is-active': activeStep === {{ $index }} }"
                    >
                        <span class="step-n">{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</span>
                        <div>
                            <h3 class="step-h">{{ $step['title'] }}</h3>
                            <p class="step-p">{{ $step['body'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="how-right">
            <div class="how-photo">
                <img
                    src="{{ $processImages[0] }}"
                    :src="images[activeStep]"
                    alt="{{ __('ui.landing.hero_image_alt') }}"
                    loading="lazy"
                >
            </div>
            <div class="how-float1">
                <span class="hf1-num">{{ $landing->stats['avg_rating'] }}</span>
                <p class="hf1-lbl">{{ __('ui.landing.stats.avg_rating') }}<br>{{ number_format($landing->stats['happy_travelers']) }} {{ __('ui.landing.stats.travelers') }}</p>
            </div>
            <div class="how-float2">
                <div class="hf2-av">
                    <img src="https://images.unsplash.com/photo-1531746020798-e6953c6e8e04?w=100&q=80" alt="{{ __('ui.landing.process_status_avatar_alt') }}" loading="lazy">
                </div>
                <div>
                    <p class="hf2-name">{{ __('ui.landing.process_status_title') }}</p>
                    <p class="hf2-info">{{ __('ui.landing.process_status_body') }}</p>
                </div>
            </div>
        </div>
    </div>
</section>
