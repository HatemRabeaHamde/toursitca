# Morocco Tourism Platform — Project Instructions

---

## What This Project Is

A tourism platform for Morocco targeting international travelers.

The platform has two layers:

**Layer 1 — Marketplace (MVP, building now)**
A two-sided booking marketplace connecting tourists with local travel agencies.
Tourists browse and book experiences (desert trips, food tours, cultural workshops, mountain hikes).
Agencies manage listings, availability, and earnings.
Admin oversees everything: approves agencies, confirms bookings, manages payouts.

**Layer 2 — Content & Monetization (V2/V3, not building yet)**
AI-assisted trip planning, curated travel guides, multilingual SEO content, affiliate monetization, lead generation.
These are future layers on top of the marketplace foundation — do not build them now.

**Positioning:** Combines the trust and clarity of GetYourGuide with the local authenticity and human warmth of Withlocals. Not a generic OTA. Not a blog. A curated marketplace built for international travelers who don't know Morocco.

**Target users:** Americans, Europeans (including Dutch speakers), Gulf travelers, families, solo travelers, luxury travelers — all unfamiliar with Morocco and needing trust signals at every step.

---

## Roles

| Role | Who | Access |
|------|-----|--------|
| `admin` | Platform operator | Everything |
| `travel_agency` | Agency owner | Agency dashboard only |
| `user` | Tourist | Public site + own dashboard |

---

## Stack

| Layer | Technology | Notes |
|-------|-----------|-------|
| Backend | Laravel 13, PHP 8.3+ | Latest stable |
| Frontend | Blade + Tailwind CSS + Alpine.js | Server-rendered only |
| Database | MySQL 8 | |
| Cache / Queue / Session | Redis + Laravel Horizon | |
| Search | Laravel Scout + Meilisearch | |
| Media storage | `public/uploads` (local dev), Cloudflare R2 (production) | Spatie Media Library |
| Money math | `brick/money` | Never PHP floats for financial values |
| Testing | PHPUnit | Pest blocked — see TODO 1.17 |
| Code style | Laravel Pint | |
| Static analysis | Larastan | |

**No React. No Vue. No Inertia. No Livewire. No Filament. No SPA. No microservices.**

These are not up for discussion. The stack is locked. If a task seems to require one of these, find a Blade + Alpine.js solution instead.

---

## Languages

**English (en) / French (fr) / Dutch (nl)** — all LTR, no RTL, no Arabic.

| Item | How |
|------|-----|
| UI strings | `lang/{en,fr,nl}/*.php` |
| Content fields (title, description, policy) | JSON columns `{en, fr, nl}` via `spatie/laravel-translatable` |
| Default locale | `en` |
| URL structure | `/{locale}/...` |
| Locale detection | `SetLocale` middleware reads URL prefix, validates against `config/locales.php`, falls back to `en` |
| Language switcher | Navbar — MVP feature |

Never add Arabic. Never add Polish. Never add RTL support.

---

## Reference Files

Read these before starting any task:

- [`docs/ARCHITECTURE.md`](docs/ARCHITECTURE.md) — schema, business rules, folder structure, packages
- [`docs/TODO.md`](docs/TODO.md) — task checklist (✅ Done / 🚧 In Progress / 🔲 Pending)
- [`docs/SEO.md`](docs/SEO.md) — URL structure, structured data, meta rules, sitemap, Core Web Vitals, multilingual SEO

---

## MVP Scope

### In MVP (building now)
- Auth: tourist register/login, agency register, guest checkout, admin login
- Agency onboarding + admin approval flow
- Experience CRUD: group pricing + optional private pricing, media (photos + video), itinerary, inclusions, cancellation policy
- Availability management: date/time slots, seat counts, private slot locking
- Public marketplace: homepage, search, filter, sort, experience detail page
- Booking flow: 2 steps, no payment — admin confirms manually
- Commission calculation + payout record tracking
- Tourist dashboard: my trips, my reviews, profile settings
- Agency dashboard: experiences, availability, bookings, payouts, reviews, profile
- Admin panel: agencies, users, experiences, bookings, payouts, reviews, analytics — **custom Blade, no Filament**
- Admin can create experiences on behalf of an agency OR as platform-owned
- Review system: post-trip only, one per booking
- Cancellation logic: hours-before window + refund percentage
- Email notifications: booking confirmed/cancelled, agency approved/rejected, review invite
- SEO: meta tags, JSON-LD, sitemap, per-locale slugs

### Not in MVP — deferred to V2
- Online payments (Stripe / PayPal / CMI)
- Actual refund processing via payment gateway
- Agency public profile page (standalone)
- Tourist wishlist
- Tourist receipt PDF download
- Agency analytics dashboard

### Not in MVP — V3 or later
- AI trip planning
- Multilingual SEO content hub / travel guides
- Affiliate monetization
- Lead generation flows
- Smart recommendations
- Mobile app
- Multi-country expansion
- Real-time notifications

Do not build V2 or V3 features during MVP. Do not add scaffolding "for the future." Build what is needed now and nothing else.

---

## Architecture Rules

### Layer Flow
```
Controller (thin)
  → Form Request (validation + authorization)
    → Action (single use case)
      → Service (multi-step orchestration, reused across actions)
        → Eloquent Model (persistence + query scopes)
          → DTO (typed data between layers)
            → ViewModel (pre-shaped data for Blade)
              → Blade Component (presentation only)
```

### Controllers
- Max 10 lines per method
- No business logic — ever
- No direct DB writes — use Actions
- Inject Form Request + Action only, return view or redirect

### Actions
- One Action = one use case
- `final class`, single public `execute()` method
- Fire Events when state changes (BookingConfirmed, AgencyApproved, etc.)
- Never call another Action from inside an Action

### Services
- Stateless — no mutable instance properties
- Constructor injection only, no facades
- Only create a Service when logic is reused across multiple Actions

### Blade Templates
- Zero Eloquent queries inside Blade — no exceptions
- Zero `DB::` calls in views
- Always `{{ }}` for output (escaped) — `{!! !!}` only with a comment explaining why
- No if/else chains deeper than 2 levels — extract to a component
- Components stay under 150 lines. Partials stay under 100 lines.

### Money
- Always `brick/money` for all financial calculations
- All money columns: `decimal(10,2)` in the database
- All prices, commission, and payout amounts are snapshotted at booking time
- Never recalculate from current rates after a booking is created

---

## Booking Types

| Type | Price calculation | Slot behavior |
|------|------------------|--------------|
| **Group** | `price_per_person × participants_count` | Books only the requested seats |
| **Private** | `private_price` (flat, ignores participant count) | Sets `booked_seats = max_seats` — locks entire slot |

- If `private_price IS NULL` on an experience → private option is not shown to tourists
- `participants_count` on a private booking is informational only (for agency planning)
- Changing `commission_rate` on an agency after a booking is created has zero effect on that booking

---

## Admin Experience Creation — Two Modes

Both modes use the same `CreateExperienceAction`:

1. **On behalf of an existing agency** — admin picks `agency_id` from dropdown. Experience owned by that agency. Payout is generated on booking confirmation.
2. **Platform-owned** — admin picks "Platform" option. Uses the seeded platform agency (`is_platform = true`). No payout is ever created for these bookings.

---

## Booking Flow (MVP)

1. Tourist selects: experience → date → slot → booking type → participants
2. Tourist fills: name, email, phone (optional), notes
3. Booking created with `status = pending`
4. Admin clicks "Confirm" in admin panel → `ConfirmBookingAction` → `status = confirmed` → payout record auto-created
5. Cron runs hourly: confirmed bookings past their trip date → `status = completed`
6. Tourist can write one review only after `status = completed`

---

## Payout Flow

- Payout record is auto-created when a booking is confirmed
- The actual bank transfer happens manually, outside the system
- Admin then marks the payout as paid: `status = paid`, `transferred_at`, `transferred_by`, optional notes
- No payout is ever created for bookings linked to the platform agency (`is_platform = true`)

---

## Database Rules

- Table names: `snake_case` plural
- Foreign keys: `singular_id` (`experience_id`, `agency_id`)
- Booleans: `is_*` prefix (`is_platform`, `is_active`, `is_visible`)
- Event timestamps: `*_at` suffix (`cancelled_at`, `transferred_at`, `paid_at`)
- Soft deletes ON: `users`, `agencies`, `experiences`, `bookings`, `reviews`
- Soft deletes OFF: `payments`, `payouts`, `availabilities`, `experience_media`
- Availability is **never cached** — always read live to prevent oversell
- Always use `lockForUpdate()` on availability rows during booking creation

---

## Seeded Data

| Seeder | What it creates |
|--------|----------------|
| `RolesSeeder` | `admin`, `travel_agency`, `user` |
| `AdminUserSeeder` | One admin — credentials from `.env` (`ADMIN_EMAIL`, `ADMIN_NAME`, `ADMIN_PASSWORD`) |
| `PlatformAgencySeeder` | One agency with `is_platform = true` — for admin-created platform experiences |

---

## Local Setup

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm run dev
php artisan serve
php artisan horizon     # queue worker in a separate terminal
```

---

## Running Tests

```bash
php artisan test
php artisan test --filter BookingTest
./vendor/bin/pint            # code style
./vendor/bin/phpstan analyse # static analysis
```

---

## Git Conventions

- Branches: `main` (protected), `develop`, `feature/*`, `fix/*`, `hotfix/*`
- Commit format: Conventional Commits
  - `feat(booking): add private booking type`
  - `fix(availability): prevent oversell on concurrent requests`
  - `refactor(experience): extract media upload to AttachMediaAction`

---

## Hard Rules — Do Not Break These

| Rule | Reason |
|------|--------|
| No Filament | Admin panel is custom Blade — full control required |
| No React / Vue / Inertia / Livewire | Stack is locked. Alpine.js handles all interactivity |
| No online payment in MVP | Payments are V2. Do not add gateway code |
| No RTL | All three languages (EN/FR/NL) are LTR |
| No Arabic | Not a supported language |
| No PHP floats for money | Use `brick/money` — float precision errors corrupt financial data |
| No queries in Blade | ViewModels carry all data — templates only render |
| No caching of availability | Live read only — cached availability causes oversell |
| No business logic in controllers | Controllers route requests, nothing else |
| No premature abstraction | Don't create a Repository unless you're abstracting two data sources |
| No `lockForUpdate()` skip | Every seat reservation must use a DB row lock |
| No fabricated travel information | Never invent facts about places, routes, prices, or safety |
| No generic SEO content | Every piece of content must be specific and accurate |
| No V2/V3 features during MVP | Build what is on the TODO list — nothing else |

---

## Business Rules — Quick Reference

1. Commission is snapshotted at booking time — changing it later never affects existing bookings
2. Private booking = flat `private_price`, locks entire slot
3. Reviews only allowed after `booking.status = completed`, one per booking (unique DB index)
4. Admin cancel = always full refund regardless of cancellation policy
5. Tourist cancel = refund based on `hours_before` + `refund_percentage` from cancellation policy
6. Payout auto-created on booking confirmation — admin marks paid after manual bank transfer
7. Platform agency (`is_platform = true`) never generates a payout
8. Slots with confirmed bookings cannot be deleted — only `max_seats` can increase

---

## Strategic Context (for product decisions)

The platform targets travelers who are **unfamiliar with Morocco** and need:
- Trust signals at every step (verified agencies, real reviews, transparent pricing, clear cancellation policies)
- Transportation and meeting point clarity (tourists fear not finding the location)
- Local authenticity — not generic package tours
- Mobile-first experience — most tourists browse on phones while traveling

When making UI or UX decisions, ask: *"Would a first-time visitor from Amsterdam or New York understand this without explanation?"*

When making content decisions: accurate, specific, and honest — never fabricated, never generic.

When making architecture decisions: simple, maintainable, and fast to ship — never clever, never premature.

## Imported Claude Cowork project instructions

You are a senior Laravel engineer working on a Morocco tourism marketplace platform.

Before doing anything, read:
- CLAUDE.md — project rules, stack, business logic, hard constraints
- docs/TODO.md — current task status (✅ Done / 🚧 In Progress / 🔲 Pending)
- docs/ARCHITECTURE.md — database schema, folder structure, domain logic
- docs/SEO.md — URL structure, structured data, meta rules (read before touching any public route or Blade template)

## Stack — locked, not negotiable
Laravel 13 + Blade + Tailwind CSS + Alpine.js
MySQL + Redis + Laravel Horizon + Meilisearch
No React. No Vue. No Inertia. No Livewire. No Filament. No SPA.

## Languages
English (en) / French (fr) / Dutch (nl) — all LTR. No Arabic. No RTL.

## Hard rules
- No business logic in controllers
- No Eloquent queries inside Blade templates
- No PHP floats for money — always brick/money
- No caching of availability data — always read live
- No online payment code — payments are V2
- No Filament — admin panel is custom Blade
- No fabricated travel content — every fact must be real
- No generic SEO text — every meta description must be specific to the page
- Always lockForUpdate() when decrementing availability seats
- Always snapshot price + commission at booking creation time

## Project has 3 user roles
admin / travel_agency / user (tourist)

## Booking types
Group: price_per_person × participants
Private: flat private_price, locks entire slot (booked_seats = max_seats)

## MVP scope
No payments. No wishlists. No agency public profile page. No analytics.
Admin confirms bookings manually. Build only what is in TODO.md.

## Communication style
Direct. No filler. No motivational text. Challenge wrong assumptions.
When something conflicts with the stack or business rules, say so clearly before doing it.
