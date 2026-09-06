@extends('emails.layout')

@section('content')
@php
    $booking = $booking;
    $experience = $booking->experience;
    $reviewUrl = url('/en/my/reviews/create?booking=' . $booking->id);
@endphp

<div class="badge" style="background:#EDE9E4;color:#6B6259;">How was it?</div>
<h1 class="title">We'd love to hear from you, {{ $booking->guest_name }}!</h1>
<p class="lead">
    Your experience <strong>{{ $experience?->getTranslation('title', 'en', false) }}</strong>
    is now complete. Your honest review helps other travelers discover great experiences in Morocco
    and helps our local partners improve.
</p>

<div style="text-align:center;margin:28px 0;">
    <a href="{{ $reviewUrl }}" class="btn">Leave a Review</a>
</div>

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
        <span class="box-label">Reference</span>
        <span class="box-value">#{{ str_pad($booking->id, 6, '0', STR_PAD_LEFT) }}</span>
    </div>
</div>

<p style="font-size:12px;color:#A89F97;line-height:1.6;margin:16px 0 0;text-align:center;">
    Reviews can only be submitted once per booking and are visible to all travelers.
</p>
@endsection
