<section class="testi-section">
    <div class="sec-head">
        <div>
            <p class="sec-label">{{ __('ui.landing.sections.testimonials_label') }}</p>
            <h2 class="sec-h2">{{ __('ui.landing.sections.testimonials_title') }}</h2>
        </div>
    </div>

    @if ($landing->testimonials->isNotEmpty())
        <div class="testi-grid">
            @foreach ($landing->testimonials as $testimonial)
                <article class="t-card">
                    <p class="t-q">"</p>
                    <p class="t-text">{{ $testimonial['body'] }}</p>
                    <div class="t-stars">{{ str_repeat('★', (int) round($testimonial['rating'])) }}</div>
                    <div class="t-author">
                        <div class="t-av"></div>
                        <div>
                            <p class="t-name">{{ $testimonial['author_name'] }}</p>
                            <p class="t-from">{{ $testimonial['author_country'] }}</p>
                            <p class="t-exp">{{ $testimonial['experience_title'] }}</p>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    @else
        <div class="testi-empty">{{ __('ui.landing.sections.testimonials_empty') }}</div>
    @endif
</section>
