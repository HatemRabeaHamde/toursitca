<?php

namespace App\Http\Controllers\Agency;

use App\Domain\Review\Models\Review;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function index(Request $request): View
    {
        $agencyId = (int) $request->user()->agency->id;

        $reviews = Review::query()
            ->with(['experience', 'user', 'booking'])
            ->whereHas('experience', fn ($q) => $q->where('agency_id', $agencyId))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $ratingAvg = Review::query()
            ->whereHas('experience', fn ($q) => $q->where('agency_id', $agencyId))
            ->where('is_visible', true)
            ->avg('rating');

        $ratingCount = Review::query()
            ->whereHas('experience', fn ($q) => $q->where('agency_id', $agencyId))
            ->where('is_visible', true)
            ->count();

        return view('agency.reviews.index', compact('reviews', 'ratingAvg', 'ratingCount'));
    }
}
