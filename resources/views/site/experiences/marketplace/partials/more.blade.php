{{-- "Show more experiences" (appends the next page in place; a normal link without JS). --}}
@if($page->cards)
    @if($page->nextPageUrl())
        <a href="{{ $page->nextPageUrl() }}" class="lv-button lv-button--outline lv-t-label-m" data-load-more>{{ __('experiences.more.button') }}</a>
    @endif
    <p class="lv-t-caption lv-tone-muted">{{ __('experiences.more.showing', ['shown' => $page->shown(), 'total' => $page->total()]) }}</p>
@endif
