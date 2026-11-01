@extends('layouts.admin')

@section('title', $host->name)
@section('page-title', $host->name)
@section('page-subtitle', $host->reviewed_at === null ? __('hosts_admin.status.review') : '')

@section('content')
    @include('hosts.partials.form', [
        'action' => route('admin.hosts.update', $host),
        'method' => 'PATCH',
        'host' => $host,
        'admin' => true,
        'agencies' => $agencies,
        'destinations' => $destinations,
        'interests' => $interests,
        'languages' => $languages,
    ])
@endsection
