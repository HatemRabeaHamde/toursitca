# Marketplace Flow Logic Plan

## Scope

This plan covers the marketplace flow from the landing/search listing page through experience details, availability, checkout, and booking confirmation.

Flow:

```txt
Landing / Search Listing
  -> Experience Show
  -> Participant + Date + Language Selection
  -> Availability Options
  -> Book Now / Add To Cart
  -> Checkout Activity / Pickup
  -> Checkout Contact
  -> Checkout Payment
  -> Booking Confirmation
```

The implementation should stay aligned with the current Laravel Blade architecture:

- Controllers receive requests and return responses only.
- Business logic lives in Domain actions, queries, and services.
- Form Requests validate all user input.
- Blade receives prepared data and should not calculate business rules.
- All customer-facing strings and content should remain localization-ready.

## Current Project Fit

Already available:

- `experiences` table with translatable `title`, `description`, `inclusions`, and `exclusions`.
- `Experience` model using `spatie/laravel-translatable`.
- `experience_media` for images/videos.
- `availabilities` with date, time slot, max seats, booked seats, and active status.
- `bookings` with basic group/private booking flow.
- Seat locking/reservation logic during booking creation.
- Locale-prefixed public routes.
- Basic public experience listing.

Missing for the target flow:

- Search/filter query layer.
- Dedicated experience show route by slug.
- Experience options.
- Participant type pricing, such as adult and child.
- Server-side quote endpoint.
- Checkout session with timed hold.
- Pickup step.
- Contact step.
- Payment/pay-later step.
- Wishlist.
- Cart.
- Reviews.
- Currency conversion.
- Promotions.
- Rich destination and SEO sections.

## Phase 1: Landing / Search Listing

Goal:

Build the logic required for a GetYourGuide-style listing page, starting from the current `/en/experiences` page.

Features:

- Keyword search.
- City/destination filter.
- Category filter.
- Date availability filter.
- Price range filter.
- Private option filter.
- Pickup availability filter.
- Sorting.
- Result count.
- Pagination.
- Prepared card data.

### Folder Changes

```txt
app/Domain/Experience/
 ├── DTOs/
 │   ├── ExperienceSearchData.php
 │   └── ExperienceCardData.php
 ├── Queries/
 │   └── ExperienceSearchQuery.php
 └── Services/
     └── ExperienceCardPresenter.php

app/Http/Requests/Site/
 └── ExperienceSearchRequest.php

resources/views/site/experiences/
 ├── index.blade.php
 └── partials/
     ├── filters.blade.php
     └── card.blade.php
```

### Database Changes

Add to `experiences`:

```txt
slug unique
original_price nullable
pickup_enabled boolean default false
is_top_rated boolean default false
```

Deferred:

```txt
tags
feature taxonomy
advanced ranking scores
currency conversion
discount engine
wishlist counts
```

### Data Flow

```txt
GET /{locale}/experiences?search=Marrakesh&date=2026-06-11&category=desert&private_available=1
        ↓
ExperienceSearchRequest
        ↓
ExperienceSearchData
        ↓
ExperienceSearchQuery
        ↓
ExperienceCardPresenter
        ↓
resources/views/site/experiences/index.blade.php
```

### Query Filters

Supported in the first implementation:

```txt
search
city
category
date
min_price
max_price
private_available
pickup_available
sort
```

Sort options:

```txt
recommended
price_low
price_high
rating
newest
```

Recommended sorting should initially use:

```txt
is_top_rated desc
rating_avg desc
reviews_count desc
created_at desc
```

### Card Data Contract

Each experience card should receive:

```txt
id
slug
title
city
category
thumbnail_url
duration_label
rating_avg
reviews_count
badges
features
price_from
original_price
currency
is_private_available
is_pickup_available
show_url
```

Example:

```php
[
    'id' => 1,
    'slug' => 'marrakech-balloon-ride',
    'title' => 'Marrakech: Balloon Ride with Berber Breakfast & Certificate',
    'city' => 'Marrakesh',
    'category' => 'balloon',
    'thumbnail_url' => 'https://example.test/image.jpg',
    'duration_label' => '3 - 4 hours',
    'rating_avg' => '4.8',
    'reviews_count' => 10073,
    'badges' => ['top_rated'],
    'features' => ['Private option available', 'Pickup available'],
    'price_from' => '4509.00',
    'original_price' => '8841.00',
    'currency' => 'MAD',
    'is_private_available' => true,
    'is_pickup_available' => true,
    'show_url' => '/en/experiences/marrakech-balloon-ride',
]
```

### Security And Performance

- Validate all query params in `ExperienceSearchRequest`.
- Do not accept any display price from the request.
- Use eager loading for `agency` and first media image.
- Use `whereHas` for date availability filtering.
- Keep pagination.
- Add indexes for `slug` and search-heavy filters where needed.

## Phase 2: Experience Show Page

Goal:

Create a dedicated public detail page for each experience.

Route:

```txt
GET /{locale}/experiences/{experience:slug}
```

### Folder Changes

```txt
app/Domain/Experience/
 ├── DTOs/
 │   └── ExperienceDetailData.php
 └── Services/
     ├── ExperienceDetailService.php
     └── RecommendationService.php

resources/views/site/experiences/
 ├── show.blade.php
 └── partials/
     ├── gallery.blade.php
     ├── booking-widget.blade.php
     ├── highlights.blade.php
     ├── itinerary.blade.php
     ├── important-information.blade.php
     ├── reviews-summary.blade.php
     └── recommendations.blade.php
```

### Data Loaded

```txt
experience
agency/provider
media gallery
badges
rating summary
options
languages
next available dates
policies
wishlist state
recommendations
```

### Content Fields To Support

Translatable JSON:

```txt
highlights
full_description
important_information
not_suitable_for
what_to_bring
not_allowed
know_before_you_go
pickup_instructions
```

Structured fields:

```txt
free_cancellation_hours
pay_later_enabled
pickup_enabled
is_certified
is_top_rated
product_code
```

## Phase 3: Experience Options, Participants, And Pricing

Goal:

Support multiple activity options and participant-specific pricing.

### Tables

```txt
experience_options
 - id
 - experience_id
 - title JSON
 - duration_minutes
 - pickup_enabled
 - private_available
 - pay_later_enabled
 - cancellation_hours
 - price_type: per_person/per_group
 - status

experience_option_prices
 - id
 - experience_option_id
 - participant_type: adult/child/infant
 - min_age
 - max_age
 - price
 - original_price
 - currency

experience_option_languages
 - id
 - experience_option_id
 - language_code
 - type: live_guide/audio_guide
```

### Participant Rules

Support:

```json
{
  "adult": 5,
  "child": 2
}
```

Validation:

- Participant type must exist for the selected option.
- Total participants cannot exceed available seats.
- Total participants cannot exceed option capacity.
- Age constraints are enforced through configured participant price rows.

### Private Booking Rule

If the user chooses private booking:

```txt
booking_type = private
participants_count = number of real travelers entered by the user
participants_breakdown = adult/child counts
charged_seats = max_seats
booked_seats = max_seats
```

Pricing:

- If `private_price` exists, charge it as a fixed private total.
- If `private_price` does not exist, charge `price_per_person * max_seats`.
- The real traveler count is stored for operations, but it does not reduce charged seats.

Example:

```txt
availability max_seats = 10
user selects private
real travelers = 3

participants_count = 3
charged_seats = 10
booked_seats = 10
```

## Phase 4: Availability And Quote

Goal:

Let the detail page ask the server for availability and prices based on selected participants/date/language.

### Availability Table Evolution

Current `availabilities` can be evolved with:

```txt
experience_option_id nullable initially
starts_at
ends_at
held_seats default 0
```

Or replaced later by:

```txt
experience_option_availabilities
 - id
 - experience_option_id
 - starts_at
 - ends_at
 - max_seats
 - booked_seats
 - held_seats
 - is_active
```

### Endpoints

```txt
POST /{locale}/experiences/{experience:slug}/availability
POST /{locale}/experiences/{experience:slug}/quote
```

### Quote Data Returned

```txt
available_dates
available_start_times
remaining_seats
cancellation_deadline
adult subtotal
child subtotal
original total
discount amount
final total
currency
taxes_and_fees_included
```

## Phase 5: Checkout Session

Goal:

Create a timed checkout hold before final booking.

### Table

```txt
checkout_sessions
 - id
 - uuid
 - user_id nullable
 - session_id nullable
 - experience_id
 - experience_option_id nullable
 - availability_id
 - locale
 - currency
 - booking_type: group/private
 - participants JSON
 - participants_count
 - charged_seats
 - tour_language
 - price_snapshot JSON
 - reserved_until
 - status: active/expired/completed/cancelled
```

### Data Flow

```txt
POST /{locale}/checkout/start
        ↓
CheckoutStartRequest
        ↓
CreateCheckoutSessionAction
        ↓
Validate selected experience, option, availability, participants, language
        ↓
Hold seats temporarily
        ↓
Create checkout session
        ↓
Redirect to checkout activity step
```

### Hold Rules

- Hold duration should initially be 30 minutes.
- Checkout cleanup job releases expired holds.
- Final booking still locks availability row and re-checks seats.

## Phase 6: Checkout Activity / Pickup

Goal:

Collect pickup details or allow the customer to skip if unknown.

Routes:

```txt
GET /{locale}/checkout/{checkoutSession:uuid}/activity
POST /{locale}/checkout/{checkoutSession:uuid}/activity
```

Fields:

```txt
pickup_status: add_now/unknown/not_required
pickup_address
pickup_lat
pickup_lng
```

Rules:

- If the selected option does not support pickup, store `not_required`.
- If pickup is required and the user chooses `unknown`, allow checkout only if the provider supports later pickup confirmation.
- Map validation can be deferred; start with address text and optional coordinates.

## Phase 7: Checkout Contact

Goal:

Collect customer and lead traveler details.

Routes:

```txt
GET /{locale}/checkout/{checkoutSession:uuid}/contact
POST /{locale}/checkout/{checkoutSession:uuid}/contact
```

Fields:

```txt
first_name
last_name
email
phone
country
special_requests
```

Rules:

- Prefill from logged-in user where possible.
- Guests can continue with session ownership.
- Checkout session must be active and not expired.

## Phase 8: Checkout Payment / Pay Later

Goal:

Create the final booking from the checkout session.

First version:

```txt
Reserve now and pay later
```

Later:

```txt
Stripe
PayPal
CMI
```

Routes:

```txt
GET /{locale}/checkout/{checkoutSession:uuid}/payment
POST /{locale}/checkout/{checkoutSession:uuid}/payment
```

Final confirmation flow:

```txt
ConfirmCheckoutPaymentAction
        ↓
DB transaction
        ↓
Lock availability row
        ↓
Re-check active session and seats
        ↓
Create booking from checkout price snapshot
        ↓
Mark checkout session completed
        ↓
Redirect confirmation
```

## Phase 9: Booking Model Evolution

Add:

```txt
checkout_session_id nullable
experience_option_id nullable
participants JSON
tour_language
pickup_status
pickup_address
pickup_lat
pickup_lng
charged_seats
price_snapshot JSON
payment_status
payment_provider nullable
payment_reference nullable
```

Keep:

```txt
unit_price
total_price
commission_rate
commission_amount
agency_amount
reserved_until
status
```

Important:

- All pricing values must be snapshotted at booking time.
- Never recalculate existing booking totals from current experience prices.

## Phase 10: Wishlist

Table:

```txt
wishlist_items
 - id
 - user_id
 - experience_id
 - timestamps
```

Rules:

- First version can require login.
- Guest wishlist via session can be added later.
- Listing and show pages receive `is_wishlisted`.

## Phase 11: Cart

Table:

```txt
cart_items
 - id
 - user_id nullable
 - session_id nullable
 - experience_id
 - experience_option_id nullable
 - availability_id nullable
 - booking_type
 - participants JSON
 - tour_language
 - currency
 - price_snapshot JSON
 - expires_at nullable
```

Rule:

- Cart should not hold seats initially.
- Seats should be held only when checkout starts.

## Phase 12: Reviews

Tables:

```txt
reviews
 - id
 - booking_id
 - user_id
 - experience_id
 - rating
 - title
 - body
 - locale
 - is_verified
 - status

review_topic_ratings
 - id
 - review_id
 - topic
 - rating
```

Rules:

- Only completed bookings can create reviews.
- One review per booking.
- Update denormalized `rating_avg` and `reviews_count` on experiences.
- Support filtering by rating, traveler type, and sorting later.

## Phase 13: Currency

Tables:

```txt
currencies
currency_rates
```

Service:

```txt
CurrencyConversionService
```

Rules:

- Base booking currency remains authoritative.
- Display currency is snapshotted in checkout/booking.
- Currency rates are cached.
- External API keys must be stored in `.env`.

## Phase 14: Promotions

Tables:

```txt
promotion_codes
promotion_redemptions
```

Rules:

- Validate promo codes server-side.
- Support min order total.
- Support category/experience restrictions.
- Store promo impact in `price_snapshot`.

## Phase 15: Destination And SEO Expansion

For city landing pages:

```txt
destinations
destination_sections
destination_nearby_destinations
destination_top_sights
```

Use cases:

- `/en/marrakesh-l208`
- Top activities.
- Things to do.
- Places to see.
- Trip inspiration.
- Nearby destinations.
- SEO copy by locale.

## Routing Plan

Public routes:

```txt
GET  /{locale}
GET  /{locale}/experiences
GET  /{locale}/experiences/{experience:slug}

POST /{locale}/experiences/{experience:slug}/quote
POST /{locale}/experiences/{experience:slug}/availability

POST /{locale}/checkout/start
GET  /{locale}/checkout/{checkoutSession:uuid}/activity
POST /{locale}/checkout/{checkoutSession:uuid}/activity
GET  /{locale}/checkout/{checkoutSession:uuid}/contact
POST /{locale}/checkout/{checkoutSession:uuid}/contact
GET  /{locale}/checkout/{checkoutSession:uuid}/payment
POST /{locale}/checkout/{checkoutSession:uuid}/payment
GET  /{locale}/checkout/{checkoutSession:uuid}/confirmation

GET  /{locale}/cart
POST /{locale}/cart/items
DELETE /{locale}/cart/items/{cartItem}

POST /{locale}/wishlist/{experience}
```

## DI Plan

Use auto-resolution where possible. Add interface bindings only where implementation swapping is likely:

```php
ExperienceSearchInterface::class => EloquentExperienceSearchQuery::class
PricingServiceInterface::class => ExperiencePricingService::class
CurrencyConverterInterface::class => CurrencyConversionService::class
CheckoutReservationInterface::class => DatabaseCheckoutReservationService::class
PaymentGatewayInterface::class => ConfiguredPaymentGateway::class
```

## Security Checklist

- Validate all request input with Form Requests.
- Never trust price, discount, or totals from the browser.
- Use UUIDs for checkout sessions.
- Enforce checkout session ownership by user id or session id.
- Use DB transactions and row locks for availability.
- Re-check availability before creating booking.
- Store payment provider secrets in `.env`.
- Confirm online payments through provider verification/webhooks.
- Sanitize customer-entered pickup/contact/special request fields.
- Rate-limit quote and checkout-start endpoints.
- Keep user tokens in httpOnly cookies, not localStorage.

## Performance Checklist

- Use eager loading for listing and detail pages.
- Keep listing paginated.
- Cache currency rates.
- Cache recommendation results.
- Cache review summaries where possible.
- Add indexes for common filters.
- Avoid N+1 queries in Blade partials.
- Keep quote endpoint lightweight.
- Use queue jobs for checkout expiration cleanup and later AI translation.

## Implementation Order

Recommended order:

1. Landing/search listing logic.
2. Experience show route and detail service.
3. Experience options and participant pricing.
4. Availability and quote endpoints.
5. Checkout session and timed holds.
6. Checkout activity/pickup step.
7. Checkout contact step.
8. Checkout payment/pay-later step.
9. Booking creation from checkout snapshot.
10. Wishlist.
11. Cart.
12. Reviews.
13. Currency conversion.
14. Promotions.
15. Destination SEO sections.
16. AI translation from dashboard.

## Next Immediate Step

Start with Phase 1 only:

```txt
Landing / Search Listing Logic
```

Deliverables:

- Migration for listing fields.
- Search request.
- Search DTO.
- Search query.
- Card DTO.
- Card presenter.
- Update `ExperienceController@index`.
- Update listing Blade to consume prepared card data.
- Tests for search/filter/sort behavior.

