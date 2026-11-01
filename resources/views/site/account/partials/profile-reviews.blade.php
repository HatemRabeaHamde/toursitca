<section id="reviews" class="lv-flow-card lv-account-anchor" aria-labelledby="reviews-title">
    <div>
        <h2 id="reviews-title" class="lv-t-h4 lv-serif lv-tone-ink">{{ __('account.profile.reviews') }}</h2>
        <p class="lv-t-body-s lv-tone-muted">{{ __('account.profile.reviews_help') }}</p>
    </div>

    @foreach($page['reviews'] as $review)
        <div class="lv-account-review">
            @if($review['image'])<img src="{{ $review['image'] }}" alt="" width="90" height="68" loading="lazy">@endif
            <div class="flex min-w-0 flex-col gap-1">
                <p class="flex flex-wrap items-center gap-2 lv-t-h5 lv-serif lv-tone-ink">{{ $review['title'] }}
                    @if($review['visible'])<span class="lv-pill lv-pill--confirmed lv-t-micro">{{ __('account.profile.published') }}</span>@endif
                </p>
                <p class="lv-t-caption lv-tone-muted">{{ $review['line'] }}</p>
            </div>
        </div>
    @endforeach

    @foreach($page['toReview'] as $trip)
        <div class="lv-account-prompt">
            <div>
                <p class="lv-t-label-m lv-tone-ink">{{ __('account.profile.how_was', ['title' => $trip['title']]) }}</p>
                <p class="lv-t-caption lv-tone-muted">{{ __('account.profile.how_was_help') }}</p>
            </div>
            <x-site.atoms.link-arrow :href="$trip['href']" arrow="terracotta">{{ __('account.bookings.leave_review') }}</x-site.atoms.link-arrow>
        </div>
    @endforeach

    @if(! $page['reviews'] && ! $page['toReview'])
        <p class="lv-t-body-s lv-tone-muted">{{ __('account.profile.no_reviews') }}</p>
    @endif
</section>
