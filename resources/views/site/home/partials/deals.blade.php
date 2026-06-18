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
        <div class="deals-grid">
            @foreach ($landing->dealCards->take(3) as $card)
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
    @endif
</section>
