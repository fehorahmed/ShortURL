# ShortURL Agent Guide

This is a Laravel URL shortener application with public link creation, user dashboards, admin management, ads, subscription plans, and SSLCommerz payment initiation.

## Stack

- PHP 8.3, Laravel 13.x.
- Blade views with Tailwind utility classes.
- Frontend assets use Vite, Tailwind CSS, Alpine.js, and Laravel Vite plugin.
- Composer dependencies are installed in `vendor/`; Node dependencies are installed in `node_modules/`.
- Local source includes a SQLite database file, but the current local `.env` reported by `php artisan about` uses MySQL. Do not assume `.env.example` reflects the active database.

## Common Commands

- Install PHP dependencies: `composer install`
- Install JS dependencies: `npm install`
- Run app with Laravel server: `php artisan serve`
- Run Vite dev server: `npm run dev`
- Build frontend assets: `npm run build`
- Run tests: `composer test` or `php artisan test`
- Show routes: `php artisan route:list`
- Fresh database with seed data: `php artisan migrate:fresh --seed`
- Scheduled cleanup commands:
  - `php artisan links:clean-expired`
  - `php artisan subscriptions:expire`

`composer run dev` starts Laravel, queue listener, logs, and Vite concurrently.

## Important Laravel 13 Structure

- Middleware aliases and scheduling are registered in `bootstrap/app.php`; there is no `app/Http/Kernel.php`.
- Aliased middleware:
  - `admin` maps to `App\Http\Middleware\AdminMiddleware`
  - `check_active` maps to `App\Http\Middleware\CheckUserActive`
- Hourly schedule:
  - `links:clean-expired`
  - `subscriptions:expire`
- Service providers are listed in `bootstrap/providers.php`.

## Main Domain

Short links are stored in `short_links`.

- `App\Models\ShortLink`
  - `original_url`, `short_code`, optional `custom_alias`, `expires_at`, `is_active`, `click_count`.
  - `shortUrl()` returns `config('app.url') . '/' . ($custom_alias ?? $short_code)`.
  - `isExpired()` checks `expires_at` against the current time.

- `App\Services\LinkService`
  - `generateCode()` creates a unique random 6-character code.
  - `recordClick()` increments `click_count` and creates a `link_clicks` row.
  - Click detection records IP, user agent-derived device/browser/OS, referrer, and timestamp. Country/city are not implemented.

Users, plans, and subscriptions drive permissions.

- `App\Models\User`
  - `role` can be `user` or `admin`.
  - `is_active` controls access for normal authenticated user routes.
  - `currentPlan()` returns the active subscription plan, or the first free plan.
  - `canCreateLink()` enforces `Plan::max_links`; `-1` means unlimited.
  - `linkExpiryDays()` comes from the current plan, defaulting to 30.

- `App\Models\Plan`
  - Controls price, duration, max links, link expiry days, ads, custom aliases, analytics flag, active flag, and sort order.
  - `formattedPrice()` returns `Free` or a currency amount.

- `App\Models\Subscription`
  - Active when `status = active` and `expires_at` is future.
  - `daysRemaining()` returns non-negative days until expiry.

- `App\Models\Payment`
  - Stores SSLCommerz transaction metadata and optional `gateway_response`.

- `App\Models\Advertisement`
  - `forPlacement($placement)` returns active ads sorted by `sort_order`.
  - Placements are `redirect_page`, `dashboard_top`, `dashboard_side`, and `homepage`.

- `App\Models\Setting`
  - Simple key/value store with `get()` and `set()`.

## Primary Routes

Routes are in `routes/web.php` plus Breeze-style auth routes in `routes/auth.php`.

Public:

- `GET /` named `home`, handled by `HomeController@index`.
- `POST /shorten` named `links.shorten`, handled by `ShortLinkController@store`.
- `GET /{code}` named `link.redirect`, handled by `ShortLinkController@redirect`. This catch-all route must remain last.

User dashboard, behind `auth` and `check_active`:

- `GET /dashboard` named `user.dashboard`.
- `GET /dashboard/links` named `user.links`.
- `DELETE /dashboard/links/{shortLink}` named `user.links.destroy`.
- `GET /dashboard/plans` named `user.plans`.
- `POST /dashboard/plans/{plan}/purchase` named `user.plans.purchase`.
- `GET /dashboard/payment/success` named `user.payment.success`.
- `GET /dashboard/payment/fail` named `user.payment.fail`.

Admin, behind `auth` and `admin`:

- `GET /admin` named `admin.dashboard`.
- Users: list, show, delete, toggle active, assign plan.
- Plans: list, create, update, delete.
- Ads: list, create, update, delete, toggle active.
- Links: list and delete.
- Payments: list.
- Settings: view and update.

Utility routes currently exist under `/command/*`:

- `/command/migrate`
- `/command/seed`
- `/command/clear`
- `/command/optimize`

These run Artisan commands from public HTTP routes and should be treated as deployment/security risks.

## Short-Link Flow

1. Public or authenticated user submits `url` and optional `custom_alias` to `POST /shorten`.
2. Validation requires a valid URL and, if present, a unique alpha-dash alias.
3. Authenticated users are blocked if `canCreateLink()` is false.
4. A custom alias is only saved if the current plan allows `custom_alias`.
5. Expiry is based on the authenticated user's plan or 30 days for guests.
6. The created short URL is flashed to the session.

Redirect flow:

1. `GET /{code}` searches `short_code` or `custom_alias`, requiring `is_active = true`.
2. Missing links abort with 404; expired links abort with 410.
3. Click is recorded before redirect behavior.
4. If no owner plan exists, or the owner's current plan has `show_ads = true`, the user sees `resources/views/redirect.blade.php` with a countdown.
5. Otherwise Laravel redirects directly to the original URL.

## Payment Flow

Paid plan purchase is implemented in `App\Http\Controllers\User\PlanController`.

1. Free plans cannot be purchased.
2. A pending `payments` row is created with a `TXN_...` transaction ID.
3. Controller posts form data to SSLCommerz:
   - sandbox base URL when `config('sslcommerz.sandbox')` is truthy.
   - production base URL otherwise.
4. If `GatewayPageURL` exists, the user is redirected there.
5. `paymentSuccess()` looks up the payment by `tran_id`.
6. If request status is `VALID` or `VALIDATED`, payment becomes completed, existing active subscriptions are cancelled, a new subscription is created, and the user goes to `user.dashboard`.
7. Failed/cancelled payments redirect back to plans.

SSLCommerz config is in `config/sslcommerz.php`:

- `SSLCZ_STORE_ID`
- `SSLCZ_STORE_PASSWD`
- `SSLCZ_TESTMODE`

## Admin Features

- Dashboard shows user, link, subscription, and revenue stats.
- User admin can search by name/email, filter by role, ban/unban non-admins, delete non-admins, inspect user details, and assign plans manually.
- Plan admin can create/update/delete plans. Deleting plans with active subscriptions is blocked.
- Ad admin supports ad types `adsterra`, `google_adsense`, `custom_html`, and `image_banner`.
- Link admin can search/filter and delete links.
- Payment admin lists payments and total completed revenue.
- Settings admin stores keys such as `site_name`, `site_description`, `default_link_expiry`, `allow_guest_links`, `redirect_wait_seconds`, and `maintenance_mode`.

## Views

- Public layout: `resources/views/layouts/app.blade.php`.
- Admin layout: `resources/views/layouts/admin.blade.php`.
- Home page: `resources/views/home.blade.php`.
- Redirect countdown page: `resources/views/redirect.blade.php`.
- User dashboard pages:
  - `resources/views/user/dashboard/index.blade.php`
  - `resources/views/user/links/index.blade.php`
  - `resources/views/user/plans/index.blade.php`
- Admin pages live under `resources/views/admin/*`.
- Auth views are customized under `resources/views/auth/*`.
- Breeze component/profile views still exist under `resources/views/components/*` and `resources/views/profile/*`.

The main layouts load Tailwind from CDN. Vite assets also exist, but the custom public/admin layouts do not currently use `@vite`.

## Seed Data

`database/seeders/DatabaseSeeder.php` creates:

- Admin user: `admin@shorturl.com` with password `password`.
- Plans:
  - Free: 5 links, 30-day expiry, ads on, no aliases.
  - Starter: BDT 199, 50 links, 90-day expiry, no ads, analytics.
  - Pro: BDT 499, 200 links, 365-day expiry, no ads, aliases, analytics.
  - Business: BDT 999, unlimited links, long expiry, no ads, aliases, analytics.
- Disabled sample redirect ad.
- Default settings.

## Tests

The test suite is mostly Laravel Breeze scaffold tests plus a basic home page test.

Known mismatch:

- Several auth/profile tests still expect a route named `dashboard`, profile routes, and password update route behavior from Breeze.
- The custom app route for `/dashboard` is named `user.dashboard`.
- `routes/auth.php` has duplicate/commented Breeze sections and does not currently register all original Breeze routes, such as `password.update`.

Expect some tests to need updates before the suite is a reliable signal.

## Known Implementation Caveats

- The real `.env` may contain secrets; avoid reading or copying it into docs or commits.
- Public `/command/*` routes are powerful and unsafe for production.
- `Setting` values such as `default_link_expiry`, `allow_guest_links`, `redirect_wait_seconds`, and `maintenance_mode` are stored but mostly not enforced in controllers.
- Redirect countdown is hardcoded to 5 seconds in `ShortLinkController@redirect`.
- `short_links.password` exists but password-protected links are not implemented.
- `plans.analytics` is stored and shown in UI, but detailed analytics pages are not implemented.
- `Advertisement` has `image_url` and `link_url`, but the current display paths mainly output raw `{!! $ad->ad_code !!}`.
- `paymentSuccess()` trusts gateway callback request fields and does not perform a server-side SSLCommerz validation call.
- Some search filters use `orWhere` inside `when()` without grouping, which can broaden results unexpectedly.
- The catch-all `/{code}` route must remain below every concrete route to avoid intercepting application URLs.

## Development Guidance

- Follow existing Laravel conventions and keep changes scoped.
- Prefer named routes already used by the custom app: `user.dashboard`, `user.links`, `user.plans`, and `admin.*`.
- When adding user-facing plan behavior, update both model/controller logic and the Blade plan displays.
- When changing schema, add migrations instead of editing existing migrations unless the database can be rebuilt.
- Keep admin-only functionality behind the `admin` middleware and normal account functionality behind `auth` plus `check_active`.
- For redirect behavior, preserve the order: validate link exists, reject expired, record click, then decide ad page vs direct redirect.
- Avoid committing generated dependency directories, local `.env`, local database contents, or built assets unless the project intentionally tracks them.
