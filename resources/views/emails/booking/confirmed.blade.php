@extends('emails.layout')

@section('content')
@php
    $booking = $booking;
    $experience = $booking->experience;
    $availability = $booking->availability;
    $isPrivate = $booking->booking_type === 'private';
@endphp

<div class="badge badge--success">Booking Confirmed</div>
<h1 class="title">You're all set, {{ $booking->guest_name }}!</h1>
<p class="lead">
    Your booking has been confirmed. We look forward to welcoming you.
    Please find your booking details below.
</p>

<div class="box">
    <div class="box-row">
        <span class="box-label">Experience</span>
        <span class="box-value">{{ $experience?->getTranslation('title', 'en', false) }}</span>
    </div>
    <div class="box-row">
        <span class="box-label">Agency</span>
        <span class="box-value">{{ $experience?->agency?->name }}</span>
    </div>
    <div class="box-row">
        <span class="box-label">Date</span>
        <span class="box-value">{{ $availability?->date?->format('l, F j, Y') }}</span>
    </div>
    <div class="box-row">
        <span class="box-label">Time</span>
        <span class="box-value">{{ $availability ? substr($availability->time_slot, 0, 5) : '—' }}</span>
    </div>
    @if(!$isPrivate)
    <div class="box-row">
        <span class="box-label">Participants</span>
        <span class="box-value">{{ $booking->participants_count }}</span>
    </div>
    @else
    <div class="box-row">
        <span class="box-label">Booking type</span>
        <span class="box-value">Private</span>
    </div>
    @endif
    <div class="box-row">
        <span class="box-label">Total paid</span>
        <span class="box-value">{{ number_format((float)$booking->total_price, 0) }} MAD</span>
    </div>
    <div class="box-row">
        <span class="box-label">Reference</span>
        <span class="box-value">#{{ str_pad($booking->id, 6, '0', STR_PAD_LEFT) }}</span>
    </div>
</div>

@if($experience?->meeting_point)
<div class="box" style="margin-top:-8px;">
    <div style="font-size:12px;font-weight:600;letter-spacing:.04em;text-transform:uppercase;color:#A89F97;margin-bottom:8px;">Meeting point</div>
    <div style="font-size:13.5px;color:#3D3530;line-height:1.6;">{{ $experience->meeting_point }}</div>
</div>
@endif

@if($booking->notes)
<div class="box" style="margin-top:-8px;">
    <div style="font-size:12px;font-weight:600;letter-spacing:.04em;text-transform:uppercase;color:#A89F97;margin-bottom:8px;">Your notes</div>
    <div style="font-size:13.5px;color:#3D3530;line-height:1.6;">{{ $booking->notes }}</div>
</div>
@endif

<hr class="divider">
<p style="font-size:13px;color:#8B7B72;line-height:1.6;margin:0;">
    Need to make changes or have questions? Contact us at
    <a href="mailto:{{ config('mail.from.address') }}" style="color:#8B5E48;">{{ config('mail.from.address') }}</a>
    and include your reference number <strong>#{{ str_pad($booking->id, 6, '0', STR_PAD_LEFT) }}</strong>.
</p>
@endsection
