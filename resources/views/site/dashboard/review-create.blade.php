@extends('layouts.tourist')

@section('content')
    @php($locale = app()->getLocale())

    <a href="{{ route('site.dashboard.bookings', ['locale' => $locale]) }}" class="td-back-link">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
        {{ __('ui.pages.my_trips_title') }}
    </a>

    <p class="td-eyebrow">{{ __('ui.dashboard.write_review') }}</p>
    <h1 class="td-h1">{{ $booking->experience->getTranslation('title', $locale, false) }}</h1>
    <p class="td-page-intro">{{ __('ui.dashboard.write_review_intro') }}</p>

    <form
        method="POST"
        action="{{ route('site.reviews.store', ['locale' => $locale, 'booking' => $booking->id]) }}"
        class="td-panel"
        style="margin-top:28px; max-width:640px;"
        x-data="{ rating: {{ (int) old('rating', 0) }} }"
    >
        @csrf

        <div class="td-field" style="margin-top:0;">
            <label class="td-label">{{ __('ui.fields.rating') }}</label>
            <div class="td-star-row" role="radiogroup" aria-label="{{ __('ui.fields.rating') }}">
                <template x-for="star in [1,2,3,4,5]" :key="star">
                    <button type="button" class="td-star-btn" @click="rating = star" :aria-pressed="rating >= star">
                        <svg width="30" height="30" viewBox="0 0 24 24" :fill="rating >= star ? 'var(--gold)' : 'none'" stroke="var(--gold)" stroke-width="1.5"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                    </button>
                </template>
            </div>
            <input type="hidden" name="rating" x-model="rating">
            @error('rating')
                <p class="td-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="td-field">
            <label for="body" class="td-label">{{ __('ui.fields.review_body') }}</label>
            <textarea
                id="body"
                name="body"
                class="td-textarea"
                required
                minlength="10"
                maxlength="2000"
                placeholder="{{ __('ui.dashboard.review_body_placeholder') }}"
            >{{ old('body') }}</textarea>
            @error('body')
                <p class="td-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="td-form-actions">
            <a href="{{ route('site.dashboard.bookings', ['locale' => $locale]) }}" class="td-link-muted">{{ __('ui.actions.close') }}</a>
            <button type="submit" class="td-btn">{{ __('ui.dashboard.submit_review') }}</button>
        </div>
    </form>
@endsection
