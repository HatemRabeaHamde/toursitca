@php($items = collect($gallery)->where('type', 'image')->take(5)->values())

<div class="ed-gallery-grid">
    @forelse ($items as $index => $media)
        <div @class(['ed-gallery-tile', 'is-main' => $index === 0])>
            <img src="{{ $media['url'] }}" alt="{{ $title }}" loading="{{ $index === 0 ? 'eager' : 'lazy' }}">
            @if ($index === 0)
                <span class="ed-play-mark" aria-hidden="true">▶</span>
            @endif
        </div>
    @empty
        <div class="ed-gallery-empty">
            <div>
                <p class="ed-kicker">{{ __('ui.experience_detail.gallery_label') }}</p>
                <p>{{ __('ui.messages.no_media') }}</p>
            </div>
        </div>
    @endforelse
</div>
