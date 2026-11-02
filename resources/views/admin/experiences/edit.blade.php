@extends('layouts.admin')

@section('title', __('ui.nav.edit_experience'))
@section('page-title', __('ui.nav.edit_experience'))

@section('content')
    <x-site.experience-review-notice :experience="$experience" prefix="admin" />
    @include('experiences.partials.form', [
        'action' => route('admin.experiences.update', $experience),
        'agencies' => $agencies,
        'categories' => $categories,
        'hosts' => $hosts,
        'experience' => $experience,
        'method' => 'PATCH',
        'showAgencySelect' => true,
        'submitLabel' => __('ui.nav.update_experience'),
    ])
@endsection
