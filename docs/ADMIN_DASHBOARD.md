# Admin Dashboard — Backend Logic Reference

## Overview

The admin dashboard is the command center for the platform operator. It gives a real-time snapshot of the marketplace health: agency approvals, booking pipeline, revenue, capacity alerts, and content performance — all in a single server-rendered page with no client-side data fetching.

**Route:** `GET /admin/dashboard`  
**Controller:** `App\Http\Controllers\Admin\DashboardController@index`  
**Middleware:** `auth`, `user.not_banned`, `role:admin`  
**View:** `resources/views/admin/dashboard.blade.php`

---

## What Gets Loaded (One Request)

The controller runs **10 database queries** and passes everything to the Blade view as variables. No AJAX, no pagination on the dashboard itself.

### 1. Stat Cards (`$stats`)

Eight live counters passed as a plain array:

| Key | Query | When it turns urgent |
|-----|-------|----------------------|
| `agencies_total` | `COUNT(*)` on `agencies` | — |
| `agencies_pending` | `WHERE status = 'pending'` | Card turns amber if `> 0` |
| `experiences_total` | `COUNT(*)` on `experiences` | — |
| `experiences_published` | `WHERE status = 'published'` | — |
| `bookings_pending` | `WHERE status = 'pending'` | Card turns amber if `> 0` |
| `bookings_confirmed` | `WHERE status = 'confirmed'` | — |
| `revenue_confirmed` | `SUM(total_price) WHERE status = 'confirmed'` | Displayed as `MAD` |
| `payouts_pending` | `COUNT(*) WHERE status = 'pending'` on `payouts` | Card turns clay if `> 0` |

`revenue_confirmed` is the sum of `bookings.total_price` for confirmed bookings only. It is **not recalculated** from current prices — it reflects the price that was snapshotted at booking time.

Each stat card is a link that navigates to the corresponding filtered list page.

---

### 2. Booking Trend Charts (`$bookingsCreatedTrend`, `$bookingsConfirmedTrend`)

Two bar charts covering the **last 7 days** (today − 6 to today).

- **Created trend:** counts all bookings grouped by `created_at` date, regardless of status.
- **Confirmed trend:** counts only `status = 'confirmed'` bookings grouped by `updated_at` date (the date they were confirmed, not created).

The helper `trendDays()` builds a `Collection` of 7 date strings. `dailyTrend()` runs a `DATE(column) GROUP BY` query, then maps missing days to `0` so the chart always shows 7 bars.

Bar height is proportional: `(value / max) × 56px`, minimum `3px` for a day with zero.

---

### 3. Recent Bookings (`$recentBookings`)

The **6 most recent bookings** regardless of status, eager-loaded with:
- `experience.agency` — for the experience title and agency name
- `availability` — for the trip date

Displayed with: guest name initials avatar, experience title, agency name, total price (MAD), trip date, and a status badge (`pending` / `confirmed` / `completed` / `cancelled`).

---

### 4. Pending Agencies (`$pendingAgencies`)

The **6 most recent agencies** with `status = 'pending'`, eager-loaded with `user` for the email address.

A visual "aged" flag is computed in Blade: if `created_at` is more than 3 days ago, the initials avatar turns amber and the email line shows a warning. An inline one-click Approve button sends `POST /admin/agencies/{agency}/approve`.

---

### 5. Aged Pending Agencies (`$pendingAgenciesAged`)

A second, separate query for the same `status = 'pending'` agencies **but filtered to those waiting 3+ days** (`created_at <= now() - 3 days`). These appear in a dedicated "Needs Attention" card at the bottom of the dashboard.

Purpose: ensures agencies that were somehow missed don't fall off the main pending list silently.

---

### 6. Almost-Full Slots (`$almostFullSlots`)

Availability slots where:
- `is_active = true`
- `date >= today`
- `max_seats - booked_seats <= 2` (1 or 2 seats left)
- `max_seats - booked_seats > 0` (not yet fully booked)

Up to **6 slots**, ordered by date then time. Displayed with a capacity bar. Bar turns red if available seats ≤ 20% of capacity.

`availableSeats()` is a method on the `Availability` model: `max_seats - booked_seats`.

This is always a **live read** — availability is never cached.

---

### 7. Today's Trips (`$todaysSlots`)

Active availability slots where `date = today`, ordered by `time_slot`, up to **8 slots**.

Shows: departure time, experience title (truncated to 34 chars), agency name, and seat availability. If `avail === 0`, shows a "Full" badge. If seats ≤ 25% remaining, the count turns amber.

---

### 8. Top Experiences (`$topExperiences`)

The **5 published experiences** with the most total bookings, ranked by `bookings_total` descending.

Uses `withCount` with a conditional aggregate:
- `bookings_total` — all bookings for the experience
- `bookings_confirmed` — only `status = 'confirmed'` bookings (available but not currently displayed, ready for extension)

---

## Agency Status Lifecycle

Agencies move through these statuses. The dashboard surfaces the `pending` state:

```
pending → active    (admin approves)
pending → rejected  (admin rejects)
active  → suspended (admin suspends)
suspended → active  (admin reactivates)
```

The `is_platform` flag on an agency marks it as the seeded platform agency. It is never shown in the pending list, never generates payouts, and cannot be suspended through the UI.

---

## Booking Status Lifecycle

The dashboard surfaces `pending` and `confirmed` counts:

```
pending → confirmed   (admin confirms → payout auto-created)
pending → cancelled   (admin or tourist cancels)
confirmed → completed (cron, runs hourly, checks trip date passed)
completed → [review]  (tourist can write one review)
```

`total_price`, `commission_rate`, and `commission_amount` are **snapshotted at booking creation** and never recalculated. Changing an agency's commission rate after a booking is created has no effect on that booking.

---

## Payout Logic

A payout record is auto-created by `ConfirmBookingAction` when a booking transitions to `confirmed`. The dashboard shows `payouts_pending` — the count of payouts where `status = 'pending'`.

No payout is ever created for bookings linked to a platform agency (`is_platform = true`).

The actual bank transfer happens outside the system. Admin then marks the payout paid via `POST /admin/payouts/{payout}/mark-paid`, which sets `status = 'paid'`, `transferred_at`, and `transferred_by`.

---

## Admin Panel Routes Summary

All routes are under `prefix('admin')`, named `admin.*`, protected by `role:admin`.

| Resource | Methods | Route names |
|----------|---------|-------------|
| Dashboard | GET | `admin.dashboard` |
| Agencies | GET index, GET show, GET/POST create, GET/PATCH edit, POST approve/suspend/reactivate/reject | `admin.agencies.*` |
| Bookings | GET index, GET show, POST confirm/cancel, PATCH payment-status | `admin.bookings.*` |
| Payouts | GET index, POST mark-paid | `admin.payouts.*` |
| Reviews | GET index, POST toggle-visibility | `admin.reviews.*` |
| Experiences | GET index, GET/POST create, GET/PATCH edit, POST publish/unpublish | `admin.experiences.*` |
| Experience Options | GET index, POST store, PATCH update, DELETE destroy | `admin.experiences.options.*` |
| Availability | GET index, POST store, PATCH update, DELETE destroy | `admin.availability.*` |
| Users | GET index, GET show, POST ban/restore | `admin.users.*` |
| Landing Content | CRUD for categories, destinations, landmarks, FAQs, reels, testimonials | `admin.landing.*` |

---

## Data Flow Summary

```
GET /admin/dashboard
  └── DashboardController@index
        ├── 8 COUNT/SUM queries → $stats array
        ├── 2 DATE GROUP BY queries → $bookingsCreatedTrend, $bookingsConfirmedTrend
        ├── Booking::latest()->limit(6) → $recentBookings
        ├── Agency::pending()->latest()->limit(6) → $pendingAgencies
        ├── Agency::pending()->aged()->latest()->limit(6) → $pendingAgenciesAged
        ├── Availability::almostFull()->upcoming()->limit(6) → $almostFullSlots
        ├── Availability::today()->limit(8) → $todaysSlots
        └── Experience::published()->withCount(bookings)->top(5) → $topExperiences
              └── view('admin.dashboard') with all variables
                    ├── partials/stat-cards
                    ├── partials/recent-bookings
                    ├── partials/pending-agencies
                    ├── partials/todays-trips
                    ├── partials/top-experiences
                    ├── partials/almost-full-slots
                    └── inline aged-agencies card
```

---

## Key Business Rules Reflected in the Dashboard

1. **Pending agencies turn urgent after 3 days** — surfaced in both the main pending list (aged flag) and the dedicated "Needs Attention" card.
2. **Bookings require manual admin confirmation** — no automatic confirmation exists in MVP. The `pending` count on the dashboard is the admin's work queue.
3. **Revenue figure is confirmed bookings only** — pending bookings are not counted as revenue until confirmed.
4. **Availability is never cached** — the almost-full slots and today's trips always reflect live seat counts to prevent oversell.
5. **Platform agency is invisible** — the seeded `is_platform = true` agency never appears in pending lists, payout counts, or approval flows.
