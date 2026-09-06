<?php

namespace App\Mail;

use App\Domain\Booking\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

final class BookingConfirmedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public readonly Booking $booking) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your booking is confirmed — ' . $this->booking->experience?->getTranslation('title', 'en', false),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.booking.confirmed',
        );
    }
}
