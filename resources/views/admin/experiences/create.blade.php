@extends('layouts.admin')

@section('title', __('ui.nav.create_experience'))
@section('page-title', __('ui.nav.create_experience'))

@section('content')
    @include('experiences.partials.form', [
        'action' => route('admin.experiences.store'),
        'agencies' => $agencies,
        'categories' => $categories,
        'showAgencySelect' => true,
    ])
@endsection
