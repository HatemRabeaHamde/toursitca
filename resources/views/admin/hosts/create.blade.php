@extends('layouts.admin')

@section('title', __('hosts_admin.admin.add'))
@section('page-title', __('hosts_admin.admin.add'))

@section('content')
    @include('hosts.partials.form', [
        'action' => route('admin.hosts.store'),
        'admin' => true,
        'agencies' => $agencies,
        'destinations' => $destinations,
        'interests' => $interests,
        'languages' => $languages,
    ])
@endsection
