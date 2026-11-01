# Deploy runbook (Hostinger shared hosting)

Target: `linen-spider-307400.hostingersite.com`. SSH: `-p 65002 u383259569@194.59.166.35`. Use an SSH key, not a password.

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
ssh -p 65002 u383259569@194.59.166.35
cd domains/linen-spider-307400.hostingersite.com
mv public_html public_html.bak      # keep the default page, do not delete it
mkdir -p app
```

From your machine (no GitHub needed):
```bash
rsync -az --delete -e "ssh -p 65002" \
  --exclude .git --exclude node_modules --exclude vendor --exclude .env --exclude .env.production \
  --exclude storage/logs --exclude "Claude outputs" --exclude docs/figma \
  ./ u383259569@194.59.166.35:domains/linen-spider-307400.hostingersite.com/app/
scp -P 65002 .env.production u383259569@194.59.166.35:domains/linen-spider-307400.hostingersite.com/app/.env
```
Fill the three `CHANGE_ME` database lines in `.env.production` first.

## 3. On the server
```bash
cd domains/linen-spider-307400.hostingersite.com
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
- `https://linen-spider-307400.hostingersite.com/en` loads the landing.
- Sign in at `/login` with each demo account:
  - admin (`admin@tourstica.test`) goes to `/admin/dashboard`
  - agency (`agency@tourstica.test`) goes to `/agency/dashboard`
  - client (`client@tourstica.test`) goes to `/en/dashboard/bookings`
- Passwords are the `ADMIN_PASSWORD`, `DEMO_AGENCY_PASSWORD` and `DEMO_CLIENT_PASSWORD` lines in `.env.production`.

## Updating later
Repeat step 1 and the rsync from step 2, then on the server: `composer install --no-dev -o`, `php artisan migrate --force`, and the four `*:cache` commands.
