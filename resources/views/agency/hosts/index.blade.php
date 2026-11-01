@extends('layouts.agency')

@section('title', __('hosts_admin.agency.title'))
@section('page-title', __('hosts_admin.agency.title'))
@section('page-subtitle', __('hosts_admin.agency.subtitle'))

@section('content')
    <div class="mb-4 flex justify-end">
        <x-ui.button :href="route('agency.hosts.create')">{{ __('hosts_admin.agency.add') }}</x-ui.button>
    </div>

    <x-ui.card :title="__('hosts_admin.agency.title')">
        @if ($hosts->isEmpty())
            <x-ui.empty-state :title="__('hosts_admin.agency.empty')" />
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="text-left text-xs font-semibold uppercase text-slate-500">
                        <tr>
                            <th class="py-3 pr-4">{{ __('hosts_admin.form.name') }}</th>
                            <th class="py-3 pr-4">{{ __('hosts_admin.form.licence') }}</th>
                            <th class="py-3 pr-4">{{ __('hosts_admin.status.label') }}</th>
                            <th class="py-3 pr-4">{{ __('hosts_admin.agency.experiences') }}</th>
                            <th class="py-3 pr-4"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($hosts as $host)
                            <tr>
                                <td class="py-3 pr-4">
                                    <span class="font-medium text-slate-950">{{ $host->name }}</span>
                                    <span class="block text-xs text-slate-500">{{ collect([$host->getTranslation('role', app()->getLocale(), true), $host->city])->filter()->implode(' · ') }}</span>
                                </td>
                                <td class="py-3 pr-4 text-slate-600">{{ $host->licence_number ?: '—' }}</td>
                                <td class="py-3 pr-4">
                                    @if ($host->reviewed_at === null)
                                        <x-ui.badge color="amber">{{ __('hosts_admin.status.review') }}</x-ui.badge>
                                    @elseif ($host->is_active)
                                        <x-ui.badge color="green">{{ $host->is_verified ? __('hosts_admin.status.live_verified') : __('hosts_admin.status.live') }}</x-ui.badge>
                                    @else
                                        <x-ui.badge color="slate">{{ __('hosts_admin.status.hidden') }}</x-ui.badge>
                                    @endif
                                </td>
                                <td class="py-3 pr-4 text-slate-600">{{ $host->experiences_count }}</td>
                                <td class="py-3 pr-4">
                                    <div class="flex items-center justify-end gap-3">
                                        @if ($host->is_active)
                                            <a href="{{ route('site.hosts.show', ['locale' => app()->getLocale(), 'host' => $host->slug]) }}" target="_blank" rel="noopener" class="text-sm text-slate-600 hover:underline">{{ __('hosts_admin.agency.view') }}</a>
                                        @endif
                                        <a href="{{ route('agency.hosts.edit', $host) }}" class="text-sm font-medium text-slate-950 hover:underline">{{ __('hosts_admin.agency.edit') }}</a>
                                        @if ($host->experiences_count === 0)
                                            <form method="POST" action="{{ route('agency.hosts.destroy', $host) }}" onsubmit="return confirm(@js(__('hosts_admin.agency.confirm_delete')))">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-sm text-red-700 hover:underline">{{ __('hosts_admin.agency.delete') }}</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </x-ui.card>
@endsection
