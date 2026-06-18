@extends('layouts.admin')

@section('title', __('ui.pages.experiences_title'))

@section('content')
    <div class="mb-4 flex justify-end">
        <x-ui.button :href="route('admin.experiences.create')">{{ __('ui.nav.create_experience') }}</x-ui.button>
    </div>

    <x-ui.card :title="__('ui.pages.experiences_title')">
        @if ($experiences->isEmpty())
            <x-ui.empty-state :title="__('ui.messages.no_experiences')" />
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="text-left text-xs font-semibold uppercase text-slate-500">
                        <tr>
                            <th class="py-3 pr-4">{{ __('ui.pages.experiences_title') }}</th>
                            <th class="py-3 pr-4">{{ __('ui.fields.agency_name') }}</th>
                            <th class="py-3 pr-4">{{ __('ui.fields.city') }}</th>
                            <th class="py-3 pr-4">Status</th>
                            <th class="py-3 pr-4">Group</th>
                            <th class="py-3 pr-4">Private</th>
                            <th class="py-3 pr-4"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($experiences as $experience)
                            <tr>
                                <td class="py-3 pr-4 font-medium text-slate-950">{{ $experience->getTranslation('title', app()->getLocale(), false) }}</td>
                                <td class="py-3 pr-4 text-slate-600">{{ $experience->agency->name }}</td>
                                <td class="py-3 pr-4 text-slate-600">{{ $experience->location_city }}</td>
                                <td class="py-3 pr-4"><x-ui.badge color="slate">{{ __('ui.status.'.$experience->status) }}</x-ui.badge></td>
                                <td class="py-3 pr-4 text-slate-600">{{ number_format((float) $experience->price_per_person, 2) }} MAD</td>
                                <td class="py-3 pr-4 text-slate-600">{{ $experience->private_price ? number_format((float) $experience->private_price, 2).' MAD' : '-' }}</td>
                                <td class="py-3 pr-4">
                                    <div class="flex flex-wrap justify-end gap-2">
                                        <x-ui.button size="sm" variant="secondary" :href="route('admin.experiences.edit', $experience)">{{ __('ui.nav.edit_experience') }}</x-ui.button>
                                        <x-ui.button size="sm" variant="secondary" :href="route('admin.experiences.options.index', $experience)">{{ __('ui.nav.options') }}</x-ui.button>
                                        @if ($experience->status === 'published')
                                            <form method="POST" action="{{ route('admin.experiences.unpublish', $experience) }}">
                                                @csrf
                                                <x-ui.button size="sm" variant="ghost" type="submit">{{ __('ui.nav.unpublish') }}</x-ui.button>
                                            </form>
                                        @else
                                            <form method="POST" action="{{ route('admin.experiences.publish', $experience) }}">
                                                @csrf
                                                <x-ui.button size="sm" type="submit">{{ __('ui.nav.publish') }}</x-ui.button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <x-ui.pagination :paginator="$experiences" />
        @endif
    </x-ui.card>
@endsection
