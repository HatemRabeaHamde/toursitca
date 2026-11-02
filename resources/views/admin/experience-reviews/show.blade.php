@extends('layouts.admin')
@section('page-title', __('experience_review.inspect'))
@section('content')
    <div class="space-y-5">
        <div class="adm-card p-5 space-y-2">
            <h1 class="text-xl font-semibold">{{ data_get($page->review->payload, 'attributes.title.en') }}</h1>
            <p>{{ $page->review->experience->agency->name }} · {{ __('experience_review.states.'.$page->review->status) }}</p>
            <p>{{ __('experience_review.preview_help') }}</p>
            @if($page->review->status === 'pending')<a class="adm-btn adm-btn--primary" href="{{ route('admin.experiences.draft-preview', $page->review->experience) }}" target="_blank" rel="noopener">{{ __('experience_review.preview') }}</a>@endif
            <a class="adm-btn adm-btn--ghost" href="{{ route('admin.experiences.edit', $page->review->experience) }}">{{ __('ui.nav.edit_experience') }}</a>
        </div>
        @foreach($page->sections as $section)
            <details class="adm-card p-5" open>
                <summary class="font-semibold cursor-pointer">{{ $section['label'] }} · {{ $section['changed'] ? __('experience_review.changed') : __('experience_review.unchanged') }}</summary>
                <div class="overflow-x-auto mt-4">
                    <table class="w-full text-sm text-left">
                        <thead><tr><th class="p-2">{{ __('ui.fields.title') }}</th><th class="p-2">{{ __('experience_review.before') }}</th><th class="p-2">{{ __('experience_review.after') }}</th></tr></thead>
                        <tbody>
                            @foreach($section['rows'] as $row)
                                <tr class="border-t border-slate-200 {{ $row['changed'] ? 'bg-amber-50' : '' }}">
                                    <th class="p-2 align-top font-medium">{{ $row['label'] }}</th>
                                    <td class="p-2 align-top whitespace-pre-wrap break-words max-w-sm">{{ $row['before'] }}</td>
                                    <td class="p-2 align-top whitespace-pre-wrap break-words max-w-sm">{{ $row['after'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </details>
        @endforeach
        @if($page->review->status === 'pending')
            <form method="POST" action="{{ route('admin.experience-reviews.update', $page->review) }}" class="adm-card p-5 space-y-4">
                @csrf @method('PATCH')
                <x-ui.textarea name="reason" :label="__('experience_review.reason')" :value="old('reason')" />
                <div class="flex flex-wrap gap-3">
                    @foreach(['approved', 'changes_requested', 'rejected'] as $decision)
                        <button name="decision" value="{{ $decision }}" class="adm-btn {{ $decision === 'approved' ? 'adm-btn--primary' : 'adm-btn--ghost' }}">{{ __('experience_review.decisions.'.$decision) }}</button>
                    @endforeach
                </div>
            </form>
        @else
            <p>{{ $page->review->reason }}</p>
        @endif
    </div>
@endsection
