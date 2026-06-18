@extends('layouts.admin')

@section('title', __('ui.nav.edit_experience'))

@section('content')
    @include('experiences.partials.form', [
        'action' => route('admin.experiences.update', $experience),
        'agencies' => $agencies,
        'experience' => $experience,
        'method' => 'PATCH',
        'showAgencySelect' => true,
        'submitLabel' => __('ui.nav.update_experience'),
    ])
@endsection
