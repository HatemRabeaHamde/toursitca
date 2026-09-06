<?php

namespace App\Http\Controllers\Site;

use App\Domain\Landing\Models\NewsletterSubscriber;
use App\Http\Controllers\Controller;
use App\Http\Requests\Site\NewsletterSubscribeRequest;
use App\Mail\NewsletterInquiryReceivedMail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;

class NewsletterController extends Controller
{
    public function store(NewsletterSubscribeRequest $request): RedirectResponse|JsonResponse
    {
        $message = $request->string('message')->trim()->toString() ?: null;

        $subscriber = NewsletterSubscriber::query()->updateOrCreate(
            ['email' => $request->string('email')->lower()->toString()],
            [
                'message' => $message,
                'locale' => app()->getLocale(),
                'source' => $request->string('source')->toString() ?: 'landing',
                'subscribed_at' => now(),
            ],
        );

        if ($message) {
            Mail::to(config('services.admin.email'))->send(new NewsletterInquiryReceivedMail($subscriber));
        }

        if ($request->wantsJson()) {
            return response()->json(['message' => __('ui.messages.newsletter_subscribed')]);
        }

        return back()->with('status', __('ui.messages.newsletter_subscribed'));
    }
}
