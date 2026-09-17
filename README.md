# Turfy

Turfy connects every indoor cricket & football turf in the city on one platform: players discover venues, register for tournaments, follow city-wide leagues, and pay entry fees with bKash — while venue owners list their turf, run tournaments, and field a team in inter-venue leagues.

## Stack

Laravel 13 + Inertia.js + Vue 3 + Tailwind CSS (Breeze starter kit), SQLite for local development.

## Features

- **Venues** — indoor turfs list themselves (pending admin approval), with city/sport filters, ratings & reviews.
- **Teams** — every approved venue automatically gets a "venue team" that represents it in inter-venue competition; players can also form their own open teams.
- **Tournaments** — venue owners or admins create knockout/round-robin tournaments, free or paid. Registration is per-team, gated by sport and capacity.
- **Payments** — paid tournament registration goes through a bKash Tokenized Checkout flow. The gateway is currently a swappable stub (`App\Services\Payments\BkashService`) that mimics the real create/execute lifecycle — see [Going live with real bKash](#going-live-with-real-bkash) below.
- **Leagues** — admins pick N venue teams and generate a full home-and-away round-robin schedule in one click (`FixtureGeneratorService`), La Liga/Premier League style. Standings are computed live from match results (`StandingsService`).
- **Roles** — `admin`, `venue_owner`, `player`, enforced via policies and route middleware (`role:venue_owner,admin`).
- **Admin panel** — venue approvals, league management (add/remove teams, generate fixtures, enter results), payments ledger, user role management.

## Getting started

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed
npm run build   # or `npm run dev` for hot reload
php artisan serve
```

### Demo accounts (from the seeder, password: `password` for all)

| Role | Email |
|---|---|
| Admin | `admin@turfy.app` |
| Venue owner | `rafiq.hasan@turfy.app` (Dhanmondi Arena), `nasir.uddin@turfy.app` (Mirpur Sports Hub), etc. |
| Player | `arif.chowdhury@example.com`, and 13 others — see `database/seeders/DatabaseSeeder.php` |

The seeder creates 6 venues (one pending, for testing the approval flow), a paid and a free tournament with real registrations and payments, and a 5-team football league with a generated double round-robin schedule and some results already played out.

## Going live with real bKash

`config/bkash.php` holds the merchant credentials (`BKASH_APP_KEY`, `BKASH_APP_SECRET`, `BKASH_USERNAME`, `BKASH_PASSWORD`, `BKASH_BASE_URL`) as env-driven placeholders. To go live:

1. Get Tokenized Checkout credentials from bKash's merchant portal.
2. Implement `App\Services\Payments\BkashService::createPayment()` / `executePayment()` against bKash's real Grant Token → Create Payment → Execute Payment flow (currently mocked).
3. Nothing else changes — `PaymentService` and every controller only depend on the `PaymentGatewayContract` interface.

## Roadmap ideas

Beyond this build: slot-based venue booking for regular (non-tournament) play, push/SMS notifications for match reminders, player stat profiles, a subscription tier for venues wanting featured placement, and live score updates via websockets.
