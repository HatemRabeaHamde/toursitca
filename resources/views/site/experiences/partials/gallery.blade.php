@php
    $images = collect($gallery)->where('type', 'image')->values();
    $total  = $images->count();
    $mode   = match(true) {
        $total === 0 => 'empty',
        $total === 1 => 'single',
        $total <= 2  => 'duo',
        $total <= 4  => 'partial',
        default      => 'full',
    };
    $imagesJson = $images->map(fn ($img) => [
        'url' => $img['url'],
        'alt' => $title,
    ])->toJson(JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
@endphp

<div
    class="xp-gallery-frame"
    x-data="{
        images: {{ $imagesJson }},
        open: false,
        idx: 0,
        show(i) { this.idx = i; this.open = true; document.body.style.overflow = 'hidden'; },
        hide()  { this.open = false; document.body.style.overflow = ''; },
        prev()  { this.idx = (this.idx - 1 + this.images.length) % this.images.length; },
        next()  { this.idx = (this.idx + 1) % this.images.length; },
    }"
    @keydown.escape.window="if (open) hide()"
    @keydown.arrowleft.window="if (open) prev()"
    @keydown.arrowright.window="if (open) next()"
>

    {{-- ─── Grid ────────────────────────────────────────────── --}}
    <div class="xp-gallery xp-gallery--{{ $mode }}" role="group" aria-label="{{ __('ui.experience_detail.gallery_label') }}">

        @if ($mode === 'empty')
            <div class="xp-gallery-empty">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
            </div>

        @elseif ($mode === 'single')
            <div class="xp-gallery-cell xp-gallery-cell--span" @click="show(0)" role="button" aria-label="{{ __('ui.experience_detail.open_photo', ['n' => 1]) }}" tabindex="0" @keydown.enter="show(0)">
                <img src="{{ $images[0]['url'] }}" alt="{{ $title }}" loading="eager" decoding="async">
            </div>

        @else
            {{-- Main image --}}
            <div class="xp-gallery-cell xp-gallery-cell--main" @click="show(0)" role="button" aria-label="{{ __('ui.experience_detail.open_photo', ['n' => 1]) }}" tabindex="0" @keydown.enter="show(0)">
                <img src="{{ $images[0]['url'] }}" alt="{{ $title }}" loading="eager" decoding="async">
            </div>

            {{-- Secondary cells (up to 4) --}}
            @foreach ([1, 2, 3, 4] as $i)
                @if ($images->has($i))
                    <div class="xp-gallery-cell" @click="show({{ $i }})" role="button" aria-label="{{ __('ui.experience_detail.open_photo', ['n' => $i + 1]) }}" tabindex="0" @keydown.enter="show({{ $i }})">
                        <img src="{{ $images[$i]['url'] }}" alt="{{ $title }}" loading="lazy" decoding="async">
                    </div>
                @else
                    <div class="xp-gallery-cell xp-gallery-cell--blank"></div>
                @endif
            @endforeach
        @endif

    </div>

    {{-- ─── "View all photos" button ────────────────────────── --}}
    @if ($total > 0)
        <button type="button" class="xp-gallery-all" @click="show(0)">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
            @if ($total > 5)
                {{ __('ui.experience_detail.see_all_photos', ['count' => $total]) }}
            @else
                {{ __('ui.experience_detail.view_photos', ['count' => $total]) }}
            @endif
        </button>
    @endif

    {{-- ─── Lightbox ─────────────────────────────────────────── --}}
    <div
        class="xp-lb"
        x-show="open"
        x-cloak
        x-trap.noscroll="open"
        role="dialog"
        aria-modal="true"
        aria-label="{{ __('ui.experience_detail.gallery_label') }}"
        @click.self="hide()"
    >
        {{-- Top bar --}}
        <div class="xp-lb-bar">
            <span class="xp-lb-count" x-text="(idx + 1) + ' / ' + images.length"></span>
            <button type="button" class="xp-lb-close" @click="hide()" aria-label="{{ __('ui.actions.close') }}">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" aria-hidden="true"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>

        {{-- Main image --}}
        <div class="xp-lb-stage" @click.self="hide()">
            <button type="button" class="xp-lb-nav xp-lb-prev" @click="prev()" :disabled="images.length <= 1" aria-label="{{ __('ui.experience_detail.prev_photo') }}">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" aria-hidden="true"><polyline points="15 18 9 12 15 6"/></svg>
            </button>

            <div class="xp-lb-img-wrap">
                <img
                    class="xp-lb-img"
                    :src="images[idx]?.url"
                    :alt="images[idx]?.alt"
                    x-transition:enter="xp-lb-fade-in"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                >
            </div>

            <button type="button" class="xp-lb-nav xp-lb-next" @click="next()" :disabled="images.length <= 1" aria-label="{{ __('ui.experience_detail.next_photo') }}">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" aria-hidden="true"><polyline points="9 18 15 12 9 6"/></svg>
            </button>
        </div>

        {{-- Thumbnail strip --}}
        @if ($total > 1)
        <div class="xp-lb-thumbs" role="list">
            <template x-for="(img, i) in images" :key="i">
                <button
                    type="button"
                    class="xp-lb-thumb"
                    :class="{ 'is-active': i === idx }"
                    @click="idx = i"
                    role="listitem"
                    :aria-current="i === idx ? 'true' : 'false'"
                >
                    <img :src="img.url" :alt="img.alt" loading="lazy">
                </button>
            </template>
        </div>
        @endif

    </div>

</div>
