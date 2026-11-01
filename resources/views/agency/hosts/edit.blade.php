@extends('layouts.agency')

@section('title', $host->name)
@section('page-title', $host->name)
@section('page-subtitle', __('hosts_admin.agency.edit_help'))

@section('content')
    @include('hosts.partials.form', [
        'action' => route('agency.hosts.update', $host),
        'method' => 'PATCH',
        'host' => $host,
        'destinations' => $destinations,
        'interests' => $interests,
        'languages' => $languages,
    ])
@endsection
