# Morocco Tourism Platform — Master Task List
> Last updated: 2026-08-05
> Legend: ✅ Done | 🔲 Pending | 🚧 In Progress

---

## Current MVP Audit — 2026-08-05

This section is the current source of truth until the older phase tables below are fully reconciled. It was audited against the actual Laravel files, routes, views, migrations, and tests currently present in the project.

### Locked Product Direction

| Area | Current decision |
|------|------------------|
| Brand | `tourstica` / TourstiCa styling system is active across public and dashboard surfaces |
| Stack | Laravel + Blade + Tailwind CSS + Alpine.js only |
| MVP payments | No real online payment gateway. Checkout confirms a pending/manual booking only |
| Supported locales | `en`, `fr`, `nl` in `config/locales.php` |
| Locale files | `lang/nl/*` exists; old `lang/pl/*` has been removed |

### Confirmed Built / Mostly Built

| Area | Evidence in code | Current status |
|------|------------------|----------------|
| Public homepage | `resources/views/site/home.blade.php`, `resources/css/landing.css`, landing content models/services | 🚧 Built visually; needs final link/content QA |
| Public experiences listing | `ExperienceSearchQuery`, `ExperienceSearchData`, `experience-filters.js`, `resources/views/site/experiences/index.blade.php` | 🚧 Built; needs final filter/sort/mobile QA |
| Shared experience card style | `resources/css/experience-card.css`, `partials/card.blade.php`, `partials/card-grid.blade.php` | 🚧 Built; needs one final consistency pass across home/listing/recommendations |
| Experience detail page | `ExperienceDetailService`, `ExperienceDetailData`, gallery/detail partials, itinerary partials, booking panel | 🚧 Built; needs full browser QA and content accuracy pass |
| Checkout session flow | `CheckoutSession`, checkout actions, checkout routes, activity/contact/payment views | 🚧 Built; needs end-to-end smoke test and copy polish |
| Pickup map step | `checkout-pickup-map.js`, activity checkout view/request/action | ✅ Built |
| Contact auth during checkout | checkout login/register requests/actions/routes | ✅ Built; verify production auth edge cases |
| Admin dashboard | admin routes/controllers/views/CSS | 🚧 Broad coverage exists; needs CRUD and validation QA pass |
| Agency dashboard | agency routes/controllers/views/CSS | 🚧 Broad coverage exists; needs CRUD and validation QA pass |
| Tourist dashboard | bookings/reviews/wishlist routes and views | 🚧 Built baseline; needs profile page and cancellation UX |
| Reviews | review model/action/event/listener/routes/views/tests | 🚧 Built; needs policy and moderation edge-case tests |
| Payouts | payout model/actions/controllers/views | 🚧 Built; needs accounting/export polish and tests |
| Wishlist | model/controller/views/js/test | 🚧 Built although originally V2; needs product decision to keep in MVP or remove |
| Newsletter/inquiry | subscriber model/controller/admin view/mail/request | 🚧 Built; needs localization and notification QA |
| Emails | booking/agency/review/newsletter mail classes and views | 🚧 Built; needs localization and queue smoke test |
| Landing content admin CRUD | categories/destinations/landmarks/faqs/reels/testimonials routes/views/requests | 🚧 Built; needs media/validation QA |
| Tests | Feature tests for checkout, booking, listing, detail, wishlist, landing, dashboards | 🚧 Good baseline; full suite still needs to pass after final audit |

### Real Remaining MVP Work

| Priority | Item | Why it matters |
|----------|------|----------------|
| P0 | Run a full smoke test: homepage → listing → detail → checkout → user dashboard → admin confirm → agency booking | Confirms the main marketplace flow works after recent UI/backend changes |
| P0 | Add/finish authorization policies: `ExperiencePolicy`, `AgencyPolicy`, `BookingPolicy`, `ReviewPolicy` | Prevents cross-agency/admin/tourist access bugs |
| P0 | Verify all admin and agency CRUD forms against Form Requests and Actions | User-facing CRUD was reported unstable before; forms must match backend required fields |
| P0 | Confirm no Eloquent/DB queries remain in Blade layouts/partials | Project architecture forbids queries in Blade; there are likely dashboard/nav counters to move into composers/services |
| P0 | Full test/build gate: `php artisan test`, `npm run build`, `php artisan view:cache`, `php artisan route:list` | Required before considering the MVP stable |
| P1 | Cancellation policy and refund record logic | Needed for real cancellation UX and admin/user clarity |
| P1 | Tourist cancellation UX | Users need to cancel allowed bookings within policy window |
| P1 | Availability service consolidation | Keeps availability reads consistent and avoids duplicated date/seat logic |
| P1 | Experience detail final browser QA | Gallery/video, itinerary map, sticky booking panel, package switching, mobile behavior |
| P1 | Experiences listing final browser QA | Filters, sorting, sticky toolbar, card consistency, mobile/tablet behavior |
| P1 | Dashboard visual consistency pass | Admin, agency, and tourist should share the same TourstiCa design language |
| P1 | Review moderation/rating recalculation tests | Protects rating_avg/reviews_count integrity |
| P1 | Payout mark-paid tests and accounting view polish | Needed before manual payouts are used operationally |
| P2 | SEO: canonical, hreflang, JSON-LD, sitemap, robots.txt | Required before production/public indexing |
| P2 | Performance audit: eager loading, image lazy loading, production cache commands | Prevents slow pages and N+1 regressions |
| P2 | Deployment runbook and smoke checklist | Needed for reliable hosting updates |

### Recommended Execution Order

1. Run the full automated test/build gate and document failures.
2. Fix P0 access/security issues, especially policies and Blade queries.
3. QA and polish the public conversion flow: homepage, listing, detail, checkout.
4. QA and polish dashboard CRUD for admin and agency.
5. Finish cancellation/refund UX and tests.
6. Finish SEO/performance/deployment tasks.

---

## Already Done

| # | Item |
|---|------|
| ✅ | Full documentation analysis (DOCX read, all modules extracted) |
| ✅ | Architecture plan written (stack, patterns, folder structure) |
| ✅ | MVP scope locked (no Filament, no RTL, payments in V2) |
| ✅ | Languages confirmed: EN / FR / NL |
| ✅ | Booking types defined: Group + Private |
| ✅ | Admin experience creation modes defined (on behalf of agency / platform-owned) |
| ✅ | Database schema finalized (11 tables + indexes + soft deletes) |
| ✅ | Full folder structure defined |
| ✅ | `docs/ARCHITECTURE.md` written |
| ✅ | `docs/TODO.md` written |
| ✅ | MVP payment scope confirmed: no online payment now; admin confirms/cancels pending bookings manually |
| ✅ | Core MVP migrations added for experiences, media, availability, bookings, payouts, and platform-owned agencies |
| ✅ | Core domain models added: Agency, Experience, ExperienceMedia, Availability, Booking, Payout |
| ✅ | Core booking logic added: group/private pricing, seat locking, pending booking creation, admin confirmation payout creation |
| ✅ | Experience media storage confirmed: uploads go under `public/uploads`, videos support URL or file upload |
| ✅ | Baseline tests passing after core domain update |

---

## PHASE 1 — Foundation
**Goal:** Working Laravel 13 skeleton, layouts, locale system, roles, packages, CI

| # | Task | Why | How |
|---|------|-----|-----|
| ✅ 1.1 | Laravel 13 install + Git init | Project base | Laravel 13 app exists and Git repository initialized |
| ✅ 1.2 | Tailwind CSS + Alpine.js via Vite | Styling + interactivity | Vite, Tailwind, Alpine modules exist |
| ✅ 1.3 | Install `spatie/laravel-permission` | Roles: admin / travel_agency / user | Package installed, migrations published, middleware aliases registered |
| ✅ 1.4 | Install `spatie/laravel-translatable` | EN/FR/NL JSON content columns | Package installed and used in core models |
| ✅ 1.5 | Install `spatie/laravel-medialibrary` | Photo uploads + WebP conversions | Package installed and media migration exists |
| ✅ 1.6 | Install `spatie/laravel-activitylog` | Admin audit trail | Package installed and activity log migration exists |
| ✅ 1.7 | Install `spatie/laravel-data` | Typed DTOs | Package installed |
| ✅ 1.8 | Install `spatie/laravel-model-states` | Booking state machine | Package installed. State classes wiring is later booking work |
| ✅ 1.9 | Install `spatie/laravel-settings` | Platform config (commission, email, etc.) | Package installed |
| ✅ 1.10 | Install `brick/money` | Safe decimal money math — no float bugs | Package installed and used by `CommissionCalculator` |
| ✅ 1.11 | Install `intervention/image` | Re-encode uploads, strip EXIF | Package installed |
| ✅ 1.12 | Install `laravel/horizon` | Queue monitoring dashboard | Package installed |
| ✅ 1.13 | Install `laravel/scout` + Meilisearch driver | Fast filtered experience search | Scout and `meilisearch/meilisearch-php` installed |
| ✅ 1.14 | Install `spatie/laravel-sitemap` | Auto-generated sitemap.xml | Package installed |
| ✅ 1.15 | Install `spatie/laravel-sluggable` | Per-locale URL slugs | Package installed |
| ✅ 1.16 | Install `spatie/laravel-csp` | Content Security Policy headers | Package installed and `config/csp.php` published |
| 🚧 1.17 | Install `pestphp/pest` | Testing | Blocked: current Laravel 13 setup conflicts with available Pest Laravel plugin / `laravel/pao`. PHPUnit remains active |
| ✅ 1.18 | Install Larastan | Static analysis | `larastan/larastan` installed, `phpstan.neon` added. Local PHP 8.5 run exits without output; CI runs PHP 8.3 |
| ✅ 1.19 | Install `laravel/pint` | Code style formatter | Package installed |
| ✅ 1.20 | Install `barryvdh/laravel-debugbar` | Dev query inspection | Package installed for local development |
| ✅ 1.21 | Install `laravel/pennant` | Feature flags for V2 rollout | Package installed, config and migration published, migration applied locally |
| ✅ 1.22 | Create `config/locales.php` | Defines EN/FR/NL | Supported locales: `en`, `fr`, `nl`; all LTR. `lang/nl` exists and old `lang/pl` files have been removed |
| ✅ 1.23 | Create `config/booking.php` | Seat hold TTL, expiry hours, booking types | Constants used by booking engine |
| ✅ 1.24 | Create `config/commission.php` | Default commission rate | Used by seeder and settings |
| ✅ 1.25 | Create `config/seo.php` | Default meta title, description, OG image | Fallback for pages without specific SEO |
| ✅ 1.26 | Create `config/media.php` | Public upload paths, image/video limits, allowed MIME types | Images and uploaded videos are stored under `public/uploads/...`; external video URLs are also supported |
| ✅ 1.27 | Create lang files skeleton for EN/FR/NL | All UI strings in 3 languages | Done: `ui`, `auth`, `booking`, `validation`, `emails`, `seo` |
| ✅ 1.28 | Build `layouts/site.blade.php` | Shell for all public tourist pages | Navbar + footer + head partials + `@yield('content')` |
| ✅ 1.29 | Build `layouts/agency.blade.php` | Shell for agency dashboard | Sidebar + topbar + `@yield('content')` |
| ✅ 1.30 | Build `layouts/admin.blade.php` | Shell for admin panel | Sidebar + topbar + `@yield('content')` |
| ✅ 1.31 | Build `layouts/auth.blade.php` | Shell for login/register pages | Centered card layout, minimal |
| ✅ 1.32 | Build `layouts/_email.blade.php` | Base for all transactional emails | Inline-CSS ready, logo, footer |
| ✅ 1.33 | Build head partials | SEO head tags shared across all pages | `partials/head/{meta, og, schema, analytics}.blade.php` reads `$seo` |
| ✅ 1.34 | Build `partials/navbar.blade.php` | Top nav — language switcher, auth state, categories | Injected by `NavbarComposer` |
| ✅ 1.35 | Build `partials/footer.blade.php` | Site footer | Basic localized footer shell |
| ✅ 1.36 | Build `partials/flash.blade.php` | Session flash messages | Success / error / warning / info alerts |
| ✅ 1.37 | Build UI component library | Reusable primitives used everywhere | `components/ui/{button, card, modal, input, select, textarea, badge, alert, tab, pagination, empty-state, stat-card}.blade.php` |
| ✅ 1.38 | Create `SetLocale` middleware | Reads `/{locale}` URL prefix, sets app locale | Validates against `config/locales.php`, falls back to `en`; registered in Laravel 13 bootstrap middleware aliases |
| ✅ 1.39 | Create `SeoComposer` ViewComposer | Injects default `$seo` object into every view | Registered in `ViewServiceProvider` — default from `config/seo.php` |
| ✅ 1.40 | Create `NavbarComposer` ViewComposer | Injects categories + current locale into navbar | Registered in `ViewServiceProvider` |
| 🚧 1.41 | Write all database migrations | Core MVP tables in correct FK dependency order | Done: users extension, agencies, platform flag, experiences, experience_media, availabilities, bookings, payouts. Pending: reviews, cancellation_policies. Payments deferred to V2 |
| ✅ 1.42 | Seed roles (admin, travel_agency, user) | Required before any user is created | `RolesSeeder` using Spatie Permission |
| ✅ 1.43 | Seed admin user | First admin account | `AdminUserSeeder` uses `ADMIN_EMAIL`, `ADMIN_NAME`, and required `ADMIN_PASSWORD` from env |
| ✅ 1.44 | Seed platform agency | Owner of platform-created experiences | `PlatformAgencySeeder` — `is_platform = true`, linked to platform owner user |
| ✅ 1.45 | Setup GitHub Actions CI | Auto lint + test + build on every push | `.github/workflows/ci.yml` — Pint, Larastan, PHPUnit, Vite build |

---

## PHASE 2 — Authentication
**Goal:** Tourist register/login, agency register, guest checkout path, protected routes

| # | Task | Why | How |
|---|------|-----|-----|
| ✅ 2.1 | Tourist registration form | Tourists create accounts | Breeze register exists, captures preferred language, and assigns `user` role |
| ✅ 2.2 | Login + logout | Session authentication | Breeze session authentication exists |
| 🔲 2.3 | Email verification | Confirmed identity before actions | Laravel `MustVerifyEmail` — verify before booking |
| 🔲 2.4 | Password reset flow | Account recovery | Laravel built-in password broker + forgot/reset Blade pages |
| 🚧 2.5 | Agency registration form | Agency submits profile for approval | Creates user + agency in one DB transaction — owner, email, password, agency name, city, phone, description. Logo pending |
| ✅ 2.6 | Agency pending state screen | No dashboard access while pending | Blade page shown after agency registration |
| ✅ 2.7 | `EnsureAgencyApproved` middleware | Block pending/suspended agencies from dashboard | Checks `auth()->user()->agency->status` — redirects to pending screen |
| ✅ 2.8 | `EnsureUserNotBanned` middleware | Block banned tourists | Checks `auth()->user()->status === 'banned'`, logs the user out, and redirects to login |
| 🚧 2.9 | Language switcher in Navbar | EN/FR/NL toggle | Navbar links to localized homepage. Same-page redirect still pending |
| 🚧 2.10 | Auth Blade pages | Login, register, forgot-password, reset-password | Breeze auth pages exist. Tourist register has preferred language; full site-auth redesign pending |

---

## PHASE 3 — Agency & Experience Module
**Goal:** Agency creates and manages experiences, availability, profile. Admin creates experiences too.

| # | Task | Why | How |
|---|------|-----|-----|
| ✅ 3.1 | `Agency` model | Core agency entity | Eloquent, `HasTranslations`, soft delete, `scopePlatform()`, relationships |
| ✅ 3.2 | `Experience` model | Core listing entity | JSON translatable fields, `price_per_person`, `private_price` (nullable), scopes, relationships |
| ✅ 3.3 | `ExperienceMedia` model | Photos + videos attached to experience | `sort_order`, type enum (image/video), `source_type` enum (upload/url), public URL helper |
| ✅ 3.4 | `Availability` model | Date/time slots with seat counts | Unique constraint `(experience_id, date, time_slot)` |
| 🔲 3.5 | `CancellationPolicy` model | Per-experience refund rules | Belongs to experience, translatable description |
| 🔲 3.6 | `AvailabilityService` | Returns open slots for a given date | Respects private-locked slots, does NOT use cache |
| ✅ 3.7 | `ExperienceFormData` DTO | Typed input for experience create/update | Includes `private_price` (nullable), `agency_id`, `created_by` |
| ✅ 3.8 | `CreateExperienceAction` | Single action for agency + admin creation | Takes `ExperienceFormData`, handles both callers — same code path |
| ✅ 3.9 | `UpdateExperienceAction` | Update experience details | Admin can update all experiences; agency can update own experiences only |
| 🚧 3.10 | `PublishExperienceAction` / `UnpublishExperienceAction` | Toggle listing visibility | Publish/unpublish actions exist and require at least one image before publish. Cancellation policy validation pending |
| ✅ 3.11 | `AttachMediaAction` | Save uploaded photos, uploaded videos, and video URLs | Handles `sort_order`, public upload paths, image/video limits from `config/media.php` |
| 🔲 3.12 | `ExperiencePolicy` | Access control | Agency: own experiences only. Admin: all experiences |
| 🔲 3.13 | `AgencyPolicy` | Access control | Agency: own profile only. Admin: all agencies |
| 🔲 3.14 | Agency dashboard home | KPIs + recent bookings + pending actions | Stats bar, last 5 bookings, alerts for: draft experiences, slots with 0 seats, upcoming in 48h |
| 🚧 3.15 | Experience list page (agency) | See all agency's listings | Basic table exists: title, city, status, group/private prices, edit, publish/unpublish. Detail view pending |
| 🚧 3.16 | Experience create/edit form | Full listing creation with all sections | Create/edit form exists for admin and agency: multilingual title/description, pricing (group + optional private), location, inclusions/exclusions. Itinerary/cancellation policy pending |
| 🚧 3.17 | Media upload on experience form | Multiple photos + uploaded video + video URL | Create form supports multiple images, uploaded video, and video URL; uploads saved under `public/uploads`. Drag-reorder pending |
| 🔲 3.18 | Publish/Unpublish toggle | Agency controls listing visibility | Button on experience list, confirmation dialog |
| 🚧 3.19 | Availability calendar page | Monthly view of slot status | Basic admin/agency availability list exists. Calendar color view pending |
| 🚧 3.20 | Add/edit/delete slot form | Create availability slots | Admin/agency can create, update, delete single slots with date, time, max seats, active toggle. Bulk date entry pending |
| ✅ 3.21 | Slot protection: booked slots cannot be deleted | Data integrity — bookings reference slot | `DeleteAvailabilityAction` blocks deletion when bookings exist |
| ✅ 3.22 | Agency bookings list | See all bookings for own experiences | MVP table: ID, tourist name, experience, date, participants, booking type, total, status. Query is scoped to authenticated agency only |
| 🔲 3.23 | Agency booking detail view | Full info for one booking | Tourist contact, notes, timeline, payment status |
| 🔲 3.24 | Agency payouts page | Track pending + paid payouts | Pending list + history + summary stats (total earned, pending, paid) |
| 🔲 3.25 | Agency reviews page | See all reviews for own experiences | List + rating summary per experience |
| 🔲 3.26 | Agency profile edit page | Update public-facing agency info | Name, description (EN/FR/NL), city, phone, logo — commission rate is read-only |
| ✅ 3.27 | Admin: create experience on behalf of agency | Admin fills form, picks agency | Agency dropdown in admin experience form — uses `CreateExperienceAction` |
| ✅ 3.28 | Admin: create platform-owned experience | No payout generated | Platform agency is included in admin agency dropdown and uses the same `CreateExperienceAction` |

---

## PHASE 4 — Public Marketplace
**Goal:** Tourists browse, search, filter, and view full experience details

| # | Task | Why | How |
|---|------|-----|-----|
| 🔲 4.1 | `ExperienceCardViewModel` | Pre-shaped data for experience card | Title (current locale), group price, private price available flag, rating_avg, reviews_count, city, duration, thumbnail URL |
| 🔲 4.2 | `<x-site.experience-card>` component | Reused in home, search, similar sections | Shows: photo, title, city, rating stars, price from X, duration badge, private badge if available |
| 🔲 4.3 | Homepage | Inspiration + first conversion point | Hero (full-screen video/image + search bar), categories strip, top experiences grid, trending section, how-it-works, agency spotlight (3 cards) |
| 🔲 4.4 | `ExperienceFilterData` DTO | Typed search parameters | city, category, price_min, price_max, duration_max, difficulty, sort, page |
| 🔲 4.5 | `ExperienceSearchRepository` | Abstracts DB vs Meilisearch | Returns paginated `ExperienceCardViewModel` collection from either source |
| 🚧 4.6 | Search + filter page | Core browse experience | Basic public experiences listing exists for published experiences. Filters/sort/map pending |
| 🔲 4.7 | `<x-site.filter-sidebar>` component | Filter controls | Price range, duration, difficulty, category, guide language, date — GET form submission |
| 🔲 4.8 | Sort options | Price, rating, newest, most booked | Query scope per sort type |
| 🔲 4.9 | Map view toggle | Show experiences on a map | Leaflet.js — JSON endpoint returning lat/lng + price pins per experience |
| 🔲 4.10 | No-results empty state | Friendly message when nothing found | `<x-ui.empty-state>` — suggests broadening filters, trying nearby city |
| ✅ 4.11 | "Load more" pagination | Smoother results browsing | AJAX append of next page into `.xp-grid`, replaces numbered pagination links |
| ✅ 4.12 | "Top sights in {City}" + "Go beyond {City}" sections | Trust + discovery on listing page | `Landmark` model + admin CRUD (mirrors `Destination`); activity counts from live `Experience::published()` query; "Go beyond" reuses experience cards from other cities |
| ✅ | Grid / List view toggle on listing page | Browsing preference | `.xp-view-toggle` + Alpine `view` state persisted in localStorage; CSS-only reflow of `.xc` card into a row for list mode, same card markup/data |
| 🔲 | Deal/discount data model | `card-grid.blade.php` already renders `xc-orig` (struck-through original price), `xc-disc` (−X% badge), and `xc-deal` ("Deal ends in…") — but `experiences` has no `original_price` / `deal_ends_at` columns. Needs: migration + admin UI to set a real discount + expiry, or remove this UI until backed by real data (no fabricated pricing) |
| 🔲 | `CancellationPolicy` model + `AvailabilityService` + `ExperiencePolicy` | Still pending (see 3.5 / 3.6 / 3.12) — blocks real cancellation refund logic and centralized oversell-safe availability reads |
| 🔲 4.11 | `ExperienceDetailViewModel` | Full data for detail page | All experience fields + agency card data + media list + reviews (first page) + similar experiences + availability state |
| 🔲 4.12 | Experience detail page | Most important page — converts visitors | Media gallery, title row, key specs bar, story/description, itinerary, inclusions/exclusions, cancellation policy, agency card, availability picker, sticky booking bar, reviews, similar experiences |
| 🔲 4.13 | `<x-site.sticky-booking-bar>` | Always-visible booking CTA | Fixed top on desktop / bottom on mobile — price from + Book Now button |
| 🔲 4.14 | `<x-site.agency-card>` | Inline agency trust signal on detail page | Logo, name, city, rating, languages, contact button |
| 🔲 4.15 | Media gallery + lightbox | Photo browsing | Alpine.js lightbox, thumbnail strip, WebP via Spatie conversions |
| 🔲 4.16 | Reviews section on detail page | Social proof | Rating breakdown bar + individual review cards + paginated "Load more" |
| 🔲 4.17 | Similar experiences section | Reduce bounce when slot unavailable | 4 cards from same city or category — reuses `<x-site.experience-card>` |

---

## PHASE 5 — Booking Engine
**Goal:** 2-step checkout, group + private logic, seat locking, admin confirms manually (no payment yet)

| # | Task | Why | How |
|---|------|-----|-----|
| ✅ 5.1 | `Booking` model | Core transaction entity | Status column supports pending → confirmed → completed / cancelled. Model states package wiring is still pending |
| ✅ 5.2 | `SeatReservationService` | Prevent oversell | `lockForUpdate()` on availability, group increments requested seats, private sets `booked_seats = max_seats`, cancel releases seats |
| ✅ 5.3 | `CommissionCalculator` | Correct money math per booking type | Group: `price_per_person × participants`. Private: `private_price`. Uses `brick/money`. Returns unit, total, commission, agency amount |
| ✅ 5.4 | `BookingData` DTO | Typed input to `CreateBookingAction` | booking_type, participants_count, availability_id, guest_name, guest_email, guest_phone, special_notes, user_id (nullable) |
| ✅ 5.5 | `CreateBookingAction` | Core booking creation | Wraps `SeatReservationService` + `CommissionCalculator` in `DB::transaction()`, snapshots all prices, creates booking as `pending`; no payment in MVP |
| ✅ 5.6 | Booking type selector | Group vs Private selector UI | Basic localized select in the tourist booking form; private option is hidden when `private_price` is null. Live price UI can be enhanced later |
| ✅ 5.7 | Availability picker | Date + slot + participants selection | Basic localized slot select using active future availabilities; participant count is validated against slot seats and experience max group size |
| ✅ 5.8 | Checkout Step 1 — Select | Date, slot, booking type, participants, price summary | MVP combines selection + guest details in `Site\BookingController@create` to avoid payment/session complexity |
| ✅ 5.9 | Checkout Step 2 — Details | Guest info form | `Site\BookingController@store` validates name, email, phone, notes and creates booking (`status: pending`) with no payment |
| ✅ 5.10 | Booking confirmation page | Reassurance after booking | Shows pending status, experience, agency, date/time, booking type, participants, total price, and no-payment MVP message |
| 🔲 5.11 | `BookingPolicy` | Authorization | Only booking owner or admin can cancel |
| 🔲 5.12 | Tourist cancellation flow | Cancel within policy window | `BookingCancellationController` — checks hours_before policy, calls `CancelBookingAction` |
| ✅ 5.13 | `CancelBookingAction` | Cancel + release seats | Decrements `booked_seats` (private resets to 0), marks booking cancelled. Refund calculation/payment processing deferred |
| 🔲 5.14 | `RefundCalculator` | Hours-before window logic | Compares now vs trip datetime vs policy `hours_before` → returns refund amount (record only — actual refund in V2) |
| 🔲 5.15 | `CompleteBookingAction` + `CompleteBookingsCommand` | Auto-complete confirmed bookings after trip | Cron every hour — confirmed bookings where `availability.date < today` → `completed` |
| 🔲 5.16 | `CreatePayoutOnBookingConfirmed` listener | Auto-create payout record | Fires on `BookingConfirmed` event — skips if `agency.is_platform = true` |
| ✅ 5.17 | Admin manually confirm booking | MVP has no payment — admin confirms | Admin bookings list → "Confirm" button → `ConfirmBookingAction` → payout auto-created |
| ✅ 5.18 | `ConfirmBookingAction` | Set status to confirmed | Updates pending booking to confirmed and creates payout for non-platform agency bookings |
| ✅ 5.19 | Test: group booking flow | Validate group price + seat decrement | PHPUnit feature test |
| ✅ 5.20 | Test: private booking flow | Validate flat price + full slot lock | PHPUnit feature test |
| 🔲 5.21 | Test: concurrent seat booking | No oversell under parallel requests | Pest test — N concurrent requests on slot with M < N seats |
| ✅ 5.22 | Test: `CommissionCalculator` | Group + private price calculation | PHPUnit unit test |
| 🔲 5.23 | Test: `RefundCalculator` | Before/after window + admin override | Pest unit test |

---

## PHASE 6 — Tourist Dashboard & Reviews
**Goal:** Tourist manages bookings, writes reviews, updates profile

| # | Task | Why | How |
|---|------|-----|-----|
| 🔲 6.1 | Dashboard — My Trips | Upcoming + past bookings | `TouristDashboardController@trips` — status badge, trip countdown, cancel button (if within window) |
| 🔲 6.2 | Booking detail view | Full info for a single booking | Date, experience name, meeting point, agency contact, participants, booking type, total price, status timeline |
| 🔲 6.3 | Dashboard — My Reviews | Submitted + pending reviews | `TouristDashboardController@reviews` — "Write Review" CTA for completed bookings without a review |
| 🔲 6.4 | Write review form | Post-trip rating + comment | `ReviewController@create/store` — validates: booking must be `completed`, one review per booking |
| 🔲 6.5 | `SubmitReviewAction` | Save review + update denormalized rating | Creates review, fires `ReviewSubmitted`, updates `rating_avg` + `reviews_count` on experience row |
| 🔲 6.6 | `ReviewPolicy` | Authorization | Only booking owner, after `status = completed`, no existing review on this booking |
| 🔲 6.7 | `UpdateExperienceRatingListener` | Keep denormalized rating up to date | Fires on `ReviewSubmitted` and review hide/show — recalculates AVG + COUNT from visible reviews |
| 🔲 6.8 | Dashboard — Profile Settings | Edit name, phone, language preference, password | `TouristDashboardController@profile` + `ProfileUpdateRequest` |
| 🔲 6.9 | `SendReviewInvitesCommand` | Prompt tourists to review 24h after trip | Daily cron — finds completed bookings with no review and trip_date = yesterday, queues `ReviewInviteMail` |

---

## PHASE 7 — Admin Panel (Custom Blade)
**Goal:** Admin manages everything from a clean Blade dashboard — no Filament

| # | Task | Why | How |
|---|------|-----|-----|
| 🔲 7.1 | Admin layout + sidebar navigation | Shell for all admin pages | `layouts/admin.blade.php` — sidebar links to all sections, active state highlighting |
| 🔲 7.2 | Admin dashboard home | Platform health at a glance | KPI widgets: total bookings, GMV, commission earned, active agencies, pending approvals, pending payouts |
| 🚧 7.3 | Agencies list | See all agencies + filter by status | Basic admin agencies table exists with approve action. Filters/reject/suspend pending |
| 🔲 7.4 | Agency detail page | Deep inspect before decisions | Full profile, all experiences, booking history, payout history, commission change log |
| 🚧 7.5 | `ApproveAgencyAction` | Activate agency | Sets `status = active` and assigns `travel_agency` role. Activity log/event pending |
| 🔲 7.6 | `RejectAgencyAction` | Reject with reason | Sets `status = rejected`, requires reason field, fires `AgencyRejected` |
| 🔲 7.7 | `SuspendAgencyAction` | Suspend + hide all experiences | Sets `status = suspended`, unpublishes all their experiences, notifies agency |
| 🔲 7.8 | `UpdateCommissionRateAction` | Change agency commission | Updates `commission_rate`, logs old → new rate in activity_log — no effect on existing bookings |
| 🔲 7.9 | Users list | See all tourist accounts | Table: name, email, registration date, bookings count, total spent, status — ban/restore actions |
| 🔲 7.10 | User detail page | Full tourist history | Booking history, review history, account status log |
| 🔲 7.11 | Ban / restore user | Remove bad actors | Requires reason — `status = banned` or `status = active` — logs to activity_log |
| 🚧 7.12 | Experiences moderation list | Platform-wide experience overview | Basic admin experiences table exists. Force-unpublish/moderation actions pending |
| 🔲 7.13 | Force unpublish experience | Content control | Sets `status = unpublished`, reason required, agency notified by email |
| 🔲 7.14 | Admin create experience (2 modes) | Add experience from admin | Form with agency selector dropdown (includes "Platform — no payout" option) — uses `CreateExperienceAction` |
| ✅ 7.15 | Bookings list | See all platform bookings | MVP table: ID, tourist, experience, agency, date, booking type, total, status. Status filter is included; agency/date filters can be added later |
| ✅ 7.16 | Booking detail + manual confirm | Admin confirms pending bookings | MVP confirm is available from the bookings list and calls `ConfirmBookingAction`; detail page can be added later |
| 🔲 7.17 | Admin force cancel booking | Cancel any booking + full refund | `CancelBookingAction` with admin override — always full refund regardless of policy |
| 🔲 7.18 | Payouts — pending list | Money owed to agencies | Grouped by agency, subtotal per agency, days waiting, "Mark as Paid" button |
| 🔲 7.19 | `MarkPayoutPaidAction` | Record manual bank transfer | Sets `status = paid`, `transferred_at`, `transferred_by`, notes — logs to activity_log |
| 🔲 7.20 | Payouts — history + CSV export | Audit trail for accounting | All paid payouts, filter by agency + date range, CSV download |
| 🔲 7.21 | Reviews moderation | Hide/show reviews | Table + `is_visible` toggle — record preserved, never deleted, fires `UpdateExperienceRatingListener` |
| 🔲 7.22 | Platform analytics page | Revenue + growth overview | Commission by day/week/month (chart), top experiences, top agencies, tourist stats — all from cached aggregates |

---

## PHASE 8 — Email Notifications
**Goal:** Right email, right language, right time — for every key event

| # | Task | Why | How |
|---|------|-----|-----|
| 🔲 8.1 | `BookingConfirmedMail` | Tourist + agency notified on confirm | Tourist gets: reference, date, meeting point, agency contact. Agency gets: tourist name, experience, date, participants |
| 🔲 8.2 | `BookingCancelledMail` | Both parties notified on cancel | Includes refund amount (record only — actual transfer V2) |
| 🔲 8.3 | `AgencyApprovedMail` | Agency knows they're activated | Welcome message + link to dashboard + getting started tips |
| 🔲 8.4 | `AgencyRejectedMail` | Agency knows why they were rejected | Admin's rejection reason included |
| 🔲 8.5 | `ReviewInviteMail` | Prompt tourist to leave review | Sent 24h after trip date by cron — direct link to write-review page |
| 🔲 8.6 | All emails respect `preferred_lang` | EN/FR/NL per user | `Mail::send()` sets `App::setLocale($user->preferred_lang)` before rendering template |

---

## PHASE 9 — SEO & Multilingual Dutch
**Goal:** Ranked on Google, correct hreflang, every public page has proper meta

| # | Task | Why | How |
|---|------|-----|-----|
| 🔲 9.1 | Per-locale slugs on experiences | Clean SEO URLs: `/en/experiences/desert-sunset-tour` | `spatie/laravel-sluggable` translatable variant — slug generated from experience title per locale |
| 🔲 9.2 | Locale-prefixed route groups | All public routes under `/{locale}/` | Route::prefix('{locale}')->middleware('set.locale') |
| 🔲 9.3 | `hreflang` alternate links | Tell Google about EN/FR/NL versions | Generated in `partials/head/meta.blade.php` from route helper |
| 🔲 9.4 | JSON-LD: `Organization` | Homepage brand signal | Name, URL, logo — in schema partial |
| 🔲 9.5 | JSON-LD: `Product` + `AggregateRating` | Experience ranking boost | Price range, rating, review count, availability — on detail page |
| 🔲 9.6 | JSON-LD: `BreadcrumbList` | Navigation in search results | Home > City > Category > Experience — on all inner pages |
| 🔲 9.7 | Sitemap generation | All experiences indexed | `spatie/laravel-sitemap` — daily cron, separate sitemaps for experiences + static |
| 🔲 9.8 | `robots.txt` | Block admin/agency/checkout from crawlers | Disallow `/admin/*`, `/agency/*`, `/checkout/*` |
| 🔲 9.9 | Canonical tags | No duplicate content across locales | `$seo->canonical` set per page, rendered in meta partial |
| 🔲 9.10 | OG + Twitter cards | Social sharing preview | `og:title`, `og:description`, `og:image` from `$seo` ViewModel — on all public pages |

---

## PHASE 10 — Performance & Optimization
**Goal:** Sub-3 second page loads, zero N+1, Redis caching working

| # | Task | Why | How |
|---|------|-----|-----|
| 🔲 10.1 | Redis cache for home sections | Fast homepage repeat loads | `Cache::remember('home:featured', 900, ...)` — busted on experience publish/unpublish |
| 🔲 10.2 | Redis cache for experience cards | Fast search results | TTL 1h, busted on `experience.saved` event via model observer |
| 🔲 10.3 | Denormalized `rating_avg` + `reviews_count` | No AVG() on every search query | Updated by `UpdateExperienceRatingListener` — eliminates GROUP BY on hot paths |
| 🔲 10.4 | Eager loading audit | Find and fix all N+1 queries | Use Laravel Debugbar in dev — check every list page and detail page |
| 🔲 10.5 | WebP image conversions | Smaller file sizes, faster load | Spatie Media Library `thumb` (400×300), `card` (800×600), `hero` (1600×900) — generated on upload via `media` queue |
| 🔲 10.6 | `loading="lazy"` on all below-fold images | Faster LCP score | Applied in `<x-site.experience-card>` and gallery |
| 🔲 10.7 | Production optimizations | Faster app bootstrap | `route:cache`, `config:cache`, `view:cache`, `event:cache` in deploy script |
| 🔲 10.8 | Cloudflare CDN for static assets | Global fast delivery | R2 disk in production, Cloudflare proxies all media |

---

## PHASE 11 — Testing & Deployment
**Goal:** Stable, tested, deployed to staging then production

| # | Task | Why | How |
|---|------|-----|-----|
| 🔲 11.1 | Full feature test suite | Prevent regressions | Group booking, private booking, seat lock, cancellation, review rules, admin confirm, payout creation |
| 🔲 11.2 | Staging server setup | Catch environment-specific issues | Forge staging server, `.env.staging`, same stack as production |
| 🔲 11.3 | Staging deploy | Validate everything end to end | Deploy + run migrations + seed + smoke test |
| 🔲 11.4 | Manual smoke test checklist | Golden path validation | Register → browse → book (group) → book (private) → admin confirm → review → payout mark paid |
| 🔲 11.5 | Production deploy | Go live | Zero-downtime via symlink switch, `migrate --force`, `optimize`, Horizon start |
| 🔲 11.6 | Ops runbook | Guide for common incidents | How to: manually refund, force cancel booking, reset payout, ban user, reindex Meilisearch |

---

## V2 — After MVP (Next Sprint)

| # | Task | Why |
|---|------|-----|
| 🔲 V2.1 | Payment gateway integration (Stripe/PayPal/CMI) | Actual money collection |
| 🔲 V2.2 | Refund processing via gateway API | Real refunds on cancellation |
| 🔲 V2.3 | Agency public profile page `/agencies/{slug}` | Standalone trust page for tourists |
| 🔲 V2.4 | Tourist wishlist | Save experiences between sessions |
| 🔲 V2.5 | Tourist receipt download (PDF) | Booking confirmation document |
| 🔲 V2.6 | Agency analytics dashboard | Views, bookings, revenue, review trends |

---

## V3 — Future

| # | Task |
|---|------|
| 🔲 V3.1 | Smart recommendations engine |
| 🔲 V3.2 | Mobile app (iOS + Android) |
| 🔲 V3.3 | Multi-country expansion |
| 🔲 V3.4 | Real-time notifications (Laravel Reverb) |

---

## Summary

| Phase | Tasks | Status |
|-------|-------|--------|
| Phase 1 — Foundation | 45 tasks | 🔲 Pending |
| Phase 2 — Auth | 10 tasks | 🔲 Pending |
| Phase 3 — Agency & Experience | 28 tasks | 🔲 Pending |
| Phase 4 — Public Marketplace | 17 tasks | 🔲 Pending |
| Phase 5 — Booking Engine | 23 tasks | 🔲 Pending |
| Phase 6 — Tourist Dashboard & Reviews | 9 tasks | 🔲 Pending |
| Phase 7 — Admin Panel | 22 tasks | 🔲 Pending |
| Phase 8 — Email Notifications | 6 tasks | 🔲 Pending |
| Phase 9 — SEO & i18n | 10 tasks | 🔲 Pending |
| Phase 10 — Performance | 8 tasks | 🔲 Pending |
| Phase 11 — Testing & Deployment | 6 tasks | 🔲 Pending |
| **Total MVP** | **184 tasks** | |
| V2 | 6 tasks | Deferred |
| V3 | 4 tasks | Deferred |
