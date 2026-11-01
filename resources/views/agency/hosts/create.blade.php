@extends('layouts.agency')

@section('title', __('hosts_admin.agency.add'))
@section('page-title', __('hosts_admin.agency.add'))
@section('page-subtitle', __('hosts_admin.agency.add_help'))

@section('content')
    @include('hosts.partials.form', [
        'action' => route('agency.hosts.store'),
        'destinations' => $destinations,
        'interests' => $interests,
        'languages' => $languages,
    ])
@endsection
