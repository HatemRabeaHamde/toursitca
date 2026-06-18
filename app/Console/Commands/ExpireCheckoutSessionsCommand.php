<?php

namespace App\Console\Commands;

use App\Domain\Checkout\Actions\ExpireCheckoutSessionsAction;
use Illuminate\Console\Command;

class ExpireCheckoutSessionsCommand extends Command
{
    protected $signature = 'checkout:expire';

    protected $description = 'Expire timed-out checkout sessions and release held seats.';

    public function handle(ExpireCheckoutSessionsAction $action): int
    {
        $expiredCount = $action->execute();

        $this->info("Expired {$expiredCount} checkout session(s).");

        return self::SUCCESS;
    }
}
