@extends('emails.layout')

@section('content')
<div class="badge badge--success">New Trip Inquiry</div>
<h1 class="title">Someone wants help planning a trip</h1>
<p class="lead">
    A visitor submitted the "Get matched" form on the experiences page. Details below.
</p>

<div class="box">
    <div class="box-row">
        <span class="box-label">Email</span>
        <span class="box-value">{{ $subscriber->email }}</span>
    </div>
    <div class="box-row">
        <span class="box-label">Locale</span>
        <span class="box-value">{{ strtoupper($subscriber->locale ?? '—') }}</span>
    </div>
    <div class="box-row">
        <span class="box-label">Submitted</span>
        <span class="box-value">{{ $subscriber->subscribed_at->format('M j, Y H:i') }}</span>
    </div>
</div>

@if ($subscriber->message)
    <div class="box">
        <div style="font-size:12px;font-weight:600;letter-spacing:.04em;text-transform:uppercase;color:#A89F97;margin-bottom:12px;">Message</div>
        <div style="font-size:13.5px;color:#3D3530;line-height:1.75;">{{ $subscriber->message }}</div>
    </div>
@endif

<div style="text-align:center;margin:28px 0;">
    <a href="{{ route('admin.newsletter.index') }}" class="btn">View in Admin Dashboard</a>
</div>

<hr class="divider">
<p style="font-size:13px;color:#8B7B72;line-height:1.6;margin:0;">
    Reply directly to <a href="mailto:{{ $subscriber->email }}" style="color:#8B5E48;">{{ $subscriber->email }}</a> to follow up.
</p>
@endsection
