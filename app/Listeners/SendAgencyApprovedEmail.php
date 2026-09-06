<?php

namespace App\Listeners;

use App\Domain\Agency\Events\AgencyApproved;
use App\Mail\AgencyApprovedMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

final class SendAgencyApprovedEmail implements ShouldQueue
{
    public function handle(AgencyApproved $event): void
    {
        $agency = $event->agency->load('user');

        Mail::to($agency->user->email, $agency->user->name)
            ->send(new AgencyApprovedMail($agency));
    }
}
