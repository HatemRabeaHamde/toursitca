@extends('layouts.agency')

@section('title', __('ui.nav.create_experience'))

@section('content')
    @include('experiences.partials.form', [
        'action' => route('agency.experiences.store'),
    ])
@endsection
