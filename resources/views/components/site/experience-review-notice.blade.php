@props(['experience', 'prefix' => 'agency'])

<div class="rounded-xl border border-slate-200 bg-white p-4 mb-5 space-y-3">
    <p class="font-semibold">{{ __('experience_review.state') }}: {{ __('experience_review.states.'.$experience->review_status) }}</p>
    <p class="text-sm text-slate-600">{{ __('experience_review.draft_help') }}</p>
    @if($experience->latestChangeRequest?->reason)<p class="text-sm font-medium">{{ $experience->latestChangeRequest->reason }}</p>@endif
    <a class="underline text-sm" href="{{ route($prefix.'.experiences.draft-preview', $experience) }}" target="_blank" rel="noopener">{{ __('experience_review.preview') }}</a>
    @error('review')<p role="alert" class="text-red-700">{{ $message }}</p>@enderror
    @if($prefix === 'agency')
        <x-site.experience-review-actions :experience="$experience" />
    @endif
</div>
