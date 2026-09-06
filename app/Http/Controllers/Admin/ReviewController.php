<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Review\Models\Review;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function index(Request $request): View
    {
        $visibility = $request->string('visibility')->toString();

        $reviews = Review::query()
            ->with(['booking', 'experience', 'user'])
            ->when($visibility === 'visible', fn ($q) => $q->where('is_visible', true))
            ->when($visibility === 'hidden', fn ($q) => $q->where('is_visible', false))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('admin.reviews.index', compact('reviews', 'visibility'));
    }

    public function toggleVisibility(Review $review): RedirectResponse
    {
        $review->forceFill(['is_visible' => ! $review->is_visible])->save();

        return redirect()->back()->with(
            'success',
            $review->is_visible ? 'Review published.' : 'Review hidden.'
        );
    }
}
