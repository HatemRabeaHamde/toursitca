@props([
    'availabilities',
    'experiences',
    'routePrefix',
])

@if ($availabilities->isEmpty())
    <x-ui.empty-state :title="__('ui.messages.no_availability')" />
@else
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="text-left text-xs font-semibold uppercase text-slate-500">
                <tr>
                    <th class="py-3 pr-4">{{ __('ui.fields.experience') }}</th>
                    <th class="py-3 pr-4">{{ __('ui.fields.experience_option') }}</th>
                    <th class="py-3 pr-4">{{ __('ui.fields.date') }}</th>
                    <th class="py-3 pr-4">{{ __('ui.fields.time_slot') }}</th>
                    <th class="py-3 pr-4">{{ __('ui.fields.max_seats') }}</th>
                    <th class="py-3 pr-4">{{ __('ui.fields.booked_seats') }}</th>
                    <th class="py-3 pr-4">Status</th>
                    <th class="py-3 pr-4"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach ($availabilities as $availability)
                    <tr>
                        <td class="py-3 pr-4 font-medium text-slate-950">{{ $availability->experience->getTranslation('title', app()->getLocale(), false) }}</td>
                        <td class="py-3 pr-4 text-slate-600">
                            {{ $availability->option?->getTranslation('title', app()->getLocale(), false) ?? __('ui.fields.general_availability') }}
                        </td>
                        <td class="py-3 pr-4 text-slate-600">{{ $availability->date->toDateString() }}</td>
                        <td class="py-3 pr-4 text-slate-600">{{ substr($availability->time_slot, 0, 5) }}</td>
                        <td class="py-3 pr-4 text-slate-600">{{ $availability->max_seats }}</td>
                        <td class="py-3 pr-4 text-slate-600">{{ $availability->booked_seats }}</td>
                        <td class="py-3 pr-4">
                            <x-ui.badge color="{{ $availability->is_active ? 'green' : 'slate' }}">
                                {{ $availability->is_active ? __('ui.fields.active') : __('ui.status.unpublished') }}
                            </x-ui.badge>
                        </td>
                        <td class="py-3 pr-4">
                            <details class="min-w-80">
                                <summary class="cursor-pointer text-sm font-medium text-brand-700">{{ __('ui.nav.update_availability') }}</summary>
                                <div class="mt-4 rounded-md border border-slate-200 bg-slate-50 p-4">
                                    @include('availability.partials.form', [
                                        'action' => route($routePrefix.'.availability.update', $availability),
                                        'experiences' => $experiences,
                                        'availability' => $availability,
                                        'method' => 'PATCH',
                                    ])
                                    <form method="POST" action="{{ route($routePrefix.'.availability.destroy', $availability) }}" class="mt-3 flex justify-end">
                                        @csrf
                                        @method('DELETE')
                                        <x-ui.button type="submit" size="sm" variant="danger">{{ __('ui.nav.delete') }}</x-ui.button>
                                    </form>
                                </div>
                            </details>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <x-ui.pagination :paginator="$availabilities" />
@endif
