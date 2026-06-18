@extends('layouts.agency')

@section('title', __('ui.nav.availability'))

@section('content')
    <div class="space-y-6">
        <x-ui.card :title="__('ui.nav.availability')">
            @include('availability.partials.form', [
                'action' => route('agency.availability.store'),
                'experiences' => $experiences,
            ])
        </x-ui.card>

        <x-ui.card :title="__('ui.nav.availability')">
            @include('availability.partials.table', [
                'availabilities' => $availabilities,
                'experiences' => $experiences,
                'routePrefix' => 'agency',
            ])
        </x-ui.card>
    </div>
@endsection
