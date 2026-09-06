@if ($landing->travelReels->isNotEmpty())
    <section class="reels-section">
        <div class="reels-header">
            <div>
                <p class="sec-label">{{ __('ui.landing.sections.reels_label') }}</p>
                <h2 class="sec-h2">{{ __('ui.landing.sections.reels_title') }}</h2>
            </div>
        </div>

        <div class="reels-row">
            @foreach ($landing->travelReels as $reel)
                <a class="reel" href="{{ $reel['video_url'] ?: '#' }}">
                    <img src="{{ $reel['thumbnail_url'] ?: 'https://images.unsplash.com/photo-1539650116574-75c0c6d73f6e?w=400&q=80' }}" alt="{{ $reel['title'] }}" loading="lazy">
                    <div class="reel-veil"></div>
                    <div class="reel-play"><svg width="14" height="14" viewBox="0 0 24 24" fill="white"><polygon points="5 3 19 12 5 21 5 3"/></svg></div>
                    <div class="reel-footer">
                        <p class="reel-city">{{ $reel['city'] ?: __('ui.landing.morocco') }}</p>
                        <p class="reel-title">{{ $reel['title'] }}</p>
                    </div>
                </a>
            @endforeach
        </div>
    </section>
@endif
