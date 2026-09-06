@extends('emails.layout')

@section('content')
@php $agency = $agency; @endphp

<div class="badge badge--error">Application Update</div>
<h1 class="title">Update on your application</h1>
<p class="lead">
    Thank you for applying to join {{ config('app.name') }} as a travel agency.
    After reviewing your application, we are unfortunately unable to approve
    <strong>{{ $agency->name }}</strong> at this time.
</p>

<div class="box">
    <div style="font-size:13.5px;color:#3D3530;line-height:1.7;">
        We carefully review each application to ensure the quality and trust
        our travelers expect. If you believe this decision was made in error,
        or if you have additional information to share, please reach out to us directly.
    </div>
</div>

<hr class="divider">
<p style="font-size:13px;color:#8B7B72;line-height:1.6;margin:0;">
    Questions? Contact us at
    <a href="mailto:{{ config('mail.from.address') }}" style="color:#8B5E48;">{{ config('mail.from.address') }}</a>
    and reference your agency name <strong>{{ $agency->name }}</strong>.
</p>
@endsection
