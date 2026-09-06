<?php

namespace App\Mail;

use App\Domain\Agency\Models\Agency;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

final class AgencyRejectedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public readonly Agency $agency) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Update on your agency application — ' . config('app.name'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.agency.rejected',
        );
    }
}
