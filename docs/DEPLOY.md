# Deploy runbook (Hostinger shared hosting)

Target: the Hostinger test site. The server host, SSH port, account user and domain are kept out of git in `docs/DEPLOY.local.env` (git-ignored; ask the project owner for it). Load it before running the commands below, and use an SSH key, not a password.

```bash
source docs/DEPLOY.local.env   # defines DEPLOY_HOST, DEPLOY_PORT, DEPLOY_USER, DEPLOY_DOMAIN
```

The seeders create demo content and demo accounts. Fine on a test domain; do not seed a real production database with them.

## 0. Once, in hPanel
- PHP version: 8.3 or newer (`composer.json` requires `^8.3`).
- Create a MySQL database and user; note the name, user and password.
- Back up the database before any migration if it already holds data.

## 1. Build assets locally (the server has no Node)
```bash
rm -rf node_modules && npm ci && npm run build
```
`public/build` is gitignored, so it is uploaded separately in step 3.

## 2. Put the code on the server
Keep the app *above* `public_html` and point `public_html` at `public/`.

```bash
ssh -p "$DEPLOY_PORT" "$DEPLOY_USER@$DEPLOY_HOST"
cd "domains/$DEPLOY_DOMAIN"
mv public_html public_html.bak      # keep the default page, do not delete it
mkdir -p app
```

From your machine (no GitHub needed):
```bash
rsync -az --delete -e "ssh -p $DEPLOY_PORT" \
  --exclude .git --exclude node_modules --exclude vendor --exclude .env --exclude .env.production \
  --exclude docs/DEPLOY.local.env \
  --exclude storage/logs --exclude "Claude outputs" --exclude docs/figma \
  ./ "$DEPLOY_USER@$DEPLOY_HOST:domains/$DEPLOY_DOMAIN/app/"
scp -P "$DEPLOY_PORT" .env.production "$DEPLOY_USER@$DEPLOY_HOST:domains/$DEPLOY_DOMAIN/app/.env"
```
Fill the three `CHANGE_ME` database lines in `.env.production` first.

## 3. On the server
```bash
cd "domains/$DEPLOY_DOMAIN"   # the server shell does not have DEPLOY_* set: type the domain here
ln -s app/public public_html
cd app
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan db:seed --force
php artisan db:seed --class=DemoAccessSeeder --force
php artisan db:seed --class=DemoClientSeeder --force
php artisan storage:link
php artisan config:cache && php artisan route:cache && php artisan view:cache && php artisan event:cache
```
Never run `migrate:fresh` here.

## 4. Check
- `https://$DEPLOY_DOMAIN/en` loads the landing.
- Sign in at `/login` with each demo account:
  - admin (`admin@tourstica.test`) goes to `/admin/dashboard`
  - agency (`agency@tourstica.test`) goes to `/agency/dashboard`
  - client (`client@tourstica.test`) goes to `/en/dashboard/bookings`
- Passwords are the `ADMIN_PASSWORD`, `DEMO_AGENCY_PASSWORD` and `DEMO_CLIENT_PASSWORD` lines in `.env.production`.

## Updating later
Repeat step 1 and the rsync from step 2, then on the server: `composer install --no-dev -o`, `php artisan migrate --force`, and the four `*:cache` commands.

## Explicit dashboard QA batch

`AdminQaSeeder` is never included in the default seeder. Run it only on the requested test domain after a database backup:

```bash
read -s -p 'QA fixture password (16+ characters): ' QA_SEED_PASSWORD
export QA_SEED_PASSWORD
php artisan db:seed --class=AdminQaSeeder --force
unset QA_SEED_PASSWORD
```

Creates 1 QA admin, 8 agency owners/agencies, 20 clients, 16 inactive unverified hosts, 24 draft experiences, 72 packages, 384 availability rows, 96 booking scenarios, 48 payout fixtures and 24 hidden reviews. All records carry `[TEST]` names/notes and dedicated `qa-*` emails/slugs. No notifications, real transfers or public listings are created. All fixture accounts share the supplied QA password. The active agency test account is `qa-agent01@tourstica.test`; admin is `qa-admin@tourstica.test`; client is `qa-client01@tourstica.test`. Agencies 7 and 8 demonstrate pending and suspended states.

The whole batch runs in one transaction. Account collisions abort without resetting existing users. If the QA admin already exists, rerunning is a no-op and preserves any manual edits. A new password argument never rotates existing credentials. Do not publish synthetic fixtures as real travel offers.


### Booking/review workflow release (prepared locally 2026-11-03)

Deployed release: `c0a85f5` on 2026-11-03 (includes workflow `47cca02`, Morocco timezone and subsequent booking/search/branding changes). Private backup: `backups/latest-20261103T015608Z`, referenced by `backups/latest-release`. Both migrations and the live rollback smoke passed; original environment, record counts and published listings were preserved. Public routes and hashed assets return 200. Read tasks/plan.md and the C08 limitations in docs/TODO.md first. The built-in browser currently reports local browser use disabled in Work settings; desktop and 375px QA remain unverified. Real parallel-connection MySQL capacity tests are also outstanding, and Larastan still exits 1 silently under local PHP 8.5.

Before release, back up MySQL, code and uploads privately. Check for duplicate non-null bookings.checkout_session_id values before applying its new unique index; do not merge or delete any bookings automatically. Apply the experience-review workflow and cancelled-payout/checkout-uniqueness migrations, upload the rebuilt assets and refresh autoload/config/routes/views. Existing published experiences retain their publication and receive no invented reviewer/date; existing unpublished rows start in draft review state. New authoring always starts as a draft. Never re-run broad seeders during this release.

Verify: agency creates and submits → anonymous detail/quote still denied → admin inspects real-page draft preview and approves → published date/time/package quote → checkout hold/contact → instant confirmation → client, agency and admin booking details. Test a staged published edit, withdrawal, rejection reason, stale decision, private departure, child age limits, repeated submit, expiry and cancelled payouts. Confirm selected host/package/time/policy stay unchanged on an old booking after later publication edits.

The cancelled payout enum is intentionally retained by rollback so cancelled financial history cannot silently become payable. Disable new authoring/checkout if rollback compatibility cannot be demonstrated; preserve schema/data and reconcile existing cancelled/paid records manually. No gateway, transfer or customer email is run by migration/backfill.

Deployment packaging note: preserve the Hostinger document-root mode (0755). When using a temporary staging directory, set its mode before archiving or restore the target root mode immediately after extraction. Do not overwrite the host root .htaccess/.env or existing uploads. Archive extraction during this release initially restricted the root to 0700; it was corrected and all public HTTP checks were repeated successfully.
