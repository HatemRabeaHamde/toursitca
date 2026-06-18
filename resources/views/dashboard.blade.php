<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 space-y-4">
                    <h3 class="text-lg font-semibold">{{ __('ui.pages.tourist_title') }}</h3>
                    <div class="flex flex-wrap gap-3">
                        <a class="btn-primary" href="{{ route('site.experiences.index', ['locale' => app()->getLocale()]) }}">{{ __('ui.nav.experiences') }}</a>
                        <a class="btn-secondary" href="{{ route('agency.register') }}">{{ __('ui.nav.agency_register') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
