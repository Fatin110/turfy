# Turfy

Laravel 13 + Inertia.js + Vue 3 + Tailwind (Breeze starter). See README.md for the feature overview and setup.

## Conventions

- **Mass assignment**: models use PHP attributes (`#[Fillable([...])]`) instead of `protected $fillable`. When a controller passes a new field into `::create()`/`::update()`, it must be added to that model's `#[Fillable]` list or it is silently dropped (no exception) — this caused real bugs during development (`Tournament`/`League` missing `created_by`). `php artisan db:seed` runs inside `Model::unguarded()`, which bypasses this check entirely, so a seeder succeeding does **not** prove mass assignment works from a real request — test via the actual controller/route.
- **Route model binding**: `Venue`, `Tournament`, `Team`, and `League` override `getRouteKeyName()` to `slug` for public-facing routes. Any route/frontend call that needs to look one up by numeric `id` instead (e.g. admin action routes driven by a table row's `id`) must declare it explicitly: `Route::patch('/venues/{venue:id}/approve', ...)`. Passing a numeric id to a route that implicitly binds by slug 404s silently.
- **Slugs**: generated via `App\Support\Slug::unique($name, Model::class)`, not regenerated on rename.
- **Payments**: `App\Services\Payments\PaymentGatewayContract` is the swap point for real bKash — see README's "Going live with real bKash".
- **Team ↔ user eligibility**: a user can act for a team if they're a roster player, its captain, or (for a venue's team) the venue's owner — see `Team::scopeManageableBy()`. Don't reach for `$user->teams()` (roster-only) when checking who can register/manage a team, or venue owners get locked out of their own venue's team.

## Local dev

```bash
php artisan migrate:fresh --seed   # reset + demo data
npm run build                      # or npm run dev
php artisan serve
```
