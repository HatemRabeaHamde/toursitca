@extends('layouts.tourist')

@section('content')
    @php($locale = app()->getLocale())

    <div class="td-page-head">
        <div>
            <p class="td-eyebrow">{{ __('ui.pages.tourist_title') }}</p>
            <h1 class="td-h1">{{ __('ui.pages.my_reviews_title') }}</h1>
            <p class="td-page-intro">{{ __('ui.dashboard.my_reviews_intro') }}</p>
        </div>
    </div>

    @if ($reviews->isEmpty())
        <div class="td-empty">
            <span class="td-empty-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
            </span>
            <h2>{{ __('ui.messages.no_reviews') }}</h2>
            <p>{{ __('ui.dashboard.no_reviews_intro') }}</p>
            <a href="{{ route('site.dashboard.bookings', ['locale' => $locale]) }}" class="td-cta">{{ __('ui.pages.my_trips_title') }}</a>
        </div>
    @else
        <div class="td-list">
            @foreach ($reviews as $review)
                <article class="td-review-card">
                    <div class="td-review-head">
                        <span class="td-card-title">{{ $review->experience->getTranslation('title', $locale, false) }}</span>
                        <div class="td-stars" aria-label="{{ $review->rating }} / 5">
                            @for ($i = 1; $i <= 5; $i++)
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="{{ $i <= $review->rating ? 'var(--gold)' : 'none' }}" stroke="var(--gold)" stroke-width="1.5"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                            @endfor
                        </div>
                    </div>

                    <p class="td-review-body">{{ $review->body }}</p>
                    <p class="td-review-date">{{ $review->created_at->translatedFormat('j F Y') }}</p>

                    @if ($review->agency_reply)
                        <div class="td-review-reply">
                            <p class="td-review-reply-label">{{ __('ui.experience_detail.agency_reply') }}</p>
                            <p class="td-review-reply-body">{{ $review->agency_reply }}</p>
                        </div>
                    @endif
                </article>
            @endforeach
        </div>

        @if ($reviews->hasPages())
            <div class="mt-8">
                {{ $reviews->links() }}
            </div>
        @endif
    @endif
@endsection
