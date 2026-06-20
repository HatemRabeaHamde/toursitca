<section class="deals-section" id="deals">
    <div class="deals-head-row">
        <div>
            <p class="sec-label">{{ __('ui.landing.sections.deals_label') }}</p>
            <h2 class="sec-h2">{{ __('ui.landing.sections.deals_title') }}</h2>
        </div>
    </div>

    @if ($landing->dealCards->isEmpty())
        <div class="deals-empty">{{ __('ui.landing.sections.deals_empty') }}</div>
    @else
        <div
            class="carousel-shell"
            x-data="homeCarousel({ autoplay: @js($landing->dealCards->count() > 4) })"
            x-on:mouseenter="pause()"
            x-on:mouseleave="resume()"
            x-on:focusin="pause()"
            x-on:focusout="resume()"
            x-on:touchstart.passive="pause()"
        >
            <div class="deals-grid carousel-track" x-ref="track" aria-label="{{ __('ui.landing.carousel.deals') }}">
                @foreach ($landing->dealCards as $card)
                    @php
                        $dealPct = null;

                        if ($card->originalPrice !== null) {
                            $dealPrice = (float) str_replace(',', '', $card->priceFrom);
                            $dealOriginal = (float) str_replace(',', '', $card->originalPrice);

                            if ($dealOriginal > 0) {
                                $dealPct = max(0, (int) round((1 - ($dealPrice / $dealOriginal)) * 100));
                            }
                        }
                    @endphp

                    <a class="deal-card" href="{{ $card->showUrl }}">
                        <div class="deal-img">
                            <img src="{{ $card->thumbnailUrl ?: 'https://images.unsplash.com/photo-1539650116574-75c0c6d73f6e?w=700&q=80' }}" alt="{{ $card->title }}" loading="lazy">
                            @if ($dealPct)
                                <span class="deal-pct">-{{ $dealPct }}%</span>
                            @endif
                        </div>
                        <div class="deal-body">
                            <p class="deal-city">{{ $card->city }}</p>
                            <h3 class="deal-title">{{ $card->title }}</h3>
                            <div class="deal-prices">
                                <span class="deal-new">{{ $card->priceFrom }}</span>
                                @if ($card->originalPrice)
                                    <span class="deal-old">{{ $card->originalPrice }}</span>
                                @endif
                                <span class="deal-cur">{{ $card->currency }}</span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            @if ($landing->dealCards->count() > 4)
                <div class="carousel-actions">
                    <button class="carousel-btn" type="button" x-on:click="previous()" aria-label="{{ __('ui.landing.carousel.previous_offers') }}">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
                    </button>
                    <button class="carousel-btn" type="button" x-on:click="next()" aria-label="{{ __('ui.landing.carousel.next_offers') }}">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                    </button>
                </div>
            @endif
        </div>
    @endif
</section>
