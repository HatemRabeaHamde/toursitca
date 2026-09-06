@extends('emails.layout')

@section('content')
@php $agency = $agency; @endphp

<div class="badge badge--success">Application Approved</div>
<h1 class="title">Welcome to {{ config('app.name') }}, {{ $agency->name }}!</h1>
<p class="lead">
    Congratulations! Your agency has been reviewed and approved. You can now log in,
    create your experiences, and start receiving bookings from travelers worldwide.
</p>

<div style="text-align:center;margin:28px 0;">
    <a href="{{ url('/agency/dashboard') }}" class="btn">Go to Your Dashboard</a>
</div>

<div class="box">
    <div style="font-size:12px;font-weight:600;letter-spacing:.04em;text-transform:uppercase;color:#A89F97;margin-bottom:12px;">Getting started</div>
    <div style="font-size:13.5px;color:#3D3530;line-height:1.75;">
        ✓ &nbsp;Complete your agency profile<br>
        ✓ &nbsp;Create your first experience listing<br>
        ✓ &nbsp;Add availability slots<br>
        ✓ &nbsp;Start accepting bookings
    </div>
</div>

<hr class="divider">
<p style="font-size:13px;color:#8B7B72;line-height:1.6;margin:0;">
    Need help getting started? Contact us at
    <a href="mailto:{{ config('mail.from.address') }}" style="color:#8B5E48;">{{ config('mail.from.address') }}</a>.
</p>
@endsection
