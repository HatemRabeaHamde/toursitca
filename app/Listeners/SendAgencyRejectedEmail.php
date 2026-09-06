<?php

namespace App\Listeners;

use App\Domain\Agency\Events\AgencyRejected;
use App\Mail\AgencyRejectedMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

final class SendAgencyRejectedEmail implements ShouldQueue
{
    public function handle(AgencyRejected $event): void
    {
        $agency = $event->agency->load('user');

        Mail::to($agency->user->email, $agency->user->name)
            ->send(new AgencyRejectedMail($agency));
    }
}
