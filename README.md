# Underground Network Inc.

> Power beneath the surface.

The platform behind Underground Network Inc. — a global strategic advisory and
influence firm. Built as an **API-first, domain-driven** Laravel application on
MySQL: the JSON API is the product, and the marketing site is one of its clients.

**Status: work in progress.** The domain, application and persistence layers
are in place across Content, Insights, Engagement and Membership; the JSON API
surface is live for Membership only, and the landing page is not finished.
See [Roadmap](#roadmap).

---

## Architecture

The Laravel skeleton in `app/` stays deliberately thin. All business code lives
under `src/`, in four layers with a strict inward dependency rule — each layer
may only depend on the ones above it.

| Layer | Namespace | Path | Knows about |
| --- | --- | --- | --- |
| Domain | `Domain\` | `src/Domain` | Nothing. Pure PHP: entities, value objects, enums, domain events, repository *contracts*. |
| Application | `Application\` | `src/Application` | Domain only. Use cases: queries and actions, plus the DTOs they exchange. |
| Infrastructure | `Infrastructure\` | `src/Infrastructure` | Domain + Application. Eloquent records, repository implementations, service-provider bindings. |
| Interfaces | `Interfaces\` | `src/Interfaces` | Everything. HTTP controllers, form requests, API resources. |

The domain layer contains no Laravel imports at all — it is framework-agnostic
and testable without booting the container.

### Bounded contexts

- **Content** — the firm's catalogue and positioning: capabilities, sectors,
  metrics, engagement models, brand pillars, and the authored `Narrative`.
- **Insights** — published thinking from the think-tank practice.
- **Engagement** — one of two write sides. `ConfidentialInquiry` is an
  aggregate root with an explicit status machine (`received → under_review →
  engaged / declined → archived`) and its own opaque `InquiryReference`
  (`UG-2026-7KQ4XB`).
- **Membership** — the other write side. `MembershipApplication` is an
  aggregate root over a vetted `MembershipTier`, with its own status machine
  and opaque `MembershipReference`. See [Membership](#membership) below.

### API-first

`Application\Content\Queries\ComposeLandingPage` assembles a single
`LandingPage` DTO. Both `GET /api/v1/landing-page` and the server-rendered page
are projections of that one object, so the site and the API cannot drift.

---

## API surface

Base path `/api/v1`. The version handshake and the full Membership contract
are live; the remaining resource endpoints are the agreed shape but are not
wired up yet (Content and Insights are served today only as
server-rendered pages — see the `Interfaces\Http\Api\V1` roadmap item).

| Method | Endpoint | Status |
| --- | --- | --- |
| `GET` | `/api/v1` | live |
| `GET` | `/api/v1/landing-page` | planned |
| `GET` | `/api/v1/capabilities`, `/capabilities/{slug}` | planned |
| `GET` | `/api/v1/sectors`, `/sectors/{slug}` | planned |
| `GET` | `/api/v1/metrics` | planned |
| `GET` | `/api/v1/engagement-models` | planned |
| `GET` | `/api/v1/pillars` | planned |
| `GET` | `/api/v1/insights`, `/insights/{slug}` | planned |
| `POST` | `/api/v1/inquiries` | planned |
| `GET` | `/api/v1/inquiries/{reference}` | planned |
| `GET` | `/api/v1/membership/tiers` | live |
| `POST` | `/api/v1/membership/applications` | live (rate-limited: 10/min) |
| `GET` | `/api/v1/membership/applications/{reference}` | live |

---

## Membership

Underground extends a small number of vetted membership tiers. There is no
public checkout — every applicant is reviewed by hand before a tier is
granted, so the write path is a review queue, not a payment flow.

- `GET /api/v1/membership/tiers` (and `GET /membership` for the
  server-rendered page) list the tiers currently open for application.
- `POST /api/v1/membership/applications` (and the matching
  `POST /membership/apply/{tier}` form) submits an application against one
  tier. It is rate-limited to 10 requests/minute per client, validated
  through `ApplyForMembershipRequest`, and returns the application's opaque
  `MembershipReference` (e.g. `UGM-2026-7KQ4XB`) on success.
- `GET /api/v1/membership/applications/{reference}` tracks an application by
  that reference, returning `404` if it doesn't exist.

`MembershipApplication` is an aggregate root with its own status machine
(`MembershipApplicationStatus`); applications are never seeded and move
through the machine only via reviewer action, not through the public API.

---

## Getting started

Requires PHP 8.3+, Composer and MySQL 8.

```bash
composer install
cp .env.example .env
php artisan key:generate
```

Create the schema, then serve:

```bash
mysql -uroot -e "CREATE DATABASE IF NOT EXISTS underground CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

```bash
php artisan migrate --seed
```

```bash
php artisan serve
```

Run the test suite (SQLite in-memory, no MySQL required) and the code-style
check the same way CI does:

```bash
php artisan test
vendor/bin/pint --test
```

---

## Running with Docker

A production-shaped multi-stage `Dockerfile` (Node stage builds the Vite
assets, a Composer stage installs PHP dependencies, a slim `php:8.3-cli-alpine`
stage runs the app with `php artisan serve`) and a `docker-compose.yml` wiring
the app to MySQL 8 are provided at the repo root.

```bash
export APP_KEY="$(php artisan key:generate --show)"
export DB_PASSWORD=changeme
export DB_ROOT_PASSWORD=changeme-too

docker compose up --build
```

The `app` service refuses to start without `APP_KEY`, `DB_PASSWORD` and
`DB_ROOT_PASSWORD` set — these are real secrets and the compose file
deliberately has no baked-in default for them (see
[Known gaps](#known-gaps)). The container's entrypoint waits for MySQL, then
runs `php artisan migrate --force` before serving on `:8000`
(`http://localhost:8000` by default; override with `APP_PORT`).

---

## Roadmap

- [x] Layered DDD skeleton, PSR-4 namespaces, container bindings
- [x] Content, Insights, Engagement and Membership domain models
- [x] Application use cases (`ComposeLandingPage`, `SubmitConfidentialInquiry`,
      `ApplyForMembership`, …)
- [x] Remaining Eloquent repositories (engagement models, pillars, insights, inquiries, narrative)
- [x] Membership domain, application, persistence and JSON API layers
- [x] Feature and unit test suites (116 tests, unit + feature, SQLite-backed)
- [x] CI pipeline (`.github/workflows/ci.yml`): install, migrate, test, Pint, build
- [x] Production Dockerfile and Docker Compose (app + MySQL 8)
- [ ] Migrations, seeders and factories — migrations and most factories are
      complete, but `ContentSeeder` (capabilities/sectors/metrics) doesn't
      exist yet and `InsightSeeder` is wired but not called from
      `DatabaseSeeder`
- [ ] `Interfaces\Http\Api\V1` controllers, form requests and resources — done
      for Membership only; Content, Insights and Engagement still need JSON
      API controllers (they're currently server-rendered pages only)
- [ ] Landing page — desktop and mobile — against the reference designs in `design/`

Reference designs for the landing page live in [`design/`](design/), alongside
the portrait used in the hero composition.

---

## Known gaps

Things this module deliberately left for local reconciliation rather than
guessing at, plus real secrets no repo can supply:

- **Migrations were not run against a real database here.** This sandbox
  only exercises them via `php artisan test`'s SQLite in-memory connection;
  run `php artisan migrate` against real MySQL 8 locally (or via
  `docker compose up`) to confirm the schema applies cleanly end to end.
- **`APP_KEY`, database credentials, and mail credentials are real secrets**
  and are intentionally not baked into `.env.example`, the CI workflow, or
  `docker-compose.yml` (which refuses to start the `app`/`mysql` services
  without `APP_KEY`, `DB_PASSWORD` and `DB_ROOT_PASSWORD` set). `MAIL_MAILER`
  defaults to `log`; point it at a real transport before anything needs to
  actually send mail.
- **Content seeding is incomplete**: `ContentSeeder` (capabilities, sectors,
  metrics) doesn't exist yet, and `InsightSeeder` exists but isn't called
  from `DatabaseSeeder` — `php artisan migrate --seed` today only seeds
  engagement models, pillars, the narrative, and membership tiers.
- **CORS** currently allows all origins on `api/*` with
  `supports_credentials: false` (Laravel's framework default, no
  `config/cors.php` override) — reasonable for a public, cookie-free JSON
  API, but revisit if the API ever needs to carry session credentials
  cross-origin.
- **Session/cookie defaults** (`database` driver, `SameSite=lax`,
  `http_only=true`, `secure` following `SESSION_SECURE_COOKIE`) are
  Laravel's production-sane defaults; set `SESSION_SECURE_COOKIE=true` (as
  `docker-compose.yml` already does) once the app is served over HTTPS.
- The `npm run build` step in CI and in the Docker frontend stage fetches
  the Instrument Sans / Playfair Display font files from `fonts.bunny.net`
  at build time (via `laravel-vite-plugin`'s font helper) — it needs normal
  outbound internet access to succeed, which this sandbox's network policy
  did not provide, so that step could not be exercised here. GitHub Actions
  runners and a typical Docker build host have unrestricted egress, so this
  is expected to work there.

---

© 2026 Underground Network Inc. All rights reserved.
