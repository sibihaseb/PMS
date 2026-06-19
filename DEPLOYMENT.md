# Deployment

Brief production and DevOps notes for Workboard.

## Server setup

On a fresh Ubuntu server:

1. Install Nginx, PHP 8.2+ (with `php-fpm`, `sqlite3` or `pdo_mysql`, `mbstring`, `xml`, `curl`, `zip`, `bcmath`), Composer, and optionally Node if you keep the web UI assets.
2. Clone the repo to `/var/www/workboard`, set ownership to the deploy user / `www-data`.
3. Per deploy:
   - `composer install --no-dev --optimize-autoloader`
   - `cp .env` (from secrets store — never commit)
   - `php artisan migrate --force`
   - `php artisan config:cache`
   - `php artisan route:cache`
   - `php artisan view:cache`
   - `php artisan event:cache` (if used)
   - Reload PHP-FPM and reload Nginx.

Set `APP_ENV=production`, `APP_DEBUG=false`, and a real `APP_URL` with HTTPS.

## SSH and access

- Disable password SSH; use key-based auth only.
- Developers use personal SSH keys; production deploys use a dedicated **deploy key** or CI SSH key with least privilege.
- CI/CD stores the private key in GitHub Actions secrets and uses `appleboy/ssh-action` or similar — never embed keys in the repo.

## Secrets and environment

- `.env` is gitignored because it holds DB credentials, `APP_KEY`, and Stripe secrets.
- Production secrets live in the host's `.env` (managed by Forge/Envoyer) or a secrets manager (AWS SSM, Doppler, Vault).
- CI uses GitHub Secrets for test Stripe keys and injects them as env vars — not committed files.

## CI/CD pipeline

| Trigger | Actions |
|---------|---------|
| Pull request | `composer install`, migrate SQLite, `php artisan test`, `pint --test` |
| Merge to `main` | Same checks, then deploy to staging/production |
| Deploy | SSH to server, pull release, `composer install --no-dev`, `migrate --force`, cache commands, reload FPM |

Tag releases for traceability; run migrations before switching traffic.

## Zero-downtime deploys

- Use atomic deploys (symlink swap via Envoy, Deployer, or Forge).
- Run `php artisan migrate --force` **before** pointing the symlink to the new release when migrations are backward-compatible.
- For breaking migrations, use expand/contract: deploy code that supports both schemas, migrate, then deploy code that requires the new schema.
- Keep `php artisan down` as a last resort only.

## Background work

- **Queue workers:** Supervisor or systemd unit running `php artisan queue:work --sleep=3 --tries=3`.
- **Scheduler:** Cron entry `* * * * * cd /var/www/workboard && php artisan schedule:run >> /dev/null 2>&1`.
- After deploy, `supervisorctl reread && supervisorctl update && supervisorctl restart workboard-worker:*` so workers load new code.

## Stripe webhook in production

- HTTPS endpoint required (e.g. `https://api.example.com/api/stripe/webhook`).
- Set `STRIPE_WEBHOOK_SECRET` from the Stripe Dashboard endpoint signing secret.
- Route is excluded from CSRF in `bootstrap/app.php`.
- Use Cashier's `WebhookController`; verify events in Stripe Dashboard.
- For reliability, Stripe retries failed webhooks — return 2xx quickly; offload heavy work to queues.

## Rollback

1. Point the release symlink to the previous known-good release.
2. Reload PHP-FPM / restart workers.
3. If a migration broke data, restore DB from the pre-deploy backup — do not run `migrate:rollback` on production without a plan.
4. Revert the Git tag and redeploy the prior commit if needed.

## Production readiness checklist

- [ ] `APP_ENV=production`, `APP_DEBUG=false`
- [ ] `php artisan config:cache`, `route:cache`, `view:cache`
- [ ] HTTPS/SSL (Let's Encrypt or load balancer TLS)
- [ ] Queue workers under Supervisor/systemd
- [ ] Scheduler cron configured
- [ ] Log rotation (`daily` channel + logrotate)
- [ ] Database backups before each `migrate --force`
- [ ] Stripe live keys and webhook endpoint (test mode for assessment only)
