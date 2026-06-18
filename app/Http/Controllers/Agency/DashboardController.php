<?php

namespace App\Http\Controllers\Agency;

use App\Domain\Booking\Models\Booking;
use App\Domain\Experience\Models\Availability;
use App\Domain\Experience\Models\Experience;
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

        $stats = [
            'experiences_total' => Experience::query()->where('agency_id', $agencyId)->count(),
            'experiences_published' => Experience::query()->where('agency_id', $agencyId)->where('status', 'published')->count(),
            'experiences_draft' => Experience::query()->where('agency_id', $agencyId)->where('status', 'draft')->count(),
            'bookings_pending' => Booking::query()->whereHas('experience', fn ($query) => $query->where('agency_id', $agencyId))->where('status', 'pending')->count(),
            'bookings_confirmed' => Booking::query()->whereHas('experience', fn ($query) => $query->where('agency_id', $agencyId))->where('status', 'confirmed')->count(),
            'agency_earnings_confirmed' => Booking::query()
                ->whereHas('experience', fn ($query) => $query->where('agency_id', $agencyId))
                ->where('status', 'confirmed')
                ->sum('agency_amount'),
        ];

        $bookingsCreatedTrend = $this->dailyTrend(
            Booking::query()->whereHas('experience', fn ($query) => $query->where('agency_id', $agencyId)),
            $trendDays,
            'created_at',
        );

        $bookingsConfirmedTrend = $this->dailyTrend(
            Booking::query()
                ->whereHas('experience', fn ($query) => $query->where('agency_id', $agencyId))
                ->where('status', 'confirmed'),
            $trendDays,
            'updated_at',
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
