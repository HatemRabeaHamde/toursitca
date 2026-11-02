@props(['experience'])

@if($experience->review_status === 'pending')
    <form method="POST" action="{{ route('agency.experiences.review.destroy', $experience) }}">
        @csrf @method('DELETE')
        <x-ui.button size="sm" type="submit" variant="secondary">{{ __('experience_review.withdraw') }}</x-ui.button>
    </form>
@elseif($experience->status !== 'published' || in_array($experience->latestChangeRequest?->status, ['draft', 'rejected', 'changes_requested'], true))
    <form method="POST" action="{{ route('agency.experiences.review.store', $experience) }}">
        @csrf
        <x-ui.button size="sm" type="submit">{{ __('experience_review.submit') }}</x-ui.button>
    </form>
@endif
