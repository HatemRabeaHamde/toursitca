@extends('layouts.agency')

@section('title', __('ui.nav.edit_experience'))
@section('page-title', __('ui.nav.edit_experience'))

@section('content')
    <x-site.experience-review-notice :experience="$experience" prefix="agency" />
    @include('experiences.partials.form', [
        'action' => route('agency.experiences.update', $experience),
        'categories' => $categories,
        'hosts' => $hosts,
        'experience' => $experience,
        'method' => 'PATCH',
        'submitLabel' => __('ui.nav.update_experience'),
    ])
@endsection
