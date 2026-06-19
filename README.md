# Workboard

Multi-tenant project management SaaS API built with Laravel 12, Sanctum, and Cashier (Stripe).

## Quick start

```bash
git clone <repo-url> workboard
cd workboard
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed
php artisan serve
```

API base URL: `http://localhost:8000/api`

## Demo credentials

After seeding:

| Field | Value |
|-------|-------|
| Email | `demo@workboard.test` |
| Password | `password` |
| Organization | Demo Company (Free plan, 2 existing projects) |

Login via `POST /api/login` to receive a Bearer token.

## Running tests

```bash
php artisan test
```

## Stripe test mode setup

1. Create a [Stripe test account](https://dashboard.stripe.com/test/apikeys).
2. Add keys to `.env`:

```env
STRIPE_KEY=pk_test_...
STRIPE_SECRET=sk_test_...
STRIPE_WEBHOOK_SECRET=whsec_...
STRIPE_PRO_PRICE_ID=price_...
CASHIER_CURRENCY=usd
```

3. Create a recurring **Pro** product/price in Stripe test mode and copy the Price ID to `STRIPE_PRO_PRICE_ID`.
4. Forward webhooks locally:

```bash
stripe listen --forward-to localhost:8000/api/stripe/webhook
```

5. Use `POST /api/billing/checkout` (as org owner) to get a Checkout URL.

Billing is attached to the **Organization**, not individual users. Project limits apply org-wide.

## API documentation

See [API.md](API.md) for request/response examples.

## Deployment notes

See [DEPLOYMENT.md](DEPLOYMENT.md).

## Done / Not done / Next

**Done:** Auth, multi-tenant projects CRUD, Free plan 402 enforcement, Stripe billing (checkout + webhook + status), tasks (create/list), seeders, feature tests, CI.

**Not done:** Live production deploy (optional bonus), Phase 2 change request (Team plan, soft deletes, rate limiting).

**Next:** Phase 2 on a separate branch — Team plan (20 projects), soft-deleted projects excluded from usage, per-org rate limiting (60 req/min).
