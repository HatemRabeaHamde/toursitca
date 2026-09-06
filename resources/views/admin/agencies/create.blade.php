@extends('layouts.admin')

@section('title', __('ui.nav.create_platform_agency'))

@section('content')
    <x-ui.card :title="__('ui.nav.create_platform_agency')">
        <form method="POST" action="{{ route('admin.agencies.store') }}" class="space-y-4">
            @csrf

            <div class="grid gap-4 md:grid-cols-2">
                <x-ui.input name="agency_name" :label="__('ui.fields.agency_name')" required />
                <x-ui.input name="city" :label="__('ui.fields.city')" required />
                <x-ui.input name="phone" :label="__('ui.fields.phone')" />
            </div>

            <x-ui.textarea name="description" :label="__('ui.fields.description')" />

            <div class="flex justify-end">
                <x-ui.button type="submit">{{ __('ui.nav.create_platform_agency') }}</x-ui.button>
            </div>
        </form>
    </x-ui.card>
@endsection
