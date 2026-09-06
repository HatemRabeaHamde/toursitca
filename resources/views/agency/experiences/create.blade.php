@extends('layouts.agency')

@section('title', __('ui.nav.create_experience'))
@section('page-title', __('ui.nav.create_experience'))

@section('content')
    @include('experiences.partials.form', [
        'action' => route('agency.experiences.store'),
        'categories' => $categories,
    ])
@endsection
