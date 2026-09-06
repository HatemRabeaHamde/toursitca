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
                @php $filledStars = (int) round((float) $testimonial['rating']); @endphp
                <article class="t-card">
                    <p class="t-q">"</p>
                    <p class="t-text">{{ $testimonial['body'] }}</p>
                    <div class="t-stars">{{ str_repeat('★', min($filledStars, 5)) }}</div>
                    <div class="t-author">
                        <div class="t-av">
                            @if (!empty($testimonial['avatar_url']))
                                <img src="{{ $testimonial['avatar_url'] }}" alt="{{ $testimonial['author_name'] }}" loading="lazy" style="width:100%;height:100%;object-fit:cover;border-radius:50%;">
                            @else
                                <span style="font-size:.85rem;font-weight:700;line-height:1;">{{ mb_strtoupper(mb_substr($testimonial['author_name'], 0, 1)) }}</span>
                            @endif
                        </div>
                        <div>
                            <p class="t-name">{{ $testimonial['author_name'] }}</p>
                            @if (!empty($testimonial['author_country']))
                                <p class="t-from">{{ $testimonial['author_country'] }}</p>
                            @endif
                            @if (!empty($testimonial['experience_title']))
                                <p class="t-exp">{{ $testimonial['experience_title'] }}</p>
                            @endif
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    @else
        <div class="testi-empty">{{ __('ui.landing.sections.testimonials_empty') }}</div>
    @endif
</section>
