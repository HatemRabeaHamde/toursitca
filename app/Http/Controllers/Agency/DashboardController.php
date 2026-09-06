<?php

namespace App\Http\Controllers\Agency;

use App\Domain\Booking\Models\Booking;
use App\Domain\Experience\Models\Availability;
use App\Domain\Experience\Models\Experience;
use App\Domain\Payout\Models\Payout;
use App\Domain\Review\Models\Review;
use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $agencyId = (int) auth()->user()->agency->id;
        $trendDays = $this->trendDays();

        $bookingsBase = fn () => Booking::query()->whereHas('experience', fn ($q) => $q->where('agency_id', $agencyId));
        $reviewsBase  = fn () => Review::query()->whereHas('experience', fn ($q) => $q->where('agency_id', $agencyId))->where('is_visible', true);

        $stats = [
            'experiences_total'     => Experience::query()->where('agency_id', $agencyId)->count(),
            'experiences_published' => Experience::query()->where('agency_id', $agencyId)->where('status', 'published')->count(),
            'experiences_draft'     => Experience::query()->where('agency_id', $agencyId)->where('status', 'draft')->count(),
            'bookings_pending'      => $bookingsBase()->where('status', 'pending')->count(),
            'bookings_confirmed'    => $bookingsBase()->where('status', 'confirmed')->count(),
            'agency_earnings_total' => $bookingsBase()
                ->whereIn('status', ['confirmed', 'completed'])
                ->sum('agency_amount'),
            'agency_earnings_month' => $bookingsBase()
                ->whereIn('status', ['confirmed', 'completed'])
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('agency_amount'),
            'pending_payout'        => Payout::query()->where('agency_id', $agencyId)->where('status', 'pending')->sum('amount'),
            'reviews_count'         => $reviewsBase()->count(),
            'reviews_avg'           => round((float) $reviewsBase()->avg('rating'), 1),
        ];

        $draftExperiences = Experience::query()
            ->where('agency_id', $agencyId)
            ->where('status', 'draft')
            ->limit(3)
            ->get(['id', 'title']);

        $upcomingIn48h = Availability::query()
            ->with('experience:id,title')
            ->whereHas('experience', fn ($query) => $query->where('agency_id', $agencyId))
            ->where('is_active', true)
            ->whereBetween('date', [now()->toDateString(), now()->addHours(48)->toDateString()])
            ->orderBy('date')
            ->orderBy('time_slot')
            ->limit(3)
            ->get();

        $bookingsCreatedTrend = $this->dailyTrend(
            Booking::query()->whereHas('experience', fn ($query) => $query->where('agency_id', $agencyId)),
            $trendDays,
            'created_at',
        );

        $bookingsConfirmedTrend = $this->dailyTrend(
            Booking::query()
                ->whereHas('experience', fn ($query) => $query->where('agency_id', $agencyId))
                ->whereIn('status', ['confirmed', 'completed']),
            $trendDays,
            'created_at',
        );

        $recentBookings = Booking::query()
            ->with(['experience', 'availability'])
            ->whereHas('experience', fn ($query) => $query->where('agency_id', $agencyId))
            ->latest()
            ->limit(6)
            ->get();

        $upcomingSlots = Availability::query()
            ->with('experience')
            ->whereHas('experience', fn ($query) => $query->where('agency_id', $agencyId))
            ->where('is_active', true)
            ->whereDate('date', '>=', now()->toDateString())
            ->orderBy('date')
            ->orderBy('time_slot')
            ->limit(6)
            ->get();

        $almostFullSlots = Availability::query()
            ->with('experience')
            ->whereHas('experience', fn ($query) => $query->where('agency_id', $agencyId))
            ->where('is_active', true)
            ->whereDate('date', '>=', now()->toDateString())
            ->whereRaw('max_seats - booked_seats <= 2')
            ->whereRaw('max_seats - booked_seats > 0')
            ->orderBy('date')
            ->orderBy('time_slot')
            ->limit(6)
            ->get();

        return view('agency.home', compact(
            'stats',
            'recentBookings',
            'upcomingSlots',
            'almostFullSlots',
            'trendDays',
            'bookingsCreatedTrend',
            'bookingsConfirmedTrend',
            'draftExperiences',
            'upcomingIn48h',
        ));
    }

    private function trendDays(): Collection
    {
        return collect(range(6, 0))
            ->map(fn (int $offset) => now()->subDays($offset)->toDateString());
    }

    private function dailyTrend($query, Collection $trendDays, string $column): Collection
    {
        $rows = $query
            ->selectRaw("DATE($column) as day, COUNT(*) as total")
            ->whereDate($column, '>=', $trendDays->first())
            ->groupBy('day')
            ->pluck('total', 'day');

        return $trendDays->map(fn (string $day) => [
            'day' => $day,
            'label' => Carbon::parse($day)->format('D'),
            'value' => (int) ($rows[$day] ?? 0),
        ]);
    }
}
