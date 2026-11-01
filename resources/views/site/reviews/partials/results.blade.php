{{-- Two masonry columns (newest first, left → right). No data-reveal inside: re-rendered in place. --}}
@if($page->reviews)
    <div class="grid items-start gap-5 md:grid-cols-2 md:gap-6">
        @foreach([0, 1] as $column)
            <ul class="flex flex-col gap-5" data-list="col-{{ $column }}">
                @foreach($page->reviews as $index => $review)
                    @continue($index % 2 !== $column)
                    <li data-reveal-item data-card><x-site.organisms.traveller-review-card :review="$review" /></li>
                @endforeach
            </ul>
        @endforeach
    </div>
@else
    <div class="lv-xempty" data-card>
        <p class="lv-t-h4 lv-tone-ink">{{ __('reviews.empty.title') }}</p>
        <p class="lv-t-body-m lv-tone-muted">{{ __('reviews.empty.body') }}</p>
        <a href="{{ $page->indexUrl }}#reviews" class="lv-arrow-link lv-t-label-m lv-tone-terracotta" data-filter-link>
            <span>{{ __('reviews.empty.reset') }}</span>
            <x-site.atoms.icon name="arrow-right-ink" :size="16" />
        </a>
    </div>
@endif
