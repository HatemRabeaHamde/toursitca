@if($page->reviews)
    @if($page->nextPageUrl())
        <a href="{{ $page->nextPageUrl() }}" class="lv-button lv-button--outline lv-t-label-m" data-load-more>{{ __('reviews.more.button') }}</a>
    @endif
    <p class="lv-t-caption lv-tone-muted">{{ __('reviews.more.showing', ['shown' => $page->shown(), 'total' => $page->total()]) }}</p>
@endif
