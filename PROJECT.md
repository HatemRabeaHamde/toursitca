# Morocco Tourism Platform — Project File
> Living reference for all files, their purpose, and what will be built.
> Scope: **MVP only** (no V2/V3 features).
> Stack: Laravel 11 · Blade · Tailwind CSS · Alpine.js (lightweight interactivity) · MySQL · Redis

---

## What Is Built / What Will Be Built

### Legend
- ✅ Done
- 🔨 In progress
- ⏳ Pending
- ❌ Removed from scope (V2/V3)

---

## Root Files

| File | Status | Purpose |
|------|--------|---------|
| `.env` | ✅ | Environment config. DB=MySQL, currency=MAD, app name set |
| `composer.json` | ✅ | PHP dependencies (Laravel 11, Breeze, Spatie packages, Scout, Horizon) |
| `package.json` | ✅ | JS dependencies (Tailwind, Alpine.js + plugins, Vite) |
| `vite.config.js` | ✅ | Asset bundler config |
| `tailwind.config.js` | ✅ | Tailwind with RTL plugin, brand colors (orange/sand palette), Arabic font, custom components |
| `PROJECT.md` | ✅ | This file |

---

## config/

| File | Status | Purpose |
|------|--------|---------|
| `config/app.php` | ✅ | Default Laravel (locales configured) |
| `config/booking.php` | ✅ | Seat-hold TTL (24h), lock timeout, max participants, default cancellation policy |
| `config/payment.php` | ✅ | Gateway abstraction: Stripe / PayPal / CMI. Switch via `PAYMENT_GATEWAY` env var |
| `config/commission.php` | ✅ | Default commission rate (15%), min/max bounds |
| `config/locales.php` | ✅ | Supported locales: en / ar / fr. RTL list. Native names + flags |
| `config/seo.php` | ✅ | Default title, description, OG image, organization schema data |
| `config/media.php` | ✅ | Disk, max file size, image conversion sizes (thumb/card/hero), max 15 per experience |
| `config/permission.php` | ✅ | Published by spatie/laravel-permission |

---

## resources/css/

| File | Status | Purpose |
|------|--------|---------|
| `resources/css/app.css` | ✅ | Tailwind entry. Global base layer (RTL font switch, body), component layer (btn-*, input, card, badge classes) |

---

## resources/js/

| File | Status | Purpose |
|------|--------|---------|
| `resources/js/app.js` | ✅ | Alpine init + plugin registration (intersect, focus). Registers all Alpine.data() components |
| `resources/js/modules/search-filters.js` | ✅ | Alpine component: filter sidebar open/close, form submit, reset |
| `resources/js/modules/availability-picker.js` | ✅ | Alpine component: date pick → fetch time slots → participant counter → running total |
| `resources/js/modules/checkout.js` | ✅ | Alpine component: 3-step checkout wizard (step 1/2/3 navigation) |
| `resources/js/modules/media-gallery.js` | ✅ | Alpine component: gallery carousel + lightbox (prev/next/select) |

---

## database/migrations/

| File | Status | Purpose |
|------|--------|---------|
| `0001_01_01_000000_create_users_table.php` | ✅ | Default Laravel users (email, password, email_verified_at) |
| `0001_01_01_000001_create_cache_table.php` | ✅ | Cache table |
| `0001_01_01_000002_create_jobs_table.php` | ✅ | Queue jobs table |
| `2026_05_13_161613_create_media_table.php` | ✅ | Spatie Media Library |
| `2026_05_13_161613_create_permission_tables.php` | ✅ | Spatie Permission (roles, model_has_roles, etc.) |
| `2026_05_13_161614_create_activity_log_table.php` | ✅ | Spatie Activitylog (admin audit trail) |
| `..._extend_users_table.php` | ⏳ | Add: phone, avatar, preferred_lang, status (active/banned), soft deletes |
| `..._create_agencies_table.php` | ⏳ | id, user_id (FK), name, slug, description, logo, commission_rate, status (pending/active/suspended), city, phone, languages (json), timestamps, soft deletes |
| `..._create_agency_bank_accounts_table.php` | ⏳ | id, agency_id, account_holder, iban, swift, bank_name. Required for payout transfers |
| `..._create_experiences_table.php` | ⏳ | id, agency_id, title (json), description (json), category, price, duration_hours, max_group_size, difficulty, location_city, lat/lng, meeting_point, inclusions (json), exclusions (json), status (draft/published/unpublished), thumbnail, slug (json), timestamps, soft deletes |
| `..._create_experience_media_table.php` | ⏳ | id, experience_id, type (image/video), path, sort_order, timestamps |
| `..._create_cancellation_policies_table.php` | ⏳ | id, experience_id (unique), hours_before, refund_percentage, description (json), timestamps |
| `..._create_availabilities_table.php` | ⏳ | id, experience_id, date, time_slot, max_seats, booked_seats (default 0), is_active. Unique: (experience_id, date, time_slot). Indexes on (experience_id, date, is_active) |
| `..._create_bookings_table.php` | ⏳ | id, user_id, experience_id, availability_id, participants_count, unit_price (snapshot), total_price, commission_rate (snapshot), commission_amount, agency_amount, status (pending/confirmed/completed/cancelled), guest_name, guest_email, guest_phone, special_notes, cancelled_at, cancellation_reason, reserved_until (seat hold expiry), timestamps, soft deletes |
| `..._create_payments_table.php` | ⏳ | id, booking_id (unique), amount, currency, method (credit_card/paypal/cmi), status (pending/paid/refunded/failed), gateway_ref, paid_at, refunded_at, refund_amount, timestamps |
| `..._create_payouts_table.php` | ⏳ | id, agency_id, booking_id (unique), amount, status (pending/paid), notes, transferred_at, transferred_by (admin user_id FK), timestamps |
| `..._create_reviews_table.php` | ⏳ | id, booking_id (unique), user_id, experience_id, rating (1-5), comment, is_visible (default true), timestamps. Unique: booking_id |

---

## app/Domain/

### Agency/
| File | Status | Purpose |
|------|--------|---------|
| `Models/Agency.php` | ⏳ | Eloquent model. Relations: belongsTo(User), hasMany(Experience), hasMany(Payout). Spatie Translatable on description. Sluggable. Soft deletes |
| `DTOs/AgencyRegistrationData.php` | ⏳ | Spatie Data object: name, description, city, phone, logo |
| `Actions/ApproveAgencyAction.php` | ⏳ | Set status=active, assign travel_agency role, fire AgencyApproved event |
| `Actions/RejectAgencyAction.php` | ⏳ | Set status=pending (or rejected), notify with reason |
| `Actions/SuspendAgencyAction.php` | ⏳ | Set status=suspended, hide all experiences, notify agency |
| `Actions/UpdateCommissionRateAction.php` | ⏳ | Update commission_rate, log to activity_log |
| `Events/AgencyApproved.php` | ⏳ | Fired on approval. Listened by: send approval email |
| `Events/AgencyRejected.php` | ⏳ | Fired on rejection. Listened by: send rejection email with reason |
| `Policies/AgencyPolicy.php` | ⏳ | update/delete: only owner or admin |
| `ViewModels/AgencyProfileViewModel.php` | ⏳ | Prepares public agency data for admin view |

### Experience/
| File | Status | Purpose |
|------|--------|---------|
| `Models/Experience.php` | ⏳ | Eloquent. Translatable (title, description, inclusions, exclusions). HasMany(Availability, Review, ExperienceMedia). HasOne(CancellationPolicy). BelongsTo(Agency). Soft deletes. Scope: published() |
| `Models/ExperienceMedia.php` | ⏳ | BelongsTo(Experience). Ordered by sort_order |
| `Models/Availability.php` | ⏳ | BelongsTo(Experience). HasMany(Booking). Computed: available_seats = max_seats - booked_seats |
| `Models/CancellationPolicy.php` | ⏳ | BelongsTo(Experience). Translatable(description) |
| `DTOs/ExperienceFormData.php` | ⏳ | Validated input for create/update (title, description, category, price, etc.) |
| `DTOs/ExperienceFilterData.php` | ⏳ | Search filters: city, category, price_min, price_max, duration, difficulty, date |
| `Actions/CreateExperienceAction.php` | ⏳ | Create experience + default draft status |
| `Actions/UpdateExperienceAction.php` | ⏳ | Update + bust cache |
| `Actions/PublishExperienceAction.php` | ⏳ | Validate has media + cancellation policy → set status=published |
| `Actions/AttachMediaAction.php` | ⏳ | Handle media uploads, run conversions via queue |
| `Services/AvailabilityService.php` | ⏳ | getAvailableSlots(experience, date), hasSeats(availability, count) |
| `Repositories/ExperienceSearchRepository.php` | ⏳ | Wraps Meilisearch Scout query. Applies filters, returns paginated results |
| `ViewModels/ExperienceCardViewModel.php` | ⏳ | Data needed for a card: title, price, rating_avg, reviews_count, thumbnail_url, duration, agency_name |
| `ViewModels/ExperienceDetailViewModel.php` | ⏳ | Full detail page data: all card fields + itinerary, media, policy, agency, similar experiences |
| `ViewModels/SearchResultsViewModel.php` | ⏳ | Paginated cards + active filters + result count |
| `Policies/ExperiencePolicy.php` | ⏳ | create/update/delete: only agency owner or admin |

### Booking/
| File | Status | Purpose |
|------|--------|---------|
| `Models/Booking.php` | ⏳ | BelongsTo(User, Experience, Availability). HasOne(Payment, Payout, Review). Spatie model states. Soft deletes |
| `States/PendingState.php` | ⏳ | Initial state. Transitions → Confirmed, Cancelled |
| `States/ConfirmedState.php` | ⏳ | Payment received. Transitions → Completed, Cancelled |
| `States/CompletedState.php` | ⏳ | Trip date passed. Transitions → none |
| `States/CancelledState.php` | ⏳ | Terminal state |
| `DTOs/BookingData.php` | ⏳ | experience_id, availability_id, participants_count, guest_name, guest_email, guest_phone, special_notes |
| `Services/SeatReservationService.php` | ⏳ | DB transaction: lock availability row, check seats, increment booked_seats, create booking with reserved_until |
| `Services/CommissionCalculator.php` | ⏳ | Pure calculation: total_price, commission_amount, agency_amount using brick/money for precision |
| `Services/RefundCalculator.php` | ⏳ | Given booking + cancellation time: return refund amount based on policy hours/percentage |
| `Actions/CreateBookingAction.php` | ⏳ | Validate → SeatReservationService → CommissionCalculator → persist booking |
| `Actions/ConfirmBookingAction.php` | ⏳ | Set status=confirmed → fire BookingConfirmed event |
| `Actions/CancelBookingAction.php` | ⏳ | RefundCalculator → set status=cancelled → release seat → fire BookingCancelled |
| `Actions/CompleteBookingAction.php` | ⏳ | Set status=completed (cron or admin) |
| `Events/BookingConfirmed.php` | ⏳ | Listened by: CreatePayoutListener, send confirmation email |
| `Events/BookingCancelled.php` | ⏳ | Listened by: release seat, process refund, send cancellation email |
| `Events/BookingCompleted.php` | ⏳ | Listened by: send review invite email (24h delay) |
| `ViewModels/BookingSummaryViewModel.php` | ⏳ | Checkout confirmation data, email data |

### Payment/
| File | Status | Purpose |
|------|--------|---------|
| `Gateways/PaymentGatewayInterface.php` | ⏳ | Contract: initiate(booking, method): PaymentResult, handleWebhook(request): void |
| `Gateways/StripeGateway.php` | ⏳ | Implements PaymentGatewayInterface via Stripe Payment Intents |
| `Gateways/PaypalGateway.php` | ⏳ | Implements PaymentGatewayInterface via PayPal REST API |
| `Gateways/CmiGateway.php` | ⏳ | Implements PaymentGatewayInterface for Moroccan CMI gateway |
| `Models/Payment.php` | ⏳ | BelongsTo(Booking). status cast to enum |
| `DTOs/PaymentResult.php` | ⏳ | success: bool, gateway_ref: string, redirect_url: ?string |
| `Actions/InitiatePaymentAction.php` | ⏳ | Resolve gateway from config → call initiate() → store payment record |
| `Actions/RecordPaymentWebhookAction.php` | ⏳ | Verify webhook signature → update payment status → fire ConfirmBookingAction |

### Payout/
| File | Status | Purpose |
|------|--------|---------|
| `Models/Payout.php` | ⏳ | BelongsTo(Agency, Booking). Status: pending/paid |
| `Listeners/CreatePayoutOnBookingConfirmed.php` | ⏳ | Listens BookingConfirmed → auto-create payout record with agency_amount |
| `Actions/MarkPayoutPaidAction.php` | ⏳ | Admin action: set status=paid, transferred_at=now, transferred_by=admin id, notes |
| `ViewModels/PendingPayoutsViewModel.php` | ⏳ | Groups pending payouts by agency with subtotals |

### Review/
| File | Status | Purpose |
|------|--------|---------|
| `Models/Review.php` | ⏳ | BelongsTo(Booking, User, Experience). Scope: visible() |
| `Actions/SubmitReviewAction.php` | ⏳ | Verify booking.status=completed + no existing review → create + bust rating cache |
| `Policies/ReviewPolicy.php` | ⏳ | create: only booking owner. Booking must be completed |

### User/
| File | Status | Purpose |
|------|--------|---------|
| `Models/User.php` | ⏳ | HasOne(Agency). HasMany(Booking). HasMany(Review). Spatie HasRoles |

### Shared/
| File | Status | Purpose |
|------|--------|---------|
| `Shared/Money/Money.php` | ⏳ | Value object wrapping brick/money. format() → "1,000 MAD" |
| `Shared/Casts/MoneyCast.php` | ⏳ | Eloquent cast for money columns (decimal stored, Money returned) |
| `Shared/Concerns/HasMultilingual.php` | ⏳ | Trait: getTranslated(column, locale) helper on models |

---

## app/Http/

### Controllers/Site/ (Public tourist site)
| File | Status | Purpose |
|------|--------|---------|
| `HomeController.php` | ⏳ | Homepage: categories, top experiences (cached), trending by city |
| `ExperienceController.php` | ⏳ | show(): detail page with ExperienceDetailViewModel |
| `SearchController.php` | ⏳ | index(): search + filter using ExperienceSearchRepository, paginated |
| `BookingController.php` | ⏳ | create/store step 1+2: seat check, guest/user data. Returns checkout view |
| `PaymentController.php` | ⏳ | store(): initiate payment → redirect to gateway or confirmation |
| `ReviewController.php` | ⏳ | store(): submit review (auth required, booking must be completed) |
| `ExperienceSlotsController.php` | ⏳ | JSON endpoint: GET /experiences/{id}/slots?date=YYYY-MM-DD → available slots |
| `AuthController.php` | ⏳ | Delegates to Breeze (login, register, logout, verify email, reset password) |

### Controllers/Agency/ (Agency dashboard — auth + approved only)
| File | Status | Purpose |
|------|--------|---------|
| `DashboardController.php` | ⏳ | Home: today's bookings KPIs, recent bookings, pending alerts |
| `ExperienceController.php` | ⏳ | CRUD for agency's own experiences. Uses CreateExperienceAction, UpdateExperienceAction |
| `AvailabilityController.php` | ⏳ | Calendar view + slot management (create/update/delete slots) |
| `BookingController.php` | ⏳ | List bookings for agency experiences. Show detail. Export CSV |
| `PayoutController.php` | ⏳ | List pending + paid payouts. Summary stats |
| `ProfileController.php` | ⏳ | Edit public agency profile + account settings |

### Controllers/Admin/ (Admin panel — admin role only)
| File | Status | Purpose |
|------|--------|---------|
| `DashboardController.php` | ⏳ | Platform KPIs: bookings, GMV, commission, agencies, tourists. Revenue chart data |
| `AgencyController.php` | ⏳ | List, view, approve, reject, suspend, restore agencies. Set commission rate |
| `PayoutController.php` | ⏳ | Pending payouts grouped by agency. Mark as paid (single + bulk). Export CSV |
| `UserController.php` | ⏳ | List tourist accounts. View detail. Ban / restore |
| `ExperienceController.php` | ⏳ | Platform-wide list. Force-unpublish with reason |
| `ReviewController.php` | ⏳ | All reviews. Toggle visibility (hide/show). Filter by rating/flagged |
| `BookingController.php` | ⏳ | View all bookings. Force-cancel with full refund |

### Controllers/Webhooks/
| File | Status | Purpose |
|------|--------|---------|
| `StripeWebhookController.php` | ⏳ | Verify Stripe signature → RecordPaymentWebhookAction |
| `PaypalWebhookController.php` | ⏳ | Verify PayPal IPN → RecordPaymentWebhookAction |

### Middleware/
| File | Status | Purpose |
|------|--------|---------|
| `SetLocale.php` | ⏳ | Read locale from URL prefix → App::setLocale() → set dir attribute |
| `EnsureAgencyApproved.php` | ⏳ | Check agency.status=active. Redirect with message if pending/suspended |
| `DetectRtl.php` | ⏳ | Share $isRtl, $dir variables with all views |

### Requests/Site/
| File | Status | Purpose |
|------|--------|---------|
| `BookingStoreRequest.php` | ⏳ | Validate: experience_id, availability_id, participants_count, guest_name, guest_email |
| `ReviewStoreRequest.php` | ⏳ | Validate: rating (1-5), comment (optional). Authorize: booking owner, booking completed |

### Requests/Agency/
| File | Status | Purpose |
|------|--------|---------|
| `ExperienceStoreRequest.php` | ⏳ | Validate all experience fields. Multilingual title/description |
| `AvailabilityStoreRequest.php` | ⏳ | Validate date(s), time_slot, max_seats |

### ViewComposers/
| File | Status | Purpose |
|------|--------|---------|
| `NavbarComposer.php` | ⏳ | Share: $categories, $currentLocale, $locales, $authUser to all views |
| `SeoComposer.php` | ⏳ | Share: $seo (default Seo object) to all views |

---

## app/Seo/

| File | Status | Purpose |
|------|--------|---------|
| `Seo.php` | ⏳ | Value object: title, description, canonical, og_image, robots, schema (array). Builder methods |
| `SeoFactory.php` | ⏳ | Static helpers: forExperience(Experience), forHomepage(), forSearch(filters) |

---

## app/Mail/

| File | Status | Purpose |
|------|--------|---------|
| `BookingConfirmedMail.php` | ⏳ | Tourist: booking reference, date, meeting point, agency contact, cancellation steps |
| `BookingCancelledMail.php` | ⏳ | Tourist: booking reference, refund amount and timeline |
| `AgencyApprovedMail.php` | ⏳ | Agency: welcome, link to dashboard, getting started |
| `AgencyRejectedMail.php` | ⏳ | Agency: rejection reason |
| `ReviewInviteMail.php` | ⏳ | Tourist: rate your experience (sent 24h after trip) |

---

## app/Console/Commands/

| File | Status | Purpose |
|------|--------|---------|
| `ExpireUnpaidBookingsCommand.php` | ⏳ | Run every 5 min. Find pending bookings where reserved_until < now → CancelBookingAction |
| `CompleteBookingsCommand.php` | ⏳ | Run hourly. Find confirmed bookings where trip date passed → CompleteBookingAction |
| `SendReviewInvitesCommand.php` | ⏳ | Run daily 09:00. Find completed bookings 24h ago without a review → send ReviewInviteMail |

---

## resources/views/layouts/

| File | Status | Purpose |
|------|--------|---------|
| `site.blade.php` | ⏳ | Public site shell: head (SEO partials), navbar, flash, @yield('content'), footer. RTL-aware |
| `agency.blade.php` | ⏳ | Agency dashboard: sidebar nav (experiences, availability, bookings, payouts, profile) + main content area |
| `admin.blade.php` | ⏳ | Admin panel: top nav + sidebar (agencies, payouts, users, experiences, reviews, bookings, analytics) |
| `auth.blade.php` | ⏳ | Centered card layout for login/register/reset pages |

---

## resources/views/partials/

| File | Status | Purpose |
|------|--------|---------|
| `partials/head/meta.blade.php` | ⏳ | title, description, robots canonical from $seo |
| `partials/head/og.blade.php` | ⏳ | og:title, og:description, og:image, og:url, twitter card |
| `partials/head/schema.blade.php` | ⏳ | JSON-LD: Organization (home), Product+AggregateRating (experience), BreadcrumbList |
| `partials/head/analytics.blade.php` | ⏳ | GA/Plausible script (only in production) |
| `partials/navbar.blade.php` | ⏳ | Logo, nav links (Explore, How it works), locale switcher, auth links, mobile hamburger |
| `partials/footer.blade.php` | ⏳ | Nav links, social, language selector, copyright, policy links |
| `partials/flash.blade.php` | ⏳ | Session success/error/warning messages as dismissable alerts |

---

## resources/views/components/ui/ (Generic — used everywhere)

| File | Status | Purpose |
|------|--------|---------|
| `button.blade.php` | ⏳ | Props: variant (primary/secondary/ghost/danger), size, href, type, disabled |
| `input.blade.php` | ⏳ | Props: name, label, type, placeholder, required. Auto-renders $errors |
| `select.blade.php` | ⏳ | Props: name, label, options (array), selected. Renders $errors |
| `textarea.blade.php` | ⏳ | Props: name, label, rows, placeholder |
| `card.blade.php` | ⏳ | White rounded shadow wrapper. Slots: header, default, footer |
| `modal.blade.php` | ⏳ | Alpine-driven overlay modal. Props: id, title. Slots: default, footer |
| `badge.blade.php` | ⏳ | Props: color (green/amber/red/slate), text. For status labels |
| `alert.blade.php` | ⏳ | Props: type (success/error/warning/info), dismissable |
| `pagination.blade.php` | ⏳ | Tailwind-styled Laravel paginator |
| `empty-state.blade.php` | ⏳ | Props: icon, title, description, action (label+href). For no-results states |
| `stat-card.blade.php` | ⏳ | Dashboard KPI card: label, value, change %, icon |

---

## resources/views/components/site/ (Tourist-facing)

| File | Status | Purpose |
|------|--------|---------|
| `experience-card.blade.php` | ⏳ | Grid card: thumbnail, title, agency name, city, rating stars, price, duration, difficulty badge, "Book Now" link |
| `search-bar.blade.php` | ⏳ | Hero search: city autocomplete, category dropdown, date picker, Search button |
| `filter-sidebar.blade.php` | ⏳ | Price range, duration, difficulty, category, guide language checkboxes. Alpine for mobile open/close |
| `availability-picker.blade.php` | ⏳ | Date calendar → slot buttons → participant counter → running total. Alpine-driven |
| `sticky-booking-bar.blade.php` | ⏳ | Fixed bar (desktop top / mobile bottom): price, rating, "Book Now" button |
| `review-card.blade.php` | ⏳ | Reviewer name, date, star rating, comment |
| `itinerary-list.blade.php` | ⏳ | Step-by-step timeline (time + description) |
| `inclusions-table.blade.php` | ⏳ | Green checkmarks (included) + red X (excluded) table |
| `category-strip.blade.php` | ⏳ | Horizontal scroll: Desert, Food, Cultural, Mountain, Workshop, Adventure icon chips |
| `star-rating.blade.php` | ⏳ | Props: rating (decimal), max (5), size. Renders filled/half/empty stars |

---

## resources/views/site/ (Public pages — MVP)

| File | Status | Purpose |
|------|--------|---------|
| `home.blade.php` | ⏳ | Hero (video/image, search bar), categories strip, top experiences grid, how it works, agency spotlight |
| `search.blade.php` | ⏳ | Filter sidebar + results grid + sort + result count + empty state + map toggle placeholder |
| `experience/show.blade.php` | ⏳ | Full detail: media gallery, title row, key specs, story, itinerary, inclusions, policy, agency card, availability picker, reviews |
| `checkout/step1-review.blade.php` | ⏳ | Summary: experience, date, slot, participants, price breakdown. Confirm to proceed |
| `checkout/step2-details.blade.php` | ⏳ | Guest form: name, email, phone, special notes |
| `checkout/step3-payment.blade.php` | ⏳ | Payment method selector (Stripe card / PayPal). SSL badge. Price summary |
| `checkout/confirmation.blade.php` | ⏳ | Success: booking ref, details, meeting point, add to calendar, print receipt |
| `auth/login.blade.php` | ⏳ | Email + password. "Continue as guest" link. Forgot password |
| `auth/register.blade.php` | ⏳ | Name, email, password, preferred language. Email verification prompt |
| `auth/forgot-password.blade.php` | ⏳ | Enter email → receive reset link |

---

## resources/views/agency/ (Agency dashboard — MVP)

| File | Status | Purpose |
|------|--------|---------|
| `home.blade.php` | ⏳ | Stats bar (bookings today, earnings this month, pending payout, active experiences), recent bookings, pending actions, quick links |
| `experiences/index.blade.php` | ⏳ | Table: name, category, price, status, bookings count. Edit/View/Publish/Unpublish actions |
| `experiences/create.blade.php` | ⏳ | Multi-tab form: Basic Info, Location, Media, Itinerary, Inclusions, Cancellation Policy |
| `experiences/edit.blade.php` | ⏳ | Same form pre-filled |
| `availability/index.blade.php` | ⏳ | Monthly calendar view (green/grey/red dates) + slot list table + add slot form |
| `bookings/index.blade.php` | ⏳ | Filter by experience/date/status. Table: tourist, experience, date, participants, amount, status |
| `bookings/show.blade.php` | ⏳ | Full booking detail: tourist contact, special notes, payment status, booking timeline |
| `payouts/index.blade.php` | ⏳ | Tabs: Pending / History. Summary stats: total earned, total pending, total paid |
| `profile/edit.blade.php` | ⏳ | Agency public info (name, description, city, phone, logo, languages). Account settings |

---

## resources/views/admin/ (Admin panel — MVP)

| File | Status | Purpose |
|------|--------|---------|
| `dashboard.blade.php` | ⏳ | KPI cards (bookings, GMV, commission, agencies, tourists), pending actions bar, recent activity, revenue chart |
| `agencies/index.blade.php` | ⏳ | Table: name, city, status badge, commission rate, experiences count, total earned. Approve/Reject/Suspend actions |
| `agencies/show.blade.php` | ⏳ | Full profile, experiences, booking history, payout history, commission change log |
| `payouts/index.blade.php` | ⏳ | Pending payouts grouped by agency. Mark as paid (single + bulk). Export CSV |
| `payouts/history.blade.php` | ⏳ | Paid payouts: date, amount, admin who processed, notes |
| `users/index.blade.php` | ⏳ | Tourist accounts table. Ban/restore actions |
| `users/show.blade.php` | ⏳ | Profile, booking history, review history |
| `experiences/index.blade.php` | ⏳ | All experiences across all agencies. Force-unpublish action |
| `reviews/index.blade.php` | ⏳ | All reviews: toggle visibility. Filter by 1-star, flagged, hidden |
| `bookings/index.blade.php` | ⏳ | All bookings. Force-cancel with full refund action |

---

## routes/

| File | Status | Purpose |
|------|--------|---------|
| `web.php` | ⏳ | Delegates to site.php, agency.php, admin.php, webhooks.php |
| `site.php` | ⏳ | Public: home, search, experience detail, slot JSON, checkout steps, payment, confirmation, auth |
| `agency.php` | ⏳ | Auth + approved: dashboard, experiences CRUD, availability, bookings, payouts, profile |
| `admin.php` | ⏳ | Admin role only: dashboard, agencies, payouts, users, experiences, reviews, bookings |
| `webhooks.php` | ⏳ | No CSRF: stripe webhook, paypal webhook |
| `console.php` | ⏳ | Scheduled commands: expire unpaid, complete bookings, send review invites, sitemap |

---

## database/seeders/

| File | Status | Purpose |
|------|--------|---------|
| `RolesSeeder.php` | ⏳ | Create roles: admin, travel_agency, user via Spatie Permission |
| `AdminUserSeeder.php` | ⏳ | Create admin@morocco-tourism.com with admin role |
| `DemoDataSeeder.php` | ⏳ | 3 agencies, 10 experiences, availabilities, 20 demo bookings (local dev only) |

---

## tests/

| File | Status | Purpose |
|------|--------|---------|
| `Unit/CommissionCalculatorTest.php` | ⏳ | Test all commission/agency amount math edge cases |
| `Unit/RefundCalculatorTest.php` | ⏳ | Test refund at various times relative to policy window |
| `Feature/Booking/CreateBookingTest.php` | ⏳ | Full booking flow: seat lock, commission snapshot, concurrent bookings (race condition) |
| `Feature/Booking/CancelBookingTest.php` | ⏳ | Tourist cancel within/outside window. Admin cancel (always full refund) |
| `Feature/Payout/PayoutFlowTest.php` | ⏳ | Booking confirmed → payout auto-created → admin marks paid |
| `Feature/Auth/AgencyApprovalTest.php` | ⏳ | Register agency → admin approves → agency can create experiences |

---

## Removed from Scope (V2/V3 — not being built)

| Feature | Why removed |
|---------|------------|
| Agency public profile page | V2 |
| Tourist dashboard (bookings, receipts) | V2 |
| Wishlist | V2 |
| Agency analytics (views, revenue charts) | V2 |
| Multilingual content (AR/FR experience listings) | V2 |
| Smart recommendations | V3 |
| Mobile app | V3 |
| Multi-country expansion | V3 |
| Review photos from tourists | V2 |
