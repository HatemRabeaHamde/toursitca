<section id="important" class="ed-reference-card">
    <h2>{{ __('ui.experience_detail.important_info_title') }}</h2>
    <div class="ed-step-list compact">
        @foreach (__('ui.experience_detail.important_items') as $item)
            <div class="ed-step">
                <span>{{ $loop->iteration }}</span>
                <div>
                    <h3>{{ $item['title'] }}</h3>
                    <p>{{ $item['body'] }}</p>
                </div>
            </div>
        @endforeach
    </div>
</section>

<section id="reviews" class="ed-reference-card">
    <h2>{{ __('ui.experience_detail.reviews_preview_title') }}</h2>
    @if ($detail->reviewSummary['has_reviews'])
        <div class="ed-review-box">
            <div class="ed-review-score">{{ $detail->reviewSummary['rating'] }}</div>
            <div>
                <p class="ed-step-title">{{ trans_choice('ui.messages.review_count', $detail->reviewSummary['count'], ['count' => number_format($detail->reviewSummary['count'])]) }}</p>
                <p>{{ __('ui.experience_detail.verified_provider') }}</p>
            </div>
        </div>
    @else
        <p>{{ __('ui.experience_detail.no_reviews_yet') }}</p>
    @endif
</section>
