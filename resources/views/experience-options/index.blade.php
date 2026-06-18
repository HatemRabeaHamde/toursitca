@extends('layouts.'.$routePrefix)

@section('title', __('ui.pages.options_title'))

@section('content')
    <div class="mb-4">
        <x-ui.button :href="route($routePrefix.'.experiences.index')" variant="secondary">{{ __('ui.nav.back_to_experiences') }}</x-ui.button>
    </div>

    <x-ui.card :title="$experience->getTranslation('title', app()->getLocale(), false)">
        <p class="text-sm text-slate-600">{{ __('ui.pages.options_intro') }}</p>
    </x-ui.card>

    <div class="mt-6 grid gap-6 lg:grid-cols-2">
        <x-ui.card :title="__('ui.nav.create_option')">
            @include('experience-options.partials.form', [
                'action' => route($routePrefix.'.experiences.options.store', $experience),
                'method' => 'POST',
                'option' => null,
            ])
        </x-ui.card>

        <div class="space-y-6">
            @forelse ($experience->options as $option)
                <x-ui.card :title="$option->getTranslation('title', app()->getLocale(), false)">
                    @include('experience-options.partials.summary', ['option' => $option])
                    @include('experience-options.partials.form', [
                        'action' => route($routePrefix.'.experiences.options.update', ['experience' => $experience, 'option' => $option]),
                        'method' => 'PATCH',
                        'option' => $option,
                    ])
                    <form method="POST" action="{{ route($routePrefix.'.experiences.options.destroy', ['experience' => $experience, 'option' => $option]) }}" class="mt-4 flex justify-end">
                        @csrf
                        @method('DELETE')
                        <x-ui.button type="submit" variant="danger" size="sm">{{ __('ui.nav.delete') }}</x-ui.button>
                    </form>
                </x-ui.card>
            @empty
                <x-ui.empty-state :title="__('ui.messages.no_options')" />
            @endforelse
        </div>
    </div>
@endsection
