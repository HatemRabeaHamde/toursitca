<?php

namespace App\Http\Controllers\Site;

use App\Domain\Landing\Models\NewsletterSubscriber;
use App\Http\Controllers\Controller;
use App\Http\Requests\Site\NewsletterSubscribeRequest;
use Illuminate\Http\RedirectResponse;

class NewsletterController extends Controller
{
    public function store(NewsletterSubscribeRequest $request): RedirectResponse
    {
        NewsletterSubscriber::query()->updateOrCreate(
            ['email' => $request->string('email')->lower()->toString()],
            [
                'locale' => app()->getLocale(),
                'source' => 'landing',
                'subscribed_at' => now(),
            ],
        );

        return back()->with('status', __('ui.messages.newsletter_subscribed'));
    }
}
