<?php

namespace App\Http\Controllers\Site;

use App\Domain\Booking\Models\Booking;
use App\Domain\Review\Actions\SubmitReviewAction;
use App\Domain\Review\DTOs\ReviewData;
use App\Domain\Review\Models\Review;
use App\Http\Controllers\Controller;
use App\Http\Requests\Site\ReviewStoreRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Request $request): View
    {
        $reviews = Review::query()
            ->with('experience')
            ->where('user_id', $request->user()->id)
            ->latest()
            ->paginate(10);

        return view('site.dashboard.reviews', compact('reviews'));
    }

    public function create(Request $request, string $locale, Booking $booking): View
    {
        abort_unless($booking->user_id === $request->user()->id, 403);
        abort_unless($booking->status === 'completed', 404);
        abort_if($booking->review()->exists(), 404);

        $booking->load('experience');

        return view('site.dashboard.review-create', compact('booking'));
    }

    public function store(ReviewStoreRequest $request, string $locale, Booking $booking, SubmitReviewAction $submitReviewAction): RedirectResponse
    {
        abort_unless($booking->user_id === $request->user()->id, 403);
        abort_unless($booking->status === 'completed', 404);
        abort_if($booking->review()->exists(), 404);

        $submitReviewAction->execute($booking, new ReviewData(
            bookingId: $booking->id,
            rating: (int) $request->validated('rating'),
            body: (string) $request->validated('body'),
        ));

        return redirect()
            ->route('site.dashboard.bookings', ['locale' => app()->getLocale()])
            ->with('success', __('ui.messages.review_submitted'));
    }
}
