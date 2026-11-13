# Tourstica — Morocco Experience Marketplace

Tourstica is a multilingual marketplace where travel agencies publish experiences in Morocco (tours, activities, guided days) and travellers discover, book and review them. It is a server-rendered Laravel app with three areas:

| Area | Who | What they do |
|------|-----|--------------|
| **Public site** (`/{locale}/…`) | Travellers | Browse destinations, interests, hosts and experiences; get a live quote; check out; leave reviews; manage a wishlist |
| **Agency dashboard** (`/agency/…`) | Travel agencies | Register (then wait for approval), manage experiences, options, availability, hosts, bookings, payouts and reviews |
| **Admin panel** (`/admin/…`) | Platform operator | Approve agencies and listing changes, edit site content (with revisions), oversee bookings, payouts and commission |

## Key behaviour

- **Instant confirmation.** An available slot is confirmed at checkout without manual approval. Capacity is checked live under a row lock, so two people cannot take the last seat.
- **Group vs private booking.** Group: `price_per_person × participants` on a shared slot. Private: flat `private_price` that takes the whole slot.
- **Moderated publishing.** New listings and edits to published listings are staged in `experience_change_requests` and go live only after an admin approves them. Drafts can be previewed but not booked.
- **Snapshots.** A booking stores the package, host, time and policy as they were at checkout. Payouts are created from that snapshot, after commit.
- **No online payment yet.** Payment status is tracked separately; the gateway layer (Stripe / PayPal / CMI) is an interface for a later phase.
- **Commission.** Default 15% (`DEFAULT_COMMISSION_RATE`), allowed range 0–50%. Money math uses `brick/money`; currency is MAD.
- **Languages.** English, French, Dutch (`config/locales.php`). UI strings live in `lang/`, content fields are JSON columns via `spatie/laravel-translatable`.

## Stack

| Layer | Choice |
|-------|--------|
| Backend | PHP 8.3, Laravel 13, Breeze auth |
| Frontend | Blade, Tailwind CSS, Alpine.js, GSAP, Lenis, Leaflet (maps), Vite |
| Data | MySQL 8 in production, SQLite locally/tests |
| Packages | Spatie: permission, media-library, translatable, model-states, data, settings, sitemap, sluggable, activitylog, csp · Laravel Horizon, Pennant, Scout (+ Meilisearch) |
| Quality | PHPUnit, Larastan, Pint |

## Code layout

```
app/
  Domain/          Business modules: Agency, Booking, Checkout, Discover, Experience,
                   Landing, Payment, Payout, Review, User, Wishlist, Shared
                   (models, actions and view models live inside each module)
  Http/Controllers/Admin | Agency | Auth | Site
  Seo/  Settings/  ViewModels/  Policies/
routes/
  site.php     public pages under /{locale}
  agency.php   agency registration + dashboard
  admin.php    admin panel + site-content editor
  auth.php     Breeze auth
  web.php      profile
docs/          architecture, deploy runbook, frontend contracts, SEO, TODO
```

## Getting started

Requirements: PHP 8.3+, Composer, Node 20+, and SQLite (or MySQL).

```bash
composer setup          # install, copy .env, key:generate, migrate, npm install, build
php artisan db:seed     # roles, admin, demo agency, landing/discover content, demo experiences
composer dev            # server + queue + log tail (pail) + Vite together
```

Then open http://localhost:8000 (redirects to `/en`).

The seeders create accounts from the `ADMIN_*` and `DEMO_*` values in `.env`, so set those before seeding. Do not run the demo seeders against a real production database.

### Useful environment values

| Key | Purpose |
|-----|---------|
| `ADMIN_NAME`, `ADMIN_EMAIL`, `ADMIN_PASSWORD` | First admin account |
| `DEMO_ADMIN_*`, `DEMO_AGENCY_*`, `DEMO_CLIENT_*` | Demo logins created by the seeders |
| `DEFAULT_COMMISSION_RATE` | Platform commission (%) |
| `APP_DEFAULT_CURRENCY` | Defaults to `MAD` |

## Tests

```bash
composer test            # PHPUnit feature + unit tests
npm run test:booking     # JS tests for the booking widget (node --test)
./vendor/bin/phpstan     # Larastan
```

## Deployment

Deploys to Hostinger shared hosting. The server has no Node, so assets are built locally and uploaded. Follow [docs/DEPLOY.md](docs/DEPLOY.md) step by step.

## More documentation

- [docs/ARCHITECTURE.md](docs/ARCHITECTURE.md): product rules, roles, booking model
- [docs/MARKETPLACE_FLOW_PLAN.md](docs/MARKETPLACE_FLOW_PLAN.md): booking and checkout flow
- [docs/ADMIN_DASHBOARD.md](docs/ADMIN_DASHBOARD.md): admin features
- `docs/*_CONTRACT.md`: data contracts between controllers and Blade views
- [docs/SEO.md](docs/SEO.md), [docs/TODO.md](docs/TODO.md), [PROJECT.md](PROJECT.md)
