# Tourstica — Morocco Tourism Platform
> Project snapshot: 2026-07-01 | Stack: Laravel 13 · Blade · Tailwind · Alpine.js · MySQL 8 · Redis

---

## 1. What Is This Project

A two-sided booking marketplace connecting international tourists with vetted Moroccan travel agencies.

**Target users:** Americans, Europeans (EN/FR/NL), Gulf travelers — unfamiliar with Morocco, need trust signals at every step.

**Three roles:**

| Role | Who | Access |
|------|-----|--------|
| `admin` | Platform operator | Full control — agencies, bookings, experiences, payouts, content |
| `travel_agency` | Agency owner | Own dashboard — experiences, availability, bookings, payouts, reviews |
| `user` | Tourist | Public site + own trip history + reviews |

**Languages:** English · French · Dutch (EN/FR/NL). All LTR. No Arabic. No RTL. No Polish.

---

## 2. Stack (Locked — Not Negotiable)

| Layer | Technology |
|-------|-----------|
| Backend | Laravel 13, PHP 8.3+ |
| Frontend | Blade + Tailwind CSS + Alpine.js |
| Database | MySQL 8 |
| Cache / Queue | Redis + Laravel Horizon |
| Search | Laravel Scout + Meilisearch |
| Media | `public/uploads` (dev) / Cloudflare R2 (prod) · Spatie Media Library |
| Money | `brick/money` — never PHP floats |
| Testing | PHPUnit |
| Code style | Laravel Pint |
| Static analysis | Larastan |

**No React. No Vue. No Inertia. No Livewire. No Filament. No SPA.**

---

## 3. Domain Models — Existence Status

| Model | File | DB Migration | Status |
|-------|------|-------------|--------|
| `User` | `app/Models/User.php` | ✅ | ✅ Built |
| `Agency` | `app/Domain/Agency/Models/Agency.php` | ✅ | ✅ Built |
| `Experience` | `app/Domain/Experience/Models/Experience.php` | ✅ | ✅ Built |
| `ExperienceMedia` | `app/Domain/Experience/Models/ExperienceMedia.php` | ✅ | ✅ Built |
| `ExperienceOption` | `app/Domain/Experience/Models/ExperienceOption.php` | ✅ | ✅ Built |
| `ExperienceOptionPrice` | `app/Domain/Experience/Models/ExperienceOptionPrice.php` | ✅ | ✅ Built |
| `ExperienceOptionLanguage` | `app/Domain/Experience/Models/ExperienceOptionLanguage.php` | ✅ | ✅ Built |
| `Availability` | `app/Domain/Experience/Models/Availability.php` | ✅ | ✅ Built |
| `CheckoutSession` | `app/Domain/Checkout/Models/CheckoutSession.php` | ✅ | ✅ Built (single-experience) |
| `Booking` | `app/Domain/Booking/Models/Booking.php` | ✅ | ✅ Built |
| `Payout` | `app/Domain/Payout/Models/Payout.php` | ✅ | ✅ Built |
| `Review` | `app/Domain/Review/Models/Review.php` | ❌ | ❌ Missing — folders exist, no files |
| `CancellationPolicy` | — | ❌ | ❌ Missing — no model, no migration |
| `Cart` | — | ❌ | ❌ New — needed for multi-trip checkout |
| `CartItem` | — | ❌ | ❌ New — needed for multi-trip checkout |
| `ExperienceCategory` | `app/Domain/Landing/Models/ExperienceCategory.php` | ✅ | ✅ Built |
| `Destination` | `app/Domain/Landing/Models/Destination.php` | ✅ | ✅ Built |
| `Landmark` | `app/Domain/Landing/Models/Landmark.php` | ✅ | ✅ Built |
| `LandingFaq` | `app/Domain/Landing/Models/LandingFaq.php` | ✅ | ✅ Built |
| `TravelReel` | `app/Domain/Landing/Models/TravelReel.php` | ✅ | ✅ Built |
| `Testimonial` | `app/Domain/Landing/Models/Testimonial.php` | ✅ | ✅ Built |

---

## 4. Booking Flow (Multi-Trip Cart)

> **This is the confirmed booking flow.** Replaces the old single-experience checkout.

### Step 1 — Browse & Add to Cart

Tourist browses experiences on the public marketplace.

For **each experience added to the cart**, they must configure:

| Field | Options |
|-------|---------|
| Package | Select from `ExperienceOption` list (e.g. "Standard Tour", "Premium With Lunch") |
| Date + Time | Select from active `Availability` slots for the chosen option |
| Participants | Number + type per ticket: `adults (18+)`, `children (3–17)`, `infants (0–2)` — priced separately via `ExperienceOptionPrice` |
| Booking Type | `group` (price per person) or `private` (flat price, locks entire slot) — shown only if `ExperienceOption.private_available = true` |

Tourist can add multiple experiences to the same cart.

### Step 2 — Pickup Location

After the cart is configured, tourist sets pickup info (applies to all items in the cart that have `pickup_enabled = true`):

- `pickup_status`: `hotel` / `riad` / `address` / `none`
- `pickup_address`: free text
- `pickup_lat` / `pickup_lng`: optional coordinates

### Step 3 — Contact Details

Tourist fills in:
- First name, last name
- Email
- Phone (optional)
- Country
- Special requests (optional)

### Step 4 — Review & Confirm

Tourist sees full cart summary:
- Each experience: name, date, option, participants breakdown, subtotal
- Grand total (sum of all items)
- No payment collected in MVP — admin confirms manually

Submitting the form:
1. `ConvertCartToBookingsAction` runs inside a single `DB::transaction()`
2. For each `CartItem`: locks the availability row with `lockForUpdate()`, decrements held seats → increments booked seats, creates one `Booking` record
3. All bookings share `contact_*` fields from the cart
4. Cart is marked `completed`
5. Tourist lands on confirmation page showing all bookings with reference numbers

### What This Requires (New Models Needed)

```
Cart
  ├─ id, uuid
  ├─ user_id (nullable — guests use session)
  ├─ session_id
  ├─ locale, currency
  ├─ contact_first_name, contact_last_name, contact_email, contact_phone, contact_country
  ├─ pickup_status, pickup_address, pickup_lat, pickup_lng
  ├─ special_requests
  ├─ status (active | completed | expired)
  ├─ reserved_until
  └─ timestamps

CartItem
  ├─ id
  ├─ cart_id → Cart
  ├─ experience_id → Experience
  ├─ experience_option_id → ExperienceOption
  ├─ availability_id → Availability
  ├─ booking_type (group | private)
  ├─ participants JSON {adult: 2, child: 1, infant: 0}
  ├─ participants_count
  ├─ charged_seats
  ├─ price_snapshot JSON (full quote at time of adding)
  └─ timestamps
```

### Actions Required

| Action | Status |
|--------|--------|
| `CreateCartAction` | ❌ New |
| `AddCartItemAction` | ❌ New |
| `RemoveCartItemAction` | ❌ New |
| `UpdateCartItemAction` | ❌ New |
| `SetCartPickupAction` | ❌ New |
| `SetCartContactAction` | ❌ New |
| `ConvertCartToBookingsAction` | ❌ New |
| `ExpireCartsCommand` | ❌ New (hourly cron) |
| `ExperienceQuoteService` | ✅ Built — reused per CartItem |
| `SeatReservationService` | ✅ Built — called per CartItem in transaction |

> **Migration note:** `CheckoutSession` table stays for backward compatibility but new flow uses `carts` + `cart_items`. Old single-experience checkout routes can be deprecated after the new cart is live.

---

## 5. Experience Pricing Structure

```
Experience
  └── ExperienceOption (package — e.g. "3h Cooking Class", "Full Day Tour")
        ├── ExperienceOptionPrice × N (per participant type)
        │     participant_type: adult | child | infant
        │     min_age / max_age
        │     price (MAD)
        │     original_price (MAD, nullable — for strike-through deals)
        ├── ExperienceOptionLanguage × N (languages this option is available in)
        └── Availability × N (date + time_slot + max_seats)
              ├── experience_option_id (nullable — slot can be for one option only, or shared)
              └── booked_seats + held_seats
```

**Price resolution per booking:**
- `group`: sum across participant types × counts, using `ExperienceOptionPrice` per type
- `private`: `Experience.private_price` (flat), ignores participant count for pricing

---

## 6. Feature Areas — Full Status

### 6.1 Foundation & Auth

| Feature | Status | Notes |
|---------|--------|-------|
| Laravel 13 install, Vite, Tailwind, Alpine.js | ✅ Done | |
| Spatie packages (permission, translatable, medialibrary, activity, data) | ✅ Done | |
| `brick/money` | ✅ Done | |
| `config/locales.php` (EN/FR/NL) | ✅ Done | Old TODO says PL — fixed to NL |
| `config/booking.php`, `config/commission.php`, `config/seo.php` | ✅ Done | |
| Database migrations (users, agencies, experiences, availability, bookings, payouts) | ✅ Done | |
| Seeders: roles, admin user, platform agency | ✅ Done | |
| Lang files: EN, FR | ✅ Done | NL skeleton pending |
| Public layout (`layouts/site.blade.php`) | ✅ Done | |
| Agency layout (`layouts/agency.blade.php`) | ✅ Done | Redesigned with `ag-*` CSS system |
| Admin layout (`layouts/admin.blade.php`) | 🚧 In Progress | Old layout exists — needs redesign with `adm-*` CSS |
| Auth layout | ✅ Done | |
| Navbar, footer, flash partials | ✅ Done | |
| UI component library | ✅ Done | button, card, modal, input, select, badge, alert, pagination, empty-state, stat-card |
| `SetLocale` middleware | ✅ Done | |
| `SeoComposer`, `NavbarComposer` | ✅ Done | |
| Tourist register + login + logout | ✅ Done | |
| Agency register + pending screen | ✅ Done | |
| Email verification | 🔲 Pending | |
| Password reset | 🔲 Pending | |
| Post-login redirect by role | 🔲 Pending | Bug: all roles go to same route after login |
| Language switcher | 🚧 In Progress | Links exist — same-page redirect pending |
| Dutch (NL) lang files | 🔲 Pending | |

### 6.2 Agency Dashboard

| Feature | Status | Notes |
|---------|--------|-------|
| Agency dashboard home (KPIs, recent bookings, trends, slots) | ✅ Done | Modern `ag-*` CSS design |
| Experience list (agency) | ✅ Done | Basic table |
| Experience create form | ✅ Done | All core fields |
| Experience edit form | ✅ Done | |
| Media upload (images + video URL + video file) | ✅ Done | |
| Publish / Unpublish experience | ✅ Done | Requires ≥1 image |
| Availability calendar + slot management | 🚧 In Progress | List view done; calendar color view pending |
| Add / edit / delete slot | ✅ Done | |
| Bookings list (agency-scoped) | ✅ Done | |
| Booking detail view (agency) | 🔲 Pending | |
| Payouts page | 🔲 Pending | |
| Reviews page | 🔲 Pending | |
| Agency profile edit | 🔲 Pending | |
| `ExperiencePolicy` | 🔲 Pending | |
| `AgencyPolicy` | 🔲 Pending | |
| `CancellationPolicy` model + CRUD | 🔲 Pending | Blocks publish validation |

### 6.3 Admin Panel

> **Design rule:** All admin pages use the new `adm-*` CSS system. Old basic layout (`layouts/admin.blade.php`) to be replaced.

#### Admin Dashboard Home
| Feature | Status | Notes |
|---------|--------|-------|
| Stats: agencies_total, agencies_pending, experiences_total/published, bookings_pending/confirmed | ✅ Logic done | View needs redesign |
| Stats: revenue_confirmed, payouts_pending | 🔲 Pending | Need to add to controller |
| 7-day booking trends (created + confirmed bar charts) | ✅ Logic done | View needs redesign |
| Recent bookings widget | ✅ Logic done | View needs redesign |
| Pending agencies widget (incl. aged >3 days) | ✅ Logic done | View needs redesign |
| Almost-full slots widget | ✅ Logic done | View needs redesign |

#### Admin — Agencies
| Feature | Status | Notes |
|---------|--------|-------|
| Agencies list (paginated) | ✅ Done | Needs redesign |
| Agency create (platform agency) | ✅ Done | |
| Agency approve | ✅ Done | `ApproveAgencyAction` |
| Agency edit / update (commission, city, status) | 🔲 Pending | Route + controller method + view |
| Agency suspend / reactivate | 🔲 Pending | `SuspendAgencyAction` + `ReactivateAgencyAction` |
| Agency reject with reason | 🔲 Pending | `RejectAgencyAction` |
| Agency detail/show page | 🔲 Pending | Full profile + experiences + bookings + payouts |
| Update commission rate | 🔲 Pending | `UpdateCommissionRateAction` + activity log |

#### Admin — Experiences
| Feature | Status | Notes |
|---------|--------|-------|
| Experiences list (all agencies) | ✅ Done | Needs redesign |
| Experience create (on behalf of agency or platform) | ✅ Done | |
| Experience edit | ✅ Done | |
| Publish / Unpublish | ✅ Done | |
| Force unpublish with reason | 🔲 Pending | |
| Experience detail/show | 🔲 Pending | |
| ExperienceOptions management | ✅ Done | index, store, update, destroy routes |

#### Admin — Availability
| Feature | Status | Notes |
|---------|--------|-------|
| Availability list (all experiences) | ✅ Done | Needs redesign |
| Add / edit / delete slot | ✅ Done | |
| Filter by experience / agency | 🔲 Pending | |

#### Admin — Bookings
| Feature | Status | Notes |
|---------|--------|-------|
| Bookings list (all, with filters) | ✅ Done | status + payment_status filters |
| Manual confirm | ✅ Done | `ConfirmBookingAction` |
| Update payment status | ✅ Done | |
| Booking detail/show page | 🔲 Pending | |
| Admin cancel (always full refund) | 🔲 Pending | `CancelBookingAction` exists — route + controller method pending |

#### Admin — Payouts
| Feature | Status | Notes |
|---------|--------|-------|
| Payout model + migration | ✅ Done | status: pending/paid |
| `CreatePayoutFromBookingAction` | ✅ Done | Auto-runs on booking confirm |
| Payouts list (grouped by agency) | 🔲 Pending | Controller + route + view — all missing |
| `MarkPayoutPaidAction` | 🔲 Pending | Sets status=paid, transferred_at, transferred_by |
| Payout history / CSV export | 🔲 Pending | V2 scope |

#### Admin — Reviews
| Feature | Status | Notes |
|---------|--------|-------|
| `Review` model | ❌ Missing | Domain folder exists, no PHP files |
| `reviews` migration | ❌ Missing | |
| Reviews list | 🔲 Pending | Controller + route + view |
| Hide/show review toggle (`is_visible`) | 🔲 Pending | |
| `UpdateExperienceRatingListener` | 🔲 Pending | Recalculates rating_avg + reviews_count |

#### Admin — Users
| Feature | Status | Notes |
|---------|--------|-------|
| `User` model | ✅ Done | |
| Users list | 🔲 Pending | Controller + route + view — all missing |
| User detail / booking history | 🔲 Pending | |
| Ban / restore user | 🔲 Pending | `status` column exists |

#### Admin — Landing Content
| Feature | Status | Notes |
|---------|--------|-------|
| Routes for all 6 content types | ✅ Done | categories, destinations, landmarks, faqs, reels, testimonials |
| `LandingContentController` (all 6) | ✅ Done | Returns JSON — designed as API |
| Blade admin UI for 6 content types | 🔲 Pending | Pages call the JSON API via Alpine.js fetch |

### 6.4 Public Marketplace

| Feature | Status | Notes |
|---------|--------|-------|
| Homepage (hero, categories, top experiences, how-it-works) | 🔲 Pending | |
| Experience list / search page | 🚧 In Progress | Basic listing exists; filters/sort/map pending |
| Grid / List view toggle | ✅ Done | |
| "Top sights" + "Go beyond" sections | ✅ Done | |
| Experience detail page | 🔲 Pending | Most important page |
| Media gallery + lightbox | 🔲 Pending | |
| Reviews section on detail page | 🔲 Pending | |
| Similar experiences section | 🔲 Pending | |
| `ExperienceCardViewModel` | 🔲 Pending | |
| `ExperienceDetailViewModel` | 🔲 Pending | |

### 6.5 Cart & Checkout (Multi-Trip)

| Feature | Status | Notes |
|---------|--------|-------|
| `Cart` model + migration | 🔲 Pending | New — see Section 4 |
| `CartItem` model + migration | 🔲 Pending | New — see Section 4 |
| `CreateCartAction` | 🔲 Pending | |
| `AddCartItemAction` | 🔲 Pending | |
| `RemoveCartItemAction` | 🔲 Pending | |
| `UpdateCartItemAction` | 🔲 Pending | |
| `SetCartPickupAction` | 🔲 Pending | |
| `SetCartContactAction` | 🔲 Pending | |
| `ConvertCartToBookingsAction` | 🔲 Pending | Creates N bookings in one transaction |
| `ExpireCartsCommand` | 🔲 Pending | Hourly cron |
| Cart UI — Add to cart button on experience page | 🔲 Pending | |
| Cart UI — Mini cart (item count in navbar) | 🔲 Pending | |
| Cart UI — Cart page (review all items) | 🔲 Pending | |
| Checkout Step 1 — Pickup | 🔲 Pending | Shared across all items |
| Checkout Step 2 — Contact details | 🔲 Pending | |
| Checkout Step 3 — Review & Confirm | 🔲 Pending | |
| Booking confirmation page (multi) | 🔲 Pending | Shows all N bookings |
| `ExperienceQuoteService` | ✅ Done | Reused per CartItem |
| `SeatReservationService` | ✅ Done | Called per CartItem inside transaction |
| Old `CheckoutSession` flow | 🔲 Deprecate | Replace with Cart flow |

### 6.6 Tourist Dashboard

| Feature | Status | Notes |
|---------|--------|-------|
| My Trips (upcoming + past) | 🔲 Pending | |
| Trip detail view | 🔲 Pending | |
| Cancel booking (within policy) | 🔲 Pending | `CancelBookingAction` exists |
| My Reviews | 🔲 Pending | |
| Write review (post-trip only) | 🔲 Pending | |
| Profile settings | 🔲 Pending | |

### 6.7 Review System

| Feature | Status | Notes |
|---------|--------|-------|
| `Review` model + migration | 🔲 Pending | Columns: booking_id, user_id, experience_id, rating, comment, is_visible, replied_at |
| Unique constraint: one review per booking | 🔲 Pending | DB unique index on booking_id |
| `SubmitReviewAction` | 🔲 Pending | Validates: booking.status=completed, no existing review |
| `ReviewPolicy` | 🔲 Pending | |
| `UpdateExperienceRatingListener` | 🔲 Pending | |
| Reviews on experience detail page | 🔲 Pending | |
| Admin review moderation | 🔲 Pending | |

### 6.8 Cancellation Policy

| Feature | Status | Notes |
|---------|--------|-------|
| `CancellationPolicy` model + migration | 🔲 Pending | Per experience: hours_before + refund_percentage |
| `RefundCalculator` service | 🔲 Pending | Now vs trip datetime vs policy window |
| Tourist cancel flow | 🔲 Pending | |
| Admin cancel (always 100% refund) | 🔲 Pending | Route + controller method |
| `CompleteBookingAction` + cron | 🔲 Pending | Auto-complete confirmed after trip date |

### 6.9 Email Notifications

| Feature | Status | Notes |
|---------|--------|-------|
| `BookingConfirmedMail` | 🔲 Pending | Tourist + agency |
| `BookingCancelledMail` | 🔲 Pending | Tourist + agency |
| `AgencyApprovedMail` | 🔲 Pending | |
| `AgencyRejectedMail` | 🔲 Pending | |
| `ReviewInviteMail` | 🔲 Pending | 24h after trip |
| All emails respect `preferred_lang` | 🔲 Pending | |

### 6.10 SEO

| Feature | Status | Notes |
|---------|--------|-------|
| Per-locale slugs | 🔲 Pending | `spatie/laravel-sluggable` |
| Locale-prefixed routes `/{locale}/...` | 🔲 Pending | |
| `hreflang` alternates | 🔲 Pending | |
| JSON-LD: Organization, Product, BreadcrumbList | 🔲 Pending | |
| Sitemap | 🔲 Pending | `spatie/laravel-sitemap` — daily cron |
| `robots.txt` | 🔲 Pending | Block admin/agency/checkout |
| OG + Twitter cards | 🔲 Pending | |

---

## 7. Admin Panel — Page-by-Page Build Plan

> Uses `adm-*` CSS design system. Dark navy sidebar (`#1E3A5F`), gold accents, DM Serif Display + DM Sans.

### Files to Create / Replace

| File | Action |
|------|--------|
| `resources/css/admin-dashboard.css` | Create new |
| `resources/views/layouts/admin.blade.php` | Replace (keep route names, rebuild HTML) |
| `resources/views/admin/dashboard.blade.php` | Replace |
| `resources/views/admin/dashboard/partials/stat-cards.blade.php` | Create |
| `resources/views/admin/dashboard/partials/recent-bookings.blade.php` | Create |
| `resources/views/admin/dashboard/partials/pending-agencies.blade.php` | Create |
| `resources/views/admin/dashboard/partials/almost-full-slots.blade.php` | Create |
| `resources/views/admin/agencies/index.blade.php` | Redesign |
| `resources/views/admin/agencies/show.blade.php` | Create |
| `resources/views/admin/agencies/edit.blade.php` | Create |
| `resources/views/admin/bookings/index.blade.php` | Redesign |
| `resources/views/admin/bookings/show.blade.php` | Create |
| `resources/views/admin/experiences/index.blade.php` | Redesign |
| `resources/views/admin/availability/index.blade.php` | Redesign |
| `resources/views/admin/payouts/index.blade.php` | Create |
| `resources/views/admin/reviews/index.blade.php` | Create |
| `resources/views/admin/users/index.blade.php` | Create |
| `resources/views/admin/landing/categories.blade.php` | Create (Alpine.js → JSON API) |
| `resources/views/admin/landing/destinations.blade.php` | Create |
| `resources/views/admin/landing/landmarks.blade.php` | Create |
| `resources/views/admin/landing/faqs.blade.php` | Create |
| `resources/views/admin/landing/reels.blade.php` | Create |
| `resources/views/admin/landing/testimonials.blade.php` | Create |

### Missing Controllers to Create

| Controller | Methods needed |
|------------|---------------|
| `Admin\PayoutController` | `index`, `markPaid` |
| `Admin\ReviewController` | `index`, `toggleVisibility` |
| `Admin\UserController` | `index`, `show`, `ban`, `restore` |
| `Admin\AgencyController` (extend) | add `show`, `edit`, `update`, `suspend`, `reactivate`, `reject` |
| `Admin\BookingController` (extend) | add `show`, `cancel` |

### Missing Routes to Add

```php
// Payouts
Route::get('/payouts', [PayoutController::class, 'index'])->name('payouts.index');
Route::post('/payouts/{payout}/mark-paid', [PayoutController::class, 'markPaid'])->name('payouts.mark-paid');

// Reviews
Route::get('/reviews', [ReviewController::class, 'index'])->name('reviews.index');
Route::post('/reviews/{review}/toggle-visibility', [ReviewController::class, 'toggleVisibility'])->name('reviews.toggle-visibility');

// Users
Route::get('/users', [UserController::class, 'index'])->name('users.index');
Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
Route::post('/users/{user}/ban', [UserController::class, 'ban'])->name('users.ban');
Route::post('/users/{user}/restore', [UserController::class, 'restore'])->name('users.restore');

// Agencies (add to existing group)
Route::get('/agencies/{agency}', [AgencyController::class, 'show'])->name('agencies.show');
Route::get('/agencies/{agency}/edit', [AgencyController::class, 'edit'])->name('agencies.edit');
Route::patch('/agencies/{agency}', [AgencyController::class, 'update'])->name('agencies.update');
Route::post('/agencies/{agency}/suspend', [AgencyController::class, 'suspend'])->name('agencies.suspend');
Route::post('/agencies/{agency}/reactivate', [AgencyController::class, 'reactivate'])->name('agencies.reactivate');
Route::post('/agencies/{agency}/reject', [AgencyController::class, 'reject'])->name('agencies.reject');

// Bookings (add to existing group)
Route::get('/bookings/{booking}', [BookingController::class, 'show'])->name('bookings.show');
Route::post('/bookings/{booking}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');
```

---

## 8. Sidebar Navigation — Final List

```
● Tourstica (logo)

○ Dashboard
○ Agencies          [badge: pending count]
○ Experiences       [badge: draft count]
○ Availability
○ Bookings          [badge: pending count]
○ Payouts           [badge: pending count]
○ Reviews           [badge: hidden count]
○ Users

▼ Content
  ○ Categories
  ○ Destinations
  ○ Landmarks
  ○ FAQs
  ○ Reels
  ○ Testimonials

— — —
⎋ Log out
```

---

## 9. What Is NOT in MVP

| Feature | Milestone |
|---------|-----------|
| Online payments (Stripe / PayPal / CMI) | V2 |
| Actual refund processing via gateway | V2 |
| Agency public profile page (standalone) | V2 |
| Tourist wishlist | V2 |
| Tourist receipt PDF | V2 |
| Payout CSV export | V2 |
| AI trip planning | V3 |
| Multilingual SEO content hub | V3 |
| Affiliate monetization | V3 |
| Smart recommendations | V3 |
| Mobile app | V3 |
| Multi-country expansion | V3 |

---

## 10. Build Priority Order (Current)

1. **Admin CSS + layout redesign** — `adm-*` system, replace `layouts/admin.blade.php`
2. **Admin dashboard home** — redesign with new partials
3. **Admin agencies** — add show, edit, suspend, reject pages
4. **Admin bookings** — add show, cancel
5. **Admin payouts** — full build from scratch
6. **Review model + migration** — unblocks reviews everywhere
7. **Admin reviews** — list + toggle visibility
8. **Admin users** — list + show + ban/restore
9. **Admin landing content** — 6 Blade pages with Alpine.js CRUD
10. **Post-login redirect by role** — fix routing bug
11. **Cart + CartItem models** — foundation for new checkout flow
12. **Cart actions** — Add, Remove, Update, Pickup, Contact, Convert
13. **Cart UI** — add-to-cart, mini cart, cart page, checkout steps
14. **Experience detail page** — public-facing most important page
15. **Homepage** — hero, categories, top experiences
16. **Review system** — model, actions, tourist flow, admin moderation
17. **Cancellation policy** — model, calculator, tourist cancel flow
18. **Email notifications** — all transactional emails
19. **SEO** — locale routes, hreflang, JSON-LD, sitemap
