@extends('layouts.admin')

@section('title', __('ui.pages.agencies_title'))

@section('content')
    <div class="mb-4 flex justify-end">
        <x-ui.button :href="route('admin.agencies.create')">{{ __('ui.nav.create_platform_agency') }}</x-ui.button>
    </div>

    <x-ui.card :title="__('ui.pages.agencies_title')">
        @if ($agencies->isEmpty())
            <x-ui.empty-state :title="__('ui.messages.no_agencies')" />
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="text-left text-xs font-semibold uppercase text-slate-500">
                        <tr>
                            <th class="py-3 pr-4">{{ __('ui.fields.agency_name') }}</th>
                            <th class="py-3 pr-4">{{ __('ui.fields.owner_name') }}</th>
                            <th class="py-3 pr-4">{{ __('ui.fields.city') }}</th>
                            <th class="py-3 pr-4">Status</th>
                            <th class="py-3 pr-4">{{ __('ui.nav.experiences') }}</th>
                            <th class="py-3 pr-4"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($agencies as $agency)
                            <tr>
                                <td class="py-3 pr-4 font-medium text-slate-950">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span>{{ $agency->name }}</span>
                                        @if ($agency->is_platform)
                                            <x-ui.badge color="green">{{ __('ui.status.platform') }}</x-ui.badge>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-3 pr-4 text-slate-600">{{ $agency->user->name }}</td>
                                <td class="py-3 pr-4 text-slate-600">{{ $agency->city }}</td>
                                <td class="py-3 pr-4"><x-ui.badge color="slate">{{ __('ui.status.'.$agency->status) }}</x-ui.badge></td>
                                <td class="py-3 pr-4 text-slate-600">{{ $agency->experiences_count }}</td>
                                <td class="py-3 pr-4 text-right">
                                    @if ($agency->status === 'pending')
                                        <form method="POST" action="{{ route('admin.agencies.approve', $agency) }}">
                                            @csrf
                                            <x-ui.button type="submit" size="sm">{{ __('ui.status.active') }}</x-ui.button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <x-ui.pagination :paginator="$agencies" />
        @endif
    </x-ui.card>
@endsection
