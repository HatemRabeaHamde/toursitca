@extends('layouts.admin')
@section('page-title', __('experience_review.queue'))
@section('content')
    <div class="adm-card p-5 space-y-4">
        <nav class="flex flex-wrap gap-3"><a class="adm-btn adm-btn--ghost" href="{{ route('admin.experience-reviews.index') }}">{{ __('experience_review.pending') }}</a><a class="adm-btn adm-btn--ghost" href="{{ route('admin.experience-reviews.index', ['status' => 'all']) }}">{{ __('experience_review.history') }}</a></nav>
        <p>{{ __('experience_review.queue_help') }}</p>
        @forelse($page->reviews as $review)
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 py-4">
                <div>
                    <p class="font-semibold">{{ data_get($review->payload, 'attributes.title.en') }}</p>
                    <p class="text-sm text-slate-600">{{ __('experience_review.states.'.$review->status) }} · {{ $review->experience->agency->name }} · {{ $review->submitted_at?->format('Y-m-d H:i') }}</p>
                </div>
                <a class="adm-btn adm-btn--primary" href="{{ route('admin.experience-reviews.show', $review) }}">{{ __('experience_review.inspect') }}</a>
            </div>
        @empty
            <p>{{ __('experience_review.empty') }}</p>
        @endforelse
        {{ $page->reviews->links() }}
    </div>
@endsection
