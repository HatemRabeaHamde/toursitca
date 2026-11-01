<?php

namespace App\Http\Controllers\Site;

use App\Domain\Booking\Models\Booking;
use App\Domain\Booking\Services\BookingTripPresenter;
use App\Domain\Review\Actions\SubmitReviewAction;
use App\Domain\Review\DTOs\ReviewData;
use App\Domain\Review\Models\Review;
use App\Domain\Review\Services\ReviewHighlights;
use App\Http\Controllers\Controller;
use App\Http\Requests\Site\ReviewStoreRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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

    public function create(Request $request, string $locale, Booking $booking, BookingTripPresenter $trips): View
    {
        abort_unless($booking->user_id === $request->user()->id, 403);
        abort_unless($booking->status === 'completed', 404);
        abort_if($booking->review()->exists(), 404);

        $booking->load(['experience.media', 'experience.host', 'experience.agency', 'availability']);

        return view('site.account.review-create', ['booking' => $booking, 'trip' => $trips->fromBooking($booking)]);
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
            details: array_filter($request->safe()->only(array_keys(Review::DETAIL_RATINGS))),
            photos: $request->file('photos', []),
        ));

        return redirect()->route('site.reviews.thanks', ['locale' => $locale, 'booking' => $booking]);
    }

    public function thanks(Request $request, string $locale, Booking $booking, ReviewHighlights $authors): View
    {
        abort_unless($booking->user_id === $request->user()->id, 403);
        abort_unless($booking->review()->exists(), 404);

        $booking->load(['review.photos', 'experience.host', 'user']);

        return view('site.account.review-thanks', [
            'booking' => $booking,
            'review' => $booking->review,
            'author' => $authors->author($booking->review->setRelation('booking', $booking)->setRelation('user', $booking->user)),
            'avatar' => $authors->avatar($booking->user?->avatar),
            'firstName' => Str::before(trim((string) $booking->user?->name), ' '),
        ]);
    }
}
