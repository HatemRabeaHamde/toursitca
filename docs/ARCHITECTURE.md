# Morocco Tourism Platform — Architecture Plan
> Last updated: 2026-05-13

---

## Stack Decisions

| Item | Choice | Reason |
|------|--------|--------|
| Backend | Laravel 11 | Full-featured PHP framework, great ecosystem |
| Frontend | Blade + Tailwind CSS + Alpine.js | Server-rendered, SEO-friendly, no SPA complexity |
| Admin Panel | Custom Blade + Tailwind (no Filament) | Full control, easier to customize |
| Agency Dashboard | Custom Blade + Tailwind | Full brand control, easy to update |
| Database | MySQL 8 | Reliable, well-supported with Laravel |
| Cache / Queue | Redis | Sessions + cache + job queues in one |
| Queue Monitor | Laravel Horizon | Visibility on background jobs |
| Search | Laravel Scout + Meilisearch | Fast filtered experience search |
| Media | Spatie Media Library | Conversions, WebP, polymorphic attachments |
| Storage | Cloudflare R2 (S3-compatible) | Cheap egress, global CDN |
| Mail | Postmark or Resend | Reliable transactional email |
| Payments | Gateway-agnostic interface (V2) | Deferred — Stripe / PayPal / CMI TBD |
| Testing | Pest PHP | Clean syntax, fast |
| Money Math | brick/money | No float errors on financial calculations |

---

## Languages

**English / French / Dutch** (EN / FR / NL) — all LTR, no RTL needed.

- UI strings: `lang/{en,fr,nl}/*.php`
- Content fields (title, description, policy): JSON columns `{en, fr, nl}` via `spatie/laravel-translatable`
- Language switcher in Navbar (MVP)
- Default locale: `en`
- URL prefix: `/{locale}/...`

---

## Roles

| Role | Who | Access |
|------|-----|--------|
| `admin` | Platform operator | Everything |
| `travel_agency` | Agency owner | Agency dashboard only |
| `user` | Tourist | Public site + own dashboard |

Managed by `spatie/laravel-permission`.

---

## Booking Types

Every experience supports two modes (set by agency):

| Type | Price Logic | Slot Behavior |
|------|-------------|---------------|
| **Group** | `price_per_person × participants` | Joins shared slot, only books chosen seats |
| **Private** | `private_price` (flat, fixed) | Locks entire slot — `booked_seats = max_seats` |

- If `private_price` is NULL → private booking not available for that experience.
- Both types snapshot price + commission rate at booking time.

---

## Admin Experience Creation — Two Modes

1. **On behalf of an existing agency** — Admin picks agency from dropdown, fills form. Experience owned by that agency.
2. **Platform-owned experience** — Admin picks "Platform" option. Uses a seeded platform agency (`is_platform = true`). No payout is generated for these bookings.

Both modes use the same `CreateExperienceAction`.

---

## MVP Scope (what gets built now)

| Module | In MVP |
|--------|--------|
| Auth (tourist + agency + admin, guest checkout) | YES |
| Agency onboarding + admin approval | YES |
| Experience CRUD (group + private pricing) | YES |
| Experience media gallery | YES |
| Availability + seat management | YES |
| Search / filter / sort | YES |
| Experience detail page (full UX) | YES |
| Booking flow — 2 steps, no payment | YES |
| Commission calc + payout structure | YES |
| Tourist dashboard (trips, reviews, profile) | YES |
| Agency dashboard (full) | YES |
| Admin panel (full — custom Blade) | YES |
| Admin creates experience (2 modes) | YES |
| Review system (post-trip only) | YES |
| Cancellation logic | YES |
| Email notifications | YES |
| Language switcher EN/FR/NL | YES |
| SEO (meta, JSON-LD, sitemap) | YES |
| Online payments | **V2** |
| Actual refund processing | **V2** |
| Agency public profile page | **V2** |
| Tourist wishlist | **V2** |
| Tourist receipt download | **V2** |
| Agency analytics | **V2** |
| Smart recommendations | **V3** |
| Mobile app | **V3** |
| Multi-country | **V3** |

---

## Architecture Style

**Modular Layered Monolith** — organized by domain, not by type.

```
HTTP Layer (Controller)
    └── Form Request (validation + authorization)
        └── Action (single use-case, business logic)
            └── Service (stateful orchestration, reused across actions)
                └── Eloquent Model (persistence + scopes)
                    └── DTO (typed data transfer between layers)
                        └── ViewModel (pre-shaped data for Blade)
                            └── Blade Component (presentation only)
```

### Key Rules
- Controllers are thin — max 10 lines per method, no business logic
- Actions are single-purpose — one action per use case
- Services handle multi-step orchestration reused by multiple actions
- ViewModels keep logic out of Blade templates
- No Eloquent queries inside Blade files, ever
- Money calculations always use `brick/money`, never PHP floats
- DB transactions around every multi-row write

---

## Database Schema

### Table: `users`
| Column | Type | Notes |
|--------|------|-------|
| id | bigint unsigned | PK |
| name | varchar(100) | |
| email | varchar(150) | Unique |
| password | varchar(255) | Bcrypt |
| phone | varchar(20) | Nullable |
| avatar | varchar(255) | Nullable, storage path |
| preferred_lang | varchar(5) | en / fr / nl |
| status | enum | active / banned |
| email_verified_at | timestamp | Nullable |
| created_at / updated_at | timestamp | |
| deleted_at | timestamp | Soft delete |

### Table: `agencies`
| Column | Type | Notes |
|--------|------|-------|
| id | bigint unsigned | PK |
| user_id | bigint unsigned | FK users.id |
| name | varchar(150) | |
| description | text | Nullable |
| logo | varchar(255) | Nullable |
| commission_rate | decimal(5,2) | Default 10.00 — set by admin |
| status | enum | pending / active / suspended |
| city | varchar(80) | Nullable |
| phone | varchar(20) | Nullable |
| is_platform | boolean | Default false — marks platform-owned agency |
| created_at / updated_at | timestamp | |
| deleted_at | timestamp | Soft delete |

### Table: `experiences`
| Column | Type | Notes |
|--------|------|-------|
| id | bigint unsigned | PK |
| agency_id | bigint unsigned | FK agencies.id |
| title | json | {en, fr, nl} |
| description | json | {en, fr, nl} |
| category | varchar(80) | food / desert / mountain / cultural / workshop |
| difficulty | enum | easy / moderate / hard |
| duration_hours | decimal(4,1) | e.g. 3.5 |
| max_group_size | tinyint | |
| price_per_person | decimal(10,2) | Group price |
| private_price | decimal(10,2) | Nullable — NULL means no private option |
| location_city | varchar(80) | |
| location_lat | decimal(10,7) | Nullable |
| location_lng | decimal(10,7) | Nullable |
| meeting_point | text | Nullable |
| inclusions | json | Nullable |
| exclusions | json | Nullable |
| status | enum | draft / published / unpublished |
| thumbnail | varchar(255) | Nullable |
| rating_avg | decimal(3,2) | Denormalized, updated on review events |
| reviews_count | int | Denormalized, updated on review events |
| created_by | bigint unsigned | FK users.id — who created it |
| created_at / updated_at | timestamp | |
| deleted_at | timestamp | Soft delete |

### Table: `experience_media`
| Column | Type | Notes |
|--------|------|-------|
| id | bigint unsigned | PK |
| experience_id | bigint unsigned | FK |
| type | enum | image / video |
| path | varchar(255) | Storage path or external URL |
| sort_order | tinyint | Default 0 |
| created_at / updated_at | timestamp | |

### Table: `availabilities`
| Column | Type | Notes |
|--------|------|-------|
| id | bigint unsigned | PK |
| experience_id | bigint unsigned | FK |
| date | date | |
| time_slot | time | e.g. 09:00:00 |
| max_seats | tinyint | |
| booked_seats | tinyint | Default 0 |
| is_active | boolean | Default true |
| created_at / updated_at | timestamp | |

Unique: `(experience_id, date, time_slot)`

### Table: `bookings`
| Column | Type | Notes |
|--------|------|-------|
| id | bigint unsigned | PK |
| user_id | bigint unsigned | FK — nullable for guest |
| experience_id | bigint unsigned | FK |
| availability_id | bigint unsigned | FK |
| booking_type | enum | group / private |
| participants_count | tinyint | |
| unit_price | decimal(10,2) | Snapshot: price_per_person or private_price |
| total_price | decimal(10,2) | Snapshot |
| commission_rate | decimal(5,2) | Snapshot |
| commission_amount | decimal(10,2) | Snapshot |
| agency_amount | decimal(10,2) | Snapshot |
| status | enum | pending / confirmed / completed / cancelled |
| guest_name | varchar(100) | |
| guest_email | varchar(150) | |
| guest_phone | varchar(20) | Nullable |
| special_notes | text | Nullable |
| cancelled_at | timestamp | Nullable |
| cancellation_reason | text | Nullable |
| created_at / updated_at | timestamp | |

### Table: `payments` (structure built MVP, used V2)
| Column | Type | Notes |
|--------|------|-------|
| id | bigint unsigned | PK |
| booking_id | bigint unsigned | FK unique — one payment per booking |
| amount | decimal(10,2) | |
| currency | varchar(5) | Default MAD |
| method | enum | credit_card / paypal |
| status | enum | pending / paid / refunded / failed |
| gateway_ref | varchar(255) | Nullable |
| paid_at | timestamp | Nullable |
| refunded_at | timestamp | Nullable |
| refund_amount | decimal(10,2) | Nullable |
| created_at / updated_at | timestamp | |

### Table: `payouts`
| Column | Type | Notes |
|--------|------|-------|
| id | bigint unsigned | PK |
| agency_id | bigint unsigned | FK |
| booking_id | bigint unsigned | FK unique |
| amount | decimal(10,2) | = booking.agency_amount |
| status | enum | pending / paid |
| notes | text | Nullable |
| transferred_at | timestamp | Nullable |
| transferred_by | bigint unsigned | FK users.id — which admin paid |
| created_at / updated_at | timestamp | |

No payout created for bookings on platform-agency experiences (`is_platform = true`).

### Table: `reviews`
| Column | Type | Notes |
|--------|------|-------|
| id | bigint unsigned | PK |
| booking_id | bigint unsigned | FK unique — one review per booking |
| user_id | bigint unsigned | FK |
| experience_id | bigint unsigned | FK — for fast queries |
| rating | tinyint | 1–5 |
| comment | text | Nullable |
| is_visible | boolean | Default true — admin can hide |
| created_at / updated_at | timestamp | |

### Table: `cancellation_policies`
| Column | Type | Notes |
|--------|------|-------|
| id | bigint unsigned | PK |
| experience_id | bigint unsigned | FK unique |
| hours_before | smallint | Hours before trip qualifying for refund |
| refund_percentage | decimal(5,2) | 0.00 = no refund, 100.00 = full |
| description | json | Nullable — {en, fr, nl} |
| created_at / updated_at | timestamp | |

---

## Business Logic Rules

### Commission Calculation
```
total_price      = price_per_person × participants  (group)
total_price      = private_price                    (private)
commission_amount = total_price × (commission_rate / 100)
agency_amount    = total_price - commission_amount
```
All three values are snapshotted permanently on the booking. Changing agency rate later has zero effect on existing bookings.

### Booking States
```
pending ──admin confirms──► confirmed ──trip date passed──► completed
pending ──24h no confirm──► cancelled  (V2: payment expiry)
confirmed ──tourist cancels (within window)──► cancelled
confirmed ──admin cancels (anytime)──► cancelled + full refund
```

### Seat Management (SeatReservationService)
1. `lockForUpdate()` on the availability row
2. Check: `max_seats - booked_seats >= participants_count`
3. Create booking + increment `booked_seats` in same DB transaction
4. Private booking: set `booked_seats = max_seats` (locks slot entirely)
5. On cancel: decrement `booked_seats` (private: reset to 0)

### Cancellation & Refund
| Scenario | Refund |
|----------|--------|
| Tourist cancels BEFORE policy window | `total_price × (refund_percentage / 100)` |
| Tourist cancels AFTER policy window | 0 |
| Admin cancels anytime | Always full refund |
| Booking never confirmed (expired) | No charge, no refund |

### Review Rules
- Only the tourist who made the booking can review
- Only after `booking.status = completed`
- Exactly one review per booking (unique index on `booking_id`)
- Admin can hide (set `is_visible = false`) — record never deleted
- `rating_avg` computed from all visible reviews — denormalized on experience row

### Payout Flow
1. Booking confirmed → payout record auto-created (`status = pending`)
2. Admin sees pending payouts grouped by agency
3. Admin does bank transfer manually (outside system)
4. Admin marks payout as paid in system (`transferred_at`, `transferred_by`)
5. No payout created for `is_platform = true` agency

---

## Key Indexes

| Index | Purpose |
|-------|---------|
| `experiences(status, location_city, category)` | Search filters |
| `experiences(status, price_per_person)` | Price sort |
| `availabilities(experience_id, date, is_active)` | Slot lookup |
| `availabilities` unique `(experience_id, date, time_slot)` | No duplicate slots |
| `bookings(user_id, status)` | Tourist dashboard |
| `bookings(experience_id, status)` | Agency bookings view |
| `bookings(status, created_at)` | Cron jobs |
| `payouts(agency_id, status)` | Pending payouts |
| `payments(booking_id)` unique | One payment per booking |
| `reviews(booking_id)` unique | One review per booking |
| `reviews(experience_id, is_visible)` | Rating queries |

---

## Soft Deletes

| Table | Soft Delete |
|-------|------------|
| users | YES |
| agencies | YES |
| experiences | YES |
| bookings | YES |
| reviews | YES |
| payments | NO |
| payouts | NO |
| availabilities | NO |
| experience_media | NO |

---

## Caching Strategy (Redis)

| Cache Key | TTL | Bust Trigger |
|-----------|-----|-------------|
| `home:featured` | 15m | Experience published/unpublished |
| `home:categories` | 1h | Never (static list) |
| `experience:{id}:card` | 1h | Experience saved |
| `experience:{id}:detail` | 30m | Experience saved |
| `agency:{id}:card` | 1h | Agency saved |
| Availability data | NEVER | Always read live — prevents oversell |

---

## Queue Architecture

| Queue | Workers | Jobs |
|-------|---------|------|
| `default` | 2 | General |
| `notifications` | 1 | Emails |
| `media` | 1 | Image conversions |
| `low` | 1 | Sitemap, analytics |

---

## Folder Structure

```
app/
├── Console/Commands/
│   ├── CompleteBookingsCommand.php
│   ├── ExpireUnpaidBookingsCommand.php
│   └── SendReviewInvitesCommand.php
├── Domain/
│   ├── Agency/
│   │   ├── Actions/         ApproveAgencyAction, RejectAgencyAction, SuspendAgencyAction, UpdateCommissionRateAction
│   │   ├── DTOs/            AgencyRegistrationData
│   │   ├── Events/          AgencyApproved, AgencyRejected
│   │   ├── Models/          Agency
│   │   ├── Policies/        AgencyPolicy
│   │   └── ViewModels/      AgencyCardViewModel
│   ├── Booking/
│   │   ├── Actions/         CreateBookingAction, ConfirmBookingAction, CancelBookingAction, CompleteBookingAction
│   │   ├── DTOs/            BookingData
│   │   ├── Events/          BookingConfirmed, BookingCancelled, BookingCompleted
│   │   ├── Models/          Booking
│   │   ├── Services/        SeatReservationService, CommissionCalculator, RefundCalculator
│   │   ├── States/          PendingState, ConfirmedState, CompletedState, CancelledState
│   │   └── ViewModels/      BookingSummaryViewModel
│   ├── Experience/
│   │   ├── Actions/         CreateExperienceAction, UpdateExperienceAction, PublishExperienceAction, AttachMediaAction
│   │   ├── DTOs/            ExperienceFormData, ExperienceFilterData
│   │   ├── Models/          Experience, ExperienceMedia, Availability, CancellationPolicy
│   │   ├── Policies/        ExperiencePolicy
│   │   ├── Repositories/    ExperienceSearchRepository
│   │   ├── Services/        AvailabilityService
│   │   └── ViewModels/      ExperienceCardViewModel, ExperienceDetailViewModel, SearchResultsViewModel
│   ├── Payment/
│   │   ├── DTOs/            PaymentResult
│   │   ├── Gateways/        PaymentGatewayInterface, StripeGateway (stub), PaypalGateway (stub), CmiGateway (stub)
│   │   └── Models/          Payment
│   ├── Payout/
│   │   ├── Actions/         CreatePayoutFromBookingAction, MarkPayoutPaidAction
│   │   ├── Listeners/       CreatePayoutOnBookingConfirmed
│   │   ├── Models/          Payout
│   │   └── ViewModels/      PendingPayoutsViewModel
│   ├── Review/
│   │   ├── Actions/         SubmitReviewAction
│   │   ├── Models/          Review
│   │   └── Policies/        ReviewPolicy
│   ├── User/
│   │   ├── Actions/         RegisterUserAction
│   │   └── Models/          User
│   └── Shared/
│       ├── Casts/           MoneyCast
│       ├── Concerns/        HasTranslations, HasSlug
│       └── Money/           MoneyValue
├── Http/
│   ├── Controllers/
│   │   ├── Admin/           DashboardController, AgencyController, UserController, ExperienceController,
│   │   │                    BookingController, PayoutController, ReviewController, AnalyticsController
│   │   ├── Agency/          DashboardController, ExperienceController, AvailabilityController,
│   │   │                    BookingController, PayoutController, ReviewController, ProfileController
│   │   ├── Site/            HomeController, ExperienceController, BookingController,
│   │   │                    BookingCancellationController, ReviewController,
│   │   │                    TouristDashboardController, Auth/*
│   │   └── Webhooks/        StripeWebhookController, PaypalWebhookController
│   ├── Middleware/          SetLocale, EnsureAgencyApproved, EnsureUserNotBanned
│   ├── Requests/            Site/*, Agency/*
│   └── ViewComposers/       NavbarComposer, SeoComposer
├── Mail/                    BookingConfirmedMail, BookingCancelledMail, AgencyApprovedMail,
│                            AgencyRejectedMail, ReviewInviteMail
├── Providers/               AppServiceProvider, DomainServiceProvider, EventServiceProvider, ViewServiceProvider
├── Settings/                PlatformSettings
└── View/Components/
    ├── Site/                ExperienceCard, BookingTypeSelector, AvailabilityPicker,
    │                        StickyBookingBar, ReviewCard, AgencyCard
    └── Ui/                  Button, Modal, Input, Select, Textarea, Card, Badge, Alert, Tab, Pagination, EmptyState

resources/views/
├── layouts/                 site, agency, admin, auth, _email
├── partials/head/           meta, og, schema, analytics
├── partials/                navbar, footer, flash
├── components/site/         experience-card, booking-type-selector, availability-picker,
│                            sticky-booking-bar, review-card, agency-card, itinerary-list,
│                            inclusions-table, filter-sidebar, search-bar, category-strip
├── components/ui/           button, card, modal, input, select, textarea, badge,
│                            alert, tab, pagination, empty-state
├── site/                    home, search, experience/show, checkout/step1, checkout/step2,
│                            checkout/confirmation, auth/*, dashboard/*
├── agency/                  home, experiences/*, availability/index, bookings/*, payouts/index,
│                            reviews/index, profile/edit
├── admin/                   home, agencies/*, users/*, experiences/*, bookings/*,
│                            payouts/*, reviews/index, analytics/index
└── emails/                  booking-confirmed, booking-cancelled, agency-approved,
                             agency-rejected, review-invite

routes/
├── web.php        (requires all below)
├── site.php       (public + tourist dashboard)
├── agency.php     (/agency/* — EnsureAgencyApproved)
├── admin.php      (/admin/* — admin role only)
├── auth.php
├── webhooks.php   (no CSRF — signature middleware)
└── console.php

config/
├── booking.php    (seat hold minutes, expiry hours, booking types)
├── commission.php (default rate)
├── locales.php    (en/fr/nl definitions)
├── seo.php        (default title, description, og image)
└── media.php      (disk, max size, conversions)
```

---

## Recommended Packages

| Package | Purpose |
|---------|---------|
| `spatie/laravel-permission` | Roles and permissions |
| `spatie/laravel-translatable` | EN/FR/NL JSON content fields |
| `spatie/laravel-medialibrary` | Media uploads + WebP conversions |
| `spatie/laravel-sluggable` | Per-locale URL slugs |
| `spatie/laravel-sitemap` | Auto-generated sitemap.xml |
| `spatie/laravel-data` | Typed DTOs |
| `spatie/laravel-activitylog` | Admin audit trail |
| `spatie/laravel-backup` | Daily DB + storage backups |
| `spatie/laravel-model-states` | Booking state machine |
| `spatie/laravel-csp` | Content Security Policy headers |
| `spatie/laravel-settings` | Typed platform settings |
| `laravel/horizon` | Queue monitoring dashboard |
| `laravel/scout` + Meilisearch | Fast experience search |
| `intervention/image` | Image re-encode + EXIF strip |
| `brick/money` | Safe decimal money math |
| `barryvdh/laravel-debugbar` | Dev query inspection |
| `nunomaduro/larastan` | Static analysis |
| `laravel/pint` | Code style formatter |
| `pestphp/pest` | Test framework |
| `laravel/pennant` | Feature flags for V2 rollout |

---

## Coding Rules

- **Controllers**: thin — max 10 lines per method, no business logic, no DB writes
- **Actions**: single-purpose, `final class`, one public `execute()` method
- **Services**: stateless, injected via constructor, no facades
- **Blade**: no queries, no Eloquent, always `{{ }}`, no if/else chains deeper than 2 levels
- **Money**: always `brick/money` — never PHP floats for financial values
- **Commits**: Conventional Commits — `feat(booking): add private booking support`
- **Branches**: `main` (protected), `develop`, `feature/*`, `fix/*`
- **DB naming**: snake_case plural tables, `singular_id` FKs, `is_*` booleans, `*_at` timestamps

---

## Deployment

| Item | Choice |
|------|--------|
| Server | Ubuntu 22.04, PHP 8.3, Nginx |
| Provisioning | Laravel Forge |
| DB | Managed MySQL 8 |
| Cache/Queue | Redis (DB0=cache, DB1=queue, DB2=session) |
| Storage | Cloudflare R2 |
| CDN | Cloudflare |
| Mail | Postmark or Resend |
| CI/CD | GitHub Actions → lint + test + build → staging → manual prod |
| Backups | spatie/laravel-backup daily → R2 separate bucket |
| Monitoring | Sentry (errors) + UptimeRobot + Horizon dashboard |
