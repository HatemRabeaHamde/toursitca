# Morocco Tourism Platform (Laravel marketplace)

Curated booking marketplace connecting international tourists unfamiliar with Morocco with local agencies. Roles: `admin`, `travel_agency`, `user`.

## Read only when relevant

- `AGENTS.md`: full instructions (MVP scope, booking and payout flows, admin modes, seeders, local setup, product context)
- `docs/ARCHITECTURE.md`: schema, business logic, folder structure, packages
- `docs/TODO.md`: task status. Search for the task; do not read it all.
- `docs/SEO.md`: read before touching any public route or Blade template

## Stack (locked)

Laravel 13, PHP 8.3+, Blade, Tailwind, Alpine.js, MySQL 8, Redis + Horizon, Scout + Meilisearch, Spatie Media Library, `brick/money`, PHPUnit (Pest blocked, see TODO 1.17), Pint, Larastan.

No React, Vue, Inertia, Livewire, Filament, SPA or microservices. Find a Blade + Alpine solution instead.

Languages: en, fr, nl only (LTR). UI strings in `lang/{en,fr,nl}/*.php`; content via `spatie/laravel-translatable`; URLs are `/{locale}/...`. Never add Arabic, Polish or RTL.

## Architecture

Controller → Form Request → Action → Service → Model → DTO → ViewModel → Blade Component

- Controllers: max 10 lines per method, no business logic or DB writes; inject Form Request + Action only.
- Actions: `final class`, one `execute()`, one use case; fire Events on state change; never call another Action.
- Services: stateless, constructor injection, no facades; only when logic is reused across Actions.
- Blade: no queries or `DB::`; `{{ }}` only (`{!! !!}` needs a comment why); max 2 levels of if/else; components under 150 lines, partials under 100.
- No Repository unless abstracting two data sources.

## Database and money

- `snake_case` plural tables, `singular_id` FKs, `is_*` booleans, `*_at` event timestamps.
- Soft deletes on: users, agencies, experiences, bookings, reviews. Off: payments, payouts, availabilities, experience_media.
- Availability is never cached. Always `lockForUpdate()` on availability rows when reserving seats.
- All money via `brick/money`, never PHP floats; `decimal(10,2)` columns. Snapshot prices, commission and payouts at booking time; never recalculate.

## Business rules

1. Group price = `price_per_person × participants_count`, books requested seats. Private = flat `private_price`, sets `booked_seats = max_seats`; hidden when `private_price` is NULL.
2. Available slots create `confirmed` bookings immediately at checkout (user-approved 2026-11-02). Admin may still confirm legacy pending records; cron marks `completed` after the trip date. Payment status is independent.
3. Reviews only after `completed`, one per booking.
4. Admin cancel = full refund. Tourist cancel = `hours_before` + `refund_percentage` from the policy.
5. Payout auto-created on confirmation; admin marks it paid after a manual bank transfer. Platform agency (`is_platform = true`) never gets a payout.
6. Slots with confirmed bookings cannot be deleted; `max_seats` can only increase.

## Scope

Build only what is on the TODO list, with no scaffolding for the future. No online payment or gateway code in MVP. No V2/V3 features (see `AGENTS.md`).

Never invent facts about places, routes, prices or safety. No generic SEO content.

UI test: would a first-time visitor from Amsterdam or New York understand this without explanation? Mobile-first. Architecture: simple and fast to ship, never clever or premature.

## Commands and git

```bash
php artisan test
php artisan test --filter BookingTest
./vendor/bin/pint
./vendor/bin/phpstan analyse
```

Branches: `main` (protected), `develop`, `feature/*`, `fix/*`, `hotfix/*`. Conventional Commits, e.g. `fix(availability): prevent oversell on concurrent requests`.
