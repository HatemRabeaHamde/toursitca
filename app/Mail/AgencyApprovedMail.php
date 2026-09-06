<?php

namespace App\Mail;

use App\Domain\Agency\Models\Agency;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

final class AgencyApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public readonly Agency $agency) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your agency has been approved — ' . config('app.name'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.agency.approved',
        );
    }
}
