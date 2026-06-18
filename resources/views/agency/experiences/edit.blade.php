@extends('layouts.agency')

@section('title', __('ui.nav.edit_experience'))

@section('content')
    @include('experiences.partials.form', [
        'action' => route('agency.experiences.update', $experience),
        'experience' => $experience,
        'method' => 'PATCH',
        'submitLabel' => __('ui.nav.update_experience'),
    ])
@endsection
