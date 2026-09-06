<?php

namespace App\Mail;

use App\Domain\Landing\Models\NewsletterSubscriber;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

final class NewsletterInquiryReceivedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public readonly NewsletterSubscriber $subscriber) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New trip inquiry — '.config('app.name'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.admin.newsletter-inquiry',
        );
    }
}
