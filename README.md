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
STRIPE_TEAM_PRICE_ID=price_...
CASHIER_CURRENCY=usd
```

3. Create recurring **Pro** and **Team** products/prices in Stripe test mode.
4. Forward webhooks locally:

```bash
stripe listen --forward-to localhost:8000/api/stripe/webhook
```

5. Use `POST /api/billing/checkout` with `{ "plan": "pro" }` or `{ "plan": "team" }` (org owner only).

Billing is attached to the **Organization**, not individual users. Project limits apply org-wide.

## Plans

| Plan | Project limit |
|------|---------------|
| Free | 3 |
| Team | 20 |
| Pro | Unlimited |

## Phase 2 features

- **Team plan** — 20 projects via Stripe Team price
- **Soft deletes** — `DELETE /api/projects/{id}` soft-deletes; `POST /api/projects/{id}/restore` restores; deleted projects do not count toward limits
- **Rate limiting** — 60 requests/min per organization on protected API routes (HTTP 429)

## API documentation

See [API.md](API.md) for request/response examples.

## Deployment notes

See [DEPLOYMENT.md](DEPLOYMENT.md).

## Done / Not done / Next

**Done:** Phase 1 (auth, tenancy, projects CRUD, Free/Pro billing, tasks, CI, docs) and Phase 2 (Team plan, soft deletes + restore, per-org rate limiting).

**Not done:** Live production deploy (optional bonus).

**Next:** Optional production deploy; Stripe live-mode hardening if shipping beyond test mode.
