<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Agency\Models\Agency;
use App\Domain\Booking\Models\Booking;
use App\Domain\Experience\Models\Availability;
use App\Domain\Experience\Models\Experience;
use App\Domain\Payout\Models\Payout;
use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $trendDays = $this->trendDays();

        $stats = [
            'agencies_total' => Agency::query()->count(),
            'agencies_pending' => Agency::query()->where('status', 'pending')->count(),
            'experiences_total' => Experience::query()->count(),
            'experiences_published' => Experience::query()->where('status', 'published')->count(),
            'bookings_pending' => Booking::query()->where('status', 'pending')->count(),
            'bookings_confirmed' => Booking::query()->where('status', 'confirmed')->count(),
            'revenue_confirmed' => (float) Booking::query()->where('status', 'confirmed')->sum('total_price'),
            'payouts_pending' => Payout::query()->where('status', 'pending')->count(),
        ];

        $bookingsCreatedTrend = $this->dailyTrend(
            Booking::query(),
            $trendDays,
            'created_at',
        );

        $bookingsConfirmedTrend = $this->dailyTrend(
            Booking::query()->where('status', 'confirmed'),
            $trendDays,
            'updated_at',
        );

        $recentBookings = Booking::query()
            ->with(['experience.agency', 'availability'])
            ->latest()
            ->limit(6)
            ->get();

        $pendingAgencies = Agency::query()
            ->with('user')
            ->where('status', 'pending')
            ->latest()
            ->limit(6)
            ->get();

        $pendingAgenciesAged = Agency::query()
            ->with('user')
            ->where('status', 'pending')
            ->where('created_at', '<=', now()->subDays(3))
            ->latest()
            ->limit(6)
            ->get();

        $almostFullSlots = Availability::query()
            ->with('experience.agency')
            ->where('is_active', true)
            ->whereDate('date', '>=', now()->toDateString())
            ->whereRaw('max_seats - booked_seats <= 2')
            ->whereRaw('max_seats - booked_seats > 0')
            ->orderBy('date')
            ->orderBy('time_slot')
            ->limit(6)
            ->get();

        $todaysSlots = Availability::query()
            ->with('experience.agency')
            ->where('is_active', true)
            ->whereDate('date', now()->toDateString())
            ->orderBy('time_slot')
            ->limit(8)
            ->get();

        $topExperiences = Experience::query()
            ->withCount(['bookings as bookings_total', 'bookings as bookings_confirmed' => fn ($q) => $q->where('status', 'confirmed')])
            ->with('agency:id,name')
            ->where('status', 'published')
            ->orderByDesc('bookings_total')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'recentBookings',
            'pendingAgencies',
            'pendingAgenciesAged',
            'almostFullSlots',
            'todaysSlots',
            'topExperiences',
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
