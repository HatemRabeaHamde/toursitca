@extends('emails.layout')

@section('content')
@php
    $booking = $booking;
    $experience = $booking->experience;
    $availability = $booking->availability;
@endphp

<div class="badge badge--warning">Booking Cancelled</div>
<h1 class="title">Your booking has been cancelled</h1>
<p class="lead">
    We're sorry to inform you that your booking has been cancelled.
    @if($booking->cancellation_reason)
        Reason: <em>{{ $booking->cancellation_reason }}</em>
    @endif
</p>

<div class="box">
    <div class="box-row">
        <span class="box-label">Experience</span>
        <span class="box-value">{{ $experience?->getTranslation('title', 'en', false) }}</span>
    </div>
    <div class="box-row">
        <span class="box-label">Date</span>
        <span class="box-value">{{ $availability?->date?->format('l, F j, Y') }}</span>
    </div>
    <div class="box-row">
        <span class="box-label">Amount</span>
        <span class="box-value">{{ number_format((float)$booking->total_price, 0) }} MAD</span>
    </div>
    <div class="box-row">
        <span class="box-label">Reference</span>
        <span class="box-value">#{{ str_pad($booking->id, 6, '0', STR_PAD_LEFT) }}</span>
    </div>
</div>

<p style="font-size:13px;color:#8B7B72;line-height:1.6;margin:0;">
    If you have any questions, please contact us at
    <a href="mailto:{{ config('mail.from.address') }}" style="color:#8B5E48;">{{ config('mail.from.address') }}</a>.
</p>
@endsection
