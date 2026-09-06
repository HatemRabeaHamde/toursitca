<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Landing\Models\NewsletterSubscriber;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NewsletterController extends Controller
{
    public function index(Request $request): View
    {
        $filter = $request->string('filter')->toString();

        $subscribers = NewsletterSubscriber::query()
            ->when($filter === 'inquiries', fn ($q) => $q->whereNotNull('message'))
            ->when($filter === 'unread', fn ($q) => $q->whereNull('read_at'))
            ->latest('subscribed_at')
            ->paginate(25)
            ->withQueryString();

        return view('admin.newsletter.index', compact('subscribers', 'filter'));
    }

    public function markRead(NewsletterSubscriber $subscriber): RedirectResponse
    {
        if (! $subscriber->read_at) {
            $subscriber->forceFill(['read_at' => now()])->save();
        }

        return redirect()->back();
    }
}
