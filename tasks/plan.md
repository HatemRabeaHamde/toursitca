# Guided booking and mandatory experience approval

Date: 2026-11-03. Status: C01–C07 implemented locally; C08 automated/SQLite checks pass, with browser/MySQL/release checks outstanding. Not deployed.
Task checklist: docs/TODO.md. Scope corrected after user feedback on 2026-11-02; the previous B01–B20 expansion is superseded by the bounded tasks below.

## Outcome and boundaries

Travellers use the existing calendar and package options to choose a real experience, date, start time and party; see an accurate total; and complete an instantly confirmed booking when all inventory is available. Every newly listed experience must first receive explicit admin approval. Publishing approval and booking confirmation are separate processes.

Use Laravel 13, Blade, Tailwind, Alpine, MySQL, PHPUnit, Spatie translations/media and Brick money arithmetic. UI remains EN/FR/NL. No framework replacement, online payments, messaging platform or page builder.

Reference: https://www.withlocals.com/experience/full-coverage-marrakech-city-tour-009d6dc7/locals/
The supplied URL could not be read by the web tool. The five user screenshots directly establish the date/time/traveller selectors, chosen host, unavailable selection warning, private badge, reviews, adult/child counters and larger-party contact CTA. They do not establish the competitor's backend, payment rules or unseen checkout screens. Behaviours below are our proposed design. Keep Tourstica branding; do not copy their prices, age limits, eight-person limit, reviews or host data.

## Scope correction: reuse the implemented flow

The existing calendar already exposes available dates/times; this is an implemented capability to preserve and test, not a new feature to build. Options are the existing package model for group/private capability, person/group price basis, adult/child prices, pick-up and cancellation settings. The experience already has max_group_size and each availability has max_seats. There is no proposed global eight-person maximum or second package system.

| Existing capability | Planned action and exact surface |
|---|---|
| Available dates/times | Reuse current calendar and availability picker; check only consistency after date/party/package changes |
| Package options | Keep the existing options section and agency/admin option forms as the authoring source |
| Party limit | Use experience.max_group_size and the selected availability's remaining capacity; do not introduce a copied numeric default |
| Group/private and pricing | Read the chosen package and supported experience settings; verify backend quote, card and checkout agree |
| Cancellation and pick-up | Show the chosen option's settings in the booking summary and checkout; do not take the first unrelated option's value |
| Host | Display the host already attached to the experience; no new host selector or independent host calendar is assumed |
| Instant confirmation | Keep confirmed booking creation, live final capacity checks, payouts and separate payment status |

The phrase “choose an available host” in the original plan was an unnecessary expansion into a multi-host marketplace. It does not describe the current calendar and is removed from core scope. If multiple selectable hosts per experience are explicitly requested later, design that separately.

The phrase “offer alternative times/hosts” is also removed as a new subsystem. The calendar is already where a traveller chooses another available time. For the narrow race case where seats disappear after selection, show a clear message and reopen/refresh that same calendar, preserving party/package where valid. Never silently swap the time/package or host.

## Verified gaps to address, without rebuilding existing controls

- PublishExperienceAction currently lets an agency publish directly after an image check: mandatory admin review is new work.
- UpdateExperienceAction writes live content directly: published changes and direct option/media edits need the same review protection.
- ExperienceQuoteService currently does not branch on an option's price_type, although the option model/request stores per_person/per_group. This is a concrete pricing-consistency check/fix, not a new pricing feature.
- Option private_available, experience.private_price and booking_type must be checked together; a crafted request must not enable a private package that is not offered.
- ExperiencePageService currently derives cancellationHours from the first populated option. Summary/checkout must use the selected package instead.
- Child ages already exist; option request currently stores hard-coded 5–12 child bands. First verify the current authoring/UI and required business rules; any missing age edit belongs in the existing option form. Do not add a second standalone configuration screen or assume new discounts.
- UpdateAvailabilityAction currently fills and saves directly: verify booked/held slot move/capacity protections and add only missing guards.
- BookingTripPresenter currently reads the experience's current host. If preserving the originally booked host is required, snapshot it without adding host selection.

## Traveller journey, retaining existing selection order

1. Keep the current experience calendar and date/time selection, party controls and options section. Improve their styling to match the clarity of the supplied screenshots using Tourstica branding.
2. A selected package is the source for its price basis, private/group capabilities, child prices, pick-up and cancellation. Existing max_group_size and slot capacity constrain participants.
3. Booking form header: show the real rating and linked review count beneath the price, and a Private/Shared group badge tracking the current booking type. Show person/group price basis without equating per-group pricing with a private trip. Do not invent ratings for unrated trips.
4. Refresh the authoritative quote when date/time, party or option changes. Show total and price basis in the existing booking card/option card, with loading/error states and mobile accessibility.
5. Continue into the existing checkout hold, optional pick-up and contact/auth steps. Carry the exact selected option and availability throughout.
6. Final confirmation checks live capacity again to cover another customer booking after the calendar was loaded. Confirm immediately when valid; otherwise return to the current calendar with an explanation.
7. Confirmation, client account and agency/admin booking details display the same package, party, time, amount and payment state.

No additional availability button, alternative-host screen, inquiry inbox, independent host inventory, or new age/party configuration section is required by this corrected core plan. The screenshots are a UX reference, not a request to duplicate every competitor subsystem.

## Mandatory publication and edit review

Publication: draft → pending_review → published; pending_review → changes_requested or rejected; published → unpublished/suspended. Agency withdrawal returns its submission to draft. Re-submission creates a new auditable version.

- Agency actions: Save draft, Preview, Submit for review, Withdraw, Unpublish. Remove direct agency publish permission at route, policy and Action levels.
- Admin queue: new and changed experiences, owner, submitted time, completeness, preview, version diff, reasons and approve/request changes/reject/suspend actions.
- Admin-created/platform-owned experiences also use an explicit approval/publish action and audit trail. The same admin may approve their own draft; no unrequested second-admin dependency.
- Required review: accurate EN content and locale fallback, images/alt, itinerary/duration, meeting/pick-up, hosts, prices and child rules, capacity, cancellation terms and SEO fields. No fake licences, reviews or guaranteed claims.
- Published content edits are staged in a change request. The public approved version remains unchanged until approval; do not overwrite the live record on Save. New experiences stay private until approval.
- Stage the entire reviewed aggregate: texts, media references, itinerary, prices/options, host assignments and cancellation rules. Protect direct option/media/update routes as well as the main form.
- Admin approval locks the experience and submitted version, checks its base version, applies the approved aggregate atomically and records reviewer/time. Reject stale approvals and requests modified after submission.
- Operational calendars and closing dates do not require editorial approval. Dates may open only within the approved experience/package capacity and pricing rules. Unpublish/stop-sales takes effect immediately. Existing confirmed bookings remain accessible.
- Public listings, detail routes, search indexes, sitemap, quote, checkout start and final confirmation enforce the same eligibility service. Invalidate content/search projections on approval or suspension; never cache availability.

## Minimal backend changes

Keep experiences, experience_options, their price/language rows, availabilities and existing checkout/booking tables. Add review state/audit fields and a versioned experience_change_requests payload for reviewed changes. Do not replace the availability uniqueness/indexes or add multi-host inventory for this task.

Use a common approval eligibility check for public detail/list/search/sitemap and booking entry points. Stage pending edits, including options and media, outside the approved public record; approval applies them atomically with a base-version check. Operational availability changes remain in the existing calendar subject to booking/hold protection.

Pricing, limits and ages stay in the current experience/package authoring flow. Review current schema/form gaps before any migration. Do not equate per_group price basis with private booking semantics: display and validate both as the existing package offers them.

Retain row locking and no availability cache. Verify existing checkout holds/expiry/idempotency before changing them; add only missing protections. Confirmation stays immediate and payment status remains independent. Existing booking financial snapshots never recalculate after edits.

## Dashboard changes and their locations

| Actor | Location | Actual change |
|---|---|---|
| Agency | Existing experience editor/list | Save draft, preview, submit for review, review status/reasons; replace direct Publish |
| Agency | Existing options forms | Keep package authoring; correct any demonstrated mismatch in group/private, price basis or age rules here |
| Agency | Existing availability screen | Retain available dates/time/capacity management; protect held/booked slots |
| Admin | Experience management | Add pending-review filter/queue, preview/version diff and approve/request changes/reject actions |
| Admin | Existing experience/options/booking forms | Same package fields, accurate selected-package booking details and explicit publication audit |
| Host | No new workspace in core | Remains agency-managed; an independent host account/dashboard remains a separately scoped optional extension |
| Client | Existing booking card/options, checkout and My bookings | Consistent selected package/date/party/price and immediate confirmed state |

If optional host login is later approved, an invited host may manage only their assigned records/calendar and submit drafts under their agency. They cannot publish directly. Independent host businesses/payouts are not inferred from this request.

## Corrected implementation tasks

Tasks are tracked here and indexed in docs/TODO.md. Each slice must stay focused and include its corresponding tests.

| ID | Deliverable and acceptance | Dependencies | Location / verification |
|---|---|---|---|
| C01 | Trace current calendar → option → quote → checkout; document actual working behaviour and reproduce mismatches | None | Existing JS modules, ExperiencePageService, ExperienceQuoteService, option forms; focused tests/browser |
| C02 | Add experience review transitions and audit/version schema; reject invalid/stale transitions | C01 | Model/migration/review service; PHPUnit |
| C03 | Agency submits; admin previews/approves/rejects; crafted direct publication is denied | C02 | Existing agency/admin experience routes, requests, Actions/policies and Blade; authorization tests |
| C04 | Stage published text/option/media edits; only approved versions become public | C03 | Update/option/media handlers, change request service and eligibility checks; bypass and old-version tests |
| C05 | Fix demonstrated selected-package price/private/cancellation discrepancies, reusing current authoring forms | C01 | Quote service, package request, ExperiencePageService and summary; price/capability tests |
| C06 | Polish existing calendar/card/options and propagate selected package into checkout | C05 | Existing Blade/Alpine components and EN/FR/NL; desktop/375px/keyboard browser checks |
| C07 | Verify final capacity, held/booked slot protection and stable booking details; implement only missing safeguards | C04–C06 | Existing availability/checkout/booking services and dashboard presenters; concurrency/retry/edit tests |
| C08 | Rehearse migration, full role flow and staged deployment | C07 | PHPUnit/Pint/Larastan/build, MySQL rehearsal and live browser smoke |

Checkpoints: C01 identifies reused vs missing behaviour before schema changes; C04 proves no publication bypass; C07 proves that calendar, quote and confirmed booking agree; C08 completes release verification. Split a task further if more than five focused files are required.

## Acceptance and release checks

- Available calendar times remain functional; no duplicate date/time system or additional forced selection step.
- Current experience and package settings govern party count, group/private, price, children, pick-up and cancellation. No global eight-person limit or copied age band.
- Switching packages/date/party refreshes only affected state and invalidates stale quotes; no silent substitution.
- New listings cannot become public/bookable before admin approval. Published pending edits do not alter the approved public version, including direct option/media routes.
- New available bookings confirm instantly; price/party/option/time match across checkout, confirmation and admin/agency/client views. Unpaid never displays as paid.
- Check last-seat/private concurrency using real MySQL connections, expired holds, repeat submit and held/booked availability updates. SQLite alone cannot prove row-lock behaviour.
- Test EN/FR/NL, desktop, 375px, keyboard focus, slow/stale responses and login return in the existing flow.
- Run php artisan test, ./vendor/bin/pint, ./vendor/bin/phpstan analyse and the Vite build during implementation. Do not claim Larastan passes while its known silent exit persists.
- Back up code/MySQL/uploads; rehearse additive review migrations and audit existing published records without inventing historical approvals. Preserve all confirmed bookings and financial snapshots.
- Implementation verification (2026-11-03): 214 PHPUnit tests / 1,140 assertions and seven Node interaction tests pass, plus Pint, Vite build and Blade/route compilation. An HTTP integration test covers agency submission, admin approval, client quote/checkout/confirmation and all role dashboards in EN/FR/NL. Both additive migrations were applied to the isolated local SQLite QA database.
- Remaining release checks: Work settings disable browser automation, so desktop/375px visual verification is not claimed. No local MySQL runtime is available to prove parallel row-lock behaviour. Larastan still exits 1 silently on PHP 8.5. No remote deployment was made.
